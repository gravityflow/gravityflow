const gulp = require( 'gulp' );
const decompress = require( 'gulp-decompress' );
const pkg = require( '../package.json' );

module.exports = {
	icons() {
		return gulp.src( [
			'gflow-icons-common*.zip',
		] )
			.pipe( decompress() )
			.pipe( gulp.dest( `${ pkg.gravityflow.paths.dev }icons/common` ) );
	},
};
