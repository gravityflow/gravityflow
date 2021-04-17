/**
 * @function set_acc_active_attributes
 * @param {HTMLElement} target The domnode to modify.
 * @param {HTMLElement} content The domnode to modify.
 * @description Set the active aria attributes for accessibility on an accordion/toggle.
 */

const setAccActiveAttributes = ( target, content ) => {
	target.setAttribute( 'aria-expanded', 'true' );
	target.setAttribute( 'aria-selected', 'true' );

	content.setAttribute( 'aria-hidden', 'false' );
};

/**
 * @function set_acc_inactive_attributes
 * @param {HTMLElement} target The domnode to modify.
 * @param {HTMLElement} content The domnode to modify.
 * @description Set the inactive aria attributes for accessibility on an accordion/toggle.
 */

const setAccInactiveAttributes = ( target, content ) => {
	target.setAttribute( 'aria-expanded', 'false' );
	target.setAttribute( 'aria-selected', 'false' );

	content.setAttribute( 'aria-hidden', 'true' );
};

export { setAccActiveAttributes, setAccInactiveAttributes };
