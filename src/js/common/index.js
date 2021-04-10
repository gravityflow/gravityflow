/**
 * Common
 *
 * Code shared between the theme and admin bundles.
 */

import components from './components';

/**
 * @function init
 * @description Initialize the module
 */

const init = () => {
	components();

	console.info( 'Gravity Flow Common: Initialized all common scripts.' );
};

export default init;
