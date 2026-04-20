<?php

use MiMFa\Library\Internal;
use MiMFa\Library\Struct;
use MiMFa\Library\Storage;

$data = $data??[];
$routeHandler = function ($data) {
    $module = new (module("Storage"))(
        \_::$Address->TempDirectory,
        rtrim(array_values(\_::$Sequence)[\_::$Back->AdminOrigin==0?1:0], "\\\/").\_::$Address->TempRootUrlPath
    );
    $module->ModifyAccess = \_::$User->AdminAccess;
    $module->LockSwitch = false;
    $module->AppendToolsBar = [Struct::Icon("broom", "sendDelete()", ["class"=>"be fore red", "tooltip"=>"Reset all temporaries"])];
    $module->AcceptableFormats = [];
    return $module->ToString();
};

(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "clock",
            "Title" => "'Temporary' 'Files' 'Management'"
        ]);
        
    })
    ->Delete(fn()=>Storage::SetDirectory(\_::$Address->TempDirectory) || Storage::SetDirectory(Internal::$Directory)?deliverRedirect(Struct::Success("The temporary folder cleared successfully!")):warning("There is no changed!"))
    ->Default(fn()=>response($routeHandler($data)))
->Handle();