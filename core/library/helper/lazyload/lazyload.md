# LazyLoad

`http://wonko.com/post/lazyload-200-released`

After quite a while without updates, I’ve finally released version 2.0.0 of LazyLoad.

LazyLoad is a tiny (only 1,541 bytes minified), dependency-free JavaScript library that makes it super easy to load external JavaScript and (new in this version) CSS files on demand. It’s ideal for quickly and unobtrusively loading large external scripts and stylesheets either lazily after the rest of the page has finished loading or on demand as needed.

In addition to CSS support, this version of LazyLoad also adds support for parallel loading of multiple resources in browsers that support it. To load multiple resources in parallel, simply pass an array of URLs in a single LazyLoad call.

### Downloads

- lazyload.js (full source) (`https://github.com/rgrove/lazyload/raw/release-2.0.1/lazyload.js`)
- lazyload-min.js (minified source) (`https://github.com/rgrove/lazyload/raw/release-2.0.1/lazyload-min.js`)
- Archive: tgz | zip

### Usage

Using LazyLoad is simple. Just call the appropriate method — css() to load CSS, js() to load JavaScript — and pass in a URL or array of URLs to load. You can also provide a callback function if you’d like to be notified when the resources have finished loading, as well as an argument to pass to the callback and a scope in which to execute the callback.

```javascript
// Load a single JavaScript file and execute a callback when it finishes loading.
LazyLoad.js('http://example.com/foo.js', function () {
  alert('foo.js has been loaded');
});

// Load multiple JS files and execute a callback when they've all finished.
LazyLoad.js(['foo.js', 'bar.js', 'baz.js'], function () {
  alert('all files have been loaded');
});

// Load a CSS file and pass an argument to the callback function.
LazyLoad.css('foo.css', function (arg) {
  alert(arg);
}, 'foo.css has been loaded');

// Load a CSS file and execute the callback in a different scope.
LazyLoad.css('foo.css', function () {
  alert(this.foo); // displays 'bar'
}, null, {foo: 'bar'});
```

### Supported Browsers

- Firefox 2+
- Google Chrome (all versions)
- Internet Explorer 6+
- Opera 9+
- Safari 3+
- Mobile Safari (all versions)

### Caveats

All browsers support parallel loading of CSS. However, only Firefox and Opera currently support parallel script loading while preserving execution order. To ensure that scripts are always executed in the correct order, LazyLoad will load all scripts sequentially in browsers other than Firefox and Opera. Hopefully other browsers will improve their parallel script loading behavior soon.

Sadly, Firefox, Safari, and Google Chrome don’t provide any indication when a CSS file has finished loading. In these browsers, CSS load callbacks will execute after a short delay, but there’s no way to automatically guarantee that the CSS has finished loading before the callback is executed. Luckily, there’s a fairly painless [manual workaround](http://wonko.com/post/how-to-prevent-yui-get-race-conditions) that you can use to detect when CSS has finished loading, but it’s not possible for LazyLoad to do it for you.
