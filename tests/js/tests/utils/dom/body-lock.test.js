import 'utils/__mocks__/wp';

import * as bodyLock from 'utils/dom/body-lock';

describe( 'bodyLock', () => {
	it( 'lock the body scroll', () => {
		bodyLock.lock();

		const style = document.body.style;

		expect( style.position ).toBe( 'fixed' );
		expect( style.marginTop ).toBe( '-0px' ); // weird thing jest's browser has it as -0px
	} );
} );

describe( 'bodyUnlock', () => {
	it( 'unlock the body scroll', () => {
		bodyLock.unlock();

		const style = document.body.style;

		expect( style.position ).toBe( 'static' );
		expect( style.marginTop ).toBe( '0px' );
	} );
} );
