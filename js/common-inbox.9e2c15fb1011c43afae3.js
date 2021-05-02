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
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var ag_grid_community__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ag-grid-community */ \"./node_modules/ag-grid-community/dist/ag-grid-community.cjs.js\");\n/**\n * Inbox\n *\n * Initializes the inbox component\n */\n\nvar el = {};\nvar instances = {};\nvar gridOptions = window.gflow_config.grid_options || {};\n\nvar initializeGrid = function initializeGrid() {\n  gridOptions.getRowNodeId = function (data) {\n    return data.id;\n  };\n\n  instances.grid = new ag_grid_community__WEBPACK_IMPORTED_MODULE_0__.Grid(el.container, gridOptions);\n};\n\nvar init = function init(container) {\n  el.container = container;\n  initializeGrid();\n  console.info('Gravity Flow Common: Initialized inbox component.');\n};\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (init);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy9jb21tb24vY29tcG9uZW50cy9pbmJveC5qcz9jMDA0Il0sIm5hbWVzIjpbImVsIiwiaW5zdGFuY2VzIiwiZ3JpZE9wdGlvbnMiLCJ3aW5kb3ciLCJnZmxvd19jb25maWciLCJncmlkX29wdGlvbnMiLCJpbml0aWFsaXplR3JpZCIsImdldFJvd05vZGVJZCIsImRhdGEiLCJpZCIsImdyaWQiLCJHcmlkIiwiY29udGFpbmVyIiwiaW5pdCIsImNvbnNvbGUiLCJpbmZvIl0sIm1hcHBpbmdzIjoiOzs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUVBLElBQU1BLEVBQUUsR0FBRyxFQUFYO0FBQ0EsSUFBTUMsU0FBUyxHQUFHLEVBQWxCO0FBRUEsSUFBTUMsV0FBVyxHQUFHQyxNQUFNLENBQUNDLFlBQVAsQ0FBb0JDLFlBQXBCLElBQW9DLEVBQXhEOztBQUVBLElBQU1DLGNBQWMsR0FBRyxTQUFqQkEsY0FBaUIsR0FBTTtBQUM1QkosYUFBVyxDQUFDSyxZQUFaLEdBQTJCLFVBQUVDLElBQUYsRUFBWTtBQUN0QyxXQUFPQSxJQUFJLENBQUNDLEVBQVo7QUFDQSxHQUZEOztBQUtBUixXQUFTLENBQUNTLElBQVYsR0FBaUIsSUFBSUMsbURBQUosQ0FBVVgsRUFBRSxDQUFDWSxTQUFiLEVBQXdCVixXQUF4QixDQUFqQjtBQUNBLENBUEQ7O0FBU0EsSUFBTVcsSUFBSSxHQUFHLFNBQVBBLElBQU8sQ0FBRUQsU0FBRixFQUFpQjtBQUM3QlosSUFBRSxDQUFDWSxTQUFILEdBQWVBLFNBQWY7QUFFQU4sZ0JBQWM7QUFFZFEsU0FBTyxDQUFDQyxJQUFSLENBQWMsbURBQWQ7QUFDQSxDQU5EOztBQVFBLGlFQUFlRixJQUFmIiwiZmlsZSI6Ii4vc3JjL2pzL2NvbW1vbi9jb21wb25lbnRzL2luYm94LmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBJbmJveFxuICpcbiAqIEluaXRpYWxpemVzIHRoZSBpbmJveCBjb21wb25lbnRcbiAqL1xuXG5pbXBvcnQgeyBHcmlkIH0gZnJvbSAnYWctZ3JpZC1jb21tdW5pdHknO1xuXG5jb25zdCBlbCA9IHt9O1xuY29uc3QgaW5zdGFuY2VzID0ge307XG5cbmNvbnN0IGdyaWRPcHRpb25zID0gd2luZG93LmdmbG93X2NvbmZpZy5ncmlkX29wdGlvbnMgfHwge307XG5cbmNvbnN0IGluaXRpYWxpemVHcmlkID0gKCkgPT4ge1xuXHRncmlkT3B0aW9ucy5nZXRSb3dOb2RlSWQgPSAoIGRhdGEgKSA9PiB7XG5cdFx0cmV0dXJuIGRhdGEuaWQ7XG5cdH07XG5cdFxuXG5cdGluc3RhbmNlcy5ncmlkID0gbmV3IEdyaWQoIGVsLmNvbnRhaW5lciwgZ3JpZE9wdGlvbnMgKTtcbn07XG5cbmNvbnN0IGluaXQgPSAoIGNvbnRhaW5lciApID0+IHtcblx0ZWwuY29udGFpbmVyID0gY29udGFpbmVyO1xuXG5cdGluaXRpYWxpemVHcmlkKCk7XG5cblx0Y29uc29sZS5pbmZvKCAnR3Jhdml0eSBGbG93IENvbW1vbjogSW5pdGlhbGl6ZWQgaW5ib3ggY29tcG9uZW50LicgKTtcbn07XG5cbmV4cG9ydCBkZWZhdWx0IGluaXQ7XG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./src/js/common/components/inbox.js\n");

/***/ })

}]);