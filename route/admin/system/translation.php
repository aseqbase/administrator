<?php
use \MiMFa\Library\Struct;
use \MiMFa\Library\Convert;
use MiMFa\Library\Script;

$data = $data ?? [];
$routeHandler = function () use ($data) {
    module("Table");
    $table = table("Translate_Lexicon", prefix: false);
    $langs = \_::$Front->Translate->GetLanguages();
    $module = new \MiMFa\Module\Table($table);
    $module->SelectQuery = $table->SelectQuery(join(",", ["Id", "KeyCode", ...loop($langs, fn($v, $k) => "ValueOptions AS '" . strtoupper($k) . "'")]));
    $module->ExcludeColumns = ["Id"];
    $module->AllowLabelTranslation = false;
    $module->Controlable = true;
    $module->AddAccess =
        $module->RemoveAccess =
        $module->ModifyAccess =
        $module->DuplicateAccess =
        $module->UpdateAccess = \_::$User->AdminAccess;
    $module->ImportAccess =
        $module->ExportAccess = false;

    foreach ($langs as $k => $value)
        $module->CellsValues[$k] = function ($v) use ($k) {
            return getBetween(Convert::FromJson($v), $k, \_::$Front->Translate->Language ?? "x", "x");
        };
    $module->CellsTypes = [
        "Id" => "hidden",
        "KeyCode" => "text",
        "ValueOptions" => "json"
    ];
    pod($module, $data);
    return $module->ToString();
};

(new Router())
    ->if(!\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Default(function () {
        part(\_::$User->InHandlerPath);
    })
    ->else()
    ->if(receiveGet("export") ?? false)
    ->Get(function () {//Exports
        return uploadContent(Convert::FromCells(Convert::FieldsToCells(\_::$Front->Translate->GetLexicon("ORDER BY `KeyCode` ASC"))), "Lexicon.csv");
    })
    ->else()
    ->Stream(function () {//Imports
        if ($file = downloadStream()) {
            $remain = (receiveStream("total") ?? 0) - (receiveStream("chunk") ?? 0) - 1;
            if (is_string($file)) {
                procedure("_('.content .progressbar').val(0.0).removeClass('invisible');");
                // $c = floatval(count(preg_find_all("/\n/", $file))) + 2;
                // $n = 0;
                // $speed = 1000;
                // $pack = [];
                // foreach (Convert::ToFieldsIterator($file) as $k => $v) {
                //     $pack[] = $v;
                //     if (((++$n % $speed) === 0) && \_::$Front->Translate->SetLexicon($pack)) {
                //         $pack = [];
                //         procedure("_('.content .progressbar').val(" . round($n / $c, 3) . ").removeClass('invisible');");
                //     }
                // }
                // unset($file);

                // if ($pack && \_::$Front->Translate->SetLexicon($pack)) {
                //     $pack = [];
                //     procedure("_('.content .progressbar').val(" . round($n / $c, 3) . ").removeClass('invisible');");
                // }

                $file = Convert::ToFields($file);
                $c = count($file);
                $n = \_::$Front->Translate->SetLexicon($file);
                procedure("_('.content .progressbar').val(0.5).removeClass('invisible');");
                unset($file);

                if ($n) {
                    procedure("_('.content .progressbar').val(1).addClass('invisible');");
                    return redirect(Struct::Success("$n of $c key values setted successfuly in lexicon!"), delay: 2000);
                } else
                    return error("There occurred a problem!");
            } elseif ($file === false)
                return error("There occurred a problem in uploading the file!");
            else if ($remain <= 1)
                return procedure("_('.content .progressbar').val(1).addClass('invisible');");
            else
                return procedure("_('.content .progressbar').val($file).removeClass('invisible');");
        }
    })
    ->Delete(function () {//Deletes
        if (\_::$Front->Translate->ClearAll())
            deliverRedirect(Struct::Success("All key values cleared successfuly from the lexicon!"));
        else
            deliverError("There occurred a problem!");
    })
    ->Get(function () use ($routeHandler) {//Shows
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
        (\_::$Front->AdministratorView)($routeHandler, [
            "Title" => "Translation",
            "Image" => "language",
            "Updatable" => $upd,
            "Content" => Struct::Style("
                .{$moduleTranslator->MainClass} .button {
                    border: none !important;
                }
            ") . Struct::Center(
                        Struct::Container([
                            [
                                Struct::Slot(
                                    Struct::Division(__("Root Language"), ["class" => "be end align", "style" => "padding: calc(var(--size-0) / 2) var(--size-0);"]) .
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
                                    Struct::Division(__("A ``sample` `text``"), ["id" => $id, "class" => "be align start", "style" => "padding: calc(var(--size-0) / 2);"])
                                ),
                            ]
                        ]) .
                        Struct::$Break .
                        Struct::$BreakLine .
                        Struct::$Break .
                        Struct::Division(
                            (
                                $upd ?
                                Struct::Button("View Lexicon", \_::$Address->UrlPath) :
                                Struct::Button("Edit Lexicon", \_::$Address->UrlPath . "?update=true")
                            ) .
                            Struct::Button("Export Lexicon", \_::$Address->UrlPath . "?export=true", ["target" => "blank"]) .
                            Struct::Button("Import Lexicon", Script::UploadStream(extensions: [".csv"])) .
                            Struct::Button(
                                "Clear Lexicon",
                                "if(confirm('Are you sure to clear all lexicon records?')) sendDeleteRequest(null, {'truncate':true}, '.content');",
                                ["class" => "error"]
                            ),
                            ["class" => "be flex middle center", "style" => "gap:var(--size-0);"]
                        ) . Struct::ProgressBar(attributes: ["class" => "view invisible be wide"])
                        ,
                        ["class" => "content"]
                    )
        ]);
    })
    ->Default(fn() => response($routeHandler()))
    ->Handle();