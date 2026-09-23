# Vito Extended

A [VitoDeploy](https://vitodeploy.com) 4.x community plugin that adds project-wide overview pages for deployments and primary domains.

Instead of opening each server and site one by one, you get two tables across your whole project: the latest deployment per site, and every primary domain with SSL status at a glance.

## Features

### Latest Deployments

- One row per site — the most recent deployment only
- Sorted by domain
- Clickable domain links
- Commit and release info
- **Redeploy** button (enabled only when the latest deployment failed)

### Primary Domains

- Every primary hosted domain across all servers
- Sorted by domain
- Clickable domain links
- Certificate type, status, and expiry
- **Fix SSL** button for Let's Encrypt domains that need attention (missing cert, missing expiry, etc.)

## Requirements

- VitoDeploy **4.x**

## Install from Community Plugins

1. In Vito, go to **Admin → Plugins → Community**
2. Find **Vito Extended** and click **Install**
3. Open the **Installed** tab, then **Enable** the plugin
4. Rebuild frontend assets on your Vito instance:

   ```bash
   npm run build
   ```

5. Use the **Vito Extended** item in the sidebar, or open:

   - `/vito-extended/latest-deployments`
   - `/vito-extended/primary-domains`

## Local development

1. Clone this repo into your Vito instance:

   ```
   app/Vito/Plugins/Kjjd84/VitoExtended
   ```

   For repo `kjjd84/vito-extended`, the namespace must be `App\Vito\Plugins\Kjjd84\VitoExtended`.

2. Go to **Admin → Plugins → Discover**, install the plugin, then **Enable** it.

3. Rebuild frontend assets if needed:

   ```bash
   npm run build
   ```

On install/enable, the plugin publishes its Inertia pages into `resources/js/pages/plugins/kjjd84-vito-extended/` and adds sidebar navigation.

## Updating

In Vito, go to **Admin → Plugins**, click **Check for updates**, then update from the plugin menu when a new release is available.

## License

MIT

## Links

- [Plugin development](https://vitodeploy.com/docs/4.x/plugins)
- [Managing plugins (admin)](https://vitodeploy.com/docs/admin/plugins)
