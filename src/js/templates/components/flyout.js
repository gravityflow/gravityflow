/**
 * @function settingToggle
 * @description The gear icon for an ag grid instance that will trigger the settings flyout
 */

export const flyoutContainer = (
	id = '',
	closeButtonClasses = '',
	content = '',
	direction = '',
	position = '',
	wrapperClasses = ''
) =>
	`
	<article id="${ id }" class="${ wrapperClasses } gform-flyout--${ direction } gform-flyout--${ position }">
		<button 
			class="${ closeButtonClasses }" 
			data-js="gform-flyout-close" 
			title="Close this flyout"
		></button>
		${ content }
	</article>
	`; // todo: needs i18n
