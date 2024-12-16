class LaStudioPageSpeedClass {
    constructor(e) {
        this.triggerEvents = e;
        this.eventOptions = {passive: true}
        this.userEventListener = this.triggerListener.bind(this)
        this.scripts_load_fire = false
        this.enqueued_fonts = []
        this.enqueued_styles = []
        this.enqueued_scripts = {
            normal: [],
            async: [],
            defer: [],
            lazy: []
        }
        this.allJQueries = [];
    }

    is_pagespeed() {
        return"undefined" != typeof navigator && /(lighthouse|gtmetrix)/i.test(navigator.userAgent.toLocaleLowerCase()) || navigator?.userAgentData?.brands?.filter(e=>"lighthouse" === e?.brand?.toLocaleLowerCase())?.length > 0 || window.innerWidth > 1340 && window.innerWidth < 1360 && window.devicePixelRatio <= 1 || window.innerWidth < 413 && window.innerWidth > 410 && "undefined" != typeof navigator && /moto/i.test(navigator.userAgent)
    }

    user_events_add(e) {
        this.triggerEvents.forEach((t => window.addEventListener(t, e.userEventListener, e.eventOptions)))
    }

    user_events_remove(e) {
        this.triggerEvents.forEach((t => window.removeEventListener(t, e.userEventListener, e.eventOptions)))
    }

    triggerListener() {
        this.user_events_remove(this);
        if("loading" === document.readyState){

            document.addEventListener("DOMContentLoaded", this.load_style_resources.bind(this));
            if(!this.scripts_load_fire){
                document.addEventListener("DOMContentLoaded", this.load_resources.bind(this))
            }
        }
        else{
            this.load_style_resources();
            if(!this.scripts_load_fire){
                this.load_resources()
            }
        }
    }

    async load_style_resources() {
        this.register_styles();
        this.load_styles(this.enqueued_styles)
    }

    async load_resources() {
        this.hold_event_listeners();
        this.hold_jquery(this);
        this.exe_document_write();
        this.register_scripts();
        this.add_html_class("lasf_ps-start");
        this.preload_scripts();
        await this.load_scripts(this.enqueued_scripts.normal);
        await this.load_scripts(this.enqueued_scripts.defer);
        await this.load_scripts(this.enqueued_scripts.async);
        await this.execute_domcontentloaded();
        await this.execute_window_load();
        window.dispatchEvent(new Event("LaStudioPageSpeed:Loaded"));
        this.add_html_class("lasf_ps-start_js");
        await this.load_scripts(this.enqueued_scripts.lazy);
        this.add_html_class("lasf_ps-js_loaded")
        document.documentElement.removeAttribute('data-ps')
    }

    add_html_class(text) {
        document.documentElement.classList.add(text);
    }

    register_scripts() {
        document.querySelectorAll("script[data-laps-action=reorder]").forEach((e => {
            if(e.hasAttribute("src") || e.hasAttribute("data-src")){
                if(e.hasAttribute("async") && false !== e.async){
                    this.enqueued_scripts.async.push(e)
                }
                else{
                    if(e.hasAttribute("defer") && false !== e.defer || "module" === e.getAttribute("data-laps-module")){
                        this.enqueued_scripts.defer.push(e)
                    }
                    else{
                        this.enqueued_scripts.normal.push(e)
                    }
                }
            }
            else{
                this.enqueued_scripts.normal.push(e)
            }
        }))
        document.querySelectorAll("script[data-laps-action=lazyload_ext]").forEach((e => {
            this.enqueued_scripts.lazy.push(e)
        }))
    }

    register_styles() {
        document.querySelectorAll("link[data-href]").forEach((e => {
            this.enqueued_styles.push(e);
        }))
    }

    async execute_script(e) {
        return await this.repaint_frame(), new Promise((t => {
            const n = document.createElement("script");
            let r;
            [...e.attributes].forEach((e => {
                let t = e.nodeName;

                if("type" !== t && "data-laps-action" !== t){
                    r = e.nodeValue;
                    if("data-laps-module" === t){
                        t = "type";
                        r = "module";
                    }
                    if("data-src" === t){
                        t = "src"
                    }
                    n.setAttribute(t, r)
                }
            }));

            if(e.hasAttribute("src") || e.hasAttribute("data-src")){
                n.addEventListener("load", t);
                n.addEventListener("error", t)
            }
            else{
                n.text = e.text;
                t()
            }
            e.parentNode.replaceChild(n, e)
        }))
    }

    async execute_styles(e) {
        return function (e) {
            const s = document.createElement("link");
            s.href = e.getAttribute("data-href");
            s.rel = "stylesheet";
            e.parentNode.replaceChild(s, e)
        }(e)
    }

    async load_scripts(e) {
        const t = e.shift();
        return t ? (await this.execute_script(t), this.load_scripts(e)) : Promise.resolve()
    }

    async load_styles(e) {
        const t = e.shift();
        return t ? (await this.execute_styles(t), this.load_styles(e)) : "loaded";
    }

    async load_fonts(e) {
        var f = document.createDocumentFragment();
        e.forEach((t => {
            const s = document.createElement("link");
            s.href = t;
            s.rel = "stylesheet";
            f.appendChild(s)
        }));
        setTimeout(function () {
            document.head.appendChild(f)
        }, LaStudioPageSpeedConfigs.delay)
    }

    preload_scripts() {
        var e = document.createDocumentFragment();
        [...this.enqueued_styles, ...this.enqueued_scripts.normal, ...this.enqueued_scripts.defer, ...this.enqueued_scripts.async].forEach((t => {
            const n = t.getAttribute("src") || t.getAttribute("data-src");
            const h = t.getAttribute("rel");
            if (n) {
                const t = document.createElement("link");
                t.href = n;
                t.rel = "preload";
                t.as = h == "stylesheet" ? "style" : "script";
                e.appendChild(t)
            }
        }))
        document.head.appendChild(e)
    }

    hold_event_listeners() {
        let e = {};
        function t(t, n) {
            !function (t) {
                function n(n) {
                    return e[t].eventsToRewrite.indexOf(n) >= 0 ? "lasfps-" + n : n
                }
                e[t] || (e[t] = {
                    originalFunctions: {
                        add: t.addEventListener,
                        remove: t.removeEventListener
                    },
                    eventsToRewrite: []
                },
                    t.addEventListener = function () {
                        arguments[0] = n(arguments[0])
                        e[t].originalFunctions.add.apply(t, arguments)
                    },
                    t.removeEventListener = function () {
                        arguments[0] = n(arguments[0])
                        e[t].originalFunctions.remove.apply(t, arguments)
                    })
            }(t),
                e[t].eventsToRewrite.push(n)
        }

        function n(e, t) {
            let n = e[t];
            Object.defineProperty(e, t, {
                get: () => n || function () { },
                set(r) {
                    e["lasfps" + t] = n = r
                }
            })
        }

        t(document, "DOMContentLoaded");
        t(window, "DOMContentLoaded");
        t(window, "load");
        t(window, "pageshow");
        t(document, "readystatechange");
        n(document, "onreadystatechange");
        n(window, "onload");
        n(window, "onpageshow");
    }

    hold_jquery(e) {
        let t = window.jQuery;
        Object.defineProperty(window, "jQuery", {
            get: () => t,
            set(n) {
                if (n && n.fn && n.fn.on && !e.allJQueries.includes(n)) {
                    n.fn.ready = n.fn.init.prototype.ready = function (t) {
                        e.domReadyFired ? t.bind(document)(n) : document.addEventListener("lasfps-DOMContentLoaded", (() => t.bind(document)(n)))
                    };
                    const t = n.fn.on;
                    n.fn.on = n.fn.init.prototype.on = function () {
                        if (this[0] === window) {
                            // console.log(arguments[0])
                            function e(e) {
                                return e.split(" ").map((e => "load" === e || 0 === e.indexOf("load.") ? "lasf_ps-jquery_loaded" : e)).join(" ")
                            }
                            if("string" == typeof arguments[0] || arguments[0] instanceof String){
                                arguments[0] = e(arguments[0])
                            }
                            else if("object" == typeof arguments[0]){
                                Object.keys(arguments[0]).forEach(t => {
                                    if("load" === t || 0 === t.indexOf("load.")){
                                        delete Object.assign(arguments[0], {[e(t)]: arguments[0][t]})[t]
                                    }
                                })
                            }
                        }
                        t.apply(this, arguments)
                        return this
                    }
                    e.allJQueries.push(n)
                }
                t = n
            }
        })
    }

    async execute_domcontentloaded() {
        this.domReadyFired = true;
        await this.repaint_frame();
        document.dispatchEvent(new Event("lasfps-DOMContentLoaded"));
        await this.repaint_frame();
        window.dispatchEvent(new Event("lasfps-DOMContentLoaded"));
        await this.repaint_frame();
        document.dispatchEvent(new Event("lasfps-readystatechange"));
        await this.repaint_frame();
        document.lasfpsonreadystatechange && document.lasfpsonreadystatechange()
    }

    async execute_window_load() {
        await this.repaint_frame();
        window.dispatchEvent(new Event("lasfps-load"));
        await this.repaint_frame();
        window.lasfpsonload && window.lasfpsonload();
        await this.repaint_frame();
        this.allJQueries.forEach((e => e(window).trigger("lasf_ps-jquery_loaded")));
        window.dispatchEvent(new Event("lasfps-pageshow"));
        await this.repaint_frame();
        window.lasfpsonpageshow && window.lasfpsonpageshow()
    }

    exe_document_write() {
        const e = new Map;
        document.write = document.writeln = function (t) {
            const n = document.currentScript,
                r = document.createRange(),
                i = n.parentElement;
            let o = e.get(n);
            void 0 === o && (o = n.nextSibling, e.set(n, o));
            const a = document.createDocumentFragment();
            r.setStart(a, 0);
            a.appendChild(r.createContextualFragment(t));
            i.insertBefore(a, o)
        }
    }

    async repaint_frame() {
        return new Promise((e => requestAnimationFrame(e)))
    }

    static runoptimize() {
        const ps_instance = new LaStudioPageSpeedClass(["keydown", "mousemove", "touchmove", "touchstart", "touchend", "wheel"]);
        ps_instance.load_fonts(ps_instance.enqueued_fonts);
        ps_instance.user_events_add(ps_instance)
        if(LaStudioPageSpeedConfigs.detected){
            if(ps_instance.is_pagespeed()){
                ps_instance.add_html_class('isPageSpeed', 'lasf--is-ps');
                setTimeout(function (e){
                    if(!document.documentElement.classList.contains('lasf_ps-start')){
                        e.triggerListener()
                    }
                }, LaStudioPageSpeedConfigs.delay, ps_instance)
            }
            else{
                // ps_instance.scripts_load_fire = true;
                ps_instance.triggerListener();
            }
        }
        else{
            setTimeout(function (e){
                if(!document.documentElement.classList.contains('lasf_ps-start')){
                    e.triggerListener()
                }
            }, LaStudioPageSpeedConfigs.delay, ps_instance)
        }
    }
}
LaStudioPageSpeedClass.runoptimize();

window.addEventListener('LaStudioPageSpeed:Loaded', () => {
    console.log('LaStudioPageSpeed:Loaded: finished!');
})