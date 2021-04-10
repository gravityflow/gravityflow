const { NODE_ENV } = process.env;
module.exports = require( `./webpack/${ NODE_ENV }` );