const { gravityflow: { paths } } = require( '../../package.json' );

module.exports = {
	'scripts-admin': [
		'core-js/modules/es.array.iterator',
		`./${ paths.js_src }admin/index.js`,
	],
};
