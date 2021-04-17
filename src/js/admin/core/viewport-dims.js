/**
 * @module
 * @exports viewportDims
 * @description Sets viewport dimensions using verge on shared state
 * and detects mobile or desktop state.
 */

import verge from 'verge';
import state from '../config/state';
import { MOBILE_BREAKPOINT } from '../config/options';

const viewportDims = () => {
	state.v_height = verge.viewportH();
	state.v_width = verge.viewportW();

	if ( state.v_width >= MOBILE_BREAKPOINT ) {
		state.is_desktop = true;
		state.is_mobile = false;
	} else {
		state.is_desktop = false;
		state.is_mobile = true;
	}
};

export default viewportDims;
