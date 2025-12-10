const fs = require('fs');
const path = require('path');
const readline = require('readline');

const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout
});

const askQuestion = (query) => new Promise((resolve) => rl.question(query, resolve));

// Configuration
const IGNORE_DIRS = ['node_modules', '.git', 'build', 'dist', '.idea', '.vscode', 'scripts'];
const IGNORE_FILES = ['setup.js', 'package-lock.json', '.DS_Store'];

// Helper to convert strings
const formatters = {
    slug: (str) => str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, ''),
    pascal: (str) => str.replace(/(^\w|[^a-zA-Z0-9]\w)/g, match => match.replace(/[^a-zA-Z0-9]/, '').toUpperCase()),
    snake: (str) => str.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, ''),
    upperSnake: (str) => str.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '').toUpperCase(),
    title: (str) => str.trim(),
};

async function main() {
    console.log('🚀  Plugin Setup Script\n');
    console.log('This script will rename all instances of "PluginBase" to your new plugin name.\n');

    const name = await askQuestion('Plugin Name (e.g. "My Awesome Plugin"): ');
    if (!name) {
        console.error('Plugin name is required.');
        process.exit(1);
    }

    const description = await askQuestion('Plugin Description (optional): ');

    // Derived names
    const newSlug = formatters.slug(name);
    const newPascal = formatters.pascal(name);
    const newSnake = formatters.snake(name);
    const newUpperSnake = formatters.upperSnake(name);
    const newTitle = formatters.title(name);

    console.log('\nPlease confirm the following values:');
    console.log(`- Slug:        ${newSlug} (e.g. text-domain, file names)`);
    console.log(`- Pascal:      ${newPascal} (e.g. Namespaces, Classes)`);
    console.log(`- Upper Snake: ${newUpperSnake} (e.g. Constants)`);
    console.log(`- Title:       ${newTitle} (e.g. Plugin Name header)`);

    const confirm = await askQuestion('\nIs this correct? (y/n): ');
    if (confirm.toLowerCase() !== 'y') {
        console.log('Aborted.');
        process.exit(0);
    }

    console.log('\nProcessing...');

    // Walk and replace
    const rootDir = path.resolve(__dirname, '..');

    // Replacement map matches:
    // 1. Text Domain / Slug: plugin-base -> new-slug
    // 2. Namespace: PluginBase -> NewPascal
    // 3. Constant: PLUGIN_BASE -> NEW_UPPER_SNAKE
    // 4. Title: PluginBase -> New Title (specifically for "Plugin Name: PluginBase" header usually, but let's be careful)

    // We define precise string replacements to avoid accidental partial matches
    const replacements = [
        { from: 'PluginBase', to: newPascal },          // Namespace / Class
        { from: 'plugin-base', to: newSlug },           // Slug / Text Domain
        { from: 'PLUGIN_BASE', to: newUpperSnake },     // Constants
        { from: 'Plugin Base', to: newTitle },          // Text (Generic) (Case insensitive check might be safer but let's stick to knowns)
    ];

    // Special case for the Plugin Name header in main file
    // We will handle file content replacements dynamically

    await processDirectory(rootDir, replacements);

    console.log('\n✅  Renaming complete!');
    console.log(`\nNote: You may need to run 'npm install' if package.json was changed.\n`);

    rl.close();
}

async function processDirectory(dir, replacements) {
    const files = fs.readdirSync(dir);

    for (const file of files) {
        const fullPath = path.join(dir, file);
        const stat = fs.statSync(fullPath);

        if (stat.isDirectory()) {
            if (IGNORE_DIRS.includes(file)) continue;
            await processDirectory(fullPath, replacements);

            // Rename directory if needed
            // NOTE: We do this AFTER processing children to avoid path errors
            renamePathIfNeeded(fullPath, replacements);

        } else {
            if (IGNORE_FILES.includes(file)) continue;

            // Read content
            let content;
            try {
                content = fs.readFileSync(fullPath, 'utf8');
            } catch (e) {
                // Binaries, etc.
                continue;
            }

            let newContent = content;

            // Apply replacements
            replacements.forEach(rep => {
                // Global replace
                const regex = new RegExp(escapeRegExp(rep.from), 'g');
                newContent = newContent.replace(regex, rep.to);
            });

            // Write back if changed
            if (newContent !== content) {
                fs.writeFileSync(fullPath, newContent, 'utf8');
                console.log(`Updated: ${path.relative(process.cwd(), fullPath)}`);
            }

            // Rename file if needed
            renamePathIfNeeded(fullPath, replacements);
        }
    }
}

function renamePathIfNeeded(oldPath, replacements) {
    const dir = path.dirname(oldPath);
    const filename = path.basename(oldPath);

    let newFilename = filename;
    replacements.forEach(rep => {
        const regex = new RegExp(escapeRegExp(rep.from), 'g');
        newFilename = newFilename.replace(regex, rep.to);
    });

    if (newFilename !== filename) {
        const newPath = path.join(dir, newFilename);
        fs.renameSync(oldPath, newPath);
        console.log(`Renamed: ${filename} -> ${newFilename}`);
    }
}

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

main();
