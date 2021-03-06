(function (e, t) {
    "$:nomunge";
    function L(e) {
        return typeof e === "string"
    }

    function A(e) {
        var t = r.call(arguments, 1);
        return function () {
            return e.apply(this, t.concat(r.call(arguments)))
        }
    }

    function O(e) {
        return e.replace(x, "$2")
    }

    function M(e) {
        return e.replace(/(?:^[^?#]*\?([^#]*).*$)?.*/, "$1")
    }

    function _(t, r, s, u, f) {
        var l, c, h, p, d;
        if (u !== n) {
            h = s.match(t ? x : /^([^#?]*)\??([^#]*)(#?.*)/);
            d = h[3] || "";
            if (f === 2 && L(u)) {
                c = u.replace(t ? S : E, "")
            } else {
                p = a(h[2]);
                u = L(u) ? a[t ? g : m](u) : u;
                c = f === 2 ? u : f === 1 ? e.extend({}, u, p) : e.extend({}, p, u);
                c = o(c);
                if (t) {
                    c = c.replace(T, i)
                }
            }
            l = h[1] + (t ? C : c || !h[1] ? "?" : "") + c + d
        } else {
            l = r(s !== n ? s : location.href)
        }
        return l
    }

    function D(e, t, r) {
        if (t === n || typeof t === "boolean") {
            r = t;
            t = s[e ? g : m]()
        } else {
            t = L(t) ? t.replace(e ? S : E, "") : t
        }
        return a(t, r)
    }

    function P(t, r, i, o) {
        if (!L(i) && typeof i !== "object") {
            o = i;
            i = r;
            r = n
        }
        return this.each(function () {
            var n = e(this), u = r || p()[(this.nodeName || "").toLowerCase()] || "", a = u && n.attr(u) || "";
            n.attr(u, s[t](a, i, o))
        })
    }

    var n, r = Array.prototype.slice, i = decodeURIComponent, s = e.param, o, u, a, f, l = e.bbq = e.bbq || {}, c, h, p, d = e.event.special, v = "hashchange", m = "querystring", g = "fragment", y = "elemUrlAttr", b = "href", w = "src", E = /^.*\?|#.*$/g, S, x, T, N, C, k = {};
    s[m] = A(_, 0, M);
    s[g] = u = A(_, 1, O);
    s.sorted = o = function (t, n) {
        var r = [], i = {};
        e.each(s(t, n).split("&"), function (e, t) {
            var n = t.replace(/(?:%5B|=).*$/, ""), s = i[n];
            if (!s) {
                s = i[n] = [];
                r.push(n)
            }
            s.push(t)
        });
        return e.map(r.sort(),function (e) {
            return i[e]
        }).join("&")
    };
    u.noEscape = function (t) {
        t = t || "";
        var n = e.map(t.split(""), encodeURIComponent);
        T = new RegExp(n.join("|"), "g")
    };
    u.noEscape(",/");
    u.ajaxCrawlable = function (e) {
        if (e !== n) {
            if (e) {
                S = /^.*(?:#!|#)/;
                x = /^([^#]*)(?:#!|#)?(.*)$/;
                C = "#!"
            } else {
                S = /^.*#/;
                x = /^([^#]*)#?(.*)$/;
                C = "#"
            }
            N = !!e
        }
        return N
    };
    u.ajaxCrawlable(0);
    e.deparam = a = function (t, r) {
        var s = {}, o = {"true": !0, "false": !1, "null": null};
        e.each(t.replace(/\+/g, " ").split("&"), function (t, u) {
            var a = u.split("="), f = i(a[0]), l, c = s, h = 0, p = f.split("]["), d = p.length - 1;
            if (/\[/.test(p[0]) && /\]$/.test(p[d])) {
                p[d] = p[d].replace(/\]$/, "");
                p = p.shift().split("[").concat(p);
                d = p.length - 1
            } else {
                d = 0
            }
            if (a.length === 2) {
                l = i(a[1]);
                if (r) {
                    l = l && !isNaN(l) ? +l : l === "undefined" ? n : o[l] !== n ? o[l] : l
                }
                if (d) {
                    for (; h <= d; h++) {
                        f = p[h] === "" ? c.length : p[h];
                        c = c[f] = h < d ? c[f] || (p[h + 1] && isNaN(p[h + 1]) ? {} : []) : l
                    }
                } else {
                    if (e.isArray(s[f])) {
                        s[f].push(l)
                    } else if (s[f] !== n) {
                        s[f] = [s[f], l]
                    } else {
                        s[f] = l
                    }
                }
            } else if (f) {
                s[f] = r ? n : ""
            }
        });
        return s
    };
    a[m] = A(D, 0);
    a[g] = f = A(D, 1);
    e[y] || (e[y] = function (t) {
        return e.extend(k, t)
    })({a: b, base: b, iframe: w, img: w, input: w, form: "action", link: b, script: w});
    p = e[y];
    e.fn[m] = A(P, m);
    e.fn[g] = A(P, g);
    l.pushState = c = function (e, t) {
        if (L(e) && /^#/.test(e) && t === n) {
            t = 2
        }
        var r = e !== n, i = u(location.href, r ? e : {}, r ? t : 2);
        location.href = i
    };
    l.getState = h = function (e, t) {
        return e === n || typeof e === "boolean" ? f(e) : f(t)[e]
    };
    l.removeState = function (t) {
        var r = {};
        if (t !== n) {
            r = h();
            e.each(e.isArray(t) ? t : arguments, function (e, t) {
                delete r[t]
            })
        }
        c(r, 2)
    };
    d[v] = e.extend(d[v], {add: function (t) {
        function i(e) {
            var t = e[g] = u();
            e.getState = function (e, r) {
                return e === n || typeof e === "boolean" ? a(t, e) : a(t, r)[e]
            };
            r.apply(this, arguments)
        }

        var r;
        if (e.isFunction(t)) {
            r = t;
            return i
        } else {
            r = t.handler;
            t.handler = i
        }
    }})
})(jQuery, this);
(function (e, t, n) {
    "$:nomunge";
    function f(e) {
        e = e || location.href;
        return"#" + e.replace(/^[^#]*#?(.*)$/, "$1")
    }

    var r = "hashchange", i = document, s, o = e.event.special, u = i.documentMode, a = "on" + r in t && (u === n || u > 7);
    e.fn[r] = function (e) {
        return e ? this.bind(r, e) : this.trigger(r)
    };
    e.fn[r].delay = 50;
    o[r] = e.extend(o[r], {setup: function () {
        if (a) {
            return false
        }
        e(s.start)
    }, teardown: function () {
        if (a) {
            return false
        }
        e(s.stop)
    }});
    s = function () {
        function p() {
            var n = f(), i = h(u);
            if (n !== u) {
                c(u = n, i);
                e(t).trigger(r)
            } else if (i !== u) {
                location.href = location.href.replace(/#.*/, "") + i
            }
            o = setTimeout(p, e.fn[r].delay)
        }

        var s = {}, o, u = f(), l = function (e) {
            return e
        }, c = l, h = l;
        s.start = function () {
            o || p()
        };
        s.stop = function () {
            o && clearTimeout(o);
            o = n
        };
        navigator.userAgent.match(/MSIE/i) !== null && !a && function () {
            var t, n;
            s.start = function () {
                if (!t) {
                    n = e.fn[r].src;
                    n = n && n + f();
                    t = e('<iframe tabindex="-1" title="empty"/>').hide().one("load",function () {
                        n || c(f());
                        p()
                    }).attr("src", n || "javascript:0").insertAfter("body")[0].contentWindow;
                    i.onpropertychange = function () {
                        try {
                            if (event.propertyName === "title") {
                                t.document.title = i.title
                            }
                        } catch (e) {
                        }
                    }
                }
            };
            s.stop = l;
            h = function () {
                return f(t.location.href)
            };
            c = function (n, s) {
                var o = t.document, u = e.fn[r].domain;
                if (n !== s) {
                    o.title = i.title;
                    o.open();
                    u && o.write('<script>document.domain="' + u + '"</script>');
                    o.close();
                    t.location.hash = n
                }
            }
        }();
        return s
    }()
})(jQuery, this)