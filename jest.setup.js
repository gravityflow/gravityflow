import Enzyme, { shallow, render, mount } from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';
import moment from 'moment-timezone';

Enzyme.configure( { adapter: new Adapter() } );

moment.tz.setDefault( 'America/Los_Angeles' );

global.shallow = shallow;
global.render = render;
global.mount = mount;
