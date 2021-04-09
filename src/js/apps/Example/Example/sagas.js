import { call, delay, put } from 'redux-saga/effects';
import controller from 'utils/redux-saga/controller';
import { reqExample, recExample, errExample } from './ducks';
import { fetchExample } from './api';

export function* exampleSaga() {
	try {
		yield delay( 2000 ); // we'll add a little delay to extend the ui example
		const response = yield call( fetchExample ); // we execute the call to fetch
		yield put( recExample( { data: response } ) ); // if good we we execute the receive example action
	} catch ( e ) {
		yield put( errExample( e ) );
	}
}

/**
 * Our request example action is mapped to our example saga by the controller util
 * It calls our local api function to fetch, which calls the root api file which then maps requests
 * and parses fetch responses with error handling
 *
 * @class
 */

export default function* Example() {
	yield* controller( {
		[ reqExample ]: exampleSaga,
	} );
}
