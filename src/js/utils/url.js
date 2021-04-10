/**
 * @module
 * @description Url utils
 */

import _ from 'lodash';
import queryToJson from './data/query-to-json';
import updateQueryVar from './data/update-query-var';

let query;
let url;

/**
 * @function getFilterString
 * @description Private function used to set or remove the term id from the comma separated FILTER_KEY.
 * @param {string} value the term id.
 * @param {boolean} remove or add the value?
 * @param {string} urlKey to update
 * @private
 */

const getFilterString = ( value, remove = false, urlKey ) => {
	query = queryToJson();
	const values = {}.hasOwnProperty.call( query, urlKey )
		? query[ urlKey ].split( ',' )
		: [];
	if ( remove ) {
		_.pull( values, value );
	} else {
		values.push( value );
	}
	return _.uniq( values ).join( ',' );
};

/**
 * @function updateUrl
 * @description Test for location history and store the query object in it while pushing the url.
 * @private
 */

const updateUrl = () => {
	if ( window.history ) {
		query = queryToJson();
		window.history.pushState( query, '', url );
	}
};

/**
 * @function addFilter
 * @description Adds a filter to the url.
 * @param {string} value the term id.
 */

const addFilter = ( value, key ) => {
	url = updateQueryVar( key, getFilterString( value, false, key ) );
	updateUrl();
};

/**
 * @function removeFilter
 * @description Removes a filter from the url.
 * @param {string} value the term id.
 */

const removeFilter = ( value, key ) => {
	const values = getFilterString( value, true, key );
	url = values.length ? updateQueryVar( key, values ) : updateQueryVar( key );
	updateUrl();
};

/**
 * @function removeAll
 * @description Removes all queries from the url.
 */

const removeAll = () => {
	url = url ? url.split( '?' )[ 0 ] : window.location.href.split( '?' )[ 0 ];
	updateUrl();
};

export { addFilter, removeFilter, removeAll };
