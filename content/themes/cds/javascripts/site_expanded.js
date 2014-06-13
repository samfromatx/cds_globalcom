(function () {
    "use strict";

    function a() {}

    function b(a, b) {
        for (var c = a.length; c--;)
            if (a[c].listener === b) return c;
        return -1
    }

    function c(a) {
        return function () {
            return this[a].apply(this, arguments)
        }
    }
    var d = a.prototype,
        e = this,
        f = e.EventEmitter;
    d.getListeners = function (a) {
        var b, c, d = this._getEvents();
        if (a instanceof RegExp) {
            b = {};
            for (c in d) d.hasOwnProperty(c) && a.test(c) && (b[c] = d[c])
        } else b = d[a] || (d[a] = []);
        return b
    }, d.flattenListeners = function (a) {
        var b, c = [];
        for (b = 0; b < a.length; b += 1) c.push(a[b].listener);
        return c
    }, d.getListenersAsObject = function (a) {
        var b, c = this.getListeners(a);
        return c instanceof Array && (b = {}, b[a] = c), b || c
    }, d.addListener = function (a, c) {
        var d, e = this.getListenersAsObject(a),
            f = "object" == typeof c;
        for (d in e) e.hasOwnProperty(d) && -1 === b(e[d], c) && e[d].push(f ? c : {
            listener: c,
            once: !1
        });
        return this
    }, d.on = c("addListener"), d.addOnceListener = function (a, b) {
        return this.addListener(a, {
            listener: b,
            once: !0
        })
    }, d.once = c("addOnceListener"), d.defineEvent = function (a) {
        return this.getListeners(a), this
    }, d.defineEvents = function (a) {
        for (var b = 0; b < a.length; b += 1) this.defineEvent(a[b]);
        return this
    }, d.removeListener = function (a, c) {
        var d, e, f = this.getListenersAsObject(a);
        for (e in f) f.hasOwnProperty(e) && (d = b(f[e], c), -1 !== d && f[e].splice(d, 1));
        return this
    }, d.off = c("removeListener"), d.addListeners = function (a, b) {
        return this.manipulateListeners(!1, a, b)
    }, d.removeListeners = function (a, b) {
        return this.manipulateListeners(!0, a, b)
    }, d.manipulateListeners = function (a, b, c) {
        var d, e, f = a ? this.removeListener : this.addListener,
            g = a ? this.removeListeners : this.addListeners;
        if ("object" != typeof b || b instanceof RegExp)
            for (d = c.length; d--;) f.call(this, b, c[d]);
        else
            for (d in b) b.hasOwnProperty(d) && (e = b[d]) && ("function" == typeof e ? f.call(this, d, e) : g.call(this, d, e));
        return this
    }, d.removeEvent = function (a) {
        var b, c = typeof a,
            d = this._getEvents();
        if ("string" === c) delete d[a];
        else if (a instanceof RegExp)
            for (b in d) d.hasOwnProperty(b) && a.test(b) && delete d[b];
        else delete this._events;
        return this
    }, d.removeAllListeners = c("removeEvent"), d.emitEvent = function (a, b) {
        var c, d, e, f, g = this.getListenersAsObject(a);
        for (e in g)
            if (g.hasOwnProperty(e))
                for (d = g[e].length; d--;) c = g[e][d], c.once === !0 && this.removeListener(a, c.listener), f = c.listener.apply(this, b || []), f === this._getOnceReturnValue() && this.removeListener(a, c.listener);
        return this
    }, d.trigger = c("emitEvent"), d.emit = function (a) {
        var b = Array.prototype.slice.call(arguments, 1);
        return this.emitEvent(a, b)
    }, d.setOnceReturnValue = function (a) {
        return this._onceReturnValue = a, this
    }, d._getOnceReturnValue = function () {
        return this.hasOwnProperty("_onceReturnValue") ? this._onceReturnValue : !0
    }, d._getEvents = function () {
        return this._events || (this._events = {})
    }, a.noConflict = function () {
        return e.EventEmitter = f, a
    }, "function" == typeof define && define.amd ? define(function () {
        return a
    }) : "object" == typeof module && module.exports ? module.exports = a : this.EventEmitter = a
}).call(this),
function (a) {
    "use strict";

    function b(b) {
        var c = a.event;
        return c.target = c.target || c.srcElement || b, c
    }
    var c = document.documentElement,
        d = function () {};
    c.addEventListener ? d = function (a, b, c) {
        a.addEventListener(b, c, !1)
    } : c.attachEvent && (d = function (a, c, d) {
        a[c + d] = d.handleEvent ? function () {
            var c = b(a);
            d.handleEvent.call(d, c)
        } : function () {
            var c = b(a);
            d.call(a, c)
        }, a.attachEvent("on" + c, a[c + d])
    });
    var e = function () {};
    c.removeEventListener ? e = function (a, b, c) {
        a.removeEventListener(b, c, !1)
    } : c.detachEvent && (e = function (a, b, c) {
        a.detachEvent("on" + b, a[b + c]);
        try {
            delete a[b + c]
        } catch (d) {
            a[b + c] = void 0
        }
    });
    var f = {
        bind: d,
        unbind: e
    };
    "function" == typeof define && define.amd ? define(f) : "object" == typeof exports ? module.exports = f : a.eventie = f
}(this),
function (a) {
    "use strict";

    function b(a) {
        "function" == typeof a && (b.isReady ? a() : f.push(a))
    }

    function c(a) {
        var c = "readystatechange" === a.type && "complete" !== e.readyState;
        if (!b.isReady && !c) {
            b.isReady = !0;
            for (var d = 0, g = f.length; g > d; d++) {
                var h = f[d];
                h()
            }
        }
    }

    function d(d) {
        return d.bind(e, "DOMContentLoaded", c), d.bind(e, "readystatechange", c), d.bind(a, "load", c), b
    }
    var e = a.document,
        f = [];
    b.isReady = !1, "function" == typeof define && define.amd ? (b.isReady = "function" == typeof requirejs, define(["eventie/eventie"], d)) : a.docReady = d(a.eventie)
}(this),
function (a) {
    "use strict";

    function b(a) {
        if (a) {
            if ("string" == typeof d[a]) return a;
            a = a.charAt(0).toUpperCase() + a.slice(1);
            for (var b, e = 0, f = c.length; f > e; e++)
                if (b = c[e] + a, "string" == typeof d[b]) return b
        }
    }
    var c = "Webkit Moz ms Ms O".split(" "),
        d = document.documentElement.style;
    "function" == typeof define && define.amd ? define(function () {
        return b
    }) : "object" == typeof exports ? module.exports = b : a.getStyleProperty = b
}(window),
function (a) {
    "use strict";

    function b(a) {
        var b = parseFloat(a),
            c = -1 === a.indexOf("%") && !isNaN(b);
        return c && b
    }

    function c() {
        for (var a = {
            width: 0,
            height: 0,
            innerWidth: 0,
            innerHeight: 0,
            outerWidth: 0,
            outerHeight: 0
        }, b = 0, c = g.length; c > b; b++) {
            var d = g[b];
            a[d] = 0
        }
        return a
    }

    function d(a) {
        function d(a) {
            if ("string" == typeof a && (a = document.querySelector(a)), a && "object" == typeof a && a.nodeType) {
                var d = f(a);
                if ("none" === d.display) return c();
                var e = {};
                e.width = a.offsetWidth, e.height = a.offsetHeight;
                for (var k = e.isBorderBox = !(!j || !d[j] || "border-box" !== d[j]), l = 0, m = g.length; m > l; l++) {
                    var n = g[l],
                        o = d[n];
                    o = h(a, o);
                    var p = parseFloat(o);
                    e[n] = isNaN(p) ? 0 : p
                }
                var q = e.paddingLeft + e.paddingRight,
                    r = e.paddingTop + e.paddingBottom,
                    s = e.marginLeft + e.marginRight,
                    t = e.marginTop + e.marginBottom,
                    u = e.borderLeftWidth + e.borderRightWidth,
                    v = e.borderTopWidth + e.borderBottomWidth,
                    w = k && i,
                    x = b(d.width);
                x !== !1 && (e.width = x + (w ? 0 : q + u));
                var y = b(d.height);
                return y !== !1 && (e.height = y + (w ? 0 : r + v)), e.innerWidth = e.width - (q + u), e.innerHeight = e.height - (r + v), e.outerWidth = e.width + s, e.outerHeight = e.height + t, e
            }
        }

        function h(a, b) {
            if (e || -1 === b.indexOf("%")) return b;
            var c = a.style,
                d = c.left,
                f = a.runtimeStyle,
                g = f && f.left;
            return g && (f.left = a.currentStyle.left), c.left = b, b = c.pixelLeft, c.left = d, g && (f.left = g), b
        }
        var i, j = a("boxSizing");
        return function () {
            if (j) {
                var a = document.createElement("div");
                a.style.width = "200px", a.style.padding = "1px 2px 3px 4px", a.style.borderStyle = "solid", a.style.borderWidth = "1px 2px 3px 4px", a.style[j] = "border-box";
                var c = document.body || document.documentElement;
                c.appendChild(a);
                var d = f(a);
                i = 200 === b(d.width), c.removeChild(a)
            }
        }(), d
    }
    var e = a.getComputedStyle,
        f = e ? function (a) {
            return e(a, null)
        } : function (a) {
            return a.currentStyle
        }, g = ["paddingLeft", "paddingRight", "paddingTop", "paddingBottom", "marginLeft", "marginRight", "marginTop", "marginBottom", "borderLeftWidth", "borderRightWidth", "borderTopWidth", "borderBottomWidth"];
    "function" == typeof define && define.amd ? define(["get-style-property/get-style-property"], d) : "object" == typeof exports ? module.exports = d(require("get-style-property")) : a.getSize = d(a.getStyleProperty)
}(window),
function (a) {
    "use strict";

    function b() {}

    function c(a) {
        function c(b) {
            b.prototype.option || (b.prototype.option = function (b) {
                a.isPlainObject(b) && (this.options = a.extend(!0, this.options, b))
            })
        }

        function e(b, c) {
            a.fn[b] = function (e) {
                if ("string" == typeof e) {
                    for (var g = d.call(arguments, 1), h = 0, i = this.length; i > h; h++) {
                        var j = this[h],
                            k = a.data(j, b);
                        if (k)
                            if (a.isFunction(k[e]) && "_" !== e.charAt(0)) {
                                var l = k[e].apply(k, g);
                                if (void 0 !== l) return l
                            } else f("no such method '" + e + "' for " + b + " instance");
                            else f("cannot call methods on " + b + " prior to initialization; attempted to call '" + e + "'")
                    }
                    return this
                }
                return this.each(function () {
                    var d = a.data(this, b);
                    d ? (d.option(e), d._init()) : (d = new c(this, e), a.data(this, b, d))
                })
            }
        }
        if (a) {
            var f = "undefined" == typeof console ? b : function (a) {
                    console.error(a)
                };
            return a.bridget = function (a, b) {
                c(b), e(a, b)
            }, a.bridget
        }
    }
    var d = Array.prototype.slice;
    "function" == typeof define && define.amd ? define(["jquery"], c) : c(a.jQuery)
}(window),
function (a, b) {
    "use strict";

    function c(a, b) {
        return a[h](b)
    }

    function d(a) {
        if (!a.parentNode) {
            var b = document.createDocumentFragment();
            b.appendChild(a)
        }
    }

    function e(a, b) {
        d(a);
        for (var c = a.parentNode.querySelectorAll(b), e = 0, f = c.length; f > e; e++)
            if (c[e] === a) return !0;
        return !1
    }

    function f(a, b) {
        return d(a), c(a, b)
    }
    var g, h = function () {
            if (b.matchesSelector) return "matchesSelector";
            for (var a = ["webkit", "moz", "ms", "o"], c = 0, d = a.length; d > c; c++) {
                var e = a[c],
                    f = e + "MatchesSelector";
                if (b[f]) return f
            }
        }();
    if (h) {
        var i = document.createElement("div"),
            j = c(i, "div");
        g = j ? c : f
    } else g = e;
    "function" == typeof define && define.amd ? define(function () {
        return g
    }) : window.matchesSelector = g
}(this, Element.prototype),
function (a) {
    "use strict";

    function b(a, b) {
        for (var c in b) a[c] = b[c];
        return a
    }

    function c(a) {
        for (var b in a) return !1;
        return b = null, !0
    }

    function d(a) {
        return a.replace(/([A-Z])/g, function (a) {
            return "-" + a.toLowerCase()
        })
    }

    function e(a, e, f) {
        function h(a, b) {
            a && (this.element = a, this.layout = b, this.position = {
                x: 0,
                y: 0
            }, this._create())
        }
        var i = f("transition"),
            j = f("transform"),
            k = i && j,
            l = !! f("perspective"),
            m = {
                WebkitTransition: "webkitTransitionEnd",
                MozTransition: "transitionend",
                OTransition: "otransitionend",
                transition: "transitionend"
            }[i],
            n = ["transform", "transition", "transitionDuration", "transitionProperty"],
            o = function () {
                for (var a = {}, b = 0, c = n.length; c > b; b++) {
                    var d = n[b],
                        e = f(d);
                    e && e !== d && (a[d] = e)
                }
                return a
            }();
        b(h.prototype, a.prototype), h.prototype._create = function () {
            this._transn = {
                ingProperties: {},
                clean: {},
                onEnd: {}
            }, this.css({
                position: "absolute"
            })
        }, h.prototype.handleEvent = function (a) {
            var b = "on" + a.type;
            this[b] && this[b](a)
        }, h.prototype.getSize = function () {
            this.size = e(this.element)
        }, h.prototype.css = function (a) {
            var b = this.element.style;
            for (var c in a) {
                var d = o[c] || c;
                b[d] = a[c]
            }
        }, h.prototype.getPosition = function () {
            var a = g(this.element),
                b = this.layout.options,
                c = b.isOriginLeft,
                d = b.isOriginTop,
                e = parseInt(a[c ? "left" : "right"], 10),
                f = parseInt(a[d ? "top" : "bottom"], 10);
            e = isNaN(e) ? 0 : e, f = isNaN(f) ? 0 : f;
            var h = this.layout.size;
            e -= c ? h.paddingLeft : h.paddingRight, f -= d ? h.paddingTop : h.paddingBottom, this.position.x = e, this.position.y = f
        }, h.prototype.layoutPosition = function () {
            var a = this.layout.size,
                b = this.layout.options,
                c = {};
            b.isOriginLeft ? (c.left = this.position.x + a.paddingLeft + "px", c.right = "") : (c.right = this.position.x + a.paddingRight + "px", c.left = ""), b.isOriginTop ? (c.top = this.position.y + a.paddingTop + "px", c.bottom = "") : (c.bottom = this.position.y + a.paddingBottom + "px", c.top = ""), this.css(c), this.emitEvent("layout", [this])
        };
        var p = l ? function (a, b) {
                return "translate3d(" + a + "px, " + b + "px, 0)"
            } : function (a, b) {
                return "translate(" + a + "px, " + b + "px)"
            };
        h.prototype._transitionTo = function (a, b) {
            this.getPosition();
            var c = this.position.x,
                d = this.position.y,
                e = parseInt(a, 10),
                f = parseInt(b, 10),
                g = e === this.position.x && f === this.position.y;
            if (this.setPosition(a, b), g && !this.isTransitioning) return void this.layoutPosition();
            var h = a - c,
                i = b - d,
                j = {}, k = this.layout.options;
            h = k.isOriginLeft ? h : -h, i = k.isOriginTop ? i : -i, j.transform = p(h, i), this.transition({
                to: j,
                onTransitionEnd: {
                    transform: this.layoutPosition
                },
                isCleaning: !0
            })
        }, h.prototype.goTo = function (a, b) {
            this.setPosition(a, b), this.layoutPosition()
        }, h.prototype.moveTo = k ? h.prototype._transitionTo : h.prototype.goTo, h.prototype.setPosition = function (a, b) {
            this.position.x = parseInt(a, 10), this.position.y = parseInt(b, 10)
        }, h.prototype._nonTransition = function (a) {
            this.css(a.to), a.isCleaning && this._removeStyles(a.to);
            for (var b in a.onTransitionEnd) a.onTransitionEnd[b].call(this)
        }, h.prototype._transition = function (a) {
            if (!parseFloat(this.layout.options.transitionDuration)) return void this._nonTransition(a);
            var b = this._transn;
            for (var c in a.onTransitionEnd) b.onEnd[c] = a.onTransitionEnd[c];
            for (c in a.to) b.ingProperties[c] = !0, a.isCleaning && (b.clean[c] = !0);
            if (a.from) {
                this.css(a.from);
                var d = this.element.offsetHeight;
                d = null
            }
            this.enableTransition(a.to), this.css(a.to), this.isTransitioning = !0
        };
        var q = j && d(j) + ",opacity";
        h.prototype.enableTransition = function () {
            this.isTransitioning || (this.css({
                transitionProperty: q,
                transitionDuration: this.layout.options.transitionDuration
            }), this.element.addEventListener(m, this, !1))
        }, h.prototype.transition = h.prototype[i ? "_transition" : "_nonTransition"], h.prototype.onwebkitTransitionEnd = function (a) {
            this.ontransitionend(a)
        }, h.prototype.onotransitionend = function (a) {
            this.ontransitionend(a)
        };
        var r = {
            "-webkit-transform": "transform",
            "-moz-transform": "transform",
            "-o-transform": "transform"
        };
        h.prototype.ontransitionend = function (a) {
            if (a.target === this.element) {
                var b = this._transn,
                    d = r[a.propertyName] || a.propertyName;
                if (delete b.ingProperties[d], c(b.ingProperties) && this.disableTransition(), d in b.clean && (this.element.style[a.propertyName] = "", delete b.clean[d]), d in b.onEnd) {
                    var e = b.onEnd[d];
                    e.call(this), delete b.onEnd[d]
                }
                this.emitEvent("transitionEnd", [this])
            }
        }, h.prototype.disableTransition = function () {
            this.removeTransitionStyles(), this.element.removeEventListener(m, this, !1), this.isTransitioning = !1
        }, h.prototype._removeStyles = function (a) {
            var b = {};
            for (var c in a) b[c] = "";
            this.css(b)
        };
        var s = {
            transitionProperty: "",
            transitionDuration: ""
        };
        return h.prototype.removeTransitionStyles = function () {
            this.css(s)
        }, h.prototype.removeElem = function () {
            this.element.parentNode.removeChild(this.element), this.emitEvent("remove", [this])
        }, h.prototype.remove = function () {
            if (!i || !parseFloat(this.layout.options.transitionDuration)) return void this.removeElem();
            var a = this;
            this.on("transitionEnd", function () {
                return a.removeElem(), !0
            }), this.hide()
        }, h.prototype.reveal = function () {
            delete this.isHidden, this.css({
                display: ""
            });
            var a = this.layout.options;
            this.transition({
                from: a.hiddenStyle,
                to: a.visibleStyle,
                isCleaning: !0
            })
        }, h.prototype.hide = function () {
            this.isHidden = !0, this.css({
                display: ""
            });
            var a = this.layout.options;
            this.transition({
                from: a.visibleStyle,
                to: a.hiddenStyle,
                isCleaning: !0,
                onTransitionEnd: {
                    opacity: function () {
                        this.isHidden && this.css({
                            display: "none"
                        })
                    }
                }
            })
        }, h.prototype.destroy = function () {
            this.css({
                position: "",
                left: "",
                right: "",
                top: "",
                bottom: "",
                transition: "",
                transform: ""
            })
        }, h
    }
    var f = document.defaultView,
        g = f && f.getComputedStyle ? function (a) {
            return f.getComputedStyle(a, null)
        } : function (a) {
            return a.currentStyle
        };
    "function" == typeof define && define.amd ? define(["eventEmitter/EventEmitter", "get-size/get-size", "get-style-property/get-style-property"], e) : (a.Outlayer = {}, a.Outlayer.Item = e(a.EventEmitter, a.getSize, a.getStyleProperty))
}(window),
function (a) {
    "use strict";

    function b(a, b) {
        for (var c in b) a[c] = b[c];
        return a
    }

    function c(a) {
        return "[object Array]" === l.call(a)
    }

    function d(a) {
        var b = [];
        if (c(a)) b = a;
        else if (a && "number" == typeof a.length)
            for (var d = 0, e = a.length; e > d; d++) b.push(a[d]);
        else b.push(a);
        return b
    }

    function e(a, b) {
        var c = n(b, a); - 1 !== c && b.splice(c, 1)
    }

    function f(a) {
        return a.replace(/(.)([A-Z])/g, function (a, b, c) {
            return b + "-" + c
        }).toLowerCase()
    }

    function g(c, g, l, n, o, p) {
        function q(a, c) {
            if ("string" == typeof a && (a = h.querySelector(a)), !a || !m(a)) return void(i && i.error("Bad " + this.constructor.namespace + " element: " + a));
            this.element = a, this.options = b({}, this.options), this.option(c);
            var d = ++s;
            this.element.outlayerGUID = d, t[d] = this, this._create(), this.options.isInitLayout && this.layout()
        }

        function r(a, c) {
            a.prototype[c] = b({}, q.prototype[c])
        }
        var s = 0,
            t = {};
        return q.namespace = "outlayer", q.Item = p, q.prototype.options = {
            containerStyle: {
                position: "relative"
            },
            isInitLayout: !0,
            isOriginLeft: !0,
            isOriginTop: !0,
            isResizeBound: !0,
            transitionDuration: "0.4s",
            hiddenStyle: {
                opacity: 0,
                transform: "scale(0.001)"
            },
            visibleStyle: {
                opacity: 1,
                transform: "scale(1)"
            }
        }, b(q.prototype, l.prototype), q.prototype.option = function (a) {
            b(this.options, a)
        }, q.prototype._create = function () {
            this.reloadItems(), this.stamps = [], this.stamp(this.options.stamp), b(this.element.style, this.options.containerStyle), this.options.isResizeBound && this.bindResize()
        }, q.prototype.reloadItems = function () {
            this.items = this._itemize(this.element.children)
        }, q.prototype._itemize = function (a) {
            for (var b = this._filterFindItemElements(a), c = this.constructor.Item, d = [], e = 0, f = b.length; f > e; e++) {
                var g = b[e],
                    h = new c(g, this);
                d.push(h)
            }
            return d
        }, q.prototype._filterFindItemElements = function (a) {
            a = d(a);
            for (var b = this.options.itemSelector, c = [], e = 0, f = a.length; f > e; e++) {
                var g = a[e];
                if (m(g))
                    if (b) {
                        o(g, b) && c.push(g);
                        for (var h = g.querySelectorAll(b), i = 0, j = h.length; j > i; i++) c.push(h[i])
                    } else c.push(g)
            }
            return c
        }, q.prototype.getItemElements = function () {
            for (var a = [], b = 0, c = this.items.length; c > b; b++) a.push(this.items[b].element);
            return a
        }, q.prototype.layout = function () {
            this._resetLayout(), this._manageStamps();
            var a = void 0 !== this.options.isLayoutInstant ? this.options.isLayoutInstant : !this._isLayoutInited;
            this.layoutItems(this.items, a), this._isLayoutInited = !0
        }, q.prototype._init = q.prototype.layout, q.prototype._resetLayout = function () {
            this.getSize()
        }, q.prototype.getSize = function () {
            this.size = n(this.element)
        }, q.prototype._getMeasurement = function (a, b) {
            var c, d = this.options[a];
            d ? ("string" == typeof d ? c = this.element.querySelector(d) : m(d) && (c = d), this[a] = c ? n(c)[b] : d) : this[a] = 0
        }, q.prototype.layoutItems = function (a, b) {
            a = this._getItemsForLayout(a), this._layoutItems(a, b), this._postLayout()
        }, q.prototype._getItemsForLayout = function (a) {
            for (var b = [], c = 0, d = a.length; d > c; c++) {
                var e = a[c];
                e.isIgnored || b.push(e)
            }
            return b
        }, q.prototype._layoutItems = function (a, b) {
            function c() {
                d.emitEvent("layoutComplete", [d, a])
            }
            var d = this;
            if (!a || !a.length) return void c();
            this._itemsOn(a, "layout", c);
            for (var e = [], f = 0, g = a.length; g > f; f++) {
                var h = a[f],
                    i = this._getItemLayoutPosition(h);
                i.item = h, i.isInstant = b || h.isLayoutInstant, e.push(i)
            }
            this._processLayoutQueue(e)
        }, q.prototype._getItemLayoutPosition = function () {
            return {
                x: 0,
                y: 0
            }
        }, q.prototype._processLayoutQueue = function (a) {
            for (var b = 0, c = a.length; c > b; b++) {
                var d = a[b];
                this._positionItem(d.item, d.x, d.y, d.isInstant)
            }
        }, q.prototype._positionItem = function (a, b, c, d) {
            d ? a.goTo(b, c) : a.moveTo(b, c)
        }, q.prototype._postLayout = function () {
            var a = this._getContainerSize();
            a && (this._setContainerMeasure(a.width, !0), this._setContainerMeasure(a.height, !1))
        }, q.prototype._getContainerSize = k, q.prototype._setContainerMeasure = function (a, b) {
            if (void 0 !== a) {
                var c = this.size;
                c.isBorderBox && (a += b ? c.paddingLeft + c.paddingRight + c.borderLeftWidth + c.borderRightWidth : c.paddingBottom + c.paddingTop + c.borderTopWidth + c.borderBottomWidth), a = Math.max(a, 0), this.element.style[b ? "width" : "height"] = a + "px"
            }
        }, q.prototype._itemsOn = function (a, b, c) {
            function d() {
                return e++, e === f && c.call(g), !0
            }
            for (var e = 0, f = a.length, g = this, h = 0, i = a.length; i > h; h++) {
                var j = a[h];
                j.on(b, d)
            }
        }, q.prototype.ignore = function (a) {
            var b = this.getItem(a);
            b && (b.isIgnored = !0)
        }, q.prototype.unignore = function (a) {
            var b = this.getItem(a);
            b && delete b.isIgnored
        }, q.prototype.stamp = function (a) {
            if (a = this._find(a)) {
                this.stamps = this.stamps.concat(a);
                for (var b = 0, c = a.length; c > b; b++) {
                    var d = a[b];
                    this.ignore(d)
                }
            }
        }, q.prototype.unstamp = function (a) {
            if (a = this._find(a))
                for (var b = 0, c = a.length; c > b; b++) {
                    var d = a[b];
                    e(d, this.stamps), this.unignore(d)
                }
        }, q.prototype._find = function (a) {
            return a ? ("string" == typeof a && (a = this.element.querySelectorAll(a)), a = d(a)) : void 0
        }, q.prototype._manageStamps = function () {
            if (this.stamps && this.stamps.length) {
                this._getBoundingRect();
                for (var a = 0, b = this.stamps.length; b > a; a++) {
                    var c = this.stamps[a];
                    this._manageStamp(c)
                }
            }
        }, q.prototype._getBoundingRect = function () {
            var a = this.element.getBoundingClientRect(),
                b = this.size;
            this._boundingRect = {
                left: a.left + b.paddingLeft + b.borderLeftWidth,
                top: a.top + b.paddingTop + b.borderTopWidth,
                right: a.right - (b.paddingRight + b.borderRightWidth),
                bottom: a.bottom - (b.paddingBottom + b.borderBottomWidth)
            }
        }, q.prototype._manageStamp = k, q.prototype._getElementOffset = function (a) {
            var b = a.getBoundingClientRect(),
                c = this._boundingRect,
                d = n(a),
                e = {
                    left: b.left - c.left - d.marginLeft,
                    top: b.top - c.top - d.marginTop,
                    right: c.right - b.right - d.marginRight,
                    bottom: c.bottom - b.bottom - d.marginBottom
                };
            return e
        }, q.prototype.handleEvent = function (a) {
            var b = "on" + a.type;
            this[b] && this[b](a)
        }, q.prototype.bindResize = function () {
            this.isResizeBound || (c.bind(a, "resize", this), this.isResizeBound = !0)
        }, q.prototype.unbindResize = function () {
            c.unbind(a, "resize", this), this.isResizeBound = !1
        }, q.prototype.onresize = function () {
            function a() {
                b.resize(), delete b.resizeTimeout
            }
            this.resizeTimeout && clearTimeout(this.resizeTimeout);
            var b = this;
            this.resizeTimeout = setTimeout(a, 100)
        }, q.prototype.resize = function () {
            var a = n(this.element),
                b = this.size && a;
            b && a.innerWidth === this.size.innerWidth || this.layout()
        }, q.prototype.addItems = function (a) {
            var b = this._itemize(a);
            return b.length && (this.items = this.items.concat(b)), b
        }, q.prototype.appended = function (a) {
            var b = this.addItems(a);
            b.length && (this.layoutItems(b, !0), this.reveal(b))
        }, q.prototype.prepended = function (a) {
            var b = this._itemize(a);
            if (b.length) {
                var c = this.items.slice(0);
                this.items = b.concat(c), this._resetLayout(), this._manageStamps(), this.layoutItems(b, !0), this.reveal(b), this.layoutItems(c)
            }
        }, q.prototype.reveal = function (a) {
            var b = a && a.length;
            if (b)
                for (var c = 0; b > c; c++) {
                    var d = a[c];
                    d.reveal()
                }
        }, q.prototype.hide = function (a) {
            var b = a && a.length;
            if (b)
                for (var c = 0; b > c; c++) {
                    var d = a[c];
                    d.hide()
                }
        }, q.prototype.getItem = function (a) {
            for (var b = 0, c = this.items.length; c > b; b++) {
                var d = this.items[b];
                if (d.element === a) return d
            }
        }, q.prototype.getItems = function (a) {
            if (a && a.length) {
                for (var b = [], c = 0, d = a.length; d > c; c++) {
                    var e = a[c],
                        f = this.getItem(e);
                    f && b.push(f)
                }
                return b
            }
        }, q.prototype.remove = function (a) {
            a = d(a);
            var b = this.getItems(a);
            if (b && b.length) {
                this._itemsOn(b, "remove", function () {
                    this.emitEvent("removeComplete", [this, b])
                });
                for (var c = 0, f = b.length; f > c; c++) {
                    var g = b[c];
                    g.remove(), e(g, this.items)
                }
            }
        }, q.prototype.destroy = function () {
            var a = this.element.style;
            a.height = "", a.position = "", a.width = "";
            for (var b = 0, c = this.items.length; c > b; b++) {
                var d = this.items[b];
                d.destroy()
            }
            this.unbindResize(), delete this.element.outlayerGUID, j && j.removeData(this.element, this.constructor.namespace)
        }, q.data = function (a) {
            var b = a && a.outlayerGUID;
            return b && t[b]
        }, q.create = function (a, c) {
            function d() {
                q.apply(this, arguments)
            }
            return Object.create ? d.prototype = Object.create(q.prototype) : b(d.prototype, q.prototype), d.prototype.constructor = d, r(d, "options"), b(d.prototype.options, c), d.namespace = a, d.data = q.data, d.Item = function () {
                p.apply(this, arguments)
            }, d.Item.prototype = new p, g(function () {
                for (var b = f(a), c = h.querySelectorAll(".js-" + b), e = "data-" + b + "-options", g = 0, k = c.length; k > g; g++) {
                    var l, m = c[g],
                        n = m.getAttribute(e);
                    try {
                        l = n && JSON.parse(n)
                    } catch (o) {
                        i && i.error("Error parsing " + e + " on " + m.nodeName.toLowerCase() + (m.id ? "#" + m.id : "") + ": " + o);
                        continue
                    }
                    var p = new d(m, l);
                    j && j.data(m, a, p)
                }
            }), j && j.bridget && j.bridget(a, d), d
        }, q.Item = p, q
    }
    var h = a.document,
        i = a.console,
        j = a.jQuery,
        k = function () {}, l = Object.prototype.toString,
        m = "object" == typeof HTMLElement ? function (a) {
            return a instanceof HTMLElement
        } : function (a) {
            return a && "object" == typeof a && 1 === a.nodeType && "string" == typeof a.nodeName
        }, n = Array.prototype.indexOf ? function (a, b) {
            return a.indexOf(b)
        } : function (a, b) {
            for (var c = 0, d = a.length; d > c; c++)
                if (a[c] === b) return c;
            return -1
        };
    "function" == typeof define && define.amd ? define(["eventie/eventie", "doc-ready/doc-ready", "eventEmitter/EventEmitter", "get-size/get-size", "matches-selector/matches-selector", "./item"], g) : a.Outlayer = g(a.eventie, a.docReady, a.EventEmitter, a.getSize, a.matchesSelector, a.Outlayer.Item)
}(window),
function (a) {
    "use strict";

    function b(a, b) {
        var d = a.create("masonry");
        return d.prototype._resetLayout = function () {
            this.getSize(), this._getMeasurement("columnWidth", "outerWidth"), this._getMeasurement("gutter", "outerWidth"), this.measureColumns();
            var a = this.cols;
            for (this.colYs = []; a--;) this.colYs.push(0);
            this.maxY = 0
        }, d.prototype.measureColumns = function () {
            if (this.getContainerWidth(), !this.columnWidth) {
                var a = this.items[0],
                    c = a && a.element;
                this.columnWidth = c && b(c).outerWidth || this.containerWidth
            }
            this.columnWidth += this.gutter, this.cols = Math.floor((this.containerWidth + this.gutter) / this.columnWidth), this.cols = Math.max(this.cols, 1)
        }, d.prototype.getContainerWidth = function () {
            var a = this.options.isFitWidth ? this.element.parentNode : this.element,
                c = b(a);
            this.containerWidth = c && c.innerWidth
        }, d.prototype._getItemLayoutPosition = function (a) {
            a.getSize();
            var b = a.size.outerWidth % this.columnWidth,
                d = b && 1 > b ? "round" : "ceil",
                e = Math[d](a.size.outerWidth / this.columnWidth);
            e = Math.min(e, this.cols);
            for (var f = this._getColGroup(e), g = Math.min.apply(Math, f), h = c(f, g), i = {
                    x: this.columnWidth * h,
                    y: g
                }, j = g + a.size.outerHeight, k = this.cols + 1 - f.length, l = 0; k > l; l++) this.colYs[h + l] = j;
            return i
        }, d.prototype._getColGroup = function (a) {
            if (2 > a) return this.colYs;
            for (var b = [], c = this.cols + 1 - a, d = 0; c > d; d++) {
                var e = this.colYs.slice(d, d + a);
                b[d] = Math.max.apply(Math, e)
            }
            return b
        }, d.prototype._manageStamp = function (a) {
            var c = b(a),
                d = this._getElementOffset(a),
                e = this.options.isOriginLeft ? d.left : d.right,
                f = e + c.outerWidth,
                g = Math.floor(e / this.columnWidth);
            g = Math.max(0, g);
            var h = Math.floor(f / this.columnWidth);
            h -= f % this.columnWidth ? 0 : 1, h = Math.min(this.cols - 1, h);
            for (var i = (this.options.isOriginTop ? d.top : d.bottom) + c.outerHeight, j = g; h >= j; j++) this.colYs[j] = Math.max(i, this.colYs[j])
        }, d.prototype._getContainerSize = function () {
            this.maxY = Math.max.apply(Math, this.colYs);
            var a = {
                height: this.maxY
            };
            return this.options.isFitWidth && (a.width = this._getContainerFitWidth()), a
        }, d.prototype._getContainerFitWidth = function () {
            for (var a = 0, b = this.cols; --b && 0 === this.colYs[b];) a++;
            return (this.cols - a) * this.columnWidth - this.gutter
        }, d.prototype.resize = function () {
            var a = this.containerWidth;
            this.getContainerWidth(), a !== this.containerWidth && this.layout()
        }, d
    }
    var c = Array.prototype.indexOf ? function (a, b) {
            return a.indexOf(b)
        } : function (a, b) {
            for (var c = 0, d = a.length; d > c; c++) {
                var e = a[c];
                if (e === b) return c
            }
            return -1
        };
    "function" == typeof define && define.amd ? define(["outlayer/outlayer", "get-size/get-size"], b) : a.Masonry = b(a.Outlayer, a.getSize)
}(window), "function" != typeof Object.create && (Object.create = function (a) {
    function b() {}
    return b.prototype = a, new b
}),
function (a, b, c, d) {
    var e = {
        init: function (b, c) {
            var d = this;
            d.$elem = a(c), d.options = a.extend({}, a.fn.owlCarousel.options, d.$elem.data(), b), d.userOptions = b, d.loadContent()
        },
        loadContent: function () {
            function b(a) {
                if ("function" == typeof c.options.jsonSuccess) c.options.jsonSuccess.apply(this, [a]);
                else {
                    var b = "";
                    for (var d in a.owl) b += a.owl[d].item;
                    c.$elem.html(b)
                }
                c.logIn()
            }
            var c = this;
            if ("function" == typeof c.options.beforeInit && c.options.beforeInit.apply(this, [c.$elem]), "string" == typeof c.options.jsonPath) {
                var d = c.options.jsonPath;
                a.getJSON(d, b)
            } else c.logIn()
        },
        logIn: function () {
            var a = this;
            a.$elem.data("owl-originalStyles", a.$elem.attr("style")).data("owl-originalClasses", a.$elem.attr("class")), a.$elem.css({
                opacity: 0
            }), a.orignalItems = a.options.items, a.checkBrowser(), a.wrapperWidth = 0, a.checkVisible, a.setVars()
        },
        setVars: function () {
            var a = this;
            return 0 === a.$elem.children().length ? !1 : (a.baseClass(), a.eventTypes(), a.$userItems = a.$elem.children(), a.itemsAmount = a.$userItems.length, a.wrapItems(), a.$owlItems = a.$elem.find(".owl-item"), a.$owlWrapper = a.$elem.find(".owl-wrapper"), a.playDirection = "next", a.prevItem = 0, a.prevArr = [0], a.currentItem = 0, a.customEvents(), void a.onStartup())
        },
        onStartup: function () {
            var a = this;
            a.updateItems(), a.calculateAll(), a.buildControls(), a.updateControls(), a.response(), a.moveEvents(), a.stopOnHover(), a.owlStatus(), a.options.transitionStyle !== !1 && a.transitionTypes(a.options.transitionStyle), a.options.autoPlay === !0 && (a.options.autoPlay = 5e3), a.play(), a.$elem.find(".owl-wrapper").css("display", "block"), a.$elem.is(":visible") ? a.$elem.css("opacity", 1) : a.watchVisibility(), a.onstartup = !1, a.eachMoveUpdate(), "function" == typeof a.options.afterInit && a.options.afterInit.apply(this, [a.$elem])
        },
        eachMoveUpdate: function () {
            var a = this;
            a.options.lazyLoad === !0 && a.lazyLoad(), a.options.autoHeight === !0 && a.autoHeight(), a.onVisibleItems(), "function" == typeof a.options.afterAction && a.options.afterAction.apply(this, [a.$elem])
        },
        updateVars: function () {
            var a = this;
            "function" == typeof a.options.beforeUpdate && a.options.beforeUpdate.apply(this, [a.$elem]), a.watchVisibility(), a.updateItems(), a.calculateAll(), a.updatePosition(), a.updateControls(), a.eachMoveUpdate(), "function" == typeof a.options.afterUpdate && a.options.afterUpdate.apply(this, [a.$elem])
        },
        reload: function () {
            var a = this;
            setTimeout(function () {
                a.updateVars()
            }, 0)
        },
        watchVisibility: function () {
            var a = this;
            return a.$elem.is(":visible") !== !1 ? !1 : (a.$elem.css({
                opacity: 0
            }), clearInterval(a.autoPlayInterval), clearInterval(a.checkVisible), void(a.checkVisible = setInterval(function () {
                a.$elem.is(":visible") && (a.reload(), a.$elem.animate({
                    opacity: 1
                }, 200), clearInterval(a.checkVisible))
            }, 500)))
        },
        wrapItems: function () {
            var a = this;
            a.$userItems.wrapAll('<div class="owl-wrapper">').wrap('<div class="owl-item"></div>'), a.$elem.find(".owl-wrapper").wrap('<div class="owl-wrapper-outer">'), a.wrapperOuter = a.$elem.find(".owl-wrapper-outer"), a.$elem.css("display", "block")
        },
        baseClass: function () {
            var a = this,
                b = a.$elem.hasClass(a.options.baseClass),
                c = a.$elem.hasClass(a.options.theme);
            b || a.$elem.addClass(a.options.baseClass), c || a.$elem.addClass(a.options.theme)
        },
        updateItems: function () {
            var b = this;
            if (b.options.responsive === !1) return !1;
            if (b.options.singleItem === !0) return b.options.items = b.orignalItems = 1, b.options.itemsCustom = !1, b.options.itemsDesktop = !1, b.options.itemsDesktopSmall = !1, b.options.itemsTablet = !1, b.options.itemsTabletSmall = !1, b.options.itemsMobile = !1, !1;
            var c = a(b.options.responsiveBaseWidth).width();
            if (c > (b.options.itemsDesktop[0] || b.orignalItems) && (b.options.items = b.orignalItems), "undefined" != typeof b.options.itemsCustom && b.options.itemsCustom !== !1) {
                b.options.itemsCustom.sort(function (a, b) {
                    return a[0] - b[0]
                });
                for (var d in b.options.itemsCustom) "undefined" != typeof b.options.itemsCustom[d] && b.options.itemsCustom[d][0] <= c && (b.options.items = b.options.itemsCustom[d][1])
            } else c <= b.options.itemsDesktop[0] && b.options.itemsDesktop !== !1 && (b.options.items = b.options.itemsDesktop[1]), c <= b.options.itemsDesktopSmall[0] && b.options.itemsDesktopSmall !== !1 && (b.options.items = b.options.itemsDesktopSmall[1]), c <= b.options.itemsTablet[0] && b.options.itemsTablet !== !1 && (b.options.items = b.options.itemsTablet[1]), c <= b.options.itemsTabletSmall[0] && b.options.itemsTabletSmall !== !1 && (b.options.items = b.options.itemsTabletSmall[1]), c <= b.options.itemsMobile[0] && b.options.itemsMobile !== !1 && (b.options.items = b.options.itemsMobile[1]);
            b.options.items > b.itemsAmount && b.options.itemsScaleUp === !0 && (b.options.items = b.itemsAmount)
        },
        response: function () {
            var c, d = this;
            if (d.options.responsive !== !0) return !1;
            var e = a(b).width();
            d.resizer = function () {
                a(b).width() !== e && (d.options.autoPlay !== !1 && clearInterval(d.autoPlayInterval), clearTimeout(c), c = setTimeout(function () {
                    e = a(b).width(), d.updateVars()
                }, d.options.responsiveRefreshRate))
            }, a(b).resize(d.resizer)
        },
        updatePosition: function () {
            var a = this;
            a.jumpTo(a.currentItem), a.options.autoPlay !== !1 && a.checkAp()
        },
        appendItemsSizes: function () {
            var b = this,
                c = 0,
                d = b.itemsAmount - b.options.items;
            b.$owlItems.each(function (e) {
                var f = a(this);
                f.css({
                    width: b.itemWidth
                }).data("owl-item", Number(e)), (e % b.options.items === 0 || e === d) && (e > d || (c += 1)), f.data("owl-roundPages", c)
            })
        },
        appendWrapperSizes: function () {
            var a = this,
                b = 0,
                b = a.$owlItems.length * a.itemWidth;
            a.$owlWrapper.css({
                width: 2 * b,
                left: 0
            }), a.appendItemsSizes()
        },
        calculateAll: function () {
            var a = this;
            a.calculateWidth(), a.appendWrapperSizes(), a.loops(), a.max()
        },
        calculateWidth: function () {
            var a = this;
            a.itemWidth = Math.round(a.$elem.width() / a.options.items)
        },
        max: function () {
            var a = this,
                b = -1 * (a.itemsAmount * a.itemWidth - a.options.items * a.itemWidth);
            return a.options.items > a.itemsAmount ? (a.maximumItem = 0, b = 0, a.maximumPixels = 0) : (a.maximumItem = a.itemsAmount - a.options.items, a.maximumPixels = b), b
        },
        min: function () {
            return 0
        },
        loops: function () {
            var b = this;
            b.positionsInArray = [0], b.pagesInArray = [];
            for (var c = 0, d = 0, e = 0; e < b.itemsAmount; e++)
                if (d += b.itemWidth, b.positionsInArray.push(-d), b.options.scrollPerPage === !0) {
                    var f = a(b.$owlItems[e]),
                        g = f.data("owl-roundPages");
                    g !== c && (b.pagesInArray[c] = b.positionsInArray[e], c = g)
                }
        },
        buildControls: function () {
            var b = this;
            (b.options.navigation === !0 || b.options.pagination === !0) && (b.owlControls = a('<div class="owl-controls"/>').toggleClass("clickable", !b.browser.isTouch).appendTo(b.$elem)), b.options.pagination === !0 && b.buildPagination(), b.options.navigation === !0 && b.buildButtons()
        },
        buildButtons: function () {
            var b = this,
                c = a('<div class="owl-buttons"/>');
            b.owlControls.append(c), b.buttonPrev = a("<div/>", {
                "class": "owl-prev",
                html: b.options.navigationText[0] || ""
            }), b.buttonNext = a("<div/>", {
                "class": "owl-next",
                html: b.options.navigationText[1] || ""
            }), c.append(b.buttonPrev).append(b.buttonNext), c.on("touchstart.owlControls mousedown.owlControls", 'div[class^="owl"]', function (a) {
                a.preventDefault()
            }), c.on("touchend.owlControls mouseup.owlControls", 'div[class^="owl"]', function (c) {
                c.preventDefault(), a(this).hasClass("owl-next") ? b.next() : b.prev()
            })
        },
        buildPagination: function () {
            var b = this;
            b.paginationWrapper = a('<div class="owl-pagination"/>'), b.owlControls.append(b.paginationWrapper), b.paginationWrapper.on("touchend.owlControls mouseup.owlControls", ".owl-page", function (c) {
                c.preventDefault(), Number(a(this).data("owl-page")) !== b.currentItem && b.goTo(Number(a(this).data("owl-page")), !0)
            })
        },
        updatePagination: function () {
            var b = this;
            if (b.options.pagination === !1) return !1;
            b.paginationWrapper.html("");
            for (var c = 0, d = b.itemsAmount - b.itemsAmount % b.options.items, e = 0; e < b.itemsAmount; e++)
                if (e % b.options.items === 0) {
                    if (c += 1, d === e) var f = b.itemsAmount - b.options.items;
                    var g = a("<div/>", {
                        "class": "owl-page"
                    }),
                        h = a("<span></span>", {
                            text: b.options.paginationNumbers === !0 ? c : "",
                            "class": b.options.paginationNumbers === !0 ? "owl-numbers" : ""
                        });
                    g.append(h), g.data("owl-page", d === e ? f : e), g.data("owl-roundPages", c), b.paginationWrapper.append(g)
                }
            b.checkPagination()
        },
        checkPagination: function () {
            var b = this;
            return b.options.pagination === !1 ? !1 : void b.paginationWrapper.find(".owl-page").each(function () {
                a(this).data("owl-roundPages") === a(b.$owlItems[b.currentItem]).data("owl-roundPages") && (b.paginationWrapper.find(".owl-page").removeClass("active"), a(this).addClass("active"))
            })
        },
        checkNavigation: function () {
            var a = this;
            return a.options.navigation === !1 ? !1 : void(a.options.rewindNav === !1 && (0 === a.currentItem && 0 === a.maximumItem ? (a.buttonPrev.addClass("disabled"), a.buttonNext.addClass("disabled")) : 0 === a.currentItem && 0 !== a.maximumItem ? (a.buttonPrev.addClass("disabled"), a.buttonNext.removeClass("disabled")) : a.currentItem === a.maximumItem ? (a.buttonPrev.removeClass("disabled"), a.buttonNext.addClass("disabled")) : 0 !== a.currentItem && a.currentItem !== a.maximumItem && (a.buttonPrev.removeClass("disabled"), a.buttonNext.removeClass("disabled"))))
        },
        updateControls: function () {
            var a = this;
            a.updatePagination(), a.checkNavigation(), a.owlControls && (a.options.items >= a.itemsAmount ? a.owlControls.hide() : a.owlControls.show())
        },
        destroyControls: function () {
            var a = this;
            a.owlControls && a.owlControls.remove()
        },
        next: function (a) {
            var b = this;
            if (b.isTransition) return !1;
            if (b.currentItem += b.options.scrollPerPage === !0 ? b.options.items : 1, b.currentItem > b.maximumItem + (1 == b.options.scrollPerPage ? b.options.items - 1 : 0)) {
                if (b.options.cycle === !0) return b.$owlItems.last().after(b.$owlItems.first()), b.$owlItems = b.$elem.find(".owl-item"), b.currentItem -= 2, b.transformCycle(b.maximumItem - 1), void setTimeout(function () {
                    b.swapSpeed("slideSpeed"), b.next()
                }, 5);
                if (b.options.rewindNav !== !0) return b.currentItem = b.maximumItem, !1;
                b.currentItem = 0, a = "rewind"
            }
            b.goTo(b.currentItem, a)
        },
        prev: function (a) {
            var b = this;
            if (b.isTransition) return !1;
            if (b.options.scrollPerPage === !0 && b.currentItem > 0 && b.currentItem < b.options.items ? b.currentItem = 0 : b.currentItem -= b.options.scrollPerPage === !0 ? b.options.items : 1, b.currentItem < 0) {
                if (b.options.cycle === !0) return b.$owlItems.first().before(b.$owlItems.last()), b.$owlItems = b.$elem.find(".owl-item"), b.currentItem += 2, b.transformCycle(1), void setTimeout(function () {
                    b.swapSpeed("slideSpeed"), b.prev()
                }, 5);
                if (b.options.rewindNav !== !0) return b.currentItem = 0, !1;
                b.currentItem = b.maximumItem, a = "rewind"
            }
            b.goTo(b.currentItem, a)
        },
        transformCycle: function (a) {
            var b = this,
                c = b.positionsInArray[a];
            b.browser.support3d === !0 ? (b.swapSpeed(0), b.transition3d(c)) : b.css2slide(c, 0)
        },
        goTo: function (a, b, c) {
            var d = this;
            if (d.isTransition) return !1;
            if ("function" == typeof d.options.beforeMove && d.options.beforeMove.apply(this, [d.$elem]), a >= d.maximumItem ? a = d.maximumItem : 0 >= a && (a = 0), d.currentItem = d.owl.currentItem = a, d.options.transitionStyle !== !1 && "drag" !== c && 1 === d.options.items && d.browser.support3d === !0) return d.swapSpeed(0), d.browser.support3d === !0 ? d.transition3d(d.positionsInArray[a]) : d.css2slide(d.positionsInArray[a], 1), d.afterGo(), d.singleItemTransition(), !1;
            var e = d.positionsInArray[a];
            d.browser.support3d === !0 ? (d.isCss3Finish = !1, b === !0 ? (d.swapSpeed("paginationSpeed"), setTimeout(function () {
                d.isCss3Finish = !0
            }, d.options.paginationSpeed)) : "rewind" === b ? (d.swapSpeed(d.options.rewindSpeed), setTimeout(function () {
                d.isCss3Finish = !0
            }, d.options.rewindSpeed)) : (d.swapSpeed("slideSpeed"), setTimeout(function () {
                d.isCss3Finish = !0
            }, d.options.slideSpeed)), d.transition3d(e)) : b === !0 ? d.css2slide(e, d.options.paginationSpeed) : "rewind" === b ? d.css2slide(e, d.options.rewindSpeed) : d.css2slide(e, d.options.slideSpeed), d.afterGo()
        },
        jumpTo: function (a) {
            var b = this;
            "function" == typeof b.options.beforeMove && b.options.beforeMove.apply(this, [b.$elem]), a >= b.maximumItem || -1 === a ? a = b.maximumItem : 0 >= a && (a = 0), b.swapSpeed(0), b.browser.support3d === !0 ? b.transition3d(b.positionsInArray[a]) : b.css2slide(b.positionsInArray[a], 1), b.currentItem = b.owl.currentItem = a, b.afterGo()
        },
        afterGo: function () {
            var a = this;
            a.prevArr.push(a.currentItem), a.prevItem = a.owl.prevItem = a.prevArr[a.prevArr.length - 2], a.prevArr.shift(0), a.prevItem !== a.currentItem && (a.checkPagination(), a.checkNavigation(), a.eachMoveUpdate(), a.options.autoPlay !== !1 && a.checkAp()), "function" != typeof a.options.afterMove || a.prevItem === a.currentItem && !a.options.cycle || a.options.afterMove.apply(this, [a.$elem])
        },
        stop: function () {
            var a = this;
            a.apStatus = "stop", clearInterval(a.autoPlayInterval)
        },
        checkAp: function () {
            var a = this;
            "stop" !== a.apStatus && a.play()
        },
        play: function () {
            var a = this;
            return a.apStatus = "play", a.options.autoPlay === !1 ? !1 : (clearInterval(a.autoPlayInterval), void(a.autoPlayInterval = setInterval(function () {
                a.next(!0)
            }, a.options.autoPlay)))
        },
        swapSpeed: function (a) {
            var b = this;
            "slideSpeed" === a ? b.$owlWrapper.css(b.addCssSpeed(b.options.slideSpeed)) : "paginationSpeed" === a ? b.$owlWrapper.css(b.addCssSpeed(b.options.paginationSpeed)) : "string" != typeof a && b.$owlWrapper.css(b.addCssSpeed(a))
        },
        addCssSpeed: function (a) {
            return {
                "-webkit-transition": "all " + a + "ms ease",
                "-moz-transition": "all " + a + "ms ease",
                "-o-transition": "all " + a + "ms ease",
                transition: "all " + a + "ms ease"
            }
        },
        removeTransition: function () {
            return {
                "-webkit-transition": "",
                "-moz-transition": "",
                "-o-transition": "",
                transition: ""
            }
        },
        doTranslate: function (a) {
            return {
                "-webkit-transform": "translate3d(" + a + "px, 0px, 0px)",
                "-moz-transform": "translate3d(" + a + "px, 0px, 0px)",
                "-o-transform": "translate3d(" + a + "px, 0px, 0px)",
                "-ms-transform": "translate3d(" + a + "px, 0px, 0px)",
                transform: "translate3d(" + a + "px, 0px,0px)"
            }
        },
        transition3d: function (a) {
            var b = this;
            b.$owlWrapper.css(b.doTranslate(a))
        },
        css2move: function (a) {
            var b = this;
            b.$owlWrapper.css({
                left: a
            })
        },
        css2slide: function (a, b) {
            var c = this;
            c.isCssFinish = !1, c.$owlWrapper.stop(!0, !0).animate({
                left: a
            }, {
                duration: b || c.options.slideSpeed,
                complete: function () {
                    c.isCssFinish = !0
                }
            })
        },
        checkBrowser: function () {
            var a = this,
                d = "translate3d(0px, 0px, 0px)",
                e = c.createElement("div");
            e.style.cssText = "  -moz-transform:" + d + "; -ms-transform:" + d + "; -o-transform:" + d + "; -webkit-transform:" + d + "; transform:" + d;
            var f = /translate3d\(0px, 0px, 0px\)/g,
                g = e.style.cssText.match(f),
                h = null !== g && 1 === g.length,
                i = "ontouchstart" in b || navigator.msMaxTouchPoints;
            a.browser = {
                support3d: h,
                isTouch: i
            }
        },
        moveEvents: function () {
            var a = this;
            (a.options.mouseDrag !== !1 || a.options.touchDrag !== !1) && (a.gestures(), a.disabledEvents())
        },
        eventTypes: function () {
            var a = this,
                b = ["s", "e", "x"];
            a.ev_types = {}, a.options.mouseDrag === !0 && a.options.touchDrag === !0 ? b = ["touchstart.owl mousedown.owl", "touchmove.owl mousemove.owl", "touchend.owl touchcancel.owl mouseup.owl"] : a.options.mouseDrag === !1 && a.options.touchDrag === !0 ? b = ["touchstart.owl", "touchmove.owl", "touchend.owl touchcancel.owl"] : a.options.mouseDrag === !0 && a.options.touchDrag === !1 && (b = ["mousedown.owl", "mousemove.owl", "mouseup.owl"]), a.ev_types.start = b[0], a.ev_types.move = b[1], a.ev_types.end = b[2]
        },
        disabledEvents: function () {
            var b = this;
            b.$elem.on("dragstart.owl", function (a) {
                a.preventDefault()
            }), b.$elem.on("mousedown.disableTextSelect", function (b) {
                return a(b.target).is("input, textarea, select, option")
            })
        },
        gestures: function () {
            function e(a) {
                return a.touches ? {
                    x: a.touches[0].pageX,
                    y: a.touches[0].pageY
                } : a.pageX !== d ? {
                    x: a.pageX,
                    y: a.pageY
                } : {
                    x: a.clientX,
                    y: a.clientY
                }
            }

            function f(b) {
                "on" === b ? (a(c).on(k.ev_types.move, h), a(c).on(k.ev_types.end, i)) : "off" === b && (a(c).off(k.ev_types.move), a(c).off(k.ev_types.end))
            }

            function g(c) {
                var c = c.originalEvent || c || b.event;
                if (3 === c.which) return !1;
                if (!(k.itemsAmount <= k.options.items)) {
                    if (k.isCssFinish === !1 && !k.options.dragBeforeAnimFinish) return !1;
                    if (k.isCss3Finish === !1 && !k.options.dragBeforeAnimFinish) return !1;
                    k.options.autoPlay !== !1 && clearInterval(k.autoPlayInterval), k.browser.isTouch === !0 || k.$owlWrapper.hasClass("grabbing") || k.$owlWrapper.addClass("grabbing"), k.newPosX = 0, k.newRelativeX = 0, a(this).css(k.removeTransition());
                    var d = a(this).position();
                    l.relativePos = d.left, l.offsetX = e(c).x - d.left, l.offsetY = e(c).y - d.top, l.cyclePosition = 0, f("on"), l.sliding = !1, l.targetElement = c.target || c.srcElement
                }
            }

            function h(d) {
                var d = d.originalEvent || d || b.event;
                k.newPosX = e(d).x - l.offsetX, k.newPosY = e(d).y - l.offsetY, k.newRelativeX = k.newPosX - l.relativePos, "function" == typeof k.options.startDragging && l.dragging !== !0 && 0 !== k.newRelativeX && (l.dragging = !0, k.options.startDragging.apply(k, [k.$elem])), (k.newRelativeX > 8 || k.newRelativeX < -8 && k.browser.isTouch === !0) && (d.preventDefault ? d.preventDefault() : d.returnValue = !1, l.sliding = !0), (k.newPosY > 10 || k.newPosY < -10) && l.sliding === !1 && a(c).off("touchmove.owl");
                var f = function () {
                    return k.newRelativeX / 5
                }, g = function () {
                        return k.maximumPixels + k.newRelativeX / 5
                    };
                k.options.cycle ? j(k.newPosX) : (k.newPosX = Math.max(Math.min(k.newPosX, f()), g()), k.browser.support3d === !0 ? k.transition3d(k.newPosX) : k.css2move(k.newPosX))
            }

            function i(c) {
                var c = c.originalEvent || c || b.event;
                if (c.target = c.target || c.srcElement, l.dragging = !1, k.browser.isTouch !== !0 && k.$owlWrapper.removeClass("grabbing"), k.dragDirection = k.owl.dragDirection = k.newRelativeX < 0 ? "left" : "right", 0 !== k.newRelativeX) {
                    var d = k.getNewPosition();
                    if (k.goTo(d, !1, "drag"), l.targetElement === c.target && k.browser.isTouch !== !0) {
                        a(c.target).on("click.disable", function (b) {
                            b.stopImmediatePropagation(), b.stopPropagation(), b.preventDefault(), a(c.target).off("click.disable")
                        });
                        var e = a._data(c.target, "events").click,
                            g = e.pop();
                        e.splice(0, 0, g)
                    }
                }
                f("off")
            }

            function j(a) {
                var b = l.cyclePosition * k.itemWidth;
                if (a > 0) var c = a + k.itemWidth - b;
                else var c = a - k.itemWidth - b; if (c >= 0)(a >= b || l.cyclePosition < 0) && (k.$owlItems.first().before(k.$owlItems.last()), k.$owlItems = k.$elem.find(".owl-item"), l.cyclePosition++);
                else {
                    var d = b + k.max();
                    (Math.abs(a) >= Math.abs(d) || l.cyclePosition > 0) && (k.$owlItems.last().after(k.$owlItems.first()), k.$owlItems = k.$elem.find(".owl-item"), l.cyclePosition--)
                }
                a -= l.cyclePosition * k.itemWidth, k.browser.support3d === !0 ? k.transition3d(a) : k.css2move(a)
            }
            var k = this,
                l = {
                    offsetX: 0,
                    offsetY: 0,
                    baseElWidth: 0,
                    relativePos: 0,
                    position: null,
                    minSwipe: null,
                    maxSwipe: null,
                    sliding: null,
                    dargging: null,
                    targetElement: null
                };
            k.isCssFinish = !0, k.$elem.on(k.ev_types.start, ".owl-wrapper", g)
        },
        getNewPosition: function () {
            var a, b = this;
            return a = b.closestItem(), a > b.maximumItem ? (b.currentItem = b.maximumItem, a = b.maximumItem) : b.newPosX >= 0 && (a = 0, b.currentItem = 0), a
        },
        closestItem: function () {
            var b = this,
                c = b.options.scrollPerPage === !0 ? b.pagesInArray : b.positionsInArray,
                d = b.newPosX,
                e = null;
            return a.each(c, function (f, g) {
                d - b.itemWidth / 20 > c[f + 1] && d - b.itemWidth / 20 < g && "left" === b.moveDirection() ? (e = g, b.currentItem = b.options.scrollPerPage === !0 ? a.inArray(e, b.positionsInArray) : f) : d + b.itemWidth / 20 < g && d + b.itemWidth / 20 > (c[f + 1] || c[f] - b.itemWidth) && "right" === b.moveDirection() && (b.options.scrollPerPage === !0 ? (e = c[f + 1] || c[c.length - 1], b.currentItem = a.inArray(e, b.positionsInArray)) : (e = c[f + 1], b.currentItem = f + 1))
            }), b.currentItem
        },
        moveDirection: function () {
            var a, b = this;
            return b.newRelativeX < 0 ? (a = "right", b.playDirection = "next") : (a = "left", b.playDirection = "prev"), a
        },
        customEvents: function () {
            var a = this;
            a.$elem.on("owl.next", function () {
                a.next()
            }), a.$elem.on("owl.prev", function () {
                a.prev()
            }), a.$elem.on("owl.play", function (b, c) {
                a.options.autoPlay = c, a.play(), a.hoverStatus = "play"
            }), a.$elem.on("owl.stop", function () {
                a.stop(), a.hoverStatus = "stop"
            }), a.$elem.on("owl.goTo", function (b, c) {
                a.goTo(c)
            }), a.$elem.on("owl.jumpTo", function (b, c) {
                a.jumpTo(c)
            })
        },
        stopOnHover: function () {
            var a = this;
            a.options.stopOnHover === !0 && a.browser.isTouch !== !0 && a.options.autoPlay !== !1 && (a.$elem.on("mouseover", function () {
                a.stop()
            }), a.$elem.on("mouseout", function () {
                "stop" !== a.hoverStatus && a.play()
            }))
        },
        lazyLoad: function () {
            var b = this;
            if (b.options.lazyLoad === !1) return !1;
            for (var c = 0; c < b.itemsAmount; c++) {
                var e = a(b.$owlItems[c]);
                if ("loaded" !== e.data("owl-loaded")) {
                    var f, g = e.data("owl-item"),
                        h = e.find(".lazyOwl");
                    "string" == typeof h.data("src") ? (e.data("owl-loaded") === d && (h.hide(), e.addClass("loading").data("owl-loaded", "checked")), f = b.options.lazyFollow === !0 ? g >= b.currentItem : !0, f && g < b.currentItem + b.options.items && h.length && b.lazyPreload(e, h)) : e.data("owl-loaded", "loaded")
                }
            }
        },
        lazyPreload: function (a, b) {
            function c() {
                f += 1, e.completeImg(b.get(0)) || g === !0 ? d() : 100 >= f ? setTimeout(c, 100) : d()
            }

            function d() {
                a.data("owl-loaded", "loaded").removeClass("loading"), b.removeAttr("data-src"), "fade" === e.options.lazyEffect ? b.fadeIn(400) : b.show(), "function" == typeof e.options.afterLazyLoad && e.options.afterLazyLoad.apply(this, [e.$elem])
            }
            var e = this,
                f = 0;
            if ("DIV" === b.prop("tagName")) {
                b.css("background-image", "url(" + b.data("src") + ")");
                var g = !0
            } else b[0].src = b.data("src");
            c()
        },
        autoHeight: function () {
            function b() {
                g += 1, e.completeImg(f.get(0)) ? c() : 100 >= g ? setTimeout(b, 100) : e.wrapperOuter.css("height", "")
            }

            function c() {
                var b = a(e.$owlItems[e.currentItem]).height();
                e.wrapperOuter.css("height", b + "px"), e.wrapperOuter.hasClass("autoHeight") || setTimeout(function () {
                    e.wrapperOuter.addClass("autoHeight")
                }, 0)
            }
            var e = this,
                f = a(e.$owlItems[e.currentItem]).find("img");
            if (f.get(0) !== d) {
                var g = 0;
                b()
            } else c()
        },
        completeImg: function (a) {
            return a.complete ? "undefined" != typeof a.naturalWidth && 0 == a.naturalWidth ? !1 : !0 : !1
        },
        onVisibleItems: function () {
            var b = this;
            b.options.addClassActive === !0 && b.$owlItems.removeClass("active"), b.visibleItems = [];
            for (var c = b.currentItem; c < b.currentItem + b.options.items; c++) b.visibleItems.push(c), b.options.addClassActive === !0 && a(b.$owlItems[c]).addClass("active");
            b.owl.visibleItems = b.visibleItems
        },
        transitionTypes: function (a) {
            var b = this;
            b.outClass = "owl-" + a + "-out", b.inClass = "owl-" + a + "-in"
        },
        singleItemTransition: function () {
            function a(a) {
                return {
                    position: "relative",
                    left: a + "px"
                }
            }
            var b = this;
            b.isTransition = !0;
            var c = b.outClass,
                d = b.inClass,
                e = b.$owlItems.eq(b.currentItem),
                f = b.$owlItems.eq(b.prevItem),
                g = Math.abs(b.positionsInArray[b.currentItem]) + b.positionsInArray[b.prevItem],
                h = Math.abs(b.positionsInArray[b.currentItem]) + b.itemWidth / 2;
            b.$owlWrapper.addClass("owl-origin").css({
                "-webkit-transform-origin": h + "px",
                "-moz-perspective-origin": h + "px",
                "perspective-origin": h + "px"
            });
            var i = "webkitAnimationEnd oAnimationEnd MSAnimationEnd animationend";
            f.css(a(g, 10)).addClass(c).on(i, function () {
                b.endPrev = !0, f.off(i), b.clearTransStyle(f, c)
            }), e.addClass(d).on(i, function () {
                b.endCurrent = !0, e.off(i), b.clearTransStyle(e, d)
            })
        },
        clearTransStyle: function (a, b) {
            var c = this;
            a.css({
                position: "",
                left: ""
            }).removeClass(b), c.endPrev && c.endCurrent && (c.$owlWrapper.removeClass("owl-origin"), c.endPrev = !1, c.endCurrent = !1, c.isTransition = !1)
        },
        owlStatus: function () {
            var a = this;
            a.owl = {
                userOptions: a.userOptions,
                baseElement: a.$elem,
                userItems: a.$userItems,
                owlItems: a.$owlItems,
                currentItem: a.currentItem,
                prevItem: a.prevItem,
                visibleItems: a.visibleItems,
                isTouch: a.browser.isTouch,
                browser: a.browser,
                dragDirection: a.dragDirection
            }
        },
        clearEvents: function () {
            var d = this;
            d.$elem.off(".owl owl mousedown.disableTextSelect"), a(c).off(".owl owl"), a(b).off("resize", d.resizer)
        },
        unWrap: function () {
            var a = this;
            0 !== a.$elem.children().length && (a.$owlWrapper.unwrap(), a.$userItems.unwrap().unwrap(), a.owlControls && a.owlControls.remove()), a.clearEvents(), a.$elem.attr("style", a.$elem.data("owl-originalStyles") || "").attr("class", a.$elem.data("owl-originalClasses"))
        },
        destroy: function () {
            var a = this;
            a.stop(), clearInterval(a.checkVisible), a.unWrap(), a.$elem.removeData()
        },
        reinit: function (b) {
            var c = this,
                d = a.extend({}, c.userOptions, b);
            c.unWrap(), c.init(d, c.$elem)
        },
        addItem: function (a, b) {
            var c, e = this;
            return a ? 0 === e.$elem.children().length ? (e.$elem.append(a), e.setVars(), !1) : (e.unWrap(), c = b === d || -1 === b ? -1 : b, c >= e.$userItems.length || -1 === c ? e.$userItems.eq(-1).after(a) : e.$userItems.eq(c).before(a), void e.setVars()) : !1
        },
        removeItem: function (a) {
            var b, c = this;
            return 0 === c.$elem.children().length ? !1 : (b = a === d || -1 === a ? -1 : a, c.unWrap(), c.$userItems.eq(b).remove(), void c.setVars())
        }
    };
    a.fn.owlCarousel = function (b) {
        return this.each(function () {
            if (a(this).data("owl-init") === !0) return !1;
            a(this).data("owl-init", !0);
            var c = Object.create(e);
            c.init(b, this), a.data(this, "owlCarousel", c)
        })
    }, a.fn.owlCarousel.options = {
        items: 5,
        itemsCustom: !1,
        itemsDesktop: [1199, 4],
        itemsDesktopSmall: [979, 3],
        itemsTablet: [768, 2],
        itemsTabletSmall: !1,
        itemsMobile: [479, 1],
        singleItem: !1,
        itemsScaleUp: !1,
        slideSpeed: 200,
        paginationSpeed: 800,
        rewindSpeed: 1e3,
        autoPlay: !1,
        stopOnHover: !1,
        navigation: !1,
        navigationText: ["prev", "next"],
        rewindNav: !0,
        scrollPerPage: !1,
        pagination: !0,
        paginationNumbers: !1,
        cycle: !1,
        responsive: !0,
        responsiveRefreshRate: 200,
        responsiveBaseWidth: b,
        baseClass: "owl-carousel",
        theme: "owl-theme",
        lazyLoad: !1,
        lazyFollow: !0,
        lazyEffect: "fade",
        autoHeight: !1,
        jsonPath: !1,
        jsonSuccess: !1,
        dragBeforeAnimFinish: !0,
        mouseDrag: !0,
        touchDrag: !0,
        addClassActive: !1,
        transitionStyle: !1,
        beforeUpdate: !1,
        afterUpdate: !1,
        beforeInit: !1,
        afterInit: !1,
        beforeMove: !1,
        afterMove: !1,
        afterAction: !1,
        startDragging: !1,
        afterLazyLoad: !1
    }
}(jQuery, window, document), Modernizr.addTest("csspointerevents", function () {
    var a, b = document.createElement("x"),
        c = document.documentElement,
        d = window.getComputedStyle;
    return "pointerEvents" in b.style ? (b.style.pointerEvents = "auto", b.style.pointerEvents = "x", c.appendChild(b), a = d && "auto" === d(b, "").pointerEvents, c.removeChild(b), !! a) : !1
}),
function () {
    var a, b, c, d, e, f;
    a = jQuery, e = a(".home .features"), e.append('<div class="gutter">'), e.masonry({
        itemSelector: ".widget",
        gutter: ".gutter",
        transitionDuration: 0
    }), b = a(".home .banner"), d = b.find(".item"), c = null, f = function () {
        var b;
        return b = a(window).width(), d.each(function () {
            var d, e, f, g, h, i, j;
            return g = a(this), e = g.data("content-width"), d = c / e, f = g.find("img"), i = f.attr("height") / f.attr("width"), g.height(e > b ? b * d : c), h = g.height(), j = h / i, f.height(h), f.width(h / i), f.css("left", -1 * (j - b) / 2 + "px")
        })
    }, b.owlCarousel({
        navigation: !0,
        slideSpeed: 300,
        paginationSpeed: 400,
        singleItem: !0,
        navigation: !1,
        afterInit: function () {
            return c = b.height(), f()
        },
        autoPlay: 1e4,
        stopOnHover: !0
    }), b.find(".item").on("click", function () {
        return b.trigger("owl.next")
    }), a(window).on("resize", f)
}.call(this),
function () {
    var a, b, c, d, e, f, g, h, i, j, k, l, m, n, o;
    if (a = jQuery, b = a("form.eloqua-form"), b.length) {
        for (g = b.parent().find("[id^=more-]"), g.parent("p").length && (g = g.parent()), c = a("<div>"), c.addClass("gated"), c.hide(), g.nextUntil(b).detach().appendTo(c), g.after(c), k = location.search.substring(1), j = {}, o = k.split("&"), m = 0, n = o.length; n > m; m++) i = o[m], h = i.split("="), 2 === h.length && (j[h[0]] = h[1]);
        b.find(":input[data-qsarg]").each(function (b, c) {
            var d;
            return c = a(c), d = c.data("qsarg"), d in j ? c.val(j[d]) : void 0
        }), b.on("submit", function () {
            var b;
            return b = a(this), "checkValidity" in this && !this.checkValidity() ? !1 : l(!0)
        }), l = function (a) {
            var d, e;
            return b.slideUp(), c.slideDown(), a && (d = c.find("a"), e = c.find("embed, iframe"), 1 === d.length && 0 === e.length) ? window.open(d.prop("href"), "_blank") : void 0
        }, f = 0, window.SetElqContent = function () {
            switch (f) {
            case 0:
                e();
                console.log("case 0: ")
                break;
            case 1:
                console.log("case 1: ")
                d()
                
            }
            return f++
        }, e = function () {
            var a, b;
            //elqVEmail = GetElqContentPersonalizationValue("V_ElqEmailAddress");
            localStorage.setItem("elqVEmail", GetElqContentPersonalizationValue("V_ElqEmailAddress")); if (localStorage.getItem("elqProg2") == null) {localStorage.setItem("elqProg2", "false");}           
            console.log("case e: " + GetElqContentPersonalizationValue("V_ElqEmailAddress"))
            return a = GetElqContentPersonalizationValue("V_ElqEmailAddress"), b = "<C_EmailAddress>" + a + "</C_EmailAddress>", _elqQ.push(["elqDataLookup", "b25edf2517d04bea9ecdc4866011e11e", b])
        }, d = function () {
            var c, d, e;
            return "function" == typeof GetElqCustomerGUID && (e = b.find(":input[name=elqCustomerGUID]"), e.val(GetElqCustomerGUID)), c = ":input:visible:not([type=submit])", d = b.find(c), d.each(function (b, c) {
                var d, e;
                return c = a(c), d = c.data("api-name"), (e = GetElqContentPersonalizationValue(d)) ? "emailAddress" === c.prop("name") ? c.val(e) : c.remove() : void 0
            }), b.find(c).length <= 1 ? l(!1) : void 0
        }, _elqQ.push(["elqDataLookup", "b518cf2c082d458d86faa124f333c9f2", ""]), _elqQ.push(["elqGetCustomerGUID"])
    }
}.call(this),
function () {
    var a, b, c, d, e;
    a = jQuery, b = a(".industries"), c = b.find("dt"), c.next("dd").hide(), e = Math.floor(Math.random() * c.length), d = c.slice(e, e + 1), d.addClass("active"), d.next("dd").show(), c.find("a").click(function () {
        var b, d;
        return b = a(this), d = b.parent(), d.hasClass("active") || (c.removeClass("active"), c.next("dd").slideUp("fast"), d.addClass("active"), d.next("dd").slideDown("fast")), !1
    })
}.call(this),
function () {
    var a, b, c, d, e;
    a = jQuery, b = a(".banner img"), b.length && (d = a("body.home").length > 0, c = b.width(), e = function () {
        var e;
        if (!d) return e = a(window).width(), 1e3 > e && (e = 1e3), b.each(function () {
            var b;
            return b = a(this), b.css("left", -1 * (c - e) / 2 + "px")
        })
    }, a(window).on("resize", e), e())
}.call(this),
function () {
    var a, b;
    a = jQuery, b = a(".country select"), b.on("change", function () {
        var b, c;
        return b = a(this), c = b.val(), c ? window.location = c : void 0
    })
}.call(this),
function () {
    var a, b, c, d, e, f, g;
    a = jQuery, c = a(".resource-filter:not(.inline) select"), e = {}, d = function () {
        var b, d;
        return c.each(function () {
            var b, c, d;
            return b = a(this), d = b.prop("name"), c = b.val(), c ? e[d] = c : void 0
        }), b = location.pathname.replace(/page\/\d+\//, ""), (d = a.param(e)) && (b += "?" + d), location.href = b
    }, c.on("change", d), f = a(".resources ~ .pagination a"), g = a(".resources.more"), f.on("click", function () {
        return g.length ? (f.parents(".pagination").hide(), g.slideDown(), !1) : void 0
    }), b = a(".resource-filter.inline select"), b.on("change", function () {
        var c, d;
        return d = b.val(), a(".pagination").hide(), a(".resources.more").show(), c = a(".resource"), d ? (c.hide(), c.filter("." + d).show()) : c.show()
    })
}.call(this),
function () {
    var a, b, c;
    a = jQuery, c = a('.contact select[name="subject"]'), b = a('.contact select[name="customer_service"]'), b.hide(), c.on("change", function () {
        return "Customer Service" === c.val() ? b.show() : (b.val(""), b.hide())
    }), a(".contact").on("submit", function () {
        var b;
        return b = a(this), b.after("<p>Thank you for your submission.</p>").slideDown(), b.slideUp()
    })
}.call(this),
function () {
    var a, b, c, d;
    a = jQuery, c = a(".menu-button"), d = a(".container"), b = a("body > nav"), c.on("click", function () {
        return d.toggleClass("nav-open"), b.toggleClass("active")
    })
}.call(this),
function () {
    var a, b, c;
    a = jQuery, c = a(".search"), b = c.find("input"), b.on("focus", function () {
        return c.addClass("open")
    }), b.on("blur", function () {
        return c.removeClass("open")
    })
}.call(this),
function () {
    var a, b;
    Modernizr.csspointerevents || (a = jQuery, b = a(".dropdown"), b.on("click", function (b) {
        var c, d;
        return c = a(this), b.offsetX < c.width() - 30 ? !0 : (d = c.find("select"), d.one("blur", function () {
            return a(this).attr("size", 1)
        }), d.attr("size", d.find("option").length), d.focus(), !1)
    }))
}.call(this);
$(document).ready(function () {
    $(".btn-info").click(function (e) {
       // e.preventDefault();
        console.log("click download");
        $("#elqForm").submit();
    });

    $(".eventon_list_event .no_events").parent().parent().prev().hide();
    $(".eventon_list_event .no_events").parent().parent().hide();

    if (querystring('sbj') == "customerservice") {
        $('#formsubject').prop('selectedIndex', 1);
        $("#formcustomer").show();
    }

});
function querystring(key) {
   var re=new RegExp('(?:\\?|&)'+key+'=(.*?)(?=&|$)','gi');
   var r=[], m;
   while ((m=re.exec(document.location.search)) != null) r.push(m[1]);
   return r;
}