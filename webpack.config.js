const defaultConfig = require('@wordpress/scripts/config/webpack.config');

const exportEntries = [];
exportEntries.push({
	...defaultConfig,
	devServer: {
		...defaultConfig.devServer,
		allowedHosts: [
			'plugins.local',    // Allows your custom local domain
		],
	}
});


module.exports = [...exportEntries];
