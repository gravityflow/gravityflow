import React, { Component } from 'react';
import PropTypes from 'prop-types';
import Loading from 'app-common/Loading';

export default class Example extends Component {
	static propTypes = {
		isLoading: PropTypes.bool,
		data: PropTypes.any,
		reqExample: PropTypes.func.isRequired,
	};

	static defaultProps = {
		isLoading: true,
		data: {},
	};

	componentDidMount() {
		const { data, reqExample } = this.props;
		if ( ! data.length ) {
			reqExample();
		}
	}

	render() {
		const { isLoading, data } = this.props;

		if ( isLoading ) {
			return <Loading />;
		}

		return (
			<div>
				Hello from Example, here&lsquo;s your note: &ldquo;
				{ data.title }
				&ldquo;
			</div>
		);
	}
}
