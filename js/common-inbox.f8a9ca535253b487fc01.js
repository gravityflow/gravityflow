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
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var ag_grid_community__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ag-grid-community */ \"./node_modules/ag-grid-community/dist/ag-grid-community.cjs.js\");\n/**\n * Inbox\n *\n * Initializes the inbox component\n */\n\nvar el = {};\nvar instances = {};\nvar gridOptions = window.gflow_config.grid_options || {};\n\nvar initializeGrid = function initializeGrid() {\n  gridOptions.getRowNodeId = function (data) {\n    return data.id;\n  };\n\n  instances.grid = new ag_grid_community__WEBPACK_IMPORTED_MODULE_0__.Grid(el.container, gridOptions);\n};\n\nvar refreshGrid = function refreshGrid() {};\n\nvar addEventListeners = function addEventListeners() {\n  var refreshButton = document.querySelector('[data-js=\"refresh_inbox\"]');\n  refreshButton.addEventListener('click', function (e) {\n    e.preventDefault();\n  });\n};\n\nvar init = function init(container) {\n  el.container = container;\n  initializeGrid();\n  addEventListeners();\n  console.info('Gravity Flow Common: Initialized inbox component.');\n};\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (init);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy9jb21tb24vY29tcG9uZW50cy9pbmJveC5qcz9jMDA0Il0sIm5hbWVzIjpbImVsIiwiaW5zdGFuY2VzIiwiZ3JpZE9wdGlvbnMiLCJ3aW5kb3ciLCJnZmxvd19jb25maWciLCJncmlkX29wdGlvbnMiLCJpbml0aWFsaXplR3JpZCIsImdldFJvd05vZGVJZCIsImRhdGEiLCJpZCIsImdyaWQiLCJHcmlkIiwiY29udGFpbmVyIiwicmVmcmVzaEdyaWQiLCJhZGRFdmVudExpc3RlbmVycyIsInJlZnJlc2hCdXR0b24iLCJkb2N1bWVudCIsInF1ZXJ5U2VsZWN0b3IiLCJhZGRFdmVudExpc3RlbmVyIiwiZSIsInByZXZlbnREZWZhdWx0IiwiaW5pdCIsImNvbnNvbGUiLCJpbmZvIl0sIm1hcHBpbmdzIjoiOzs7OztBQUFBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFFQTtBQUVBLElBQU1BLEVBQUUsR0FBRyxFQUFYO0FBQ0EsSUFBTUMsU0FBUyxHQUFHLEVBQWxCO0FBRUEsSUFBTUMsV0FBVyxHQUFHQyxNQUFNLENBQUNDLFlBQVAsQ0FBb0JDLFlBQXBCLElBQW9DLEVBQXhEOztBQUVBLElBQU1DLGNBQWMsR0FBRyxTQUFqQkEsY0FBaUIsR0FBTTtBQUM1QkosYUFBVyxDQUFDSyxZQUFaLEdBQTJCLFVBQUVDLElBQUYsRUFBWTtBQUN0QyxXQUFPQSxJQUFJLENBQUNDLEVBQVo7QUFDQSxHQUZEOztBQUlBUixXQUFTLENBQUNTLElBQVYsR0FBaUIsSUFBSUMsbURBQUosQ0FBVVgsRUFBRSxDQUFDWSxTQUFiLEVBQXdCVixXQUF4QixDQUFqQjtBQUNBLENBTkQ7O0FBUUEsSUFBTVcsV0FBVyxHQUFHLFNBQWRBLFdBQWMsR0FBTSxDQUV6QixDQUZEOztBQUlBLElBQU1DLGlCQUFpQixHQUFHLFNBQXBCQSxpQkFBb0IsR0FBTTtBQUMvQixNQUFNQyxhQUFhLEdBQUdDLFFBQVEsQ0FBQ0MsYUFBVCxDQUF3QiwyQkFBeEIsQ0FBdEI7QUFDQUYsZUFBYSxDQUFDRyxnQkFBZCxDQUFnQyxPQUFoQyxFQUF5QyxVQUFVQyxDQUFWLEVBQWM7QUFDdERBLEtBQUMsQ0FBQ0MsY0FBRjtBQUVBLEdBSEQ7QUFJQSxDQU5EOztBQVFBLElBQU1DLElBQUksR0FBRyxTQUFQQSxJQUFPLENBQUVULFNBQUYsRUFBaUI7QUFDN0JaLElBQUUsQ0FBQ1ksU0FBSCxHQUFlQSxTQUFmO0FBRUFOLGdCQUFjO0FBRWRRLG1CQUFpQjtBQUVqQlEsU0FBTyxDQUFDQyxJQUFSLENBQWMsbURBQWQ7QUFDQSxDQVJEOztBQVVBLGlFQUFlRixJQUFmIiwiZmlsZSI6Ii4vc3JjL2pzL2NvbW1vbi9jb21wb25lbnRzL2luYm94LmpzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBJbmJveFxuICpcbiAqIEluaXRpYWxpemVzIHRoZSBpbmJveCBjb21wb25lbnRcbiAqL1xuXG5pbXBvcnQgeyBHcmlkIH0gZnJvbSAnYWctZ3JpZC1jb21tdW5pdHknO1xuXG5jb25zdCBlbCA9IHt9O1xuY29uc3QgaW5zdGFuY2VzID0ge307XG5cbmNvbnN0IGdyaWRPcHRpb25zID0gd2luZG93LmdmbG93X2NvbmZpZy5ncmlkX29wdGlvbnMgfHwge307XG5cbmNvbnN0IGluaXRpYWxpemVHcmlkID0gKCkgPT4ge1xuXHRncmlkT3B0aW9ucy5nZXRSb3dOb2RlSWQgPSAoIGRhdGEgKSA9PiB7XG5cdFx0cmV0dXJuIGRhdGEuaWQ7XG5cdH07XG5cblx0aW5zdGFuY2VzLmdyaWQgPSBuZXcgR3JpZCggZWwuY29udGFpbmVyLCBncmlkT3B0aW9ucyApO1xufTtcblxuY29uc3QgcmVmcmVzaEdyaWQgPSAoKSA9PiB7XG5cdFxufVxuXG5jb25zdCBhZGRFdmVudExpc3RlbmVycyA9ICgpID0+IHtcblx0Y29uc3QgcmVmcmVzaEJ1dHRvbiA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoICdbZGF0YS1qcz1cInJlZnJlc2hfaW5ib3hcIl0nICk7XG5cdHJlZnJlc2hCdXR0b24uYWRkRXZlbnRMaXN0ZW5lciggJ2NsaWNrJywgZnVuY3Rpb24oIGUgKSB7XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXG5cdH0pO1xufTtcblxuY29uc3QgaW5pdCA9ICggY29udGFpbmVyICkgPT4ge1xuXHRlbC5jb250YWluZXIgPSBjb250YWluZXI7XG5cblx0aW5pdGlhbGl6ZUdyaWQoKTtcblxuXHRhZGRFdmVudExpc3RlbmVycygpO1xuXG5cdGNvbnNvbGUuaW5mbyggJ0dyYXZpdHkgRmxvdyBDb21tb246IEluaXRpYWxpemVkIGluYm94IGNvbXBvbmVudC4nICk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCBpbml0O1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./src/js/common/components/inbox.js\n");

/***/ })

}]);