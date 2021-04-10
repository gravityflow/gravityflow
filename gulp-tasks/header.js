const gulp = require( 'gulp' );
const header = require( 'gulp-header' );
const pkg = require( '../package.json' );

module.exports = {
	adminIconsStyle() {
		return gulp.src( `${ pkg.gravityflow.paths.css_src }admin/base/_icons.pcss` )
			.pipe( header( `/* -----------------------------------------------------------------------------
 *
 * Admin Font Icons (via IcoMoon)
 *
 * This file is generated using the \`gulp icons\` task. Do not edit it directly.
 *
 * ----------------------------------------------------------------------------- */

` ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }admin/base/` ) );
	},
	adminIconsVariables() {
		return gulp.src( `${ pkg.gravityflow.paths.css_src }admin/variables/_icons.pcss` )
			.pipe( header( `/* -----------------------------------------------------------------------------
 *
 * Variables: Admin Icons (via IcoMoon)
 *
 * This file is generated using the \`gulp icons\` task. Do not edit it directly.
 *
 * ----------------------------------------------------------------------------- */

:root {` ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }admin/variables/` ) );
	},
	themeIconsStyle() {
		return gulp.src( `${ pkg.gravityflow.paths.css_src }theme/base/_icons.pcss` )
			.pipe( header( `/* -----------------------------------------------------------------------------
 *
 * Theme Font Icons (via IcoMoon)
 *
 * This file is generated using the \`gulp icons\` task. Do not edit it directly.
 *
 * ----------------------------------------------------------------------------- */

` ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }theme/base/` ) );
	},
	themeIconsVariables() {
		return gulp.src( `${ pkg.gravityflow.paths.css_src }theme/variables/_icons.pcss` )
			.pipe( header( `/* -----------------------------------------------------------------------------
 *
 * Variables: Theme Icons (via IcoMoon)
 *
 * This file is generated using the \`gulp icons\` task. Do not edit it directly.
 *
 * ----------------------------------------------------------------------------- */

:root {` ) )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.css_src }theme/variables/` ) );
	},
};
