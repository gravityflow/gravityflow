const gulp = require( 'gulp' );
const replace = require( 'gulp-replace' );
const pkg = require( '../package.json' );

module.exports = {
	adminIconsStyle() {
		return gulp.src( [
			`${ pkg.gravityflow.paths.css_src }admin/base/_icons.pcss`,
		] )
			.pipe( replace( /url\('fonts\/(.+)'\) /g, 'url(\'../fonts/$1\') ' ) )
			.pipe( replace( / {2}/g, '\t' ) )
			.pipe( replace( /}$\n^\./gm, '}\n\n\.' ) )
			.pipe( replace( /'gflow-icons-admin' !important/g, 'var(--t-font-family-admin-icons) !important' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }admin/base/` ) );
	},
	adminIconsVariables() {
		return gulp.src( [
			`${ pkg.gravityflow.paths.css_src }admin/variables/_icons.pcss`,
		] )
			.pipe( replace( /(\\[a-f0-9]+);/g, '"$1";' ) )
			.pipe( replace( /\$icomoon-font-path: "fonts" !default;\n/g, '' ) )
			.pipe( replace( /\$icomoon-font-family: "gflow-icons-admin" !default;\n/g, '' ) )
			.pipe( replace( /\$/g, '\t--' ) )
			.pipe( replace( /;\n\n$/m, ';\n' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }admin/variables/` ) );
	},
	themeIconsStyle() {
		return gulp.src( [
			`${ pkg.gravityflow.paths.css_src }theme/base/_icons.pcss`,
		] )
			.pipe( replace( /url\('fonts\/(.+)'\) /g, 'url(\'../fonts/$1\') ' ) )
			.pipe( replace( / {2}/g, '\t' ) )
			.pipe( replace( /}$\n^\./gm, '}\n\n\.' ) )
			.pipe( replace( /'gflow-icons-theme' !important/g, 'var(--t-font-family-theme-icons) !important' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }theme/base/` ) );
	},
	themeIconsVariables() {
		return gulp.src( [
			`${ pkg.gravityflow.paths.css_src }theme/variables/_icons.pcss`,
		] )
			.pipe( replace( /(\\[a-f0-9]+);/g, '"$1";' ) )
			.pipe( replace( /\$icomoon-font-path: "fonts" !default;\n/g, '' ) )
			.pipe( replace( /\$icomoon-font-family: "gflow-icons-theme" !default;\n/g, '' ) )
			.pipe( replace( /\$/g, '\t--' ) )
			.pipe( replace( /;\n\n$/m, ';\n' ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }theme/variables/` ) );
	},
};
