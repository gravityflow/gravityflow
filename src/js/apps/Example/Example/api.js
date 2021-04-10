import { retrieve } from 'app-common/api';

export function fetchExample() {
	return retrieve( 'https://jsonplaceholder.typicode.com', 'example', {
		method: 'GET',
	} );
}
