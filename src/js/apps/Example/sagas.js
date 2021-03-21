import map from 'lodash/map';
import { fork, all } from 'redux-saga/effects';
import example from './Example/sagas';

export const childSagas = [ example ];

export function* mainSaga( childrenSagas ) {
	yield all( [
		...map( childrenSagas, ( childSaga ) => fork( childSaga ) ),
	] );
}
