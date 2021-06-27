import config from 'gflow-config';
import { template } from 'lodash';

/**
 * Get your endpoints safely here first with optional chaining
 */

export const endpoints = {
	example: {
		path: config?.example?.api?.base_path,
		nonce: config?.example?.api?.nonce,
	},
};

/**
 * Now map them to action names and add templates as needed
 */

export const PATH_MAP = {
	example: {
		// Add product to a project
		// example:
		// request('example', { method: 'POST', restParams: { project_id: int }, json: { space: int, items: [{ post_id: int, configuration: '', quantity: int }] }}).then(data => console.log(data))
		// Accepted properties in the JSON body are
		// space (the (optional) ID of the selected space within the project) and
		// items, an array of example items.
		endpoint: template(
			`${ endpoints.example.path }/<%= project_id %>/items`
		),
		nonce: endpoints.example.nonce,
	},
};
