const put = ( key, value ) => {
	window.window.localStorage.setItem( key, value );
};

const get = ( key ) => window.window.localStorage.getItem( key );

const remove = ( key ) => window.window.localStorage.removeItem( key );

const clear = () => {
	window.window.localStorage.clear();
};

export { put, get, remove, clear };
