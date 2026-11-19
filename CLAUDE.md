# BuddyPress Activity Bump

Guideline doc: how to build here. History lives in `git log` and `readme.txt`.

- Main file `bp-bump.php`: one helper `wb_bp_activity_bump()` does the bump; hooks register on `bp_init` only when the Activity component is active.
- A bump is a single prepared UPDATE of `date_recorded` by activity ID, then the BuddyPress activity caches are cleared. First bump stores the original date in the existing `activity_bump_date` activity meta. Top-level activities only, never `activity_comment`.
- One option, `bp_bump_admin_general_options` (`bp_bump_activity_option`: favorite-activity, commented-activity, both-activity). Read it only through `wb_bp_activity_bump_mode()`. No new tables or options.
- Admin shell lives in `admin/` (wbcom wrapper under `admin/wbcom/`). Admin CSS uses the `:root` tokens in `wbcom-admin-setting.css`; no hex outside `:root`.
- No bundled updater: free GitHub-only release, updates come from GitHub releases.
- Build: `npx grunt cssmin uglify rtlcss` for assets, `wp i18n make-pot . languages/bp-activity-bump.pot`, `bash bin/local-ci.sh`, `bash bin/build-release.sh`.
- PHP 8.1 minimum. Docs: `docs/website/`, lint with `~/.claude/skills/wbcom-documentation/bin/lint-plugin-docs.mjs`.
