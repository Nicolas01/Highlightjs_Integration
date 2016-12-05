$(document).ready(function() {
    $('pre.code2highlight, code.code2highlight, pre.mw-code').each(function(i, block) {
        hljs.highlightBlock(block);
    });
});
