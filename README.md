# PluginBase Boilerplate

A modern, un-opinionated WordPress plugin boilerplate built with **React**, **TypeScript**, **Vite**, and **TailwindCSS**.

This boilerplate provides a solid foundation for building complex WordPress plugins with a modern React-based admin interface, while keeping the PHP side clean and standard.

## Features

- ⚡️ **Vite**: Blazing fast hot module replacement (HMR) and build performance.
- ⚛️ **React 19**: Typical React setup for Admin UI.
- 🎨 **TailwindCSS**: Utility-first CSS framework for rapid UI development.
- 📘 **TypeScript**: Type safety for your frontend code.
- 🐘 **Clean PHP**: Namespaced structure with a simple SPL autoloader (no Composer requirement by default).
- 🔄 **Auto-Renaming**: Includes a setup script to rename the boilerplate to your specific plugin name effortlessly.

## Getting Started

### Prerequisites

- Node.js (v18+)
- WordPress Installation

### Installation

1.  Clone this repository into your WordPress `wp-content/plugins` directory.
2.  Run `npm install` to install frontend dependencies.

### Renaming the Boilerplate

This project comes with a helper script to rename the plugin from "PluginBase" to your desired name (updating namespaces, constants, slugs, and filenames).

```bash
npm run setup
```

Follow the interactive prompts to define your plugin name, description, and confirm the derived values (Slug, PascalCase, etc.). The script will automatically search and replace text and rename files.

## Development Workflow

### Development Mode (HMR)

To start the development server with Hot Module Replacement:

```bash
npm run dev
```

This will:
1.  Start the Vite dev server.
2.  Create a `.dev-server-running` file in the root.
3.  The PHP plugin detects this file and loads assets from localhost instead of the build folder.

### Production Build

To build the assets for production:

```bash
npm run build
```

This will:
1.  Compile TypeScript and React code.
2.  Generate optimized CSS and JS files in `admin/build/`.
3.  Generate a `manifest.json` for PHP to locate the hashed filenames.

## Directory Structure

```text
/
├── admin/                  # React Admin App
│   ├── src/                # Frontend Source
│   └── build/              # Computed Build Output
├── includes/               # PHP Classes (Autoloaded)
│   └── Plugin.php          # Main Class
├── dev/                    # Development Helpers
├── scripts/                # Node.js Maintenance Scripts
├── bootstrap.php           # Autoloader
├── plugin-base.php         # Main Entry File
├── vite.config.js          # Vite Configuration
└── package.json            # Node Dependencies
```

## License

[MIT](LICENSE)
