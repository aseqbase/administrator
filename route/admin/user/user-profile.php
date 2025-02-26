<?php
use MiMFa\Module\Profile;
$path = \Req::$Page;
template("Main");
$templ = new \MiMFa\Template\Main();
if(!isValid($path))
    $templ->Content = function() {
        page("home");
    };
else $templ->Content = function() use($path) {
        module("Profile");
        $mod = new Profile(\_::$Back->User->DataTable);
        $mod->KeyColumn = "Signature";
        $table1 = \_::$Back->User->GroupDataTable->Name;
        $mod->SelectQuery = "
            SELECT A.{$mod->KeyColumn}, B.Title AS 'Group', A.Image, A.Name, A.Bio, A.Signature, A.Email, A.Status, A.CreateTime
            FROM {$mod->DataTable->Name} AS A
            WHERE A.Id=:Id
            LEFT OUTER JOIN $table1 AS B ON A.GroupId=B.Id;
        ";
        $mod->SelectParameters = [":Id"=>$path];
        $mod->Updatable = false;
        $access = $mod->UpdateAccess = \_::$Config->AdminAccess;
        $mod->CellsTypes = [
            "Id" =>false,
            "FirstName"=>$access,
            "MiddleName"=>$access,
            "LastName"=>$access,
            "Organization"=>$access,
            "Status" =>$access,
            "Contact"=>$access,
            "Address" =>$access,
            "Path" =>$access,
            "Email"=>$access,
            "Password" =>false,
            "GroupId" =>function() {
                $std = new stdClass();
                $std->Title = "Group";
                $std->Type = "select";
                $std->Options = table("UserGroup")->DoSelectPairs("Id" , "Title" );
                return $std;
            },
            "Gender" =>["Male"=>"Male","Female"=>"Female","X"=>"X"],
            "Image" =>"Image" ,
            "Bio" =>"strings",
            "UpdateTime" =>$access,
            "CreateTime" =>$access,
            "MetaData" =>$access
        ];
        $mod->Render();
    };
$templ->Render();
?>