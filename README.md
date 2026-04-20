# Member Verification

A WordPress plugin that displays membership status at `/verify/{user_id}`.

## Requirements

- WordPress 6.0+
- PHP 7.4+
- [Ultimate Member](https://wordpress.org/plugins/ultimate-member/) plugin

## Installation

1. Copy the `memberverification` folder into `wp-content/plugins/`
2. Activate the plugin in **Plugins → Installed Plugins**
3. Go to **Settings → Permalinks** and click **Save Changes** to register the URL rules

## Usage

Visit `/verify/{user_id}` where `{user_id}` is a WordPress user ID.

**Examples:**
- `https://yoursite.com/verify/18`
- `https://yoursite.com/verify/42`

The page displays one of two messages:

| Result | Displayed when |
|---|---|
| **Member in Good Standing** | User exists and has the role `um_member`, `um_board-member`, or `administrator` |
| **Not a member** | User does not exist, has none of the above roles, or no user ID was provided |

The page renders using your active theme — it will look like any other page on your site.

## Notes

- The verification page is publicly accessible (no login required)
- The plugin has no settings page
