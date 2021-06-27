/**
 * Toggle template
 *
 * @param {string} id
 * @param {string} name
 * @param {boolean} checked
 * @param {string} disabledLabel
 * @param {string} enabledLabel
 * @param {string} wrapperClasses
 * @param {string} jsAttribute
 *
 * @return {string}
 */

export default function (
	id = '',
	name = '',
	checked = false,
	disabledLabel = '',
	enabledLabel = '',
	wrapperClasses = 'gform-field__toggle',
	jsAttribute = 'gform-toggle'
) {
	return `
	<div class="${ wrapperClasses }">
		<span class="gform-settings-input__container">
			<input 
				type="checkbox" 
				class="gform-field__toggle-input" 
				data-js="${ jsAttribute }" 
				${ id ? `id=${ id }` : '' }
				${ name ? `name=${ name }` : '' }
				${ checked ? 'checked' : '' }
			>
			<label class="gform-field__toggle-container" ${ id ? `for=${ id }` : '' }>
				<span 
					class="gform-field__toggle-switch-text screen-reader-text"
					data-disabled-text="${ disabledLabel }"
					data-enabled-text="${ enabledLabel }"
				>${ checked ? enabledLabel : disabledLabel }</span>
				<span class="gform-field__toggle-switch"></span>
			</label>
		</span>
	</div>
	`;
}
