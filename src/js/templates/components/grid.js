/**
 * @function settingToggle
 * @description The gear icon for an ag grid instance that will trigger the settings flyout
 */

export const settingToggle = ( triggerId = 'grid-settings', title = '' ) =>
	`
	<button 
		class="gflow-grid__button gflow-grid__button--settings gflow-icon gflow-icon--cog" 
		data-js="${ triggerId }" 
		title="${ title }"
	></button>
	`;
