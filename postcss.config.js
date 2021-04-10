const postcssFunctions = require( './assets/library/theme/pcss/functions' );
const pkg = require( './package.json' );

module.exports = ( { file, options, env } = {} ) => ( { // eslint-disable-line no-unused-vars
	plugins: {
		'postcss-import-ext-glob': { sort: 'asc' },
		'postcss-import': {
			path: [
				`./${ pkg.square1.paths.core_theme }`,
			],
		},
		'postcss-mixins': {},
		'postcss-custom-properties': { preserve: false },
		'postcss-simple-vars': {},
		'postcss-custom-media': {},
		'postcss-functions': { functions: postcssFunctions },
		'postcss-quantity-queries': {},
		'postcss-aspect-ratio': {},
		'postcss-nested': {},
		'postcss-inline-svg': {},
		'postcss-preset-env': { stage: 0, autoprefixer: { grid: true } },
		'postcss-calc': {},
		'postcss-assets': {
			baseUrl: pkg.square1.paths.core_theme_postcss_assets_base_url,
		},
	},
} );
