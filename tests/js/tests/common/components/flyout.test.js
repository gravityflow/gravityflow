import Flyout from 'common/components/Flyout';

describe( 'flyoutComponent', () => {
	it( 'renders to body by default with correct wrapper class', () => {
		const flyoutInstance = new Flyout();
		const flyoutEl = document.querySelectorAll(
			'body > .gform-flyout'
		)[ 0 ];

		expect( flyoutEl ).toBeDefined();
	} );

	it( 'contains a flyout and close HTMLElement', () => {
		const flyoutInstance = new Flyout();

		expect( flyoutInstance.flyoutElement ).toBeDefined();
		expect( flyoutInstance.closeElement ).toBeDefined();
	} );

	it( 'is closed by default', () => {
		const flyoutInstance = new Flyout();

		expect( flyoutInstance.state.open ).toBe( false );
	} );
} );
