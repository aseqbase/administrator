<?php

use MiMFa\Module\Profile;
$path = \Req::$Page;
template("Main");
$templ = new \MiMFa\Template\Main();
if(!isValid($path))
    $templ->Content = function(){
        page("home");
    };
else
    $templ->Content = function() use($path){
        inspect(\_::$Config->UserAccess);
        module("Profile");
        $mod = new Profile(\_::$Back->User->GroupDataTable);
        $mod->KeyColumn = "Name";
        $mod->Updatable = false;
        $access = $mod->UpdateAccess = \_::$Config->AdminAccess;
        $mod->CellsTypes = [
            "Id" =>false,
            "Status" =>$access,
            "Path" =>$access,
            "MetaData" =>$access,
            "CreateTime" =>$access,
            "UpdateTime" =>$access,
            "Access" =>function(){
                $std = new stdClass();
                $std->Type="number";
                $std->Attributes=["min"=>\_::$Config->BanAccess,"max"=>\_::$Config->UserAccess];
                return $std;
            },
            "Image" =>"Image" ,
            "Description" =>"strings"
        ];
        $mod->SelectCondition = "`Id`=:Id";
        $mod->SelectParameters = [":Id"=>$path];
        $mod->Render();
    };
$templ->Render();
?>