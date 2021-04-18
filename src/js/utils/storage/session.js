const put = ( key, value ) => {
	window.sessionStorage.setItem( key, value );
};

const get = ( key ) => window.sessionStorage.getItem( key );

const remove = ( key ) => window.sessionStorage.removeItem( key );

const clear = () => {
	window.sessionStorage.clear();
};

export { put, get, remove, clear };
