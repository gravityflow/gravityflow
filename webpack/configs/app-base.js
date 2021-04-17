/**
 * External Dependencies
 */
const webpack = require( 'webpack' );
const ReactRefreshWebpackPlugin = require('@pmmmwh/react-refresh-webpack-plugin');

module.exports = {
	devtool: 'eval-source-map',
	devServer: {
		disableHostCheck: true,
		headers: {
			'Access-Control-Allow-Origin': '*',
		},
		hot: true,
		port: 3000,
	},
	plugins: [
		//new webpack.HashedModuleIdsPlugin(),
		new webpack.LoaderOptionsPlugin( {
			debug: true,
		} ),
    new ReactRefreshWebpackPlugin(), // TODO: Fix fast refresh
	],
	externals: {
		jquery: 'jQuery',
		'global-config': 'gravityflow_admin_config',
	},
	module: {
		rules: [
			{
				test: /\.pcss$/,
				use: [
					'style-loader',
					{
						loader: 'css-loader',
						options: {
							modules: {
								localIdentName: '[name]__[local]___[hash:base64:5]',
							},
							importLoaders: 1,
						},
					},
					'postcss-loader',
				],
			},
			{
				test: /\.css$/,
				include: /node_modules/,
				use: [ 'style-loader', 'css-loader' ],
			},
			{
				test: /\.js$|jsx/,
				exclude: /node_modules/,
				use: [
          {
            loader: require.resolve('babel-loader'),
            options: {
              plugins: [
								[
									require.resolve('react-refresh/babel'),
									{
										skipEnvCheck: true
									}
								]
              ],
            },
          },
				]
			}
		],
	},
	optimization: {
		noEmitOnErrors: true, // NoEmitOnErrorsPlugin
		concatenateModules: true, //ModuleConcatenationPlugin
		moduleIds: 'deterministic',
	},
};
