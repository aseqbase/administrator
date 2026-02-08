<?php

use MiMFa\Library\Convert;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
auth(\_::$User->AdminAccess);

if($path = downloadStream()) try{
    if(is_string($path)){ 
        if($files = Convert::FromZipFile($path, \_::$Address->Address)){
            table("Package")->Insert([
                "Name"=>basename($received["name"]),
                //"Size"=>get($received, "size"),
                "Paths"=>Convert::ToJson($files)
            ]);
        }
    }
    elseif($path === false) error("There occurred a problem in extracting the package!");
    else return deliverProgress($path);
}catch(\Exception $ex){deliverError($ex);}

use MiMFa\Module\Table;
module("Table");
$module = new Table(table("Package"));
$module->AllowServerSide = true;
$module->Updatable = true;
$module->AddAccess = 
$module->ImportAccess = 
$module->RemoveAccess = 
$module->ModifyAccess = false;
$module->UpdateAccess = \_::$User->AdminAccess;
$module->PrependControlsCreator = function($id, $row) {
    return [Struct::Icon("trash")];
};
pod($module, $data);
response(Struct::Center([
    Struct::Button(__("Upload a package").Struct::Icon("plus"), Script::UploadStream(extensions: [".zip"]))
]));
$module->Render();
?>