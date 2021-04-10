import applyBrowserClasses from 'utils/dom/apply-browser-classes';

const mockTestResult = {
	android: false,
	ios: false,
	edge: false,
	chrome: false,
	firefox: false,
	ie: false,
	opera: false,
	safari: false,
};

jest.mock( 'utils/tests', () => {
	return {
		browserTests: () => mockTestResult,
	};
} );

describe( 'applyBrowserClasses', () => {
	beforeEach( resetTestResult );

	const tests = [
		{ property: 'android', value: 'device-android' },
		{ property: 'ios', value: 'device-ios' },
		{ property: 'edge', value: 'browser-edge' },
		{ property: 'chrome', value: 'browser-chrome' },
		{ property: 'firefox', value: 'browser-firefox' },
		{ property: 'ie', value: 'browser-ie' },
		{ property: 'opera', value: 'browser-opera' },
		{ property: 'safari', value: 'browser-safari' },
	];

	tests.forEach( ( test ) => {
		it( `adds browser class ${ test.value }`, () => {
			mockTestResult[ test.property ] = true;
			applyBrowserClasses();

			expect( document.body.classList.contains( test.value ) ).toBe(
				true
			);
		} );
	} );
} );

function resetTestResult() {
	Object.keys( mockTestResult ).forEach( ( key ) => {
		mockTestResult[ key ] = false;
	} );

	return mockTestResult;
}
