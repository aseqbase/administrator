<?php
inspect(\_::$Config->AdminAccess);
use \MiMFa\Library\Router;
use \MiMFa\Library\Html;
use \MiMFa\Library\Convert;
use MiMFa\Library\Script;

(new Router())
    ->if(!auth(\_::$Config->AdminAccess))
    ->Default(function () {
        part(\User::$InHandlerPath);
    })
    ->else()
    ->if(receiveGet("export") ?? false)
    ->Get(function () {//Exports
        $cells = [""];
        $dic = [];
        foreach (\_::$Back->Translate->GetAll("ORDER BY `KeyCode` ASC") as $value) {
            foreach ($value as $k => $v)
                $dic[$k] = $v;
            $cells[] = loop($dic, function ($v) {
                return $v; });
            foreach ($dic as $k => $v)
                $dic[$k] = null;
        }
        $cells[0] = loop($dic, function ($v, $k) {
            return $k; });
        \MiMFa\Library\Local::Load(Convert::FromCells($cells), "Lexicon.csv");
    })
    ->else()
    ->Post(function () {//Imports
        $c = 0;
        $keys = [];
        foreach (Convert::ToCells(urldecode(first(receivePost()))) as $row) {
            if ($c === 0) {
                $keys = $row;
                // $length = count($row);
                // for ($i = 0; $i < $length; $i++)
                //     $keys[$i] = $row[$i];
            } else {
                $col = [];
                foreach ($row as $i => $value)
                    if (isset($keys[$i]))
                        $col[$keys[$i]] = $value;
                $dic[] = $col;
            }
            $c++;
        }
        $c = count($dic);
        if ($c > 0 && \_::$Back->Translate->SetAll($dic))
            flipResponse(Html::Success("$c key values setted successfuly in lexicon!"));
        else
            renderError("There occurred a problem!");
    })
    ->Delete(function () {//Deletes
        if (\_::$Back->Translate->ClearAll())
            flipResponse(Html::Success("All key values cleared successfuly from the lexicon!"));
        else
            renderError("There occurred a problem!");
    })
    ->Get(function () {//Shows
        $upd = receive("update");
        view("part", [
            "Name" => "admin/table/lexicon",
            "Title" => "Translation",
            "Image" => "language",
            "Updatable" => $upd,
            "Content" => Html::Center(
                (
                    $upd ?
                    Html::Button("View Lexicon", "/" . \_::$Direction) :
                    Html::Button("Edit Lexicon", "/" . \_::$Direction . "?update=true")
                ) .
                Html::Button("Export Lexicon", "/" . \_::$Direction . "?export=true", ["target" => "blank"]) .
                Html::Button("Import Lexicon", Script::ImportFile($timeout = 300000)) .
                Html::Button("Clear Lexicon", "
                        if(confirm('Are you sure to clear all lexicon records?'))
                            sendDelete(null, {'truncate':true}, '.content');
                    ", ["class" => "error"])
                ,
                ["class" => "content"]
            )
        ]);
    })
    ->Default(function () {
        part("admin/table/lexicon");
    })
    ->Handle();
?>