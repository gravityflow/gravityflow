/**
 * Element to slide gets the following CSS:
 *   max-height: 0;
 *   overflow: hidden;
 */

import _ from 'lodash';
import getHiddenHeight from './get-hidden-height';

const options = {
	timeoutDelay: 25,
};

const requestIds = [];

/**
 * Interpolated piece-wise function for CSS ease function using cubic-bezier(0.25, 0.1, 0.25, 1)
 * At t = 0, easeFxn(t) = 0
 * At t = 1, easeFxn(t) = 1
 * If t < 0.2074, easeFxn(t) = -3.8716*t^3 + 6.1370*t^2 + 0.4*t
 * If t >= 0.2074, easeFxn(t) = 1.1317*(t-1)^3 - 0.1975*(t-1)^2 + 1
 *
 * @param {number} t Progress from 0 to 1
 */
const easeFxn = ( t ) =>
	t < 0.2074
		? -3.8716 * t * t * t + 6.137 * t * t + 0.4 * t
		: 1.1317 * ( t - 1 ) * ( t - 1 ) * ( t - 1 ) -
		  0.1975 * ( t - 1 ) * ( t - 1 ) +
		  1;

/**
 * Check that request id exists, if not create an entry
 *
 * @param {string} id Unique ID of animation
 */
const checkRequestIds = ( id ) => {
	if ( ! requestIds[ id ] ) {
		requestIds[ id ] = {
			up: null,
			down: null,
		};
	}
};

/**
 * Cancel animations with request id
 *
 * @param {string} id Unique ID of animation
 */
const cancelAnimations = ( id ) => {
	if ( requestIds[ id ].up ) {
		window.cancelAnimationFrame( requestIds[ id ].up );
		requestIds[ id ].up = null;
	}
	if ( requestIds[ id ].down ) {
		window.cancelAnimationFrame( requestIds[ id ].down );
		requestIds[ id ].down = null;
	}
};

/**
 * Like jQuery's slideDown function
 *
 * @param {Node} elem Element to show and hide
 * @param {string} id Unique ID of animation
 * @param {number} time Length of animation
 * @param {Function} callback Callback function
 */
export const down = ( elem, id, time = 400, callback = null ) => {
	const startHeight = elem.offsetHeight;
	const endHeight = getHiddenHeight( elem );
	let startTime = null;
	elem.style.maxHeight = '0';

	checkRequestIds( id );
	cancelAnimations( id );

	const step = ( timestamp ) => {
		if ( ! startTime ) {
			startTime = timestamp;
		}
		const timeDiff = timestamp - startTime;
		const progress = easeFxn( timeDiff / time );
		const height = progress * ( endHeight - startHeight ) + startHeight;
		elem.style.maxHeight = `${ height }px`;

		if ( timeDiff < time ) {
			requestIds[ id ].down = window.requestAnimationFrame( step );
		} else {
			requestIds[ id ].down = null;
			elem.style.maxHeight = 'none';
			if ( callback ) {
				callback();
			}
		}
	};

	_.delay( () => {
		requestIds[ id ].down = window.requestAnimationFrame( step );
	}, options.timeoutDelay );
};

/**
 * Slide element up
 *
 * @param {Node} elem Element to show and hide
 * @param {string} id Unique ID of animation
 * @param {number} time Length of animation
 * @param {Function} callback Callback function
 */
export const up = ( elem, id, time = 400, callback = null ) => {
	const startHeight = elem.offsetHeight;
	const endHeight = 0;
	let startTime = null;
	elem.style.maxHeight = `${ startHeight }px`;

	checkRequestIds( id );
	cancelAnimations( id );

	const step = ( timestamp ) => {
		if ( ! startTime ) {
			startTime = timestamp;
		}
		const timeDiff = timestamp - startTime;
		const progress = easeFxn( timeDiff / time );
		const height = progress * ( endHeight - startHeight ) + startHeight;
		elem.style.maxHeight = `${ height }px`;

		if ( timeDiff < time ) {
			requestIds[ id ].up = window.requestAnimationFrame( step );
		} else {
			requestIds[ id ].up = null;
			elem.style.maxHeight = '0';
			if ( callback ) {
				callback();
			}
		}
	};

	_.delay( () => {
		requestIds[ id ].up = window.requestAnimationFrame( step );
	}, options.timeoutDelay );
};
