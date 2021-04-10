const scripts = require( './scripts' );
const styles = require( './styles' );

scripts.use.push( {
	loader: 'ifdef-loader',
	options: {
		'INCLUDEREACT': false,
		'version': 3,
		'ifdef-verbose': true,
		'ifdef-triple-slash': false,
	},
} );

module.exports = [
	scripts,
	styles,
];
