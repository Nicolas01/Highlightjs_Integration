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
            $.cachedScript('https://cdnjs.cloudflare.com/ajax/libs/highlightjs-line-numbers.js/2.8.0/highlightjs-line-numbers.min.js')
                .done(function (script, textStatus) {
                    $.cachedScript('https://cdn.jsdelivr.net/gh/TRSasasusu/highlightjs-highlight-lines.js@1.2.0/highlightjs-highlight-lines.min.js')
                        .done(function (script, textStatus) {
                            var highlightColor = null;
                            var getHighlightColor = function (element) {
                                // Get the default backgroud color 
                                color = window.getComputedStyle(element, null).getPropertyValue('background-color');
                                var rgb = color.substring(color.indexOf('(') + 1, color.lastIndexOf(')')).split(/,\s*/).map(Number);
                                // Calculate luminance using CCIR 601
                                var luminance = Math.round((rgb[0] * 0.299) + (rgb[1] * 0.587) + (rgb[2] * 0.114));
                                // Lighten or darken the background color based on the luminance
                                if (luminance > 125) {
                                    for (var i = 0; i < 3; ++i) {
                                        rgb[i] = Math.round(rgb[i] * 0.9);
                                    }
                                } else {
                                    for (var i = 0; i < 3; ++i) {
                                        rgb[i] = Math.min(255, rgb[i] + Math.round((256 - rgb[i]) * 0.1));
                                    }
                                }
                                // Build the color string
                                if (rgb.length > 3) {
                                    color = `rgba(${rgb[0]},${rgb[1]},${rgb[2]},${rgb[3]})`;
                                } else {
                                    color = `rgb(${rgb[0]},${rgb[1]},${rgb[2]})`;
                                }
                                return color;
                            };

                            $('pre.code2highlight, code.code2highlight, pre.mwcode').each((index, element) => {
                                // Apply highlight.js to this block
                                hljs.highlightElement(element);
                                // Add line numbers if specified
                                if (element.hasAttribute('line')) {
                                    element.removeAttribute('line');
                                    var start = 1;
                                    // Get the start line number if specified
                                    if (element.hasAttribute('start')) {
                                        start = parseInt($(element).attr('start'));
                                        element.removeAttribute('start');
                                    }
                                    hljs.lineNumbersBlock(element, {
                                        startFrom: start
                                    });
                                }
                                // Highlight lines if specified
                                if (element.hasAttribute('highlight')) {
                                    var highlightArr = $(element).attr('highlight').split(/,\s*/);
                                    element.removeAttribute('highlight');
                                    // Only calculate the highlight color once
                                    if (!highlightColor) {
                                        highlightColor = getHighlightColor(element);
                                    }
                                    var config = [];
                                    highlightArr.forEach((entry) => {
                                        if (!isNaN(entry)) {
                                            // Single line
                                            var line = parseInt(entry);
                                            config.push({
                                                start: line,
                                                end: line,
                                                color: highlightColor
                                            });
                                        } else {
                                            // Range of lines
                                            var matches = entry.match(/([0-9]+)-([0-9]+)/);
                                            if (matches && parseInt(matches[1]) < parseInt(matches[2])) {
                                                config.push({
                                                    start: parseInt(matches[1]),
                                                    end: parseInt(matches[2]),
                                                    color: highlightColor
                                                });
                                            }
                                        }
                                    });
                                    hljs.highlightLinesElement(element, config)
                                }
                            });
                        });
                });
        });
});
