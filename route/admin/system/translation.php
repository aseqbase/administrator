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
    ->File(function () {//Imports
        $dic = Convert::ToFields(Script::Download(receiveFile(), binary: false));
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
        $upd = received("update");
        $id = "_" . getId();
        $moduleTranslator = new (module("Translator"))();
        $moduleTranslator->Items = \_::$Front->Translate->GetLanguages();
        $moduleTranslator->AllowDefault = true;
        $moduleTranslator->AllowLabel = true;
        $moduleTranslator->AllowImage = false;
        $moduleTranslator->Style = new MiMFa\Library\Style();
        $moduleTranslator->Style->Padding = "0px";
        $moduleTranslator["class"] = "be start flex";
        view("part", [
            "Name" => "admin/table/lexicon",
            "Title" => "Translation",
            "Image" => "language",
            "Updatable" => $upd,
            "Content" => Struct::Center(
                Struct::Container([
                    [
                        Struct::Slot(
                            Struct::Division(__("Root Language"), ["class"=>"be end align", "style"=>"padding: calc(var(--size-0) / 2) var(--size-0);"]) .
                            Struct::TextsInput(
                                "Sample text",
                                "A ``sample` `text``",
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
                            )
                        ),
                        Struct::Slot(
                            $moduleTranslator->ToString() .
                            Struct::Division(__("A ``sample` `text``"), ["id" => $id, "class"=>"be align start", "style"=>"padding: calc(var(--size-0) / 2);"])
                        ),
                    ]
                ]) .
                Struct::$BreakLine .
                (
                    $upd ?
                    Struct::Button("View Lexicon", "/" . \_::$User->Direction) :
                    Struct::Button("Edit Lexicon", "/" . \_::$User->Direction . "?update=true")
                ) .
                Struct::Button("Export Lexicon", "/" . \_::$User->Direction . "?export=true", ["target" => "blank"]) .
                Struct::Button("Import Lexicon", Script::Upload([".csv"], timeout: 300000)) .
                Struct::Button("Clear Lexicon", "
                        if(confirm('Are you sure to clear all lexicon records?'))
                            sendDeleteRequest(null, {'truncate':true}, '.content');
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