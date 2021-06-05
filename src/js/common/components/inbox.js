/**
 * Inbox
 *
 * Initializes the inbox component
 */

import { Grid } from 'ag-grid-community';

import Flyout from './flyout';
import * as gridTemplates from 'templates/components/grid';

const el = {};
const instances = {};
const options = {
	animateRows: false,
};
const data = window?.gflow_config?.grid_options || {};
const config = window?.gflow_config || {};
const gridOptions = Object.assign( {}, data, options );

const initializeGrid = () => {
	gridOptions.getRowNodeId = ( row ) => {
		return parseInt( row.id );
	};

	instances.grid = new Grid( el.container, gridOptions );

	const sortCol = config?.default_sort_col || 'none';
	const sortDir = config?.default_sort_dir || 'asc';

	if ( sortCol === 'none' ) {
		return;
	}

	const gridCol = gridOptions.columnApi.getColumn( sortCol );

	if ( ! gridCol ) {
		return;
	}

	gridOptions.columnApi.applyColumnState( {
		state: [
			{
				colId: sortCol,
				sort: sortDir,
			},
		],
	} );
};

/**
 * @function initializeSettings
 * @description Inject the settings trigger and instantiate the flyout
 */

const initializeSettings = () => {
	el.container.insertAdjacentHTML(
		'afterbegin',
		gridTemplates.settingToggle(
			'inbox-settings',
			'Toggle settings for this table'
		)
	);
	instances.settingsFlyout = new Flyout( {
		triggers: '[data-js="inbox-settings"]',
	} );
};

const getIdsFromModel = () => {
	const ids = [];
	gridOptions.api.forEachNode( ( node ) => ids.push( node.data.id ) );
	return ids;
};

const refreshGrid = async () => {
	const current_ids = getIdsFromModel();
	const formData = new window.FormData();

	current_ids.forEach( ( item ) => formData.append( 'current_ids[]', item ) );
	formData.append(
		'gflow_access_token',
		window?.gflow_config?.current_user_token || null
	);

	const response = await window.fetch( '/wp-json/gf/v2/refresh_inbox_items', {
		method: 'post',
		body: formData,
	} );

	const responseJson = await response.json();

	gridOptions.api.applyTransaction( responseJson );
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

	initializeGrid();
	initializeSettings();
	bindEvents();

	console.info( 'Gravity Flow Common: Initialized inbox component.' );
};

export default init;
