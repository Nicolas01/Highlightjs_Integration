<?php

class HighlightjsIntegration {

    public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin) {
        $out->addModules('ext.HighlightjsIntegration');
        return true;
    }

    public static function onParserFirstCallInit(Parser &$parser) {
        global $wgHighlightTags;

        foreach ($wgHighlightTags as $tag) {
            // $parser->setHook( tag, array( class, method ) );
            $parser->setHook($tag, array('HighlightjsIntegration', 'renderSyntaxhighlight'));
		}

		return true;
    }

    public static function renderSyntaxhighlight($in, $param = array(), $parser = null, $frame = false) {
        global $wgLangMapping;
        // Replace strip markers (For e.g. {{#tag:syntaxhighlight|<nowiki>...}})
		$out = $parser->mStripState->unstripNoWiki( $in );

		// Don't trim leading spaces away, just the linefeeds
		$out = preg_replace( '/^\n+/', '', rtrim( $out ) );

        // Convert deprecated attributes
		if ( isset( $param['enclose'] ) ) {
			if ( $param['enclose'] === 'none' ) {
				$param['inline'] = true;
			}
			unset( $param['enclose'] );
		}

        // get the language
        //<syntaxhighlight lang="bash">
        //</syntaxhighlight>
        $lang = isset($param['lang']) ? $param['lang'] : '';
        // map lang if necessary
        if (array_key_exists($lang, $wgLangMapping)) {
            $lang = $wgLangMapping[$lang];
        }

        // Allow certain HTML attributes
		$htmlAttribs = Sanitizer::validateAttributes(
			$param, array_flip( [ 'line', 'start', 'highlight', 'style', 'class', 'id', 'dir' ] )
		);

        // class
        $highlightClass = 'code2highlight';
        $htmlAttribs['class'] = isset($htmlAttribs['class']) ? $htmlAttribs['class'] . ' ' . $highlightClass : $highlightClass;
        if (!empty($lang)) {
            $htmlAttribs['class'] .= " lang-$lang";
        }

        if ( !( isset( $htmlAttribs['dir'] ) && $htmlAttribs['dir'] === 'rtl' ) ) {
			$htmlAttribs['dir'] = 'ltr';
		}

        $out = htmlspecialchars(trim($out));

        // inline ?
        //<syntaxhighlight lang="bash" inline></syntaxhighlight>
        $inline = isset($param['inline']);

        if ($inline) {
            // Enforce inlineness. Stray newlines may result in unexpected list and paragraph processing
			// (also known as doBlockLevels()).
			$out = str_replace( "\n", ' ', $out );
            $htmlAttribs['style'] = isset($htmlAttribs['style']) ? 'display: inline;' . $htmlAttribs['style'] : 'display: inline;';
            $out = Html::rawElement('code', $htmlAttribs, $out);
        }
        else {
            // Use 'nowiki' strip marker to prevent list processing (also known as doBlockLevels()).
			// However, leave the wrapping <pre/> outside to prevent <p/>-wrapping.
			$marker = $parser::MARKER_PREFIX . '-highlightjsinner-' .
            sprintf( '%08X', $parser->mMarkerIndex++ ) . $parser::MARKER_SUFFIX;
            $parser->mStripState->addNoWiki( $marker, $out );
            $out = Html::openElement( 'pre', $htmlAttribs ) .
				$marker .
				Html::closeElement( 'pre' );
        }
        return $out;
    }

}
