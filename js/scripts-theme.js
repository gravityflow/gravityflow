/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/theme/index.js":
/*!*******************************************!*\
  !*** ./src/js/theme/index.js + 9 modules ***!
  \*******************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("// ESM COMPAT FLAG\n__webpack_require__.r(__webpack_exports__);\n\n// EXTERNAL MODULE: ./node_modules/whatwg-fetch/fetch.js\nvar fetch = __webpack_require__(\"./node_modules/whatwg-fetch/fetch.js\");\n// EXTERNAL MODULE: ./node_modules/lodash/debounce.js\nvar debounce = __webpack_require__(\"./node_modules/lodash/debounce.js\");\nvar debounce_default = /*#__PURE__*/__webpack_require__.n(debounce);\n// EXTERNAL MODULE: ./node_modules/lodash/assign.js\nvar lodash_assign = __webpack_require__(\"./node_modules/lodash/assign.js\");\nvar assign_default = /*#__PURE__*/__webpack_require__.n(lodash_assign);\n;// CONCATENATED MODULE: ./src/js/utils/events.js\n\n\nvar on = function on(el, name, handler) {\n  if (el.addEventListener) {\n    el.addEventListener(name, handler);\n  } else {\n    el.attachEvent(\"on\".concat(name), function () {\n      handler.call(el);\n    });\n  }\n};\n\nvar ready = function ready(fn) {\n  if (document.readyState !== 'loading') {\n    fn();\n  } else if (document.addEventListener) {\n    document.addEventListener('DOMContentLoaded', fn);\n  } else {\n    document.attachEvent('onreadystatechange', function () {\n      if (document.readyState !== 'loading') {\n        fn();\n      }\n    });\n  }\n};\n\nvar trigger = function trigger(opts) {\n  var event;\n\n  var options = assign_default()({\n    data: {},\n    el: document,\n    event: '',\n    native: true\n  }, opts);\n\n  if (options.native) {\n    event = document.createEvent('HTMLEvents');\n    event.initEvent(options.event, true, false);\n  } else {\n    try {\n      event = new window.CustomEvent(options.event, {\n        detail: options.data\n      });\n    } catch (e) {\n      event = document.createEvent('CustomEvent');\n      event.initCustomEvent(options.event, true, true, options.data);\n    }\n  }\n\n  options.el.dispatchEvent(event);\n};\n\n\n;// CONCATENATED MODULE: ./src/js/utils/tests.js\n/**\n * @module\n * @description Some handy test for common issues.\n */\nvar isJson = function isJson(str) {\n  try {\n    JSON.parse(str);\n  } catch (e) {\n    return false;\n  }\n\n  return true;\n};\n\nvar canLocalStore = function canLocalStore() {\n  var mod;\n  var result = false;\n\n  try {\n    mod = new Date();\n    window.localStorage.setItem(mod, mod.toString());\n    result = window.localStorage.getItem(mod) === mod.toString();\n    window.localStorage.removeItem(mod);\n    return result;\n  } catch (_error) {\n    return result;\n  }\n};\n\nvar android = /(android)/i.test(window.navigator.userAgent);\nvar chrome = !!window.chrome;\nvar firefox = typeof InstallTrigger !== 'undefined';\nvar ie =\n/* @cc_on!@ */\n false || document.documentMode || false;\nvar edge = !ie && !!window.StyleMedia;\nvar ios = !!window.navigator.userAgent.match(/(iPod|iPhone|iPad)/i);\nvar iosMobile = !!window.navigator.userAgent.match(/(iPod|iPhone)/i);\nvar opera = !!window.opera || window.navigator.userAgent.indexOf(' OPR/') >= 0;\nvar safari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0 || !chrome && !opera && window.webkitAudioContext !== 'undefined'; // eslint-disable-line\n\nvar os = window.navigator.platform;\n/**\n * do not change to arrow function until testing dependencies are updated beyond the following reported issue\n * https://github.com/facebook/jest/issues/5001\n */\n\nfunction browserTests() {\n  return {\n    android: android,\n    chrome: chrome,\n    edge: edge,\n    firefox: firefox,\n    ie: ie,\n    ios: ios,\n    iosMobile: iosMobile,\n    opera: opera,\n    safari: safari,\n    os: os\n  };\n}\n\n\n;// CONCATENATED MODULE: ./src/js/utils/dom/apply-browser-classes.js\n/**\n * @function browserClasses\n * @description sets up browser classes on body without using user agent strings where possible.\n */\n\n\nvar applyBrowserClasses = function applyBrowserClasses() {\n  var browser = browserTests();\n  var classes = document.body.classList;\n\n  if (browser.android) {\n    classes.add('device-android');\n  } else if (browser.ios) {\n    classes.add('device-ios');\n  }\n\n  if (browser.edge) {\n    classes.add('browser-edge');\n  } else if (browser.chrome) {\n    classes.add('browser-chrome');\n  } else if (browser.firefox) {\n    classes.add('browser-firefox');\n  } else if (browser.ie) {\n    classes.add('browser-ie');\n  } else if (browser.opera) {\n    classes.add('browser-opera');\n  } else if (browser.safari) {\n    classes.add('browser-safari');\n  }\n};\n\n/* harmony default export */ var apply_browser_classes = (applyBrowserClasses);\n// EXTERNAL MODULE: ./node_modules/verge/verge.js\nvar verge = __webpack_require__(\"./node_modules/verge/verge.js\");\nvar verge_default = /*#__PURE__*/__webpack_require__.n(verge);\n;// CONCATENATED MODULE: ./src/js/theme/config/state.js\n/* harmony default export */ var state = ({\n  desktop_initialized: false,\n  is_desktop: false,\n  is_tablet: false,\n  is_mobile: false,\n  mobile_initialized: false,\n  v_height: 0,\n  v_width: 0\n});\n;// CONCATENATED MODULE: ./src/js/theme/config/options.js\n// breakpoint settings\nvar MEDIUM_BREAKPOINT = 768;\nvar FULL_BREAKPOINT = 960;\n;// CONCATENATED MODULE: ./src/js/theme/core/viewport-dims.js\n/**\n * @module\n * @exports viewportDims\n * @description Sets viewport dimensions using verge on shared state\n * and detects mobile or desktop state.\n */\n\n\n\n\nvar viewportDims = function viewportDims() {\n  state.v_height = verge_default().viewportH();\n  state.v_width = verge_default().viewportW();\n\n  if (state.v_width >= FULL_BREAKPOINT) {\n    state.is_desktop = true;\n    state.is_tablet = false;\n    state.is_mobile = false;\n  } else if (state.v_width >= MEDIUM_BREAKPOINT) {\n    state.is_desktop = false;\n    state.is_tablet = true;\n    state.is_mobile = false;\n  } else {\n    state.is_desktop = false;\n    state.is_tablet = false;\n    state.is_mobile = true;\n  }\n};\n\n/* harmony default export */ var viewport_dims = (viewportDims);\n;// CONCATENATED MODULE: ./src/js/theme/core/resize.js\n/**\n * @module\n * @exports resize\n * @description Kicks in any third party plugins that operate on a sitewide basis.\n */\n\n\n\nvar resize = function resize() {\n  // code for resize events can go here\n  viewport_dims();\n  trigger({\n    event: 'gravityflow/resize_executed',\n    native: false\n  });\n};\n\n/* harmony default export */ var core_resize = (resize);\n;// CONCATENATED MODULE: ./src/js/theme/core/plugins.js\n/**\n * @module\n * @exports plugins\n * @description Kicks in any third party plugins that operate on\n * a sitewide basis.\n */\nvar plugins = function plugins() {// initialize global external plugins here\n};\n\n/* harmony default export */ var core_plugins = (plugins);\n;// CONCATENATED MODULE: ./src/js/theme/core/ready.js\n\n\n // @EXAMPLE_REACT_APP\n// import * as tools from 'utils/tools';\n// import { HMR_DEV } from 'config/wp-settings';\n// you MUST do this in every module you use lodash in.\n// A custom bundle of only the lodash you use will be built by babel.\n\n\n\n // @EXAMPLE_REACT_APP\n// const el = {\n// \texampleAppRoot: tools.getNodes( 'example-app' )[ 0 ],\n// };\n\n/**\n * @function bindEvents\n * @description Bind global event listeners here,\n */\n\nvar bindEvents = function bindEvents() {\n  on(window, 'resize', debounce_default()(core_resize, 200, false));\n};\n/**\n * @function init\n * @description The core dispatcher for init across the codebase.\n */\n\n\nvar init = function init() {\n  // apply browser classes\n  apply_browser_classes(); // init external plugins\n\n  core_plugins(); // set initial states\n\n  viewport_dims(); // initialize global events\n\n  bindEvents(); // @EXAMPLE_REACT_APP (Make sure to include the wrapping if block for ALL react apps\n  // #if INCLUDEREACT\n  // if ( el.exampleAppRoot && ! HMR_DEV ) {\n  // \timport( 'Example' /* webpackChunkName:\"example\" */ );\n  // }\n  // #endif\n  // }\n\n  console.info('GravityFlow Theme: Initialized all javascript that targeted document ready.');\n};\n/**\n * @function domReady\n * @description Export our dom ready enabled init.\n */\n\n\nvar domReady = function domReady() {\n  ready(init);\n};\n\n/* harmony default export */ var core_ready = (domReady);\n;// CONCATENATED MODULE: ./src/js/theme/index.js\n\n\ncore_ready();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy91dGlscy9ldmVudHMuanM/MzAxNCIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy91dGlscy90ZXN0cy5qcz81ZmNmIiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3V0aWxzL2RvbS9hcHBseS1icm93c2VyLWNsYXNzZXMuanM/ZmZjOSIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy90aGVtZS9jb25maWcvc3RhdGUuanM/NTU1ZSIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy90aGVtZS9jb25maWcvb3B0aW9ucy5qcz83YmY3Iiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3RoZW1lL2NvcmUvdmlld3BvcnQtZGltcy5qcz8wNDEyIiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3RoZW1lL2NvcmUvcmVzaXplLmpzP2U1ZjciLCJ3ZWJwYWNrOi8vZ3Jhdml0eWZsb3cvLi9zcmMvanMvdGhlbWUvY29yZS9wbHVnaW5zLmpzPzU3MjEiLCJ3ZWJwYWNrOi8vZ3Jhdml0eWZsb3cvLi9zcmMvanMvdGhlbWUvY29yZS9yZWFkeS5qcz8zNWFkIiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3RoZW1lL2luZGV4LmpzPzZiNmYiXSwibmFtZXMiOlsib24iLCJlbCIsIm5hbWUiLCJoYW5kbGVyIiwiYWRkRXZlbnRMaXN0ZW5lciIsImF0dGFjaEV2ZW50IiwiY2FsbCIsInJlYWR5IiwiZm4iLCJkb2N1bWVudCIsInJlYWR5U3RhdGUiLCJ0cmlnZ2VyIiwib3B0cyIsImV2ZW50Iiwib3B0aW9ucyIsImRhdGEiLCJuYXRpdmUiLCJjcmVhdGVFdmVudCIsImluaXRFdmVudCIsIndpbmRvdyIsIkN1c3RvbUV2ZW50IiwiZGV0YWlsIiwiZSIsImluaXRDdXN0b21FdmVudCIsImRpc3BhdGNoRXZlbnQiLCJpc0pzb24iLCJzdHIiLCJKU09OIiwicGFyc2UiLCJjYW5Mb2NhbFN0b3JlIiwibW9kIiwicmVzdWx0IiwiRGF0ZSIsImxvY2FsU3RvcmFnZSIsInNldEl0ZW0iLCJ0b1N0cmluZyIsImdldEl0ZW0iLCJyZW1vdmVJdGVtIiwiX2Vycm9yIiwiYW5kcm9pZCIsInRlc3QiLCJuYXZpZ2F0b3IiLCJ1c2VyQWdlbnQiLCJjaHJvbWUiLCJmaXJlZm94IiwiSW5zdGFsbFRyaWdnZXIiLCJpZSIsImRvY3VtZW50TW9kZSIsImVkZ2UiLCJTdHlsZU1lZGlhIiwiaW9zIiwibWF0Y2giLCJpb3NNb2JpbGUiLCJvcGVyYSIsImluZGV4T2YiLCJzYWZhcmkiLCJPYmplY3QiLCJwcm90b3R5cGUiLCJIVE1MRWxlbWVudCIsIndlYmtpdEF1ZGlvQ29udGV4dCIsIm9zIiwicGxhdGZvcm0iLCJicm93c2VyVGVzdHMiLCJhcHBseUJyb3dzZXJDbGFzc2VzIiwiYnJvd3NlciIsImNsYXNzZXMiLCJib2R5IiwiY2xhc3NMaXN0IiwiYWRkIiwiZGVza3RvcF9pbml0aWFsaXplZCIsImlzX2Rlc2t0b3AiLCJpc190YWJsZXQiLCJpc19tb2JpbGUiLCJtb2JpbGVfaW5pdGlhbGl6ZWQiLCJ2X2hlaWdodCIsInZfd2lkdGgiLCJNRURJVU1fQlJFQUtQT0lOVCIsIkZVTExfQlJFQUtQT0lOVCIsInZpZXdwb3J0RGltcyIsInN0YXRlIiwidmVyZ2UiLCJyZXNpemUiLCJwbHVnaW5zIiwiYmluZEV2ZW50cyIsImluaXQiLCJjb25zb2xlIiwiaW5mbyIsImRvbVJlYWR5Il0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7OztBQU9BLElBQU1BLEVBQUUsR0FBRyxTQUFMQSxFQUFLLENBQUVDLEVBQUYsRUFBTUMsSUFBTixFQUFZQyxPQUFaLEVBQXlCO0FBQ25DLE1BQUtGLEVBQUUsQ0FBQ0csZ0JBQVIsRUFBMkI7QUFDMUJILE1BQUUsQ0FBQ0csZ0JBQUgsQ0FBcUJGLElBQXJCLEVBQTJCQyxPQUEzQjtBQUNBLEdBRkQsTUFFTztBQUNORixNQUFFLENBQUNJLFdBQUgs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ImZpbGUiOiIuL3NyYy9qcy90aGVtZS9pbmRleC5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogQG1vZHVsZVxuICogQGRlc2NyaXB0aW9uIFNvbWUgZXZlbnQgZnVuY3Rpb25zIGZvciB1c2UgaW4gb3RoZXIgbW9kdWxlc1xuICovXG5cbmltcG9ydCBfIGZyb20gJ2xvZGFzaCc7XG5cbmNvbnN0IG9uID0gKCBlbCwgbmFtZSwgaGFuZGxlciApID0+IHtcblx0aWYgKCBlbC5hZGRFdmVudExpc3RlbmVyICkge1xuXHRcdGVsLmFkZEV2ZW50TGlzdGVuZXIoIG5hbWUsIGhhbmRsZXIgKTtcblx0fSBlbHNlIHtcblx0XHRlbC5hdHRhY2hFdmVudCggYG9uJHsgbmFtZSB9YCwgKCkgPT4ge1xuXHRcdFx0aGFuZGxlci5jYWxsKCBlbCApO1xuXHRcdH0gKTtcblx0fVxufTtcblxuY29uc3QgcmVhZHkgPSAoIGZuICkgPT4ge1xuXHRpZiAoIGRvY3VtZW50LnJlYWR5U3RhdGUgIT09ICdsb2FkaW5nJyApIHtcblx0XHRmbigpO1xuXHR9IGVsc2UgaWYgKCBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyICkge1xuXHRcdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoICdET01Db250ZW50TG9hZGVkJywgZm4gKTtcblx0fSBlbHNlIHtcblx0XHRkb2N1bWVudC5hdHRhY2hFdmVudCggJ29ucmVhZHlzdGF0ZWNoYW5nZScsICgpID0+IHtcblx0XHRcdGlmICggZG9jdW1lbnQucmVhZHlTdGF0ZSAhPT0gJ2xvYWRpbmcnICkge1xuXHRcdFx0XHRmbigpO1xuXHRcdFx0fVxuXHRcdH0gKTtcblx0fVxufTtcblxuY29uc3QgdHJpZ2dlciA9ICggb3B0cyApID0+IHtcblx0bGV0IGV2ZW50O1xuXHRjb25zdCBvcHRpb25zID0gXy5hc3NpZ24oXG5cdFx0e1xuXHRcdFx0ZGF0YToge30sXG5cdFx0XHRlbDogZG9jdW1lbnQsXG5cdFx0XHRldmVudDogJycsXG5cdFx0XHRuYXRpdmU6IHRydWUsXG5cdFx0fSxcblx0XHRvcHRzXG5cdCk7XG5cblx0aWYgKCBvcHRpb25zLm5hdGl2ZSApIHtcblx0XHRldmVudCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KCAnSFRNTEV2ZW50cycgKTtcblx0XHRldmVudC5pbml0RXZlbnQoIG9wdGlvbnMuZXZlbnQsIHRydWUsIGZhbHNlICk7XG5cdH0gZWxzZSB7XG5cdFx0dHJ5IHtcblx0XHRcdGV2ZW50ID0gbmV3IHdpbmRvdy5DdXN0b21FdmVudCggb3B0aW9ucy5ldmVudCwge1xuXHRcdFx0XHRkZXRhaWw6IG9wdGlvbnMuZGF0YSxcblx0XHRcdH0gKTtcblx0XHR9IGNhdGNoICggZSApIHtcblx0XHRcdGV2ZW50ID0gZG9jdW1lbnQuY3JlYXRlRXZlbnQoICdDdXN0b21FdmVudCcgKTtcblx0XHRcdGV2ZW50LmluaXRDdXN0b21FdmVudCggb3B0aW9ucy5ldmVudCwgdHJ1ZSwgdHJ1ZSwgb3B0aW9ucy5kYXRhICk7XG5cdFx0fVxuXHR9XG5cblx0b3B0aW9ucy5lbC5kaXNwYXRjaEV2ZW50KCBldmVudCApO1xufTtcblxuZXhwb3J0IHsgb24sIHJlYWR5LCB0cmlnZ2VyIH07XG4iLCIvKipcbiAqIEBtb2R1bGVcbiAqIEBkZXNjcmlwdGlvbiBTb21lIGhhbmR5IHRlc3QgZm9yIGNvbW1vbiBpc3N1ZXMuXG4gKi9cblxuY29uc3QgaXNKc29uID0gKCBzdHIgKSA9PiB7XG5cdHRyeSB7XG5cdFx0SlNPTi5wYXJzZSggc3RyICk7XG5cdH0gY2F0Y2ggKCBlICkge1xuXHRcdHJldHVybiBmYWxzZTtcblx0fVxuXG5cdHJldHVybiB0cnVlO1xufTtcblxuY29uc3QgY2FuTG9jYWxTdG9yZSA9ICgpID0+IHtcblx0bGV0IG1vZDtcblx0bGV0IHJlc3VsdCA9IGZhbHNlO1xuXG5cdHRyeSB7XG5cdFx0bW9kID0gbmV3IERhdGUoKTtcblx0XHR3aW5kb3cubG9jYWxTdG9yYWdlLnNldEl0ZW0oIG1vZCwgbW9kLnRvU3RyaW5nKCkgKTtcblxuXHRcdHJlc3VsdCA9IHdpbmRvdy5sb2NhbFN0b3JhZ2UuZ2V0SXRlbSggbW9kICkgPT09IG1vZC50b1N0cmluZygpO1xuXHRcdHdpbmRvdy5sb2NhbFN0b3JhZ2UucmVtb3ZlSXRlbSggbW9kICk7XG5cdFx0cmV0dXJuIHJlc3VsdDtcblx0fSBjYXRjaCAoIF9lcnJvciApIHtcblx0XHRyZXR1cm4gcmVzdWx0O1xuXHR9XG59O1xuXG5jb25zdCBhbmRyb2lkID0gLyhhbmRyb2lkKS9pLnRlc3QoIHdpbmRvdy5uYXZpZ2F0b3IudXNlckFnZW50ICk7XG5jb25zdCBjaHJvbWUgPSAhISB3aW5kb3cuY2hyb21lO1xuY29uc3QgZmlyZWZveCA9IHR5cGVvZiBJbnN0YWxsVHJpZ2dlciAhPT0gJ3VuZGVmaW5lZCc7XG5jb25zdCBpZSA9IC8qIEBjY19vbiFAICovIGZhbHNlIHx8IGRvY3VtZW50LmRvY3VtZW50TW9kZSB8fCBmYWxzZTtcbmNvbnN0IGVkZ2UgPSAhIGllICYmICEhIHdpbmRvdy5TdHlsZU1lZGlhO1xuY29uc3QgaW9zID0gISEgd2luZG93Lm5hdmlnYXRvci51c2VyQWdlbnQubWF0Y2goIC8oaVBvZHxpUGhvbmV8aVBhZCkvaSApO1xuY29uc3QgaW9zTW9iaWxlID0gISEgd2luZG93Lm5hdmlnYXRvci51c2VyQWdlbnQubWF0Y2goIC8oaVBvZHxpUGhvbmUpL2kgKTtcbmNvbnN0IG9wZXJhID1cblx0ISEgd2luZG93Lm9wZXJhIHx8IHdpbmRvdy5uYXZpZ2F0b3IudXNlckFnZW50LmluZGV4T2YoICcgT1BSLycgKSA+PSAwO1xuY29uc3Qgc2FmYXJpID0gT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5jYWxsKHdpbmRvdy5IVE1MRWxlbWVudCkuaW5kZXhPZignQ29uc3RydWN0b3InKSA+IDAgfHwgIWNocm9tZSAmJiAhb3BlcmEgJiYgd2luZG93LndlYmtpdEF1ZGlvQ29udGV4dCAhPT0gJ3VuZGVmaW5lZCc7IC8vIGVzbGludC1kaXNhYmxlLWxpbmVcbmNvbnN0IG9zID0gd2luZG93Lm5hdmlnYXRvci5wbGF0Zm9ybTtcblxuLyoqXG4gKiBkbyBub3QgY2hhbmdlIHRvIGFycm93IGZ1bmN0aW9uIHVudGlsIHRlc3RpbmcgZGVwZW5kZW5jaWVzIGFyZSB1cGRhdGVkIGJleW9uZCB0aGUgZm9sbG93aW5nIHJlcG9ydGVkIGlzc3VlXG4gKiBodHRwczovL2dpdGh1Yi5jb20vZmFjZWJvb2svamVzdC9pc3N1ZXMvNTAwMVxuICovXG5mdW5jdGlvbiBicm93c2VyVGVzdHMoKSB7XG5cdHJldHVybiB7XG5cdFx0YW5kcm9pZCxcblx0XHRjaHJvbWUsXG5cdFx0ZWRnZSxcblx0XHRmaXJlZm94LFxuXHRcdGllLFxuXHRcdGlvcyxcblx0XHRpb3NNb2JpbGUsXG5cdFx0b3BlcmEsXG5cdFx0c2FmYXJpLFxuXHRcdG9zLFxuXHR9O1xufVxuXG5leHBvcnQgeyBpc0pzb24sIGNhbkxvY2FsU3RvcmUsIGJyb3dzZXJUZXN0cyB9O1xuIiwiLyoqXG4gKiBAZnVuY3Rpb24gYnJvd3NlckNsYXNzZXNcbiAqIEBkZXNjcmlwdGlvbiBzZXRzIHVwIGJyb3dzZXIgY2xhc3NlcyBvbiBib2R5IHdpdGhvdXQgdXNpbmcgdXNlciBhZ2VudCBzdHJpbmdzIHdoZXJlIHBvc3NpYmxlLlxuICovXG5cbmltcG9ydCAqIGFzIHRlc3QgZnJvbSAnLi4vdGVzdHMnO1xuXG5jb25zdCBhcHBseUJyb3dzZXJDbGFzc2VzID0gKCkgPT4ge1xuXHRjb25zdCBicm93c2VyID0gdGVzdC5icm93c2VyVGVzdHMoKTtcblx0Y29uc3QgY2xhc3NlcyA9IGRvY3VtZW50LmJvZHkuY2xhc3NMaXN0O1xuXG5cdGlmICggYnJvd3Nlci5hbmRyb2lkICkge1xuXHRcdGNsYXNzZXMuYWRkKCAnZGV2aWNlLWFuZHJvaWQnICk7XG5cdH0gZWxzZSBpZiAoIGJyb3dzZXIuaW9zICkge1xuXHRcdGNsYXNzZXMuYWRkKCAnZGV2aWNlLWlvcycgKTtcblx0fVxuXG5cdGlmICggYnJvd3Nlci5lZGdlICkge1xuXHRcdGNsYXNzZXMuYWRkKCAnYnJvd3Nlci1lZGdlJyApO1xuXHR9IGVsc2UgaWYgKCBicm93c2VyLmNocm9tZSApIHtcblx0XHRjbGFzc2VzLmFkZCggJ2Jyb3dzZXItY2hyb21lJyApO1xuXHR9IGVsc2UgaWYgKCBicm93c2VyLmZpcmVmb3ggKSB7XG5cdFx0Y2xhc3Nlcy5hZGQoICdicm93c2VyLWZpcmVmb3gnICk7XG5cdH0gZWxzZSBpZiAoIGJyb3dzZXIuaWUgKSB7XG5cdFx0Y2xhc3Nlcy5hZGQoICdicm93c2VyLWllJyApO1xuXHR9IGVsc2UgaWYgKCBicm93c2VyLm9wZXJhICkge1xuXHRcdGNsYXNzZXMuYWRkKCAnYnJvd3Nlci1vcGVyYScgKTtcblx0fSBlbHNlIGlmICggYnJvd3Nlci5zYWZhcmkgKSB7XG5cdFx0Y2xhc3Nlcy5hZGQoICdicm93c2VyLXNhZmFyaScgKTtcblx0fVxufTtcblxuZXhwb3J0IGRlZmF1bHQgYXBwbHlCcm93c2VyQ2xhc3NlcztcbiIsImV4cG9ydCBkZWZhdWx0IHtcblx0ZGVza3RvcF9pbml0aWFsaXplZDogZmFsc2UsXG5cdGlzX2Rlc2t0b3A6IGZhbHNlLFxuXHRpc190YWJsZXQ6IGZhbHNlLFxuXHRpc19tb2JpbGU6IGZhbHNlLFxuXHRtb2JpbGVfaW5pdGlhbGl6ZWQ6IGZhbHNlLFxuXHR2X2hlaWdodDogMCxcblx0dl93aWR0aDogMCxcbn07XG4iLCIvLyBicmVha3BvaW50IHNldHRpbmdzXG5cbmV4cG9ydCBjb25zdCBNRURJVU1fQlJFQUtQT0lOVCA9IDc2ODtcbmV4cG9ydCBjb25zdCBGVUxMX0JSRUFLUE9JTlQgPSA5NjA7XG4iLCIvKipcbiAqIEBtb2R1bGVcbiAqIEBleHBvcnRzIHZpZXdwb3J0RGltc1xuICogQGRlc2NyaXB0aW9uIFNldHMgdmlld3BvcnQgZGltZW5zaW9ucyB1c2luZyB2ZXJnZSBvbiBzaGFyZWQgc3RhdGVcbiAqIGFuZCBkZXRlY3RzIG1vYmlsZSBvciBkZXNrdG9wIHN0YXRlLlxuICovXG5cbmltcG9ydCB2ZXJnZSBmcm9tICd2ZXJnZSc7XG5pbXBvcnQgc3RhdGUgZnJvbSAnLi4vY29uZmlnL3N0YXRlJztcbmltcG9ydCB7IEZVTExfQlJFQUtQT0lOVCwgTUVESVVNX0JSRUFLUE9JTlQgfSBmcm9tICcuLi9jb25maWcvb3B0aW9ucyc7XG5cbmNvbnN0IHZpZXdwb3J0RGltcyA9ICgpID0+IHtcblx0c3RhdGUudl9oZWlnaHQgPSB2ZXJnZS52aWV3cG9ydEgoKTtcblx0c3RhdGUudl93aWR0aCA9IHZlcmdlLnZpZXdwb3J0VygpO1xuXG5cdGlmICggc3RhdGUudl93aWR0aCA+PSBGVUxMX0JSRUFLUE9JTlQgKSB7XG5cdFx0c3RhdGUuaXNfZGVza3RvcCA9IHRydWU7XG5cdFx0c3RhdGUuaXNfdGFibGV0ID0gZmFsc2U7XG5cdFx0c3RhdGUuaXNfbW9iaWxlID0gZmFsc2U7XG5cdH0gZWxzZSBpZiAoIHN0YXRlLnZfd2lkdGggPj0gTUVESVVNX0JSRUFLUE9JTlQgKSB7XG5cdFx0c3RhdGUuaXNfZGVza3RvcCA9IGZhbHNlO1xuXHRcdHN0YXRlLmlzX3RhYmxldCA9IHRydWU7XG5cdFx0c3RhdGUuaXNfbW9iaWxlID0gZmFsc2U7XG5cdH0gZWxzZSB7XG5cdFx0c3RhdGUuaXNfZGVza3RvcCA9IGZhbHNlO1xuXHRcdHN0YXRlLmlzX3RhYmxldCA9IGZhbHNlO1xuXHRcdHN0YXRlLmlzX21vYmlsZSA9IHRydWU7XG5cdH1cbn07XG5cbmV4cG9ydCBkZWZhdWx0IHZpZXdwb3J0RGltcztcbiIsIi8qKlxuICogQG1vZHVsZVxuICogQGV4cG9ydHMgcmVzaXplXG4gKiBAZGVzY3JpcHRpb24gS2lja3MgaW4gYW55IHRoaXJkIHBhcnR5IHBsdWdpbnMgdGhhdCBvcGVyYXRlIG9uIGEgc2l0ZXdpZGUgYmFzaXMuXG4gKi9cblxuaW1wb3J0IHsgdHJpZ2dlciB9IGZyb20gJ3V0aWxzL2V2ZW50cyc7XG5pbXBvcnQgdmlld3BvcnREaW1zIGZyb20gJy4vdmlld3BvcnQtZGltcyc7XG5cbmNvbnN0IHJlc2l6ZSA9ICgpID0+IHtcblx0Ly8gY29kZSBmb3IgcmVzaXplIGV2ZW50cyBjYW4gZ28gaGVyZVxuXG5cdHZpZXdwb3J0RGltcygpO1xuXG5cdHRyaWdnZXIoIHsgZXZlbnQ6ICdncmF2aXR5Zmxvdy9yZXNpemVfZXhlY3V0ZWQnLCBuYXRpdmU6IGZhbHNlIH0gKTtcbn07XG5cbmV4cG9ydCBkZWZhdWx0IHJlc2l6ZTtcbiIsIi8qKlxuICogQG1vZHVsZVxuICogQGV4cG9ydHMgcGx1Z2luc1xuICogQGRlc2NyaXB0aW9uIEtpY2tzIGluIGFueSB0aGlyZCBwYXJ0eSBwbHVnaW5zIHRoYXQgb3BlcmF0ZSBvblxuICogYSBzaXRld2lkZSBiYXNpcy5cbiAqL1xuXG5jb25zdCBwbHVnaW5zID0gKCkgPT4ge1xuXHQvLyBpbml0aWFsaXplIGdsb2JhbCBleHRlcm5hbCBwbHVnaW5zIGhlcmVcbn07XG5cbmV4cG9ydCBkZWZhdWx0IHBsdWdpbnM7XG4iLCIvKipcbiAqIEBtb2R1bGVcbiAqIEBleHBvcnRzIHJlYWR5XG4gKiBAZGVzY3JpcHRpb24gVGhlIGNvcmUgZGlzcGF0Y2hlciBmb3IgdGhlIGRvbSByZWFkeSBldmVudCBqYXZhc2NyaXB0LlxuICovXG5cbmltcG9ydCBfIGZyb20gJ2xvZGFzaCc7XG5pbXBvcnQgeyBvbiwgcmVhZHkgfSBmcm9tICd1dGlscy9ldmVudHMnO1xuaW1wb3J0IGFwcGx5QnJvd3NlckNsYXNzZXMgZnJvbSAndXRpbHMvZG9tL2FwcGx5LWJyb3dzZXItY2xhc3Nlcyc7XG4vLyBARVhBTVBMRV9SRUFDVF9BUFBcblxuLy8gaW1wb3J0ICogYXMgdG9vbHMgZnJvbSAndXRpbHMvdG9vbHMnO1xuLy8gaW1wb3J0IHsgSE1SX0RFViB9IGZyb20gJ2NvbmZpZy93cC1zZXR0aW5ncyc7XG5cbi8vIHlvdSBNVVNUIGRvIHRoaXMgaW4gZXZlcnkgbW9kdWxlIHlvdSB1c2UgbG9kYXNoIGluLlxuLy8gQSBjdXN0b20gYnVuZGxlIG9mIG9ubHkgdGhlIGxvZGFzaCB5b3UgdXNlIHdpbGwgYmUgYnVpbHQgYnkgYmFiZWwuXG5cbmltcG9ydCByZXNpemUgZnJvbSAnLi9yZXNpemUnO1xuaW1wb3J0IHBsdWdpbnMgZnJvbSAnLi9wbHVnaW5zJztcbmltcG9ydCB2aWV3cG9ydERpbXMgZnJvbSAnLi92aWV3cG9ydC1kaW1zJztcblxuLy8gQEVYQU1QTEVfUkVBQ1RfQVBQXG5cbi8vIGNvbnN0IGVsID0ge1xuLy8gXHRleGFtcGxlQXBwUm9vdDogdG9vbHMuZ2V0Tm9kZXMoICdleGFtcGxlLWFwcCcgKVsgMCBdLFxuLy8gfTtcblxuLyoqXG4gKiBAZnVuY3Rpb24gYmluZEV2ZW50c1xuICogQGRlc2NyaXB0aW9uIEJpbmQgZ2xvYmFsIGV2ZW50IGxpc3RlbmVycyBoZXJlLFxuICovXG5cbmNvbnN0IGJpbmRFdmVudHMgPSAoKSA9PiB7XG5cdG9uKCB3aW5kb3csICdyZXNpemUnLCBfLmRlYm91bmNlKCByZXNpemUsIDIwMCwgZmFsc2UgKSApO1xufTtcblxuLyoqXG4gKiBAZnVuY3Rpb24gaW5pdFxuICogQGRlc2NyaXB0aW9uIFRoZSBjb3JlIGRpc3BhdGNoZXIgZm9yIGluaXQgYWNyb3NzIHRoZSBjb2RlYmFzZS5cbiAqL1xuXG5jb25zdCBpbml0ID0gKCkgPT4ge1xuXHQvLyBhcHBseSBicm93c2VyIGNsYXNzZXNcblxuXHRhcHBseUJyb3dzZXJDbGFzc2VzKCk7XG5cblx0Ly8gaW5pdCBleHRlcm5hbCBwbHVnaW5zXG5cblx0cGx1Z2lucygpO1xuXG5cdC8vIHNldCBpbml0aWFsIHN0YXRlc1xuXG5cdHZpZXdwb3J0RGltcygpO1xuXG5cdC8vIGluaXRpYWxpemUgZ2xvYmFsIGV2ZW50c1xuXG5cdGJpbmRFdmVudHMoKTtcblxuXHQvLyBARVhBTVBMRV9SRUFDVF9BUFAgKE1ha2Ugc3VyZSB0byBpbmNsdWRlIHRoZSB3cmFwcGluZyBpZiBibG9jayBmb3IgQUxMIHJlYWN0IGFwcHNcblxuXHQvLyAjaWYgSU5DTFVERVJFQUNUXG5cdC8vIGlmICggZWwuZXhhbXBsZUFwcFJvb3QgJiYgISBITVJfREVWICkge1xuXHQvLyBcdGltcG9ydCggJ0V4YW1wbGUnIC8qIHdlYnBhY2tDaHVua05hbWU6XCJleGFtcGxlXCIgKi8gKTtcblx0Ly8gfVxuXHQvLyAjZW5kaWZcblx0Ly8gfVxuXG5cdGNvbnNvbGUuaW5mbyhcblx0XHQnR3Jhdml0eUZsb3cgVGhlbWU6IEluaXRpYWxpemVkIGFsbCBqYXZhc2NyaXB0IHRoYXQgdGFyZ2V0ZWQgZG9jdW1lbnQgcmVhZHkuJ1xuXHQpO1xufTtcblxuLyoqXG4gKiBAZnVuY3Rpb24gZG9tUmVhZHlcbiAqIEBkZXNjcmlwdGlvbiBFeHBvcnQgb3VyIGRvbSByZWFkeSBlbmFibGVkIGluaXQuXG4gKi9cblxuY29uc3QgZG9tUmVhZHkgPSAoKSA9PiB7XG5cdHJlYWR5KCBpbml0ICk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCBkb21SZWFkeTtcbiIsImltcG9ydCAnd2hhdHdnLWZldGNoJztcbmltcG9ydCByZWFkeSBmcm9tICcuL2NvcmUvcmVhZHknO1xuXG5yZWFkeSgpO1xuIl0sInNvdXJjZVJvb3QiOiIifQ==\n//# sourceURL=webpack-internal:///./src/js/theme/index.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		if(__webpack_module_cache__[moduleId]) {
/******/ 			return __webpack_module_cache__[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			id: moduleId,
/******/ 			loaded: false,
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/******/ 	// the startup function
/******/ 	// It's empty as some runtime module handles the default behavior
/******/ 	__webpack_require__.x = function() {};
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	!function() {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/node module decorator */
/******/ 	!function() {
/******/ 		__webpack_require__.nmd = function(module) {
/******/ 			module.paths = [];
/******/ 			if (!module.children) module.children = [];
/******/ 			return module;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	!function() {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// Promise = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"scripts-theme": 0
/******/ 		};
/******/ 		
/******/ 		var deferredModules = [
/******/ 			["./node_modules/core-js/modules/es.array.iterator.js","vendor-theme"],
/******/ 			["./src/js/theme/index.js","vendor-theme"]
/******/ 		];
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		var checkDeferredModules = function() {};
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = function(parentChunkLoadingFunction, data) {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			var executeModules = data[3];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0, resolves = [];
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					resolves.push(installedChunks[chunkId][0]);
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			for(moduleId in moreModules) {
/******/ 				if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 					__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 				}
/******/ 			}
/******/ 			if(runtime) runtime(__webpack_require__);
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			while(resolves.length) {
/******/ 				resolves.shift()();
/******/ 			}
/******/ 		
/******/ 			// add entry modules from loaded chunk to deferred list
/******/ 			if(executeModules) deferredModules.push.apply(deferredModules, executeModules);
/******/ 		
/******/ 			// run deferred modules when all chunks ready
/******/ 			return checkDeferredModules();
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkgravityflow"] = self["webpackChunkgravityflow"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 		
/******/ 		function checkDeferredModulesImpl() {
/******/ 			var result;
/******/ 			for(var i = 0; i < deferredModules.length; i++) {
/******/ 				var deferredModule = deferredModules[i];
/******/ 				var fulfilled = true;
/******/ 				for(var j = 1; j < deferredModule.length; j++) {
/******/ 					var depId = deferredModule[j];
/******/ 					if(installedChunks[depId] !== 0) fulfilled = false;
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferredModules.splice(i--, 1);
/******/ 					result = __webpack_require__(__webpack_require__.s = deferredModule[0]);
/******/ 				}
/******/ 			}
/******/ 			if(deferredModules.length === 0) {
/******/ 				__webpack_require__.x();
/******/ 				__webpack_require__.x = function() {};
/******/ 			}
/******/ 			return result;
/******/ 		}
/******/ 		var startup = __webpack_require__.x;
/******/ 		__webpack_require__.x = function() {
/******/ 			// reset startup function so it can be called again when more startup code is added
/******/ 			__webpack_require__.x = startup || (function() {});
/******/ 			return (checkDeferredModules = checkDeferredModulesImpl)();
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// run startup
/******/ 	var __webpack_exports__ = __webpack_require__.x();
/******/ 	
/******/ })()
;