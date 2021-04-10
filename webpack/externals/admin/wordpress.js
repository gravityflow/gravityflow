const camelCase = require( 'lodash/camelCase' );

const wp = [
	'api-fetch',
	'blob',
	'block-editor',
	'blocks',
	'components',
	'compose',
	'data',
	'date',
	'editor',
	'element',
	'hooks',
	'i18n',
	'keycodes',
	'server-side-render',
	'utils',
	'viewport',
];

const createNamespace = ( dependency ) => {
	if ( dependency.includes( '-' ) ) {
		return camelCase( dependency );
	}
	return dependency;
};

// Puts @wordpress/[dependency] on the window object as window.wp.[dependency]
const externals = wp.reduce(
	( result, dependency ) => {
		const namespace = createNamespace( dependency );

		result[ `@wordpress/${ dependency }` ] = {
			var: `wp.${ namespace }`,
			root: [ 'wp', namespace ],
		};
		return result;
	},
	{}
);

module.exports = externals;
