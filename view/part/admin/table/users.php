<?php
inspect(\_::$User->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Module\Table;
module("Table");
$module = new Table(\_::$User->DataTable);
$table1 = \_::$User->GroupDataTable->Name;
$module->SelectQuery = "
    SELECT A.{$module->KeyColumn}, B.Title AS 'Group', A.Signature, A.Image, A.Name, A.Bio, A.Email, A.Status, A.CreateTime
    FROM {$module->DataTable->Name} AS A
    LEFT OUTER JOIN $table1 AS B ON A.GroupId=B.Id
    WHERE B.Access<=".\_::$User->GetAccess();
$module->KeyColumns = ["Name" , "Signature" ];
$module->ExcludeColumns = ["MetaData" ];
$module->Updatable = true;
$module->AllowServerSide = true;
$module->UpdateAccess = \_::$User->AdminAccess;
$module->CellsTypes = [
    "Id" =>"number",
    "GroupId" => function(){
        $std = new stdClass();
        $std->Title = "Group";
        $std->Type = "select";
        $std->Options = table("UserGroup")->SelectPairs("Id" , "Title", "Access<=".\_::$User->GetAccess());
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
        $std->Type = \_::$User->GetAccess(\_::$User->SuperAccess)?"calendar":"hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function($t, $v){
        return \_::$User->GetAccess(\_::$User->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData" =>"json"
    ];
pod($module, $data);
$module->Render();
?>