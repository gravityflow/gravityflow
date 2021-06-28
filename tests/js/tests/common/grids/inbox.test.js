import { INBOX_DEFAULT_ID } from 'common/config/constants';
import { getGridId, getGridOptions } from 'common/grids/inbox';

describe( 'inboxGrid', () => {
	it( 'gets the default grid id correctly', () => {
		const grid = document.createElement( 'div' );
		const gridId = getGridId( grid );

		expect( gridId ).toMatch( INBOX_DEFAULT_ID );
	} );

	it( 'gets the unique grid id correctly from the grid container', () => {
		const grid = document.createElement( 'div' );
		grid.setAttribute( 'data-grid-id', 'testing' );
		const gridId = getGridId( grid );

		expect( gridId ).toMatch( 'testing' );
	} );

	it( 'gets the default grid options correctly', () => {
		const gridOptions = getGridOptions();

		expect( gridOptions ).toMatchObject( { animateRows: false } );
	} );
} );
