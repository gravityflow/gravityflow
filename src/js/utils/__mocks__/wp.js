const wpSettingsMock = {};

Object.defineProperty(window, 'gravityflow_config', {
	value: wpSettingsMock,
});

module.export = wpSettingsMock;
