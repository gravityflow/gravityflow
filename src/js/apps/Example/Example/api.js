import request from 'utils/request';

export function fetchExample() {
	return request( 'example', {
		baseUrl: 'https://jsonplaceholder.typicode.com',
		method: 'GET',
	} );
}
