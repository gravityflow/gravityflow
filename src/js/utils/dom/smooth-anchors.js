/**
 * Enable this module in your ready function to cause all hash links to smooth scroll sitewide!
 */

import * as tools from '../tools';
import scrollTo from '../dom/scroll-to';

const handleAnchorClick = ( e ) => {
	const target = document.getElementById( e.target.hash.substring( 1 ) );
	if ( ! target ) {
		return;
	}

	e.preventDefault();

	window.history.pushState( null, null, e.target.hash );

	scrollTo( {
		offset: -150,
		duration: 300,
		$target: $( target ),
	} );
};

const bindEvents = () => {
	const anchorLinks = tools.convertElements(
		document.querySelectorAll( 'a[href^="#"]:not([href="#"])' )
	);
	if ( ! anchorLinks.length ) {
		return;
	}

	anchorLinks.forEach( ( link ) =>
		link.addEventListener( 'click', handleAnchorClick )
	);
};

const init = () => {
	bindEvents();
};

export default init;
