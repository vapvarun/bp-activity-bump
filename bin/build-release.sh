#!/usr/bin/env bash
# Build dist/bp-activity-bump-<version>.zip from the committed tree (git archive), minus .distignore.
# Runs the local quality gate first. Every zip entry is deflated: the zip CLI stores tiny
# files (index.php stubs) as plain text, which upload firewalls block.
set -euo pipefail
cd "$(dirname "$0")/.."
slug=bp-activity-bump
bash bin/local-ci.sh
version=$(grep -m1 -E '^\s*\*\s*Version:' bp-bump.php | awk '{print $NF}')
rm -rf dist && mkdir -p "dist/$slug"
git archive HEAD | tar -x -C "dist/$slug"
while IFS= read -r rule; do
	case "$rule" in ''|\#*) continue ;; esac
	# "/x" is anchored at the plugin root; a bare pattern ("*.md") matches at any depth.
	if [ "${rule#/}" != "$rule" ]; then
		find "dist/$slug" -path "dist/$slug/${rule#/}" -prune -exec rm -rf {} +
	else
		find "dist/$slug" -name "$rule" -prune -exec rm -rf {} +
	fi
done < .distignore
( cd dist && python3 -I -c 'import os,sys,zipfile
with zipfile.ZipFile(sys.argv[1], "w", zipfile.ZIP_DEFLATED) as z:
    for r, d, f in os.walk(sys.argv[2]):
        d.sort()
        for n in sorted(f): z.write(os.path.join(r, n))' "$slug-$version.zip" "$slug" )
[ "$(LC_ALL=C grep -a -c '<?php' "dist/$slug-$version.zip" || true)" = 0 ] || { echo "Zip has plain-text PHP"; exit 1; }
leaks=$(unzip -l "dist/$slug-$version.zip" | grep -iE '/(audit|bin|docs|tests|vendor|node_modules)/|CLAUDE|\.md$|composer\.|phpcs|phpstan|\.dist' || true)
[ -z "$leaks" ] || { echo "Zip contains dev files:"; echo "$leaks"; exit 1; }
echo "Built dist/$slug-$version.zip"
