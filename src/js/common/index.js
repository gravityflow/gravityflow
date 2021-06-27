/**
 * Common
 *
 * Code shared between the theme and admin bundles.
 */

import grids from './grids';

/**
 * @function init
 * @description Initialize the module
 */

const init = () => {
	grids();

	console.info( 'Gravity Flow Common: Initialized all common scripts.' );
};

export default init;
