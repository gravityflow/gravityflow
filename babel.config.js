module.exports = {
	presets: [
		'@babel/preset-react',
		[
			'@babel/preset-env',
			{
				useBuiltIns: 'entry',
				modules: false,
				corejs: '3.1',
			},
		],
	],
	plugins: [
		'lodash',
		[
			'module-resolver',
			{
				root: [ '.' ],
				alias: {
					apps: './src/js/apps',
					'app-common': './src/js/apps/common',
					config: './src/js/admin/config',
					common: './src/js/common',
					Example: './src/js/apps/Example',
					pcss: './src/css/admin',
					utils: './src/js/utils',
				},
			},
		],

		'@babel/plugin-proposal-optional-chaining',
		'@babel/plugin-transform-runtime',
		'@babel/plugin-proposal-object-rest-spread',
		'@babel/plugin-syntax-dynamic-import',
		'@babel/plugin-transform-regenerator',
		'@babel/plugin-proposal-class-properties',
		'@babel/plugin-transform-object-assign',
	],
	env: {
		test: {
			presets: [
				[
					'@babel/preset-env',
					{
						useBuiltIns: 'entry',
						modules: 'commonjs',
						corejs: '3.1',
					},
				],
			],
			plugins: ['istanbul'],
		},
	},
};
