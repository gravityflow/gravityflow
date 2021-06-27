/**
 * @function settingToggle
 * @description The gear icon for an ag grid instance that will trigger the settings flyout
 */

export const flyoutContainer = (
	id = '',
	closeButtonClasses = '',
	content = '',
	description = '',
	direction = '',
	position = '',
	title = '',
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
		${ title || description ? '<header class="gform-flyout__head">' : '' }
		${ title ? `<div class="gform-flyout__title">${ title }</div>` : '' }
		${ description ? `<div class="gform-flyout__desc">${ description }</div>` : '' }
		${ title || description ? '</header>' : '' }
		<div class="gform-flyout__body">${ content }</div>
	</article>
	`; // todo: needs i18n
