<?php
namespace MiMFa\Module;

use MiMFa\Library\Convert;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
use MiMFa\Template\Message;

/**
 * To manage the storage
 *@copyright All rights are reserved for MiMFa Development Group
 *@author Mohammad Fathi
 *@see https://aseqbase.ir, https://github.com/aseqbase/aseqbase
 *@link https://github.com/aseqbase/aseqbase/wiki/Modules See the Documentation
 */
class Storage extends Module
{
    public string|null $Arrange = "items";
    public string $ArrangeRequest = "arrange";
    public string|null $Method = "Storage";
    public array|null $AcceptableFormats = null;
    public int|null $MinFileSize = null;
    public int|null $MaxFileSize = null;
    /**
     * The upload speed in Byte Per Second
     * @var int|null
     */
    public int|null $Speed = null;
    public readonly string $RootDirectory;
    public readonly string $RootAddress;
    public readonly string $CurrentDirectory;
    public readonly string $CurrentAddress;
    public bool $AllowStreamManagement = true;
    public bool $MultiSelect = true;
    public string $LockRequest = "lock";
    public bool|null $Lock = false;
    public int|bool|null $ModifyAccess = true;

    public function __construct($rootDirectory, $rootAddress)
    {
        parent::__construct();
        $this->RootDirectory = \MiMFa\Library\Storage::GetAbsolutePath($rootDirectory);
        $this->RootAddress = \MiMFa\Library\Storage::GetAbsoluteUrl($rootAddress);

        $this->Arrange = receiveGet($this->ArrangeRequest) ?? getMemo($this->ArrangeRequest) ?? $this->Arrange;
        $this->Lock = boolval(receiveGet($this->LockRequest) ?? getMemo($this->LockRequest)) ?? $this->Lock;

        $this->MainClass = receiveGet("Class") ?? $this->MainClass;
        $p = receiveGet("Path");
        if ($p && startsWith($p = \MiMFa\Library\Storage::GetAbsolutePath($p), $rootDirectory)) {
            $this->CurrentDirectory = $p;
            $this->CurrentAddress = \MiMFa\Library\Storage::GetUrl($rootAddress . substr($p, strlen($rootDirectory)));
        } else {
            $this->CurrentDirectory = $this->RootDirectory;
            $this->CurrentAddress = $this->RootAddress;
        }
        $this->Router->Set($this->Method, fn() => $this->Exclusive());
    }

    public function GetStyle()
    {
        yield parent::GetStyle();
        yield Struct::Style("
            .{$this->MainClass} .toolbar{
                border-bottom: var(--border-1);
                margin-bottom: 0px;
            }
            .{$this->MainClass} .toolbar>*>*{
                padding: calc(var(--size-0) / 2) var(--size-0);
                display: inline-block;
            }
            .{$this->MainClass} .toolbar .parent:hover{
                cursor:pointer;
                background-color: #8882;
            }
            #{$this->MainClass}_progress{
                background-color: transparent;
                color: var(--fore-color-output-special);
                box-shadow: none;
                text-shadow: none;
                border-bottom: none;
                display: block;
                width: 100%;
                height: 4px;
                margin-top: 0px;
                margin-bottom: var(--size-0);
            }    
            .{$this->MainClass} .item.cutted{
                opacity: 0.5;
            }

            .{$this->MainClass} .items-arrange .item{
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
            .{$this->MainClass} .items-arrange .item:hover{
                background-color: #8882;
            }
            .{$this->MainClass} .items-arrange .item .title{
                text-align: center;
                font-size: var(--size-1);
                line-height: 1.2em;
                overflow-wrap: anywhere;
            }
            .{$this->MainClass} .items-arrange .item:has(input[name=\"Path\"]:checked){
                background-color: rgba(121, 159, 241, 0.2);
            }
            .{$this->MainClass} .table-arrange tr td>*{
                max-width: 100%;
                overflow: hidden;
                line-height: 1.2em;
                overflow-wrap: anywhere;
            }
            .{$this->MainClass} .table-arrange :is(tr, tr td){
                padding: 0px;
            }
            .{$this->MainClass} .table-arrange .item{
                padding: calc(var(--size-0) / 2) calc(var(--size-0) / 2);
            }
            .{$this->MainClass} .table-arrange .item .title{
                text-align: center;
            }
            .{$this->MainClass} .table-arrange .item-icon{
                padding: calc(var(--size-0) / 4) calc(var(--size-0) / 2);
                aspect-ratio: 1;
                margin-inline-end: var(--size-0);
                border-radius: var(--radius-max);
            }
            .{$this->MainClass} .table-arrange tr:hover .item-icon{
                background-color: #8882;
            }
            .{$this->MainClass} .table-arrange tr:has(.item:hover){
                background-color: #8882;
            }
            .{$this->MainClass} .table-arrange tr:has(input[name=\"Path\"]:checked){
                background-color: rgba(121, 159, 241, 0.2);
            }
        ");
    }

    public function GetInner()
    {
        $modifyAccess = !$this->Lock && \_::$User->HasAccess($this->ModifyAccess);
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
        yield parent::GetInner();
        yield Struct::Division(
                Struct::Division([
                    ...($this->CurrentDirectory !== $this->RootDirectory ? [
                        Struct::Action(
                            Struct::Icon("folder-open", null, ["class" => "be fore green"]) . " " .
                            (trim(preg_find("/[^\/\\\]+[\/\\\]$/u", $this->CurrentDirectory) ?? "", DIRECTORY_SEPARATOR)) .
                            " " . Struct::Icon("arrow-left"),
                            $this->GoBackScript(),
                            ["class" => "be flex middle center parent"]
                        ) . " "
                    ] : []),
                    ...($modifyAccess ? [
                        Struct::Icon("download", $this->UploadScript(), ["tooltip" => "Upload a new file"]),
                        Struct::Icon("folder", $this->CreatefolderScript(), ["tooltip" => "Create a new folder"]),
                        Struct::Icon("file", $this->CreateFileScript(), ["tooltip" => "Create a new file"]),
                    ] : []),
                    Struct::Division("", ["class" => "selected-details multi-selected", "style" => "margin:0px; padding: calc(var(--size-0) / 2) var(--size-0); color: var(--color-gray);"]),
                    ...($modifyAccess ? [
                        Struct::Icon("copy", $this->CopyScript(), ["tooltip" => "Copy selected items", "class" => "selected"]),
                        Struct::Icon("cut", $this->CutScript(), ["tooltip" => "Cut selected items", "class" => "selected"]),
                        Struct::Icon("paste", $this->PasteScript(), ["tooltip" => "Paste items here", "class" => "clipboard", "hidden" => "hidden"]),
                        Struct::Icon("edit", $this->RenameScript(), ["tooltip" => "Rename selected itema", "class" => "selected"]),
                        Struct::Icon("trash", $this->DeleteScript(), ["tooltip" => "Delete selected itema", "class" => "selected", "style" => "color: var(--color-red);"]),
                        Struct::Icon("archive", $this->CompressScript(), ["tooltip" => "Compress selected items", "class" => "selected"]),
                        Struct::Icon("folder-open", $this->ExtractScript(), ["tooltip" => "Extract selected items", "class" => "selected"]),
                        Struct::Icon("upload", $this->DownloadScript(), ["tooltip" => "Download selected items", "class" => "selected"]),
                        Struct::Icon("link", $this->CopyAbsoluteUrlScript(), ["tooltip" => "Copy absolute URL of selected items", "class" => "selected"]),
                        //Struct::Icon("info", $this->PropertiesScript(), ["tooltip" => "View properties of selected items", "class" => "selected"])
                    ] : []),
                ], ["class" => "be align start"]) .
                Struct::Division([
                    ...($this->MultiSelect ? [
                        //Struct::Icon("close", $this->DeselectAllScript(), ["tooltip" => "Deselect all", "class" => "multi-selected"]),
                        Struct::Icon("check-circle", $this->SelectAllScript(), ["tooltip" => "Select all"]),
                    ] : []),
                    Struct::Icon("refresh", $this->GoScript(), ["tooltip" => "Reload the page"]),
                    Struct::Icon("list", Script::SetMemo($this->ArrangeRequest, "table") . ";" . $this->GoScript(), $this->Arrange === "table" ? ["class" => "hidden"] : []),
                    Struct::Icon("th", Script::SetMemo($this->ArrangeRequest, "items") . ";" . $this->GoScript(), $this->Arrange === "items" ? ["class" => "hidden"] : []),
                    ...(
                        \_::$User->HasAccess($this->ModifyAccess) ? ($this->Lock ? [
                            Struct::Icon("lock", Script::SetMemo($this->LockRequest, "0", path:\_::$Address->UrlPath) . ";" . $this->GoScript())
                        ] : [
                            Struct::Icon("lock-open", Script::SetMemo($this->LockRequest, "1", path:\_::$Address->UrlPath) . ";" . $this->GoScript())
                        ]) : [])
                ], ["class" => "be align end"])
                ,
                ["class" => "toolbar be flex justify"]
            ) . Struct::ProgressBar(0, null, ["id" => "{$this->MainClass}_progress", "class" => "be invisible"]) . $items .
            $this->GetContextMenu(".{$this->MainClass}") .
            Struct::Script(
                "
                function {$this->Name}_SelectedChanged(){
                    var selectedCount = _('.{$this->MainClass} .item [name=\"Path\"]:checked').length;
                    
                    if(typeof InternalClipboard !== 'undefined' && InternalClipboard.length > 0)
                        _('.{$this->MainClass} .clipboard').removeAttr('hidden');
                    else _('.{$this->MainClass} .clipboard').addAttr('hidden');
                    
                    if(selectedCount > 0) _('.{$this->MainClass} .selected').removeClass('hidden');
                    else _('.{$this->MainClass} .selected').addClass('hidden');

                    if(selectedCount > 0) _('.{$this->MainClass} .not-selected').addClass('hidden');
                    else _('.{$this->MainClass} .not-selected').removeClass('hidden');

                    if(selectedCount > 1) _('.{$this->MainClass} .multi-selected').removeClass('hidden');
                    else _('.{$this->MainClass} .multi-selected').addClass('hidden');

                    if(selectedCount === 1) _('.{$this->MainClass} .single-selected').removeClass('hidden');
                    else _('.{$this->MainClass} .single-selected').addClass('hidden');

                    if(selectedCount > 1) _('.{$this->MainClass} .selected-details').text(selectedCount + ' " . __("items") . "');
                    else _('.{$this->MainClass} .selected-details').text('');
                }
                
                _('.{$this->MainClass} .item').on('click', function (e, item) {" . ($this->MultiSelect ? "
                    if(e.shiftKey) {
                        isch = false;
                        _('.{$this->MainClass} .item').reverse().each(function(it){
                            if(isch === null) return;
                            else if(it === item || _(it).contains(item))
                                if(isch) isch = null;
                                else isch = true;
                            else if(isch) _(it).matches('[name=\"Path\"]').toggleAttr('checked');
                            else if(_(it).matches('[name=\"Path\"]').checked) isch = true;
                        });
                        _(item).matches('[name=\"Path\"]').toggleAttr('checked');
                    } else if(e.ctrlKey) {
                        _(item).matches('[name=\"Path\"]').toggleAttr('checked');
                    } else {
                        _('.{$this->MainClass} .item [name=\"Path\"]').removeAttr('checked');
                        _(item).matches('[name=\"Path\"]').addAttr('checked');
                    }
                " : "
                    _('.{$this->MainClass} .item [name=\"Path\"]').removeAttr('checked');
                    _(item).matches('[name=\"Path\"]').addAttr('checked');
                ") . "
                    {$this->Name}_SelectedChanged();
                });

                _('.{$this->MainClass} .item').on('contextmenu', function (e, item) {
                    if(_(item).matches('[name=\"Path\"]:checked').length < 1) {
                        _('.{$this->MainClass} .item [name=\"Path\"]').removeAttr('checked');
                        _(item).matches('[name=\"Path\"]').addAttr('checked');
                    }
                " . ($this->MultiSelect ? "
                    if(_('.{$this->MainClass} .item [name=\"Path\"]:checked').length <= 1) {
                        _('.{$this->MainClass} .item [name=\"Path\"]').removeAttr('checked');
                        _(item).matches('[name=\"Path\"]').addAttr('checked');
                    }
                " : "
                    _('.{$this->MainClass} .item [name=\"Path\"]').removeAttr('checked');
                    _(item).matches('[name=\"Path\"]').addAttr('checked');
                ") . "
                    {$this->Name}_SelectedChanged();
                    e.preventDefault();
                });

                {$this->Name}_CurrentDirectory = " . Script::Convert(urlencode($this->CurrentDirectory)) . ";
                
                {$this->Name}_SelectedChanged();
            "
            );
    }

    public function GetScript()
    {
        $modifyAccess = !$this->Lock && \_::$User->HasAccess($this->ModifyAccess);
        $successScript = "(d,e)=>{if(d) \$('.{$this->MainClass}').replaceWith(d); else if(e) alert(e); _('#{$this->MainClass}_progress').val(0).addClass('invisible');}";
		yield parent::GetScript();
		yield Struct::Script(
            "
            _(document).on('click', function(e){
                if(_(e.target).matches(' .item').length > 0){
                    _('.{$this->MainClass} .item [name=\"Path\"]').removeAttr('checked');
                    {$this->Name}_SelectedChanged();
                }
            });
            _('.{$this->MainClass}').on('contextmenu', function (e, item) {
                if(_(e.target).matches('.item').length > 0) {
                    _('.{$this->MainClass} .item [name=\"Path\"]').removeAttr('checked');
                }
                {$this->Name}_SelectedChanged();
            });
            _(document).on('keydown', function(e){
                if(e.target.tagName.toLowerCase() !== 'input' && e.target.tagName.toLowerCase() !== 'textarea'){
                    switch(e.key.toLowerCase()){
                        case 'f4':
                            " . $this->PropertiesScript() . ";
                            e.preventDefault();
                            break;
                        case 'f5':
                            if(!e.ctrlKey) {
                                " . $this->GoScript() . ";
                                e.preventDefault();
                            }
                            break;
                        case 'f6':
                            " . Script::Reload() . ";
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
                        case 'd':
                            if(e.ctrlKey) {
                                " . $this->DownloadScript() . ";
                                e.preventDefault();
                            }
                            break;" .
            ($this->MultiSelect ? "
                        case 'a':
                            if(e.ctrlKey && e.shiftKey) {
                                " . $this->DeselectAllScript() . ";
                                e.preventDefault();
                            }
                            else if(e.ctrlKey) {
                                " . $this->SelectAllScript() . ";
                                e.preventDefault();
                            }
                            break;" : "") .
            ($modifyAccess ? "
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
                            if(e.ctrlKey && e.shiftKey) {
                                " . $this->CreateFileScript() . ";
                                e.preventDefault();
                            }
                            else if(e.ctrlKey) {
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

            function {$this->Name}_CurrentAddress(){
                return '?path='+{$this->Name}_CurrentDirectory;
            }
            function {$this->Name}_Send(path = null, data = null){
                " . Script::Send(
                    $this->Method,
                    "\${(path ?? {$this->Name}_CurrentAddress())+'&class={$this->MainClass}'}",
                    "\${data}",
                    ".{$this->MainClass}",
                    onSuccessScript: $successScript
                ) . "
            }

            function {$this->Name}_Go(path = null, data = null){
                {$this->Name}_Send(path ? '? path=' + encodeURIComponent(path) : {$this->Name}_CurrentAddress(), data);
            }

            function {$this->Name}_SelectAll(){
                _('.{$this->MainClass} .item [name=\"Path\"]').each(function(it){
                    _(it).addAttr('checked');
                    {$this->Name}_SelectedChanged();
                });
            }
            function {$this->Name}_DeselectAll(){
                _('.{$this->MainClass} .item [name=\"Path\"]').each(function(it){
                    _(it).removeAttr('checked');
                    {$this->Name}_SelectedChanged();
                });
            }

            
            function {$this->Name}_SelectedPath(tag){
                return _(tag).matches('input[name=\"Path\"]:checked').val();
            }
            function {$this->Name}_SelectedPaths(sourceSelector = null){
                sourceSelector = sourceSelector ? sourceSelector : '.{$this->MainClass} .item';
                let paths = [];
                for(const item of _(sourceSelector+' input[name=\"Path\"]:checked'))
                    paths.push(item.value);
                return paths;
            }
            function {$this->Name}_SelectedRelativeUrls(sourceSelector = null){
                urls = [];
                for(const path of {$this->Name}_SelectedPaths(sourceSelector))
                    urls.push(" . Script::Convert($this->GetRelativeUrl($this->RootDirectory)) . " + normalizeUrl(path.substring('" . addslashes($this->RootDirectory) . "'.length)));
                return urls;
            }
            function {$this->Name}_SelectedAbsoluteUrls(sourceSelector = null){
                urls = [];
                for(const path of {$this->Name}_SelectedPaths(sourceSelector))
                    urls.push(" . Script::Convert($this->GetAbsoluteUrl($this->RootDirectory)) . " + normalizeUrl(path.substring('" . addslashes($this->RootDirectory) . "'.length)));
                return urls;
            }

            function {$this->Name}_Open(sourceSelector = null){
                for(const path of {$this->Name}_SelectedPaths(sourceSelector)) {$this->Name}_Go(path);
            }

            function {$this->Name}_Download(sourceSelector = null){
                for(const path of {$this->Name}_SelectedAbsoluteUrls(sourceSelector))
                    load(path, true);
            }

            function {$this->Name}_CopyRelativeUrl(sourceSelector = null){
                copy({$this->Name}_SelectedRelativeUrls(sourceSelector).join('\\n'));
            }

            function {$this->Name}_CopyAbsoluteUrl(sourceSelector = null){
                copy({$this->Name}_SelectedAbsoluteUrls(sourceSelector).join('\\n'));
            }

            function {$this->Name}_Properties(sourceSelector = null){
                paths = {$this->Name}_SelectedPaths(sourceSelector);
                if(paths.length != 1){
                    alert('Please select a single item to view its properties.');
                    return;
                }
                {$this->Name}_Go(paths[0], null, (d,e)=>{
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
        " . ($modifyAccess ? "
            function {$this->Name}_UploadFile(){
            " . Script::UploadStream(
                        target: "\${{$this->Name}_CurrentAddress()+'&class={$this->MainClass}'}",
                        extensions: $this->AcceptableFormats,
                        minSize: $this->MinFileSize,
                        maxSize: $this->MaxFileSize,
                        speed: $this->Speed,
                        multiple: true,
                        onSuccessScript: $successScript
                    ) . "
            }

            function {$this->Name}_CreateFolder(name){
                if(name || (name = " . Script::Prompt('Input the new folder`s name:', 'New Folder') . ")) {
                    {$this->Name}_Go(null, " . Script::Convert(["name" => "\${encodeURIComponent(name)}", "action" => "new-folder"]) . ");
                }
            }

            function {$this->Name}_CreateFile(name){
                if(name || (name = " . Script::Prompt('Input the new file`s name:', 'New File.txt') . ")) {
                    {$this->Name}_Go(null, " . Script::Convert(["name" => "\${encodeURIComponent(name)}", "action" => "new-file"]) . ");
                }
            }

            function {$this->Name}_Copy(sourceSelector = null){
                sourceSelector = sourceSelector ? sourceSelector : '.{$this->MainClass} .item';
                _(sourceSelector).removeClass('cutted');
                InternalClipboard = {$this->Name}_SelectedPaths(sourceSelector);
                InternalClipboardMode = 'copy';
            }
            
            function {$this->Name}_Cut(sourceSelector = null){
                sourceSelector = sourceSelector ? sourceSelector : '.{$this->MainClass} .item';
                _(sourceSelector).removeClass('cutted');
                InternalClipboard = {$this->Name}_SelectedPaths(sourceSelector);
                InternalClipboardMode = 'cut';
                _(sourceSelector+':has(input[name=\"Path\"]:checked)').addClass('cutted');
            }
            
            function {$this->Name}_Paste(sourceSelector = null){
                if(InternalClipboardMode == 'cut'){
                    {$this->Name}_Go(null, " . Script::Convert(["paths" => "\${InternalClipboard}", "action" => "move"]) . ");
                    InternalClipboard = [];
                    InternalClipboardMode = null;
                } else if(InternalClipboardMode == 'copy') {
                    {$this->Name}_Go(null, " . Script::Convert(["paths" => "\${InternalClipboard}", "action" => "copy"]) . ");
                }
            }
            function {$this->Name}_Rename(sourceSelector = null){
                paths = {$this->Name}_SelectedPaths(sourceSelector);
                if(!paths.length){
                    alert('Please select at leaset a single item.');
                    return;
                }
                name = " . Script::Prompt('Input the new name:', '${(paths[0].match(/[^\/\\\]+[\/\\\]?$/)??["New Name"])[0].replace(/[\/\\\]+$/giu, "")}') . ";
                if(name) {$this->Name}_Go(null, " . Script::Convert(["paths" => "\${paths}", "name" => "\${encodeURIComponent(name)}", "action" => "rename"]) . ");
            }

            function {$this->Name}_Delete(sourceSelector = null){
                if(confirm('Are you sure to delete the selected items?')){
                    paths = {$this->Name}_SelectedPaths(sourceSelector);
                    if(!paths.length){
                        alert('Please select at leaset a single item.');
                        return;
                    }
                    {$this->Name}_Go(null, " . Script::Convert(["paths" => "\${paths}", "action" => "delete"]) . ");
                }
            }

            function {$this->Name}_Compress(sourceSelector = null){
                paths = {$this->Name}_SelectedPaths(sourceSelector);
                if(!paths.length){
                    alert('Please select at leaset a single item.');
                    return;
                }
                name = " . Script::Prompt('Input the compresseed file name:', '${(paths[0].match(/[^\/\\\]+[\/\\\]?$/)??["New Name"])[0].replace(/[\/\\\]+$/giu, "")+".zip"}') . ";
                if(name) {$this->Name}_Go(null, " . Script::Convert(["paths" => "\${paths}", "name" => "\${encodeURIComponent(name)}", "action" => "compress"]) . ");
            }
            
            function {$this->Name}_Extract(sourceSelector = null){
                paths = {$this->Name}_SelectedPaths(sourceSelector);
                if(!paths.length){
                    alert('Please select at leaset a single item.');
                    return;
                }
                {$this->Name}_Go(null, " . Script::Convert(["paths" => "\${paths}", "action" => "extract"]) . ");
            }
        " : "")
        );
    }

    public function GetTableArrange()
    {
        return Struct::Division(
            Struct::Table([
                ["Name", "Size", "URL", "Type", "UpdateTime"],
                ...loop(\MiMFa\Library\Storage::GetDirectoryItems($this->CurrentDirectory), function ($it, $k, $i) {
                    $aurl = $this->GetAbsoluteUrl($it["Path"]);
                    $url = getUrlRequest($aurl);
                    return Struct::Row([
                        "\${" .
                        Struct::Division(
                            Struct::CheckInput("Path", $it["Path"], ["class" => "hidden"]) .
                            Struct::Span(
                                $it["IsDirectory"] ? Struct::Icon("folder", null, ["class" => "be fore yellow"]) : Struct::Icon("file", null, ["class" => "be fore blue"]),
                                null,
                                ["class" => "item-icon"]
                            ) .
                            Struct::Span("\${{$it["Name"]}}", null, [
                                //"ondblclick" => $this->RenameScript(),
                                "class" => "title"
                            ]),
                            ["class" => "item", "ondblclick" => $it["IsDirectory"] ? $this->GoScript($it["Path"]) : Script::Load($aurl, true)]
                        ) .
                        "}",
                        $it["Size"] ? Convert::ToCompactNumber($it["Size"] ?? 0) . "B" : null,
                        $it["IsDirectory"] ? Struct::Icon("folder-open", $this->GoScript($it["Path"])) : ("\${" . Struct::Icon("copy", Script::Copy($aurl)) . Struct::Link("\${" . Convert::ToExcerpt($url, 0, 50) . "}", $aurl, ["class" => "view md-hide", "target" => "_blank"]) . "}"),
                        $it["MimeType"],
                        Convert::ToShownDateTimeString($it["UpdateTime"])
                    ]);
                })
            ]),
            ["class" => "table-arrange"]
        );
    }
    public function GetItemsArrange()
    {
        return Struct::Division(loop(
            \MiMFa\Library\Storage::GetDirectoryItems($this->CurrentDirectory),
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
                                "Size: " . ($it["Size"] ? Convert::ToCompactNumber($it["Size"]) . "B" : null),
                                "Type: " . $it["MimeType"],
                                "Modified: " . Convert::ToShownDateTimeString($it["UpdateTime"]),
                            ], ["class" => "be align start"])
                        ]
                    ) .
                    Struct::CheckInput("Path", $it["Path"], ["class" => "hidden"]),
                    ["class" => "item", "ondblclick" => $it["IsDirectory"] ? $this->GoScript($it["Path"]) : Script::Load($aurl, true)]
                );
            }
        ), ["class" => "items-arrange"]);
    }

    public function GetContextMenu($selector = null)
    {
        $modifyAccess = !$this->Lock && \_::$User->HasAccess($this->ModifyAccess);
        $itemSelector = $selector ? "$selector .item" : ".{$this->MainClass} .item";
        return Struct::ContextMenu([
            Struct::Action(Struct::Division(Struct::Icon("eye") . Struct::Span("Open")) . Struct::Division("Enter", ["class" => "shortcut-key"]), $this->OpenScript($itemSelector), ["class" => "selected"]),
            ...($modifyAccess ? [
                Struct::Action(Struct::Division(Struct::Icon("copy") . Struct::Span("Copy")) . Struct::Division("Ctrl+C", ["class" => "shortcut-key"]), $this->CopyScript($itemSelector), ["class" => "selected"]),
                Struct::Action(Struct::Division(Struct::Icon("cut") . Struct::Span("Cut")) . Struct::Division("Ctrl+X", ["class" => "shortcut-key"]), $this->CutScript($itemSelector), ["class" => "selected"]),
                Struct::Action(Struct::Division(Struct::Icon("paste") . Struct::Span("Paste")) . Struct::Division("Ctrl+V", ["class" => "shortcut-key"]), $this->PasteScript($selector), ["class" => "clipboard"]),
                Struct::Action(Struct::Division(Struct::Icon("edit") . Struct::Span("Rename")) . Struct::Division("F2", ["class" => "shortcut-key"]), $this->RenameScript($itemSelector), ["class" => "selected"]),
                Struct::Action(Struct::Division(Struct::Icon("trash") . Struct::Span("Delete")) . Struct::Division("Del", ["class" => "shortcut-key"]), $this->DeleteScript($itemSelector), ["class" => "selected"]),
                Struct::Element(null, "hr", ["class" => "selected"]),
                Struct::Action(
                    Struct::Division(Struct::Icon("plus") . Struct::Span("New")) . Struct::Division(Struct::Icon("chevron-right"), ["class" => "shortcut-key"]) . Struct::ContextMenu([
                        Struct::Action(Struct::Division(Struct::Icon("plus") . Struct::Span("Upload a new file")) . Struct::Division("Ctrl+U", ["class" => "shortcut-key"]), $this->UploadScript(), ["class" => "not-selected"]),
                        Struct::Action(Struct::Division(Struct::Icon("folder-plus") . Struct::Span("Create a new folder")) . Struct::Division("Ctrl+N", ["class" => "shortcut-key"]), $this->CreatefolderScript(), ["class" => "not-selected"]),
                        Struct::Action(Struct::Division(Struct::Icon("file") . Struct::Span("Create a new file")) . Struct::Division("Ctrl+Shift+N", ["class" => "shortcut-key"]), $this->CreateFileScript(), ["class" => "not-selected"]),
                    ]),
                    null,
                    ["class" => "not-selected"]
                ),
            ] : []),
            Struct::Action(
                Struct::Division(Struct::Icon("link") . Struct::Span("Share")) . Struct::Division(Struct::Icon("chevron-right"), ["class" => "shortcut-key"]) . Struct::ContextMenu([
                    Struct::Action(Struct::Division(Struct::Icon("upload") . Struct::Span("Download")) . Struct::Division("Ctrl+D", ["class" => "shortcut-key"]), $this->DownloadScript($itemSelector)),
                    Struct::Action(Struct::Division(Struct::Icon("link-slash") . Struct::Span("Copy Relative URL")) . Struct::Division("F7", ["class" => "shortcut-key"]), $this->CopyRelativeUrlScript($itemSelector)),
                    Struct::Action(Struct::Division(Struct::Icon("link") . Struct::Span("Copy Absolute URL")) . Struct::Division("F8", ["class" => "shortcut-key"]), $this->CopyAbsoluteUrlScript($itemSelector)),
                ]),
                null,
                ["class" => "selected"]
            ),
            ...($modifyAccess ? [
                Struct::Element(null, "hr", ["class" => "selected"]),
                Struct::Action(Struct::Division(Struct::Icon("archive") . Struct::Span("Compress")) . Struct::Division("Ctrl+Shift+C", ["class" => "shortcut-key"]), $this->CompressScript($itemSelector), ["class" => "selected"]),
                Struct::Action(Struct::Division(Struct::Icon("folder-open") . Struct::Span("Extract")) . Struct::Division("Ctrl+Shift+E", ["class" => "shortcut-key"]), $this->ExtractScript($itemSelector), ["class" => "selected"]),
                Struct::Element(null, "hr", ["class" => "selected"]),
            ] : []),
            Struct::Action(
                Struct::Division(Struct::Icon("th-list") . Struct::Span("Arrange")) . Struct::Division(Struct::Icon("chevron-right"), ["class" => "shortcut-key"]) . Struct::ContextMenu([
                    Struct::Action(Struct::Division(Struct::Icon($this->Arrange === "table" ? "check" : "list") . Struct::Span("Table")) . Struct::Division("", ["class" => "shortcut-key"]), Script::SetMemo($this->ArrangeRequest, "table") . ";" . $this->GoScript()),
                    Struct::Action(Struct::Division(Struct::Icon($this->Arrange === "items" ? "check" : "th") . Struct::Span("Items")) . Struct::Division("", ["class" => "shortcut-key"]), Script::SetMemo($this->ArrangeRequest, "items") . ";" . $this->GoScript()),
                ]),
                null,
                ["class" => "not-selected"]
            ),
            ...($this->MultiSelect ? [
                Struct::Action(Struct::Division(Struct::Icon("check-circle") . Struct::Span("Select All")) . Struct::Division("Ctrl+A", ["class" => "shortcut-key"]), $this->SelectAllScript()),
                Struct::Action(Struct::Division(Struct::Icon("close") . Struct::Span("Deselect All")) . Struct::Division("Ctrl+Shift+A", ["class" => "shortcut-key"]), $this->DeselectAllScript(), ["class" => "multi-selected"]),
            ] : []),
            Struct::Action(Struct::Division(Struct::Icon("refresh") . Struct::Span("Refresh")) . Struct::Division("F5", ["class" => "shortcut-key"]), $this->GoScript(), ["class" => "not-selected"]),
            //Struct::Action(Struct::Division(Struct::Icon("info-circle") . Struct::Span("Properties")) . Struct::Division("F4", ["class" => "shortcut-key"]), $this->PropertiesScript(), ["class" => "single-selected"]),
        ], $selector);
    }

    public function GetAbsoluteUrl(string $path): string
    {
        return \MiMFa\Library\Storage::GetAbsoluteUrl($this->RootAddress . normalizeUrl(substr($path, strlen($this->RootDirectory))));
    }
    public function GetRelativeUrl(string $path): string
    {
        return getUrlRequest($this->GetAbsoluteUrl($path));
    }

    public function SendScript($path = null, $data = null)
    {
        return "{$this->Name}_Send(" . Script::Convert($path) . "," . Script::Convert($data) . ")";
    }
    public function GoScript($path = null, $data = null)
    {
        return "{$this->Name}_Go(" . Script::Convert($path) . "," . Script::Convert($data) . ")";
    }
    public function GoBackScript()
    {
        return $this->GoScript(dirname(rtrim($this->CurrentDirectory, DIRECTORY_SEPARATOR)) . DIRECTORY_SEPARATOR);
    }
    public function UploadScript()
    {
        return "{$this->Name}_UploadFile()";
    }
    public function CreateFolderScript($name = null)
    {
        return "{$this->Name}_CreateFolder(" . Script::Convert($name) . ")";
    }
    public function CreateFileScript($name = null)
    {
        return "{$this->Name}_CreateFile(" . Script::Convert($name) . ")";
    }
    public function SelectAllScript()
    {
        return "{$this->Name}_SelectAll()";
    }
    public function DeselectAllScript()
    {
        return "{$this->Name}_DeselectAll()";
    }

    public function OpenScript($selector = null)
    {
        return "{$this->Name}_Open(" . Script::Convert($selector) . ")";
    }
    public function CopyScript($selector = null)
    {
        return "{$this->Name}_Copy(" . Script::Convert($selector) . ")";
    }
    public function CutScript($selector = null)
    {
        return "{$this->Name}_Cut(" . Script::Convert($selector) . ")";
    }
    public function PasteScript($selector = null)
    {
        return "{$this->Name}_Paste(" . Script::Convert($selector) . ")";
    }
    public function RenameScript($selector = null)
    {
        return "{$this->Name}_Rename(" . Script::Convert($selector) . ")";
    }
    public function DeleteScript($selector = null)
    {
        return "{$this->Name}_Delete(" . Script::Convert($selector) . ")  ";
    }
    public function CompressScript($selector = null)
    {
        return "{$this->Name}_Compress(" . Script::Convert($selector) . ")";
    }
    public function ExtractScript($selector = null)
    {
        return "{$this->Name}_Extract(" . Script::Convert($selector) . ")";
    }
    public function DownloadScript($selector = null)
    {
        return "{$this->Name}_Download(" . Script::Convert($selector) . ")";
    }
    public function CopyRelativeUrlScript($selector = null)
    {
        return "{$this->Name}_CopyRelativeUrl(" . Script::Convert($selector) . ")";
    }
    public function CopyAbsoluteUrlScript($selector = null)
    {
        return "{$this->Name}_CopyAbsoluteUrl(" . Script::Convert($selector) . ")";
    }
    public function PropertiesScript($selector = null)
    {
        return "{$this->Name}_Properties(" . Script::Convert($selector) . ")";
    }

    public function Exclusive()
    {
        $modifyAccess = !$this->Lock && \_::$User->HasAccess($this->ModifyAccess);
        $received = receive($this->Method);
        if ($modifyAccess)
            try {
                $act = get($received, "action");
                if ($act) {
                    switch (strtolower($act)) {
                        case "new-folder":
                            if (\MiMFa\Library\Storage::CreateDirectory($this->CurrentDirectory . \MiMFa\Library\Storage::SanitizeName(urldecode(get($received, "name")))))
                                success("The folder created successfully!");
                            else
                                return error("Could not to create the folder!");
                            break;
                        case "new-file":
                            if (\MiMFa\Library\Storage::CreateFile($this->CurrentDirectory . \MiMFa\Library\Storage::SanitizeName(urldecode(get($received, "name")))))
                                success("The file created successfully!");
                            else
                                return error("Could not to create the file!");
                            break;
                        case "rename":
                            $name = urldecode(get($received, "name"));
                            $paths = get($received, "paths");
                            foreach ($paths as $path) {
                                if (!\MiMFa\Library\Storage::Rename($path, $name))
                                    error("Could not to rename the '$name' item!", null);
                            }
                            break;
                        case "delete":
                            $paths = get($received, "paths");
                            foreach ($paths as $path) {
                                if (!\MiMFa\Library\Storage::Delete($path))
                                    error("Could not to delete the '$path' item!", null);
                            }
                            break;
                        case "copy":
                            $paths = get($received, "paths");
                            foreach ($paths as $path) {
                                $name = basename(rtrim($path, "\\\/"));
                                $nPath = null;
                                if (($this->CurrentDirectory . $name) === $path)
                                    $nPath = \MiMFa\Library\Storage::GenerateUniquePath($this->CurrentDirectory, "Copy", "-" . $name);
                                elseif (startsWith($this->CurrentDirectory, $path))
                                    return error("You can not copy an item in itself!");
                                else
                                    $nPath = $this->CurrentDirectory . $name;
                                if (!\MiMFa\Library\Storage::Copy($path, $nPath))
                                    error("Could not to copy the '$name' item!", null);
                            }
                            break;
                        case "move":
                            $paths = get($received, "paths");
                            foreach ($paths as $path) {
                                if (startsWith($this->CurrentDirectory, $path))
                                    return warning("You item does not move in itself!");
                                $name = basename(rtrim($path, "\\\/"));
                                if (!\MiMFa\Library\Storage::Move($path, $this->CurrentDirectory . $name))
                                    error("Could not to move the '$name' item!", null);
                            }
                            break;
                        case "compress":
                            $name = urldecode(get($received, "name"));
                            $paths = get($received, "paths");
                            if (
                                $paths &&
                                ($arr = \MiMFa\Library\Storage::Compress(
                                    $this->CurrentDirectory . ($name = $name ?? ("archive-" . date("Y-m-d-H-i-s") . ".zip")),
                                    ...$paths
                                ))
                            )
                                success("There are " . count($arr) . " items compressed successfully in the '$name' file!");
                            else
                                return error("Could not to compress the selected items!");
                            break;
                        case "extract":
                            $paths = get($received, "paths");
                            foreach ($paths as $path)
                                if (
                                    $arr = \MiMFa\Library\Storage::Decompress(
                                        $path,
                                        $this->CurrentDirectory
                                    )
                                )
                                    success("All " . count($arr) . " files extracted successfully!");
                                else
                                    return error("Could not to extract the selected files!");
                            break;
                    }
                }
            } catch (\Exception $ex) {
                error($ex, null);
            }
        //$this->Router->Get()->Switch();
        //return $this->ToString();
        responseStatus(200);
        return $this->GetStruct();
    }

    public function Stream()
    {
        try {
            if (
                $progress = downloadStream(
                    $this->CurrentDirectory,
                    extensions: $this->AcceptableFormats,
                    minSize: $this->MinFileSize,
                    maxSize: $this->MaxFileSize,
                )
            ) {
                $name = receiveStream("name");
                $remain = (receiveStream("total") ?? 0) - (receiveStream("chunk") ?? 0) - 1;
                $id = Convert::ToId($name);
                if (is_string($progress))
                    success("The \"$name\" file uploaded successfully!");
                else if ($progress === false)
                    return error("Could not to upload the \"$name\" file!");
                else if ($this->AllowStreamManagement) {
                    if ($progress === true)
                        return response(Struct::Result("Please wait to upload the \"$name\" file!" . Struct::$Break . Struct::ProgressBar(null, null, ["class" => "be wide"]), "fa-spinner fa-spin", attributes: ["id" => $id, "class" => "view hide"]), 210);
                    else if ($remain <= 1)
                        return deliverProcedure("
                            _('#{$this->MainClass}_progress').addClass('invisible');
                            _('#$id').remove();
                        ");
                    else
                        return deliverProcedure("
                        _('#{$this->MainClass}_progress').val($progress).removeClass('invisible');
                        _('#$id').removeClass('hide');
                        _('#$id .progressbar').val($progress);
                        ");
                } else if ($remain <= 1)
                    return deliverProcedure("_('#{$this->MainClass}_progress').addClass('invisible');");
                else
                    return deliverProcedure("_('#{$this->MainClass}_progress').val($progress).removeClass('invisible');");
            }
            else return parent::Stream();
        } catch (\Exception $ex) {
            error($ex, null);
        }
        return $this->GetStruct();
    }
}