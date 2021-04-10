/**
 * Internal Dependencies
 */
const { gravityflow: { paths } } = require( '../../package.json' );

module.exports = {
	'scripts-theme': [
		'core-js/modules/es.array.iterator',
		`./${ paths.js_src }theme/index.js`,
	],
};
