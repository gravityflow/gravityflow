const wordpress = require( './wordpress' );
const vendor = require( './vendor' );

module.exports = {
	'gflow-config': 'gflow_config',
	...wordpress,
	...vendor,
};
