var loadedUrls = [];

LazyLoad = (function (win, doc) {
    var env,           /* User agent and feature test information. */
        head,          /* Reference to the <head> element (populated lazily). */
        pending = {},  /* Requests currently in progress, if any. */
        /* Number of times we've polled to check whether a pending stylesheet has
         * finished loading in WebKit. If this gets too high, we're probably stalled. */
        pollCount = 0,
        queue = {css: [], js: []},     /* Queued requests. */
        styleSheets = doc.styleSheets; /* Reference to the browser's list of stylesheets. */
    /* Creates and returns an HTML element with the specified name and attributes.
     * @method createNode
     * @param {String} name element name
     * @param {Object} attrs name/value mapping of element attributes
     * @return {HTMLElement}
     * @private */
    function createNode(name, attrs) {
        var node = doc.createElement(name), attr;
        for (attr in attrs) {
            if (attrs.hasOwnProperty(attr)) {
                node.setAttribute(attr, attrs[attr]);
            }
        }
        return node;
    }

    /* Called when the current pending resource of the specified type has finished
     * loading. Executes the associated callback (if any) and loads the next
     * resource in the queue.
     * @method finish
     * @param {String} type resource type ('css' or 'js')
     * @private */
    function finish(type) {
        var p = pending[type],
            callback,
            urls;

        if (p) {
            callback = p.callback;
            urls     = p.urls;
            urls.shift();
            pollCount = 0;
            /* If this is the last of the pending URLs, execute the callback and
             * start the next request in the queue (if any). */
            if (!urls.length) {
                if (callback) {
                    callback.call(p.context, p.obj);
                }
                pending[type] = null;
                if (queue[type].length) {
                    load(type);
                }
            }
        }
    }

    /* Populates the <code>env</code> variable with user agent and feature test
     * information.
     * @method getEnv
     * @private */
    function getEnv() {
        /* No need to run again if already populated. */
        if (env) {
            return;
        }
        var ua = navigator.userAgent;
        env = {
            /* True if this browser supports disabling async mode on dynamically
             * created script nodes. See
             * wiki.whatwg.org/wiki/Dynamic_Script_Execution_Order */
            async: doc.createElement('script').async === true
        };
        (env.webkit = /AppleWebKit\//.test(ua))
        || (env.ie = /MSIE/.test(ua))
        || (env.opera = /Opera/.test(ua))
        || (env.gecko = /Gecko\//.test(ua))
        || (env.unknown = true);
    }

     /* @method load
      * @param {String} type resource type ('css' or 'js')
      * @param {String|Array} urls (optional) URL or array of URLs to load
      * @param {Function} callback (optional) callback function to execute when the resource is loaded
      * @param {Object} obj (optional) object to pass to the callback function
      * @param {Object} context (optional) if provided, the callback function will
               be executed in this object's context
      * @private */
    function load(type, urls, callback, obj, context) {
        var _finish = function () {
            finish(type);
        },
        isCSS = type === 'css',
        i, len, node, p, pendingUrls, url;
        getEnv();
        if (urls) {
            urls = typeof urls === 'string' ? [urls] : urls.concat();
            if (!(isCSS || env.async || env.gecko || env.opera)) {
                /* Load sequentially. */
                for(i = 0, len = urls.length; i < len; ++i) {
                    queue[type].push({
                        urls     : [urls[i]],
                        callback : i === len - 1 ? callback : null, /* callback is only added to the last URL */
                        obj      : obj,
                        context  : context
                    });
                }
            } else {
                /* Load in parallel. */
                queue[type].push({
                    urls     : urls,
                    callback : callback,
                    obj      : obj,
                    context  : context
                });
            }
        }
        if (pending[type] || !(p = pending[type] = queue[type].shift())) {
            return;
        }
        head || (head = doc.head || doc.getElementsByTagName('head')[0]);
        pendingUrls = p.urls;
        for (i = 0, len = pendingUrls.length; i < len; ++i) {
            url = pendingUrls[i];
            if (!window.loadedUrls.includes(url)) {
                if (isCSS) {
                    node = createNode('link', {
                        charset : 'utf-8',
                        'class' : 'lazyload',
                        href    : url,
                        rel     : 'stylesheet',
                        type    : 'text/css'
                    });
                    window.loadedUrls.push(url);
                } else {
                    node = createNode('script', {
                        charset : 'utf-8',
                        'class' : 'lazyload',
                        src     : url
                    });
                    window.loadedUrls.push(url);
                    node.async = false;
                }
            } else {
                _finish();
            }
            if (env.ie) {
                node.onreadystatechange = function () {
                    var readyState = this.readyState;

                    if (readyState === 'loaded' || readyState === 'complete') {
                        this.onreadystatechange = null;
                        _finish();
                    }
                };
            } else if (isCSS && (env.gecko || env.webkit)) {
                /* Gecko and WebKit don't support the onload event on link nodes. In
                 * WebKit, we can poll for changes to document.styleSheets to figure out
                 * when stylesheets have loaded, but in Gecko we just have to finish
                 * after a brief delay and hope for the best. */
                if (env.webkit) {
                    p.urls[i] = node.href; /* resolve relative URLs (or polling won't work) */
                    poll();
                } else {
                    setTimeout(_finish, 50 * len);
                }
            } else {
                node.onload = node.onerror = _finish;
            }
            head.appendChild(node);
        }
    }

    /* Begins polling to determine when pending stylesheets have finished loading
     * in WebKit. Polling stops when all pending stylesheets have loaded.
     * @method poll
     * @private */
    function poll() {
        var css = pending.css, i;
        if (!css) {
            return;
        }
        i = styleSheets.length;

        /* Look for a stylesheet matching the pending URL. */
        while (i && --i) {
            if (styleSheets[i].href === css.urls[0]) {
                finish('css');
                break;
            }
        }
        pollCount += 1;
        if (css) {
            if (pollCount < 200) {
                setTimeout(poll, 50);
            } else {
                /* We've been polling for 10 seconds and nothing's happened, which may
                 * indicate that the stylesheet has been removed from the document
                 * before it had a chance to load. Stop polling and finish the pending
                 * request to prevent blocking further requests. */
                finish('css');
            }
        }
    }
    return {
         /* @method css
          * @param {String|Array} urls CSS URL or array of CSS URLs to load
          * @param {Function} callback (optional) callback function to
            execute when the specified stylesheets are loaded
          * @param {Object} obj (optional) object to pass to the callback function
          * @param {Object} context (optional) if provided, the callback
            function will be executed in this object's context
          * @static */
        css: function (urls, callback, obj, context) {
            load('css', urls, callback, obj, context);
        },
        /* @method js
         * @param {String|Array} urls JS URL or array of JS URLs to load
         * @param {Function} callback (optional) callback function to
           execute when the specified scripts are loaded
         * @param {Object} obj (optional) object to pass to the callback function
         * @param {Object} context (optional) if provided, the callback function
           will be executed in this object's context
           @static */
        js: function (urls, callback, obj, context) {
            load('js', urls, callback, obj, context);
        }
    };
})(this, this.document);