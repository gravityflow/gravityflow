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
	gridOptions.getRowNodeId = ( node ) => {
		return parseInt( node.id );
	};

	instances.grid = new Grid( el.container, gridOptions );
};

const getIdsFromModel = () => {
	const ids = [];
	gridOptions.api.forEachNode( ( node ) => ids.push( node.data.id ) );
	return ids;
};

const refreshGrid = async () => {
	const formData = new window.FormData();

	getIdsFromModel().forEach( ( item ) =>
		formData.append( 'current_ids[]', item )
	);

	const response = await window.fetch(
		'/wp-admin/admin-ajax.php?action=gflow_inbox_refresh_inbox_items',
		{
			method: 'post',
			body: formData,
		}
	);

	const items = await response.json();

	gridOptions.api.applyTransaction( items );
};

const bindEvents = () => {
	const refreshButton = document.querySelector( '[data-js="refresh_inbox"]' );
	refreshButton.addEventListener( 'click', function ( e ) {
		e.preventDefault();
		refreshGrid();
	} );
};

const init = ( container ) => {
	el.container = container;

	bindEvents();
	initializeGrid();

	console.info( 'Gravity Flow Common: Initialized inbox component.' );
};

export default init;
