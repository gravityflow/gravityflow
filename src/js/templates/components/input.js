/**
 * Input template
 *
 * @param {string} type
 * @param {string} id
 * @param {string} name
 * @param {string} value
 * @param {string} classes
 * @param {string} jsAttribute
 *
 * @return {string}
 */

export default function (
	type = 'text',
	id = '',
	name = '',
	value = '',
	classes = 'gform-input',
	jsAttribute = 'gform-input'
) {
	return `
	<input 
		class="${ classes }" 
		value="${ value }" 
		type="${ type }" 
		data-js="${ jsAttribute }"
		${ id ? `id=${ id }` : '' }
		${ name ? `name=${ name }` : '' }
	/>
	`;
}
