/**
 * @function popup
 * @since 1.0
 * @description Launch a popup with all standard javascript popup options available plus a center method.
 * It will automatically harvest the url to load from the passed event if a url is not supplied, and has
 * desirable defaults.
 */

import _ from 'lodash';

const popup = ( opts ) => {
	const options = Object.assign(
		{
			event: null,
			url: '',
			center: true,
			name: '_blank',
			specs: {
				menubar: 0,
				scrollbars: 0,
				status: 1,
				titlebar: 1,
				toolbar: 0,
				top: 100,
				left: 100,
				width: 500,
				height: 300,
			},
		},
		opts
	);

	if ( options.event ) {
		options.event.preventDefault();
		if ( ! options.url.length ) {
			options.url = options.event.currentTarget.href;
		}
	}

	if ( options.url.length ) {
		if ( options.center ) {
			options.specs.top =
				window.screen.height / 2 - options.specs.height / 2;
			options.specs.left =
				window.screen.width / 2 - options.specs.width / 2;
		}

		const specs = [];

		_.forEach( options.specs, ( val, key ) => {
			const spec = `${ key }=${ val }`;
			specs.push( spec );
		} );

		window.open( options.url, options.name, specs.join() );
	}
};

export default popup;
