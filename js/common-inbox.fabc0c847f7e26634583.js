/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
(self["webpackChunkgravityflow"] = self["webpackChunkgravityflow"] || []).push([["common-inbox"],{

/***/ "./src/js/common/components/inbox.js":
/*!*******************************************!*\
  !*** ./src/js/common/components/inbox.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var ag_grid_community__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ag-grid-community */ \"./node_modules/ag-grid-community/dist/ag-grid-community.cjs.js\");\n/**\n * Inbox\n *\n * Initializes the inbox component\n */\n\nvar el = {};\nvar instances = {};\nvar gridOptions = window.gflow_config.grid_options || {};\n\nvar initializeGrid = function initializeGrid() {\n  instances.grid = new ag_grid_community__WEBPACK_IMPORTED_MODULE_0__.Grid(el.container, gridOptions);\n};\n\nvar init = function init(container) {\n  el.container = container;\n  initializeGrid();\n  console.info('Gravity Flow Common: Initialized inbox component.');\n};\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (init);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy9jb21tb24vY29tcG9uZW50cy9pbmJveC5qcz9jMDA0Il0sIm5hbWVzIjpbImVsIiwiaW5zdGFuY2VzIiwiZ3JpZE9wdGlvbnMiLCJ3aW5kb3ciLCJnZmxvd19jb25maWciLCJncmlkX29wdGlvbnMiLCJpbml0aWFsaXplR3JpZCIsImdyaWQiLCJHcmlkIiwiY29udGFpbmVyIiwiaW5pdCIsImNvbnNvbGUiLCJpbmZvIl0sIm1hcHBpbmdzIjoiOzs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUVBLElBQU1BLEVBQUUsR0FBRyxFQUFYO0FBQ0EsSUFBTUMsU0FBUyxHQUFHLEVBQWxCO0FBRUEsSUFBTUMsV0FBVyxHQUFHQyxNQUFNLENBQUNDLFlBQVAsQ0FBb0JDLFlBQXBCLElBQW9DLEVBQXhEOztBQUVBLElBQU1DLGNBQWMsR0FBRyxTQUFqQkEsY0FBaUIsR0FBTTtBQUM1QkwsV0FBUyxDQUFDTSxJQUFWLEdBQWlCLElBQUlDLG1EQUFKLENBQVVSLEVBQUUsQ0FBQ1MsU0FBYixFQUF3QlAsV0FBeEIsQ0FBakI7QUFDQSxDQUZEOztBQUlBLElBQU1RLElBQUksR0FBRyxTQUFQQSxJQUFPLENBQUVELFNBQUYsRUFBaUI7QUFDN0JULElBQUUsQ0FBQ1MsU0FBSCxHQUFlQSxTQUFmO0FBRUFILGdCQUFjO0FBRWRLLFNBQU8sQ0FBQ0MsSUFBUixDQUFjLG1EQUFkO0FBQ0EsQ0FORDs7QUFRQSxpRUFBZUYsSUFBZiIsImZpbGUiOiIuL3NyYy9qcy9jb21tb24vY29tcG9uZW50cy9pbmJveC5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogSW5ib3hcbiAqXG4gKiBJbml0aWFsaXplcyB0aGUgaW5ib3ggY29tcG9uZW50XG4gKi9cblxuaW1wb3J0IHsgR3JpZCB9IGZyb20gJ2FnLWdyaWQtY29tbXVuaXR5JztcblxuY29uc3QgZWwgPSB7fTtcbmNvbnN0IGluc3RhbmNlcyA9IHt9O1xuXG5jb25zdCBncmlkT3B0aW9ucyA9IHdpbmRvdy5nZmxvd19jb25maWcuZ3JpZF9vcHRpb25zIHx8IHt9O1xuXG5jb25zdCBpbml0aWFsaXplR3JpZCA9ICgpID0+IHtcblx0aW5zdGFuY2VzLmdyaWQgPSBuZXcgR3JpZCggZWwuY29udGFpbmVyLCBncmlkT3B0aW9ucyApO1xufTtcblxuY29uc3QgaW5pdCA9ICggY29udGFpbmVyICkgPT4ge1xuXHRlbC5jb250YWluZXIgPSBjb250YWluZXI7XG5cblx0aW5pdGlhbGl6ZUdyaWQoKTtcblxuXHRjb25zb2xlLmluZm8oICdHcmF2aXR5IEZsb3cgQ29tbW9uOiBJbml0aWFsaXplZCBpbmJveCBjb21wb25lbnQuJyApO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgaW5pdDtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/js/common/components/inbox.js\n");

/***/ })

}]);