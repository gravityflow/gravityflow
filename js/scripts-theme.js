/*
 * ATTENTION: An "eval-source-map" devtool has been used.
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file with attached SourceMaps in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/theme/index.js":
/*!********************************************!*\
  !*** ./src/js/theme/index.js + 12 modules ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

eval("// ESM COMPAT FLAG\n__webpack_require__.r(__webpack_exports__);\n\n// EXTERNAL MODULE: ./node_modules/whatwg-fetch/fetch.js\nvar fetch = __webpack_require__(\"./node_modules/whatwg-fetch/fetch.js\");\n// EXTERNAL MODULE: ./node_modules/lodash/debounce.js\nvar debounce = __webpack_require__(\"./node_modules/lodash/debounce.js\");\nvar debounce_default = /*#__PURE__*/__webpack_require__.n(debounce);\n// EXTERNAL MODULE: ./node_modules/lodash/assign.js\nvar lodash_assign = __webpack_require__(\"./node_modules/lodash/assign.js\");\nvar assign_default = /*#__PURE__*/__webpack_require__.n(lodash_assign);\n;// CONCATENATED MODULE: ./src/js/utils/events.js\n\n\nvar on = function on(el, name, handler) {\n  if (el.addEventListener) {\n    el.addEventListener(name, handler);\n  } else {\n    el.attachEvent(\"on\".concat(name), function () {\n      handler.call(el);\n    });\n  }\n};\n\nvar ready = function ready(fn) {\n  if (document.readyState !== 'loading') {\n    fn();\n  } else if (document.addEventListener) {\n    document.addEventListener('DOMContentLoaded', fn);\n  } else {\n    document.attachEvent('onreadystatechange', function () {\n      if (document.readyState !== 'loading') {\n        fn();\n      }\n    });\n  }\n};\n\nvar trigger = function trigger(opts) {\n  var event;\n\n  var options = assign_default()({\n    data: {},\n    el: document,\n    event: '',\n    \"native\": true\n  }, opts);\n\n  if (options[\"native\"]) {\n    event = document.createEvent('HTMLEvents');\n    event.initEvent(options.event, true, false);\n  } else {\n    try {\n      event = new window.CustomEvent(options.event, {\n        detail: options.data\n      });\n    } catch (e) {\n      event = document.createEvent('CustomEvent');\n      event.initCustomEvent(options.event, true, true, options.data);\n    }\n  }\n\n  options.el.dispatchEvent(event);\n};\n\n\n;// CONCATENATED MODULE: ./src/js/utils/tests.js\n/**\n * @module\n * @description Some handy test for common issues.\n */\nvar isJson = function isJson(str) {\n  try {\n    JSON.parse(str);\n  } catch (e) {\n    return false;\n  }\n\n  return true;\n};\n\nvar canLocalStore = function canLocalStore() {\n  var mod;\n  var result = false;\n\n  try {\n    mod = new Date();\n    window.localStorage.setItem(mod, mod.toString());\n    result = window.localStorage.getItem(mod) === mod.toString();\n    window.localStorage.removeItem(mod);\n    return result;\n  } catch (_error) {\n    return result;\n  }\n};\n\nvar android = /(android)/i.test(window.navigator.userAgent);\nvar chrome = !!window.chrome;\nvar firefox = typeof InstallTrigger !== 'undefined';\nvar ie =\n/* @cc_on!@ */\n false || document.documentMode || false;\nvar edge = !ie && !!window.StyleMedia;\nvar ios = !!window.navigator.userAgent.match(/(iPod|iPhone|iPad)/i);\nvar iosMobile = !!window.navigator.userAgent.match(/(iPod|iPhone)/i);\nvar opera = !!window.opera || window.navigator.userAgent.indexOf(' OPR/') >= 0;\nvar safari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0 || !chrome && !opera && window.webkitAudioContext !== 'undefined'; // eslint-disable-line\n\nvar os = window.navigator.platform;\n/**\n * do not change to arrow function until testing dependencies are updated beyond the following reported issue\n * https://github.com/facebook/jest/issues/5001\n */\n\nfunction browserTests() {\n  return {\n    android: android,\n    chrome: chrome,\n    edge: edge,\n    firefox: firefox,\n    ie: ie,\n    ios: ios,\n    iosMobile: iosMobile,\n    opera: opera,\n    safari: safari,\n    os: os\n  };\n}\n\n\n;// CONCATENATED MODULE: ./src/js/utils/dom/apply-browser-classes.js\n/**\n * @function browserClasses\n * @description sets up browser classes on body without using user agent strings where possible.\n */\n\n\nvar applyBrowserClasses = function applyBrowserClasses() {\n  var browser = browserTests();\n  var classes = document.body.classList;\n\n  if (browser.android) {\n    classes.add('device-android');\n  } else if (browser.ios) {\n    classes.add('device-ios');\n  }\n\n  if (browser.edge) {\n    classes.add('browser-edge');\n  } else if (browser.chrome) {\n    classes.add('browser-chrome');\n  } else if (browser.firefox) {\n    classes.add('browser-firefox');\n  } else if (browser.ie) {\n    classes.add('browser-ie');\n  } else if (browser.opera) {\n    classes.add('browser-opera');\n  } else if (browser.safari) {\n    classes.add('browser-safari');\n  }\n};\n\n/* harmony default export */ const apply_browser_classes = (applyBrowserClasses);\n// EXTERNAL MODULE: ./node_modules/verge/verge.js\nvar verge = __webpack_require__(\"./node_modules/verge/verge.js\");\nvar verge_default = /*#__PURE__*/__webpack_require__.n(verge);\n;// CONCATENATED MODULE: ./src/js/theme/config/state.js\n/* harmony default export */ const state = ({\n  desktop_initialized: false,\n  is_desktop: false,\n  is_tablet: false,\n  is_mobile: false,\n  mobile_initialized: false,\n  v_height: 0,\n  v_width: 0\n});\n;// CONCATENATED MODULE: ./src/js/theme/config/options.js\n// breakpoint settings\nvar MEDIUM_BREAKPOINT = 768;\nvar FULL_BREAKPOINT = 960;\n;// CONCATENATED MODULE: ./src/js/theme/core/viewport-dims.js\n/**\n * @module\n * @exports viewportDims\n * @description Sets viewport dimensions using verge on shared state\n * and detects mobile or desktop state.\n */\n\n\n\n\nvar viewportDims = function viewportDims() {\n  state.v_height = verge_default().viewportH();\n  state.v_width = verge_default().viewportW();\n\n  if (state.v_width >= FULL_BREAKPOINT) {\n    state.is_desktop = true;\n    state.is_tablet = false;\n    state.is_mobile = false;\n  } else if (state.v_width >= MEDIUM_BREAKPOINT) {\n    state.is_desktop = false;\n    state.is_tablet = true;\n    state.is_mobile = false;\n  } else {\n    state.is_desktop = false;\n    state.is_tablet = false;\n    state.is_mobile = true;\n  }\n};\n\n/* harmony default export */ const viewport_dims = (viewportDims);\n;// CONCATENATED MODULE: ./src/js/theme/core/resize.js\n/**\n * @module\n * @exports resize\n * @description Kicks in any third party plugins that operate on a sitewide basis.\n */\n\n\n\nvar resize = function resize() {\n  // code for resize events can go here\n  viewport_dims();\n  trigger({\n    event: 'gravityflow/resize_executed',\n    \"native\": false\n  });\n};\n\n/* harmony default export */ const core_resize = (resize);\n;// CONCATENATED MODULE: ./src/js/theme/core/plugins.js\n/**\n * @module\n * @exports plugins\n * @description Kicks in any third party plugins that operate on\n * a sitewide basis.\n */\nvar plugins = function plugins() {// initialize global external plugins here\n};\n\n/* harmony default export */ const core_plugins = (plugins);\n;// CONCATENATED MODULE: ./src/js/utils/tools.js\n/**\n * @module\n * @description Some vanilla js cross browser utils\n */\n\n/**\n * Add a class to a dom element or exit safely if not set\n *\n * @param el Node\n * @param className Class string\n * @return {*} Node or false\n */\nvar addClass = function addClass(el) {\n  var className = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';\n  var element = el;\n\n  if (!element) {\n    return false;\n  }\n\n  element.classList.add(className);\n  return element;\n};\n/**\n *\n * Get immediate child nodes and return an array of them\n *\n * @param el\n * @return {Array} Iterable array of dom nodes\n */\n\nvar getChildren = function getChildren(el) {\n  var children = [];\n  var i = el.children.length;\n\n  for (i; i--;) {\n    // eslint-disable-line\n    if (el.children[i].nodeType !== 8) {\n      children.unshift(el.children[i]);\n    }\n  }\n\n  return children;\n};\n/**\n *\n * Test if a dom node has a class or returns false if el not defined\n *\n * @param el\n * @param className\n * @return {boolean}\n */\n\nvar hasClass = function hasClass(el) {\n  var className = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';\n\n  if (!el) {\n    return false;\n  }\n\n  return el.classList.contains(className);\n};\n/**\n * Removes a class from the dom node\n *\n * @param el\n * @param className\n * @return {*} returns false or el if passed\n */\n\nvar removeClass = function removeClass(el, className) {\n  var element = el;\n\n  if (!element) {\n    return false;\n  }\n\n  element.classList.remove(className);\n  return element;\n};\n/**\n * Remove a class from an element that contains a string\n *\n * @param el\n * @param string\n */\n\nvar removeClassThatContains = function removeClassThatContains(el) {\n  var string = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';\n\n  for (var i = 0; i < el.classList.length; i++) {\n    if (el.classList.item(i).indexOf(string) !== -1) {\n      el.classList.remove(el.classList.item(i));\n    }\n  }\n};\n/**\n * Compares an els classList against an array of strings to see if any match\n *\n * @param el the element to check against\n * @param arr The array of classes as strings to test against\n * @param prefix optional prefix string applied to all test strings\n * @param suffix optional suffix string\n */\n\nvar hasClassFromArray = function hasClassFromArray(el) {\n  var arr = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];\n  var prefix = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';\n  var suffix = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '';\n  return arr.some(function (c) {\n    return el.classList.contains(\"\".concat(prefix).concat(c).concat(suffix));\n  });\n};\n/**\n * Highly efficient function to convert a nodelist into a standard array. Allows you to run Array.forEach\n *\n * @param {Element|NodeList} elements to convert\n * @return {Array} Of converted elements\n */\n\nvar convertElements = function convertElements() {\n  var elements = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];\n  var converted = [];\n  var i = elements.length;\n\n  for (i; i--; converted.unshift(elements[i])) {\n    ;\n  } // eslint-disable-line\n\n\n  return converted;\n};\n/**\n * Should be used at all times for getting nodes throughout our app. Please use the data-js attribute whenever possible\n *\n * @param selector The selector string to search for. If arg 4 is false (default) then we search for [data-js=\"selector\"]\n * @param convert Convert the NodeList to an array? Then we can Array.forEach directly. Uses convertElements from above\n * @param node Parent node to search from. Defaults to document\n * @param custom Is this a custom selector where we don't want to use the data-js attribute?\n * @return {NodeList}\n */\n\nvar getNodes = function getNodes() {\n  var selector = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';\n  var convert = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;\n  var node = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : document;\n  var custom = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;\n  var selectorString = custom ? selector : \"[data-js=\\\"\".concat(selector, \"\\\"]\");\n  var nodes = node.querySelectorAll(selectorString);\n\n  if (convert) {\n    nodes = convertElements(nodes);\n  }\n\n  return nodes;\n};\n/**\n * Util to see if we should load a given chunk on a page. Just add data-load-chunk-products to load that particular\n * one on any element on the page.\n * Handled in assets/js/src/theme/core/components.js and assets/js/src/theme/core/pages.js\n *\n * @param name chunk name\n */\n\nvar shouldLoadChunk = function shouldLoadChunk() {\n  var name = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';\n  var node = document.querySelectorAll(\"[data-load-chunk-\".concat(name, \"]\"));\n  return node.length > 0;\n};\n/**\n * Gets the closest ancestor that matches a selector string\n *\n * @param el\n * @param selector\n * @return {*}\n */\n\nvar closest = function closest(el, selector) {\n  var matchesFn;\n  var parent;\n  ['matches', 'webkitMatchesSelector', 'mozMatchesSelector', 'msMatchesSelector', 'oMatchesSelector'].some(function (fn) {\n    if (typeof document.body[fn] === 'function') {\n      matchesFn = fn;\n      return true;\n    }\n    /* istanbul ignore next */\n\n\n    return false;\n  });\n\n  while (el) {\n    parent = el.parentElement;\n\n    if (parent && parent[matchesFn](selector)) {\n      return parent;\n    }\n\n    el = parent; // eslint-disable-line\n  }\n\n  return null;\n};\n/**\n * Insert a node after another node\n *\n * @param newNode {Element|NodeList}\n * @param referenceNode {Element|NodeList}\n */\n\nvar insertAfter = function insertAfter(newNode, referenceNode) {\n  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextElementSibling);\n};\n/**\n * Insert a node before another node\n *\n * @param newNode {Element|NodeList}\n * @param referenceNode {Element|NodeList}\n */\n\nvar insertBefore = function insertBefore(newNode, referenceNode) {\n  referenceNode.parentNode.insertBefore(newNode, referenceNode);\n};\n/**\n * Set multiple element attributes at once\n *\n * @param el\n * @param attrs\n */\n\nvar setAttributes = function setAttributes(el, attrs) {\n  for (var key in attrs) {\n    el.setAttribute(key, attrs[key]);\n  }\n};\n;// CONCATENATED MODULE: ./src/js/common/components/index.js\n/**\n * Components\n *\n * Initializes all common components\n */\n\nvar el = {\n  inbox: getNodes('gflow-inbox')[0]\n};\n/**\n * @function init\n * @description Initialize the module\n */\n\nvar init = function init() {\n  if (el.inbox) {\n    Promise.all(/*! import() | common-inbox */[__webpack_require__.e(\"vendor-theme\"), __webpack_require__.e(\"common-inbox\")]).then(__webpack_require__.bind(__webpack_require__, /*! ./inbox */ \"./src/js/common/components/inbox.js\")).then(function (module) {\n      module[\"default\"](el.inbox);\n    });\n  }\n\n  console.info('Gravity Flow Common: Initialized all common components.');\n};\n\n/* harmony default export */ const components = (init);\n;// CONCATENATED MODULE: ./src/js/common/index.js\n/**\n * Common\n *\n * Code shared between the theme and admin bundles.\n */\n\n/**\n * @function init\n * @description Initialize the module\n */\n\nvar common_init = function init() {\n  components();\n  console.info('Gravity Flow Common: Initialized all common scripts.');\n};\n\n/* harmony default export */ const common = (common_init);\n;// CONCATENATED MODULE: ./src/js/theme/core/ready.js\n\n\n // @EXAMPLE_REACT_APP\n// import * as tools from 'utils/tools';\n// import { HMR_DEV } from 'config/wp-settings';\n// you MUST do this in every module you use lodash in.\n// A custom bundle of only the lodash you use will be built by babel.\n\n\n\n\n // @EXAMPLE_REACT_APP\n// const el = {\n// \texampleAppRoot: tools.getNodes( 'example-app' )[ 0 ],\n// };\n\n/**\n * @function bindEvents\n * @description Bind global event listeners here,\n */\n\nvar bindEvents = function bindEvents() {\n  on(window, 'resize', debounce_default()(core_resize, 200, false));\n};\n/**\n * @function init\n * @description The core dispatcher for init across the codebase.\n */\n\n\nvar ready_init = function init() {\n  // apply browser classes\n  apply_browser_classes(); // init external plugins\n\n  core_plugins(); // set initial states\n\n  viewport_dims(); // initialize global events\n\n  bindEvents(); // initialize modules\n\n  common(); // @EXAMPLE_REACT_APP (Make sure to include the wrapping if block for ALL react apps\n  ////////////////////\n  ///////////////////////////////////////////\n  //////////////////////////////////////////////////////////\n  /////\n  //////////\n  // }\n\n  console.info('GravityFlow Theme: Initialized all javascript that targeted document ready.');\n};\n/**\n * @function domReady\n * @description Export our dom ready enabled init.\n */\n\n\nvar domReady = function domReady() {\n  ready(ready_init);\n};\n\n/* harmony default export */ const core_ready = (domReady);\n;// CONCATENATED MODULE: ./src/js/theme/index.js\n\n\ncore_ready();//# sourceURL=[module]\n//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy91dGlscy9ldmVudHMuanM/MzAxNCIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy91dGlscy90ZXN0cy5qcz81ZmNmIiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3V0aWxzL2RvbS9hcHBseS1icm93c2VyLWNsYXNzZXMuanM/ZmZjOSIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy90aGVtZS9jb25maWcvc3RhdGUuanM/NTU1ZSIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy90aGVtZS9jb25maWcvb3B0aW9ucy5qcz83YmY3Iiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3RoZW1lL2NvcmUvdmlld3BvcnQtZGltcy5qcz8wNDEyIiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3RoZW1lL2NvcmUvcmVzaXplLmpzP2U1ZjciLCJ3ZWJwYWNrOi8vZ3Jhdml0eWZsb3cvLi9zcmMvanMvdGhlbWUvY29yZS9wbHVnaW5zLmpzPzU3MjEiLCJ3ZWJwYWNrOi8vZ3Jhdml0eWZsb3cvLi9zcmMvanMvdXRpbHMvdG9vbHMuanM/YjE2MSIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy9jb21tb24vY29tcG9uZW50cy9pbmRleC5qcz8zNzE1Iiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL2NvbW1vbi9pbmRleC5qcz9iODhmIiwid2VicGFjazovL2dyYXZpdHlmbG93Ly4vc3JjL2pzL3RoZW1lL2NvcmUvcmVhZHkuanM/MzVhZCIsIndlYnBhY2s6Ly9ncmF2aXR5Zmxvdy8uL3NyYy9qcy90aGVtZS9pbmRleC5qcz82YjZmIl0sIm5hbWVzIjpbIm9uIiwiZWwiLCJuYW1lIiwiaGFuZGxlciIsImFkZEV2ZW50TGlzdGVuZXIiLCJhdHRhY2hFdmVudCIsImNhbGwiLCJyZWFkeSIsImZuIiwiZG9jdW1lbnQiLCJyZWFkeVN0YXRlIiwidHJpZ2dlciIsIm9wdHMiLCJldmVudCIsIm9wdGlvbnMiLCJkYXRhIiwiY3JlYXRlRXZlbnQiLCJpbml0RXZlbnQiLCJ3aW5kb3ciLCJDdXN0b21FdmVudCIsImRldGFpbCIsImUiLCJpbml0Q3VzdG9tRXZlbnQiLCJkaXNwYXRjaEV2ZW50IiwiaXNKc29uIiwic3RyIiwiSlNPTiIsInBhcnNlIiwiY2FuTG9jYWxTdG9yZSIsIm1vZCIsInJlc3VsdCIsIkRhdGUiLCJsb2NhbFN0b3JhZ2UiLCJzZXRJdGVtIiwidG9TdHJpbmciLCJnZXRJdGVtIiwicmVtb3ZlSXRlbSIsIl9lcnJvciIsImFuZHJvaWQiLCJ0ZXN0IiwibmF2aWdhdG9yIiwidXNlckFnZW50IiwiY2hyb21lIiwiZmlyZWZveCIsIkluc3RhbGxUcmlnZ2VyIiwiaWUiLCJkb2N1bWVudE1vZGUiLCJlZGdlIiwiU3R5bGVNZWRpYSIsImlvcyIsIm1hdGNoIiwiaW9zTW9iaWxlIiwib3BlcmEiLCJpbmRleE9mIiwic2FmYXJpIiwiT2JqZWN0IiwicHJvdG90eXBlIiwiSFRNTEVsZW1lbnQiLCJ3ZWJraXRBdWRpb0NvbnRleHQiLCJvcyIsInBsYXRmb3JtIiwiYnJvd3NlclRlc3RzIiwiYXBwbHlCcm93c2VyQ2xhc3NlcyIsImJyb3dzZXIiLCJjbGFzc2VzIiwiYm9keSIsImNsYXNzTGlzdCIsImFkZCIsImRlc2t0b3BfaW5pdGlhbGl6ZWQiLCJpc19kZXNrdG9wIiwiaXNfdGFibGV0IiwiaXNfbW9iaWxlIiwibW9iaWxlX2luaXRpYWxpemVkIiwidl9oZWlnaHQiLCJ2X3dpZHRoIiwiTUVESVVNX0JSRUFLUE9JTlQiLCJGVUxMX0JSRUFLUE9JTlQiLCJ2aWV3cG9ydERpbXMiLCJzdGF0ZSIsInZlcmdlIiwicmVzaXplIiwicGx1Z2lucyIsImFkZENsYXNzIiwiY2xhc3NOYW1lIiwiZWxlbWVudCIsImdldENoaWxkcmVuIiwiY2hpbGRyZW4iLCJpIiwibGVuZ3RoIiwibm9kZVR5cGUiLCJ1bnNoaWZ0IiwiaGFzQ2xhc3MiLCJjb250YWlucyIsInJlbW92ZUNsYXNzIiwicmVtb3ZlIiwicmVtb3ZlQ2xhc3NUaGF0Q29udGFpbnMiLCJzdHJpbmciLCJpdGVtIiwiaGFzQ2xhc3NGcm9tQXJyYXkiLCJhcnIiLCJwcmVmaXgiLCJzdWZmaXgiLCJzb21lIiwiYyIsImNvbnZlcnRFbGVtZW50cyIsImVsZW1lbnRzIiwiY29udmVydGVkIiwiZ2V0Tm9kZXMiLCJzZWxlY3RvciIsImNvbnZlcnQiLCJub2RlIiwiY3VzdG9tIiwic2VsZWN0b3JTdHJpbmciLCJub2RlcyIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJzaG91bGRMb2FkQ2h1bmsiLCJjbG9zZXN0IiwibWF0Y2hlc0ZuIiwicGFyZW50IiwicGFyZW50RWxlbWVudCIsImluc2VydEFmdGVyIiwibmV3Tm9kZSIsInJlZmVyZW5jZU5vZGUiLCJwYXJlbnROb2RlIiwiaW5zZXJ0QmVmb3JlIiwibmV4dEVsZW1lbnRTaWJsaW5nIiwic2V0QXR0cmlidXRlcyIsImF0dHJzIiwia2V5Iiwic2V0QXR0cmlidXRlIiwiaW5ib3giLCJ0b29scyIsImluaXQiLCJ0aGVuIiwibW9kdWxlIiwiY29uc29sZSIsImluZm8iLCJjb21wb25lbnRzIiwiYmluZEV2ZW50cyIsImNvbW1vbiIsImRvbVJlYWR5Il0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7Ozs7OztBQU9BLElBQU1BLEVBQUUsR0FBRyxTQUFMQSxFQUFLLENBQUVDLEVBQUYsRUFBTUMsSUFBTixFQUFZQyxPQUFaLEVBQXlCO0FBQ25DLE1BQUtGLEVBQUUsQ0FBQ0csZ0JBQVIsRUFBMkI7QUFDMUJILE1BQUUsQ0FBQ0csZ0JBQUgsQ0FBcUJGLElBQXJCLEVBQTJCQyxPQUEzQjtBQUNBLEdBRkQsTUFFTztBQUNORixNQUFFLENBQUNJLFdBQUgsYUFBc0JILElBQXRCLEdBQStCLFlBQU07QUFDcENDLGFBQU8sQ0FBQ0csSUFBUixDQUFjTCxFQUFkO0FBQ0EsS0FGRDtBQUdBO0FBQ0QsQ0FSRDs7QUFVQSxJQUFNTSxLQUFLLEdBQUcsU0FBUkEsS0FBUSxD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bnQiOlsiLyoqXG4gKiBAbW9kdWxlXG4gKiBAZGVzY3JpcHRpb24gU29tZSBldmVudCBmdW5jdGlvbnMgZm9yIHVzZSBpbiBvdGhlciBtb2R1bGVzXG4gKi9cblxuaW1wb3J0IF8gZnJvbSAnbG9kYXNoJztcblxuY29uc3Qgb24gPSAoIGVsLCBuYW1lLCBoYW5kbGVyICkgPT4ge1xuXHRpZiAoIGVsLmFkZEV2ZW50TGlzdGVuZXIgKSB7XG5cdFx0ZWwuYWRkRXZlbnRMaXN0ZW5lciggbmFtZSwgaGFuZGxlciApO1xuXHR9IGVsc2Uge1xuXHRcdGVsLmF0dGFjaEV2ZW50KCBgb24keyBuYW1lIH1gLCAoKSA9PiB7XG5cdFx0XHRoYW5kbGVyLmNhbGwoIGVsICk7XG5cdFx0fSApO1xuXHR9XG59O1xuXG5jb25zdCByZWFkeSA9ICggZm4gKSA9PiB7XG5cdGlmICggZG9jdW1lbnQucmVhZHlTdGF0ZSAhPT0gJ2xvYWRpbmcnICkge1xuXHRcdGZuKCk7XG5cdH0gZWxzZSBpZiAoIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIgKSB7XG5cdFx0ZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lciggJ0RPTUNvbnRlbnRMb2FkZWQnLCBmbiApO1xuXHR9IGVsc2Uge1xuXHRcdGRvY3VtZW50LmF0dGFjaEV2ZW50KCAnb25yZWFkeXN0YXRlY2hhbmdlJywgKCkgPT4ge1xuXHRcdFx0aWYgKCBkb2N1bWVudC5yZWFkeVN0YXRlICE9PSAnbG9hZGluZycgKSB7XG5cdFx0XHRcdGZuKCk7XG5cdFx0XHR9XG5cdFx0fSApO1xuXHR9XG59O1xuXG5jb25zdCB0cmlnZ2VyID0gKCBvcHRzICkgPT4ge1xuXHRsZXQgZXZlbnQ7XG5cdGNvbnN0IG9wdGlvbnMgPSBfLmFzc2lnbihcblx0XHR7XG5cdFx0XHRkYXRhOiB7fSxcblx0XHRcdGVsOiBkb2N1bWVudCxcblx0XHRcdGV2ZW50OiAnJyxcblx0XHRcdG5hdGl2ZTogdHJ1ZSxcblx0XHR9LFxuXHRcdG9wdHNcblx0KTtcblxuXHRpZiAoIG9wdGlvbnMubmF0aXZlICkge1xuXHRcdGV2ZW50ID0gZG9jdW1lbnQuY3JlYXRlRXZlbnQoICdIVE1MRXZlbnRzJyApO1xuXHRcdGV2ZW50LmluaXRFdmVudCggb3B0aW9ucy5ldmVudCwgdHJ1ZSwgZmFsc2UgKTtcblx0fSBlbHNlIHtcblx0XHR0cnkge1xuXHRcdFx0ZXZlbnQgPSBuZXcgd2luZG93LkN1c3RvbUV2ZW50KCBvcHRpb25zLmV2ZW50LCB7XG5cdFx0XHRcdGRldGFpbDogb3B0aW9ucy5kYXRhLFxuXHRcdFx0fSApO1xuXHRcdH0gY2F0Y2ggKCBlICkge1xuXHRcdFx0ZXZlbnQgPSBkb2N1bWVudC5jcmVhdGVFdmVudCggJ0N1c3RvbUV2ZW50JyApO1xuXHRcdFx0ZXZlbnQuaW5pdEN1c3RvbUV2ZW50KCBvcHRpb25zLmV2ZW50LCB0cnVlLCB0cnVlLCBvcHRpb25zLmRhdGEgKTtcblx0XHR9XG5cdH1cblxuXHRvcHRpb25zLmVsLmRpc3BhdGNoRXZlbnQoIGV2ZW50ICk7XG59O1xuXG5leHBvcnQgeyBvbiwgcmVhZHksIHRyaWdnZXIgfTtcbiIsIi8qKlxuICogQG1vZHVsZVxuICogQGRlc2NyaXB0aW9uIFNvbWUgaGFuZHkgdGVzdCBmb3IgY29tbW9uIGlzc3Vlcy5cbiAqL1xuXG5jb25zdCBpc0pzb24gPSAoIHN0ciApID0+IHtcblx0dHJ5IHtcblx0XHRKU09OLnBhcnNlKCBzdHIgKTtcblx0fSBjYXRjaCAoIGUgKSB7XG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9XG5cblx0cmV0dXJuIHRydWU7XG59O1xuXG5jb25zdCBjYW5Mb2NhbFN0b3JlID0gKCkgPT4ge1xuXHRsZXQgbW9kO1xuXHRsZXQgcmVzdWx0ID0gZmFsc2U7XG5cblx0dHJ5IHtcblx0XHRtb2QgPSBuZXcgRGF0ZSgpO1xuXHRcdHdpbmRvdy5sb2NhbFN0b3JhZ2Uuc2V0SXRlbSggbW9kLCBtb2QudG9TdHJpbmcoKSApO1xuXG5cdFx0cmVzdWx0ID0gd2luZG93LmxvY2FsU3RvcmFnZS5nZXRJdGVtKCBtb2QgKSA9PT0gbW9kLnRvU3RyaW5nKCk7XG5cdFx0d2luZG93LmxvY2FsU3RvcmFnZS5yZW1vdmVJdGVtKCBtb2QgKTtcblx0XHRyZXR1cm4gcmVzdWx0O1xuXHR9IGNhdGNoICggX2Vycm9yICkge1xuXHRcdHJldHVybiByZXN1bHQ7XG5cdH1cbn07XG5cbmNvbnN0IGFuZHJvaWQgPSAvKGFuZHJvaWQpL2kudGVzdCggd2luZG93Lm5hdmlnYXRvci51c2VyQWdlbnQgKTtcbmNvbnN0IGNocm9tZSA9ICEhIHdpbmRvdy5jaHJvbWU7XG5jb25zdCBmaXJlZm94ID0gdHlwZW9mIEluc3RhbGxUcmlnZ2VyICE9PSAndW5kZWZpbmVkJztcbmNvbnN0IGllID0gLyogQGNjX29uIUAgKi8gZmFsc2UgfHwgZG9jdW1lbnQuZG9jdW1lbnRNb2RlIHx8IGZhbHNlO1xuY29uc3QgZWRnZSA9ICEgaWUgJiYgISEgd2luZG93LlN0eWxlTWVkaWE7XG5jb25zdCBpb3MgPSAhISB3aW5kb3cubmF2aWdhdG9yLnVzZXJBZ2VudC5tYXRjaCggLyhpUG9kfGlQaG9uZXxpUGFkKS9pICk7XG5jb25zdCBpb3NNb2JpbGUgPSAhISB3aW5kb3cubmF2aWdhdG9yLnVzZXJBZ2VudC5tYXRjaCggLyhpUG9kfGlQaG9uZSkvaSApO1xuY29uc3Qgb3BlcmEgPVxuXHQhISB3aW5kb3cub3BlcmEgfHwgd2luZG93Lm5hdmlnYXRvci51c2VyQWdlbnQuaW5kZXhPZiggJyBPUFIvJyApID49IDA7XG5jb25zdCBzYWZhcmkgPSBPYmplY3QucHJvdG90eXBlLnRvU3RyaW5nLmNhbGwod2luZG93LkhUTUxFbGVtZW50KS5pbmRleE9mKCdDb25zdHJ1Y3RvcicpID4gMCB8fCAhY2hyb21lICYmICFvcGVyYSAmJiB3aW5kb3cud2Via2l0QXVkaW9Db250ZXh0ICE9PSAndW5kZWZpbmVkJzsgLy8gZXNsaW50LWRpc2FibGUtbGluZVxuY29uc3Qgb3MgPSB3aW5kb3cubmF2aWdhdG9yLnBsYXRmb3JtO1xuXG4vKipcbiAqIGRvIG5vdCBjaGFuZ2UgdG8gYXJyb3cgZnVuY3Rpb24gdW50aWwgdGVzdGluZyBkZXBlbmRlbmNpZXMgYXJlIHVwZGF0ZWQgYmV5b25kIHRoZSBmb2xsb3dpbmcgcmVwb3J0ZWQgaXNzdWVcbiAqIGh0dHBzOi8vZ2l0aHViLmNvbS9mYWNlYm9vay9qZXN0L2lzc3Vlcy81MDAxXG4gKi9cbmZ1bmN0aW9uIGJyb3dzZXJUZXN0cygpIHtcblx0cmV0dXJuIHtcblx0XHRhbmRyb2lkLFxuXHRcdGNocm9tZSxcblx0XHRlZGdlLFxuXHRcdGZpcmVmb3gsXG5cdFx0aWUsXG5cdFx0aW9zLFxuXHRcdGlvc01vYmlsZSxcblx0XHRvcGVyYSxcblx0XHRzYWZhcmksXG5cdFx0b3MsXG5cdH07XG59XG5cbmV4cG9ydCB7IGlzSnNvbiwgY2FuTG9jYWxTdG9yZSwgYnJvd3NlclRlc3RzIH07XG4iLCIvKipcbiAqIEBmdW5jdGlvbiBicm93c2VyQ2xhc3Nlc1xuICogQGRlc2NyaXB0aW9uIHNldHMgdXAgYnJvd3NlciBjbGFzc2VzIG9uIGJvZHkgd2l0aG91dCB1c2luZyB1c2VyIGFnZW50IHN0cmluZ3Mgd2hlcmUgcG9zc2libGUuXG4gKi9cblxuaW1wb3J0ICogYXMgdGVzdCBmcm9tICcuLi90ZXN0cyc7XG5cbmNvbnN0IGFwcGx5QnJvd3NlckNsYXNzZXMgPSAoKSA9PiB7XG5cdGNvbnN0IGJyb3dzZXIgPSB0ZXN0LmJyb3dzZXJUZXN0cygpO1xuXHRjb25zdCBjbGFzc2VzID0gZG9jdW1lbnQuYm9keS5jbGFzc0xpc3Q7XG5cblx0aWYgKCBicm93c2VyLmFuZHJvaWQgKSB7XG5cdFx0Y2xhc3Nlcy5hZGQoICdkZXZpY2UtYW5kcm9pZCcgKTtcblx0fSBlbHNlIGlmICggYnJvd3Nlci5pb3MgKSB7XG5cdFx0Y2xhc3Nlcy5hZGQoICdkZXZpY2UtaW9zJyApO1xuXHR9XG5cblx0aWYgKCBicm93c2VyLmVkZ2UgKSB7XG5cdFx0Y2xhc3Nlcy5hZGQoICdicm93c2VyLWVkZ2UnICk7XG5cdH0gZWxzZSBpZiAoIGJyb3dzZXIuY2hyb21lICkge1xuXHRcdGNsYXNzZXMuYWRkKCAnYnJvd3Nlci1jaHJvbWUnICk7XG5cdH0gZWxzZSBpZiAoIGJyb3dzZXIuZmlyZWZveCApIHtcblx0XHRjbGFzc2VzLmFkZCggJ2Jyb3dzZXItZmlyZWZveCcgKTtcblx0fSBlbHNlIGlmICggYnJvd3Nlci5pZSApIHtcblx0XHRjbGFzc2VzLmFkZCggJ2Jyb3dzZXItaWUnICk7XG5cdH0gZWxzZSBpZiAoIGJyb3dzZXIub3BlcmEgKSB7XG5cdFx0Y2xhc3Nlcy5hZGQoICdicm93c2VyLW9wZXJhJyApO1xuXHR9IGVsc2UgaWYgKCBicm93c2VyLnNhZmFyaSApIHtcblx0XHRjbGFzc2VzLmFkZCggJ2Jyb3dzZXItc2FmYXJpJyApO1xuXHR9XG59O1xuXG5leHBvcnQgZGVmYXVsdCBhcHBseUJyb3dzZXJDbGFzc2VzO1xuIiwiZXhwb3J0IGRlZmF1bHQge1xuXHRkZXNrdG9wX2luaXRpYWxpemVkOiBmYWxzZSxcblx0aXNfZGVza3RvcDogZmFsc2UsXG5cdGlzX3RhYmxldDogZmFsc2UsXG5cdGlzX21vYmlsZTogZmFsc2UsXG5cdG1vYmlsZV9pbml0aWFsaXplZDogZmFsc2UsXG5cdHZfaGVpZ2h0OiAwLFxuXHR2X3dpZHRoOiAwLFxufTtcbiIsIi8vIGJyZWFrcG9pbnQgc2V0dGluZ3NcblxuZXhwb3J0IGNvbnN0IE1FRElVTV9CUkVBS1BPSU5UID0gNzY4O1xuZXhwb3J0IGNvbnN0IEZVTExfQlJFQUtQT0lOVCA9IDk2MDtcbiIsIi8qKlxuICogQG1vZHVsZVxuICogQGV4cG9ydHMgdmlld3BvcnREaW1zXG4gKiBAZGVzY3JpcHRpb24gU2V0cyB2aWV3cG9ydCBkaW1lbnNpb25zIHVzaW5nIHZlcmdlIG9uIHNoYXJlZCBzdGF0ZVxuICogYW5kIGRldGVjdHMgbW9iaWxlIG9yIGRlc2t0b3Agc3RhdGUuXG4gKi9cblxuaW1wb3J0IHZlcmdlIGZyb20gJ3ZlcmdlJztcbmltcG9ydCBzdGF0ZSBmcm9tICcuLi9jb25maWcvc3RhdGUnO1xuaW1wb3J0IHsgRlVMTF9CUkVBS1BPSU5ULCBNRURJVU1fQlJFQUtQT0lOVCB9IGZyb20gJy4uL2NvbmZpZy9vcHRpb25zJztcblxuY29uc3Qgdmlld3BvcnREaW1zID0gKCkgPT4ge1xuXHRzdGF0ZS52X2hlaWdodCA9IHZlcmdlLnZpZXdwb3J0SCgpO1xuXHRzdGF0ZS52X3dpZHRoID0gdmVyZ2Uudmlld3BvcnRXKCk7XG5cblx0aWYgKCBzdGF0ZS52X3dpZHRoID49IEZVTExfQlJFQUtQT0lOVCApIHtcblx0XHRzdGF0ZS5pc19kZXNrdG9wID0gdHJ1ZTtcblx0XHRzdGF0ZS5pc190YWJsZXQgPSBmYWxzZTtcblx0XHRzdGF0ZS5pc19tb2JpbGUgPSBmYWxzZTtcblx0fSBlbHNlIGlmICggc3RhdGUudl93aWR0aCA+PSBNRURJVU1fQlJFQUtQT0lOVCApIHtcblx0XHRzdGF0ZS5pc19kZXNrdG9wID0gZmFsc2U7XG5cdFx0c3RhdGUuaXNfdGFibGV0ID0gdHJ1ZTtcblx0XHRzdGF0ZS5pc19tb2JpbGUgPSBmYWxzZTtcblx0fSBlbHNlIHtcblx0XHRzdGF0ZS5pc19kZXNrdG9wID0gZmFsc2U7XG5cdFx0c3RhdGUuaXNfdGFibGV0ID0gZmFsc2U7XG5cdFx0c3RhdGUuaXNfbW9iaWxlID0gdHJ1ZTtcblx0fVxufTtcblxuZXhwb3J0IGRlZmF1bHQgdmlld3BvcnREaW1zO1xuIiwiLyoqXG4gKiBAbW9kdWxlXG4gKiBAZXhwb3J0cyByZXNpemVcbiAqIEBkZXNjcmlwdGlvbiBLaWNrcyBpbiBhbnkgdGhpcmQgcGFydHkgcGx1Z2lucyB0aGF0IG9wZXJhdGUgb24gYSBzaXRld2lkZSBiYXNpcy5cbiAqL1xuXG5pbXBvcnQgeyB0cmlnZ2VyIH0gZnJvbSAndXRpbHMvZXZlbnRzJztcbmltcG9ydCB2aWV3cG9ydERpbXMgZnJvbSAnLi92aWV3cG9ydC1kaW1zJztcblxuY29uc3QgcmVzaXplID0gKCkgPT4ge1xuXHQvLyBjb2RlIGZvciByZXNpemUgZXZlbnRzIGNhbiBnbyBoZXJlXG5cblx0dmlld3BvcnREaW1zKCk7XG5cblx0dHJpZ2dlciggeyBldmVudDogJ2dyYXZpdHlmbG93L3Jlc2l6ZV9leGVjdXRlZCcsIG5hdGl2ZTogZmFsc2UgfSApO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgcmVzaXplO1xuIiwiLyoqXG4gKiBAbW9kdWxlXG4gKiBAZXhwb3J0cyBwbHVnaW5zXG4gKiBAZGVzY3JpcHRpb24gS2lja3MgaW4gYW55IHRoaXJkIHBhcnR5IHBsdWdpbnMgdGhhdCBvcGVyYXRlIG9uXG4gKiBhIHNpdGV3aWRlIGJhc2lzLlxuICovXG5cbmNvbnN0IHBsdWdpbnMgPSAoKSA9PiB7XG5cdC8vIGluaXRpYWxpemUgZ2xvYmFsIGV4dGVybmFsIHBsdWdpbnMgaGVyZVxufTtcblxuZXhwb3J0IGRlZmF1bHQgcGx1Z2lucztcbiIsIi8qKlxuICogQG1vZHVsZVxuICogQGRlc2NyaXB0aW9uIFNvbWUgdmFuaWxsYSBqcyBjcm9zcyBicm93c2VyIHV0aWxzXG4gKi9cblxuLyoqXG4gKiBBZGQgYSBjbGFzcyB0byBhIGRvbSBlbGVtZW50IG9yIGV4aXQgc2FmZWx5IGlmIG5vdCBzZXRcbiAqXG4gKiBAcGFyYW0gZWwgTm9kZVxuICogQHBhcmFtIGNsYXNzTmFtZSBDbGFzcyBzdHJpbmdcbiAqIEByZXR1cm4geyp9IE5vZGUgb3IgZmFsc2VcbiAqL1xuXG5leHBvcnQgY29uc3QgYWRkQ2xhc3MgPSAoIGVsLCBjbGFzc05hbWUgPSAnJyApID0+IHtcblx0Y29uc3QgZWxlbWVudCA9IGVsO1xuXHRpZiAoICEgZWxlbWVudCApIHtcblx0XHRyZXR1cm4gZmFsc2U7XG5cdH1cblxuXHRlbGVtZW50LmNsYXNzTGlzdC5hZGQoIGNsYXNzTmFtZSApO1xuXHRyZXR1cm4gZWxlbWVudDtcbn07XG5cbi8qKlxuICpcbiAqIEdldCBpbW1lZGlhdGUgY2hpbGQgbm9kZXMgYW5kIHJldHVybiBhbiBhcnJheSBvZiB0aGVtXG4gKlxuICogQHBhcmFtIGVsXG4gKiBAcmV0dXJuIHtBcnJheX0gSXRlcmFibGUgYXJyYXkgb2YgZG9tIG5vZGVzXG4gKi9cblxuZXhwb3J0IGNvbnN0IGdldENoaWxkcmVuID0gKCBlbCApID0+IHtcblx0Y29uc3QgY2hpbGRyZW4gPSBbXTtcblx0bGV0IGkgPSBlbC5jaGlsZHJlbi5sZW5ndGg7XG5cdGZvciAoaTsgaS0tOykgeyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lXG5cdFx0aWYgKCBlbC5jaGlsZHJlblsgaSBdLm5vZGVUeXBlICE9PSA4ICkge1xuXHRcdFx0Y2hpbGRyZW4udW5zaGlmdCggZWwuY2hpbGRyZW5bIGkgXSApO1xuXHRcdH1cblx0fVxuXG5cdHJldHVybiBjaGlsZHJlbjtcbn07XG5cbi8qKlxuICpcbiAqIFRlc3QgaWYgYSBkb20gbm9kZSBoYXMgYSBjbGFzcyBvciByZXR1cm5zIGZhbHNlIGlmIGVsIG5vdCBkZWZpbmVkXG4gKlxuICogQHBhcmFtIGVsXG4gKiBAcGFyYW0gY2xhc3NOYW1lXG4gKiBAcmV0dXJuIHtib29sZWFufVxuICovXG5cbmV4cG9ydCBjb25zdCBoYXNDbGFzcyA9ICggZWwsIGNsYXNzTmFtZSA9ICcnICkgPT4ge1xuXHRpZiAoICEgZWwgKSB7XG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9XG5cblx0cmV0dXJuIGVsLmNsYXNzTGlzdC5jb250YWlucyggY2xhc3NOYW1lICk7XG59O1xuXG4vKipcbiAqIFJlbW92ZXMgYSBjbGFzcyBmcm9tIHRoZSBkb20gbm9kZVxuICpcbiAqIEBwYXJhbSBlbFxuICogQHBhcmFtIGNsYXNzTmFtZVxuICogQHJldHVybiB7Kn0gcmV0dXJucyBmYWxzZSBvciBlbCBpZiBwYXNzZWRcbiAqL1xuXG5leHBvcnQgY29uc3QgcmVtb3ZlQ2xhc3MgPSAoIGVsLCBjbGFzc05hbWUgKSA9PiB7XG5cdGNvbnN0IGVsZW1lbnQgPSBlbDtcblx0aWYgKCAhIGVsZW1lbnQgKSB7XG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9XG5cblx0ZWxlbWVudC5jbGFzc0xpc3QucmVtb3ZlKCBjbGFzc05hbWUgKTtcblx0cmV0dXJuIGVsZW1lbnQ7XG59O1xuXG4vKipcbiAqIFJlbW92ZSBhIGNsYXNzIGZyb20gYW4gZWxlbWVudCB0aGF0IGNvbnRhaW5zIGEgc3RyaW5nXG4gKlxuICogQHBhcmFtIGVsXG4gKiBAcGFyYW0gc3RyaW5nXG4gKi9cblxuZXhwb3J0IGNvbnN0IHJlbW92ZUNsYXNzVGhhdENvbnRhaW5zID0gKCBlbCwgc3RyaW5nID0gJycgKSA9PiB7XG5cdGZvciAoIGxldCBpID0gMDsgaSA8IGVsLmNsYXNzTGlzdC5sZW5ndGg7IGkrKyApIHtcblx0XHRpZiAoIGVsLmNsYXNzTGlzdC5pdGVtKCBpICkuaW5kZXhPZiggc3RyaW5nICkgIT09IC0xICkge1xuXHRcdFx0ZWwuY2xhc3NMaXN0LnJlbW92ZSggZWwuY2xhc3NMaXN0Lml0ZW0oIGkgKSApO1xuXHRcdH1cblx0fVxufTtcblxuLyoqXG4gKiBDb21wYXJlcyBhbiBlbHMgY2xhc3NMaXN0IGFnYWluc3QgYW4gYXJyYXkgb2Ygc3RyaW5ncyB0byBzZWUgaWYgYW55IG1hdGNoXG4gKlxuICogQHBhcmFtIGVsIHRoZSBlbGVtZW50IHRvIGNoZWNrIGFnYWluc3RcbiAqIEBwYXJhbSBhcnIgVGhlIGFycmF5IG9mIGNsYXNzZXMgYXMgc3RyaW5ncyB0byB0ZXN0IGFnYWluc3RcbiAqIEBwYXJhbSBwcmVmaXggb3B0aW9uYWwgcHJlZml4IHN0cmluZyBhcHBsaWVkIHRvIGFsbCB0ZXN0IHN0cmluZ3NcbiAqIEBwYXJhbSBzdWZmaXggb3B0aW9uYWwgc3VmZml4IHN0cmluZ1xuICovXG5cbmV4cG9ydCBjb25zdCBoYXNDbGFzc0Zyb21BcnJheSA9ICggZWwsIGFyciA9IFtdLCBwcmVmaXggPSAnJywgc3VmZml4ID0gJycgKSA9PlxuXHRhcnIuc29tZSggKCBjICkgPT5cblx0XHRlbC5jbGFzc0xpc3QuY29udGFpbnMoIGAkeyBwcmVmaXggfSR7IGMgfSR7IHN1ZmZpeCB9YCApXG5cdCk7XG5cbi8qKlxuICogSGlnaGx5IGVmZmljaWVudCBmdW5jdGlvbiB0byBjb252ZXJ0IGEgbm9kZWxpc3QgaW50byBhIHN0YW5kYXJkIGFycmF5LiBBbGxvd3MgeW91IHRvIHJ1biBBcnJheS5mb3JFYWNoXG4gKlxuICogQHBhcmFtIHtFbGVtZW50fE5vZGVMaXN0fSBlbGVtZW50cyB0byBjb252ZXJ0XG4gKiBAcmV0dXJuIHtBcnJheX0gT2YgY29udmVydGVkIGVsZW1lbnRzXG4gKi9cblxuZXhwb3J0IGNvbnN0IGNvbnZlcnRFbGVtZW50cyA9ICggZWxlbWVudHMgPSBbXSApID0+IHtcblx0Y29uc3QgY29udmVydGVkID0gW107XG5cdGxldCBpID0gZWxlbWVudHMubGVuZ3RoO1xuXHRmb3IgKGk7IGktLTsgY29udmVydGVkLnVuc2hpZnQoZWxlbWVudHNbaV0pKTsgLy8gZXNsaW50LWRpc2FibGUtbGluZVxuXG5cdHJldHVybiBjb252ZXJ0ZWQ7XG59O1xuXG4vKipcbiAqIFNob3VsZCBiZSB1c2VkIGF0IGFsbCB0aW1lcyBmb3IgZ2V0dGluZyBub2RlcyB0aHJvdWdob3V0IG91ciBhcHAuIFBsZWFzZSB1c2UgdGhlIGRhdGEtanMgYXR0cmlidXRlIHdoZW5ldmVyIHBvc3NpYmxlXG4gKlxuICogQHBhcmFtIHNlbGVjdG9yIFRoZSBzZWxlY3RvciBzdHJpbmcgdG8gc2VhcmNoIGZvci4gSWYgYXJnIDQgaXMgZmFsc2UgKGRlZmF1bHQpIHRoZW4gd2Ugc2VhcmNoIGZvciBbZGF0YS1qcz1cInNlbGVjdG9yXCJdXG4gKiBAcGFyYW0gY29udmVydCBDb252ZXJ0IHRoZSBOb2RlTGlzdCB0byBhbiBhcnJheT8gVGhlbiB3ZSBjYW4gQXJyYXkuZm9yRWFjaCBkaXJlY3RseS4gVXNlcyBjb252ZXJ0RWxlbWVudHMgZnJvbSBhYm92ZVxuICogQHBhcmFtIG5vZGUgUGFyZW50IG5vZGUgdG8gc2VhcmNoIGZyb20uIERlZmF1bHRzIHRvIGRvY3VtZW50XG4gKiBAcGFyYW0gY3VzdG9tIElzIHRoaXMgYSBjdXN0b20gc2VsZWN0b3Igd2hlcmUgd2UgZG9uJ3Qgd2FudCB0byB1c2UgdGhlIGRhdGEtanMgYXR0cmlidXRlP1xuICogQHJldHVybiB7Tm9kZUxpc3R9XG4gKi9cblxuZXhwb3J0IGNvbnN0IGdldE5vZGVzID0gKFxuXHRzZWxlY3RvciA9ICcnLFxuXHRjb252ZXJ0ID0gZmFsc2UsXG5cdG5vZGUgPSBkb2N1bWVudCxcblx0Y3VzdG9tID0gZmFsc2VcbikgPT4ge1xuXHRjb25zdCBzZWxlY3RvclN0cmluZyA9IGN1c3RvbSA/IHNlbGVjdG9yIDogYFtkYXRhLWpzPVwiJHsgc2VsZWN0b3IgfVwiXWA7XG5cdGxldCBub2RlcyA9IG5vZGUucXVlcnlTZWxlY3RvckFsbCggc2VsZWN0b3JTdHJpbmcgKTtcblx0aWYgKCBjb252ZXJ0ICkge1xuXHRcdG5vZGVzID0gY29udmVydEVsZW1lbnRzKCBub2RlcyApO1xuXHR9XG5cdHJldHVybiBub2Rlcztcbn07XG5cbi8qKlxuICogVXRpbCB0byBzZWUgaWYgd2Ugc2hvdWxkIGxvYWQgYSBnaXZlbiBjaHVuayBvbiBhIHBhZ2UuIEp1c3QgYWRkIGRhdGEtbG9hZC1jaHVuay1wcm9kdWN0cyB0byBsb2FkIHRoYXQgcGFydGljdWxhclxuICogb25lIG9uIGFueSBlbGVtZW50IG9uIHRoZSBwYWdlLlxuICogSGFuZGxlZCBpbiBhc3NldHMvanMvc3JjL3RoZW1lL2NvcmUvY29tcG9uZW50cy5qcyBhbmQgYXNzZXRzL2pzL3NyYy90aGVtZS9jb3JlL3BhZ2VzLmpzXG4gKlxuICogQHBhcmFtIG5hbWUgY2h1bmsgbmFtZVxuICovXG5cbmV4cG9ydCBjb25zdCBzaG91bGRMb2FkQ2h1bmsgPSAoIG5hbWUgPSAnJyApID0+IHtcblx0Y29uc3Qgbm9kZSA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoIGBbZGF0YS1sb2FkLWNodW5rLSR7IG5hbWUgfV1gICk7XG5cdHJldHVybiBub2RlLmxlbmd0aCA+IDA7XG59O1xuXG4vKipcbiAqIEdldHMgdGhlIGNsb3Nlc3QgYW5jZXN0b3IgdGhhdCBtYXRjaGVzIGEgc2VsZWN0b3Igc3RyaW5nXG4gKlxuICogQHBhcmFtIGVsXG4gKiBAcGFyYW0gc2VsZWN0b3JcbiAqIEByZXR1cm4geyp9XG4gKi9cblxuZXhwb3J0IGNvbnN0IGNsb3Nlc3QgPSAoIGVsLCBzZWxlY3RvciApID0+IHtcblx0bGV0IG1hdGNoZXNGbjtcblx0bGV0IHBhcmVudDtcblxuXHRbXG5cdFx0J21hdGNoZXMnLFxuXHRcdCd3ZWJraXRNYXRjaGVzU2VsZWN0b3InLFxuXHRcdCdtb3pNYXRjaGVzU2VsZWN0b3InLFxuXHRcdCdtc01hdGNoZXNTZWxlY3RvcicsXG5cdFx0J29NYXRjaGVzU2VsZWN0b3InLFxuXHRdLnNvbWUoICggZm4gKSA9PiB7XG5cdFx0aWYgKCB0eXBlb2YgZG9jdW1lbnQuYm9keVsgZm4gXSA9PT0gJ2Z1bmN0aW9uJyApIHtcblx0XHRcdG1hdGNoZXNGbiA9IGZuO1xuXHRcdFx0cmV0dXJuIHRydWU7XG5cdFx0fVxuXHRcdC8qIGlzdGFuYnVsIGlnbm9yZSBuZXh0ICovXG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9ICk7XG5cblx0d2hpbGUgKCBlbCApIHtcblx0XHRwYXJlbnQgPSBlbC5wYXJlbnRFbGVtZW50O1xuXHRcdGlmICggcGFyZW50ICYmIHBhcmVudFsgbWF0Y2hlc0ZuIF0oIHNlbGVjdG9yICkgKSB7XG5cdFx0XHRyZXR1cm4gcGFyZW50O1xuXHRcdH1cblxuXHRcdGVsID0gcGFyZW50OyAvLyBlc2xpbnQtZGlzYWJsZS1saW5lXG5cdH1cblxuXHRyZXR1cm4gbnVsbDtcbn07XG5cbi8qKlxuICogSW5zZXJ0IGEgbm9kZSBhZnRlciBhbm90aGVyIG5vZGVcbiAqXG4gKiBAcGFyYW0gbmV3Tm9kZSB7RWxlbWVudHxOb2RlTGlzdH1cbiAqIEBwYXJhbSByZWZlcmVuY2VOb2RlIHtFbGVtZW50fE5vZGVMaXN0fVxuICovXG5leHBvcnQgY29uc3QgaW5zZXJ0QWZ0ZXIgPSAoIG5ld05vZGUsIHJlZmVyZW5jZU5vZGUgKSA9PiB7XG5cdHJlZmVyZW5jZU5vZGUucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUoXG5cdFx0bmV3Tm9kZSxcblx0XHRyZWZlcmVuY2VOb2RlLm5leHRFbGVtZW50U2libGluZ1xuXHQpO1xufTtcblxuLyoqXG4gKiBJbnNlcnQgYSBub2RlIGJlZm9yZSBhbm90aGVyIG5vZGVcbiAqXG4gKiBAcGFyYW0gbmV3Tm9kZSB7RWxlbWVudHxOb2RlTGlzdH1cbiAqIEBwYXJhbSByZWZlcmVuY2VOb2RlIHtFbGVtZW50fE5vZGVMaXN0fVxuICovXG5cbmV4cG9ydCBjb25zdCBpbnNlcnRCZWZvcmUgPSAoIG5ld05vZGUsIHJlZmVyZW5jZU5vZGUgKSA9PiB7XG5cdHJlZmVyZW5jZU5vZGUucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUoIG5ld05vZGUsIHJlZmVyZW5jZU5vZGUgKTtcbn07XG5cbi8qKlxuICogU2V0IG11bHRpcGxlIGVsZW1lbnQgYXR0cmlidXRlcyBhdCBvbmNlXG4gKlxuICogQHBhcmFtIGVsXG4gKiBAcGFyYW0gYXR0cnNcbiAqL1xuXG5leHBvcnQgY29uc3Qgc2V0QXR0cmlidXRlcyA9ICggZWwsIGF0dHJzICkgPT4ge1xuXHRmb3IgKCBjb25zdCBrZXkgaW4gYXR0cnMgKSB7XG5cdFx0ZWwuc2V0QXR0cmlidXRlKCBrZXksIGF0dHJzWyBrZXkgXSApO1xuXHR9XG59O1xuIiwiLyoqXG4gKiBDb21wb25lbnRzXG4gKlxuICogSW5pdGlhbGl6ZXMgYWxsIGNvbW1vbiBjb21wb25lbnRzXG4gKi9cblxuaW1wb3J0ICogYXMgdG9vbHMgZnJvbSAndXRpbHMvdG9vbHMnO1xuXG5jb25zdCBlbCA9IHtcblx0aW5ib3g6IHRvb2xzLmdldE5vZGVzKCAnZ2Zsb3ctaW5ib3gnIClbIDAgXSxcbn07XG5cbi8qKlxuICogQGZ1bmN0aW9uIGluaXRcbiAqIEBkZXNjcmlwdGlvbiBJbml0aWFsaXplIHRoZSBtb2R1bGVcbiAqL1xuXG5jb25zdCBpbml0ID0gKCkgPT4ge1xuXHRpZiAoIGVsLmluYm94ICkge1xuXHRcdGltcG9ydCggJy4vaW5ib3gnIC8qIHdlYnBhY2tDaHVua05hbWU6XCJjb21tb24taW5ib3hcIiAqLyApLnRoZW4oXG5cdFx0XHQoIG1vZHVsZSApID0+IHtcblx0XHRcdFx0bW9kdWxlLmRlZmF1bHQoIGVsLmluYm94ICk7XG5cdFx0XHR9XG5cdFx0KTtcblx0fVxuXG5cdGNvbnNvbGUuaW5mbyggJ0dyYXZpdHkgRmxvdyBDb21tb246IEluaXRpYWxpemVkIGFsbCBjb21tb24gY29tcG9uZW50cy4nICk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCBpbml0O1xuIiwiLyoqXG4gKiBDb21tb25cbiAqXG4gKiBDb2RlIHNoYXJlZCBiZXR3ZWVuIHRoZSB0aGVtZSBhbmQgYWRtaW4gYnVuZGxlcy5cbiAqL1xuXG5pbXBvcnQgY29tcG9uZW50cyBmcm9tICcuL2NvbXBvbmVudHMnO1xuXG4vKipcbiAqIEBmdW5jdGlvbiBpbml0XG4gKiBAZGVzY3JpcHRpb24gSW5pdGlhbGl6ZSB0aGUgbW9kdWxlXG4gKi9cblxuY29uc3QgaW5pdCA9ICgpID0+IHtcblx0Y29tcG9uZW50cygpO1xuXG5cdGNvbnNvbGUuaW5mbyggJ0dyYXZpdHkgRmxvdyBDb21tb246IEluaXRpYWxpemVkIGFsbCBjb21tb24gc2NyaXB0cy4nICk7XG59O1xuXG5leHBvcnQgZGVmYXVsdCBpbml0O1xuIiwiLyoqXG4gKiBAbW9kdWxlXG4gKiBAZXhwb3J0cyByZWFkeVxuICogQGRlc2NyaXB0aW9uIFRoZSBjb3JlIGRpc3BhdGNoZXIgZm9yIHRoZSBkb20gcmVhZHkgZXZlbnQgamF2YXNjcmlwdC5cbiAqL1xuXG5pbXBvcnQgXyBmcm9tICdsb2Rhc2gnO1xuaW1wb3J0IHsgb24sIHJlYWR5IH0gZnJvbSAndXRpbHMvZXZlbnRzJztcbmltcG9ydCBhcHBseUJyb3dzZXJDbGFzc2VzIGZyb20gJ3V0aWxzL2RvbS9hcHBseS1icm93c2VyLWNsYXNzZXMnO1xuLy8gQEVYQU1QTEVfUkVBQ1RfQVBQXG5cbi8vIGltcG9ydCAqIGFzIHRvb2xzIGZyb20gJ3V0aWxzL3Rvb2xzJztcbi8vIGltcG9ydCB7IEhNUl9ERVYgfSBmcm9tICdjb25maWcvd3Atc2V0dGluZ3MnO1xuXG4vLyB5b3UgTVVTVCBkbyB0aGlzIGluIGV2ZXJ5IG1vZHVsZSB5b3UgdXNlIGxvZGFzaCBpbi5cbi8vIEEgY3VzdG9tIGJ1bmRsZSBvZiBvbmx5IHRoZSBsb2Rhc2ggeW91IHVzZSB3aWxsIGJlIGJ1aWx0IGJ5IGJhYmVsLlxuXG5pbXBvcnQgcmVzaXplIGZyb20gJy4vcmVzaXplJztcbmltcG9ydCBwbHVnaW5zIGZyb20gJy4vcGx1Z2lucyc7XG5pbXBvcnQgdmlld3BvcnREaW1zIGZyb20gJy4vdmlld3BvcnQtZGltcyc7XG5cbmltcG9ydCBjb21tb24gZnJvbSAnY29tbW9uJztcblxuLy8gQEVYQU1QTEVfUkVBQ1RfQVBQXG5cbi8vIGNvbnN0IGVsID0ge1xuLy8gXHRleGFtcGxlQXBwUm9vdDogdG9vbHMuZ2V0Tm9kZXMoICdleGFtcGxlLWFwcCcgKVsgMCBdLFxuLy8gfTtcblxuLyoqXG4gKiBAZnVuY3Rpb24gYmluZEV2ZW50c1xuICogQGRlc2NyaXB0aW9uIEJpbmQgZ2xvYmFsIGV2ZW50IGxpc3RlbmVycyBoZXJlLFxuICovXG5cbmNvbnN0IGJpbmRFdmVudHMgPSAoKSA9PiB7XG5cdG9uKCB3aW5kb3csICdyZXNpemUnLCBfLmRlYm91bmNlKCByZXNpemUsIDIwMCwgZmFsc2UgKSApO1xufTtcblxuLyoqXG4gKiBAZnVuY3Rpb24gaW5pdFxuICogQGRlc2NyaXB0aW9uIFRoZSBjb3JlIGRpc3BhdGNoZXIgZm9yIGluaXQgYWNyb3NzIHRoZSBjb2RlYmFzZS5cbiAqL1xuXG5jb25zdCBpbml0ID0gKCkgPT4ge1xuXHQvLyBhcHBseSBicm93c2VyIGNsYXNzZXNcblxuXHRhcHBseUJyb3dzZXJDbGFzc2VzKCk7XG5cblx0Ly8gaW5pdCBleHRlcm5hbCBwbHVnaW5zXG5cblx0cGx1Z2lucygpO1xuXG5cdC8vIHNldCBpbml0aWFsIHN0YXRlc1xuXG5cdHZpZXdwb3J0RGltcygpO1xuXG5cdC8vIGluaXRpYWxpemUgZ2xvYmFsIGV2ZW50c1xuXG5cdGJpbmRFdmVudHMoKTtcblxuXHQvLyBpbml0aWFsaXplIG1vZHVsZXNcblxuXHRjb21tb24oKTtcblxuXHQvLyBARVhBTVBMRV9SRUFDVF9BUFAgKE1ha2Ugc3VyZSB0byBpbmNsdWRlIHRoZSB3cmFwcGluZyBpZiBibG9jayBmb3IgQUxMIHJlYWN0IGFwcHNcblxuLy8vLy8vLy8vLy8vLy8vLy8vLy9cbi8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy9cbi8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy8vLy9cbi8vLy8vXG4vLy8vLy8vLy8vXG5cdC8vIH1cblxuXHRjb25zb2xlLmluZm8oXG5cdFx0J0dyYXZpdHlGbG93IFRoZW1lOiBJbml0aWFsaXplZCBhbGwgamF2YXNjcmlwdCB0aGF0IHRhcmdldGVkIGRvY3VtZW50IHJlYWR5Lidcblx0KTtcbn07XG5cbi8qKlxuICogQGZ1bmN0aW9uIGRvbVJlYWR5XG4gKiBAZGVzY3JpcHRpb24gRXhwb3J0IG91ciBkb20gcmVhZHkgZW5hYmxlZCBpbml0LlxuICovXG5cbmNvbnN0IGRvbVJlYWR5ID0gKCkgPT4ge1xuXHRyZWFkeSggaW5pdCApO1xufTtcblxuZXhwb3J0IGRlZmF1bHQgZG9tUmVhZHk7XG4iLCJpbXBvcnQgJ3doYXR3Zy1mZXRjaCc7XG5pbXBvcnQgcmVhZHkgZnJvbSAnLi9jb3JlL3JlYWR5JztcblxucmVhZHkoKTtcbiJdLCJzb3VyY2VSb290IjoiIn0=\n//# sourceURL=webpack-internal:///./src/js/theme/index.js\n");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
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
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					result = fn();
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/ensure chunk */
/******/ 	(() => {
/******/ 		__webpack_require__.f = {};
/******/ 		// This file contains only the entry chunk.
/******/ 		// The chunk loading function for additional chunks
/******/ 		__webpack_require__.e = (chunkId) => {
/******/ 			return Promise.all(Object.keys(__webpack_require__.f).reduce((promises, key) => {
/******/ 				__webpack_require__.f[key](chunkId, promises);
/******/ 				return promises;
/******/ 			}, []));
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/get javascript chunk filename */
/******/ 	(() => {
/******/ 		// This function allow to reference async chunks
/******/ 		__webpack_require__.u = (chunkId) => {
/******/ 			// return url for filenames based on template
/******/ 			return "" + chunkId + "." + "ae3c4b46d0ba727a94b4" + ".js";
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/get mini-css chunk filename */
/******/ 	(() => {
/******/ 		// This function allow to reference all chunks
/******/ 		__webpack_require__.miniCssF = (chunkId) => {
/******/ 			// return url for filenames based on template
/******/ 			return undefined;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/load script */
/******/ 	(() => {
/******/ 		var inProgress = {};
/******/ 		var dataWebpackPrefix = "gravityflow:";
/******/ 		// loadScript function to load a script via script tag
/******/ 		__webpack_require__.l = (url, done, key, chunkId) => {
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
/******/ 			var onScriptComplete = (prev, event) => {
/******/ 				// avoid mem leaks in IE.
/******/ 				script.onerror = script.onload = null;
/******/ 				clearTimeout(timeout);
/******/ 				var doneFns = inProgress[url];
/******/ 				delete inProgress[url];
/******/ 				script.parentNode && script.parentNode.removeChild(script);
/******/ 				doneFns && doneFns.forEach((fn) => (fn(event)));
/******/ 				if(prev) return prev(event);
/******/ 			}
/******/ 			;
/******/ 			var timeout = setTimeout(onScriptComplete.bind(null, undefined, { type: 'timeout', target: script }), 120000);
/******/ 			script.onerror = onScriptComplete.bind(null, script.onerror);
/******/ 			script.onload = onScriptComplete.bind(null, script.onload);
/******/ 			needAttach && document.head.appendChild(script);
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/node module decorator */
/******/ 	(() => {
/******/ 		__webpack_require__.nmd = (module) => {
/******/ 			module.paths = [];
/******/ 			if (!module.children) module.children = [];
/******/ 			return module;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/publicPath */
/******/ 	(() => {
/******/ 		var scriptUrl;
/******/ 		if (__webpack_require__.g.importScripts) scriptUrl = __webpack_require__.g.location + "";
/******/ 		var document = __webpack_require__.g.document;
/******/ 		if (!scriptUrl && document) {
/******/ 			if (document.currentScript)
/******/ 				scriptUrl = document.currentScript.src
/******/ 			if (!scriptUrl) {
/******/ 				var scripts = document.getElementsByTagName("script");
/******/ 				if(scripts.length) scriptUrl = scripts[scripts.length - 1].src
/******/ 			}
/******/ 		}
/******/ 		// When supporting browsers where an automatic publicPath is not supported you must specify an output.publicPath manually via configuration
/******/ 		// or pass an empty string ("") and set the __webpack_public_path__ variable from your code to use your own logic.
/******/ 		if (!scriptUrl) throw new Error("Automatic publicPath is not supported in this browser");
/******/ 		scriptUrl = scriptUrl.replace(/#.*$/, "").replace(/\?.*$/, "").replace(/\/[^\/]+$/, "/");
/******/ 		__webpack_require__.p = scriptUrl;
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"scripts-theme": 0
/******/ 		};
/******/ 		
/******/ 		__webpack_require__.f.j = (chunkId, promises) => {
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
/******/ 							var promise = new Promise((resolve, reject) => (installedChunkData = installedChunks[chunkId] = [resolve, reject]));
/******/ 							promises.push(installedChunkData[2] = promise);
/******/ 		
/******/ 							// start chunk loading
/******/ 							var url = __webpack_require__.p + __webpack_require__.u(chunkId);
/******/ 							// create error before stack unwound to get useful stacktrace later
/******/ 							var error = new Error();
/******/ 							var loadingEnded = (event) => {
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
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			for(moduleId in moreModules) {
/******/ 				if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 					__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 				}
/******/ 			}
/******/ 			if(runtime) var result = runtime(__webpack_require__);
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkIds[i]] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkgravityflow"] = self["webpackChunkgravityflow"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["vendor-theme"], () => (__webpack_require__("./node_modules/core-js/modules/es.array.iterator.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["vendor-theme"], () => (__webpack_require__("./src/js/theme/index.js")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;