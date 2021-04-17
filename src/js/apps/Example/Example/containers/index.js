import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import { reqExample } from '../ducks';
import Example from '../components';

export function mapStateToProps( { example } ) {
	return {
		...example,
	};
}

export function mapDispatchToProps( dispatch ) {
	return bindActionCreators(
		{
			reqExample,
		},
		dispatch
	);
}

export default connect( mapStateToProps, mapDispatchToProps )( Example );
