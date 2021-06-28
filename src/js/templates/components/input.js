/**
 * Input template
 *
 * @param {string} type
 * @param {string} id
 * @param {string} name
 * @param {string} value
 * @param {string} classes
 * @param {string} jsAttribute
 * @param {string} placeholder
 *
 * @return {string}
 */

export default function (
	type = 'text',
	id = '',
	name = '',
	value = '',
	classes = 'gform-input',
	jsAttribute = 'gform-input',
	placeholder = ''
) {
	return `
	<input 
		class="${ classes }" 
		value="${ value }" 
		type="${ type }" 
		data-js="${ jsAttribute }"
		${ placeholder ? `placeholder="${ placeholder }"` : '' }
		${ id ? `id="${ id }"` : '' }
		${ name ? `name="${ name }"` : '' }
	/>
	`;
}
