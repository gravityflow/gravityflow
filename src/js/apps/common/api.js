import template from 'lodash/template';
import reduce from 'lodash/reduce';
import isFunction from 'lodash/isFunction';
import omit from 'lodash/omit';
import isEmpty from 'lodash/isEmpty';
import toFormData from 'object-to-formdata';
import { stringify } from 'query-string';
import stripTags from 'voca/strip_tags';
import trim from 'voca/trim';
import unescapeHTML from 'voca/unescape_html';

const PATH_MAP = {
	exampleMultiple: template( '/todos/<%= count %>' ),
	example: '/todos/1',
};

// reasonably generic post (or get)
// pathKey must be one of PATH_MAP
// if pathKey references a template function,
// then options should have a restParms key, which functions as a dictionary for the url template
// can also have a params key, which is a dict of queryParams
// you can also add other valid fetch options, to options (such as method and body)

export function retrieve( host, pathKey, options ) {
	const defaultOptions = { method: 'GET', ...options };
	const newOptions = omit( defaultOptions, 'body' );

	// determine full path, using template if needed
	if ( ! pathKey || ! PATH_MAP[ pathKey ] ) {
		throw new Error( `Unknown pathKey: ${ pathKey }` );
	}
	let path = PATH_MAP[ pathKey ];
	if ( isFunction( path ) ) {
		path = PATH_MAP[ pathKey ]( {
			...options.body,
			...newOptions.restParms,
		} );
	}
	let url = `${ host }${ path }`;

	// encode as multipart/form-data
	if ( newOptions.method !== 'GET' && newOptions.method !== 'HEAD' ) {
		const parseBody = options.body ? options.body : {};
		newOptions.body = toFormData( parseBody );
	}

	if ( newOptions.json ) {
		newOptions.body = JSON.stringify( newOptions.json );
	}

	// any params destined to become query parameters
	const params = reduce(
		newOptions.params,
		( mem, param, key ) => {
			const newMem = { ...mem };
			newMem[ key ] = param;
			return param ? newMem : mem;
		},
		{}
	);
	if ( params && ! isEmpty( params ) ) {
		const p = stringify( params, { arrayFormat: 'bracket' } );
		url = `${ url }?${ p }`;
	}

	console.info( `Fetching url: ${ url }` );
	console.info( 'with options', { ...newOptions, body: newOptions.body } );
	const start = Date.now();

	// do the fetch
	return window
		.fetch( url, { ...newOptions } )
		.then( ( response ) => {
			console.info( response );
			if ( response.ok ) {
				return response.text().then( ( text ) => {
					try {
						const data = JSON.parse( text );
						const time = Date.now() - start;
						console.info(
							`Data for ${ pathKey } in ${ time }ms:`,
							data
						);

						return {
							data,
							status: response.status,
						};
					} catch ( error ) {
						const message = trim(
							stripTags( unescapeHTML( text ) )
						);
						const err = new Error(
							`Invalid server response. ${ message }`
						);
						err.detail = {
							url,
							data: message,
							status: response.status,
							error,
						};
						throw err;
					}
				} );
			}

			// error
			return response.text().then( ( data ) => {
				const message = trim(
					stripTags( unescapeHTML( JSON.parse( data ).error ) )
				);
				const err = new Error(
					`Unknown server response. ${ message }`
				);
				err.detail = {
					url,
					data: message,
					status: response.status,
				};
				throw err;
			} );
		} )
		.catch( ( error ) => {
			const message = error.detail;
			const err = new Error( `Unknown server response. ${ message }` );
			err.detail = {
				url,
				data: message,
				status: error.status,
			};
			throw err;
		} );
}
