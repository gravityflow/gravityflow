/**
 * @module
 * @exports ready
 * @description The core dispatcher for the dom ready event javascript.
 */

import _ from 'lodash';
import { on, ready } from 'utils/events';
import applyBrowserClasses from 'utils/dom/apply-browser-classes';
// @EXAMPLE_REACT_APP

// import * as tools from 'utils/tools';
// import { HMR_DEV } from 'config/wp-settings';

// you MUST do this in every module you use lodash in.
// A custom bundle of only the lodash you use will be built by babel.

import resize from './resize';
import plugins from './plugins';
import viewportDims from './viewport-dims';

import common from 'common';

// @EXAMPLE_REACT_APP

// const el = {
// 	exampleAppRoot: tools.getNodes( 'example-app' )[ 0 ],
// };

/**
 * @function bindEvents
 * @description Bind global event listeners here,
 */

const bindEvents = () => {
	on( window, 'resize', _.debounce( resize, 200, false ) );
};

/**
 * @function init
 * @description The core dispatcher for init across the codebase.
 */

const init = () => {
	// apply browser classes

	applyBrowserClasses();

	// init external plugins

	plugins();

	// set initial states

	viewportDims();

	// initialize global events

	bindEvents();

	// initialize modules

	common();

	// @EXAMPLE_REACT_APP (Make sure to include the wrapping if block for ALL react apps

	// #if INCLUDEREACT
	// if ( el.exampleAppRoot && ! HMR_DEV ) {
	// 	import( 'Example' /* webpackChunkName:"example" */ );
	// }
	// #endif
	// }

	console.info(
		'GravityFlow Theme: Initialized all javascript that targeted document ready.'
	);
};

/**
 * @function domReady
 * @description Export our dom ready enabled init.
 */

const domReady = () => {
	ready( init );
};

export default domReady;
