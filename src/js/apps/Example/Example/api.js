import { retrieve } from 'common/api';

export function fetchExample() {
	return retrieve( 'https://jsonplaceholder.typicode.com', 'example', {
		method: 'GET',
	} );
}
