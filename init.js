jQuery.cachedScript = function (url, options) {
    // Allow user to set any option except for dataType, cache, and url
    options = $.extend(options || {}, {
        dataType: "script",
        cache: true,
        url: url
    });

    // Use $.ajax() since it is more flexible than $.getScript
    // Return the jqXHR object so we can chain callbacks
    return jQuery.ajax(options);
};

$(document).ready(function () {
    // load the highlight.min.js script this way to avoid Uncaught SyntaxError: unterminated regular expression literal
    $.cachedScript('https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/highlight.min.js')
        .done(function (script, textStatus) {

            hljs.configure({
                cssSelector: 'pre.code2highlight, code.code2highlight, pre.mwcode'
            });

            hljs.highlightAll();
        });

});
