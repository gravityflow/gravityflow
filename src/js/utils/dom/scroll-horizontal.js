const scrollHorizontal = ( e, target = null ) => {
	if ( ! target ) {
		return;
	}

	if ( target.scrollWidth <= target.clientWidth ) {
		return;
	}

	const event = window.event || e;
	const delta = Math.max(
		-1,
		Math.min( 1, event.wheelDelta || -event.detail )
	);
	target.scrollLeft -= (delta * 40); //eslint-disable-line
	e.preventDefault();
};

export default scrollHorizontal;
