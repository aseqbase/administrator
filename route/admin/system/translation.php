<?php
auth(\_::$User->AdminAccess);

use \MiMFa\Library\Struct;
use \MiMFa\Library\Convert;
use MiMFa\Library\Script;

(new Router())
    ->if(!\_::$User->GetAccess(\_::$User->AdminAccess))
    ->Default(function () {
        part(\_::$User->InHandlerPath);
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
            deliverBreaker(Struct::Success("$c key values setted successfuly in lexicon!"));
        else
            error("There occurred a problem!");
    })
    ->Delete(function () {//Deletes
        if (\_::$Back->Translate->ClearAll())
            deliverBreaker(Struct::Success("All key values cleared successfuly from the lexicon!"));
        else
            error("There occurred a problem!");
    })
    ->Get(function () {//Shows
        $upd = getReceived("update");
        view("part", [
            "Name" => "admin/table/lexicon",
            "Title" => "Translation",
            "Image" => "language",
            "Updatable" => $upd,
            "Content" => Struct::Center(
                (
                    $upd ?
                    Struct::Button("View Lexicon", "/" . \_::$Address->Direction) :
                    Struct::Button("Edit Lexicon", "/" . \_::$Address->Direction . "?update=true")
                ) .
                Struct::Button("Export Lexicon", "/" . \_::$Address->Direction . "?export=true", ["target" => "blank"]) .
                Struct::Button("Import Lexicon", Script::ImportFile($timeout = 300000)) .
                Struct::Button("Clear Lexicon", "
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