<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
use MiMFa\Library\Storage;
use MiMFa\Module\Table;
$data = $data ?? [];
$routeHandler = function () use ($data) {
    if ($path = downloadStream())
        try {
            if (is_string($path)) {
                if ($files = Storage::Decompress($path, \_::$Address->Directory)) {
                    table("Package")->Insert([
                        "Name" => basename($path),
                        "Paths" => Convert::ToJson($files)
                    ]);
                }
            } elseif ($path === false)
                error("There occurred a problem in extracting the package!");
        } catch (\Exception $ex) {
            deliverError($ex);
        }

    module("Table");
    $module = new Table(table("Package"));
    $module->AllowServerSide = true;
    $module->Updatable = true;
    $module->AddAccess =
        $module->ImportAccess =
        $module->RemoveAccess =
        $module->ModifyAccess = false;
    $module->UpdateAccess = \_::$User->AdminAccess;
    $module->PrependControlsCreator = function ($id, $row) {
        return [Struct::Icon("trash")];
    };
    pod($module, $data);
    return Struct::Center([
        Struct::Button(__("Upload a package") . Struct::Icon("plus"), Script::UploadStream(extensions: [".zip"]))
    ]) . $module->ToString();
};

(new Router())
    ->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdministratorView)($routeHandler, [
            "Image" => "puzzle-piece",
            "Title" => "Plugins Management"
        ]);
    })
    ->Default(fn() => response($routeHandler()))
    ->Handle();