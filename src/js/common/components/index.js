/**
 * Components
 *
 * Initializes all common components
 */

import * as tools from 'utils/tools';

const el = {
	inbox: tools.getNodes( 'gflow-inbox' )[ 0 ],
};

/**
 * @function init
 * @description Initialize the module
 */

const init = () => {
	if ( el.inbox ) {
		import( './inbox' /* webpackChunkName:"common-inbox" */ ).then(
			( module ) => {
				module.default( el.inbox );
			}
		);
	}

	console.info( 'Gravity Flow Common: Initialized all common components.' );
};

export default init;
