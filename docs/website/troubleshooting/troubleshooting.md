# Troubleshooting

## An activity did not move to the top

1. Open **WB Plugins > Activity Bump > General** and check that **Bump On** matches the action you tested.
2. Make sure the BuddyPress **Activity** component is on under **Settings > BuddyPress > Components**.
3. If your site uses a page cache, clear it. Logged-out visitors can be served a saved copy of the stream.

## I see a notice that BuddyPress is required

The plugin needs BuddyPress or BuddyBoss Platform. Activate it and the notice goes away. The plugin stays active meanwhile, so your setting is kept.
