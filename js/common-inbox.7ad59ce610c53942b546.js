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
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export */ __webpack_require__.d(__webpack_exports__, {\n/* harmony export */   \"default\": () => (__WEBPACK_DEFAULT_EXPORT__)\n/* harmony export */ });\n/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ \"./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js\");\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/regenerator */ \"./node_modules/@babel/runtime/regenerator/index.js\");\n/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_1__);\n/* harmony import */ var ag_grid_community__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ag-grid-community */ \"./node_modules/ag-grid-community/dist/ag-grid-community.cjs.js\");\n\n\n\n/**\n * Inbox\n *\n * Initializes the inbox component\n */\n\nvar el = {};\nvar instances = {};\nvar gridOptions = window.gflow_config.grid_options || {};\n\nvar initializeGrid = function initializeGrid() {\n  gridOptions.getRowNodeId = function (data) {\n    return data.id;\n  };\n\n  instances.grid = new ag_grid_community__WEBPACK_IMPORTED_MODULE_2__.Grid(el.container, gridOptions);\n};\n\nvar getIdsFromModel = function getIdsFromModel() {\n  var ids = [];\n  gridOptions.api.forEachNode(function (node) {\n    return ids.push(node.data.id);\n  });\n  return ids;\n};\n\nvar refreshGrid = /*#__PURE__*/function () {\n  var _ref = (0,_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__.default)( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_1___default().mark(function _callee() {\n    var current_ids, response;\n    return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_1___default().wrap(function _callee$(_context) {\n      while (1) {\n        switch (_context.prev = _context.next) {\n          case 0:\n            current_ids = getIdsFromModel();\n            _context.next = 3;\n            return fetch('https://gravity-dev.local/wp-admin/admin-ajax.php?action=gflow_inbox_refresh_inbox_items', {\n              method: 'post',\n              body: {\n                current_ids: current_ids\n              }\n            });\n\n          case 3:\n            response = _context.sent;\n            console.log(response);\n\n          case 5:\n          case \"end\":\n            return _context.stop();\n        }\n      }\n    }, _callee);\n  }));\n\n  return function refreshGrid() {\n    return _ref.apply(this, arguments);\n  };\n}();\n\nvar addEventListeners = function addEventListeners() {\n  var refreshButton = document.querySelector('[data-js=\"refresh_inbox\"]');\n  refreshButton.addEventListener('click', function (e) {\n    e.preventDefault();\n    refreshGrid();\n  });\n};\n\nvar init = function init(container) {\n  el.container = container;\n  initializeGrid();\n  addEventListeners();\n  console.info('Gravity Flow Common: Initialized inbox component.');\n};\n\n/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (init);//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy9jb21tb24vY29tcG9uZW50cy9pbmJveC5qcz9jMDA0Il0sIm5hbWVzIjpbImVsIiwiaW5zdGFuY2VzIiwiZ3JpZE9wdGlvbnMiLCJ3aW5kb3ciLCJnZmxvd19jb25maWciLCJncmlkX29wdGlvbnMiLCJpbml0aWFsaXplR3JpZCIsImdldFJvd05vZGVJZCIsImRhdGEiLCJpZCIsImdyaWQiLCJHcmlkIiwiY29udGFpbmVyIiwiZ2V0SWRzRnJvbU1vZGVsIiwiaWRzIiwiYXBpIiwiZm9yRWFjaE5vZGUiLCJub2RlIiwicHVzaCIsInJlZnJlc2hHcmlkIiwiY3VycmVudF9pZHMiLCJmZXRjaCIsIm1ldGhvZCIsImJvZHkiLCJyZXNwb25zZSIsImNvbnNvbGUiLCJsb2ciLCJhZGRFdmVudExpc3RlbmVycyIsInJlZnJlc2hCdXR0b24iLCJkb2N1bWVudCIsInF1ZXJ5U2VsZWN0b3IiLCJhZGRFdmVudExpc3RlbmVyIiwiZSIsInByZXZlbnREZWZhdWx0IiwiaW5pdCIsImluZm8iXSwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7O0FBQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUVBO0FBRUEsSUFBTUEsRUFBRSxHQUFHLEVBQVg7QUFDQSxJQUFNQyxTQUFTLEdBQUcsRUFBbEI7QUFFQSxJQUFNQyxXQUFXLEdBQUdDLE1BQU0sQ0FBQ0MsWUFBUCxDQUFvQkMsWUFBcEIsSUFBb0MsRUFBeEQ7O0FBRUEsSUFBTUMsY0FBYyxHQUFHLFNBQWpCQSxjQUFpQixHQUFNO0FBQzVCSixhQUFXLENBQUNLLFlBQVosR0FBMkIsVUFBRUMsSUFBRixFQUFZO0FBQ3RDLFdBQU9BLElBQUksQ0FBQ0MsRUFBWjtBQUNBLEdBRkQ7O0FBSUFSLFdBQVMsQ0FBQ1MsSUFBVixHQUFpQixJQUFJQyxtREFBSixDQUFVWCxFQUFFLENBQUNZLFNBQWIsRUFBd0JWLFdBQXhCLENBQWpCO0FBQ0EsQ0FORDs7QUFRQSxJQUFNVyxlQUFlLEdBQUcsU0FBbEJBLGVBQWtCLEdBQU07QUFDN0IsTUFBSUMsR0FBRyxHQUFHLEVBQVY7QUFFQVosYUFBVyxDQUFDYSxHQUFaLENBQWdCQyxXQUFoQixDQUE0QixVQUFDQyxJQUFEO0FBQUEsV0FBVUgsR0FBRyxDQUFDSSxJQUFKLENBQVNELElBQUksQ0FBQ1QsSUFBTCxDQUFVQyxFQUFuQixDQUFWO0FBQUEsR0FBNUI7QUFFQSxTQUFPSyxHQUFQO0FBQ0EsQ0FORDs7QUFRQSxJQUFNSyxXQUFXO0FBQUEsbUxBQUc7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQ2JDLHVCQURhLEdBQ0NQLGVBQWUsRUFEaEI7QUFBQTtBQUFBLG1CQUdJUSxLQUFLLENBQzNCLDBGQUQyQixFQUUzQjtBQUNDQyxvQkFBTSxFQUFFLE1BRFQ7QUFFQ0Msa0JBQUksRUFBRTtBQUNMSCwyQkFBVyxFQUFYQTtBQURLO0FBRlAsYUFGMkIsQ0FIVDs7QUFBQTtBQUdiSSxvQkFIYTtBQWNuQkMsbUJBQU8sQ0FBQ0MsR0FBUixDQUFhRixRQUFiOztBQWRtQjtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQSxHQUFIOztBQUFBLGtCQUFYTCxXQUFXO0FBQUE7QUFBQTtBQUFBLEdBQWpCOztBQWlCQSxJQUFNUSxpQkFBaUIsR0FBRyxTQUFwQkEsaUJBQW9CLEdBQU07QUFDL0IsTUFBTUMsYUFBYSxHQUFHQyxRQUFRLENBQUNDLGFBQVQsQ0FBd0IsMkJBQXhCLENBQXRCO0FBQ0FGLGVBQWEsQ0FBQ0csZ0JBQWQsQ0FBZ0MsT0FBaEMsRUFBeUMsVUFBVUMsQ0FBVixFQUFjO0FBQ3REQSxLQUFDLENBQUNDLGNBQUY7QUFDQWQsZUFBVztBQUNYLEdBSEQ7QUFJQSxDQU5EOztBQVFBLElBQU1lLElBQUksR0FBRyxTQUFQQSxJQUFPLENBQUV0QixTQUFGLEVBQWlCO0FBQzdCWixJQUFFLENBQUNZLFNBQUgsR0FBZUEsU0FBZjtBQUVBTixnQkFBYztBQUVkcUIsbUJBQWlCO0FBRWpCRixTQUFPLENBQUNVLElBQVIsQ0FBYyxtREFBZDtBQUNBLENBUkQ7O0FBVUEsaUVBQWVELElBQWYiLCJmaWxlIjoiLi9zcmMvanMvY29tbW9uL2NvbXBvbmVudHMvaW5ib3guanMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIEluYm94XG4gKlxuICogSW5pdGlhbGl6ZXMgdGhlIGluYm94IGNvbXBvbmVudFxuICovXG5cbmltcG9ydCB7IEdyaWQgfSBmcm9tICdhZy1ncmlkLWNvbW11bml0eSc7XG5cbmNvbnN0IGVsID0ge307XG5jb25zdCBpbnN0YW5jZXMgPSB7fTtcblxuY29uc3QgZ3JpZE9wdGlvbnMgPSB3aW5kb3cuZ2Zsb3dfY29uZmlnLmdyaWRfb3B0aW9ucyB8fCB7fTtcblxuY29uc3QgaW5pdGlhbGl6ZUdyaWQgPSAoKSA9PiB7XG5cdGdyaWRPcHRpb25zLmdldFJvd05vZGVJZCA9ICggZGF0YSApID0+IHtcblx0XHRyZXR1cm4gZGF0YS5pZDtcblx0fTtcblxuXHRpbnN0YW5jZXMuZ3JpZCA9IG5ldyBHcmlkKCBlbC5jb250YWluZXIsIGdyaWRPcHRpb25zICk7XG59O1xuXG5jb25zdCBnZXRJZHNGcm9tTW9kZWwgPSAoKSA9PiB7XG5cdGxldCBpZHMgPSBbXTtcblxuXHRncmlkT3B0aW9ucy5hcGkuZm9yRWFjaE5vZGUoKG5vZGUpID0+IGlkcy5wdXNoKG5vZGUuZGF0YS5pZCkgKTtcblxuXHRyZXR1cm4gaWRzO1xufTtcblxuY29uc3QgcmVmcmVzaEdyaWQgPSBhc3luYyAoKSA9PiB7XG5cdGNvbnN0IGN1cnJlbnRfaWRzID0gZ2V0SWRzRnJvbU1vZGVsKCk7XG5cblx0Y29uc3QgcmVzcG9uc2UgPSBhd2FpdCBmZXRjaChcblx0XHQnaHR0cHM6Ly9ncmF2aXR5LWRldi5sb2NhbC93cC1hZG1pbi9hZG1pbi1hamF4LnBocD9hY3Rpb249Z2Zsb3dfaW5ib3hfcmVmcmVzaF9pbmJveF9pdGVtcycsXG5cdFx0e1xuXHRcdFx0bWV0aG9kOiAncG9zdCcsXG5cdFx0XHRib2R5OiB7XG5cdFx0XHRcdGN1cnJlbnRfaWRzXG5cdFx0XHR9XG5cdFx0fVxuXHQpO1xuXG5cblx0Y29uc29sZS5sb2coIHJlc3BvbnNlICk7XG59XG5cbmNvbnN0IGFkZEV2ZW50TGlzdGVuZXJzID0gKCkgPT4ge1xuXHRjb25zdCByZWZyZXNoQnV0dG9uID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvciggJ1tkYXRhLWpzPVwicmVmcmVzaF9pbmJveFwiXScgKTtcblx0cmVmcmVzaEJ1dHRvbi5hZGRFdmVudExpc3RlbmVyKCAnY2xpY2snLCBmdW5jdGlvbiggZSApIHtcblx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0cmVmcmVzaEdyaWQoKTtcblx0fSk7XG59O1xuXG5jb25zdCBpbml0ID0gKCBjb250YWluZXIgKSA9PiB7XG5cdGVsLmNvbnRhaW5lciA9IGNvbnRhaW5lcjtcblxuXHRpbml0aWFsaXplR3JpZCgpO1xuXG5cdGFkZEV2ZW50TGlzdGVuZXJzKCk7XG5cblx0Y29uc29sZS5pbmZvKCAnR3Jhdml0eSBGbG93IENvbW1vbjogSW5pdGlhbGl6ZWQgaW5ib3ggY29tcG9uZW50LicgKTtcbn07XG5cbmV4cG9ydCBkZWZhdWx0IGluaXQ7XG4iXSwic291cmNlUm9vdCI6IiJ9\n//# sourceURL=webpack-internal:///./src/js/common/components/inbox.js\n");

/***/ })

}]);