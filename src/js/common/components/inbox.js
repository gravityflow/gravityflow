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
	gridOptions.getRowNodeId = ( data ) => {
		return parseInt( data.id );
	};

	instances.grid = new Grid( el.container, gridOptions );
};

const getIdsFromModel = () => {
	let ids = [];

	gridOptions.api.forEachNode((node) => ids.push(node.data.id) );

	return ids;
};

const refreshGrid = async () => {
	const current_ids = getIdsFromModel();
	const formData = new FormData();

	current_ids.forEach((item) => formData.append( 'current_ids[]', item ) );

	const response = await fetch(
		'/wp-admin/admin-ajax.php?action=gflow_inbox_refresh_inbox_items',
		{
			method: 'post',
			body: formData
		}
	);

	const data = await response.json();

	gridOptions.api.applyTransaction(data);
};

const addEventListeners = () => {
	const refreshButton = document.querySelector( '[data-js="refresh_inbox"]' );
	refreshButton.addEventListener( 'click', function( e ) {
		e.preventDefault();
		refreshGrid();
	});
};

const init = ( container ) => {
	el.container = container;

	initializeGrid();

	addEventListeners();

	console.info( 'Gravity Flow Common: Initialized inbox component.' );
};

export default init;
