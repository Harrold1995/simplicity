<div id="fbroot"></div>
<script>
    window.fbGetUrl = "<?php echo base_url();?>formbuilder/getData/<?php echo $id;?>";
    window.fbGetListUrl = "<?php echo base_url();?>formbuilder/getFields";
    window.recordset = "<?php echo $recordset;?>";
    window.recordsets = [
        { value: 'none', name: 'Select Type' },
        { value: 'lease', name: 'Lease' },
        { value: 'property', name: 'Property' }
    ];
    window.fbSaveUrl = "<?php echo base_url();?>formbuilder/saveData/<?php echo $id;?>";
    !function (l) {
        function e(e) {
            for (var r, t, n = e[0], o = e[1], u = e[2], f = 0, i = []; f < n.length; f++) t = n[f], Object.prototype.hasOwnProperty.call(p, t) && p[t] && i.push(p[t][0]), p[t] = 0;
            for (r in o) Object.prototype.hasOwnProperty.call(o, r) && (l[r] = o[r]);
            for (s && s(e); i.length;) i.shift()();
            return c.push.apply(c, u || []), a()
        }

        function a() {
            for (var e, r = 0; r < c.length; r++) {
                for (var t = c[r], n = !0, o = 1; o < t.length; o++) {
                    var u = t[o];
                    0 !== p[u] && (n = !1)
                }
                n && (c.splice(r--, 1), e = f(f.s = t[0]))
            }
            return e
        }

        var t = {}, p = {1: 0}, c = [];

        function f(e) {
            if (t[e]) return t[e].exports;
            var r = t[e] = {i: e, l: !1, exports: {}};
            return l[e].call(r.exports, r, r.exports, f), r.l = !0, r.exports
        }

        f.m = l, f.c = t, f.d = function (e, r, t) {
            f.o(e, r) || Object.defineProperty(e, r, {enumerable: !0, get: t})
        }, f.r = function (e) {
            "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {value: "Module"}), Object.defineProperty(e, "__esModule", {value: !0})
        }, f.t = function (r, e) {
            if (1 & e && (r = f(r)), 8 & e) return r;
            if (4 & e && "object" == typeof r && r && r.__esModule) return r;
            var t = Object.create(null);
            if (f.r(t), Object.defineProperty(t, "default", {
                    enumerable: !0,
                    value: r
                }), 2 & e && "string" != typeof r) for (var n in r) f.d(t, n, function (e) {
                return r[e]
            }.bind(null, n));
            return t
        }, f.n = function (e) {
            var r = e && e.__esModule ? function () {
                return e.default
            } : function () {
                return e
            };
            return f.d(r, "a", r), r
        }, f.o = function (e, r) {
            return Object.prototype.hasOwnProperty.call(e, r)
        }, f.p = "/";
        var r = this["webpackJsonpform-editor"] = this["webpackJsonpform-editor"] || [], n = r.push.bind(r);
        r.push = e, r = r.slice();
        for (var o = 0; o < r.length; o++) e(r[o]);
        var s = n;
        a()
    }([])</script>
<script src="<?php echo base_url();?>themes/default/assets/feditor/static/js/2.7ca18032.chunk.js?v="></script>
<script src="<?php echo base_url();?>themes/default/assets/feditor/static/js/main.9eb1a1d1.chunk.js"></script>
