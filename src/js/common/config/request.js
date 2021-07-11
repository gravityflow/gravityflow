import config from 'gflow-config';
import { template } from 'lodash';

/**
 * Get your endpoints safely here first with optional chaining
 */

export const endpoints = config?.endpoints;

/**
 * Now map them to action names and add templates as needed
 */

const getPathMap = () => {
	const map = {};

	// Add product to a project
	// example:
	// request('example', { method: 'POST', restParams: { project_id: int }, json: { space: int, items: [{ post_id: int, configuration: '', quantity: int }] }}).then(data => console.log(data))
	// Accepted properties in the JSON body are
	// space (the (optional) ID of the selected space within the project) and
	// items, an array of example items.
	for ( const endpoint in endpoints ) {
		const data = endpoints[ endpoint ];
		map[ endpoint ] = {
			endpoint: template( `${ data.path }${ data.rest_params }` ),
			nonce: data.nonce,
		};
	}

	return map;
};

export const PATH_MAP = getPathMap();
