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
import * as tools from 'utils/tools';
import * as gridTemplates from 'templates/components/grid';
import * as inboxTemplates from 'templates/grids/inbox';

const el = {};
const instances = {
	grids: {},
	gridOptions: {},
	flyouts: {},
};
const globalOptions = {
	animateRows: false,
};
const data = gflowConfig?.grids || {};
const config = gflowConfig || {};

/**
 * @function intComparator
 * @description Function used by ag grid when sorting numeric columns
 *
 * @since 2.7.4
 *
 * @param {...*} args The args sent to this method, which are
 *                    {String}  The current column object.
 *                    {String}  The first value to compare.
 *                    {String}  The second value to compare.
 *                    {Object}  The node representing the first row.
 *                    {Object}  The node representing the second row.
 *                    {Boolean} Whether the current sort is inverted.
 *
 * @return {number} Either -1, 0, or 1.
 */
const intComparator = ( ...args ) => {
	const value1 = args[ 3 ].data[ args[ 0 ].sortKey ] ?? args[ 1 ];
	const value2 = args[ 4 ].data[ args[ 0 ].sortKey ] ?? args[ 2 ];

	return Math.sign( parseInt( value1 ) - parseInt( value2 ) );
};

/**
 * @function applyColumnComparator
 * @description Apply a comparator based on column type. String or int.
 *
 * @since 2.7.4
 *
 * @param {Object} column
 * @return {*}
 */

const applyColumnComparator = ( column ) => {
	if ( column.compareType === 'string' ) {
		return column;
	}

	column.comparator = intComparator.bind( null, column );

	return column;
};

/**
 * @function getGridId
 * @description Gets the grid id used to match options to instances in the dom.
 *
 * @since 2.7.4
 *
 * @param {HTMLElement} grid The wrapping container the grid is injected to
 * @return {string}
 */

export const getGridId = ( grid ) => grid.dataset.gridId || INBOX_DEFAULT_ID;

/**
 * @function getGridOptions
 * @description Merges global options with the instance specific options for a grid instance.
 *
 * @since 2.7.4
 *
 * @param {string} gridId The unique identifier for the grid instance.
 * @return {Object}
 */

export const getGridOptions = ( gridId = INBOX_DEFAULT_ID ) =>
	Object.assign( {}, data[ gridId ].grid_options, globalOptions );

/**
 * @function initializeGrid
 * @description Initializes an ag grid instance on the passed containing div.
 *
 * @since 2.7.4
 *
 * @param {HTMLElement} grid The grid container.
 */

export const initializeGrid = ( grid ) => {
	const gridId = getGridId( grid );

	if ( ! data[ gridId ]?.grid_options ) {
		console.error( `Cant find inbox options for grid id: ${ gridId }` );
	}

	instances.gridOptions[ gridId ] = getGridOptions( gridId );

	instances.gridOptions[ gridId ].getRowNodeId = ( row ) => {
		return parseInt( row.id );
	};

	instances.gridOptions[ gridId ].columnDefs.forEach( ( column, index ) => {
		instances.gridOptions[ gridId ].columnDefs[
			index
		] = applyColumnComparator( column );
	} );

	instances.grids[ gridId ] = new Grid(
		grid,
		instances.gridOptions[ gridId ]
	);
	initializeSearch( grid );
	initializeSettings( grid, gridId );

	const sortCol = config?.default_sort_col || 'none';
	const sortDir = config?.default_sort_dir || 'asc';

	if ( sortCol === 'none' ) {
		return;
	}

	const gridCol = instances.gridOptions[ gridId ].columnApi.getColumn(
		sortCol
	);

	if ( ! gridCol ) {
		return;
	}

	instances.gridOptions[ gridId ].columnApi.applyColumnState( {
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

/**
 * @function initializeSearch
 * @description Inject the search input for the whole table instance.
 */

const initializeSearch = ( grid ) => {
	grid.insertAdjacentHTML( 'afterbegin', inboxTemplates.header() );
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
 * @function applySearch
 * @description Apply a search term to the grid.
 */

const applySearch = ( e ) => {
	const input = e.delegateTarget;
	const gridContainer = tools.closest( input, '[data-js="gflow-inbox"]' );
	const gridId = getGridId( gridContainer );
	instances.gridOptions[ gridId ].api.setQuickFilter( input.value );
	console.log( 'hi' );
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

	delegate(
		document.body,
		'[data-js="gflow-inbox-search"]',
		'keyup',
		applySearch
	);
};

const init = ( containers ) => {
	el.containers = containers;

	initializeGrids();
	bindEvents();

	console.info( 'Gravity Flow Common: Initialized inbox components.' );
};

export default init;
