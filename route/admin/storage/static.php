<?php
 $routeHandler = function(){
    $module = new (module("Storage"))(
        \_::$Address->AssetDirectory,
        rtrim(array_values(\_::$Sequence)[\_::$Back->AdminOrigin==0?1:0], "\\\/").\_::$Address->AssetRootUrlPath
    );
    $module->ModifyAccess = \_::$User->AdminAccess;
    return $module->ToString();
};

(new Router())
->if(\_::$User->HasAccess(\_::$User->AdminAccess))
    ->Get(function () use($routeHandler) {
        (\_::$Front->AdministratorView)($routeHandler, [
            "Image" => "folder-tree",
            "Title" => "'Organized' 'Files' 'Management'"
        ]);
        
    })
    ->Default(fn()=>response($routeHandler()))
    ->Handle();