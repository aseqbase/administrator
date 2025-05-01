<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$module = new Table(\_::$Back->User->DataTable);
$table1 = \_::$Back->User->GroupDataTable->Name;
$module->SelectQuery = "
    SELECT A.{$module->KeyColumn}, B.Title AS 'Group', A.Image, A.Name, A.Bio, A.Signature, A.Email, A.Status, A.CreateTime
    FROM {$module->DataTable->Name} AS A
    LEFT OUTER JOIN $table1 AS B ON A.GroupId=B.Id;
";
$module->KeyColumns = ["Name" , "Signature" ];
$module->ExcludeColumns = ["Signature" , "MetaData" ];
$module->Updatable = true;
$module->AllowServerSide = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->CellsTypes = [
    "Id" =>"number",
    "GroupId" => function(){
        $std = new stdClass();
        $std->Title = "Group";
        $std->Type = "select";
        $std->Options = table("UserGroup")->SelectPairs("Id" , "Title" );
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
swap($module, $data);
$module->Render();
?>