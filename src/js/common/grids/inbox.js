/**
 * Inbox
 *
 * Initializes the inbox component
 */

import { Grid } from 'ag-grid-community';
import delegate from 'delegate';
import gflowConfig from 'gflow-config';
import request from 'utils/request';

import Flyout from 'common/components/flyout';
import { INBOX_DEFAULT_ID } from 'common/config/constants';
import * as gridTemplates from 'templates/components/grid';
import * as inboxTemplates from 'templates/grids/inbox';

const el = {};
const instances = {
	grids: {},
	flyouts: {},
};
const globalOptions = {
	animateRows: false,
};
const data = gflowConfig?.grids || {};
const config = gflowConfig || {};

let gridId;

const intComparator = ( ...args ) => {
	const value1 = args[ 3 ].data[ args[ 0 ].sortKey ] ?? args[ 1 ];
	const value2 = args[ 4 ].data[ args[ 0 ].sortKey ] ?? args[ 2 ];

	return Math.sign( parseInt( value1 ) - parseInt( value2 ) );
};

const applyColumnComparator = ( column ) => {
	if ( column.compareType === 'string' ) {
		return column;
	}

	column.comparator = intComparator.bind( null, column );

	return column;
};

const initializeGrid = ( grid ) => {
	gridId = grid.dataset.gridId || INBOX_DEFAULT_ID;

	if ( ! data[ gridId ]?.grid_options ) {
		console.error( `Cant find inbox options for grid id: ${ gridId }` );
	}

	const gridOptions = Object.assign(
		{},
		data[ gridId ].grid_options,
		globalOptions
	);

	gridOptions.getRowNodeId = ( row ) => {
		return parseInt( row.id );
	};

	gridOptions.columnDefs.forEach( ( column, index ) => {
		gridOptions.columnDefs[ index ] = applyColumnComparator( column );
	} );

	instances.grids[ gridId ] = new Grid( grid, gridOptions );

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

	initializeSettings( grid, gridId );
};

/**
 * @function initializeSettings
 * @description Inject the settings trigger and instantiate the flyout
 */

const initializeSettings = ( grid, gridId ) => {
	grid.insertAdjacentHTML(
		'afterbegin',
		gridTemplates.settingToggle(
			'inbox-settings',
			'Toggle settings for this table' // todo: needs i18n
		)
	);
	instances.flyouts[ gridId ] = new Flyout( {
		content: inboxTemplates.settings(),
		position: 'absolute',
		target: '.gflow-inbox.gflow-grid',
		title: 'Inbox Settings',
		triggers: '[data-js="inbox-settings"]',
		wrapperClasses: 'gform-flyout gform-flyout--inbox-settings',
	} );
};

const getIdsFromModel = () => {
	const ids = [];
	data[ INBOX_DEFAULT_ID ].grid_options.api.forEachNode( ( node ) =>
		ids.push( node.data.id )
	);
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

	console.log( request );

	const response = await window.fetch( '/wp-json/gf/v2/refresh_inbox_items', {
		method: 'post',
		body: formData,
	} );

	const responseJson = await response.json();

	data[ INBOX_DEFAULT_ID ].grid_options.api.applyTransaction( responseJson );
};

/**
 * @function handleSettingsChange
 * @description Handle changes to the settings for the inbox
 */

const handleSettingsChange = ( e ) => {
	// switch based on setting name to handle cases, name is also value stored and passed in on init from php in config
	console.log( e.delegateTarget.name );
};

/**
 * @function initializeGrids
 * @description Iterate over all found grids and initialize.
 */

const initializeGrids = () => {
	el.containers.forEach( ( grid ) => initializeGrid( grid ) );
};

const bindEvents = () => {
	const refreshButton = document.querySelector( '[data-js="refresh_inbox"]' );
	refreshButton.addEventListener( 'click', function ( e ) {
		e.preventDefault();
		refreshGrid();
	} );

	delegate(
		document.body,
		'[data-js="inbox-setting"]',
		'change',
		handleSettingsChange
	);
};

const init = ( containers ) => {
	el.containers = containers;

	initializeGrids();
	bindEvents();

	console.info( 'Gravity Flow Common: Initialized inbox components.' );
};

export default init;
