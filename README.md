# Vito Extended — VitoDeploy Plugin

A VitoDeploy plugin that adds two project-wide overview pages:

- **Latest Deployments** — latest deployment per site across all servers
- **Primary Domains** — primary domain for every site across all servers

## Requirements

- VitoDeploy 4.x

## Local development

1. Copy or symlink this repository into your Vito instance:

   ```
   app/Vito/Plugins/Kjjd84/VitoExtended
   ```

   For repo `kjjd84/vito-extended`, the namespace must be `App\Vito\Plugins\Kjjd84\VitoExtended`.

2. In Vito, go to **Admin → Plugins → Discover**, install the plugin, then **Enable** it.

3. Rebuild frontend assets if needed:

   ```bash
   npm run build
   ```

4. Open:

   - `/vito-extended/latest-deployments`
   - `/vito-extended/primary-domains`

The plugin publishes its Inertia pages into `resources/js/pages/plugins/kjjd84-vito-extended/` on install/enable.

## Publishing

Add the `vitodeploy-plugin` topic to your GitHub repository to list it in Vito's community plugins catalog.

## Documentation

- [Plugin development](https://vitodeploy.com/docs/4.x/plugins)
- [Managing plugins (admin)](https://vitodeploy.com/docs/admin/plugins)
