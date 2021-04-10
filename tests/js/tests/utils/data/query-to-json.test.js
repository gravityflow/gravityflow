import queryToJson from 'utils/data/query-to-json';

describe( 'queryToJson', () => {
	it( 'returns json object from a valid query string', () => {
		const query = 'param1=true&param2=1024&param3=';

		expect( queryToJson( query ) ).toMatchObject( {
			param1: 'true',
			param2: '1024',
			param3: '',
		} );
	} );

	it( 'returns json object from the current location query string', () => {
		jsdom.reconfigure( {
			url: 'http://localhost/?param1=true&param2=1024&param3=',
		} );

		expect( queryToJson() ).toMatchObject( {
			param1: 'true',
			param2: '1024',
			param3: '',
		} );
	} );

	it( 'returns empty json object from the current location if there isnt any query string', () => {
		jsdom.reconfigure( {
			url: 'http://localhost/',
		} );

		expect( queryToJson() ).toMatchObject( {} );
	} );
} );
