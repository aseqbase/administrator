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
    public bool $MultiSelect = true;
    public $ModifyAccess = true;

    public function __construct($rootAddress, $rootUrl)
    {
        parent::__construct();
        $this->RootAddress = Local::GetAbsoluteAddress($rootAddress);
        $this->RootUrl = Local::GetAbsoluteUrl($rootUrl);
        setMemo("Arrange", $this->Arrange = receiveGet("Arrange") ?? $this->Arrange ?? getMemo("Arrange"));
        $this->Name = receiveGet("Class") ?? $this->Name;
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
                overflow-wrap: anywhere;
            }
            .{$this->Name} .table-arrange{
                direction: ltr;
            }
            .{$this->Name} .table-arrange tr td>*{
                max-width: 100%;
                overflow: hidden;
                line-height: 1.2em;
                overflow-wrap: anywhere;
            }
            .{$this->Name} .table-arrange .item{
                padding: calc(var(--size-0) / 4) calc(var(--size-0) / 2);
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
                            $this->GoBackScript(),
                            ["class" => "be flex middle center parent"]
                        ) . " "
                    ] : []),
                    Struct::Icon("download", $this->UploadScript(), ["tooltip" => "Upload a new file"]),
                    Struct::Icon("folder", $this->CreatefolderScript(), ["tooltip" => "Create a new folder"]),
                    Struct::Icon("file", $this->CreateFileScript(), ["tooltip" => "Create a new file"]),
                    Struct::Icon("check-circle", $this->SelectAllScript(), ["tooltip" => "Select all"]),
                    Struct::Icon("circle", $this->DeselectAllScript(), ["tooltip" => "Deselect all"]),
                ], ["class" => "be align start flex col-sm"]) .
                Struct::Division([
                    Struct::Icon("refresh", $this->GoScript(), ["tooltip" => "Reload the page"]),
                    Struct::Icon("list", $this->SendScript("?path=" . urlencode($this->CurrentAddress) . "&arrange=table"), $this->Arrange === "table" ? ["class" => "hidden"] : []),
                    Struct::Icon("th", $this->SendScript("?path=" . urlencode($this->CurrentAddress) . "&arrange=items"), $this->Arrange === "items" ? ["class" => "hidden"] : [])
                ], ["class" => "be align end col-sm col-sm-2"])
            ], ["class" => "toolbar"]) . $items . Struct::Script(
                    "
            function {$this->MainName}_Select(e, item) {" . ($this->MultiSelect ? "
                if(e.shiftKey) {
                    isch = false;
                    _('.{$this->Name} .item').each(function(it){
                        if(isch === null) return;
                        else if(it === item) {
                            if(isch) isch = null;
                            //_(item).matches('[name=\"Path\"]').toggleAttr('checked');
                        }
                        else if(_(it).matches('[name=\"Path\"]').checked) isch = true;
                        else if(isch) _(it).matches('[name=\"Path\"]').toggleAttr('checked');
                    });
                    _(item).matches('[name=\"Path\"]').toggleAttr('checked');
                } else if(e.ctrlKey) {
                    _(item).matches('[name=\"Path\"]').toggleAttr('checked');
                } else {
                    _('.{$this->Name} .item [name=\"Path\"]').removeAttr('checked');
                    _(item).matches('[name=\"Path\"]').addAttr('checked');
                }
            " : "
                _('.{$this->Name} .item [name=\"Path\"]').removeAttr('checked');
                _(item).matches('[name=\"Path\"]').addAttr('checked');
            ") . "}
            _('.{$this->Name} .item').on('click', {$this->MainName}_Select);
            _('.{$this->Name} .item').on('contextmenu', {$this->MainName}_Select);

            {$this->MainName}_CurrentAddress = " . Script::Convert(urlencode($this->CurrentAddress)) . ";
        "
                );
    }

    public function GetScript()
    {
        $successScript = "(d,e)=>{if(d) \$('.{$this->Name}').replaceWith(d); else alert(e);}";
        return parent::GetScript() . Struct::Script(
            "
            _(document).on('keydown', function(e){
                if(e.target.tagName.toLowerCase() !== 'input' && e.target.tagName.toLowerCase() !== 'textarea'){
                    switch(e.key.toLowerCase()){
                        case 'f4':
                            " . $this->PropertiesScript() . ";
                            e.preventDefault();
                            break;
                        case 'f5':
                            " . $this->GoScript() . ";
                            e.preventDefault();
                            break;
                        case 'f6':
                            reload();
                            e.preventDefault();
                            break;
                        case 'f7':
                            " . $this->CopyRelativeUrlScript() . ";
                            e.preventDefault();
                            break;
                        case 'f8':
                            " . $this->CopyAbsoluteUrlScript() . ";
                            e.preventDefault();
                            break;
                        case 'enter':
                            " . $this->OpenScript() . ";
                            e.preventDefault();
                            break;
                        case 'backspace':
                            " . $this->GoBackScript() . ";
                            e.preventDefault();
                            break;
                        case 'a':
                            if(e.ctrlKey && e.shiftKey) {
                                " . $this->DeselectAllScript() . ";
                                e.preventDefault();
                            }
                            else if(e.ctrlKey) {
                                " . $this->SelectAllScript() . ";
                                e.preventDefault();
                            }
                            break;
                        case 'd':
                            if(e.ctrlKey) {
                                " . $this->DownloadScript() . ";
                                e.preventDefault();
                            }
                            break;" . (\_::$User->HasAccess($this->ModifyAccess) ? "
                        case 'f2':
                            " . $this->RenameScript() . ";
                            e.preventDefault();
                            break;
                        case 'delete':
                            " . $this->DeleteScript() . ";
                            e.preventDefault();
                            break;
                        case 'c':
                            if(e.ctrlKey && e.shiftKey) {
                                " . $this->CompressScript() . ";
                                e.preventDefault();
                            }
                            else if(e.ctrlKey) {
                                " . $this->CopyScript() . ";
                                e.preventDefault();
                            }
                            break;
                        case 'e':
                            if(e.ctrlKey && e.shiftKey) {
                                " . $this->ExtractScript() . ";
                                e.preventDefault();
                            }
                            break;
                        case 'n':
                            if(e.ctrlKey) {
                                " . $this->CreateFolderScript() . ";
                                e.preventDefault();
                            }
                            break;
                        case 'u':
                            if(e.ctrlKey) {
                                " . $this->UploadScript() . ";
                                e.preventDefault();
                            }
                            break;
                        case 'v':
                            if(e.ctrlKey) {
                                " . $this->PasteScript() . ";
                                e.preventDefault();
                            }
                            break;
                        case 'x':
                            if(e.ctrlKey) {
                                " . $this->CutScript() . ";
                                e.preventDefault();
                            }
                            break;" : "") . "
                    }
                }
            });

            InternalClipboard = [];
            InternalClipboardMode = 'copy';

            function {$this->MainName}_CurrentUrl(){
                return '?path='+{$this->MainName}_CurrentAddress+'&arrange={$this->Arrange}';
            }
            function {$this->MainName}_Send(path = null, data = null){
                " . Script::Send(
                    $this->Method,
                    "\${(path ?? {$this->MainName}_CurrentUrl())+'&class={$this->Name}'}",
                    "\${data}",
                    ".{$this->Name}",
                    $successScript,
                ) . "
            }

            function {$this->MainName}_Go(path = null, data = null){
                {$this->MainName}_Send(path ? '? path=' + encodeURIComponent(path) + '&arrange={$this->Arrange}' : {$this->MainName}_CurrentUrl(), data);
            }

            function {$this->MainName}_SelectAll(){
                _('.{$this->Name} .item [name=\"Path\"]').each(function(it){
                    _(it).addAttr('checked');
                });
            }
            function {$this->MainName}_DeselectAll(){
                _('.{$this->Name} .item [name=\"Path\"]').each(function(it){
                    _(it).removeAttr('checked');
                });
            }

            
            function {$this->MainName}_SelectedPath(tag){
                return _(tag).matches('input[name=\"Path\"]:checked').val();
            }
            function {$this->MainName}_SelectedPaths(sourceSelector = null){
                sourceSelector = sourceSelector ? sourceSelector : '.{$this->Name}';
                let paths = [];
                for(const item of _(sourceSelector+' .item input[name=\"Path\"]:checked'))
                    paths.push(item.value);
                return paths;
            }
            function {$this->MainName}_SelectedRelativeUrls(sourceSelector = null){
                urls = [];
                for(const path of {$this->MainName}_SelectedPaths(sourceSelector))
                    urls.push(" . Script::Convert($this->GetRelativeUrl($this->RootAddress)) . " + normalizeUrl(path.substring('" . addslashes($this->RootAddress) . "'.length)));
                return urls;
            }
            function {$this->MainName}_SelectedAbsoluteUrls(sourceSelector = null){
                urls = [];
                for(const path of {$this->MainName}_SelectedPaths(sourceSelector))
                    urls.push(" . Script::Convert($this->GetAbsoluteUrl($this->RootAddress)) . " + normalizeUrl(path.substring('" . addslashes($this->RootAddress) . "'.length)));
                return urls;
            }

            function {$this->MainName}_Open(sourceSelector = null){
                for(const path of {$this->MainName}_SelectedPaths(sourceSelector)) {$this->MainName}_Go(path);
            }

            function {$this->MainName}_Download(sourceSelector = null){
                for(const path of {$this->MainName}_SelectedAbsoluteUrls(sourceSelector))
                    load(path, true);
            }

            function {$this->MainName}_CopyRelativeUrl(sourceSelector = null){
                copy({$this->MainName}_SelectedRelativeUrls(sourceSelector).join('\\n'));
            }

            function {$this->MainName}_CopyAbsoluteUrl(sourceSelector = null){
                copy({$this->MainName}_SelectedAbsoluteUrls(sourceSelector).join('\\n'));
            }

            function {$this->MainName}_Properties(sourceSelector = null){
                paths = {$this->MainName}_SelectedPaths(sourceSelector);
                if(paths.length != 1){
                    alert('Please select a single item to view its properties.');
                    return;
                }
                {$this->MainName}_Go(paths[0], null, (d,e)=>{
                    if(d){
                        showModal('Properties', d, {class: 'large'});
                    } else {
                        alert(e);
                    }
                });
            }

            function normalizeUrl(url){
                return url
                .replace(/%/g,'/')
                .replace(/\\\\+/g,'/')
                .replace(/\\/+/g,'/')
                .replace(/\\+/g,'%2B')
                .replace(/ /g,'%20');
            }
        " . (\_::$User->HasAccess($this->ModifyAccess) ? "
            function {$this->MainName}_UploadFile(){
            " . Script::UploadDialog(
                        $this->AcceptedFormats,
                        "\${{$this->MainName}_CurrentUrl()+'&class={$this->Name}'}",
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

            function {$this->MainName}_Copy(sourceSelector = null){
                InternalClipboard = {$this->MainName}_SelectedPaths(sourceSelector);
                InternalClipboardMode = 'copy';
            }
            
            function {$this->MainName}_Cut(sourceSelector = null){
                InternalClipboard = {$this->MainName}_SelectedPaths(sourceSelector);
                InternalClipboardMode = 'cut';
            }
            
            function {$this->MainName}_Paste(sourceSelector = null){
                if(InternalClipboardMode == 'cut'){
                    {$this->MainName}_Go(null, " . Script::Convert(["paths" => "\${InternalClipboard}", "action" => "move"]) . ");
                } else {
                    {$this->MainName}_Go(null, " . Script::Convert(["paths" => "\${InternalClipboard}", "action" => "copy"]) . ");
                }
            }
            function {$this->MainName}_Rename(sourceSelector = null){
                paths = {$this->MainName}_SelectedPaths(sourceSelector);
                if(!paths.length){
                    alert('Please select at leaset a single item to rename.');
                    return;
                }
                name = " . Script::Prompt('Input the new name:', '${(paths[0].match(/[^\/\\\]+[\/\\\]?$/)??["New Name"])[0].replace(/[\/\\\]+$/giu, "")}') . ";
                if(name) {$this->MainName}_Go(null, " . Script::Convert(["paths" => "\${paths}", "name" => "\${encodeURIComponent(name)}", "action" => "rename"]) . ");
            }

            function {$this->MainName}_Delete(sourceSelector = null){
                if(confirm('Are you sure to delete the selected items?')){
                    paths = {$this->MainName}_SelectedPaths(sourceSelector);
                    {$this->MainName}_Go(null, " . Script::Convert(["paths" => "\${paths}", "action" => "delete"]) . ");
                }
            }

            function {$this->MainName}_Compress(sourceSelector = null){
                paths = {$this->MainName}_SelectedPaths(sourceSelector);
                {$this->MainName}_Go(null, " . Script::Convert(["paths" => "\${paths}", "action" => "compress"]) . ");
            }
            
            function {$this->MainName}_Extract(sourceSelector = null){
                paths = {$this->MainName}_SelectedPaths(sourceSelector);
                {$this->MainName}_Go(null, " . Script::Convert(["paths" => "\${paths}", "action" => "extract"]) . ");
            }
        " : "")
        );
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
                        Struct::Division(
                            Struct::CheckInput("Path", $it["Path"], ["class" => "hidden"]) .
                            Struct::Span(
                                $it["IsDirectory"] ? Struct::Icon("folder", null, ["class" => "be fore yellow"]) : Struct::Icon("file", null, ["class" => "be fore blue"]),
                                null,
                                ["class" => "item-icon", "ondblclick" => $it["IsDirectory"] ? $this->GoScript($it["Path"]) : Script::Load($aurl, true)]
                            ) .
                            Struct::Span("\${{$it["Name"]}}", null, [
                                //"ondblclick" => $this->RenameScript(),
                                "class" => "title"
                            ]),
                            ["class" => "item"]
                        ) .
                        "}",
                        $it["Size"] ? Struct::Number(content: $it["Size"]) . "B" : null,
                        $it["IsDirectory"] ? Struct::Icon("folder-open", $this->GoScript($it["Path"])) : ("\${" . Struct::Icon("copy", Script::Copy($aurl)) . Struct::Link("\${" . Convert::ToExcerpt($url, 0, 50) . "}", $aurl, ["class" => "view md-hide", "target" => "_blank"]) . "}"),
                        $it["MimeType"],
                        Convert::ToShownDateTimeString($it["UpdateTime"])
                    ]);
                })
            ]) .
            $this->GetContextMenu(".table-arrange") .
            $this->GetItemsContextMenu(".table-arrange"),
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
                                //"ondblclick" => $this->RenameScript(),
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
            $this->GetContextMenu(".items-arrange") .
            $this->GetItemsContextMenu(".items-arrange"),
        ], ["class" => "items-arrange"]);
    }

    public function GetContextMenu($selector = null)
    {
        return Struct::ContextMenu([
            ...(\_::$User->HasAccess($this->ModifyAccess) ? [
                Struct::Action(Struct::Division(Struct::Icon("paste") . Struct::Span("Paste")) . Struct::Division("Ctrl+V", ["class" => "shortcut-key"]), $this->PasteScript($selector)),
                Struct::$BreakLine,
                Struct::Action(Struct::Icon("upload") . Struct::Span("Upload a new file"), $this->UploadScript()),
                Struct::Action(Struct::Icon("folder") . Struct::Span("Create a new folder"), $this->CreatefolderScript()),
                Struct::Action(Struct::Icon("file") . Struct::Span("Create a new file"), $this->CreateFileScript()),
                Struct::$BreakLine,
            ] : []),
            Struct::Action(Struct::Icon("check-circle") . Struct::Span("Select All"), $this->SelectAllScript()),
            Struct::Action(Struct::Icon("circle") . Struct::Span("Deselect All"), $this->DeselectAllScript()),
            Struct::Action(Struct::Icon("refresh") . Struct::Span("Refresh"), $this->GoScript()),
        ], $selector);
    }
    public function GetItemsContextMenu($selector = null)
    {
        return Struct::ContextMenu([
            Struct::Action(Struct::Division(Struct::Icon("eye") . Struct::Span("Open")) . Struct::Division("Enter", ["class" => "shortcut-key"]), $this->OpenScript($selector)),
            ...(\_::$User->HasAccess($this->ModifyAccess) ? [
                Struct::Action(Struct::Division(Struct::Icon("copy") . Struct::Span("Copy")) . Struct::Division("Ctrl+C", ["class" => "shortcut-key"]), $this->CopyScript($selector)),
                Struct::Action(Struct::Division(Struct::Icon("cut") . Struct::Span("Cut")) . Struct::Division("Ctrl+X", ["class" => "shortcut-key"]), $this->CutScript($selector)),
                Struct::Action(Struct::Division(Struct::Icon("paste") . Struct::Span("Paste")) . Struct::Division("Ctrl+V", ["class" => "shortcut-key"]), $this->PasteScript($selector)),
                Struct::Action(Struct::Division(Struct::Icon("edit") . Struct::Span("Rename")) . Struct::Division("F2", ["class" => "shortcut-key"]), $this->RenameScript($selector)),
                Struct::Action(Struct::Division(Struct::Icon("trash") . Struct::Span("Delete")) . Struct::Division("Del", ["class" => "shortcut-key"]), $this->DeleteScript($selector)),
                Struct::$BreakLine,
                Struct::Action(Struct::Division(Struct::Icon("compress") . Struct::Span("Compress")) . Struct::Division("Ctrl+Shift+C", ["class" => "shortcut-key"]), $this->CompressScript()),
                Struct::Action(Struct::Division(Struct::Icon("expand") . Struct::Span("Extract")) . Struct::Division("Ctrl+Shift+E", ["class" => "shortcut-key"]), $this->ExtractScript()),
                Struct::Action(Struct::Division(Struct::Icon("upload") . Struct::Span("Download")) . Struct::Division("Ctrl+D", ["class" => "shortcut-key"]), $this->DownloadScript()),
                Struct::$BreakLine
            ] : []),
            Struct::Action(Struct::Division(Struct::Icon("link") . Struct::Span("Copy Relative URL")) . Struct::Division("F7", ["class" => "shortcut-key"]), $this->CopyRelativeUrlScript()),
            Struct::Action(Struct::Division(Struct::Icon("link") . Struct::Span("Copy Absolute URL")) . Struct::Division("F8", ["class" => "shortcut-key"]), $this->CopyAbsoluteUrlScript()),
            //Struct::Action(Struct::Division(Struct::Icon("info-circle") . Struct::Span("Properties")) . Struct::Division("F4", ["class" => "shortcut-key"]), $this->PropertiesScript()),
        ], $selector . " .item");
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
    public function GoBackScript()
    {
        return $this->GoScript(dirname(rtrim($this->CurrentAddress, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR);
    }
    public function UploadScript()
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
    public function SelectAllScript()
    {
        return "{$this->MainName}_SelectAll()";
    }
    public function DeselectAllScript()
    {
        return "{$this->MainName}_DeselectAll()";
    }

    public function OpenScript($selector = null)
    {
        return "{$this->MainName}_Open(" . Script::Convert($selector) . ")";
    }
    public function CopyScript($selector = null)
    {
        return "{$this->MainName}_Copy(" . Script::Convert($selector) . ")";
    }
    public function CutScript($selector = null)
    {
        return "{$this->MainName}_Cut(" . Script::Convert($selector) . ")";
    }
    public function PasteScript($selector = null)
    {
        return "{$this->MainName}_Paste(" . Script::Convert($selector) . ")";
    }
    public function RenameScript($selector = null)
    {
        return "{$this->MainName}_Rename(" . Script::Convert($selector) . ")";
    }
    public function DeleteScript($selector = null)
    {
        return "{$this->MainName}_Delete(" . Script::Convert($selector) . ")  ";
    }
    public function CompressScript($selector = null)
    {
        return "{$this->MainName}_Compress(" . Script::Convert($selector) . ")";
    }
    public function ExtractScript($selector = null)
    {
        return "{$this->MainName}_Extract(" . Script::Convert($selector) . ")";
    }
    public function DownloadScript($selector = null)
    {
        return "{$this->MainName}_Download(" . Script::Convert($selector) . ")";
    }
    public function CopyRelativeUrlScript($selector = null)
    {
        return "{$this->MainName}_CopyRelativeUrl(" . Script::Convert($selector) . ")";
    }
    public function CopyAbsoluteUrlScript($selector = null)
    {
        return "{$this->MainName}_CopyAbsoluteUrl(" . Script::Convert($selector) . ")";
    }
    public function PropertiesScript($selector = null)
    {
        return "{$this->MainName}_Properties(" . Script::Convert($selector) . ")";
    }

    public function Exclusive()
    {
        $received = receive($this->Method);
        if (\_::$User->HasAccess($this->ModifyAccess)) {
            $act = get($received, "action");
            if ($act) {
                switch (strtolower($act)) {
                    case "new-folder":
                        if (Local::CreateDirectory($this->CurrentAddress . Local::SanitizeName(urldecode(get($received, "name")))))
                            success("The folder created successfully!");
                        else
                            return error("Could not to create the folder!");
                        break;
                    case "new-file":
                        if (Local::CreateFile($this->CurrentAddress . Local::SanitizeName(urldecode(get($received, "name")))))
                            success("The file created successfully!");
                        else
                            return error("Could not to create the file!");
                        break;
                    case "rename":
                        $name = Local::SanitizeName(urldecode(get($received, "name")));
                        $extension = preg_find("/((\.[^.]*)|[\\/\\\])$/u", $name) ?? "";
                        $nameWOE = $extension ? substr($name, 0, -strlen($extension)) : $name;
                        $paths = get($received, "paths");
                        foreach ($paths as $path) {
                            if (!Local::Move($path, Local::GenerateAddress($nameWOE, $extension, dirname(rtrim($path, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR, false)))
                                return error("Could not to rename the '$name' item!");
                        }
                        break;
                    case "delete":
                        $paths = get($received, "paths");
                        foreach ($paths as $path) {
                            if (!Local::Delete($path))
                                return error("Could not to delete the '$path' item!");
                        }
                        break;
                    case "copy":
                        $paths = get($received, "paths");
                        foreach ($paths as $path) {
                            $name = basename($path);
                            if (!Local::Copy($path, $this->CurrentAddress . $name))
                                return error("Could not to copy the '$name' item!");
                        }
                        break;
                    case "move":
                        $paths = get($received, "paths");
                        foreach ($paths as $path) {
                            $name = basename($path);
                            if (!Local::Move($path, $this->CurrentAddress . $name))
                                return error("Could not to move the '$name' item!");
                        }
                        break;
                    case "compress":
                        $paths = get($received, "paths");
                        if (
                            Convert::ToZipFile(
                                $paths,
                                $this->CurrentAddress . ($name = "Archive_" . date("Ymd_His") . ".zip")
                            )
                        )
                            success("The '$name' compressed file is ready!");
                        else
                            return error("Could not to compress the selected items!");
                        break;
                    case "extract":
                        $paths = get($received, "paths");
                        foreach ($paths as $path)
                            if (
                                $arr = Convert::FromZipFile(
                                    $path,
                                    $this->CurrentAddress
                                )
                            )
                                success("All " . count($arr) . " files extracted successfully!");
                            else
                                return error("Could not to extracted the selected files!");
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
                        return error("Could not to upload the file!");
                } catch (\Exception $ex) {
                    error($ex);
                }
        }
        //$this->Router->Get()->Switch();
        //return $this->ToString();
        return $this->GetOpenTag() .
            $this->Get() .
            $this->GetCloseTag();
    }
}