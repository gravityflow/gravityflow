import toggle from 'templates/components/toggle';

/**
 * @function settings
 * @description The content for the inbox settings flyout
 */

export const settings = () =>
	`
	<span class="gform-flyout__setting-label">
		Enable Push Notifications
	</span>
	<span class="gform-flyout__setting-desc">
		You will have to allow push notifications for this domain and browser to enable this feature. If you haven't already,
		your browser will ask you to enable them one time.
	</span>
	${ toggle(
		'inbox-setting-push-notifications',
		'inbox-setting-push-notifications',
		false,
		'disabled',
		'enabled',
		'gform-field__toggle gform-flyout__setting',
		'inbox-setting'
	) }
	`;
