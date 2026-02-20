<?php
$data = $data??[];
$routeHandler = function () use($data) {
    $module = new (module("Storage"))(
        dirname(\_::$Address->Directory).DIRECTORY_SEPARATOR,
        rtrim(array_values(\_::$Sequence)[\_::$Back->AdminOrigin==0?1:0], "\\\/")."/"
    );
    $module->ModifyAccess = \_::$User->SuperAccess;
    $module->LockSwitch = true;
    $module->AcceptableFormats = [];
    return $module->ToString();
};

(new Router())
->if(\_::$User->HasAccess(\_::$User->SuperAccess))
    ->Get(function () use($routeHandler) {
        (\_::$Front->AdministratorView)($routeHandler, [
            "Image" => "folder",
            "Title" => "'Root' 'Files' 'Management'"
        ]);
        
    })
    ->Default(fn()=>response($routeHandler()))
    ->Handle();
?>