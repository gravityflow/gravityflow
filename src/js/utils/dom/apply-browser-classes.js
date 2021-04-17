/**
 * @function browserClasses
 * @description sets up browser classes on body without using user agent strings where possible.
 */

import * as test from '../tests';

const applyBrowserClasses = () => {
	const browser = test.browserTests();
	const classes = document.body.classList;

	if ( browser.android ) {
		classes.add( 'device-android' );
	} else if ( browser.ios ) {
		classes.add( 'device-ios' );
	}

	if ( browser.edge ) {
		classes.add( 'browser-edge' );
	} else if ( browser.chrome ) {
		classes.add( 'browser-chrome' );
	} else if ( browser.firefox ) {
		classes.add( 'browser-firefox' );
	} else if ( browser.ie ) {
		classes.add( 'browser-ie' );
	} else if ( browser.opera ) {
		classes.add( 'browser-opera' );
	} else if ( browser.safari ) {
		classes.add( 'browser-safari' );
	}
};

export default applyBrowserClasses;
