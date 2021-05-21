/**
 * @function uniqueId
 * @description Generate a unique id
 *
 * @param {string} prefix
 * @return {string}
 */

export default function uniqueId( prefix = 'id' ) {
	return `${ prefix }-${ Math.random().toString( 36 ).substr( 2, 9 ) }`;
}
