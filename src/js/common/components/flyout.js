import * as templates from 'templates/components/flyout';
import * as tools from 'utils/tools';
import focusLoop from 'utils/a11y/focus-loop';
import uniqueId from 'utils/data/unique-id';

export default class Flyout {
	/**
	 * @param {Object} options
	 */
	constructor( options = {} ) {
		Object.assign(
			this,
			{
				closeButtonClasses: 'gform-flyout__close', // classes for the close button
				content: '', // the html content
				direction: 'right', // direction to fly in from, left or right
				id: uniqueId( 'flyout' ), // id for the flyout
				insertPosition: 'beforeend', // insert position relative to target
				lockBody: false, // whether to lock body scroll when open
				onClose: () => {}, // function to fire when closed
				onOpen: () => {}, // function to fire when opened
				position: 'fixed', // fixed or absolute positioning
				renderOnInit: true, // render on initialization?
				target: 'body', // the selector to append the flyout to
				triggers: '[data-js="gform-trigger-flyout"]', // the selector[s] of the trigger that shows it
				wrapperClasses: 'gform-flyout', // additional classes for the wrapper
			},
			options
		);

		this.state = {
			open: false,
		};

		this.flyoutElement = null;
		this.triggerElement = null;
		this.closeElement = null;

		if ( this.renderOnInit ) {
			this.init();
		}
	}

	init() {
		this.render();
		this.bindEvents();
	}

	bindEvents() {
		this.flyoutElement.addEventListener( 'keydown', this.handleKeyEvents );
		this.closeElement.addEventListener( 'click', this.closeFlyout );
		tools
			.getNodes( this.triggers, true, document, true )
			.forEach( ( trigger ) =>
				trigger.addEventListener( 'click', this.handleTriggerClick )
			);
	}

	closeFlyout = () => {
		this.flyoutElement.classList.remove( 'gform-flyout--open' );
		this.triggerElement.focus();
		this.state.open = false;
	};

	handleKeyEvents = ( e ) =>
		focusLoop( e, this.triggerElement, this.flyoutElement );

	handleTriggerClick = ( e ) => {
		this.triggerElement = e.target;
		if ( this.state.open ) {
			this.flyoutElement.classList.remove( 'gform-flyout--open' );
			this.triggerElement.focus();
		} else {
			this.flyoutElement.classList.add( 'gform-flyout--open' );
			this.closeElement.focus();
		}
		this.state.open = ! this.state.open;
	};

	render() {
		const target = document.querySelectorAll( this.target )[ 0 ];
		if ( ! target ) {
			console.error(
				`Flyout could not render as ${ this.target } could not be found.`
			);
			return;
		}

		target.insertAdjacentHTML(
			this.insertPosition,
			templates.flyoutContainer(
				this.id,
				this.closeButtonClasses,
				this.content,
				this.direction,
				this.position,
				this.wrapperClasses
			)
		);

		this.flyoutElement = document.getElementById( this.id );
		this.closeElement = tools.getNodes(
			'gform-flyout-close',
			false,
			this.flyoutElement
		)[ 0 ];

		console.info(
			`Gravity Flow Common: Initialized flyout component on ${ this.target }.`
		);
	}
}
