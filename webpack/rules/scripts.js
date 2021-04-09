module.exports = {
	test: /\.js$/,
	exclude: [ /node_modules\/(?!(swiper|dom7)\/).*/ ],
	use: [
		{
			loader: 'babel-loader',
		},
	],
};
