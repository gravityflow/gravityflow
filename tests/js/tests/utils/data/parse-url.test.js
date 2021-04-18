import parseUrl from 'utils/data/parse-url';

describe( 'updateQueryVar', () => {
	it( 'parse the given url into its components', () => {
		const url = 'http://username:password@hostname/path?arg=value#anchor';

		expect( parseUrl( url ) ).toMatchObject( {
			fragment: 'anchor',
			host: 'hostname',
			pass: 'password',
			path: '/path',
			query: 'arg=value',
			scheme: 'http',
			source: null,
			user: 'username',
		} );
	} );

	it( 'return the specified component from the specified url', () => {
		const url = 'http://username:password@hostname/path?arg=value#anchor';
		expect( parseUrl( url, 'scheme' ) ).toBe( 'http' );
	} );
} );
