/**
 * Grids
 *
 * Initializes all grids, eg inbox, status etc.
 */

import * as tools from 'utils/tools';

const el = {
	inboxes: tools.getNodes( 'gflow-inbox', true ),
};

/**
 * @function init
 * @description Initialize the module
 */

const init = () => {
	if ( el.inboxes.length ) {
		import( './inbox' /* webpackChunkName:"common-inbox" */ ).then(
			( module ) => {
				module.default( el.inboxes );
			}
		);
	}

	console.info( 'Gravity Flow Common: Initialized all common grids.' );
};

export default init;
