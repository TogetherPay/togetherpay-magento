var Splitoff = Splitoff || function () {
    var i = {};
    var n = 'https://dev.splitoff.io/pay/develop/cart.html';
    var a = 'Splitoff';
    var e;
    var t;
    var o;
    var r = "";
    var s = "";
    var d = ["US", "CA", "GB", "AU", "NZ"];
    var l;
    var c;
    var f;
    var p;
    var m;
    var u = 300;
    var y;

    function b() {
        var e = document.createElement("style");
        var t = "             @-webkit-keyframes buy-rotate {                 from { -webkit-transform: rotate(0deg); }                 to { -webkit-transform: rotate(359deg); }             }             @-moz-keyframes buy-rotate {                 from { -moz-transform: rotate(0deg); }                 to { -moz-transform: rotate(359deg); }             }             @-o-keyframes buy-rotate {                 from { -o-transform: rotate(0deg); }                 to { -o-transform: rotate(359deg); }             }             @keyframes buy-rotate {                 from { transform: rotate(0deg); }                 to { transform: rotate(359deg); }             }             .buy-loader {                 display: none;                 position: relative;                 width: 100%;                 height: 100%;                 overflow: hidden;             }             .buy-loader:after {                 content: '';                 position: absolute;                 margin: auto;                 left: 0;                 right: 0;                 top: 0;                 bottom: 0;                 width: 100px;                 height: 100px;                 border-left: 15px solid #CCCCCC;                 border-right: 15px solid #CCCCCC;                 border-bottom: 15px solid #CCCCCC;                 border-top: 15px solid #25659F;                 border-radius: 100%;                 -webkit-animation: buy-rotate 1s infinite linear;                 -moz-animation: buy-rotate 1s infinite linear;                 -o-animation: buy-rotate 1s infinite linear;                 animation: buy-rotate 1s infinite linear;             }             .buy-backdrop {                 display: none;                 position: fixed !important;                 top: 0 !important;                 left: 0 !important;                 width: 100% !important;                 height: 100% !important;                 background-color: rgba(0, 0, 0, 0.8) !important;                 padding: 0 !important;                 margin: 0 !important;                 -webkit-overflow-scrolling: touch !important;                 overflow: auto !important;                 z-index: 99999 !important;                 zoom: 1 !important;             }             .buy-popup-message {                 display: none !important;                 position: fixed !important;                 top: 50% !important;                 left: 50% !important;                 transform: translate(-50%, -50%) !important;                 margin-top: 100px !important;                 z-index: 100000 !important;                 color: white;                 font-family: 'HelveticaNeue-Light', 'Helvetica Neue Light', 'Helvetica Neue', Helvetica, Arial, 'Lucida Grande', sans-serif !important;                 font-weight: bold !important;                 text-align: center !important;                 max-width: 230px !important;                 cursor: pointer !important;             }             .buy-container-closer {                 display: none;                 position: fixed !important;                 top: 0 !important;                 right: 0 !important;                 z-index: 100000 !important;                 color: #999999 !important;                 cursor: pointer !important;                 padding: 20px !important;             }             .buy-container-closer:hover {                 color: #FFFFFF !important;             }             .buy-container-closer:after {                 content: '×' !important;                 font-size: 40px !important;                 line-height: 20px !important;             }         ";
        e.type = "text/css";
        if (e.styleSheet) {
            e.styleSheet.cssText = t
        } else {
            e.appendChild(document.createTextNode(t))
        }
        document.head.appendChild(e)
    }

    function C() {
        var e = c.document.createElement("style");
        var t = "             @-webkit-keyframes buy-rotate {                 from { -webkit-transform: rotate(0deg); }                 to { -webkit-transform: rotate(359deg); }             }             @-moz-keyframes buy-rotate {                 from { -moz-transform: rotate(0deg); }                 to { -moz-transform: rotate(359deg); }             }             @-o-keyframes buy-rotate {                 from { -o-transform: rotate(0deg); }                 to { -o-transform: rotate(359deg); }             }             @keyframes buy-rotate {                 from { transform: rotate(0deg); }                 to { transform: rotate(359deg); }             }             .buy-loader {                 display: block;                 position: relative;                 width: 100%;                 height: 100%;                 overflow: hidden;             }             .buy-loader:after {                 content: '';                 position: absolute;                 margin: auto;                 left: 0;                 right: 0;                 top: 0;                 bottom: 0;                 width: 100px;                 height: 100px;                 border-left: 15px solid #CCCCCC;                 border-right: 15px solid #CCCCCC;                 border-bottom: 15px solid #CCCCCC;                 border-top: 15px solid #25659F;                 border-radius: 100%;                 -webkit-animation: buy-rotate 1s infinite linear;                 -moz-animation: buy-rotate 1s infinite linear;                 -o-animation: buy-rotate 1s infinite linear;                 animation: buy-rotate 1s infinite linear;             }         ";
        e.type = "text/css";
        if (e.styleSheet) {
            e.styleSheet.cssText = t
        } else {
            e.appendChild(c.document.createTextNode(t))
        }
        c.document.head.appendChild(e)
    }

    function h() {
        if (!e) {
            e = document.createElement("div");
            e.className = "buy-backdrop";
            t = document.createElement("div");
            t.className = "buy-container-closer";
            o = document.createElement("div");
            o.className = "buy-loader";
            p = document.createElement("div");
            p.className = "buy-popup-message";
            p.textContent = "Click here if you can't see the " + a + " window.";
            e.appendChild(o);
            document.body.appendChild(e);
            document.body.appendChild(p);
            document.body.appendChild(t)
        }
    }

    function v() {
        if (c) {
            m = c.document.createElement("div");
            m.className = "buy-loader";
            c.document.body.appendChild(m)
        }
    }

    function g() {
        var e = 420;
        var t = 750;
        var o = l.outerHeight / 2 + l.screenY - t / 2;
        var n = l.outerWidth / 2 + l.screenX - e / 2;
        var r = ["top=" + o, "left=" + n, "width=" + e, "height=" + t, "scrollbars=yes", "status=yes", "resizable=yes"];
        c = l.open("", a, r.join());
        if (c) {
            C();
            v();
            l.addEventListener("beforeunload", i.close);
            l.addEventListener("message", w)
        }
    }

    function w(e) {
        if (e.origin === n) {
            y = true;
            if (typeof i.onComplete === "function") {
                i.onComplete({data: JSON.parse(e.data)})
            } else {
                console.warn(a + ": 'onComplete' handler not defined.")
            }
        } else {
            console.warn(a + ": " + e.origin + " is an untrusted origin for message. Should be: " + n)
        }
    }

    function x() {
        if (e) {
            e.style.setProperty("display", "block", "important")
        }
    }

    function k() {
        if (e) {
            e.style.setProperty("display", "none", "important")
        }
    }

    function z() {
        if (o) {
            o.style.setProperty("display", "block", "important")
        }
    }

    function E() {
        if (o) {
            o.style.setProperty("display", "none", "important")
        }
    }

    function N() {
        if (p) {
            p.style.setProperty("display", "block", "important")
        }
    }

    function L() {
        if (p) {
            p.style.setProperty("display", "none", "important")
        }
    }

    function P() {
        if (t) {
            t.style.setProperty("display", "block", "important")
        }
    }

    function A() {
        if (t) {
            t.style.setProperty("display", "none", "important")
        }
    }

    function S() {
        if (y) {
            return
        }
        if (typeof i.onComplete === "function") {
            i.onComplete({data: {status: "CANCELLED"}})
        } else {
            console.warn(a + ": 'onComplete' handler not defined.")
        }
    }

    function F() {
        i.close();
        S()
    }

    function U(e, merchantId, t) {
        var o;

        if (e) {
                //o = n + "/" + s.toLowerCase() + "/checkout/?token=" + e;
                o = n + "?orderToken=" + e;
                //o += '&id=110009'
                o += '&id=' + merchantId;
                o += "&relativeCallbackUrl=" + encodeURIComponent(r);
                if (c) {
                    o += "&isWindowed=true";
                    if (!c.closed) {
                        c.location.href = o
                    }
                } else {
                    l.location.href = o
                }
        } else {
            E();
            k();
            console.error(a + ": 'token' not provided, cannot continue.")
        }
    }

    i.redirect = function (e) {
        console.log(e);
        U(e && e.token, e && e.merchantId)
    };
    i.show = function (e) {
        console.warn(a + ": 'show' is deprecated. Please use 'redirect' instead.");
        i.redirect(e)
    };
    i.display = function (e) {
        console.warn(a + ": 'display' is deprecated. Please use 'redirect' instead.");
        i.redirect(e)
    };
    i.initialize = function (e) {
        if (e && e.countryCode) {
            if (d.indexOf(e.countryCode) !== -1) {
                if (e.countryCode === "GB") {
                    e.countryCode = "UK"
                }
                s = e.countryCode;
                r = e.relativeCallbackURL || r;
                l = e.window || window;
                y = false;
                x();
                z()
            } else {
                console.error(a + ": A valid 'countryCode' wasn't provided, cannot continue. Should be one of these: " + d.join(", ") + ".")
            }
        } else {
            console.error(a + ": 'countryCode' not provided, cannot continue.")
        }
    };
    i.init = function (e) {
        console.warn(a + ": 'init' is deprecated. Please use 'initialize' instead.");
        e = e || {};
        e.countryCode = "AU";
        i.initialize(e)
    };
    i.open = function () {
        if (!l) {
            console.error(a + ": Can't open the window. Did you call 'initialize' first?");
            return
        }
        if (!c || c && c.closed) {
            g()
        } else {
            i.focus()
        }
        if (f) {
            clearInterval(f)
        }
        f = setInterval(function () {
            if (c && c.closed) {
                F()
            }
        }, u);
        N();
        P();
        if (e) {
            e.onclick = i.focus
        }
        if (p) {
            p.onclick = i.focus
        }
        if (t) {
            t.onclick = F
        }
        return c
    };
    i.focus = function () {
        if (c) {
            c.focus()
        }
    };
    i.close = function () {
        if (c) {
            c.close();
            clearInterval(f);
            l.removeEventListener("beforeunload", i.close);
            l.removeEventListener("message", w);
            L();
            A();
            E();
            k()
        }
    };
    i.transfer = function (e) {
        U(e && e.token, true)
    };
    if (document.body) {
        b();
        h()
    } else {
        document.addEventListener("DOMContentLoaded", function () {
            b();
            h()
        })
    }
    return i
}();
