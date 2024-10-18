const defaultConfigs = require('@wordpress/scripts/config/webpack.config');

// Two configs due to experimental features
defaultConfigs.forEach(config => ({
    ...config,
    entry: {
		'upload-song': './blocks/upload-song/src',
	},
}));

module.exports = defaultConfigs;