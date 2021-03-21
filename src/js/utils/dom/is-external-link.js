/**
 * @function isExternalLink
 * @description test if a url points to the website domain.
 */

const isExternalLink = ( url ) => {
	const match = url.match(
		/^([^:\/?#]+:)?(?:\/\/([^\/?#]*))?([^?#]+)?(\?[^#]*)?(#.*)?/
	);
	if (
		typeof match[ 1 ] === 'string' &&
		match[ 1 ].length > 0 &&
		match[ 1 ].toLowerCase() !== window.location.protocol
	) {
		return true;
	}

	if (
		typeof match[ 2 ] === 'string' &&
		match[ 2 ].length > 0 &&
		match[ 2 ].replace(
			new RegExp(
				`:(${
					{
						'http:': 80,
						'https:': 443,
					}[ window.location.protocol ]
				})?$`
			),
			''
		) !== window.location.host
	) {
		return true;
	}

	return false;
};

export default isExternalLink;
