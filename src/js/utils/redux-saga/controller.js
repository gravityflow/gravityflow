import { takeEvery } from 'redux-saga/effects';

function createActionHandler( map ) {
	return function actionHandler( action ) {
		const handler = map[ action.type ];

		if ( typeof handler === 'function' ) {
			return handler( action );
		}

		return handler;
	};
}

// keys must be unique, and values should be a single action handler
export default function* controller( map ) {
	yield takeEvery( Object.keys( map ), createActionHandler( map ) );
}
