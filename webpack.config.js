const defaultConfigs = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

// Two configs due to experimental features
const newConfigs = defaultConfigs.map(config => ({
    ...config,
    entry: {
      'upload-song': './blocks/upload-song/src',
    },
}));

module.exports = newConfigs;