<?php
ACCESS(\_::$CONFIG->AdminAccess);
use \MiMFa\Library\HTML;
use \MiMFa\Library\Convert;
if(isset($_GET["export"])){
    $cells = [""];
    $dic = [];
    foreach (\MiMFa\Library\Translate::GetAll("ORDER BY `KeyCode` ASC") as $value){
        foreach ($value as $k=>$v)
            $dic[$k] = $v;
        $cells[] = loop($dic, function($k, $v){ return $v;});
        foreach ($dic as $k=>$v)
            $dic[$k] = null;
    }
    $cells[0] = loop($dic, function($k){return $k;});
    \MiMFa\Library\Local::Download(Convert::FromCells($cells), "Lexicon.csv");
    die;
} elseif(isset($_GET["import"])){
    $c = 0;
    $keys = [];
    foreach (Convert::ToCells(urldecode(first($_POST))) as $row) {
        if($c===0) {
            $keys = $row;
            for ($i = 0; $i < $length; $i++)
                $keys[$i] = $keys[$i];
        } else {
            $col = [];
            foreach ($row as $i=>$value)
                if(isset($keys[$i])) $col[$keys[$i]] = $value;
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
else{
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
}
?>