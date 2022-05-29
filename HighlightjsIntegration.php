<?php

if ( function_exists( 'wfLoadExtension' ) ) {
    wfLoadExtension( 'Highlightjs_Integration' );
    return true;
} else {
    die( 'This version of the highlightjs-integration extension requires MediaWiki 1.35+' );
}
