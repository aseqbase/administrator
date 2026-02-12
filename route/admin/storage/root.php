<?php
$data = $data??[];
$routeHandler = function () use($data) {
    $module = new (module("Storage"))(
        \_::$Address->Directory,
        rtrim(array_values(\_::$Sequence)[\_::$Back->AdminOrigin==0?1:0], "\\\/").\_::$Address->RootUrlPath
    );
    $module->ModifyAccess = \_::$User->SuperAccess;
    $module->LockSwitch = true;
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