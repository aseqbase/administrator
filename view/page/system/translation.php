<?php
ACCESS(\_::$CONFIG->AdminAccess);
use \MiMFa\Library\HTML;
if(isset($_GET["export"])){
    $content = [""];
    $dic = [];
    foreach (\MiMFa\Library\Translate::GetAll() as $value){
        foreach ($value as $k=>$v)
            $dic[$k] = preg_find("/[\s\W]/", $v)?('"'.str_replace("\"","\\\"",$v).'"'):$v;
        $content[] = join(",",loop($dic, function($k, $v){ return $v;}));
        foreach ($dic as $k=>$v)
            $dic[$k] = null;
    }
    $content[0] = join(",",loop($dic, function($k){return $k;}));
    \MiMFa\Library\Local::Download(join("\r\n", $content), "Lexicon.csv");
    die;
} elseif(isset($_GET["import"])){
    $c = 0;
    $keys = [];
    $dic = [];
    //$pat = '/(?<=^|\,)((([\"\'])[^\"]*\3)|([^\"\,][^\,]*)|(\s?))(?=$|\,)/m';
    $pat = '/\,/m';
    $cdic = [];
    $data = code(urldecode(first($_POST)), $cdic);
    foreach (preg_split("/(\r\n)|(\n\r)|\n/", $data) as $row) {
        if($c===0) {
            $keys = preg_split($pat, $row);
            for ($i = 0; $i < $length; $i++)
                $keys[$i] = decode($keys[$i], $cdic);
        } else {
            $col = [];
            foreach (preg_split($pat, $row) as $i=>$value)
                if(isset($keys[$i])) $col[$keys[$i]] = trim(decode($value, $cdic), "\"'");
            $dic[] = $col;
        }
        $c++;
    }
    $c -=2;
    if($c > 0 && \MiMFa\Library\Translate::SetAll($dic))
        FLIP(HTML::Success("$c key values setted successfuly in lexicon!"));
    else die(HTML::Error("There occured a problem!"));
} elseif(isset($_GET["truncate"])){
    if(\MiMFa\Library\Translate::ClearAll())
        FLIP(HTML::Success("All key values cleared successfuly from the lexicon!"));
    else die(HTML::Error("There occured a problem!"));
}

MODULE("PrePage");
$module = new MiMFa\Module\PrePage();
$module->Title = "Translation";
$module->Draw();
if(!RECEIVE(null,"post")){
    echo HTML::Center(
        (RECEIVE("update")?HTML::Button("View Lexicon","/".\_::$DIRECTION):HTML::Button("Edit Lexicon","/".\_::$DIRECTION."?update=true")).
        HTML::Button("Export Lexicon","/".\_::$DIRECTION."?export&".\_::$CONFIG->ViewHandlerKey."=value").
        HTML::Button("Import Lexicon","
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.onchange = evt => {
        const [file] = input.files;
        if (file) {
            //URL.createObjectURL(file);
            const reader = new FileReader();
            reader.addEventListener('load', (event) => {
                postData(
                    '/".\_::$DIRECTION."?import&".\_::$CONFIG->ViewHandlerKey."=value',
                    'data='+encodeURIComponent(event.target.result),
                    'content',
                    function(data, selector){
						$(selector + ' .result').remove();
						if(isEmpty(data)) load();
						else {
							data = (data !=null && typeof(data) == 'object')?data.statusText:data??'".__("The form submitted successfully!")."';
							if(isSet(data) && !isEmpty(data)) $(selector).prepend(data);
                            load();
						}
					}
                );
            });
            reader.readAsText(file);
        }
    }
    $(input).trigger('click');
    return false;
").
        HTML::Button("Clear Lexicon","
        if(confirm('Are you sure to clear all lexicon records?'))
            postData(
                '/".\_::$DIRECTION."?truncate&".\_::$CONFIG->ViewHandlerKey."=value',
                {
                    selector:'content',
                    successHandler: function(data, selector){
						$(selector + ' .result').remove();
						if(isEmpty(data)) load();
						else {
							data = (data !=null && typeof(data) == 'object')?data.statusText:data??'".__("The form submitted successfully!")."';
							if(isSet(data) && !isEmpty(data)) $(selector).prepend(data);
                            load();
						}
					}
                }
            );
    ", ["class"=>"Error"])
    );
}
echo HTML::Page(PART("table/lexicon", print:false));
?>