# Description

This extension allows source code to be syntax highlighted on the wiki pages.  
This README file might be out of date, have a look at the [extension page](https://www.mediawiki.org/wiki/Extension:Highlightjs_Integration) for updated informations.


# Requirements

This version of the extension has been tested with highlight.js `11.7.0` and MediaWiki `1.39.3`.


# Installation

Add the following line to your `LocalSettings.php` file:

``` php
wfLoadExtension( 'Highlightjs_Integration' );
```

This extension uses a bundled copy of `highlight.js` `11.7.0` with the common languages.  
If you would like to use a different copy of the library, you can download it on the [highlight.js website](https://highlightjs.org/download) and replace the `extensions/Highlightjs_Integration/highlight` folder.


## Adding external libraries to handle additional languages

Some languages are not available in `highlight.js` but you can add external libraries to handle additional languages.  
Here is the list of all [supported languages](https://github.com/highlightjs/highlight.js/blob/main/SUPPORTED_LANGUAGES.md), the ones with a package name are external which need to be installed.

Installation of the [T-SQL language grammar for highlight.js](https://github.com/highlightjs/highlightjs-tsql):

* download the `tsql.min.js` and `ssms.min.css` from the [highlightjs-tsql GitHub repo](https://github.com/highlightjs/highlightjs-tsql/tree/main/dist).
* copy the files to the `extensions/Highlightjs_Integration/highlight` folder.
* update the `extensions/Highlightjs_Integration/extension.json` file.

```js
{
    // ...
    "ResourceModules": {
        "ext.HighlightjsIntegration": {
            "scripts": [
                "highlight/highlight.min.js",
                "highlight/tsql.min.js",      // add the javascript file for the T-SQL language
                "init.js"
            ],
            "styles": [
                "custom.css",
                "highlight/styles/vs2015.min.css",
                "ssms.min.css"               // add the css file for the T-SQL language if any
            ]
```

## Bug Uncaught SyntaxError: unterminated regular expression literal

The load of the `highlight.min.js` script in this extension raises a syntax error.  
As a workaround, the `highlight.min.js` script is loaded from the CDNJS url instead of the file provided with this extension.


# Usage

On a mediawiki page, you can use `syntaxhighlight` or `source` tag:

```xml
<syntaxhighlight>
<?php
// some php code
</syntaxhighlight>
```


# Parameters

* `lang`     Defines the language.
* `inline`   The source code should be inline as part of a paragraph.

```xml
<syntaxhighlight lang="php">
<?php
// some php code
</syntaxhighlight>
```

```xml
<syntaxhighlight lang="cs" inline>var c = new Class();</syntaxhighlight>
```