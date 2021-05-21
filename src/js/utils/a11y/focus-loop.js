import * as tools from 'utils/tools';

/**
 * @function focusLoop
 * @description Loop through focusable els inside a container. Bound to a keydown listener usually.
 *
 * @param {KeyboardEvent} e
 * @param {HTMLElement} trigger
 * @param {HTMLElement} container
 */

export default function focusLoop( e = {}, trigger = null, container = null ) {
	if ( ! container || ! trigger ) {
		console.error(
			'You need to pass a container and trigger node to focusLoop.'
		);
		return;
	}
	// esc key, refocus the settings trigger in the editor preview for the active field
	if ( e.keyCode === 27 ) {
		trigger.focus();
		return;
	}
	// not tab key, exit
	if ( e.keyCode !== 9 ) {
		return;
	}
	// get visible focusable items
	const focusable = tools.getFocusable( container );
	// store first and last visible item
	const firstFocusableEl = focusable[ 0 ];
	const lastFocusableEl = focusable[ focusable.length - 1 ];

	// shiftkey was involved, we're going backwards, focus last el if we are leaving first
	if ( e.shiftKey ) {
		/* shift + tab */
		if ( document.activeElement === firstFocusableEl ) {
			lastFocusableEl.focus();
			e.preventDefault();
		}
		// regular tabbing direction, bring us back to first el at reaching end
	} /* tab */ else if ( document.activeElement === lastFocusableEl ) {
		firstFocusableEl.focus();
		e.preventDefault();
	}
}
