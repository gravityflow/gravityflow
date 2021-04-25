/**
 * Inbox
 *
 * Initializes the inbox component
 */

import { Grid } from 'ag-grid-community';

const el = {};
const instances = {};

const gridOptions = window.gflow_config.grid_options || {};

const initializeGrid = () => {
	instances.grid = new Grid( el.container, gridOptions );
};

const init = ( container ) => {
	el.container = container;

	initializeGrid();

	console.info( 'Gravity Flow Common: Initialized inbox component.' );
};

export default init;
