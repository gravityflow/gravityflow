import * as tools from 'utils/tools';

describe( 'addClass', () => {
	const divClass = 'test';

	it( 'returns false if null or undefined is passed', () => {
		const nullCheck = tools.addClass( null, divClass );
		const undefinedCheck = tools.addClass( undefined, divClass );

		expect( nullCheck ).toBe( false );
		expect( undefinedCheck ).toBe( false );
	} );

	it( 'adds a className to an elements classList', () => {
		const div = document.createElement( 'div' );

		tools.addClass( div, divClass );

		expect( div.classList.contains( divClass ) ).toBe( true );
	} );
} );

describe( 'getChildren', () => {
	it( 'gets all immediate child nodes of a parent as a simple array', () => {
		const parent = document.createElement( 'div' );
		const child1 = document.createElement( 'div' );
		const child2 = document.createElement( 'div' );

		parent.appendChild( child1 );
		parent.appendChild( child2 );

		const children = tools.getChildren( parent );

		expect( children instanceof Array ).toBe( true );
		expect( children.length ).toBe( 2 );
	} );
} );

describe( 'hasClass', () => {
	it( 'confirms a class exists on a dom node', () => {
		const div = document.createElement( 'div' );

		div.classList.add( 'test' );

		expect( tools.hasClass( div, 'test' ) ).toBe( true );
	} );

	it( 'returns false if el does not exist', () => {
		expect( tools.hasClass( null, 'test' ) ).toBe( false );
	} );
} );

describe( 'removeClass', () => {
	const divClass = 'test';

	it( 'returns false if null or undefined is passed', () => {
		const nullCheck = tools.removeClass( null, divClass );
		const undefinedCheck = tools.removeClass( undefined, divClass );

		expect( nullCheck ).toBe( false );
		expect( undefinedCheck ).toBe( false );
	} );

	it( 'adds a className to an elements classList', () => {
		const div = document.createElement( 'div' );

		div.classList.add( divClass );
		tools.removeClass( div, divClass );

		expect( div.classList.contains( divClass ) ).toBe( false );
	} );
} );

describe( 'convertElements', () => {
	it( 'converts a nodelist into a simple array', () => {
		const parent = document.createElement( 'div' );
		const child1 = document.createElement( 'div' );
		const child2 = document.createElement( 'div' );

		parent.appendChild( child1 );
		parent.appendChild( child2 );

		const nodes = parent.querySelectorAll( 'div' );
		const converted = tools.convertElements( nodes );

		expect( converted instanceof Array ).toBe( true );
		expect( converted.length ).toBe( 2 );
	} );
} );

describe( 'getNodes', () => {
	const parent = document.createElement( 'div' );
	const child1 = document.createElement( 'div' );
	const child2 = document.createElement( 'div' );
	const attributeKey = 'test-attribute';
	const classKey = 'test';

	child1.setAttribute( 'data-js', attributeKey );
	child1.classList.add( classKey );

	child2.setAttribute( 'data-js', attributeKey );
	child2.classList.add( classKey );

	parent.appendChild( child1 );
	parent.appendChild( child2 );

	it( 'gets a single node by attribute', () => {
		const node = tools.getNodes( attributeKey, false, parent )[ 0 ];
		expect( node ).toBeDefined();
	} );

	it( 'gets multiple nodes by attribute', () => {
		const nodes = tools.getNodes( attributeKey, false, parent );

		expect( nodes ).toBeDefined();
		expect( nodes.length ).toBe( 2 );
	} );

	it( 'gets multiple nodes by attribute and converts them to an array', () => {
		const nodes = tools.getNodes( attributeKey, true, parent );

		expect( nodes ).toBeDefined();
		expect( nodes.length ).toBe( 2 );
		expect( nodes instanceof Array ).toBe( true );
	} );

	it( 'gets multiple nodes by css selector string', () => {
		const nodes = tools.getNodes( `.${ classKey }`, false, parent, true );

		expect( nodes ).toBeDefined();
		expect( nodes.length ).toBe( 2 );
	} );
} );

describe( 'closest', () => {
	const parent = document.createElement( 'div' );
	const child = document.createElement( 'div' );
	const classKey = 'test';

	parent.classList.add( classKey );
	parent.appendChild( child );

	it( 'gets a parent node by selector string', () => {
		const node = tools.closest( child, `.${ classKey }` );
		expect( node ).toBeDefined();
	} );

	it( 'returns null if no match', () => {
		const node = tools.closest( child, '.other-test' );
		expect( node ).toBeNull();
	} );
} );

describe( 'insertAfter', () => {
	it( 'inserts an element after another one', () => {
		const parent = document.createElement( 'div' );
		const child1 = document.createElement( 'div' );
		const child2 = document.createElement( 'div' );

		parent.appendChild( child1 );

		child2.classList.add( 'test' );
		tools.insertAfter( child2, child1 );

		const nodes = parent.querySelectorAll( 'div' );

		expect( nodes[ 1 ].classList.contains( 'test' ) ).toBe( true );
	} );
} );

describe( 'insertBefore', () => {
	it( 'inserts an element before another one', () => {
		const parent = document.createElement( 'div' );
		const child1 = document.createElement( 'div' );
		const child2 = document.createElement( 'div' );

		parent.appendChild( child1 );

		child2.classList.add( 'test' );
		tools.insertBefore( child2, child1 );

		const nodes = parent.querySelectorAll( 'div' );

		expect( nodes[ 0 ].classList.contains( 'test' ) ).toBe( true );
	} );
} );
