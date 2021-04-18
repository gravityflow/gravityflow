const del = require( 'del' );
const pkg = require( '../package.json' );

const getIconPaths = ( target = 'admin' ) => ([
	`${ pkg.gravityflow.paths.root }/dev/icons/${ target }`,
	`${ pkg.gravityflow.paths.fonts }gform-icons-${ target }.*`,
	`${ pkg.gravityflow.paths.css_src }${ target }/base/_icons.pcss`,
	`${ pkg.gravityflow.paths.css_src }${ target }/variables/_icons.pcss`,
]);

module.exports = {
	adminIconsStart() {
		return del( getIconPaths() );
	},
	adminIconsEnd() {
		return del( [
			'gflow-icons-admin*.zip',
		] );
	},
	themeIconsStart() {
		return del( getIconPaths( 'theme' ) );
	},
	themeIconsEnd() {
		return del( [
			'gflow-icons-theme*.zip',
		] );
	},
};
