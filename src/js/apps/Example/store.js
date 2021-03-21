import { createStore, applyMiddleware } from 'redux';
import createSagaMiddleware from 'redux-saga';

export default function configureStore( reducer, mainSaga, childrenSagas ) {
	const sagaMiddleware = createSagaMiddleware();

	const store = createStore( reducer, applyMiddleware( sagaMiddleware ) );

	sagaMiddleware.run( mainSaga, childrenSagas );

	return store;
}
