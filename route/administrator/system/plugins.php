<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Math;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
use MiMFa\Library\Storage;
use MiMFa\Module\Table;
library("Math");
$data = $data ?? [];
$routeHandler = function ($data) {
    if ($path = downloadStream(false))
        try {
            $dest = array_keys(\_::$Sequence)[\_::$Back->AdminOrigin + 1];
            $bsd = $dest . "-bootstrap" . DIRECTORY_SEPARATOR;
            if (is_string($path)) {
                if ($files = Storage::Decompress($path, $dest)) {
                    $manifest = Convert::FromJson(open($bsd . "manifest.json")) ?? null;
                    $md = get($manifest, "MetaData") ?? [];
                    if ($schema = open($dest . "-bootstrap/schema.sql")) {
                        $md["Tables"] = array_values(array_unique($md["Tables"] ?? preg_find_all("/(?<=%%PREFIX%%)\w+\b/", $schema)));
                        $schema = str_replace('%%DATABASE%%', \_::$Back->DataBaseName, $schema);
                        $schema = str_replace('%%PREFIX%%', \_::$Back->DataBasePrefix, $schema);
                        $pdo = \_::$Back->DataBase->Connection();
                        if ($pdo->exec($schema) === false) {
                            Storage::DeleteDirectory($bsd);
                            return deliverWarning("A problem is occured while working on database!");
                        }
                    }
                    //if(!$manifest) $manifest = Convert::FromJson(open($dest . "composer.json"))??null;
                    $name = get($manifest, "Name") ?: basename($path);
                    $package = [
                        "Name" => $name,
                        "Version" => get($manifest, "Version"),
                        "Title" => $name = get($manifest, "Title") ?: $name,
                        "Description" => get($manifest, "Description"),
                        "Content" => get($manifest, "Content"),
                        "Image" => get($manifest, "Image"),
                        "Reference" => get($manifest, "Reference"),
                        "MetaData" => $md,
                        "Paths" => Convert::ToJson(($pts = get($manifest, "Paths")) ? loop($pts, fn($v) => $dest . ltrim($v, "\\\/")) : $files)
                    ];
                    $id = table("Package")->SelectValue("Id", \_::$Back->DataBase->StartWrap . "Name" . \_::$Back->DataBase->StartWrap . "=:Name", [":Name" => $name]);
                    if ($id)
                        table("Package")->Set($id, $package);
                    else
                        table("Package")->Insert($package);

                    Storage::DeleteDirectory($bsd);
                    return deliverRedirect(Struct::Success("The '$name' package installed successfully!"), delay: 2000);
                }
            } elseif ($path === false) {
                Storage::DeleteDirectory($bsd);
                return deliverError("There occurred a problem in extracting the package!");
            }
            Storage::DeleteDirectory($bsd);
            return deliverWarning("Something went wrong!");
        } catch (\Exception $ex) {
            Storage::DeleteDirectory($bsd);
            return deliverError($ex);
        }

    module("Table");
    $module = new Table(table("Package"));
    $module->IncludeColumns = ["Image", "Title", "Description", "Status", "UpdateTime"];
    $module->AllowServerSide = true;
    $module->AllowDataTranslation = false;
    $module->Updatable = true;
    $module->AddAccess =
        $module->DuplicateAccess =
        $module->ImportAccess =
        $module->RemoveAccess = false;
    $module->ModifyAccess =
        $module->UpdateAccess = \_::$User->AdminAccess;
    $module->AppendControls = fn($id, $row) => [
        Struct::Icon(
            "trash-alt",
            "if(" . Script::Confirm("Are you sure to uninstall this package?") . ") " .
            Script::Send("Delete", null, ["Id" => $id])
        )
    ];
    pod($module, $data);
    return Struct::Center([
        Struct::Button(__("Install or Update a package from computer") . Struct::Icon("plus"), Script::UploadStream(extensions: [".zip"]))
    ]) . $module->ToString();
};

(new Router())
    ->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use ($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "puzzle-piece",
            "Title" => "Plugins Management"
        ]);
    })
    ->Delete(function () {
        if (($id = receiveDelete("Id")) && $package = table("Package")->Get($id)) {
            $package["MetaData"] = Convert::FromJson($package["MetaData"]);
            $dirs = [];
            $msg = "";
            foreach (Convert::FromJson($package["Paths"]) as $k => $v) {
                if (Storage::FindFile($v))
                    Storage::DeleteFile($v);
                $d = dirname($v);
                $dirs[$d] = ($dirs[$d] ?? 0) + 1;
            }
            $c = Math::Sum(array_values($dirs));
            foreach ($dirs as $d => $v)
                if (!Storage::GetDirectory($d))
                    Storage::DeleteDirectory($d);

            if ($tables = get($package, "MetaData", "Tables"))
                foreach ($tables as $k => $v)
                    try {
                        table($v)->Drop();
                    } catch (Exception $ex) {
                        $msg .= Struct::Warning($ex);
                    }
            if (table("Package")->Del($id))
                return deliverRedirect($msg . Struct::Success("The '{$package["Title"]}' package with $c files removed successfully!"));
            else
                return deliverError($msg . "Could not remove the '{$package["Title"]}' package!");
        }
        return deliverError("Could not remove the package!");
    })
    ->Default(fn() => response($routeHandler($data)))
    ->Handle();