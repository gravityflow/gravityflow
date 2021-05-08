const gulp = require( 'gulp' );
const replace = require( 'gulp-replace' );
const pkg = require( '../package.json' );

module.exports = {
	commonIconsStyle() {
		return gulp.src( [
			`${ pkg.gravityflow.paths.css_src }common/base/_icons.pcss`,
		] )
			.pipe( replace( /url\('fonts\/(.+)'\) /g, 'url(\'../fonts/$1\') ' ) )
			.pipe( replace( / {2}/g, '\t' ) )
			.pipe( replace( /}$\n^\./gm, '}\n\n\.' ) )
			.pipe( replace( /'gflow-icons-common' !important/g, 'var(--t-font-family-common-icons) !important' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }common/base/` ) );
	},
	commonIconsVariables() {
		return gulp.src( [
			`${ pkg.gravityflow.paths.css_src }common/variables/_icons.pcss`,
		] )
			.pipe( replace( /(\\[a-f0-9]+);/g, '"$1";' ) )
			.pipe( replace( /\$icomoon-font-path: "fonts" !default;\n/g, '' ) )
			.pipe( replace( /\$icomoon-font-family: "gflow-icons-common" !default;\n/g, '' ) )
			.pipe( replace( /\$/g, '\t--' ) )
			.pipe( replace( /;\n\n$/m, ';\n' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }common/variables/` ) );
	},
};
