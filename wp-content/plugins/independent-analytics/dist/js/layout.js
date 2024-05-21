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
})({"hR6B0":[function(require,module,exports) {
var _scrollToTop = require("./modules/scroll-to-top");
var _notices = require("./modules/notices");
var _stickySidebar = require("./modules/sticky-sidebar");
var _support = require("./modules/support");
jQuery(function($) {
    (0, _stickySidebar.StickySidebar).setup();
    (0, _notices.Notices).setup();
    (0, _scrollToTop.ScrollToTop).setup();
    (0, _support.Support).setup();
});

},{"./modules/scroll-to-top":"cgcMF","./modules/notices":"9NjSc","./modules/sticky-sidebar":"dzCa7","./modules/support":"e3uOb"}],"cgcMF":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "ScrollToTop", function() {
    return ScrollToTop;
});
var $ = jQuery;
var ScrollToTop = {
    setup: function setup() {
        $("#scroll-to-top").on("click", function() {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
            $(this).blur();
        });
    }
};

},{"@parcel/transformer-js/src/esmodule-helpers.js":"jIm8e"}],"jIm8e":[function(require,module,exports) {
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

},{}],"9NjSc":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "Notices", function() {
    return Notices;
});
var _objectSpreadJs = require("@swc/helpers/lib/_object_spread.js");
var _objectSpreadJsDefault = parcelHelpers.interopDefault(_objectSpreadJs);
var $ = jQuery;
var Notices = {
    setup: function() {
        $("#dismiss-notice").on("click", function() {
            var data = (0, _objectSpreadJsDefault.default)({}, iawpActions.confirm_cache_cleared);
            $(".iawp-notice.iawp-warning").hide();
            jQuery.post(ajaxurl, data, function(response) {}).fail(function() {});
        });
    }
};

},{"@swc/helpers/lib/_object_spread.js":"d5EJT","@parcel/transformer-js/src/esmodule-helpers.js":"jIm8e"}],"d5EJT":[function(require,module,exports) {
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

},{"./_define_property":"6IXzf"}],"6IXzf":[function(require,module,exports) {
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

},{}],"dzCa7":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "StickySidebar", function() {
    return StickySidebar;
});
var _objectSpreadJs = require("@swc/helpers/lib/_object_spread.js");
var _objectSpreadJsDefault = parcelHelpers.interopDefault(_objectSpreadJs);
var $ = jQuery;
var StickySidebar = {
    setup: function() {
        var _this = this;
        if ($("#iawp-layout-sidebar").length == 0) return;
        var scrollPosition = window.scrollY;
        var sidebar = document.getElementById("iawp-layout-sidebar");
        var sidebarContainer = document.querySelector(".iawp-layout-sidebar");
        var layoutContainer = document.getElementById("iawp-layout");
        var self = this;
        if (!sidebar && !layoutContainer) return; // These elements aren't visible on an interrupt page such as migration pending page
        sidebar.scroll(0, window.scrollY);
        this.setMinMainHeight();
        document.addEventListener("scroll", function() {
            var change = scrollPosition - window.scrollY;
            if (window.scrollY < 1 || window.scrollY > $(document).height() - $(window).height() - 1) {
                scrollPosition = window.scrollY;
                return;
            }
            sidebar.scroll(0, sidebar.scrollTop - change);
            scrollPosition = window.scrollY;
        });
        window.addEventListener("resize", function() {
            _this.setMinMainHeight();
        });
        document.getElementById("collapse-sidebar").addEventListener("click", function() {
            var isSidebarCollapsed = layoutContainer.classList.toggle("collapsed");
            _this.saveSidebarState(isSidebarCollapsed);
            sidebar.scroll(0, window.scrollY);
            _this.setMinMainHeight();
            _this.setTableHorizontal();
        });
        $("#mobile-menu-toggle").on("click", function() {
            if ($("#menu-container").hasClass("open")) {
                $("#menu-container").removeClass("open");
                $(this).find(".text").text(iawpText.openMobileMenu);
            } else {
                $("#menu-container").addClass("open");
                $(this).find(".text").text(iawpText.closeMobileMenu);
            }
        });
        var dataTableContainer = $("#data-table-container");
        var dataTable = $("#data-table");
        // Data table resizing
        if (dataTable.width() > dataTableContainer.width()) self.setTableHorizontal();
        $(window).on("resize", function() {
            self.setTableHorizontal();
            self.setReportTitleMaxWidth();
        });
        this.setReportTitleMaxWidth();
    },
    saveSidebarState: function(isSidebarCollapsed) {
        var data = (0, _objectSpreadJsDefault.default)({}, iawpActions.update_user_settings, {
            "is_sidebar_collapsed": isSidebarCollapsed
        });
        jQuery.post(ajaxurl, data, function(response) {}).fail(function() {});
    },
    setMinMainHeight: function() {
        $(".iawp-layout-main").css("min-height", $(".iawp-layout-sidebar .inner").outerHeight(true) + 32);
    },
    setTableHorizontal: function() {
        if ($("#data-table").width() > $("#data-table-container").width()) $("#data-table-container").addClass("horizontal");
        else $("#data-table-container").removeClass("horizontal");
    },
    setReportTitleMaxWidth: function() {
        if ($(window).width() < 600) $(".rename-report").css("max-width", "");
        else $(".rename-report").css("max-width", "calc(100% - " + $(".report-title-bar .buttons").width() + "px)");
    }
};

},{"@swc/helpers/lib/_object_spread.js":"d5EJT","@parcel/transformer-js/src/esmodule-helpers.js":"jIm8e"}],"e3uOb":[function(require,module,exports) {
var parcelHelpers = require("@parcel/transformer-js/src/esmodule-helpers.js");
parcelHelpers.defineInteropFlag(exports);
parcelHelpers.export(exports, "Support", function() {
    return Support;
});
var $ = jQuery;
var Support = {
    setup: function() {
        if ($("body").hasClass("analytics_page_independent-analytics-support-center")) {
            $("#search-field").focus();
            var form = document.getElementById("search-form");
            var searchField = document.getElementById("search-field");
            form.onsubmit = function(e) {
                e.preventDefault();
                window.open("https://independentwp.com/?post_type=kb_article&s=" + searchField.value);
            };
        }
    }
};

},{"@parcel/transformer-js/src/esmodule-helpers.js":"jIm8e"}]},["hR6B0"], "hR6B0", "parcelRequirec571")

//# sourceMappingURL=layout.js.map
