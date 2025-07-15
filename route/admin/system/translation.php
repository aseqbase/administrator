<?php
inspect(\_::$Config->AdminAccess);
use \MiMFa\Library\Router;
use \MiMFa\Library\Html;
use \MiMFa\Library\Convert;
use MiMFa\Library\Script;

(new Router())->Route
    ->if(!auth(\_::$Config->AdminAccess))
    ->Default(function () {
        part(MiMFa\Library\User::$InHandlerPath);
    })
    ->else
    ->if(\Req::ReceiveGet("export") ?? false)
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
    ->else
    ->Post(function () {//Imports
        $c = 0;
        $keys = [];
        foreach (Convert::ToCells(urldecode(first(\Req::ReceivePost()))) as $row) {
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
        $c -= 2;
        if ($c > 0 && \_::$Back->Translate->SetAll($dic))
            \Res::Flip(Html::Success("$c key values setted successfuly in lexicon!"));
        else
            \Res::Error("There occured a problem!");
    })
    ->Delete(function () {//Deletes
        if (\_::$Back->Translate->ClearAll())
            \Res::Flip(Html::Success("All key values cleared successfuly from the lexicon!"));
        else
            \Res::Error("There occured a problem!");
    })
    ->Get(function () {//Shows
        $upd = \Req::Receive("update");
        view("part", [
            "Name" => "admin/table/lexicon",
            "Title" => "Translation",
            "Image" => "language",
            "Updatable" => $upd,
            "Content" => Html::Center(
                (
                    $upd ?
                    Html::Button("View Lexicon", "/" . \Req::$Direction) :
                    Html::Button("Edit Lexicon", "/" . \Req::$Direction . "?update=true")
                ) .
                Html::Button("Export Lexicon", "/" . \Req::$Direction . "?export=true", ["target" => "blank"]) .
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