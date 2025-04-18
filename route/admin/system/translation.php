<?php
inspect(\_::$Config->AdminAccess);
use \MiMFa\Library\Router;
use \MiMFa\Library\Html;
use \MiMFa\Library\Convert;
use MiMFa\Module\Part;

(new Router())->Route
    ->if(\Req::Get("export")??false)
        ->Get(function() {//Exports
            $cells = [""];
            $dic = [];
            foreach (\_::$Back->Translate->GetAll("ORDER BY `KeyCode` ASC") as $value){
                foreach ($value as $k=>$v)
                    $dic[$k] = $v;
                $cells[] = loop($dic, function($k, $v){ return $v;});
                foreach ($dic as $k=>$v)
                    $dic[$k] = null;
            }
            $cells[0] = loop($dic, function($k){return $k;});
            \MiMFa\Library\Local::Load(Convert::FromCells($cells), "Lexicon.csv");
        })
    ->else
        ->Post(function() {//Imports
            $c = 0;
            $keys = [];
            foreach (Convert::ToCells(urldecode(first(\Req::Post()))) as $row) {
                if($c===0) {
                    $keys = $row;
                    // $length = count($row);
                    // for ($i = 0; $i < $length; $i++)
                    //     $keys[$i] = $row[$i];
                } else {
                    $col = [];
                    foreach ($row as $i=>$value)
                        if(isset($keys[$i])) $col[$keys[$i]] = $value;
                    $dic[] = $col;
                }
                $c++;
            }
            $c -=2;
            if($c > 0 && \_::$Back->Translate->SetAll($dic))
                \Res::Flip(Html::Success("$c key values setted successfuly in lexicon!"));
            else \Res::Error("There occured a problem!");
        })
        ->Delete(function() {//Deletes
            if(\_::$Back->Translate->ClearAll())
                \Res::Flip(Html::Success("All key values cleared successfuly from the lexicon!"));
            else \Res::Error("There occured a problem!");
        })
        ->Get(function() {//Shows
            $upd = \Req::Receive("update");
            view("part", [
                "Name" => "table/lexicon",
                "Title" => "Translation",
                "Image" => "language",
                "Updatable" => $upd,
                "Content" => Html::Center(
                    (
                        $upd?
                            Html::Button("View Lexicon","/".\Req::$Direction):
                            Html::Button("Edit Lexicon","/".\Req::$Direction."?update=true")
                    ).
                    Html::Button("Export Lexicon","/".\Req::$Direction."?export=true", ["target"=>"blank"]).
                    Html::Button("Import Lexicon","
                        var input = document.createElement('input');
                        input.setAttribute('Type' , 'file');
                        input.onchange = evt => {
                            const [file] = input.files;
                            if (file) {
                                //URL.createObjectUrl(file);
                                const reader = new FileReader();
                                reader.addEventListener('load', (event) => {
                                    sendFile(null, 'data='+encodeURIComponent(event.target.result), '.content');
                                });
                                reader.readAsText(file);
                            }
                        }
                        $(input).trigger('click');
                        return false;
                    "
                   ).
                    Html::Button("Clear Lexicon", "
                        if(confirm('Are you sure to clear all lexicon records?'))
                            sendDelete(null, {'truncate':true}, '.content');
                    ", ["class"=>"error"])
                , ["class"=>"content"])
            ]);
        })
        ->Default(function() {//Deletes
            part("table/lexicon");
        })
        ->Handle();
?>