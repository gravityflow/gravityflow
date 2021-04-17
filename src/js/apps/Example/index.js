import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';

import App from './app';
import * as tools from 'utils/tools';
import configureStore from './store';
import reducers from './reducers';
import { mainSaga, childSagas } from './sagas';

// @EXAMPLE_REACT_APP

const store = configureStore( reducers, mainSaga, childSagas );

render(
	<Provider store={ store }>
		<App />
	</Provider>,
	tools.getNodes( 'example-app' )[ 0 ]
);

console.info( 'Gravity Flow Admin: Rendered example app.' );
