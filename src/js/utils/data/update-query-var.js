function updateQueryVar( key, value, url = window.location.href ) {
	const separator = '?';

	const hashSplit = url.split( '#' );
	const hash = hashSplit[ 1 ] ? `#${ hashSplit[ 1 ] }` : '';
	const querySplit = hashSplit[ 0 ].split( '?' );
	const host = querySplit[ 0 ];
	const query = querySplit[ 1 ];
	const params = query !== undefined ? query.split( '&' ) : [];
	let updated = false;

	params.forEach( ( item, index ) => {
		if ( item.startsWith( `${ key }=` ) ) {
			updated = true;
			params[ index ] = `${ key }=${ value }`;
		}
	} );

	if ( ! updated ) {
		params[ params.length ] = `${ key }=${ value }`;
	}

	return `${ host }${ separator }${ params.join( '&' ) }${ hash }`;
}

export default updateQueryVar;
