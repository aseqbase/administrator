<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Math;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
use MiMFa\Library\Storage;
use MiMFa\Module\Table;
library("Math");
$data = $data ?? [];

function InstallPackage($package, $version = null)
{
    if (isAbsoluteUrl($package))
        return InstallPackageFromUrl($package, $version);
    elseif (isFile($package))
        return InstallPackageFromFile($package, $version);
    else
        return InstallPackageFromPath($package, $version);
}
function InstallPackageFromUrl($url, $version = null)
{
    return InstallPackageFromFile(download(str_replace("{0}", $version ?? "", $url), null));
}
function InstallPackageFromPath($path, $version = null)
{
    return InstallPackageFromFile(download($version ?
        (startsWith($version, "v") ? str_replace(["{0}", "{1}"], [$path, $version], "https://github.com/{0}/archive/refs/tags/{1}.zip") :
            str_replace(["{0}", "{1}"], [$path, $version], "https://packagist.org/downloads/{0}?v={1}")) :
        str_replace("{0}", $path, "https://github.com/{0}/archive/refs/tags/v8.0.0.zip"), extensions: [".zip"]));
}
function InstallPackageFromFile($filePath, &$name = null)
{
    $dest = InstallerDestination();
    $temp = Storage::CreateUniqueDirectory(\_::$Address->TempDirectory);
    try {
        if (is_string($filePath)) {
            if ($files = Storage::Decompress($filePath, $temp)) {
                $tempDir = Storage::ParentDirectory(...$files) ?? $temp;
                $c = strlen($tempDir);
                foreach ($files as $value)
                    if (!startsWith(basename($value), "-", "~", "composer.json", ".gitignore"))
                        Storage::Move($value, $dest . substr($value, 0, $c));
                $manifest = Convert::FromJson(open($tempDir . "-bootstrap" . DIRECTORY_SEPARATOR . "manifest.json")) ?? null;
                if (!$manifest && path($tempDir . "composer.json"))
                    $manifest = Convert::FromJson(open($tempDir . "composer.json")) ?? null;
                $md = get($manifest, "MetaData") ?? [];
                $preInstals = get($manifest, "PreInstalls") ?? [];
                $postInstals = get($manifest, "PostInstalls") ?? [];
                foreach ($preInstals as $key => $value)
                    is_numeric($key) ? InstallPackage($value) : InstallPackage($key, $value);
                if ($schema = open($tempDir . "-bootstrap/schema.sql")) {
                    $md["Tables"] = array_values(array_unique($md["Tables"] ?? preg_find_all("/(?<=%%PREFIX%%)\w+\b/", $schema)));
                    $schema = str_replace('%%DATABASE%%', \_::$Back->DataBaseName, $schema);
                    $schema = str_replace('%%PREFIX%%', \_::$Back->DataBasePrefix, $schema);
                    $pdo = \_::$Back->DataBase->Connection();
                    if ($pdo->exec($schema) === false) {
                        Storage::DeleteDirectory($temp);
                        return deliverWarning("A problem is occured while working on database!");
                    }
                }
                $name = get($manifest, "Name") ?: basename($filePath);
                $package = [
                    "Name" => $name,
                    "Version" => get($manifest, "Version"),
                    "Title" => $name = get($manifest, "Title") ?: $name,
                    "Description" => get($manifest, "Description"),
                    "Content" => get($manifest, "Content") ?: Storage::GetFile($dest . "README-$name.md"),
                    "Image" => get($manifest, "Image")?:"plug",
                    "Reference" => get($manifest, "Reference"),
                    "MetaData" => $md,
                    "Paths" => Convert::ToJson(($pts = get($manifest, "Paths")) ? loop($pts, fn($v) => $dest . ltrim($v, "\\\/")) : $files)
                ];
                $id = table("Package")->SelectValue("Id", \_::$Back->DataBase->StartWrap . "Name" . \_::$Back->DataBase->StartWrap . "=:Name", [":Name" => $name]);
                if ($id)
                    table("Package")->Set($id, $package);
                else
                    table("Package")->Insert($package);

                Storage::DeleteDirectory($temp);
                foreach ($postInstals as $key => $value)
                    is_numeric($key) ? InstallPackage($value) : InstallPackage($key, $value);
                return true;
            }
        } elseif ($filePath === false) {
            Storage::DeleteDirectory($temp);
            return deliverError("There occurred a problem in extracting the package!");
        } else
            return deliverWarning("Could not access to the package '$filePath'!");
        Storage::DeleteDirectory($temp);
        if ($filePath)
            return deliverError("Could not install the '$filePath'!");
        else
            return deliverError("Something went wrong!");
    } catch (\Exception $ex) {
        Storage::DeleteDirectory($temp);
        return deliverError($ex);
    }
}
function InstallerDestination()
{
    return array_keys(\_::$Sequence)[receiveGet("dest") ?? (\_::$Back->AdminOrigin + 1)];
}

$routeHandler = function ($data) {
    if ($filePath = downloadStream(false))
        if (InstallPackageFromFile($filePath, $name))
            return deliverRedirect(Struct::Success("The '$name' package installed successfully!"), delay: 2000);
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
    $module->CellsTypes = [
        "Image" => "image",
        "Content" => "content",
        "Paths" => "json",
    ];
    pod($module, $data);
    return Struct::Division(
        Struct::Button(
            Struct::Span(Struct::Icon("plus") . __("Install or Update from computer"), null, ["class" => "be flex middle gap-1"]),
            Script::UploadStream(\_::$Address->Url, extensions: [".zip"])
        ) .
        Struct::Field(
            "text",
            "source",
            title: "Source: ",
            attributes: ["PlaceHolder" => "The package url path"],
            description: Struct::Icon("download", Script::Send(
                "PUT",
                \_::$Address->Url,
                [
                    "Source" => "\${_('.package-installer input[name=\"source\"]').val()}"
                ]
            ))
        ) .
        Struct::Field(
            "select",
            "dest",
            receiveGet("dest"),
            title: "Target: ",
            options: loop(array_keys(\_::$Sequence), fn($v) => substr($v, strlen(\_::$Address->RootDirectory)) ?: "[ROOT DIRECTORY]"),
            attributes: ["onchange" => Script::Load(\_::$Address->UrlPath . "?dest=\${this.value}")]
        ),
        ["class" => "be flex middle justify wide package-installer"]
    ) . $module->ToString();
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
    ->Put(function () {
        $name = receivePut("Source");
        try {
            if (InstallPackage($name))
                return deliverRedirect(Struct::Success("The '$name' package installed successfully!"), delay: 2000);
            else
                return deliverError("Could not download the '$name'!");
        } catch (\Exception $ex) {
            return deliverError($ex);
        }
    })
    ->Default(fn() => response($routeHandler($data)))
    ->Handle();