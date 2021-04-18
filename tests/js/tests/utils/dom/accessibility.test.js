import * as accessibility from 'utils/dom/accessibility';

describe( 'setAccActiveAttributes', () => {
	it( 'activate the aria attributes for accessibility', () => {
		const target = document.createElement( 'div' );
		const content = document.createElement( 'div' );

		accessibility.setAccActiveAttributes( target, content );

		expect( target.getAttribute( 'aria-expanded' ) ).toBe( 'true' );
		expect( target.getAttribute( 'aria-selected' ) ).toBe( 'true' );

		expect( content.getAttribute( 'aria-hidden' ) ).toBe( 'false' );
	} );

	it( 'deactivate the aria attributes for accessibility', () => {
		const target = document.createElement( 'div' );
		const content = document.createElement( 'div' );

		accessibility.setAccInactiveAttributes( target, content );

		expect( target.getAttribute( 'aria-expanded' ) ).toBe( 'false' );
		expect( target.getAttribute( 'aria-selected' ) ).toBe( 'false' );

		expect( content.getAttribute( 'aria-hidden' ) ).toBe( 'true' );
	} );
} );
