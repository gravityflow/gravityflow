/**
 * @module
 * @description Some event functions for use in other modules
 */

import _ from 'lodash';

const on = ( el, name, handler ) => {
	if ( el.addEventListener ) {
		el.addEventListener( name, handler );
	} else {
		el.attachEvent( `on${ name }`, () => {
			handler.call( el );
		} );
	}
};

const ready = ( fn ) => {
	if ( document.readyState !== 'loading' ) {
		fn();
	} else if ( document.addEventListener ) {
		document.addEventListener( 'DOMContentLoaded', fn );
	} else {
		document.attachEvent( 'onreadystatechange', () => {
			if ( document.readyState !== 'loading' ) {
				fn();
			}
		} );
	}
};

const trigger = ( opts ) => {
	let event;
	const options = _.assign(
		{
			data: {},
			el: document,
			event: '',
			native: true,
		},
		opts
	);

	if ( options.native ) {
		event = document.createEvent( 'HTMLEvents' );
		event.initEvent( options.event, true, false );
	} else {
		try {
			event = new window.CustomEvent( options.event, {
				detail: options.data,
			} );
		} catch ( e ) {
			event = document.createEvent( 'CustomEvent' );
			event.initCustomEvent( options.event, true, true, options.data );
		}
	}

	options.el.dispatchEvent( event );
};

export { on, ready, trigger };
