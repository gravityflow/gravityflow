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
			class="${ closeButtonClasses } gform-button gform-button--secondary gform-button--circular gform-button--size-xs"
			data-js="gform-flyout-close" 
			title="Close this flyout"
		>
			<i class="gform-button__icon gflow-icon gflow-icon--delete"></i>
		</button>
		${ content }
	</article>
	`; // todo: needs i18n
