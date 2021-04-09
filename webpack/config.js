function module_exists( name ) {
	try {
		return require.resolve( name );
	} catch ( e ) {
		return false;
	}
}

const config = module_exists( '../config.json' ) ? require( '../config.json' ) : {
	pluginPath: '/wp-content/plugins/gravityflow/',
};

module.exports = config;
