<?php

class HighlightjsIntegration
{
    public static function onBeforePageDisplay(OutputPage &$out, Skin &$skin)
    {
        $out->addModules('ext.HighlightjsIntegration');
        return true;
    }

    public static function onParserFirstCallInit(Parser &$parser)
    {
        global $wgHighlightTags;

        foreach ($wgHighlightTags as $tag)
        {
            // $parser->setHook( tag, array( class, method ) );
            $parser->setHook($tag, array(
                'HighlightjsIntegration',
                'renderSyntaxhighlight'
            ));
        }

        return true;
    }

    public static function renderSyntaxhighlight($in, $param = array(), $parser = null, $frame = false)
    {
        global $wgLangMapping;

        // get the language
        // <syntaxhighlight lang="bash">...</syntaxhighlight>
        $lang = $param['lang'];

        // map lang if necessary
        if (array_key_exists($lang, $wgLangMapping))
        {
            $lang = $wgLangMapping[$lang];
        }

        // Set allowed HTML attributes
        $htmlAttributes = Sanitizer::validateAttributes( $param, [ 'class', 'id', 'style' ] );

        // class
        if (array_key_exists('class', $htmlAttributes)) {
            $htmlAttributes['class'] .= ' code2highlight';
        }
        else {
            $htmlAttributes['class'] = 'code2highlight';
        }

        if ($lang)
        {
            $htmlAttributes['class'] .= " lang-$lang";
        }

        // inline
        //<syntaxhighlight lang="bash" inline>...</syntaxhighlight>
        if (isset($param['inline']))
        {
            $htmlAttributes['style'] .= 'display:inline;';
            $tag = 'code';
        }
        else
        {
            $tag = 'pre';
        }

        // Replace strip markers (For e.g. {{#tag:syntaxhighlight|<nowiki>...}})
        $out = $parser->getStripState()->unstripNoWiki( $in );
        $out = htmlspecialchars( rtrim( $out ) );

        // Use 'nowiki' strip marker to prevent list processing (also known as doBlockLevels())
        $marker = $parser::MARKER_PREFIX
            . '-highlightjsinner-'
            . sprintf( '%08X', $parser->mMarkerIndex++ )
            . $parser::MARKER_SUFFIX;
        $parser->getStripState()->addNoWiki( $marker, $out );
        // However, leave the wrapping <pre/> outside to prevent <p/>-wrapping
        $out = Html::openElement( $tag, $htmlAttributes ) . $marker . Html::closeElement( $tag );

        return $out;
    }
}
