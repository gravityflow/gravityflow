/**
 * Inbox
 *
 * Initializes the inbox component
 */

import { Grid } from 'ag-grid-community';

const el = {};
const instances = {};

const gridOptions = {
	columnDefs: [
		{ headerName: 'Make', field: 'make' },
		{ headerName: 'Model', field: 'model' },
		{ headerName: 'Price', field: 'price' },
	],
	rowData: [
		{ make: 'Toyota', model: 'Celica', price: 35000 },
		{ make: 'Ford', model: 'Mondeo', price: 32000 },
		{ make: 'Porsche', model: 'Boxter', price: 72000 },
	],
};

const initializeGrid = () => {
	instances.grid = new Grid( el.container, gridOptions );
};

const init = ( container ) => {
	el.container = container;

	initializeGrid();

	console.info( 'Gravity Flow Common: Initialized inbox component.' );
};

export default init;
