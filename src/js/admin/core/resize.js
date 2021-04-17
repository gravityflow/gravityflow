/**
 * @module
 * @exports resize
 * @description Kicks in any third party plugins that operate on a sitewide basis.
 */

import { trigger } from 'utils/events';
import viewportDims from './viewport-dims';

const resize = () => {
	// code for resize events can go here

	viewportDims();

	trigger( { event: 'gravityflow/resize_executed', native: false } );
};

export default resize;
