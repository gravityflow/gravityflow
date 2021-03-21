/**
 * @function
 * @description Check if a url is to a file
 */

const isFileUrl = ( url = '' ) => {
	const ext = url.split( '/' ).pop();
	return ext.indexOf( '.' ) !== -1;
};

export default isFileUrl;
