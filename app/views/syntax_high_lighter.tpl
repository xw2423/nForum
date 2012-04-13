<link rel="stylesheet" type="text/css" href="<{$static}><{$base}>/<{$syntax}>/styles/shCore.css" />
<link rel="stylesheet" type="text/css" href="<{$static}><{$base}>/<{$syntax}>/styles/shThemeDefault.css" />
<style type="text/css">
.syntaxhighlighter table{table-layout:auto}
</style>
<script type="text/javascript">
SyntaxHighlighter.defaults['gutter'] = true;
SyntaxHighlighter.defaults['toolbar'] = false;
SyntaxHighlighter.config.strings['alert'] = '语法高亮:\n';
SyntaxHighlighter.config.strings['noBrush'] = '无法找到以下语言:';
var sh_init = function(){
    function path(){
        var args = arguments, result = [];

        for(var i = 0; i < args.length; i++)
            result.push(args[i].replace('@', SYS.base + '/<{$syntax}>/scripts/'));

        return result;
    };
    SyntaxHighlighter.vars.discoveredBrushes = null;
    SyntaxHighlighter.autoloader.apply(null, path(
        'applescript            @shBrushAppleScript.js',
        'actionscript3 as3      @shBrushAS3.js',
        'bash shell             @shBrushBash.js',
        'coldfusion cf          @shBrushColdFusion.js',
        'cpp c                  @shBrushCpp.js',
        'c# c-sharp csharp      @shBrushCSharp.js',
        'css                    @shBrushCss.js',
        'delphi pascal          @shBrushDelphi.js',
        'diff patch pas         @shBrushDiff.js',
        'erl erlang             @shBrushErlang.js',
        'groovy                 @shBrushGroovy.js',
        'java                   @shBrushJava.js',
        'jfx javafx             @shBrushJavaFX.js',
        'js jscript javascript  @shBrushJScript.js',
        'perl pl                @shBrushPerl.js',
        'php                    @shBrushPhp.js',
        'text plain             @shBrushPlain.js',
        'py python              @shBrushPython.js',
        'ruby rails ror rb      @shBrushRuby.js',
        'sass scss              @shBrushSass.js',
        'scala                  @shBrushScala.js',
        'sql                    @shBrushSql.js',
        'vb vbnet               @shBrushVb.js',
        'xml xhtml xslt html    @shBrushXml.js'
    ));
    SyntaxHighlighter.all();
}
</script>
