let mockStorage = {};

const localStorageMock = {
	setItem: (key, val) => {
		Object.assign(mockStorage, {
			[key]: val,
		});
	},
	getItem: (key) => {
		return mockStorage[key] || null;
	},
	removeItem: (key) => {
		delete mockStorage[key];
	},
	clear: () => {
		mockStorage = {};
	},
};

Object.defineProperty(window, 'localStorage', {
	value: localStorageMock,
});

module.export = localStorageMock;
