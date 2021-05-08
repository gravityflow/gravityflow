const gulp = require( 'gulp' );
const requireDir = require( 'require-dir' );
const tasks = requireDir( './gulp-tasks' );
const browserSync = require( 'browser-sync' ).create( 'Gravityflow Dev' );

function module_exists( name ) {
	try {
		return require.resolve( name );
	} catch ( e ) {
		return false;
	}
}

const config = module_exists( './config.json' )
	? require( './config.json' )
	: {
			proxy: 'gravity-forms.local',
			certsPath: '',
	  };

/**
 * List out your tasks as defined in the gulp_tasks directory
 * require-dir module will bring those in here as an object
 * Each task type object will be named by its filename
 * So: 'postcss:admin' means a file named 'postcss' in the gulp-tasks dir,
 * and the method admin inside that modules export.
 * You must follow this approach, or modify the registerTasks function below.
 *
 * @type {string[]}
 */

const gulpTasks = [
	/* Copy tasks */

	'copy:commonIconsFonts', // copy fonts for admin icons from dev folder to fonts directory
	'copy:commonIconsStyles', // copy styles for admin icons to pcss shared dir
	'copy:commonIconsVariables', // copy variables for admin icons to theme pcss variables dir

	/* Clean tasks */

	'clean:commonIconsStart', // delete all files related to icons in pcss, in prep for reinjection
	'clean:commonIconsEnd', // delete admin icon zip
	'clean:js', // clean chunks javascript

	/* Decompress tasks */

	'decompress:icons', // extract icomoon admin kit to dev directory

	/* Eslint tasks */

	'eslint:theme', // lint the theme js according to the products lint rules, uses fix to auto correct common issues
	'eslint:apps', // lint the apps js according to the products lint rules, uses fix to auto correct common issues
	'eslint:utils', // lint the utils js according to the products lint rules, uses fix to auto correct common issues
	'eslint:admin', // lint the admin js according to the products lint rules, uses fix to auto correct common issues

	/* Footer tasks */

	'footer:commonIconsVariables', // just adds a closing } to the admin icons variables file during the icons import transform tasks

	/* Header tasks */

	'header:commonIconsStyle', // sets the header for the admin icons style file in base during the icons import transform tasks
	'header:commonIconsVariables', // sets the header for the admin icons style file in vars during the icons import transform tasks

	/* Postcss tasks */

	'postcss:adminCss', // the postcss task that transforms admin css
	'postcss:themeCss', // the postcss task that transforms theme css

	/* Replace tasks */

	'replace:commonIconsStyle', // runs regex to replace and convert scss to pcss compatible with our systems in the icons task
	'replace:commonIconsVariables', // runs regex to replace and convert scss to pcss compatible with our systems in the icons task

	/* Shell tasks */

	'shell:test', // runs jests tests
	'shell:scriptsThemeDev', // runs webpack for the theme dev build
	'shell:scriptsThemeProd', // runs webpack for the theme prod build
	'shell:scriptsAdminDev', // runs webpack for the admin dev build
	'shell:scriptsAdminProd', // runs webpack for the admin prod build

	/* Stylelint tasks */

	'stylelint:admin', // lints and fixes the admin pcss
	'stylelint:theme', // lints and fixes the theme pcss
	'stylelint:common', // lints and fixes the common pcss modules
	'stylelint:apps', // lints and fixes the apps pcss modules

	/* Watch Tasks (THESE MUST BE LAST) */

	'watch:main', // watch all fe assets and run appropriate routines
	'watch:watchAdminJS', // watch admin js and run appropriate webpack tasks
	'watch:watchThemeJS', // watch theme js and run appropriate webpack tasks
];

/**
 * Iterate over the above array. Split on the colon to access the imported tasks array's
 * corresponding function for the current task in the loop
 */

function registerTasks() {
	gulpTasks.forEach( ( task ) => {
		const parts = task.split( ':' );
		gulp.task( task, tasks[ parts[ 0 ] ][ parts[ 1 ] ] );
	} );
}

/**
 * Register all tasks in the gulp_tasks directory
 */

registerTasks();

const watchTasks = [ 'watch:main', 'watch:watchAdminJS', 'watch:watchThemeJS' ];

gulp.task( 'watch', gulp.parallel( watchTasks ) );

/**
 * Lints js and css, fixed common issues automatically.
 */

gulp.task(
	'lint',
	gulp.series(
		gulp.parallel(
			'eslint:theme',
			'eslint:apps',
			'eslint:utils',
			'eslint:admin',
			'stylelint:admin',
			'stylelint:common',
			'stylelint:theme',
			'stylelint:apps'
		)
	)
);

/**
 * Tests js.
 */

gulp.task( 'test', gulp.series( gulp.parallel( 'shell:test' ) ) );

/**
 * Run linting and tests
 */

gulp.task( 'validate', gulp.series( gulp.parallel( 'lint', 'test' ) ) );

/**
 * Takes a zip file from icomoon and injects it into the postcss, modifying the scss to pcss and handling all conversions/cleanup.
 */

gulp.task(
	'icons',
	gulp.series(
		'clean:commonIconsStart',
		'decompress:icons',
		'copy:commonIconsFonts',
		'copy:commonIconsStyles',
		'copy:commonIconsVariables',
		'replace:commonIconsStyle',
		'replace:commonIconsVariables',
		'header:commonIconsStyle',
		'header:commonIconsVariables',
		'footer:commonIconsVariables',
		'clean:commonIconsEnd',
		'postcss:adminCss',
		'postcss:themeCss'
	)
);

/**
 * Watches all css and js, runs tasks and reloads browser using browsersync.
 */

gulp.task(
	'dev',
	gulp.parallel( watchTasks, async function () {
		browserSync.init( {
			watchTask: true,
			debugInfo: true,
			logConnections: true,
			notify: true,
			open: 'external',
			host: config.proxy,
			proxy: `https://${ config.proxy }`,
			https: {
				key: `${ config.certsPath }/${ config.proxy }.key`,
				cert: `${ config.certsPath }/${ config.proxy }.crt`,
			},
			ghostMode: {
				scroll: true,
				links: true,
				forms: true,
			},
		} );
	} )
);

/**
 * Builds the entire package for production locally
 */

gulp.task(
	'dist',
	gulp.series(
		gulp.parallel( 'lint', 'test' ),
		gulp.parallel( 'clean:js', 'postcss:adminCss', 'postcss:themeCss' ),
		gulp.parallel( 'shell:scriptsThemeDev', 'shell:scriptsAdminDev' )
	)
);

gulp.task( 'default', gulp.series( 'dist' ) );
