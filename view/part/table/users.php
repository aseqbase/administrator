<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$mod = new Table(\_::$Back->User->DataTable);
$table1 = \_::$Back->User->GroupDataTable->Name;
$mod->SelectQuery = "
    SELECT A.{$mod->KeyColumn}, B.Title AS 'Group', A.Image, A.Name, A.Bio, A.Signature, A.Email, A.Status, A.CreateTime
    FROM {$mod->DataTable->Name} AS A
    LEFT OUTER JOIN $table1 AS B ON A.GroupId=B.Id;
";
$mod->KeyColumns = ["Name" , "Signature" ];
$mod->ExcludeColumns = ["Signature" , "MetaData" ];
$mod->Updatable = true;
$mod->AllowServerSide = true;
$mod->UpdateAccess = \_::$Config->AdminAccess;
$mod->CellsTypes = [
    "Id" =>"number",
    "GroupId" => function(){
        $std = new stdClass();
        $std->Title = "Group";
        $std->Type = "select";
        $std->Options = table("UserGroup")->DoSelectPairs("Id" , "Title" );
        return $std;
    },
    "Name" =>"string",
    "Image" =>"image" ,
    "Bio" =>"strings",
    "Email"=>"email",
    "Signature" =>"string",
    "Password" =>"password",
    "FirstName"=>"string",
    "MiddleName"=>"string",
    "LastName"=>"string",
    "Gender" =>"enum",
    "Contact"=>"tel",
    "Organization"=>"string",
    "Address" =>"string",
    "Path" =>"string",
    "Status" =>[-1=>"Blocked",0=>"Deactivated",1=>"Activated"],
    "UpdateTime" =>function($t, $v){
        $std = new stdClass();
        $std->Type = auth(\_::$Config->SuperAccess)?"calendar":"hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function($t, $v){
        return auth(\_::$Config->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>"json"
    ];
$mod->Render();
?>