import input from 'templates/components/input';
import toggle from 'templates/components/toggle';

/**
 * @function settings
 * @description The content for the inbox settings flyout
 */

export const settings = () =>
	`
	<span class="gform-flyout__setting-label">
		Enable Live Refresh
	</span>
	<span class="gform-flyout__setting-desc">
		This setting enables checking the server for new inbox entries on a configurable frequency.
	</span>
	${ toggle(
		'inbox-setting-polling-enabled',
		'inbox-setting-polling-enabled',
		true,
		'disabled',
		'enabled',
		'gform-field__toggle gform-flyout__setting',
		'inbox-setting'
	) }
	
	<label class="gform-flyout__setting-label" for="gform-inbox-setting-polling-frequency">
		Refresh Frequency
	</label>
	<span class="gform-flyout__setting-desc">
		Control the frequency with which the server is checked for new inbox entries. Value is in seconds.
	</span>
	${ input(
		'number',
		'inbox-setting-polling-frequency',
		'inbox-setting-polling-frequency',
		'60',
		'gform-input gform-flyout__setting gform-flyout__setting--poll-freq',
		'inbox-setting'
	) }
	
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
