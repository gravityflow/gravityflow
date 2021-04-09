const gulp = require( 'gulp' );
const decompress = require( 'gulp-decompress' );
const pkg = require( '../package.json' );

module.exports = {
	adminIcons() {
		return gulp.src( [
			'gflow-icons-admin*.zip',
		] )
			.pipe( decompress() )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.dev }icons/admin` ) );
	},
	themeIcons() {
		return gulp.src( [
			'gflow--icons-theme*.zip',
		] )
			.pipe( decompress() )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.dev }icons/theme` ) );
	},
};
