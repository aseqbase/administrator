<?php
namespace MiMFa\Module;

use MiMFa\Library\Convert;
use MiMFa\Library\Local;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;

/**
 * To manage the storage
 *@copyright All rights are reserved for MiMFa Development Group
 *@author Mohammad Fathi
 *@see https://aseqbase.ir, https://github.com/aseqbase/aseqbase
 *@link https://github.com/aseqbase/aseqbase/wiki/Modules See the Documentation
 */
class Storage extends Module
{
    public string|null $Arrange = null;
    public string|null $Method = "Storage";
    public array|null $AcceptedFormats = null;
    public readonly string $RootAddress;
    public readonly string $RootUrl;
    public readonly string $CurrentAddress;
    public readonly string $CurrentUrl;

    public function __construct($rootAddress, $rootUrl)
    {
        parent::__construct();
        $this->RootAddress = Local::GetAbsoluteAddress($rootAddress);
        $this->RootUrl = Local::GetAbsoluteUrl($rootUrl);
        setMemo("Arrange", $this->Arrange = receiveGet("Arrange") ?? $this->Arrange ?? getMemo("Arrange"));
        $p = receiveGet("Path");
        if ($p && startsWith($p = Local::GetAbsoluteAddress($p), $rootAddress)) {
            $this->CurrentAddress = $p;
            $this->CurrentUrl = Local::GetUrl($rootUrl . substr($p, strlen($rootAddress)));
        } else {
            $this->CurrentAddress = $this->RootAddress;
            $this->CurrentUrl = $this->RootUrl;
        }
        $this->Router->Set($this->Method, fn() => $this->Exclusive());
    }

    public function GetStyle()
    {
        return parent::GetStyle() . Struct::Style("
            .{$this->Name} .toolbar{
                direction: ltr;
                border-bottom: var(--border-1);
                margin-bottom: var(--size-0);
            }
            .{$this->Name} .toolbar .icon{
                padding: calc(var(--size-0) / 2);
            }
            .{$this->Name} .toolbar .parent{
                padding: calc(var(--size-0) / 2);
            }
            .{$this->Name} .toolbar .parent:hover{
                padding: calc(var(--size-0) / 2);
                cursor:pointer;
                background-color: #8882;
            }
            .{$this->Name} .items-arrange .item{
                display: inline-flex;
                align-content: center;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                gap: var(--size-0);
                margin: calc(var(--size-0) / 4);
                padding: calc(var(--size-0) / 2) calc(var(--size-0) / 4);
                width: calc(var(--size-max) * 2);
            }
            .{$this->Name} .items-arrange .item:hover{
                background-color: #8882;
            }
            .{$this->Name} .items-arrange .item .title{
                text-align: center;
                font-size: var(--size-1);
                line-height: 1.2em;
                max-height: 3.5em;
                overflow: hidden;
                overflow-wrap: anywhere;
            }
            .{$this->Name} .table-arrange{
                direction: ltr;
            }
            .{$this->Name} .table-arrange .item td>*{
                max-width: 100%;
                overflow: hidden;
                line-height: 1.2em;
                overflow-wrap: anywhere;
            }
            .{$this->Name} .table-arrange .item .title{
                text-align: center;
            }
            .{$this->Name} .table-arrange .item-icon{
                padding: calc(var(--size-0) / 4) calc(var(--size-0) / 2);
                aspect-ratio: 1;
                margin-inline-end: var(--size-0);
                border-radius: var(--radius-max);
            }
            .{$this->Name} .table-arrange tr:hover .item-icon{
                background-color: #8882;
            }
            .{$this->Name} .item:has(.checkinput:checked){
                background-color: rgba(121, 159, 241, 0.2);
            }
        ");
    }

    public function Get()
    {
        $items = null;
        switch (strtolower($this->Arrange ?? "")) {
            case "table":
                $items = $this->GetTableArrange();
                break;
            case "items":
            default:
                $items = $this->GetItemsArrange();
                break;
        }
        return parent::Get() .
            Struct::SmallFrame([
                Struct::Division([
                    ...($this->CurrentAddress !== $this->RootAddress ? [
                        Struct::Action(
                            Struct::Icon("folder-open", null, ["class" => "be fore green"]) .
                            (trim(preg_find("/[^\/\\\]+[\/\\\]$/u", $this->CurrentAddress) ?? "", DIRECTORY_SEPARATOR)) .
                            Struct::Icon("arrow-left"),
                            $this->GoScript(dirname(rtrim($this->CurrentAddress, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR)
                            ,
                            ["class" => "be flex middle center parent"]
                        ) . " "
                    ] : []),
                    Struct::Icon("download", $this->UploadFileScript(), ["tooltip" => "Upload a new file"]),
                    Struct::Icon("folder", $this->CreatefolderScript(), ["tooltip" => "Create a new folder"]),
                    Struct::Icon("file", $this->CreateFileScript(), ["tooltip" => "Create a new file"]),
                ], ["class" => "be align start flex col-sm"]) .
                Struct::Division([
                    Struct::Icon("refresh", $this->GoScript(), ["tooltip" => "Reload the page"]),
                    Struct::Icon("list", $this->SendScript("?path=" . urlencode($this->CurrentAddress) . "&arrange=table"), $this->Arrange === "table" ? ["class" => "hidden"] : []),
                    Struct::Icon("th", $this->SendScript("?path=" . urlencode($this->CurrentAddress) . "&arrange=items"), $this->Arrange === "items" ? ["class" => "hidden"] : [])
                ], ["class" => "be align end col-sm col-sm-2"])
            ], ["class" => "toolbar"]) . $items;
    }

    public function GetScript()
    {
        $url = "?path=" . urlencode($this->CurrentAddress) . "&arrange={$this->Arrange}";
        $successScript = "(d,e)=>{if(d) _('.{$this->Name}').replace(d); else alert(e);}";
        return parent::GetScript() . Struct::Script("

            function {$this->MainName}_Send(path = null, data = null){
                " . Script::Send(
                    $this->Method,
                    "\${path ?? " . Script::Convert($url) . "}",
                    "\${data}",
                    ".{$this->Name}",
                    $successScript,
                ) . "
            }

            function {$this->MainName}_Go(path = null, data = null){
                {$this->MainName}_Send(path ? '? path=' + encodeURIComponent(path) + '&arrange={$this->Arrange}' : " . Script::Convert($url) . ", data);
            }

            function {$this->MainName}_UploadFile(){
            " . Script::UploadDialog(
                    $this->AcceptedFormats,
                    $url,
                    $successScript,
                    method: $this->Method,
                    binary: true
                ) . "
            }

            function {$this->MainName}_CreateFolder(name){
                if(name || (name = " . Script::Prompt('Input the new folder`s name:', 'New Folder') . ")) {
                    {$this->MainName}_Go(null, " . Script::Convert(["name" => "\${encodeURIComponent(name)}", "action" => "new-folder"]) . ");
                }
            }

            function {$this->MainName}_CreateFile(name){
                if(name || (name = " . Script::Prompt('Input the new file`s name:', 'New File') . ")) {
                    {$this->MainName}_Go(null, " . Script::Convert(["name" => "\${encodeURIComponent(name)}", "action" => "new-file"]) . ");
                }
            }
        ");
    }

    public function GetTableArrange()
    {
        return Struct::Division(
            Struct::Table([
                ["Name", "Size", "URL", "Type", "UpdateTime"],
                ...loop(Local::GetDirectoryItems($this->CurrentAddress), function ($it, $k, $i) {
                    $aurl = $this->GetAbsoluteUrl($it["Path"]);
                    $url = getRequest($aurl);
                    return Struct::Row([
                        "\${" .
                        Struct::CheckInput("Selected", $it["Path"], ["class" => "hidden"]) .
                        Struct::Span(
                            $it["IsDirectory"] ? Struct::Icon("folder", null, ["class" => "be fore yellow"]) : Struct::Icon("file", null, ["class" => "be fore blue"]),
                            null,
                            ["class" => "item-icon", "ondblclick" => $it["IsDirectory"] ? $this->GoScript($it["Path"]) : Script::Load($aurl, true)]
                        ) .
                        Struct::Span("\${{$it["Name"]}}", null, ["class" => "title"]) .
                        "}",
                        $it["Size"] ? Struct::Number(content: $it["Size"]) . "B" : null,
                        $it["IsDirectory"] ? Struct::Icon("folder-open", $this->GoScript($it["Path"])) : ("\${" . Struct::Icon("copy", Script::Copy($aurl)) . Struct::Link("\${" . Convert::ToExcerpt($url, 0, 50) . "}", $aurl, ["class" => "view md-hide", "target" => "_blank"]) . "}"),
                        $it["MimeType"],
                        Convert::ToShownDateTimeString($it["UpdateTime"])
                    ], ["class" => "item", "onclick" => "_(this).select('input[name=\"Path\"]').addAttr('checked', 'checked')"]);
                })
            ]) .
            $this->GetContextMenu(),
            ["class" => "table-arrange"]
        );
    }
    public function GetItemsArrange()
    {
        return Struct::Division([
            ...loop(
                Local::GetDirectoryItems($this->CurrentAddress),
                function ($it, $k, $i) {
                    $aurl = $this->GetAbsoluteUrl($it["Path"]);
                    return Struct::Division(
                        Struct::Span(
                            $it["IsDirectory"] ? Struct::Icon("folder", null, ["class" => "be fore yellow fa-2x"]) : Struct::Icon("file", null, ["class" => "be fore blue fa-2x"]),
                            null,
                            ["class" => "item-icon"]
                        ) .
                        Struct::Span(
                            "\${{$it["Name"]}}",
                            [
                                "class" => "title",
                                "tooltip" => Struct::Part([
                                    "Address: " . $it["Name"] . " " . Struct::Icon("copy", Script::Copy($aurl)),
                                    "Size: " . ($it["Size"] ? Struct::Number($it["Size"]) . "B" : null),
                                    "Type: " . $it["MimeType"],
                                    "Modified: " . Convert::ToShownDateTimeString($it["UpdateTime"]),
                                    "Created: " . Convert::ToShownDateTimeString($it["CreateTime"])
                                ], ["class" => "be align start"])
                            ]
                        ) .
                        Struct::CheckInput("Path", $it["Path"], ["class" => "hidden"]),
                        ["class" => "item", "ondblclick" => $it["IsDirectory"] ? $this->GoScript($it["Path"]) : Script::Load($aurl, true)]
                    );
                }
            ),
            $this->GetContextMenu()
        ], ["class" => "items-arrange"]);
    }

    public function GetContextMenu()
    {
        return Struct::ContextMenu([
            Struct::Action(Struct::Icon("upload") . Struct::Span("Upload a new file"), $this->UploadFileScript()),
            Struct::Action(Struct::Icon("folder") . Struct::Span("Create a new folder"), $this->CreatefolderScript()),
            Struct::Action(Struct::Icon("file") . Struct::Span("Create a new file"), $this->CreateFileScript()),
            Struct::Action(Struct::Icon("refresh") . Struct::Span("Refresh"), $this->GoScript()),
        ]);
    }

    public function GetAbsoluteUrl(string $path): string
    {
        return Local::GetAbsoluteUrl($this->RootUrl . normalizeUrl(substr($path, strlen($this->RootAddress))));
    }
    public function GetRelativeUrl(string $path): string
    {
        return getRequest($this->GetAbsoluteUrl($path));
    }

    public function SendScript($path = null, $data = null)
    {
        return "{$this->MainName}_Send(" . Script::Convert($path) . "," . Script::Convert($data) . ")";
    }
    public function GoScript($path = null, $data = null)
    {
        return "{$this->MainName}_Go(" . Script::Convert($path) . "," . Script::Convert($data) . ")";
    }
    public function UploadFileScript()
    {
        return "{$this->MainName}_UploadFile()";
    }
    public function CreateFolderScript($name = null)
    {
        return "{$this->MainName}_CreateFolder(" . Script::Convert($name) . ")";
    }
    public function CreateFileScript($name = null)
    {
        return "{$this->MainName}_CreateFile(" . Script::Convert($name) . ")";
    }

    public function Exclusive()
    {
        $received = receive($this->Method);
        $act = get($received, "action");
        if ($act) {
            switch (strtolower($act)) {
                case "new-folder":
                    if (Local::CreateDirectory($this->CurrentAddress . Local::SanitizeName(urldecode(get($received, "name")))))
                        success("The folder created successfully!");
                    else
                        error("Could not to create the folder!");
                    break;
                case "new-file":
                    if (Local::CreateFile($this->CurrentAddress . Local::SanitizeName(urldecode(get($received, "name")))))
                        success("The file created successfully!");
                    else
                        error("Could not to create the file!");
                    break;
            }
        }
        if ($file = get($received, "data"))
            try {
                if (
                    Script::Download(
                        $file,
                        $this->CurrentAddress . get($received, "name"),
                        method: $this->Method,
                        binary: true
                    )
                )
                    success("The file uploaded successfully!");
                else
                    error("Could not to upload the file!");
            } catch (\Exception $ex) {
                error($ex);
            }
        $this->Router->Get()->Switch();
        return $this->ToString();
    }
}