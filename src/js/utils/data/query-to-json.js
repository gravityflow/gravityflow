const queryToJson = ( params = '' ) => {
	const query = params.length ? params : window.location.search.slice( 1 );
	const pairs = query.length ? query.split( '&' ) : [];
	const result = {};
	let pairArray = [];

	pairs.forEach( ( pair ) => {
		pairArray = pair.split( '=' );
		result[ pairArray[ 0 ] ] = decodeURIComponent( pairArray[ 1 ] || '' );
	} );

	return JSON.parse( JSON.stringify( result ) );
};

export default queryToJson;
