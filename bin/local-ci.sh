#!/usr/bin/env bash
# Local quality gate for bp-activity-bump. Run before every commit; bin/build-release.sh runs it first.
# 1. PHP syntax  2. version in header / constant / readme  3. changelog entry  4. WPCS (when phpcs is installed)
set -uo pipefail
cd "$(dirname "${BASH_SOURCE[0]}")/.."
FAILED=0
bad() { printf 'FAIL %s\n' "$1"; FAILED=1; }

LINT="$(find . -name '*.php' -not -path './node_modules/*' -print0 | xargs -0 -n1 php -l 2>&1 | grep -v '^No syntax errors' || true)"
[ -z "$LINT" ] || { echo "$LINT"; bad "PHP syntax errors"; }

V_HEADER="$(grep -oE '^\s*\*\s*Version:\s*[0-9.]+' bp-bump.php | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')"
V_CONST="$(grep -oE "BP_ACTIVITY_BUMP_VERSION', '[0-9.]+'" bp-bump.php | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')"
V_README="$(grep -oE '^Stable tag: [0-9.]+' readme.txt | grep -oE '[0-9]+\.[0-9]+\.[0-9]+')"
{ [ "$V_HEADER" = "$V_CONST" ] && [ "$V_CONST" = "$V_README" ]; } || bad "version mismatch: header=$V_HEADER constant=$V_CONST readme=$V_README"
grep -qE "^= ${V_HEADER//./\\.} " readme.txt || bad "readme.txt changelog has no '= $V_HEADER' entry"

if command -v phpcs >/dev/null 2>&1; then
	phpcs -q --standard=WordPress-Extra --exclude=WordPress.Files.FileName,Universal.Files.SeparateFunctionsFromOO --extensions=php --ignore=node_modules --runtime-set testVersion 8.1- bp-bump.php admin || bad "phpcs violations"
else
	echo "phpcs not installed, skipped"
fi

[ "$FAILED" -eq 0 ] && echo "local-ci OK ($V_HEADER)"
exit "$FAILED"
