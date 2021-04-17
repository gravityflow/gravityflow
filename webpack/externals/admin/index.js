const wordpress = require( './wordpress' );
const vendor = require( './vendor' );

module.exports = {
	...wordpress,
	...vendor,
};
