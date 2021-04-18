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
/*!********************************************!*\
  !*** ./src/js/theme/index.js + 12 modules ***!
  \********************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

eval("// ESM COMPAT FLAG\n__webpack_require__.r(__webpack_exports__);\n\n// EXTERNAL MODULE: ./node_modules/whatwg-fetch/fetch.js\nvar fetch = __webpack_require__(\"./node_modules/whatwg-fetch/fetch.js\");\n// EXTERNAL MODULE: ./node_modules/lodash/debounce.js\nvar debounce = __webpack_require__(\"./node_modules/lodash/debounce.js\");\nvar debounce_default = /*#__PURE__*/__webpack_require__.n(debounce);\n// EXTERNAL MODULE: ./node_modules/lodash/assign.js\nvar lodash_assign = __webpack_require__(\"./node_modules/lodash/assign.js\");\nvar assign_default = /*#__PURE__*/__webpack_require__.n(lodash_assign);\n;// CONCATENATED MODULE: ./src/js/utils/events.js\n\n\nvar on = function on(el, name, handler) {\n  if (el.addEventListener) {\n    el.addEventListener(name, handler);\n  } else {\n    el.attachEvent(\"on\".concat(name), function () {\n      handler.call(el);\n    });\n  }\n};\n\nvar ready = function ready(fn) {\n  if (document.readyState !== 'loading') {\n    fn();\n  } else if (document.addEventListener) {\n    document.addEventListener('DOMContentLoaded', fn);\n  } else {\n    document.attachEvent('onreadystatechange', function () {\n      if (document.readyState !== 'loading') {\n        fn();\n      }\n    });\n  }\n};\n\nvar trigger = function trigger(opts) {\n  var event;\n\n  var options = assign_default()({\n    data: {},\n    el: document,\n    event: '',\n    native: true\n  }, opts);\n\n  if (options.native) {\n    event = document.createEvent('HTMLEvents');\n    event.initEvent(options.event, true, false);\n  } else {\n    try {\n      event = new window.CustomEvent(options.event, {\n        detail: options.data\n      });\n    } catch (e) {\n      event = document.createEvent('CustomEvent');\n      event.initCustomEvent(options.event, true, true, options.data);\n    }\n  }\n\n  options.el.dispatchEvent(event);\n};\n\n\n;// CONCATENATED MODULE: ./src/js/utils/tests.js\n/**\n * @module\n * @description Some handy test for common issues.\n */\nvar isJson = function isJson(str) {\n  try {\n    JSON.parse(str);\n  } catch (e) {\n    return false;\n  }\n\n  return true;\n};\n\nvar canLocalStore = function canLocalStore() {\n  var mod;\n  var result = false;\n\n  try {\n    mod = new Date();\n    window.localStorage.setItem(mod, mod.toString());\n    result = window.localStorage.getItem(mod) === mod.toString();\n    window.localStorage.removeItem(mod);\n    return result;\n  } catch (_error) {\n    return result;\n  }\n};\n\nvar android = /(android)/i.test(window.navigator.userAgent);\nvar chrome = !!window.chrome;\nvar firefox = typeof InstallTrigger !== 'undefined';\nvar ie =\n/* @cc_on!@ */\n false || document.documentMode || false;\nvar edge = !ie && !!window.StyleMedia;\nvar ios = !!window.navigator.userAgent.match(/(iPod|iPhone|iPad)/i);\nvar iosMobile = !!window.navigator.userAgent.match(/(iPod|iPhone)/i);\nvar opera = !!window.opera || window.navigator.userAgent.indexOf(' OPR/') >= 0;\nvar safari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0 || !chrome && !opera && window.webkitAudioContext !== 'undefined'; // eslint-disable-line\n\nvar os = window.navigator.platform;\n/**\n * do not change to arrow function until testing dependencies are updated beyond the following reported issue\n * https://github.com/facebook/jest/issues/5001\n */\n\nfunction browserTests() {\n  return {\n    android: android,\n    chrome: chrome,\n    edge: edge,\n    firefox: firefox,\n    ie: ie,\n    ios: ios,\n    iosMobile: iosMobile,\n    opera: opera,\n    safari: safari,\n    os: os\n  };\n}\n\n\n;// CONCATENATED MODULE: ./src/js/utils/dom/apply-browser-classes.js\n/**\n * @function browserClasses\n * @description sets up browser classes on body without using user agent strings where possible.\n */\n\n\nvar applyBrowserClasses = function applyBrowserClasses() {\n  var browser = browserTests();\n  var classes = document.body.classList;\n\n  if (browser.android) {\n    classes.add('device-android');\n  } else if (browser.ios) {\n    classes.add('device-ios');\n  }\n\n  if (browser.edge) {\n    classes.add('browser-edge');\n  } else if (browser.chrome) {\n    classes.add('browser-chrome');\n  } else if (browser.firefox) {\n    classes.add('browser-firefox');\n  } else if (browser.ie) {\n    classes.add('browser-ie');\n  } else if (browser.opera) {\n    classes.add('browser-opera');\n  } else if (browser.safari) {\n    classes.add('browser-safari');\n  }\n};\n\n/* harmony default export */ var apply_browser_classes = (applyBrowserClasses);\n// EXTERNAL MODULE: ./node_modules/verge/verge.js\nvar verge = __webpack_require__(\"./node_modules/verge/verge.js\");\nvar verge_default = /*#__PURE__*/__webpack_require__.n(verge);\n;// CONCATENATED MODULE: ./src/js/theme/config/state.js\n/* harmony default export */ var state = ({\n  desktop_initialized: false,\n  is_desktop: false,\n  is_tablet: false,\n  is_mobile: false,\n  mobile_initialized: false,\n  v_height: 0,\n  v_width: 0\n});\n;// CONCATENATED MODULE: ./src/js/theme/config/options.js\n// breakpoint settings\nvar MEDIUM_BREAKPOINT = 768;\nvar FULL_BREAKPOINT = 960;\n;// CONCATENATED MODULE: ./src/js/theme/core/viewport-dims.js\n/**\n * @module\n * @exports viewportDims\n * @description Sets viewport dimensions using verge on shared state\n * and detects mobile or desktop state.\n */\n\n\n\n\nvar viewportDims = function viewportDims() {\n  state.v_height = verge_default().viewportH();\n  state.v_width = verge_default().viewportW();\n\n  if (state.v_width >= FULL_BREAKPOINT) {\n    state.is_desktop = true;\n    state.is_tablet = false;\n    state.is_mobile = false;\n  } else if (state.v_width >= MEDIUM_BREAKPOINT) {\n    state.is_desktop = false;\n    state.is_tablet = true;\n    state.is_mobile = false;\n  } else {\n    state.is_desktop = false;\n    state.is_tablet = false;\n    state.is_mobile = true;\n  }\n};\n\n/* harmony default export */ var viewport_dims = (viewportDims);\n;// CONCATENATED MODULE: ./src/js/theme/core/resize.js\n/**\n * @module\n * @exports resize\n * @description Kicks in any third party plugins that operate on a sitewide basis.\n */\n\n\n\nvar resize = function resize() {\n  // code for resize events can go here\n  viewport_dims();\n  trigger({\n    event: 'gravityflow/resize_executed',\n    native: false\n  });\n};\n\n/* harmony default export */ var core_resize = (resize);\n;// CONCATENATED MODULE: ./src/js/theme/core/plugins.js\n/**\n * @module\n * @exports plugins\n * @description Kicks in any third party plugins that operate on\n * a sitewide basis.\n */\nvar plugins = function plugins() {// initialize global external plugins here\n};\n\n/* harmony default export */ var core_plugins = (plugins);\n;// CONCATENATED MODULE: ./src/js/utils/tools.js\n/**\n * @module\n * @description Some vanilla js cross browser utils\n */\n\n/**\n * Add a class to a dom element or exit safely if not set\n *\n * @param el Node\n * @param className Class string\n * @return {*} Node or false\n */\nvar addClass = function addClass(el) {\n  var className = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';\n  var element = el;\n\n  if (!element) {\n    return false;\n  }\n\n  element.classList.add(className);\n  return element;\n};\n/**\n *\n * Get immediate child nodes and return an array of them\n *\n * @param el\n * @return {Array} Iterable array of dom nodes\n */\n\nvar getChildren = function getChildren(el) {\n  var children = [];\n  var i = el.children.length;\n\n  for (i; i--;) {\n    // eslint-disable-line\n    if (el.children[i].nodeType !== 8) {\n      children.unshift(el.children[i]);\n    }\n  }\n\n  return children;\n};\n/**\n *\n * Test if a dom node has a class or returns false if el not defined\n *\n * @param el\n * @param className\n * @return {boolean}\n */\n\nvar hasClass = function hasClass(el) {\n  var className = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';\n\n  if (!el) {\n    return false;\n  }\n\n  return el.classList.contains(className);\n};\n/**\n * Removes a class from the dom node\n *\n * @param el\n * @param className\n * @return {*} returns false or el if passed\n */\n\nvar removeClass = function removeClass(el, className) {\n  var element = el;\n\n  if (!element) {\n    return false;\n  }\n\n  element.classList.remove(className);\n  return element;\n};\n/**\n * Remove a class from an element that contains a string\n *\n * @param el\n * @param string\n */\n\nvar removeClassThatContains = function removeClassThatContains(el) {\n  var string = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';\n\n  for (var i = 0; i < el.classList.length; i++) {\n    if (el.classList.item(i).indexOf(string) !== -1) {\n      el.classList.remove(el.classList.item(i));\n    }\n  }\n};\n/**\n * Compares an els classList against an array of strings to see if any match\n *\n * @param el the element to check against\n * @param arr The array of classes as strings to test against\n * @param prefix optional prefix string applied to all test strings\n * @param suffix optional suffix string\n */\n\nvar hasClassFromArray = function hasClassFromArray(el) {\n  var arr = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];\n  var prefix = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';\n  var suffix = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '';\n  return arr.some(function (c) {\n    return el.classList.contains(\"\".concat(prefix).concat(c).concat(suffix));\n  });\n};\n/**\n * Highly efficient function to convert a nodelist into a standard array. Allows you to run Array.forEach\n *\n * @param {Element|NodeList} elements to convert\n * @return {Array} Of converted elements\n */\n\nvar convertElements = function convertElements() {\n  var elements = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];\n  var converted = [];\n  var i = elements.length;\n\n  for (i; i--; converted.unshift(elements[i])) {\n    ;\n  } // eslint-disable-line\n\n\n  return converted;\n};\n/**\n * Should be used at all times for getting nodes throughout our app. Please use the data-js attribute whenever possible\n *\n * @param selector The selector string to search for. If arg 4 is false (default) then we search for [data-js=\"selector\"]\n * @param convert Convert the NodeList to an array? Then we can Array.forEach directly. Uses convertElements from above\n * @param node Parent node to search from. Defaults to document\n * @param custom Is this a custom selector where we don't want to use the data-js attribute?\n * @return {NodeList}\n */\n\nvar getNodes = function getNodes() {\n  var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';\n  var convert = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;\n  var node = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : document;\n  var custom = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;\n  var selectorString = custom ? selector : \"[data-js=\\\"\".concat(selector, \"\\\"]\");\n  var nodes = node.querySelectorAll(selectorString);\n\n  if (convert) {\n    nodes = convertElements(nodes);\n  }\n\n  return nodes;\n};\n/**\n * Util to see if we should load a given chunk on a page. Just add data-load-chunk-products to load that particular\n * one on any element on the page.\n * Handled in assets/js/src/theme/core/components.js and assets/js/src/theme/core/pages.js\n *\n * @param name chunk name\n */\n\nvar shouldLoadChunk = function shouldLoadChunk() {\n  var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';\n  var node = document.querySelectorAll(\"[data-load-chunk-\".concat(name, \"]\"));\n  return node.length > 0;\n};\n/**\n * Gets the closest ancestor that matches a selector string\n *\n * @param el\n * @param selector\n * @return {*}\n */\n\nvar closest = function closest(el, selector) {\n  var matchesFn;\n  var parent;\n  ['matches', 'webkitMatchesSelector', 'mozMatchesSelector', 'msMatchesSelector', 'oMatchesSelector'].some(function (fn) {\n    if (typeof document.body[fn] === 'function') {\n      matchesFn = fn;\n      return true;\n    }\n    /* istanbul ignore next */\n\n\n    return false;\n  });\n\n  while (el) {\n    parent = el.parentElement;\n\n    if (parent && parent[matchesFn](selector)) {\n      return parent;\n    }\n\n    el = parent; // eslint-disable-line\n  }\n\n  return null;\n};\n/**\n * Insert a node after another node\n *\n * @param newNode {Element|NodeList}\n * @param referenceNode {Element|NodeList}\n */\n\nvar insertAfter = function insertAfter(newNode, referenceNode) {\n  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextElementSibling);\n};\n/**\n * Insert a node before another node\n *\n * @param newNode {Element|NodeList}\n * @param referenceNode {Element|NodeList}\n */\n\nvar insertBefore = function insertBefore(newNode, referenceNode) {\n  referenceNode.parentNode.insertBefore(newNode, referenceNode);\n};\n/**\n * Set multiple element attributes at once\n *\n * @param el\n * @param attrs\n */\n\nvar setAttributes = function setAttributes(el, attrs) {\n  for (var key in attrs) {\n    el.setAttribute(key, attrs[key]);\n  }\n};\n;// CONCATENATED MODULE: ./src/js/common/components/index.js\n/**\n * Components\n *\n * Initializes all common components\n */\n\nvar el = {\n  inbox: getNodes('gflow-inbox')[0]\n};\n/**\n * @function init\n * @description Initialize the module\n */\n\nvar init = function init() {\n  if (el.inbox) {\n    __webpack_require__.e(/*! import() | common-inbox */ \"common-inbox\").then(__webpack_require__.bind(__webpack_require__, /*! ./inbox */ \"./src/js/common/components/inbox.js\")).then(function (module) {\n      module.default(el.inbox);\n    });\n  }\n\n  console.info('Gravity Flow Common: Initialized all common components.');\n};\n\n/* harmony default export */ var components = (init);\n;// CONCATENATED MODULE: ./src/js/common/index.js\n/**\n * Common\n *\n * Code shared between the theme and admin bundles.\n */\n\n/**\n * @function init\n * @description Initialize the module\n */\n\nvar common_init = function init() {\n  components();\n  console.info('Gravity Flow Common: Initialized all common scripts.');\n};\n\n/* harmony default export */ var common = (common_init);\n;// CONCATENATED MODULE: ./src/js/theme/core/ready.js\n\n\n // @EXAMPLE_REACT_APP\n// import * as tools from 'utils/tools';\n// import { HMR_DEV } from 'config/wp-settings';\n// you MUST do this in every module you use lodash in.\n// A custom bundle of only the lodash you use will be built by babel.\n\n\n\n\n // @EXAMPLE_REACT_APP\n// const el = {\n// \texampleAppRoot: tools.getNodes( 'example-app' )[ 0 ],\n// };\n\n/**\n * @function bindEvents\n * @description Bind global event listeners here,\n */\n\nvar bindEvents = function bindEvents() {\n  on(window, 'resize', debounce_default()(core_resize, 200, false));\n};\n/**\n * @function init\n * @description The core dispatcher for init across the codebase.\n */\n\n\nvar ready_init = function init() {\n  // apply browser classes\n  apply_browser_classes(); // init external plugins\n\n  core_plugins(); // set initial states\n\n  viewport_dims(); // initialize global events\n\n  bindEvents(); // initialize modules\n\n  common(); // @EXAMPLE_REACT_APP (Make sure to include the wrapping if block for ALL react apps\n  // #if INCLUDEREACT\n  // if ( el.exampleAppRoot && ! HMR_DEV ) {\n  // \timport( 'Example' /* webpackChunkName:\"example\" */ );\n  // }\n  // #endif\n  // }\n\n  console.info('GravityFlow Theme: Initialized all javascript that targeted document ready.');\n};\n/**\n * @function domReady\n * @description Export our dom ready enabled init.\n */\n\n\nvar domReady = function domReady() {\n  ready(ready_init);\n};\n\n/* harmony default export */ var core_ready = (domReady);\n;// CONCATENATED MODULE: ./src/js/theme/index.js\n\n\ncore_ready();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy91dGlscy9ldmVudHMuanM/MzAxNCIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy91dGlscy90ZXN0cy5qcz81ZmNmIiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3V0aWxzL2RvbS9hcHBseS1icm93c2VyLWNsYXNzZXMuanM/ZmZjOSIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy90aGVtZS9jb25maWcvc3RhdGUuanM/NTU1ZSIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy90aGVtZS9jb25maWcvb3B0aW9ucy5qcz83YmY3Iiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3RoZW1lL2NvcmUvdmlld3BvcnQtZGltcy5qcz8wNDEyIiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3RoZW1lL2NvcmUvcmVzaXplLmpzP2U1ZjciLCJ3ZWJwYWNrOi8vZ3Jhdml0eWZsb3cvLi9zcmMvanMvdGhlbWUvY29yZS9wbHVnaW5zLmpzPzU3MjEiLCJ3ZWJwYWNrOi8vZ3Jhdml0eWZsb3cvLi9zcmMvanMvdXRpbHMvdG9vbHMuanM/YjE2MSIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy9jb21tb24vY29tcG9uZW50cy9pbmRleC5qcz8zNzE1Iiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL2NvbW1vbi9pbmRleC5qcz9iODhmIiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3RoZW1lL2NvcmUvcmVhZHkuanM/MzVhZCIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy90aGVtZS9pbmRleC5qcz82YjZmIl0sIm5hbWVzIjpbIm9uIiwiZWwiLCJuYW1lIiwiaGFuZGxlciIsImFkZEV2ZW50TGlzdGVuZXIiLCJhdHRhY2hFdmVudCIsImNhbGwiLCJyZWFkeSIsImZuIiwiZG9jdW1lbnQiLCJyZWFkeVN0YXRlIiwidHJpZ2dlciIsIm9wdHMiLCJldmVudCIsIm9wdGlvbnMiLCJkYXRhIiwibmF0aXZlIiwiY3JlYXRlRXZlbnQiLCJpbml0RXZlbnQiLCJ3aW5kb3ciLCJDdXN0b21FdmVudCIsImRldGFpbCIsImUiLCJpbml0Q3VzdG9tRXZlbnQiLCJkaXNwYXRjaEV2ZW50IiwiaXNKc29uIiwic3RyIiwiSlNPTiIsInBhcnNlIiwiY2FuTG9jYWxTdG9yZSIsIm1vZCIsInJlc3VsdCIsIkRhdGUiLCJsb2NhbFN0b3JhZ2UiLCJzZXRJdGVtIiwidG9TdHJpbmciLCJnZXRJdGVtIiwicmVtb3ZlSXRlbSIsIl9lcnJvciIsImFuZHJvaWQiLCJ0ZXN0IiwibmF2aWdhdG9yIiwidXNlckFnZW50IiwiY2hyb21lIiwiZmlyZWZveCIsIkluc3RhbGxUcmlnZ2VyIiwiaWUiLCJkb2N1bWVudE1vZGUiLCJlZGdlIiwiU3R5bGVNZWRpYSIsImlvcyIsIm1hdGNoIiwiaW9zTW9iaWxlIiwib3BlcmEiLCJpbmRleE9mIiwic2FmYXJpIiwiT2JqZWN0IiwicHJvdG90eXBlIiwiSFRNTEVsZW1lbnQiLCJ3ZWJraXRBdWRpb0NvbnRleHQiLCJvcyIsInBsYXRmb3JtIiwiYnJvd3NlclRlc3RzIiwiYXBwbHlCcm93c2VyQ2xhc3NlcyIsImJyb3dzZXIiLCJjbGFzc2VzIiwiYm9keSIsImNsYXNzTGlzdCIsImFkZCIsImRlc2t0b3BfaW5pdGlhbGl6ZWQiLCJpc19kZXNrdG9wIiwiaXNfdGFibGV0IiwiaXNfbW9iaWxlIiwibW9iaWxlX2luaXRpYWxpemVkIiwidl9oZWlnaHQiLCJ2X3dpZHRoIiwiTUVESVVNX0JSRUFLUE9JTlQiLCJGVUxMX0JSRUFLUE9JTlQiLCJ2aWV3cG9ydERpbXMiLCJzdGF0ZSIsInZlcmdlIiwicmVzaXplIiwicGx1Z2lucyIsImFkZENsYXNzIiwiY2xhc3NOYW1lIiwiZWxlbWVudCIsImdldENoaWxkcmVuIiwiY2hpbGRyZW4iLCJpIiwibGVuZ3RoIiwibm9kZVR5cGUiLCJ1bnNoaWZ0IiwiaGFzQ2xhc3MiLCJjb250YWlucyIsInJlbW92ZUNsYXNzIiwicmVtb3ZlIiwicmVtb3ZlQ2xhc3NUaGF0Q29udGFpbnMiLCJzdHJpbmciLCJpdGVtIiwiaGFzQ2xhc3NGcm9tQXJyYXkiLCJhcnIiLCJwcmVmaXgiLCJzdWZmaXgiLCJzb21lIiwiYyIsImNvbnZlcnRFbGVtZW50cyIsImVsZW1lbnRzIiwiY29udmVydGVkIiwiZ2V0Tm9kZXMiLCJzZWxlY3RvciIsImNvbnZlcnQiLCJub2RlIiwiY3VzdG9tIiwic2VsZWN0b3JTdHJpbmciLCJub2RlcyIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJzaG91bGRMb2FkQ2h1bmsiLCJjbG9zZXN0IiwibWF0Y2hlc0ZuIiwicGFyZW50IiwicGFyZW50RWxlbWVudCIsImluc2VydEFmdGVyIiwibmV3Tm9kZSIsInJlZmVyZW5jZU5vZGUiLCJwYXJlbnROb2RlIiwiaW5zZXJ0QmVmb3JlIiwibmV4dEVsZW1lbnRTaWJsaW5nIiwic2V0QXR0cmlidXRlcyIsImF0dHJzIiwia2V5Iiwic2V0QXR0cmlidXRlIiwiaW5ib3giLCJ0b29scyIsImluaXQiLCJ0aGVuIiwibW9kdWxlIiwiZGVmYXVsdCIsImNvbnNvbGUiLCJpbmZvIiwiY29tcG9uZW50cyIsImJpbmRFdmVudHMiLCJjb21tb24iLCJkb21SZWFkeSJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7QUFPQSxJQUFNQSxFQUFFLEdBQUcsU0FBTEEsRUFBSyxDQUFFQyxFQUFGLEVBQU1DLElBQU4sRUFBWUMsT0FBWixFQUF5QjtBQUNuQyxNQUFLRixFQUFFLENBQUNHLGdCQUFSLEVBQTJCO0FBQzFCSCxNQUFFLENBQUNHLGdCQUFILENBQXFCRixJQUFyQixFQUEyQkMsT0FBM0I7QUFDQSxHQUZELE1BRU87QUFDTkYsTUFBRSxDQUFDSSxXQUFILGFBQXNCSCxJQUF0QixHQUErQixZQUFNO0FBQ3BDQyxhQUFPLENBQUNHLElBQVIsQ0FBY0wsRUFBZDtBQUNBLEtBRkQ7QUFHQTtBQUNELENBUkQ7O0FBVUEsSUFBTU0sS0FB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OiIuL3NyYy9qcy90aGVtZS9pbmRleC5qcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogQG1vZHVsZVxuICogQGRlc2NyaXB0aW9uIFNvbWUgZXZlbnQgZnVuY3Rpb25zIGZvciB1c2UgaW4gb3RoZXIgbW9kdWxlc1xuICovXG5cbmltcG9ydCBfIGZyb20gJ2xvZGFzaCc7XG5cbmNvbnN0IG9uID0gKCBlbCwgbmFtZSwgaGFuZGxlciApID0+IHtcblx0aWYgKCBlbC5hZGRFdmVudExpc3RlbmVyICkge1xuXHRcdGVsLmFkZEV2ZW50TGlzdGVuZXIoIG5hbWUsIGhhbmRsZXIgKTtcblx0fSBlbHNlIHtcblx0XHRlbC5hdHRhY2hFdmVudCggYG9uJHsgbmFtZSB9YCwgKCkgPT4ge1xuXHRcdFx0aGFuZGxlci5jYWxsKCBlbCApO1xuXHRcdH0gKTtcblx0fVxufTtcblxuY29uc3QgcmVhZHkgPSAoIGZuICkgPT4ge1xuXHRpZiAoIGRvY3VtZW50LnJlYWR5U3RhdGUgIT09ICdsb2FkaW5nJyApIHtcblx0XHRmbigpO1xuXHR9IGVsc2UgaWYgKCBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyICkge1xuXHRcdGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoICdET01Db250ZW50TG9hZGVkJywgZm4gKTtcblx0fSBlbHNlIHtcblx0XHRkb2N1bWVudC5hdHRhY2hFdmVudCggJ29ucmVhZHlzdGF0ZWNoYW5nZScsICgpID0+IHtcblx0XHRcdGlmICggZG9jdW1lbnQucmVhZHlTdGF0ZSAhPT0gJ2xvYWRpbmcnICkge1xuXHRcdFx0XHRmbigpO1xuXHRcdFx0fVxuXHRcdH0gKTtcblx0fVxufTtcblxuY29uc3QgdHJpZ2dlciA9ICggb3B0cyApID0+IHtcblx0bGV0IGV2ZW50O1xuXHRjb25zdCBvcHRpb25zID0gXy5hc3NpZ24oXG5cdFx0e1xuXHRcdFx0ZGF0YToge30sXG5cdFx0XHRlbDogZG9jdW1lbnQsXG5cdFx0XHRldmVudDogJycsXG5cdFx0XHRuYXRpdmU6IHRydWUsXG5cdFx0fSxcblx0XHRvcHRzXG5cdCk7XG5cblx0aWYgKCBvcHRpb25zLm5hdGl2ZSApIHtcblx0XHRldmVudCA9IGRvY3VtZW50LmNyZWF0ZUV2ZW50KCAnSFRNTEV2ZW50cycgKTtcblx0XHRldmVudC5pbml0RXZlbnQoIG9wdGlvbnMuZXZlbnQsIHRydWUsIGZhbHNlICk7XG5cdH0gZWxzZSB7XG5cdFx0dHJ5IHtcblx0XHRcdGV2ZW50ID0gbmV3IHdpbmRvdy5DdXN0b21FdmVudCggb3B0aW9ucy5ldmVudCwge1xuXHRcdFx0XHRkZXRhaWw6IG9wdGlvbnMuZGF0YSxcblx0XHRcdH0gKTtcblx0XHR9IGNhdGNoICggZSApIHtcblx0XHRcdGV2ZW50ID0gZG9jdW1lbnQuY3JlYXRlRXZlbnQoICdDdXN0b21FdmVudCcgKTtcblx0XHRcdGV2ZW50LmluaXRDdXN0b21FdmVudCggb3B0aW9ucy5ldmVudCwgdHJ1ZSwgdHJ1ZSwgb3B0aW9ucy5kYXRhICk7XG5cdFx0fVxuXHR9XG5cblx0b3B0aW9ucy5lbC5kaXNwYXRjaEV2ZW50KCBldmVudCApO1xufTtcblxuZXhwb3J0IHsgb24sIHJlYWR5LCB0cmlnZ2VyIH07XG4iLCIvKipcbiAqIEBtb2R1bGVcbiAqIEBkZXNjcmlwdGlvbiBTb21lIGhhbmR5IHRlc3QgZm9yIGNvbW1vbiBpc3N1ZXMuXG4gKi9cblxuY29uc3QgaXNKc29uID0gKCBzdHIgKSA9PiB7XG5cdHRyeSB7XG5cdFx0SlNPTi5wYXJzZSggc3RyICk7XG5cdH0gY2F0Y2ggKCBlICkge1xuXHRcdHJldHVybiBmYWxzZTtcblx0fVxuXG5cdHJldHVybiB0cnVlO1xufTtcblxuY29uc3QgY2FuTG9jYWxTdG9yZSA9ICgpID0+IHtcblx0bGV0IG1vZDtcblx0bGV0IHJlc3VsdCA9IGZhbHNlO1xuXG5cdHRyeSB7XG5cdFx0bW9kID0gbmV3IERhdGUoKTtcblx0XHR3aW5kb3cubG9jYWxTdG9yYWdlLnNldEl0ZW0oIG1vZCwgbW9kLnRvU3RyaW5nKCkgKTtcblxuXHRcdHJlc3VsdCA9IHdpbmRvdy5sb2NhbFN0b3JhZ2UuZ2V0SXRlbSggbW9kICkgPT09IG1vZC50b1N0cmluZygpO1xuXHRcdHdpbmRvdy5sb2NhbFN0b3JhZ2UucmVtb3ZlSXRlbSggbW9kICk7XG5cdFx0cmV0dXJuIHJlc3VsdDtcblx0fSBjYXRjaCAoIF9lcnJvciApIHtcblx0XHRyZXR1cm4gcmVzdWx0O1xuXHR9XG59O1xuXG5jb25zdCBhbmRyb2lkID0gLyhhbmRyb2lkKS9pLnRlc3QoIHdpbmRvdy5uYXZpZ2F0b3IudXNlckFnZW50ICk7XG5jb25zdCBjaHJvbWUgPSAhISB3aW5kb3cuY2hyb21lO1xuY29uc3QgZmlyZWZveCA9IHR5cGVvZiBJbnN0YWxsVHJpZ2dlciAhPT0gJ3VuZGVmaW5lZCc7XG5jb25zdCBpZSA9IC8qIEBjY19vbiFAICovIGZhbHNlIHx8IGRvY3VtZW50LmRvY3VtZW50TW9kZSB8fCBmYWxzZTtcbmNvbnN0IGVkZ2UgPSAhIGllICYmICEhIHdpbmRvdy5TdHlsZU1lZGlhO1xuY29uc3QgaW9zID0gISEgd2luZG93Lm5hdmlnYXRvci51c2VyQWdlbnQubWF0Y2goIC8oaVBvZHxpUGhvbmV8aVBhZCkvaSApO1xuY29uc3QgaW9zTW9iaWxlID0gISEgd2luZG93Lm5hdmlnYXRvci51c2VyQWdlbnQubWF0Y2goIC8oaVBvZHxpUGhvbmUpL2kgKTtcbmNvbnN0IG9wZXJhID1cblx0ISEgd2luZG93Lm9wZXJhIHx8IHdpbmRvdy5uYXZpZ2F0b3IudXNlckFnZW50LmluZGV4T2YoICcgT1BSLycgKSA+PSAwO1xuY29uc3Qgc2FmYXJpID0gT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5jYWxsKHdpbmRvdy5IVE1MRWxlbWVudCkuaW5kZXhPZignQ29uc3RydWN0b3InKSA+IDAgfHwgIWNocm9tZSAmJiAhb3BlcmEgJiYgd2luZG93LndlYmtpdEF1ZGlvQ29udGV4dCAhPT0gJ3VuZGVmaW5lZCc7IC8vIGVzbGludC1kaXNhYmxlLWxpbmVcbmNvbnN0IG9zID0gd2luZG93Lm5hdmlnYXRvci5wbGF0Zm9ybTtcblxuLyoqXG4gKiBkbyBub3QgY2hhbmdlIHRvIGFycm93IGZ1bmN0aW9uIHVudGlsIHRlc3RpbmcgZGVwZW5kZW5jaWVzIGFyZSB1cGRhdGVkIGJleW9uZCB0aGUgZm9sbG93aW5nIHJlcG9ydGVkIGlzc3VlXG4gKiBodHRwczovL2dpdGh1Yi5jb20vZmFjZWJvb2svamVzdC9pc3N1ZXMvNTAwMVxuICovXG5mdW5jdGlvbiBicm93c2VyVGVzdHMoKSB7XG5cdHJldHVybiB7XG5cdFx0YW5kcm9pZCxcblx0XHRjaHJvbWUsXG5cdFx0ZWRnZSxcblx0XHRmaXJlZm94LFxuXHRcdGllLFxuXHRcdGlvcyxcblx0XHRpb3NNb2JpbGUsXG5cdFx0b3BlcmEsXG5cdFx0c2FmYXJpLFxuXHRcdG9zLFxuXHR9O1xufVxuXG5leHBvcnQgeyBpc0pzb24sIGNhbkxvY2FsU3RvcmUsIGJyb3dzZXJUZXN0cyB9O1xuIiwiLyoqXG4gKiBAZnVuY3Rpb24gYnJvd3NlckNsYXNzZXNcbiAqIEBkZXNjcmlwdGlvbiBzZXRzIHVwIGJyb3dzZXIgY2xhc3NlcyBvbiBib2R5IHdpdGhvdXQgdXNpbmcgdXNlciBhZ2VudCBzdHJpbmdzIHdoZXJlIHBvc3NpYmxlLlxuICovXG5cbmltcG9ydCAqIGFzIHRlc3QgZnJvbSAnLi4vdGVzdHMnO1xuXG5jb25zdCBhcHBseUJyb3dzZXJDbGFzc2VzID0gKCkgPT4ge1xuXHRjb25zdCBicm93c2VyID0gdGVzdC5icm93c2VyVGVzdHMoKTtcblx0Y29uc3QgY2xhc3NlcyA9IGRvY3VtZW50LmJvZHkuY2xhc3NMaXN0O1xuXG5cdGlmICggYnJvd3Nlci5hbmRyb2lkICkge1xuXHRcdGNsYXNzZXMuYWRkKCAnZGV2aWNlLWFuZHJvaWQnICk7XG5cdH0gZWxzZSBpZiAoIGJyb3dzZXIuaW9zICkge1xuXHRcdGNsYXNzZXMuYWRkKCAnZGV2aWNlLWlvcycgKTtcblx0fVxuXG5cdGlmICggYnJvd3Nlci5lZGdlICkge1xuXHRcdGNsYXNzZXMuYWRkKCAnYnJvd3Nlci1lZGdlJyApO1xuXHR9IGVsc2UgaWYgKCBicm93c2VyLmNocm9tZSApIHtcblx0XHRjbGFzc2VzLmFkZCggJ2Jyb3dzZXItY2hyb21lJyApO1xuXHR9IGVsc2UgaWYgKCBicm93c2VyLmZpcmVmb3ggKSB7XG5cdFx0Y2xhc3Nlcy5hZGQoICdicm93c2VyLWZpcmVmb3gnICk7XG5cdH0gZWxzZSBpZiAoIGJyb3dzZXIuaWUgKSB7XG5cdFx0Y2xhc3Nlcy5hZGQoICdicm93c2VyLWllJyApO1xuXHR9IGVsc2UgaWYgKCBicm93c2VyLm9wZXJhICkge1xuXHRcdGNsYXNzZXMuYWRkKCAnYnJvd3Nlci1vcGVyYScgKTtcblx0fSBlbHNlIGlmICggYnJvd3Nlci5zYWZhcmkgKSB7XG5cdFx0Y2xhc3Nlcy5hZGQoICdicm93c2VyLXNhZmFyaScgKTtcblx0fVxufTtcblxuZXhwb3J0IGRlZmF1bHQgYXBwbHlCcm93c2VyQ2xhc3NlcztcbiIsImV4cG9ydCBkZWZhdWx0IHtcblx0ZGVza3RvcF9pbml0aWFsaXplZDogZmFsc2UsXG5cdGlzX2Rlc2t0b3A6IGZhbHNlLFxuXHRpc190YWJsZXQ6IGZhbHNlLFxuXHRpc19tb2JpbGU6IGZhbHNlLFxuXHRtb2JpbGVfaW5pdGlhbGl6ZWQ6IGZhbHNlLFxuXHR2X2hlaWdodDogMCxcblx0dl93aWR0aDogMCxcbn07XG4iLCIvLyBicmVha3BvaW50IHNldHRpbmdzXG5cbmV4cG9ydCBjb25zdCBNRURJVU1fQlJFQUtQT0lOVCA9IDc2ODtcbmV4cG9ydCBjb25zdCBGVUxMX0JSRUFLUE9JTlQgPSA5NjA7XG4iLCIvKipcbiAqIEBtb2R1bGVcbiAqIEBleHBvcnRzIHZpZXdwb3J0RGltc1xuICogQGRlc2NyaXB0aW9uIFNldHMgdmlld3BvcnQgZGltZW5zaW9ucyB1c2luZyB2ZXJnZSBvbiBzaGFyZWQgc3RhdGVcbiAqIGFuZCBkZXRlY3RzIG1vYmlsZSBvciBkZXNrdG9wIHN0YXRlLlxuICovXG5cbmltcG9ydCB2ZXJnZSBmcm9tICd2ZXJnZSc7XG5pbXBvcnQgc3RhdGUgZnJvbSAnLi4vY29uZmlnL3N0YXRlJztcbmltcG9ydCB7IEZVTExfQlJFQUtQT0lOVCwgTUVESVVNX0JSRUFLUE9JTlQgfSBmcm9tICcuLi9jb25maWcvb3B0aW9ucyc7XG5cbmNvbnN0IHZpZXdwb3J0RGltcyA9ICgpID0+IHtcblx0c3RhdGUudl9oZWlnaHQgPSB2ZXJnZS52aWV3cG9ydEgoKTtcblx0c3RhdGUudl93aWR0aCA9IHZlcmdlLnZpZXdwb3J0VygpO1xuXG5cdGlmICggc3RhdGUudl93aWR0aCA+PSBGVUxMX0JSRUFLUE9JTlQgKSB7XG5cdFx0c3RhdGUuaXNfZGVza3RvcCA9IHRydWU7XG5cdFx0c3RhdGUuaXNfdGFibGV0ID0gZmFsc2U7XG5cdFx0c3RhdGUuaXNfbW9iaWxlID0gZmFsc2U7XG5cdH0gZWxzZSBpZiAoIHN0YXRlLnZfd2lkdGggPj0gTUVESVVNX0JSRUFLUE9JTlQgKSB7XG5cdFx0c3RhdGUuaXNfZGVza3RvcCA9IGZhbHNlO1xuXHRcdHN0YXRlLmlzX3RhYmxldCA9IHRydWU7XG5cdFx0c3RhdGUuaXNfbW9iaWxlID0gZmFsc2U7XG5cdH0gZWxzZSB7XG5cdFx0c3RhdGUuaXNfZGVza3RvcCA9IGZhbHNlO1xuXHRcdHN0YXRlLmlzX3RhYmxldCA9IGZhbHNlO1xuXHRcdHN0YXRlLmlzX21vYmlsZSA9IHRydWU7XG5cdH1cbn07XG5cbmV4cG9ydCBkZWZhdWx0IHZpZXdwb3J0RGltcztcbiIsIi8qKlxuICogQG1vZHVsZVxuICogQGV4cG9ydHMgcmVzaXplXG4gKiBAZGVzY3JpcHRpb24gS2lja3MgaW4gYW55IHRoaXJkIHBhcnR5IHBsdWdpbnMgdGhhdCBvcGVyYXRlIG9uIGEgc2l0ZXdpZGUgYmFzaXMuXG4gKi9cblxuaW1wb3J0IHsgdHJpZ2dlciB9IGZyb20gJ3V0aWxzL2V2ZW50cyc7XG5pbXBvcnQgdmlld3BvcnREaW1zIGZyb20gJy4vdmlld3BvcnQtZGltcyc7XG5cbmNvbnN0IHJlc2l6ZSA9ICgpID0+IHtcblx0Ly8gY29kZSBmb3IgcmVzaXplIGV2ZW50cyBjYW4gZ28gaGVyZVxuXG5cdHZpZXdwb3J0RGltcygpO1xuXG5cdHRyaWdnZXIoIHsgZXZlbnQ6ICdncmF2aXR5Zmxvdy9yZXNpemVfZXhlY3V0ZWQnLCBuYXRpdmU6IGZhbHNlIH0gKTtcbn07XG5cbmV4cG9ydCBkZWZhdWx0IHJlc2l6ZTtcbiIsIi8qKlxuICogQG1vZHVsZVxuICogQGV4cG9ydHMgcGx1Z2luc1xuICogQGRlc2NyaXB0aW9uIEtpY2tzIGluIGFueSB0aGlyZCBwYXJ0eSBwbHVnaW5zIHRoYXQgb3BlcmF0ZSBvblxuICogYSBzaXRld2lkZSBiYXNpcy5cbiAqL1xuXG5jb25zdCBwbHVnaW5zID0gKCkgPT4ge1xuXHQvLyBpbml0aWFsaXplIGdsb2JhbCBleHRlcm5hbCBwbHVnaW5zIGhlcmVcbn07XG5cbmV4cG9ydCBkZWZhdWx0IHBsdWdpbnM7XG4iLCIvKipcbiAqIEBtb2R1bGVcbiAqIEBkZXNjcmlwdGlvbiBTb21lIHZhbmlsbGEganMgY3Jvc3MgYnJvd3NlciB1dGlsc1xuICovXG5cbi8qKlxuICogQWRkIGEgY2xhc3MgdG8gYSBkb20gZWxlbWVudCBvciBleGl0IHNhZmVseSBpZiBub3Qgc2V0XG4gKlxuICogQHBhcmFtIGVsIE5vZGVcbiAqIEBwYXJhbSBjbGFzc05hbWUgQ2xhc3Mgc3RyaW5nXG4gKiBAcmV0dXJuIHsqfSBOb2RlIG9yIGZhbHNlXG4gKi9cblxuZXhwb3J0IGNvbnN0IGFkZENsYXNzID0gKCBlbCwgY2xhc3NOYW1lID0gJycgKSA9PiB7XG5cdGNvbnN0IGVsZW1lbnQgPSBlbDtcblx0aWYgKCAhIGVsZW1lbnQgKSB7XG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9XG5cblx0ZWxlbWVudC5jbGFzc0xpc3QuYWRkKCBjbGFzc05hbWUgKTtcblx0cmV0dXJuIGVsZW1lbnQ7XG59O1xuXG4vKipcbiAqXG4gKiBHZXQgaW1tZWRpYXRlIGNoaWxkIG5vZGVzIGFuZCByZXR1cm4gYW4gYXJyYXkgb2YgdGhlbVxuICpcbiAqIEBwYXJhbSBlbFxuICogQHJldHVybiB7QXJyYXl9IEl0ZXJhYmxlIGFycmF5IG9mIGRvbSBub2Rlc1xuICovXG5cbmV4cG9ydCBjb25zdCBnZXRDaGlsZHJlbiA9ICggZWwgKSA9PiB7XG5cdGNvbnN0IGNoaWxkcmVuID0gW107XG5cdGxldCBpID0gZWwuY2hpbGRyZW4ubGVuZ3RoO1xuXHRmb3IgKGk7IGktLTspIHsgLy8gZXNsaW50LWRpc2FibGUtbGluZVxuXHRcdGlmICggZWwuY2hpbGRyZW5bIGkgXS5ub2RlVHlwZSAhPT0gOCApIHtcblx0XHRcdGNoaWxkcmVuLnVuc2hpZnQoIGVsLmNoaWxkcmVuWyBpIF0gKTtcblx0XHR9XG5cdH1cblxuXHRyZXR1cm4gY2hpbGRyZW47XG59O1xuXG4vKipcbiAqXG4gKiBUZXN0IGlmIGEgZG9tIG5vZGUgaGFzIGEgY2xhc3Mgb3IgcmV0dXJucyBmYWxzZSBpZiBlbCBub3QgZGVmaW5lZFxuICpcbiAqIEBwYXJhbSBlbFxuICogQHBhcmFtIGNsYXNzTmFtZVxuICogQHJldHVybiB7Ym9vbGVhbn1cbiAqL1xuXG5leHBvcnQgY29uc3QgaGFzQ2xhc3MgPSAoIGVsLCBjbGFzc05hbWUgPSAnJyApID0+IHtcblx0aWYgKCAhIGVsICkge1xuXHRcdHJldHVybiBmYWxzZTtcblx0fVxuXG5cdHJldHVybiBlbC5jbGFzc0xpc3QuY29udGFpbnMoIGNsYXNzTmFtZSApO1xufTtcblxuLyoqXG4gKiBSZW1vdmVzIGEgY2xhc3MgZnJvbSB0aGUgZG9tIG5vZGVcbiAqXG4gKiBAcGFyYW0gZWxcbiAqIEBwYXJhbSBjbGFzc05hbWVcbiAqIEByZXR1cm4geyp9IHJldHVybnMgZmFsc2Ugb3IgZWwgaWYgcGFzc2VkXG4gKi9cblxuZXhwb3J0IGNvbnN0IHJlbW92ZUNsYXNzID0gKCBlbCwgY2xhc3NOYW1lICkgPT4ge1xuXHRjb25zdCBlbGVtZW50ID0gZWw7XG5cdGlmICggISBlbGVtZW50ICkge1xuXHRcdHJldHVybiBmYWxzZTtcblx0fVxuXG5cdGVsZW1lbnQuY2xhc3NMaXN0LnJlbW92ZSggY2xhc3NOYW1lICk7XG5cdHJldHVybiBlbGVtZW50O1xufTtcblxuLyoqXG4gKiBSZW1vdmUgYSBjbGFzcyBmcm9tIGFuIGVsZW1lbnQgdGhhdCBjb250YWlucyBhIHN0cmluZ1xuICpcbiAqIEBwYXJhbSBlbFxuICogQHBhcmFtIHN0cmluZ1xuICovXG5cbmV4cG9ydCBjb25zdCByZW1vdmVDbGFzc1RoYXRDb250YWlucyA9ICggZWwsIHN0cmluZyA9ICcnICkgPT4ge1xuXHRmb3IgKCBsZXQgaSA9IDA7IGkgPCBlbC5jbGFzc0xpc3QubGVuZ3RoOyBpKysgKSB7XG5cdFx0aWYgKCBlbC5jbGFzc0xpc3QuaXRlbSggaSApLmluZGV4T2YoIHN0cmluZyApICE9PSAtMSApIHtcblx0XHRcdGVsLmNsYXNzTGlzdC5yZW1vdmUoIGVsLmNsYXNzTGlzdC5pdGVtKCBpICkgKTtcblx0XHR9XG5cdH1cbn07XG5cbi8qKlxuICogQ29tcGFyZXMgYW4gZWxzIGNsYXNzTGlzdCBhZ2FpbnN0IGFuIGFycmF5IG9mIHN0cmluZ3MgdG8gc2VlIGlmIGFueSBtYXRjaFxuICpcbiAqIEBwYXJhbSBlbCB0aGUgZWxlbWVudCB0byBjaGVjayBhZ2FpbnN0XG4gKiBAcGFyYW0gYXJyIFRoZSBhcnJheSBvZiBjbGFzc2VzIGFzIHN0cmluZ3MgdG8gdGVzdCBhZ2FpbnN0XG4gKiBAcGFyYW0gcHJlZml4IG9wdGlvbmFsIHByZWZpeCBzdHJpbmcgYXBwbGllZCB0byBhbGwgdGVzdCBzdHJpbmdzXG4gKiBAcGFyYW0gc3VmZml4IG9wdGlvbmFsIHN1ZmZpeCBzdHJpbmdcbiAqL1xuXG5leHBvcnQgY29uc3QgaGFzQ2xhc3NGcm9tQXJyYXkgPSAoIGVsLCBhcnIgPSBbXSwgcHJlZml4ID0gJycsIHN1ZmZpeCA9ICcnICkgPT5cblx0YXJyLnNvbWUoICggYyApID0+XG5cdFx0ZWwuY2xhc3NMaXN0LmNvbnRhaW5zKCBgJHsgcHJlZml4IH0keyBjIH0keyBzdWZmaXggfWAgKVxuXHQpO1xuXG4vKipcbiAqIEhpZ2hseSBlZmZpY2llbnQgZnVuY3Rpb24gdG8gY29udmVydCBhIG5vZGVsaXN0IGludG8gYSBzdGFuZGFyZCBhcnJheS4gQWxsb3dzIHlvdSB0byBydW4gQXJyYXkuZm9yRWFjaFxuICpcbiAqIEBwYXJhbSB7RWxlbWVudHxOb2RlTGlzdH0gZWxlbWVudHMgdG8gY29udmVydFxuICogQHJldHVybiB7QXJyYXl9IE9mIGNvbnZlcnRlZCBlbGVtZW50c1xuICovXG5cbmV4cG9ydCBjb25zdCBjb252ZXJ0RWxlbWVudHMgPSAoIGVsZW1lbnRzID0gW10gKSA9PiB7XG5cdGNvbnN0IGNvbnZlcnRlZCA9IFtdO1xuXHRsZXQgaSA9IGVsZW1lbnRzLmxlbmd0aDtcblx0Zm9yIChpOyBpLS07IGNvbnZlcnRlZC51bnNoaWZ0KGVsZW1lbnRzW2ldKSk7IC8vIGVzbGludC1kaXNhYmxlLWxpbmVcblxuXHRyZXR1cm4gY29udmVydGVkO1xufTtcblxuLyoqXG4gKiBTaG91bGQgYmUgdXNlZCBhdCBhbGwgdGltZXMgZm9yIGdldHRpbmcgbm9kZXMgdGhyb3VnaG91dCBvdXIgYXBwLiBQbGVhc2UgdXNlIHRoZSBkYXRhLWpzIGF0dHJpYnV0ZSB3aGVuZXZlciBwb3NzaWJsZVxuICpcbiAqIEBwYXJhbSBzZWxlY3RvciBUaGUgc2VsZWN0b3Igc3RyaW5nIHRvIHNlYXJjaCBmb3IuIElmIGFyZyA0IGlzIGZhbHNlIChkZWZhdWx0KSB0aGVuIHdlIHNlYXJjaCBmb3IgW2RhdGEtanM9XCJzZWxlY3RvclwiXVxuICogQHBhcmFtIGNvbnZlcnQgQ29udmVydCB0aGUgTm9kZUxpc3QgdG8gYW4gYXJyYXk/IFRoZW4gd2UgY2FuIEFycmF5LmZvckVhY2ggZGlyZWN0bHkuIFVzZXMgY29udmVydEVsZW1lbnRzIGZyb20gYWJvdmVcbiAqIEBwYXJhbSBub2RlIFBhcmVudCBub2RlIHRvIHNlYXJjaCBmcm9tLiBEZWZhdWx0cyB0byBkb2N1bWVudFxuICogQHBhcmFtIGN1c3RvbSBJcyB0aGlzIGEgY3VzdG9tIHNlbGVjdG9yIHdoZXJlIHdlIGRvbid0IHdhbnQgdG8gdXNlIHRoZSBkYXRhLWpzIGF0dHJpYnV0ZT9cbiAqIEByZXR1cm4ge05vZGVMaXN0fVxuICovXG5cbmV4cG9ydCBjb25zdCBnZXROb2RlcyA9IChcblx0c2VsZWN0b3IgPSAnJyxcblx0Y29udmVydCA9IGZhbHNlLFxuXHRub2RlID0gZG9jdW1lbnQsXG5cdGN1c3RvbSA9IGZhbHNlXG4pID0+IHtcblx0Y29uc3Qgc2VsZWN0b3JTdHJpbmcgPSBjdXN0b20gPyBzZWxlY3RvciA6IGBbZGF0YS1qcz1cIiR7IHNlbGVjdG9yIH1cIl1gO1xuXHRsZXQgbm9kZXMgPSBub2RlLnF1ZXJ5U2VsZWN0b3JBbGwoIHNlbGVjdG9yU3RyaW5nICk7XG5cdGlmICggY29udmVydCApIHtcblx0XHRub2RlcyA9IGNvbnZlcnRFbGVtZW50cyggbm9kZXMgKTtcblx0fVxuXHRyZXR1cm4gbm9kZXM7XG59O1xuXG4vKipcbiAqIFV0aWwgdG8gc2VlIGlmIHdlIHNob3VsZCBsb2FkIGEgZ2l2ZW4gY2h1bmsgb24gYSBwYWdlLiBKdXN0IGFkZCBkYXRhLWxvYWQtY2h1bmstcHJvZHVjdHMgdG8gbG9hZCB0aGF0IHBhcnRpY3VsYXJcbiAqIG9uZSBvbiBhbnkgZWxlbWVudCBvbiB0aGUgcGFnZS5cbiAqIEhhbmRsZWQgaW4gYXNzZXRzL2pzL3NyYy90aGVtZS9jb3JlL2NvbXBvbmVudHMuanMgYW5kIGFzc2V0cy9qcy9zcmMvdGhlbWUvY29yZS9wYWdlcy5qc1xuICpcbiAqIEBwYXJhbSBuYW1lIGNodW5rIG5hbWVcbiAqL1xuXG5leHBvcnQgY29uc3Qgc2hvdWxkTG9hZENodW5rID0gKCBuYW1lID0gJycgKSA9PiB7XG5cdGNvbnN0IG5vZGUgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCBgW2RhdGEtbG9hZC1jaHVuay0keyBuYW1lIH1dYCApO1xuXHRyZXR1cm4gbm9kZS5sZW5ndGggPiAwO1xufTtcblxuLyoqXG4gKiBHZXRzIHRoZSBjbG9zZXN0IGFuY2VzdG9yIHRoYXQgbWF0Y2hlcyBhIHNlbGVjdG9yIHN0cmluZ1xuICpcbiAqIEBwYXJhbSBlbFxuICogQHBhcmFtIHNlbGVjdG9yXG4gKiBAcmV0dXJuIHsqfVxuICovXG5cbmV4cG9ydCBjb25zdCBjbG9zZXN0ID0gKCBlbCwgc2VsZWN0b3IgKSA9PiB7XG5cdGxldCBtYXRjaGVzRm47XG5cdGxldCBwYXJlbnQ7XG5cblx0W1xuXHRcdCdtYXRjaGVzJyxcblx0XHQnd2Via2l0TWF0Y2hlc1NlbGVjdG9yJyxcblx0XHQnbW96TWF0Y2hlc1NlbGVjdG9yJyxcblx0XHQnbXNNYXRjaGVzU2VsZWN0b3InLFxuXHRcdCdvTWF0Y2hlc1NlbGVjdG9yJyxcblx0XS5zb21lKCAoIGZuICkgPT4ge1xuXHRcdGlmICggdHlwZW9mIGRvY3VtZW50LmJvZHlbIGZuIF0gPT09ICdmdW5jdGlvbicgKSB7XG5cdFx0XHRtYXRjaGVzRm4gPSBmbjtcblx0XHRcdHJldHVybiB0cnVlO1xuXHRcdH1cblx0XHQvKiBpc3RhbmJ1bCBpZ25vcmUgbmV4dCAqL1xuXHRcdHJldHVybiBmYWxzZTtcblx0fSApO1xuXG5cdHdoaWxlICggZWwgKSB7XG5cdFx0cGFyZW50ID0gZWwucGFyZW50RWxlbWVudDtcblx0XHRpZiAoIHBhcmVudCAmJiBwYXJlbnRbIG1hdGNoZXNGbiBdKCBzZWxlY3RvciApICkge1xuXHRcdFx0cmV0dXJuIHBhcmVudDtcblx0XHR9XG5cblx0XHRlbCA9IHBhcmVudDsgLy8gZXNsaW50LWRpc2FibGUtbGluZVxuXHR9XG5cblx0cmV0dXJuIG51bGw7XG59O1xuXG4vKipcbiAqIEluc2VydCBhIG5vZGUgYWZ0ZXIgYW5vdGhlciBub2RlXG4gKlxuICogQHBhcmFtIG5ld05vZGUge0VsZW1lbnR8Tm9kZUxpc3R9XG4gKiBAcGFyYW0gcmVmZXJlbmNlTm9kZSB7RWxlbWVudHxOb2RlTGlzdH1cbiAqL1xuZXhwb3J0IGNvbnN0IGluc2VydEFmdGVyID0gKCBuZXdOb2RlLCByZWZlcmVuY2VOb2RlICkgPT4ge1xuXHRyZWZlcmVuY2VOb2RlLnBhcmVudE5vZGUuaW5zZXJ0QmVmb3JlKFxuXHRcdG5ld05vZGUsXG5cdFx0cmVmZXJlbmNlTm9kZS5uZXh0RWxlbWVudFNpYmxpbmdcblx0KTtcbn07XG5cbi8qKlxuICogSW5zZXJ0IGEgbm9kZSBiZWZvcmUgYW5vdGhlciBub2RlXG4gKlxuICogQHBhcmFtIG5ld05vZGUge0VsZW1lbnR8Tm9kZUxpc3R9XG4gKiBAcGFyYW0gcmVmZXJlbmNlTm9kZSB7RWxlbWVudHxOb2RlTGlzdH1cbiAqL1xuXG5leHBvcnQgY29uc3QgaW5zZXJ0QmVmb3JlID0gKCBuZXdOb2RlLCByZWZlcmVuY2VOb2RlICkgPT4ge1xuXHRyZWZlcmVuY2VOb2RlLnBhcmVudE5vZGUuaW5zZXJ0QmVmb3JlKCBuZXdOb2RlLCByZWZlcmVuY2VOb2RlICk7XG59O1xuXG4vKipcbiAqIFNldCBtdWx0aXBsZSBlbGVtZW50IGF0dHJpYnV0ZXMgYXQgb25jZVxuICpcbiAqIEBwYXJhbSBlbFxuICogQHBhcmFtIGF0dHJzXG4gKi9cblxuZXhwb3J0IGNvbnN0IHNldEF0dHJpYnV0ZXMgPSAoIGVsLCBhdHRycyApID0+IHtcblx0Zm9yICggY29uc3Qga2V5IGluIGF0dHJzICkge1xuXHRcdGVsLnNldEF0dHJpYnV0ZSgga2V5LCBhdHRyc1sga2V5IF0gKTtcblx0fVxufTtcbiIsIi8qKlxuICogQ29tcG9uZW50c1xuICpcbiAqIEluaXRpYWxpemVzIGFsbCBjb21tb24gY29tcG9uZW50c1xuICovXG5cbmltcG9ydCAqIGFzIHRvb2xzIGZyb20gJ3V0aWxzL3Rvb2xzJztcblxuY29uc3QgZWwgPSB7XG5cdGluYm94OiB0b29scy5nZXROb2RlcyggJ2dmbG93LWluYm94JyApWyAwIF0sXG59O1xuXG4vKipcbiAqIEBmdW5jdGlvbiBpbml0XG4gKiBAZGVzY3JpcHRpb24gSW5pdGlhbGl6ZSB0aGUgbW9kdWxlXG4gKi9cblxuY29uc3QgaW5pdCA9ICgpID0+IHtcblx0aWYgKCBlbC5pbmJveCApIHtcblx0XHRpbXBvcnQoICcuL2luYm94JyAvKiB3ZWJwYWNrQ2h1bmtOYW1lOlwiY29tbW9uLWluYm94XCIgKi8gKS50aGVuKFxuXHRcdFx0KCBtb2R1bGUgKSA9PiB7XG5cdFx0XHRcdG1vZHVsZS5kZWZhdWx0KCBlbC5pbmJveCApO1xuXHRcdFx0fVxuXHRcdCk7XG5cdH1cblxuXHRjb25zb2xlLmluZm8oICdHcmF2aXR5IEZsb3cgQ29tbW9uOiBJbml0aWFsaXplZCBhbGwgY29tbW9uIGNvbXBvbmVudHMuJyApO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgaW5pdDtcbiIsIi8qKlxuICogQ29tbW9uXG4gKlxuICogQ29kZSBzaGFyZWQgYmV0d2VlbiB0aGUgdGhlbWUgYW5kIGFkbWluIGJ1bmRsZXMuXG4gKi9cblxuaW1wb3J0IGNvbXBvbmVudHMgZnJvbSAnLi9jb21wb25lbnRzJztcblxuLyoqXG4gKiBAZnVuY3Rpb24gaW5pdFxuICogQGRlc2NyaXB0aW9uIEluaXRpYWxpemUgdGhlIG1vZHVsZVxuICovXG5cbmNvbnN0IGluaXQgPSAoKSA9PiB7XG5cdGNvbXBvbmVudHMoKTtcblxuXHRjb25zb2xlLmluZm8oICdHcmF2aXR5IEZsb3cgQ29tbW9uOiBJbml0aWFsaXplZCBhbGwgY29tbW9uIHNjcmlwdHMuJyApO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgaW5pdDtcbiIsIi8qKlxuICogQG1vZHVsZVxuICogQGV4cG9ydHMgcmVhZHlcbiAqIEBkZXNjcmlwdGlvbiBUaGUgY29yZSBkaXNwYXRjaGVyIGZvciB0aGUgZG9tIHJlYWR5IGV2ZW50IGphdmFzY3JpcHQuXG4gKi9cblxuaW1wb3J0IF8gZnJvbSAnbG9kYXNoJztcbmltcG9ydCB7IG9uLCByZWFkeSB9IGZyb20gJ3V0aWxzL2V2ZW50cyc7XG5pbXBvcnQgYXBwbHlCcm93c2VyQ2xhc3NlcyBmcm9tICd1dGlscy9kb20vYXBwbHktYnJvd3Nlci1jbGFzc2VzJztcbi8vIEBFWEFNUExFX1JFQUNUX0FQUFxuXG4vLyBpbXBvcnQgKiBhcyB0b29scyBmcm9tICd1dGlscy90b29scyc7XG4vLyBpbXBvcnQgeyBITVJfREVWIH0gZnJvbSAnY29uZmlnL3dwLXNldHRpbmdzJztcblxuLy8geW91IE1VU1QgZG8gdGhpcyBpbiBldmVyeSBtb2R1bGUgeW91IHVzZSBsb2Rhc2ggaW4uXG4vLyBBIGN1c3RvbSBidW5kbGUgb2Ygb25seSB0aGUgbG9kYXNoIHlvdSB1c2Ugd2lsbCBiZSBidWlsdCBieSBiYWJlbC5cblxuaW1wb3J0IHJlc2l6ZSBmcm9tICcuL3Jlc2l6ZSc7XG5pbXBvcnQgcGx1Z2lucyBmcm9tICcuL3BsdWdpbnMnO1xuaW1wb3J0IHZpZXdwb3J0RGltcyBmcm9tICcuL3ZpZXdwb3J0LWRpbXMnO1xuXG5pbXBvcnQgY29tbW9uIGZyb20gJ2NvbW1vbic7XG5cbi8vIEBFWEFNUExFX1JFQUNUX0FQUFxuXG4vLyBjb25zdCBlbCA9IHtcbi8vIFx0ZXhhbXBsZUFwcFJvb3Q6IHRvb2xzLmdldE5vZGVzKCAnZXhhbXBsZS1hcHAnIClbIDAgXSxcbi8vIH07XG5cbi8qKlxuICogQGZ1bmN0aW9uIGJpbmRFdmVudHNcbiAqIEBkZXNjcmlwdGlvbiBCaW5kIGdsb2JhbCBldmVudCBsaXN0ZW5lcnMgaGVyZSxcbiAqL1xuXG5jb25zdCBiaW5kRXZlbnRzID0gKCkgPT4ge1xuXHRvbiggd2luZG93LCAncmVzaXplJywgXy5kZWJvdW5jZSggcmVzaXplLCAyMDAsIGZhbHNlICkgKTtcbn07XG5cbi8qKlxuICogQGZ1bmN0aW9uIGluaXRcbiAqIEBkZXNjcmlwdGlvbiBUaGUgY29yZSBkaXNwYXRjaGVyIGZvciBpbml0IGFjcm9zcyB0aGUgY29kZWJhc2UuXG4gKi9cblxuY29uc3QgaW5pdCA9ICgpID0+IHtcblx0Ly8gYXBwbHkgYnJvd3NlciBjbGFzc2VzXG5cblx0YXBwbHlCcm93c2VyQ2xhc3NlcygpO1xuXG5cdC8vIGluaXQgZXh0ZXJuYWwgcGx1Z2luc1xuXG5cdHBsdWdpbnMoKTtcblxuXHQvLyBzZXQgaW5pdGlhbCBzdGF0ZXNcblxuXHR2aWV3cG9ydERpbXMoKTtcblxuXHQvLyBpbml0aWFsaXplIGdsb2JhbCBldmVudHNcblxuXHRiaW5kRXZlbnRzKCk7XG5cblx0Ly8gaW5pdGlhbGl6ZSBtb2R1bGVzXG5cblx0Y29tbW9uKCk7XG5cblx0Ly8gQEVYQU1QTEVfUkVBQ1RfQVBQIChNYWtlIHN1cmUgdG8gaW5jbHVkZSB0aGUgd3JhcHBpbmcgaWYgYmxvY2sgZm9yIEFMTCByZWFjdCBhcHBzXG5cblx0Ly8gI2lmIElOQ0xVREVSRUFDVFxuXHQvLyBpZiAoIGVsLmV4YW1wbGVBcHBSb290ICYmICEgSE1SX0RFViApIHtcblx0Ly8gXHRpbXBvcnQoICdFeGFtcGxlJyAvKiB3ZWJwYWNrQ2h1bmtOYW1lOlwiZXhhbXBsZVwiICovICk7XG5cdC8vIH1cblx0Ly8gI2VuZGlmXG5cdC8vIH1cblxuXHRjb25zb2xlLmluZm8oXG5cdFx0J0dyYXZpdHlGbG93IFRoZW1lOiBJbml0aWFsaXplZCBhbGwgamF2YXNjcmlwdCB0aGF0IHRhcmdldGVkIGRvY3VtZW50IHJlYWR5Lidcblx0KTtcbn07XG5cbi8qKlxuICogQGZ1bmN0aW9uIGRvbVJlYWR5XG4gKiBAZGVzY3JpcHRpb24gRXhwb3J0IG91ciBkb20gcmVhZHkgZW5hYmxlZCBpbml0LlxuICovXG5cbmNvbnN0IGRvbVJlYWR5ID0gKCkgPT4ge1xuXHRyZWFkeSggaW5pdCApO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgZG9tUmVhZHk7XG4iLCJpbXBvcnQgJ3doYXR3Zy1mZXRjaCc7XG5pbXBvcnQgcmVhZHkgZnJvbSAnLi9jb3JlL3JlYWR5JztcblxucmVhZHkoKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/js/theme/index.js\n");

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
/******/ 	/* webpack/runtime/ensure chunk */
/******/ 	!function() {
/******/ 		__webpack_require__.f = {};
/******/ 		// This file contains only the entry chunk.
/******/ 		// The chunk loading function for additional chunks
/******/ 		__webpack_require__.e = function(chunkId) {
/******/ 			return Promise.all(Object.keys(__webpack_require__.f).reduce(function(promises, key) {
/******/ 				__webpack_require__.f[key](chunkId, promises);
/******/ 				return promises;
/******/ 			}, []));
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/get javascript chunk filename */
/******/ 	!function() {
/******/ 		// This function allow to reference async chunks
/******/ 		__webpack_require__.u = function(chunkId) {
/******/ 			// return url for filenames based on template
/******/ 			return "" + chunkId + "." + "94994eae35e9633ca1e1" + ".js";
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/get mini-css chunk filename */
/******/ 	!function() {
/******/ 		// This function allow to reference all chunks
/******/ 		__webpack_require__.miniCssF = function(chunkId) {
/******/ 			// return url for filenames based on template
/******/ 			return undefined;
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
/******/ 	/* webpack/runtime/load script */
/******/ 	!function() {
/******/ 		var inProgress = {};
/******/ 		var dataWebpackPrefix = "gravityflow:";
/******/ 		// loadScript function to load a script via script tag
/******/ 		__webpack_require__.l = function(url, done, key, chunkId) {
/******/ 			if(inProgress[url]) { inProgress[url].push(done); return; }
/******/ 			var script, needAttach;
/******/ 			if(key !== undefined) {
/******/ 				var scripts = document.getElementsByTagName("script");
/******/ 				for(var i = 0; i < scripts.length; i++) {
/******/ 					var s = scripts[i];
/******/ 					if(s.getAttribute("src") == url || s.getAttribute("data-webpack") == dataWebpackPrefix + key) { script = s; break; }
/******/ 				}
/******/ 			}
/******/ 			if(!script) {
/******/ 				needAttach = true;
/******/ 				script = document.createElement('script');
/******/ 		
/******/ 				script.charset = 'utf-8';
/******/ 				script.timeout = 120;
/******/ 				if (__webpack_require__.nc) {
/******/ 					script.setAttribute("nonce", __webpack_require__.nc);
/******/ 				}
/******/ 				script.setAttribute("data-webpack", dataWebpackPrefix + key);
/******/ 				script.src = url;
/******/ 			}
/******/ 			inProgress[url] = [done];
/******/ 			var onScriptComplete = function(prev, event) {
/******/ 				// avoid mem leaks in IE.
/******/ 				script.onerror = script.onload = null;
/******/ 				clearTimeout(timeout);
/******/ 				var doneFns = inProgress[url];
/******/ 				delete inProgress[url];
/******/ 				script.parentNode && script.parentNode.removeChild(script);
/******/ 				doneFns && doneFns.forEach(function(fn) { return fn(event); });
/******/ 				if(prev) return prev(event);
/******/ 			}
/******/ 			;
/******/ 			var timeout = setTimeout(onScriptComplete.bind(null, undefined, { type: 'timeout', target: script }), 120000);
/******/ 			script.onerror = onScriptComplete.bind(null, script.onerror);
/******/ 			script.onload = onScriptComplete.bind(null, script.onload);
/******/ 			needAttach && document.head.appendChild(script);
/******/ 		};
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
/******/ 	/* webpack/runtime/publicPath */
/******/ 	!function() {
/******/ 		__webpack_require__.p = "/wp-content/plugins/gravityflow/js/";
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
/******/ 		__webpack_require__.f.j = function(chunkId, promises) {
/******/ 				// JSONP chunk loading for javascript
/******/ 				var installedChunkData = __webpack_require__.o(installedChunks, chunkId) ? installedChunks[chunkId] : undefined;
/******/ 				if(installedChunkData !== 0) { // 0 means "already installed".
/******/ 		
/******/ 					// a Promise means "currently loading".
/******/ 					if(installedChunkData) {
/******/ 						promises.push(installedChunkData[2]);
/******/ 					} else {
/******/ 						if(true) { // all chunks have JS
/******/ 							// setup Promise in chunk cache
/******/ 							var promise = new Promise(function(resolve, reject) {
/******/ 								installedChunkData = installedChunks[chunkId] = [resolve, reject];
/******/ 							});
/******/ 							promises.push(installedChunkData[2] = promise);
/******/ 		
/******/ 							// start chunk loading
/******/ 							var url = __webpack_require__.p + __webpack_require__.u(chunkId);
/******/ 							// create error before stack unwound to get useful stacktrace later
/******/ 							var error = new Error();
/******/ 							var loadingEnded = function(event) {
/******/ 								if(__webpack_require__.o(installedChunks, chunkId)) {
/******/ 									installedChunkData = installedChunks[chunkId];
/******/ 									if(installedChunkData !== 0) installedChunks[chunkId] = undefined;
/******/ 									if(installedChunkData) {
/******/ 										var errorType = event && (event.type === 'load' ? 'missing' : event.type);
/******/ 										var realSrc = event && event.target && event.target.src;
/******/ 										error.message = 'Loading chunk ' + chunkId + ' failed.\n(' + errorType + ': ' + realSrc + ')';
/******/ 										error.name = 'ChunkLoadError';
/******/ 										error.type = errorType;
/******/ 										error.request = realSrc;
/******/ 										installedChunkData[1](error);
/******/ 									}
/******/ 								}
/******/ 							};
/******/ 							__webpack_require__.l(url, loadingEnded, "chunk-" + chunkId, chunkId);
/******/ 						} else installedChunks[chunkId] = 0;
/******/ 					}
/******/ 				}
/******/ 		};
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