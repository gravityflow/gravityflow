import arrayToInt from 'utils/data/array-to-int';

describe( 'arrayToInt', () => {
	it( 'converts all string type integers into true integers in an array', () => {
		const arr = [ '9', 11, '15' ];
		const parsed = arrayToInt( arr );
		expect( Number.isInteger( parsed[ 0 ] ) ).toBe( true );
		expect( Number.isInteger( parsed[ 1 ] ) ).toBe( true );
		expect( Number.isInteger( parsed[ 2 ] ) ).toBe( true );
	} );
} );
