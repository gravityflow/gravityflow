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
				animationDelay: 215, // total runtime of close animation. must be synced with css
				closeButtonClasses: 'gform-flyout__close', // classes for the close button
				content: '', // the html content
				description: '', // the optional description for the flyout
				direction: 'right', // direction to fly in from, left or right
				id: uniqueId( 'flyout' ), // id for the flyout
				insertPosition: 'beforeend', // insert position relative to target
				lockBody: false, // whether to lock body scroll when open
				onClose: () => {}, // function to fire when closed
				onOpen: () => {}, // function to fire when opened
				position: 'fixed', // fixed or absolute positioning
				renderOnInit: true, // render on initialization?
				target: 'body', // the selector to append the flyout to
				title: '', // the optional title for the flyout
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

	showFlyout() {
		this.onOpen();
		this.flyoutElement.classList.add( 'anim-in-ready' );
		window.setTimeout( () => {
			this.flyoutElement.classList.add( 'anim-in-active' );
		}, 25 );
	}

	closeFlyout = () => {
		if ( ! this.flyoutElement.classList.contains( 'anim-in-active' ) ) {
			return;
		}

		this.flyoutElement.classList.remove( 'anim-in-ready' );
		this.flyoutElement.classList.remove( 'anim-in-active' );
		this.flyoutElement.classList.add( 'anim-out-ready' );

		window.setTimeout( () => {
			this.flyoutElement.classList.add( 'anim-out-active' );
		}, 25 );

		window.setTimeout( () => {
			this.flyoutElement.classList.remove( 'anim-out-ready' );
			this.flyoutElement.classList.remove( 'anim-out-active' );
		}, this.animationDelay );

		this.state.open = false;
		this.onClose();
	};

	maybeCloseFlyout = ( e ) => {
		if ( e.detail?.activeId === this.id ) {
			return;
		}

		this.flyoutElement.classList.remove( 'anim-in-ready' );
		this.flyoutElement.classList.remove( 'anim-in-active' );
		this.flyoutElement.classList.remove( 'anim-out-ready' );
		this.flyoutElement.classList.remove( 'anim-out-active' );
		this.state.open = false;
	};

	handleKeyEvents = ( e ) =>
		focusLoop(
			e,
			this.triggerElement,
			this.flyoutElement,
			this.closeFlyout
		);

	handleTriggerClick = ( e ) => {
		this.triggerElement = e.target;
		if ( this.state.open ) {
			this.closeFlyout();
			this.triggerElement.focus();
			this.state.open = false;
		} else {
			this.showFlyout();
			this.closeElement.focus();
			this.state.open = true;
		}
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
				this.description,
				this.direction,
				this.position,
				this.title,
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

	bindEvents() {
		this.flyoutElement.addEventListener( 'keydown', this.handleKeyEvents );
		this.closeElement.addEventListener( 'click', this.closeFlyout );
		tools
			.getNodes( this.triggers, true, document, true )
			.forEach( ( trigger ) =>
				trigger.addEventListener( 'click', this.handleTriggerClick )
			);

		document.addEventListener(
			'gform/close-flyouts',
			this.maybeCloseFlyout
		);
	}

	init() {
		this.render();
		this.bindEvents();
	}
}
