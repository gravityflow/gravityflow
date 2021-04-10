module.exports = {
	scTheme: {
		minSize: 2000,
		cacheGroups: {
			vendor: {
				test: /[\\/]node_modules[\\/]/,
				name: 'vendor-theme',
				chunks: 'all',
			},
		},
	},
	scAdmin: {
		minSize: 2000,
		cacheGroups: {
			vendor: {
				test: /[\\/]node_modules[\\/]/,
				name: 'vendor-admin',
				chunks: 'all',
			},
		},
	},
};
