<?php
auth(\_::$User->AdminAccess);

use \MiMFa\Library\Struct;
use \MiMFa\Library\Convert;
use MiMFa\Library\Script;

(new Router())
    ->if(!\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Default(function () {
        part(\_::$User->InHandlerPath);
    })
    ->else()
    ->if(receiveGet("export") ?? false)
    ->Get(function () {//Exports
        \MiMFa\Library\Local::Load(Convert::FromCells(Convert::FieldsToCells(\_::$Front->Translate->GetAll("ORDER BY `KeyCode` ASC"))), "Lexicon.csv");
    })
    ->else()
    ->Post(function () {//Imports
        $dic = Convert::ToFields(urldecode(first(receivePost())));
        $c = count($dic);
        if ($c > 0 && \_::$Front->Translate->SetAll($dic))
            deliverBreaker(Struct::Success("$c key values setted successfuly in lexicon!"));
        else
            error("There occurred a problem!");
    })
    ->Delete(function () {//Deletes
        if (\_::$Front->Translate->ClearAll())
            deliverBreaker(Struct::Success("All key values cleared successfuly from the lexicon!"));
        else
            error("There occurred a problem!");
    })
    ->Get(function () {//Shows
        $upd = getReceived("update");
        $id = "_" . getId();
        view("part", [
            "Name" => "admin/table/lexicon",
            "Title" => "Translation",
            "Image" => "language",
            "Updatable" => $upd,
            "Content" => Struct::Center(
                Struct::Container([
                    [
                        Struct::TextsInput(
                            "Sample text",
                            "A ''sample' 'text''",
                            attributes: [
                                "class" => "be wide ltr",
                                "oninput" => \_::$Front->MakeFillScript(
                                    "#$id",
                                    function ($text) {
                                        return __($text);
                                    },
                                    ["\${this.value}"]
                                )
                            ]
                        ),
                        Struct::Division("A ''sample' 'text''", ["id" => $id]),
                    ]
                ]) .
                Struct::$BreakLine .
                (
                    $upd ?
                    Struct::Button("View Lexicon", "/" . \_::$User->Direction) :
                    Struct::Button("Edit Lexicon", "/" . \_::$User->Direction . "?update=true")
                ) .
                Struct::Button("Export Lexicon", "/" . \_::$User->Direction . "?export=true", ["target" => "blank"]) .
                Struct::Button("Import Lexicon", Script::ImportFile([".csv"], timeout: 300000)) .
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