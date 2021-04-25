/**
 * Inbox
 *
 * Initializes the inbox component
 */

import { Grid } from 'ag-grid-community';

const el = {};
const instances = {};
const options = {
	animateRows: true,
	pagination: true,
	paginationPageSize: 10,
};
const data = window?.gflow_config?.grid_options || {};
const gridOptions = Object.assign( {}, data, options );

const initializeGrid = () => {
	instances.grid = new Grid( el.container, gridOptions );
};

const init = ( container ) => {
	el.container = container;

	initializeGrid();

	console.info( 'Gravity Flow Common: Initialized inbox component.' );
};

export default init;
