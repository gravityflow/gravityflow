import config from 'gflow-config';
import { PATH_MAP } from 'common/config/request';
import { is, omit, startsWith, isEmpty } from 'ramda';
import objectToFormData from 'utils/data/object-to-form-data';
import { stringify } from 'query-string';
import stripTags from 'underscore.string/stripTags';
import trim from 'underscore.string/trim';
import unescapeHTML from 'underscore.string/unescapeHTML';
import { pickBy, identity } from 'lodash';

/**
 * The request module abstracts away most of the pain of dealing with raw fetch.
 * Basic principles:
 * When a base_path needs variables in it, use the template function as seen below and pass those as "restParams" in your options
 * When you need query args appended for a get, pass them as "params" in your options object
 * When you want to POST set method: 'POST' and if passing json body set json: { data } in your options
 */

// reasonably generic post (or get)
// pathKey must be one of PATH_MAP
// if pathKey references a template function,
// then options should have a restParams key, which functions as a dictionary for the url template
// can also have a params key, which is a dict of queryParams
// you can also add other valid fetch options, to options (such as method and body)
export default async function request( pathKey, options = {} ) {
	const defaultOptions = { method: 'GET', ...options };
	const newOptions = omit( [ 'body' ], defaultOptions );

	if ( ! pathKey || ! PATH_MAP[ pathKey ] ) {
		throw new Error( `Unknown pathKey: ${ pathKey }` );
	}
	const host = newOptions.baseUrl || config.site_url;
	let path = PATH_MAP[ pathKey ].endpoint;
	if ( is( Function, path ) ) {
		path = PATH_MAP[ pathKey ].endpoint( {
			...options.body,
			...newOptions.restParams,
		} );
	}
	console.log( host );
	let url = `${ host }${ path }`;

	// encode as multipart/form-data
	if ( newOptions.method !== 'GET' && newOptions.method !== 'HEAD' ) {
		const parseBody = options.body ? options.body : {};
		newOptions.body = objectToFormData( parseBody );
	}

	// todo: the real fix here is to check the content-type and encode as required,
	// since body is already an associative array
	// override if POSTing json
	if ( newOptions.json ) {
		newOptions.body = JSON.stringify( newOptions.json );
	}

	// any params destined to become query parameters
	const params = newOptions.params || {};
	if ( params && ! isEmpty( params ) ) {
		const filteredParams = pickBy( params, identity );
		const p = stringify( filteredParams, { arrayFormat: 'bracket' } );
		url = `${ url }?${ p }`;
	}

	const initialHeaders = PATH_MAP[ pathKey ].nonce
		? {
				'X-WP-Nonce': PATH_MAP[ pathKey ].nonce,
				'Content-Type': 'application/json',
		  }
		: {};

	const headers = newOptions.headers
		? {
				...initialHeaders,
				...newOptions.headers,
		  }
		: initialHeaders;

	console.info( `Fetching url: ${ url }` );
	console.info( 'with options', { ...newOptions, body: newOptions.body } );
	console.info( 'and headers: ', headers );
	const start = Date.now();

	// do the fetch
	return window
		.fetch( url, { ...newOptions, headers } )
		.then( ( response ) => {
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
							totalPages: response.headers.get(
								'x-wp-totalpages'
							),
							totalPosts: response.headers.get( 'x-wp-total' ),
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

			// we get 40x's and 500's with valid json
			// we also seem to get errors with application/json which are actually html/text? (see #92408)
			// ie expected errors - need to make sure these get handled!!
			if (
				startsWith(
					response.headers.get( 'Content-Type' ),
					'application/json'
				)
			) {
				return response.text().then( ( text ) => {
					try {
						const data = JSON.parse( text );
						console.info( `Data for ${ pathKey }:`, data );
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
				const message = trim( stripTags( unescapeHTML( data ) ) );
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
			console.info( JSON.stringify( error ) );
			console.info( error.detail );
			return { error };
		} );
}
