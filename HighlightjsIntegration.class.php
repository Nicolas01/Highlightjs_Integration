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

        // get the language
        //<syntaxhighlight lang="bash">
        //</syntaxhighlight>
        $lang = isset($param['lang']) ? $param['lang'] : '';

        // map lang if necessary
        if (array_key_exists($lang, $wgLangMapping)) {
            $lang = $wgLangMapping[$lang];
        }


        // class
        $htmlAttribs['class'] = isset($param['class']) ? $param['class'] . ' code2highlight' : 'code2highlight';
        if (!empty($lang)) {
            $htmlAttribs['class'] .= " lang-$lang";
        }
        // id
        if (isset( $param['id']))
        {
            $htmlAttribs['id'] = $param['id'];
        }

        $code = htmlspecialchars(trim($in));

        // inline ?
        //<syntaxhighlight lang="bash" inline></syntaxhighlight>
        $inline = isset($param['inline']);

        if ($inline) {
            $htmlAttribs['style'] = 'display: inline;';
            $out = Html::rawElement('code', $htmlAttribs, $code);
            return $out;
        }
        else {
            $out = Html::rawElement('pre', $htmlAttribs, $code);
            return $out;
        }
    }

}
