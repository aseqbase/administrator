<?php

use MiMFa\Library\Convert;
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
auth(\_::$User->AdminAccess);

if($file = receiveFile()) try{
    $path = Script::Download($file, binary:true);
    if($files = Convert::FromZipFile($path, \_::$Address->Address)){
        table("Package")->Insert([
            "Name"=>basename($file),
            "Paths"=>Convert::ToJson($files)
        ]);
    }
    return;
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
    Struct::Button(__("Upload a package").Struct::Icon("plus"), Script::Upload([".zip"], binary:true))
]));
$module->Render();
?>