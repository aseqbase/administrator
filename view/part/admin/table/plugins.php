<?php
use MiMFa\Library\Script;
use MiMFa\Library\Struct;
auth(\_::$User->AdminAccess);

if($file = receiveFile()) try{
    $path = Script::Download($file, false, true);
    $file = new ZipArchive();
    $file->open($path);
    //$file->deleteName("composer.json");
    if($file->extractTo(\_::$Address->Address)){
        // table("Package")->Insert([
        //     "Paths"=>Convert::ToJson($file)
        // ]);
    }
    $file->close();
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