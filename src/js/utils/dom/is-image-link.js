/**
 * @function
 * @description Check is a url string passed in is an image link
 */

const isImageUrl = ( url = '' ) => {
	const ext = url.split( '.' ).pop();
	const test = ext.toLowerCase().match( /(jpg|jpeg|png|gif)/g );
	return test && test.length > 0;
};

export default isImageUrl;
