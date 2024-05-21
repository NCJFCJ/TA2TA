// modules are defined as an array
// [ module function, map of requires ]
//
// map of requires is short require name -> numeric require
//
// anything defined in a previous bundle is accessed via the
// orig method which is the require for previous bundles

(function (modules, entry, mainEntry, parcelRequireName, globalName) {
  /* eslint-disable no-undef */
  var globalObject =
    typeof globalThis !== 'undefined'
      ? globalThis
      : typeof self !== 'undefined'
      ? self
      : typeof window !== 'undefined'
      ? window
      : typeof global !== 'undefined'
      ? global
      : {};
  /* eslint-enable no-undef */

  // Save the require from previous bundle to this closure if any
  var previousRequire =
    typeof globalObject[parcelRequireName] === 'function' &&
    globalObject[parcelRequireName];

  var cache = previousRequire.cache || {};
  // Do not use `require` to prevent Webpack from trying to bundle this call
  var nodeRequire =
    typeof module !== 'undefined' &&
    typeof module.require === 'function' &&
    module.require.bind(module);

  function newRequire(name, jumped) {
    if (!cache[name]) {
      if (!modules[name]) {
        // if we cannot find the module within our internal map or
        // cache jump to the current global require ie. the last bundle
        // that was added to the page.
        var currentRequire =
          typeof globalObject[parcelRequireName] === 'function' &&
          globalObject[parcelRequireName];
        if (!jumped && currentRequire) {
          return currentRequire(name, true);
        }

        // If there are other bundles on this page the require from the
        // previous one is saved to 'previousRequire'. Repeat this as
        // many times as there are bundles until the module is found or
        // we exhaust the require chain.
        if (previousRequire) {
          return previousRequire(name, true);
        }

        // Try the node require function if it exists.
        if (nodeRequire && typeof name === 'string') {
          return nodeRequire(name);
        }

        var err = new Error("Cannot find module '" + name + "'");
        err.code = 'MODULE_NOT_FOUND';
        throw err;
      }

      localRequire.resolve = resolve;
      localRequire.cache = {};

      var module = (cache[name] = new newRequire.Module(name));

      modules[name][0].call(
        module.exports,
        localRequire,
        module,
        module.exports,
        this
      );
    }

    return cache[name].exports;

    function localRequire(x) {
      var res = localRequire.resolve(x);
      return res === false ? {} : newRequire(res);
    }

    function resolve(x) {
      var id = modules[name][1][x];
      return id != null ? id : x;
    }
  }

  function Module(moduleName) {
    this.id = moduleName;
    this.bundle = newRequire;
    this.exports = {};
  }

  newRequire.isParcelRequire = true;
  newRequire.Module = Module;
  newRequire.modules = modules;
  newRequire.cache = cache;
  newRequire.parent = previousRequire;
  newRequire.register = function (id, exports) {
    modules[id] = [
      function (require, module) {
        module.exports = exports;
      },
      {},
    ];
  };

  Object.defineProperty(newRequire, 'root', {
    get: function () {
      return globalObject[parcelRequireName];
    },
  });

  globalObject[parcelRequireName] = newRequire;

  for (var i = 0; i < entry.length; i++) {
    newRequire(entry[i]);
  }

  if (mainEntry) {
    // Expose entry point to Node, AMD or browser globals
    // Based on https://github.com/ForbesLindesay/umd/blob/master/template.js
    var mainExports = newRequire(mainEntry);

    // CommonJS
    if (typeof exports === 'object' && typeof module !== 'undefined') {
      module.exports = mainExports;

      // RequireJS
    } else if (typeof define === 'function' && define.amd) {
      define(function () {
        return mainExports;
      });

      // <script>
    } else if (globalName) {
      this[globalName] = mainExports;
    }
  }
})({"kILFo":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
var _stimulus = require("@hotwired/stimulus");
var _chartController = require("./controllers/chart_controller");
var _chartControllerDefault = parcelHelpers.interopDefault(_chartController);
window.Stimulus = (0, _stimulus.Application).start();
Stimulus.register("chart", (0, _chartControllerDefault.default));

},{"@hotwired/stimulus":"27q4D","./controllers/chart_controller":"irT0c","@parcel/transformer-js/src/esmodule-helpers.js":"jIm8e"}],"27q4D":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "Application", function() {
    return Application;
});
parcelHelpers.export(exports, "AttributeObserver", function() {
    return AttributeObserver;
});
parcelHelpers.export(exports, "Context", function() {
    return Context;
});
parcelHelpers.export(exports, "Controller", function() {
    return Controller;
});
parcelHelpers.export(exports, "ElementObserver", function() {
    return ElementObserver;
});
parcelHelpers.export(exports, "IndexedMultimap", function() {
    return IndexedMultimap;
});
parcelHelpers.export(exports, "Multimap", function() {
    return Multimap;
});
parcelHelpers.export(exports, "StringMapObserver", function() {
    return StringMapObserver;
});
parcelHelpers.export(exports, "TokenListObserver", function() {
    return TokenListObserver;
});
parcelHelpers.export(exports, "ValueListObserver", function() {
    return ValueListObserver;
});
parcelHelpers.export(exports, "add", function() {
    return add;
});
parcelHelpers.export(exports, "defaultSchema", function() {
    return defaultSchema;
});
parcelHelpers.export(exports, "del", function() {
    return del;
});
parcelHelpers.export(exports, "fetch", function() {
    return fetch;
});
parcelHelpers.export(exports, "prune", function() {
    return prune;
});
var _asyncToGeneratorJs = require("@swc/helpers/lib/_async_to_generator.js");
var _asyncToGeneratorJsDefault = parcelHelpers.interopDefault(_asyncToGeneratorJs);
var _classCallCheckJs = require("@swc/helpers/lib/_class_call_check.js");
var _classCallCheckJsDefault = parcelHelpers.interopDefault(_classCallCheckJs);
var _createClassJs = require("@swc/helpers/lib/_create_class.js");
var _createClassJsDefault = parcelHelpers.interopDefault(_createClassJs);
var _definePropertyJs = require("@swc/helpers/lib/_define_property.js");
var _definePropertyJsDefault = parcelHelpers.interopDefault(_definePropertyJs);
var _getJs = require("@swc/helpers/lib/_get.js");
var _getJsDefault = parcelHelpers.interopDefault(_getJs);
var _getPrototypeOfJs = require("@swc/helpers/lib/_get_prototype_of.js");
var _getPrototypeOfJsDefault = parcelHelpers.interopDefault(_getPrototypeOfJs);
var _inheritsJs = require("@swc/helpers/lib/_inherits.js");
var _inheritsJsDefault = parcelHelpers.interopDefault(_inheritsJs);
var _slicedToArrayJs = require("@swc/helpers/lib/_sliced_to_array.js");
var _slicedToArrayJsDefault = parcelHelpers.interopDefault(_slicedToArrayJs);
var _toConsumableArrayJs = require("@swc/helpers/lib/_to_consumable_array.js");
var _toConsumableArrayJsDefault = parcelHelpers.interopDefault(_toConsumableArrayJs);
var _typeOfJs = require("@swc/helpers/lib/_type_of.js");
var _typeOfJsDefault = parcelHelpers.interopDefault(_typeOfJs);
var _createSuperJs = require("@swc/helpers/lib/_create_super.js");
var _createSuperJsDefault = parcelHelpers.interopDefault(_createSuperJs);
var _regeneratorRuntime = require("regenerator-runtime");
var _regeneratorRuntimeDefault = parcelHelpers.interopDefault(_regeneratorRuntime);
/*
Stimulus 3.0.1
Copyright © 2021 Basecamp, LLC
 */ var EventListener = /*#__PURE__*/ function() {
    "use strict";
    function EventListener(eventTarget, eventName, eventOptions) {
        (0, _classCallCheckJsDefault.default)(this, EventListener);
        this.eventTarget = eventTarget;
        this.eventName = eventName;
        this.eventOptions = eventOptions;
        this.unorderedBindings = new Set();
    }
    (0, _createClassJsDefault.default)(EventListener, [
        {
            key: "connect",
            value: function connect() {
                this.eventTarget.addEventListener(this.eventName, this, this.eventOptions);
            }
        },
        {
            key: "disconnect",
            value: function disconnect() {
                this.eventTarget.removeEventListener(this.eventName, this, this.eventOptions);
            }
        },
        {
            key: "bindingConnected",
            value: function bindingConnected(binding) {
                this.unorderedBindings.add(binding);
            }
        },
        {
            key: "bindingDisconnected",
            value: function bindingDisconnected(binding) {
                this.unorderedBindings.delete(binding);
            }
        },
        {
            key: "handleEvent",
            value: function handleEvent(event) {
                var extendedEvent = extendEvent(event);
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                try {
                    for(var _iterator = this.bindings[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var binding = _step.value;
                        if (extendedEvent.immediatePropagationStopped) break;
                        else binding.handleEvent(extendedEvent);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        },
        {
            key: "bindings",
            get: function get() {
                return Array.from(this.unorderedBindings).sort(function(left, right) {
                    var leftIndex = left.index, rightIndex = right.index;
                    return leftIndex < rightIndex ? -1 : leftIndex > rightIndex ? 1 : 0;
                });
            }
        }
    ]);
    return EventListener;
}();
function extendEvent(event) {
    if ("immediatePropagationStopped" in event) return event;
    else {
        var stopImmediatePropagation = event.stopImmediatePropagation;
        return Object.assign(event, {
            immediatePropagationStopped: false,
            stopImmediatePropagation: function() {
                this.immediatePropagationStopped = true;
                stopImmediatePropagation.call(this);
            }
        });
    }
}
var Dispatcher = /*#__PURE__*/ function() {
    "use strict";
    function Dispatcher(application) {
        (0, _classCallCheckJsDefault.default)(this, Dispatcher);
        this.application = application;
        this.eventListenerMaps = new Map;
        this.started = false;
    }
    (0, _createClassJsDefault.default)(Dispatcher, [
        {
            key: "start",
            value: function start() {
                if (!this.started) {
                    this.started = true;
                    this.eventListeners.forEach(function(eventListener) {
                        return eventListener.connect();
                    });
                }
            }
        },
        {
            key: "stop",
            value: function stop() {
                if (this.started) {
                    this.started = false;
                    this.eventListeners.forEach(function(eventListener) {
                        return eventListener.disconnect();
                    });
                }
            }
        },
        {
            key: "eventListeners",
            get: function get() {
                return Array.from(this.eventListenerMaps.values()).reduce(function(listeners, map) {
                    return listeners.concat(Array.from(map.values()));
                }, []);
            }
        },
        {
            key: "bindingConnected",
            value: function bindingConnected(binding) {
                this.fetchEventListenerForBinding(binding).bindingConnected(binding);
            }
        },
        {
            key: "bindingDisconnected",
            value: function bindingDisconnected(binding) {
                this.fetchEventListenerForBinding(binding).bindingDisconnected(binding);
            }
        },
        {
            key: "handleError",
            value: function handleError(error1, message) {
                var detail = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {};
                this.application.handleError(error1, "Error ".concat(message), detail);
            }
        },
        {
            key: "fetchEventListenerForBinding",
            value: function fetchEventListenerForBinding(binding) {
                var eventTarget = binding.eventTarget, eventName = binding.eventName, eventOptions = binding.eventOptions;
                return this.fetchEventListener(eventTarget, eventName, eventOptions);
            }
        },
        {
            key: "fetchEventListener",
            value: function fetchEventListener(eventTarget, eventName, eventOptions) {
                var eventListenerMap = this.fetchEventListenerMapForEventTarget(eventTarget);
                var cacheKey = this.cacheKey(eventName, eventOptions);
                var eventListener = eventListenerMap.get(cacheKey);
                if (!eventListener) {
                    eventListener = this.createEventListener(eventTarget, eventName, eventOptions);
                    eventListenerMap.set(cacheKey, eventListener);
                }
                return eventListener;
            }
        },
        {
            key: "createEventListener",
            value: function createEventListener(eventTarget, eventName, eventOptions) {
                var eventListener = new EventListener(eventTarget, eventName, eventOptions);
                if (this.started) eventListener.connect();
                return eventListener;
            }
        },
        {
            key: "fetchEventListenerMapForEventTarget",
            value: function fetchEventListenerMapForEventTarget(eventTarget) {
                var eventListenerMap = this.eventListenerMaps.get(eventTarget);
                if (!eventListenerMap) {
                    eventListenerMap = new Map;
                    this.eventListenerMaps.set(eventTarget, eventListenerMap);
                }
                return eventListenerMap;
            }
        },
        {
            key: "cacheKey",
            value: function cacheKey(eventName, eventOptions) {
                var parts = [
                    eventName
                ];
                Object.keys(eventOptions).sort().forEach(function(key) {
                    parts.push("".concat(eventOptions[key] ? "" : "!").concat(key));
                });
                return parts.join(":");
            }
        }
    ]);
    return Dispatcher;
}();
var descriptorPattern = /^((.+?)(@(window|document))?->)?(.+?)(#([^:]+?))(:(.+))?$/;
function parseActionDescriptorString(descriptorString) {
    var source = descriptorString.trim();
    var matches = source.match(descriptorPattern) || [];
    return {
        eventTarget: parseEventTarget(matches[4]),
        eventName: matches[2],
        eventOptions: matches[9] ? parseEventOptions(matches[9]) : {},
        identifier: matches[5],
        methodName: matches[7]
    };
}
function parseEventTarget(eventTargetName) {
    if (eventTargetName == "window") return window;
    else if (eventTargetName == "document") return document;
}
function parseEventOptions(eventOptions) {
    return eventOptions.split(":").reduce(function(options, token) {
        return Object.assign(options, (0, _definePropertyJsDefault.default)({}, token.replace(/^!/, ""), !/^!/.test(token)));
    }, {});
}
function stringifyEventTarget(eventTarget) {
    if (eventTarget == window) return "window";
    else if (eventTarget == document) return "document";
}
function camelize(value) {
    return value.replace(/(?:[_-])([a-z0-9])/g, function(_, char) {
        return char.toUpperCase();
    });
}
function capitalize(value) {
    return value.charAt(0).toUpperCase() + value.slice(1);
}
function dasherize(value) {
    return value.replace(/([A-Z])/g, function(_, char) {
        return "-".concat(char.toLowerCase());
    });
}
function tokenize(value) {
    return value.match(/[^\s]+/g) || [];
}
var Action = /*#__PURE__*/ function() {
    "use strict";
    function Action(element, index, descriptor) {
        (0, _classCallCheckJsDefault.default)(this, Action);
        this.element = element;
        this.index = index;
        this.eventTarget = descriptor.eventTarget || element;
        this.eventName = descriptor.eventName || getDefaultEventNameForElement(element) || error("missing event name");
        this.eventOptions = descriptor.eventOptions || {};
        this.identifier = descriptor.identifier || error("missing identifier");
        this.methodName = descriptor.methodName || error("missing method name");
    }
    (0, _createClassJsDefault.default)(Action, [
        {
            key: "toString",
            value: function toString() {
                var eventNameSuffix = this.eventTargetName ? "@".concat(this.eventTargetName) : "";
                return "".concat(this.eventName).concat(eventNameSuffix, "->").concat(this.identifier, "#").concat(this.methodName);
            }
        },
        {
            key: "params",
            get: function get() {
                if (this.eventTarget instanceof Element) return this.getParamsFromEventTargetAttributes(this.eventTarget);
                else return {};
            }
        },
        {
            key: "getParamsFromEventTargetAttributes",
            value: function getParamsFromEventTargetAttributes(eventTarget) {
                var params = {};
                var pattern = new RegExp("^data-".concat(this.identifier, "-(.+)-param$"));
                var attributes = Array.from(eventTarget.attributes);
                attributes.forEach(function(param) {
                    var name = param.name, value = param.value;
                    var match = name.match(pattern);
                    var key = match && match[1];
                    if (key) Object.assign(params, (0, _definePropertyJsDefault.default)({}, camelize(key), typecast(value)));
                });
                return params;
            }
        },
        {
            key: "eventTargetName",
            get: function get() {
                return stringifyEventTarget(this.eventTarget);
            }
        }
    ], [
        {
            key: "forToken",
            value: function forToken(token) {
                return new this(token.element, token.index, parseActionDescriptorString(token.content));
            }
        }
    ]);
    return Action;
}();
var defaultEventNames = {
    "a": function(e) {
        return "click";
    },
    "button": function(e) {
        return "click";
    },
    "form": function(e) {
        return "submit";
    },
    "details": function(e) {
        return "toggle";
    },
    "input": function(e) {
        return e.getAttribute("type") == "submit" ? "click" : "input";
    },
    "select": function(e) {
        return "change";
    },
    "textarea": function(e) {
        return "input";
    }
};
function getDefaultEventNameForElement(element) {
    var tagName = element.tagName.toLowerCase();
    if (tagName in defaultEventNames) return defaultEventNames[tagName](element);
}
function error(message) {
    throw new Error(message);
}
function typecast(value) {
    try {
        return JSON.parse(value);
    } catch (o_O) {
        return value;
    }
}
var Binding = /*#__PURE__*/ function() {
    "use strict";
    function Binding(context, action) {
        (0, _classCallCheckJsDefault.default)(this, Binding);
        this.context = context;
        this.action = action;
    }
    (0, _createClassJsDefault.default)(Binding, [
        {
            key: "index",
            get: function get() {
                return this.action.index;
            }
        },
        {
            key: "eventTarget",
            get: function get() {
                return this.action.eventTarget;
            }
        },
        {
            key: "eventOptions",
            get: function get() {
                return this.action.eventOptions;
            }
        },
        {
            key: "identifier",
            get: function get() {
                return this.context.identifier;
            }
        },
        {
            key: "handleEvent",
            value: function handleEvent(event) {
                if (this.willBeInvokedByEvent(event)) this.invokeWithEvent(event);
            }
        },
        {
            key: "eventName",
            get: function get() {
                return this.action.eventName;
            }
        },
        {
            key: "method",
            get: function get() {
                var method = this.controller[this.methodName];
                if (typeof method == "function") return method;
                throw new Error('Action "'.concat(this.action, '" references undefined method "').concat(this.methodName, '"'));
            }
        },
        {
            key: "invokeWithEvent",
            value: function invokeWithEvent(event) {
                var target = event.target, currentTarget = event.currentTarget;
                try {
                    var params = this.action.params;
                    var actionEvent = Object.assign(event, {
                        params: params
                    });
                    this.method.call(this.controller, actionEvent);
                    this.context.logDebugActivity(this.methodName, {
                        event: event,
                        target: target,
                        currentTarget: currentTarget,
                        action: this.methodName
                    });
                } catch (error2) {
                    var ref = this, identifier = ref.identifier, controller = ref.controller, element = ref.element, index = ref.index;
                    var detail = {
                        identifier: identifier,
                        controller: controller,
                        element: element,
                        index: index,
                        event: event
                    };
                    this.context.handleError(error2, 'invoking action "'.concat(this.action, '"'), detail);
                }
            }
        },
        {
            key: "willBeInvokedByEvent",
            value: function willBeInvokedByEvent(event) {
                var eventTarget = event.target;
                if (this.element === eventTarget) return true;
                else if (eventTarget instanceof Element && this.element.contains(eventTarget)) return this.scope.containsElement(eventTarget);
                else return this.scope.containsElement(this.action.element);
            }
        },
        {
            key: "controller",
            get: function get() {
                return this.context.controller;
            }
        },
        {
            key: "methodName",
            get: function get() {
                return this.action.methodName;
            }
        },
        {
            key: "element",
            get: function get() {
                return this.scope.element;
            }
        },
        {
            key: "scope",
            get: function get() {
                return this.context.scope;
            }
        }
    ]);
    return Binding;
}();
var ElementObserver = /*#__PURE__*/ function() {
    "use strict";
    function ElementObserver(element, delegate) {
        var _this = this;
        (0, _classCallCheckJsDefault.default)(this, ElementObserver);
        this.mutationObserverInit = {
            attributes: true,
            childList: true,
            subtree: true
        };
        this.element = element;
        this.started = false;
        this.delegate = delegate;
        this.elements = new Set;
        this.mutationObserver = new MutationObserver(function(mutations) {
            return _this.processMutations(mutations);
        });
    }
    (0, _createClassJsDefault.default)(ElementObserver, [
        {
            key: "start",
            value: function start() {
                if (!this.started) {
                    this.started = true;
                    this.mutationObserver.observe(this.element, this.mutationObserverInit);
                    this.refresh();
                }
            }
        },
        {
            key: "pause",
            value: function pause(callback) {
                if (this.started) {
                    this.mutationObserver.disconnect();
                    this.started = false;
                }
                callback();
                if (!this.started) {
                    this.mutationObserver.observe(this.element, this.mutationObserverInit);
                    this.started = true;
                }
            }
        },
        {
            key: "stop",
            value: function stop() {
                if (this.started) {
                    this.mutationObserver.takeRecords();
                    this.mutationObserver.disconnect();
                    this.started = false;
                }
            }
        },
        {
            key: "refresh",
            value: function refresh() {
                if (this.started) {
                    var matches = new Set(this.matchElementsInTree());
                    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                    try {
                        for(var _iterator = Array.from(this.elements)[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                            var element = _step.value;
                            if (!matches.has(element)) this.removeElement(element);
                        }
                    } catch (err) {
                        _didIteratorError = true;
                        _iteratorError = err;
                    } finally{
                        try {
                            if (!_iteratorNormalCompletion && _iterator.return != null) {
                                _iterator.return();
                            }
                        } finally{
                            if (_didIteratorError) {
                                throw _iteratorError;
                            }
                        }
                    }
                    var _iteratorNormalCompletion1 = true, _didIteratorError1 = false, _iteratorError1 = undefined;
                    try {
                        for(var _iterator1 = Array.from(matches)[Symbol.iterator](), _step1; !(_iteratorNormalCompletion1 = (_step1 = _iterator1.next()).done); _iteratorNormalCompletion1 = true){
                            var element1 = _step1.value;
                            this.addElement(element1);
                        }
                    } catch (err) {
                        _didIteratorError1 = true;
                        _iteratorError1 = err;
                    } finally{
                        try {
                            if (!_iteratorNormalCompletion1 && _iterator1.return != null) {
                                _iterator1.return();
                            }
                        } finally{
                            if (_didIteratorError1) {
                                throw _iteratorError1;
                            }
                        }
                    }
                }
            }
        },
        {
            key: "processMutations",
            value: function processMutations(mutations) {
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                if (this.started) try {
                    for(var _iterator = mutations[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var mutation = _step.value;
                        this.processMutation(mutation);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        },
        {
            key: "processMutation",
            value: function processMutation(mutation) {
                if (mutation.type == "attributes") this.processAttributeChange(mutation.target, mutation.attributeName);
                else if (mutation.type == "childList") {
                    this.processRemovedNodes(mutation.removedNodes);
                    this.processAddedNodes(mutation.addedNodes);
                }
            }
        },
        {
            key: "processAttributeChange",
            value: function processAttributeChange(node, attributeName) {
                var element = node;
                if (this.elements.has(element)) {
                    if (this.delegate.elementAttributeChanged && this.matchElement(element)) this.delegate.elementAttributeChanged(element, attributeName);
                    else this.removeElement(element);
                } else if (this.matchElement(element)) this.addElement(element);
            }
        },
        {
            key: "processRemovedNodes",
            value: function processRemovedNodes(nodes) {
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                try {
                    for(var _iterator = Array.from(nodes)[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var node = _step.value;
                        var element = this.elementFromNode(node);
                        if (element) this.processTree(element, this.removeElement);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        },
        {
            key: "processAddedNodes",
            value: function processAddedNodes(nodes) {
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                try {
                    for(var _iterator = Array.from(nodes)[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var node = _step.value;
                        var element = this.elementFromNode(node);
                        if (element && this.elementIsActive(element)) this.processTree(element, this.addElement);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        },
        {
            key: "matchElement",
            value: function matchElement(element) {
                return this.delegate.matchElement(element);
            }
        },
        {
            key: "matchElementsInTree",
            value: function matchElementsInTree() {
                var tree = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : this.element;
                return this.delegate.matchElementsInTree(tree);
            }
        },
        {
            key: "processTree",
            value: function processTree(tree, processor) {
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                try {
                    for(var _iterator = this.matchElementsInTree(tree)[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var element = _step.value;
                        processor.call(this, element);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        },
        {
            key: "elementFromNode",
            value: function elementFromNode(node) {
                if (node.nodeType == Node.ELEMENT_NODE) return node;
            }
        },
        {
            key: "elementIsActive",
            value: function elementIsActive(element) {
                if (element.isConnected != this.element.isConnected) return false;
                else return this.element.contains(element);
            }
        },
        {
            key: "addElement",
            value: function addElement(element) {
                if (!this.elements.has(element)) {
                    if (this.elementIsActive(element)) {
                        this.elements.add(element);
                        if (this.delegate.elementMatched) this.delegate.elementMatched(element);
                    }
                }
            }
        },
        {
            key: "removeElement",
            value: function removeElement(element) {
                if (this.elements.has(element)) {
                    this.elements.delete(element);
                    if (this.delegate.elementUnmatched) this.delegate.elementUnmatched(element);
                }
            }
        }
    ]);
    return ElementObserver;
}();
var AttributeObserver = /*#__PURE__*/ function() {
    "use strict";
    function AttributeObserver(element, attributeName, delegate) {
        (0, _classCallCheckJsDefault.default)(this, AttributeObserver);
        this.attributeName = attributeName;
        this.delegate = delegate;
        this.elementObserver = new ElementObserver(element, this);
    }
    (0, _createClassJsDefault.default)(AttributeObserver, [
        {
            key: "element",
            get: function get() {
                return this.elementObserver.element;
            }
        },
        {
            key: "selector",
            get: function get() {
                return "[".concat(this.attributeName, "]");
            }
        },
        {
            key: "start",
            value: function start() {
                this.elementObserver.start();
            }
        },
        {
            key: "pause",
            value: function pause(callback) {
                this.elementObserver.pause(callback);
            }
        },
        {
            key: "stop",
            value: function stop() {
                this.elementObserver.stop();
            }
        },
        {
            key: "refresh",
            value: function refresh() {
                this.elementObserver.refresh();
            }
        },
        {
            key: "started",
            get: function get() {
                return this.elementObserver.started;
            }
        },
        {
            key: "matchElement",
            value: function matchElement(element) {
                return element.hasAttribute(this.attributeName);
            }
        },
        {
            key: "matchElementsInTree",
            value: function matchElementsInTree(tree) {
                var match = this.matchElement(tree) ? [
                    tree
                ] : [];
                var matches = Array.from(tree.querySelectorAll(this.selector));
                return match.concat(matches);
            }
        },
        {
            key: "elementMatched",
            value: function elementMatched(element) {
                if (this.delegate.elementMatchedAttribute) this.delegate.elementMatchedAttribute(element, this.attributeName);
            }
        },
        {
            key: "elementUnmatched",
            value: function elementUnmatched(element) {
                if (this.delegate.elementUnmatchedAttribute) this.delegate.elementUnmatchedAttribute(element, this.attributeName);
            }
        },
        {
            key: "elementAttributeChanged",
            value: function elementAttributeChanged(element, attributeName) {
                if (this.delegate.elementAttributeValueChanged && this.attributeName == attributeName) this.delegate.elementAttributeValueChanged(element, attributeName);
            }
        }
    ]);
    return AttributeObserver;
}();
var StringMapObserver = /*#__PURE__*/ function() {
    "use strict";
    function StringMapObserver(element, delegate) {
        var _this = this;
        (0, _classCallCheckJsDefault.default)(this, StringMapObserver);
        this.element = element;
        this.delegate = delegate;
        this.started = false;
        this.stringMap = new Map;
        this.mutationObserver = new MutationObserver(function(mutations) {
            return _this.processMutations(mutations);
        });
    }
    (0, _createClassJsDefault.default)(StringMapObserver, [
        {
            key: "start",
            value: function start() {
                if (!this.started) {
                    this.started = true;
                    this.mutationObserver.observe(this.element, {
                        attributes: true,
                        attributeOldValue: true
                    });
                    this.refresh();
                }
            }
        },
        {
            key: "stop",
            value: function stop() {
                if (this.started) {
                    this.mutationObserver.takeRecords();
                    this.mutationObserver.disconnect();
                    this.started = false;
                }
            }
        },
        {
            key: "refresh",
            value: function refresh() {
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                if (this.started) try {
                    for(var _iterator = this.knownAttributeNames[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var attributeName = _step.value;
                        this.refreshAttribute(attributeName, null);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        },
        {
            key: "processMutations",
            value: function processMutations(mutations) {
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                if (this.started) try {
                    for(var _iterator = mutations[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var mutation = _step.value;
                        this.processMutation(mutation);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        },
        {
            key: "processMutation",
            value: function processMutation(mutation) {
                var attributeName = mutation.attributeName;
                if (attributeName) this.refreshAttribute(attributeName, mutation.oldValue);
            }
        },
        {
            key: "refreshAttribute",
            value: function refreshAttribute(attributeName, oldValue) {
                var key = this.delegate.getStringMapKeyForAttribute(attributeName);
                if (key != null) {
                    if (!this.stringMap.has(attributeName)) this.stringMapKeyAdded(key, attributeName);
                    var value = this.element.getAttribute(attributeName);
                    if (this.stringMap.get(attributeName) != value) this.stringMapValueChanged(value, key, oldValue);
                    if (value == null) {
                        var _$oldValue = this.stringMap.get(attributeName);
                        this.stringMap.delete(attributeName);
                        if (_$oldValue) this.stringMapKeyRemoved(key, attributeName, _$oldValue);
                    } else this.stringMap.set(attributeName, value);
                }
            }
        },
        {
            key: "stringMapKeyAdded",
            value: function stringMapKeyAdded(key, attributeName) {
                if (this.delegate.stringMapKeyAdded) this.delegate.stringMapKeyAdded(key, attributeName);
            }
        },
        {
            key: "stringMapValueChanged",
            value: function stringMapValueChanged(value, key, oldValue) {
                if (this.delegate.stringMapValueChanged) this.delegate.stringMapValueChanged(value, key, oldValue);
            }
        },
        {
            key: "stringMapKeyRemoved",
            value: function stringMapKeyRemoved(key, attributeName, oldValue) {
                if (this.delegate.stringMapKeyRemoved) this.delegate.stringMapKeyRemoved(key, attributeName, oldValue);
            }
        },
        {
            key: "knownAttributeNames",
            get: function get() {
                return Array.from(new Set(this.currentAttributeNames.concat(this.recordedAttributeNames)));
            }
        },
        {
            key: "currentAttributeNames",
            get: function get() {
                return Array.from(this.element.attributes).map(function(attribute) {
                    return attribute.name;
                });
            }
        },
        {
            key: "recordedAttributeNames",
            get: function get() {
                return Array.from(this.stringMap.keys());
            }
        }
    ]);
    return StringMapObserver;
}();
function add(map, key, value) {
    fetch(map, key).add(value);
}
function del(map, key, value) {
    fetch(map, key).delete(value);
    prune(map, key);
}
function fetch(map, key) {
    var values = map.get(key);
    if (!values) {
        values = new Set();
        map.set(key, values);
    }
    return values;
}
function prune(map, key) {
    var values = map.get(key);
    if (values != null && values.size == 0) map.delete(key);
}
var Multimap = /*#__PURE__*/ function() {
    "use strict";
    function Multimap() {
        (0, _classCallCheckJsDefault.default)(this, Multimap);
        this.valuesByKey = new Map();
    }
    (0, _createClassJsDefault.default)(Multimap, [
        {
            key: "keys",
            get: function get() {
                return Array.from(this.valuesByKey.keys());
            }
        },
        {
            key: "values",
            get: function get() {
                var sets = Array.from(this.valuesByKey.values());
                return sets.reduce(function(values, set) {
                    return values.concat(Array.from(set));
                }, []);
            }
        },
        {
            key: "size",
            get: function get() {
                var sets = Array.from(this.valuesByKey.values());
                return sets.reduce(function(size, set) {
                    return size + set.size;
                }, 0);
            }
        },
        {
            key: "add",
            value: function add1(key, value) {
                add(this.valuesByKey, key, value);
            }
        },
        {
            key: "delete",
            value: function _delete(key, value) {
                del(this.valuesByKey, key, value);
            }
        },
        {
            key: "has",
            value: function has(key, value) {
                var values = this.valuesByKey.get(key);
                return values != null && values.has(value);
            }
        },
        {
            key: "hasKey",
            value: function hasKey(key) {
                return this.valuesByKey.has(key);
            }
        },
        {
            key: "hasValue",
            value: function hasValue(value) {
                var sets = Array.from(this.valuesByKey.values());
                return sets.some(function(set) {
                    return set.has(value);
                });
            }
        },
        {
            key: "getValuesForKey",
            value: function getValuesForKey(key) {
                var values = this.valuesByKey.get(key);
                return values ? Array.from(values) : [];
            }
        },
        {
            key: "getKeysForValue",
            value: function getKeysForValue(value) {
                return Array.from(this.valuesByKey).filter(function(param) {
                    var _param = (0, _slicedToArrayJsDefault.default)(param, 2), key = _param[0], values = _param[1];
                    return values.has(value);
                }).map(function(param) {
                    var _param = (0, _slicedToArrayJsDefault.default)(param, 2), key = _param[0], values = _param[1];
                    return key;
                });
            }
        }
    ]);
    return Multimap;
}();
var IndexedMultimap = /*#__PURE__*/ function(Multimap) {
    "use strict";
    (0, _inheritsJsDefault.default)(IndexedMultimap, Multimap);
    var _super = (0, _createSuperJsDefault.default)(IndexedMultimap);
    function IndexedMultimap() {
        (0, _classCallCheckJsDefault.default)(this, IndexedMultimap);
        var _this;
        _this = _super.call(this);
        _this.keysByValue = new Map;
        return _this;
    }
    (0, _createClassJsDefault.default)(IndexedMultimap, [
        {
            key: "values",
            get: function get() {
                return Array.from(this.keysByValue.keys());
            }
        },
        {
            key: "add",
            value: function add1(key, value) {
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(IndexedMultimap.prototype), "add", this).call(this, key, value);
                add(this.keysByValue, value, key);
            }
        },
        {
            key: "delete",
            value: function _delete(key, value) {
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(IndexedMultimap.prototype), "delete", this).call(this, key, value);
                del(this.keysByValue, value, key);
            }
        },
        {
            key: "hasValue",
            value: function hasValue(value) {
                return this.keysByValue.has(value);
            }
        },
        {
            key: "getKeysForValue",
            value: function getKeysForValue(value) {
                var set = this.keysByValue.get(value);
                return set ? Array.from(set) : [];
            }
        }
    ]);
    return IndexedMultimap;
}(Multimap);
var TokenListObserver = /*#__PURE__*/ function() {
    "use strict";
    function TokenListObserver(element, attributeName, delegate) {
        (0, _classCallCheckJsDefault.default)(this, TokenListObserver);
        this.attributeObserver = new AttributeObserver(element, attributeName, this);
        this.delegate = delegate;
        this.tokensByElement = new Multimap;
    }
    (0, _createClassJsDefault.default)(TokenListObserver, [
        {
            key: "started",
            get: function get() {
                return this.attributeObserver.started;
            }
        },
        {
            key: "start",
            value: function start() {
                this.attributeObserver.start();
            }
        },
        {
            key: "pause",
            value: function pause(callback) {
                this.attributeObserver.pause(callback);
            }
        },
        {
            key: "stop",
            value: function stop() {
                this.attributeObserver.stop();
            }
        },
        {
            key: "refresh",
            value: function refresh() {
                this.attributeObserver.refresh();
            }
        },
        {
            key: "element",
            get: function get() {
                return this.attributeObserver.element;
            }
        },
        {
            key: "attributeName",
            get: function get() {
                return this.attributeObserver.attributeName;
            }
        },
        {
            key: "elementMatchedAttribute",
            value: function elementMatchedAttribute(element) {
                this.tokensMatched(this.readTokensForElement(element));
            }
        },
        {
            key: "elementAttributeValueChanged",
            value: function elementAttributeValueChanged(element) {
                var ref = (0, _slicedToArrayJsDefault.default)(this.refreshTokensForElement(element), 2), unmatchedTokens = ref[0], matchedTokens = ref[1];
                this.tokensUnmatched(unmatchedTokens);
                this.tokensMatched(matchedTokens);
            }
        },
        {
            key: "elementUnmatchedAttribute",
            value: function elementUnmatchedAttribute(element) {
                this.tokensUnmatched(this.tokensByElement.getValuesForKey(element));
            }
        },
        {
            key: "tokensMatched",
            value: function tokensMatched(tokens) {
                var _this = this;
                tokens.forEach(function(token) {
                    return _this.tokenMatched(token);
                });
            }
        },
        {
            key: "tokensUnmatched",
            value: function tokensUnmatched(tokens) {
                var _this = this;
                tokens.forEach(function(token) {
                    return _this.tokenUnmatched(token);
                });
            }
        },
        {
            key: "tokenMatched",
            value: function tokenMatched(token) {
                this.delegate.tokenMatched(token);
                this.tokensByElement.add(token.element, token);
            }
        },
        {
            key: "tokenUnmatched",
            value: function tokenUnmatched(token) {
                this.delegate.tokenUnmatched(token);
                this.tokensByElement.delete(token.element, token);
            }
        },
        {
            key: "refreshTokensForElement",
            value: function refreshTokensForElement(element) {
                var previousTokens = this.tokensByElement.getValuesForKey(element);
                var currentTokens = this.readTokensForElement(element);
                var firstDifferingIndex = zip(previousTokens, currentTokens).findIndex(function(param) {
                    var _param = (0, _slicedToArrayJsDefault.default)(param, 2), previousToken = _param[0], currentToken = _param[1];
                    return !tokensAreEqual(previousToken, currentToken);
                });
                if (firstDifferingIndex == -1) return [
                    [],
                    []
                ];
                else return [
                    previousTokens.slice(firstDifferingIndex),
                    currentTokens.slice(firstDifferingIndex)
                ];
            }
        },
        {
            key: "readTokensForElement",
            value: function readTokensForElement(element) {
                var attributeName = this.attributeName;
                var tokenString = element.getAttribute(attributeName) || "";
                return parseTokenString(tokenString, element, attributeName);
            }
        }
    ]);
    return TokenListObserver;
}();
function parseTokenString(tokenString, element, attributeName) {
    return tokenString.trim().split(/\s+/).filter(function(content) {
        return content.length;
    }).map(function(content, index) {
        return {
            element: element,
            attributeName: attributeName,
            content: content,
            index: index
        };
    });
}
function zip(left, right) {
    var length = Math.max(left.length, right.length);
    return Array.from({
        length: length
    }, function(_, index) {
        return [
            left[index],
            right[index]
        ];
    });
}
function tokensAreEqual(left, right) {
    return left && right && left.index == right.index && left.content == right.content;
}
var ValueListObserver = /*#__PURE__*/ function() {
    "use strict";
    function ValueListObserver(element, attributeName, delegate) {
        (0, _classCallCheckJsDefault.default)(this, ValueListObserver);
        this.tokenListObserver = new TokenListObserver(element, attributeName, this);
        this.delegate = delegate;
        this.parseResultsByToken = new WeakMap;
        this.valuesByTokenByElement = new WeakMap;
    }
    (0, _createClassJsDefault.default)(ValueListObserver, [
        {
            key: "started",
            get: function get() {
                return this.tokenListObserver.started;
            }
        },
        {
            key: "start",
            value: function start() {
                this.tokenListObserver.start();
            }
        },
        {
            key: "stop",
            value: function stop() {
                this.tokenListObserver.stop();
            }
        },
        {
            key: "refresh",
            value: function refresh() {
                this.tokenListObserver.refresh();
            }
        },
        {
            key: "element",
            get: function get() {
                return this.tokenListObserver.element;
            }
        },
        {
            key: "attributeName",
            get: function get() {
                return this.tokenListObserver.attributeName;
            }
        },
        {
            key: "tokenMatched",
            value: function tokenMatched(token) {
                var element = token.element;
                var value = this.fetchParseResultForToken(token).value;
                if (value) {
                    this.fetchValuesByTokenForElement(element).set(token, value);
                    this.delegate.elementMatchedValue(element, value);
                }
            }
        },
        {
            key: "tokenUnmatched",
            value: function tokenUnmatched(token) {
                var element = token.element;
                var value = this.fetchParseResultForToken(token).value;
                if (value) {
                    this.fetchValuesByTokenForElement(element).delete(token);
                    this.delegate.elementUnmatchedValue(element, value);
                }
            }
        },
        {
            key: "fetchParseResultForToken",
            value: function fetchParseResultForToken(token) {
                var parseResult = this.parseResultsByToken.get(token);
                if (!parseResult) {
                    parseResult = this.parseToken(token);
                    this.parseResultsByToken.set(token, parseResult);
                }
                return parseResult;
            }
        },
        {
            key: "fetchValuesByTokenForElement",
            value: function fetchValuesByTokenForElement(element) {
                var valuesByToken = this.valuesByTokenByElement.get(element);
                if (!valuesByToken) {
                    valuesByToken = new Map;
                    this.valuesByTokenByElement.set(element, valuesByToken);
                }
                return valuesByToken;
            }
        },
        {
            key: "parseToken",
            value: function parseToken(token) {
                try {
                    var value = this.delegate.parseValueForToken(token);
                    return {
                        value: value
                    };
                } catch (error3) {
                    return {
                        error: error3
                    };
                }
            }
        }
    ]);
    return ValueListObserver;
}();
var BindingObserver = /*#__PURE__*/ function() {
    "use strict";
    function BindingObserver(context, delegate) {
        (0, _classCallCheckJsDefault.default)(this, BindingObserver);
        this.context = context;
        this.delegate = delegate;
        this.bindingsByAction = new Map;
    }
    (0, _createClassJsDefault.default)(BindingObserver, [
        {
            key: "start",
            value: function start() {
                if (!this.valueListObserver) {
                    this.valueListObserver = new ValueListObserver(this.element, this.actionAttribute, this);
                    this.valueListObserver.start();
                }
            }
        },
        {
            key: "stop",
            value: function stop() {
                if (this.valueListObserver) {
                    this.valueListObserver.stop();
                    delete this.valueListObserver;
                    this.disconnectAllActions();
                }
            }
        },
        {
            key: "element",
            get: function get() {
                return this.context.element;
            }
        },
        {
            key: "identifier",
            get: function get() {
                return this.context.identifier;
            }
        },
        {
            key: "actionAttribute",
            get: function get() {
                return this.schema.actionAttribute;
            }
        },
        {
            key: "schema",
            get: function get() {
                return this.context.schema;
            }
        },
        {
            key: "bindings",
            get: function get() {
                return Array.from(this.bindingsByAction.values());
            }
        },
        {
            key: "connectAction",
            value: function connectAction(action) {
                var binding = new Binding(this.context, action);
                this.bindingsByAction.set(action, binding);
                this.delegate.bindingConnected(binding);
            }
        },
        {
            key: "disconnectAction",
            value: function disconnectAction(action) {
                var binding = this.bindingsByAction.get(action);
                if (binding) {
                    this.bindingsByAction.delete(action);
                    this.delegate.bindingDisconnected(binding);
                }
            }
        },
        {
            key: "disconnectAllActions",
            value: function disconnectAllActions() {
                var _this = this;
                this.bindings.forEach(function(binding) {
                    return _this.delegate.bindingDisconnected(binding);
                });
                this.bindingsByAction.clear();
            }
        },
        {
            key: "parseValueForToken",
            value: function parseValueForToken(token) {
                var action = Action.forToken(token);
                if (action.identifier == this.identifier) return action;
            }
        },
        {
            key: "elementMatchedValue",
            value: function elementMatchedValue(element, action) {
                this.connectAction(action);
            }
        },
        {
            key: "elementUnmatchedValue",
            value: function elementUnmatchedValue(element, action) {
                this.disconnectAction(action);
            }
        }
    ]);
    return BindingObserver;
}();
var ValueObserver = /*#__PURE__*/ function() {
    "use strict";
    function ValueObserver(context, receiver) {
        (0, _classCallCheckJsDefault.default)(this, ValueObserver);
        this.context = context;
        this.receiver = receiver;
        this.stringMapObserver = new StringMapObserver(this.element, this);
        this.valueDescriptorMap = this.controller.valueDescriptorMap;
        this.invokeChangedCallbacksForDefaultValues();
    }
    (0, _createClassJsDefault.default)(ValueObserver, [
        {
            key: "start",
            value: function start() {
                this.stringMapObserver.start();
            }
        },
        {
            key: "stop",
            value: function stop() {
                this.stringMapObserver.stop();
            }
        },
        {
            key: "element",
            get: function get() {
                return this.context.element;
            }
        },
        {
            key: "controller",
            get: function get() {
                return this.context.controller;
            }
        },
        {
            key: "getStringMapKeyForAttribute",
            value: function getStringMapKeyForAttribute(attributeName) {
                if (attributeName in this.valueDescriptorMap) return this.valueDescriptorMap[attributeName].name;
            }
        },
        {
            key: "stringMapKeyAdded",
            value: function stringMapKeyAdded(key, attributeName) {
                var descriptor = this.valueDescriptorMap[attributeName];
                if (!this.hasValue(key)) this.invokeChangedCallback(key, descriptor.writer(this.receiver[key]), descriptor.writer(descriptor.defaultValue));
            }
        },
        {
            key: "stringMapValueChanged",
            value: function stringMapValueChanged(value, name, oldValue) {
                var descriptor = this.valueDescriptorNameMap[name];
                if (value === null) return;
                if (oldValue === null) oldValue = descriptor.writer(descriptor.defaultValue);
                this.invokeChangedCallback(name, value, oldValue);
            }
        },
        {
            key: "stringMapKeyRemoved",
            value: function stringMapKeyRemoved(key, attributeName, oldValue) {
                var descriptor = this.valueDescriptorNameMap[key];
                if (this.hasValue(key)) this.invokeChangedCallback(key, descriptor.writer(this.receiver[key]), oldValue);
                else this.invokeChangedCallback(key, descriptor.writer(descriptor.defaultValue), oldValue);
            }
        },
        {
            key: "invokeChangedCallbacksForDefaultValues",
            value: function invokeChangedCallbacksForDefaultValues() {
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                try {
                    for(var _iterator = this.valueDescriptors[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var _value = _step.value, key = _value.key, name = _value.name, defaultValue = _value.defaultValue, writer = _value.writer;
                        if (defaultValue != undefined && !this.controller.data.has(key)) this.invokeChangedCallback(name, writer(defaultValue), undefined);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        },
        {
            key: "invokeChangedCallback",
            value: function invokeChangedCallback(name, rawValue, rawOldValue) {
                var changedMethodName = "".concat(name, "Changed");
                var changedMethod = this.receiver[changedMethodName];
                if (typeof changedMethod == "function") {
                    var descriptor = this.valueDescriptorNameMap[name];
                    var value = descriptor.reader(rawValue);
                    var oldValue = rawOldValue;
                    if (rawOldValue) oldValue = descriptor.reader(rawOldValue);
                    changedMethod.call(this.receiver, value, oldValue);
                }
            }
        },
        {
            key: "valueDescriptors",
            get: function get() {
                var valueDescriptorMap = this.valueDescriptorMap;
                return Object.keys(valueDescriptorMap).map(function(key) {
                    return valueDescriptorMap[key];
                });
            }
        },
        {
            key: "valueDescriptorNameMap",
            get: function get() {
                var _this = this;
                var descriptors = {};
                Object.keys(this.valueDescriptorMap).forEach(function(key) {
                    var descriptor = _this.valueDescriptorMap[key];
                    descriptors[descriptor.name] = descriptor;
                });
                return descriptors;
            }
        },
        {
            key: "hasValue",
            value: function hasValue(attributeName) {
                var descriptor = this.valueDescriptorNameMap[attributeName];
                var hasMethodName = "has".concat(capitalize(descriptor.name));
                return this.receiver[hasMethodName];
            }
        }
    ]);
    return ValueObserver;
}();
var TargetObserver = /*#__PURE__*/ function() {
    "use strict";
    function TargetObserver(context, delegate) {
        (0, _classCallCheckJsDefault.default)(this, TargetObserver);
        this.context = context;
        this.delegate = delegate;
        this.targetsByName = new Multimap;
    }
    (0, _createClassJsDefault.default)(TargetObserver, [
        {
            key: "start",
            value: function start() {
                if (!this.tokenListObserver) {
                    this.tokenListObserver = new TokenListObserver(this.element, this.attributeName, this);
                    this.tokenListObserver.start();
                }
            }
        },
        {
            key: "stop",
            value: function stop() {
                if (this.tokenListObserver) {
                    this.disconnectAllTargets();
                    this.tokenListObserver.stop();
                    delete this.tokenListObserver;
                }
            }
        },
        {
            key: "tokenMatched",
            value: function tokenMatched(param) {
                var element = param.element, name = param.content;
                if (this.scope.containsElement(element)) this.connectTarget(element, name);
            }
        },
        {
            key: "tokenUnmatched",
            value: function tokenUnmatched(param) {
                var element = param.element, name = param.content;
                this.disconnectTarget(element, name);
            }
        },
        {
            key: "connectTarget",
            value: function connectTarget(element, name) {
                var _a;
                if (!this.targetsByName.has(name, element)) {
                    var _this = this;
                    this.targetsByName.add(name, element);
                    (_a = this.tokenListObserver) === null || _a === void 0 || _a.pause(function() {
                        return _this.delegate.targetConnected(element, name);
                    });
                }
            }
        },
        {
            key: "disconnectTarget",
            value: function disconnectTarget(element, name) {
                var _a;
                if (this.targetsByName.has(name, element)) {
                    var _this = this;
                    this.targetsByName.delete(name, element);
                    (_a = this.tokenListObserver) === null || _a === void 0 || _a.pause(function() {
                        return _this.delegate.targetDisconnected(element, name);
                    });
                }
            }
        },
        {
            key: "disconnectAllTargets",
            value: function disconnectAllTargets() {
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined, _iteratorNormalCompletion2 = true, _didIteratorError2 = false, _iteratorError2 = undefined;
                try {
                    for(var _iterator = this.targetsByName.keys[Symbol.iterator](), _step; !(_iteratorNormalCompletion2 = (_step = _iterator.next()).done); _iteratorNormalCompletion2 = true){
                        var name = _step.value;
                        try {
                            for(var _iterator2 = this.targetsByName.getValuesForKey(name)[Symbol.iterator](), _step2; !(_iteratorNormalCompletion = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion = true){
                                var element = _step2.value;
                                this.disconnectTarget(element, name);
                            }
                        } catch (err) {
                            _didIteratorError = true;
                            _iteratorError = err;
                        } finally{
                            try {
                                if (!_iteratorNormalCompletion && _iterator2.return != null) {
                                    _iterator2.return();
                                }
                            } finally{
                                if (_didIteratorError) {
                                    throw _iteratorError;
                                }
                            }
                        }
                    }
                } catch (err) {
                    _didIteratorError2 = true;
                    _iteratorError2 = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion2 && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError2) {
                            throw _iteratorError2;
                        }
                    }
                }
            }
        },
        {
            key: "attributeName",
            get: function get() {
                return "data-".concat(this.context.identifier, "-target");
            }
        },
        {
            key: "element",
            get: function get() {
                return this.context.element;
            }
        },
        {
            key: "scope",
            get: function get() {
                return this.context.scope;
            }
        }
    ]);
    return TargetObserver;
}();
var Context = /*#__PURE__*/ function() {
    "use strict";
    function Context(module, scope) {
        var _this = this;
        (0, _classCallCheckJsDefault.default)(this, Context);
        this.logDebugActivity = function(functionName) {
            var detail = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
            var identifier = _this.identifier, controller = _this.controller, element = _this.element;
            detail = Object.assign({
                identifier: identifier,
                controller: controller,
                element: element
            }, detail);
            _this.application.logDebugActivity(_this.identifier, functionName, detail);
        };
        this.module = module;
        this.scope = scope;
        this.controller = new module.controllerConstructor(this);
        this.bindingObserver = new BindingObserver(this, this.dispatcher);
        this.valueObserver = new ValueObserver(this, this.controller);
        this.targetObserver = new TargetObserver(this, this);
        try {
            this.controller.initialize();
            this.logDebugActivity("initialize");
        } catch (error4) {
            this.handleError(error4, "initializing controller");
        }
    }
    (0, _createClassJsDefault.default)(Context, [
        {
            key: "connect",
            value: function connect() {
                this.bindingObserver.start();
                this.valueObserver.start();
                this.targetObserver.start();
                try {
                    this.controller.connect();
                    this.logDebugActivity("connect");
                } catch (error5) {
                    this.handleError(error5, "connecting controller");
                }
            }
        },
        {
            key: "disconnect",
            value: function disconnect() {
                try {
                    this.controller.disconnect();
                    this.logDebugActivity("disconnect");
                } catch (error6) {
                    this.handleError(error6, "disconnecting controller");
                }
                this.targetObserver.stop();
                this.valueObserver.stop();
                this.bindingObserver.stop();
            }
        },
        {
            key: "application",
            get: function get() {
                return this.module.application;
            }
        },
        {
            key: "identifier",
            get: function get() {
                return this.module.identifier;
            }
        },
        {
            key: "schema",
            get: function get() {
                return this.application.schema;
            }
        },
        {
            key: "dispatcher",
            get: function get() {
                return this.application.dispatcher;
            }
        },
        {
            key: "element",
            get: function get() {
                return this.scope.element;
            }
        },
        {
            key: "parentElement",
            get: function get() {
                return this.element.parentElement;
            }
        },
        {
            key: "handleError",
            value: function handleError(error7, message) {
                var detail = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {};
                var ref = this, identifier = ref.identifier, controller = ref.controller, element = ref.element;
                detail = Object.assign({
                    identifier: identifier,
                    controller: controller,
                    element: element
                }, detail);
                this.application.handleError(error7, "Error ".concat(message), detail);
            }
        },
        {
            key: "targetConnected",
            value: function targetConnected(element, name) {
                this.invokeControllerMethod("".concat(name, "TargetConnected"), element);
            }
        },
        {
            key: "targetDisconnected",
            value: function targetDisconnected(element, name) {
                this.invokeControllerMethod("".concat(name, "TargetDisconnected"), element);
            }
        },
        {
            key: "invokeControllerMethod",
            value: function invokeControllerMethod(methodName) {
                for(var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++){
                    args[_key - 1] = arguments[_key];
                }
                var _controller;
                var controller = this.controller;
                if (typeof controller[methodName] == "function") (_controller = controller)[methodName].apply(_controller, (0, _toConsumableArrayJsDefault.default)(args));
            }
        }
    ]);
    return Context;
}();
function readInheritableStaticArrayValues(constructor1, propertyName) {
    var ancestors = getAncestorsForConstructor(constructor1);
    return Array.from(ancestors.reduce(function(values, constructor) {
        getOwnStaticArrayValues(constructor, propertyName).forEach(function(name) {
            return values.add(name);
        });
        return values;
    }, new Set));
}
function readInheritableStaticObjectPairs(constructor2, propertyName) {
    var ancestors = getAncestorsForConstructor(constructor2);
    return ancestors.reduce(function(pairs, constructor) {
        var _pairs;
        (_pairs = pairs).push.apply(_pairs, (0, _toConsumableArrayJsDefault.default)(getOwnStaticObjectPairs(constructor, propertyName)));
        return pairs;
    }, []);
}
function getAncestorsForConstructor(constructor) {
    var ancestors = [];
    while(constructor){
        ancestors.push(constructor);
        constructor = Object.getPrototypeOf(constructor);
    }
    return ancestors.reverse();
}
function getOwnStaticArrayValues(constructor, propertyName) {
    var definition = constructor[propertyName];
    return Array.isArray(definition) ? definition : [];
}
function getOwnStaticObjectPairs(constructor, propertyName) {
    var definition = constructor[propertyName];
    return definition ? Object.keys(definition).map(function(key) {
        return [
            key,
            definition[key]
        ];
    }) : [];
}
function bless(constructor) {
    return shadow(constructor, getBlessedProperties(constructor));
}
function shadow(constructor, properties) {
    var shadowConstructor = extend(constructor);
    var shadowProperties = getShadowProperties(constructor.prototype, properties);
    Object.defineProperties(shadowConstructor.prototype, shadowProperties);
    return shadowConstructor;
}
function getBlessedProperties(constructor) {
    var blessings = readInheritableStaticArrayValues(constructor, "blessings");
    return blessings.reduce(function(blessedProperties, blessing) {
        var properties = blessing(constructor);
        for(var key in properties){
            var descriptor = blessedProperties[key] || {};
            blessedProperties[key] = Object.assign(descriptor, properties[key]);
        }
        return blessedProperties;
    }, {});
}
function getShadowProperties(prototype, properties) {
    return getOwnKeys(properties).reduce(function(shadowProperties, key) {
        var descriptor = getShadowedDescriptor(prototype, properties, key);
        if (descriptor) Object.assign(shadowProperties, (0, _definePropertyJsDefault.default)({}, key, descriptor));
        return shadowProperties;
    }, {});
}
function getShadowedDescriptor(prototype, properties, key) {
    var shadowingDescriptor = Object.getOwnPropertyDescriptor(prototype, key);
    var shadowedByValue = shadowingDescriptor && "value" in shadowingDescriptor;
    if (!shadowedByValue) {
        var descriptor = Object.getOwnPropertyDescriptor(properties, key).value;
        if (shadowingDescriptor) {
            descriptor.get = shadowingDescriptor.get || descriptor.get;
            descriptor.set = shadowingDescriptor.set || descriptor.set;
        }
        return descriptor;
    }
}
var getOwnKeys = function() {
    if (typeof Object.getOwnPropertySymbols == "function") return function(object) {
        return (0, _toConsumableArrayJsDefault.default)(Object.getOwnPropertyNames(object)).concat((0, _toConsumableArrayJsDefault.default)(Object.getOwnPropertySymbols(object)));
    };
    else return Object.getOwnPropertyNames;
}();
var extend = function _target() {
    function extendWithReflect(constructor) {
        function extended() {
            return Reflect.construct(constructor, arguments, this instanceof extended ? this.constructor : void 0);
        }
        extended.prototype = Object.create(constructor.prototype, {
            constructor: {
                value: extended
            }
        });
        Reflect.setPrototypeOf(extended, constructor);
        return extended;
    }
    function testReflectExtension() {
        var a = function a() {
            this.a.call(this);
        };
        var b = extendWithReflect(a);
        b.prototype.a = function() {};
        return new b;
    }
    try {
        testReflectExtension();
        return extendWithReflect;
    } catch (error) {
        return function(constructor3) {
            return /*#__PURE__*/ function(constructor) {
                "use strict";
                (0, _inheritsJsDefault.default)(extended, constructor);
                var _super = (0, _createSuperJsDefault.default)(extended);
                function extended() {
                    (0, _classCallCheckJsDefault.default)(this, extended);
                    return _super.apply(this, arguments);
                }
                return extended;
            }(constructor3);
        };
    }
}();
function blessDefinition(definition) {
    return {
        identifier: definition.identifier,
        controllerConstructor: bless(definition.controllerConstructor)
    };
}
var Module = /*#__PURE__*/ function() {
    "use strict";
    function Module(application, definition) {
        (0, _classCallCheckJsDefault.default)(this, Module);
        this.application = application;
        this.definition = blessDefinition(definition);
        this.contextsByScope = new WeakMap;
        this.connectedContexts = new Set;
    }
    (0, _createClassJsDefault.default)(Module, [
        {
            key: "identifier",
            get: function get() {
                return this.definition.identifier;
            }
        },
        {
            key: "controllerConstructor",
            get: function get() {
                return this.definition.controllerConstructor;
            }
        },
        {
            key: "contexts",
            get: function get() {
                return Array.from(this.connectedContexts);
            }
        },
        {
            key: "connectContextForScope",
            value: function connectContextForScope(scope) {
                var context = this.fetchContextForScope(scope);
                this.connectedContexts.add(context);
                context.connect();
            }
        },
        {
            key: "disconnectContextForScope",
            value: function disconnectContextForScope(scope) {
                var context = this.contextsByScope.get(scope);
                if (context) {
                    this.connectedContexts.delete(context);
                    context.disconnect();
                }
            }
        },
        {
            key: "fetchContextForScope",
            value: function fetchContextForScope(scope) {
                var context = this.contextsByScope.get(scope);
                if (!context) {
                    context = new Context(this, scope);
                    this.contextsByScope.set(scope, context);
                }
                return context;
            }
        }
    ]);
    return Module;
}();
var ClassMap = /*#__PURE__*/ function() {
    "use strict";
    function ClassMap(scope) {
        (0, _classCallCheckJsDefault.default)(this, ClassMap);
        this.scope = scope;
    }
    (0, _createClassJsDefault.default)(ClassMap, [
        {
            key: "has",
            value: function has(name) {
                return this.data.has(this.getDataKey(name));
            }
        },
        {
            key: "get",
            value: function get(name) {
                return this.getAll(name)[0];
            }
        },
        {
            key: "getAll",
            value: function getAll(name) {
                var tokenString = this.data.get(this.getDataKey(name)) || "";
                return tokenize(tokenString);
            }
        },
        {
            key: "getAttributeName",
            value: function getAttributeName(name) {
                return this.data.getAttributeNameForKey(this.getDataKey(name));
            }
        },
        {
            key: "getDataKey",
            value: function getDataKey(name) {
                return "".concat(name, "-class");
            }
        },
        {
            key: "data",
            get: function get() {
                return this.scope.data;
            }
        }
    ]);
    return ClassMap;
}();
var DataMap = /*#__PURE__*/ function() {
    "use strict";
    function DataMap(scope) {
        (0, _classCallCheckJsDefault.default)(this, DataMap);
        this.scope = scope;
    }
    (0, _createClassJsDefault.default)(DataMap, [
        {
            key: "element",
            get: function get() {
                return this.scope.element;
            }
        },
        {
            key: "identifier",
            get: function get() {
                return this.scope.identifier;
            }
        },
        {
            key: "get",
            value: function get(key) {
                var name = this.getAttributeNameForKey(key);
                return this.element.getAttribute(name);
            }
        },
        {
            key: "set",
            value: function set(key, value) {
                var name = this.getAttributeNameForKey(key);
                this.element.setAttribute(name, value);
                return this.get(key);
            }
        },
        {
            key: "has",
            value: function has(key) {
                var name = this.getAttributeNameForKey(key);
                return this.element.hasAttribute(name);
            }
        },
        {
            key: "delete",
            value: function _delete(key) {
                if (this.has(key)) {
                    var name = this.getAttributeNameForKey(key);
                    this.element.removeAttribute(name);
                    return true;
                } else return false;
            }
        },
        {
            key: "getAttributeNameForKey",
            value: function getAttributeNameForKey(key) {
                return "data-".concat(this.identifier, "-").concat(dasherize(key));
            }
        }
    ]);
    return DataMap;
}();
var Guide = /*#__PURE__*/ function() {
    "use strict";
    function Guide(logger) {
        (0, _classCallCheckJsDefault.default)(this, Guide);
        this.warnedKeysByObject = new WeakMap;
        this.logger = logger;
    }
    (0, _createClassJsDefault.default)(Guide, [
        {
            key: "warn",
            value: function warn(object, key, message) {
                var warnedKeys = this.warnedKeysByObject.get(object);
                if (!warnedKeys) {
                    warnedKeys = new Set;
                    this.warnedKeysByObject.set(object, warnedKeys);
                }
                if (!warnedKeys.has(key)) {
                    warnedKeys.add(key);
                    this.logger.warn(message, object);
                }
            }
        }
    ]);
    return Guide;
}();
function attributeValueContainsToken(attributeName, token) {
    return "[".concat(attributeName, '~="').concat(token, '"]');
}
var TargetSet = /*#__PURE__*/ function() {
    "use strict";
    function TargetSet(scope) {
        (0, _classCallCheckJsDefault.default)(this, TargetSet);
        this.scope = scope;
    }
    (0, _createClassJsDefault.default)(TargetSet, [
        {
            key: "element",
            get: function get() {
                return this.scope.element;
            }
        },
        {
            key: "identifier",
            get: function get() {
                return this.scope.identifier;
            }
        },
        {
            key: "schema",
            get: function get() {
                return this.scope.schema;
            }
        },
        {
            key: "has",
            value: function has(targetName) {
                return this.find(targetName) != null;
            }
        },
        {
            key: "find",
            value: function find() {
                for(var _len = arguments.length, targetNames = new Array(_len), _key = 0; _key < _len; _key++){
                    targetNames[_key] = arguments[_key];
                }
                var _this = this;
                return targetNames.reduce(function(target, targetName) {
                    return target || _this.findTarget(targetName) || _this.findLegacyTarget(targetName);
                }, undefined);
            }
        },
        {
            key: "findAll",
            value: function findAll() {
                for(var _len = arguments.length, targetNames = new Array(_len), _key = 0; _key < _len; _key++){
                    targetNames[_key] = arguments[_key];
                }
                var _this = this;
                return targetNames.reduce(function(targets, targetName) {
                    return (0, _toConsumableArrayJsDefault.default)(targets).concat((0, _toConsumableArrayJsDefault.default)(_this.findAllTargets(targetName)), (0, _toConsumableArrayJsDefault.default)(_this.findAllLegacyTargets(targetName)));
                }, []);
            }
        },
        {
            key: "findTarget",
            value: function findTarget(targetName) {
                var selector = this.getSelectorForTargetName(targetName);
                return this.scope.findElement(selector);
            }
        },
        {
            key: "findAllTargets",
            value: function findAllTargets(targetName) {
                var selector = this.getSelectorForTargetName(targetName);
                return this.scope.findAllElements(selector);
            }
        },
        {
            key: "getSelectorForTargetName",
            value: function getSelectorForTargetName(targetName) {
                var attributeName = this.schema.targetAttributeForScope(this.identifier);
                return attributeValueContainsToken(attributeName, targetName);
            }
        },
        {
            key: "findLegacyTarget",
            value: function findLegacyTarget(targetName) {
                var selector = this.getLegacySelectorForTargetName(targetName);
                return this.deprecate(this.scope.findElement(selector), targetName);
            }
        },
        {
            key: "findAllLegacyTargets",
            value: function findAllLegacyTargets(targetName) {
                var _this = this;
                var selector = this.getLegacySelectorForTargetName(targetName);
                return this.scope.findAllElements(selector).map(function(element) {
                    return _this.deprecate(element, targetName);
                });
            }
        },
        {
            key: "getLegacySelectorForTargetName",
            value: function getLegacySelectorForTargetName(targetName) {
                var targetDescriptor = "".concat(this.identifier, ".").concat(targetName);
                return attributeValueContainsToken(this.schema.targetAttribute, targetDescriptor);
            }
        },
        {
            key: "deprecate",
            value: function deprecate(element, targetName) {
                if (element) {
                    var identifier = this.identifier;
                    var attributeName = this.schema.targetAttribute;
                    var revisedAttributeName = this.schema.targetAttributeForScope(identifier);
                    this.guide.warn(element, "target:".concat(targetName), "Please replace ".concat(attributeName, '="').concat(identifier, ".").concat(targetName, '" with ').concat(revisedAttributeName, '="').concat(targetName, '". ') + "The ".concat(attributeName, " attribute is deprecated and will be removed in a future version of Stimulus."));
                }
                return element;
            }
        },
        {
            key: "guide",
            get: function get() {
                return this.scope.guide;
            }
        }
    ]);
    return TargetSet;
}();
var Scope = /*#__PURE__*/ function() {
    "use strict";
    function Scope(schema, element2, identifier, logger) {
        var _this = this;
        (0, _classCallCheckJsDefault.default)(this, Scope);
        this.targets = new TargetSet(this);
        this.classes = new ClassMap(this);
        this.data = new DataMap(this);
        this.containsElement = function(element) {
            return element.closest(_this.controllerSelector) === _this.element;
        };
        this.schema = schema;
        this.element = element2;
        this.identifier = identifier;
        this.guide = new Guide(logger);
    }
    (0, _createClassJsDefault.default)(Scope, [
        {
            key: "findElement",
            value: function findElement(selector) {
                return this.element.matches(selector) ? this.element : this.queryElements(selector).find(this.containsElement);
            }
        },
        {
            key: "findAllElements",
            value: function findAllElements(selector) {
                return (0, _toConsumableArrayJsDefault.default)(this.element.matches(selector) ? [
                    this.element
                ] : []).concat((0, _toConsumableArrayJsDefault.default)(this.queryElements(selector).filter(this.containsElement)));
            }
        },
        {
            key: "queryElements",
            value: function queryElements(selector) {
                return Array.from(this.element.querySelectorAll(selector));
            }
        },
        {
            key: "controllerSelector",
            get: function get() {
                return attributeValueContainsToken(this.schema.controllerAttribute, this.identifier);
            }
        }
    ]);
    return Scope;
}();
var ScopeObserver = /*#__PURE__*/ function() {
    "use strict";
    function ScopeObserver(element, schema, delegate) {
        (0, _classCallCheckJsDefault.default)(this, ScopeObserver);
        this.element = element;
        this.schema = schema;
        this.delegate = delegate;
        this.valueListObserver = new ValueListObserver(this.element, this.controllerAttribute, this);
        this.scopesByIdentifierByElement = new WeakMap;
        this.scopeReferenceCounts = new WeakMap;
    }
    (0, _createClassJsDefault.default)(ScopeObserver, [
        {
            key: "start",
            value: function start() {
                this.valueListObserver.start();
            }
        },
        {
            key: "stop",
            value: function stop() {
                this.valueListObserver.stop();
            }
        },
        {
            key: "controllerAttribute",
            get: function get() {
                return this.schema.controllerAttribute;
            }
        },
        {
            key: "parseValueForToken",
            value: function parseValueForToken(token) {
                var element = token.element, identifier = token.content;
                var scopesByIdentifier = this.fetchScopesByIdentifierForElement(element);
                var scope = scopesByIdentifier.get(identifier);
                if (!scope) {
                    scope = this.delegate.createScopeForElementAndIdentifier(element, identifier);
                    scopesByIdentifier.set(identifier, scope);
                }
                return scope;
            }
        },
        {
            key: "elementMatchedValue",
            value: function elementMatchedValue(element, value) {
                var referenceCount = (this.scopeReferenceCounts.get(value) || 0) + 1;
                this.scopeReferenceCounts.set(value, referenceCount);
                if (referenceCount == 1) this.delegate.scopeConnected(value);
            }
        },
        {
            key: "elementUnmatchedValue",
            value: function elementUnmatchedValue(element, value) {
                var referenceCount = this.scopeReferenceCounts.get(value);
                if (referenceCount) {
                    this.scopeReferenceCounts.set(value, referenceCount - 1);
                    if (referenceCount == 1) this.delegate.scopeDisconnected(value);
                }
            }
        },
        {
            key: "fetchScopesByIdentifierForElement",
            value: function fetchScopesByIdentifierForElement(element) {
                var scopesByIdentifier = this.scopesByIdentifierByElement.get(element);
                if (!scopesByIdentifier) {
                    scopesByIdentifier = new Map;
                    this.scopesByIdentifierByElement.set(element, scopesByIdentifier);
                }
                return scopesByIdentifier;
            }
        }
    ]);
    return ScopeObserver;
}();
var Router = /*#__PURE__*/ function() {
    "use strict";
    function Router(application) {
        (0, _classCallCheckJsDefault.default)(this, Router);
        this.application = application;
        this.scopeObserver = new ScopeObserver(this.element, this.schema, this);
        this.scopesByIdentifier = new Multimap;
        this.modulesByIdentifier = new Map;
    }
    (0, _createClassJsDefault.default)(Router, [
        {
            key: "element",
            get: function get() {
                return this.application.element;
            }
        },
        {
            key: "schema",
            get: function get() {
                return this.application.schema;
            }
        },
        {
            key: "logger",
            get: function get() {
                return this.application.logger;
            }
        },
        {
            key: "controllerAttribute",
            get: function get() {
                return this.schema.controllerAttribute;
            }
        },
        {
            key: "modules",
            get: function get() {
                return Array.from(this.modulesByIdentifier.values());
            }
        },
        {
            key: "contexts",
            get: function get() {
                return this.modules.reduce(function(contexts, module) {
                    return contexts.concat(module.contexts);
                }, []);
            }
        },
        {
            key: "start",
            value: function start() {
                this.scopeObserver.start();
            }
        },
        {
            key: "stop",
            value: function stop() {
                this.scopeObserver.stop();
            }
        },
        {
            key: "loadDefinition",
            value: function loadDefinition(definition) {
                this.unloadIdentifier(definition.identifier);
                var module = new Module(this.application, definition);
                this.connectModule(module);
            }
        },
        {
            key: "unloadIdentifier",
            value: function unloadIdentifier(identifier) {
                var module = this.modulesByIdentifier.get(identifier);
                if (module) this.disconnectModule(module);
            }
        },
        {
            key: "getContextForElementAndIdentifier",
            value: function getContextForElementAndIdentifier(element, identifier) {
                var module = this.modulesByIdentifier.get(identifier);
                if (module) return module.contexts.find(function(context) {
                    return context.element == element;
                });
            }
        },
        {
            key: "handleError",
            value: function handleError(error8, message, detail) {
                this.application.handleError(error8, message, detail);
            }
        },
        {
            key: "createScopeForElementAndIdentifier",
            value: function createScopeForElementAndIdentifier(element, identifier) {
                return new Scope(this.schema, element, identifier, this.logger);
            }
        },
        {
            key: "scopeConnected",
            value: function scopeConnected(scope) {
                this.scopesByIdentifier.add(scope.identifier, scope);
                var module = this.modulesByIdentifier.get(scope.identifier);
                if (module) module.connectContextForScope(scope);
            }
        },
        {
            key: "scopeDisconnected",
            value: function scopeDisconnected(scope) {
                this.scopesByIdentifier.delete(scope.identifier, scope);
                var module = this.modulesByIdentifier.get(scope.identifier);
                if (module) module.disconnectContextForScope(scope);
            }
        },
        {
            key: "connectModule",
            value: function connectModule(module) {
                this.modulesByIdentifier.set(module.identifier, module);
                var scopes = this.scopesByIdentifier.getValuesForKey(module.identifier);
                scopes.forEach(function(scope) {
                    return module.connectContextForScope(scope);
                });
            }
        },
        {
            key: "disconnectModule",
            value: function disconnectModule(module) {
                this.modulesByIdentifier.delete(module.identifier);
                var scopes = this.scopesByIdentifier.getValuesForKey(module.identifier);
                scopes.forEach(function(scope) {
                    return module.disconnectContextForScope(scope);
                });
            }
        }
    ]);
    return Router;
}();
var defaultSchema = {
    controllerAttribute: "data-controller",
    actionAttribute: "data-action",
    targetAttribute: "data-target",
    targetAttributeForScope: function(identifier) {
        return "data-".concat(identifier, "-target");
    }
};
var Application = /*#__PURE__*/ function() {
    "use strict";
    function Application() {
        var element = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : document.documentElement, schema = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : defaultSchema;
        var _this = this;
        (0, _classCallCheckJsDefault.default)(this, Application);
        this.logger = console;
        this.debug = false;
        this.logDebugActivity = function(identifier, functionName) {
            var detail = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {};
            if (_this.debug) _this.logFormattedMessage(identifier, functionName, detail);
        };
        this.element = element;
        this.schema = schema;
        this.dispatcher = new Dispatcher(this);
        this.router = new Router(this);
    }
    (0, _createClassJsDefault.default)(Application, [
        {
            key: "start",
            value: function start() {
                var _this = this;
                return (0, _asyncToGeneratorJsDefault.default)((0, _regeneratorRuntimeDefault.default).mark(function _callee() {
                    return (0, _regeneratorRuntimeDefault.default).wrap(function _callee$(_ctx) {
                        while(1)switch(_ctx.prev = _ctx.next){
                            case 0:
                                _ctx.next = 2;
                                return domReady();
                            case 2:
                                _this.logDebugActivity("application", "starting");
                                _this.dispatcher.start();
                                _this.router.start();
                                _this.logDebugActivity("application", "start");
                            case 6:
                            case "end":
                                return _ctx.stop();
                        }
                    }, _callee);
                }))();
            }
        },
        {
            key: "stop",
            value: function stop() {
                this.logDebugActivity("application", "stopping");
                this.dispatcher.stop();
                this.router.stop();
                this.logDebugActivity("application", "stop");
            }
        },
        {
            key: "register",
            value: function register(identifier, controllerConstructor) {
                if (controllerConstructor.shouldLoad) this.load({
                    identifier: identifier,
                    controllerConstructor: controllerConstructor
                });
            }
        },
        {
            key: "load",
            value: function load(head) {
                for(var _len = arguments.length, rest = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++){
                    rest[_key - 1] = arguments[_key];
                }
                var _this = this;
                var definitions = Array.isArray(head) ? head : [
                    head
                ].concat((0, _toConsumableArrayJsDefault.default)(rest));
                definitions.forEach(function(definition) {
                    return _this.router.loadDefinition(definition);
                });
            }
        },
        {
            key: "unload",
            value: function unload(head) {
                for(var _len = arguments.length, rest = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++){
                    rest[_key - 1] = arguments[_key];
                }
                var _this = this;
                var identifiers = Array.isArray(head) ? head : [
                    head
                ].concat((0, _toConsumableArrayJsDefault.default)(rest));
                identifiers.forEach(function(identifier) {
                    return _this.router.unloadIdentifier(identifier);
                });
            }
        },
        {
            key: "controllers",
            get: function get() {
                return this.router.contexts.map(function(context) {
                    return context.controller;
                });
            }
        },
        {
            key: "getControllerForElementAndIdentifier",
            value: function getControllerForElementAndIdentifier(element, identifier) {
                var context = this.router.getContextForElementAndIdentifier(element, identifier);
                return context ? context.controller : null;
            }
        },
        {
            key: "handleError",
            value: function handleError(error9, message, detail) {
                var _a;
                this.logger.error("%s\n\n%o\n\n%o", message, error9, detail);
                (_a = window.onerror) === null || _a === void 0 || _a.call(window, message, "", 0, 0, error9);
            }
        },
        {
            key: "logFormattedMessage",
            value: function logFormattedMessage(identifier, functionName) {
                var detail = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {};
                detail = Object.assign({
                    application: this
                }, detail);
                this.logger.groupCollapsed("".concat(identifier, " #").concat(functionName));
                this.logger.log("details:", Object.assign({}, detail));
                this.logger.groupEnd();
            }
        }
    ], [
        {
            key: "start",
            value: function start(element, schema) {
                var application = new Application(element, schema);
                application.start();
                return application;
            }
        }
    ]);
    return Application;
}();
function domReady() {
    return new Promise(function(resolve) {
        if (document.readyState == "loading") document.addEventListener("DOMContentLoaded", function() {
            return resolve();
        });
        else resolve();
    });
}
function ClassPropertiesBlessing(constructor) {
    var classes = readInheritableStaticArrayValues(constructor, "classes");
    return classes.reduce(function(properties, classDefinition) {
        return Object.assign(properties, propertiesForClassDefinition(classDefinition));
    }, {});
}
function propertiesForClassDefinition(key) {
    var _obj;
    return _obj = {}, (0, _definePropertyJsDefault.default)(_obj, "".concat(key, "Class"), {
        get: function() {
            var classes = this.classes;
            if (classes.has(key)) return classes.get(key);
            else {
                var attribute = classes.getAttributeName(key);
                throw new Error('Missing attribute "'.concat(attribute, '"'));
            }
        }
    }), (0, _definePropertyJsDefault.default)(_obj, "".concat(key, "Classes"), {
        get: function() {
            return this.classes.getAll(key);
        }
    }), (0, _definePropertyJsDefault.default)(_obj, "has".concat(capitalize(key), "Class"), {
        get: function() {
            return this.classes.has(key);
        }
    }), _obj;
}
function TargetPropertiesBlessing(constructor) {
    var targets = readInheritableStaticArrayValues(constructor, "targets");
    return targets.reduce(function(properties, targetDefinition) {
        return Object.assign(properties, propertiesForTargetDefinition(targetDefinition));
    }, {});
}
function propertiesForTargetDefinition(name) {
    var _obj;
    return _obj = {}, (0, _definePropertyJsDefault.default)(_obj, "".concat(name, "Target"), {
        get: function() {
            var target = this.targets.find(name);
            if (target) return target;
            else throw new Error('Missing target element "'.concat(name, '" for "').concat(this.identifier, '" controller'));
        }
    }), (0, _definePropertyJsDefault.default)(_obj, "".concat(name, "Targets"), {
        get: function() {
            return this.targets.findAll(name);
        }
    }), (0, _definePropertyJsDefault.default)(_obj, "has".concat(capitalize(name), "Target"), {
        get: function() {
            return this.targets.has(name);
        }
    }), _obj;
}
function ValuePropertiesBlessing(constructor) {
    var valueDefinitionPairs = readInheritableStaticObjectPairs(constructor, "values");
    var propertyDescriptorMap = {
        valueDescriptorMap: {
            get: function() {
                var _this = this;
                return valueDefinitionPairs.reduce(function(result, valueDefinitionPair) {
                    var valueDescriptor = parseValueDefinitionPair(valueDefinitionPair);
                    var attributeName = _this.data.getAttributeNameForKey(valueDescriptor.key);
                    return Object.assign(result, (0, _definePropertyJsDefault.default)({}, attributeName, valueDescriptor));
                }, {});
            }
        }
    };
    return valueDefinitionPairs.reduce(function(properties, valueDefinitionPair) {
        return Object.assign(properties, propertiesForValueDefinitionPair(valueDefinitionPair));
    }, propertyDescriptorMap);
}
function propertiesForValueDefinitionPair(valueDefinitionPair) {
    var definition = parseValueDefinitionPair(valueDefinitionPair);
    var key = definition.key, name = definition.name, read = definition.reader, write = definition.writer;
    var _obj;
    return _obj = {}, (0, _definePropertyJsDefault.default)(_obj, name, {
        get: function() {
            var value = this.data.get(key);
            if (value !== null) return read(value);
            else return definition.defaultValue;
        },
        set: function(value) {
            if (value === undefined) this.data.delete(key);
            else this.data.set(key, write(value));
        }
    }), (0, _definePropertyJsDefault.default)(_obj, "has".concat(capitalize(name)), {
        get: function() {
            return this.data.has(key) || definition.hasCustomDefaultValue;
        }
    }), _obj;
}
function parseValueDefinitionPair(param) {
    var _param = (0, _slicedToArrayJsDefault.default)(param, 2), token = _param[0], typeDefinition = _param[1];
    return valueDescriptorForTokenAndTypeDefinition(token, typeDefinition);
}
function parseValueTypeConstant(constant) {
    switch(constant){
        case Array:
            return "array";
        case Boolean:
            return "boolean";
        case Number:
            return "number";
        case Object:
            return "object";
        case String:
            return "string";
    }
}
function parseValueTypeDefault(defaultValue) {
    switch(typeof defaultValue === "undefined" ? "undefined" : (0, _typeOfJsDefault.default)(defaultValue)){
        case "boolean":
            return "boolean";
        case "number":
            return "number";
        case "string":
            return "string";
    }
    if (Array.isArray(defaultValue)) return "array";
    if (Object.prototype.toString.call(defaultValue) === "[object Object]") return "object";
}
function parseValueTypeObject(typeObject) {
    var typeFromObject = parseValueTypeConstant(typeObject.type);
    if (typeFromObject) {
        var defaultValueType = parseValueTypeDefault(typeObject.default);
        if (typeFromObject !== defaultValueType) throw new Error('Type "'.concat(typeFromObject, '" must match the type of the default value. Given default value: "').concat(typeObject.default, '" as "').concat(defaultValueType, '"'));
        return typeFromObject;
    }
}
function parseValueTypeDefinition(typeDefinition) {
    var typeFromObject = parseValueTypeObject(typeDefinition);
    var typeFromDefaultValue = parseValueTypeDefault(typeDefinition);
    var typeFromConstant = parseValueTypeConstant(typeDefinition);
    var type = typeFromObject || typeFromDefaultValue || typeFromConstant;
    if (type) return type;
    throw new Error('Unknown value type "'.concat(typeDefinition, '"'));
}
function defaultValueForDefinition(typeDefinition) {
    var constant = parseValueTypeConstant(typeDefinition);
    if (constant) return defaultValuesByType[constant];
    var defaultValue = typeDefinition.default;
    if (defaultValue !== undefined) return defaultValue;
    return typeDefinition;
}
function valueDescriptorForTokenAndTypeDefinition(token, typeDefinition) {
    var key = "".concat(dasherize(token), "-value");
    var type = parseValueTypeDefinition(typeDefinition);
    return {
        type: type,
        key: key,
        name: camelize(key),
        get defaultValue () {
            return defaultValueForDefinition(typeDefinition);
        },
        get hasCustomDefaultValue () {
            return parseValueTypeDefault(typeDefinition) !== undefined;
        },
        reader: readers[type],
        writer: writers[type] || writers.default
    };
}
var defaultValuesByType = {
    get array () {
        return [];
    },
    boolean: false,
    number: 0,
    get object () {
        return {};
    },
    string: ""
};
var readers = {
    array: function(value) {
        var array = JSON.parse(value);
        if (!Array.isArray(array)) throw new TypeError("Expected array");
        return array;
    },
    boolean: function(value) {
        return !(value == "0" || value == "false");
    },
    number: function(value) {
        return Number(value);
    },
    object: function(value) {
        var object = JSON.parse(value);
        if (object === null || typeof object != "object" || Array.isArray(object)) throw new TypeError("Expected object");
        return object;
    },
    string: function(value) {
        return value;
    }
};
var writers = {
    default: writeString,
    array: writeJSON,
    object: writeJSON
};
function writeJSON(value) {
    return JSON.stringify(value);
}
function writeString(value) {
    return "".concat(value);
}
var Controller = /*#__PURE__*/ function() {
    "use strict";
    function Controller(context) {
        (0, _classCallCheckJsDefault.default)(this, Controller);
        this.context = context;
    }
    (0, _createClassJsDefault.default)(Controller, [
        {
            key: "application",
            get: function get() {
                return this.context.application;
            }
        },
        {
            key: "scope",
            get: function get() {
                return this.context.scope;
            }
        },
        {
            key: "element",
            get: function get() {
                return this.scope.element;
            }
        },
        {
            key: "identifier",
            get: function get() {
                return this.scope.identifier;
            }
        },
        {
            key: "targets",
            get: function get() {
                return this.scope.targets;
            }
        },
        {
            key: "classes",
            get: function get() {
                return this.scope.classes;
            }
        },
        {
            key: "data",
            get: function get() {
                return this.scope.data;
            }
        },
        {
            key: "initialize",
            value: function initialize() {}
        },
        {
            key: "connect",
            value: function connect() {}
        },
        {
            key: "disconnect",
            value: function disconnect() {}
        },
        {
            key: "dispatch",
            value: function dispatch(eventName) {
                var ref = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {}, _target = ref.target, target = _target === void 0 ? this.element : _target, _detail = ref.detail, detail = _detail === void 0 ? {} : _detail, _prefix = ref.prefix, prefix = _prefix === void 0 ? this.identifier : _prefix, _bubbles = ref.bubbles, bubbles = _bubbles === void 0 ? true : _bubbles, _cancelable = ref.cancelable, cancelable = _cancelable === void 0 ? true : _cancelable;
                var type = prefix ? "".concat(prefix, ":").concat(eventName) : eventName;
                var event = new CustomEvent(type, {
                    detail: detail,
                    bubbles: bubbles,
                    cancelable: cancelable
                });
                target.dispatchEvent(event);
                return event;
            }
        }
    ], [
        {
            key: "shouldLoad",
            get: function get() {
                return true;
            }
        }
    ]);
    return Controller;
}();
Controller.blessings = [
    ClassPropertiesBlessing,
    TargetPropertiesBlessing,
    ValuePropertiesBlessing
];
Controller.targets = [];
Controller.values = {};

},{"@swc/helpers/lib/_async_to_generator.js":"fKf1r","@swc/helpers/lib/_class_call_check.js":"gNxF8","@swc/helpers/lib/_create_class.js":"iyoaN","@swc/helpers/lib/_define_property.js":"6IXzf","@swc/helpers/lib/_get.js":"5g4pb","@swc/helpers/lib/_get_prototype_of.js":"7Gb6H","@swc/helpers/lib/_inherits.js":"atvDk","@swc/helpers/lib/_sliced_to_array.js":"4IWLM","@swc/helpers/lib/_to_consumable_array.js":"cccKv","@swc/helpers/lib/_type_of.js":"9FF45","@swc/helpers/lib/_create_super.js":"5rW3S","regenerator-runtime":"7j2bv","@parcel/transformer-js/src/esmodule-helpers.js":"jIm8e"}],"fKf1r":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _asyncToGenerator;
function _asyncToGenerator(fn) {
    return function() {
        var self = this, args = arguments;
        return new Promise(function(resolve, reject) {
            var gen = fn.apply(self, args);
            function _next(value) {
                asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
            }
            function _throw(err) {
                asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
            }
            _next(undefined);
        });
    };
}
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
    try {
        var info = gen[key](arg);
        var value = info.value;
    } catch (error) {
        reject(error);
        return;
    }
    if (info.done) resolve(value);
    else Promise.resolve(value).then(_next, _throw);
}

},{}],"gNxF8":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _classCallCheck;
function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) throw new TypeError("Cannot call a class as a function");
}

},{}],"iyoaN":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _createClass;
function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    return Constructor;
}
function _defineProperties(target, props) {
    for(var i = 0; i < props.length; i++){
        var descriptor = props[i];
        descriptor.enumerable = descriptor.enumerable || false;
        descriptor.configurable = true;
        if ("value" in descriptor) descriptor.writable = true;
        Object.defineProperty(target, descriptor.key, descriptor);
    }
}

},{}],"6IXzf":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _defineProperty;
function _defineProperty(obj, key, value) {
    if (key in obj) Object.defineProperty(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
    });
    else obj[key] = value;
    return obj;
}

},{}],"5g4pb":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _get;
var _superPropBase = _interopRequireDefault(require("./_super_prop_base"));
function _get(target, property, receiver) {
    return get(target, property, receiver);
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}
function get(target1, property1, receiver1) {
    if (typeof Reflect !== "undefined" && Reflect.get) get = Reflect.get;
    else get = function get(target, property, receiver) {
        var base = _superPropBase.default(target, property);
        if (!base) return;
        var desc = Object.getOwnPropertyDescriptor(base, property);
        if (desc.get) return desc.get.call(receiver || target);
        return desc.value;
    };
    return get(target1, property1, receiver1);
}

},{"./_super_prop_base":"cT49D"}],"cT49D":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _superPropBase;
var _getPrototypeOf = _interopRequireDefault(require("./_get_prototype_of"));
function _superPropBase(object, property) {
    while(!Object.prototype.hasOwnProperty.call(object, property)){
        object = _getPrototypeOf.default(object);
        if (object === null) break;
    }
    return object;
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}

},{"./_get_prototype_of":"7Gb6H"}],"7Gb6H":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _getPrototypeOf;
function _getPrototypeOf(o) {
    return getPrototypeOf(o);
}
function getPrototypeOf(o1) {
    getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function getPrototypeOf(o) {
        return o.__proto__ || Object.getPrototypeOf(o);
    };
    return getPrototypeOf(o1);
}

},{}],"atvDk":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _inherits;
var _setPrototypeOf = _interopRequireDefault(require("./_set_prototype_of"));
function _inherits(subClass, superClass) {
    if (typeof superClass !== "function" && superClass !== null) throw new TypeError("Super expression must either be null or a function");
    subClass.prototype = Object.create(superClass && superClass.prototype, {
        constructor: {
            value: subClass,
            writable: true,
            configurable: true
        }
    });
    if (superClass) _setPrototypeOf.default(subClass, superClass);
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}

},{"./_set_prototype_of":"1rATD"}],"1rATD":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _setPrototypeOf;
function _setPrototypeOf(o, p) {
    return setPrototypeOf(o, p);
}
function setPrototypeOf(o1, p1) {
    setPrototypeOf = Object.setPrototypeOf || function setPrototypeOf(o, p) {
        o.__proto__ = p;
        return o;
    };
    return setPrototypeOf(o1, p1);
}

},{}],"4IWLM":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _slicedToArray;
var _arrayWithHoles = _interopRequireDefault(require("./_array_with_holes"));
var _iterableToArray = _interopRequireDefault(require("./_iterable_to_array"));
var _nonIterableRest = _interopRequireDefault(require("./_non_iterable_rest"));
var _unsupportedIterableToArray = _interopRequireDefault(require("./_unsupported_iterable_to_array"));
function _slicedToArray(arr, i) {
    return _arrayWithHoles.default(arr) || _iterableToArray.default(arr, i) || _unsupportedIterableToArray.default(arr, i) || _nonIterableRest.default();
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}

},{"./_array_with_holes":"kAkr9","./_iterable_to_array":"d0B07","./_non_iterable_rest":"bXNgi","./_unsupported_iterable_to_array":"jhPJb"}],"kAkr9":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _arrayWithHoles;
function _arrayWithHoles(arr) {
    if (Array.isArray(arr)) return arr;
}

},{}],"d0B07":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _iterableToArray;
function _iterableToArray(iter) {
    if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
}

},{}],"bXNgi":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _nonIterableRest;
function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

},{}],"jhPJb":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _unsupportedIterableToArray;
var _arrayLikeToArray = _interopRequireDefault(require("./_array_like_to_array"));
function _unsupportedIterableToArray(o, minLen) {
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray.default(o, minLen);
    var n = Object.prototype.toString.call(o).slice(8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return Array.from(n);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray.default(o, minLen);
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}

},{"./_array_like_to_array":"4K9fh"}],"4K9fh":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _arrayLikeToArray;
function _arrayLikeToArray(arr, len) {
    if (len == null || len > arr.length) len = arr.length;
    for(var i = 0, arr2 = new Array(len); i < len; i++)arr2[i] = arr[i];
    return arr2;
}

},{}],"cccKv":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _toConsumableArray;
var _arrayWithoutHoles = _interopRequireDefault(require("./_array_without_holes"));
var _iterableToArray = _interopRequireDefault(require("./_iterable_to_array"));
var _nonIterableSpread = _interopRequireDefault(require("./_non_iterable_spread"));
var _unsupportedIterableToArray = _interopRequireDefault(require("./_unsupported_iterable_to_array"));
function _toConsumableArray(arr) {
    return _arrayWithoutHoles.default(arr) || _iterableToArray.default(arr) || _unsupportedIterableToArray.default(arr) || _nonIterableSpread.default();
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}

},{"./_array_without_holes":"26osg","./_iterable_to_array":"d0B07","./_non_iterable_spread":"nlNPL","./_unsupported_iterable_to_array":"jhPJb"}],"26osg":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _arrayWithoutHoles;
var _arrayLikeToArray = _interopRequireDefault(require("./_array_like_to_array"));
function _arrayWithoutHoles(arr) {
    if (Array.isArray(arr)) return _arrayLikeToArray.default(arr);
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}

},{"./_array_like_to_array":"4K9fh"}],"nlNPL":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _nonIterableSpread;
function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

},{}],"9FF45":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _typeof;
function _typeof(obj) {
    "@swc/helpers - typeof";
    return obj && obj.constructor === Symbol ? "symbol" : typeof obj;
}

},{}],"5rW3S":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _createSuper;
var _isNativeReflectConstruct = _interopRequireDefault(require("./_is_native_reflect_construct"));
var _getPrototypeOf = _interopRequireDefault(require("./_get_prototype_of"));
var _possibleConstructorReturn = _interopRequireDefault(require("./_possible_constructor_return"));
function _createSuper(Derived) {
    var hasNativeReflectConstruct = _isNativeReflectConstruct.default();
    return function _createSuperInternal() {
        var Super = _getPrototypeOf.default(Derived), result;
        if (hasNativeReflectConstruct) {
            var NewTarget = _getPrototypeOf.default(this).constructor;
            result = Reflect.construct(Super, arguments, NewTarget);
        } else result = Super.apply(this, arguments);
        return _possibleConstructorReturn.default(this, result);
    };
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}

},{"./_is_native_reflect_construct":"aPH71","./_get_prototype_of":"7Gb6H","./_possible_constructor_return":"hAvqf"}],"aPH71":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _isNativeReflectConstruct;
function _isNativeReflectConstruct() {
    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
    if (Reflect.construct.sham) return false;
    if (typeof Proxy === "function") return true;
    try {
        Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function() {}));
        return true;
    } catch (e) {
        return false;
    }
}

},{}],"hAvqf":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _possibleConstructorReturn;
var _assertThisInitialized = _interopRequireDefault(require("./_assert_this_initialized"));
var _typeOf = _interopRequireDefault(require("./_type_of"));
function _possibleConstructorReturn(self, call) {
    if (call && (_typeOf.default(call) === "object" || typeof call === "function")) return call;
    return _assertThisInitialized.default(self);
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}

},{"./_assert_this_initialized":"l7nF8","./_type_of":"9FF45"}],"l7nF8":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _assertThisInitialized;
function _assertThisInitialized(self) {
    if (self === void 0) throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    return self;
}

},{}],"7j2bv":[function(require,module,exports) {
/**
 * Copyright (c) 2014-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */ var runtime = function(exports) {
    "use strict";
    var Op = Object.prototype;
    var hasOwn = Op.hasOwnProperty;
    var undefined; // More compressible than void 0.
    var $Symbol = typeof Symbol === "function" ? Symbol : {};
    var iteratorSymbol = $Symbol.iterator || "@@iterator";
    var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
    var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";
    function define(obj, key, value) {
        Object.defineProperty(obj, key, {
            value: value,
            enumerable: true,
            configurable: true,
            writable: true
        });
        return obj[key];
    }
    try {
        // IE 8 has a broken Object.defineProperty that only works on DOM objects.
        define({}, "");
    } catch (err1) {
        define = function define(obj, key, value) {
            return obj[key] = value;
        };
    }
    function wrap(innerFn, outerFn, self, tryLocsList) {
        // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
        var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
        var generator = Object.create(protoGenerator.prototype);
        var context = new Context(tryLocsList || []);
        // The ._invoke method unifies the implementations of the .next,
        // .throw, and .return methods.
        generator._invoke = makeInvokeMethod(innerFn, self, context);
        return generator;
    }
    exports.wrap = wrap;
    // Try/catch helper to minimize deoptimizations. Returns a completion
    // record like context.tryEntries[i].completion. This interface could
    // have been (and was previously) designed to take a closure to be
    // invoked without arguments, but in all the cases we care about we
    // already have an existing method we want to call, so there's no need
    // to create a new function object. We can even get away with assuming
    // the method takes exactly one argument, since that happens to be true
    // in every case, so we don't have to touch the arguments object. The
    // only additional allocation required is the completion record, which
    // has a stable shape and so hopefully should be cheap to allocate.
    function tryCatch(fn, obj, arg) {
        try {
            return {
                type: "normal",
                arg: fn.call(obj, arg)
            };
        } catch (err) {
            return {
                type: "throw",
                arg: err
            };
        }
    }
    var GenStateSuspendedStart = "suspendedStart";
    var GenStateSuspendedYield = "suspendedYield";
    var GenStateExecuting = "executing";
    var GenStateCompleted = "completed";
    // Returning this object from the innerFn has the same effect as
    // breaking out of the dispatch switch statement.
    var ContinueSentinel = {};
    // Dummy constructor functions that we use as the .constructor and
    // .constructor.prototype properties for functions that return Generator
    // objects. For full spec compliance, you may wish to configure your
    // minifier not to mangle the names of these two functions.
    function Generator() {}
    function GeneratorFunction() {}
    function GeneratorFunctionPrototype() {}
    // This is a polyfill for %IteratorPrototype% for environments that
    // don't natively support it.
    var IteratorPrototype = {};
    define(IteratorPrototype, iteratorSymbol, function() {
        return this;
    });
    var getProto = Object.getPrototypeOf;
    var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
    if (NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) // This environment has a native %IteratorPrototype%; use it instead
    // of the polyfill.
    IteratorPrototype = NativeIteratorPrototype;
    var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype);
    GeneratorFunction.prototype = GeneratorFunctionPrototype;
    define(Gp, "constructor", GeneratorFunctionPrototype);
    define(GeneratorFunctionPrototype, "constructor", GeneratorFunction);
    GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction");
    // Helper for defining the .next, .throw, and .return methods of the
    // Iterator interface in terms of a single ._invoke method.
    function defineIteratorMethods(prototype) {
        [
            "next",
            "throw",
            "return"
        ].forEach(function(method) {
            define(prototype, method, function(arg) {
                return this._invoke(method, arg);
            });
        });
    }
    exports.isGeneratorFunction = function(genFun) {
        var ctor = typeof genFun === "function" && genFun.constructor;
        return ctor ? ctor === GeneratorFunction || // For the native GeneratorFunction constructor, the best we can
        // do is to check its .name property.
        (ctor.displayName || ctor.name) === "GeneratorFunction" : false;
    };
    exports.mark = function(genFun) {
        if (Object.setPrototypeOf) Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
        else {
            genFun.__proto__ = GeneratorFunctionPrototype;
            define(genFun, toStringTagSymbol, "GeneratorFunction");
        }
        genFun.prototype = Object.create(Gp);
        return genFun;
    };
    // Within the body of any async function, `await x` is transformed to
    // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
    // `hasOwn.call(value, "__await")` to determine if the yielded value is
    // meant to be awaited.
    exports.awrap = function(arg) {
        return {
            __await: arg
        };
    };
    function AsyncIterator(generator, PromiseImpl) {
        function invoke(method, arg, resolve, reject) {
            var record = tryCatch(generator[method], generator, arg);
            if (record.type === "throw") reject(record.arg);
            else {
                var result = record.arg;
                var value1 = result.value;
                if (value1 && typeof value1 === "object" && hasOwn.call(value1, "__await")) return PromiseImpl.resolve(value1.__await).then(function(value) {
                    invoke("next", value, resolve, reject);
                }, function(err) {
                    invoke("throw", err, resolve, reject);
                });
                return PromiseImpl.resolve(value1).then(function(unwrapped) {
                    // When a yielded Promise is resolved, its final value becomes
                    // the .value of the Promise<{value,done}> result for the
                    // current iteration.
                    result.value = unwrapped;
                    resolve(result);
                }, function(error) {
                    // If a rejected Promise was yielded, throw the rejection back
                    // into the async generator function so it can be handled there.
                    return invoke("throw", error, resolve, reject);
                });
            }
        }
        var previousPromise;
        function enqueue(method, arg) {
            function callInvokeWithMethodAndArg() {
                return new PromiseImpl(function(resolve, reject) {
                    invoke(method, arg, resolve, reject);
                });
            }
            return previousPromise = // If enqueue has been called before, then we want to wait until
            // all previous Promises have been resolved before calling invoke,
            // so that results are always delivered in the correct order. If
            // enqueue has not been called before, then it is important to
            // call invoke immediately, without waiting on a callback to fire,
            // so that the async generator function has the opportunity to do
            // any necessary setup in a predictable way. This predictability
            // is why the Promise constructor synchronously invokes its
            // executor callback, and why async functions synchronously
            // execute code before the first await. Since we implement simple
            // async functions in terms of async generators, it is especially
            // important to get this right, even though it requires care.
            previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, // Avoid propagating failures to Promises returned by later
            // invocations of the iterator.
            callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg();
        }
        // Define the unified helper method that is used to implement .next,
        // .throw, and .return (see defineIteratorMethods).
        this._invoke = enqueue;
    }
    defineIteratorMethods(AsyncIterator.prototype);
    define(AsyncIterator.prototype, asyncIteratorSymbol, function() {
        return this;
    });
    exports.AsyncIterator = AsyncIterator;
    // Note that simple async functions are implemented on top of
    // AsyncIterator objects; they just return a Promise for the value of
    // the final result produced by the iterator.
    exports.async = function(innerFn, outerFn, self, tryLocsList, PromiseImpl) {
        if (PromiseImpl === void 0) PromiseImpl = Promise;
        var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl);
        return exports.isGeneratorFunction(outerFn) ? iter // If outerFn is a generator, return the full iterator.
         : iter.next().then(function(result) {
            return result.done ? result.value : iter.next();
        });
    };
    function makeInvokeMethod(innerFn, self, context) {
        var state = GenStateSuspendedStart;
        return function invoke(method, arg) {
            if (state === GenStateExecuting) throw new Error("Generator is already running");
            if (state === GenStateCompleted) {
                if (method === "throw") throw arg;
                // Be forgiving, per 25.3.3.3.3 of the spec:
                // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
                return doneResult();
            }
            context.method = method;
            context.arg = arg;
            while(true){
                var delegate = context.delegate;
                if (delegate) {
                    var delegateResult = maybeInvokeDelegate(delegate, context);
                    if (delegateResult) {
                        if (delegateResult === ContinueSentinel) continue;
                        return delegateResult;
                    }
                }
                if (context.method === "next") // Setting context._sent for legacy support of Babel's
                // function.sent implementation.
                context.sent = context._sent = context.arg;
                else if (context.method === "throw") {
                    if (state === GenStateSuspendedStart) {
                        state = GenStateCompleted;
                        throw context.arg;
                    }
                    context.dispatchException(context.arg);
                } else if (context.method === "return") context.abrupt("return", context.arg);
                state = GenStateExecuting;
                var record = tryCatch(innerFn, self, context);
                if (record.type === "normal") {
                    // If an exception is thrown from innerFn, we leave state ===
                    // GenStateExecuting and loop back for another invocation.
                    state = context.done ? GenStateCompleted : GenStateSuspendedYield;
                    if (record.arg === ContinueSentinel) continue;
                    return {
                        value: record.arg,
                        done: context.done
                    };
                } else if (record.type === "throw") {
                    state = GenStateCompleted;
                    // Dispatch the exception by looping back around to the
                    // context.dispatchException(context.arg) call above.
                    context.method = "throw";
                    context.arg = record.arg;
                }
            }
        };
    }
    // Call delegate.iterator[context.method](context.arg) and handle the
    // result, either by returning a { value, done } result from the
    // delegate iterator, or by modifying context.method and context.arg,
    // setting context.delegate to null, and returning the ContinueSentinel.
    function maybeInvokeDelegate(delegate, context) {
        var method = delegate.iterator[context.method];
        if (method === undefined) {
            // A .throw or .return when the delegate iterator has no .throw
            // method always terminates the yield* loop.
            context.delegate = null;
            if (context.method === "throw") {
                // Note: ["return"] must be used for ES3 parsing compatibility.
                if (delegate.iterator["return"]) {
                    // If the delegate iterator has a return method, give it a
                    // chance to clean up.
                    context.method = "return";
                    context.arg = undefined;
                    maybeInvokeDelegate(delegate, context);
                    if (context.method === "throw") // If maybeInvokeDelegate(context) changed context.method from
                    // "return" to "throw", let that override the TypeError below.
                    return ContinueSentinel;
                }
                context.method = "throw";
                context.arg = new TypeError("The iterator does not provide a 'throw' method");
            }
            return ContinueSentinel;
        }
        var record = tryCatch(method, delegate.iterator, context.arg);
        if (record.type === "throw") {
            context.method = "throw";
            context.arg = record.arg;
            context.delegate = null;
            return ContinueSentinel;
        }
        var info = record.arg;
        if (!info) {
            context.method = "throw";
            context.arg = new TypeError("iterator result is not an object");
            context.delegate = null;
            return ContinueSentinel;
        }
        if (info.done) {
            // Assign the result of the finished delegate to the temporary
            // variable specified by delegate.resultName (see delegateYield).
            context[delegate.resultName] = info.value;
            // Resume execution at the desired location (see delegateYield).
            context.next = delegate.nextLoc;
            // If context.method was "throw" but the delegate handled the
            // exception, let the outer generator proceed normally. If
            // context.method was "next", forget context.arg since it has been
            // "consumed" by the delegate iterator. If context.method was
            // "return", allow the original .return call to continue in the
            // outer generator.
            if (context.method !== "return") {
                context.method = "next";
                context.arg = undefined;
            }
        } else // Re-yield the result returned by the delegate method.
        return info;
        // The delegate iterator is finished, so forget it and continue with
        // the outer generator.
        context.delegate = null;
        return ContinueSentinel;
    }
    // Define Generator.prototype.{next,throw,return} in terms of the
    // unified ._invoke helper method.
    defineIteratorMethods(Gp);
    define(Gp, toStringTagSymbol, "Generator");
    // A Generator should always return itself as the iterator object when the
    // @@iterator function is called on it. Some browsers' implementations of the
    // iterator prototype chain incorrectly implement this, causing the Generator
    // object to not be returned from this call. This ensures that doesn't happen.
    // See https://github.com/facebook/regenerator/issues/274 for more details.
    define(Gp, iteratorSymbol, function() {
        return this;
    });
    define(Gp, "toString", function() {
        return "[object Generator]";
    });
    function pushTryEntry(locs) {
        var entry = {
            tryLoc: locs[0]
        };
        if (1 in locs) entry.catchLoc = locs[1];
        if (2 in locs) {
            entry.finallyLoc = locs[2];
            entry.afterLoc = locs[3];
        }
        this.tryEntries.push(entry);
    }
    function resetTryEntry(entry) {
        var record = entry.completion || {};
        record.type = "normal";
        delete record.arg;
        entry.completion = record;
    }
    function Context(tryLocsList) {
        // The root entry object (effectively a try statement without a catch
        // or a finally block) gives us a place to store values thrown from
        // locations where there is no enclosing try statement.
        this.tryEntries = [
            {
                tryLoc: "root"
            }
        ];
        tryLocsList.forEach(pushTryEntry, this);
        this.reset(true);
    }
    exports.keys = function(object) {
        var keys = [];
        for(var key1 in object)keys.push(key1);
        keys.reverse();
        // Rather than returning an object with a next method, we keep
        // things simple and return the next function itself.
        return function next() {
            while(keys.length){
                var key = keys.pop();
                if (key in object) {
                    next.value = key;
                    next.done = false;
                    return next;
                }
            }
            // To avoid creating an additional object, we just hang the .value
            // and .done properties off the next function object itself. This
            // also ensures that the minifier will not anonymize the function.
            next.done = true;
            return next;
        };
    };
    function values(iterable) {
        if (iterable) {
            var iteratorMethod = iterable[iteratorSymbol];
            if (iteratorMethod) return iteratorMethod.call(iterable);
            if (typeof iterable.next === "function") return iterable;
            if (!isNaN(iterable.length)) {
                var i = -1, next1 = function next() {
                    while(++i < iterable.length)if (hasOwn.call(iterable, i)) {
                        next.value = iterable[i];
                        next.done = false;
                        return next;
                    }
                    next.value = undefined;
                    next.done = true;
                    return next;
                };
                return next1.next = next1;
            }
        }
        // Return an iterator with no values.
        return {
            next: doneResult
        };
    }
    exports.values = values;
    function doneResult() {
        return {
            value: undefined,
            done: true
        };
    }
    Context.prototype = {
        constructor: Context,
        reset: function reset(skipTempReset) {
            this.prev = 0;
            this.next = 0;
            // Resetting context._sent for legacy support of Babel's
            // function.sent implementation.
            this.sent = this._sent = undefined;
            this.done = false;
            this.delegate = null;
            this.method = "next";
            this.arg = undefined;
            this.tryEntries.forEach(resetTryEntry);
            if (!skipTempReset) {
                for(var name in this)// Not sure about the optimal order of these conditions:
                if (name.charAt(0) === "t" && hasOwn.call(this, name) && !isNaN(+name.slice(1))) this[name] = undefined;
            }
        },
        stop: function stop() {
            this.done = true;
            var rootEntry = this.tryEntries[0];
            var rootRecord = rootEntry.completion;
            if (rootRecord.type === "throw") throw rootRecord.arg;
            return this.rval;
        },
        dispatchException: function dispatchException(exception) {
            if (this.done) throw exception;
            var context = this;
            function handle(loc, caught) {
                record.type = "throw";
                record.arg = exception;
                context.next = loc;
                if (caught) {
                    // If the dispatched exception was caught by a catch block,
                    // then let that catch block handle the exception normally.
                    context.method = "next";
                    context.arg = undefined;
                }
                return !!caught;
            }
            for(var i = this.tryEntries.length - 1; i >= 0; --i){
                var entry = this.tryEntries[i];
                var record = entry.completion;
                if (entry.tryLoc === "root") // Exception thrown outside of any try block that could handle
                // it, so set the completion value of the entire function to
                // throw the exception.
                return handle("end");
                if (entry.tryLoc <= this.prev) {
                    var hasCatch = hasOwn.call(entry, "catchLoc");
                    var hasFinally = hasOwn.call(entry, "finallyLoc");
                    if (hasCatch && hasFinally) {
                        if (this.prev < entry.catchLoc) return handle(entry.catchLoc, true);
                        else if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
                    } else if (hasCatch) {
                        if (this.prev < entry.catchLoc) return handle(entry.catchLoc, true);
                    } else if (hasFinally) {
                        if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
                    } else throw new Error("try statement without catch or finally");
                }
            }
        },
        abrupt: function abrupt(type, arg) {
            for(var i = this.tryEntries.length - 1; i >= 0; --i){
                var entry = this.tryEntries[i];
                if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) {
                    var finallyEntry = entry;
                    break;
                }
            }
            if (finallyEntry && (type === "break" || type === "continue") && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc) // Ignore the finally entry if control is not jumping to a
            // location outside the try/catch block.
            finallyEntry = null;
            var record = finallyEntry ? finallyEntry.completion : {};
            record.type = type;
            record.arg = arg;
            if (finallyEntry) {
                this.method = "next";
                this.next = finallyEntry.finallyLoc;
                return ContinueSentinel;
            }
            return this.complete(record);
        },
        complete: function complete(record, afterLoc) {
            if (record.type === "throw") throw record.arg;
            if (record.type === "break" || record.type === "continue") this.next = record.arg;
            else if (record.type === "return") {
                this.rval = this.arg = record.arg;
                this.method = "return";
                this.next = "end";
            } else if (record.type === "normal" && afterLoc) this.next = afterLoc;
            return ContinueSentinel;
        },
        finish: function finish(finallyLoc) {
            for(var i = this.tryEntries.length - 1; i >= 0; --i){
                var entry = this.tryEntries[i];
                if (entry.finallyLoc === finallyLoc) {
                    this.complete(entry.completion, entry.afterLoc);
                    resetTryEntry(entry);
                    return ContinueSentinel;
                }
            }
        },
        "catch": function(tryLoc) {
            for(var i = this.tryEntries.length - 1; i >= 0; --i){
                var entry = this.tryEntries[i];
                if (entry.tryLoc === tryLoc) {
                    var record = entry.completion;
                    if (record.type === "throw") {
                        var thrown = record.arg;
                        resetTryEntry(entry);
                    }
                    return thrown;
                }
            }
            // The context.catch method must only be called with a location
            // argument that corresponds to a known catch block.
            throw new Error("illegal catch attempt");
        },
        delegateYield: function delegateYield(iterable, resultName, nextLoc) {
            this.delegate = {
                iterator: values(iterable),
                resultName: resultName,
                nextLoc: nextLoc
            };
            if (this.method === "next") // Deliberately forget the last sent value so that we don't
            // accidentally pass it on to the delegate.
            this.arg = undefined;
            return ContinueSentinel;
        }
    };
    // Regardless of whether this script is executing as a CommonJS module
    // or not, return the runtime object so that we can declare the variable
    // regeneratorRuntime in the outer scope, which allows this module to be
    // injected easily by `bin/regenerator --include-runtime script.js`.
    return exports;
}(module.exports);
try {
    regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
    // This module should not be running in strict mode, so the above
    // assignment should always work unless something is misconfigured. Just
    // in case runtime.js accidentally runs in strict mode, in modern engines
    // we can explicitly access globalThis. In older engines we can escape
    // strict mode using a global Function call. This could conceivably fail
    // if a Content Security Policy forbids using Function, but in that case
    // the proper solution is to fix the accidental strict mode problem. If
    // you've misconfigured your bundler to force strict mode and applied a
    // CSP to forbid Function, and you're not willing to fix either of those
    // problems, please detail your unique predicament in a GitHub issue.
    if (typeof globalThis === "object") globalThis.regeneratorRuntime = runtime;
    else Function("r", "regeneratorRuntime = r")(runtime);
}

},{}],"jIm8e":[function(require,module,exports) {
exports.interopDefault = function(a) {
    return a && a.__esModule ? a : {
        default: a
    };
};
exports.defineInteropFlag = function(a) {
    Object.defineProperty(a, "__esModule", {
        value: true
    });
};
exports.exportAll = function(source, dest) {
    Object.keys(source).forEach(function(key) {
        if (key === "default" || key === "__esModule" || dest.hasOwnProperty(key)) return;
        Object.defineProperty(dest, key, {
            enumerable: true,
            get: function get() {
                return source[key];
            }
        });
    });
    return dest;
};
exports.export = function(dest, destName, get) {
    Object.defineProperty(dest, destName, {
        enumerable: true,
        get: get
    });
};

},{}],"irT0c":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "default", function() {
    return _class;
});
var _assertThisInitializedJs = require("@swc/helpers/lib/_assert_this_initialized.js");
var _assertThisInitializedJsDefault = parcelHelpers.interopDefault(_assertThisInitializedJs);
var _classCallCheckJs = require("@swc/helpers/lib/_class_call_check.js");
var _classCallCheckJsDefault = parcelHelpers.interopDefault(_classCallCheckJs);
var _createClassJs = require("@swc/helpers/lib/_create_class.js");
var _createClassJsDefault = parcelHelpers.interopDefault(_createClassJs);
var _definePropertyJs = require("@swc/helpers/lib/_define_property.js");
var _definePropertyJsDefault = parcelHelpers.interopDefault(_definePropertyJs);
var _inheritsJs = require("@swc/helpers/lib/_inherits.js");
var _inheritsJsDefault = parcelHelpers.interopDefault(_inheritsJs);
var _toConsumableArrayJs = require("@swc/helpers/lib/_to_consumable_array.js");
var _toConsumableArrayJsDefault = parcelHelpers.interopDefault(_toConsumableArrayJs);
var _createSuperJs = require("@swc/helpers/lib/_create_super.js");
var _createSuperJsDefault = parcelHelpers.interopDefault(_createSuperJs);
var _stimulus = require("@hotwired/stimulus");
var _corsairPlugin = require("../chart_plugins/corsair_plugin");
var _corsairPluginDefault = parcelHelpers.interopDefault(_corsairPlugin);
var _htmlLegendPlugin = require("../chart_plugins/html_legend_plugin");
var _htmlLegendPluginDefault = parcelHelpers.interopDefault(_htmlLegendPlugin);
var _chartJs = require("chart.js");
var _Chart;
(_Chart = (0, _chartJs.Chart)).register.apply(_Chart, (0, _toConsumableArrayJsDefault.default)((0, _chartJs.registerables)));
var _class = /*#__PURE__*/ function(Controller) {
    "use strict";
    (0, _inheritsJsDefault.default)(_class, Controller);
    var _super = (0, _createSuperJsDefault.default)(_class);
    function _class() {
        (0, _classCallCheckJsDefault.default)(this, _class);
        var _this;
        _this = _super.apply(this, arguments);
        (0, _definePropertyJsDefault.default)((0, _assertThisInitializedJsDefault.default)(_this), "currencyTickText", function(value) {
            return _this.formatCurrency(value);
        });
        return _this;
    }
    (0, _createClassJsDefault.default)(_class, [
        {
            key: "locale",
            get: function get() {
                try {
                    new Intl.NumberFormat(this.localeValue);
                    return this.localeValue;
                } catch (e) {
                    return "en-US";
                }
            }
        },
        {
            key: "isPreview",
            value: function isPreview() {
                return this.previewValue === true;
            }
        },
        {
            key: "isUsingWooCommerce",
            value: function isUsingWooCommerce() {
                return this.usingWooCommerceValue === true;
            }
        },
        {
            key: "formatCurrency",
            value: function formatCurrency(value) {
                return new Intl.NumberFormat(this.localeValue, {
                    style: "currency",
                    currency: this.currencyValue,
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(value);
            }
        },
        {
            key: "tooltipTitle",
            value: function tooltipTitle(tooltip) {
                var label = JSON.parse(tooltip[0].label);
                return label.tooltipLabel;
            }
        },
        {
            key: "tooltipLabel",
            value: function tooltipLabel(tooltip) {
                if (typeof tooltip.dataset.tooltipLabel === "function") return tooltip.dataset.tooltipLabel(tooltip);
                return tooltip.dataset.label + ": " + tooltip.formattedValue;
            }
        },
        {
            key: "tickText",
            value: function tickText(value) {
                var label = JSON.parse(this.getLabelForValue(value));
                return label.tick;
            }
        },
        {
            key: "getVisitorsDataset",
            value: function getVisitorsDataset() {
                return {
                    id: "visitors",
                    label: iawpText.visitors,
                    data: this.visitorsValue,
                    borderColor: "rgba(246,157,10,1)",
                    fill: true,
                    backgroundColor: "rgba(246,157,10,0.2)",
                    pointBackgroundColor: "rgba(246,157,10,1)",
                    tension: 0.4,
                    yAxisID: "y",
                    hidden: !this.visibleDatasetsValue.includes("visitors")
                };
            }
        },
        {
            key: "getViewsDataset",
            value: function getViewsDataset() {
                return {
                    id: "views",
                    label: iawpText.views,
                    data: this.viewsValue,
                    borderColor: "rgba(108,70,174,1)",
                    fill: true,
                    backgroundColor: "rgba(108,70,174,0.2)",
                    pointBackgroundColor: "rgba(108,70,174,1)",
                    tension: 0.4,
                    yAxisID: "y",
                    hidden: !this.visibleDatasetsValue.includes("views")
                };
            }
        },
        {
            key: "getSessionsDataset",
            value: function getSessionsDataset() {
                if (this.isPreview()) return null;
                return {
                    id: "sessions",
                    label: iawpText.sessions,
                    data: this.sessionsValue,
                    borderColor: "rgba(217, 59, 41, 1)",
                    fill: true,
                    backgroundColor: "rgba(217, 59, 41, 0.2)",
                    pointBackgroundColor: "rgba(217, 59, 41, 1)",
                    tension: 0.4,
                    yAxisID: "y",
                    hidden: !this.visibleDatasetsValue.includes("sessions")
                };
            }
        },
        {
            key: "getOrdersDataset",
            value: function getOrdersDataset() {
                if (this.isPreview() || !this.isUsingWooCommerce()) return null;
                return {
                    id: "orders",
                    label: iawpText.orders,
                    data: this.woocommerceOrdersValue,
                    borderColor: "rgba(35, 125, 68, 1)",
                    fill: true,
                    backgroundColor: "rgba(35, 125, 68, .2)",
                    pointBackgroundColor: "rgba(35, 125, 68, 1)",
                    tension: 0.4,
                    yAxisID: "y1",
                    hidden: !this.visibleDatasetsValue.includes("orders")
                };
            }
        },
        {
            key: "getNetSalesDataset",
            value: function getNetSalesDataset() {
                var _this = this;
                if (this.isPreview() || !this.isUsingWooCommerce()) return null;
                return {
                    id: "net-sales",
                    label: iawpText.netSales,
                    data: this.woocommerceNetSalesValue,
                    borderColor: "rgba(52, 152, 219, 1)",
                    fill: true,
                    backgroundColor: "rgba(52, 152, 219, 0.2)",
                    pointBackgroundColor: "rgba(52, 152, 219, 1)",
                    tension: 0.4,
                    yAxisID: "y2",
                    tooltipLabel: function(tooltip) {
                        return tooltip.dataset.label + ": " + _this.formatCurrency(tooltip.raw);
                    },
                    hidden: !this.visibleDatasetsValue.includes("net-sales")
                };
            }
        },
        {
            key: "connect",
            value: function connect() {
                (0, _chartJs.Chart).defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"';
                var element = document.getElementById("myChart");
                var labels = this.labelsValue;
                var data = {
                    labels: labels,
                    datasets: [
                        this.getVisitorsDataset(),
                        this.getViewsDataset(),
                        this.getSessionsDataset(),
                        this.getOrdersDataset(),
                        this.getNetSalesDataset()
                    ].filter(function(dataset) {
                        return dataset !== null;
                    })
                };
                var options = {
                    locale: this.locale,
                    animation: {
                        duration: 0
                    },
                    interaction: {
                        intersect: false,
                        mode: "index"
                    },
                    scales: {
                        y: {
                            title: {
                                text: "".concat(iawpText.visitors, " / ").concat(iawpText.views, " / ").concat(iawpText.sessions),
                                display: this.previewValue ? false : true
                            },
                            grid: {
                                borderColor: "#DEDAE6",
                                tickColor: "#DEDAE6",
                                display: true,
                                drawOnChartArea: true,
                                borderDash: [
                                    2,
                                    4
                                ]
                            },
                            beginAtZero: true,
                            suggestedMax: 10,
                            // grace: '26%',
                            ticks: {
                                color: document.body.classList.contains("iawp-dark-mode") ? "#ffffff" : "#6D6A73",
                                font: {
                                    size: 14,
                                    weight: 400
                                },
                                precision: 0
                            }
                        },
                        y1: {
                            title: {
                                text: iawpText.orders,
                                display: true,
                                color: "rgba(35, 125, 68, 1)"
                            },
                            position: "right",
                            display: "auto",
                            grid: {
                                borderColor: "rgba(35, 125, 68, 1)",
                                tickColor: "rgba(35, 125, 68, 1)",
                                display: false,
                                drawOnChartArea: false,
                                borderDash: [
                                    2,
                                    4
                                ]
                            },
                            beginAtZero: true,
                            suggestedMax: 10,
                            // grace: '26%',
                            ticks: {
                                // color: document.body.classList.contains('iawp-dark-mode') ? '#ffffff' : '#6D6A73',
                                color: "rgba(35, 125, 68, 1)",
                                font: {
                                    size: 14,
                                    weight: 400
                                },
                                precision: 0
                            }
                        },
                        y2: {
                            title: {
                                text: iawpText.netSales,
                                display: true,
                                color: "rgba(52, 152, 219, 1)"
                            },
                            position: "right",
                            display: "auto",
                            grid: {
                                borderColor: "rgba(52, 152, 219, 1)",
                                tickColor: "rgba(52, 152, 219, 1)",
                                display: false,
                                drawOnChartArea: false,
                                borderDash: [
                                    2,
                                    4
                                ]
                            },
                            beginAtZero: true,
                            suggestedMax: 10,
                            // grace: '26%',
                            ticks: {
                                // color: document.body.classList.contains('iawp-dark-mode') ? '#ffffff' : '#6D6A73',
                                color: "rgba(52, 152, 219, 1)",
                                font: {
                                    size: 14,
                                    weight: 400
                                },
                                precision: 0,
                                callback: this.currencyTickText
                            }
                        },
                        x: {
                            grid: {
                                borderColor: "#DEDAE6",
                                tickColor: "#DEDAE6",
                                display: true,
                                drawOnChartArea: false
                            },
                            ticks: {
                                color: document.body.classList.contains("iawp-dark-mode") ? "#ffffff" : "#6D6A73",
                                autoSkip: true,
                                autoSkipPadding: 16,
                                maxRotation: 0,
                                // maxTicksLimit: 20,
                                font: {
                                    size: 14,
                                    weight: 400
                                },
                                callback: this.tickText
                            }
                        }
                    },
                    plugins: {
                        mode: String,
                        htmlLegend: {
                            container: element.parentNode.querySelector(".legend"),
                            callback: function callback(visibleDatasets) {
                                // Todo - Actually track visible datasets
                                document.dispatchEvent(new CustomEvent("iawp:changeVisibleDatasets", {
                                    detail: {
                                        visibleDatasets: visibleDatasets
                                    }
                                }));
                            }
                        },
                        legend: {
                            display: false
                        },
                        corsair: {
                            dash: [
                                2,
                                4
                            ],
                            color: "#777",
                            width: 1
                        },
                        tooltip: {
                            callbacks: {
                                title: this.tooltipTitle,
                                label: this.tooltipLabel
                            }
                        }
                    },
                    elements: {
                        point: {
                            radius: 4
                        }
                    }
                };
                var config = {
                    type: "line",
                    data: data,
                    options: options,
                    plugins: [
                        (0, _htmlLegendPluginDefault.default),
                        (0, _corsairPluginDefault.default)
                    ]
                };
                window.iawp_chart = new (0, _chartJs.Chart)(element, config);
            }
        }
    ]);
    return _class;
}((0, _stimulus.Controller));
(0, _definePropertyJsDefault.default)(_class, "values", {
    locale: String,
    currency: {
        type: String,
        default: "USD"
    },
    preview: Boolean,
    usingWooCommerce: Boolean,
    labels: Array,
    views: Array,
    visitors: Array,
    sessions: Array,
    woocommerceOrders: Array,
    woocommerceNetSales: Array,
    visibleDatasets: Array
});

},{"@swc/helpers/lib/_assert_this_initialized.js":"l7nF8","@swc/helpers/lib/_class_call_check.js":"gNxF8","@swc/helpers/lib/_create_class.js":"iyoaN","@swc/helpers/lib/_define_property.js":"6IXzf","@swc/helpers/lib/_inherits.js":"atvDk","@swc/helpers/lib/_to_consumable_array.js":"cccKv","@swc/helpers/lib/_create_super.js":"5rW3S","@hotwired/stimulus":"27q4D","../chart_plugins/corsair_plugin":"1DRHy","../chart_plugins/html_legend_plugin":"3Qrst","chart.js":"h4klJ","@parcel/transformer-js/src/esmodule-helpers.js":"jIm8e"}],"1DRHy":[function(require,module,exports) {
module.exports = {
    id: "corsair",
    beforeInit: function(chart, _, opts) {
        if (opts.disabled) return;
        chart.corsair = {
            x: 0,
            y: 0
        };
    },
    afterEvent: function(chart, evt, opts) {
        if (opts.disabled) return;
        var _chartArea = chart.chartArea, top = _chartArea.top, bottom = _chartArea.bottom, left = _chartArea.left, right = _chartArea.right;
        var _event = evt.event, x = _event.x, y = _event.y;
        if (x < left || x > right || y < top || y > bottom) {
            chart.corsair = {
                x: x,
                y: y,
                draw: false
            };
            chart.draw();
            return;
        }
        chart.corsair = {
            x: x,
            y: y,
            draw: true
        };
        chart.draw();
    },
    afterDatasetsDraw: function(chart, _, opts) {
        if (opts.disabled) return;
        var ctx = chart.ctx, _chartArea = chart.chartArea, top = _chartArea.top, bottom = _chartArea.bottom, left = _chartArea.left, right = _chartArea.right;
        var _corsair = chart.corsair, x = _corsair.x, y = _corsair.y, draw = _corsair.draw;
        if (!draw) return;
        // console.log(chart);
        x = chart.tooltip.caretX;
        ctx.lineWidth = opts.width || 0;
        // // Todo - Why does dash fuck up dots?
        ctx.setLineDash(opts.dash || []);
        ctx.strokeStyle = opts.color || "black";
        ctx.save();
        ctx.beginPath();
        ctx.moveTo(x, bottom);
        ctx.lineTo(x, top);
        // Uncomment these 2 lines to add horizontal line
        // ctx.moveTo(left, y);
        // ctx.lineTo(right, y);
        ctx.stroke();
        ctx.restore();
        ctx.setLineDash([]);
    }
};

},{}],"3Qrst":[function(require,module,exports) {
module.exports = {
    id: "htmlLegend",
    getLegendContainer: function(options) {
        if (options.container instanceof HTMLElement) return options.container;
        else return document.getElementById(options.containerID);
    },
    afterUpdate: function(chart, args, options) {
        var legendContainer = this.getLegendContainer(options);
        var legendList = legendContainer.querySelector("ul");
        // Create a list as needed
        if (!legendList) {
            legendList = document.createElement("ul");
            legendList.classList.add("legend-list");
            legendContainer.appendChild(legendList);
        }
        // Remove old legend items
        while(legendList.firstChild)legendList.firstChild.remove();
        // Reuse the built-in legendItems generator
        var items = chart.options.plugins.legend.labels.generateLabels(chart);
        items.forEach(function(legendData) {
            var id = chart.data.datasets.find(function(dataset) {
                return dataset.label === legendData.text;
            }).id;
            var li = document.createElement("li");
            li.onclick = function() {
                var type = chart.config.type;
                if (type === "pie" || type === "doughnut") // Pie and doughnut charts only have a single dataset and visibility is per item
                chart.toggleDataVisibility(legendData.index);
                else chart.setDatasetVisibility(legendData.datasetIndex, !chart.isDatasetVisible(legendData.datasetIndex));
                chart.update();
                if (typeof options.callback === "function") {
                    var visibleDatasets = chart.data.datasets.filter(function(dataset, index) {
                        return chart.isDatasetVisible(index);
                    }).map(function(dataset) {
                        return dataset.id;
                    });
                    options.callback(visibleDatasets);
                }
            };
            li.classList.add("legend-item", "legend-item-for-".concat(id));
            if (legendData.hidden) li.classList.add("hidden");
            // Color box
            var boxSpan = document.createElement("span");
            // Text
            var textContainer = document.createElement("p");
            textContainer.textContent = legendData.text;
            li.appendChild(boxSpan);
            li.appendChild(textContainer);
            legendList.appendChild(li);
        });
    }
};

},{}],"h4klJ":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "defaults", function() {
    return 0, _helpersSegmentJs.d;
});
parcelHelpers.export(exports, "Animation", function() {
    return Animation;
});
parcelHelpers.export(exports, "Animations", function() {
    return Animations;
});
parcelHelpers.export(exports, "ArcElement", function() {
    return ArcElement;
});
parcelHelpers.export(exports, "BarController", function() {
    return BarController;
});
parcelHelpers.export(exports, "BarElement", function() {
    return BarElement;
});
parcelHelpers.export(exports, "BasePlatform", function() {
    return BasePlatform;
});
parcelHelpers.export(exports, "BasicPlatform", function() {
    return BasicPlatform;
});
parcelHelpers.export(exports, "BubbleController", function() {
    return BubbleController;
});
parcelHelpers.export(exports, "CategoryScale", function() {
    return CategoryScale;
});
parcelHelpers.export(exports, "Chart", function() {
    return Chart;
});
parcelHelpers.export(exports, "DatasetController", function() {
    return DatasetController;
});
parcelHelpers.export(exports, "Decimation", function() {
    return plugin_decimation;
});
parcelHelpers.export(exports, "DomPlatform", function() {
    return DomPlatform;
});
parcelHelpers.export(exports, "DoughnutController", function() {
    return DoughnutController;
});
parcelHelpers.export(exports, "Element", function() {
    return Element;
});
parcelHelpers.export(exports, "Filler", function() {
    return index;
});
parcelHelpers.export(exports, "Interaction", function() {
    return Interaction;
});
parcelHelpers.export(exports, "Legend", function() {
    return plugin_legend;
});
parcelHelpers.export(exports, "LineController", function() {
    return LineController;
});
parcelHelpers.export(exports, "LineElement", function() {
    return LineElement;
});
parcelHelpers.export(exports, "LinearScale", function() {
    return LinearScale;
});
parcelHelpers.export(exports, "LogarithmicScale", function() {
    return LogarithmicScale;
});
parcelHelpers.export(exports, "PieController", function() {
    return PieController;
});
parcelHelpers.export(exports, "PointElement", function() {
    return PointElement;
});
parcelHelpers.export(exports, "PolarAreaController", function() {
    return PolarAreaController;
});
parcelHelpers.export(exports, "RadarController", function() {
    return RadarController;
});
parcelHelpers.export(exports, "RadialLinearScale", function() {
    return RadialLinearScale;
});
parcelHelpers.export(exports, "Scale", function() {
    return Scale;
});
parcelHelpers.export(exports, "ScatterController", function() {
    return ScatterController;
});
parcelHelpers.export(exports, "SubTitle", function() {
    return plugin_subtitle;
});
parcelHelpers.export(exports, "Ticks", function() {
    return Ticks;
});
parcelHelpers.export(exports, "TimeScale", function() {
    return TimeScale;
});
parcelHelpers.export(exports, "TimeSeriesScale", function() {
    return TimeSeriesScale;
});
parcelHelpers.export(exports, "Title", function() {
    return plugin_title;
});
parcelHelpers.export(exports, "Tooltip", function() {
    return plugin_tooltip;
});
parcelHelpers.export(exports, "_adapters", function() {
    return adapters;
});
parcelHelpers.export(exports, "_detectPlatform", function() {
    return _detectPlatform;
});
parcelHelpers.export(exports, "animator", function() {
    return animator;
});
parcelHelpers.export(exports, "controllers", function() {
    return controllers;
});
parcelHelpers.export(exports, "elements", function() {
    return elements;
});
parcelHelpers.export(exports, "layouts", function() {
    return layouts;
});
parcelHelpers.export(exports, "plugins", function() {
    return plugins;
});
parcelHelpers.export(exports, "registerables", function() {
    return registerables;
});
parcelHelpers.export(exports, "registry", function() {
    return registry;
});
parcelHelpers.export(exports, "scales", function() {
    return scales;
});
var _assertThisInitializedJs = require("@swc/helpers/lib/_assert_this_initialized.js");
var _assertThisInitializedJsDefault = parcelHelpers.interopDefault(_assertThisInitializedJs);
var _classCallCheckJs = require("@swc/helpers/lib/_class_call_check.js");
var _classCallCheckJsDefault = parcelHelpers.interopDefault(_classCallCheckJs);
var _createClassJs = require("@swc/helpers/lib/_create_class.js");
var _createClassJsDefault = parcelHelpers.interopDefault(_createClassJs);
var _definePropertyJs = require("@swc/helpers/lib/_define_property.js");
var _definePropertyJsDefault = parcelHelpers.interopDefault(_definePropertyJs);
var _getJs = require("@swc/helpers/lib/_get.js");
var _getJsDefault = parcelHelpers.interopDefault(_getJs);
var _getPrototypeOfJs = require("@swc/helpers/lib/_get_prototype_of.js");
var _getPrototypeOfJsDefault = parcelHelpers.interopDefault(_getPrototypeOfJs);
var _inheritsJs = require("@swc/helpers/lib/_inherits.js");
var _inheritsJsDefault = parcelHelpers.interopDefault(_inheritsJs);
var _objectSpreadJs = require("@swc/helpers/lib/_object_spread.js");
var _objectSpreadJsDefault = parcelHelpers.interopDefault(_objectSpreadJs);
var _slicedToArrayJs = require("@swc/helpers/lib/_sliced_to_array.js");
var _slicedToArrayJsDefault = parcelHelpers.interopDefault(_slicedToArrayJs);
var _toConsumableArrayJs = require("@swc/helpers/lib/_to_consumable_array.js");
var _toConsumableArrayJsDefault = parcelHelpers.interopDefault(_toConsumableArrayJs);
var _typeOfJs = require("@swc/helpers/lib/_type_of.js");
var _typeOfJsDefault = parcelHelpers.interopDefault(_typeOfJs);
var _wrapNativeSuperJs = require("@swc/helpers/lib/_wrap_native_super.js");
var _wrapNativeSuperJsDefault = parcelHelpers.interopDefault(_wrapNativeSuperJs);
var _createSuperJs = require("@swc/helpers/lib/_create_super.js");
var _createSuperJsDefault = parcelHelpers.interopDefault(_createSuperJs);
/*!
 * Chart.js v3.8.0
 * https://www.chartjs.org
 * (c) 2022 Chart.js Contributors
 * Released under the MIT License
 */ var _helpersSegmentJs = require("./chunks/helpers.segment.js");
var Animator = /*#__PURE__*/ function() {
    "use strict";
    function Animator() {
        (0, _classCallCheckJsDefault.default)(this, Animator);
        this._request = null;
        this._charts = new Map();
        this._running = false;
        this._lastDate = undefined;
    }
    (0, _createClassJsDefault.default)(Animator, [
        {
            key: "_notify",
            value: function _notify(chart, anims, date, type) {
                var callbacks = anims.listeners[type];
                var numSteps = anims.duration;
                callbacks.forEach(function(fn) {
                    return fn({
                        chart: chart,
                        initial: anims.initial,
                        numSteps: numSteps,
                        currentStep: Math.min(date - anims.start, numSteps)
                    });
                });
            }
        },
        {
            key: "_refresh",
            value: function _refresh() {
                var _this = this;
                if (this._request) return;
                this._running = true;
                this._request = (0, _helpersSegmentJs.r).call(window, function() {
                    _this._update();
                    _this._request = null;
                    if (_this._running) _this._refresh();
                });
            }
        },
        {
            key: "_update",
            value: function _update() {
                var date = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : Date.now();
                var _this = this;
                var remaining = 0;
                this._charts.forEach(function(anims, chart) {
                    if (!anims.running || !anims.items.length) return;
                    var items = anims.items;
                    var i = items.length - 1;
                    var draw1 = false;
                    var item;
                    for(; i >= 0; --i){
                        item = items[i];
                        if (item._active) {
                            if (item._total > anims.duration) anims.duration = item._total;
                            item.tick(date);
                            draw1 = true;
                        } else {
                            items[i] = items[items.length - 1];
                            items.pop();
                        }
                    }
                    if (draw1) {
                        chart.draw();
                        _this._notify(chart, anims, date, "progress");
                    }
                    if (!items.length) {
                        anims.running = false;
                        _this._notify(chart, anims, date, "complete");
                        anims.initial = false;
                    }
                    remaining += items.length;
                });
                this._lastDate = date;
                if (remaining === 0) this._running = false;
            }
        },
        {
            key: "_getAnims",
            value: function _getAnims(chart) {
                var charts = this._charts;
                var anims = charts.get(chart);
                if (!anims) {
                    anims = {
                        running: false,
                        initial: true,
                        items: [],
                        listeners: {
                            complete: [],
                            progress: []
                        }
                    };
                    charts.set(chart, anims);
                }
                return anims;
            }
        },
        {
            key: "listen",
            value: function listen(chart, event, cb) {
                this._getAnims(chart).listeners[event].push(cb);
            }
        },
        {
            key: "add",
            value: function add(chart, items) {
                var _items;
                if (!items || !items.length) return;
                (_items = this._getAnims(chart).items).push.apply(_items, (0, _toConsumableArrayJsDefault.default)(items));
            }
        },
        {
            key: "has",
            value: function has(chart) {
                return this._getAnims(chart).items.length > 0;
            }
        },
        {
            key: "start",
            value: function start(chart) {
                var anims = this._charts.get(chart);
                if (!anims) return;
                anims.running = true;
                anims.start = Date.now();
                anims.duration = anims.items.reduce(function(acc, cur) {
                    return Math.max(acc, cur._duration);
                }, 0);
                this._refresh();
            }
        },
        {
            key: "running",
            value: function running(chart) {
                if (!this._running) return false;
                var anims = this._charts.get(chart);
                if (!anims || !anims.running || !anims.items.length) return false;
                return true;
            }
        },
        {
            key: "stop",
            value: function stop(chart) {
                var anims = this._charts.get(chart);
                if (!anims || !anims.items.length) return;
                var items = anims.items;
                var i = items.length - 1;
                for(; i >= 0; --i)items[i].cancel();
                anims.items = [];
                this._notify(chart, anims, Date.now(), "complete");
            }
        },
        {
            key: "remove",
            value: function remove(chart) {
                return this._charts.delete(chart);
            }
        }
    ]);
    return Animator;
}();
var animator = new Animator();
var transparent = "transparent";
var interpolators = {
    boolean: function(from, to, factor) {
        return factor > 0.5 ? to : from;
    },
    color: function(from, to, factor) {
        var c0 = (0, _helpersSegmentJs.c)(from || transparent);
        var c1 = c0.valid && (0, _helpersSegmentJs.c)(to || transparent);
        return c1 && c1.valid ? c1.mix(c0, factor).hexString() : to;
    },
    number: function(from, to, factor) {
        return from + (to - from) * factor;
    }
};
var Animation = /*#__PURE__*/ function() {
    "use strict";
    function Animation(cfg, target, prop, to) {
        (0, _classCallCheckJsDefault.default)(this, Animation);
        var currentValue = target[prop];
        to = (0, _helpersSegmentJs.a)([
            cfg.to,
            to,
            currentValue,
            cfg.from
        ]);
        var from = (0, _helpersSegmentJs.a)([
            cfg.from,
            currentValue,
            to
        ]);
        this._active = true;
        this._fn = cfg.fn || interpolators[cfg.type || (typeof from === "undefined" ? "undefined" : (0, _typeOfJsDefault.default)(from))];
        this._easing = (0, _helpersSegmentJs.e)[cfg.easing] || (0, _helpersSegmentJs.e).linear;
        this._start = Math.floor(Date.now() + (cfg.delay || 0));
        this._duration = this._total = Math.floor(cfg.duration);
        this._loop = !!cfg.loop;
        this._target = target;
        this._prop = prop;
        this._from = from;
        this._to = to;
        this._promises = undefined;
    }
    (0, _createClassJsDefault.default)(Animation, [
        {
            key: "active",
            value: function active() {
                return this._active;
            }
        },
        {
            key: "update",
            value: function update(cfg, to, date) {
                if (this._active) {
                    this._notify(false);
                    var currentValue = this._target[this._prop];
                    var elapsed = date - this._start;
                    var remain = this._duration - elapsed;
                    this._start = date;
                    this._duration = Math.floor(Math.max(remain, cfg.duration));
                    this._total += elapsed;
                    this._loop = !!cfg.loop;
                    this._to = (0, _helpersSegmentJs.a)([
                        cfg.to,
                        to,
                        currentValue,
                        cfg.from
                    ]);
                    this._from = (0, _helpersSegmentJs.a)([
                        cfg.from,
                        currentValue,
                        to
                    ]);
                }
            }
        },
        {
            key: "cancel",
            value: function cancel() {
                if (this._active) {
                    this.tick(Date.now());
                    this._active = false;
                    this._notify(false);
                }
            }
        },
        {
            key: "tick",
            value: function tick(date) {
                var elapsed = date - this._start;
                var duration = this._duration;
                var prop = this._prop;
                var from = this._from;
                var loop = this._loop;
                var to = this._to;
                var factor;
                this._active = from !== to && (loop || elapsed < duration);
                if (!this._active) {
                    this._target[prop] = to;
                    this._notify(true);
                    return;
                }
                if (elapsed < 0) {
                    this._target[prop] = from;
                    return;
                }
                factor = elapsed / duration % 2;
                factor = loop && factor > 1 ? 2 - factor : factor;
                factor = this._easing(Math.min(1, Math.max(0, factor)));
                this._target[prop] = this._fn(from, to, factor);
            }
        },
        {
            key: "wait",
            value: function wait() {
                var promises = this._promises || (this._promises = []);
                return new Promise(function(res, rej) {
                    promises.push({
                        res: res,
                        rej: rej
                    });
                });
            }
        },
        {
            key: "_notify",
            value: function _notify(resolved) {
                var method = resolved ? "res" : "rej";
                var promises = this._promises || [];
                for(var i = 0; i < promises.length; i++)promises[i][method]();
            }
        }
    ]);
    return Animation;
}();
var numbers = [
    "x",
    "y",
    "borderWidth",
    "radius",
    "tension"
];
var colors = [
    "color",
    "borderColor",
    "backgroundColor"
];
(0, _helpersSegmentJs.d).set("animation", {
    delay: undefined,
    duration: 1000,
    easing: "easeOutQuart",
    fn: undefined,
    from: undefined,
    loop: undefined,
    to: undefined,
    type: undefined
});
var animationOptions = Object.keys((0, _helpersSegmentJs.d).animation);
(0, _helpersSegmentJs.d).describe("animation", {
    _fallback: false,
    _indexable: false,
    _scriptable: function(name) {
        return name !== "onProgress" && name !== "onComplete" && name !== "fn";
    }
});
(0, _helpersSegmentJs.d).set("animations", {
    colors: {
        type: "color",
        properties: colors
    },
    numbers: {
        type: "number",
        properties: numbers
    }
});
(0, _helpersSegmentJs.d).describe("animations", {
    _fallback: "animation"
});
(0, _helpersSegmentJs.d).set("transitions", {
    active: {
        animation: {
            duration: 400
        }
    },
    resize: {
        animation: {
            duration: 0
        }
    },
    show: {
        animations: {
            colors: {
                from: "transparent"
            },
            visible: {
                type: "boolean",
                duration: 0
            }
        }
    },
    hide: {
        animations: {
            colors: {
                to: "transparent"
            },
            visible: {
                type: "boolean",
                easing: "linear",
                fn: function(v) {
                    return v | 0;
                }
            }
        }
    }
});
var Animations = /*#__PURE__*/ function() {
    "use strict";
    function Animations(chart, config) {
        (0, _classCallCheckJsDefault.default)(this, Animations);
        this._chart = chart;
        this._properties = new Map();
        this.configure(config);
    }
    (0, _createClassJsDefault.default)(Animations, [
        {
            key: "configure",
            value: function configure(config) {
                if (!(0, _helpersSegmentJs.i)(config)) return;
                var animatedProps = this._properties;
                Object.getOwnPropertyNames(config).forEach(function(key) {
                    var cfg = config[key];
                    if (!(0, _helpersSegmentJs.i)(cfg)) return;
                    var resolved = {};
                    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                    try {
                        for(var _iterator = animationOptions[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                            var option = _step.value;
                            resolved[option] = cfg[option];
                        }
                    } catch (err) {
                        _didIteratorError = true;
                        _iteratorError = err;
                    } finally{
                        try {
                            if (!_iteratorNormalCompletion && _iterator.return != null) {
                                _iterator.return();
                            }
                        } finally{
                            if (_didIteratorError) {
                                throw _iteratorError;
                            }
                        }
                    }
                    ((0, _helpersSegmentJs.b)(cfg.properties) && cfg.properties || [
                        key
                    ]).forEach(function(prop) {
                        if (prop === key || !animatedProps.has(prop)) animatedProps.set(prop, resolved);
                    });
                });
            }
        },
        {
            key: "_animateOptions",
            value: function _animateOptions(target, values) {
                var newOptions = values.options;
                var options = resolveTargetOptions(target, newOptions);
                if (!options) return [];
                var animations = this._createAnimations(options, newOptions);
                if (newOptions.$shared) awaitAll(target.options.$animations, newOptions).then(function() {
                    target.options = newOptions;
                }, function() {});
                return animations;
            }
        },
        {
            key: "_createAnimations",
            value: function _createAnimations(target, values) {
                var animatedProps = this._properties;
                var animations = [];
                var running = target.$animations || (target.$animations = {});
                var props = Object.keys(values);
                var date = Date.now();
                var i;
                for(i = props.length - 1; i >= 0; --i){
                    var prop = props[i];
                    if (prop.charAt(0) === "$") continue;
                    if (prop === "options") {
                        var _animations;
                        (_animations = animations).push.apply(_animations, (0, _toConsumableArrayJsDefault.default)(this._animateOptions(target, values)));
                        continue;
                    }
                    var value = values[prop];
                    var animation = running[prop];
                    var cfg = animatedProps.get(prop);
                    if (animation) {
                        if (cfg && animation.active()) {
                            animation.update(cfg, value, date);
                            continue;
                        } else animation.cancel();
                    }
                    if (!cfg || !cfg.duration) {
                        target[prop] = value;
                        continue;
                    }
                    running[prop] = animation = new Animation(cfg, target, prop, value);
                    animations.push(animation);
                }
                return animations;
            }
        },
        {
            key: "update",
            value: function update(target, values) {
                if (this._properties.size === 0) {
                    Object.assign(target, values);
                    return;
                }
                var animations = this._createAnimations(target, values);
                if (animations.length) {
                    animator.add(this._chart, animations);
                    return true;
                }
            }
        }
    ]);
    return Animations;
}();
function awaitAll(animations, properties) {
    var running = [];
    var keys = Object.keys(properties);
    for(var i = 0; i < keys.length; i++){
        var anim = animations[keys[i]];
        if (anim && anim.active()) running.push(anim.wait());
    }
    return Promise.all(running);
}
function resolveTargetOptions(target, newOptions) {
    if (!newOptions) return;
    var options = target.options;
    if (!options) {
        target.options = newOptions;
        return;
    }
    if (options.$shared) target.options = options = Object.assign({}, options, {
        $shared: false,
        $animations: {}
    });
    return options;
}
function scaleClip(scale, allowedOverflow) {
    var opts = scale && scale.options || {};
    var reverse = opts.reverse;
    var min = opts.min === undefined ? allowedOverflow : 0;
    var max = opts.max === undefined ? allowedOverflow : 0;
    return {
        start: reverse ? max : min,
        end: reverse ? min : max
    };
}
function defaultClip(xScale, yScale, allowedOverflow) {
    if (allowedOverflow === false) return false;
    var x = scaleClip(xScale, allowedOverflow);
    var y = scaleClip(yScale, allowedOverflow);
    return {
        top: y.end,
        right: x.end,
        bottom: y.start,
        left: x.start
    };
}
function toClip(value) {
    var t, r, b, l;
    if ((0, _helpersSegmentJs.i)(value)) {
        t = value.top;
        r = value.right;
        b = value.bottom;
        l = value.left;
    } else t = r = b = l = value;
    return {
        top: t,
        right: r,
        bottom: b,
        left: l,
        disabled: value === false
    };
}
function getSortedDatasetIndices(chart, filterVisible) {
    var keys = [];
    var metasets = chart._getSortedDatasetMetas(filterVisible);
    var i, ilen;
    for(i = 0, ilen = metasets.length; i < ilen; ++i)keys.push(metasets[i].index);
    return keys;
}
function applyStack(stack, value, dsIndex) {
    var options = arguments.length > 3 && arguments[3] !== void 0 ? arguments[3] : {};
    var keys = stack.keys;
    var singleMode = options.mode === "single";
    var i, ilen, datasetIndex, otherValue;
    if (value === null) return;
    for(i = 0, ilen = keys.length; i < ilen; ++i){
        datasetIndex = +keys[i];
        if (datasetIndex === dsIndex) {
            if (options.all) continue;
            break;
        }
        otherValue = stack.values[datasetIndex];
        if ((0, _helpersSegmentJs.g)(otherValue) && (singleMode || value === 0 || (0, _helpersSegmentJs.s)(value) === (0, _helpersSegmentJs.s)(otherValue))) value += otherValue;
    }
    return value;
}
function convertObjectDataToArray(data) {
    var keys = Object.keys(data);
    var adata = new Array(keys.length);
    var i, ilen, key;
    for(i = 0, ilen = keys.length; i < ilen; ++i){
        key = keys[i];
        adata[i] = {
            x: key,
            y: data[key]
        };
    }
    return adata;
}
function isStacked(scale, meta) {
    var stacked = scale && scale.options.stacked;
    return stacked || stacked === undefined && meta.stack !== undefined;
}
function getStackKey(indexScale, valueScale, meta) {
    return "".concat(indexScale.id, ".").concat(valueScale.id, ".").concat(meta.stack || meta.type);
}
function getUserBounds(scale) {
    var ref = scale.getUserBounds(), min = ref.min, max = ref.max, minDefined = ref.minDefined, maxDefined = ref.maxDefined;
    return {
        min: minDefined ? min : Number.NEGATIVE_INFINITY,
        max: maxDefined ? max : Number.POSITIVE_INFINITY
    };
}
function getOrCreateStack(stacks, stackKey, indexValue) {
    var subStack = stacks[stackKey] || (stacks[stackKey] = {});
    return subStack[indexValue] || (subStack[indexValue] = {});
}
function getLastIndexInStack(stack, vScale, positive, type) {
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = vScale.getMatchingVisibleMetas(type).reverse()[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var meta = _step.value;
            var value = stack[meta.index];
            if (positive && value > 0 || !positive && value < 0) return meta.index;
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
    return null;
}
function updateStacks(controller, parsed) {
    var chart = controller.chart, meta = controller._cachedMeta;
    var stacks = chart._stacks || (chart._stacks = {});
    var iScale = meta.iScale, vScale = meta.vScale, datasetIndex = meta.index;
    var iAxis = iScale.axis;
    var vAxis = vScale.axis;
    var key = getStackKey(iScale, vScale, meta);
    var ilen = parsed.length;
    var stack;
    for(var i = 0; i < ilen; ++i){
        var item = parsed[i];
        var index1 = item[iAxis], value = item[vAxis];
        var itemStacks = item._stacks || (item._stacks = {});
        stack = itemStacks[vAxis] = getOrCreateStack(stacks, key, index1);
        stack[datasetIndex] = value;
        stack._top = getLastIndexInStack(stack, vScale, true, meta.type);
        stack._bottom = getLastIndexInStack(stack, vScale, false, meta.type);
    }
}
function getFirstScaleId(chart, axis) {
    var scales1 = chart.scales;
    return Object.keys(scales1).filter(function(key) {
        return scales1[key].axis === axis;
    }).shift();
}
function createDatasetContext(parent, index2) {
    return (0, _helpersSegmentJs.h)(parent, {
        active: false,
        dataset: undefined,
        datasetIndex: index2,
        index: index2,
        mode: "default",
        type: "dataset"
    });
}
function createDataContext(parent, index3, element) {
    return (0, _helpersSegmentJs.h)(parent, {
        active: false,
        dataIndex: index3,
        parsed: undefined,
        raw: undefined,
        element: element,
        index: index3,
        mode: "default",
        type: "data"
    });
}
function clearStacks(meta, items) {
    var datasetIndex = meta.controller.index;
    var axis = meta.vScale && meta.vScale.axis;
    if (!axis) return;
    items = items || meta._parsed;
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = items[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var parsed = _step.value;
            var stacks = parsed._stacks;
            if (!stacks || stacks[axis] === undefined || stacks[axis][datasetIndex] === undefined) return;
            delete stacks[axis][datasetIndex];
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
}
var isDirectUpdateMode = function(mode) {
    return mode === "reset" || mode === "none";
};
var cloneIfNotShared = function(cached, shared) {
    return shared ? cached : Object.assign({}, cached);
};
var createStack = function(canStack, meta, chart) {
    return canStack && !meta.hidden && meta._stacked && {
        keys: getSortedDatasetIndices(chart, true),
        values: null
    };
};
var DatasetController = /*#__PURE__*/ function() {
    "use strict";
    function DatasetController(chart, datasetIndex) {
        (0, _classCallCheckJsDefault.default)(this, DatasetController);
        this.chart = chart;
        this._ctx = chart.ctx;
        this.index = datasetIndex;
        this._cachedDataOpts = {};
        this._cachedMeta = this.getMeta();
        this._type = this._cachedMeta.type;
        this.options = undefined;
        this._parsing = false;
        this._data = undefined;
        this._objectData = undefined;
        this._sharedOptions = undefined;
        this._drawStart = undefined;
        this._drawCount = undefined;
        this.enableOptionSharing = false;
        this.supportsDecimation = false;
        this.$context = undefined;
        this._syncList = [];
        this.initialize();
    }
    (0, _createClassJsDefault.default)(DatasetController, [
        {
            key: "initialize",
            value: function initialize() {
                var meta = this._cachedMeta;
                this.configure();
                this.linkScales();
                meta._stacked = isStacked(meta.vScale, meta);
                this.addElements();
            }
        },
        {
            key: "updateIndex",
            value: function updateIndex(datasetIndex) {
                if (this.index !== datasetIndex) clearStacks(this._cachedMeta);
                this.index = datasetIndex;
            }
        },
        {
            key: "linkScales",
            value: function linkScales() {
                var chart = this.chart;
                var meta = this._cachedMeta;
                var dataset = this.getDataset();
                var chooseId = function(axis, x, y, r) {
                    return axis === "x" ? x : axis === "r" ? r : y;
                };
                var xid = meta.xAxisID = (0, _helpersSegmentJs.v)(dataset.xAxisID, getFirstScaleId(chart, "x"));
                var yid = meta.yAxisID = (0, _helpersSegmentJs.v)(dataset.yAxisID, getFirstScaleId(chart, "y"));
                var rid = meta.rAxisID = (0, _helpersSegmentJs.v)(dataset.rAxisID, getFirstScaleId(chart, "r"));
                var indexAxis = meta.indexAxis;
                var iid = meta.iAxisID = chooseId(indexAxis, xid, yid, rid);
                var vid = meta.vAxisID = chooseId(indexAxis, yid, xid, rid);
                meta.xScale = this.getScaleForId(xid);
                meta.yScale = this.getScaleForId(yid);
                meta.rScale = this.getScaleForId(rid);
                meta.iScale = this.getScaleForId(iid);
                meta.vScale = this.getScaleForId(vid);
            }
        },
        {
            key: "getDataset",
            value: function getDataset() {
                return this.chart.data.datasets[this.index];
            }
        },
        {
            key: "getMeta",
            value: function getMeta() {
                return this.chart.getDatasetMeta(this.index);
            }
        },
        {
            key: "getScaleForId",
            value: function getScaleForId(scaleID) {
                return this.chart.scales[scaleID];
            }
        },
        {
            key: "_getOtherScale",
            value: function _getOtherScale(scale) {
                var meta = this._cachedMeta;
                return scale === meta.iScale ? meta.vScale : meta.iScale;
            }
        },
        {
            key: "reset",
            value: function reset() {
                this._update("reset");
            }
        },
        {
            key: "_destroy",
            value: function _destroy() {
                var meta = this._cachedMeta;
                if (this._data) (0, _helpersSegmentJs.u)(this._data, this);
                if (meta._stacked) clearStacks(meta);
            }
        },
        {
            key: "_dataCheck",
            value: function _dataCheck() {
                var dataset = this.getDataset();
                var data = dataset.data || (dataset.data = []);
                var _data = this._data;
                if ((0, _helpersSegmentJs.i)(data)) this._data = convertObjectDataToArray(data);
                else if (_data !== data) {
                    if (_data) {
                        (0, _helpersSegmentJs.u)(_data, this);
                        var meta = this._cachedMeta;
                        clearStacks(meta);
                        meta._parsed = [];
                    }
                    if (data && Object.isExtensible(data)) (0, _helpersSegmentJs.l)(data, this);
                    this._syncList = [];
                    this._data = data;
                }
            }
        },
        {
            key: "addElements",
            value: function addElements() {
                var meta = this._cachedMeta;
                this._dataCheck();
                if (this.datasetElementType) meta.dataset = new this.datasetElementType();
            }
        },
        {
            key: "buildOrUpdateElements",
            value: function buildOrUpdateElements(resetNewElements) {
                var meta = this._cachedMeta;
                var dataset = this.getDataset();
                var stackChanged = false;
                this._dataCheck();
                var oldStacked = meta._stacked;
                meta._stacked = isStacked(meta.vScale, meta);
                if (meta.stack !== dataset.stack) {
                    stackChanged = true;
                    clearStacks(meta);
                    meta.stack = dataset.stack;
                }
                this._resyncElements(resetNewElements);
                if (stackChanged || oldStacked !== meta._stacked) updateStacks(this, meta._parsed);
            }
        },
        {
            key: "configure",
            value: function configure() {
                var config = this.chart.config;
                var scopeKeys = config.datasetScopeKeys(this._type);
                var scopes = config.getOptionScopes(this.getDataset(), scopeKeys, true);
                this.options = config.createResolver(scopes, this.getContext());
                this._parsing = this.options.parsing;
                this._cachedDataOpts = {};
            }
        },
        {
            key: "parse",
            value: function parse1(start, count) {
                var ref = this, meta = ref._cachedMeta, data = ref._data;
                var iScale = meta.iScale, _stacked = meta._stacked;
                var iAxis = iScale.axis;
                var sorted = start === 0 && count === data.length ? true : meta._sorted;
                var prev = start > 0 && meta._parsed[start - 1];
                var i, cur, parsed;
                if (this._parsing === false) {
                    meta._parsed = data;
                    meta._sorted = true;
                    parsed = data;
                } else {
                    if ((0, _helpersSegmentJs.b)(data[start])) parsed = this.parseArrayData(meta, data, start, count);
                    else if ((0, _helpersSegmentJs.i)(data[start])) parsed = this.parseObjectData(meta, data, start, count);
                    else parsed = this.parsePrimitiveData(meta, data, start, count);
                    var isNotInOrderComparedToPrev = function() {
                        return cur[iAxis] === null || prev && cur[iAxis] < prev[iAxis];
                    };
                    for(i = 0; i < count; ++i){
                        meta._parsed[i + start] = cur = parsed[i];
                        if (sorted) {
                            if (isNotInOrderComparedToPrev()) sorted = false;
                            prev = cur;
                        }
                    }
                    meta._sorted = sorted;
                }
                if (_stacked) updateStacks(this, parsed);
            }
        },
        {
            key: "parsePrimitiveData",
            value: function parsePrimitiveData(meta, data, start, count) {
                var iScale = meta.iScale, vScale = meta.vScale;
                var iAxis = iScale.axis;
                var vAxis = vScale.axis;
                var labels = iScale.getLabels();
                var singleScale = iScale === vScale;
                var parsed = new Array(count);
                var i, ilen, index4;
                for(i = 0, ilen = count; i < ilen; ++i){
                    index4 = i + start;
                    var _obj;
                    parsed[i] = (_obj = {}, (0, _definePropertyJsDefault.default)(_obj, iAxis, singleScale || iScale.parse(labels[index4], index4)), (0, _definePropertyJsDefault.default)(_obj, vAxis, vScale.parse(data[index4], index4)), _obj);
                }
                return parsed;
            }
        },
        {
            key: "parseArrayData",
            value: function parseArrayData(meta, data, start, count) {
                var xScale = meta.xScale, yScale = meta.yScale;
                var parsed = new Array(count);
                var i, ilen, index5, item;
                for(i = 0, ilen = count; i < ilen; ++i){
                    index5 = i + start;
                    item = data[index5];
                    parsed[i] = {
                        x: xScale.parse(item[0], index5),
                        y: yScale.parse(item[1], index5)
                    };
                }
                return parsed;
            }
        },
        {
            key: "parseObjectData",
            value: function parseObjectData(meta, data, start, count) {
                var xScale = meta.xScale, yScale = meta.yScale;
                var __parsing = this._parsing, _xAxisKey = __parsing.xAxisKey, xAxisKey = _xAxisKey === void 0 ? "x" : _xAxisKey, _yAxisKey = __parsing.yAxisKey, yAxisKey = _yAxisKey === void 0 ? "y" : _yAxisKey;
                var parsed = new Array(count);
                var i, ilen, index6, item;
                for(i = 0, ilen = count; i < ilen; ++i){
                    index6 = i + start;
                    item = data[index6];
                    parsed[i] = {
                        x: xScale.parse((0, _helpersSegmentJs.f)(item, xAxisKey), index6),
                        y: yScale.parse((0, _helpersSegmentJs.f)(item, yAxisKey), index6)
                    };
                }
                return parsed;
            }
        },
        {
            key: "getParsed",
            value: function getParsed(index7) {
                return this._cachedMeta._parsed[index7];
            }
        },
        {
            key: "getDataElement",
            value: function getDataElement(index8) {
                return this._cachedMeta.data[index8];
            }
        },
        {
            key: "applyStack",
            value: function applyStack1(scale, parsed, mode) {
                var chart = this.chart;
                var meta = this._cachedMeta;
                var value = parsed[scale.axis];
                var stack = {
                    keys: getSortedDatasetIndices(chart, true),
                    values: parsed._stacks[scale.axis]
                };
                return applyStack(stack, value, meta.index, {
                    mode: mode
                });
            }
        },
        {
            key: "updateRangeFromParsed",
            value: function updateRangeFromParsed(range, scale, parsed, stack) {
                var parsedValue = parsed[scale.axis];
                var value = parsedValue === null ? NaN : parsedValue;
                var values = stack && parsed._stacks[scale.axis];
                if (stack && values) {
                    stack.values = values;
                    value = applyStack(stack, parsedValue, this._cachedMeta.index);
                }
                range.min = Math.min(range.min, value);
                range.max = Math.max(range.max, value);
            }
        },
        {
            key: "getMinMax",
            value: function getMinMax(scale, canStack) {
                var meta = this._cachedMeta;
                var _parsed = meta._parsed;
                var sorted = meta._sorted && scale === meta.iScale;
                var ilen = _parsed.length;
                var otherScale = this._getOtherScale(scale);
                var stack = createStack(canStack, meta, this.chart);
                var range = {
                    min: Number.POSITIVE_INFINITY,
                    max: Number.NEGATIVE_INFINITY
                };
                var ref = getUserBounds(otherScale), otherMin = ref.min, otherMax = ref.max;
                var i, parsed;
                function _skip() {
                    parsed = _parsed[i];
                    var otherValue = parsed[otherScale.axis];
                    return !(0, _helpersSegmentJs.g)(parsed[scale.axis]) || otherMin > otherValue || otherMax < otherValue;
                }
                for(i = 0; i < ilen; ++i){
                    if (_skip()) continue;
                    this.updateRangeFromParsed(range, scale, parsed, stack);
                    if (sorted) break;
                }
                if (sorted) for(i = ilen - 1; i >= 0; --i){
                    if (_skip()) continue;
                    this.updateRangeFromParsed(range, scale, parsed, stack);
                    break;
                }
                return range;
            }
        },
        {
            key: "getAllParsedValues",
            value: function getAllParsedValues(scale) {
                var parsed = this._cachedMeta._parsed;
                var values = [];
                var i, ilen, value;
                for(i = 0, ilen = parsed.length; i < ilen; ++i){
                    value = parsed[i][scale.axis];
                    if ((0, _helpersSegmentJs.g)(value)) values.push(value);
                }
                return values;
            }
        },
        {
            key: "getMaxOverflow",
            value: function getMaxOverflow() {
                return false;
            }
        },
        {
            key: "getLabelAndValue",
            value: function getLabelAndValue(index9) {
                var meta = this._cachedMeta;
                var iScale = meta.iScale;
                var vScale = meta.vScale;
                var parsed = this.getParsed(index9);
                return {
                    label: iScale ? "" + iScale.getLabelForValue(parsed[iScale.axis]) : "",
                    value: vScale ? "" + vScale.getLabelForValue(parsed[vScale.axis]) : ""
                };
            }
        },
        {
            key: "_update",
            value: function _update(mode) {
                var meta = this._cachedMeta;
                this.update(mode || "default");
                meta._clip = toClip((0, _helpersSegmentJs.v)(this.options.clip, defaultClip(meta.xScale, meta.yScale, this.getMaxOverflow())));
            }
        },
        {
            key: "update",
            value: function update(mode) {}
        },
        {
            key: "draw",
            value: function draw2() {
                var ctx = this._ctx;
                var chart = this.chart;
                var meta = this._cachedMeta;
                var elements1 = meta.data || [];
                var area = chart.chartArea;
                var active = [];
                var start = this._drawStart || 0;
                var count = this._drawCount || elements1.length - start;
                var drawActiveElementsOnTop = this.options.drawActiveElementsOnTop;
                var i;
                if (meta.dataset) meta.dataset.draw(ctx, area, start, count);
                for(i = start; i < start + count; ++i){
                    var element = elements1[i];
                    if (element.hidden) continue;
                    if (element.active && drawActiveElementsOnTop) active.push(element);
                    else element.draw(ctx, area);
                }
                for(i = 0; i < active.length; ++i)active[i].draw(ctx, area);
            }
        },
        {
            key: "getStyle",
            value: function getStyle(index10, active) {
                var mode = active ? "active" : "default";
                return index10 === undefined && this._cachedMeta.dataset ? this.resolveDatasetElementOptions(mode) : this.resolveDataElementOptions(index10 || 0, mode);
            }
        },
        {
            key: "getContext",
            value: function getContext(index11, active, mode) {
                var dataset = this.getDataset();
                var context;
                if (index11 >= 0 && index11 < this._cachedMeta.data.length) {
                    var element = this._cachedMeta.data[index11];
                    context = element.$context || (element.$context = createDataContext(this.getContext(), index11, element));
                    context.parsed = this.getParsed(index11);
                    context.raw = dataset.data[index11];
                    context.index = context.dataIndex = index11;
                } else {
                    context = this.$context || (this.$context = createDatasetContext(this.chart.getContext(), this.index));
                    context.dataset = dataset;
                    context.index = context.datasetIndex = this.index;
                }
                context.active = !!active;
                context.mode = mode;
                return context;
            }
        },
        {
            key: "resolveDatasetElementOptions",
            value: function resolveDatasetElementOptions(mode) {
                return this._resolveElementOptions(this.datasetElementType.id, mode);
            }
        },
        {
            key: "resolveDataElementOptions",
            value: function resolveDataElementOptions(index12, mode) {
                return this._resolveElementOptions(this.dataElementType.id, mode, index12);
            }
        },
        {
            key: "_resolveElementOptions",
            value: function _resolveElementOptions(elementType) {
                var mode = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : "default", index13 = arguments.length > 2 ? arguments[2] : void 0;
                var _this = this;
                var active = mode === "active";
                var cache = this._cachedDataOpts;
                var cacheKey = elementType + "-" + mode;
                var cached = cache[cacheKey];
                var sharing = this.enableOptionSharing && (0, _helpersSegmentJs.j)(index13);
                if (cached) return cloneIfNotShared(cached, sharing);
                var config = this.chart.config;
                var scopeKeys = config.datasetElementScopeKeys(this._type, elementType);
                var prefixes = active ? [
                    "".concat(elementType, "Hover"),
                    "hover",
                    elementType,
                    ""
                ] : [
                    elementType,
                    ""
                ];
                var scopes = config.getOptionScopes(this.getDataset(), scopeKeys);
                var names = Object.keys((0, _helpersSegmentJs.d).elements[elementType]);
                var context = function() {
                    return _this.getContext(index13, active);
                };
                var values = config.resolveNamedOptions(scopes, names, context, prefixes);
                if (values.$shared) {
                    values.$shared = sharing;
                    cache[cacheKey] = Object.freeze(cloneIfNotShared(values, sharing));
                }
                return values;
            }
        },
        {
            key: "_resolveAnimations",
            value: function _resolveAnimations(index14, transition, active) {
                var chart = this.chart;
                var cache = this._cachedDataOpts;
                var cacheKey = "animation-".concat(transition);
                var cached = cache[cacheKey];
                if (cached) return cached;
                var options;
                if (chart.options.animation !== false) {
                    var config = this.chart.config;
                    var scopeKeys = config.datasetAnimationScopeKeys(this._type, transition);
                    var scopes = config.getOptionScopes(this.getDataset(), scopeKeys);
                    options = config.createResolver(scopes, this.getContext(index14, active, transition));
                }
                var animations = new Animations(chart, options && options.animations);
                if (options && options._cacheable) cache[cacheKey] = Object.freeze(animations);
                return animations;
            }
        },
        {
            key: "getSharedOptions",
            value: function getSharedOptions(options) {
                if (!options.$shared) return;
                return this._sharedOptions || (this._sharedOptions = Object.assign({}, options));
            }
        },
        {
            key: "includeOptions",
            value: function includeOptions(mode, sharedOptions) {
                return !sharedOptions || isDirectUpdateMode(mode) || this.chart._animationsDisabled;
            }
        },
        {
            key: "updateElement",
            value: function updateElement(element, index15, properties, mode) {
                if (isDirectUpdateMode(mode)) Object.assign(element, properties);
                else this._resolveAnimations(index15, mode).update(element, properties);
            }
        },
        {
            key: "updateSharedOptions",
            value: function updateSharedOptions(sharedOptions, mode, newOptions) {
                if (sharedOptions && !isDirectUpdateMode(mode)) this._resolveAnimations(undefined, mode).update(sharedOptions, newOptions);
            }
        },
        {
            key: "_setStyle",
            value: function _setStyle(element, index16, mode, active) {
                element.active = active;
                var options = this.getStyle(index16, active);
                this._resolveAnimations(index16, mode, active).update(element, {
                    options: !active && this.getSharedOptions(options) || options
                });
            }
        },
        {
            key: "removeHoverStyle",
            value: function removeHoverStyle(element, datasetIndex, index17) {
                this._setStyle(element, index17, "active", false);
            }
        },
        {
            key: "setHoverStyle",
            value: function setHoverStyle(element, datasetIndex, index18) {
                this._setStyle(element, index18, "active", true);
            }
        },
        {
            key: "_removeDatasetHoverStyle",
            value: function _removeDatasetHoverStyle() {
                var element = this._cachedMeta.dataset;
                if (element) this._setStyle(element, undefined, "active", false);
            }
        },
        {
            key: "_setDatasetHoverStyle",
            value: function _setDatasetHoverStyle() {
                var element = this._cachedMeta.dataset;
                if (element) this._setStyle(element, undefined, "active", true);
            }
        },
        {
            key: "_resyncElements",
            value: function _resyncElements(resetNewElements) {
                var data = this._data;
                var elements2 = this._cachedMeta.data;
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                try {
                    for(var _iterator = this._syncList[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var _value = (0, _slicedToArrayJsDefault.default)(_step.value, 3), method = _value[0], arg1 = _value[1], arg2 = _value[2];
                        this[method](arg1, arg2);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
                this._syncList = [];
                var numMeta = elements2.length;
                var numData = data.length;
                var count = Math.min(numData, numMeta);
                if (count) this.parse(0, count);
                if (numData > numMeta) this._insertElements(numMeta, numData - numMeta, resetNewElements);
                else if (numData < numMeta) this._removeElements(numData, numMeta - numData);
            }
        },
        {
            key: "_insertElements",
            value: function _insertElements(start, count) {
                var resetNewElements = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : true;
                var meta = this._cachedMeta;
                var data = meta.data;
                var end = start + count;
                var i;
                var move = function(arr) {
                    arr.length += count;
                    for(i = arr.length - 1; i >= end; i--)arr[i] = arr[i - count];
                };
                move(data);
                for(i = start; i < end; ++i)data[i] = new this.dataElementType();
                if (this._parsing) move(meta._parsed);
                this.parse(start, count);
                if (resetNewElements) this.updateElements(data, start, count, "reset");
            }
        },
        {
            key: "updateElements",
            value: function updateElements(element, start, count, mode) {}
        },
        {
            key: "_removeElements",
            value: function _removeElements(start, count) {
                var meta = this._cachedMeta;
                if (this._parsing) {
                    var removed = meta._parsed.splice(start, count);
                    if (meta._stacked) clearStacks(meta, removed);
                }
                meta.data.splice(start, count);
            }
        },
        {
            key: "_sync",
            value: function _sync(args) {
                if (this._parsing) this._syncList.push(args);
                else {
                    var _args = (0, _slicedToArrayJsDefault.default)(args, 3), method = _args[0], arg1 = _args[1], arg2 = _args[2];
                    this[method](arg1, arg2);
                }
                this.chart._dataChanges.push([
                    this.index
                ].concat((0, _toConsumableArrayJsDefault.default)(args)));
            }
        },
        {
            key: "_onDataPush",
            value: function _onDataPush() {
                var count = arguments.length;
                this._sync([
                    "_insertElements",
                    this.getDataset().data.length - count,
                    count
                ]);
            }
        },
        {
            key: "_onDataPop",
            value: function _onDataPop() {
                this._sync([
                    "_removeElements",
                    this._cachedMeta.data.length - 1,
                    1
                ]);
            }
        },
        {
            key: "_onDataShift",
            value: function _onDataShift() {
                this._sync([
                    "_removeElements",
                    0,
                    1
                ]);
            }
        },
        {
            key: "_onDataSplice",
            value: function _onDataSplice(start, count) {
                if (count) this._sync([
                    "_removeElements",
                    start,
                    count
                ]);
                var newCount = arguments.length - 2;
                if (newCount) this._sync([
                    "_insertElements",
                    start,
                    newCount
                ]);
            }
        },
        {
            key: "_onDataUnshift",
            value: function _onDataUnshift() {
                this._sync([
                    "_insertElements",
                    0,
                    arguments.length
                ]);
            }
        }
    ]);
    return DatasetController;
}();
DatasetController.defaults = {};
DatasetController.prototype.datasetElementType = null;
DatasetController.prototype.dataElementType = null;
function getAllScaleValues(scale, type) {
    if (!scale._cache.$bar) {
        var visibleMetas = scale.getMatchingVisibleMetas(type);
        var values = [];
        for(var i = 0, ilen = visibleMetas.length; i < ilen; i++)values = values.concat(visibleMetas[i].controller.getAllParsedValues(scale));
        scale._cache.$bar = (0, _helpersSegmentJs._)(values.sort(function(a, b) {
            return a - b;
        }));
    }
    return scale._cache.$bar;
}
function computeMinSampleSize(meta) {
    var scale = meta.iScale;
    var values = getAllScaleValues(scale, meta.type);
    var min = scale._length;
    var i, ilen, curr, prev;
    var updateMinAndPrev = function() {
        if (curr === 32767 || curr === -32768) return;
        if ((0, _helpersSegmentJs.j)(prev)) min = Math.min(min, Math.abs(curr - prev) || min);
        prev = curr;
    };
    for(i = 0, ilen = values.length; i < ilen; ++i){
        curr = scale.getPixelForValue(values[i]);
        updateMinAndPrev();
    }
    prev = undefined;
    for(i = 0, ilen = scale.ticks.length; i < ilen; ++i){
        curr = scale.getPixelForTick(i);
        updateMinAndPrev();
    }
    return min;
}
function computeFitCategoryTraits(index19, ruler, options, stackCount) {
    var thickness = options.barThickness;
    var size, ratio;
    if ((0, _helpersSegmentJs.k)(thickness)) {
        size = ruler.min * options.categoryPercentage;
        ratio = options.barPercentage;
    } else {
        size = thickness * stackCount;
        ratio = 1;
    }
    return {
        chunk: size / stackCount,
        ratio: ratio,
        start: ruler.pixels[index19] - size / 2
    };
}
function computeFlexCategoryTraits(index20, ruler, options, stackCount) {
    var pixels = ruler.pixels;
    var curr = pixels[index20];
    var prev = index20 > 0 ? pixels[index20 - 1] : null;
    var next = index20 < pixels.length - 1 ? pixels[index20 + 1] : null;
    var percent = options.categoryPercentage;
    if (prev === null) prev = curr - (next === null ? ruler.end - ruler.start : next - curr);
    if (next === null) next = curr + curr - prev;
    var start = curr - (curr - Math.min(prev, next)) / 2 * percent;
    var size = Math.abs(next - prev) / 2 * percent;
    return {
        chunk: size / stackCount,
        ratio: options.barPercentage,
        start: start
    };
}
function parseFloatBar(entry, item, vScale, i) {
    var startValue = vScale.parse(entry[0], i);
    var endValue = vScale.parse(entry[1], i);
    var min = Math.min(startValue, endValue);
    var max = Math.max(startValue, endValue);
    var barStart = min;
    var barEnd = max;
    if (Math.abs(min) > Math.abs(max)) {
        barStart = max;
        barEnd = min;
    }
    item[vScale.axis] = barEnd;
    item._custom = {
        barStart: barStart,
        barEnd: barEnd,
        start: startValue,
        end: endValue,
        min: min,
        max: max
    };
}
function parseValue(entry, item, vScale, i) {
    if ((0, _helpersSegmentJs.b)(entry)) parseFloatBar(entry, item, vScale, i);
    else item[vScale.axis] = vScale.parse(entry, i);
    return item;
}
function parseArrayOrPrimitive(meta, data, start, count) {
    var iScale = meta.iScale;
    var vScale = meta.vScale;
    var labels = iScale.getLabels();
    var singleScale = iScale === vScale;
    var parsed = [];
    var i, ilen, item, entry;
    for(i = start, ilen = start + count; i < ilen; ++i){
        entry = data[i];
        item = {};
        item[iScale.axis] = singleScale || iScale.parse(labels[i], i);
        parsed.push(parseValue(entry, item, vScale, i));
    }
    return parsed;
}
function isFloatBar(custom) {
    return custom && custom.barStart !== undefined && custom.barEnd !== undefined;
}
function barSign(size, vScale, actualBase) {
    if (size !== 0) return (0, _helpersSegmentJs.s)(size);
    return (vScale.isHorizontal() ? 1 : -1) * (vScale.min >= actualBase ? 1 : -1);
}
function borderProps(properties) {
    var reverse, start, end, top, bottom;
    if (properties.horizontal) {
        reverse = properties.base > properties.x;
        start = "left";
        end = "right";
    } else {
        reverse = properties.base < properties.y;
        start = "bottom";
        end = "top";
    }
    if (reverse) {
        top = "end";
        bottom = "start";
    } else {
        top = "start";
        bottom = "end";
    }
    return {
        start: start,
        end: end,
        reverse: reverse,
        top: top,
        bottom: bottom
    };
}
function setBorderSkipped(properties, options, stack, index21) {
    var edge = options.borderSkipped;
    var res = {};
    if (!edge) {
        properties.borderSkipped = res;
        return;
    }
    var ref = borderProps(properties), start = ref.start, end = ref.end, reverse = ref.reverse, top = ref.top, bottom = ref.bottom;
    if (edge === "middle" && stack) {
        properties.enableBorderRadius = true;
        if ((stack._top || 0) === index21) edge = top;
        else if ((stack._bottom || 0) === index21) edge = bottom;
        else {
            res[parseEdge(bottom, start, end, reverse)] = true;
            edge = top;
        }
    }
    res[parseEdge(edge, start, end, reverse)] = true;
    properties.borderSkipped = res;
}
function parseEdge(edge, a, b, reverse) {
    if (reverse) {
        edge = swap(edge, a, b);
        edge = startEnd(edge, b, a);
    } else edge = startEnd(edge, a, b);
    return edge;
}
function swap(orig, v1, v2) {
    return orig === v1 ? v2 : orig === v2 ? v1 : orig;
}
function startEnd(v, start, end) {
    return v === "start" ? start : v === "end" ? end : v;
}
function setInflateAmount(properties, param, ratio) {
    var inflateAmount = param.inflateAmount;
    properties.inflateAmount = inflateAmount === "auto" ? ratio === 1 ? 0.33 : 0 : inflateAmount;
}
var BarController = /*#__PURE__*/ function(DatasetController) {
    "use strict";
    (0, _inheritsJsDefault.default)(BarController, DatasetController);
    var _super = (0, _createSuperJsDefault.default)(BarController);
    function BarController() {
        (0, _classCallCheckJsDefault.default)(this, BarController);
        return _super.apply(this, arguments);
    }
    (0, _createClassJsDefault.default)(BarController, [
        {
            key: "parsePrimitiveData",
            value: function parsePrimitiveData(meta, data, start, count) {
                return parseArrayOrPrimitive(meta, data, start, count);
            }
        },
        {
            key: "parseArrayData",
            value: function parseArrayData(meta, data, start, count) {
                return parseArrayOrPrimitive(meta, data, start, count);
            }
        },
        {
            key: "parseObjectData",
            value: function parseObjectData(meta, data, start, count) {
                var iScale = meta.iScale, vScale = meta.vScale;
                var __parsing = this._parsing, _xAxisKey = __parsing.xAxisKey, xAxisKey = _xAxisKey === void 0 ? "x" : _xAxisKey, _yAxisKey = __parsing.yAxisKey, yAxisKey = _yAxisKey === void 0 ? "y" : _yAxisKey;
                var iAxisKey = iScale.axis === "x" ? xAxisKey : yAxisKey;
                var vAxisKey = vScale.axis === "x" ? xAxisKey : yAxisKey;
                var parsed = [];
                var i, ilen, item, obj;
                for(i = start, ilen = start + count; i < ilen; ++i){
                    obj = data[i];
                    item = {};
                    item[iScale.axis] = iScale.parse((0, _helpersSegmentJs.f)(obj, iAxisKey), i);
                    parsed.push(parseValue((0, _helpersSegmentJs.f)(obj, vAxisKey), item, vScale, i));
                }
                return parsed;
            }
        },
        {
            key: "updateRangeFromParsed",
            value: function updateRangeFromParsed(range, scale, parsed, stack) {
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(BarController.prototype), "updateRangeFromParsed", this).call(this, range, scale, parsed, stack);
                var custom = parsed._custom;
                if (custom && scale === this._cachedMeta.vScale) {
                    range.min = Math.min(range.min, custom.min);
                    range.max = Math.max(range.max, custom.max);
                }
            }
        },
        {
            key: "getMaxOverflow",
            value: function getMaxOverflow() {
                return 0;
            }
        },
        {
            key: "getLabelAndValue",
            value: function getLabelAndValue(index22) {
                var meta = this._cachedMeta;
                var iScale = meta.iScale, vScale = meta.vScale;
                var parsed = this.getParsed(index22);
                var custom = parsed._custom;
                var value = isFloatBar(custom) ? "[" + custom.start + ", " + custom.end + "]" : "" + vScale.getLabelForValue(parsed[vScale.axis]);
                return {
                    label: "" + iScale.getLabelForValue(parsed[iScale.axis]),
                    value: value
                };
            }
        },
        {
            key: "initialize",
            value: function initialize() {
                this.enableOptionSharing = true;
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(BarController.prototype), "initialize", this).call(this);
                var meta = this._cachedMeta;
                meta.stack = this.getDataset().stack;
            }
        },
        {
            key: "update",
            value: function update(mode) {
                var meta = this._cachedMeta;
                this.updateElements(meta.data, 0, meta.data.length, mode);
            }
        },
        {
            key: "updateElements",
            value: function updateElements(bars, start, count, mode) {
                var reset = mode === "reset";
                var ref = this, index23 = ref.index, vScale = ref._cachedMeta.vScale;
                var base = vScale.getBasePixel();
                var horizontal = vScale.isHorizontal();
                var ruler = this._getRuler();
                var firstOpts = this.resolveDataElementOptions(start, mode);
                var sharedOptions = this.getSharedOptions(firstOpts);
                var includeOptions = this.includeOptions(mode, sharedOptions);
                this.updateSharedOptions(sharedOptions, mode, firstOpts);
                for(var i = start; i < start + count; i++){
                    var parsed = this.getParsed(i);
                    var vpixels = reset || (0, _helpersSegmentJs.k)(parsed[vScale.axis]) ? {
                        base: base,
                        head: base
                    } : this._calculateBarValuePixels(i);
                    var ipixels = this._calculateBarIndexPixels(i, ruler);
                    var stack = (parsed._stacks || {})[vScale.axis];
                    var properties = {
                        horizontal: horizontal,
                        base: vpixels.base,
                        enableBorderRadius: !stack || isFloatBar(parsed._custom) || index23 === stack._top || index23 === stack._bottom,
                        x: horizontal ? vpixels.head : ipixels.center,
                        y: horizontal ? ipixels.center : vpixels.head,
                        height: horizontal ? ipixels.size : Math.abs(vpixels.size),
                        width: horizontal ? Math.abs(vpixels.size) : ipixels.size
                    };
                    if (includeOptions) properties.options = sharedOptions || this.resolveDataElementOptions(i, bars[i].active ? "active" : mode);
                    var options = properties.options || bars[i].options;
                    setBorderSkipped(properties, options, stack, index23);
                    setInflateAmount(properties, options, ruler.ratio);
                    this.updateElement(bars[i], i, properties, mode);
                }
            }
        },
        {
            key: "_getStacks",
            value: function _getStacks(last, dataIndex) {
                var meta = this._cachedMeta;
                var iScale = meta.iScale;
                var metasets = iScale.getMatchingVisibleMetas(this._type);
                var stacked = iScale.options.stacked;
                var ilen = metasets.length;
                var stacks = [];
                var i, item;
                for(i = 0; i < ilen; ++i){
                    item = metasets[i];
                    if (!item.controller.options.grouped) continue;
                    if (typeof dataIndex !== "undefined") {
                        var val = item.controller.getParsed(dataIndex)[item.controller._cachedMeta.vScale.axis];
                        if ((0, _helpersSegmentJs.k)(val) || isNaN(val)) continue;
                    }
                    if (stacked === false || stacks.indexOf(item.stack) === -1 || stacked === undefined && item.stack === undefined) stacks.push(item.stack);
                    if (item.index === last) break;
                }
                if (!stacks.length) stacks.push(undefined);
                return stacks;
            }
        },
        {
            key: "_getStackCount",
            value: function _getStackCount(index24) {
                return this._getStacks(undefined, index24).length;
            }
        },
        {
            key: "_getStackIndex",
            value: function _getStackIndex(datasetIndex, name, dataIndex) {
                var stacks = this._getStacks(datasetIndex, dataIndex);
                var index25 = name !== undefined ? stacks.indexOf(name) : -1;
                return index25 === -1 ? stacks.length - 1 : index25;
            }
        },
        {
            key: "_getRuler",
            value: function _getRuler() {
                var opts = this.options;
                var meta = this._cachedMeta;
                var iScale = meta.iScale;
                var pixels = [];
                var i, ilen;
                for(i = 0, ilen = meta.data.length; i < ilen; ++i)pixels.push(iScale.getPixelForValue(this.getParsed(i)[iScale.axis], i));
                var barThickness = opts.barThickness;
                var min = barThickness || computeMinSampleSize(meta);
                return {
                    min: min,
                    pixels: pixels,
                    start: iScale._startPixel,
                    end: iScale._endPixel,
                    stackCount: this._getStackCount(),
                    scale: iScale,
                    grouped: opts.grouped,
                    ratio: barThickness ? 1 : opts.categoryPercentage * opts.barPercentage
                };
            }
        },
        {
            key: "_calculateBarValuePixels",
            value: function _calculateBarValuePixels(index26) {
                var ref = this, __cachedMeta = ref._cachedMeta, vScale = __cachedMeta.vScale, _stacked = __cachedMeta._stacked, _options = ref.options, baseValue = _options.base, minBarLength = _options.minBarLength;
                var actualBase = baseValue || 0;
                var parsed = this.getParsed(index26);
                var custom = parsed._custom;
                var floating = isFloatBar(custom);
                var value = parsed[vScale.axis];
                var start = 0;
                var length = _stacked ? this.applyStack(vScale, parsed, _stacked) : value;
                var head, size;
                if (length !== value) {
                    start = length - value;
                    length = value;
                }
                if (floating) {
                    value = custom.barStart;
                    length = custom.barEnd - custom.barStart;
                    if (value !== 0 && (0, _helpersSegmentJs.s)(value) !== (0, _helpersSegmentJs.s)(custom.barEnd)) start = 0;
                    start += value;
                }
                var startValue = !(0, _helpersSegmentJs.k)(baseValue) && !floating ? baseValue : start;
                var base = vScale.getPixelForValue(startValue);
                if (this.chart.getDataVisibility(index26)) head = vScale.getPixelForValue(start + length);
                else head = base;
                size = head - base;
                if (Math.abs(size) < minBarLength) {
                    size = barSign(size, vScale, actualBase) * minBarLength;
                    if (value === actualBase) base -= size / 2;
                    var startPixel = vScale.getPixelForDecimal(0);
                    var endPixel = vScale.getPixelForDecimal(1);
                    var min = Math.min(startPixel, endPixel);
                    var max = Math.max(startPixel, endPixel);
                    base = Math.max(Math.min(base, max), min);
                    head = base + size;
                }
                if (base === vScale.getPixelForValue(actualBase)) {
                    var halfGrid = (0, _helpersSegmentJs.s)(size) * vScale.getLineWidthForValue(actualBase) / 2;
                    base += halfGrid;
                    size -= halfGrid;
                }
                return {
                    size: size,
                    base: base,
                    head: head,
                    center: head + size / 2
                };
            }
        },
        {
            key: "_calculateBarIndexPixels",
            value: function _calculateBarIndexPixels(index27, ruler) {
                var scale = ruler.scale;
                var options = this.options;
                var skipNull = options.skipNull;
                var maxBarThickness = (0, _helpersSegmentJs.v)(options.maxBarThickness, Infinity);
                var center, size;
                if (ruler.grouped) {
                    var stackCount = skipNull ? this._getStackCount(index27) : ruler.stackCount;
                    var range = options.barThickness === "flex" ? computeFlexCategoryTraits(index27, ruler, options, stackCount) : computeFitCategoryTraits(index27, ruler, options, stackCount);
                    var stackIndex = this._getStackIndex(this.index, this._cachedMeta.stack, skipNull ? index27 : undefined);
                    center = range.start + range.chunk * stackIndex + range.chunk / 2;
                    size = Math.min(maxBarThickness, range.chunk * range.ratio);
                } else {
                    center = scale.getPixelForValue(this.getParsed(index27)[scale.axis], index27);
                    size = Math.min(maxBarThickness, ruler.min * ruler.ratio);
                }
                return {
                    base: center - size / 2,
                    head: center + size / 2,
                    center: center,
                    size: size
                };
            }
        },
        {
            key: "draw",
            value: function draw2() {
                var meta = this._cachedMeta;
                var vScale = meta.vScale;
                var rects = meta.data;
                var ilen = rects.length;
                var i = 0;
                for(; i < ilen; ++i)if (this.getParsed(i)[vScale.axis] !== null) rects[i].draw(this._ctx);
            }
        }
    ]);
    return BarController;
}(DatasetController);
BarController.id = "bar";
BarController.defaults = {
    datasetElementType: false,
    dataElementType: "bar",
    categoryPercentage: 0.8,
    barPercentage: 0.9,
    grouped: true,
    animations: {
        numbers: {
            type: "number",
            properties: [
                "x",
                "y",
                "base",
                "width",
                "height"
            ]
        }
    }
};
BarController.overrides = {
    scales: {
        _index_: {
            type: "category",
            offset: true,
            grid: {
                offset: true
            }
        },
        _value_: {
            type: "linear",
            beginAtZero: true
        }
    }
};
var BubbleController = /*#__PURE__*/ function(DatasetController) {
    "use strict";
    (0, _inheritsJsDefault.default)(BubbleController, DatasetController);
    var _super = (0, _createSuperJsDefault.default)(BubbleController);
    function BubbleController() {
        (0, _classCallCheckJsDefault.default)(this, BubbleController);
        return _super.apply(this, arguments);
    }
    (0, _createClassJsDefault.default)(BubbleController, [
        {
            key: "initialize",
            value: function initialize() {
                this.enableOptionSharing = true;
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(BubbleController.prototype), "initialize", this).call(this);
            }
        },
        {
            key: "parsePrimitiveData",
            value: function parsePrimitiveData(meta, data, start, count) {
                var parsed = (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(BubbleController.prototype), "parsePrimitiveData", this).call(this, meta, data, start, count);
                for(var i = 0; i < parsed.length; i++)parsed[i]._custom = this.resolveDataElementOptions(i + start).radius;
                return parsed;
            }
        },
        {
            key: "parseArrayData",
            value: function parseArrayData(meta, data, start, count) {
                var parsed = (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(BubbleController.prototype), "parseArrayData", this).call(this, meta, data, start, count);
                for(var i = 0; i < parsed.length; i++){
                    var item = data[start + i];
                    parsed[i]._custom = (0, _helpersSegmentJs.v)(item[2], this.resolveDataElementOptions(i + start).radius);
                }
                return parsed;
            }
        },
        {
            key: "parseObjectData",
            value: function parseObjectData(meta, data, start, count) {
                var parsed = (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(BubbleController.prototype), "parseObjectData", this).call(this, meta, data, start, count);
                for(var i = 0; i < parsed.length; i++){
                    var item = data[start + i];
                    parsed[i]._custom = (0, _helpersSegmentJs.v)(item && item.r && +item.r, this.resolveDataElementOptions(i + start).radius);
                }
                return parsed;
            }
        },
        {
            key: "getMaxOverflow",
            value: function getMaxOverflow() {
                var data = this._cachedMeta.data;
                var max = 0;
                for(var i = data.length - 1; i >= 0; --i)max = Math.max(max, data[i].size(this.resolveDataElementOptions(i)) / 2);
                return max > 0 && max;
            }
        },
        {
            key: "getLabelAndValue",
            value: function getLabelAndValue(index28) {
                var meta = this._cachedMeta;
                var xScale = meta.xScale, yScale = meta.yScale;
                var parsed = this.getParsed(index28);
                var x = xScale.getLabelForValue(parsed.x);
                var y = yScale.getLabelForValue(parsed.y);
                var r = parsed._custom;
                return {
                    label: meta.label,
                    value: "(" + x + ", " + y + (r ? ", " + r : "") + ")"
                };
            }
        },
        {
            key: "update",
            value: function update(mode) {
                var points = this._cachedMeta.data;
                this.updateElements(points, 0, points.length, mode);
            }
        },
        {
            key: "updateElements",
            value: function updateElements(points, start, count, mode) {
                var reset = mode === "reset";
                var __cachedMeta = this._cachedMeta, iScale = __cachedMeta.iScale, vScale = __cachedMeta.vScale;
                var firstOpts = this.resolveDataElementOptions(start, mode);
                var sharedOptions = this.getSharedOptions(firstOpts);
                var includeOptions = this.includeOptions(mode, sharedOptions);
                var iAxis = iScale.axis;
                var vAxis = vScale.axis;
                for(var i = start; i < start + count; i++){
                    var point = points[i];
                    var parsed = !reset && this.getParsed(i);
                    var properties = {};
                    var iPixel = properties[iAxis] = reset ? iScale.getPixelForDecimal(0.5) : iScale.getPixelForValue(parsed[iAxis]);
                    var vPixel = properties[vAxis] = reset ? vScale.getBasePixel() : vScale.getPixelForValue(parsed[vAxis]);
                    properties.skip = isNaN(iPixel) || isNaN(vPixel);
                    if (includeOptions) {
                        properties.options = this.resolveDataElementOptions(i, point.active ? "active" : mode);
                        if (reset) properties.options.radius = 0;
                    }
                    this.updateElement(point, i, properties, mode);
                }
                this.updateSharedOptions(sharedOptions, mode, firstOpts);
            }
        },
        {
            key: "resolveDataElementOptions",
            value: function resolveDataElementOptions(index29, mode) {
                var parsed = this.getParsed(index29);
                var values = (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(BubbleController.prototype), "resolveDataElementOptions", this).call(this, index29, mode);
                if (values.$shared) values = Object.assign({}, values, {
                    $shared: false
                });
                var radius = values.radius;
                if (mode !== "active") values.radius = 0;
                values.radius += (0, _helpersSegmentJs.v)(parsed && parsed._custom, radius);
                return values;
            }
        }
    ]);
    return BubbleController;
}(DatasetController);
BubbleController.id = "bubble";
BubbleController.defaults = {
    datasetElementType: false,
    dataElementType: "point",
    animations: {
        numbers: {
            type: "number",
            properties: [
                "x",
                "y",
                "borderWidth",
                "radius"
            ]
        }
    }
};
BubbleController.overrides = {
    scales: {
        x: {
            type: "linear"
        },
        y: {
            type: "linear"
        }
    },
    plugins: {
        tooltip: {
            callbacks: {
                title: function() {
                    return "";
                }
            }
        }
    }
};
function getRatioAndOffset(rotation, circumference, cutout) {
    var ratioX = 1;
    var ratioY = 1;
    var offsetX = 0;
    var offsetY = 0;
    if (circumference < (0, _helpersSegmentJs.T)) {
        var startAngle = rotation;
        var endAngle = startAngle + circumference;
        var startX = Math.cos(startAngle);
        var startY = Math.sin(startAngle);
        var endX = Math.cos(endAngle);
        var endY = Math.sin(endAngle);
        var calcMax = function(angle, a, b) {
            return (0, _helpersSegmentJs.p)(angle, startAngle, endAngle, true) ? 1 : Math.max(a, a * cutout, b, b * cutout);
        };
        var calcMin = function(angle, a, b) {
            return (0, _helpersSegmentJs.p)(angle, startAngle, endAngle, true) ? -1 : Math.min(a, a * cutout, b, b * cutout);
        };
        var maxX = calcMax(0, startX, endX);
        var maxY = calcMax((0, _helpersSegmentJs.H), startY, endY);
        var minX = calcMin((0, _helpersSegmentJs.P), startX, endX);
        var minY = calcMin((0, _helpersSegmentJs.P) + (0, _helpersSegmentJs.H), startY, endY);
        ratioX = (maxX - minX) / 2;
        ratioY = (maxY - minY) / 2;
        offsetX = -(maxX + minX) / 2;
        offsetY = -(maxY + minY) / 2;
    }
    return {
        ratioX: ratioX,
        ratioY: ratioY,
        offsetX: offsetX,
        offsetY: offsetY
    };
}
var DoughnutController = /*#__PURE__*/ function(DatasetController) {
    "use strict";
    (0, _inheritsJsDefault.default)(DoughnutController, DatasetController);
    var _super = (0, _createSuperJsDefault.default)(DoughnutController);
    function DoughnutController(chart, datasetIndex) {
        (0, _classCallCheckJsDefault.default)(this, DoughnutController);
        var _this;
        _this = _super.call(this, chart, datasetIndex);
        _this.enableOptionSharing = true;
        _this.innerRadius = undefined;
        _this.outerRadius = undefined;
        _this.offsetX = undefined;
        _this.offsetY = undefined;
        return _this;
    }
    (0, _createClassJsDefault.default)(DoughnutController, [
        {
            key: "linkScales",
            value: function linkScales() {}
        },
        {
            key: "parse",
            value: function parse1(start, count) {
                var data = this.getDataset().data;
                var meta = this._cachedMeta;
                if (this._parsing === false) meta._parsed = data;
                else {
                    var getter = function(i) {
                        return +data[i];
                    };
                    if ((0, _helpersSegmentJs.i)(data[start])) {
                        var __parsing = this._parsing, _key = __parsing.key, key = _key === void 0 ? "value" : _key;
                        getter = function(i) {
                            return +(0, _helpersSegmentJs.f)(data[i], key);
                        };
                    }
                    var i1, ilen;
                    for(i1 = start, ilen = start + count; i1 < ilen; ++i1)meta._parsed[i1] = getter(i1);
                }
            }
        },
        {
            key: "_getRotation",
            value: function _getRotation() {
                return (0, _helpersSegmentJs.t)(this.options.rotation - 90);
            }
        },
        {
            key: "_getCircumference",
            value: function _getCircumference() {
                return (0, _helpersSegmentJs.t)(this.options.circumference);
            }
        },
        {
            key: "_getRotationExtents",
            value: function _getRotationExtents() {
                var min = (0, _helpersSegmentJs.T);
                var max = -(0, _helpersSegmentJs.T);
                for(var i = 0; i < this.chart.data.datasets.length; ++i)if (this.chart.isDatasetVisible(i)) {
                    var controller = this.chart.getDatasetMeta(i).controller;
                    var rotation = controller._getRotation();
                    var circumference = controller._getCircumference();
                    min = Math.min(min, rotation);
                    max = Math.max(max, rotation + circumference);
                }
                return {
                    rotation: min,
                    circumference: max - min
                };
            }
        },
        {
            key: "update",
            value: function update(mode) {
                var chart = this.chart;
                var chartArea = chart.chartArea;
                var meta = this._cachedMeta;
                var arcs = meta.data;
                var spacing = this.getMaxBorderWidth() + this.getMaxOffset(arcs) + this.options.spacing;
                var maxSize = Math.max((Math.min(chartArea.width, chartArea.height) - spacing) / 2, 0);
                var cutout = Math.min((0, _helpersSegmentJs.m)(this.options.cutout, maxSize), 1);
                var chartWeight = this._getRingWeight(this.index);
                var ref = this._getRotationExtents(), circumference = ref.circumference, rotation = ref.rotation;
                var ref1 = getRatioAndOffset(rotation, circumference, cutout), ratioX = ref1.ratioX, ratioY = ref1.ratioY, offsetX = ref1.offsetX, offsetY = ref1.offsetY;
                var maxWidth = (chartArea.width - spacing) / ratioX;
                var maxHeight = (chartArea.height - spacing) / ratioY;
                var maxRadius = Math.max(Math.min(maxWidth, maxHeight) / 2, 0);
                var outerRadius = (0, _helpersSegmentJs.n)(this.options.radius, maxRadius);
                var innerRadius = Math.max(outerRadius * cutout, 0);
                var radiusLength = (outerRadius - innerRadius) / this._getVisibleDatasetWeightTotal();
                this.offsetX = offsetX * outerRadius;
                this.offsetY = offsetY * outerRadius;
                meta.total = this.calculateTotal();
                this.outerRadius = outerRadius - radiusLength * this._getRingWeightOffset(this.index);
                this.innerRadius = Math.max(this.outerRadius - radiusLength * chartWeight, 0);
                this.updateElements(arcs, 0, arcs.length, mode);
            }
        },
        {
            key: "_circumference",
            value: function _circumference(i, reset) {
                var opts = this.options;
                var meta = this._cachedMeta;
                var circumference = this._getCircumference();
                if (reset && opts.animation.animateRotate || !this.chart.getDataVisibility(i) || meta._parsed[i] === null || meta.data[i].hidden) return 0;
                return this.calculateCircumference(meta._parsed[i] * circumference / (0, _helpersSegmentJs.T));
            }
        },
        {
            key: "updateElements",
            value: function updateElements(arcs, start, count, mode) {
                var reset = mode === "reset";
                var chart = this.chart;
                var chartArea = chart.chartArea;
                var opts = chart.options;
                var animationOpts = opts.animation;
                var centerX = (chartArea.left + chartArea.right) / 2;
                var centerY = (chartArea.top + chartArea.bottom) / 2;
                var animateScale = reset && animationOpts.animateScale;
                var innerRadius = animateScale ? 0 : this.innerRadius;
                var outerRadius = animateScale ? 0 : this.outerRadius;
                var firstOpts = this.resolveDataElementOptions(start, mode);
                var sharedOptions = this.getSharedOptions(firstOpts);
                var includeOptions = this.includeOptions(mode, sharedOptions);
                var startAngle = this._getRotation();
                var i;
                for(i = 0; i < start; ++i)startAngle += this._circumference(i, reset);
                for(i = start; i < start + count; ++i){
                    var circumference = this._circumference(i, reset);
                    var arc = arcs[i];
                    var properties = {
                        x: centerX + this.offsetX,
                        y: centerY + this.offsetY,
                        startAngle: startAngle,
                        endAngle: startAngle + circumference,
                        circumference: circumference,
                        outerRadius: outerRadius,
                        innerRadius: innerRadius
                    };
                    if (includeOptions) properties.options = sharedOptions || this.resolveDataElementOptions(i, arc.active ? "active" : mode);
                    startAngle += circumference;
                    this.updateElement(arc, i, properties, mode);
                }
                this.updateSharedOptions(sharedOptions, mode, firstOpts);
            }
        },
        {
            key: "calculateTotal",
            value: function calculateTotal() {
                var meta = this._cachedMeta;
                var metaData = meta.data;
                var total = 0;
                var i;
                for(i = 0; i < metaData.length; i++){
                    var value = meta._parsed[i];
                    if (value !== null && !isNaN(value) && this.chart.getDataVisibility(i) && !metaData[i].hidden) total += Math.abs(value);
                }
                return total;
            }
        },
        {
            key: "calculateCircumference",
            value: function calculateCircumference(value) {
                var total = this._cachedMeta.total;
                if (total > 0 && !isNaN(value)) return (0, _helpersSegmentJs.T) * (Math.abs(value) / total);
                return 0;
            }
        },
        {
            key: "getLabelAndValue",
            value: function getLabelAndValue(index30) {
                var meta = this._cachedMeta;
                var chart = this.chart;
                var labels = chart.data.labels || [];
                var value = (0, _helpersSegmentJs.o)(meta._parsed[index30], chart.options.locale);
                return {
                    label: labels[index30] || "",
                    value: value
                };
            }
        },
        {
            key: "getMaxBorderWidth",
            value: function getMaxBorderWidth(arcs) {
                var max = 0;
                var chart = this.chart;
                var i, ilen, meta, controller, options;
                if (!arcs) {
                    for(i = 0, ilen = chart.data.datasets.length; i < ilen; ++i)if (chart.isDatasetVisible(i)) {
                        meta = chart.getDatasetMeta(i);
                        arcs = meta.data;
                        controller = meta.controller;
                        break;
                    }
                }
                if (!arcs) return 0;
                for(i = 0, ilen = arcs.length; i < ilen; ++i){
                    options = controller.resolveDataElementOptions(i);
                    if (options.borderAlign !== "inner") max = Math.max(max, options.borderWidth || 0, options.hoverBorderWidth || 0);
                }
                return max;
            }
        },
        {
            key: "getMaxOffset",
            value: function getMaxOffset(arcs) {
                var max = 0;
                for(var i = 0, ilen = arcs.length; i < ilen; ++i){
                    var options = this.resolveDataElementOptions(i);
                    max = Math.max(max, options.offset || 0, options.hoverOffset || 0);
                }
                return max;
            }
        },
        {
            key: "_getRingWeightOffset",
            value: function _getRingWeightOffset(datasetIndex) {
                var ringWeightOffset = 0;
                for(var i = 0; i < datasetIndex; ++i)if (this.chart.isDatasetVisible(i)) ringWeightOffset += this._getRingWeight(i);
                return ringWeightOffset;
            }
        },
        {
            key: "_getRingWeight",
            value: function _getRingWeight(datasetIndex) {
                return Math.max((0, _helpersSegmentJs.v)(this.chart.data.datasets[datasetIndex].weight, 1), 0);
            }
        },
        {
            key: "_getVisibleDatasetWeightTotal",
            value: function _getVisibleDatasetWeightTotal() {
                return this._getRingWeightOffset(this.chart.data.datasets.length) || 1;
            }
        }
    ]);
    return DoughnutController;
}(DatasetController);
DoughnutController.id = "doughnut";
DoughnutController.defaults = {
    datasetElementType: false,
    dataElementType: "arc",
    animation: {
        animateRotate: true,
        animateScale: false
    },
    animations: {
        numbers: {
            type: "number",
            properties: [
                "circumference",
                "endAngle",
                "innerRadius",
                "outerRadius",
                "startAngle",
                "x",
                "y",
                "offset",
                "borderWidth",
                "spacing"
            ]
        }
    },
    cutout: "50%",
    rotation: 0,
    circumference: 360,
    radius: "100%",
    spacing: 0,
    indexAxis: "r"
};
DoughnutController.descriptors = {
    _scriptable: function(name) {
        return name !== "spacing";
    },
    _indexable: function(name) {
        return name !== "spacing";
    }
};
DoughnutController.overrides = {
    aspectRatio: 1,
    plugins: {
        legend: {
            labels: {
                generateLabels: function(chart) {
                    var data = chart.data;
                    if (data.labels.length && data.datasets.length) {
                        var _options = chart.legend.options, pointStyle = _options.labels.pointStyle;
                        return data.labels.map(function(label, i) {
                            var meta = chart.getDatasetMeta(0);
                            var style = meta.controller.getStyle(i);
                            return {
                                text: label,
                                fillStyle: style.backgroundColor,
                                strokeStyle: style.borderColor,
                                lineWidth: style.borderWidth,
                                pointStyle: pointStyle,
                                hidden: !chart.getDataVisibility(i),
                                index: i
                            };
                        });
                    }
                    return [];
                }
            },
            onClick: function(e, legendItem, legend) {
                legend.chart.toggleDataVisibility(legendItem.index);
                legend.chart.update();
            }
        },
        tooltip: {
            callbacks: {
                title: function() {
                    return "";
                },
                label: function(tooltipItem) {
                    var dataLabel = tooltipItem.label;
                    var value = ": " + tooltipItem.formattedValue;
                    if ((0, _helpersSegmentJs.b)(dataLabel)) {
                        dataLabel = dataLabel.slice();
                        dataLabel[0] += value;
                    } else dataLabel += value;
                    return dataLabel;
                }
            }
        }
    }
};
var LineController = /*#__PURE__*/ function(DatasetController) {
    "use strict";
    (0, _inheritsJsDefault.default)(LineController, DatasetController);
    var _super = (0, _createSuperJsDefault.default)(LineController);
    function LineController() {
        (0, _classCallCheckJsDefault.default)(this, LineController);
        return _super.apply(this, arguments);
    }
    (0, _createClassJsDefault.default)(LineController, [
        {
            key: "initialize",
            value: function initialize() {
                this.enableOptionSharing = true;
                this.supportsDecimation = true;
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(LineController.prototype), "initialize", this).call(this);
            }
        },
        {
            key: "update",
            value: function update(mode) {
                var meta = this._cachedMeta;
                var line = meta.dataset, tmp = meta.data, points = tmp === void 0 ? [] : tmp, _dataset = meta._dataset;
                var animationsDisabled = this.chart._animationsDisabled;
                var ref = getStartAndCountOfVisiblePoints(meta, points, animationsDisabled), start = ref.start, count = ref.count;
                this._drawStart = start;
                this._drawCount = count;
                if (scaleRangesChanged(meta)) {
                    start = 0;
                    count = points.length;
                }
                line._chart = this.chart;
                line._datasetIndex = this.index;
                line._decimated = !!_dataset._decimated;
                line.points = points;
                var options = this.resolveDatasetElementOptions(mode);
                if (!this.options.showLine) options.borderWidth = 0;
                options.segment = this.options.segment;
                this.updateElement(line, undefined, {
                    animated: !animationsDisabled,
                    options: options
                }, mode);
                this.updateElements(points, start, count, mode);
            }
        },
        {
            key: "updateElements",
            value: function updateElements(points, start, count, mode) {
                var reset = mode === "reset";
                var __cachedMeta = this._cachedMeta, iScale = __cachedMeta.iScale, vScale = __cachedMeta.vScale, _stacked = __cachedMeta._stacked, _dataset = __cachedMeta._dataset;
                var firstOpts = this.resolveDataElementOptions(start, mode);
                var sharedOptions = this.getSharedOptions(firstOpts);
                var includeOptions = this.includeOptions(mode, sharedOptions);
                var iAxis = iScale.axis;
                var vAxis = vScale.axis;
                var _options = this.options, spanGaps = _options.spanGaps, segment = _options.segment;
                var maxGapLength = (0, _helpersSegmentJs.q)(spanGaps) ? spanGaps : Number.POSITIVE_INFINITY;
                var directUpdate = this.chart._animationsDisabled || reset || mode === "none";
                var prevParsed = start > 0 && this.getParsed(start - 1);
                for(var i = start; i < start + count; ++i){
                    var point = points[i];
                    var parsed = this.getParsed(i);
                    var properties = directUpdate ? point : {};
                    var nullData = (0, _helpersSegmentJs.k)(parsed[vAxis]);
                    var iPixel = properties[iAxis] = iScale.getPixelForValue(parsed[iAxis], i);
                    var vPixel = properties[vAxis] = reset || nullData ? vScale.getBasePixel() : vScale.getPixelForValue(_stacked ? this.applyStack(vScale, parsed, _stacked) : parsed[vAxis], i);
                    properties.skip = isNaN(iPixel) || isNaN(vPixel) || nullData;
                    properties.stop = i > 0 && Math.abs(parsed[iAxis] - prevParsed[iAxis]) > maxGapLength;
                    if (segment) {
                        properties.parsed = parsed;
                        properties.raw = _dataset.data[i];
                    }
                    if (includeOptions) properties.options = sharedOptions || this.resolveDataElementOptions(i, point.active ? "active" : mode);
                    if (!directUpdate) this.updateElement(point, i, properties, mode);
                    prevParsed = parsed;
                }
                this.updateSharedOptions(sharedOptions, mode, firstOpts);
            }
        },
        {
            key: "getMaxOverflow",
            value: function getMaxOverflow() {
                var meta = this._cachedMeta;
                var dataset = meta.dataset;
                var border = dataset.options && dataset.options.borderWidth || 0;
                var data = meta.data || [];
                if (!data.length) return border;
                var firstPoint = data[0].size(this.resolveDataElementOptions(0));
                var lastPoint = data[data.length - 1].size(this.resolveDataElementOptions(data.length - 1));
                return Math.max(border, firstPoint, lastPoint) / 2;
            }
        },
        {
            key: "draw",
            value: function draw2() {
                var meta = this._cachedMeta;
                meta.dataset.updateControlPoints(this.chart.chartArea, meta.iScale.axis);
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(LineController.prototype), "draw", this).call(this);
            }
        }
    ]);
    return LineController;
}(DatasetController);
LineController.id = "line";
LineController.defaults = {
    datasetElementType: "line",
    dataElementType: "point",
    showLine: true,
    spanGaps: false
};
LineController.overrides = {
    scales: {
        _index_: {
            type: "category"
        },
        _value_: {
            type: "linear"
        }
    }
};
function getStartAndCountOfVisiblePoints(meta, points, animationsDisabled) {
    var pointCount = points.length;
    var start = 0;
    var count = pointCount;
    if (meta._sorted) {
        var iScale = meta.iScale, _parsed = meta._parsed;
        var axis = iScale.axis;
        var ref = iScale.getUserBounds(), min = ref.min, max = ref.max, minDefined = ref.minDefined, maxDefined = ref.maxDefined;
        if (minDefined) start = (0, _helpersSegmentJs.w)(Math.min((0, _helpersSegmentJs.x)(_parsed, iScale.axis, min).lo, animationsDisabled ? pointCount : (0, _helpersSegmentJs.x)(points, axis, iScale.getPixelForValue(min)).lo), 0, pointCount - 1);
        if (maxDefined) count = (0, _helpersSegmentJs.w)(Math.max((0, _helpersSegmentJs.x)(_parsed, iScale.axis, max).hi + 1, animationsDisabled ? 0 : (0, _helpersSegmentJs.x)(points, axis, iScale.getPixelForValue(max)).hi + 1), start, pointCount) - start;
        else count = pointCount - start;
    }
    return {
        start: start,
        count: count
    };
}
function scaleRangesChanged(meta) {
    var xScale = meta.xScale, yScale = meta.yScale, _scaleRanges = meta._scaleRanges;
    var newRanges = {
        xmin: xScale.min,
        xmax: xScale.max,
        ymin: yScale.min,
        ymax: yScale.max
    };
    if (!_scaleRanges) {
        meta._scaleRanges = newRanges;
        return true;
    }
    var changed = _scaleRanges.xmin !== xScale.min || _scaleRanges.xmax !== xScale.max || _scaleRanges.ymin !== yScale.min || _scaleRanges.ymax !== yScale.max;
    Object.assign(_scaleRanges, newRanges);
    return changed;
}
var PolarAreaController = /*#__PURE__*/ function(DatasetController) {
    "use strict";
    (0, _inheritsJsDefault.default)(PolarAreaController, DatasetController);
    var _super = (0, _createSuperJsDefault.default)(PolarAreaController);
    function PolarAreaController(chart, datasetIndex) {
        (0, _classCallCheckJsDefault.default)(this, PolarAreaController);
        var _this;
        _this = _super.call(this, chart, datasetIndex);
        _this.innerRadius = undefined;
        _this.outerRadius = undefined;
        return _this;
    }
    (0, _createClassJsDefault.default)(PolarAreaController, [
        {
            key: "getLabelAndValue",
            value: function getLabelAndValue(index31) {
                var meta = this._cachedMeta;
                var chart = this.chart;
                var labels = chart.data.labels || [];
                var value = (0, _helpersSegmentJs.o)(meta._parsed[index31].r, chart.options.locale);
                return {
                    label: labels[index31] || "",
                    value: value
                };
            }
        },
        {
            key: "parseObjectData",
            value: function parseObjectData(meta, data, start, count) {
                return (0, _helpersSegmentJs.y).bind(this)(meta, data, start, count);
            }
        },
        {
            key: "update",
            value: function update(mode) {
                var arcs = this._cachedMeta.data;
                this._updateRadius();
                this.updateElements(arcs, 0, arcs.length, mode);
            }
        },
        {
            key: "getMinMax",
            value: function getMinMax() {
                var _this = this;
                var meta = this._cachedMeta;
                var range = {
                    min: Number.POSITIVE_INFINITY,
                    max: Number.NEGATIVE_INFINITY
                };
                meta.data.forEach(function(element, index32) {
                    var parsed = _this.getParsed(index32).r;
                    if (!isNaN(parsed) && _this.chart.getDataVisibility(index32)) {
                        if (parsed < range.min) range.min = parsed;
                        if (parsed > range.max) range.max = parsed;
                    }
                });
                return range;
            }
        },
        {
            key: "_updateRadius",
            value: function _updateRadius() {
                var chart = this.chart;
                var chartArea = chart.chartArea;
                var opts = chart.options;
                var minSize = Math.min(chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
                var outerRadius = Math.max(minSize / 2, 0);
                var innerRadius = Math.max(opts.cutoutPercentage ? outerRadius / 100 * opts.cutoutPercentage : 1, 0);
                var radiusLength = (outerRadius - innerRadius) / chart.getVisibleDatasetCount();
                this.outerRadius = outerRadius - radiusLength * this.index;
                this.innerRadius = this.outerRadius - radiusLength;
            }
        },
        {
            key: "updateElements",
            value: function updateElements(arcs, start, count, mode) {
                var reset = mode === "reset";
                var chart = this.chart;
                var opts = chart.options;
                var animationOpts = opts.animation;
                var scale = this._cachedMeta.rScale;
                var centerX = scale.xCenter;
                var centerY = scale.yCenter;
                var datasetStartAngle = scale.getIndexAngle(0) - 0.5 * (0, _helpersSegmentJs.P);
                var angle = datasetStartAngle;
                var i;
                var defaultAngle = 360 / this.countVisibleElements();
                for(i = 0; i < start; ++i)angle += this._computeAngle(i, mode, defaultAngle);
                for(i = start; i < start + count; i++){
                    var arc = arcs[i];
                    var startAngle = angle;
                    var endAngle = angle + this._computeAngle(i, mode, defaultAngle);
                    var outerRadius = chart.getDataVisibility(i) ? scale.getDistanceFromCenterForValue(this.getParsed(i).r) : 0;
                    angle = endAngle;
                    if (reset) {
                        if (animationOpts.animateScale) outerRadius = 0;
                        if (animationOpts.animateRotate) startAngle = endAngle = datasetStartAngle;
                    }
                    var properties = {
                        x: centerX,
                        y: centerY,
                        innerRadius: 0,
                        outerRadius: outerRadius,
                        startAngle: startAngle,
                        endAngle: endAngle,
                        options: this.resolveDataElementOptions(i, arc.active ? "active" : mode)
                    };
                    this.updateElement(arc, i, properties, mode);
                }
            }
        },
        {
            key: "countVisibleElements",
            value: function countVisibleElements() {
                var _this = this;
                var meta = this._cachedMeta;
                var count = 0;
                meta.data.forEach(function(element, index33) {
                    if (!isNaN(_this.getParsed(index33).r) && _this.chart.getDataVisibility(index33)) count++;
                });
                return count;
            }
        },
        {
            key: "_computeAngle",
            value: function _computeAngle(index34, mode, defaultAngle) {
                return this.chart.getDataVisibility(index34) ? (0, _helpersSegmentJs.t)(this.resolveDataElementOptions(index34, mode).angle || defaultAngle) : 0;
            }
        }
    ]);
    return PolarAreaController;
}(DatasetController);
PolarAreaController.id = "polarArea";
PolarAreaController.defaults = {
    dataElementType: "arc",
    animation: {
        animateRotate: true,
        animateScale: true
    },
    animations: {
        numbers: {
            type: "number",
            properties: [
                "x",
                "y",
                "startAngle",
                "endAngle",
                "innerRadius",
                "outerRadius"
            ]
        }
    },
    indexAxis: "r",
    startAngle: 0
};
PolarAreaController.overrides = {
    aspectRatio: 1,
    plugins: {
        legend: {
            labels: {
                generateLabels: function(chart) {
                    var data = chart.data;
                    if (data.labels.length && data.datasets.length) {
                        var _options = chart.legend.options, pointStyle = _options.labels.pointStyle;
                        return data.labels.map(function(label, i) {
                            var meta = chart.getDatasetMeta(0);
                            var style = meta.controller.getStyle(i);
                            return {
                                text: label,
                                fillStyle: style.backgroundColor,
                                strokeStyle: style.borderColor,
                                lineWidth: style.borderWidth,
                                pointStyle: pointStyle,
                                hidden: !chart.getDataVisibility(i),
                                index: i
                            };
                        });
                    }
                    return [];
                }
            },
            onClick: function(e, legendItem, legend) {
                legend.chart.toggleDataVisibility(legendItem.index);
                legend.chart.update();
            }
        },
        tooltip: {
            callbacks: {
                title: function() {
                    return "";
                },
                label: function(context) {
                    return context.chart.data.labels[context.dataIndex] + ": " + context.formattedValue;
                }
            }
        }
    },
    scales: {
        r: {
            type: "radialLinear",
            angleLines: {
                display: false
            },
            beginAtZero: true,
            grid: {
                circular: true
            },
            pointLabels: {
                display: false
            },
            startAngle: 0
        }
    }
};
var PieController = /*#__PURE__*/ function(DoughnutController) {
    "use strict";
    (0, _inheritsJsDefault.default)(PieController, DoughnutController);
    var _super = (0, _createSuperJsDefault.default)(PieController);
    function PieController() {
        (0, _classCallCheckJsDefault.default)(this, PieController);
        return _super.apply(this, arguments);
    }
    return PieController;
}(DoughnutController);
PieController.id = "pie";
PieController.defaults = {
    cutout: 0,
    rotation: 0,
    circumference: 360,
    radius: "100%"
};
var RadarController = /*#__PURE__*/ function(DatasetController) {
    "use strict";
    (0, _inheritsJsDefault.default)(RadarController, DatasetController);
    var _super = (0, _createSuperJsDefault.default)(RadarController);
    function RadarController() {
        (0, _classCallCheckJsDefault.default)(this, RadarController);
        return _super.apply(this, arguments);
    }
    (0, _createClassJsDefault.default)(RadarController, [
        {
            key: "getLabelAndValue",
            value: function getLabelAndValue(index35) {
                var vScale = this._cachedMeta.vScale;
                var parsed = this.getParsed(index35);
                return {
                    label: vScale.getLabels()[index35],
                    value: "" + vScale.getLabelForValue(parsed[vScale.axis])
                };
            }
        },
        {
            key: "parseObjectData",
            value: function parseObjectData(meta, data, start, count) {
                return (0, _helpersSegmentJs.y).bind(this)(meta, data, start, count);
            }
        },
        {
            key: "update",
            value: function update(mode) {
                var meta = this._cachedMeta;
                var line = meta.dataset;
                var points = meta.data || [];
                var labels = meta.iScale.getLabels();
                line.points = points;
                if (mode !== "resize") {
                    var options = this.resolveDatasetElementOptions(mode);
                    if (!this.options.showLine) options.borderWidth = 0;
                    var properties = {
                        _loop: true,
                        _fullLoop: labels.length === points.length,
                        options: options
                    };
                    this.updateElement(line, undefined, properties, mode);
                }
                this.updateElements(points, 0, points.length, mode);
            }
        },
        {
            key: "updateElements",
            value: function updateElements(points, start, count, mode) {
                var scale = this._cachedMeta.rScale;
                var reset = mode === "reset";
                for(var i = start; i < start + count; i++){
                    var point = points[i];
                    var options = this.resolveDataElementOptions(i, point.active ? "active" : mode);
                    var pointPosition = scale.getPointPositionForValue(i, this.getParsed(i).r);
                    var x = reset ? scale.xCenter : pointPosition.x;
                    var y = reset ? scale.yCenter : pointPosition.y;
                    var properties = {
                        x: x,
                        y: y,
                        angle: pointPosition.angle,
                        skip: isNaN(x) || isNaN(y),
                        options: options
                    };
                    this.updateElement(point, i, properties, mode);
                }
            }
        }
    ]);
    return RadarController;
}(DatasetController);
RadarController.id = "radar";
RadarController.defaults = {
    datasetElementType: "line",
    dataElementType: "point",
    indexAxis: "r",
    showLine: true,
    elements: {
        line: {
            fill: "start"
        }
    }
};
RadarController.overrides = {
    aspectRatio: 1,
    scales: {
        r: {
            type: "radialLinear"
        }
    }
};
var ScatterController = /*#__PURE__*/ function(LineController) {
    "use strict";
    (0, _inheritsJsDefault.default)(ScatterController, LineController);
    var _super = (0, _createSuperJsDefault.default)(ScatterController);
    function ScatterController() {
        (0, _classCallCheckJsDefault.default)(this, ScatterController);
        return _super.apply(this, arguments);
    }
    return ScatterController;
}(LineController);
ScatterController.id = "scatter";
ScatterController.defaults = {
    showLine: false,
    fill: false
};
ScatterController.overrides = {
    interaction: {
        mode: "point"
    },
    plugins: {
        tooltip: {
            callbacks: {
                title: function() {
                    return "";
                },
                label: function(item) {
                    return "(" + item.label + ", " + item.formattedValue + ")";
                }
            }
        }
    },
    scales: {
        x: {
            type: "linear"
        },
        y: {
            type: "linear"
        }
    }
};
var controllers = /*#__PURE__*/ Object.freeze({
    __proto__: null,
    BarController: BarController,
    BubbleController: BubbleController,
    DoughnutController: DoughnutController,
    LineController: LineController,
    PolarAreaController: PolarAreaController,
    PieController: PieController,
    RadarController: RadarController,
    ScatterController: ScatterController
});
function abstract() {
    throw new Error("This method is not implemented: Check that a complete date adapter is provided.");
}
var DateAdapter = /*#__PURE__*/ function() {
    "use strict";
    function DateAdapter(options) {
        (0, _classCallCheckJsDefault.default)(this, DateAdapter);
        this.options = options || {};
    }
    (0, _createClassJsDefault.default)(DateAdapter, [
        {
            key: "formats",
            value: function formats() {
                return abstract();
            }
        },
        {
            key: "parse",
            value: function parse1(value, format) {
                return abstract();
            }
        },
        {
            key: "format",
            value: function format1(timestamp, format) {
                return abstract();
            }
        },
        {
            key: "add",
            value: function add(timestamp, amount, unit) {
                return abstract();
            }
        },
        {
            key: "diff",
            value: function diff(a, b, unit) {
                return abstract();
            }
        },
        {
            key: "startOf",
            value: function startOf(timestamp, unit, weekday) {
                return abstract();
            }
        },
        {
            key: "endOf",
            value: function endOf(timestamp, unit) {
                return abstract();
            }
        }
    ]);
    return DateAdapter;
}();
DateAdapter.override = function(members) {
    Object.assign(DateAdapter.prototype, members);
};
var adapters = {
    _date: DateAdapter
};
function binarySearch(metaset, axis, value, intersect) {
    var controller = metaset.controller, data = metaset.data, _sorted = metaset._sorted;
    var iScale = controller._cachedMeta.iScale;
    if (iScale && axis === iScale.axis && axis !== "r" && _sorted && data.length) {
        var lookupMethod = iScale._reversePixels ? (0, _helpersSegmentJs.A) : (0, _helpersSegmentJs.x);
        if (!intersect) return lookupMethod(data, axis, value);
        else if (controller._sharedOptions) {
            var el = data[0];
            var range = typeof el.getRange === "function" && el.getRange(axis);
            if (range) {
                var start = lookupMethod(data, axis, value - range);
                var end = lookupMethod(data, axis, value + range);
                return {
                    lo: start.lo,
                    hi: end.hi
                };
            }
        }
    }
    return {
        lo: 0,
        hi: data.length - 1
    };
}
function evaluateInteractionItems(chart, axis, position, handler, intersect) {
    var metasets = chart.getSortedVisibleDatasetMetas();
    var value = position[axis];
    for(var i = 0, ilen = metasets.length; i < ilen; ++i){
        var _i = metasets[i], index36 = _i.index, data = _i.data;
        var ref = binarySearch(metasets[i], axis, value, intersect), lo = ref.lo, hi = ref.hi;
        for(var j = lo; j <= hi; ++j){
            var element = data[j];
            if (!element.skip) handler(element, index36, j);
        }
    }
}
function getDistanceMetricForAxis(axis) {
    var useX = axis.indexOf("x") !== -1;
    var useY = axis.indexOf("y") !== -1;
    return function(pt1, pt2) {
        var deltaX = useX ? Math.abs(pt1.x - pt2.x) : 0;
        var deltaY = useY ? Math.abs(pt1.y - pt2.y) : 0;
        return Math.sqrt(Math.pow(deltaX, 2) + Math.pow(deltaY, 2));
    };
}
function getIntersectItems(chart, position, axis, useFinalPosition, includeInvisible) {
    var items = [];
    if (!includeInvisible && !chart.isPointInArea(position)) return items;
    var evaluationFunc = function evaluationFunc(element, datasetIndex, index37) {
        if (!includeInvisible && !(0, _helpersSegmentJs.B)(element, chart.chartArea, 0)) return;
        if (element.inRange(position.x, position.y, useFinalPosition)) items.push({
            element: element,
            datasetIndex: datasetIndex,
            index: index37
        });
    };
    evaluateInteractionItems(chart, axis, position, evaluationFunc, true);
    return items;
}
function getNearestRadialItems(chart, position, axis, useFinalPosition) {
    var items = [];
    function evaluationFunc(element, datasetIndex, index38) {
        var ref = element.getProps([
            "startAngle",
            "endAngle"
        ], useFinalPosition), startAngle = ref.startAngle, endAngle = ref.endAngle;
        var angle = (0, _helpersSegmentJs.C)(element, {
            x: position.x,
            y: position.y
        }).angle;
        if ((0, _helpersSegmentJs.p)(angle, startAngle, endAngle)) items.push({
            element: element,
            datasetIndex: datasetIndex,
            index: index38
        });
    }
    evaluateInteractionItems(chart, axis, position, evaluationFunc);
    return items;
}
function getNearestCartesianItems(chart, position, axis, intersect, useFinalPosition, includeInvisible) {
    var items = [];
    var distanceMetric = getDistanceMetricForAxis(axis);
    var minDistance = Number.POSITIVE_INFINITY;
    function evaluationFunc(element, datasetIndex, index39) {
        var inRange1 = element.inRange(position.x, position.y, useFinalPosition);
        if (intersect && !inRange1) return;
        var center = element.getCenterPoint(useFinalPosition);
        var pointInArea = !!includeInvisible || chart.isPointInArea(center);
        if (!pointInArea && !inRange1) return;
        var distance = distanceMetric(position, center);
        if (distance < minDistance) {
            items = [
                {
                    element: element,
                    datasetIndex: datasetIndex,
                    index: index39
                }
            ];
            minDistance = distance;
        } else if (distance === minDistance) items.push({
            element: element,
            datasetIndex: datasetIndex,
            index: index39
        });
    }
    evaluateInteractionItems(chart, axis, position, evaluationFunc);
    return items;
}
function getNearestItems(chart, position, axis, intersect, useFinalPosition, includeInvisible) {
    if (!includeInvisible && !chart.isPointInArea(position)) return [];
    return axis === "r" && !intersect ? getNearestRadialItems(chart, position, axis, useFinalPosition) : getNearestCartesianItems(chart, position, axis, intersect, useFinalPosition, includeInvisible);
}
function getAxisItems(chart, position, axis, intersect, useFinalPosition) {
    var items = [];
    var rangeMethod = axis === "x" ? "inXRange" : "inYRange";
    var intersectsItem = false;
    evaluateInteractionItems(chart, axis, position, function(element, datasetIndex, index40) {
        if (element[rangeMethod](position[axis], useFinalPosition)) {
            items.push({
                element: element,
                datasetIndex: datasetIndex,
                index: index40
            });
            intersectsItem = intersectsItem || element.inRange(position.x, position.y, useFinalPosition);
        }
    });
    if (intersect && !intersectsItem) return [];
    return items;
}
var Interaction = {
    evaluateInteractionItems: evaluateInteractionItems,
    modes: {
        index: function(chart, e, options, useFinalPosition) {
            var position = (0, _helpersSegmentJs.z)(e, chart);
            var axis = options.axis || "x";
            var includeInvisible = options.includeInvisible || false;
            var items = options.intersect ? getIntersectItems(chart, position, axis, useFinalPosition, includeInvisible) : getNearestItems(chart, position, axis, false, useFinalPosition, includeInvisible);
            var elements3 = [];
            if (!items.length) return [];
            chart.getSortedVisibleDatasetMetas().forEach(function(meta) {
                var index41 = items[0].index;
                var element = meta.data[index41];
                if (element && !element.skip) elements3.push({
                    element: element,
                    datasetIndex: meta.index,
                    index: index41
                });
            });
            return elements3;
        },
        dataset: function(chart, e, options, useFinalPosition) {
            var position = (0, _helpersSegmentJs.z)(e, chart);
            var axis = options.axis || "xy";
            var includeInvisible = options.includeInvisible || false;
            var items = options.intersect ? getIntersectItems(chart, position, axis, useFinalPosition, includeInvisible) : getNearestItems(chart, position, axis, false, useFinalPosition, includeInvisible);
            if (items.length > 0) {
                var datasetIndex = items[0].datasetIndex;
                var data = chart.getDatasetMeta(datasetIndex).data;
                items = [];
                for(var i = 0; i < data.length; ++i)items.push({
                    element: data[i],
                    datasetIndex: datasetIndex,
                    index: i
                });
            }
            return items;
        },
        point: function(chart, e, options, useFinalPosition) {
            var position = (0, _helpersSegmentJs.z)(e, chart);
            var axis = options.axis || "xy";
            var includeInvisible = options.includeInvisible || false;
            return getIntersectItems(chart, position, axis, useFinalPosition, includeInvisible);
        },
        nearest: function(chart, e, options, useFinalPosition) {
            var position = (0, _helpersSegmentJs.z)(e, chart);
            var axis = options.axis || "xy";
            var includeInvisible = options.includeInvisible || false;
            return getNearestItems(chart, position, axis, options.intersect, useFinalPosition, includeInvisible);
        },
        x: function(chart, e, options, useFinalPosition) {
            var position = (0, _helpersSegmentJs.z)(e, chart);
            return getAxisItems(chart, position, "x", options.intersect, useFinalPosition);
        },
        y: function(chart, e, options, useFinalPosition) {
            var position = (0, _helpersSegmentJs.z)(e, chart);
            return getAxisItems(chart, position, "y", options.intersect, useFinalPosition);
        }
    }
};
var STATIC_POSITIONS = [
    "left",
    "top",
    "right",
    "bottom"
];
function filterByPosition(array, position) {
    return array.filter(function(v) {
        return v.pos === position;
    });
}
function filterDynamicPositionByAxis(array, axis) {
    return array.filter(function(v) {
        return STATIC_POSITIONS.indexOf(v.pos) === -1 && v.box.axis === axis;
    });
}
function sortByWeight(array, reverse) {
    return array.sort(function(a, b) {
        var v0 = reverse ? b : a;
        var v1 = reverse ? a : b;
        return v0.weight === v1.weight ? v0.index - v1.index : v0.weight - v1.weight;
    });
}
function wrapBoxes(boxes) {
    var layoutBoxes = [];
    var i, ilen, box, pos, stack, stackWeight;
    for(i = 0, ilen = (boxes || []).length; i < ilen; ++i){
        box = boxes[i];
        var ref, ref2, ref3;
        ref = box, pos = ref.position, ref2 = ref.options, stack = ref2.stack, ref3 = ref2.stackWeight, stackWeight = ref3 === void 0 ? 1 : ref3, ref2, ref;
        layoutBoxes.push({
            index: i,
            box: box,
            pos: pos,
            horizontal: box.isHorizontal(),
            weight: box.weight,
            stack: stack && pos + stack,
            stackWeight: stackWeight
        });
    }
    return layoutBoxes;
}
function buildStacks(layouts1) {
    var stacks = {};
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = layouts1[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var wrap = _step.value;
            var stack = wrap.stack, pos = wrap.pos, stackWeight = wrap.stackWeight;
            if (!stack || !STATIC_POSITIONS.includes(pos)) continue;
            var _stack = stacks[stack] || (stacks[stack] = {
                count: 0,
                placed: 0,
                weight: 0,
                size: 0
            });
            _stack.count++;
            _stack.weight += stackWeight;
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
    return stacks;
}
function setLayoutDims(layouts2, params) {
    var stacks = buildStacks(layouts2);
    var vBoxMaxWidth = params.vBoxMaxWidth, hBoxMaxHeight = params.hBoxMaxHeight;
    var i, ilen, layout;
    for(i = 0, ilen = layouts2.length; i < ilen; ++i){
        layout = layouts2[i];
        var fullSize = layout.box.fullSize;
        var stack = stacks[layout.stack];
        var factor = stack && layout.stackWeight / stack.weight;
        if (layout.horizontal) {
            layout.width = factor ? factor * vBoxMaxWidth : fullSize && params.availableWidth;
            layout.height = hBoxMaxHeight;
        } else {
            layout.width = vBoxMaxWidth;
            layout.height = factor ? factor * hBoxMaxHeight : fullSize && params.availableHeight;
        }
    }
    return stacks;
}
function buildLayoutBoxes(boxes) {
    var layoutBoxes = wrapBoxes(boxes);
    var fullSize = sortByWeight(layoutBoxes.filter(function(wrap) {
        return wrap.box.fullSize;
    }), true);
    var left = sortByWeight(filterByPosition(layoutBoxes, "left"), true);
    var right = sortByWeight(filterByPosition(layoutBoxes, "right"));
    var top = sortByWeight(filterByPosition(layoutBoxes, "top"), true);
    var bottom = sortByWeight(filterByPosition(layoutBoxes, "bottom"));
    var centerHorizontal = filterDynamicPositionByAxis(layoutBoxes, "x");
    var centerVertical = filterDynamicPositionByAxis(layoutBoxes, "y");
    return {
        fullSize: fullSize,
        leftAndTop: left.concat(top),
        rightAndBottom: right.concat(centerVertical).concat(bottom).concat(centerHorizontal),
        chartArea: filterByPosition(layoutBoxes, "chartArea"),
        vertical: left.concat(right).concat(centerVertical),
        horizontal: top.concat(bottom).concat(centerHorizontal)
    };
}
function getCombinedMax(maxPadding, chartArea, a, b) {
    return Math.max(maxPadding[a], chartArea[a]) + Math.max(maxPadding[b], chartArea[b]);
}
function updateMaxPadding(maxPadding, boxPadding) {
    maxPadding.top = Math.max(maxPadding.top, boxPadding.top);
    maxPadding.left = Math.max(maxPadding.left, boxPadding.left);
    maxPadding.bottom = Math.max(maxPadding.bottom, boxPadding.bottom);
    maxPadding.right = Math.max(maxPadding.right, boxPadding.right);
}
function updateDims(chartArea, params, layout, stacks) {
    var pos = layout.pos, box = layout.box;
    var maxPadding = chartArea.maxPadding;
    if (!(0, _helpersSegmentJs.i)(pos)) {
        if (layout.size) chartArea[pos] -= layout.size;
        var stack = stacks[layout.stack] || {
            size: 0,
            count: 1
        };
        stack.size = Math.max(stack.size, layout.horizontal ? box.height : box.width);
        layout.size = stack.size / stack.count;
        chartArea[pos] += layout.size;
    }
    if (box.getPadding) updateMaxPadding(maxPadding, box.getPadding());
    var newWidth = Math.max(0, params.outerWidth - getCombinedMax(maxPadding, chartArea, "left", "right"));
    var newHeight = Math.max(0, params.outerHeight - getCombinedMax(maxPadding, chartArea, "top", "bottom"));
    var widthChanged = newWidth !== chartArea.w;
    var heightChanged = newHeight !== chartArea.h;
    chartArea.w = newWidth;
    chartArea.h = newHeight;
    return layout.horizontal ? {
        same: widthChanged,
        other: heightChanged
    } : {
        same: heightChanged,
        other: widthChanged
    };
}
function handleMaxPadding(chartArea) {
    var maxPadding = chartArea.maxPadding;
    function updatePos(pos) {
        var change = Math.max(maxPadding[pos] - chartArea[pos], 0);
        chartArea[pos] += change;
        return change;
    }
    chartArea.y += updatePos("top");
    chartArea.x += updatePos("left");
    updatePos("right");
    updatePos("bottom");
}
function getMargins(horizontal, chartArea) {
    var maxPadding = chartArea.maxPadding;
    function marginForPositions(positions) {
        var margin = {
            left: 0,
            top: 0,
            right: 0,
            bottom: 0
        };
        positions.forEach(function(pos) {
            margin[pos] = Math.max(chartArea[pos], maxPadding[pos]);
        });
        return margin;
    }
    return horizontal ? marginForPositions([
        "left",
        "right"
    ]) : marginForPositions([
        "top",
        "bottom"
    ]);
}
function fitBoxes(boxes, chartArea, params, stacks) {
    var refitBoxes = [];
    var i, ilen, layout, box, refit, changed;
    for(i = 0, ilen = boxes.length, refit = 0; i < ilen; ++i){
        layout = boxes[i];
        box = layout.box;
        box.update(layout.width || chartArea.w, layout.height || chartArea.h, getMargins(layout.horizontal, chartArea));
        var ref = updateDims(chartArea, params, layout, stacks), same = ref.same, other = ref.other;
        refit |= same && refitBoxes.length;
        changed = changed || other;
        if (!box.fullSize) refitBoxes.push(layout);
    }
    return refit && fitBoxes(refitBoxes, chartArea, params, stacks) || changed;
}
function setBoxDims(box, left, top, width, height) {
    box.top = top;
    box.left = left;
    box.right = left + width;
    box.bottom = top + height;
    box.width = width;
    box.height = height;
}
function placeBoxes(boxes, chartArea, params, stacks) {
    var userPadding = params.padding;
    var x = chartArea.x, y = chartArea.y;
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = boxes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var layout = _step.value;
            var box = layout.box;
            var stack = stacks[layout.stack] || {
                count: 1,
                placed: 0,
                weight: 1
            };
            var weight = layout.stackWeight / stack.weight || 1;
            if (layout.horizontal) {
                var width = chartArea.w * weight;
                var height = stack.size || box.height;
                if ((0, _helpersSegmentJs.j)(stack.start)) y = stack.start;
                if (box.fullSize) setBoxDims(box, userPadding.left, y, params.outerWidth - userPadding.right - userPadding.left, height);
                else setBoxDims(box, chartArea.left + stack.placed, y, width, height);
                stack.start = y;
                stack.placed += width;
                y = box.bottom;
            } else {
                var height1 = chartArea.h * weight;
                var width1 = stack.size || box.width;
                if ((0, _helpersSegmentJs.j)(stack.start)) x = stack.start;
                if (box.fullSize) setBoxDims(box, x, userPadding.top, width1, params.outerHeight - userPadding.bottom - userPadding.top);
                else setBoxDims(box, x, chartArea.top + stack.placed, width1, height1);
                stack.start = x;
                stack.placed += height1;
                x = box.right;
            }
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
    chartArea.x = x;
    chartArea.y = y;
}
(0, _helpersSegmentJs.d).set("layout", {
    autoPadding: true,
    padding: {
        top: 0,
        right: 0,
        bottom: 0,
        left: 0
    }
});
var layouts = {
    addBox: function(chart, item) {
        if (!chart.boxes) chart.boxes = [];
        item.fullSize = item.fullSize || false;
        item.position = item.position || "top";
        item.weight = item.weight || 0;
        item._layers = item._layers || function() {
            return [
                {
                    z: 0,
                    draw: function(chartArea) {
                        item.draw(chartArea);
                    }
                }
            ];
        };
        chart.boxes.push(item);
    },
    removeBox: function(chart, layoutItem) {
        var index42 = chart.boxes ? chart.boxes.indexOf(layoutItem) : -1;
        if (index42 !== -1) chart.boxes.splice(index42, 1);
    },
    configure: function(chart, item, options) {
        item.fullSize = options.fullSize;
        item.position = options.position;
        item.weight = options.weight;
    },
    update: function(chart, width, height, minPadding) {
        if (!chart) return;
        var padding = (0, _helpersSegmentJs.D)(chart.options.layout.padding);
        var availableWidth = Math.max(width - padding.width, 0);
        var availableHeight = Math.max(height - padding.height, 0);
        var boxes = buildLayoutBoxes(chart.boxes);
        var verticalBoxes = boxes.vertical;
        var horizontalBoxes = boxes.horizontal;
        (0, _helpersSegmentJs.E)(chart.boxes, function(box) {
            if (typeof box.beforeLayout === "function") box.beforeLayout();
        });
        var visibleVerticalBoxCount = verticalBoxes.reduce(function(total, wrap) {
            return wrap.box.options && wrap.box.options.display === false ? total : total + 1;
        }, 0) || 1;
        var params = Object.freeze({
            outerWidth: width,
            outerHeight: height,
            padding: padding,
            availableWidth: availableWidth,
            availableHeight: availableHeight,
            vBoxMaxWidth: availableWidth / 2 / visibleVerticalBoxCount,
            hBoxMaxHeight: availableHeight / 2
        });
        var maxPadding = Object.assign({}, padding);
        updateMaxPadding(maxPadding, (0, _helpersSegmentJs.D)(minPadding));
        var chartArea = Object.assign({
            maxPadding: maxPadding,
            w: availableWidth,
            h: availableHeight,
            x: padding.left,
            y: padding.top
        }, padding);
        var stacks = setLayoutDims(verticalBoxes.concat(horizontalBoxes), params);
        fitBoxes(boxes.fullSize, chartArea, params, stacks);
        fitBoxes(verticalBoxes, chartArea, params, stacks);
        if (fitBoxes(horizontalBoxes, chartArea, params, stacks)) fitBoxes(verticalBoxes, chartArea, params, stacks);
        handleMaxPadding(chartArea);
        placeBoxes(boxes.leftAndTop, chartArea, params, stacks);
        chartArea.x += chartArea.w;
        chartArea.y += chartArea.h;
        placeBoxes(boxes.rightAndBottom, chartArea, params, stacks);
        chart.chartArea = {
            left: chartArea.left,
            top: chartArea.top,
            right: chartArea.left + chartArea.w,
            bottom: chartArea.top + chartArea.h,
            height: chartArea.h,
            width: chartArea.w
        };
        (0, _helpersSegmentJs.E)(boxes.chartArea, function(layout) {
            var box = layout.box;
            Object.assign(box, chart.chartArea);
            box.update(chartArea.w, chartArea.h, {
                left: 0,
                top: 0,
                right: 0,
                bottom: 0
            });
        });
    }
};
var BasePlatform = /*#__PURE__*/ function() {
    "use strict";
    function BasePlatform() {
        (0, _classCallCheckJsDefault.default)(this, BasePlatform);
    }
    (0, _createClassJsDefault.default)(BasePlatform, [
        {
            key: "acquireContext",
            value: function acquireContext(canvas, aspectRatio) {}
        },
        {
            key: "releaseContext",
            value: function releaseContext(context) {
                return false;
            }
        },
        {
            key: "addEventListener",
            value: function addEventListener(chart, type, listener) {}
        },
        {
            key: "removeEventListener",
            value: function removeEventListener(chart, type, listener) {}
        },
        {
            key: "getDevicePixelRatio",
            value: function getDevicePixelRatio() {
                return 1;
            }
        },
        {
            key: "getMaximumSize",
            value: function getMaximumSize(element, width, height, aspectRatio) {
                width = Math.max(0, width || element.width);
                height = height || element.height;
                return {
                    width: width,
                    height: Math.max(0, aspectRatio ? Math.floor(width / aspectRatio) : height)
                };
            }
        },
        {
            key: "isAttached",
            value: function isAttached(canvas) {
                return true;
            }
        },
        {
            key: "updateConfig",
            value: function updateConfig(config) {}
        }
    ]);
    return BasePlatform;
}();
var BasicPlatform = /*#__PURE__*/ function(BasePlatform) {
    "use strict";
    (0, _inheritsJsDefault.default)(BasicPlatform, BasePlatform);
    var _super = (0, _createSuperJsDefault.default)(BasicPlatform);
    function BasicPlatform() {
        (0, _classCallCheckJsDefault.default)(this, BasicPlatform);
        return _super.apply(this, arguments);
    }
    (0, _createClassJsDefault.default)(BasicPlatform, [
        {
            key: "acquireContext",
            value: function acquireContext(item) {
                return item && item.getContext && item.getContext("2d") || null;
            }
        },
        {
            key: "updateConfig",
            value: function updateConfig(config) {
                config.options.animation = false;
            }
        }
    ]);
    return BasicPlatform;
}(BasePlatform);
var EXPANDO_KEY = "$chartjs";
var EVENT_TYPES = {
    touchstart: "mousedown",
    touchmove: "mousemove",
    touchend: "mouseup",
    pointerenter: "mouseenter",
    pointerdown: "mousedown",
    pointermove: "mousemove",
    pointerup: "mouseup",
    pointerleave: "mouseout",
    pointerout: "mouseout"
};
var isNullOrEmpty = function(value) {
    return value === null || value === "";
};
function initCanvas(canvas, aspectRatio) {
    var style = canvas.style;
    var renderHeight = canvas.getAttribute("height");
    var renderWidth = canvas.getAttribute("width");
    canvas[EXPANDO_KEY] = {
        initial: {
            height: renderHeight,
            width: renderWidth,
            style: {
                display: style.display,
                height: style.height,
                width: style.width
            }
        }
    };
    style.display = style.display || "block";
    style.boxSizing = style.boxSizing || "border-box";
    if (isNullOrEmpty(renderWidth)) {
        var displayWidth = (0, _helpersSegmentJs.I)(canvas, "width");
        if (displayWidth !== undefined) canvas.width = displayWidth;
    }
    if (isNullOrEmpty(renderHeight)) {
        if (canvas.style.height === "") canvas.height = canvas.width / (aspectRatio || 2);
        else {
            var displayHeight = (0, _helpersSegmentJs.I)(canvas, "height");
            if (displayHeight !== undefined) canvas.height = displayHeight;
        }
    }
    return canvas;
}
var eventListenerOptions = (0, _helpersSegmentJs.K) ? {
    passive: true
} : false;
function addListener(node, type, listener) {
    node.addEventListener(type, listener, eventListenerOptions);
}
function removeListener(chart, type, listener) {
    chart.canvas.removeEventListener(type, listener, eventListenerOptions);
}
function fromNativeEvent(event, chart) {
    var type = EVENT_TYPES[event.type] || event.type;
    var ref = (0, _helpersSegmentJs.z)(event, chart), x = ref.x, y = ref.y;
    return {
        type: type,
        chart: chart,
        native: event,
        x: x !== undefined ? x : null,
        y: y !== undefined ? y : null
    };
}
function nodeListContains(nodeList, canvas) {
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = nodeList[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var node = _step.value;
            if (node === canvas || node.contains(canvas)) return true;
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
}
function createAttachObserver(chart, type, listener) {
    var canvas = chart.canvas;
    var observer = new MutationObserver(function(entries) {
        var trigger = false;
        var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
        try {
            for(var _iterator = entries[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                var entry = _step.value;
                trigger = trigger || nodeListContains(entry.addedNodes, canvas);
                trigger = trigger && !nodeListContains(entry.removedNodes, canvas);
            }
        } catch (err) {
            _didIteratorError = true;
            _iteratorError = err;
        } finally{
            try {
                if (!_iteratorNormalCompletion && _iterator.return != null) {
                    _iterator.return();
                }
            } finally{
                if (_didIteratorError) {
                    throw _iteratorError;
                }
            }
        }
        if (trigger) listener();
    });
    observer.observe(document, {
        childList: true,
        subtree: true
    });
    return observer;
}
function createDetachObserver(chart, type, listener) {
    var canvas = chart.canvas;
    var observer = new MutationObserver(function(entries) {
        var trigger = false;
        var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
        try {
            for(var _iterator = entries[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                var entry = _step.value;
                trigger = trigger || nodeListContains(entry.removedNodes, canvas);
                trigger = trigger && !nodeListContains(entry.addedNodes, canvas);
            }
        } catch (err) {
            _didIteratorError = true;
            _iteratorError = err;
        } finally{
            try {
                if (!_iteratorNormalCompletion && _iterator.return != null) {
                    _iterator.return();
                }
            } finally{
                if (_didIteratorError) {
                    throw _iteratorError;
                }
            }
        }
        if (trigger) listener();
    });
    observer.observe(document, {
        childList: true,
        subtree: true
    });
    return observer;
}
var drpListeningCharts = new Map();
var oldDevicePixelRatio = 0;
function onWindowResize() {
    var dpr = window.devicePixelRatio;
    if (dpr === oldDevicePixelRatio) return;
    oldDevicePixelRatio = dpr;
    drpListeningCharts.forEach(function(resize, chart) {
        if (chart.currentDevicePixelRatio !== dpr) resize();
    });
}
function listenDevicePixelRatioChanges(chart, resize) {
    if (!drpListeningCharts.size) window.addEventListener("resize", onWindowResize);
    drpListeningCharts.set(chart, resize);
}
function unlistenDevicePixelRatioChanges(chart) {
    drpListeningCharts.delete(chart);
    if (!drpListeningCharts.size) window.removeEventListener("resize", onWindowResize);
}
function createResizeObserver(chart, type, listener) {
    var canvas = chart.canvas;
    var container = canvas && (0, _helpersSegmentJs.G)(canvas);
    if (!container) return;
    var resize = (0, _helpersSegmentJs.J)(function(width, height) {
        var w = container.clientWidth;
        listener(width, height);
        if (w < container.clientWidth) listener();
    }, window);
    var observer = new ResizeObserver(function(entries) {
        var entry = entries[0];
        var width = entry.contentRect.width;
        var height = entry.contentRect.height;
        if (width === 0 && height === 0) return;
        resize(width, height);
    });
    observer.observe(container);
    listenDevicePixelRatioChanges(chart, resize);
    return observer;
}
function releaseObserver(chart, type, observer) {
    if (observer) observer.disconnect();
    if (type === "resize") unlistenDevicePixelRatioChanges(chart);
}
function createProxyAndListen(chart, type, listener) {
    var canvas = chart.canvas;
    var proxy = (0, _helpersSegmentJs.J)(function(event) {
        if (chart.ctx !== null) listener(fromNativeEvent(event, chart));
    }, chart, function(args) {
        var event = args[0];
        return [
            event,
            event.offsetX,
            event.offsetY
        ];
    });
    addListener(canvas, type, proxy);
    return proxy;
}
var DomPlatform = /*#__PURE__*/ function(BasePlatform) {
    "use strict";
    (0, _inheritsJsDefault.default)(DomPlatform, BasePlatform);
    var _super = (0, _createSuperJsDefault.default)(DomPlatform);
    function DomPlatform() {
        (0, _classCallCheckJsDefault.default)(this, DomPlatform);
        return _super.apply(this, arguments);
    }
    (0, _createClassJsDefault.default)(DomPlatform, [
        {
            key: "acquireContext",
            value: function acquireContext(canvas, aspectRatio) {
                var context = canvas && canvas.getContext && canvas.getContext("2d");
                if (context && context.canvas === canvas) {
                    initCanvas(canvas, aspectRatio);
                    return context;
                }
                return null;
            }
        },
        {
            key: "releaseContext",
            value: function releaseContext(context) {
                var canvas = context.canvas;
                if (!canvas[EXPANDO_KEY]) return false;
                var initial = canvas[EXPANDO_KEY].initial;
                [
                    "height",
                    "width"
                ].forEach(function(prop) {
                    var value = initial[prop];
                    if ((0, _helpersSegmentJs.k)(value)) canvas.removeAttribute(prop);
                    else canvas.setAttribute(prop, value);
                });
                var style = initial.style || {};
                Object.keys(style).forEach(function(key) {
                    canvas.style[key] = style[key];
                });
                canvas.width = canvas.width;
                delete canvas[EXPANDO_KEY];
                return true;
            }
        },
        {
            key: "addEventListener",
            value: function addEventListener(chart, type, listener) {
                this.removeEventListener(chart, type);
                var proxies = chart.$proxies || (chart.$proxies = {});
                var handlers = {
                    attach: createAttachObserver,
                    detach: createDetachObserver,
                    resize: createResizeObserver
                };
                var handler = handlers[type] || createProxyAndListen;
                proxies[type] = handler(chart, type, listener);
            }
        },
        {
            key: "removeEventListener",
            value: function removeEventListener(chart, type) {
                var proxies = chart.$proxies || (chart.$proxies = {});
                var proxy = proxies[type];
                if (!proxy) return;
                var handlers = {
                    attach: releaseObserver,
                    detach: releaseObserver,
                    resize: releaseObserver
                };
                var handler = handlers[type] || removeListener;
                handler(chart, type, proxy);
                proxies[type] = undefined;
            }
        },
        {
            key: "getDevicePixelRatio",
            value: function getDevicePixelRatio() {
                return window.devicePixelRatio;
            }
        },
        {
            key: "getMaximumSize",
            value: function getMaximumSize(canvas, width, height, aspectRatio) {
                return (0, _helpersSegmentJs.F)(canvas, width, height, aspectRatio);
            }
        },
        {
            key: "isAttached",
            value: function isAttached(canvas) {
                var container = (0, _helpersSegmentJs.G)(canvas);
                return !!(container && container.isConnected);
            }
        }
    ]);
    return DomPlatform;
}(BasePlatform);
function _detectPlatform(canvas) {
    if (!(0, _helpersSegmentJs.L)() || typeof OffscreenCanvas !== "undefined" && canvas instanceof OffscreenCanvas) return BasicPlatform;
    return DomPlatform;
}
var Element = /*#__PURE__*/ function() {
    "use strict";
    function Element() {
        (0, _classCallCheckJsDefault.default)(this, Element);
        this.x = undefined;
        this.y = undefined;
        this.active = false;
        this.options = undefined;
        this.$animations = undefined;
    }
    (0, _createClassJsDefault.default)(Element, [
        {
            key: "tooltipPosition",
            value: function tooltipPosition(useFinalPosition) {
                var ref = this.getProps([
                    "x",
                    "y"
                ], useFinalPosition), x = ref.x, y = ref.y;
                return {
                    x: x,
                    y: y
                };
            }
        },
        {
            key: "hasValue",
            value: function hasValue() {
                return (0, _helpersSegmentJs.q)(this.x) && (0, _helpersSegmentJs.q)(this.y);
            }
        },
        {
            key: "getProps",
            value: function getProps(props, final) {
                var _this = this;
                var anims = this.$animations;
                if (!final || !anims) return this;
                var ret = {};
                props.forEach(function(prop) {
                    ret[prop] = anims[prop] && anims[prop].active() ? anims[prop]._to : _this[prop];
                });
                return ret;
            }
        }
    ]);
    return Element;
}();
Element.defaults = {};
Element.defaultRoutes = undefined;
var formatters = {
    values: function(value) {
        return (0, _helpersSegmentJs.b)(value) ? value : "" + value;
    },
    numeric: function(tickValue, index, ticks) {
        if (tickValue === 0) return "0";
        var locale = this.chart.options.locale;
        var notation;
        var delta = tickValue;
        if (ticks.length > 1) {
            var maxTick = Math.max(Math.abs(ticks[0].value), Math.abs(ticks[ticks.length - 1].value));
            if (maxTick < 1e-4 || maxTick > 1e+15) notation = "scientific";
            delta = calculateDelta(tickValue, ticks);
        }
        var logDelta = (0, _helpersSegmentJs.M)(Math.abs(delta));
        var numDecimal = Math.max(Math.min(-1 * Math.floor(logDelta), 20), 0);
        var options = {
            notation: notation,
            minimumFractionDigits: numDecimal,
            maximumFractionDigits: numDecimal
        };
        Object.assign(options, this.options.ticks.format);
        return (0, _helpersSegmentJs.o)(tickValue, locale, options);
    },
    logarithmic: function(tickValue, index43, ticks) {
        if (tickValue === 0) return "0";
        var remain = tickValue / Math.pow(10, Math.floor((0, _helpersSegmentJs.M)(tickValue)));
        if (remain === 1 || remain === 2 || remain === 5) return formatters.numeric.call(this, tickValue, index43, ticks);
        return "";
    }
};
function calculateDelta(tickValue, ticks) {
    var delta = ticks.length > 3 ? ticks[2].value - ticks[1].value : ticks[1].value - ticks[0].value;
    if (Math.abs(delta) >= 1 && tickValue !== Math.floor(tickValue)) delta = tickValue - Math.floor(tickValue);
    return delta;
}
var Ticks = {
    formatters: formatters
};
(0, _helpersSegmentJs.d).set("scale", {
    display: true,
    offset: false,
    reverse: false,
    beginAtZero: false,
    bounds: "ticks",
    grace: 0,
    grid: {
        display: true,
        lineWidth: 1,
        drawBorder: true,
        drawOnChartArea: true,
        drawTicks: true,
        tickLength: 8,
        tickWidth: function(_ctx, options) {
            return options.lineWidth;
        },
        tickColor: function(_ctx, options) {
            return options.color;
        },
        offset: false,
        borderDash: [],
        borderDashOffset: 0.0,
        borderWidth: 1
    },
    title: {
        display: false,
        text: "",
        padding: {
            top: 4,
            bottom: 4
        }
    },
    ticks: {
        minRotation: 0,
        maxRotation: 50,
        mirror: false,
        textStrokeWidth: 0,
        textStrokeColor: "",
        padding: 3,
        display: true,
        autoSkip: true,
        autoSkipPadding: 3,
        labelOffset: 0,
        callback: Ticks.formatters.values,
        minor: {},
        major: {},
        align: "center",
        crossAlign: "near",
        showLabelBackdrop: false,
        backdropColor: "rgba(255, 255, 255, 0.75)",
        backdropPadding: 2
    }
});
(0, _helpersSegmentJs.d).route("scale.ticks", "color", "", "color");
(0, _helpersSegmentJs.d).route("scale.grid", "color", "", "borderColor");
(0, _helpersSegmentJs.d).route("scale.grid", "borderColor", "", "borderColor");
(0, _helpersSegmentJs.d).route("scale.title", "color", "", "color");
(0, _helpersSegmentJs.d).describe("scale", {
    _fallback: false,
    _scriptable: function(name) {
        return !name.startsWith("before") && !name.startsWith("after") && name !== "callback" && name !== "parser";
    },
    _indexable: function(name) {
        return name !== "borderDash" && name !== "tickBorderDash";
    }
});
(0, _helpersSegmentJs.d).describe("scales", {
    _fallback: "scale"
});
(0, _helpersSegmentJs.d).describe("scale.ticks", {
    _scriptable: function(name) {
        return name !== "backdropPadding" && name !== "callback";
    },
    _indexable: function(name) {
        return name !== "backdropPadding";
    }
});
function autoSkip(scale, ticks) {
    var tickOpts = scale.options.ticks;
    var ticksLimit = tickOpts.maxTicksLimit || determineMaxTicks(scale);
    var majorIndices = tickOpts.major.enabled ? getMajorIndices(ticks) : [];
    var numMajorIndices = majorIndices.length;
    var first = majorIndices[0];
    var last = majorIndices[numMajorIndices - 1];
    var newTicks = [];
    if (numMajorIndices > ticksLimit) {
        skipMajors(ticks, newTicks, majorIndices, numMajorIndices / ticksLimit);
        return newTicks;
    }
    var spacing = calculateSpacing(majorIndices, ticks, ticksLimit);
    if (numMajorIndices > 0) {
        var i, ilen;
        var avgMajorSpacing = numMajorIndices > 1 ? Math.round((last - first) / (numMajorIndices - 1)) : null;
        skip(ticks, newTicks, spacing, (0, _helpersSegmentJs.k)(avgMajorSpacing) ? 0 : first - avgMajorSpacing, first);
        for(i = 0, ilen = numMajorIndices - 1; i < ilen; i++)skip(ticks, newTicks, spacing, majorIndices[i], majorIndices[i + 1]);
        skip(ticks, newTicks, spacing, last, (0, _helpersSegmentJs.k)(avgMajorSpacing) ? ticks.length : last + avgMajorSpacing);
        return newTicks;
    }
    skip(ticks, newTicks, spacing);
    return newTicks;
}
function determineMaxTicks(scale) {
    var offset = scale.options.offset;
    var tickLength = scale._tickSize();
    var maxScale = scale._length / tickLength + (offset ? 0 : 1);
    var maxChart = scale._maxLength / tickLength;
    return Math.floor(Math.min(maxScale, maxChart));
}
function calculateSpacing(majorIndices, ticks, ticksLimit) {
    var evenMajorSpacing = getEvenSpacing(majorIndices);
    var spacing = ticks.length / ticksLimit;
    if (!evenMajorSpacing) return Math.max(spacing, 1);
    var factors = (0, _helpersSegmentJs.N)(evenMajorSpacing);
    for(var i = 0, ilen = factors.length - 1; i < ilen; i++){
        var factor = factors[i];
        if (factor > spacing) return factor;
    }
    return Math.max(spacing, 1);
}
function getMajorIndices(ticks) {
    var result = [];
    var i, ilen;
    for(i = 0, ilen = ticks.length; i < ilen; i++)if (ticks[i].major) result.push(i);
    return result;
}
function skipMajors(ticks, newTicks, majorIndices, spacing) {
    var count = 0;
    var next = majorIndices[0];
    var i;
    spacing = Math.ceil(spacing);
    for(i = 0; i < ticks.length; i++)if (i === next) {
        newTicks.push(ticks[i]);
        count++;
        next = majorIndices[count * spacing];
    }
}
function skip(ticks, newTicks, spacing, majorStart, majorEnd) {
    var start = (0, _helpersSegmentJs.v)(majorStart, 0);
    var end = Math.min((0, _helpersSegmentJs.v)(majorEnd, ticks.length), ticks.length);
    var count = 0;
    var length, i, next;
    spacing = Math.ceil(spacing);
    if (majorEnd) {
        length = majorEnd - majorStart;
        spacing = length / Math.floor(length / spacing);
    }
    next = start;
    while(next < 0){
        count++;
        next = Math.round(start + count * spacing);
    }
    for(i = Math.max(start, 0); i < end; i++)if (i === next) {
        newTicks.push(ticks[i]);
        count++;
        next = Math.round(start + count * spacing);
    }
}
function getEvenSpacing(arr) {
    var len = arr.length;
    var i, diff;
    if (len < 2) return false;
    for(diff = arr[0], i = 1; i < len; ++i){
        if (arr[i] - arr[i - 1] !== diff) return false;
    }
    return diff;
}
var reverseAlign = function(align) {
    return align === "left" ? "right" : align === "right" ? "left" : align;
};
var offsetFromEdge = function(scale, edge, offset) {
    return edge === "top" || edge === "left" ? scale[edge] + offset : scale[edge] - offset;
};
function sample(arr, numItems) {
    var result = [];
    var increment = arr.length / numItems;
    var len = arr.length;
    var i = 0;
    for(; i < len; i += increment)result.push(arr[Math.floor(i)]);
    return result;
}
function getPixelForGridLine(scale, index44, offsetGridLines) {
    var length = scale.ticks.length;
    var validIndex1 = Math.min(index44, length - 1);
    var start = scale._startPixel;
    var end = scale._endPixel;
    var epsilon = 1e-6;
    var lineValue = scale.getPixelForTick(validIndex1);
    var offset;
    if (offsetGridLines) {
        if (length === 1) offset = Math.max(lineValue - start, end - lineValue);
        else if (index44 === 0) offset = (scale.getPixelForTick(1) - lineValue) / 2;
        else offset = (lineValue - scale.getPixelForTick(validIndex1 - 1)) / 2;
        lineValue += validIndex1 < index44 ? offset : -offset;
        if (lineValue < start - epsilon || lineValue > end + epsilon) return;
    }
    return lineValue;
}
function garbageCollect(caches, length) {
    (0, _helpersSegmentJs.E)(caches, function(cache) {
        var gc = cache.gc;
        var gcLen = gc.length / 2;
        var i;
        if (gcLen > length) {
            for(i = 0; i < gcLen; ++i)delete cache.data[gc[i]];
            gc.splice(0, gcLen);
        }
    });
}
function getTickMarkLength(options) {
    return options.drawTicks ? options.tickLength : 0;
}
function getTitleHeight(options, fallback) {
    if (!options.display) return 0;
    var font = (0, _helpersSegmentJs.$)(options.font, fallback);
    var padding = (0, _helpersSegmentJs.D)(options.padding);
    var lines = (0, _helpersSegmentJs.b)(options.text) ? options.text.length : 1;
    return lines * font.lineHeight + padding.height;
}
function createScaleContext(parent, scale) {
    return (0, _helpersSegmentJs.h)(parent, {
        scale: scale,
        type: "scale"
    });
}
function createTickContext(parent, index45, tick) {
    return (0, _helpersSegmentJs.h)(parent, {
        tick: tick,
        index: index45,
        type: "tick"
    });
}
function titleAlign(align, position, reverse) {
    var ret = (0, _helpersSegmentJs.a0)(align);
    if (reverse && position !== "right" || !reverse && position === "right") ret = reverseAlign(ret);
    return ret;
}
function titleArgs(scale, offset, position, align) {
    var top = scale.top, left = scale.left, bottom = scale.bottom, right = scale.right, chart = scale.chart;
    var chartArea = chart.chartArea, scales2 = chart.scales;
    var rotation = 0;
    var maxWidth, titleX, titleY;
    var height = bottom - top;
    var width = right - left;
    if (scale.isHorizontal()) {
        titleX = (0, _helpersSegmentJs.a1)(align, left, right);
        if ((0, _helpersSegmentJs.i)(position)) {
            var positionAxisID = Object.keys(position)[0];
            var value = position[positionAxisID];
            titleY = scales2[positionAxisID].getPixelForValue(value) + height - offset;
        } else if (position === "center") titleY = (chartArea.bottom + chartArea.top) / 2 + height - offset;
        else titleY = offsetFromEdge(scale, position, offset);
        maxWidth = right - left;
    } else {
        if ((0, _helpersSegmentJs.i)(position)) {
            var positionAxisID1 = Object.keys(position)[0];
            var value1 = position[positionAxisID1];
            titleX = scales2[positionAxisID1].getPixelForValue(value1) - width + offset;
        } else if (position === "center") titleX = (chartArea.left + chartArea.right) / 2 - width + offset;
        else titleX = offsetFromEdge(scale, position, offset);
        titleY = (0, _helpersSegmentJs.a1)(align, bottom, top);
        rotation = position === "left" ? -(0, _helpersSegmentJs.H) : (0, _helpersSegmentJs.H);
    }
    return {
        titleX: titleX,
        titleY: titleY,
        maxWidth: maxWidth,
        rotation: rotation
    };
}
var Scale = /*#__PURE__*/ function(Element) {
    "use strict";
    (0, _inheritsJsDefault.default)(Scale, Element);
    var _super = (0, _createSuperJsDefault.default)(Scale);
    function Scale(cfg) {
        (0, _classCallCheckJsDefault.default)(this, Scale);
        var _this;
        _this = _super.call(this);
        _this.id = cfg.id;
        _this.type = cfg.type;
        _this.options = undefined;
        _this.ctx = cfg.ctx;
        _this.chart = cfg.chart;
        _this.top = undefined;
        _this.bottom = undefined;
        _this.left = undefined;
        _this.right = undefined;
        _this.width = undefined;
        _this.height = undefined;
        _this._margins = {
            left: 0,
            right: 0,
            top: 0,
            bottom: 0
        };
        _this.maxWidth = undefined;
        _this.maxHeight = undefined;
        _this.paddingTop = undefined;
        _this.paddingBottom = undefined;
        _this.paddingLeft = undefined;
        _this.paddingRight = undefined;
        _this.axis = undefined;
        _this.labelRotation = undefined;
        _this.min = undefined;
        _this.max = undefined;
        _this._range = undefined;
        _this.ticks = [];
        _this._gridLineItems = null;
        _this._labelItems = null;
        _this._labelSizes = null;
        _this._length = 0;
        _this._maxLength = 0;
        _this._longestTextCache = {};
        _this._startPixel = undefined;
        _this._endPixel = undefined;
        _this._reversePixels = false;
        _this._userMax = undefined;
        _this._userMin = undefined;
        _this._suggestedMax = undefined;
        _this._suggestedMin = undefined;
        _this._ticksLength = 0;
        _this._borderValue = 0;
        _this._cache = {};
        _this._dataLimitsCached = false;
        _this.$context = undefined;
        return _this;
    }
    (0, _createClassJsDefault.default)(Scale, [
        {
            key: "init",
            value: function init(options) {
                this.options = options.setContext(this.getContext());
                this.axis = options.axis;
                this._userMin = this.parse(options.min);
                this._userMax = this.parse(options.max);
                this._suggestedMin = this.parse(options.suggestedMin);
                this._suggestedMax = this.parse(options.suggestedMax);
            }
        },
        {
            key: "parse",
            value: function parse1(raw, index) {
                return raw;
            }
        },
        {
            key: "getUserBounds",
            value: function getUserBounds() {
                var ref = this, _userMin = ref._userMin, _userMax = ref._userMax, _suggestedMin = ref._suggestedMin, _suggestedMax = ref._suggestedMax;
                _userMin = (0, _helpersSegmentJs.O)(_userMin, Number.POSITIVE_INFINITY);
                _userMax = (0, _helpersSegmentJs.O)(_userMax, Number.NEGATIVE_INFINITY);
                _suggestedMin = (0, _helpersSegmentJs.O)(_suggestedMin, Number.POSITIVE_INFINITY);
                _suggestedMax = (0, _helpersSegmentJs.O)(_suggestedMax, Number.NEGATIVE_INFINITY);
                return {
                    min: (0, _helpersSegmentJs.O)(_userMin, _suggestedMin),
                    max: (0, _helpersSegmentJs.O)(_userMax, _suggestedMax),
                    minDefined: (0, _helpersSegmentJs.g)(_userMin),
                    maxDefined: (0, _helpersSegmentJs.g)(_userMax)
                };
            }
        },
        {
            key: "getMinMax",
            value: function getMinMax(canStack) {
                var ref = this.getUserBounds(), min = ref.min, max = ref.max, minDefined = ref.minDefined, maxDefined = ref.maxDefined;
                var range;
                if (minDefined && maxDefined) return {
                    min: min,
                    max: max
                };
                var metas = this.getMatchingVisibleMetas();
                for(var i = 0, ilen = metas.length; i < ilen; ++i){
                    range = metas[i].controller.getMinMax(this, canStack);
                    if (!minDefined) min = Math.min(min, range.min);
                    if (!maxDefined) max = Math.max(max, range.max);
                }
                min = maxDefined && min > max ? max : min;
                max = minDefined && min > max ? min : max;
                return {
                    min: (0, _helpersSegmentJs.O)(min, (0, _helpersSegmentJs.O)(max, min)),
                    max: (0, _helpersSegmentJs.O)(max, (0, _helpersSegmentJs.O)(min, max))
                };
            }
        },
        {
            key: "getPadding",
            value: function getPadding() {
                return {
                    left: this.paddingLeft || 0,
                    top: this.paddingTop || 0,
                    right: this.paddingRight || 0,
                    bottom: this.paddingBottom || 0
                };
            }
        },
        {
            key: "getTicks",
            value: function getTicks() {
                return this.ticks;
            }
        },
        {
            key: "getLabels",
            value: function getLabels() {
                var data = this.chart.data;
                return this.options.labels || (this.isHorizontal() ? data.xLabels : data.yLabels) || data.labels || [];
            }
        },
        {
            key: "beforeLayout",
            value: function beforeLayout() {
                this._cache = {};
                this._dataLimitsCached = false;
            }
        },
        {
            key: "beforeUpdate",
            value: function beforeUpdate() {
                (0, _helpersSegmentJs.Q)(this.options.beforeUpdate, [
                    this
                ]);
            }
        },
        {
            key: "update",
            value: function update(maxWidth, maxHeight, margins) {
                var _options = this.options, beginAtZero = _options.beginAtZero, grace = _options.grace, tickOpts = _options.ticks;
                var sampleSize = tickOpts.sampleSize;
                this.beforeUpdate();
                this.maxWidth = maxWidth;
                this.maxHeight = maxHeight;
                this._margins = margins = Object.assign({
                    left: 0,
                    right: 0,
                    top: 0,
                    bottom: 0
                }, margins);
                this.ticks = null;
                this._labelSizes = null;
                this._gridLineItems = null;
                this._labelItems = null;
                this.beforeSetDimensions();
                this.setDimensions();
                this.afterSetDimensions();
                this._maxLength = this.isHorizontal() ? this.width + margins.left + margins.right : this.height + margins.top + margins.bottom;
                if (!this._dataLimitsCached) {
                    this.beforeDataLimits();
                    this.determineDataLimits();
                    this.afterDataLimits();
                    this._range = (0, _helpersSegmentJs.R)(this, grace, beginAtZero);
                    this._dataLimitsCached = true;
                }
                this.beforeBuildTicks();
                this.ticks = this.buildTicks() || [];
                this.afterBuildTicks();
                var samplingEnabled = sampleSize < this.ticks.length;
                this._convertTicksToLabels(samplingEnabled ? sample(this.ticks, sampleSize) : this.ticks);
                this.configure();
                this.beforeCalculateLabelRotation();
                this.calculateLabelRotation();
                this.afterCalculateLabelRotation();
                if (tickOpts.display && (tickOpts.autoSkip || tickOpts.source === "auto")) {
                    this.ticks = autoSkip(this, this.ticks);
                    this._labelSizes = null;
                    this.afterAutoSkip();
                }
                if (samplingEnabled) this._convertTicksToLabels(this.ticks);
                this.beforeFit();
                this.fit();
                this.afterFit();
                this.afterUpdate();
            }
        },
        {
            key: "configure",
            value: function configure() {
                var reversePixels = this.options.reverse;
                var startPixel, endPixel;
                if (this.isHorizontal()) {
                    startPixel = this.left;
                    endPixel = this.right;
                } else {
                    startPixel = this.top;
                    endPixel = this.bottom;
                    reversePixels = !reversePixels;
                }
                this._startPixel = startPixel;
                this._endPixel = endPixel;
                this._reversePixels = reversePixels;
                this._length = endPixel - startPixel;
                this._alignToPixels = this.options.alignToPixels;
            }
        },
        {
            key: "afterUpdate",
            value: function afterUpdate() {
                (0, _helpersSegmentJs.Q)(this.options.afterUpdate, [
                    this
                ]);
            }
        },
        {
            key: "beforeSetDimensions",
            value: function beforeSetDimensions() {
                (0, _helpersSegmentJs.Q)(this.options.beforeSetDimensions, [
                    this
                ]);
            }
        },
        {
            key: "setDimensions",
            value: function setDimensions() {
                if (this.isHorizontal()) {
                    this.width = this.maxWidth;
                    this.left = 0;
                    this.right = this.width;
                } else {
                    this.height = this.maxHeight;
                    this.top = 0;
                    this.bottom = this.height;
                }
                this.paddingLeft = 0;
                this.paddingTop = 0;
                this.paddingRight = 0;
                this.paddingBottom = 0;
            }
        },
        {
            key: "afterSetDimensions",
            value: function afterSetDimensions() {
                (0, _helpersSegmentJs.Q)(this.options.afterSetDimensions, [
                    this
                ]);
            }
        },
        {
            key: "_callHooks",
            value: function _callHooks(name) {
                this.chart.notifyPlugins(name, this.getContext());
                (0, _helpersSegmentJs.Q)(this.options[name], [
                    this
                ]);
            }
        },
        {
            key: "beforeDataLimits",
            value: function beforeDataLimits() {
                this._callHooks("beforeDataLimits");
            }
        },
        {
            key: "determineDataLimits",
            value: function determineDataLimits() {}
        },
        {
            key: "afterDataLimits",
            value: function afterDataLimits() {
                this._callHooks("afterDataLimits");
            }
        },
        {
            key: "beforeBuildTicks",
            value: function beforeBuildTicks() {
                this._callHooks("beforeBuildTicks");
            }
        },
        {
            key: "buildTicks",
            value: function buildTicks() {
                return [];
            }
        },
        {
            key: "afterBuildTicks",
            value: function afterBuildTicks() {
                this._callHooks("afterBuildTicks");
            }
        },
        {
            key: "beforeTickToLabelConversion",
            value: function beforeTickToLabelConversion() {
                (0, _helpersSegmentJs.Q)(this.options.beforeTickToLabelConversion, [
                    this
                ]);
            }
        },
        {
            key: "generateTickLabels",
            value: function generateTickLabels(ticks) {
                var tickOpts = this.options.ticks;
                var i, ilen, tick;
                for(i = 0, ilen = ticks.length; i < ilen; i++){
                    tick = ticks[i];
                    tick.label = (0, _helpersSegmentJs.Q)(tickOpts.callback, [
                        tick.value,
                        i,
                        ticks
                    ], this);
                }
            }
        },
        {
            key: "afterTickToLabelConversion",
            value: function afterTickToLabelConversion() {
                (0, _helpersSegmentJs.Q)(this.options.afterTickToLabelConversion, [
                    this
                ]);
            }
        },
        {
            key: "beforeCalculateLabelRotation",
            value: function beforeCalculateLabelRotation() {
                (0, _helpersSegmentJs.Q)(this.options.beforeCalculateLabelRotation, [
                    this
                ]);
            }
        },
        {
            key: "calculateLabelRotation",
            value: function calculateLabelRotation() {
                var options = this.options;
                var tickOpts = options.ticks;
                var numTicks = this.ticks.length;
                var minRotation = tickOpts.minRotation || 0;
                var maxRotation = tickOpts.maxRotation;
                var labelRotation = minRotation;
                var tickWidth, maxHeight, maxLabelDiagonal;
                if (!this._isVisible() || !tickOpts.display || minRotation >= maxRotation || numTicks <= 1 || !this.isHorizontal()) {
                    this.labelRotation = minRotation;
                    return;
                }
                var labelSizes = this._getLabelSizes();
                var maxLabelWidth = labelSizes.widest.width;
                var maxLabelHeight = labelSizes.highest.height;
                var maxWidth = (0, _helpersSegmentJs.w)(this.chart.width - maxLabelWidth, 0, this.maxWidth);
                tickWidth = options.offset ? this.maxWidth / numTicks : maxWidth / (numTicks - 1);
                if (maxLabelWidth + 6 > tickWidth) {
                    tickWidth = maxWidth / (numTicks - (options.offset ? 0.5 : 1));
                    maxHeight = this.maxHeight - getTickMarkLength(options.grid) - tickOpts.padding - getTitleHeight(options.title, this.chart.options.font);
                    maxLabelDiagonal = Math.sqrt(maxLabelWidth * maxLabelWidth + maxLabelHeight * maxLabelHeight);
                    labelRotation = (0, _helpersSegmentJs.S)(Math.min(Math.asin((0, _helpersSegmentJs.w)((labelSizes.highest.height + 6) / tickWidth, -1, 1)), Math.asin((0, _helpersSegmentJs.w)(maxHeight / maxLabelDiagonal, -1, 1)) - Math.asin((0, _helpersSegmentJs.w)(maxLabelHeight / maxLabelDiagonal, -1, 1))));
                    labelRotation = Math.max(minRotation, Math.min(maxRotation, labelRotation));
                }
                this.labelRotation = labelRotation;
            }
        },
        {
            key: "afterCalculateLabelRotation",
            value: function afterCalculateLabelRotation() {
                (0, _helpersSegmentJs.Q)(this.options.afterCalculateLabelRotation, [
                    this
                ]);
            }
        },
        {
            key: "afterAutoSkip",
            value: function afterAutoSkip() {}
        },
        {
            key: "beforeFit",
            value: function beforeFit() {
                (0, _helpersSegmentJs.Q)(this.options.beforeFit, [
                    this
                ]);
            }
        },
        {
            key: "fit",
            value: function fit() {
                var minSize = {
                    width: 0,
                    height: 0
                };
                var ref = this, chart = ref.chart, _options = ref.options, tickOpts = _options.ticks, titleOpts = _options.title, gridOpts = _options.grid;
                var display = this._isVisible();
                var isHorizontal = this.isHorizontal();
                if (display) {
                    var titleHeight = getTitleHeight(titleOpts, chart.options.font);
                    if (isHorizontal) {
                        minSize.width = this.maxWidth;
                        minSize.height = getTickMarkLength(gridOpts) + titleHeight;
                    } else {
                        minSize.height = this.maxHeight;
                        minSize.width = getTickMarkLength(gridOpts) + titleHeight;
                    }
                    if (tickOpts.display && this.ticks.length) {
                        var ref4 = this._getLabelSizes(), first = ref4.first, last = ref4.last, widest = ref4.widest, highest = ref4.highest;
                        var tickPadding = tickOpts.padding * 2;
                        var angleRadians = (0, _helpersSegmentJs.t)(this.labelRotation);
                        var cos = Math.cos(angleRadians);
                        var sin = Math.sin(angleRadians);
                        if (isHorizontal) {
                            var labelHeight = tickOpts.mirror ? 0 : sin * widest.width + cos * highest.height;
                            minSize.height = Math.min(this.maxHeight, minSize.height + labelHeight + tickPadding);
                        } else {
                            var labelWidth = tickOpts.mirror ? 0 : cos * widest.width + sin * highest.height;
                            minSize.width = Math.min(this.maxWidth, minSize.width + labelWidth + tickPadding);
                        }
                        this._calculatePadding(first, last, sin, cos);
                    }
                }
                this._handleMargins();
                if (isHorizontal) {
                    this.width = this._length = chart.width - this._margins.left - this._margins.right;
                    this.height = minSize.height;
                } else {
                    this.width = minSize.width;
                    this.height = this._length = chart.height - this._margins.top - this._margins.bottom;
                }
            }
        },
        {
            key: "_calculatePadding",
            value: function _calculatePadding(first, last, sin, cos) {
                var _options = this.options, _ticks = _options.ticks, align = _ticks.align, padding = _ticks.padding, position = _options.position;
                var isRotated = this.labelRotation !== 0;
                var labelsBelowTicks = position !== "top" && this.axis === "x";
                if (this.isHorizontal()) {
                    var offsetLeft = this.getPixelForTick(0) - this.left;
                    var offsetRight = this.right - this.getPixelForTick(this.ticks.length - 1);
                    var paddingLeft = 0;
                    var paddingRight = 0;
                    if (isRotated) {
                        if (labelsBelowTicks) {
                            paddingLeft = cos * first.width;
                            paddingRight = sin * last.height;
                        } else {
                            paddingLeft = sin * first.height;
                            paddingRight = cos * last.width;
                        }
                    } else if (align === "start") paddingRight = last.width;
                    else if (align === "end") paddingLeft = first.width;
                    else if (align !== "inner") {
                        paddingLeft = first.width / 2;
                        paddingRight = last.width / 2;
                    }
                    this.paddingLeft = Math.max((paddingLeft - offsetLeft + padding) * this.width / (this.width - offsetLeft), 0);
                    this.paddingRight = Math.max((paddingRight - offsetRight + padding) * this.width / (this.width - offsetRight), 0);
                } else {
                    var paddingTop = last.height / 2;
                    var paddingBottom = first.height / 2;
                    if (align === "start") {
                        paddingTop = 0;
                        paddingBottom = first.height;
                    } else if (align === "end") {
                        paddingTop = last.height;
                        paddingBottom = 0;
                    }
                    this.paddingTop = paddingTop + padding;
                    this.paddingBottom = paddingBottom + padding;
                }
            }
        },
        {
            key: "_handleMargins",
            value: function _handleMargins() {
                if (this._margins) {
                    this._margins.left = Math.max(this.paddingLeft, this._margins.left);
                    this._margins.top = Math.max(this.paddingTop, this._margins.top);
                    this._margins.right = Math.max(this.paddingRight, this._margins.right);
                    this._margins.bottom = Math.max(this.paddingBottom, this._margins.bottom);
                }
            }
        },
        {
            key: "afterFit",
            value: function afterFit() {
                (0, _helpersSegmentJs.Q)(this.options.afterFit, [
                    this
                ]);
            }
        },
        {
            key: "isHorizontal",
            value: function isHorizontal() {
                var _options = this.options, axis = _options.axis, position = _options.position;
                return position === "top" || position === "bottom" || axis === "x";
            }
        },
        {
            key: "isFullSize",
            value: function isFullSize() {
                return this.options.fullSize;
            }
        },
        {
            key: "_convertTicksToLabels",
            value: function _convertTicksToLabels(ticks) {
                this.beforeTickToLabelConversion();
                this.generateTickLabels(ticks);
                var i, ilen;
                for(i = 0, ilen = ticks.length; i < ilen; i++)if ((0, _helpersSegmentJs.k)(ticks[i].label)) {
                    ticks.splice(i, 1);
                    ilen--;
                    i--;
                }
                this.afterTickToLabelConversion();
            }
        },
        {
            key: "_getLabelSizes",
            value: function _getLabelSizes() {
                var labelSizes = this._labelSizes;
                if (!labelSizes) {
                    var sampleSize = this.options.ticks.sampleSize;
                    var ticks = this.ticks;
                    if (sampleSize < ticks.length) ticks = sample(ticks, sampleSize);
                    this._labelSizes = labelSizes = this._computeLabelSizes(ticks, ticks.length);
                }
                return labelSizes;
            }
        },
        {
            key: "_computeLabelSizes",
            value: function _computeLabelSizes(ticks, length) {
                var ref = this, ctx = ref.ctx, caches = ref._longestTextCache;
                var widths = [];
                var heights = [];
                var widestLabelSize = 0;
                var highestLabelSize = 0;
                var i, j, jlen, label, tickFont, fontString, cache, lineHeight, width, height, nestedLabel;
                for(i = 0; i < length; ++i){
                    label = ticks[i].label;
                    tickFont = this._resolveTickFontOptions(i);
                    ctx.font = fontString = tickFont.string;
                    cache = caches[fontString] = caches[fontString] || {
                        data: {},
                        gc: []
                    };
                    lineHeight = tickFont.lineHeight;
                    width = height = 0;
                    if (!(0, _helpersSegmentJs.k)(label) && !(0, _helpersSegmentJs.b)(label)) {
                        width = (0, _helpersSegmentJs.U)(ctx, cache.data, cache.gc, width, label);
                        height = lineHeight;
                    } else if ((0, _helpersSegmentJs.b)(label)) for(j = 0, jlen = label.length; j < jlen; ++j){
                        nestedLabel = label[j];
                        if (!(0, _helpersSegmentJs.k)(nestedLabel) && !(0, _helpersSegmentJs.b)(nestedLabel)) {
                            width = (0, _helpersSegmentJs.U)(ctx, cache.data, cache.gc, width, nestedLabel);
                            height += lineHeight;
                        }
                    }
                    widths.push(width);
                    heights.push(height);
                    widestLabelSize = Math.max(width, widestLabelSize);
                    highestLabelSize = Math.max(height, highestLabelSize);
                }
                garbageCollect(caches, length);
                var widest = widths.indexOf(widestLabelSize);
                var highest = heights.indexOf(highestLabelSize);
                var valueAt = function(idx) {
                    return {
                        width: widths[idx] || 0,
                        height: heights[idx] || 0
                    };
                };
                return {
                    first: valueAt(0),
                    last: valueAt(length - 1),
                    widest: valueAt(widest),
                    highest: valueAt(highest),
                    widths: widths,
                    heights: heights
                };
            }
        },
        {
            key: "getLabelForValue",
            value: function getLabelForValue(value) {
                return value;
            }
        },
        {
            key: "getPixelForValue",
            value: function getPixelForValue(value, index) {
                return NaN;
            }
        },
        {
            key: "getValueForPixel",
            value: function getValueForPixel(pixel) {}
        },
        {
            key: "getPixelForTick",
            value: function getPixelForTick(index46) {
                var ticks = this.ticks;
                if (index46 < 0 || index46 > ticks.length - 1) return null;
                return this.getPixelForValue(ticks[index46].value);
            }
        },
        {
            key: "getPixelForDecimal",
            value: function getPixelForDecimal(decimal) {
                if (this._reversePixels) decimal = 1 - decimal;
                var pixel = this._startPixel + decimal * this._length;
                return (0, _helpersSegmentJs.V)(this._alignToPixels ? (0, _helpersSegmentJs.W)(this.chart, pixel, 0) : pixel);
            }
        },
        {
            key: "getDecimalForPixel",
            value: function getDecimalForPixel(pixel) {
                var decimal = (pixel - this._startPixel) / this._length;
                return this._reversePixels ? 1 - decimal : decimal;
            }
        },
        {
            key: "getBasePixel",
            value: function getBasePixel() {
                return this.getPixelForValue(this.getBaseValue());
            }
        },
        {
            key: "getBaseValue",
            value: function getBaseValue() {
                var ref = this, min = ref.min, max = ref.max;
                return min < 0 && max < 0 ? max : min > 0 && max > 0 ? min : 0;
            }
        },
        {
            key: "getContext",
            value: function getContext(index47) {
                var ticks = this.ticks || [];
                if (index47 >= 0 && index47 < ticks.length) {
                    var tick = ticks[index47];
                    return tick.$context || (tick.$context = createTickContext(this.getContext(), index47, tick));
                }
                return this.$context || (this.$context = createScaleContext(this.chart.getContext(), this));
            }
        },
        {
            key: "_tickSize",
            value: function _tickSize() {
                var optionTicks = this.options.ticks;
                var rot = (0, _helpersSegmentJs.t)(this.labelRotation);
                var cos = Math.abs(Math.cos(rot));
                var sin = Math.abs(Math.sin(rot));
                var labelSizes = this._getLabelSizes();
                var padding = optionTicks.autoSkipPadding || 0;
                var w = labelSizes ? labelSizes.widest.width + padding : 0;
                var h = labelSizes ? labelSizes.highest.height + padding : 0;
                return this.isHorizontal() ? h * cos > w * sin ? w / cos : h / sin : h * sin < w * cos ? h / cos : w / sin;
            }
        },
        {
            key: "_isVisible",
            value: function _isVisible() {
                var display = this.options.display;
                if (display !== "auto") return !!display;
                return this.getMatchingVisibleMetas().length > 0;
            }
        },
        {
            key: "_computeGridLineItems",
            value: function _computeGridLineItems(chartArea) {
                var axis = this.axis;
                var chart = this.chart;
                var options = this.options;
                var grid = options.grid, position = options.position;
                var offset = grid.offset;
                var isHorizontal = this.isHorizontal();
                var ticks = this.ticks;
                var ticksLength = ticks.length + (offset ? 1 : 0);
                var tl = getTickMarkLength(grid);
                var items = [];
                var borderOpts = grid.setContext(this.getContext());
                var axisWidth = borderOpts.drawBorder ? borderOpts.borderWidth : 0;
                var axisHalfWidth = axisWidth / 2;
                var alignBorderValue = function alignBorderValue(pixel) {
                    return (0, _helpersSegmentJs.W)(chart, pixel, axisWidth);
                };
                var borderValue, i, lineValue, alignedLineValue;
                var tx1, ty1, tx2, ty2, x1, y1, x2, y2;
                if (position === "top") {
                    borderValue = alignBorderValue(this.bottom);
                    ty1 = this.bottom - tl;
                    ty2 = borderValue - axisHalfWidth;
                    y1 = alignBorderValue(chartArea.top) + axisHalfWidth;
                    y2 = chartArea.bottom;
                } else if (position === "bottom") {
                    borderValue = alignBorderValue(this.top);
                    y1 = chartArea.top;
                    y2 = alignBorderValue(chartArea.bottom) - axisHalfWidth;
                    ty1 = borderValue + axisHalfWidth;
                    ty2 = this.top + tl;
                } else if (position === "left") {
                    borderValue = alignBorderValue(this.right);
                    tx1 = this.right - tl;
                    tx2 = borderValue - axisHalfWidth;
                    x1 = alignBorderValue(chartArea.left) + axisHalfWidth;
                    x2 = chartArea.right;
                } else if (position === "right") {
                    borderValue = alignBorderValue(this.left);
                    x1 = chartArea.left;
                    x2 = alignBorderValue(chartArea.right) - axisHalfWidth;
                    tx1 = borderValue + axisHalfWidth;
                    tx2 = this.left + tl;
                } else if (axis === "x") {
                    if (position === "center") borderValue = alignBorderValue((chartArea.top + chartArea.bottom) / 2 + 0.5);
                    else if ((0, _helpersSegmentJs.i)(position)) {
                        var positionAxisID = Object.keys(position)[0];
                        var value = position[positionAxisID];
                        borderValue = alignBorderValue(this.chart.scales[positionAxisID].getPixelForValue(value));
                    }
                    y1 = chartArea.top;
                    y2 = chartArea.bottom;
                    ty1 = borderValue + axisHalfWidth;
                    ty2 = ty1 + tl;
                } else if (axis === "y") {
                    if (position === "center") borderValue = alignBorderValue((chartArea.left + chartArea.right) / 2);
                    else if ((0, _helpersSegmentJs.i)(position)) {
                        var positionAxisID2 = Object.keys(position)[0];
                        var value2 = position[positionAxisID2];
                        borderValue = alignBorderValue(this.chart.scales[positionAxisID2].getPixelForValue(value2));
                    }
                    tx1 = borderValue - axisHalfWidth;
                    tx2 = tx1 - tl;
                    x1 = chartArea.left;
                    x2 = chartArea.right;
                }
                var limit = (0, _helpersSegmentJs.v)(options.ticks.maxTicksLimit, ticksLength);
                var step = Math.max(1, Math.ceil(ticksLength / limit));
                for(i = 0; i < ticksLength; i += step){
                    var optsAtIndex = grid.setContext(this.getContext(i));
                    var lineWidth = optsAtIndex.lineWidth;
                    var lineColor = optsAtIndex.color;
                    var borderDash = grid.borderDash || [];
                    var borderDashOffset = optsAtIndex.borderDashOffset;
                    var tickWidth = optsAtIndex.tickWidth;
                    var tickColor = optsAtIndex.tickColor;
                    var tickBorderDash = optsAtIndex.tickBorderDash || [];
                    var tickBorderDashOffset = optsAtIndex.tickBorderDashOffset;
                    lineValue = getPixelForGridLine(this, i, offset);
                    if (lineValue === undefined) continue;
                    alignedLineValue = (0, _helpersSegmentJs.W)(chart, lineValue, lineWidth);
                    if (isHorizontal) tx1 = tx2 = x1 = x2 = alignedLineValue;
                    else ty1 = ty2 = y1 = y2 = alignedLineValue;
                    items.push({
                        tx1: tx1,
                        ty1: ty1,
                        tx2: tx2,
                        ty2: ty2,
                        x1: x1,
                        y1: y1,
                        x2: x2,
                        y2: y2,
                        width: lineWidth,
                        color: lineColor,
                        borderDash: borderDash,
                        borderDashOffset: borderDashOffset,
                        tickWidth: tickWidth,
                        tickColor: tickColor,
                        tickBorderDash: tickBorderDash,
                        tickBorderDashOffset: tickBorderDashOffset
                    });
                }
                this._ticksLength = ticksLength;
                this._borderValue = borderValue;
                return items;
            }
        },
        {
            key: "_computeLabelItems",
            value: function _computeLabelItems(chartArea) {
                var axis = this.axis;
                var options = this.options;
                var position = options.position, optionTicks = options.ticks;
                var isHorizontal = this.isHorizontal();
                var ticks = this.ticks;
                var align = optionTicks.align, crossAlign = optionTicks.crossAlign, padding = optionTicks.padding, mirror = optionTicks.mirror;
                var tl = getTickMarkLength(options.grid);
                var tickAndPadding = tl + padding;
                var hTickAndPadding = mirror ? -padding : tickAndPadding;
                var rotation = -(0, _helpersSegmentJs.t)(this.labelRotation);
                var items = [];
                var i, ilen, tick, label, x, y, textAlign, pixel, font, lineHeight, lineCount, textOffset;
                var textBaseline = "middle";
                if (position === "top") {
                    y = this.bottom - hTickAndPadding;
                    textAlign = this._getXAxisLabelAlignment();
                } else if (position === "bottom") {
                    y = this.top + hTickAndPadding;
                    textAlign = this._getXAxisLabelAlignment();
                } else if (position === "left") {
                    var ret = this._getYAxisLabelAlignment(tl);
                    textAlign = ret.textAlign;
                    x = ret.x;
                } else if (position === "right") {
                    var ret1 = this._getYAxisLabelAlignment(tl);
                    textAlign = ret1.textAlign;
                    x = ret1.x;
                } else if (axis === "x") {
                    if (position === "center") y = (chartArea.top + chartArea.bottom) / 2 + tickAndPadding;
                    else if ((0, _helpersSegmentJs.i)(position)) {
                        var positionAxisID = Object.keys(position)[0];
                        var value = position[positionAxisID];
                        y = this.chart.scales[positionAxisID].getPixelForValue(value) + tickAndPadding;
                    }
                    textAlign = this._getXAxisLabelAlignment();
                } else if (axis === "y") {
                    if (position === "center") x = (chartArea.left + chartArea.right) / 2 - tickAndPadding;
                    else if ((0, _helpersSegmentJs.i)(position)) {
                        var positionAxisID3 = Object.keys(position)[0];
                        var value3 = position[positionAxisID3];
                        x = this.chart.scales[positionAxisID3].getPixelForValue(value3);
                    }
                    textAlign = this._getYAxisLabelAlignment(tl).textAlign;
                }
                if (axis === "y") {
                    if (align === "start") textBaseline = "top";
                    else if (align === "end") textBaseline = "bottom";
                }
                var labelSizes = this._getLabelSizes();
                for(i = 0, ilen = ticks.length; i < ilen; ++i){
                    tick = ticks[i];
                    label = tick.label;
                    var optsAtIndex = optionTicks.setContext(this.getContext(i));
                    pixel = this.getPixelForTick(i) + optionTicks.labelOffset;
                    font = this._resolveTickFontOptions(i);
                    lineHeight = font.lineHeight;
                    lineCount = (0, _helpersSegmentJs.b)(label) ? label.length : 1;
                    var halfCount = lineCount / 2;
                    var color = optsAtIndex.color;
                    var strokeColor = optsAtIndex.textStrokeColor;
                    var strokeWidth = optsAtIndex.textStrokeWidth;
                    var tickTextAlign = textAlign;
                    if (isHorizontal) {
                        x = pixel;
                        if (textAlign === "inner") {
                            if (i === ilen - 1) tickTextAlign = !this.options.reverse ? "right" : "left";
                            else if (i === 0) tickTextAlign = !this.options.reverse ? "left" : "right";
                            else tickTextAlign = "center";
                        }
                        if (position === "top") {
                            if (crossAlign === "near" || rotation !== 0) textOffset = -lineCount * lineHeight + lineHeight / 2;
                            else if (crossAlign === "center") textOffset = -labelSizes.highest.height / 2 - halfCount * lineHeight + lineHeight;
                            else textOffset = -labelSizes.highest.height + lineHeight / 2;
                        } else {
                            if (crossAlign === "near" || rotation !== 0) textOffset = lineHeight / 2;
                            else if (crossAlign === "center") textOffset = labelSizes.highest.height / 2 - halfCount * lineHeight;
                            else textOffset = labelSizes.highest.height - lineCount * lineHeight;
                        }
                        if (mirror) textOffset *= -1;
                    } else {
                        y = pixel;
                        textOffset = (1 - lineCount) * lineHeight / 2;
                    }
                    var backdrop = void 0;
                    if (optsAtIndex.showLabelBackdrop) {
                        var labelPadding = (0, _helpersSegmentJs.D)(optsAtIndex.backdropPadding);
                        var height = labelSizes.heights[i];
                        var width = labelSizes.widths[i];
                        var top = y + textOffset - labelPadding.top;
                        var left = x - labelPadding.left;
                        switch(textBaseline){
                            case "middle":
                                top -= height / 2;
                                break;
                            case "bottom":
                                top -= height;
                                break;
                        }
                        switch(textAlign){
                            case "center":
                                left -= width / 2;
                                break;
                            case "right":
                                left -= width;
                                break;
                        }
                        backdrop = {
                            left: left,
                            top: top,
                            width: width + labelPadding.width,
                            height: height + labelPadding.height,
                            color: optsAtIndex.backdropColor
                        };
                    }
                    items.push({
                        rotation: rotation,
                        label: label,
                        font: font,
                        color: color,
                        strokeColor: strokeColor,
                        strokeWidth: strokeWidth,
                        textOffset: textOffset,
                        textAlign: tickTextAlign,
                        textBaseline: textBaseline,
                        translation: [
                            x,
                            y
                        ],
                        backdrop: backdrop
                    });
                }
                return items;
            }
        },
        {
            key: "_getXAxisLabelAlignment",
            value: function _getXAxisLabelAlignment() {
                var _options = this.options, position = _options.position, ticks = _options.ticks;
                var rotation = -(0, _helpersSegmentJs.t)(this.labelRotation);
                if (rotation) return position === "top" ? "left" : "right";
                var align = "center";
                if (ticks.align === "start") align = "left";
                else if (ticks.align === "end") align = "right";
                else if (ticks.align === "inner") align = "inner";
                return align;
            }
        },
        {
            key: "_getYAxisLabelAlignment",
            value: function _getYAxisLabelAlignment(tl) {
                var _options = this.options, position = _options.position, _ticks = _options.ticks, crossAlign = _ticks.crossAlign, mirror = _ticks.mirror, padding = _ticks.padding;
                var labelSizes = this._getLabelSizes();
                var tickAndPadding = tl + padding;
                var widest = labelSizes.widest.width;
                var textAlign;
                var x;
                if (position === "left") {
                    if (mirror) {
                        x = this.right + padding;
                        if (crossAlign === "near") textAlign = "left";
                        else if (crossAlign === "center") {
                            textAlign = "center";
                            x += widest / 2;
                        } else {
                            textAlign = "right";
                            x += widest;
                        }
                    } else {
                        x = this.right - tickAndPadding;
                        if (crossAlign === "near") textAlign = "right";
                        else if (crossAlign === "center") {
                            textAlign = "center";
                            x -= widest / 2;
                        } else {
                            textAlign = "left";
                            x = this.left;
                        }
                    }
                } else if (position === "right") {
                    if (mirror) {
                        x = this.left + padding;
                        if (crossAlign === "near") textAlign = "right";
                        else if (crossAlign === "center") {
                            textAlign = "center";
                            x -= widest / 2;
                        } else {
                            textAlign = "left";
                            x -= widest;
                        }
                    } else {
                        x = this.left + tickAndPadding;
                        if (crossAlign === "near") textAlign = "left";
                        else if (crossAlign === "center") {
                            textAlign = "center";
                            x += widest / 2;
                        } else {
                            textAlign = "right";
                            x = this.right;
                        }
                    }
                } else textAlign = "right";
                return {
                    textAlign: textAlign,
                    x: x
                };
            }
        },
        {
            key: "_computeLabelArea",
            value: function _computeLabelArea() {
                if (this.options.ticks.mirror) return;
                var chart = this.chart;
                var position = this.options.position;
                if (position === "left" || position === "right") return {
                    top: 0,
                    left: this.left,
                    bottom: chart.height,
                    right: this.right
                };
                if (position === "top" || position === "bottom") return {
                    top: this.top,
                    left: 0,
                    bottom: this.bottom,
                    right: chart.width
                };
            }
        },
        {
            key: "drawBackground",
            value: function drawBackground() {
                var ref = this, ctx = ref.ctx, backgroundColor = ref.options.backgroundColor, left = ref.left, top = ref.top, width = ref.width, height = ref.height;
                if (backgroundColor) {
                    ctx.save();
                    ctx.fillStyle = backgroundColor;
                    ctx.fillRect(left, top, width, height);
                    ctx.restore();
                }
            }
        },
        {
            key: "getLineWidthForValue",
            value: function getLineWidthForValue(value) {
                var grid = this.options.grid;
                if (!this._isVisible() || !grid.display) return 0;
                var ticks = this.ticks;
                var index48 = ticks.findIndex(function(t) {
                    return t.value === value;
                });
                if (index48 >= 0) {
                    var opts = grid.setContext(this.getContext(index48));
                    return opts.lineWidth;
                }
                return 0;
            }
        },
        {
            key: "drawGrid",
            value: function drawGrid(chartArea) {
                var grid = this.options.grid;
                var ctx = this.ctx;
                var items = this._gridLineItems || (this._gridLineItems = this._computeGridLineItems(chartArea));
                var i, ilen;
                var drawLine = function(p1, p2, style) {
                    if (!style.width || !style.color) return;
                    ctx.save();
                    ctx.lineWidth = style.width;
                    ctx.strokeStyle = style.color;
                    ctx.setLineDash(style.borderDash || []);
                    ctx.lineDashOffset = style.borderDashOffset;
                    ctx.beginPath();
                    ctx.moveTo(p1.x, p1.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.stroke();
                    ctx.restore();
                };
                if (grid.display) for(i = 0, ilen = items.length; i < ilen; ++i){
                    var item = items[i];
                    if (grid.drawOnChartArea) drawLine({
                        x: item.x1,
                        y: item.y1
                    }, {
                        x: item.x2,
                        y: item.y2
                    }, item);
                    if (grid.drawTicks) drawLine({
                        x: item.tx1,
                        y: item.ty1
                    }, {
                        x: item.tx2,
                        y: item.ty2
                    }, {
                        color: item.tickColor,
                        width: item.tickWidth,
                        borderDash: item.tickBorderDash,
                        borderDashOffset: item.tickBorderDashOffset
                    });
                }
            }
        },
        {
            key: "drawBorder",
            value: function drawBorder() {
                var ref = this, chart = ref.chart, ctx = ref.ctx, grid = ref.options.grid;
                var borderOpts = grid.setContext(this.getContext());
                var axisWidth = grid.drawBorder ? borderOpts.borderWidth : 0;
                if (!axisWidth) return;
                var lastLineWidth = grid.setContext(this.getContext(0)).lineWidth;
                var borderValue = this._borderValue;
                var x1, x2, y1, y2;
                if (this.isHorizontal()) {
                    x1 = (0, _helpersSegmentJs.W)(chart, this.left, axisWidth) - axisWidth / 2;
                    x2 = (0, _helpersSegmentJs.W)(chart, this.right, lastLineWidth) + lastLineWidth / 2;
                    y1 = y2 = borderValue;
                } else {
                    y1 = (0, _helpersSegmentJs.W)(chart, this.top, axisWidth) - axisWidth / 2;
                    y2 = (0, _helpersSegmentJs.W)(chart, this.bottom, lastLineWidth) + lastLineWidth / 2;
                    x1 = x2 = borderValue;
                }
                ctx.save();
                ctx.lineWidth = borderOpts.borderWidth;
                ctx.strokeStyle = borderOpts.borderColor;
                ctx.beginPath();
                ctx.moveTo(x1, y1);
                ctx.lineTo(x2, y2);
                ctx.stroke();
                ctx.restore();
            }
        },
        {
            key: "drawLabels",
            value: function drawLabels(chartArea) {
                var optionTicks = this.options.ticks;
                if (!optionTicks.display) return;
                var ctx = this.ctx;
                var area = this._computeLabelArea();
                if (area) (0, _helpersSegmentJs.X)(ctx, area);
                var items = this._labelItems || (this._labelItems = this._computeLabelItems(chartArea));
                var i, ilen;
                for(i = 0, ilen = items.length; i < ilen; ++i){
                    var item = items[i];
                    var tickFont = item.font;
                    var label = item.label;
                    if (item.backdrop) {
                        ctx.fillStyle = item.backdrop.color;
                        ctx.fillRect(item.backdrop.left, item.backdrop.top, item.backdrop.width, item.backdrop.height);
                    }
                    var y = item.textOffset;
                    (0, _helpersSegmentJs.Y)(ctx, label, 0, y, tickFont, item);
                }
                if (area) (0, _helpersSegmentJs.Z)(ctx);
            }
        },
        {
            key: "drawTitle",
            value: function drawTitle() {
                var ref = this, ctx = ref.ctx, _options = ref.options, position = _options.position, title = _options.title, reverse = _options.reverse;
                if (!title.display) return;
                var font = (0, _helpersSegmentJs.$)(title.font);
                var padding = (0, _helpersSegmentJs.D)(title.padding);
                var align = title.align;
                var offset = font.lineHeight / 2;
                if (position === "bottom" || position === "center" || (0, _helpersSegmentJs.i)(position)) {
                    offset += padding.bottom;
                    if ((0, _helpersSegmentJs.b)(title.text)) offset += font.lineHeight * (title.text.length - 1);
                } else offset += padding.top;
                var ref5 = titleArgs(this, offset, position, align), titleX = ref5.titleX, titleY = ref5.titleY, maxWidth = ref5.maxWidth, rotation = ref5.rotation;
                (0, _helpersSegmentJs.Y)(ctx, title.text, 0, 0, font, {
                    color: title.color,
                    maxWidth: maxWidth,
                    rotation: rotation,
                    textAlign: titleAlign(align, position, reverse),
                    textBaseline: "middle",
                    translation: [
                        titleX,
                        titleY
                    ]
                });
            }
        },
        {
            key: "draw",
            value: function draw2(chartArea) {
                if (!this._isVisible()) return;
                this.drawBackground();
                this.drawGrid(chartArea);
                this.drawBorder();
                this.drawTitle();
                this.drawLabels(chartArea);
            }
        },
        {
            key: "_layers",
            value: function _layers() {
                var _this = this;
                var opts = this.options;
                var tz = opts.ticks && opts.ticks.z || 0;
                var gz = (0, _helpersSegmentJs.v)(opts.grid && opts.grid.z, -1);
                if (!this._isVisible() || this.draw !== Scale.prototype.draw) return [
                    {
                        z: tz,
                        draw: function(chartArea) {
                            _this.draw(chartArea);
                        }
                    }
                ];
                return [
                    {
                        z: gz,
                        draw: function(chartArea) {
                            _this.drawBackground();
                            _this.drawGrid(chartArea);
                            _this.drawTitle();
                        }
                    },
                    {
                        z: gz + 1,
                        draw: function() {
                            _this.drawBorder();
                        }
                    },
                    {
                        z: tz,
                        draw: function(chartArea) {
                            _this.drawLabels(chartArea);
                        }
                    }
                ];
            }
        },
        {
            key: "getMatchingVisibleMetas",
            value: function getMatchingVisibleMetas(type) {
                var metas = this.chart.getSortedVisibleDatasetMetas();
                var axisID = this.axis + "AxisID";
                var result = [];
                var i, ilen;
                for(i = 0, ilen = metas.length; i < ilen; ++i){
                    var meta = metas[i];
                    if (meta[axisID] === this.id && (!type || meta.type === type)) result.push(meta);
                }
                return result;
            }
        },
        {
            key: "_resolveTickFontOptions",
            value: function _resolveTickFontOptions(index49) {
                var opts = this.options.ticks.setContext(this.getContext(index49));
                return (0, _helpersSegmentJs.$)(opts.font);
            }
        },
        {
            key: "_maxDigits",
            value: function _maxDigits() {
                var fontSize = this._resolveTickFontOptions(0).lineHeight;
                return (this.isHorizontal() ? this.width : this.height) / fontSize;
            }
        }
    ]);
    return Scale;
}((0, _wrapNativeSuperJsDefault.default)(Element));
var TypedRegistry = /*#__PURE__*/ function() {
    "use strict";
    function TypedRegistry(type, scope, override) {
        (0, _classCallCheckJsDefault.default)(this, TypedRegistry);
        this.type = type;
        this.scope = scope;
        this.override = override;
        this.items = Object.create(null);
    }
    (0, _createClassJsDefault.default)(TypedRegistry, [
        {
            key: "isForType",
            value: function isForType(type) {
                return Object.prototype.isPrototypeOf.call(this.type.prototype, type.prototype);
            }
        },
        {
            key: "register",
            value: function register(item) {
                var proto = Object.getPrototypeOf(item);
                var parentScope;
                if (isIChartComponent(proto)) parentScope = this.register(proto);
                var items = this.items;
                var id = item.id;
                var scope = this.scope + "." + id;
                if (!id) throw new Error("class does not have id: " + item);
                if (id in items) return scope;
                items[id] = item;
                registerDefaults(item, scope, parentScope);
                if (this.override) (0, _helpersSegmentJs.d).override(item.id, item.overrides);
                return scope;
            }
        },
        {
            key: "get",
            value: function get(id) {
                return this.items[id];
            }
        },
        {
            key: "unregister",
            value: function unregister(item) {
                var items = this.items;
                var id = item.id;
                var scope = this.scope;
                if (id in items) delete items[id];
                if (scope && id in (0, _helpersSegmentJs.d)[scope]) {
                    delete (0, _helpersSegmentJs.d)[scope][id];
                    if (this.override) delete (0, _helpersSegmentJs.a2)[id];
                }
            }
        }
    ]);
    return TypedRegistry;
}();
function registerDefaults(item, scope, parentScope) {
    var itemDefaults = (0, _helpersSegmentJs.a3)(Object.create(null), [
        parentScope ? (0, _helpersSegmentJs.d).get(parentScope) : {},
        (0, _helpersSegmentJs.d).get(scope),
        item.defaults
    ]);
    (0, _helpersSegmentJs.d).set(scope, itemDefaults);
    if (item.defaultRoutes) routeDefaults(scope, item.defaultRoutes);
    if (item.descriptors) (0, _helpersSegmentJs.d).describe(scope, item.descriptors);
}
function routeDefaults(scope, routes) {
    Object.keys(routes).forEach(function(property) {
        var propertyParts = property.split(".");
        var sourceName = propertyParts.pop();
        var sourceScope = [
            scope
        ].concat(propertyParts).join(".");
        var parts = routes[property].split(".");
        var targetName = parts.pop();
        var targetScope = parts.join(".");
        (0, _helpersSegmentJs.d).route(sourceScope, sourceName, targetScope, targetName);
    });
}
function isIChartComponent(proto) {
    return "id" in proto && "defaults" in proto;
}
var Registry = /*#__PURE__*/ function() {
    "use strict";
    function Registry() {
        (0, _classCallCheckJsDefault.default)(this, Registry);
        this.controllers = new TypedRegistry(DatasetController, "datasets", true);
        this.elements = new TypedRegistry(Element, "elements");
        this.plugins = new TypedRegistry(Object, "plugins");
        this.scales = new TypedRegistry(Scale, "scales");
        this._typedRegistries = [
            this.controllers,
            this.scales,
            this.elements
        ];
    }
    (0, _createClassJsDefault.default)(Registry, [
        {
            key: "add",
            value: function add() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                this._each("register", args);
            }
        },
        {
            key: "remove",
            value: function remove() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                this._each("unregister", args);
            }
        },
        {
            key: "addControllers",
            value: function addControllers() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                this._each("register", args, this.controllers);
            }
        },
        {
            key: "addElements",
            value: function addElements() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                this._each("register", args, this.elements);
            }
        },
        {
            key: "addPlugins",
            value: function addPlugins() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                this._each("register", args, this.plugins);
            }
        },
        {
            key: "addScales",
            value: function addScales() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                this._each("register", args, this.scales);
            }
        },
        {
            key: "getController",
            value: function getController(id) {
                return this._get(id, this.controllers, "controller");
            }
        },
        {
            key: "getElement",
            value: function getElement(id) {
                return this._get(id, this.elements, "element");
            }
        },
        {
            key: "getPlugin",
            value: function getPlugin(id) {
                return this._get(id, this.plugins, "plugin");
            }
        },
        {
            key: "getScale",
            value: function getScale(id) {
                return this._get(id, this.scales, "scale");
            }
        },
        {
            key: "removeControllers",
            value: function removeControllers() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                this._each("unregister", args, this.controllers);
            }
        },
        {
            key: "removeElements",
            value: function removeElements() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                this._each("unregister", args, this.elements);
            }
        },
        {
            key: "removePlugins",
            value: function removePlugins() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                this._each("unregister", args, this.plugins);
            }
        },
        {
            key: "removeScales",
            value: function removeScales() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                this._each("unregister", args, this.scales);
            }
        },
        {
            key: "_each",
            value: function _each(method, args, typedRegistry) {
                var _this = this;
                (0, _toConsumableArrayJsDefault.default)(args).forEach(function(arg) {
                    var _this1 = _this;
                    var reg = typedRegistry || _this._getRegistryForType(arg);
                    if (typedRegistry || reg.isForType(arg) || reg === _this.plugins && arg.id) _this._exec(method, reg, arg);
                    else (0, _helpersSegmentJs.E)(arg, function(item) {
                        var itemReg = typedRegistry || _this1._getRegistryForType(item);
                        _this1._exec(method, itemReg, item);
                    });
                });
            }
        },
        {
            key: "_exec",
            value: function _exec(method, registry1, component) {
                var camelMethod = (0, _helpersSegmentJs.a4)(method);
                (0, _helpersSegmentJs.Q)(component["before" + camelMethod], [], component);
                registry1[method](component);
                (0, _helpersSegmentJs.Q)(component["after" + camelMethod], [], component);
            }
        },
        {
            key: "_getRegistryForType",
            value: function _getRegistryForType(type) {
                for(var i = 0; i < this._typedRegistries.length; i++){
                    var reg = this._typedRegistries[i];
                    if (reg.isForType(type)) return reg;
                }
                return this.plugins;
            }
        },
        {
            key: "_get",
            value: function _get(id, typedRegistry, type) {
                var item = typedRegistry.get(id);
                if (item === undefined) throw new Error('"' + id + '" is not a registered ' + type + ".");
                return item;
            }
        }
    ]);
    return Registry;
}();
var registry = new Registry();
var PluginService = /*#__PURE__*/ function() {
    "use strict";
    function PluginService() {
        (0, _classCallCheckJsDefault.default)(this, PluginService);
        this._init = [];
    }
    (0, _createClassJsDefault.default)(PluginService, [
        {
            key: "notify",
            value: function notify(chart, hook, args, filter) {
                if (hook === "beforeInit") {
                    this._init = this._createDescriptors(chart, true);
                    this._notify(this._init, chart, "install");
                }
                var descriptors = filter ? this._descriptors(chart).filter(filter) : this._descriptors(chart);
                var result = this._notify(descriptors, chart, hook, args);
                if (hook === "afterDestroy") {
                    this._notify(descriptors, chart, "stop");
                    this._notify(this._init, chart, "uninstall");
                }
                return result;
            }
        },
        {
            key: "_notify",
            value: function _notify(descriptors, chart, hook, args) {
                args = args || {};
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                try {
                    for(var _iterator = descriptors[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var descriptor = _step.value;
                        var plugin = descriptor.plugin;
                        var method = plugin[hook];
                        var params = [
                            chart,
                            args,
                            descriptor.options
                        ];
                        if ((0, _helpersSegmentJs.Q)(method, params, plugin) === false && args.cancelable) return false;
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
                return true;
            }
        },
        {
            key: "invalidate",
            value: function invalidate() {
                if (!(0, _helpersSegmentJs.k)(this._cache)) {
                    this._oldCache = this._cache;
                    this._cache = undefined;
                }
            }
        },
        {
            key: "_descriptors",
            value: function _descriptors(chart) {
                if (this._cache) return this._cache;
                var descriptors = this._cache = this._createDescriptors(chart);
                this._notifyStateChanges(chart);
                return descriptors;
            }
        },
        {
            key: "_createDescriptors",
            value: function _createDescriptors(chart, all) {
                var config = chart && chart.config;
                var options = (0, _helpersSegmentJs.v)(config.options && config.options.plugins, {});
                var plugins1 = allPlugins(config);
                return options === false && !all ? [] : createDescriptors(chart, plugins1, options, all);
            }
        },
        {
            key: "_notifyStateChanges",
            value: function _notifyStateChanges(chart) {
                var previousDescriptors = this._oldCache || [];
                var descriptors = this._cache;
                var diff = function(a, b) {
                    return a.filter(function(x) {
                        return !b.some(function(y) {
                            return x.plugin.id === y.plugin.id;
                        });
                    });
                };
                this._notify(diff(previousDescriptors, descriptors), chart, "stop");
                this._notify(diff(descriptors, previousDescriptors), chart, "start");
            }
        }
    ]);
    return PluginService;
}();
function allPlugins(config) {
    var plugins2 = [];
    var keys = Object.keys(registry.plugins.items);
    for(var i = 0; i < keys.length; i++)plugins2.push(registry.getPlugin(keys[i]));
    var local = config.plugins || [];
    for(var i3 = 0; i3 < local.length; i3++){
        var plugin = local[i3];
        if (plugins2.indexOf(plugin) === -1) plugins2.push(plugin);
    }
    return plugins2;
}
function getOpts(options, all) {
    if (!all && options === false) return null;
    if (options === true) return {};
    return options;
}
function createDescriptors(chart, plugins3, options, all) {
    var result = [];
    var context = chart.getContext();
    for(var i = 0; i < plugins3.length; i++){
        var plugin = plugins3[i];
        var id = plugin.id;
        var opts = getOpts(options[id], all);
        if (opts === null) continue;
        result.push({
            plugin: plugin,
            options: pluginOpts(chart.config, plugin, opts, context)
        });
    }
    return result;
}
function pluginOpts(config, plugin, opts, context) {
    var keys = config.pluginScopeKeys(plugin);
    var scopes = config.getOptionScopes(opts, keys);
    return config.createResolver(scopes, context, [
        ""
    ], {
        scriptable: false,
        indexable: false,
        allKeys: true
    });
}
function getIndexAxis(type, options) {
    var datasetDefaults = (0, _helpersSegmentJs.d).datasets[type] || {};
    var datasetOptions = (options.datasets || {})[type] || {};
    return datasetOptions.indexAxis || options.indexAxis || datasetDefaults.indexAxis || "x";
}
function getAxisFromDefaultScaleID(id, indexAxis) {
    var axis = id;
    if (id === "_index_") axis = indexAxis;
    else if (id === "_value_") axis = indexAxis === "x" ? "y" : "x";
    return axis;
}
function getDefaultScaleIDFromAxis(axis, indexAxis) {
    return axis === indexAxis ? "_index_" : "_value_";
}
function axisFromPosition(position) {
    if (position === "top" || position === "bottom") return "x";
    if (position === "left" || position === "right") return "y";
}
function determineAxis(id, scaleOptions) {
    if (id === "x" || id === "y") return id;
    return scaleOptions.axis || axisFromPosition(scaleOptions.position) || id.charAt(0).toLowerCase();
}
function mergeScaleConfig(config, options) {
    var chartDefaults = (0, _helpersSegmentJs.a2)[config.type] || {
        scales: {}
    };
    var configScales = options.scales || {};
    var chartIndexAxis = getIndexAxis(config.type, options);
    var firstIDs = Object.create(null);
    var scales3 = Object.create(null);
    Object.keys(configScales).forEach(function(id) {
        var scaleConf = configScales[id];
        if (!(0, _helpersSegmentJs.i)(scaleConf)) return console.error("Invalid scale configuration for scale: ".concat(id));
        if (scaleConf._proxy) return console.warn("Ignoring resolver passed as options for scale: ".concat(id));
        var axis = determineAxis(id, scaleConf);
        var defaultId = getDefaultScaleIDFromAxis(axis, chartIndexAxis);
        var defaultScaleOptions = chartDefaults.scales || {};
        firstIDs[axis] = firstIDs[axis] || id;
        scales3[id] = (0, _helpersSegmentJs.aa)(Object.create(null), [
            {
                axis: axis
            },
            scaleConf,
            defaultScaleOptions[axis],
            defaultScaleOptions[defaultId]
        ]);
    });
    config.data.datasets.forEach(function(dataset) {
        var type = dataset.type || config.type;
        var indexAxis = dataset.indexAxis || getIndexAxis(type, options);
        var datasetDefaults = (0, _helpersSegmentJs.a2)[type] || {};
        var defaultScaleOptions = datasetDefaults.scales || {};
        Object.keys(defaultScaleOptions).forEach(function(defaultID) {
            var axis = getAxisFromDefaultScaleID(defaultID, indexAxis);
            var id = dataset[axis + "AxisID"] || firstIDs[axis] || axis;
            scales3[id] = scales3[id] || Object.create(null);
            (0, _helpersSegmentJs.aa)(scales3[id], [
                {
                    axis: axis
                },
                configScales[id],
                defaultScaleOptions[defaultID]
            ]);
        });
    });
    Object.keys(scales3).forEach(function(key) {
        var scale = scales3[key];
        (0, _helpersSegmentJs.aa)(scale, [
            (0, _helpersSegmentJs.d).scales[scale.type],
            (0, _helpersSegmentJs.d).scale
        ]);
    });
    return scales3;
}
function initOptions(config) {
    var options = config.options || (config.options = {});
    options.plugins = (0, _helpersSegmentJs.v)(options.plugins, {});
    options.scales = mergeScaleConfig(config, options);
}
function initData(data) {
    data = data || {};
    data.datasets = data.datasets || [];
    data.labels = data.labels || [];
    return data;
}
function initConfig(config) {
    config = config || {};
    config.data = initData(config.data);
    initOptions(config);
    return config;
}
var keyCache = new Map();
var keysCached = new Set();
function cachedKeys(cacheKey, generate) {
    var keys = keyCache.get(cacheKey);
    if (!keys) {
        keys = generate();
        keyCache.set(cacheKey, keys);
        keysCached.add(keys);
    }
    return keys;
}
var addIfFound = function(set, obj, key) {
    var opts = (0, _helpersSegmentJs.f)(obj, key);
    if (opts !== undefined) set.add(opts);
};
var Config = /*#__PURE__*/ function() {
    "use strict";
    function Config(config) {
        (0, _classCallCheckJsDefault.default)(this, Config);
        this._config = initConfig(config);
        this._scopeCache = new Map();
        this._resolverCache = new Map();
    }
    (0, _createClassJsDefault.default)(Config, [
        {
            key: "platform",
            get: function get() {
                return this._config.platform;
            }
        },
        {
            key: "type",
            get: function get() {
                return this._config.type;
            },
            set: function set(type) {
                this._config.type = type;
            }
        },
        {
            key: "data",
            get: function get() {
                return this._config.data;
            },
            set: function set(data) {
                this._config.data = initData(data);
            }
        },
        {
            key: "options",
            get: function get() {
                return this._config.options;
            },
            set: function set(options) {
                this._config.options = options;
            }
        },
        {
            key: "plugins",
            get: function get() {
                return this._config.plugins;
            }
        },
        {
            key: "update",
            value: function update() {
                var config = this._config;
                this.clearCache();
                initOptions(config);
            }
        },
        {
            key: "clearCache",
            value: function clearCache() {
                this._scopeCache.clear();
                this._resolverCache.clear();
            }
        },
        {
            key: "datasetScopeKeys",
            value: function datasetScopeKeys(datasetType) {
                return cachedKeys(datasetType, function() {
                    return [
                        [
                            "datasets.".concat(datasetType),
                            ""
                        ]
                    ];
                });
            }
        },
        {
            key: "datasetAnimationScopeKeys",
            value: function datasetAnimationScopeKeys(datasetType, transition) {
                return cachedKeys("".concat(datasetType, ".transition.").concat(transition), function() {
                    return [
                        [
                            "datasets.".concat(datasetType, ".transitions.").concat(transition),
                            "transitions.".concat(transition), 
                        ],
                        [
                            "datasets.".concat(datasetType),
                            ""
                        ]
                    ];
                });
            }
        },
        {
            key: "datasetElementScopeKeys",
            value: function datasetElementScopeKeys(datasetType, elementType) {
                return cachedKeys("".concat(datasetType, "-").concat(elementType), function() {
                    return [
                        [
                            "datasets.".concat(datasetType, ".elements.").concat(elementType),
                            "datasets.".concat(datasetType),
                            "elements.".concat(elementType),
                            ""
                        ]
                    ];
                });
            }
        },
        {
            key: "pluginScopeKeys",
            value: function pluginScopeKeys(plugin) {
                var id = plugin.id;
                var type = this.type;
                return cachedKeys("".concat(type, "-plugin-").concat(id), function() {
                    return [
                        [
                            "plugins.".concat(id), 
                        ].concat((0, _toConsumableArrayJsDefault.default)(plugin.additionalOptionScopes || []))
                    ];
                });
            }
        },
        {
            key: "_cachedScopes",
            value: function _cachedScopes(mainScope, resetCache) {
                var _scopeCache = this._scopeCache;
                var cache = _scopeCache.get(mainScope);
                if (!cache || resetCache) {
                    cache = new Map();
                    _scopeCache.set(mainScope, cache);
                }
                return cache;
            }
        },
        {
            key: "getOptionScopes",
            value: function getOptionScopes(mainScope, keyLists, resetCache) {
                var ref = this, options = ref.options, type = ref.type;
                var cache = this._cachedScopes(mainScope, resetCache);
                var cached = cache.get(keyLists);
                if (cached) return cached;
                var scopes = new Set();
                keyLists.forEach(function(keys) {
                    if (mainScope) {
                        scopes.add(mainScope);
                        keys.forEach(function(key) {
                            return addIfFound(scopes, mainScope, key);
                        });
                    }
                    keys.forEach(function(key) {
                        return addIfFound(scopes, options, key);
                    });
                    keys.forEach(function(key) {
                        return addIfFound(scopes, (0, _helpersSegmentJs.a2)[type] || {}, key);
                    });
                    keys.forEach(function(key) {
                        return addIfFound(scopes, (0, _helpersSegmentJs.d), key);
                    });
                    keys.forEach(function(key) {
                        return addIfFound(scopes, (0, _helpersSegmentJs.a5), key);
                    });
                });
                var array = Array.from(scopes);
                if (array.length === 0) array.push(Object.create(null));
                if (keysCached.has(keyLists)) cache.set(keyLists, array);
                return array;
            }
        },
        {
            key: "chartOptionScopes",
            value: function chartOptionScopes() {
                var ref = this, options = ref.options, type = ref.type;
                return [
                    options,
                    (0, _helpersSegmentJs.a2)[type] || {},
                    (0, _helpersSegmentJs.d).datasets[type] || {},
                    {
                        type: type
                    },
                    (0, _helpersSegmentJs.d),
                    (0, _helpersSegmentJs.a5)
                ];
            }
        },
        {
            key: "resolveNamedOptions",
            value: function resolveNamedOptions(scopes, names, context) {
                var prefixes = arguments.length > 3 && arguments[3] !== void 0 ? arguments[3] : [
                    ""
                ];
                var result = {
                    $shared: true
                };
                var ref = getResolver(this._resolverCache, scopes, prefixes), resolver = ref.resolver, subPrefixes = ref.subPrefixes;
                var options = resolver;
                if (needContext(resolver, names)) {
                    result.$shared = false;
                    context = (0, _helpersSegmentJs.a6)(context) ? context() : context;
                    var subResolver = this.createResolver(scopes, context, subPrefixes);
                    options = (0, _helpersSegmentJs.a7)(resolver, context, subResolver);
                }
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                try {
                    for(var _iterator = names[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var prop = _step.value;
                        result[prop] = options[prop];
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
                return result;
            }
        },
        {
            key: "createResolver",
            value: function createResolver(scopes, context) {
                var prefixes = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : [
                    ""
                ], descriptorDefaults = arguments.length > 3 ? arguments[3] : void 0;
                var resolver = getResolver(this._resolverCache, scopes, prefixes).resolver;
                return (0, _helpersSegmentJs.i)(context) ? (0, _helpersSegmentJs.a7)(resolver, context, undefined, descriptorDefaults) : resolver;
            }
        }
    ]);
    return Config;
}();
function getResolver(resolverCache, scopes, prefixes) {
    var cache = resolverCache.get(scopes);
    if (!cache) {
        cache = new Map();
        resolverCache.set(scopes, cache);
    }
    var cacheKey = prefixes.join();
    var cached = cache.get(cacheKey);
    if (!cached) {
        var resolver = (0, _helpersSegmentJs.a8)(scopes, prefixes);
        cached = {
            resolver: resolver,
            subPrefixes: prefixes.filter(function(p) {
                return !p.toLowerCase().includes("hover");
            })
        };
        cache.set(cacheKey, cached);
    }
    return cached;
}
var hasFunction = function(value) {
    return (0, _helpersSegmentJs.i)(value) && Object.getOwnPropertyNames(value).reduce(function(acc, key) {
        return acc || (0, _helpersSegmentJs.a6)(value[key]);
    }, false);
};
function needContext(proxy, names) {
    var ref = (0, _helpersSegmentJs.a9)(proxy), isScriptable = ref.isScriptable, isIndexable = ref.isIndexable;
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = names[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var prop = _step.value;
            var scriptable = isScriptable(prop);
            var indexable = isIndexable(prop);
            var value = (indexable || scriptable) && proxy[prop];
            if (scriptable && ((0, _helpersSegmentJs.a6)(value) || hasFunction(value)) || indexable && (0, _helpersSegmentJs.b)(value)) return true;
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
    return false;
}
var version = "3.8.0";
var KNOWN_POSITIONS = [
    "top",
    "bottom",
    "left",
    "right",
    "chartArea"
];
function positionIsHorizontal(position, axis) {
    return position === "top" || position === "bottom" || KNOWN_POSITIONS.indexOf(position) === -1 && axis === "x";
}
function compare2Level(l1, l2) {
    return function(a, b) {
        return a[l1] === b[l1] ? a[l2] - b[l2] : a[l1] - b[l1];
    };
}
function onAnimationsComplete(context) {
    var chart = context.chart;
    var animationOptions1 = chart.options.animation;
    chart.notifyPlugins("afterRender");
    (0, _helpersSegmentJs.Q)(animationOptions1 && animationOptions1.onComplete, [
        context
    ], chart);
}
function onAnimationProgress(context) {
    var chart = context.chart;
    var animationOptions2 = chart.options.animation;
    (0, _helpersSegmentJs.Q)(animationOptions2 && animationOptions2.onProgress, [
        context
    ], chart);
}
function getCanvas(item) {
    if ((0, _helpersSegmentJs.L)() && typeof item === "string") item = document.getElementById(item);
    else if (item && item.length) item = item[0];
    if (item && item.canvas) item = item.canvas;
    return item;
}
var instances = {};
var getChart = function(key) {
    var canvas = getCanvas(key);
    return Object.values(instances).filter(function(c) {
        return c.canvas === canvas;
    }).pop();
};
function moveNumericKeys(obj, start, move) {
    var keys = Object.keys(obj);
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = keys[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var key = _step.value;
            var intKey = +key;
            if (intKey >= start) {
                var value = obj[key];
                delete obj[key];
                if (move > 0 || intKey > start) obj[intKey + move] = value;
            }
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
}
function determineLastEvent(e, lastEvent, inChartArea, isClick) {
    if (!inChartArea || e.type === "mouseout") return null;
    if (isClick) return lastEvent;
    return e;
}
var Chart = /*#__PURE__*/ function() {
    "use strict";
    function Chart(item, userConfig) {
        var _this = this;
        (0, _classCallCheckJsDefault.default)(this, Chart);
        var config = this.config = new Config(userConfig);
        var initialCanvas = getCanvas(item);
        var existingChart = getChart(initialCanvas);
        if (existingChart) throw new Error("Canvas is already in use. Chart with ID '" + existingChart.id + "'" + " must be destroyed before the canvas can be reused.");
        var options = config.createResolver(config.chartOptionScopes(), this.getContext());
        this.platform = new (config.platform || _detectPlatform(initialCanvas))();
        this.platform.updateConfig(config);
        var context = this.platform.acquireContext(initialCanvas, options.aspectRatio);
        var canvas = context && context.canvas;
        var height = canvas && canvas.height;
        var width = canvas && canvas.width;
        this.id = (0, _helpersSegmentJs.ab)();
        this.ctx = context;
        this.canvas = canvas;
        this.width = width;
        this.height = height;
        this._options = options;
        this._aspectRatio = this.aspectRatio;
        this._layers = [];
        this._metasets = [];
        this._stacks = undefined;
        this.boxes = [];
        this.currentDevicePixelRatio = undefined;
        this.chartArea = undefined;
        this._active = [];
        this._lastEvent = undefined;
        this._listeners = {};
        this._responsiveListeners = undefined;
        this._sortedMetasets = [];
        this.scales = {};
        this._plugins = new PluginService();
        this.$proxies = {};
        this._hiddenIndices = {};
        this.attached = false;
        this._animationsDisabled = undefined;
        this.$context = undefined;
        this._doResize = (0, _helpersSegmentJs.ac)(function(mode) {
            return _this.update(mode);
        }, options.resizeDelay || 0);
        this._dataChanges = [];
        instances[this.id] = this;
        if (!context || !canvas) {
            console.error("Failed to create chart: can't acquire context from the given item");
            return;
        }
        animator.listen(this, "complete", onAnimationsComplete);
        animator.listen(this, "progress", onAnimationProgress);
        this._initialize();
        if (this.attached) this.update();
    }
    (0, _createClassJsDefault.default)(Chart, [
        {
            key: "aspectRatio",
            get: function get() {
                var ref = this, _options = ref.options, aspectRatio = _options.aspectRatio, maintainAspectRatio = _options.maintainAspectRatio, width = ref.width, height = ref.height, _aspectRatio = ref._aspectRatio;
                if (!(0, _helpersSegmentJs.k)(aspectRatio)) return aspectRatio;
                if (maintainAspectRatio && _aspectRatio) return _aspectRatio;
                return height ? width / height : null;
            }
        },
        {
            key: "data",
            get: function get() {
                return this.config.data;
            },
            set: function set(data) {
                this.config.data = data;
            }
        },
        {
            key: "options",
            get: function get() {
                return this._options;
            },
            set: function set(options) {
                this.config.options = options;
            }
        },
        {
            key: "_initialize",
            value: function _initialize() {
                this.notifyPlugins("beforeInit");
                if (this.options.responsive) this.resize();
                else (0, _helpersSegmentJs.ad)(this, this.options.devicePixelRatio);
                this.bindEvents();
                this.notifyPlugins("afterInit");
                return this;
            }
        },
        {
            key: "clear",
            value: function clear() {
                (0, _helpersSegmentJs.ae)(this.canvas, this.ctx);
                return this;
            }
        },
        {
            key: "stop",
            value: function stop() {
                animator.stop(this);
                return this;
            }
        },
        {
            key: "resize",
            value: function resize(width, height) {
                if (!animator.running(this)) this._resize(width, height);
                else this._resizeBeforeDraw = {
                    width: width,
                    height: height
                };
            }
        },
        {
            key: "_resize",
            value: function _resize(width, height) {
                var options = this.options;
                var canvas = this.canvas;
                var aspectRatio = options.maintainAspectRatio && this.aspectRatio;
                var newSize = this.platform.getMaximumSize(canvas, width, height, aspectRatio);
                var newRatio = options.devicePixelRatio || this.platform.getDevicePixelRatio();
                var mode = this.width ? "resize" : "attach";
                this.width = newSize.width;
                this.height = newSize.height;
                this._aspectRatio = this.aspectRatio;
                if (!(0, _helpersSegmentJs.ad)(this, newRatio, true)) return;
                this.notifyPlugins("resize", {
                    size: newSize
                });
                (0, _helpersSegmentJs.Q)(options.onResize, [
                    this,
                    newSize
                ], this);
                if (this.attached) {
                    if (this._doResize(mode)) this.render();
                }
            }
        },
        {
            key: "ensureScalesHaveIDs",
            value: function ensureScalesHaveIDs() {
                var options = this.options;
                var scalesOptions = options.scales || {};
                (0, _helpersSegmentJs.E)(scalesOptions, function(axisOptions, axisID) {
                    axisOptions.id = axisID;
                });
            }
        },
        {
            key: "buildOrUpdateScales",
            value: function buildOrUpdateScales() {
                var _this = this;
                var options = this.options;
                var scaleOpts = options.scales;
                var scales4 = this.scales;
                var updated = Object.keys(scales4).reduce(function(obj, id) {
                    obj[id] = false;
                    return obj;
                }, {});
                var items = [];
                if (scaleOpts) items = items.concat(Object.keys(scaleOpts).map(function(id) {
                    var scaleOptions = scaleOpts[id];
                    var axis = determineAxis(id, scaleOptions);
                    var isRadial = axis === "r";
                    var isHorizontal = axis === "x";
                    return {
                        options: scaleOptions,
                        dposition: isRadial ? "chartArea" : isHorizontal ? "bottom" : "left",
                        dtype: isRadial ? "radialLinear" : isHorizontal ? "category" : "linear"
                    };
                }));
                (0, _helpersSegmentJs.E)(items, function(item) {
                    var scaleOptions = item.options;
                    var id = scaleOptions.id;
                    var axis = determineAxis(id, scaleOptions);
                    var scaleType = (0, _helpersSegmentJs.v)(scaleOptions.type, item.dtype);
                    if (scaleOptions.position === undefined || positionIsHorizontal(scaleOptions.position, axis) !== positionIsHorizontal(item.dposition)) scaleOptions.position = item.dposition;
                    updated[id] = true;
                    var scale = null;
                    if (id in scales4 && scales4[id].type === scaleType) scale = scales4[id];
                    else {
                        var scaleClass = registry.getScale(scaleType);
                        scale = new scaleClass({
                            id: id,
                            type: scaleType,
                            ctx: _this.ctx,
                            chart: _this
                        });
                        scales4[scale.id] = scale;
                    }
                    scale.init(scaleOptions, options);
                });
                (0, _helpersSegmentJs.E)(updated, function(hasUpdated, id) {
                    if (!hasUpdated) delete scales4[id];
                });
                (0, _helpersSegmentJs.E)(scales4, function(scale) {
                    layouts.configure(_this, scale, scale.options);
                    layouts.addBox(_this, scale);
                });
            }
        },
        {
            key: "_updateMetasets",
            value: function _updateMetasets() {
                var metasets = this._metasets;
                var numData = this.data.datasets.length;
                var numMeta = metasets.length;
                metasets.sort(function(a, b) {
                    return a.index - b.index;
                });
                if (numMeta > numData) {
                    for(var i = numData; i < numMeta; ++i)this._destroyDatasetMeta(i);
                    metasets.splice(numData, numMeta - numData);
                }
                this._sortedMetasets = metasets.slice(0).sort(compare2Level("order", "index"));
            }
        },
        {
            key: "_removeUnreferencedMetasets",
            value: function _removeUnreferencedMetasets() {
                var _this = this;
                var ref = this, metasets = ref._metasets, datasets = ref.data.datasets;
                if (metasets.length > datasets.length) delete this._stacks;
                metasets.forEach(function(meta, index50) {
                    if (datasets.filter(function(x) {
                        return x === meta._dataset;
                    }).length === 0) _this._destroyDatasetMeta(index50);
                });
            }
        },
        {
            key: "buildOrUpdateControllers",
            value: function buildOrUpdateControllers() {
                var newControllers = [];
                var datasets = this.data.datasets;
                var i, ilen;
                this._removeUnreferencedMetasets();
                for(i = 0, ilen = datasets.length; i < ilen; i++){
                    var dataset = datasets[i];
                    var meta = this.getDatasetMeta(i);
                    var type = dataset.type || this.config.type;
                    if (meta.type && meta.type !== type) {
                        this._destroyDatasetMeta(i);
                        meta = this.getDatasetMeta(i);
                    }
                    meta.type = type;
                    meta.indexAxis = dataset.indexAxis || getIndexAxis(type, this.options);
                    meta.order = dataset.order || 0;
                    meta.index = i;
                    meta.label = "" + dataset.label;
                    meta.visible = this.isDatasetVisible(i);
                    if (meta.controller) {
                        meta.controller.updateIndex(i);
                        meta.controller.linkScales();
                    } else {
                        var ControllerClass = registry.getController(type);
                        var _type = (0, _helpersSegmentJs.d).datasets[type], datasetElementType = _type.datasetElementType, dataElementType = _type.dataElementType;
                        Object.assign(ControllerClass.prototype, {
                            dataElementType: registry.getElement(dataElementType),
                            datasetElementType: datasetElementType && registry.getElement(datasetElementType)
                        });
                        meta.controller = new ControllerClass(this, i);
                        newControllers.push(meta.controller);
                    }
                }
                this._updateMetasets();
                return newControllers;
            }
        },
        {
            key: "_resetElements",
            value: function _resetElements() {
                var _this = this;
                (0, _helpersSegmentJs.E)(this.data.datasets, function(dataset, datasetIndex) {
                    _this.getDatasetMeta(datasetIndex).controller.reset();
                }, this);
            }
        },
        {
            key: "reset",
            value: function reset() {
                this._resetElements();
                this.notifyPlugins("reset");
            }
        },
        {
            key: "update",
            value: function update(mode) {
                var config = this.config;
                config.update();
                var options = this._options = config.createResolver(config.chartOptionScopes(), this.getContext());
                var animsDisabled = this._animationsDisabled = !options.animation;
                this._updateScales();
                this._checkEventBindings();
                this._updateHiddenIndices();
                this._plugins.invalidate();
                if (this.notifyPlugins("beforeUpdate", {
                    mode: mode,
                    cancelable: true
                }) === false) return;
                var newControllers = this.buildOrUpdateControllers();
                this.notifyPlugins("beforeElementsUpdate");
                var minPadding = 0;
                for(var i = 0, ilen = this.data.datasets.length; i < ilen; i++){
                    var controller = this.getDatasetMeta(i).controller;
                    var reset = !animsDisabled && newControllers.indexOf(controller) === -1;
                    controller.buildOrUpdateElements(reset);
                    minPadding = Math.max(+controller.getMaxOverflow(), minPadding);
                }
                minPadding = this._minPadding = options.layout.autoPadding ? minPadding : 0;
                this._updateLayout(minPadding);
                if (!animsDisabled) (0, _helpersSegmentJs.E)(newControllers, function(controller) {
                    controller.reset();
                });
                this._updateDatasets(mode);
                this.notifyPlugins("afterUpdate", {
                    mode: mode
                });
                this._layers.sort(compare2Level("z", "_idx"));
                var ref = this, _active = ref._active, _lastEvent = ref._lastEvent;
                if (_lastEvent) this._eventHandler(_lastEvent, true);
                else if (_active.length) this._updateHoverStyles(_active, _active, true);
                this.render();
            }
        },
        {
            key: "_updateScales",
            value: function _updateScales() {
                var _this = this;
                (0, _helpersSegmentJs.E)(this.scales, function(scale) {
                    layouts.removeBox(_this, scale);
                });
                this.ensureScalesHaveIDs();
                this.buildOrUpdateScales();
            }
        },
        {
            key: "_checkEventBindings",
            value: function _checkEventBindings() {
                var options = this.options;
                var existingEvents = new Set(Object.keys(this._listeners));
                var newEvents = new Set(options.events);
                if (!(0, _helpersSegmentJs.af)(existingEvents, newEvents) || !!this._responsiveListeners !== options.responsive) {
                    this.unbindEvents();
                    this.bindEvents();
                }
            }
        },
        {
            key: "_updateHiddenIndices",
            value: function _updateHiddenIndices() {
                var _hiddenIndices = this._hiddenIndices;
                var changes = this._getUniformDataChanges() || [];
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                try {
                    for(var _iterator = changes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var _value = _step.value, method = _value.method, start = _value.start, count = _value.count;
                        var move = method === "_removeElements" ? -count : count;
                        moveNumericKeys(_hiddenIndices, start, move);
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
            }
        },
        {
            key: "_getUniformDataChanges",
            value: function _getUniformDataChanges() {
                var _dataChanges = this._dataChanges;
                if (!_dataChanges || !_dataChanges.length) return;
                this._dataChanges = [];
                var datasetCount = this.data.datasets.length;
                var makeSet = function(idx) {
                    return new Set(_dataChanges.filter(function(c) {
                        return c[0] === idx;
                    }).map(function(c, i) {
                        return i + "," + c.splice(1).join(",");
                    }));
                };
                var changeSet = makeSet(0);
                for(var i4 = 1; i4 < datasetCount; i4++){
                    if (!(0, _helpersSegmentJs.af)(changeSet, makeSet(i4))) return;
                }
                return Array.from(changeSet).map(function(c) {
                    return c.split(",");
                }).map(function(a) {
                    return {
                        method: a[1],
                        start: +a[2],
                        count: +a[3]
                    };
                });
            }
        },
        {
            key: "_updateLayout",
            value: function _updateLayout(minPadding) {
                var _this = this;
                if (this.notifyPlugins("beforeLayout", {
                    cancelable: true
                }) === false) return;
                layouts.update(this, this.width, this.height, minPadding);
                var area = this.chartArea;
                var noArea = area.width <= 0 || area.height <= 0;
                this._layers = [];
                (0, _helpersSegmentJs.E)(this.boxes, function(box) {
                    var __layers;
                    if (noArea && box.position === "chartArea") return;
                    if (box.configure) box.configure();
                    (__layers = _this._layers).push.apply(__layers, (0, _toConsumableArrayJsDefault.default)(box._layers()));
                }, this);
                this._layers.forEach(function(item, index51) {
                    item._idx = index51;
                });
                this.notifyPlugins("afterLayout");
            }
        },
        {
            key: "_updateDatasets",
            value: function _updateDatasets(mode) {
                if (this.notifyPlugins("beforeDatasetsUpdate", {
                    mode: mode,
                    cancelable: true
                }) === false) return;
                for(var i = 0, ilen = this.data.datasets.length; i < ilen; ++i)this.getDatasetMeta(i).controller.configure();
                for(var i5 = 0, ilen1 = this.data.datasets.length; i5 < ilen1; ++i5)this._updateDataset(i5, (0, _helpersSegmentJs.a6)(mode) ? mode({
                    datasetIndex: i5
                }) : mode);
                this.notifyPlugins("afterDatasetsUpdate", {
                    mode: mode
                });
            }
        },
        {
            key: "_updateDataset",
            value: function _updateDataset(index52, mode) {
                var meta = this.getDatasetMeta(index52);
                var args = {
                    meta: meta,
                    index: index52,
                    mode: mode,
                    cancelable: true
                };
                if (this.notifyPlugins("beforeDatasetUpdate", args) === false) return;
                meta.controller._update(mode);
                args.cancelable = false;
                this.notifyPlugins("afterDatasetUpdate", args);
            }
        },
        {
            key: "render",
            value: function render() {
                if (this.notifyPlugins("beforeRender", {
                    cancelable: true
                }) === false) return;
                if (animator.has(this)) {
                    if (this.attached && !animator.running(this)) animator.start(this);
                } else {
                    this.draw();
                    onAnimationsComplete({
                        chart: this
                    });
                }
            }
        },
        {
            key: "draw",
            value: function draw2() {
                var i;
                if (this._resizeBeforeDraw) {
                    var __resizeBeforeDraw = this._resizeBeforeDraw, width = __resizeBeforeDraw.width, height = __resizeBeforeDraw.height;
                    this._resize(width, height);
                    this._resizeBeforeDraw = null;
                }
                this.clear();
                if (this.width <= 0 || this.height <= 0) return;
                if (this.notifyPlugins("beforeDraw", {
                    cancelable: true
                }) === false) return;
                var layers = this._layers;
                for(i = 0; i < layers.length && layers[i].z <= 0; ++i)layers[i].draw(this.chartArea);
                this._drawDatasets();
                for(; i < layers.length; ++i)layers[i].draw(this.chartArea);
                this.notifyPlugins("afterDraw");
            }
        },
        {
            key: "_getSortedDatasetMetas",
            value: function _getSortedDatasetMetas(filterVisible) {
                var metasets = this._sortedMetasets;
                var result = [];
                var i, ilen;
                for(i = 0, ilen = metasets.length; i < ilen; ++i){
                    var meta = metasets[i];
                    if (!filterVisible || meta.visible) result.push(meta);
                }
                return result;
            }
        },
        {
            key: "getSortedVisibleDatasetMetas",
            value: function getSortedVisibleDatasetMetas() {
                return this._getSortedDatasetMetas(true);
            }
        },
        {
            key: "_drawDatasets",
            value: function _drawDatasets() {
                if (this.notifyPlugins("beforeDatasetsDraw", {
                    cancelable: true
                }) === false) return;
                var metasets = this.getSortedVisibleDatasetMetas();
                for(var i = metasets.length - 1; i >= 0; --i)this._drawDataset(metasets[i]);
                this.notifyPlugins("afterDatasetsDraw");
            }
        },
        {
            key: "_drawDataset",
            value: function _drawDataset(meta) {
                var ctx = this.ctx;
                var clip = meta._clip;
                var useClip = !clip.disabled;
                var area = this.chartArea;
                var args = {
                    meta: meta,
                    index: meta.index,
                    cancelable: true
                };
                if (this.notifyPlugins("beforeDatasetDraw", args) === false) return;
                if (useClip) (0, _helpersSegmentJs.X)(ctx, {
                    left: clip.left === false ? 0 : area.left - clip.left,
                    right: clip.right === false ? this.width : area.right + clip.right,
                    top: clip.top === false ? 0 : area.top - clip.top,
                    bottom: clip.bottom === false ? this.height : area.bottom + clip.bottom
                });
                meta.controller.draw();
                if (useClip) (0, _helpersSegmentJs.Z)(ctx);
                args.cancelable = false;
                this.notifyPlugins("afterDatasetDraw", args);
            }
        },
        {
            key: "isPointInArea",
            value: function isPointInArea(point) {
                return (0, _helpersSegmentJs.B)(point, this.chartArea, this._minPadding);
            }
        },
        {
            key: "getElementsAtEventForMode",
            value: function getElementsAtEventForMode(e, mode, options, useFinalPosition) {
                var method = Interaction.modes[mode];
                if (typeof method === "function") return method(this, e, options, useFinalPosition);
                return [];
            }
        },
        {
            key: "getDatasetMeta",
            value: function getDatasetMeta(datasetIndex) {
                var dataset = this.data.datasets[datasetIndex];
                var metasets = this._metasets;
                var meta = metasets.filter(function(x) {
                    return x && x._dataset === dataset;
                }).pop();
                if (!meta) {
                    meta = {
                        type: null,
                        data: [],
                        dataset: null,
                        controller: null,
                        hidden: null,
                        xAxisID: null,
                        yAxisID: null,
                        order: dataset && dataset.order || 0,
                        index: datasetIndex,
                        _dataset: dataset,
                        _parsed: [],
                        _sorted: false
                    };
                    metasets.push(meta);
                }
                return meta;
            }
        },
        {
            key: "getContext",
            value: function getContext() {
                return this.$context || (this.$context = (0, _helpersSegmentJs.h)(null, {
                    chart: this,
                    type: "chart"
                }));
            }
        },
        {
            key: "getVisibleDatasetCount",
            value: function getVisibleDatasetCount() {
                return this.getSortedVisibleDatasetMetas().length;
            }
        },
        {
            key: "isDatasetVisible",
            value: function isDatasetVisible(datasetIndex) {
                var dataset = this.data.datasets[datasetIndex];
                if (!dataset) return false;
                var meta = this.getDatasetMeta(datasetIndex);
                return typeof meta.hidden === "boolean" ? !meta.hidden : !dataset.hidden;
            }
        },
        {
            key: "setDatasetVisibility",
            value: function setDatasetVisibility(datasetIndex, visible) {
                var meta = this.getDatasetMeta(datasetIndex);
                meta.hidden = !visible;
            }
        },
        {
            key: "toggleDataVisibility",
            value: function toggleDataVisibility(index53) {
                this._hiddenIndices[index53] = !this._hiddenIndices[index53];
            }
        },
        {
            key: "getDataVisibility",
            value: function getDataVisibility(index54) {
                return !this._hiddenIndices[index54];
            }
        },
        {
            key: "_updateVisibility",
            value: function _updateVisibility(datasetIndex, dataIndex, visible) {
                var mode = visible ? "show" : "hide";
                var meta = this.getDatasetMeta(datasetIndex);
                var anims = meta.controller._resolveAnimations(undefined, mode);
                if ((0, _helpersSegmentJs.j)(dataIndex)) {
                    meta.data[dataIndex].hidden = !visible;
                    this.update();
                } else {
                    this.setDatasetVisibility(datasetIndex, visible);
                    anims.update(meta, {
                        visible: visible
                    });
                    this.update(function(ctx) {
                        return ctx.datasetIndex === datasetIndex ? mode : undefined;
                    });
                }
            }
        },
        {
            key: "hide",
            value: function hide(datasetIndex, dataIndex) {
                this._updateVisibility(datasetIndex, dataIndex, false);
            }
        },
        {
            key: "show",
            value: function show(datasetIndex, dataIndex) {
                this._updateVisibility(datasetIndex, dataIndex, true);
            }
        },
        {
            key: "_destroyDatasetMeta",
            value: function _destroyDatasetMeta(datasetIndex) {
                var meta = this._metasets[datasetIndex];
                if (meta && meta.controller) meta.controller._destroy();
                delete this._metasets[datasetIndex];
            }
        },
        {
            key: "_stop",
            value: function _stop() {
                var i, ilen;
                this.stop();
                animator.remove(this);
                for(i = 0, ilen = this.data.datasets.length; i < ilen; ++i)this._destroyDatasetMeta(i);
            }
        },
        {
            key: "destroy",
            value: function destroy() {
                this.notifyPlugins("beforeDestroy");
                var ref = this, canvas = ref.canvas, ctx = ref.ctx;
                this._stop();
                this.config.clearCache();
                if (canvas) {
                    this.unbindEvents();
                    (0, _helpersSegmentJs.ae)(canvas, ctx);
                    this.platform.releaseContext(ctx);
                    this.canvas = null;
                    this.ctx = null;
                }
                this.notifyPlugins("destroy");
                delete instances[this.id];
                this.notifyPlugins("afterDestroy");
            }
        },
        {
            key: "toBase64Image",
            value: function toBase64Image() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                var _canvas;
                return (_canvas = this.canvas).toDataURL.apply(_canvas, (0, _toConsumableArrayJsDefault.default)(args));
            }
        },
        {
            key: "bindEvents",
            value: function bindEvents() {
                this.bindUserEvents();
                if (this.options.responsive) this.bindResponsiveEvents();
                else this.attached = true;
            }
        },
        {
            key: "bindUserEvents",
            value: function bindUserEvents() {
                var _this = this;
                var listeners = this._listeners;
                var platform = this.platform;
                var _add = function(type, listener) {
                    platform.addEventListener(_this, type, listener);
                    listeners[type] = listener;
                };
                var listener1 = function(e, x, y) {
                    e.offsetX = x;
                    e.offsetY = y;
                    _this._eventHandler(e);
                };
                (0, _helpersSegmentJs.E)(this.options.events, function(type) {
                    return _add(type, listener1);
                });
            }
        },
        {
            key: "bindResponsiveEvents",
            value: function bindResponsiveEvents() {
                var _this = this;
                if (!this._responsiveListeners) this._responsiveListeners = {};
                var listeners = this._responsiveListeners;
                var platform = this.platform;
                var _add = function(type, listener) {
                    platform.addEventListener(_this, type, listener);
                    listeners[type] = listener;
                };
                var _remove = function(type, listener) {
                    if (listeners[type]) {
                        platform.removeEventListener(_this, type, listener);
                        delete listeners[type];
                    }
                };
                var listener2 = function(width, height) {
                    if (_this.canvas) _this.resize(width, height);
                };
                var detached;
                var attached = function() {
                    _remove("attach", attached);
                    _this.attached = true;
                    _this.resize();
                    _add("resize", listener2);
                    _add("detach", detached);
                };
                detached = function() {
                    _this.attached = false;
                    _remove("resize", listener2);
                    _this._stop();
                    _this._resize(0, 0);
                    _add("attach", attached);
                };
                if (platform.isAttached(this.canvas)) attached();
                else detached();
            }
        },
        {
            key: "unbindEvents",
            value: function unbindEvents() {
                var _this = this;
                (0, _helpersSegmentJs.E)(this._listeners, function(listener, type) {
                    _this.platform.removeEventListener(_this, type, listener);
                });
                this._listeners = {};
                (0, _helpersSegmentJs.E)(this._responsiveListeners, function(listener, type) {
                    _this.platform.removeEventListener(_this, type, listener);
                });
                this._responsiveListeners = undefined;
            }
        },
        {
            key: "updateHoverStyle",
            value: function updateHoverStyle(items, mode, enabled) {
                var prefix = enabled ? "set" : "remove";
                var meta, item, i, ilen;
                if (mode === "dataset") {
                    meta = this.getDatasetMeta(items[0].datasetIndex);
                    meta.controller["_" + prefix + "DatasetHoverStyle"]();
                }
                for(i = 0, ilen = items.length; i < ilen; ++i){
                    item = items[i];
                    var controller = item && this.getDatasetMeta(item.datasetIndex).controller;
                    if (controller) controller[prefix + "HoverStyle"](item.element, item.datasetIndex, item.index);
                }
            }
        },
        {
            key: "getActiveElements",
            value: function getActiveElements() {
                return this._active || [];
            }
        },
        {
            key: "setActiveElements",
            value: function setActiveElements(activeElements) {
                var _this = this;
                var lastActive = this._active || [];
                var active = activeElements.map(function(param) {
                    var datasetIndex = param.datasetIndex, index55 = param.index;
                    var meta = _this.getDatasetMeta(datasetIndex);
                    if (!meta) throw new Error("No dataset found at index " + datasetIndex);
                    return {
                        datasetIndex: datasetIndex,
                        element: meta.data[index55],
                        index: index55
                    };
                });
                var changed = !(0, _helpersSegmentJs.ag)(active, lastActive);
                if (changed) {
                    this._active = active;
                    this._lastEvent = null;
                    this._updateHoverStyles(active, lastActive);
                }
            }
        },
        {
            key: "notifyPlugins",
            value: function notifyPlugins(hook, args, filter) {
                return this._plugins.notify(this, hook, args, filter);
            }
        },
        {
            key: "_updateHoverStyles",
            value: function _updateHoverStyles(active, lastActive, replay) {
                var hoverOptions = this.options.hover;
                var diff = function(a, b) {
                    return a.filter(function(x) {
                        return !b.some(function(y) {
                            return x.datasetIndex === y.datasetIndex && x.index === y.index;
                        });
                    });
                };
                var deactivated = diff(lastActive, active);
                var activated = replay ? active : diff(active, lastActive);
                if (deactivated.length) this.updateHoverStyle(deactivated, hoverOptions.mode, false);
                if (activated.length && hoverOptions.mode) this.updateHoverStyle(activated, hoverOptions.mode, true);
            }
        },
        {
            key: "_eventHandler",
            value: function _eventHandler(e, replay) {
                var _this = this;
                var args = {
                    event: e,
                    replay: replay,
                    cancelable: true,
                    inChartArea: this.isPointInArea(e)
                };
                var eventFilter = function(plugin) {
                    return (plugin.options.events || _this.options.events).includes(e.native.type);
                };
                if (this.notifyPlugins("beforeEvent", args, eventFilter) === false) return;
                var changed = this._handleEvent(e, replay, args.inChartArea);
                args.cancelable = false;
                this.notifyPlugins("afterEvent", args, eventFilter);
                if (changed || args.changed) this.render();
                return this;
            }
        },
        {
            key: "_handleEvent",
            value: function _handleEvent(e, replay, inChartArea) {
                var ref = this, tmp = ref._active, lastActive = tmp === void 0 ? [] : tmp, options = ref.options;
                var useFinalPosition = replay;
                var active = this._getActiveElements(e, lastActive, inChartArea, useFinalPosition);
                var isClick = (0, _helpersSegmentJs.ah)(e);
                var lastEvent = determineLastEvent(e, this._lastEvent, inChartArea, isClick);
                if (inChartArea) {
                    this._lastEvent = null;
                    (0, _helpersSegmentJs.Q)(options.onHover, [
                        e,
                        active,
                        this
                    ], this);
                    if (isClick) (0, _helpersSegmentJs.Q)(options.onClick, [
                        e,
                        active,
                        this
                    ], this);
                }
                var changed = !(0, _helpersSegmentJs.ag)(active, lastActive);
                if (changed || replay) {
                    this._active = active;
                    this._updateHoverStyles(active, lastActive, replay);
                }
                this._lastEvent = lastEvent;
                return changed;
            }
        },
        {
            key: "_getActiveElements",
            value: function _getActiveElements(e, lastActive, inChartArea, useFinalPosition) {
                if (e.type === "mouseout") return [];
                if (!inChartArea) return lastActive;
                var hoverOptions = this.options.hover;
                return this.getElementsAtEventForMode(e, hoverOptions.mode, hoverOptions, useFinalPosition);
            }
        }
    ]);
    return Chart;
}();
var invalidatePlugins = function() {
    return (0, _helpersSegmentJs.E)(Chart.instances, function(chart) {
        return chart._plugins.invalidate();
    });
};
var enumerable = true;
Object.defineProperties(Chart, {
    defaults: {
        enumerable: enumerable,
        value: (0, _helpersSegmentJs.d)
    },
    instances: {
        enumerable: enumerable,
        value: instances
    },
    overrides: {
        enumerable: enumerable,
        value: (0, _helpersSegmentJs.a2)
    },
    registry: {
        enumerable: enumerable,
        value: registry
    },
    version: {
        enumerable: enumerable,
        value: version
    },
    getChart: {
        enumerable: enumerable,
        value: getChart
    },
    register: {
        enumerable: enumerable,
        value: function() {
            for(var _len = arguments.length, items = new Array(_len), _key = 0; _key < _len; _key++){
                items[_key] = arguments[_key];
            }
            var _registry;
            (_registry = registry).add.apply(_registry, (0, _toConsumableArrayJsDefault.default)(items));
            invalidatePlugins();
        }
    },
    unregister: {
        enumerable: enumerable,
        value: function() {
            for(var _len = arguments.length, items = new Array(_len), _key = 0; _key < _len; _key++){
                items[_key] = arguments[_key];
            }
            var _registry;
            (_registry = registry).remove.apply(_registry, (0, _toConsumableArrayJsDefault.default)(items));
            invalidatePlugins();
        }
    }
});
function clipArc(ctx, element, endAngle) {
    var startAngle = element.startAngle, pixelMargin = element.pixelMargin, x = element.x, y = element.y, outerRadius = element.outerRadius, innerRadius = element.innerRadius;
    var angleMargin = pixelMargin / outerRadius;
    ctx.beginPath();
    ctx.arc(x, y, outerRadius, startAngle - angleMargin, endAngle + angleMargin);
    if (innerRadius > pixelMargin) {
        angleMargin = pixelMargin / innerRadius;
        ctx.arc(x, y, innerRadius, endAngle + angleMargin, startAngle - angleMargin, true);
    } else ctx.arc(x, y, pixelMargin, endAngle + (0, _helpersSegmentJs.H), startAngle - (0, _helpersSegmentJs.H));
    ctx.closePath();
    ctx.clip();
}
function toRadiusCorners(value) {
    return (0, _helpersSegmentJs.aj)(value, [
        "outerStart",
        "outerEnd",
        "innerStart",
        "innerEnd"
    ]);
}
function parseBorderRadius$1(arc, innerRadius, outerRadius, angleDelta) {
    var o = toRadiusCorners(arc.options.borderRadius);
    var halfThickness = (outerRadius - innerRadius) / 2;
    var innerLimit = Math.min(halfThickness, angleDelta * innerRadius / 2);
    var computeOuterLimit = function(val) {
        var outerArcLimit = (outerRadius - Math.min(halfThickness, val)) * angleDelta / 2;
        return (0, _helpersSegmentJs.w)(val, 0, Math.min(halfThickness, outerArcLimit));
    };
    return {
        outerStart: computeOuterLimit(o.outerStart),
        outerEnd: computeOuterLimit(o.outerEnd),
        innerStart: (0, _helpersSegmentJs.w)(o.innerStart, 0, innerLimit),
        innerEnd: (0, _helpersSegmentJs.w)(o.innerEnd, 0, innerLimit)
    };
}
function rThetaToXY(r, theta, x, y) {
    return {
        x: x + r * Math.cos(theta),
        y: y + r * Math.sin(theta)
    };
}
function pathArc(ctx, element, offset, spacing, end) {
    var x = element.x, y = element.y, start = element.startAngle, pixelMargin = element.pixelMargin, innerR = element.innerRadius;
    var outerRadius = Math.max(element.outerRadius + spacing + offset - pixelMargin, 0);
    var innerRadius = innerR > 0 ? innerR + spacing + offset + pixelMargin : 0;
    var spacingOffset = 0;
    var alpha = end - start;
    if (spacing) {
        var noSpacingInnerRadius = innerR > 0 ? innerR - spacing : 0;
        var noSpacingOuterRadius = outerRadius > 0 ? outerRadius - spacing : 0;
        var avNogSpacingRadius = (noSpacingInnerRadius + noSpacingOuterRadius) / 2;
        var adjustedAngle = avNogSpacingRadius !== 0 ? alpha * avNogSpacingRadius / (avNogSpacingRadius + spacing) : alpha;
        spacingOffset = (alpha - adjustedAngle) / 2;
    }
    var beta = Math.max(0.001, alpha * outerRadius - offset / (0, _helpersSegmentJs.P)) / outerRadius;
    var angleOffset = (alpha - beta) / 2;
    var startAngle = start + angleOffset + spacingOffset;
    var endAngle = end - angleOffset - spacingOffset;
    var ref = parseBorderRadius$1(element, innerRadius, outerRadius, endAngle - startAngle), outerStart = ref.outerStart, outerEnd = ref.outerEnd, innerStart = ref.innerStart, innerEnd = ref.innerEnd;
    var outerStartAdjustedRadius = outerRadius - outerStart;
    var outerEndAdjustedRadius = outerRadius - outerEnd;
    var outerStartAdjustedAngle = startAngle + outerStart / outerStartAdjustedRadius;
    var outerEndAdjustedAngle = endAngle - outerEnd / outerEndAdjustedRadius;
    var innerStartAdjustedRadius = innerRadius + innerStart;
    var innerEndAdjustedRadius = innerRadius + innerEnd;
    var innerStartAdjustedAngle = startAngle + innerStart / innerStartAdjustedRadius;
    var innerEndAdjustedAngle = endAngle - innerEnd / innerEndAdjustedRadius;
    ctx.beginPath();
    ctx.arc(x, y, outerRadius, outerStartAdjustedAngle, outerEndAdjustedAngle);
    if (outerEnd > 0) {
        var pCenter = rThetaToXY(outerEndAdjustedRadius, outerEndAdjustedAngle, x, y);
        ctx.arc(pCenter.x, pCenter.y, outerEnd, outerEndAdjustedAngle, endAngle + (0, _helpersSegmentJs.H));
    }
    var p4 = rThetaToXY(innerEndAdjustedRadius, endAngle, x, y);
    ctx.lineTo(p4.x, p4.y);
    if (innerEnd > 0) {
        var pCenter1 = rThetaToXY(innerEndAdjustedRadius, innerEndAdjustedAngle, x, y);
        ctx.arc(pCenter1.x, pCenter1.y, innerEnd, endAngle + (0, _helpersSegmentJs.H), innerEndAdjustedAngle + Math.PI);
    }
    ctx.arc(x, y, innerRadius, endAngle - innerEnd / innerRadius, startAngle + innerStart / innerRadius, true);
    if (innerStart > 0) {
        var pCenter2 = rThetaToXY(innerStartAdjustedRadius, innerStartAdjustedAngle, x, y);
        ctx.arc(pCenter2.x, pCenter2.y, innerStart, innerStartAdjustedAngle + Math.PI, startAngle - (0, _helpersSegmentJs.H));
    }
    var p8 = rThetaToXY(outerStartAdjustedRadius, startAngle, x, y);
    ctx.lineTo(p8.x, p8.y);
    if (outerStart > 0) {
        var pCenter3 = rThetaToXY(outerStartAdjustedRadius, outerStartAdjustedAngle, x, y);
        ctx.arc(pCenter3.x, pCenter3.y, outerStart, startAngle - (0, _helpersSegmentJs.H), outerStartAdjustedAngle);
    }
    ctx.closePath();
}
function drawArc(ctx, element, offset, spacing) {
    var fullCircles = element.fullCircles, startAngle = element.startAngle, circumference = element.circumference;
    var endAngle = element.endAngle;
    if (fullCircles) {
        pathArc(ctx, element, offset, spacing, startAngle + (0, _helpersSegmentJs.T));
        for(var i = 0; i < fullCircles; ++i)ctx.fill();
        if (!isNaN(circumference)) {
            endAngle = startAngle + circumference % (0, _helpersSegmentJs.T);
            if (circumference % (0, _helpersSegmentJs.T) === 0) endAngle += (0, _helpersSegmentJs.T);
        }
    }
    pathArc(ctx, element, offset, spacing, endAngle);
    ctx.fill();
    return endAngle;
}
function drawFullCircleBorders(ctx, element, inner) {
    var x = element.x, y = element.y, startAngle = element.startAngle, pixelMargin = element.pixelMargin, fullCircles = element.fullCircles;
    var outerRadius = Math.max(element.outerRadius - pixelMargin, 0);
    var innerRadius = element.innerRadius + pixelMargin;
    var i;
    if (inner) clipArc(ctx, element, startAngle + (0, _helpersSegmentJs.T));
    ctx.beginPath();
    ctx.arc(x, y, innerRadius, startAngle + (0, _helpersSegmentJs.T), startAngle, true);
    for(i = 0; i < fullCircles; ++i)ctx.stroke();
    ctx.beginPath();
    ctx.arc(x, y, outerRadius, startAngle, startAngle + (0, _helpersSegmentJs.T));
    for(i = 0; i < fullCircles; ++i)ctx.stroke();
}
function drawBorder(ctx, element, offset, spacing, endAngle) {
    var options = element.options;
    var borderWidth = options.borderWidth, borderJoinStyle = options.borderJoinStyle;
    var inner = options.borderAlign === "inner";
    if (!borderWidth) return;
    if (inner) {
        ctx.lineWidth = borderWidth * 2;
        ctx.lineJoin = borderJoinStyle || "round";
    } else {
        ctx.lineWidth = borderWidth;
        ctx.lineJoin = borderJoinStyle || "bevel";
    }
    if (element.fullCircles) drawFullCircleBorders(ctx, element, inner);
    if (inner) clipArc(ctx, element, endAngle);
    pathArc(ctx, element, offset, spacing, endAngle);
    ctx.stroke();
}
var ArcElement = /*#__PURE__*/ function(Element) {
    "use strict";
    (0, _inheritsJsDefault.default)(ArcElement, Element);
    var _super = (0, _createSuperJsDefault.default)(ArcElement);
    function ArcElement(cfg) {
        (0, _classCallCheckJsDefault.default)(this, ArcElement);
        var _this;
        _this = _super.call(this);
        _this.options = undefined;
        _this.circumference = undefined;
        _this.startAngle = undefined;
        _this.endAngle = undefined;
        _this.innerRadius = undefined;
        _this.outerRadius = undefined;
        _this.pixelMargin = 0;
        _this.fullCircles = 0;
        if (cfg) Object.assign((0, _assertThisInitializedJsDefault.default)(_this), cfg);
        return _this;
    }
    (0, _createClassJsDefault.default)(ArcElement, [
        {
            key: "inRange",
            value: function inRange2(chartX, chartY, useFinalPosition) {
                var point = this.getProps([
                    "x",
                    "y"
                ], useFinalPosition);
                var ref = (0, _helpersSegmentJs.C)(point, {
                    x: chartX,
                    y: chartY
                }), angle = ref.angle, distance = ref.distance;
                var ref6 = this.getProps([
                    "startAngle",
                    "endAngle",
                    "innerRadius",
                    "outerRadius",
                    "circumference"
                ], useFinalPosition), startAngle = ref6.startAngle, endAngle = ref6.endAngle, innerRadius = ref6.innerRadius, outerRadius = ref6.outerRadius, circumference = ref6.circumference;
                var rAdjust = this.options.spacing / 2;
                var _circumference = (0, _helpersSegmentJs.v)(circumference, endAngle - startAngle);
                var betweenAngles = _circumference >= (0, _helpersSegmentJs.T) || (0, _helpersSegmentJs.p)(angle, startAngle, endAngle);
                var withinRadius = (0, _helpersSegmentJs.ai)(distance, innerRadius + rAdjust, outerRadius + rAdjust);
                return betweenAngles && withinRadius;
            }
        },
        {
            key: "getCenterPoint",
            value: function getCenterPoint(useFinalPosition) {
                var ref = this.getProps([
                    "x",
                    "y",
                    "startAngle",
                    "endAngle",
                    "innerRadius",
                    "outerRadius",
                    "circumference", 
                ], useFinalPosition), x = ref.x, y = ref.y, startAngle = ref.startAngle, endAngle = ref.endAngle, innerRadius = ref.innerRadius, outerRadius = ref.outerRadius;
                var _options = this.options, offset = _options.offset, spacing = _options.spacing;
                var halfAngle = (startAngle + endAngle) / 2;
                var halfRadius = (innerRadius + outerRadius + spacing + offset) / 2;
                return {
                    x: x + Math.cos(halfAngle) * halfRadius,
                    y: y + Math.sin(halfAngle) * halfRadius
                };
            }
        },
        {
            key: "tooltipPosition",
            value: function tooltipPosition(useFinalPosition) {
                return this.getCenterPoint(useFinalPosition);
            }
        },
        {
            key: "draw",
            value: function draw2(ctx) {
                var ref = this, options = ref.options, circumference = ref.circumference;
                var offset = (options.offset || 0) / 2;
                var spacing = (options.spacing || 0) / 2;
                this.pixelMargin = options.borderAlign === "inner" ? 0.33 : 0;
                this.fullCircles = circumference > (0, _helpersSegmentJs.T) ? Math.floor(circumference / (0, _helpersSegmentJs.T)) : 0;
                if (circumference === 0 || this.innerRadius < 0 || this.outerRadius < 0) return;
                ctx.save();
                var radiusOffset = 0;
                if (offset) {
                    radiusOffset = offset / 2;
                    var halfAngle = (this.startAngle + this.endAngle) / 2;
                    ctx.translate(Math.cos(halfAngle) * radiusOffset, Math.sin(halfAngle) * radiusOffset);
                    if (this.circumference >= (0, _helpersSegmentJs.P)) radiusOffset = offset;
                }
                ctx.fillStyle = options.backgroundColor;
                ctx.strokeStyle = options.borderColor;
                var endAngle = drawArc(ctx, this, radiusOffset, spacing);
                drawBorder(ctx, this, radiusOffset, spacing, endAngle);
                ctx.restore();
            }
        }
    ]);
    return ArcElement;
}((0, _wrapNativeSuperJsDefault.default)(Element));
ArcElement.id = "arc";
ArcElement.defaults = {
    borderAlign: "center",
    borderColor: "#fff",
    borderJoinStyle: undefined,
    borderRadius: 0,
    borderWidth: 2,
    offset: 0,
    spacing: 0,
    angle: undefined
};
ArcElement.defaultRoutes = {
    backgroundColor: "backgroundColor"
};
function setStyle(ctx, options) {
    var style = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : options;
    ctx.lineCap = (0, _helpersSegmentJs.v)(style.borderCapStyle, options.borderCapStyle);
    ctx.setLineDash((0, _helpersSegmentJs.v)(style.borderDash, options.borderDash));
    ctx.lineDashOffset = (0, _helpersSegmentJs.v)(style.borderDashOffset, options.borderDashOffset);
    ctx.lineJoin = (0, _helpersSegmentJs.v)(style.borderJoinStyle, options.borderJoinStyle);
    ctx.lineWidth = (0, _helpersSegmentJs.v)(style.borderWidth, options.borderWidth);
    ctx.strokeStyle = (0, _helpersSegmentJs.v)(style.borderColor, options.borderColor);
}
function lineTo(ctx, previous, target) {
    ctx.lineTo(target.x, target.y);
}
function getLineMethod(options) {
    if (options.stepped) return 0, _helpersSegmentJs.aq;
    if (options.tension || options.cubicInterpolationMode === "monotone") return 0, _helpersSegmentJs.ar;
    return lineTo;
}
function pathVars(points, segment) {
    var params = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {};
    var count = points.length;
    var tmp = params.start, paramsStart = tmp === void 0 ? 0 : tmp, tmp1 = params.end, paramsEnd = tmp1 === void 0 ? count - 1 : tmp1;
    var segmentStart = segment.start, segmentEnd = segment.end;
    var start = Math.max(paramsStart, segmentStart);
    var end = Math.min(paramsEnd, segmentEnd);
    var outside = paramsStart < segmentStart && paramsEnd < segmentStart || paramsStart > segmentEnd && paramsEnd > segmentEnd;
    return {
        count: count,
        start: start,
        loop: segment.loop,
        ilen: end < start && !outside ? count + end - start : end - start
    };
}
function pathSegment(ctx, line, segment, params) {
    var points = line.points, options = line.options;
    var ref = pathVars(points, segment, params), count = ref.count, start = ref.start, loop = ref.loop, ilen = ref.ilen;
    var lineMethod = getLineMethod(options);
    var ref7 = params || {}, _move = ref7.move, move = _move === void 0 ? true : _move, reverse = ref7.reverse;
    var i, point, prev;
    for(i = 0; i <= ilen; ++i){
        point = points[(start + (reverse ? ilen - i : i)) % count];
        if (point.skip) continue;
        else if (move) {
            ctx.moveTo(point.x, point.y);
            move = false;
        } else lineMethod(ctx, prev, point, reverse, options.stepped);
        prev = point;
    }
    if (loop) {
        point = points[(start + (reverse ? ilen : 0)) % count];
        lineMethod(ctx, prev, point, reverse, options.stepped);
    }
    return !!loop;
}
function fastPathSegment(ctx, line, segment, params) {
    var points = line.points;
    var ref = pathVars(points, segment, params), count = ref.count, start = ref.start, ilen = ref.ilen;
    var ref8 = params || {}, _move = ref8.move, move = _move === void 0 ? true : _move, reverse = ref8.reverse;
    var avgX = 0;
    var countX = 0;
    var i, point, prevX, minY, maxY, lastY;
    var pointIndex = function(index56) {
        return (start + (reverse ? ilen - index56 : index56)) % count;
    };
    var drawX = function() {
        if (minY !== maxY) {
            ctx.lineTo(avgX, maxY);
            ctx.lineTo(avgX, minY);
            ctx.lineTo(avgX, lastY);
        }
    };
    if (move) {
        point = points[pointIndex(0)];
        ctx.moveTo(point.x, point.y);
    }
    for(i = 0; i <= ilen; ++i){
        point = points[pointIndex(i)];
        if (point.skip) continue;
        var x = point.x;
        var y = point.y;
        var truncX = x | 0;
        if (truncX === prevX) {
            if (y < minY) minY = y;
            else if (y > maxY) maxY = y;
            avgX = (countX * avgX + x) / ++countX;
        } else {
            drawX();
            ctx.lineTo(x, y);
            prevX = truncX;
            countX = 0;
            minY = maxY = y;
        }
        lastY = y;
    }
    drawX();
}
function _getSegmentMethod(line) {
    var opts = line.options;
    var borderDash = opts.borderDash && opts.borderDash.length;
    var useFastPath = !line._decimated && !line._loop && !opts.tension && opts.cubicInterpolationMode !== "monotone" && !opts.stepped && !borderDash;
    return useFastPath ? fastPathSegment : pathSegment;
}
function _getInterpolationMethod(options) {
    if (options.stepped) return 0, _helpersSegmentJs.an;
    if (options.tension || options.cubicInterpolationMode === "monotone") return 0, _helpersSegmentJs.ao;
    return 0, _helpersSegmentJs.ap;
}
function strokePathWithCache(ctx, line, start, count) {
    var path = line._path;
    if (!path) {
        path = line._path = new Path2D();
        if (line.path(path, start, count)) path.closePath();
    }
    setStyle(ctx, line.options);
    ctx.stroke(path);
}
function strokePathDirect(ctx, line, start, count) {
    var segments = line.segments, options = line.options;
    var segmentMethod = _getSegmentMethod(line);
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = segments[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var segment = _step.value;
            setStyle(ctx, options, segment.style);
            ctx.beginPath();
            if (segmentMethod(ctx, line, segment, {
                start: start,
                end: start + count - 1
            })) ctx.closePath();
            ctx.stroke();
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
}
var usePath2D = typeof Path2D === "function";
function draw(ctx, line, start, count) {
    if (usePath2D && !line.options.segment) strokePathWithCache(ctx, line, start, count);
    else strokePathDirect(ctx, line, start, count);
}
var LineElement = /*#__PURE__*/ function(Element) {
    "use strict";
    (0, _inheritsJsDefault.default)(LineElement, Element);
    var _super = (0, _createSuperJsDefault.default)(LineElement);
    function LineElement(cfg) {
        (0, _classCallCheckJsDefault.default)(this, LineElement);
        var _this;
        _this = _super.call(this);
        _this.animated = true;
        _this.options = undefined;
        _this._chart = undefined;
        _this._loop = undefined;
        _this._fullLoop = undefined;
        _this._path = undefined;
        _this._points = undefined;
        _this._segments = undefined;
        _this._decimated = false;
        _this._pointsUpdated = false;
        _this._datasetIndex = undefined;
        if (cfg) Object.assign((0, _assertThisInitializedJsDefault.default)(_this), cfg);
        return _this;
    }
    (0, _createClassJsDefault.default)(LineElement, [
        {
            key: "updateControlPoints",
            value: function updateControlPoints(chartArea, indexAxis) {
                var options = this.options;
                if ((options.tension || options.cubicInterpolationMode === "monotone") && !options.stepped && !this._pointsUpdated) {
                    var loop = options.spanGaps ? this._loop : this._fullLoop;
                    (0, _helpersSegmentJs.ak)(this._points, options, chartArea, loop, indexAxis);
                    this._pointsUpdated = true;
                }
            }
        },
        {
            key: "points",
            get: function get() {
                return this._points;
            },
            set: function set(points) {
                this._points = points;
                delete this._segments;
                delete this._path;
                this._pointsUpdated = false;
            }
        },
        {
            key: "segments",
            get: function get() {
                return this._segments || (this._segments = (0, _helpersSegmentJs.al)(this, this.options.segment));
            }
        },
        {
            key: "first",
            value: function first() {
                var segments = this.segments;
                var points = this.points;
                return segments.length && points[segments[0].start];
            }
        },
        {
            key: "last",
            value: function last() {
                var segments = this.segments;
                var points = this.points;
                var count = segments.length;
                return count && points[segments[count - 1].end];
            }
        },
        {
            key: "interpolate",
            value: function interpolate(point, property) {
                var options = this.options;
                var value = point[property];
                var points = this.points;
                var segments = (0, _helpersSegmentJs.am)(this, {
                    property: property,
                    start: value,
                    end: value
                });
                if (!segments.length) return;
                var result = [];
                var _interpolate = _getInterpolationMethod(options);
                var i, ilen;
                for(i = 0, ilen = segments.length; i < ilen; ++i){
                    var _i = segments[i], start = _i.start, end = _i.end;
                    var p1 = points[start];
                    var p2 = points[end];
                    if (p1 === p2) {
                        result.push(p1);
                        continue;
                    }
                    var t = Math.abs((value - p1[property]) / (p2[property] - p1[property]));
                    var interpolated = _interpolate(p1, p2, t, options.stepped);
                    interpolated[property] = point[property];
                    result.push(interpolated);
                }
                return result.length === 1 ? result[0] : result;
            }
        },
        {
            key: "pathSegment",
            value: function pathSegment(ctx, segment, params) {
                var segmentMethod = _getSegmentMethod(this);
                return segmentMethod(ctx, this, segment, params);
            }
        },
        {
            key: "path",
            value: function path(ctx, start, count) {
                var segments = this.segments;
                var segmentMethod = _getSegmentMethod(this);
                var loop = this._loop;
                start = start || 0;
                count = count || this.points.length - start;
                var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                try {
                    for(var _iterator = segments[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                        var segment = _step.value;
                        loop &= segmentMethod(ctx, this, segment, {
                            start: start,
                            end: start + count - 1
                        });
                    }
                } catch (err) {
                    _didIteratorError = true;
                    _iteratorError = err;
                } finally{
                    try {
                        if (!_iteratorNormalCompletion && _iterator.return != null) {
                            _iterator.return();
                        }
                    } finally{
                        if (_didIteratorError) {
                            throw _iteratorError;
                        }
                    }
                }
                return !!loop;
            }
        },
        {
            key: "draw",
            value: function draw2(ctx, chartArea, start, count) {
                var options = this.options || {};
                var points = this.points || [];
                if (points.length && options.borderWidth) {
                    ctx.save();
                    draw(ctx, this, start, count);
                    ctx.restore();
                }
                if (this.animated) {
                    this._pointsUpdated = false;
                    this._path = undefined;
                }
            }
        }
    ]);
    return LineElement;
}((0, _wrapNativeSuperJsDefault.default)(Element));
LineElement.id = "line";
LineElement.defaults = {
    borderCapStyle: "butt",
    borderDash: [],
    borderDashOffset: 0,
    borderJoinStyle: "miter",
    borderWidth: 3,
    capBezierPoints: true,
    cubicInterpolationMode: "default",
    fill: false,
    spanGaps: false,
    stepped: false,
    tension: 0
};
LineElement.defaultRoutes = {
    backgroundColor: "backgroundColor",
    borderColor: "borderColor"
};
LineElement.descriptors = {
    _scriptable: true,
    _indexable: function(name) {
        return name !== "borderDash" && name !== "fill";
    }
};
function inRange$1(el, pos, axis, useFinalPosition) {
    var options = el.options;
    var ref = el.getProps([
        axis
    ], useFinalPosition), value = ref[axis];
    return Math.abs(pos - value) < options.radius + options.hitRadius;
}
var PointElement = /*#__PURE__*/ function(Element) {
    "use strict";
    (0, _inheritsJsDefault.default)(PointElement, Element);
    var _super = (0, _createSuperJsDefault.default)(PointElement);
    function PointElement(cfg) {
        (0, _classCallCheckJsDefault.default)(this, PointElement);
        var _this;
        _this = _super.call(this);
        _this.options = undefined;
        _this.parsed = undefined;
        _this.skip = undefined;
        _this.stop = undefined;
        if (cfg) Object.assign((0, _assertThisInitializedJsDefault.default)(_this), cfg);
        return _this;
    }
    (0, _createClassJsDefault.default)(PointElement, [
        {
            key: "inRange",
            value: function inRange2(mouseX, mouseY, useFinalPosition) {
                var options = this.options;
                var ref = this.getProps([
                    "x",
                    "y"
                ], useFinalPosition), x = ref.x, y = ref.y;
                return Math.pow(mouseX - x, 2) + Math.pow(mouseY - y, 2) < Math.pow(options.hitRadius + options.radius, 2);
            }
        },
        {
            key: "inXRange",
            value: function inXRange(mouseX, useFinalPosition) {
                return inRange$1(this, mouseX, "x", useFinalPosition);
            }
        },
        {
            key: "inYRange",
            value: function inYRange(mouseY, useFinalPosition) {
                return inRange$1(this, mouseY, "y", useFinalPosition);
            }
        },
        {
            key: "getCenterPoint",
            value: function getCenterPoint(useFinalPosition) {
                var ref = this.getProps([
                    "x",
                    "y"
                ], useFinalPosition), x = ref.x, y = ref.y;
                return {
                    x: x,
                    y: y
                };
            }
        },
        {
            key: "size",
            value: function size(options) {
                options = options || this.options || {};
                var radius = options.radius || 0;
                radius = Math.max(radius, radius && options.hoverRadius || 0);
                var borderWidth = radius && options.borderWidth || 0;
                return (radius + borderWidth) * 2;
            }
        },
        {
            key: "draw",
            value: function draw2(ctx, area) {
                var options = this.options;
                if (this.skip || options.radius < 0.1 || !(0, _helpersSegmentJs.B)(this, area, this.size(options) / 2)) return;
                ctx.strokeStyle = options.borderColor;
                ctx.lineWidth = options.borderWidth;
                ctx.fillStyle = options.backgroundColor;
                (0, _helpersSegmentJs.as)(ctx, options, this.x, this.y);
            }
        },
        {
            key: "getRange",
            value: function getRange() {
                var options = this.options || {};
                return options.radius + options.hitRadius;
            }
        }
    ]);
    return PointElement;
}((0, _wrapNativeSuperJsDefault.default)(Element));
PointElement.id = "point";
PointElement.defaults = {
    borderWidth: 1,
    hitRadius: 1,
    hoverBorderWidth: 1,
    hoverRadius: 4,
    pointStyle: "circle",
    radius: 3,
    rotation: 0
};
PointElement.defaultRoutes = {
    backgroundColor: "backgroundColor",
    borderColor: "borderColor"
};
function getBarBounds(bar, useFinalPosition) {
    var ref = bar.getProps([
        "x",
        "y",
        "base",
        "width",
        "height"
    ], useFinalPosition), x = ref.x, y = ref.y, base = ref.base, width = ref.width, height = ref.height;
    var left, right, top, bottom, half;
    if (bar.horizontal) {
        half = height / 2;
        left = Math.min(x, base);
        right = Math.max(x, base);
        top = y - half;
        bottom = y + half;
    } else {
        half = width / 2;
        left = x - half;
        right = x + half;
        top = Math.min(y, base);
        bottom = Math.max(y, base);
    }
    return {
        left: left,
        top: top,
        right: right,
        bottom: bottom
    };
}
function skipOrLimit(skip1, value, min, max) {
    return skip1 ? 0 : (0, _helpersSegmentJs.w)(value, min, max);
}
function parseBorderWidth(bar, maxW, maxH) {
    var value = bar.options.borderWidth;
    var skip2 = bar.borderSkipped;
    var o = (0, _helpersSegmentJs.au)(value);
    return {
        t: skipOrLimit(skip2.top, o.top, 0, maxH),
        r: skipOrLimit(skip2.right, o.right, 0, maxW),
        b: skipOrLimit(skip2.bottom, o.bottom, 0, maxH),
        l: skipOrLimit(skip2.left, o.left, 0, maxW)
    };
}
function parseBorderRadius(bar, maxW, maxH) {
    var enableBorderRadius = bar.getProps([
        "enableBorderRadius"
    ]).enableBorderRadius;
    var value = bar.options.borderRadius;
    var o = (0, _helpersSegmentJs.av)(value);
    var maxR = Math.min(maxW, maxH);
    var skip3 = bar.borderSkipped;
    var enableBorder = enableBorderRadius || (0, _helpersSegmentJs.i)(value);
    return {
        topLeft: skipOrLimit(!enableBorder || skip3.top || skip3.left, o.topLeft, 0, maxR),
        topRight: skipOrLimit(!enableBorder || skip3.top || skip3.right, o.topRight, 0, maxR),
        bottomLeft: skipOrLimit(!enableBorder || skip3.bottom || skip3.left, o.bottomLeft, 0, maxR),
        bottomRight: skipOrLimit(!enableBorder || skip3.bottom || skip3.right, o.bottomRight, 0, maxR)
    };
}
function boundingRects(bar) {
    var bounds = getBarBounds(bar);
    var width = bounds.right - bounds.left;
    var height = bounds.bottom - bounds.top;
    var border = parseBorderWidth(bar, width / 2, height / 2);
    var radius = parseBorderRadius(bar, width / 2, height / 2);
    return {
        outer: {
            x: bounds.left,
            y: bounds.top,
            w: width,
            h: height,
            radius: radius
        },
        inner: {
            x: bounds.left + border.l,
            y: bounds.top + border.t,
            w: width - border.l - border.r,
            h: height - border.t - border.b,
            radius: {
                topLeft: Math.max(0, radius.topLeft - Math.max(border.t, border.l)),
                topRight: Math.max(0, radius.topRight - Math.max(border.t, border.r)),
                bottomLeft: Math.max(0, radius.bottomLeft - Math.max(border.b, border.l)),
                bottomRight: Math.max(0, radius.bottomRight - Math.max(border.b, border.r))
            }
        }
    };
}
function inRange(bar, x, y, useFinalPosition) {
    var skipX = x === null;
    var skipY = y === null;
    var skipBoth = skipX && skipY;
    var bounds = bar && !skipBoth && getBarBounds(bar, useFinalPosition);
    return bounds && (skipX || (0, _helpersSegmentJs.ai)(x, bounds.left, bounds.right)) && (skipY || (0, _helpersSegmentJs.ai)(y, bounds.top, bounds.bottom));
}
function hasRadius(radius) {
    return radius.topLeft || radius.topRight || radius.bottomLeft || radius.bottomRight;
}
function addNormalRectPath(ctx, rect) {
    ctx.rect(rect.x, rect.y, rect.w, rect.h);
}
function inflateRect(rect, amount) {
    var refRect = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : {};
    var x = rect.x !== refRect.x ? -amount : 0;
    var y = rect.y !== refRect.y ? -amount : 0;
    var w = (rect.x + rect.w !== refRect.x + refRect.w ? amount : 0) - x;
    var h = (rect.y + rect.h !== refRect.y + refRect.h ? amount : 0) - y;
    return {
        x: rect.x + x,
        y: rect.y + y,
        w: rect.w + w,
        h: rect.h + h,
        radius: rect.radius
    };
}
var BarElement = /*#__PURE__*/ function(Element) {
    "use strict";
    (0, _inheritsJsDefault.default)(BarElement, Element);
    var _super = (0, _createSuperJsDefault.default)(BarElement);
    function BarElement(cfg) {
        (0, _classCallCheckJsDefault.default)(this, BarElement);
        var _this;
        _this = _super.call(this);
        _this.options = undefined;
        _this.horizontal = undefined;
        _this.base = undefined;
        _this.width = undefined;
        _this.height = undefined;
        _this.inflateAmount = undefined;
        if (cfg) Object.assign((0, _assertThisInitializedJsDefault.default)(_this), cfg);
        return _this;
    }
    (0, _createClassJsDefault.default)(BarElement, [
        {
            key: "draw",
            value: function draw2(ctx) {
                var ref = this, inflateAmount = ref.inflateAmount, _options = ref.options, borderColor = _options.borderColor, backgroundColor = _options.backgroundColor;
                var ref9 = boundingRects(this), inner = ref9.inner, outer = ref9.outer;
                var addRectPath = hasRadius(outer.radius) ? (0, _helpersSegmentJs.at) : addNormalRectPath;
                ctx.save();
                if (outer.w !== inner.w || outer.h !== inner.h) {
                    ctx.beginPath();
                    addRectPath(ctx, inflateRect(outer, inflateAmount, inner));
                    ctx.clip();
                    addRectPath(ctx, inflateRect(inner, -inflateAmount, outer));
                    ctx.fillStyle = borderColor;
                    ctx.fill("evenodd");
                }
                ctx.beginPath();
                addRectPath(ctx, inflateRect(inner, inflateAmount));
                ctx.fillStyle = backgroundColor;
                ctx.fill();
                ctx.restore();
            }
        },
        {
            key: "inRange",
            value: function inRange2(mouseX, mouseY, useFinalPosition) {
                return inRange(this, mouseX, mouseY, useFinalPosition);
            }
        },
        {
            key: "inXRange",
            value: function inXRange(mouseX, useFinalPosition) {
                return inRange(this, mouseX, null, useFinalPosition);
            }
        },
        {
            key: "inYRange",
            value: function inYRange(mouseY, useFinalPosition) {
                return inRange(this, null, mouseY, useFinalPosition);
            }
        },
        {
            key: "getCenterPoint",
            value: function getCenterPoint(useFinalPosition) {
                var ref = this.getProps([
                    "x",
                    "y",
                    "base",
                    "horizontal"
                ], useFinalPosition), x = ref.x, y = ref.y, base = ref.base, horizontal = ref.horizontal;
                return {
                    x: horizontal ? (x + base) / 2 : x,
                    y: horizontal ? y : (y + base) / 2
                };
            }
        },
        {
            key: "getRange",
            value: function getRange(axis) {
                return axis === "x" ? this.width / 2 : this.height / 2;
            }
        }
    ]);
    return BarElement;
}((0, _wrapNativeSuperJsDefault.default)(Element));
BarElement.id = "bar";
BarElement.defaults = {
    borderSkipped: "start",
    borderWidth: 0,
    borderRadius: 0,
    inflateAmount: "auto",
    pointStyle: undefined
};
BarElement.defaultRoutes = {
    backgroundColor: "backgroundColor",
    borderColor: "borderColor"
};
var elements = /*#__PURE__*/ Object.freeze({
    __proto__: null,
    ArcElement: ArcElement,
    LineElement: LineElement,
    PointElement: PointElement,
    BarElement: BarElement
});
function lttbDecimation(data, start, count, availableWidth, options) {
    var samples = options.samples || availableWidth;
    if (samples >= count) return data.slice(start, start + count);
    var decimated = [];
    var bucketWidth = (count - 2) / (samples - 2);
    var sampledIndex = 0;
    var endIndex = start + count - 1;
    var a = start;
    var i, maxAreaPoint, maxArea, area, nextA;
    decimated[sampledIndex++] = data[a];
    for(i = 0; i < samples - 2; i++){
        var avgX = 0;
        var avgY = 0;
        var j = void 0;
        var avgRangeStart = Math.floor((i + 1) * bucketWidth) + 1 + start;
        var avgRangeEnd = Math.min(Math.floor((i + 2) * bucketWidth) + 1, count) + start;
        var avgRangeLength = avgRangeEnd - avgRangeStart;
        for(j = avgRangeStart; j < avgRangeEnd; j++){
            avgX += data[j].x;
            avgY += data[j].y;
        }
        avgX /= avgRangeLength;
        avgY /= avgRangeLength;
        var rangeOffs = Math.floor(i * bucketWidth) + 1 + start;
        var rangeTo = Math.min(Math.floor((i + 1) * bucketWidth) + 1, count) + start;
        var _a = data[a], pointAx = _a.x, pointAy = _a.y;
        maxArea = area = -1;
        for(j = rangeOffs; j < rangeTo; j++){
            area = 0.5 * Math.abs((pointAx - avgX) * (data[j].y - pointAy) - (pointAx - data[j].x) * (avgY - pointAy));
            if (area > maxArea) {
                maxArea = area;
                maxAreaPoint = data[j];
                nextA = j;
            }
        }
        decimated[sampledIndex++] = maxAreaPoint;
        a = nextA;
    }
    decimated[sampledIndex++] = data[endIndex];
    return decimated;
}
function minMaxDecimation(data, start, count, availableWidth) {
    var avgX = 0;
    var countX = 0;
    var i, point, x, y, prevX, minIndex, maxIndex, startIndex, minY, maxY;
    var decimated = [];
    var endIndex = start + count - 1;
    var xMin = data[start].x;
    var xMax = data[endIndex].x;
    var dx = xMax - xMin;
    for(i = start; i < start + count; ++i){
        point = data[i];
        x = (point.x - xMin) / dx * availableWidth;
        y = point.y;
        var truncX = x | 0;
        if (truncX === prevX) {
            if (y < minY) {
                minY = y;
                minIndex = i;
            } else if (y > maxY) {
                maxY = y;
                maxIndex = i;
            }
            avgX = (countX * avgX + point.x) / ++countX;
        } else {
            var lastIndex = i - 1;
            if (!(0, _helpersSegmentJs.k)(minIndex) && !(0, _helpersSegmentJs.k)(maxIndex)) {
                var intermediateIndex1 = Math.min(minIndex, maxIndex);
                var intermediateIndex2 = Math.max(minIndex, maxIndex);
                if (intermediateIndex1 !== startIndex && intermediateIndex1 !== lastIndex) decimated.push((0, _objectSpreadJsDefault.default)({}, data[intermediateIndex1], {
                    x: avgX
                }));
                if (intermediateIndex2 !== startIndex && intermediateIndex2 !== lastIndex) decimated.push((0, _objectSpreadJsDefault.default)({}, data[intermediateIndex2], {
                    x: avgX
                }));
            }
            if (i > 0 && lastIndex !== startIndex) decimated.push(data[lastIndex]);
            decimated.push(point);
            prevX = truncX;
            countX = 0;
            minY = maxY = y;
            minIndex = maxIndex = startIndex = i;
        }
    }
    return decimated;
}
function cleanDecimatedDataset(dataset) {
    if (dataset._decimated) {
        var data = dataset._data;
        delete dataset._decimated;
        delete dataset._data;
        Object.defineProperty(dataset, "data", {
            value: data
        });
    }
}
function cleanDecimatedData(chart) {
    chart.data.datasets.forEach(function(dataset) {
        cleanDecimatedDataset(dataset);
    });
}
function getStartAndCountOfVisiblePointsSimplified(meta, points) {
    var pointCount = points.length;
    var start = 0;
    var count;
    var iScale = meta.iScale;
    var ref = iScale.getUserBounds(), min = ref.min, max = ref.max, minDefined = ref.minDefined, maxDefined = ref.maxDefined;
    if (minDefined) start = (0, _helpersSegmentJs.w)((0, _helpersSegmentJs.x)(points, iScale.axis, min).lo, 0, pointCount - 1);
    if (maxDefined) count = (0, _helpersSegmentJs.w)((0, _helpersSegmentJs.x)(points, iScale.axis, max).hi + 1, start, pointCount) - start;
    else count = pointCount - start;
    return {
        start: start,
        count: count
    };
}
var plugin_decimation = {
    id: "decimation",
    defaults: {
        algorithm: "min-max",
        enabled: false
    },
    beforeElementsUpdate: function(chart, args, options) {
        if (!options.enabled) {
            cleanDecimatedData(chart);
            return;
        }
        var availableWidth = chart.width;
        chart.data.datasets.forEach(function(dataset, datasetIndex) {
            var _data = dataset._data, indexAxis = dataset.indexAxis;
            var meta = chart.getDatasetMeta(datasetIndex);
            var data = _data || dataset.data;
            if ((0, _helpersSegmentJs.a)([
                indexAxis,
                chart.options.indexAxis
            ]) === "y") return;
            if (!meta.controller.supportsDecimation) return;
            var xAxis = chart.scales[meta.xAxisID];
            if (xAxis.type !== "linear" && xAxis.type !== "time") return;
            if (chart.options.parsing) return;
            var ref = getStartAndCountOfVisiblePointsSimplified(meta, data), start = ref.start, count = ref.count;
            var threshold = options.threshold || 4 * availableWidth;
            if (count <= threshold) {
                cleanDecimatedDataset(dataset);
                return;
            }
            if ((0, _helpersSegmentJs.k)(_data)) {
                dataset._data = data;
                delete dataset.data;
                Object.defineProperty(dataset, "data", {
                    configurable: true,
                    enumerable: true,
                    get: function get() {
                        return this._decimated;
                    },
                    set: function set(d) {
                        this._data = d;
                    }
                });
            }
            var decimated;
            switch(options.algorithm){
                case "lttb":
                    decimated = lttbDecimation(data, start, count, availableWidth, options);
                    break;
                case "min-max":
                    decimated = minMaxDecimation(data, start, count, availableWidth);
                    break;
                default:
                    throw new Error("Unsupported decimation algorithm '".concat(options.algorithm, "'"));
            }
            dataset._decimated = decimated;
        });
    },
    destroy: function(chart) {
        cleanDecimatedData(chart);
    }
};
function _segments(line, target, property) {
    var segments = line.segments;
    var points = line.points;
    var tpoints = target.points;
    var parts = [];
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = segments[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var segment = _step.value;
            var start = segment.start, end = segment.end;
            end = _findSegmentEnd(start, end, points);
            var bounds = _getBounds(property, points[start], points[end], segment.loop);
            if (!target.segments) {
                parts.push({
                    source: segment,
                    target: bounds,
                    start: points[start],
                    end: points[end]
                });
                continue;
            }
            var targetSegments = (0, _helpersSegmentJs.am)(target, bounds);
            var _iteratorNormalCompletion1 = true, _didIteratorError1 = false, _iteratorError1 = undefined;
            try {
                for(var _iterator1 = targetSegments[Symbol.iterator](), _step1; !(_iteratorNormalCompletion1 = (_step1 = _iterator1.next()).done); _iteratorNormalCompletion1 = true){
                    var tgt = _step1.value;
                    var subBounds = _getBounds(property, tpoints[tgt.start], tpoints[tgt.end], tgt.loop);
                    var fillSources = (0, _helpersSegmentJs.aw)(segment, points, subBounds);
                    var _iteratorNormalCompletion2 = true, _didIteratorError2 = false, _iteratorError2 = undefined;
                    try {
                        for(var _iterator2 = fillSources[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true){
                            var fillSource = _step2.value;
                            parts.push({
                                source: fillSource,
                                target: tgt,
                                start: (0, _definePropertyJsDefault.default)({}, property, _getEdge(bounds, subBounds, "start", Math.max)),
                                end: (0, _definePropertyJsDefault.default)({}, property, _getEdge(bounds, subBounds, "end", Math.min))
                            });
                        }
                    } catch (err) {
                        _didIteratorError2 = true;
                        _iteratorError2 = err;
                    } finally{
                        try {
                            if (!_iteratorNormalCompletion2 && _iterator2.return != null) {
                                _iterator2.return();
                            }
                        } finally{
                            if (_didIteratorError2) {
                                throw _iteratorError2;
                            }
                        }
                    }
                }
            } catch (err) {
                _didIteratorError1 = true;
                _iteratorError1 = err;
            } finally{
                try {
                    if (!_iteratorNormalCompletion1 && _iterator1.return != null) {
                        _iterator1.return();
                    }
                } finally{
                    if (_didIteratorError1) {
                        throw _iteratorError1;
                    }
                }
            }
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
    return parts;
}
function _getBounds(property, first, last, loop) {
    if (loop) return;
    var start = first[property];
    var end = last[property];
    if (property === "angle") {
        start = (0, _helpersSegmentJs.ax)(start);
        end = (0, _helpersSegmentJs.ax)(end);
    }
    return {
        property: property,
        start: start,
        end: end
    };
}
function _pointsFromSegments(boundary, line) {
    var ref = boundary || {}, _x = ref.x, x = _x === void 0 ? null : _x, _y = ref.y, y = _y === void 0 ? null : _y;
    var linePoints = line.points;
    var points = [];
    line.segments.forEach(function(param) {
        var start = param.start, end = param.end;
        end = _findSegmentEnd(start, end, linePoints);
        var first = linePoints[start];
        var last = linePoints[end];
        if (y !== null) {
            points.push({
                x: first.x,
                y: y
            });
            points.push({
                x: last.x,
                y: y
            });
        } else if (x !== null) {
            points.push({
                x: x,
                y: first.y
            });
            points.push({
                x: x,
                y: last.y
            });
        }
    });
    return points;
}
function _findSegmentEnd(start, end, points) {
    for(; end > start; end--){
        var point = points[end];
        if (!isNaN(point.x) && !isNaN(point.y)) break;
    }
    return end;
}
function _getEdge(a, b, prop, fn) {
    if (a && b) return fn(a[prop], b[prop]);
    return a ? a[prop] : b ? b[prop] : 0;
}
function _createBoundaryLine(boundary, line) {
    var points = [];
    var _loop = false;
    if ((0, _helpersSegmentJs.b)(boundary)) {
        _loop = true;
        points = boundary;
    } else points = _pointsFromSegments(boundary, line);
    return points.length ? new LineElement({
        points: points,
        options: {
            tension: 0
        },
        _loop: _loop,
        _fullLoop: _loop
    }) : null;
}
function _resolveTarget(sources, index57, propagate) {
    var source = sources[index57];
    var fill1 = source.fill;
    var visited = [
        index57
    ];
    var target;
    if (!propagate) return fill1;
    while(fill1 !== false && visited.indexOf(fill1) === -1){
        if (!(0, _helpersSegmentJs.g)(fill1)) return fill1;
        target = sources[fill1];
        if (!target) return false;
        if (target.visible) return fill1;
        visited.push(fill1);
        fill1 = target.fill;
    }
    return false;
}
function _decodeFill(line, index58, count) {
    var fill2 = parseFillOption(line);
    if ((0, _helpersSegmentJs.i)(fill2)) return isNaN(fill2.value) ? false : fill2;
    var target = parseFloat(fill2);
    if ((0, _helpersSegmentJs.g)(target) && Math.floor(target) === target) return decodeTargetIndex(fill2[0], index58, target, count);
    return [
        "origin",
        "start",
        "end",
        "stack",
        "shape"
    ].indexOf(fill2) >= 0 && fill2;
}
function decodeTargetIndex(firstCh, index59, target, count) {
    if (firstCh === "-" || firstCh === "+") target = index59 + target;
    if (target === index59 || target < 0 || target >= count) return false;
    return target;
}
function _getTargetPixel(fill3, scale) {
    var pixel = null;
    if (fill3 === "start") pixel = scale.bottom;
    else if (fill3 === "end") pixel = scale.top;
    else if ((0, _helpersSegmentJs.i)(fill3)) pixel = scale.getPixelForValue(fill3.value);
    else if (scale.getBasePixel) pixel = scale.getBasePixel();
    return pixel;
}
function _getTargetValue(fill4, scale, startValue) {
    var value;
    if (fill4 === "start") value = startValue;
    else if (fill4 === "end") value = scale.options.reverse ? scale.min : scale.max;
    else if ((0, _helpersSegmentJs.i)(fill4)) value = fill4.value;
    else value = scale.getBaseValue();
    return value;
}
function parseFillOption(line) {
    var options = line.options;
    var fillOption = options.fill;
    var fill5 = (0, _helpersSegmentJs.v)(fillOption && fillOption.target, fillOption);
    if (fill5 === undefined) fill5 = !!options.backgroundColor;
    if (fill5 === false || fill5 === null) return false;
    if (fill5 === true) return "origin";
    return fill5;
}
function _buildStackLine(source) {
    var scale = source.scale, index60 = source.index, line = source.line;
    var points = [];
    var segments = line.segments;
    var sourcePoints = line.points;
    var linesBelow = getLinesBelow(scale, index60);
    linesBelow.push(_createBoundaryLine({
        x: null,
        y: scale.bottom
    }, line));
    for(var i = 0; i < segments.length; i++){
        var segment = segments[i];
        for(var j = segment.start; j <= segment.end; j++)addPointsBelow(points, sourcePoints[j], linesBelow);
    }
    return new LineElement({
        points: points,
        options: {}
    });
}
function getLinesBelow(scale, index61) {
    var below = [];
    var metas = scale.getMatchingVisibleMetas("line");
    for(var i = 0; i < metas.length; i++){
        var meta = metas[i];
        if (meta.index === index61) break;
        if (!meta.hidden) below.unshift(meta.dataset);
    }
    return below;
}
function addPointsBelow(points, sourcePoint, linesBelow) {
    var _points;
    var postponed = [];
    for(var j = 0; j < linesBelow.length; j++){
        var line = linesBelow[j];
        var ref = findPoint(line, sourcePoint, "x"), first = ref.first, last = ref.last, point = ref.point;
        if (!point || first && last) continue;
        if (first) postponed.unshift(point);
        else {
            points.push(point);
            if (!last) break;
        }
    }
    (_points = points).push.apply(_points, (0, _toConsumableArrayJsDefault.default)(postponed));
}
function findPoint(line, sourcePoint, property) {
    var point = line.interpolate(sourcePoint, property);
    if (!point) return {};
    var pointValue = point[property];
    var segments = line.segments;
    var linePoints = line.points;
    var first = false;
    var last = false;
    for(var i = 0; i < segments.length; i++){
        var segment = segments[i];
        var firstValue = linePoints[segment.start][property];
        var lastValue = linePoints[segment.end][property];
        if ((0, _helpersSegmentJs.ai)(pointValue, firstValue, lastValue)) {
            first = pointValue === firstValue;
            last = pointValue === lastValue;
            break;
        }
    }
    return {
        first: first,
        last: last,
        point: point
    };
}
var simpleArc = /*#__PURE__*/ function() {
    "use strict";
    function simpleArc(opts) {
        (0, _classCallCheckJsDefault.default)(this, simpleArc);
        this.x = opts.x;
        this.y = opts.y;
        this.radius = opts.radius;
    }
    (0, _createClassJsDefault.default)(simpleArc, [
        {
            key: "pathSegment",
            value: function pathSegment(ctx, bounds, opts) {
                var ref = this, x = ref.x, y = ref.y, radius = ref.radius;
                bounds = bounds || {
                    start: 0,
                    end: (0, _helpersSegmentJs.T)
                };
                ctx.arc(x, y, radius, bounds.end, bounds.start, true);
                return !opts.bounds;
            }
        },
        {
            key: "interpolate",
            value: function interpolate(point) {
                var ref = this, x = ref.x, y = ref.y, radius = ref.radius;
                var angle = point.angle;
                return {
                    x: x + Math.cos(angle) * radius,
                    y: y + Math.sin(angle) * radius,
                    angle: angle
                };
            }
        }
    ]);
    return simpleArc;
}();
function _getTarget(source) {
    var chart = source.chart, fill6 = source.fill, line = source.line;
    if ((0, _helpersSegmentJs.g)(fill6)) return getLineByIndex(chart, fill6);
    if (fill6 === "stack") return _buildStackLine(source);
    if (fill6 === "shape") return true;
    var boundary = computeBoundary(source);
    if (boundary instanceof simpleArc) return boundary;
    return _createBoundaryLine(boundary, line);
}
function getLineByIndex(chart, index62) {
    var meta = chart.getDatasetMeta(index62);
    var visible = meta && chart.isDatasetVisible(index62);
    return visible ? meta.dataset : null;
}
function computeBoundary(source) {
    var scale = source.scale || {};
    if (scale.getPointPositionForValue) return computeCircularBoundary(source);
    return computeLinearBoundary(source);
}
function computeLinearBoundary(source) {
    var _scale = source.scale, scale = _scale === void 0 ? {} : _scale, fill7 = source.fill;
    var pixel = _getTargetPixel(fill7, scale);
    if ((0, _helpersSegmentJs.g)(pixel)) {
        var horizontal = scale.isHorizontal();
        return {
            x: horizontal ? pixel : null,
            y: horizontal ? null : pixel
        };
    }
    return null;
}
function computeCircularBoundary(source) {
    var scale = source.scale, fill8 = source.fill;
    var options = scale.options;
    var length = scale.getLabels().length;
    var start = options.reverse ? scale.max : scale.min;
    var value = _getTargetValue(fill8, scale, start);
    var target = [];
    if (options.grid.circular) {
        var center = scale.getPointPositionForValue(0, start);
        return new simpleArc({
            x: center.x,
            y: center.y,
            radius: scale.getDistanceFromCenterForValue(value)
        });
    }
    for(var i = 0; i < length; ++i)target.push(scale.getPointPositionForValue(i, value));
    return target;
}
function _drawfill(ctx, source, area) {
    var target = _getTarget(source);
    var line = source.line, scale = source.scale, axis = source.axis;
    var lineOpts = line.options;
    var fillOption = lineOpts.fill;
    var color = lineOpts.backgroundColor;
    var ref = fillOption || {}, _above = ref.above, above = _above === void 0 ? color : _above, _below = ref.below, below = _below === void 0 ? color : _below;
    if (target && line.points.length) {
        (0, _helpersSegmentJs.X)(ctx, area);
        doFill(ctx, {
            line: line,
            target: target,
            above: above,
            below: below,
            area: area,
            scale: scale,
            axis: axis
        });
        (0, _helpersSegmentJs.Z)(ctx);
    }
}
function doFill(ctx, cfg) {
    var line = cfg.line, target = cfg.target, above = cfg.above, below = cfg.below, area = cfg.area, scale = cfg.scale;
    var property = line._loop ? "angle" : cfg.axis;
    ctx.save();
    if (property === "x" && below !== above) {
        clipVertical(ctx, target, area.top);
        fill(ctx, {
            line: line,
            target: target,
            color: above,
            scale: scale,
            property: property
        });
        ctx.restore();
        ctx.save();
        clipVertical(ctx, target, area.bottom);
    }
    fill(ctx, {
        line: line,
        target: target,
        color: below,
        scale: scale,
        property: property
    });
    ctx.restore();
}
function clipVertical(ctx, target, clipY) {
    var segments = target.segments, points = target.points;
    var first = true;
    var lineLoop = false;
    ctx.beginPath();
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = segments[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var segment = _step.value;
            var start = segment.start, end = segment.end;
            var firstPoint = points[start];
            var lastPoint = points[_findSegmentEnd(start, end, points)];
            if (first) {
                ctx.moveTo(firstPoint.x, firstPoint.y);
                first = false;
            } else {
                ctx.lineTo(firstPoint.x, clipY);
                ctx.lineTo(firstPoint.x, firstPoint.y);
            }
            lineLoop = !!target.pathSegment(ctx, segment, {
                move: lineLoop
            });
            if (lineLoop) ctx.closePath();
            else ctx.lineTo(lastPoint.x, clipY);
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
    ctx.lineTo(target.first().x, clipY);
    ctx.closePath();
    ctx.clip();
}
function fill(ctx, cfg) {
    var line = cfg.line, target = cfg.target, property = cfg.property, color = cfg.color, scale = cfg.scale;
    var segments = _segments(line, target, property);
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = segments[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var _value = _step.value, src = _value.source, tgt = _value.target, start = _value.start, end = _value.end;
            var tmp = src.style, ref = tmp === void 0 ? {} : tmp, _backgroundColor = ref.backgroundColor, backgroundColor = _backgroundColor === void 0 ? color : _backgroundColor;
            var notShape = target !== true;
            ctx.save();
            ctx.fillStyle = backgroundColor;
            clipBounds(ctx, scale, notShape && _getBounds(property, start, end));
            ctx.beginPath();
            var lineLoop = !!line.pathSegment(ctx, src);
            var loop = void 0;
            if (notShape) {
                if (lineLoop) ctx.closePath();
                else interpolatedLineTo(ctx, target, end, property);
                var targetLoop = !!target.pathSegment(ctx, tgt, {
                    move: lineLoop,
                    reverse: true
                });
                loop = lineLoop && targetLoop;
                if (!loop) interpolatedLineTo(ctx, target, start, property);
            }
            ctx.closePath();
            ctx.fill(loop ? "evenodd" : "nonzero");
            ctx.restore();
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
}
function clipBounds(ctx, scale, bounds) {
    var _chartArea = scale.chart.chartArea, top = _chartArea.top, bottom = _chartArea.bottom;
    var ref = bounds || {}, property = ref.property, start = ref.start, end = ref.end;
    if (property === "x") {
        ctx.beginPath();
        ctx.rect(start, top, end - start, bottom - top);
        ctx.clip();
    }
}
function interpolatedLineTo(ctx, target, point, property) {
    var interpolatedPoint = target.interpolate(point, property);
    if (interpolatedPoint) ctx.lineTo(interpolatedPoint.x, interpolatedPoint.y);
}
var index = {
    id: "filler",
    afterDatasetsUpdate: function(chart, _args, options) {
        var count = (chart.data.datasets || []).length;
        var sources = [];
        var meta, i, line, source;
        for(i = 0; i < count; ++i){
            meta = chart.getDatasetMeta(i);
            line = meta.dataset;
            source = null;
            if (line && line.options && line instanceof LineElement) source = {
                visible: chart.isDatasetVisible(i),
                index: i,
                fill: _decodeFill(line, i, count),
                chart: chart,
                axis: meta.controller.options.indexAxis,
                scale: meta.vScale,
                line: line
            };
            meta.$filler = source;
            sources.push(source);
        }
        for(i = 0; i < count; ++i){
            source = sources[i];
            if (!source || source.fill === false) continue;
            source.fill = _resolveTarget(sources, i, options.propagate);
        }
    },
    beforeDraw: function(chart, _args, options) {
        var draw3 = options.drawTime === "beforeDraw";
        var metasets = chart.getSortedVisibleDatasetMetas();
        var area = chart.chartArea;
        for(var i = metasets.length - 1; i >= 0; --i){
            var source = metasets[i].$filler;
            if (!source) continue;
            source.line.updateControlPoints(area, source.axis);
            if (draw3) _drawfill(chart.ctx, source, area);
        }
    },
    beforeDatasetsDraw: function(chart, _args, options) {
        if (options.drawTime !== "beforeDatasetsDraw") return;
        var metasets = chart.getSortedVisibleDatasetMetas();
        for(var i = metasets.length - 1; i >= 0; --i){
            var source = metasets[i].$filler;
            if (source) _drawfill(chart.ctx, source, chart.chartArea);
        }
    },
    beforeDatasetDraw: function(chart, args, options) {
        var source = args.meta.$filler;
        if (!source || source.fill === false || options.drawTime !== "beforeDatasetDraw") return;
        _drawfill(chart.ctx, source, chart.chartArea);
    },
    defaults: {
        propagate: true,
        drawTime: "beforeDatasetDraw"
    }
};
var getBoxSize = function(labelOpts, fontSize) {
    var _boxHeight = labelOpts.boxHeight, boxHeight = _boxHeight === void 0 ? fontSize : _boxHeight, _boxWidth = labelOpts.boxWidth, boxWidth = _boxWidth === void 0 ? fontSize : _boxWidth;
    if (labelOpts.usePointStyle) {
        boxHeight = Math.min(boxHeight, fontSize);
        boxWidth = Math.min(boxWidth, fontSize);
    }
    return {
        boxWidth: boxWidth,
        boxHeight: boxHeight,
        itemHeight: Math.max(fontSize, boxHeight)
    };
};
var itemsEqual = function(a, b) {
    return a !== null && b !== null && a.datasetIndex === b.datasetIndex && a.index === b.index;
};
var Legend = /*#__PURE__*/ function(Element) {
    "use strict";
    (0, _inheritsJsDefault.default)(Legend, Element);
    var _super = (0, _createSuperJsDefault.default)(Legend);
    function Legend(config) {
        (0, _classCallCheckJsDefault.default)(this, Legend);
        var _this;
        _this = _super.call(this);
        _this._added = false;
        _this.legendHitBoxes = [];
        _this._hoveredItem = null;
        _this.doughnutMode = false;
        _this.chart = config.chart;
        _this.options = config.options;
        _this.ctx = config.ctx;
        _this.legendItems = undefined;
        _this.columnSizes = undefined;
        _this.lineWidths = undefined;
        _this.maxHeight = undefined;
        _this.maxWidth = undefined;
        _this.top = undefined;
        _this.bottom = undefined;
        _this.left = undefined;
        _this.right = undefined;
        _this.height = undefined;
        _this.width = undefined;
        _this._margins = undefined;
        _this.position = undefined;
        _this.weight = undefined;
        _this.fullSize = undefined;
        return _this;
    }
    (0, _createClassJsDefault.default)(Legend, [
        {
            key: "update",
            value: function update(maxWidth, maxHeight, margins) {
                this.maxWidth = maxWidth;
                this.maxHeight = maxHeight;
                this._margins = margins;
                this.setDimensions();
                this.buildLabels();
                this.fit();
            }
        },
        {
            key: "setDimensions",
            value: function setDimensions() {
                if (this.isHorizontal()) {
                    this.width = this.maxWidth;
                    this.left = this._margins.left;
                    this.right = this.width;
                } else {
                    this.height = this.maxHeight;
                    this.top = this._margins.top;
                    this.bottom = this.height;
                }
            }
        },
        {
            key: "buildLabels",
            value: function buildLabels() {
                var _this = this;
                var labelOpts = this.options.labels || {};
                var legendItems = (0, _helpersSegmentJs.Q)(labelOpts.generateLabels, [
                    this.chart
                ], this) || [];
                if (labelOpts.filter) legendItems = legendItems.filter(function(item) {
                    return labelOpts.filter(item, _this.chart.data);
                });
                if (labelOpts.sort) legendItems = legendItems.sort(function(a, b) {
                    return labelOpts.sort(a, b, _this.chart.data);
                });
                if (this.options.reverse) legendItems.reverse();
                this.legendItems = legendItems;
            }
        },
        {
            key: "fit",
            value: function fit() {
                var ref = this, options = ref.options, ctx = ref.ctx;
                if (!options.display) {
                    this.width = this.height = 0;
                    return;
                }
                var labelOpts = options.labels;
                var labelFont = (0, _helpersSegmentJs.$)(labelOpts.font);
                var fontSize = labelFont.size;
                var titleHeight = this._computeTitleHeight();
                var ref10 = getBoxSize(labelOpts, fontSize), boxWidth = ref10.boxWidth, itemHeight = ref10.itemHeight;
                var width, height;
                ctx.font = labelFont.string;
                if (this.isHorizontal()) {
                    width = this.maxWidth;
                    height = this._fitRows(titleHeight, fontSize, boxWidth, itemHeight) + 10;
                } else {
                    height = this.maxHeight;
                    width = this._fitCols(titleHeight, fontSize, boxWidth, itemHeight) + 10;
                }
                this.width = Math.min(width, options.maxWidth || this.maxWidth);
                this.height = Math.min(height, options.maxHeight || this.maxHeight);
            }
        },
        {
            key: "_fitRows",
            value: function _fitRows(titleHeight, fontSize, boxWidth, itemHeight) {
                var ref = this, ctx = ref.ctx, maxWidth = ref.maxWidth, _options = ref.options, padding = _options.labels.padding;
                var hitboxes = this.legendHitBoxes = [];
                var lineWidths = this.lineWidths = [
                    0
                ];
                var lineHeight = itemHeight + padding;
                var totalHeight = titleHeight;
                ctx.textAlign = "left";
                ctx.textBaseline = "middle";
                var row = -1;
                var top = -lineHeight;
                this.legendItems.forEach(function(legendItem, i) {
                    var itemWidth = boxWidth + fontSize / 2 + ctx.measureText(legendItem.text).width;
                    if (i === 0 || lineWidths[lineWidths.length - 1] + itemWidth + 2 * padding > maxWidth) {
                        totalHeight += lineHeight;
                        lineWidths[lineWidths.length - (i > 0 ? 0 : 1)] = 0;
                        top += lineHeight;
                        row++;
                    }
                    hitboxes[i] = {
                        left: 0,
                        top: top,
                        row: row,
                        width: itemWidth,
                        height: itemHeight
                    };
                    lineWidths[lineWidths.length - 1] += itemWidth + padding;
                });
                return totalHeight;
            }
        },
        {
            key: "_fitCols",
            value: function _fitCols(titleHeight, fontSize, boxWidth, itemHeight) {
                var ref = this, ctx = ref.ctx, maxHeight = ref.maxHeight, _options = ref.options, padding = _options.labels.padding;
                var hitboxes = this.legendHitBoxes = [];
                var columnSizes = this.columnSizes = [];
                var heightLimit = maxHeight - titleHeight;
                var totalWidth = padding;
                var currentColWidth = 0;
                var currentColHeight = 0;
                var left = 0;
                var col = 0;
                this.legendItems.forEach(function(legendItem, i) {
                    var itemWidth = boxWidth + fontSize / 2 + ctx.measureText(legendItem.text).width;
                    if (i > 0 && currentColHeight + itemHeight + 2 * padding > heightLimit) {
                        totalWidth += currentColWidth + padding;
                        columnSizes.push({
                            width: currentColWidth,
                            height: currentColHeight
                        });
                        left += currentColWidth + padding;
                        col++;
                        currentColWidth = currentColHeight = 0;
                    }
                    hitboxes[i] = {
                        left: left,
                        top: currentColHeight,
                        col: col,
                        width: itemWidth,
                        height: itemHeight
                    };
                    currentColWidth = Math.max(currentColWidth, itemWidth);
                    currentColHeight += itemHeight + padding;
                });
                totalWidth += currentColWidth;
                columnSizes.push({
                    width: currentColWidth,
                    height: currentColHeight
                });
                return totalWidth;
            }
        },
        {
            key: "adjustHitBoxes",
            value: function adjustHitBoxes() {
                if (!this.options.display) return;
                var titleHeight = this._computeTitleHeight();
                var ref = this, hitboxes = ref.legendHitBoxes, _options = ref.options, align = _options.align, padding = _options.labels.padding, rtl = _options.rtl;
                var rtlHelper = (0, _helpersSegmentJs.ay)(rtl, this.left, this.width);
                if (this.isHorizontal()) {
                    var row = 0;
                    var left = (0, _helpersSegmentJs.a1)(align, this.left + padding, this.right - this.lineWidths[row]);
                    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                    try {
                        for(var _iterator = hitboxes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                            var hitbox = _step.value;
                            if (row !== hitbox.row) {
                                row = hitbox.row;
                                left = (0, _helpersSegmentJs.a1)(align, this.left + padding, this.right - this.lineWidths[row]);
                            }
                            hitbox.top += this.top + titleHeight + padding;
                            hitbox.left = rtlHelper.leftForLtr(rtlHelper.x(left), hitbox.width);
                            left += hitbox.width + padding;
                        }
                    } catch (err) {
                        _didIteratorError = true;
                        _iteratorError = err;
                    } finally{
                        try {
                            if (!_iteratorNormalCompletion && _iterator.return != null) {
                                _iterator.return();
                            }
                        } finally{
                            if (_didIteratorError) {
                                throw _iteratorError;
                            }
                        }
                    }
                } else {
                    var col = 0;
                    var top = (0, _helpersSegmentJs.a1)(align, this.top + titleHeight + padding, this.bottom - this.columnSizes[col].height);
                    var _iteratorNormalCompletion3 = true, _didIteratorError3 = false, _iteratorError3 = undefined;
                    try {
                        for(var _iterator3 = hitboxes[Symbol.iterator](), _step3; !(_iteratorNormalCompletion3 = (_step3 = _iterator3.next()).done); _iteratorNormalCompletion3 = true){
                            var hitbox1 = _step3.value;
                            if (hitbox1.col !== col) {
                                col = hitbox1.col;
                                top = (0, _helpersSegmentJs.a1)(align, this.top + titleHeight + padding, this.bottom - this.columnSizes[col].height);
                            }
                            hitbox1.top = top;
                            hitbox1.left += this.left + padding;
                            hitbox1.left = rtlHelper.leftForLtr(rtlHelper.x(hitbox1.left), hitbox1.width);
                            top += hitbox1.height + padding;
                        }
                    } catch (err) {
                        _didIteratorError3 = true;
                        _iteratorError3 = err;
                    } finally{
                        try {
                            if (!_iteratorNormalCompletion3 && _iterator3.return != null) {
                                _iterator3.return();
                            }
                        } finally{
                            if (_didIteratorError3) {
                                throw _iteratorError3;
                            }
                        }
                    }
                }
            }
        },
        {
            key: "isHorizontal",
            value: function isHorizontal() {
                return this.options.position === "top" || this.options.position === "bottom";
            }
        },
        {
            key: "draw",
            value: function draw2() {
                if (this.options.display) {
                    var ctx = this.ctx;
                    (0, _helpersSegmentJs.X)(ctx, this);
                    this._draw();
                    (0, _helpersSegmentJs.Z)(ctx);
                }
            }
        },
        {
            key: "_draw",
            value: function _draw() {
                var _this = this;
                var ref = this, opts = ref.options, columnSizes = ref.columnSizes, lineWidths = ref.lineWidths, ctx = ref.ctx;
                var align = opts.align, labelOpts = opts.labels;
                var defaultColor = (0, _helpersSegmentJs.d).color;
                var rtlHelper = (0, _helpersSegmentJs.ay)(opts.rtl, this.left, this.width);
                var labelFont = (0, _helpersSegmentJs.$)(labelOpts.font);
                var fontColor = labelOpts.color, padding = labelOpts.padding;
                var fontSize = labelFont.size;
                var halfFontSize = fontSize / 2;
                var cursor;
                this.drawTitle();
                ctx.textAlign = rtlHelper.textAlign("left");
                ctx.textBaseline = "middle";
                ctx.lineWidth = 0.5;
                ctx.font = labelFont.string;
                var ref11 = getBoxSize(labelOpts, fontSize), boxWidth = ref11.boxWidth, boxHeight = ref11.boxHeight, itemHeight = ref11.itemHeight;
                var drawLegendBox = function drawLegendBox(x, y, legendItem) {
                    if (isNaN(boxWidth) || boxWidth <= 0 || isNaN(boxHeight) || boxHeight < 0) return;
                    ctx.save();
                    var lineWidth = (0, _helpersSegmentJs.v)(legendItem.lineWidth, 1);
                    ctx.fillStyle = (0, _helpersSegmentJs.v)(legendItem.fillStyle, defaultColor);
                    ctx.lineCap = (0, _helpersSegmentJs.v)(legendItem.lineCap, "butt");
                    ctx.lineDashOffset = (0, _helpersSegmentJs.v)(legendItem.lineDashOffset, 0);
                    ctx.lineJoin = (0, _helpersSegmentJs.v)(legendItem.lineJoin, "miter");
                    ctx.lineWidth = lineWidth;
                    ctx.strokeStyle = (0, _helpersSegmentJs.v)(legendItem.strokeStyle, defaultColor);
                    ctx.setLineDash((0, _helpersSegmentJs.v)(legendItem.lineDash, []));
                    if (labelOpts.usePointStyle) {
                        var drawOptions = {
                            radius: boxWidth * Math.SQRT2 / 2,
                            pointStyle: legendItem.pointStyle,
                            rotation: legendItem.rotation,
                            borderWidth: lineWidth
                        };
                        var centerX = rtlHelper.xPlus(x, boxWidth / 2);
                        var centerY = y + halfFontSize;
                        (0, _helpersSegmentJs.as)(ctx, drawOptions, centerX, centerY);
                    } else {
                        var yBoxTop = y + Math.max((fontSize - boxHeight) / 2, 0);
                        var xBoxLeft = rtlHelper.leftForLtr(x, boxWidth);
                        var borderRadius = (0, _helpersSegmentJs.av)(legendItem.borderRadius);
                        ctx.beginPath();
                        if (Object.values(borderRadius).some(function(v) {
                            return v !== 0;
                        })) (0, _helpersSegmentJs.at)(ctx, {
                            x: xBoxLeft,
                            y: yBoxTop,
                            w: boxWidth,
                            h: boxHeight,
                            radius: borderRadius
                        });
                        else ctx.rect(xBoxLeft, yBoxTop, boxWidth, boxHeight);
                        ctx.fill();
                        if (lineWidth !== 0) ctx.stroke();
                    }
                    ctx.restore();
                };
                var fillText = function fillText(x, y, legendItem) {
                    (0, _helpersSegmentJs.Y)(ctx, legendItem.text, x, y + itemHeight / 2, labelFont, {
                        strikethrough: legendItem.hidden,
                        textAlign: rtlHelper.textAlign(legendItem.textAlign)
                    });
                };
                var isHorizontal = this.isHorizontal();
                var titleHeight = this._computeTitleHeight();
                if (isHorizontal) cursor = {
                    x: (0, _helpersSegmentJs.a1)(align, this.left + padding, this.right - lineWidths[0]),
                    y: this.top + padding + titleHeight,
                    line: 0
                };
                else cursor = {
                    x: this.left + padding,
                    y: (0, _helpersSegmentJs.a1)(align, this.top + titleHeight + padding, this.bottom - columnSizes[0].height),
                    line: 0
                };
                (0, _helpersSegmentJs.az)(this.ctx, opts.textDirection);
                var lineHeight = itemHeight + padding;
                this.legendItems.forEach(function(legendItem, i) {
                    ctx.strokeStyle = legendItem.fontColor || fontColor;
                    ctx.fillStyle = legendItem.fontColor || fontColor;
                    var textWidth = ctx.measureText(legendItem.text).width;
                    var textAlign = rtlHelper.textAlign(legendItem.textAlign || (legendItem.textAlign = labelOpts.textAlign));
                    var width = boxWidth + halfFontSize + textWidth;
                    var x = cursor.x;
                    var y = cursor.y;
                    rtlHelper.setWidth(_this.width);
                    if (isHorizontal) {
                        if (i > 0 && x + width + padding > _this.right) {
                            y = cursor.y += lineHeight;
                            cursor.line++;
                            x = cursor.x = (0, _helpersSegmentJs.a1)(align, _this.left + padding, _this.right - lineWidths[cursor.line]);
                        }
                    } else if (i > 0 && y + lineHeight > _this.bottom) {
                        x = cursor.x = x + columnSizes[cursor.line].width + padding;
                        cursor.line++;
                        y = cursor.y = (0, _helpersSegmentJs.a1)(align, _this.top + titleHeight + padding, _this.bottom - columnSizes[cursor.line].height);
                    }
                    var realX = rtlHelper.x(x);
                    drawLegendBox(realX, y, legendItem);
                    x = (0, _helpersSegmentJs.aA)(textAlign, x + boxWidth + halfFontSize, isHorizontal ? x + width : _this.right, opts.rtl);
                    fillText(rtlHelper.x(x), y, legendItem);
                    if (isHorizontal) cursor.x += width + padding;
                    else cursor.y += lineHeight;
                });
                (0, _helpersSegmentJs.aB)(this.ctx, opts.textDirection);
            }
        },
        {
            key: "drawTitle",
            value: function drawTitle() {
                var opts = this.options;
                var titleOpts = opts.title;
                var titleFont = (0, _helpersSegmentJs.$)(titleOpts.font);
                var titlePadding = (0, _helpersSegmentJs.D)(titleOpts.padding);
                if (!titleOpts.display) return;
                var rtlHelper = (0, _helpersSegmentJs.ay)(opts.rtl, this.left, this.width);
                var ctx = this.ctx;
                var position = titleOpts.position;
                var halfFontSize = titleFont.size / 2;
                var topPaddingPlusHalfFontSize = titlePadding.top + halfFontSize;
                var y;
                var left = this.left;
                var maxWidth = this.width;
                if (this.isHorizontal()) {
                    var _Math;
                    maxWidth = (_Math = Math).max.apply(_Math, (0, _toConsumableArrayJsDefault.default)(this.lineWidths));
                    y = this.top + topPaddingPlusHalfFontSize;
                    left = (0, _helpersSegmentJs.a1)(opts.align, left, this.right - maxWidth);
                } else {
                    var maxHeight = this.columnSizes.reduce(function(acc, size) {
                        return Math.max(acc, size.height);
                    }, 0);
                    y = topPaddingPlusHalfFontSize + (0, _helpersSegmentJs.a1)(opts.align, this.top, this.bottom - maxHeight - opts.labels.padding - this._computeTitleHeight());
                }
                var x = (0, _helpersSegmentJs.a1)(position, left, left + maxWidth);
                ctx.textAlign = rtlHelper.textAlign((0, _helpersSegmentJs.a0)(position));
                ctx.textBaseline = "middle";
                ctx.strokeStyle = titleOpts.color;
                ctx.fillStyle = titleOpts.color;
                ctx.font = titleFont.string;
                (0, _helpersSegmentJs.Y)(ctx, titleOpts.text, x, y, titleFont);
            }
        },
        {
            key: "_computeTitleHeight",
            value: function _computeTitleHeight() {
                var titleOpts = this.options.title;
                var titleFont = (0, _helpersSegmentJs.$)(titleOpts.font);
                var titlePadding = (0, _helpersSegmentJs.D)(titleOpts.padding);
                return titleOpts.display ? titleFont.lineHeight + titlePadding.height : 0;
            }
        },
        {
            key: "_getLegendItemAt",
            value: function _getLegendItemAt(x, y) {
                var i, hitBox, lh;
                if ((0, _helpersSegmentJs.ai)(x, this.left, this.right) && (0, _helpersSegmentJs.ai)(y, this.top, this.bottom)) {
                    lh = this.legendHitBoxes;
                    for(i = 0; i < lh.length; ++i){
                        hitBox = lh[i];
                        if ((0, _helpersSegmentJs.ai)(x, hitBox.left, hitBox.left + hitBox.width) && (0, _helpersSegmentJs.ai)(y, hitBox.top, hitBox.top + hitBox.height)) return this.legendItems[i];
                    }
                }
                return null;
            }
        },
        {
            key: "handleEvent",
            value: function handleEvent(e) {
                var opts = this.options;
                if (!isListened(e.type, opts)) return;
                var hoveredItem = this._getLegendItemAt(e.x, e.y);
                if (e.type === "mousemove" || e.type === "mouseout") {
                    var previous = this._hoveredItem;
                    var sameItem = itemsEqual(previous, hoveredItem);
                    if (previous && !sameItem) (0, _helpersSegmentJs.Q)(opts.onLeave, [
                        e,
                        previous,
                        this
                    ], this);
                    this._hoveredItem = hoveredItem;
                    if (hoveredItem && !sameItem) (0, _helpersSegmentJs.Q)(opts.onHover, [
                        e,
                        hoveredItem,
                        this
                    ], this);
                } else if (hoveredItem) (0, _helpersSegmentJs.Q)(opts.onClick, [
                    e,
                    hoveredItem,
                    this
                ], this);
            }
        }
    ]);
    return Legend;
}((0, _wrapNativeSuperJsDefault.default)(Element));
function isListened(type, opts) {
    if ((type === "mousemove" || type === "mouseout") && (opts.onHover || opts.onLeave)) return true;
    if (opts.onClick && (type === "click" || type === "mouseup")) return true;
    return false;
}
var plugin_legend = {
    id: "legend",
    _element: Legend,
    start: function(chart, _args, options) {
        var legend = chart.legend = new Legend({
            ctx: chart.ctx,
            options: options,
            chart: chart
        });
        layouts.configure(chart, legend, options);
        layouts.addBox(chart, legend);
    },
    stop: function(chart) {
        layouts.removeBox(chart, chart.legend);
        delete chart.legend;
    },
    beforeUpdate: function(chart, _args, options) {
        var legend = chart.legend;
        layouts.configure(chart, legend, options);
        legend.options = options;
    },
    afterUpdate: function(chart) {
        var legend = chart.legend;
        legend.buildLabels();
        legend.adjustHitBoxes();
    },
    afterEvent: function(chart, args) {
        if (!args.replay) chart.legend.handleEvent(args.event);
    },
    defaults: {
        display: true,
        position: "top",
        align: "center",
        fullSize: true,
        reverse: false,
        weight: 1000,
        onClick: function(e, legendItem, legend) {
            var index63 = legendItem.datasetIndex;
            var ci = legend.chart;
            if (ci.isDatasetVisible(index63)) {
                ci.hide(index63);
                legendItem.hidden = true;
            } else {
                ci.show(index63);
                legendItem.hidden = false;
            }
        },
        onHover: null,
        onLeave: null,
        labels: {
            color: function(ctx) {
                return ctx.chart.options.color;
            },
            boxWidth: 40,
            padding: 10,
            generateLabels: function(chart) {
                var datasets = chart.data.datasets;
                var _options = chart.legend.options, _labels = _options.labels, usePointStyle = _labels.usePointStyle, pointStyle = _labels.pointStyle, textAlign = _labels.textAlign, color = _labels.color;
                return chart._getSortedDatasetMetas().map(function(meta) {
                    var style = meta.controller.getStyle(usePointStyle ? 0 : undefined);
                    var borderWidth = (0, _helpersSegmentJs.D)(style.borderWidth);
                    return {
                        text: datasets[meta.index].label,
                        fillStyle: style.backgroundColor,
                        fontColor: color,
                        hidden: !meta.visible,
                        lineCap: style.borderCapStyle,
                        lineDash: style.borderDash,
                        lineDashOffset: style.borderDashOffset,
                        lineJoin: style.borderJoinStyle,
                        lineWidth: (borderWidth.width + borderWidth.height) / 4,
                        strokeStyle: style.borderColor,
                        pointStyle: pointStyle || style.pointStyle,
                        rotation: style.rotation,
                        textAlign: textAlign || style.textAlign,
                        borderRadius: 0,
                        datasetIndex: meta.index
                    };
                }, this);
            }
        },
        title: {
            color: function(ctx) {
                return ctx.chart.options.color;
            },
            display: false,
            position: "center",
            text: ""
        }
    },
    descriptors: {
        _scriptable: function(name) {
            return !name.startsWith("on");
        },
        labels: {
            _scriptable: function(name) {
                return ![
                    "generateLabels",
                    "filter",
                    "sort"
                ].includes(name);
            }
        }
    }
};
var Title = /*#__PURE__*/ function(Element) {
    "use strict";
    (0, _inheritsJsDefault.default)(Title, Element);
    var _super = (0, _createSuperJsDefault.default)(Title);
    function Title(config) {
        (0, _classCallCheckJsDefault.default)(this, Title);
        var _this;
        _this = _super.call(this);
        _this.chart = config.chart;
        _this.options = config.options;
        _this.ctx = config.ctx;
        _this._padding = undefined;
        _this.top = undefined;
        _this.bottom = undefined;
        _this.left = undefined;
        _this.right = undefined;
        _this.width = undefined;
        _this.height = undefined;
        _this.position = undefined;
        _this.weight = undefined;
        _this.fullSize = undefined;
        return _this;
    }
    (0, _createClassJsDefault.default)(Title, [
        {
            key: "update",
            value: function update(maxWidth, maxHeight) {
                var opts = this.options;
                this.left = 0;
                this.top = 0;
                if (!opts.display) {
                    this.width = this.height = this.right = this.bottom = 0;
                    return;
                }
                this.width = this.right = maxWidth;
                this.height = this.bottom = maxHeight;
                var lineCount = (0, _helpersSegmentJs.b)(opts.text) ? opts.text.length : 1;
                this._padding = (0, _helpersSegmentJs.D)(opts.padding);
                var textSize = lineCount * (0, _helpersSegmentJs.$)(opts.font).lineHeight + this._padding.height;
                if (this.isHorizontal()) this.height = textSize;
                else this.width = textSize;
            }
        },
        {
            key: "isHorizontal",
            value: function isHorizontal() {
                var pos = this.options.position;
                return pos === "top" || pos === "bottom";
            }
        },
        {
            key: "_drawArgs",
            value: function _drawArgs(offset) {
                var ref = this, top = ref.top, left = ref.left, bottom = ref.bottom, right = ref.right, options = ref.options;
                var align = options.align;
                var rotation = 0;
                var maxWidth, titleX, titleY;
                if (this.isHorizontal()) {
                    titleX = (0, _helpersSegmentJs.a1)(align, left, right);
                    titleY = top + offset;
                    maxWidth = right - left;
                } else {
                    if (options.position === "left") {
                        titleX = left + offset;
                        titleY = (0, _helpersSegmentJs.a1)(align, bottom, top);
                        rotation = (0, _helpersSegmentJs.P) * -0.5;
                    } else {
                        titleX = right - offset;
                        titleY = (0, _helpersSegmentJs.a1)(align, top, bottom);
                        rotation = (0, _helpersSegmentJs.P) * 0.5;
                    }
                    maxWidth = bottom - top;
                }
                return {
                    titleX: titleX,
                    titleY: titleY,
                    maxWidth: maxWidth,
                    rotation: rotation
                };
            }
        },
        {
            key: "draw",
            value: function draw2() {
                var ctx = this.ctx;
                var opts = this.options;
                if (!opts.display) return;
                var fontOpts = (0, _helpersSegmentJs.$)(opts.font);
                var lineHeight = fontOpts.lineHeight;
                var offset = lineHeight / 2 + this._padding.top;
                var ref = this._drawArgs(offset), titleX = ref.titleX, titleY = ref.titleY, maxWidth = ref.maxWidth, rotation = ref.rotation;
                (0, _helpersSegmentJs.Y)(ctx, opts.text, 0, 0, fontOpts, {
                    color: opts.color,
                    maxWidth: maxWidth,
                    rotation: rotation,
                    textAlign: (0, _helpersSegmentJs.a0)(opts.align),
                    textBaseline: "middle",
                    translation: [
                        titleX,
                        titleY
                    ]
                });
            }
        }
    ]);
    return Title;
}((0, _wrapNativeSuperJsDefault.default)(Element));
function createTitle(chart, titleOpts) {
    var title = new Title({
        ctx: chart.ctx,
        options: titleOpts,
        chart: chart
    });
    layouts.configure(chart, title, titleOpts);
    layouts.addBox(chart, title);
    chart.titleBlock = title;
}
var plugin_title = {
    id: "title",
    _element: Title,
    start: function(chart, _args, options) {
        createTitle(chart, options);
    },
    stop: function(chart) {
        var titleBlock = chart.titleBlock;
        layouts.removeBox(chart, titleBlock);
        delete chart.titleBlock;
    },
    beforeUpdate: function(chart, _args, options) {
        var title = chart.titleBlock;
        layouts.configure(chart, title, options);
        title.options = options;
    },
    defaults: {
        align: "center",
        display: false,
        font: {
            weight: "bold"
        },
        fullSize: true,
        padding: 10,
        position: "top",
        text: "",
        weight: 2000
    },
    defaultRoutes: {
        color: "color"
    },
    descriptors: {
        _scriptable: true,
        _indexable: false
    }
};
var map = new WeakMap();
var plugin_subtitle = {
    id: "subtitle",
    start: function(chart, _args, options) {
        var title = new Title({
            ctx: chart.ctx,
            options: options,
            chart: chart
        });
        layouts.configure(chart, title, options);
        layouts.addBox(chart, title);
        map.set(chart, title);
    },
    stop: function(chart) {
        layouts.removeBox(chart, map.get(chart));
        map.delete(chart);
    },
    beforeUpdate: function(chart, _args, options) {
        var title = map.get(chart);
        layouts.configure(chart, title, options);
        title.options = options;
    },
    defaults: {
        align: "center",
        display: false,
        font: {
            weight: "normal"
        },
        fullSize: true,
        padding: 0,
        position: "top",
        text: "",
        weight: 1500
    },
    defaultRoutes: {
        color: "color"
    },
    descriptors: {
        _scriptable: true,
        _indexable: false
    }
};
var positioners = {
    average: function(items) {
        if (!items.length) return false;
        var i, len;
        var x = 0;
        var y = 0;
        var count = 0;
        for(i = 0, len = items.length; i < len; ++i){
            var el = items[i].element;
            if (el && el.hasValue()) {
                var pos = el.tooltipPosition();
                x += pos.x;
                y += pos.y;
                ++count;
            }
        }
        return {
            x: x / count,
            y: y / count
        };
    },
    nearest: function(items, eventPosition) {
        if (!items.length) return false;
        var x = eventPosition.x;
        var y = eventPosition.y;
        var minDistance = Number.POSITIVE_INFINITY;
        var i, len, nearestElement;
        for(i = 0, len = items.length; i < len; ++i){
            var el = items[i].element;
            if (el && el.hasValue()) {
                var center = el.getCenterPoint();
                var d = (0, _helpersSegmentJs.aD)(eventPosition, center);
                if (d < minDistance) {
                    minDistance = d;
                    nearestElement = el;
                }
            }
        }
        if (nearestElement) {
            var tp = nearestElement.tooltipPosition();
            x = tp.x;
            y = tp.y;
        }
        return {
            x: x,
            y: y
        };
    }
};
function pushOrConcat(base, toPush) {
    if (toPush) {
        if ((0, _helpersSegmentJs.b)(toPush)) Array.prototype.push.apply(base, toPush);
        else base.push(toPush);
    }
    return base;
}
function splitNewlines(str) {
    if ((typeof str === "string" || str instanceof String) && str.indexOf("\n") > -1) return str.split("\n");
    return str;
}
function createTooltipItem(chart, item) {
    var element = item.element, datasetIndex = item.datasetIndex, index64 = item.index;
    var controller = chart.getDatasetMeta(datasetIndex).controller;
    var ref = controller.getLabelAndValue(index64), label = ref.label, value = ref.value;
    return {
        chart: chart,
        label: label,
        parsed: controller.getParsed(index64),
        raw: chart.data.datasets[datasetIndex].data[index64],
        formattedValue: value,
        dataset: controller.getDataset(),
        dataIndex: index64,
        datasetIndex: datasetIndex,
        element: element
    };
}
function getTooltipSize(tooltip, options) {
    var ctx = tooltip.chart.ctx;
    var body = tooltip.body, footer = tooltip.footer, title = tooltip.title;
    var boxWidth = options.boxWidth, boxHeight = options.boxHeight;
    var bodyFont = (0, _helpersSegmentJs.$)(options.bodyFont);
    var titleFont = (0, _helpersSegmentJs.$)(options.titleFont);
    var footerFont = (0, _helpersSegmentJs.$)(options.footerFont);
    var titleLineCount = title.length;
    var footerLineCount = footer.length;
    var bodyLineItemCount = body.length;
    var padding = (0, _helpersSegmentJs.D)(options.padding);
    var height = padding.height;
    var width = 0;
    var combinedBodyLength = body.reduce(function(count, bodyItem) {
        return count + bodyItem.before.length + bodyItem.lines.length + bodyItem.after.length;
    }, 0);
    combinedBodyLength += tooltip.beforeBody.length + tooltip.afterBody.length;
    if (titleLineCount) height += titleLineCount * titleFont.lineHeight + (titleLineCount - 1) * options.titleSpacing + options.titleMarginBottom;
    if (combinedBodyLength) {
        var bodyLineHeight = options.displayColors ? Math.max(boxHeight, bodyFont.lineHeight) : bodyFont.lineHeight;
        height += bodyLineItemCount * bodyLineHeight + (combinedBodyLength - bodyLineItemCount) * bodyFont.lineHeight + (combinedBodyLength - 1) * options.bodySpacing;
    }
    if (footerLineCount) height += options.footerMarginTop + footerLineCount * footerFont.lineHeight + (footerLineCount - 1) * options.footerSpacing;
    var widthPadding = 0;
    var maxLineWidth = function maxLineWidth(line) {
        width = Math.max(width, ctx.measureText(line).width + widthPadding);
    };
    ctx.save();
    ctx.font = titleFont.string;
    (0, _helpersSegmentJs.E)(tooltip.title, maxLineWidth);
    ctx.font = bodyFont.string;
    (0, _helpersSegmentJs.E)(tooltip.beforeBody.concat(tooltip.afterBody), maxLineWidth);
    widthPadding = options.displayColors ? boxWidth + 2 + options.boxPadding : 0;
    (0, _helpersSegmentJs.E)(body, function(bodyItem) {
        (0, _helpersSegmentJs.E)(bodyItem.before, maxLineWidth);
        (0, _helpersSegmentJs.E)(bodyItem.lines, maxLineWidth);
        (0, _helpersSegmentJs.E)(bodyItem.after, maxLineWidth);
    });
    widthPadding = 0;
    ctx.font = footerFont.string;
    (0, _helpersSegmentJs.E)(tooltip.footer, maxLineWidth);
    ctx.restore();
    width += padding.width;
    return {
        width: width,
        height: height
    };
}
function determineYAlign(chart, size) {
    var y = size.y, height = size.height;
    if (y < height / 2) return "top";
    else if (y > chart.height - height / 2) return "bottom";
    return "center";
}
function doesNotFitWithAlign(xAlign, chart, options, size) {
    var x = size.x, width = size.width;
    var caret = options.caretSize + options.caretPadding;
    if (xAlign === "left" && x + width + caret > chart.width) return true;
    if (xAlign === "right" && x - width - caret < 0) return true;
}
function determineXAlign(chart, options, size, yAlign) {
    var x = size.x, width = size.width;
    var chartWidth = chart.width, _chartArea = chart.chartArea, left = _chartArea.left, right = _chartArea.right;
    var xAlign = "center";
    if (yAlign === "center") xAlign = x <= (left + right) / 2 ? "left" : "right";
    else if (x <= width / 2) xAlign = "left";
    else if (x >= chartWidth - width / 2) xAlign = "right";
    if (doesNotFitWithAlign(xAlign, chart, options, size)) xAlign = "center";
    return xAlign;
}
function determineAlignment(chart, options, size) {
    var yAlign = size.yAlign || options.yAlign || determineYAlign(chart, size);
    return {
        xAlign: size.xAlign || options.xAlign || determineXAlign(chart, options, size, yAlign),
        yAlign: yAlign
    };
}
function alignX(size, xAlign) {
    var x = size.x, width = size.width;
    if (xAlign === "right") x -= width;
    else if (xAlign === "center") x -= width / 2;
    return x;
}
function alignY(size, yAlign, paddingAndSize) {
    var y = size.y, height = size.height;
    if (yAlign === "top") y += paddingAndSize;
    else if (yAlign === "bottom") y -= height + paddingAndSize;
    else y -= height / 2;
    return y;
}
function getBackgroundPoint(options, size, alignment, chart) {
    var caretSize = options.caretSize, caretPadding = options.caretPadding, cornerRadius = options.cornerRadius;
    var xAlign = alignment.xAlign, yAlign = alignment.yAlign;
    var paddingAndSize = caretSize + caretPadding;
    var ref = (0, _helpersSegmentJs.av)(cornerRadius), topLeft = ref.topLeft, topRight = ref.topRight, bottomLeft = ref.bottomLeft, bottomRight = ref.bottomRight;
    var x = alignX(size, xAlign);
    var y = alignY(size, yAlign, paddingAndSize);
    if (yAlign === "center") {
        if (xAlign === "left") x += paddingAndSize;
        else if (xAlign === "right") x -= paddingAndSize;
    } else if (xAlign === "left") x -= Math.max(topLeft, bottomLeft) + caretSize;
    else if (xAlign === "right") x += Math.max(topRight, bottomRight) + caretSize;
    return {
        x: (0, _helpersSegmentJs.w)(x, 0, chart.width - size.width),
        y: (0, _helpersSegmentJs.w)(y, 0, chart.height - size.height)
    };
}
function getAlignedX(tooltip, align, options) {
    var padding = (0, _helpersSegmentJs.D)(options.padding);
    return align === "center" ? tooltip.x + tooltip.width / 2 : align === "right" ? tooltip.x + tooltip.width - padding.right : tooltip.x + padding.left;
}
function getBeforeAfterBodyLines(callback) {
    return pushOrConcat([], splitNewlines(callback));
}
function createTooltipContext(parent, tooltip, tooltipItems) {
    return (0, _helpersSegmentJs.h)(parent, {
        tooltip: tooltip,
        tooltipItems: tooltipItems,
        type: "tooltip"
    });
}
function overrideCallbacks(callbacks, context) {
    var override = context && context.dataset && context.dataset.tooltip && context.dataset.tooltip.callbacks;
    return override ? callbacks.override(override) : callbacks;
}
var Tooltip = /*#__PURE__*/ function(Element) {
    "use strict";
    (0, _inheritsJsDefault.default)(Tooltip, Element);
    var _super = (0, _createSuperJsDefault.default)(Tooltip);
    function Tooltip(config) {
        (0, _classCallCheckJsDefault.default)(this, Tooltip);
        var _this;
        _this = _super.call(this);
        _this.opacity = 0;
        _this._active = [];
        _this._eventPosition = undefined;
        _this._size = undefined;
        _this._cachedAnimations = undefined;
        _this._tooltipItems = [];
        _this.$animations = undefined;
        _this.$context = undefined;
        _this.chart = config.chart || config._chart;
        _this._chart = _this.chart;
        _this.options = config.options;
        _this.dataPoints = undefined;
        _this.title = undefined;
        _this.beforeBody = undefined;
        _this.body = undefined;
        _this.afterBody = undefined;
        _this.footer = undefined;
        _this.xAlign = undefined;
        _this.yAlign = undefined;
        _this.x = undefined;
        _this.y = undefined;
        _this.height = undefined;
        _this.width = undefined;
        _this.caretX = undefined;
        _this.caretY = undefined;
        _this.labelColors = undefined;
        _this.labelPointStyles = undefined;
        _this.labelTextColors = undefined;
        return _this;
    }
    (0, _createClassJsDefault.default)(Tooltip, [
        {
            key: "initialize",
            value: function initialize(options) {
                this.options = options;
                this._cachedAnimations = undefined;
                this.$context = undefined;
            }
        },
        {
            key: "_resolveAnimations",
            value: function _resolveAnimations() {
                var cached = this._cachedAnimations;
                if (cached) return cached;
                var chart = this.chart;
                var options = this.options.setContext(this.getContext());
                var opts = options.enabled && chart.options.animation && options.animations;
                var animations = new Animations(this.chart, opts);
                if (opts._cacheable) this._cachedAnimations = Object.freeze(animations);
                return animations;
            }
        },
        {
            key: "getContext",
            value: function getContext() {
                return this.$context || (this.$context = createTooltipContext(this.chart.getContext(), this, this._tooltipItems));
            }
        },
        {
            key: "getTitle",
            value: function getTitle(context, options) {
                var callbacks = options.callbacks;
                var beforeTitle = callbacks.beforeTitle.apply(this, [
                    context
                ]);
                var title = callbacks.title.apply(this, [
                    context
                ]);
                var afterTitle = callbacks.afterTitle.apply(this, [
                    context
                ]);
                var lines = [];
                lines = pushOrConcat(lines, splitNewlines(beforeTitle));
                lines = pushOrConcat(lines, splitNewlines(title));
                lines = pushOrConcat(lines, splitNewlines(afterTitle));
                return lines;
            }
        },
        {
            key: "getBeforeBody",
            value: function getBeforeBody(tooltipItems, options) {
                return getBeforeAfterBodyLines(options.callbacks.beforeBody.apply(this, [
                    tooltipItems
                ]));
            }
        },
        {
            key: "getBody",
            value: function getBody(tooltipItems, options) {
                var _this = this;
                var callbacks = options.callbacks;
                var bodyItems = [];
                (0, _helpersSegmentJs.E)(tooltipItems, function(context) {
                    var bodyItem = {
                        before: [],
                        lines: [],
                        after: []
                    };
                    var scoped = overrideCallbacks(callbacks, context);
                    pushOrConcat(bodyItem.before, splitNewlines(scoped.beforeLabel.call(_this, context)));
                    pushOrConcat(bodyItem.lines, scoped.label.call(_this, context));
                    pushOrConcat(bodyItem.after, splitNewlines(scoped.afterLabel.call(_this, context)));
                    bodyItems.push(bodyItem);
                });
                return bodyItems;
            }
        },
        {
            key: "getAfterBody",
            value: function getAfterBody(tooltipItems, options) {
                return getBeforeAfterBodyLines(options.callbacks.afterBody.apply(this, [
                    tooltipItems
                ]));
            }
        },
        {
            key: "getFooter",
            value: function getFooter(tooltipItems, options) {
                var callbacks = options.callbacks;
                var beforeFooter = callbacks.beforeFooter.apply(this, [
                    tooltipItems
                ]);
                var footer = callbacks.footer.apply(this, [
                    tooltipItems
                ]);
                var afterFooter = callbacks.afterFooter.apply(this, [
                    tooltipItems
                ]);
                var lines = [];
                lines = pushOrConcat(lines, splitNewlines(beforeFooter));
                lines = pushOrConcat(lines, splitNewlines(footer));
                lines = pushOrConcat(lines, splitNewlines(afterFooter));
                return lines;
            }
        },
        {
            key: "_createItems",
            value: function _createItems(options) {
                var _this = this;
                var active = this._active;
                var data = this.chart.data;
                var labelColors = [];
                var labelPointStyles = [];
                var labelTextColors = [];
                var tooltipItems = [];
                var i, len;
                for(i = 0, len = active.length; i < len; ++i)tooltipItems.push(createTooltipItem(this.chart, active[i]));
                if (options.filter) tooltipItems = tooltipItems.filter(function(element, index65, array) {
                    return options.filter(element, index65, array, data);
                });
                if (options.itemSort) tooltipItems = tooltipItems.sort(function(a, b) {
                    return options.itemSort(a, b, data);
                });
                (0, _helpersSegmentJs.E)(tooltipItems, function(context) {
                    var scoped = overrideCallbacks(options.callbacks, context);
                    labelColors.push(scoped.labelColor.call(_this, context));
                    labelPointStyles.push(scoped.labelPointStyle.call(_this, context));
                    labelTextColors.push(scoped.labelTextColor.call(_this, context));
                });
                this.labelColors = labelColors;
                this.labelPointStyles = labelPointStyles;
                this.labelTextColors = labelTextColors;
                this.dataPoints = tooltipItems;
                return tooltipItems;
            }
        },
        {
            key: "update",
            value: function update(changed, replay) {
                var options = this.options.setContext(this.getContext());
                var active = this._active;
                var properties;
                var tooltipItems = [];
                if (!active.length) {
                    if (this.opacity !== 0) properties = {
                        opacity: 0
                    };
                } else {
                    var position = positioners[options.position].call(this, active, this._eventPosition);
                    tooltipItems = this._createItems(options);
                    this.title = this.getTitle(tooltipItems, options);
                    this.beforeBody = this.getBeforeBody(tooltipItems, options);
                    this.body = this.getBody(tooltipItems, options);
                    this.afterBody = this.getAfterBody(tooltipItems, options);
                    this.footer = this.getFooter(tooltipItems, options);
                    var size = this._size = getTooltipSize(this, options);
                    var positionAndSize = Object.assign({}, position, size);
                    var alignment = determineAlignment(this.chart, options, positionAndSize);
                    var backgroundPoint = getBackgroundPoint(options, positionAndSize, alignment, this.chart);
                    this.xAlign = alignment.xAlign;
                    this.yAlign = alignment.yAlign;
                    properties = {
                        opacity: 1,
                        x: backgroundPoint.x,
                        y: backgroundPoint.y,
                        width: size.width,
                        height: size.height,
                        caretX: position.x,
                        caretY: position.y
                    };
                }
                this._tooltipItems = tooltipItems;
                this.$context = undefined;
                if (properties) this._resolveAnimations().update(this, properties);
                if (changed && options.external) options.external.call(this, {
                    chart: this.chart,
                    tooltip: this,
                    replay: replay
                });
            }
        },
        {
            key: "drawCaret",
            value: function drawCaret(tooltipPoint, ctx, size, options) {
                var caretPosition = this.getCaretPosition(tooltipPoint, size, options);
                ctx.lineTo(caretPosition.x1, caretPosition.y1);
                ctx.lineTo(caretPosition.x2, caretPosition.y2);
                ctx.lineTo(caretPosition.x3, caretPosition.y3);
            }
        },
        {
            key: "getCaretPosition",
            value: function getCaretPosition(tooltipPoint, size, options) {
                var ref = this, xAlign = ref.xAlign, yAlign = ref.yAlign;
                var caretSize = options.caretSize, cornerRadius = options.cornerRadius;
                var ref12 = (0, _helpersSegmentJs.av)(cornerRadius), topLeft = ref12.topLeft, topRight = ref12.topRight, bottomLeft = ref12.bottomLeft, bottomRight = ref12.bottomRight;
                var ptX = tooltipPoint.x, ptY = tooltipPoint.y;
                var width = size.width, height = size.height;
                var x1, x2, x3, y1, y2, y3;
                if (yAlign === "center") {
                    y2 = ptY + height / 2;
                    if (xAlign === "left") {
                        x1 = ptX;
                        x2 = x1 - caretSize;
                        y1 = y2 + caretSize;
                        y3 = y2 - caretSize;
                    } else {
                        x1 = ptX + width;
                        x2 = x1 + caretSize;
                        y1 = y2 - caretSize;
                        y3 = y2 + caretSize;
                    }
                    x3 = x1;
                } else {
                    if (xAlign === "left") x2 = ptX + Math.max(topLeft, bottomLeft) + caretSize;
                    else if (xAlign === "right") x2 = ptX + width - Math.max(topRight, bottomRight) - caretSize;
                    else x2 = this.caretX;
                    if (yAlign === "top") {
                        y1 = ptY;
                        y2 = y1 - caretSize;
                        x1 = x2 - caretSize;
                        x3 = x2 + caretSize;
                    } else {
                        y1 = ptY + height;
                        y2 = y1 + caretSize;
                        x1 = x2 + caretSize;
                        x3 = x2 - caretSize;
                    }
                    y3 = y1;
                }
                return {
                    x1: x1,
                    x2: x2,
                    x3: x3,
                    y1: y1,
                    y2: y2,
                    y3: y3
                };
            }
        },
        {
            key: "drawTitle",
            value: function drawTitle(pt, ctx, options) {
                var title = this.title;
                var length = title.length;
                var titleFont, titleSpacing, i;
                if (length) {
                    var rtlHelper = (0, _helpersSegmentJs.ay)(options.rtl, this.x, this.width);
                    pt.x = getAlignedX(this, options.titleAlign, options);
                    ctx.textAlign = rtlHelper.textAlign(options.titleAlign);
                    ctx.textBaseline = "middle";
                    titleFont = (0, _helpersSegmentJs.$)(options.titleFont);
                    titleSpacing = options.titleSpacing;
                    ctx.fillStyle = options.titleColor;
                    ctx.font = titleFont.string;
                    for(i = 0; i < length; ++i){
                        ctx.fillText(title[i], rtlHelper.x(pt.x), pt.y + titleFont.lineHeight / 2);
                        pt.y += titleFont.lineHeight + titleSpacing;
                        if (i + 1 === length) pt.y += options.titleMarginBottom - titleSpacing;
                    }
                }
            }
        },
        {
            key: "_drawColorBox",
            value: function _drawColorBox(ctx, pt, i, rtlHelper, options) {
                var labelColors = this.labelColors[i];
                var labelPointStyle = this.labelPointStyles[i];
                var boxHeight = options.boxHeight, boxWidth = options.boxWidth, boxPadding = options.boxPadding;
                var bodyFont = (0, _helpersSegmentJs.$)(options.bodyFont);
                var colorX = getAlignedX(this, "left", options);
                var rtlColorX = rtlHelper.x(colorX);
                var yOffSet = boxHeight < bodyFont.lineHeight ? (bodyFont.lineHeight - boxHeight) / 2 : 0;
                var colorY = pt.y + yOffSet;
                if (options.usePointStyle) {
                    var drawOptions = {
                        radius: Math.min(boxWidth, boxHeight) / 2,
                        pointStyle: labelPointStyle.pointStyle,
                        rotation: labelPointStyle.rotation,
                        borderWidth: 1
                    };
                    var centerX = rtlHelper.leftForLtr(rtlColorX, boxWidth) + boxWidth / 2;
                    var centerY = colorY + boxHeight / 2;
                    ctx.strokeStyle = options.multiKeyBackground;
                    ctx.fillStyle = options.multiKeyBackground;
                    (0, _helpersSegmentJs.as)(ctx, drawOptions, centerX, centerY);
                    ctx.strokeStyle = labelColors.borderColor;
                    ctx.fillStyle = labelColors.backgroundColor;
                    (0, _helpersSegmentJs.as)(ctx, drawOptions, centerX, centerY);
                } else {
                    ctx.lineWidth = labelColors.borderWidth || 1;
                    ctx.strokeStyle = labelColors.borderColor;
                    ctx.setLineDash(labelColors.borderDash || []);
                    ctx.lineDashOffset = labelColors.borderDashOffset || 0;
                    var outerX = rtlHelper.leftForLtr(rtlColorX, boxWidth - boxPadding);
                    var innerX = rtlHelper.leftForLtr(rtlHelper.xPlus(rtlColorX, 1), boxWidth - boxPadding - 2);
                    var borderRadius = (0, _helpersSegmentJs.av)(labelColors.borderRadius);
                    if (Object.values(borderRadius).some(function(v) {
                        return v !== 0;
                    })) {
                        ctx.beginPath();
                        ctx.fillStyle = options.multiKeyBackground;
                        (0, _helpersSegmentJs.at)(ctx, {
                            x: outerX,
                            y: colorY,
                            w: boxWidth,
                            h: boxHeight,
                            radius: borderRadius
                        });
                        ctx.fill();
                        ctx.stroke();
                        ctx.fillStyle = labelColors.backgroundColor;
                        ctx.beginPath();
                        (0, _helpersSegmentJs.at)(ctx, {
                            x: innerX,
                            y: colorY + 1,
                            w: boxWidth - 2,
                            h: boxHeight - 2,
                            radius: borderRadius
                        });
                        ctx.fill();
                    } else {
                        ctx.fillStyle = options.multiKeyBackground;
                        ctx.fillRect(outerX, colorY, boxWidth, boxHeight);
                        ctx.strokeRect(outerX, colorY, boxWidth, boxHeight);
                        ctx.fillStyle = labelColors.backgroundColor;
                        ctx.fillRect(innerX, colorY + 1, boxWidth - 2, boxHeight - 2);
                    }
                }
                ctx.fillStyle = this.labelTextColors[i];
            }
        },
        {
            key: "drawBody",
            value: function drawBody(pt, ctx, options) {
                var body = this.body;
                var bodySpacing = options.bodySpacing, bodyAlign = options.bodyAlign, displayColors = options.displayColors, boxHeight = options.boxHeight, boxWidth = options.boxWidth, boxPadding = options.boxPadding;
                var bodyFont = (0, _helpersSegmentJs.$)(options.bodyFont);
                var bodyLineHeight = bodyFont.lineHeight;
                var xLinePadding = 0;
                var rtlHelper = (0, _helpersSegmentJs.ay)(options.rtl, this.x, this.width);
                var fillLineOfText = function fillLineOfText(line) {
                    ctx.fillText(line, rtlHelper.x(pt.x + xLinePadding), pt.y + bodyLineHeight / 2);
                    pt.y += bodyLineHeight + bodySpacing;
                };
                var bodyAlignForCalculation = rtlHelper.textAlign(bodyAlign);
                var bodyItem, textColor, lines, i, j, ilen, jlen;
                ctx.textAlign = bodyAlign;
                ctx.textBaseline = "middle";
                ctx.font = bodyFont.string;
                pt.x = getAlignedX(this, bodyAlignForCalculation, options);
                ctx.fillStyle = options.bodyColor;
                (0, _helpersSegmentJs.E)(this.beforeBody, fillLineOfText);
                xLinePadding = displayColors && bodyAlignForCalculation !== "right" ? bodyAlign === "center" ? boxWidth / 2 + boxPadding : boxWidth + 2 + boxPadding : 0;
                for(i = 0, ilen = body.length; i < ilen; ++i){
                    bodyItem = body[i];
                    textColor = this.labelTextColors[i];
                    ctx.fillStyle = textColor;
                    (0, _helpersSegmentJs.E)(bodyItem.before, fillLineOfText);
                    lines = bodyItem.lines;
                    if (displayColors && lines.length) {
                        this._drawColorBox(ctx, pt, i, rtlHelper, options);
                        bodyLineHeight = Math.max(bodyFont.lineHeight, boxHeight);
                    }
                    for(j = 0, jlen = lines.length; j < jlen; ++j){
                        fillLineOfText(lines[j]);
                        bodyLineHeight = bodyFont.lineHeight;
                    }
                    (0, _helpersSegmentJs.E)(bodyItem.after, fillLineOfText);
                }
                xLinePadding = 0;
                bodyLineHeight = bodyFont.lineHeight;
                (0, _helpersSegmentJs.E)(this.afterBody, fillLineOfText);
                pt.y -= bodySpacing;
            }
        },
        {
            key: "drawFooter",
            value: function drawFooter(pt, ctx, options) {
                var footer = this.footer;
                var length = footer.length;
                var footerFont, i;
                if (length) {
                    var rtlHelper = (0, _helpersSegmentJs.ay)(options.rtl, this.x, this.width);
                    pt.x = getAlignedX(this, options.footerAlign, options);
                    pt.y += options.footerMarginTop;
                    ctx.textAlign = rtlHelper.textAlign(options.footerAlign);
                    ctx.textBaseline = "middle";
                    footerFont = (0, _helpersSegmentJs.$)(options.footerFont);
                    ctx.fillStyle = options.footerColor;
                    ctx.font = footerFont.string;
                    for(i = 0; i < length; ++i){
                        ctx.fillText(footer[i], rtlHelper.x(pt.x), pt.y + footerFont.lineHeight / 2);
                        pt.y += footerFont.lineHeight + options.footerSpacing;
                    }
                }
            }
        },
        {
            key: "drawBackground",
            value: function drawBackground(pt, ctx, tooltipSize, options) {
                var ref = this, xAlign = ref.xAlign, yAlign = ref.yAlign;
                var x = pt.x, y = pt.y;
                var width = tooltipSize.width, height = tooltipSize.height;
                var ref13 = (0, _helpersSegmentJs.av)(options.cornerRadius), topLeft = ref13.topLeft, topRight = ref13.topRight, bottomLeft = ref13.bottomLeft, bottomRight = ref13.bottomRight;
                ctx.fillStyle = options.backgroundColor;
                ctx.strokeStyle = options.borderColor;
                ctx.lineWidth = options.borderWidth;
                ctx.beginPath();
                ctx.moveTo(x + topLeft, y);
                if (yAlign === "top") this.drawCaret(pt, ctx, tooltipSize, options);
                ctx.lineTo(x + width - topRight, y);
                ctx.quadraticCurveTo(x + width, y, x + width, y + topRight);
                if (yAlign === "center" && xAlign === "right") this.drawCaret(pt, ctx, tooltipSize, options);
                ctx.lineTo(x + width, y + height - bottomRight);
                ctx.quadraticCurveTo(x + width, y + height, x + width - bottomRight, y + height);
                if (yAlign === "bottom") this.drawCaret(pt, ctx, tooltipSize, options);
                ctx.lineTo(x + bottomLeft, y + height);
                ctx.quadraticCurveTo(x, y + height, x, y + height - bottomLeft);
                if (yAlign === "center" && xAlign === "left") this.drawCaret(pt, ctx, tooltipSize, options);
                ctx.lineTo(x, y + topLeft);
                ctx.quadraticCurveTo(x, y, x + topLeft, y);
                ctx.closePath();
                ctx.fill();
                if (options.borderWidth > 0) ctx.stroke();
            }
        },
        {
            key: "_updateAnimationTarget",
            value: function _updateAnimationTarget(options) {
                var chart = this.chart;
                var anims = this.$animations;
                var animX = anims && anims.x;
                var animY = anims && anims.y;
                if (animX || animY) {
                    var position = positioners[options.position].call(this, this._active, this._eventPosition);
                    if (!position) return;
                    var size = this._size = getTooltipSize(this, options);
                    var positionAndSize = Object.assign({}, position, this._size);
                    var alignment = determineAlignment(chart, options, positionAndSize);
                    var point = getBackgroundPoint(options, positionAndSize, alignment, chart);
                    if (animX._to !== point.x || animY._to !== point.y) {
                        this.xAlign = alignment.xAlign;
                        this.yAlign = alignment.yAlign;
                        this.width = size.width;
                        this.height = size.height;
                        this.caretX = position.x;
                        this.caretY = position.y;
                        this._resolveAnimations().update(this, point);
                    }
                }
            }
        },
        {
            key: "_willRender",
            value: function _willRender() {
                return !!this.opacity;
            }
        },
        {
            key: "draw",
            value: function draw2(ctx) {
                var options = this.options.setContext(this.getContext());
                var opacity = this.opacity;
                if (!opacity) return;
                this._updateAnimationTarget(options);
                var tooltipSize = {
                    width: this.width,
                    height: this.height
                };
                var pt = {
                    x: this.x,
                    y: this.y
                };
                opacity = Math.abs(opacity) < 1e-3 ? 0 : opacity;
                var padding = (0, _helpersSegmentJs.D)(options.padding);
                var hasTooltipContent = this.title.length || this.beforeBody.length || this.body.length || this.afterBody.length || this.footer.length;
                if (options.enabled && hasTooltipContent) {
                    ctx.save();
                    ctx.globalAlpha = opacity;
                    this.drawBackground(pt, ctx, tooltipSize, options);
                    (0, _helpersSegmentJs.az)(ctx, options.textDirection);
                    pt.y += padding.top;
                    this.drawTitle(pt, ctx, options);
                    this.drawBody(pt, ctx, options);
                    this.drawFooter(pt, ctx, options);
                    (0, _helpersSegmentJs.aB)(ctx, options.textDirection);
                    ctx.restore();
                }
            }
        },
        {
            key: "getActiveElements",
            value: function getActiveElements() {
                return this._active || [];
            }
        },
        {
            key: "setActiveElements",
            value: function setActiveElements(activeElements, eventPosition) {
                var _this = this;
                var lastActive = this._active;
                var active = activeElements.map(function(param) {
                    var datasetIndex = param.datasetIndex, index66 = param.index;
                    var meta = _this.chart.getDatasetMeta(datasetIndex);
                    if (!meta) throw new Error("Cannot find a dataset at index " + datasetIndex);
                    return {
                        datasetIndex: datasetIndex,
                        element: meta.data[index66],
                        index: index66
                    };
                });
                var changed = !(0, _helpersSegmentJs.ag)(lastActive, active);
                var positionChanged = this._positionChanged(active, eventPosition);
                if (changed || positionChanged) {
                    this._active = active;
                    this._eventPosition = eventPosition;
                    this._ignoreReplayEvents = true;
                    this.update(true);
                }
            }
        },
        {
            key: "handleEvent",
            value: function handleEvent(e, replay) {
                var inChartArea = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : true;
                if (replay && this._ignoreReplayEvents) return false;
                this._ignoreReplayEvents = false;
                var options = this.options;
                var lastActive = this._active || [];
                var active = this._getActiveElements(e, lastActive, replay, inChartArea);
                var positionChanged = this._positionChanged(active, e);
                var changed = replay || !(0, _helpersSegmentJs.ag)(active, lastActive) || positionChanged;
                if (changed) {
                    this._active = active;
                    if (options.enabled || options.external) {
                        this._eventPosition = {
                            x: e.x,
                            y: e.y
                        };
                        this.update(true, replay);
                    }
                }
                return changed;
            }
        },
        {
            key: "_getActiveElements",
            value: function _getActiveElements(e, lastActive, replay, inChartArea) {
                var options = this.options;
                if (e.type === "mouseout") return [];
                if (!inChartArea) return lastActive;
                var active = this.chart.getElementsAtEventForMode(e, options.mode, options, replay);
                if (options.reverse) active.reverse();
                return active;
            }
        },
        {
            key: "_positionChanged",
            value: function _positionChanged(active, e) {
                var ref = this, caretX = ref.caretX, caretY = ref.caretY, options = ref.options;
                var position = positioners[options.position].call(this, active, e);
                return position !== false && (caretX !== position.x || caretY !== position.y);
            }
        }
    ]);
    return Tooltip;
}((0, _wrapNativeSuperJsDefault.default)(Element));
Tooltip.positioners = positioners;
var plugin_tooltip = {
    id: "tooltip",
    _element: Tooltip,
    positioners: positioners,
    afterInit: function(chart, _args, options) {
        if (options) chart.tooltip = new Tooltip({
            chart: chart,
            options: options
        });
    },
    beforeUpdate: function(chart, _args, options) {
        if (chart.tooltip) chart.tooltip.initialize(options);
    },
    reset: function(chart, _args, options) {
        if (chart.tooltip) chart.tooltip.initialize(options);
    },
    afterDraw: function(chart) {
        var tooltip = chart.tooltip;
        if (tooltip && tooltip._willRender()) {
            var args = {
                tooltip: tooltip
            };
            if (chart.notifyPlugins("beforeTooltipDraw", args) === false) return;
            tooltip.draw(chart.ctx);
            chart.notifyPlugins("afterTooltipDraw", args);
        }
    },
    afterEvent: function(chart, args) {
        if (chart.tooltip) {
            var useFinalPosition = args.replay;
            if (chart.tooltip.handleEvent(args.event, useFinalPosition, args.inChartArea)) args.changed = true;
        }
    },
    defaults: {
        enabled: true,
        external: null,
        position: "average",
        backgroundColor: "rgba(0,0,0,0.8)",
        titleColor: "#fff",
        titleFont: {
            weight: "bold"
        },
        titleSpacing: 2,
        titleMarginBottom: 6,
        titleAlign: "left",
        bodyColor: "#fff",
        bodySpacing: 2,
        bodyFont: {},
        bodyAlign: "left",
        footerColor: "#fff",
        footerSpacing: 2,
        footerMarginTop: 6,
        footerFont: {
            weight: "bold"
        },
        footerAlign: "left",
        padding: 6,
        caretPadding: 2,
        caretSize: 5,
        cornerRadius: 6,
        boxHeight: function(ctx, opts) {
            return opts.bodyFont.size;
        },
        boxWidth: function(ctx, opts) {
            return opts.bodyFont.size;
        },
        multiKeyBackground: "#fff",
        displayColors: true,
        boxPadding: 0,
        borderColor: "rgba(0,0,0,0)",
        borderWidth: 0,
        animation: {
            duration: 400,
            easing: "easeOutQuart"
        },
        animations: {
            numbers: {
                type: "number",
                properties: [
                    "x",
                    "y",
                    "width",
                    "height",
                    "caretX",
                    "caretY"
                ]
            },
            opacity: {
                easing: "linear",
                duration: 200
            }
        },
        callbacks: {
            beforeTitle: (0, _helpersSegmentJs.aC),
            title: function(tooltipItems) {
                if (tooltipItems.length > 0) {
                    var item = tooltipItems[0];
                    var labels = item.chart.data.labels;
                    var labelCount = labels ? labels.length : 0;
                    if (this && this.options && this.options.mode === "dataset") return item.dataset.label || "";
                    else if (item.label) return item.label;
                    else if (labelCount > 0 && item.dataIndex < labelCount) return labels[item.dataIndex];
                }
                return "";
            },
            afterTitle: (0, _helpersSegmentJs.aC),
            beforeBody: (0, _helpersSegmentJs.aC),
            beforeLabel: (0, _helpersSegmentJs.aC),
            label: function(tooltipItem) {
                if (this && this.options && this.options.mode === "dataset") return tooltipItem.label + ": " + tooltipItem.formattedValue || tooltipItem.formattedValue;
                var label = tooltipItem.dataset.label || "";
                if (label) label += ": ";
                var value = tooltipItem.formattedValue;
                if (!(0, _helpersSegmentJs.k)(value)) label += value;
                return label;
            },
            labelColor: function(tooltipItem) {
                var meta = tooltipItem.chart.getDatasetMeta(tooltipItem.datasetIndex);
                var options = meta.controller.getStyle(tooltipItem.dataIndex);
                return {
                    borderColor: options.borderColor,
                    backgroundColor: options.backgroundColor,
                    borderWidth: options.borderWidth,
                    borderDash: options.borderDash,
                    borderDashOffset: options.borderDashOffset,
                    borderRadius: 0
                };
            },
            labelTextColor: function() {
                return this.options.bodyColor;
            },
            labelPointStyle: function(tooltipItem) {
                var meta = tooltipItem.chart.getDatasetMeta(tooltipItem.datasetIndex);
                var options = meta.controller.getStyle(tooltipItem.dataIndex);
                return {
                    pointStyle: options.pointStyle,
                    rotation: options.rotation
                };
            },
            afterLabel: (0, _helpersSegmentJs.aC),
            afterBody: (0, _helpersSegmentJs.aC),
            beforeFooter: (0, _helpersSegmentJs.aC),
            footer: (0, _helpersSegmentJs.aC),
            afterFooter: (0, _helpersSegmentJs.aC)
        }
    },
    defaultRoutes: {
        bodyFont: "font",
        footerFont: "font",
        titleFont: "font"
    },
    descriptors: {
        _scriptable: function(name) {
            return name !== "filter" && name !== "itemSort" && name !== "external";
        },
        _indexable: false,
        callbacks: {
            _scriptable: false,
            _indexable: false
        },
        animation: {
            _fallback: false
        },
        animations: {
            _fallback: "animation"
        }
    },
    additionalOptionScopes: [
        "interaction"
    ]
};
var plugins = /*#__PURE__*/ Object.freeze({
    __proto__: null,
    Decimation: plugin_decimation,
    Filler: index,
    Legend: plugin_legend,
    SubTitle: plugin_subtitle,
    Title: plugin_title,
    Tooltip: plugin_tooltip
});
var addIfString = function(labels, raw, index67, addedLabels) {
    if (typeof raw === "string") {
        index67 = labels.push(raw) - 1;
        addedLabels.unshift({
            index: index67,
            label: raw
        });
    } else if (isNaN(raw)) index67 = null;
    return index67;
};
function findOrAddLabel(labels, raw, index68, addedLabels) {
    var first = labels.indexOf(raw);
    if (first === -1) return addIfString(labels, raw, index68, addedLabels);
    var last = labels.lastIndexOf(raw);
    return first !== last ? index68 : first;
}
var validIndex = function(index69, max) {
    return index69 === null ? null : (0, _helpersSegmentJs.w)(Math.round(index69), 0, max);
};
var CategoryScale = /*#__PURE__*/ function(Scale) {
    "use strict";
    (0, _inheritsJsDefault.default)(CategoryScale, Scale);
    var _super = (0, _createSuperJsDefault.default)(CategoryScale);
    function CategoryScale(cfg) {
        (0, _classCallCheckJsDefault.default)(this, CategoryScale);
        var _this;
        _this = _super.call(this, cfg);
        _this._startValue = undefined;
        _this._valueRange = 0;
        _this._addedLabels = [];
        return _this;
    }
    (0, _createClassJsDefault.default)(CategoryScale, [
        {
            key: "init",
            value: function init(scaleOptions) {
                var added = this._addedLabels;
                if (added.length) {
                    var labels = this.getLabels();
                    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
                    try {
                        for(var _iterator = added[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                            var _value = _step.value, index70 = _value.index, label = _value.label;
                            if (labels[index70] === label) labels.splice(index70, 1);
                        }
                    } catch (err) {
                        _didIteratorError = true;
                        _iteratorError = err;
                    } finally{
                        try {
                            if (!_iteratorNormalCompletion && _iterator.return != null) {
                                _iterator.return();
                            }
                        } finally{
                            if (_didIteratorError) {
                                throw _iteratorError;
                            }
                        }
                    }
                    this._addedLabels = [];
                }
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(CategoryScale.prototype), "init", this).call(this, scaleOptions);
            }
        },
        {
            key: "parse",
            value: function parse1(raw, index71) {
                if ((0, _helpersSegmentJs.k)(raw)) return null;
                var labels = this.getLabels();
                index71 = isFinite(index71) && labels[index71] === raw ? index71 : findOrAddLabel(labels, raw, (0, _helpersSegmentJs.v)(index71, raw), this._addedLabels);
                return validIndex(index71, labels.length - 1);
            }
        },
        {
            key: "determineDataLimits",
            value: function determineDataLimits() {
                var ref = this.getUserBounds(), minDefined = ref.minDefined, maxDefined = ref.maxDefined;
                var ref14 = this.getMinMax(true), min = ref14.min, max = ref14.max;
                if (this.options.bounds === "ticks") {
                    if (!minDefined) min = 0;
                    if (!maxDefined) max = this.getLabels().length - 1;
                }
                this.min = min;
                this.max = max;
            }
        },
        {
            key: "buildTicks",
            value: function buildTicks() {
                var min = this.min;
                var max = this.max;
                var offset = this.options.offset;
                var ticks = [];
                var labels = this.getLabels();
                labels = min === 0 && max === labels.length - 1 ? labels : labels.slice(min, max + 1);
                this._valueRange = Math.max(labels.length - (offset ? 0 : 1), 1);
                this._startValue = this.min - (offset ? 0.5 : 0);
                for(var value = min; value <= max; value++)ticks.push({
                    value: value
                });
                return ticks;
            }
        },
        {
            key: "getLabelForValue",
            value: function getLabelForValue(value) {
                var labels = this.getLabels();
                if (value >= 0 && value < labels.length) return labels[value];
                return value;
            }
        },
        {
            key: "configure",
            value: function configure() {
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(CategoryScale.prototype), "configure", this).call(this);
                if (!this.isHorizontal()) this._reversePixels = !this._reversePixels;
            }
        },
        {
            key: "getPixelForValue",
            value: function getPixelForValue(value) {
                if (typeof value !== "number") value = this.parse(value);
                return value === null ? NaN : this.getPixelForDecimal((value - this._startValue) / this._valueRange);
            }
        },
        {
            key: "getPixelForTick",
            value: function getPixelForTick(index72) {
                var ticks = this.ticks;
                if (index72 < 0 || index72 > ticks.length - 1) return null;
                return this.getPixelForValue(ticks[index72].value);
            }
        },
        {
            key: "getValueForPixel",
            value: function getValueForPixel(pixel) {
                return Math.round(this._startValue + this.getDecimalForPixel(pixel) * this._valueRange);
            }
        },
        {
            key: "getBasePixel",
            value: function getBasePixel() {
                return this.bottom;
            }
        }
    ]);
    return CategoryScale;
}(Scale);
CategoryScale.id = "category";
CategoryScale.defaults = {
    ticks: {
        callback: CategoryScale.prototype.getLabelForValue
    }
};
function generateTicks$1(generationOptions, dataRange) {
    var ticks = [];
    var MIN_SPACING = 1e-14;
    var bounds = generationOptions.bounds, step = generationOptions.step, min = generationOptions.min, max = generationOptions.max, precision = generationOptions.precision, count = generationOptions.count, maxTicks = generationOptions.maxTicks, maxDigits = generationOptions.maxDigits, includeBounds = generationOptions.includeBounds;
    var unit = step || 1;
    var maxSpaces = maxTicks - 1;
    var rmin = dataRange.min, rmax = dataRange.max;
    var minDefined = !(0, _helpersSegmentJs.k)(min);
    var maxDefined = !(0, _helpersSegmentJs.k)(max);
    var countDefined = !(0, _helpersSegmentJs.k)(count);
    var minSpacing = (rmax - rmin) / (maxDigits + 1);
    var spacing = (0, _helpersSegmentJs.aF)((rmax - rmin) / maxSpaces / unit) * unit;
    var factor, niceMin, niceMax, numSpaces;
    if (spacing < MIN_SPACING && !minDefined && !maxDefined) return [
        {
            value: rmin
        },
        {
            value: rmax
        }
    ];
    numSpaces = Math.ceil(rmax / spacing) - Math.floor(rmin / spacing);
    if (numSpaces > maxSpaces) spacing = (0, _helpersSegmentJs.aF)(numSpaces * spacing / maxSpaces / unit) * unit;
    if (!(0, _helpersSegmentJs.k)(precision)) {
        factor = Math.pow(10, precision);
        spacing = Math.ceil(spacing * factor) / factor;
    }
    if (bounds === "ticks") {
        niceMin = Math.floor(rmin / spacing) * spacing;
        niceMax = Math.ceil(rmax / spacing) * spacing;
    } else {
        niceMin = rmin;
        niceMax = rmax;
    }
    if (minDefined && maxDefined && step && (0, _helpersSegmentJs.aG)((max - min) / step, spacing / 1000)) {
        numSpaces = Math.round(Math.min((max - min) / spacing, maxTicks));
        spacing = (max - min) / numSpaces;
        niceMin = min;
        niceMax = max;
    } else if (countDefined) {
        niceMin = minDefined ? min : niceMin;
        niceMax = maxDefined ? max : niceMax;
        numSpaces = count - 1;
        spacing = (niceMax - niceMin) / numSpaces;
    } else {
        numSpaces = (niceMax - niceMin) / spacing;
        if ((0, _helpersSegmentJs.aH)(numSpaces, Math.round(numSpaces), spacing / 1000)) numSpaces = Math.round(numSpaces);
        else numSpaces = Math.ceil(numSpaces);
    }
    var decimalPlaces = Math.max((0, _helpersSegmentJs.aI)(spacing), (0, _helpersSegmentJs.aI)(niceMin));
    factor = Math.pow(10, (0, _helpersSegmentJs.k)(precision) ? decimalPlaces : precision);
    niceMin = Math.round(niceMin * factor) / factor;
    niceMax = Math.round(niceMax * factor) / factor;
    var j = 0;
    if (minDefined) {
        if (includeBounds && niceMin !== min) {
            ticks.push({
                value: min
            });
            if (niceMin < min) j++;
            if ((0, _helpersSegmentJs.aH)(Math.round((niceMin + j * spacing) * factor) / factor, min, relativeLabelSize(min, minSpacing, generationOptions))) j++;
        } else if (niceMin < min) j++;
    }
    for(; j < numSpaces; ++j)ticks.push({
        value: Math.round((niceMin + j * spacing) * factor) / factor
    });
    if (maxDefined && includeBounds && niceMax !== max) {
        if (ticks.length && (0, _helpersSegmentJs.aH)(ticks[ticks.length - 1].value, max, relativeLabelSize(max, minSpacing, generationOptions))) ticks[ticks.length - 1].value = max;
        else ticks.push({
            value: max
        });
    } else if (!maxDefined || niceMax === max) ticks.push({
        value: niceMax
    });
    return ticks;
}
function relativeLabelSize(value, minSpacing, param) {
    var horizontal = param.horizontal, minRotation = param.minRotation;
    var rad = (0, _helpersSegmentJs.t)(minRotation);
    var ratio = (horizontal ? Math.sin(rad) : Math.cos(rad)) || 0.001;
    var length = 0.75 * minSpacing * ("" + value).length;
    return Math.min(minSpacing / ratio, length);
}
var LinearScaleBase = /*#__PURE__*/ function(Scale) {
    "use strict";
    (0, _inheritsJsDefault.default)(LinearScaleBase, Scale);
    var _super = (0, _createSuperJsDefault.default)(LinearScaleBase);
    function LinearScaleBase(cfg) {
        (0, _classCallCheckJsDefault.default)(this, LinearScaleBase);
        var _this;
        _this = _super.call(this, cfg);
        _this.start = undefined;
        _this.end = undefined;
        _this._startValue = undefined;
        _this._endValue = undefined;
        _this._valueRange = 0;
        return _this;
    }
    (0, _createClassJsDefault.default)(LinearScaleBase, [
        {
            key: "parse",
            value: function parse1(raw, index) {
                if ((0, _helpersSegmentJs.k)(raw)) return null;
                if ((typeof raw === "number" || raw instanceof Number) && !isFinite(+raw)) return null;
                return +raw;
            }
        },
        {
            key: "handleTickRangeOptions",
            value: function handleTickRangeOptions() {
                var beginAtZero = this.options.beginAtZero;
                var ref = this.getUserBounds(), minDefined = ref.minDefined, maxDefined = ref.maxDefined;
                var ref15 = this, min = ref15.min, max = ref15.max;
                var setMin = function(v) {
                    return min = minDefined ? min : v;
                };
                var setMax = function(v) {
                    return max = maxDefined ? max : v;
                };
                if (beginAtZero) {
                    var minSign = (0, _helpersSegmentJs.s)(min);
                    var maxSign = (0, _helpersSegmentJs.s)(max);
                    if (minSign < 0 && maxSign < 0) setMax(0);
                    else if (minSign > 0 && maxSign > 0) setMin(0);
                }
                if (min === max) {
                    var offset = 1;
                    if (max >= Number.MAX_SAFE_INTEGER || min <= Number.MIN_SAFE_INTEGER) offset = Math.abs(max * 0.05);
                    setMax(max + offset);
                    if (!beginAtZero) setMin(min - offset);
                }
                this.min = min;
                this.max = max;
            }
        },
        {
            key: "getTickLimit",
            value: function getTickLimit() {
                var tickOpts = this.options.ticks;
                var maxTicksLimit = tickOpts.maxTicksLimit, stepSize = tickOpts.stepSize;
                var maxTicks;
                if (stepSize) {
                    maxTicks = Math.ceil(this.max / stepSize) - Math.floor(this.min / stepSize) + 1;
                    if (maxTicks > 1000) {
                        console.warn("scales.".concat(this.id, ".ticks.stepSize: ").concat(stepSize, " would result generating up to ").concat(maxTicks, " ticks. Limiting to 1000."));
                        maxTicks = 1000;
                    }
                } else {
                    maxTicks = this.computeTickLimit();
                    maxTicksLimit = maxTicksLimit || 11;
                }
                if (maxTicksLimit) maxTicks = Math.min(maxTicksLimit, maxTicks);
                return maxTicks;
            }
        },
        {
            key: "computeTickLimit",
            value: function computeTickLimit() {
                return Number.POSITIVE_INFINITY;
            }
        },
        {
            key: "buildTicks",
            value: function buildTicks() {
                var opts = this.options;
                var tickOpts = opts.ticks;
                var maxTicks = this.getTickLimit();
                maxTicks = Math.max(2, maxTicks);
                var numericGeneratorOptions = {
                    maxTicks: maxTicks,
                    bounds: opts.bounds,
                    min: opts.min,
                    max: opts.max,
                    precision: tickOpts.precision,
                    step: tickOpts.stepSize,
                    count: tickOpts.count,
                    maxDigits: this._maxDigits(),
                    horizontal: this.isHorizontal(),
                    minRotation: tickOpts.minRotation || 0,
                    includeBounds: tickOpts.includeBounds !== false
                };
                var dataRange = this._range || this;
                var ticks = generateTicks$1(numericGeneratorOptions, dataRange);
                if (opts.bounds === "ticks") (0, _helpersSegmentJs.aE)(ticks, this, "value");
                if (opts.reverse) {
                    ticks.reverse();
                    this.start = this.max;
                    this.end = this.min;
                } else {
                    this.start = this.min;
                    this.end = this.max;
                }
                return ticks;
            }
        },
        {
            key: "configure",
            value: function configure() {
                var ticks = this.ticks;
                var start = this.min;
                var end = this.max;
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(LinearScaleBase.prototype), "configure", this).call(this);
                if (this.options.offset && ticks.length) {
                    var offset = (end - start) / Math.max(ticks.length - 1, 1) / 2;
                    start -= offset;
                    end += offset;
                }
                this._startValue = start;
                this._endValue = end;
                this._valueRange = end - start;
            }
        },
        {
            key: "getLabelForValue",
            value: function getLabelForValue(value) {
                return (0, _helpersSegmentJs.o)(value, this.chart.options.locale, this.options.ticks.format);
            }
        }
    ]);
    return LinearScaleBase;
}(Scale);
var LinearScale = /*#__PURE__*/ function(LinearScaleBase) {
    "use strict";
    (0, _inheritsJsDefault.default)(LinearScale, LinearScaleBase);
    var _super = (0, _createSuperJsDefault.default)(LinearScale);
    function LinearScale() {
        (0, _classCallCheckJsDefault.default)(this, LinearScale);
        return _super.apply(this, arguments);
    }
    (0, _createClassJsDefault.default)(LinearScale, [
        {
            key: "determineDataLimits",
            value: function determineDataLimits() {
                var ref = this.getMinMax(true), min = ref.min, max = ref.max;
                this.min = (0, _helpersSegmentJs.g)(min) ? min : 0;
                this.max = (0, _helpersSegmentJs.g)(max) ? max : 1;
                this.handleTickRangeOptions();
            }
        },
        {
            key: "computeTickLimit",
            value: function computeTickLimit() {
                var horizontal = this.isHorizontal();
                var length = horizontal ? this.width : this.height;
                var minRotation = (0, _helpersSegmentJs.t)(this.options.ticks.minRotation);
                var ratio = (horizontal ? Math.sin(minRotation) : Math.cos(minRotation)) || 0.001;
                var tickFont = this._resolveTickFontOptions(0);
                return Math.ceil(length / Math.min(40, tickFont.lineHeight / ratio));
            }
        },
        {
            key: "getPixelForValue",
            value: function getPixelForValue(value) {
                return value === null ? NaN : this.getPixelForDecimal((value - this._startValue) / this._valueRange);
            }
        },
        {
            key: "getValueForPixel",
            value: function getValueForPixel(pixel) {
                return this._startValue + this.getDecimalForPixel(pixel) * this._valueRange;
            }
        }
    ]);
    return LinearScale;
}(LinearScaleBase);
LinearScale.id = "linear";
LinearScale.defaults = {
    ticks: {
        callback: Ticks.formatters.numeric
    }
};
function isMajor(tickVal) {
    var remain = tickVal / Math.pow(10, Math.floor((0, _helpersSegmentJs.M)(tickVal)));
    return remain === 1;
}
function generateTicks(generationOptions, dataRange) {
    var endExp = Math.floor((0, _helpersSegmentJs.M)(dataRange.max));
    var endSignificand = Math.ceil(dataRange.max / Math.pow(10, endExp));
    var ticks = [];
    var tickVal = (0, _helpersSegmentJs.O)(generationOptions.min, Math.pow(10, Math.floor((0, _helpersSegmentJs.M)(dataRange.min))));
    var exp = Math.floor((0, _helpersSegmentJs.M)(tickVal));
    var significand = Math.floor(tickVal / Math.pow(10, exp));
    var precision = exp < 0 ? Math.pow(10, Math.abs(exp)) : 1;
    do {
        ticks.push({
            value: tickVal,
            major: isMajor(tickVal)
        });
        ++significand;
        if (significand === 10) {
            significand = 1;
            ++exp;
            precision = exp >= 0 ? 1 : precision;
        }
        tickVal = Math.round(significand * Math.pow(10, exp) * precision) / precision;
    }while (exp < endExp || exp === endExp && significand < endSignificand);
    var lastTick = (0, _helpersSegmentJs.O)(generationOptions.max, tickVal);
    ticks.push({
        value: lastTick,
        major: isMajor(tickVal)
    });
    return ticks;
}
var LogarithmicScale = /*#__PURE__*/ function(Scale) {
    "use strict";
    (0, _inheritsJsDefault.default)(LogarithmicScale, Scale);
    var _super = (0, _createSuperJsDefault.default)(LogarithmicScale);
    function LogarithmicScale(cfg) {
        (0, _classCallCheckJsDefault.default)(this, LogarithmicScale);
        var _this;
        _this = _super.call(this, cfg);
        _this.start = undefined;
        _this.end = undefined;
        _this._startValue = undefined;
        _this._valueRange = 0;
        return _this;
    }
    (0, _createClassJsDefault.default)(LogarithmicScale, [
        {
            key: "parse",
            value: function parse1(raw, index73) {
                var value = LinearScaleBase.prototype.parse.apply(this, [
                    raw,
                    index73
                ]);
                if (value === 0) {
                    this._zero = true;
                    return undefined;
                }
                return (0, _helpersSegmentJs.g)(value) && value > 0 ? value : null;
            }
        },
        {
            key: "determineDataLimits",
            value: function determineDataLimits() {
                var ref = this.getMinMax(true), min = ref.min, max = ref.max;
                this.min = (0, _helpersSegmentJs.g)(min) ? Math.max(0, min) : null;
                this.max = (0, _helpersSegmentJs.g)(max) ? Math.max(0, max) : null;
                if (this.options.beginAtZero) this._zero = true;
                this.handleTickRangeOptions();
            }
        },
        {
            key: "handleTickRangeOptions",
            value: function handleTickRangeOptions() {
                var ref = this.getUserBounds(), minDefined = ref.minDefined, maxDefined = ref.maxDefined;
                var min = this.min;
                var max = this.max;
                var setMin = function(v) {
                    return min = minDefined ? min : v;
                };
                var setMax = function(v) {
                    return max = maxDefined ? max : v;
                };
                var exp = function(v, m) {
                    return Math.pow(10, Math.floor((0, _helpersSegmentJs.M)(v)) + m);
                };
                if (min === max) {
                    if (min <= 0) {
                        setMin(1);
                        setMax(10);
                    } else {
                        setMin(exp(min, -1));
                        setMax(exp(max, 1));
                    }
                }
                if (min <= 0) setMin(exp(max, -1));
                if (max <= 0) setMax(exp(min, 1));
                if (this._zero && this.min !== this._suggestedMin && min === exp(this.min, 0)) setMin(exp(min, -1));
                this.min = min;
                this.max = max;
            }
        },
        {
            key: "buildTicks",
            value: function buildTicks() {
                var opts = this.options;
                var generationOptions = {
                    min: this._userMin,
                    max: this._userMax
                };
                var ticks = generateTicks(generationOptions, this);
                if (opts.bounds === "ticks") (0, _helpersSegmentJs.aE)(ticks, this, "value");
                if (opts.reverse) {
                    ticks.reverse();
                    this.start = this.max;
                    this.end = this.min;
                } else {
                    this.start = this.min;
                    this.end = this.max;
                }
                return ticks;
            }
        },
        {
            key: "getLabelForValue",
            value: function getLabelForValue(value) {
                return value === undefined ? "0" : (0, _helpersSegmentJs.o)(value, this.chart.options.locale, this.options.ticks.format);
            }
        },
        {
            key: "configure",
            value: function configure() {
                var start = this.min;
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(LogarithmicScale.prototype), "configure", this).call(this);
                this._startValue = (0, _helpersSegmentJs.M)(start);
                this._valueRange = (0, _helpersSegmentJs.M)(this.max) - (0, _helpersSegmentJs.M)(start);
            }
        },
        {
            key: "getPixelForValue",
            value: function getPixelForValue(value) {
                if (value === undefined || value === 0) value = this.min;
                if (value === null || isNaN(value)) return NaN;
                return this.getPixelForDecimal(value === this.min ? 0 : ((0, _helpersSegmentJs.M)(value) - this._startValue) / this._valueRange);
            }
        },
        {
            key: "getValueForPixel",
            value: function getValueForPixel(pixel) {
                var decimal = this.getDecimalForPixel(pixel);
                return Math.pow(10, this._startValue + decimal * this._valueRange);
            }
        }
    ]);
    return LogarithmicScale;
}(Scale);
LogarithmicScale.id = "logarithmic";
LogarithmicScale.defaults = {
    ticks: {
        callback: Ticks.formatters.logarithmic,
        major: {
            enabled: true
        }
    }
};
function getTickBackdropHeight(opts) {
    var tickOpts = opts.ticks;
    if (tickOpts.display && opts.display) {
        var padding = (0, _helpersSegmentJs.D)(tickOpts.backdropPadding);
        return (0, _helpersSegmentJs.v)(tickOpts.font && tickOpts.font.size, (0, _helpersSegmentJs.d).font.size) + padding.height;
    }
    return 0;
}
function measureLabelSize(ctx, font, label) {
    label = (0, _helpersSegmentJs.b)(label) ? label : [
        label
    ];
    return {
        w: (0, _helpersSegmentJs.aJ)(ctx, font.string, label),
        h: label.length * font.lineHeight
    };
}
function determineLimits(angle, pos, size, min, max) {
    if (angle === min || angle === max) return {
        start: pos - size / 2,
        end: pos + size / 2
    };
    else if (angle < min || angle > max) return {
        start: pos - size,
        end: pos
    };
    return {
        start: pos,
        end: pos + size
    };
}
function fitWithPointLabels(scale) {
    var orig = {
        l: scale.left + scale._padding.left,
        r: scale.right - scale._padding.right,
        t: scale.top + scale._padding.top,
        b: scale.bottom - scale._padding.bottom
    };
    var limits = Object.assign({}, orig);
    var labelSizes = [];
    var padding = [];
    var valueCount = scale._pointLabels.length;
    var pointLabelOpts = scale.options.pointLabels;
    var additionalAngle = pointLabelOpts.centerPointLabels ? (0, _helpersSegmentJs.P) / valueCount : 0;
    for(var i = 0; i < valueCount; i++){
        var opts = pointLabelOpts.setContext(scale.getPointLabelContext(i));
        padding[i] = opts.padding;
        var pointPosition = scale.getPointPosition(i, scale.drawingArea + padding[i], additionalAngle);
        var plFont = (0, _helpersSegmentJs.$)(opts.font);
        var textSize = measureLabelSize(scale.ctx, plFont, scale._pointLabels[i]);
        labelSizes[i] = textSize;
        var angleRadians = (0, _helpersSegmentJs.ax)(scale.getIndexAngle(i) + additionalAngle);
        var angle = Math.round((0, _helpersSegmentJs.S)(angleRadians));
        var hLimits = determineLimits(angle, pointPosition.x, textSize.w, 0, 180);
        var vLimits = determineLimits(angle, pointPosition.y, textSize.h, 90, 270);
        updateLimits(limits, orig, angleRadians, hLimits, vLimits);
    }
    scale.setCenterPoint(orig.l - limits.l, limits.r - orig.r, orig.t - limits.t, limits.b - orig.b);
    scale._pointLabelItems = buildPointLabelItems(scale, labelSizes, padding);
}
function updateLimits(limits, orig, angle, hLimits, vLimits) {
    var sin = Math.abs(Math.sin(angle));
    var cos = Math.abs(Math.cos(angle));
    var x = 0;
    var y = 0;
    if (hLimits.start < orig.l) {
        x = (orig.l - hLimits.start) / sin;
        limits.l = Math.min(limits.l, orig.l - x);
    } else if (hLimits.end > orig.r) {
        x = (hLimits.end - orig.r) / sin;
        limits.r = Math.max(limits.r, orig.r + x);
    }
    if (vLimits.start < orig.t) {
        y = (orig.t - vLimits.start) / cos;
        limits.t = Math.min(limits.t, orig.t - y);
    } else if (vLimits.end > orig.b) {
        y = (vLimits.end - orig.b) / cos;
        limits.b = Math.max(limits.b, orig.b + y);
    }
}
function buildPointLabelItems(scale, labelSizes, padding) {
    var items = [];
    var valueCount = scale._pointLabels.length;
    var opts = scale.options;
    var extra = getTickBackdropHeight(opts) / 2;
    var outerDistance = scale.drawingArea;
    var additionalAngle = opts.pointLabels.centerPointLabels ? (0, _helpersSegmentJs.P) / valueCount : 0;
    for(var i = 0; i < valueCount; i++){
        var pointLabelPosition = scale.getPointPosition(i, outerDistance + extra + padding[i], additionalAngle);
        var angle = Math.round((0, _helpersSegmentJs.S)((0, _helpersSegmentJs.ax)(pointLabelPosition.angle + (0, _helpersSegmentJs.H))));
        var size = labelSizes[i];
        var y = yForAngle(pointLabelPosition.y, size.h, angle);
        var textAlign = getTextAlignForAngle(angle);
        var left = leftForTextAlign(pointLabelPosition.x, size.w, textAlign);
        items.push({
            x: pointLabelPosition.x,
            y: y,
            textAlign: textAlign,
            left: left,
            top: y,
            right: left + size.w,
            bottom: y + size.h
        });
    }
    return items;
}
function getTextAlignForAngle(angle) {
    if (angle === 0 || angle === 180) return "center";
    else if (angle < 180) return "left";
    return "right";
}
function leftForTextAlign(x, w, align) {
    if (align === "right") x -= w;
    else if (align === "center") x -= w / 2;
    return x;
}
function yForAngle(y, h, angle) {
    if (angle === 90 || angle === 270) y -= h / 2;
    else if (angle > 270 || angle < 90) y -= h;
    return y;
}
function drawPointLabels(scale, labelCount) {
    var ctx = scale.ctx, pointLabels = scale.options.pointLabels;
    for(var i = labelCount - 1; i >= 0; i--){
        var optsAtIndex = pointLabels.setContext(scale.getPointLabelContext(i));
        var plFont = (0, _helpersSegmentJs.$)(optsAtIndex.font);
        var _i = scale._pointLabelItems[i], x = _i.x, y = _i.y, textAlign = _i.textAlign, left = _i.left, top = _i.top, right = _i.right, bottom = _i.bottom;
        var backdropColor = optsAtIndex.backdropColor;
        if (!(0, _helpersSegmentJs.k)(backdropColor)) {
            var borderRadius = (0, _helpersSegmentJs.av)(optsAtIndex.borderRadius);
            var padding = (0, _helpersSegmentJs.D)(optsAtIndex.backdropPadding);
            ctx.fillStyle = backdropColor;
            var backdropLeft = left - padding.left;
            var backdropTop = top - padding.top;
            var backdropWidth = right - left + padding.width;
            var backdropHeight = bottom - top + padding.height;
            if (Object.values(borderRadius).some(function(v) {
                return v !== 0;
            })) {
                ctx.beginPath();
                (0, _helpersSegmentJs.at)(ctx, {
                    x: backdropLeft,
                    y: backdropTop,
                    w: backdropWidth,
                    h: backdropHeight,
                    radius: borderRadius
                });
                ctx.fill();
            } else ctx.fillRect(backdropLeft, backdropTop, backdropWidth, backdropHeight);
        }
        (0, _helpersSegmentJs.Y)(ctx, scale._pointLabels[i], x, y + plFont.lineHeight / 2, plFont, {
            color: optsAtIndex.color,
            textAlign: textAlign,
            textBaseline: "middle"
        });
    }
}
function pathRadiusLine(scale, radius, circular, labelCount) {
    var ctx = scale.ctx;
    if (circular) ctx.arc(scale.xCenter, scale.yCenter, radius, 0, (0, _helpersSegmentJs.T));
    else {
        var pointPosition = scale.getPointPosition(0, radius);
        ctx.moveTo(pointPosition.x, pointPosition.y);
        for(var i = 1; i < labelCount; i++){
            pointPosition = scale.getPointPosition(i, radius);
            ctx.lineTo(pointPosition.x, pointPosition.y);
        }
    }
}
function drawRadiusLine(scale, gridLineOpts, radius, labelCount) {
    var ctx = scale.ctx;
    var circular = gridLineOpts.circular;
    var color = gridLineOpts.color, lineWidth = gridLineOpts.lineWidth;
    if (!circular && !labelCount || !color || !lineWidth || radius < 0) return;
    ctx.save();
    ctx.strokeStyle = color;
    ctx.lineWidth = lineWidth;
    ctx.setLineDash(gridLineOpts.borderDash);
    ctx.lineDashOffset = gridLineOpts.borderDashOffset;
    ctx.beginPath();
    pathRadiusLine(scale, radius, circular, labelCount);
    ctx.closePath();
    ctx.stroke();
    ctx.restore();
}
function createPointLabelContext(parent, index74, label) {
    return (0, _helpersSegmentJs.h)(parent, {
        label: label,
        index: index74,
        type: "pointLabel"
    });
}
var RadialLinearScale = /*#__PURE__*/ function(LinearScaleBase1) {
    "use strict";
    (0, _inheritsJsDefault.default)(RadialLinearScale, LinearScaleBase1);
    var _super = (0, _createSuperJsDefault.default)(RadialLinearScale);
    function RadialLinearScale(cfg) {
        (0, _classCallCheckJsDefault.default)(this, RadialLinearScale);
        var _this;
        _this = _super.call(this, cfg);
        _this.xCenter = undefined;
        _this.yCenter = undefined;
        _this.drawingArea = undefined;
        _this._pointLabels = [];
        _this._pointLabelItems = [];
        return _this;
    }
    (0, _createClassJsDefault.default)(RadialLinearScale, [
        {
            key: "setDimensions",
            value: function setDimensions() {
                var padding = this._padding = (0, _helpersSegmentJs.D)(getTickBackdropHeight(this.options) / 2);
                var w = this.width = this.maxWidth - padding.width;
                var h = this.height = this.maxHeight - padding.height;
                this.xCenter = Math.floor(this.left + w / 2 + padding.left);
                this.yCenter = Math.floor(this.top + h / 2 + padding.top);
                this.drawingArea = Math.floor(Math.min(w, h) / 2);
            }
        },
        {
            key: "determineDataLimits",
            value: function determineDataLimits() {
                var ref = this.getMinMax(false), min = ref.min, max = ref.max;
                this.min = (0, _helpersSegmentJs.g)(min) && !isNaN(min) ? min : 0;
                this.max = (0, _helpersSegmentJs.g)(max) && !isNaN(max) ? max : 0;
                this.handleTickRangeOptions();
            }
        },
        {
            key: "computeTickLimit",
            value: function computeTickLimit() {
                return Math.ceil(this.drawingArea / getTickBackdropHeight(this.options));
            }
        },
        {
            key: "generateTickLabels",
            value: function generateTickLabels(ticks) {
                var _this = this;
                LinearScaleBase.prototype.generateTickLabels.call(this, ticks);
                this._pointLabels = this.getLabels().map(function(value, index75) {
                    var label = (0, _helpersSegmentJs.Q)(_this.options.pointLabels.callback, [
                        value,
                        index75
                    ], _this);
                    return label || label === 0 ? label : "";
                }).filter(function(v, i) {
                    return _this.chart.getDataVisibility(i);
                });
            }
        },
        {
            key: "fit",
            value: function fit() {
                var opts = this.options;
                if (opts.display && opts.pointLabels.display) fitWithPointLabels(this);
                else this.setCenterPoint(0, 0, 0, 0);
            }
        },
        {
            key: "setCenterPoint",
            value: function setCenterPoint(leftMovement, rightMovement, topMovement, bottomMovement) {
                this.xCenter += Math.floor((leftMovement - rightMovement) / 2);
                this.yCenter += Math.floor((topMovement - bottomMovement) / 2);
                this.drawingArea -= Math.min(this.drawingArea / 2, Math.max(leftMovement, rightMovement, topMovement, bottomMovement));
            }
        },
        {
            key: "getIndexAngle",
            value: function getIndexAngle(index76) {
                var angleMultiplier = (0, _helpersSegmentJs.T) / (this._pointLabels.length || 1);
                var startAngle = this.options.startAngle || 0;
                return (0, _helpersSegmentJs.ax)(index76 * angleMultiplier + (0, _helpersSegmentJs.t)(startAngle));
            }
        },
        {
            key: "getDistanceFromCenterForValue",
            value: function getDistanceFromCenterForValue(value) {
                if ((0, _helpersSegmentJs.k)(value)) return NaN;
                var scalingFactor = this.drawingArea / (this.max - this.min);
                if (this.options.reverse) return (this.max - value) * scalingFactor;
                return (value - this.min) * scalingFactor;
            }
        },
        {
            key: "getValueForDistanceFromCenter",
            value: function getValueForDistanceFromCenter(distance) {
                if ((0, _helpersSegmentJs.k)(distance)) return NaN;
                var scaledDistance = distance / (this.drawingArea / (this.max - this.min));
                return this.options.reverse ? this.max - scaledDistance : this.min + scaledDistance;
            }
        },
        {
            key: "getPointLabelContext",
            value: function getPointLabelContext(index77) {
                var pointLabels = this._pointLabels || [];
                if (index77 >= 0 && index77 < pointLabels.length) {
                    var pointLabel = pointLabels[index77];
                    return createPointLabelContext(this.getContext(), index77, pointLabel);
                }
            }
        },
        {
            key: "getPointPosition",
            value: function getPointPosition(index78, distanceFromCenter) {
                var additionalAngle = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : 0;
                var angle = this.getIndexAngle(index78) - (0, _helpersSegmentJs.H) + additionalAngle;
                return {
                    x: Math.cos(angle) * distanceFromCenter + this.xCenter,
                    y: Math.sin(angle) * distanceFromCenter + this.yCenter,
                    angle: angle
                };
            }
        },
        {
            key: "getPointPositionForValue",
            value: function getPointPositionForValue(index79, value) {
                return this.getPointPosition(index79, this.getDistanceFromCenterForValue(value));
            }
        },
        {
            key: "getBasePosition",
            value: function getBasePosition(index80) {
                return this.getPointPositionForValue(index80 || 0, this.getBaseValue());
            }
        },
        {
            key: "getPointLabelPosition",
            value: function getPointLabelPosition(index81) {
                var _index = this._pointLabelItems[index81], left = _index.left, top = _index.top, right = _index.right, bottom = _index.bottom;
                return {
                    left: left,
                    top: top,
                    right: right,
                    bottom: bottom
                };
            }
        },
        {
            key: "drawBackground",
            value: function drawBackground() {
                var _options = this.options, backgroundColor = _options.backgroundColor, circular = _options.grid.circular;
                if (backgroundColor) {
                    var ctx = this.ctx;
                    ctx.save();
                    ctx.beginPath();
                    pathRadiusLine(this, this.getDistanceFromCenterForValue(this._endValue), circular, this._pointLabels.length);
                    ctx.closePath();
                    ctx.fillStyle = backgroundColor;
                    ctx.fill();
                    ctx.restore();
                }
            }
        },
        {
            key: "drawGrid",
            value: function drawGrid() {
                var _this = this;
                var ctx = this.ctx;
                var opts = this.options;
                var angleLines = opts.angleLines, grid = opts.grid;
                var labelCount = this._pointLabels.length;
                var i, offset, position;
                if (opts.pointLabels.display) drawPointLabels(this, labelCount);
                if (grid.display) this.ticks.forEach(function(tick, index82) {
                    if (index82 !== 0) {
                        offset = _this.getDistanceFromCenterForValue(tick.value);
                        var optsAtIndex = grid.setContext(_this.getContext(index82 - 1));
                        drawRadiusLine(_this, optsAtIndex, offset, labelCount);
                    }
                });
                if (angleLines.display) {
                    ctx.save();
                    for(i = labelCount - 1; i >= 0; i--){
                        var optsAtIndex1 = angleLines.setContext(this.getPointLabelContext(i));
                        var color = optsAtIndex1.color, lineWidth = optsAtIndex1.lineWidth;
                        if (!lineWidth || !color) continue;
                        ctx.lineWidth = lineWidth;
                        ctx.strokeStyle = color;
                        ctx.setLineDash(optsAtIndex1.borderDash);
                        ctx.lineDashOffset = optsAtIndex1.borderDashOffset;
                        offset = this.getDistanceFromCenterForValue(opts.ticks.reverse ? this.min : this.max);
                        position = this.getPointPosition(i, offset);
                        ctx.beginPath();
                        ctx.moveTo(this.xCenter, this.yCenter);
                        ctx.lineTo(position.x, position.y);
                        ctx.stroke();
                    }
                    ctx.restore();
                }
            }
        },
        {
            key: "drawBorder",
            value: function drawBorder() {}
        },
        {
            key: "drawLabels",
            value: function drawLabels() {
                var _this = this;
                var ctx = this.ctx;
                var opts = this.options;
                var tickOpts = opts.ticks;
                if (!tickOpts.display) return;
                var startAngle = this.getIndexAngle(0);
                var offset, width;
                ctx.save();
                ctx.translate(this.xCenter, this.yCenter);
                ctx.rotate(startAngle);
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                this.ticks.forEach(function(tick, index83) {
                    if (index83 === 0 && !opts.reverse) return;
                    var optsAtIndex = tickOpts.setContext(_this.getContext(index83));
                    var tickFont = (0, _helpersSegmentJs.$)(optsAtIndex.font);
                    offset = _this.getDistanceFromCenterForValue(_this.ticks[index83].value);
                    if (optsAtIndex.showLabelBackdrop) {
                        ctx.font = tickFont.string;
                        width = ctx.measureText(tick.label).width;
                        ctx.fillStyle = optsAtIndex.backdropColor;
                        var padding = (0, _helpersSegmentJs.D)(optsAtIndex.backdropPadding);
                        ctx.fillRect(-width / 2 - padding.left, -offset - tickFont.size / 2 - padding.top, width + padding.width, tickFont.size + padding.height);
                    }
                    (0, _helpersSegmentJs.Y)(ctx, tick.label, 0, -offset, tickFont, {
                        color: optsAtIndex.color
                    });
                });
                ctx.restore();
            }
        },
        {
            key: "drawTitle",
            value: function drawTitle() {}
        }
    ]);
    return RadialLinearScale;
}(LinearScaleBase);
RadialLinearScale.id = "radialLinear";
RadialLinearScale.defaults = {
    display: true,
    animate: true,
    position: "chartArea",
    angleLines: {
        display: true,
        lineWidth: 1,
        borderDash: [],
        borderDashOffset: 0.0
    },
    grid: {
        circular: false
    },
    startAngle: 0,
    ticks: {
        showLabelBackdrop: true,
        callback: Ticks.formatters.numeric
    },
    pointLabels: {
        backdropColor: undefined,
        backdropPadding: 2,
        display: true,
        font: {
            size: 10
        },
        callback: function(label) {
            return label;
        },
        padding: 5,
        centerPointLabels: false
    }
};
RadialLinearScale.defaultRoutes = {
    "angleLines.color": "borderColor",
    "pointLabels.color": "color",
    "ticks.color": "color"
};
RadialLinearScale.descriptors = {
    angleLines: {
        _fallback: "grid"
    }
};
var INTERVALS = {
    millisecond: {
        common: true,
        size: 1,
        steps: 1000
    },
    second: {
        common: true,
        size: 1000,
        steps: 60
    },
    minute: {
        common: true,
        size: 60000,
        steps: 60
    },
    hour: {
        common: true,
        size: 3600000,
        steps: 24
    },
    day: {
        common: true,
        size: 86400000,
        steps: 30
    },
    week: {
        common: false,
        size: 604800000,
        steps: 4
    },
    month: {
        common: true,
        size: 2.628e9,
        steps: 12
    },
    quarter: {
        common: false,
        size: 7.884e9,
        steps: 4
    },
    year: {
        common: true,
        size: 3.154e10
    }
};
var UNITS = Object.keys(INTERVALS);
function sorter(a, b) {
    return a - b;
}
function parse(scale, input) {
    if ((0, _helpersSegmentJs.k)(input)) return null;
    var adapter = scale._adapter;
    var __parseOpts = scale._parseOpts, parser = __parseOpts.parser, round = __parseOpts.round, isoWeekday = __parseOpts.isoWeekday;
    var value = input;
    if (typeof parser === "function") value = parser(value);
    if (!(0, _helpersSegmentJs.g)(value)) value = typeof parser === "string" ? adapter.parse(value, parser) : adapter.parse(value);
    if (value === null) return null;
    if (round) value = round === "week" && ((0, _helpersSegmentJs.q)(isoWeekday) || isoWeekday === true) ? adapter.startOf(value, "isoWeek", isoWeekday) : adapter.startOf(value, round);
    return +value;
}
function determineUnitForAutoTicks(minUnit, min, max, capacity) {
    var ilen = UNITS.length;
    for(var i = UNITS.indexOf(minUnit); i < ilen - 1; ++i){
        var interval = INTERVALS[UNITS[i]];
        var factor = interval.steps ? interval.steps : Number.MAX_SAFE_INTEGER;
        if (interval.common && Math.ceil((max - min) / (factor * interval.size)) <= capacity) return UNITS[i];
    }
    return UNITS[ilen - 1];
}
function determineUnitForFormatting(scale, numTicks, minUnit, min, max) {
    for(var i = UNITS.length - 1; i >= UNITS.indexOf(minUnit); i--){
        var unit = UNITS[i];
        if (INTERVALS[unit].common && scale._adapter.diff(max, min, unit) >= numTicks - 1) return unit;
    }
    return UNITS[minUnit ? UNITS.indexOf(minUnit) : 0];
}
function determineMajorUnit(unit) {
    for(var i = UNITS.indexOf(unit) + 1, ilen = UNITS.length; i < ilen; ++i){
        if (INTERVALS[UNITS[i]].common) return UNITS[i];
    }
}
function addTick(ticks, time, timestamps) {
    if (!timestamps) ticks[time] = true;
    else if (timestamps.length) {
        var ref = (0, _helpersSegmentJs.aL)(timestamps, time), lo = ref.lo, hi = ref.hi;
        var timestamp = timestamps[lo] >= time ? timestamps[lo] : timestamps[hi];
        ticks[timestamp] = true;
    }
}
function setMajorTicks(scale, ticks, map1, majorUnit) {
    var adapter = scale._adapter;
    var first = +adapter.startOf(ticks[0].value, majorUnit);
    var last = ticks[ticks.length - 1].value;
    var major, index84;
    for(major = first; major <= last; major = +adapter.add(major, 1, majorUnit)){
        index84 = map1[major];
        if (index84 >= 0) ticks[index84].major = true;
    }
    return ticks;
}
function ticksFromTimestamps(scale, values, majorUnit) {
    var ticks = [];
    var map2 = {};
    var ilen = values.length;
    var i, value;
    for(i = 0; i < ilen; ++i){
        value = values[i];
        map2[value] = i;
        ticks.push({
            value: value,
            major: false
        });
    }
    return ilen === 0 || !majorUnit ? ticks : setMajorTicks(scale, ticks, map2, majorUnit);
}
var TimeScale = /*#__PURE__*/ function(Scale) {
    "use strict";
    (0, _inheritsJsDefault.default)(TimeScale, Scale);
    var _super = (0, _createSuperJsDefault.default)(TimeScale);
    function TimeScale(props) {
        (0, _classCallCheckJsDefault.default)(this, TimeScale);
        var _this;
        _this = _super.call(this, props);
        _this._cache = {
            data: [],
            labels: [],
            all: []
        };
        _this._unit = "day";
        _this._majorUnit = undefined;
        _this._offsets = {};
        _this._normalized = false;
        _this._parseOpts = undefined;
        return _this;
    }
    (0, _createClassJsDefault.default)(TimeScale, [
        {
            key: "init",
            value: function init(scaleOpts, opts) {
                var time = scaleOpts.time || (scaleOpts.time = {});
                var adapter = this._adapter = new adapters._date(scaleOpts.adapters.date);
                (0, _helpersSegmentJs.aa)(time.displayFormats, adapter.formats());
                this._parseOpts = {
                    parser: time.parser,
                    round: time.round,
                    isoWeekday: time.isoWeekday
                };
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(TimeScale.prototype), "init", this).call(this, scaleOpts);
                this._normalized = opts.normalized;
            }
        },
        {
            key: "parse",
            value: function parse1(raw, index) {
                if (raw === undefined) return null;
                return parse(this, raw);
            }
        },
        {
            key: "beforeLayout",
            value: function beforeLayout() {
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(TimeScale.prototype), "beforeLayout", this).call(this);
                this._cache = {
                    data: [],
                    labels: [],
                    all: []
                };
            }
        },
        {
            key: "determineDataLimits",
            value: function determineDataLimits() {
                var options = this.options;
                var adapter = this._adapter;
                var unit = options.time.unit || "day";
                var ref = this.getUserBounds(), min = ref.min, max = ref.max, minDefined = ref.minDefined, maxDefined = ref.maxDefined;
                function _applyBounds(bounds) {
                    if (!minDefined && !isNaN(bounds.min)) min = Math.min(min, bounds.min);
                    if (!maxDefined && !isNaN(bounds.max)) max = Math.max(max, bounds.max);
                }
                if (!minDefined || !maxDefined) {
                    _applyBounds(this._getLabelBounds());
                    if (options.bounds !== "ticks" || options.ticks.source !== "labels") _applyBounds(this.getMinMax(false));
                }
                min = (0, _helpersSegmentJs.g)(min) && !isNaN(min) ? min : +adapter.startOf(Date.now(), unit);
                max = (0, _helpersSegmentJs.g)(max) && !isNaN(max) ? max : +adapter.endOf(Date.now(), unit) + 1;
                this.min = Math.min(min, max - 1);
                this.max = Math.max(min + 1, max);
            }
        },
        {
            key: "_getLabelBounds",
            value: function _getLabelBounds() {
                var arr = this.getLabelTimestamps();
                var min = Number.POSITIVE_INFINITY;
                var max = Number.NEGATIVE_INFINITY;
                if (arr.length) {
                    min = arr[0];
                    max = arr[arr.length - 1];
                }
                return {
                    min: min,
                    max: max
                };
            }
        },
        {
            key: "buildTicks",
            value: function buildTicks() {
                var options = this.options;
                var timeOpts = options.time;
                var tickOpts = options.ticks;
                var timestamps = tickOpts.source === "labels" ? this.getLabelTimestamps() : this._generate();
                if (options.bounds === "ticks" && timestamps.length) {
                    this.min = this._userMin || timestamps[0];
                    this.max = this._userMax || timestamps[timestamps.length - 1];
                }
                var min = this.min;
                var max = this.max;
                var ticks = (0, _helpersSegmentJs.aK)(timestamps, min, max);
                this._unit = timeOpts.unit || (tickOpts.autoSkip ? determineUnitForAutoTicks(timeOpts.minUnit, this.min, this.max, this._getLabelCapacity(min)) : determineUnitForFormatting(this, ticks.length, timeOpts.minUnit, this.min, this.max));
                this._majorUnit = !tickOpts.major.enabled || this._unit === "year" ? undefined : determineMajorUnit(this._unit);
                this.initOffsets(timestamps);
                if (options.reverse) ticks.reverse();
                return ticksFromTimestamps(this, ticks, this._majorUnit);
            }
        },
        {
            key: "afterAutoSkip",
            value: function afterAutoSkip() {
                if (this.options.offsetAfterAutoskip) this.initOffsets(this.ticks.map(function(tick) {
                    return +tick.value;
                }));
            }
        },
        {
            key: "initOffsets",
            value: function initOffsets(timestamps) {
                var start = 0;
                var end = 0;
                var first, last;
                if (this.options.offset && timestamps.length) {
                    first = this.getDecimalForValue(timestamps[0]);
                    if (timestamps.length === 1) start = 1 - first;
                    else start = (this.getDecimalForValue(timestamps[1]) - first) / 2;
                    last = this.getDecimalForValue(timestamps[timestamps.length - 1]);
                    if (timestamps.length === 1) end = last;
                    else end = (last - this.getDecimalForValue(timestamps[timestamps.length - 2])) / 2;
                }
                var limit = timestamps.length < 3 ? 0.5 : 0.25;
                start = (0, _helpersSegmentJs.w)(start, 0, limit);
                end = (0, _helpersSegmentJs.w)(end, 0, limit);
                this._offsets = {
                    start: start,
                    end: end,
                    factor: 1 / (start + 1 + end)
                };
            }
        },
        {
            key: "_generate",
            value: function _generate() {
                var adapter = this._adapter;
                var min = this.min;
                var max = this.max;
                var options = this.options;
                var timeOpts = options.time;
                var minor = timeOpts.unit || determineUnitForAutoTicks(timeOpts.minUnit, min, max, this._getLabelCapacity(min));
                var stepSize = (0, _helpersSegmentJs.v)(timeOpts.stepSize, 1);
                var weekday = minor === "week" ? timeOpts.isoWeekday : false;
                var hasWeekday = (0, _helpersSegmentJs.q)(weekday) || weekday === true;
                var ticks = {};
                var first = min;
                var time, count;
                if (hasWeekday) first = +adapter.startOf(first, "isoWeek", weekday);
                first = +adapter.startOf(first, hasWeekday ? "day" : minor);
                if (adapter.diff(max, min, minor) > 100000 * stepSize) throw new Error(min + " and " + max + " are too far apart with stepSize of " + stepSize + " " + minor);
                var timestamps = options.ticks.source === "data" && this.getDataTimestamps();
                for(time = first, count = 0; time < max; time = +adapter.add(time, stepSize, minor), count++)addTick(ticks, time, timestamps);
                if (time === max || options.bounds === "ticks" || count === 1) addTick(ticks, time, timestamps);
                return Object.keys(ticks).sort(function(a, b) {
                    return a - b;
                }).map(function(x) {
                    return +x;
                });
            }
        },
        {
            key: "getLabelForValue",
            value: function getLabelForValue(value) {
                var adapter = this._adapter;
                var timeOpts = this.options.time;
                if (timeOpts.tooltipFormat) return adapter.format(value, timeOpts.tooltipFormat);
                return adapter.format(value, timeOpts.displayFormats.datetime);
            }
        },
        {
            key: "_tickFormatFunction",
            value: function _tickFormatFunction(time, index85, ticks, format) {
                var options = this.options;
                var formats = options.time.displayFormats;
                var unit = this._unit;
                var majorUnit = this._majorUnit;
                var minorFormat = unit && formats[unit];
                var majorFormat = majorUnit && formats[majorUnit];
                var tick = ticks[index85];
                var major = majorUnit && majorFormat && tick && tick.major;
                var label = this._adapter.format(time, format || (major ? majorFormat : minorFormat));
                var formatter = options.ticks.callback;
                return formatter ? (0, _helpersSegmentJs.Q)(formatter, [
                    label,
                    index85,
                    ticks
                ], this) : label;
            }
        },
        {
            key: "generateTickLabels",
            value: function generateTickLabels(ticks) {
                var i, ilen, tick;
                for(i = 0, ilen = ticks.length; i < ilen; ++i){
                    tick = ticks[i];
                    tick.label = this._tickFormatFunction(tick.value, i, ticks);
                }
            }
        },
        {
            key: "getDecimalForValue",
            value: function getDecimalForValue(value) {
                return value === null ? NaN : (value - this.min) / (this.max - this.min);
            }
        },
        {
            key: "getPixelForValue",
            value: function getPixelForValue(value) {
                var offsets = this._offsets;
                var pos = this.getDecimalForValue(value);
                return this.getPixelForDecimal((offsets.start + pos) * offsets.factor);
            }
        },
        {
            key: "getValueForPixel",
            value: function getValueForPixel(pixel) {
                var offsets = this._offsets;
                var pos = this.getDecimalForPixel(pixel) / offsets.factor - offsets.end;
                return this.min + pos * (this.max - this.min);
            }
        },
        {
            key: "_getLabelSize",
            value: function _getLabelSize(label) {
                var ticksOpts = this.options.ticks;
                var tickLabelWidth = this.ctx.measureText(label).width;
                var angle = (0, _helpersSegmentJs.t)(this.isHorizontal() ? ticksOpts.maxRotation : ticksOpts.minRotation);
                var cosRotation = Math.cos(angle);
                var sinRotation = Math.sin(angle);
                var tickFontSize = this._resolveTickFontOptions(0).size;
                return {
                    w: tickLabelWidth * cosRotation + tickFontSize * sinRotation,
                    h: tickLabelWidth * sinRotation + tickFontSize * cosRotation
                };
            }
        },
        {
            key: "_getLabelCapacity",
            value: function _getLabelCapacity(exampleTime) {
                var timeOpts = this.options.time;
                var displayFormats = timeOpts.displayFormats;
                var format = displayFormats[timeOpts.unit] || displayFormats.millisecond;
                var exampleLabel = this._tickFormatFunction(exampleTime, 0, ticksFromTimestamps(this, [
                    exampleTime
                ], this._majorUnit), format);
                var size = this._getLabelSize(exampleLabel);
                var capacity = Math.floor(this.isHorizontal() ? this.width / size.w : this.height / size.h) - 1;
                return capacity > 0 ? capacity : 1;
            }
        },
        {
            key: "getDataTimestamps",
            value: function getDataTimestamps() {
                var timestamps = this._cache.data || [];
                var i, ilen;
                if (timestamps.length) return timestamps;
                var metas = this.getMatchingVisibleMetas();
                if (this._normalized && metas.length) return this._cache.data = metas[0].controller.getAllParsedValues(this);
                for(i = 0, ilen = metas.length; i < ilen; ++i)timestamps = timestamps.concat(metas[i].controller.getAllParsedValues(this));
                return this._cache.data = this.normalize(timestamps);
            }
        },
        {
            key: "getLabelTimestamps",
            value: function getLabelTimestamps() {
                var timestamps = this._cache.labels || [];
                var i, ilen;
                if (timestamps.length) return timestamps;
                var labels = this.getLabels();
                for(i = 0, ilen = labels.length; i < ilen; ++i)timestamps.push(parse(this, labels[i]));
                return this._cache.labels = this._normalized ? timestamps : this.normalize(timestamps);
            }
        },
        {
            key: "normalize",
            value: function normalize(values) {
                return (0, _helpersSegmentJs._)(values.sort(sorter));
            }
        }
    ]);
    return TimeScale;
}(Scale);
TimeScale.id = "time";
TimeScale.defaults = {
    bounds: "data",
    adapters: {},
    time: {
        parser: false,
        unit: false,
        round: false,
        isoWeekday: false,
        minUnit: "millisecond",
        displayFormats: {}
    },
    ticks: {
        source: "auto",
        major: {
            enabled: false
        }
    }
};
function interpolate(table, val, reverse) {
    var lo = 0;
    var hi = table.length - 1;
    var prevSource, nextSource, prevTarget, nextTarget;
    if (reverse) {
        var ref;
        if (val >= table[lo].pos && val <= table[hi].pos) ref = (0, _helpersSegmentJs.x)(table, "pos", val), lo = ref.lo, hi = ref.hi, ref;
        var ref16;
        ref16 = table[lo], prevSource = ref16.pos, prevTarget = ref16.time, ref16;
        var ref17;
        ref17 = table[hi], nextSource = ref17.pos, nextTarget = ref17.time, ref17;
    } else {
        var ref18;
        if (val >= table[lo].time && val <= table[hi].time) ref18 = (0, _helpersSegmentJs.x)(table, "time", val), lo = ref18.lo, hi = ref18.hi, ref18;
        var ref19;
        ref19 = table[lo], prevSource = ref19.time, prevTarget = ref19.pos, ref19;
        var ref20;
        ref20 = table[hi], nextSource = ref20.time, nextTarget = ref20.pos, ref20;
    }
    var span = nextSource - prevSource;
    return span ? prevTarget + (nextTarget - prevTarget) * (val - prevSource) / span : prevTarget;
}
var TimeSeriesScale = /*#__PURE__*/ function(TimeScale) {
    "use strict";
    (0, _inheritsJsDefault.default)(TimeSeriesScale, TimeScale);
    var _super = (0, _createSuperJsDefault.default)(TimeSeriesScale);
    function TimeSeriesScale(props) {
        (0, _classCallCheckJsDefault.default)(this, TimeSeriesScale);
        var _this;
        _this = _super.call(this, props);
        _this._table = [];
        _this._minPos = undefined;
        _this._tableRange = undefined;
        return _this;
    }
    (0, _createClassJsDefault.default)(TimeSeriesScale, [
        {
            key: "initOffsets",
            value: function initOffsets() {
                var timestamps = this._getTimestampsForTable();
                var table = this._table = this.buildLookupTable(timestamps);
                this._minPos = interpolate(table, this.min);
                this._tableRange = interpolate(table, this.max) - this._minPos;
                (0, _getJsDefault.default)((0, _getPrototypeOfJsDefault.default)(TimeSeriesScale.prototype), "initOffsets", this).call(this, timestamps);
            }
        },
        {
            key: "buildLookupTable",
            value: function buildLookupTable(timestamps) {
                var ref = this, min = ref.min, max = ref.max;
                var items = [];
                var table = [];
                var i, ilen, prev, curr, next;
                for(i = 0, ilen = timestamps.length; i < ilen; ++i){
                    curr = timestamps[i];
                    if (curr >= min && curr <= max) items.push(curr);
                }
                if (items.length < 2) return [
                    {
                        time: min,
                        pos: 0
                    },
                    {
                        time: max,
                        pos: 1
                    }
                ];
                for(i = 0, ilen = items.length; i < ilen; ++i){
                    next = items[i + 1];
                    prev = items[i - 1];
                    curr = items[i];
                    if (Math.round((next + prev) / 2) !== curr) table.push({
                        time: curr,
                        pos: i / (ilen - 1)
                    });
                }
                return table;
            }
        },
        {
            key: "_getTimestampsForTable",
            value: function _getTimestampsForTable() {
                var timestamps = this._cache.all || [];
                if (timestamps.length) return timestamps;
                var data = this.getDataTimestamps();
                var label = this.getLabelTimestamps();
                if (data.length && label.length) timestamps = this.normalize(data.concat(label));
                else timestamps = data.length ? data : label;
                timestamps = this._cache.all = timestamps;
                return timestamps;
            }
        },
        {
            key: "getDecimalForValue",
            value: function getDecimalForValue(value) {
                return (interpolate(this._table, value) - this._minPos) / this._tableRange;
            }
        },
        {
            key: "getValueForPixel",
            value: function getValueForPixel(pixel) {
                var offsets = this._offsets;
                var decimal = this.getDecimalForPixel(pixel) / offsets.factor - offsets.end;
                return interpolate(this._table, decimal * this._tableRange + this._minPos, true);
            }
        }
    ]);
    return TimeSeriesScale;
}(TimeScale);
TimeSeriesScale.id = "timeseries";
TimeSeriesScale.defaults = TimeScale.defaults;
var scales = /*#__PURE__*/ Object.freeze({
    __proto__: null,
    CategoryScale: CategoryScale,
    LinearScale: LinearScale,
    LogarithmicScale: LogarithmicScale,
    RadialLinearScale: RadialLinearScale,
    TimeScale: TimeScale,
    TimeSeriesScale: TimeSeriesScale
});
var registerables = [
    controllers,
    elements,
    plugins,
    scales, 
];

},{"@swc/helpers/lib/_assert_this_initialized.js":"l7nF8","@swc/helpers/lib/_class_call_check.js":"gNxF8","@swc/helpers/lib/_create_class.js":"iyoaN","@swc/helpers/lib/_define_property.js":"6IXzf","@swc/helpers/lib/_get.js":"5g4pb","@swc/helpers/lib/_get_prototype_of.js":"7Gb6H","@swc/helpers/lib/_inherits.js":"atvDk","@swc/helpers/lib/_object_spread.js":"d5EJT","@swc/helpers/lib/_sliced_to_array.js":"4IWLM","@swc/helpers/lib/_to_consumable_array.js":"cccKv","@swc/helpers/lib/_type_of.js":"9FF45","@swc/helpers/lib/_wrap_native_super.js":"4U7ja","@swc/helpers/lib/_create_super.js":"5rW3S","./chunks/helpers.segment.js":"eXwLh","@parcel/transformer-js/src/esmodule-helpers.js":"jIm8e"}],"d5EJT":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _objectSpread;
var _defineProperty = _interopRequireDefault(require("./_define_property"));
function _objectSpread(target) {
    for(var i = 1; i < arguments.length; i++){
        var source = arguments[i] != null ? arguments[i] : {};
        var ownKeys = Object.keys(source);
        if (typeof Object.getOwnPropertySymbols === "function") ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function(sym) {
            return Object.getOwnPropertyDescriptor(source, sym).enumerable;
        }));
        ownKeys.forEach(function(key) {
            _defineProperty.default(target, key, source[key]);
        });
    }
    return target;
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}

},{"./_define_property":"6IXzf"}],"4U7ja":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _wrapNativeSuper;
var _construct = _interopRequireDefault(require("./_construct"));
var _isNativeFunction = _interopRequireDefault(require("./_is_native_function"));
var _getPrototypeOf = _interopRequireDefault(require("./_get_prototype_of"));
var _setPrototypeOf = _interopRequireDefault(require("./_set_prototype_of"));
function _wrapNativeSuper(Class) {
    return wrapNativeSuper(Class);
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}
function wrapNativeSuper(Class1) {
    var _cache = typeof Map === "function" ? new Map() : undefined;
    wrapNativeSuper = function wrapNativeSuper(Class) {
        if (Class === null || !_isNativeFunction.default(Class)) return Class;
        if (typeof Class !== "function") throw new TypeError("Super expression must either be null or a function");
        if (typeof _cache !== "undefined") {
            if (_cache.has(Class)) return _cache.get(Class);
            _cache.set(Class, Wrapper);
        }
        function Wrapper() {
            return _construct.default(Class, arguments, _getPrototypeOf.default(this).constructor);
        }
        Wrapper.prototype = Object.create(Class.prototype, {
            constructor: {
                value: Wrapper,
                enumerable: false,
                writable: true,
                configurable: true
            }
        });
        return _setPrototypeOf.default(Wrapper, Class);
    };
    return wrapNativeSuper(Class1);
}

},{"./_construct":"597Bk","./_is_native_function":"9evrd","./_get_prototype_of":"7Gb6H","./_set_prototype_of":"1rATD"}],"597Bk":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _construct;
var _setPrototypeOf = _interopRequireDefault(require("./_set_prototype_of"));
function _construct(Parent, args, Class) {
    return construct.apply(null, arguments);
}
function _interopRequireDefault(obj) {
    return obj && obj.__esModule ? obj : {
        default: obj
    };
}
function isNativeReflectConstruct() {
    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
    if (Reflect.construct.sham) return false;
    if (typeof Proxy === "function") return true;
    try {
        Date.prototype.toString.call(Reflect.construct(Date, [], function() {}));
        return true;
    } catch (e) {
        return false;
    }
}
function construct(Parent1, args1, Class1) {
    if (isNativeReflectConstruct()) construct = Reflect.construct;
    else construct = function construct(Parent, args, Class) {
        var a = [
            null
        ];
        a.push.apply(a, args);
        var Constructor = Function.bind.apply(Parent, a);
        var instance = new Constructor();
        if (Class) _setPrototypeOf.default(instance, Class.prototype);
        return instance;
    };
    return construct.apply(null, arguments);
}

},{"./_set_prototype_of":"1rATD"}],"9evrd":[function(require,module,exports) {
"use strict";
Object.defineProperty(exports, "__esModule", {
    value: true
});
exports.default = _isNativeFunction;
function _isNativeFunction(fn) {
    return Function.toString.call(fn).indexOf("[native code]") !== -1;
}

},{}],"eXwLh":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "$", function() {
    return toFont;
});
parcelHelpers.export(exports, "A", function() {
    return _rlookupByKey;
});
parcelHelpers.export(exports, "B", function() {
    return _isPointInArea;
});
parcelHelpers.export(exports, "C", function() {
    return getAngleFromPoint;
});
parcelHelpers.export(exports, "D", function() {
    return toPadding;
});
parcelHelpers.export(exports, "E", function() {
    return each;
});
parcelHelpers.export(exports, "F", function() {
    return getMaximumSize;
});
parcelHelpers.export(exports, "G", function() {
    return _getParentNode;
});
parcelHelpers.export(exports, "H", function() {
    return HALF_PI;
});
parcelHelpers.export(exports, "I", function() {
    return readUsedSize;
});
parcelHelpers.export(exports, "J", function() {
    return throttled;
});
parcelHelpers.export(exports, "K", function() {
    return supportsEventListenerOptions;
});
parcelHelpers.export(exports, "L", function() {
    return _isDomSupported;
});
parcelHelpers.export(exports, "M", function() {
    return log10;
});
parcelHelpers.export(exports, "N", function() {
    return _factorize;
});
parcelHelpers.export(exports, "O", function() {
    return finiteOrDefault;
});
parcelHelpers.export(exports, "P", function() {
    return PI;
});
parcelHelpers.export(exports, "Q", function() {
    return callback;
});
parcelHelpers.export(exports, "R", function() {
    return _addGrace;
});
parcelHelpers.export(exports, "S", function() {
    return toDegrees;
});
parcelHelpers.export(exports, "T", function() {
    return TAU;
});
parcelHelpers.export(exports, "U", function() {
    return _measureText;
});
parcelHelpers.export(exports, "V", function() {
    return _int16Range;
});
parcelHelpers.export(exports, "W", function() {
    return _alignPixel;
});
parcelHelpers.export(exports, "X", function() {
    return clipArea;
});
parcelHelpers.export(exports, "Y", function() {
    return renderText;
});
parcelHelpers.export(exports, "Z", function() {
    return unclipArea;
});
parcelHelpers.export(exports, "_", function() {
    return _arrayUnique;
});
parcelHelpers.export(exports, "a", function() {
    return resolve;
});
parcelHelpers.export(exports, "a$", function() {
    return QUARTER_PI;
});
parcelHelpers.export(exports, "a0", function() {
    return _toLeftRightCenter;
});
parcelHelpers.export(exports, "a1", function() {
    return _alignStartEnd;
});
parcelHelpers.export(exports, "a2", function() {
    return overrides;
});
parcelHelpers.export(exports, "a3", function() {
    return merge;
});
parcelHelpers.export(exports, "a4", function() {
    return _capitalize;
});
parcelHelpers.export(exports, "a5", function() {
    return descriptors;
});
parcelHelpers.export(exports, "a6", function() {
    return isFunction;
});
parcelHelpers.export(exports, "a7", function() {
    return _attachContext;
});
parcelHelpers.export(exports, "a8", function() {
    return _createResolver;
});
parcelHelpers.export(exports, "a9", function() {
    return _descriptors;
});
parcelHelpers.export(exports, "aA", function() {
    return _textX;
});
parcelHelpers.export(exports, "aB", function() {
    return restoreTextDirection;
});
parcelHelpers.export(exports, "aC", function() {
    return noop;
});
parcelHelpers.export(exports, "aD", function() {
    return distanceBetweenPoints;
});
parcelHelpers.export(exports, "aE", function() {
    return _setMinAndMaxByKey;
});
parcelHelpers.export(exports, "aF", function() {
    return niceNum;
});
parcelHelpers.export(exports, "aG", function() {
    return almostWhole;
});
parcelHelpers.export(exports, "aH", function() {
    return almostEquals;
});
parcelHelpers.export(exports, "aI", function() {
    return _decimalPlaces;
});
parcelHelpers.export(exports, "aJ", function() {
    return _longestText;
});
parcelHelpers.export(exports, "aK", function() {
    return _filterBetween;
});
parcelHelpers.export(exports, "aL", function() {
    return _lookup;
});
parcelHelpers.export(exports, "aM", function() {
    return isPatternOrGradient;
});
parcelHelpers.export(exports, "aN", function() {
    return getHoverColor;
});
parcelHelpers.export(exports, "aO", function() {
    return clone$1;
});
parcelHelpers.export(exports, "aP", function() {
    return _merger;
});
parcelHelpers.export(exports, "aQ", function() {
    return _mergerIf;
});
parcelHelpers.export(exports, "aR", function() {
    return _deprecated;
});
parcelHelpers.export(exports, "aS", function() {
    return toFontString;
});
parcelHelpers.export(exports, "aT", function() {
    return splineCurve;
});
parcelHelpers.export(exports, "aU", function() {
    return splineCurveMonotone;
});
parcelHelpers.export(exports, "aV", function() {
    return getStyle;
});
parcelHelpers.export(exports, "aW", function() {
    return fontString;
});
parcelHelpers.export(exports, "aX", function() {
    return toLineHeight;
});
parcelHelpers.export(exports, "aY", function() {
    return PITAU;
});
parcelHelpers.export(exports, "aZ", function() {
    return INFINITY;
});
parcelHelpers.export(exports, "a_", function() {
    return RAD_PER_DEG;
});
parcelHelpers.export(exports, "aa", function() {
    return mergeIf;
});
parcelHelpers.export(exports, "ab", function() {
    return uid;
});
parcelHelpers.export(exports, "ac", function() {
    return debounce;
});
parcelHelpers.export(exports, "ad", function() {
    return retinaScale;
});
parcelHelpers.export(exports, "ae", function() {
    return clearCanvas;
});
parcelHelpers.export(exports, "af", function() {
    return setsEqual;
});
parcelHelpers.export(exports, "ag", function() {
    return _elementsEqual;
});
parcelHelpers.export(exports, "ah", function() {
    return _isClickEvent;
});
parcelHelpers.export(exports, "ai", function() {
    return _isBetween;
});
parcelHelpers.export(exports, "aj", function() {
    return _readValueToProps;
});
parcelHelpers.export(exports, "ak", function() {
    return _updateBezierControlPoints;
});
parcelHelpers.export(exports, "al", function() {
    return _computeSegments;
});
parcelHelpers.export(exports, "am", function() {
    return _boundSegments;
});
parcelHelpers.export(exports, "an", function() {
    return _steppedInterpolation;
});
parcelHelpers.export(exports, "ao", function() {
    return _bezierInterpolation;
});
parcelHelpers.export(exports, "ap", function() {
    return _pointInLine;
});
parcelHelpers.export(exports, "aq", function() {
    return _steppedLineTo;
});
parcelHelpers.export(exports, "ar", function() {
    return _bezierCurveTo;
});
parcelHelpers.export(exports, "as", function() {
    return drawPoint;
});
parcelHelpers.export(exports, "at", function() {
    return addRoundedRectPath;
});
parcelHelpers.export(exports, "au", function() {
    return toTRBL;
});
parcelHelpers.export(exports, "av", function() {
    return toTRBLCorners;
});
parcelHelpers.export(exports, "aw", function() {
    return _boundSegment;
});
parcelHelpers.export(exports, "ax", function() {
    return _normalizeAngle;
});
parcelHelpers.export(exports, "ay", function() {
    return getRtlAdapter;
});
parcelHelpers.export(exports, "az", function() {
    return overrideTextDirection;
});
parcelHelpers.export(exports, "b", function() {
    return isArray;
});
parcelHelpers.export(exports, "b0", function() {
    return TWO_THIRDS_PI;
});
parcelHelpers.export(exports, "b1", function() {
    return _angleDiff;
});
parcelHelpers.export(exports, "c", function() {
    return color;
});
parcelHelpers.export(exports, "d", function() {
    return defaults;
});
parcelHelpers.export(exports, "e", function() {
    return effects;
});
parcelHelpers.export(exports, "f", function() {
    return resolveObjectKey;
});
parcelHelpers.export(exports, "g", function() {
    return isNumberFinite;
});
parcelHelpers.export(exports, "h", function() {
    return createContext;
});
parcelHelpers.export(exports, "i", function() {
    return isObject;
});
parcelHelpers.export(exports, "j", function() {
    return defined;
});
parcelHelpers.export(exports, "k", function() {
    return isNullOrUndef;
});
parcelHelpers.export(exports, "l", function() {
    return listenArrayEvents;
});
parcelHelpers.export(exports, "m", function() {
    return toPercentage;
});
parcelHelpers.export(exports, "n", function() {
    return toDimension;
});
parcelHelpers.export(exports, "o", function() {
    return formatNumber;
});
parcelHelpers.export(exports, "p", function() {
    return _angleBetween;
});
parcelHelpers.export(exports, "q", function() {
    return isNumber;
});
parcelHelpers.export(exports, "r", function() {
    return requestAnimFrame;
});
parcelHelpers.export(exports, "s", function() {
    return sign;
});
parcelHelpers.export(exports, "t", function() {
    return toRadians;
});
parcelHelpers.export(exports, "u", function() {
    return unlistenArrayEvents;
});
parcelHelpers.export(exports, "v", function() {
    return valueOrDefault;
});
parcelHelpers.export(exports, "w", function() {
    return _limitValue;
});
parcelHelpers.export(exports, "x", function() {
    return _lookupByKey;
});
parcelHelpers.export(exports, "y", function() {
    return _parseObjectDataRadialScale;
});
parcelHelpers.export(exports, "z", function() {
    return getRelativePosition;
});
var _classCallCheckJs = require("@swc/helpers/lib/_class_call_check.js");
var _classCallCheckJsDefault = parcelHelpers.interopDefault(_classCallCheckJs);
var _createClassJs = require("@swc/helpers/lib/_create_class.js");
var _createClassJsDefault = parcelHelpers.interopDefault(_createClassJs);
var _definePropertyJs = require("@swc/helpers/lib/_define_property.js");
var _definePropertyJsDefault = parcelHelpers.interopDefault(_definePropertyJs);
var _toConsumableArrayJs = require("@swc/helpers/lib/_to_consumable_array.js");
var _toConsumableArrayJsDefault = parcelHelpers.interopDefault(_toConsumableArrayJs);
var _typeOfJs = require("@swc/helpers/lib/_type_of.js");
var _typeOfJsDefault = parcelHelpers.interopDefault(_typeOfJs);
/*!
 * Chart.js v3.8.0
 * https://www.chartjs.org
 * (c) 2022 Chart.js Contributors
 * Released under the MIT License
 */ function fontString(pixelSize, fontStyle, fontFamily) {
    return fontStyle + " " + pixelSize + "px " + fontFamily;
}
var requestAnimFrame = function() {
    if (typeof window === "undefined") return function(callback1) {
        return callback1();
    };
    return window.requestAnimationFrame;
}();
function throttled(fn, thisArg, updateFn) {
    var updateArgs = updateFn || function(args) {
        return Array.prototype.slice.call(args);
    };
    var ticking = false;
    var args1 = [];
    return function() {
        for(var _len = arguments.length, rest = new Array(_len), _key = 0; _key < _len; _key++){
            rest[_key] = arguments[_key];
        }
        args1 = updateArgs(rest);
        if (!ticking) {
            ticking = true;
            requestAnimFrame.call(window, function() {
                ticking = false;
                fn.apply(thisArg, args1);
            });
        }
    };
}
function debounce(fn, delay) {
    var timeout;
    return function() {
        for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
            args[_key] = arguments[_key];
        }
        if (delay) {
            clearTimeout(timeout);
            timeout = setTimeout(fn, delay, args);
        } else fn.apply(this, args);
        return delay;
    };
}
var _toLeftRightCenter = function(align) {
    return align === "start" ? "left" : align === "end" ? "right" : "center";
};
var _alignStartEnd = function(align, start, end) {
    return align === "start" ? start : align === "end" ? end : (start + end) / 2;
};
var _textX = function(align, left, right, rtl) {
    var check = rtl ? "left" : "right";
    return align === check ? right : align === "center" ? (left + right) / 2 : left;
};
function noop() {}
var uid = function() {
    var id = 0;
    return function() {
        return id++;
    };
}();
function isNullOrUndef(value) {
    return value === null || typeof value === "undefined";
}
function isArray(value) {
    if (Array.isArray && Array.isArray(value)) return true;
    var type = Object.prototype.toString.call(value);
    if (type.slice(0, 7) === "[object" && type.slice(-6) === "Array]") return true;
    return false;
}
function isObject(value) {
    return value !== null && Object.prototype.toString.call(value) === "[object Object]";
}
var isNumberFinite = function(value) {
    return (typeof value === "number" || value instanceof Number) && isFinite(+value);
};
function finiteOrDefault(value, defaultValue) {
    return isNumberFinite(value) ? value : defaultValue;
}
function valueOrDefault(value, defaultValue) {
    return typeof value === "undefined" ? defaultValue : value;
}
var toPercentage = function(value, dimension) {
    return typeof value === "string" && value.endsWith("%") ? parseFloat(value) / 100 : value / dimension;
};
var toDimension = function(value, dimension) {
    return typeof value === "string" && value.endsWith("%") ? parseFloat(value) / 100 * dimension : +value;
};
function callback(fn, args, thisArg) {
    if (fn && typeof fn.call === "function") return fn.apply(thisArg, args);
}
function each(loopable, fn, thisArg, reverse) {
    var i, len, keys;
    if (isArray(loopable)) {
        len = loopable.length;
        if (reverse) for(i = len - 1; i >= 0; i--)fn.call(thisArg, loopable[i], i);
        else for(i = 0; i < len; i++)fn.call(thisArg, loopable[i], i);
    } else if (isObject(loopable)) {
        keys = Object.keys(loopable);
        len = keys.length;
        for(i = 0; i < len; i++)fn.call(thisArg, loopable[keys[i]], keys[i]);
    }
}
function _elementsEqual(a0, a1) {
    var i, ilen, v0, v1;
    if (!a0 || !a1 || a0.length !== a1.length) return false;
    for(i = 0, ilen = a0.length; i < ilen; ++i){
        v0 = a0[i];
        v1 = a1[i];
        if (v0.datasetIndex !== v1.datasetIndex || v0.index !== v1.index) return false;
    }
    return true;
}
function clone$1(source) {
    if (isArray(source)) return source.map(clone$1);
    if (isObject(source)) {
        var target = Object.create(null);
        var keys = Object.keys(source);
        var klen = keys.length;
        var k = 0;
        for(; k < klen; ++k)target[keys[k]] = clone$1(source[keys[k]]);
        return target;
    }
    return source;
}
function isValidKey(key) {
    return [
        "__proto__",
        "prototype",
        "constructor"
    ].indexOf(key) === -1;
}
function _merger(key, target, source, options) {
    if (!isValidKey(key)) return;
    var tval = target[key];
    var sval = source[key];
    if (isObject(tval) && isObject(sval)) merge(tval, sval, options);
    else target[key] = clone$1(sval);
}
function merge(target, source, options) {
    var sources = isArray(source) ? source : [
        source
    ];
    var ilen = sources.length;
    if (!isObject(target)) return target;
    options = options || {};
    var merger = options.merger || _merger;
    for(var i = 0; i < ilen; ++i){
        source = sources[i];
        if (!isObject(source)) continue;
        var keys = Object.keys(source);
        for(var k = 0, klen = keys.length; k < klen; ++k)merger(keys[k], target, source, options);
    }
    return target;
}
function mergeIf(target, source) {
    return merge(target, source, {
        merger: _mergerIf
    });
}
function _mergerIf(key, target, source) {
    if (!isValidKey(key)) return;
    var tval = target[key];
    var sval = source[key];
    if (isObject(tval) && isObject(sval)) mergeIf(tval, sval);
    else if (!Object.prototype.hasOwnProperty.call(target, key)) target[key] = clone$1(sval);
}
function _deprecated(scope, value, previous, current) {
    if (value !== undefined) console.warn(scope + ': "' + previous + '" is deprecated. Please use "' + current + '" instead');
}
var emptyString = "";
var dot = ".";
function indexOfDotOrLength(key, start) {
    var idx = key.indexOf(dot, start);
    return idx === -1 ? key.length : idx;
}
function resolveObjectKey(obj, key) {
    if (key === emptyString) return obj;
    var pos = 0;
    var idx = indexOfDotOrLength(key, pos);
    while(obj && idx > pos){
        obj = obj[key.slice(pos, idx)];
        pos = idx + 1;
        idx = indexOfDotOrLength(key, pos);
    }
    return obj;
}
function _capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
var defined = function(value) {
    return typeof value !== "undefined";
};
var isFunction = function(value) {
    return typeof value === "function";
};
var setsEqual = function(a, b) {
    if (a.size !== b.size) return false;
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = a[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var item = _step.value;
            if (!b.has(item)) return false;
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
    return true;
};
function _isClickEvent(e) {
    return e.type === "mouseup" || e.type === "click" || e.type === "contextmenu";
}
var PI = Math.PI;
var TAU = 2 * PI;
var PITAU = TAU + PI;
var INFINITY = Number.POSITIVE_INFINITY;
var RAD_PER_DEG = PI / 180;
var HALF_PI = PI / 2;
var QUARTER_PI = PI / 4;
var TWO_THIRDS_PI = PI * 2 / 3;
var log10 = Math.log10;
var sign = Math.sign;
function niceNum(range) {
    var roundedRange = Math.round(range);
    range = almostEquals(range, roundedRange, range / 1000) ? roundedRange : range;
    var niceRange = Math.pow(10, Math.floor(log10(range)));
    var fraction = range / niceRange;
    var niceFraction = fraction <= 1 ? 1 : fraction <= 2 ? 2 : fraction <= 5 ? 5 : 10;
    return niceFraction * niceRange;
}
function _factorize(value) {
    var result = [];
    var sqrt = Math.sqrt(value);
    var i;
    for(i = 1; i < sqrt; i++)if (value % i === 0) {
        result.push(i);
        result.push(value / i);
    }
    if (sqrt === (sqrt | 0)) result.push(sqrt);
    result.sort(function(a, b) {
        return a - b;
    }).pop();
    return result;
}
function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}
function almostEquals(x, y, epsilon) {
    return Math.abs(x - y) < epsilon;
}
function almostWhole(x, epsilon) {
    var rounded = Math.round(x);
    return rounded - epsilon <= x && rounded + epsilon >= x;
}
function _setMinAndMaxByKey(array, target, property) {
    var i, ilen, value;
    for(i = 0, ilen = array.length; i < ilen; i++){
        value = array[i][property];
        if (!isNaN(value)) {
            target.min = Math.min(target.min, value);
            target.max = Math.max(target.max, value);
        }
    }
}
function toRadians(degrees) {
    return degrees * (PI / 180);
}
function toDegrees(radians) {
    return radians * (180 / PI);
}
function _decimalPlaces(x) {
    if (!isNumberFinite(x)) return;
    var e = 1;
    var p = 0;
    while(Math.round(x * e) / e !== x){
        e *= 10;
        p++;
    }
    return p;
}
function getAngleFromPoint(centrePoint, anglePoint) {
    var distanceFromXCenter = anglePoint.x - centrePoint.x;
    var distanceFromYCenter = anglePoint.y - centrePoint.y;
    var radialDistanceFromCenter = Math.sqrt(distanceFromXCenter * distanceFromXCenter + distanceFromYCenter * distanceFromYCenter);
    var angle = Math.atan2(distanceFromYCenter, distanceFromXCenter);
    if (angle < -0.5 * PI) angle += TAU;
    return {
        angle: angle,
        distance: radialDistanceFromCenter
    };
}
function distanceBetweenPoints(pt1, pt2) {
    return Math.sqrt(Math.pow(pt2.x - pt1.x, 2) + Math.pow(pt2.y - pt1.y, 2));
}
function _angleDiff(a, b) {
    return (a - b + PITAU) % TAU - PI;
}
function _normalizeAngle(a) {
    return (a % TAU + TAU) % TAU;
}
function _angleBetween(angle, start, end, sameAngleIsFullCircle) {
    var a = _normalizeAngle(angle);
    var s = _normalizeAngle(start);
    var e = _normalizeAngle(end);
    var angleToStart = _normalizeAngle(s - a);
    var angleToEnd = _normalizeAngle(e - a);
    var startToAngle = _normalizeAngle(a - s);
    var endToAngle = _normalizeAngle(a - e);
    return a === s || a === e || sameAngleIsFullCircle && s === e || angleToStart > angleToEnd && startToAngle < endToAngle;
}
function _limitValue(value, min, max) {
    return Math.max(min, Math.min(max, value));
}
function _int16Range(value) {
    return _limitValue(value, -32768, 32767);
}
function _isBetween(value, start, end) {
    var epsilon = arguments.length > 3 && arguments[3] !== void 0 ? arguments[3] : 1e-6;
    return value >= Math.min(start, end) - epsilon && value <= Math.max(start, end) + epsilon;
}
var atEdge = function(t) {
    return t === 0 || t === 1;
};
var elasticIn = function(t, s, p) {
    return -(Math.pow(2, 10 * (t -= 1)) * Math.sin((t - s) * TAU / p));
};
var elasticOut = function(t, s, p) {
    return Math.pow(2, -10 * t) * Math.sin((t - s) * TAU / p) + 1;
};
var effects = {
    linear: function(t) {
        return t;
    },
    easeInQuad: function(t) {
        return t * t;
    },
    easeOutQuad: function(t) {
        return -t * (t - 2);
    },
    easeInOutQuad: function(t) {
        return (t /= 0.5) < 1 ? 0.5 * t * t : -0.5 * (--t * (t - 2) - 1);
    },
    easeInCubic: function(t) {
        return t * t * t;
    },
    easeOutCubic: function(t) {
        return (t -= 1) * t * t + 1;
    },
    easeInOutCubic: function(t) {
        return (t /= 0.5) < 1 ? 0.5 * t * t * t : 0.5 * ((t -= 2) * t * t + 2);
    },
    easeInQuart: function(t) {
        return t * t * t * t;
    },
    easeOutQuart: function(t) {
        return -((t -= 1) * t * t * t - 1);
    },
    easeInOutQuart: function(t) {
        return (t /= 0.5) < 1 ? 0.5 * t * t * t * t : -0.5 * ((t -= 2) * t * t * t - 2);
    },
    easeInQuint: function(t) {
        return t * t * t * t * t;
    },
    easeOutQuint: function(t) {
        return (t -= 1) * t * t * t * t + 1;
    },
    easeInOutQuint: function(t) {
        return (t /= 0.5) < 1 ? 0.5 * t * t * t * t * t : 0.5 * ((t -= 2) * t * t * t * t + 2);
    },
    easeInSine: function(t) {
        return -Math.cos(t * HALF_PI) + 1;
    },
    easeOutSine: function(t) {
        return Math.sin(t * HALF_PI);
    },
    easeInOutSine: function(t) {
        return -0.5 * (Math.cos(PI * t) - 1);
    },
    easeInExpo: function(t) {
        return t === 0 ? 0 : Math.pow(2, 10 * (t - 1));
    },
    easeOutExpo: function(t) {
        return t === 1 ? 1 : -Math.pow(2, -10 * t) + 1;
    },
    easeInOutExpo: function(t) {
        return atEdge(t) ? t : t < 0.5 ? 0.5 * Math.pow(2, 10 * (t * 2 - 1)) : 0.5 * (-Math.pow(2, -10 * (t * 2 - 1)) + 2);
    },
    easeInCirc: function(t) {
        return t >= 1 ? t : -(Math.sqrt(1 - t * t) - 1);
    },
    easeOutCirc: function(t) {
        return Math.sqrt(1 - (t -= 1) * t);
    },
    easeInOutCirc: function(t) {
        return (t /= 0.5) < 1 ? -0.5 * (Math.sqrt(1 - t * t) - 1) : 0.5 * (Math.sqrt(1 - (t -= 2) * t) + 1);
    },
    easeInElastic: function(t) {
        return atEdge(t) ? t : elasticIn(t, 0.075, 0.3);
    },
    easeOutElastic: function(t) {
        return atEdge(t) ? t : elasticOut(t, 0.075, 0.3);
    },
    easeInOutElastic: function(t) {
        var s = 0.1125;
        var p = 0.45;
        return atEdge(t) ? t : t < 0.5 ? 0.5 * elasticIn(t * 2, s, p) : 0.5 + 0.5 * elasticOut(t * 2 - 1, s, p);
    },
    easeInBack: function(t) {
        var s = 1.70158;
        return t * t * ((s + 1) * t - s);
    },
    easeOutBack: function(t) {
        var s = 1.70158;
        return (t -= 1) * t * ((s + 1) * t + s) + 1;
    },
    easeInOutBack: function(t) {
        var s = 1.70158;
        if ((t /= 0.5) < 1) return 0.5 * (t * t * (((s *= 1.525) + 1) * t - s));
        return 0.5 * ((t -= 2) * t * (((s *= 1.525) + 1) * t + s) + 2);
    },
    easeInBounce: function(t) {
        return 1 - effects.easeOutBounce(1 - t);
    },
    easeOutBounce: function(t) {
        var m = 7.5625;
        var d = 2.75;
        if (t < 1 / d) return m * t * t;
        if (t < 2 / d) return m * (t -= 1.5 / d) * t + 0.75;
        if (t < 2.5 / d) return m * (t -= 2.25 / d) * t + 0.9375;
        return m * (t -= 2.625 / d) * t + 0.984375;
    },
    easeInOutBounce: function(t) {
        return t < 0.5 ? effects.easeInBounce(t * 2) * 0.5 : effects.easeOutBounce(t * 2 - 1) * 0.5 + 0.5;
    }
};
/*!
 * @kurkle/color v0.2.1
 * https://github.com/kurkle/color#readme
 * (c) 2022 Jukka Kurkela
 * Released under the MIT License
 */ function round(v) {
    return v + 0.5 | 0;
}
var lim = function(v, l, h) {
    return Math.max(Math.min(v, h), l);
};
function p2b(v) {
    return lim(round(v * 2.55), 0, 255);
}
function n2b(v) {
    return lim(round(v * 255), 0, 255);
}
function b2n(v) {
    return lim(round(v / 2.55) / 100, 0, 1);
}
function n2p(v) {
    return lim(round(v * 100), 0, 100);
}
var map$1 = {
    0: 0,
    1: 1,
    2: 2,
    3: 3,
    4: 4,
    5: 5,
    6: 6,
    7: 7,
    8: 8,
    9: 9,
    A: 10,
    B: 11,
    C: 12,
    D: 13,
    E: 14,
    F: 15,
    a: 10,
    b: 11,
    c: 12,
    d: 13,
    e: 14,
    f: 15
};
var hex = Array.from("0123456789ABCDEF");
var h1 = function(b) {
    return hex[b & 0xF];
};
var h2 = function(b) {
    return hex[(b & 0xF0) >> 4] + hex[b & 0xF];
};
var eq = function(b) {
    return (b & 0xF0) >> 4 === (b & 0xF);
};
var isShort = function(v) {
    return eq(v.r) && eq(v.g) && eq(v.b) && eq(v.a);
};
function hexParse(str) {
    var len = str.length;
    var ret;
    if (str[0] === "#") {
        if (len === 4 || len === 5) ret = {
            r: 255 & map$1[str[1]] * 17,
            g: 255 & map$1[str[2]] * 17,
            b: 255 & map$1[str[3]] * 17,
            a: len === 5 ? map$1[str[4]] * 17 : 255
        };
        else if (len === 7 || len === 9) ret = {
            r: map$1[str[1]] << 4 | map$1[str[2]],
            g: map$1[str[3]] << 4 | map$1[str[4]],
            b: map$1[str[5]] << 4 | map$1[str[6]],
            a: len === 9 ? map$1[str[7]] << 4 | map$1[str[8]] : 255
        };
    }
    return ret;
}
var alpha = function(a, f) {
    return a < 255 ? f(a) : "";
};
function hexString(v) {
    var f = isShort(v) ? h1 : h2;
    return v ? "#" + f(v.r) + f(v.g) + f(v.b) + alpha(v.a, f) : undefined;
}
var HUE_RE = /^(hsla?|hwb|hsv)\(\s*([-+.e\d]+)(?:deg)?[\s,]+([-+.e\d]+)%[\s,]+([-+.e\d]+)%(?:[\s,]+([-+.e\d]+)(%)?)?\s*\)$/;
function hsl2rgbn(h, s, l) {
    var a = s * Math.min(l, 1 - l);
    var f = function(n) {
        var k = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : (n + h / 30) % 12;
        return l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
    };
    return [
        f(0),
        f(8),
        f(4)
    ];
}
function hsv2rgbn(h, s, v) {
    var f = function(n) {
        var k = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : (n + h / 60) % 6;
        return v - v * s * Math.max(Math.min(k, 4 - k, 1), 0);
    };
    return [
        f(5),
        f(3),
        f(1)
    ];
}
function hwb2rgbn(h, w, b) {
    var rgb = hsl2rgbn(h, 1, 0.5);
    var i;
    if (w + b > 1) {
        i = 1 / (w + b);
        w *= i;
        b *= i;
    }
    for(i = 0; i < 3; i++){
        rgb[i] *= 1 - w - b;
        rgb[i] += w;
    }
    return rgb;
}
function hueValue(r, g, b, d, max) {
    if (r === max) return (g - b) / d + (g < b ? 6 : 0);
    if (g === max) return (b - r) / d + 2;
    return (r - g) / d + 4;
}
function rgb2hsl(v) {
    var range = 255;
    var r = v.r / range;
    var g = v.g / range;
    var b = v.b / range;
    var max = Math.max(r, g, b);
    var min = Math.min(r, g, b);
    var l = (max + min) / 2;
    var h, s, d;
    if (max !== min) {
        d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        h = hueValue(r, g, b, d, max);
        h = h * 60 + 0.5;
    }
    return [
        h | 0,
        s || 0,
        l
    ];
}
function calln(f, a, b, c) {
    return (Array.isArray(a) ? f(a[0], a[1], a[2]) : f(a, b, c)).map(n2b);
}
function hsl2rgb(h, s, l) {
    return calln(hsl2rgbn, h, s, l);
}
function hwb2rgb(h, w, b) {
    return calln(hwb2rgbn, h, w, b);
}
function hsv2rgb(h, s, v) {
    return calln(hsv2rgbn, h, s, v);
}
function hue(h) {
    return (h % 360 + 360) % 360;
}
function hueParse(str) {
    var m = HUE_RE.exec(str);
    var a = 255;
    var v;
    if (!m) return;
    if (m[5] !== v) a = m[6] ? p2b(+m[5]) : n2b(+m[5]);
    var h = hue(+m[2]);
    var p1 = +m[3] / 100;
    var p2 = +m[4] / 100;
    if (m[1] === "hwb") v = hwb2rgb(h, p1, p2);
    else if (m[1] === "hsv") v = hsv2rgb(h, p1, p2);
    else v = hsl2rgb(h, p1, p2);
    return {
        r: v[0],
        g: v[1],
        b: v[2],
        a: a
    };
}
function rotate(v, deg) {
    var h = rgb2hsl(v);
    h[0] = hue(h[0] + deg);
    h = hsl2rgb(h);
    v.r = h[0];
    v.g = h[1];
    v.b = h[2];
}
function hslString(v) {
    if (!v) return;
    var a = rgb2hsl(v);
    var h = a[0];
    var s = n2p(a[1]);
    var l = n2p(a[2]);
    return v.a < 255 ? "hsla(".concat(h, ", ").concat(s, "%, ").concat(l, "%, ").concat(b2n(v.a), ")") : "hsl(".concat(h, ", ").concat(s, "%, ").concat(l, "%)");
}
var map = {
    x: "dark",
    Z: "light",
    Y: "re",
    X: "blu",
    W: "gr",
    V: "medium",
    U: "slate",
    A: "ee",
    T: "ol",
    S: "or",
    B: "ra",
    C: "lateg",
    D: "ights",
    R: "in",
    Q: "turquois",
    E: "hi",
    P: "ro",
    O: "al",
    N: "le",
    M: "de",
    L: "yello",
    F: "en",
    K: "ch",
    G: "arks",
    H: "ea",
    I: "ightg",
    J: "wh"
};
var names$1 = {
    OiceXe: "f0f8ff",
    antiquewEte: "faebd7",
    aqua: "ffff",
    aquamarRe: "7fffd4",
    azuY: "f0ffff",
    beige: "f5f5dc",
    bisque: "ffe4c4",
    black: "0",
    blanKedOmond: "ffebcd",
    Xe: "ff",
    XeviTet: "8a2be2",
    bPwn: "a52a2a",
    burlywood: "deb887",
    caMtXe: "5f9ea0",
    KartYuse: "7fff00",
    KocTate: "d2691e",
    cSO: "ff7f50",
    cSnflowerXe: "6495ed",
    cSnsilk: "fff8dc",
    crimson: "dc143c",
    cyan: "ffff",
    xXe: "8b",
    xcyan: "8b8b",
    xgTMnPd: "b8860b",
    xWay: "a9a9a9",
    xgYF: "6400",
    xgYy: "a9a9a9",
    xkhaki: "bdb76b",
    xmagFta: "8b008b",
    xTivegYF: "556b2f",
    xSange: "ff8c00",
    xScEd: "9932cc",
    xYd: "8b0000",
    xsOmon: "e9967a",
    xsHgYF: "8fbc8f",
    xUXe: "483d8b",
    xUWay: "2f4f4f",
    xUgYy: "2f4f4f",
    xQe: "ced1",
    xviTet: "9400d3",
    dAppRk: "ff1493",
    dApskyXe: "bfff",
    dimWay: "696969",
    dimgYy: "696969",
    dodgerXe: "1e90ff",
    fiYbrick: "b22222",
    flSOwEte: "fffaf0",
    foYstWAn: "228b22",
    fuKsia: "ff00ff",
    gaRsbSo: "dcdcdc",
    ghostwEte: "f8f8ff",
    gTd: "ffd700",
    gTMnPd: "daa520",
    Way: "808080",
    gYF: "8000",
    gYFLw: "adff2f",
    gYy: "808080",
    honeyMw: "f0fff0",
    hotpRk: "ff69b4",
    RdianYd: "cd5c5c",
    Rdigo: "4b0082",
    ivSy: "fffff0",
    khaki: "f0e68c",
    lavFMr: "e6e6fa",
    lavFMrXsh: "fff0f5",
    lawngYF: "7cfc00",
    NmoncEffon: "fffacd",
    ZXe: "add8e6",
    ZcSO: "f08080",
    Zcyan: "e0ffff",
    ZgTMnPdLw: "fafad2",
    ZWay: "d3d3d3",
    ZgYF: "90ee90",
    ZgYy: "d3d3d3",
    ZpRk: "ffb6c1",
    ZsOmon: "ffa07a",
    ZsHgYF: "20b2aa",
    ZskyXe: "87cefa",
    ZUWay: "778899",
    ZUgYy: "778899",
    ZstAlXe: "b0c4de",
    ZLw: "ffffe0",
    lime: "ff00",
    limegYF: "32cd32",
    lRF: "faf0e6",
    magFta: "ff00ff",
    maPon: "800000",
    VaquamarRe: "66cdaa",
    VXe: "cd",
    VScEd: "ba55d3",
    VpurpN: "9370db",
    VsHgYF: "3cb371",
    VUXe: "7b68ee",
    VsprRggYF: "fa9a",
    VQe: "48d1cc",
    VviTetYd: "c71585",
    midnightXe: "191970",
    mRtcYam: "f5fffa",
    mistyPse: "ffe4e1",
    moccasR: "ffe4b5",
    navajowEte: "ffdead",
    navy: "80",
    Tdlace: "fdf5e6",
    Tive: "808000",
    TivedBb: "6b8e23",
    Sange: "ffa500",
    SangeYd: "ff4500",
    ScEd: "da70d6",
    pOegTMnPd: "eee8aa",
    pOegYF: "98fb98",
    pOeQe: "afeeee",
    pOeviTetYd: "db7093",
    papayawEp: "ffefd5",
    pHKpuff: "ffdab9",
    peru: "cd853f",
    pRk: "ffc0cb",
    plum: "dda0dd",
    powMrXe: "b0e0e6",
    purpN: "800080",
    YbeccapurpN: "663399",
    Yd: "ff0000",
    Psybrown: "bc8f8f",
    PyOXe: "4169e1",
    saddNbPwn: "8b4513",
    sOmon: "fa8072",
    sandybPwn: "f4a460",
    sHgYF: "2e8b57",
    sHshell: "fff5ee",
    siFna: "a0522d",
    silver: "c0c0c0",
    skyXe: "87ceeb",
    UXe: "6a5acd",
    UWay: "708090",
    UgYy: "708090",
    snow: "fffafa",
    sprRggYF: "ff7f",
    stAlXe: "4682b4",
    tan: "d2b48c",
    teO: "8080",
    tEstN: "d8bfd8",
    tomato: "ff6347",
    Qe: "40e0d0",
    viTet: "ee82ee",
    JHt: "f5deb3",
    wEte: "ffffff",
    wEtesmoke: "f5f5f5",
    Lw: "ffff00",
    LwgYF: "9acd32"
};
function unpack() {
    var unpacked = {};
    var keys = Object.keys(names$1);
    var tkeys = Object.keys(map);
    var i, j, k, ok, nk;
    for(i = 0; i < keys.length; i++){
        ok = nk = keys[i];
        for(j = 0; j < tkeys.length; j++){
            k = tkeys[j];
            nk = nk.replace(k, map[k]);
        }
        k = parseInt(names$1[ok], 16);
        unpacked[nk] = [
            k >> 16 & 0xFF,
            k >> 8 & 0xFF,
            k & 0xFF
        ];
    }
    return unpacked;
}
var names;
function nameParse(str) {
    if (!names) {
        names = unpack();
        names.transparent = [
            0,
            0,
            0,
            0
        ];
    }
    var a = names[str.toLowerCase()];
    return a && {
        r: a[0],
        g: a[1],
        b: a[2],
        a: a.length === 4 ? a[3] : 255
    };
}
var RGB_RE = /^rgba?\(\s*([-+.\d]+)(%)?[\s,]+([-+.e\d]+)(%)?[\s,]+([-+.e\d]+)(%)?(?:[\s,/]+([-+.e\d]+)(%)?)?\s*\)$/;
function rgbParse(str) {
    var m = RGB_RE.exec(str);
    var a = 255;
    var r, g, b;
    if (!m) return;
    if (m[7] !== r) {
        var v = +m[7];
        a = m[8] ? p2b(v) : lim(v * 255, 0, 255);
    }
    r = +m[1];
    g = +m[3];
    b = +m[5];
    r = 255 & (m[2] ? p2b(r) : lim(r, 0, 255));
    g = 255 & (m[4] ? p2b(g) : lim(g, 0, 255));
    b = 255 & (m[6] ? p2b(b) : lim(b, 0, 255));
    return {
        r: r,
        g: g,
        b: b,
        a: a
    };
}
function rgbString(v) {
    return v && (v.a < 255 ? "rgba(".concat(v.r, ", ").concat(v.g, ", ").concat(v.b, ", ").concat(b2n(v.a), ")") : "rgb(".concat(v.r, ", ").concat(v.g, ", ").concat(v.b, ")"));
}
var to = function(v) {
    return v <= 0.0031308 ? v * 12.92 : Math.pow(v, 1.0 / 2.4) * 1.055 - 0.055;
};
var from = function(v) {
    return v <= 0.04045 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
};
function interpolate(rgb1, rgb2, t) {
    var r = from(b2n(rgb1.r));
    var g = from(b2n(rgb1.g));
    var b = from(b2n(rgb1.b));
    return {
        r: n2b(to(r + t * (from(b2n(rgb2.r)) - r))),
        g: n2b(to(g + t * (from(b2n(rgb2.g)) - g))),
        b: n2b(to(b + t * (from(b2n(rgb2.b)) - b))),
        a: rgb1.a + t * (rgb2.a - rgb1.a)
    };
}
function modHSL(v, i, ratio) {
    if (v) {
        var tmp = rgb2hsl(v);
        tmp[i] = Math.max(0, Math.min(tmp[i] + tmp[i] * ratio, i === 0 ? 360 : 1));
        tmp = hsl2rgb(tmp);
        v.r = tmp[0];
        v.g = tmp[1];
        v.b = tmp[2];
    }
}
function clone(v, proto) {
    return v ? Object.assign(proto || {}, v) : v;
}
function fromObject(input) {
    var v = {
        r: 0,
        g: 0,
        b: 0,
        a: 255
    };
    if (Array.isArray(input)) {
        if (input.length >= 3) {
            v = {
                r: input[0],
                g: input[1],
                b: input[2],
                a: 255
            };
            if (input.length > 3) v.a = n2b(input[3]);
        }
    } else {
        v = clone(input, {
            r: 0,
            g: 0,
            b: 0,
            a: 1
        });
        v.a = n2b(v.a);
    }
    return v;
}
function functionParse(str) {
    if (str.charAt(0) === "r") return rgbParse(str);
    return hueParse(str);
}
var Color = /*#__PURE__*/ function() {
    "use strict";
    function Color(input) {
        (0, _classCallCheckJsDefault.default)(this, Color);
        if (input instanceof Color) return input;
        var type = typeof input === "undefined" ? "undefined" : (0, _typeOfJsDefault.default)(input);
        var v;
        if (type === "object") v = fromObject(input);
        else if (type === "string") v = hexParse(input) || nameParse(input) || functionParse(input);
        this._rgb = v;
        this._valid = !!v;
    }
    (0, _createClassJsDefault.default)(Color, [
        {
            key: "valid",
            get: function get() {
                return this._valid;
            }
        },
        {
            key: "rgb",
            get: function get() {
                var v = clone(this._rgb);
                if (v) v.a = b2n(v.a);
                return v;
            },
            set: function set1(obj) {
                this._rgb = fromObject(obj);
            }
        },
        {
            key: "rgbString",
            value: function rgbString1() {
                return this._valid ? rgbString(this._rgb) : undefined;
            }
        },
        {
            key: "hexString",
            value: function hexString1() {
                return this._valid ? hexString(this._rgb) : undefined;
            }
        },
        {
            key: "hslString",
            value: function hslString1() {
                return this._valid ? hslString(this._rgb) : undefined;
            }
        },
        {
            key: "mix",
            value: function mix(color1, weight) {
                if (color1) {
                    var c1 = this.rgb;
                    var c2 = color1.rgb;
                    var w2;
                    var p = weight === w2 ? 0.5 : weight;
                    var w = 2 * p - 1;
                    var a = c1.a - c2.a;
                    var w1 = ((w * a === -1 ? w : (w + a) / (1 + w * a)) + 1) / 2.0;
                    w2 = 1 - w1;
                    c1.r = 0xFF & w1 * c1.r + w2 * c2.r + 0.5;
                    c1.g = 0xFF & w1 * c1.g + w2 * c2.g + 0.5;
                    c1.b = 0xFF & w1 * c1.b + w2 * c2.b + 0.5;
                    c1.a = p * c1.a + (1 - p) * c2.a;
                    this.rgb = c1;
                }
                return this;
            }
        },
        {
            key: "interpolate",
            value: function interpolate1(color2, t) {
                if (color2) this._rgb = interpolate(this._rgb, color2._rgb, t);
                return this;
            }
        },
        {
            key: "clone",
            value: function clone() {
                return new Color(this.rgb);
            }
        },
        {
            key: "alpha",
            value: function alpha(a) {
                this._rgb.a = n2b(a);
                return this;
            }
        },
        {
            key: "clearer",
            value: function clearer(ratio) {
                var rgb = this._rgb;
                rgb.a *= 1 - ratio;
                return this;
            }
        },
        {
            key: "greyscale",
            value: function greyscale() {
                var rgb = this._rgb;
                var val = round(rgb.r * 0.3 + rgb.g * 0.59 + rgb.b * 0.11);
                rgb.r = rgb.g = rgb.b = val;
                return this;
            }
        },
        {
            key: "opaquer",
            value: function opaquer(ratio) {
                var rgb = this._rgb;
                rgb.a *= 1 + ratio;
                return this;
            }
        },
        {
            key: "negate",
            value: function negate() {
                var v = this._rgb;
                v.r = 255 - v.r;
                v.g = 255 - v.g;
                v.b = 255 - v.b;
                return this;
            }
        },
        {
            key: "lighten",
            value: function lighten(ratio) {
                modHSL(this._rgb, 2, ratio);
                return this;
            }
        },
        {
            key: "darken",
            value: function darken(ratio) {
                modHSL(this._rgb, 2, -ratio);
                return this;
            }
        },
        {
            key: "saturate",
            value: function saturate(ratio) {
                modHSL(this._rgb, 1, ratio);
                return this;
            }
        },
        {
            key: "desaturate",
            value: function desaturate(ratio) {
                modHSL(this._rgb, 1, -ratio);
                return this;
            }
        },
        {
            key: "rotate",
            value: function rotate1(deg) {
                rotate(this._rgb, deg);
                return this;
            }
        }
    ]);
    return Color;
}();
function index_esm(input) {
    return new Color(input);
}
function isPatternOrGradient(value) {
    if (value && typeof value === "object") {
        var type = value.toString();
        return type === "[object CanvasPattern]" || type === "[object CanvasGradient]";
    }
    return false;
}
function color(value) {
    return isPatternOrGradient(value) ? value : index_esm(value);
}
function getHoverColor(value) {
    return isPatternOrGradient(value) ? value : index_esm(value).saturate(0.5).darken(0.1).hexString();
}
var overrides = Object.create(null);
var descriptors = Object.create(null);
function getScope$1(node, key) {
    if (!key) return node;
    var keys = key.split(".");
    for(var i = 0, n = keys.length; i < n; ++i){
        var k = keys[i];
        node = node[k] || (node[k] = Object.create(null));
    }
    return node;
}
function set(root, scope, values) {
    if (typeof scope === "string") return merge(getScope$1(root, scope), values);
    return merge(getScope$1(root, ""), scope);
}
var Defaults = /*#__PURE__*/ function() {
    "use strict";
    function Defaults(_descriptors1) {
        (0, _classCallCheckJsDefault.default)(this, Defaults);
        this.animation = undefined;
        this.backgroundColor = "rgba(0,0,0,0.1)";
        this.borderColor = "rgba(0,0,0,0.1)";
        this.color = "#666";
        this.datasets = {};
        this.devicePixelRatio = function(context) {
            return context.chart.platform.getDevicePixelRatio();
        };
        this.elements = {};
        this.events = [
            "mousemove",
            "mouseout",
            "click",
            "touchstart",
            "touchmove"
        ];
        this.font = {
            family: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
            size: 12,
            style: "normal",
            lineHeight: 1.2,
            weight: null
        };
        this.hover = {};
        this.hoverBackgroundColor = function(ctx, options) {
            return getHoverColor(options.backgroundColor);
        };
        this.hoverBorderColor = function(ctx, options) {
            return getHoverColor(options.borderColor);
        };
        this.hoverColor = function(ctx, options) {
            return getHoverColor(options.color);
        };
        this.indexAxis = "x";
        this.interaction = {
            mode: "nearest",
            intersect: true,
            includeInvisible: false
        };
        this.maintainAspectRatio = true;
        this.onHover = null;
        this.onClick = null;
        this.parsing = true;
        this.plugins = {};
        this.responsive = true;
        this.scale = undefined;
        this.scales = {};
        this.showLine = true;
        this.drawActiveElementsOnTop = true;
        this.describe(_descriptors1);
    }
    (0, _createClassJsDefault.default)(Defaults, [
        {
            key: "set",
            value: function set1(scope, values) {
                return set(this, scope, values);
            }
        },
        {
            key: "get",
            value: function get(scope) {
                return getScope$1(this, scope);
            }
        },
        {
            key: "describe",
            value: function describe(scope, values) {
                return set(descriptors, scope, values);
            }
        },
        {
            key: "override",
            value: function override(scope, values) {
                return set(overrides, scope, values);
            }
        },
        {
            key: "route",
            value: function route(scope, name, targetScope, targetName) {
                var scopeObject = getScope$1(this, scope);
                var targetScopeObject = getScope$1(this, targetScope);
                var privateName = "_" + name;
                var _obj;
                Object.defineProperties(scopeObject, (_obj = {}, (0, _definePropertyJsDefault.default)(_obj, privateName, {
                    value: scopeObject[name],
                    writable: true
                }), (0, _definePropertyJsDefault.default)(_obj, name, {
                    enumerable: true,
                    get: function() {
                        var local = this[privateName];
                        var target = targetScopeObject[targetName];
                        if (isObject(local)) return Object.assign({}, target, local);
                        return valueOrDefault(local, target);
                    },
                    set: function(value) {
                        this[privateName] = value;
                    }
                }), _obj));
            }
        }
    ]);
    return Defaults;
}();
var defaults = new Defaults({
    _scriptable: function(name) {
        return !name.startsWith("on");
    },
    _indexable: function(name) {
        return name !== "events";
    },
    hover: {
        _fallback: "interaction"
    },
    interaction: {
        _scriptable: false,
        _indexable: false
    }
});
function toFontString(font) {
    if (!font || isNullOrUndef(font.size) || isNullOrUndef(font.family)) return null;
    return (font.style ? font.style + " " : "") + (font.weight ? font.weight + " " : "") + font.size + "px " + font.family;
}
function _measureText(ctx, data, gc, longest, string) {
    var textWidth = data[string];
    if (!textWidth) {
        textWidth = data[string] = ctx.measureText(string).width;
        gc.push(string);
    }
    if (textWidth > longest) longest = textWidth;
    return longest;
}
function _longestText(ctx, font, arrayOfThings, cache) {
    cache = cache || {};
    var data = cache.data = cache.data || {};
    var gc = cache.garbageCollect = cache.garbageCollect || [];
    if (cache.font !== font) {
        data = cache.data = {};
        gc = cache.garbageCollect = [];
        cache.font = font;
    }
    ctx.save();
    ctx.font = font;
    var longest = 0;
    var ilen = arrayOfThings.length;
    var i, j, jlen, thing, nestedThing;
    for(i = 0; i < ilen; i++){
        thing = arrayOfThings[i];
        if (thing !== undefined && thing !== null && isArray(thing) !== true) longest = _measureText(ctx, data, gc, longest, thing);
        else if (isArray(thing)) for(j = 0, jlen = thing.length; j < jlen; j++){
            nestedThing = thing[j];
            if (nestedThing !== undefined && nestedThing !== null && !isArray(nestedThing)) longest = _measureText(ctx, data, gc, longest, nestedThing);
        }
    }
    ctx.restore();
    var gcLen = gc.length / 2;
    if (gcLen > arrayOfThings.length) {
        for(i = 0; i < gcLen; i++)delete data[gc[i]];
        gc.splice(0, gcLen);
    }
    return longest;
}
function _alignPixel(chart, pixel, width) {
    var devicePixelRatio = chart.currentDevicePixelRatio;
    var halfWidth = width !== 0 ? Math.max(width / 2, 0.5) : 0;
    return Math.round((pixel - halfWidth) * devicePixelRatio) / devicePixelRatio + halfWidth;
}
function clearCanvas(canvas, ctx) {
    ctx = ctx || canvas.getContext("2d");
    ctx.save();
    ctx.resetTransform();
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.restore();
}
function drawPoint(ctx, options, x, y) {
    var type, xOffset, yOffset, size, cornerRadius;
    var style = options.pointStyle;
    var rotation = options.rotation;
    var radius = options.radius;
    var rad = (rotation || 0) * RAD_PER_DEG;
    if (style && typeof style === "object") {
        type = style.toString();
        if (type === "[object HTMLImageElement]" || type === "[object HTMLCanvasElement]") {
            ctx.save();
            ctx.translate(x, y);
            ctx.rotate(rad);
            ctx.drawImage(style, -style.width / 2, -style.height / 2, style.width, style.height);
            ctx.restore();
            return;
        }
    }
    if (isNaN(radius) || radius <= 0) return;
    ctx.beginPath();
    switch(style){
        default:
            ctx.arc(x, y, radius, 0, TAU);
            ctx.closePath();
            break;
        case "triangle":
            ctx.moveTo(x + Math.sin(rad) * radius, y - Math.cos(rad) * radius);
            rad += TWO_THIRDS_PI;
            ctx.lineTo(x + Math.sin(rad) * radius, y - Math.cos(rad) * radius);
            rad += TWO_THIRDS_PI;
            ctx.lineTo(x + Math.sin(rad) * radius, y - Math.cos(rad) * radius);
            ctx.closePath();
            break;
        case "rectRounded":
            cornerRadius = radius * 0.516;
            size = radius - cornerRadius;
            xOffset = Math.cos(rad + QUARTER_PI) * size;
            yOffset = Math.sin(rad + QUARTER_PI) * size;
            ctx.arc(x - xOffset, y - yOffset, cornerRadius, rad - PI, rad - HALF_PI);
            ctx.arc(x + yOffset, y - xOffset, cornerRadius, rad - HALF_PI, rad);
            ctx.arc(x + xOffset, y + yOffset, cornerRadius, rad, rad + HALF_PI);
            ctx.arc(x - yOffset, y + xOffset, cornerRadius, rad + HALF_PI, rad + PI);
            ctx.closePath();
            break;
        case "rect":
            if (!rotation) {
                size = Math.SQRT1_2 * radius;
                ctx.rect(x - size, y - size, 2 * size, 2 * size);
                break;
            }
            rad += QUARTER_PI;
        case "rectRot":
            xOffset = Math.cos(rad) * radius;
            yOffset = Math.sin(rad) * radius;
            ctx.moveTo(x - xOffset, y - yOffset);
            ctx.lineTo(x + yOffset, y - xOffset);
            ctx.lineTo(x + xOffset, y + yOffset);
            ctx.lineTo(x - yOffset, y + xOffset);
            ctx.closePath();
            break;
        case "crossRot":
            rad += QUARTER_PI;
        case "cross":
            xOffset = Math.cos(rad) * radius;
            yOffset = Math.sin(rad) * radius;
            ctx.moveTo(x - xOffset, y - yOffset);
            ctx.lineTo(x + xOffset, y + yOffset);
            ctx.moveTo(x + yOffset, y - xOffset);
            ctx.lineTo(x - yOffset, y + xOffset);
            break;
        case "star":
            xOffset = Math.cos(rad) * radius;
            yOffset = Math.sin(rad) * radius;
            ctx.moveTo(x - xOffset, y - yOffset);
            ctx.lineTo(x + xOffset, y + yOffset);
            ctx.moveTo(x + yOffset, y - xOffset);
            ctx.lineTo(x - yOffset, y + xOffset);
            rad += QUARTER_PI;
            xOffset = Math.cos(rad) * radius;
            yOffset = Math.sin(rad) * radius;
            ctx.moveTo(x - xOffset, y - yOffset);
            ctx.lineTo(x + xOffset, y + yOffset);
            ctx.moveTo(x + yOffset, y - xOffset);
            ctx.lineTo(x - yOffset, y + xOffset);
            break;
        case "line":
            xOffset = Math.cos(rad) * radius;
            yOffset = Math.sin(rad) * radius;
            ctx.moveTo(x - xOffset, y - yOffset);
            ctx.lineTo(x + xOffset, y + yOffset);
            break;
        case "dash":
            ctx.moveTo(x, y);
            ctx.lineTo(x + Math.cos(rad) * radius, y + Math.sin(rad) * radius);
            break;
    }
    ctx.fill();
    if (options.borderWidth > 0) ctx.stroke();
}
function _isPointInArea(point, area, margin) {
    margin = margin || 0.5;
    return !area || point && point.x > area.left - margin && point.x < area.right + margin && point.y > area.top - margin && point.y < area.bottom + margin;
}
function clipArea(ctx, area) {
    ctx.save();
    ctx.beginPath();
    ctx.rect(area.left, area.top, area.right - area.left, area.bottom - area.top);
    ctx.clip();
}
function unclipArea(ctx) {
    ctx.restore();
}
function _steppedLineTo(ctx, previous, target, flip, mode) {
    if (!previous) return ctx.lineTo(target.x, target.y);
    if (mode === "middle") {
        var midpoint = (previous.x + target.x) / 2.0;
        ctx.lineTo(midpoint, previous.y);
        ctx.lineTo(midpoint, target.y);
    } else if (mode === "after" !== !!flip) ctx.lineTo(previous.x, target.y);
    else ctx.lineTo(target.x, previous.y);
    ctx.lineTo(target.x, target.y);
}
function _bezierCurveTo(ctx, previous, target, flip) {
    if (!previous) return ctx.lineTo(target.x, target.y);
    ctx.bezierCurveTo(flip ? previous.cp1x : previous.cp2x, flip ? previous.cp1y : previous.cp2y, flip ? target.cp2x : target.cp1x, flip ? target.cp2y : target.cp1y, target.x, target.y);
}
function renderText(ctx, text, x, y, font) {
    var opts = arguments.length > 5 && arguments[5] !== void 0 ? arguments[5] : {};
    var lines = isArray(text) ? text : [
        text
    ];
    var stroke = opts.strokeWidth > 0 && opts.strokeColor !== "";
    var i, line;
    ctx.save();
    ctx.font = font.string;
    setRenderOpts(ctx, opts);
    for(i = 0; i < lines.length; ++i){
        line = lines[i];
        if (stroke) {
            if (opts.strokeColor) ctx.strokeStyle = opts.strokeColor;
            if (!isNullOrUndef(opts.strokeWidth)) ctx.lineWidth = opts.strokeWidth;
            ctx.strokeText(line, x, y, opts.maxWidth);
        }
        ctx.fillText(line, x, y, opts.maxWidth);
        decorateText(ctx, x, y, line, opts);
        y += font.lineHeight;
    }
    ctx.restore();
}
function setRenderOpts(ctx, opts) {
    if (opts.translation) ctx.translate(opts.translation[0], opts.translation[1]);
    if (!isNullOrUndef(opts.rotation)) ctx.rotate(opts.rotation);
    if (opts.color) ctx.fillStyle = opts.color;
    if (opts.textAlign) ctx.textAlign = opts.textAlign;
    if (opts.textBaseline) ctx.textBaseline = opts.textBaseline;
}
function decorateText(ctx, x, y, line, opts) {
    if (opts.strikethrough || opts.underline) {
        var metrics = ctx.measureText(line);
        var left = x - metrics.actualBoundingBoxLeft;
        var right = x + metrics.actualBoundingBoxRight;
        var top = y - metrics.actualBoundingBoxAscent;
        var bottom = y + metrics.actualBoundingBoxDescent;
        var yDecoration = opts.strikethrough ? (top + bottom) / 2 : bottom;
        ctx.strokeStyle = ctx.fillStyle;
        ctx.beginPath();
        ctx.lineWidth = opts.decorationWidth || 2;
        ctx.moveTo(left, yDecoration);
        ctx.lineTo(right, yDecoration);
        ctx.stroke();
    }
}
function addRoundedRectPath(ctx, rect) {
    var x = rect.x, y = rect.y, w = rect.w, h = rect.h, radius = rect.radius;
    ctx.arc(x + radius.topLeft, y + radius.topLeft, radius.topLeft, -HALF_PI, PI, true);
    ctx.lineTo(x, y + h - radius.bottomLeft);
    ctx.arc(x + radius.bottomLeft, y + h - radius.bottomLeft, radius.bottomLeft, PI, HALF_PI, true);
    ctx.lineTo(x + w - radius.bottomRight, y + h);
    ctx.arc(x + w - radius.bottomRight, y + h - radius.bottomRight, radius.bottomRight, HALF_PI, 0, true);
    ctx.lineTo(x + w, y + radius.topRight);
    ctx.arc(x + w - radius.topRight, y + radius.topRight, radius.topRight, 0, -HALF_PI, true);
    ctx.lineTo(x + radius.topLeft, y);
}
var LINE_HEIGHT = new RegExp(/^(normal|(\d+(?:\.\d+)?)(px|em|%)?)$/);
var FONT_STYLE = new RegExp(/^(normal|italic|initial|inherit|unset|(oblique( -?[0-9]?[0-9]deg)?))$/);
function toLineHeight(value, size) {
    var matches = ("" + value).match(LINE_HEIGHT);
    if (!matches || matches[1] === "normal") return size * 1.2;
    value = +matches[2];
    switch(matches[3]){
        case "px":
            return value;
        case "%":
            value /= 100;
            break;
    }
    return size * value;
}
var numberOrZero = function(v) {
    return +v || 0;
};
function _readValueToProps(value, props) {
    var ret = {};
    var objProps = isObject(props);
    var keys = objProps ? Object.keys(props) : props;
    var read = isObject(value) ? objProps ? function(prop) {
        return valueOrDefault(value[prop], value[props[prop]]);
    } : function(prop) {
        return value[prop];
    } : function() {
        return value;
    };
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = keys[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var prop1 = _step.value;
            ret[prop1] = numberOrZero(read(prop1));
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
    return ret;
}
function toTRBL(value) {
    return _readValueToProps(value, {
        top: "y",
        right: "x",
        bottom: "y",
        left: "x"
    });
}
function toTRBLCorners(value) {
    return _readValueToProps(value, [
        "topLeft",
        "topRight",
        "bottomLeft",
        "bottomRight"
    ]);
}
function toPadding(value) {
    var obj = toTRBL(value);
    obj.width = obj.left + obj.right;
    obj.height = obj.top + obj.bottom;
    return obj;
}
function toFont(options, fallback) {
    options = options || {};
    fallback = fallback || defaults.font;
    var size = valueOrDefault(options.size, fallback.size);
    if (typeof size === "string") size = parseInt(size, 10);
    var style = valueOrDefault(options.style, fallback.style);
    if (style && !("" + style).match(FONT_STYLE)) {
        console.warn('Invalid font style specified: "' + style + '"');
        style = "";
    }
    var font = {
        family: valueOrDefault(options.family, fallback.family),
        lineHeight: toLineHeight(valueOrDefault(options.lineHeight, fallback.lineHeight), size),
        size: size,
        style: style,
        weight: valueOrDefault(options.weight, fallback.weight),
        string: ""
    };
    font.string = toFontString(font);
    return font;
}
function resolve(inputs, context, index, info) {
    var cacheable = true;
    var i, ilen, value;
    for(i = 0, ilen = inputs.length; i < ilen; ++i){
        value = inputs[i];
        if (value === undefined) continue;
        if (context !== undefined && typeof value === "function") {
            value = value(context);
            cacheable = false;
        }
        if (index !== undefined && isArray(value)) {
            value = value[index % value.length];
            cacheable = false;
        }
        if (value !== undefined) {
            if (info && !cacheable) info.cacheable = false;
            return value;
        }
    }
}
function _addGrace(minmax, grace, beginAtZero) {
    var min = minmax.min, max = minmax.max;
    var change = toDimension(grace, (max - min) / 2);
    var keepZero = function(value, add) {
        return beginAtZero && value === 0 ? 0 : value + add;
    };
    return {
        min: keepZero(min, -Math.abs(change)),
        max: keepZero(max, change)
    };
}
function createContext(parentContext, context) {
    return Object.assign(Object.create(parentContext), context);
}
function _lookup(table, value, cmp) {
    cmp = cmp || function(index) {
        return table[index] < value;
    };
    var hi = table.length - 1;
    var lo = 0;
    var mid;
    while(hi - lo > 1){
        mid = lo + hi >> 1;
        if (cmp(mid)) lo = mid;
        else hi = mid;
    }
    return {
        lo: lo,
        hi: hi
    };
}
var _lookupByKey = function(table, key, value) {
    return _lookup(table, value, function(index) {
        return table[index][key] < value;
    });
};
var _rlookupByKey = function(table, key, value) {
    return _lookup(table, value, function(index) {
        return table[index][key] >= value;
    });
};
function _filterBetween(values, min, max) {
    var start = 0;
    var end = values.length;
    while(start < end && values[start] < min)start++;
    while(end > start && values[end - 1] > max)end--;
    return start > 0 || end < values.length ? values.slice(start, end) : values;
}
var arrayEvents = [
    "push",
    "pop",
    "shift",
    "splice",
    "unshift"
];
function listenArrayEvents(array, listener) {
    if (array._chartjs) {
        array._chartjs.listeners.push(listener);
        return;
    }
    Object.defineProperty(array, "_chartjs", {
        configurable: true,
        enumerable: false,
        value: {
            listeners: [
                listener
            ]
        }
    });
    arrayEvents.forEach(function(key) {
        var method = "_onData" + _capitalize(key);
        var base = array[key];
        Object.defineProperty(array, key, {
            configurable: true,
            enumerable: false,
            value: function() {
                for(var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++){
                    args[_key] = arguments[_key];
                }
                var res = base.apply(this, args);
                array._chartjs.listeners.forEach(function(object) {
                    var _object;
                    if (typeof object[method] === "function") (_object = object)[method].apply(_object, (0, _toConsumableArrayJsDefault.default)(args));
                });
                return res;
            }
        });
    });
}
function unlistenArrayEvents(array, listener) {
    var stub = array._chartjs;
    if (!stub) return;
    var listeners = stub.listeners;
    var index = listeners.indexOf(listener);
    if (index !== -1) listeners.splice(index, 1);
    if (listeners.length > 0) return;
    arrayEvents.forEach(function(key) {
        delete array[key];
    });
    delete array._chartjs;
}
function _arrayUnique(items) {
    var set2 = new Set();
    var i, ilen;
    for(i = 0, ilen = items.length; i < ilen; ++i)set2.add(items[i]);
    if (set2.size === ilen) return items;
    return Array.from(set2);
}
function _createResolver(scopes) {
    var prefixes = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : [
        ""
    ], rootScopes = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : scopes, fallback = arguments.length > 3 ? arguments[3] : void 0, getTarget = arguments.length > 4 && arguments[4] !== void 0 ? arguments[4] : function() {
        return scopes[0];
    };
    if (!defined(fallback)) fallback = _resolve("_fallback", scopes);
    var _obj;
    var cache = (_obj = {}, (0, _definePropertyJsDefault.default)(_obj, Symbol.toStringTag, "Object"), (0, _definePropertyJsDefault.default)(_obj, "_cacheable", true), (0, _definePropertyJsDefault.default)(_obj, "_scopes", scopes), (0, _definePropertyJsDefault.default)(_obj, "_rootScopes", rootScopes), (0, _definePropertyJsDefault.default)(_obj, "_fallback", fallback), (0, _definePropertyJsDefault.default)(_obj, "_getTarget", getTarget), (0, _definePropertyJsDefault.default)(_obj, "override", function(scope) {
        return _createResolver([
            scope
        ].concat((0, _toConsumableArrayJsDefault.default)(scopes)), prefixes, rootScopes, fallback);
    }), _obj);
    return new Proxy(cache, {
        deleteProperty: function(target, prop) {
            delete target[prop];
            delete target._keys;
            delete scopes[0][prop];
            return true;
        },
        get: function(target, prop) {
            return _cached(target, prop, function() {
                return _resolveWithPrefixes(prop, prefixes, scopes, target);
            });
        },
        getOwnPropertyDescriptor: function(target, prop) {
            return Reflect.getOwnPropertyDescriptor(target._scopes[0], prop);
        },
        getPrototypeOf: function() {
            return Reflect.getPrototypeOf(scopes[0]);
        },
        has: function(target, prop) {
            return getKeysFromAllScopes(target).includes(prop);
        },
        ownKeys: function(target) {
            return getKeysFromAllScopes(target);
        },
        set: function(target, prop, value) {
            var storage = target._storage || (target._storage = getTarget());
            target[prop] = storage[prop] = value;
            delete target._keys;
            return true;
        }
    });
}
function _attachContext(proxy, context, subProxy, descriptorDefaults) {
    var cache = {
        _cacheable: false,
        _proxy: proxy,
        _context: context,
        _subProxy: subProxy,
        _stack: new Set(),
        _descriptors: _descriptors(proxy, descriptorDefaults),
        setContext: function(ctx) {
            return _attachContext(proxy, ctx, subProxy, descriptorDefaults);
        },
        override: function(scope) {
            return _attachContext(proxy.override(scope), context, subProxy, descriptorDefaults);
        }
    };
    return new Proxy(cache, {
        deleteProperty: function(target, prop) {
            delete target[prop];
            delete proxy[prop];
            return true;
        },
        get: function(target, prop, receiver) {
            return _cached(target, prop, function() {
                return _resolveWithContext(target, prop, receiver);
            });
        },
        getOwnPropertyDescriptor: function(target, prop) {
            return target._descriptors.allKeys ? Reflect.has(proxy, prop) ? {
                enumerable: true,
                configurable: true
            } : undefined : Reflect.getOwnPropertyDescriptor(proxy, prop);
        },
        getPrototypeOf: function() {
            return Reflect.getPrototypeOf(proxy);
        },
        has: function(target, prop) {
            return Reflect.has(proxy, prop);
        },
        ownKeys: function() {
            return Reflect.ownKeys(proxy);
        },
        set: function(target, prop, value) {
            proxy[prop] = value;
            delete target[prop];
            return true;
        }
    });
}
function _descriptors(proxy) {
    var defaults1 = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {
        scriptable: true,
        indexable: true
    };
    var __scriptable = proxy._scriptable, _scriptable = __scriptable === void 0 ? defaults1.scriptable : __scriptable, __indexable = proxy._indexable, _indexable = __indexable === void 0 ? defaults1.indexable : __indexable, __allKeys = proxy._allKeys, _allKeys = __allKeys === void 0 ? defaults1.allKeys : __allKeys;
    return {
        allKeys: _allKeys,
        scriptable: _scriptable,
        indexable: _indexable,
        isScriptable: isFunction(_scriptable) ? _scriptable : function() {
            return _scriptable;
        },
        isIndexable: isFunction(_indexable) ? _indexable : function() {
            return _indexable;
        }
    };
}
var readKey = function(prefix, name) {
    return prefix ? prefix + _capitalize(name) : name;
};
var needsSubResolver = function(prop, value) {
    return isObject(value) && prop !== "adapters" && (Object.getPrototypeOf(value) === null || value.constructor === Object);
};
function _cached(target, prop, resolve1) {
    if (Object.prototype.hasOwnProperty.call(target, prop)) return target[prop];
    var value = resolve1();
    target[prop] = value;
    return value;
}
function _resolveWithContext(target, prop, receiver) {
    var _proxy = target._proxy, _context = target._context, _subProxy = target._subProxy, descriptors1 = target._descriptors;
    var value = _proxy[prop];
    if (isFunction(value) && descriptors1.isScriptable(prop)) value = _resolveScriptable(prop, value, target, receiver);
    if (isArray(value) && value.length) value = _resolveArray(prop, value, target, descriptors1.isIndexable);
    if (needsSubResolver(prop, value)) value = _attachContext(value, _context, _subProxy && _subProxy[prop], descriptors1);
    return value;
}
function _resolveScriptable(prop, value, target, receiver) {
    var _proxy = target._proxy, _context = target._context, _subProxy = target._subProxy, _stack = target._stack;
    if (_stack.has(prop)) throw new Error("Recursion detected: " + Array.from(_stack).join("->") + "->" + prop);
    _stack.add(prop);
    value = value(_context, _subProxy || receiver);
    _stack.delete(prop);
    if (needsSubResolver(prop, value)) value = createSubResolver(_proxy._scopes, _proxy, prop, value);
    return value;
}
function _resolveArray(prop, value, target, isIndexable) {
    var _proxy = target._proxy, _context = target._context, _subProxy = target._subProxy, descriptors2 = target._descriptors;
    if (defined(_context.index) && isIndexable(prop)) value = value[_context.index % value.length];
    else if (isObject(value[0])) {
        var arr = value;
        var scopes = _proxy._scopes.filter(function(s) {
            return s !== arr;
        });
        value = [];
        var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
        try {
            for(var _iterator = arr[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
                var item = _step.value;
                var resolver = createSubResolver(scopes, _proxy, prop, item);
                value.push(_attachContext(resolver, _context, _subProxy && _subProxy[prop], descriptors2));
            }
        } catch (err) {
            _didIteratorError = true;
            _iteratorError = err;
        } finally{
            try {
                if (!_iteratorNormalCompletion && _iterator.return != null) {
                    _iterator.return();
                }
            } finally{
                if (_didIteratorError) {
                    throw _iteratorError;
                }
            }
        }
    }
    return value;
}
function resolveFallback(fallback, prop, value) {
    return isFunction(fallback) ? fallback(prop, value) : fallback;
}
var getScope = function(key, parent) {
    return key === true ? parent : typeof key === "string" ? resolveObjectKey(parent, key) : undefined;
};
function addScopes(set3, parentScopes, key, parentFallback, value) {
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = parentScopes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var parent = _step.value;
            var scope = getScope(key, parent);
            if (scope) {
                set3.add(scope);
                var fallback = resolveFallback(scope._fallback, key, value);
                if (defined(fallback) && fallback !== key && fallback !== parentFallback) return fallback;
            } else if (scope === false && defined(parentFallback) && key !== parentFallback) return null;
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
    return false;
}
function createSubResolver(parentScopes, resolver, prop, value) {
    var rootScopes = resolver._rootScopes;
    var fallback = resolveFallback(resolver._fallback, prop, value);
    var allScopes = (0, _toConsumableArrayJsDefault.default)(parentScopes).concat((0, _toConsumableArrayJsDefault.default)(rootScopes));
    var set4 = new Set();
    set4.add(value);
    var key = addScopesFromKey(set4, allScopes, prop, fallback || prop, value);
    if (key === null) return false;
    if (defined(fallback) && fallback !== prop) {
        key = addScopesFromKey(set4, allScopes, fallback, key, value);
        if (key === null) return false;
    }
    return _createResolver(Array.from(set4), [
        ""
    ], rootScopes, fallback, function() {
        return subGetTarget(resolver, prop, value);
    });
}
function addScopesFromKey(set5, allScopes, key, fallback, item) {
    while(key)key = addScopes(set5, allScopes, key, fallback, item);
    return key;
}
function subGetTarget(resolver, prop, value) {
    var parent = resolver._getTarget();
    if (!(prop in parent)) parent[prop] = {};
    var target = parent[prop];
    if (isArray(target) && isObject(value)) return value;
    return target;
}
function _resolveWithPrefixes(prop, prefixes, scopes, proxy) {
    var value;
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = prefixes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var prefix = _step.value;
            value = _resolve(readKey(prefix, prop), scopes);
            if (defined(value)) return needsSubResolver(prop, value) ? createSubResolver(scopes, proxy, prop, value) : value;
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
}
function _resolve(key, scopes) {
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = scopes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var scope = _step.value;
            if (!scope) continue;
            var value = scope[key];
            if (defined(value)) return value;
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
}
function getKeysFromAllScopes(target) {
    var keys = target._keys;
    if (!keys) keys = target._keys = resolveKeysFromAllScopes(target._scopes);
    return keys;
}
function resolveKeysFromAllScopes(scopes) {
    var set6 = new Set();
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined, _iteratorNormalCompletion1 = true, _didIteratorError1 = false, _iteratorError1 = undefined;
    try {
        for(var _iterator = scopes[Symbol.iterator](), _step; !(_iteratorNormalCompletion1 = (_step = _iterator.next()).done); _iteratorNormalCompletion1 = true){
            var scope = _step.value;
            try {
                for(var _iterator1 = Object.keys(scope).filter(function(k) {
                    return !k.startsWith("_");
                })[Symbol.iterator](), _step1; !(_iteratorNormalCompletion = (_step1 = _iterator1.next()).done); _iteratorNormalCompletion = true){
                    var key = _step1.value;
                    set6.add(key);
                }
            } catch (err) {
                _didIteratorError = true;
                _iteratorError = err;
            } finally{
                try {
                    if (!_iteratorNormalCompletion && _iterator1.return != null) {
                        _iterator1.return();
                    }
                } finally{
                    if (_didIteratorError) {
                        throw _iteratorError;
                    }
                }
            }
        }
    } catch (err) {
        _didIteratorError1 = true;
        _iteratorError1 = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion1 && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError1) {
                throw _iteratorError1;
            }
        }
    }
    return Array.from(set6);
}
function _parseObjectDataRadialScale(meta, data, start, count) {
    var iScale = meta.iScale;
    var __parsing = this._parsing, _key = __parsing.key, key = _key === void 0 ? "r" : _key;
    var parsed = new Array(count);
    var i, ilen, index, item;
    for(i = 0, ilen = count; i < ilen; ++i){
        index = i + start;
        item = data[index];
        parsed[i] = {
            r: iScale.parse(resolveObjectKey(item, key), index)
        };
    }
    return parsed;
}
var EPSILON = Number.EPSILON || 1e-14;
var getPoint = function(points, i) {
    return i < points.length && !points[i].skip && points[i];
};
var getValueAxis = function(indexAxis) {
    return indexAxis === "x" ? "y" : "x";
};
function splineCurve(firstPoint, middlePoint, afterPoint, t) {
    var previous = firstPoint.skip ? middlePoint : firstPoint;
    var current = middlePoint;
    var next = afterPoint.skip ? middlePoint : afterPoint;
    var d01 = distanceBetweenPoints(current, previous);
    var d12 = distanceBetweenPoints(next, current);
    var s01 = d01 / (d01 + d12);
    var s12 = d12 / (d01 + d12);
    s01 = isNaN(s01) ? 0 : s01;
    s12 = isNaN(s12) ? 0 : s12;
    var fa = t * s01;
    var fb = t * s12;
    return {
        previous: {
            x: current.x - fa * (next.x - previous.x),
            y: current.y - fa * (next.y - previous.y)
        },
        next: {
            x: current.x + fb * (next.x - previous.x),
            y: current.y + fb * (next.y - previous.y)
        }
    };
}
function monotoneAdjust(points, deltaK, mK) {
    var pointsLen = points.length;
    var alphaK, betaK, tauK, squaredMagnitude, pointCurrent;
    var pointAfter = getPoint(points, 0);
    for(var i = 0; i < pointsLen - 1; ++i){
        pointCurrent = pointAfter;
        pointAfter = getPoint(points, i + 1);
        if (!pointCurrent || !pointAfter) continue;
        if (almostEquals(deltaK[i], 0, EPSILON)) {
            mK[i] = mK[i + 1] = 0;
            continue;
        }
        alphaK = mK[i] / deltaK[i];
        betaK = mK[i + 1] / deltaK[i];
        squaredMagnitude = Math.pow(alphaK, 2) + Math.pow(betaK, 2);
        if (squaredMagnitude <= 9) continue;
        tauK = 3 / Math.sqrt(squaredMagnitude);
        mK[i] = alphaK * tauK * deltaK[i];
        mK[i + 1] = betaK * tauK * deltaK[i];
    }
}
function monotoneCompute(points, mK) {
    var indexAxis = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : "x";
    var valueAxis = getValueAxis(indexAxis);
    var pointsLen = points.length;
    var delta, pointBefore, pointCurrent;
    var pointAfter = getPoint(points, 0);
    for(var i = 0; i < pointsLen; ++i){
        pointBefore = pointCurrent;
        pointCurrent = pointAfter;
        pointAfter = getPoint(points, i + 1);
        if (!pointCurrent) continue;
        var iPixel = pointCurrent[indexAxis];
        var vPixel = pointCurrent[valueAxis];
        if (pointBefore) {
            delta = (iPixel - pointBefore[indexAxis]) / 3;
            pointCurrent["cp1".concat(indexAxis)] = iPixel - delta;
            pointCurrent["cp1".concat(valueAxis)] = vPixel - delta * mK[i];
        }
        if (pointAfter) {
            delta = (pointAfter[indexAxis] - iPixel) / 3;
            pointCurrent["cp2".concat(indexAxis)] = iPixel + delta;
            pointCurrent["cp2".concat(valueAxis)] = vPixel + delta * mK[i];
        }
    }
}
function splineCurveMonotone(points) {
    var indexAxis = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : "x";
    var valueAxis = getValueAxis(indexAxis);
    var pointsLen = points.length;
    var deltaK = Array(pointsLen).fill(0);
    var mK = Array(pointsLen);
    var i, pointBefore, pointCurrent;
    var pointAfter = getPoint(points, 0);
    for(i = 0; i < pointsLen; ++i){
        pointBefore = pointCurrent;
        pointCurrent = pointAfter;
        pointAfter = getPoint(points, i + 1);
        if (!pointCurrent) continue;
        if (pointAfter) {
            var slopeDelta = pointAfter[indexAxis] - pointCurrent[indexAxis];
            deltaK[i] = slopeDelta !== 0 ? (pointAfter[valueAxis] - pointCurrent[valueAxis]) / slopeDelta : 0;
        }
        mK[i] = !pointBefore ? deltaK[i] : !pointAfter ? deltaK[i - 1] : sign(deltaK[i - 1]) !== sign(deltaK[i]) ? 0 : (deltaK[i - 1] + deltaK[i]) / 2;
    }
    monotoneAdjust(points, deltaK, mK);
    monotoneCompute(points, mK, indexAxis);
}
function capControlPoint(pt, min, max) {
    return Math.max(Math.min(pt, max), min);
}
function capBezierPoints(points, area) {
    var i, ilen, point, inArea, inAreaPrev;
    var inAreaNext = _isPointInArea(points[0], area);
    for(i = 0, ilen = points.length; i < ilen; ++i){
        inAreaPrev = inArea;
        inArea = inAreaNext;
        inAreaNext = i < ilen - 1 && _isPointInArea(points[i + 1], area);
        if (!inArea) continue;
        point = points[i];
        if (inAreaPrev) {
            point.cp1x = capControlPoint(point.cp1x, area.left, area.right);
            point.cp1y = capControlPoint(point.cp1y, area.top, area.bottom);
        }
        if (inAreaNext) {
            point.cp2x = capControlPoint(point.cp2x, area.left, area.right);
            point.cp2y = capControlPoint(point.cp2y, area.top, area.bottom);
        }
    }
}
function _updateBezierControlPoints(points, options, area, loop, indexAxis) {
    var i, ilen, point, controlPoints;
    if (options.spanGaps) points = points.filter(function(pt) {
        return !pt.skip;
    });
    if (options.cubicInterpolationMode === "monotone") splineCurveMonotone(points, indexAxis);
    else {
        var prev = loop ? points[points.length - 1] : points[0];
        for(i = 0, ilen = points.length; i < ilen; ++i){
            point = points[i];
            controlPoints = splineCurve(prev, point, points[Math.min(i + 1, ilen - (loop ? 0 : 1)) % ilen], options.tension);
            point.cp1x = controlPoints.previous.x;
            point.cp1y = controlPoints.previous.y;
            point.cp2x = controlPoints.next.x;
            point.cp2y = controlPoints.next.y;
            prev = point;
        }
    }
    if (options.capBezierPoints) capBezierPoints(points, area);
}
function _isDomSupported() {
    return typeof window !== "undefined" && typeof document !== "undefined";
}
function _getParentNode(domNode) {
    var parent = domNode.parentNode;
    if (parent && parent.toString() === "[object ShadowRoot]") parent = parent.host;
    return parent;
}
function parseMaxStyle(styleValue, node, parentProperty) {
    var valueInPixels;
    if (typeof styleValue === "string") {
        valueInPixels = parseInt(styleValue, 10);
        if (styleValue.indexOf("%") !== -1) valueInPixels = valueInPixels / 100 * node.parentNode[parentProperty];
    } else valueInPixels = styleValue;
    return valueInPixels;
}
var getComputedStyle = function(element) {
    return window.getComputedStyle(element, null);
};
function getStyle(el, property) {
    return getComputedStyle(el).getPropertyValue(property);
}
var positions = [
    "top",
    "right",
    "bottom",
    "left"
];
function getPositionedStyle(styles, style, suffix) {
    var result = {};
    suffix = suffix ? "-" + suffix : "";
    for(var i = 0; i < 4; i++){
        var pos = positions[i];
        result[pos] = parseFloat(styles[style + "-" + pos + suffix]) || 0;
    }
    result.width = result.left + result.right;
    result.height = result.top + result.bottom;
    return result;
}
var useOffsetPos = function(x, y, target) {
    return (x > 0 || y > 0) && (!target || !target.shadowRoot);
};
function getCanvasPosition(e, canvas) {
    var touches = e.touches;
    var source = touches && touches.length ? touches[0] : e;
    var offsetX = source.offsetX, offsetY = source.offsetY;
    var box = false;
    var x, y;
    if (useOffsetPos(offsetX, offsetY, e.target)) {
        x = offsetX;
        y = offsetY;
    } else {
        var rect = canvas.getBoundingClientRect();
        x = source.clientX - rect.left;
        y = source.clientY - rect.top;
        box = true;
    }
    return {
        x: x,
        y: y,
        box: box
    };
}
function getRelativePosition(evt, chart) {
    if ("native" in evt) return evt;
    var canvas = chart.canvas, currentDevicePixelRatio = chart.currentDevicePixelRatio;
    var style = getComputedStyle(canvas);
    var borderBox = style.boxSizing === "border-box";
    var paddings = getPositionedStyle(style, "padding");
    var borders = getPositionedStyle(style, "border", "width");
    var ref = getCanvasPosition(evt, canvas), x = ref.x, y = ref.y, box = ref.box;
    var xOffset = paddings.left + (box && borders.left);
    var yOffset = paddings.top + (box && borders.top);
    var width = chart.width, height = chart.height;
    if (borderBox) {
        width -= paddings.width + borders.width;
        height -= paddings.height + borders.height;
    }
    return {
        x: Math.round((x - xOffset) / width * canvas.width / currentDevicePixelRatio),
        y: Math.round((y - yOffset) / height * canvas.height / currentDevicePixelRatio)
    };
}
function getContainerSize(canvas, width, height) {
    var maxWidth, maxHeight;
    if (width === undefined || height === undefined) {
        var container = _getParentNode(canvas);
        if (!container) {
            width = canvas.clientWidth;
            height = canvas.clientHeight;
        } else {
            var rect = container.getBoundingClientRect();
            var containerStyle = getComputedStyle(container);
            var containerBorder = getPositionedStyle(containerStyle, "border", "width");
            var containerPadding = getPositionedStyle(containerStyle, "padding");
            width = rect.width - containerPadding.width - containerBorder.width;
            height = rect.height - containerPadding.height - containerBorder.height;
            maxWidth = parseMaxStyle(containerStyle.maxWidth, container, "clientWidth");
            maxHeight = parseMaxStyle(containerStyle.maxHeight, container, "clientHeight");
        }
    }
    return {
        width: width,
        height: height,
        maxWidth: maxWidth || INFINITY,
        maxHeight: maxHeight || INFINITY
    };
}
var round1 = function(v) {
    return Math.round(v * 10) / 10;
};
function getMaximumSize(canvas, bbWidth, bbHeight, aspectRatio) {
    var style = getComputedStyle(canvas);
    var margins = getPositionedStyle(style, "margin");
    var maxWidth = parseMaxStyle(style.maxWidth, canvas, "clientWidth") || INFINITY;
    var maxHeight = parseMaxStyle(style.maxHeight, canvas, "clientHeight") || INFINITY;
    var containerSize = getContainerSize(canvas, bbWidth, bbHeight);
    var width = containerSize.width, height = containerSize.height;
    if (style.boxSizing === "content-box") {
        var borders = getPositionedStyle(style, "border", "width");
        var paddings = getPositionedStyle(style, "padding");
        width -= paddings.width + borders.width;
        height -= paddings.height + borders.height;
    }
    width = Math.max(0, width - margins.width);
    height = Math.max(0, aspectRatio ? Math.floor(width / aspectRatio) : height - margins.height);
    width = round1(Math.min(width, maxWidth, containerSize.maxWidth));
    height = round1(Math.min(height, maxHeight, containerSize.maxHeight));
    if (width && !height) height = round1(width / 2);
    return {
        width: width,
        height: height
    };
}
function retinaScale(chart, forceRatio, forceStyle) {
    var pixelRatio = forceRatio || 1;
    var deviceHeight = Math.floor(chart.height * pixelRatio);
    var deviceWidth = Math.floor(chart.width * pixelRatio);
    chart.height = deviceHeight / pixelRatio;
    chart.width = deviceWidth / pixelRatio;
    var canvas = chart.canvas;
    if (canvas.style && (forceStyle || !canvas.style.height && !canvas.style.width)) {
        canvas.style.height = "".concat(chart.height, "px");
        canvas.style.width = "".concat(chart.width, "px");
    }
    if (chart.currentDevicePixelRatio !== pixelRatio || canvas.height !== deviceHeight || canvas.width !== deviceWidth) {
        chart.currentDevicePixelRatio = pixelRatio;
        canvas.height = deviceHeight;
        canvas.width = deviceWidth;
        chart.ctx.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
        return true;
    }
    return false;
}
var supportsEventListenerOptions = function() {
    var passiveSupported = false;
    try {
        var options = {
            get passive () {
                passiveSupported = true;
                return false;
            }
        };
        window.addEventListener("test", null, options);
        window.removeEventListener("test", null, options);
    } catch (e) {}
    return passiveSupported;
}();
function readUsedSize(element, property) {
    var value = getStyle(element, property);
    var matches = value && value.match(/^(\d+)(\.\d+)?px$/);
    return matches ? +matches[1] : undefined;
}
function _pointInLine(p1, p2, t, mode) {
    return {
        x: p1.x + t * (p2.x - p1.x),
        y: p1.y + t * (p2.y - p1.y)
    };
}
function _steppedInterpolation(p1, p2, t, mode) {
    return {
        x: p1.x + t * (p2.x - p1.x),
        y: mode === "middle" ? t < 0.5 ? p1.y : p2.y : mode === "after" ? t < 1 ? p1.y : p2.y : t > 0 ? p2.y : p1.y
    };
}
function _bezierInterpolation(p1, p2, t, mode) {
    var cp1 = {
        x: p1.cp2x,
        y: p1.cp2y
    };
    var cp2 = {
        x: p2.cp1x,
        y: p2.cp1y
    };
    var a = _pointInLine(p1, cp1, t);
    var b = _pointInLine(cp1, cp2, t);
    var c = _pointInLine(cp2, p2, t);
    var d = _pointInLine(a, b, t);
    var e = _pointInLine(b, c, t);
    return _pointInLine(d, e, t);
}
var intlCache = new Map();
function getNumberFormat(locale, options) {
    options = options || {};
    var cacheKey = locale + JSON.stringify(options);
    var formatter = intlCache.get(cacheKey);
    if (!formatter) {
        formatter = new Intl.NumberFormat(locale, options);
        intlCache.set(cacheKey, formatter);
    }
    return formatter;
}
function formatNumber(num, locale, options) {
    return getNumberFormat(locale, options).format(num);
}
var getRightToLeftAdapter = function getRightToLeftAdapter(rectX, width) {
    return {
        x: function(x) {
            return rectX + rectX + width - x;
        },
        setWidth: function(w) {
            width = w;
        },
        textAlign: function(align) {
            if (align === "center") return align;
            return align === "right" ? "left" : "right";
        },
        xPlus: function(x, value) {
            return x - value;
        },
        leftForLtr: function(x, itemWidth) {
            return x - itemWidth;
        }
    };
};
var getLeftToRightAdapter = function getLeftToRightAdapter() {
    return {
        x: function(x) {
            return x;
        },
        setWidth: function(w) {},
        textAlign: function(align) {
            return align;
        },
        xPlus: function(x, value) {
            return x + value;
        },
        leftForLtr: function(x, _itemWidth) {
            return x;
        }
    };
};
function getRtlAdapter(rtl, rectX, width) {
    return rtl ? getRightToLeftAdapter(rectX, width) : getLeftToRightAdapter();
}
function overrideTextDirection(ctx, direction) {
    var style, original;
    if (direction === "ltr" || direction === "rtl") {
        style = ctx.canvas.style;
        original = [
            style.getPropertyValue("direction"),
            style.getPropertyPriority("direction"), 
        ];
        style.setProperty("direction", direction, "important");
        ctx.prevTextDirection = original;
    }
}
function restoreTextDirection(ctx, original) {
    if (original !== undefined) {
        delete ctx.prevTextDirection;
        ctx.canvas.style.setProperty("direction", original[0], original[1]);
    }
}
function propertyFn(property) {
    if (property === "angle") return {
        between: _angleBetween,
        compare: _angleDiff,
        normalize: _normalizeAngle
    };
    return {
        between: _isBetween,
        compare: function(a, b) {
            return a - b;
        },
        normalize: function(x) {
            return x;
        }
    };
}
function normalizeSegment(param) {
    var start = param.start, end = param.end, count = param.count, loop = param.loop, style = param.style;
    return {
        start: start % count,
        end: end % count,
        loop: loop && (end - start + 1) % count === 0,
        style: style
    };
}
function getSegment(segment, points, bounds) {
    var property = bounds.property, startBound = bounds.start, endBound = bounds.end;
    var ref = propertyFn(property), between = ref.between, normalize = ref.normalize;
    var count = points.length;
    var start = segment.start, end = segment.end, loop = segment.loop;
    var i, ilen;
    if (loop) {
        start += count;
        end += count;
        for(i = 0, ilen = count; i < ilen; ++i){
            if (!between(normalize(points[start % count][property]), startBound, endBound)) break;
            start--;
            end--;
        }
        start %= count;
        end %= count;
    }
    if (end < start) end += count;
    return {
        start: start,
        end: end,
        loop: loop,
        style: segment.style
    };
}
function _boundSegment(segment, points, bounds) {
    if (!bounds) return [
        segment
    ];
    var property = bounds.property, startBound = bounds.start, endBound = bounds.end;
    var count = points.length;
    var ref = propertyFn(property), compare = ref.compare, between = ref.between, normalize = ref.normalize;
    var ref1 = getSegment(segment, points, bounds), start = ref1.start, end = ref1.end, loop = ref1.loop, style = ref1.style;
    var result = [];
    var inside = false;
    var subStart = null;
    var value, point, prevValue;
    var startIsBefore = function() {
        return between(startBound, prevValue, value) && compare(startBound, prevValue) !== 0;
    };
    var endIsBefore = function() {
        return compare(endBound, value) === 0 || between(endBound, prevValue, value);
    };
    var shouldStart = function() {
        return inside || startIsBefore();
    };
    var shouldStop = function() {
        return !inside || endIsBefore();
    };
    for(var i = start, prev = start; i <= end; ++i){
        point = points[i % count];
        if (point.skip) continue;
        value = normalize(point[property]);
        if (value === prevValue) continue;
        inside = between(value, startBound, endBound);
        if (subStart === null && shouldStart()) subStart = compare(value, startBound) === 0 ? i : prev;
        if (subStart !== null && shouldStop()) {
            result.push(normalizeSegment({
                start: subStart,
                end: i,
                loop: loop,
                count: count,
                style: style
            }));
            subStart = null;
        }
        prev = i;
        prevValue = value;
    }
    if (subStart !== null) result.push(normalizeSegment({
        start: subStart,
        end: end,
        loop: loop,
        count: count,
        style: style
    }));
    return result;
}
function _boundSegments(line, bounds) {
    var result = [];
    var segments = line.segments;
    for(var i = 0; i < segments.length; i++){
        var _result;
        var sub = _boundSegment(segments[i], line.points, bounds);
        if (sub.length) (_result = result).push.apply(_result, (0, _toConsumableArrayJsDefault.default)(sub));
    }
    return result;
}
function findStartAndEnd(points, count, loop, spanGaps) {
    var start = 0;
    var end = count - 1;
    if (loop && !spanGaps) while(start < count && !points[start].skip)start++;
    while(start < count && points[start].skip)start++;
    start %= count;
    if (loop) end += start;
    while(end > start && points[end % count].skip)end--;
    end %= count;
    return {
        start: start,
        end: end
    };
}
function solidSegments(points, start, max, loop) {
    var count = points.length;
    var result = [];
    var last = start;
    var prev = points[start];
    var end;
    for(end = start + 1; end <= max; ++end){
        var cur = points[end % count];
        if (cur.skip || cur.stop) {
            if (!prev.skip) {
                loop = false;
                result.push({
                    start: start % count,
                    end: (end - 1) % count,
                    loop: loop
                });
                start = last = cur.stop ? end : null;
            }
        } else {
            last = end;
            if (prev.skip) start = end;
        }
        prev = cur;
    }
    if (last !== null) result.push({
        start: start % count,
        end: last % count,
        loop: loop
    });
    return result;
}
function _computeSegments(line, segmentOptions) {
    var points = line.points;
    var spanGaps = line.options.spanGaps;
    var count = points.length;
    if (!count) return [];
    var loop = !!line._loop;
    var ref = findStartAndEnd(points, count, loop, spanGaps), start = ref.start, end = ref.end;
    if (spanGaps === true) return splitByStyles(line, [
        {
            start: start,
            end: end,
            loop: loop
        }
    ], points, segmentOptions);
    var max = end < start ? end + count : end;
    var completeLoop = !!line._fullLoop && start === 0 && end === count - 1;
    return splitByStyles(line, solidSegments(points, start, max, completeLoop), points, segmentOptions);
}
function splitByStyles(line, segments, points, segmentOptions) {
    if (!segmentOptions || !segmentOptions.setContext || !points) return segments;
    return doSplitByStyles(line, segments, points, segmentOptions);
}
function doSplitByStyles(line, segments, points, segmentOptions) {
    var chartContext = line._chart.getContext();
    var baseStyle = readStyle(line.options);
    var datasetIndex = line._datasetIndex, spanGaps = line.options.spanGaps;
    var count = points.length;
    var result = [];
    var prevStyle = baseStyle;
    var start = segments[0].start;
    var i = start;
    function addStyle(s, e, l, st) {
        var dir = spanGaps ? -1 : 1;
        if (s === e) return;
        s += count;
        while(points[s % count].skip)s -= dir;
        while(points[e % count].skip)e += dir;
        if (s % count !== e % count) {
            result.push({
                start: s % count,
                end: e % count,
                loop: l,
                style: st
            });
            prevStyle = st;
            start = e % count;
        }
    }
    var _iteratorNormalCompletion = true, _didIteratorError = false, _iteratorError = undefined;
    try {
        for(var _iterator = segments[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true){
            var segment = _step.value;
            start = spanGaps ? start : segment.start;
            var prev = points[start % count];
            var style = void 0;
            for(i = start + 1; i <= segment.end; i++){
                var pt = points[i % count];
                style = readStyle(segmentOptions.setContext(createContext(chartContext, {
                    type: "segment",
                    p0: prev,
                    p1: pt,
                    p0DataIndex: (i - 1) % count,
                    p1DataIndex: i % count,
                    datasetIndex: datasetIndex
                })));
                if (styleChanged(style, prevStyle)) addStyle(start, i - 1, segment.loop, prevStyle);
                prev = pt;
                prevStyle = style;
            }
            if (start < i - 1) addStyle(start, i - 1, segment.loop, prevStyle);
        }
    } catch (err) {
        _didIteratorError = true;
        _iteratorError = err;
    } finally{
        try {
            if (!_iteratorNormalCompletion && _iterator.return != null) {
                _iterator.return();
            }
        } finally{
            if (_didIteratorError) {
                throw _iteratorError;
            }
        }
    }
    return result;
}
function readStyle(options) {
    return {
        backgroundColor: options.backgroundColor,
        borderCapStyle: options.borderCapStyle,
        borderDash: options.borderDash,
        borderDashOffset: options.borderDashOffset,
        borderJoinStyle: options.borderJoinStyle,
        borderWidth: options.borderWidth,
        borderColor: options.borderColor
    };
}
function styleChanged(style, prevStyle) {
    return prevStyle && JSON.stringify(style) !== JSON.stringify(prevStyle);
}

},{"@swc/helpers/lib/_class_call_check.js":"gNxF8","@swc/helpers/lib/_create_class.js":"iyoaN","@swc/helpers/lib/_define_property.js":"6IXzf","@swc/helpers/lib/_to_consumable_array.js":"cccKv","@swc/helpers/lib/_type_of.js":"9FF45","@parcel/transformer-js/src/esmodule-helpers.js":"jIm8e"}]},["kILFo"], "kILFo", "parcelRequirec571")

//# sourceMappingURL=dashboard_widget.js.map
