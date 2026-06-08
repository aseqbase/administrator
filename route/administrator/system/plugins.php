<?php
use MiMFa\Library\Convert;
use MiMFa\Library\Math;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
use MiMFa\Library\Storage;
use MiMFa\Module\Table;
library("Math");
$data = $data ?? [];

function InstallPackage($package, $version = null, $force = true, $parent = null)
{
    if (isAbsoluteUrl($package))
        return InstallPackageFromUrl($package, $version, $force, $parent);
    elseif (isFile($package))
        return InstallPackageFromFile($package, force: $force, parent: $parent);
    else
        return InstallPackageFromPath($package, $version, $force, $parent);
}
function InstallPackageFromUrl($url, $version = null, $force = true, $parent = null)
{
    return InstallPackageFromFile(download(str_replace("{0}", $version ?? "", $url), extensions: [".zip"]), $force, $parent);
}
function InstallPackageFromPath($reference, $version = null, $force = true, $parent = null)
{
    if (!$force) if (table("Package")->SelectRow("*", "`Reference`=:Reference", [":Reference" => $reference]))
        return true;
    return InstallPackageFromFile(download($version ?
        (startsWith($version, "v") ? str_replace(["{0}", "{1}"], [$reference, $version], "https://github.com/{0}/archive/refs/tags/{1}.zip") :
            str_replace(["{0}", "{1}"], [$reference, $version], "https://packagist.org/downloads/{0}?v={1}")) :
        str_replace("{0}", $reference, "https://github.com/{0}/archive/refs/tags/v8.0.0.zip"), extensions: [".zip"]), force: $force, parent: $parent);
}
function InstallPackageFromFile($filePath, &$name = null, $force = true, $parent = null, $special = null)
{
    $dest = InstallerDestination();
    $temp = Storage::CreateUniqueDirectory(\_::$Address->TempDirectory, "unpack", random: true);
    try {
        if (is_string($filePath)) {
            if ($exts = Storage::Decompress($filePath, $temp)) {
                $tempDir = Storage::ParentDirectory(...$exts) ?: $temp;

                $manifest = Convert::FromJson(open($tempDir . "-bootstrap" . DIRECTORY_SEPARATOR . "manifest.json")) ?? null;
                if (!$manifest)
                    $manifest = Convert::FromJson(open($tempDir . "composer.json")) ?? null;

                $c = strlen($tempDir);
                $files = [];
                foreach ($exts as $value) {
                    if($value === $tempDir."index.php")
                        switch (strtolower(get($manifest, "Type")??"")) {
                            case "project":
                                break;
                            case "package":
                            default:
                                continue 2;
                        }
                    if (!preg_match("/[\/\\\]([\-\~]|(composer\.json$)|(\.gitignore$))/i", $value))
                        Storage::Move($value, $files[] = Storage::GetAbsolutePath($dest . substr($value, $c)));
                }

                $reference = get($manifest, "Reference");
                if ($reference && !$force)
                    if (table("Package")->SelectRow("*", "`Reference`=:Reference", [":Reference" => $reference]))
                    return null;

                $md = get($manifest, "MetaData") ?? [];
                $preInstals = get($manifest, "PreInstalls") ?? [];
                $postInstals = get($manifest, "PostInstalls") ?? [];
                $name = (get($manifest, "Name") ?: basename($filePath)).$special;
                foreach ($preInstals as $key => $value)
                    is_numeric($key) ? InstallPackage($value, force: false) : InstallPackage($key, $value, $force);
                if ($schema = open($tempDir . "-bootstrap/schema.sql")) {
                    $md["Tables"] = array_values(array_unique($md["Tables"] ?? preg_find_all("/(?<=%%PREFIX%%)\w+\b/", $schema)));
                    $schema = str_replace('%%DATABASE%%', \_::$Back->DataBaseName, $schema);
                    $schema = str_replace('%%PREFIX%%', \_::$Back->DataBasePrefix, $schema);
                    $pdo = \_::$Back->DataBase->Connection();
                    if ($pdo->exec($schema) === false) {
                        Storage::DeleteDirectory($temp);
                        return deliverWarning("A problem is occured while working on database '$name'!");
                    }
                }
                $pkg = table("Package")->SelectRow("Id, Paths", \_::$Back->DataBase->StartWrap . "Name" . \_::$Back->DataBase->StartWrap . "=:Name", [":Name" => $name]);
                if($pkg["Paths"]??false)
                    foreach (Convert::FromJson($pkg["Paths"]) as $p)
                        $files[] = $p;
                $files = array_unique($files);
                $package = [
                    "Name" => $name,
                    "Version" => get($manifest, "Version"),
                    "Title" => get($manifest, "Title") ?: $name,
                    "Description" => get($manifest, "Description"),
                    "Content" => get($manifest, "Content") ?: Storage::GetFile($dest . "README-$name.md"),
                    "Image" => get($manifest, "Image") ?: "plug",
                    "Reference" => $reference,
                    "MetaData" => $md,
                    "Paths" => Convert::ToJson(($pts = get($manifest, "Paths")) ? loop($pts, fn($v) => $dest . ltrim($v, "\\\/")) : $files)
                ];
                if ($pkg["Id"]??false)
                    table("Package")->Set($pkg["Id"], $package);
                else
                    table("Package")->Insert($package);

                Storage::DeleteDirectory($temp);
                foreach ($postInstals as $key => $value)
                    is_numeric($key) ? InstallPackage($value, force: false) : InstallPackage($key, $value, $force);
                return true;
            } else {
                Storage::DeleteDirectory($temp);
                return deliverError("A problem is occurred while extracting the package '$name'!");
            }
        } elseif ($filePath === false) {
            Storage::DeleteDirectory($temp);
            return deliverError("A problem is occurred while downloading the package '$name'!");
        }
        Storage::DeleteDirectory($temp);
        if ($filePath)
            return deliverError("Could not install the '$filePath'!");
        else
            return deliverError("Something went wrong on installing '$name'!");
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
    $dest = receiveGet("dest");
    if ($filePath = downloadStream(false))
        if (InstallPackageFromFile($filePath, $name, special:(in_array(\_::$Address->Name, ["aseq","base"])?"":"--".\_::$Address->Name).($dest?"--$dest":"")))
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
    $dests = array_keys(\_::$Sequence);
    $rsc = strlen(Storage::ParentDirectory(...$dests) ?: "");
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
            $dest,
            title: "Target: ",
            options: loop($dests, fn($v) => substr($v, $rsc) ?: "[ROOT DIR]"),
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
        try {
            $name = receivePut("Source");
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