const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

module.exports = {
	test: /\.p?css$/,
	use: [
		MiniCssExtractPlugin.loader,
		{
			loader: 'css-loader',
			options: {
				sourceMap: true,
			},
		},
		{
			loader: 'postcss-loader',
			options: {
				sourceMap: true,
			},
		},
	],
};
