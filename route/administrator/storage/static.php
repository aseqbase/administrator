<?php
$data = $data??[];
$routeHandler = function ($data) {
    $module = new (module("Storage"))(
        \_::$Address->AssetDirectory,
        rtrim(array_values(\_::$Sequence)[\_::$Back->AdminOrigin==0?1:0], "\\\/").\_::$Address->AssetRootUrlPath
    );
    $module->ModifyAccess = \_::$User->AdminAccess;
    $module->AcceptableFormats = [];
    return $module->ToString();
};

(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use($routeHandler) {
        (\_::$Front->AdminView)($routeHandler, [
            "Image" => "folder-tree",
            "Title" => "'Organized' 'Files' 'Management'"
        ]);
        
    })
    ->Default(fn()=>response($routeHandler($data)))
    ->Handle();