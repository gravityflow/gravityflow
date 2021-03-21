/**
 * @function hasScrollbar
 * @description test if an el has scrollbars
 */

const hasScrollbar = ( el ) => ( {
	vertical: el.scrollHeight > el.clientHeight,
	horizontal: el.scrollWidth > el.clientWidth,
} );

export default hasScrollbar;
