import { combineReducers } from 'redux';
import example from './Example/ducks';

const rootReducer = combineReducers( {
	example,
} );

export default rootReducer;
