<?php
 $routeHandler = function(){
    $module = new (module("Storage"))(
        \_::$Address->PublicDirectory,
        rtrim(array_values(\_::$Sequence)[\_::$Back->AdminOrigin==0?1:0], "\\\/").\_::$Address->PublicRootUrlPath
    );
    $module->ModifyAccess = \_::$User->AdminAccess;
    $module->AcceptableFormats = [];
    return $module->ToString();
};

(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use($routeHandler) {
        (\_::$Front->AdministratorView)($routeHandler, [
            "Image" => "download",
            "Title" => "'Uploaded' 'Files' 'Management'"
        ]);
    })
    ->Default(fn()=>response($routeHandler()))
    ->Handle();