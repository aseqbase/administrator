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
    public \MiMFa\Library\Storage $Driver;
    public string|null $View = "table";
    public string|null $Method = "Storage";
    public array|null $AcceptedFormats = null;

    public function __construct($rootDirectory, $rootUrl)
    {
        parent::__construct();
        $this->View = receiveGet("View") ?? $this->View;
        $p = receiveGet("Path");
        if ($p && startsWith($p, $rootDirectory))
            $this->Driver = new (library("Storage"))($p, Local::GetUrl($p));
        else
            $this->Driver = new (library("Storage"))($rootDirectory, $rootUrl);
        $this->Router->Set($this->Method, fn() => $this->Exclusive());
    }

    public function GetStyle()
    {
        return parent::GetStyle() . Struct::Style("
            .{$this->Name} .toolbar .icon{
                padding: calc(var(--size-0) / 2);
            }
            .{$this->Name} .items .item{
                display: inline-flex;
                align-content: center;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                gap: var(--size-0);
                margin: calc(var(--size-0) / 4);
                padding: var(--size-0);
            }
            .{$this->Name} .items .item:hover{
                background-color: #8882;
            }
            .{$this->Name} .table .item-icon{
                padding: calc(var(--size-0) / 4) calc(var(--size-0) / 2);
                aspect-ratio: 1;
                margin-inline-end: var(--size-0);
                border-radius: var(--radius-max);
            }
            .{$this->Name} .table tr:hover .item-icon{
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
        switch (strtolower($this->View ?? "")) {
            case "table":
                $items = $this->GetTableView();
                break;
            case "items":
            default:
                $items = $this->GetItemsView();
                break;
        }
        return parent::Get() .
            Struct::Frame([
                Struct::Division([
                    Struct::Action(
                        (trim(preg_find("/[^\/\\\]+[\/\\\]$/u", $this->Driver->RootAddress) ?? "", DIRECTORY_SEPARATOR)) .
                        Struct::Icon("arrow-left"),
                        $this->GoScript(dirname(rtrim($this->Driver->RootAddress, DIRECTORY_SEPARATOR)))
                        ,
                        ["class" => "be flex middle center"]
                    ),
                    " ",
                    Struct::Icon("download", $this->UploadFileScript(), ["tooltip" => "Upload a new file"]),
                    Struct::Icon("folder", $this->CreatefolderScript(), ["tooltip" => "Create a new folder"]),
                    Struct::Icon("file", $this->CreateFileScript(), ["tooltip" => "Create a new file"]),
                ], ["class" => "be align start flex col-md"]) .
                Struct::Division([
                    Struct::Icon("refresh", $this->GoScript(), ["tooltip" => "Reload the page"]),
                    Struct::Icon("table", Script::Send(
                        $this->Method,
                        "?path=" . urlencode($this->Driver->RootAddress) . "&view=table",
                        null,
                        ".{$this->Name}",
                        "(d,e)=>{if(d) _('.{$this->Name}').replace(d); else alert(e);}",
                    ), $this->View === "table" ? ["class" => "hidden"] : []),
                    Struct::Icon("list", Script::Send(
                        $this->Method,
                        "?path=" . urlencode($this->Driver->RootAddress) . "&view=items",
                        null,
                        ".{$this->Name}",
                        "(d,e)=>{if(d) _('.{$this->Name}').replace(d); else alert(e);}",
                    ), $this->View === "items" ? ["class" => "hidden"] : [])
                ], ["class" => "be align end col-md col-md-2"])
            ], ["class" => "toolbar"]) . $items;
    }

    public function GetTableView()
    {
        return Struct::Table([
            ["Name", "Size", "URL", "Type", "UpdateTime"],
            ...loop($this->Driver->GetItems(), function ($it, $k, $i) {
                $url = $this->Driver->GetRelativeUrl($it["Path"]);
                return Struct::Row([
                    Struct::CheckInput("Selected", $it["Path"], ["class"=>"hidden"]).
                    Struct::Span(
                        $it["IsDirectory"] ? Struct::Icon("folder", null, ["class" => "be fore yellow"]) : Struct::Icon("file", null, ["class" => "be fore blue"]),
                        null,
                        ["class" => "item-icon", "ondblclick" => $it["IsDirectory"] ? $this->GoScript($it["Path"]) : Script::Load($url, true)]
                    ) .
                    htmlspecialchars(Convert::ToExcerpt($it["Name"], 0, 20, reverse: true)),
                    $it["Size"] ? Struct::Number(content: $it["Size"]) . "B" : null,
                    Struct::Icon("copy", Script::Copy($url)) . Struct::Link("\${".Convert::ToExcerpt($url, 0, 50, reverse: true)."}", $url),
                    $it["MimeType"],
                    Convert::ToShownDateTimeString($it["UpdateTime"])
                ], ["class"=>"item", "onclick"=>"_(this).select('input[name=\"Path\"]').addAttr('checked', 'checked')"]);
            })
        ]);
    }
    public function GetItemsView()
    {
        return Struct::Division(loop(
            $this->Driver->GetItems(),
            function ($it, $k, $i) {
                return Struct::Division(
                    Struct::Span(
                        $it["IsDirectory"] ? Struct::Icon("folder", null, ["class" => "be fore yellow fa-2x"]) : Struct::Icon("file", null, ["class" => "be fore blue fa-2x"]),
                        null,
                        ["class" => "item-icon", "ondblclick" => $it["IsDirectory"] ? $this->GoScript($it["Path"]) : Script::Load($this->Driver->GetRelativeUrl($it["Path"]), true)]
                    ) .
                    Struct::Span(
                        "\${" . htmlspecialchars($it["Name"]) . "}",
                        [
                            "tooltip" => Struct::Paragraph([
                                "Size: " . ($it["Size"] ? Struct::Number($it["Size"]) . "B" : null),
                                "Type: " . $it["MimeType"],
                                "Time: " . Convert::ToShownDateTimeString($it["UpdateTime"])
                            ])
                        ]
                    ).
                    Struct::CheckInput("Path", $it["Path"], ["class"=>"hidden"])
                    ,
                    ["class" => "item"]
                );
            }
        ), ["class" => "items"]);
    }

    public function UploadFileScript($path = null)
    {
        return Script::Upload(
            $this->AcceptedFormats,
            "?path=" . urlencode($path ?? $this->Driver->RootAddress) . "&view={$this->View}",
            "(d,e)=>{if(d) _('.{$this->Name}').replace(d); else alert(e);}",
            method: $this->Method,
            binary: true
        );
    }
    public function CreateFolderScript($path = null)
    {
        return "if(name = " . Script::Prompt('Input the new folder`s name:', 'New Folder') . ") " . self::GoScript(
            $path,
            ["name" => "\${encodeURIComponent(name)}", "action" => "new-folder"]
        );
    }
    public function CreateFileScript($path = null)
    {
        return "if(name = " . Script::Prompt('Input the new file`s name:', 'New File.txt') . ") " . self::GoScript(
            $path,
            ["name" => "\${encodeURIComponent(name)}", "action" => "new-file"]
        );
    }
    public function GoScript($path = null, $data = null)
    {
        return Script::Send(
            $this->Method,
            "?path=" . urlencode($path ?? $this->Driver->RootAddress) . "&view={$this->View}",
            $data,
            ".{$this->Name}",
            "(d,e)=>{if(d) _('.{$this->Name}').replace(d); else alert(e);}",
        );
    }

    public function Exclusive()
    {
        $received = receive($this->Method);
        $act = get($received, "action");
        if ($act) {
            switch (strtolower($act)) {
                case "new-folder":
                    $this->Driver->CreateFolder(urldecode(get($received, "name")));
                    success("The folder created successfully!");
                    break;
                case "new-file":
                    $this->Driver->CreateFile(urldecode(get($received, "name")));
                    success("The file created successfully!");
                    break;
            }
        }
        if ($file = get($received, "Data")) {
            Script::Download($file, $this->Driver->RootAddress.get($received, "Name"), true);
            success("The file uploaded successfully!");
        }
        $this->Router->Get()->Switch();
        return $this->ToString();
    }
}