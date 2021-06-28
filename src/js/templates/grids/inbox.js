import uniqueId from 'utils/data/unique-id';
import input from 'templates/components/input';
import toggle from 'templates/components/toggle';

/**
 * @function settings
 * @description The content for the inbox settings flyout
 */

export const settings = ( id = uniqueId( 'inbox-settings-' ) ) =>
	`
	<span class="gform-flyout__setting-label">
		Enable Push Notifications
	</span>
	<span class="gform-flyout__setting-desc">
		You will have to allow push notifications for this domain and browser to enable this feature. If you haven't already,
		your browser will ask you to enable them one time.
	</span>
	${ toggle(
		id,
		id,
		false,
		'disabled',
		'enabled',
		'gform-field__toggle gform-flyout__setting',
		'inbox-setting'
	) }
	`;

/**
 * @function header
 * @description The inbox header with search.
 */

export const header = ( searchId = uniqueId( 'inbox-header-' ) ) =>
	`
	<div class="gflow-grid__header">
		${ input(
			'search',
			searchId,
			'',
			'',
			'gform-input gflow-inbox__search',
			'gflow-inbox-search',
			'Search Inbox'
		) }
	</div>
	`;
