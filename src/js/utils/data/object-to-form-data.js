const objectToFormData = ( obj, rootName, ignoreList ) => {
	const formData = new window.FormData();

	function appendFormData( data, root ) {
		if ( ! ignore( root ) ) {
			root = root || '';
			if ( data instanceof window.File ) {
				formData.append( root, data );
			} else if ( Array.isArray( data ) ) {
				for ( let i = 0; i < data.length; i++ ) {
					appendFormData( data[ i ], root + '[' + i + ']' );
				}
			} else if ( typeof data === 'object' && data ) {
				for ( const key in data ) {
					if ( data.hasOwnProperty( key ) ) {
						if ( root === '' ) {
							appendFormData( data[ key ], key );
						} else {
							appendFormData( data[ key ], root + '.' + key );
						}
					}
				}
			} else if ( data !== null && typeof data !== 'undefined' ) {
				formData.append( root, data );
			}
		}
	}

	function ignore( root ) {
		return (
			Array.isArray( ignoreList ) &&
			ignoreList.some( ( x ) => x === root )
		);
	}

	appendFormData( obj, rootName );

	return formData;
};

export default objectToFormData;
