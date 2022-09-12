$(document).ready(function () {
    // load the highlight.min.js script this way to avoid Uncaught SyntaxError: unterminated regular expression literal
    var scriptSrc = mw.config.get('wgExtensionAssetsPath') + '/Highlightjs_Integration/highlight/highlight.min.js';
    mw.loader
      .getScript(scriptSrc)
      .then(
          function () {
              hljs.configure({
                  cssSelector: 'pre.code2highlight, code.code2highlight, pre.mwcode',
              });

              hljs.highlightAll();
          },
          function (e) {
              mw.log.error(e.message, ':', scriptSrc);
          }
      );
})
