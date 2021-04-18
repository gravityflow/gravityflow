/**
 * @function arrayToInt
 * @description Converts all array values to int.
 */

const arrayToInt = ( arr = [] ) => arr.map( ( x ) => parseInt( x, 10 ) );

export default arrayToInt;
