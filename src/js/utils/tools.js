/**
 * @module
 * @description Some vanilla js cross browser utils
 */

/**
 * Add a class to a dom element or exit safely if not set
 *
 * @param el Node
 * @param className Class string
 * @return {*} Node or false
 */

export const addClass = ( el, className = '' ) => {
	const element = el;
	if ( ! element ) {
		return false;
	}

	element.classList.add( className );
	return element;
};

/**
 *
 * Get immediate child nodes and return an array of them
 *
 * @param el
 * @return {Array} Iterable array of dom nodes
 */

export const getChildren = ( el ) => {
	const children = [];
	let i = el.children.length;
	for (i; i--;) { // eslint-disable-line
		if ( el.children[ i ].nodeType !== 8 ) {
			children.unshift( el.children[ i ] );
		}
	}

	return children;
};

/**
 *
 * Test if a dom node has a class or returns false if el not defined
 *
 * @param el
 * @param className
 * @return {boolean}
 */

export const hasClass = ( el, className = '' ) => {
	if ( ! el ) {
		return false;
	}

	return el.classList.contains( className );
};

/**
 * Removes a class from the dom node
 *
 * @param el
 * @param className
 * @return {*} returns false or el if passed
 */

export const removeClass = ( el, className ) => {
	const element = el;
	if ( ! element ) {
		return false;
	}

	element.classList.remove( className );
	return element;
};

/**
 * Remove a class from an element that contains a string
 *
 * @param el
 * @param string
 */

export const removeClassThatContains = ( el, string = '' ) => {
	for ( let i = 0; i < el.classList.length; i++ ) {
		if ( el.classList.item( i ).indexOf( string ) !== -1 ) {
			el.classList.remove( el.classList.item( i ) );
		}
	}
};

/**
 * Compares an els classList against an array of strings to see if any match
 *
 * @param el the element to check against
 * @param arr The array of classes as strings to test against
 * @param prefix optional prefix string applied to all test strings
 * @param suffix optional suffix string
 */

export const hasClassFromArray = ( el, arr = [], prefix = '', suffix = '' ) =>
	arr.some( ( c ) =>
		el.classList.contains( `${ prefix }${ c }${ suffix }` )
	);

/**
 * Highly efficient function to convert a nodelist into a standard array. Allows you to run Array.forEach
 *
 * @param {Element|NodeList} elements to convert
 * @return {Array} Of converted elements
 */

export const convertElements = ( elements = [] ) => {
	const converted = [];
	let i = elements.length;
	for (i; i--; converted.unshift(elements[i])); // eslint-disable-line

	return converted;
};

/**
 * Should be used at all times for getting nodes throughout our app. Please use the data-js attribute whenever possible
 *
 * @param selector The selector string to search for. If arg 4 is false (default) then we search for [data-js="selector"]
 * @param convert Convert the NodeList to an array? Then we can Array.forEach directly. Uses convertElements from above
 * @param node Parent node to search from. Defaults to document
 * @param custom Is this a custom selector where we don't want to use the data-js attribute?
 * @return {NodeList}
 */

export const getNodes = (
	selector = '',
	convert = false,
	node = document,
	custom = false
) => {
	const selectorString = custom ? selector : `[data-js="${ selector }"]`;
	let nodes = node.querySelectorAll( selectorString );
	if ( convert ) {
		nodes = convertElements( nodes );
	}
	return nodes;
};

/**
 * Util to see if we should load a given chunk on a page. Just add data-load-chunk-products to load that particular
 * one on any element on the page.
 * Handled in assets/js/src/theme/core/components.js and assets/js/src/theme/core/pages.js
 *
 * @param name chunk name
 */

export const shouldLoadChunk = ( name = '' ) => {
	const node = document.querySelectorAll( `[data-load-chunk-${ name }]` );
	return node.length > 0;
};

/**
 * Gets the closest ancestor that matches a selector string
 *
 * @param el
 * @param selector
 * @return {*}
 */

export const closest = ( el, selector ) => {
	let matchesFn;
	let parent;

	[
		'matches',
		'webkitMatchesSelector',
		'mozMatchesSelector',
		'msMatchesSelector',
		'oMatchesSelector',
	].some( ( fn ) => {
		if ( typeof document.body[ fn ] === 'function' ) {
			matchesFn = fn;
			return true;
		}
		/* istanbul ignore next */
		return false;
	} );

	while ( el ) {
		parent = el.parentElement;
		if ( parent && parent[ matchesFn ]( selector ) ) {
			return parent;
		}

		el = parent; // eslint-disable-line
	}

	return null;
};

/**
 * Insert a node after another node
 *
 * @param newNode {Element|NodeList}
 * @param referenceNode {Element|NodeList}
 */
export const insertAfter = ( newNode, referenceNode ) => {
	referenceNode.parentNode.insertBefore(
		newNode,
		referenceNode.nextElementSibling
	);
};

/**
 * Insert a node before another node
 *
 * @param newNode {Element|NodeList}
 * @param referenceNode {Element|NodeList}
 */

export const insertBefore = ( newNode, referenceNode ) => {
	referenceNode.parentNode.insertBefore( newNode, referenceNode );
};

/**
 * Set multiple element attributes at once
 *
 * @param el
 * @param attrs
 */

export const setAttributes = ( el, attrs ) => {
	for ( const key in attrs ) {
		el.setAttribute( key, attrs[ key ] );
	}
};
