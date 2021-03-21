import { createAction } from 'redux-actions';

export const REQ_EXAMPLE = 'emailapp/example/request';
export const REC_EXAMPLE = 'emailapp/example/receive';
export const ERR_EXAMPLE = 'emailapp/example/error';

export const reqExample = createAction( REQ_EXAMPLE );
export const recExample = createAction( REC_EXAMPLE );
export const errExample = createAction( ERR_EXAMPLE );

export const INITIAL_STATE = {
	isLoading: true,
	data: [], // maybe something extracted from the global data supplied on gravityflow_config
	error: null,
};

export default function exampleReducer( state = INITIAL_STATE, action ) {
	switch ( action.type ) {
		case REQ_EXAMPLE: {
			return {
				...state,
				isLoading: true,
			};
		}
		case REC_EXAMPLE: {
			const { data } = action.payload;
			return {
				...state,
				isLoading: false,
				...data,
			};
		}
		case ERR_EXAMPLE: {
			return {
				...state,
				isLoading: false,
				data: [],
				error: action.payload,
			};
		}
		default:
			return state;
	}
}
