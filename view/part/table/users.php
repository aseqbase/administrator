<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
use MiMFa\Library\DataBase;
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."User");
$table1 = \_::$CONFIG->DataBasePrefix."UserGroup";
$mod->SelectQuery = "
    SELECT A.{$mod->KeyColumn}, B.Title AS 'Group', A.Image, A.Name, A.Bio, A.Signature, A.Email, A.Status, A.CreateTime
    FROM {$mod->Table} AS A
    LEFT OUTER JOIN $table1 AS B ON A.GroupID=B.ID;
";
$mod->KeyColumns = ["Name", "Signature"];
$mod->ExcludeColumns = ["Signature", "MetaData"];
$mod->Updatable = true;
$mod->AllowServerSide = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->CellsTypes = [
    "ID"=>"number",
    "GroupID"=> function(){
        $std = new stdClass();
        $std->Title = "Group";
        $std->Type = "select";
        $std->Options = DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."UserGroup", "ID", "Title");
        return $std;
    },
    "Name"=>"string",
    "Image"=>"image",
    "Bio"=>"strings",
    "Email"=>"email",
    "Signature"=>"string",
    "Password"=>"password",
    "FirstName"=>"string",
    "MiddleName"=>"string",
    "LastName"=>"string",
    "Gender"=>"enum",
    "Contact"=>"tel",
    "Organization"=>"string",
    "Address"=>"string",
    "Path"=>"string",
    "Status"=>[-1=>"Blocked",0=>"Deactivated",1=>"Activated"],
    "UpdateTime"=>function($t, $v){
        $std = new stdClass();
        $std->Type = getAccess(\_::$CONFIG->SuperAccess)?"calendar":"hidden";
        $std->Value = \_::$CONFIG->GetFormattedDateTime();
        return $std;
    },
    "CreateTime"=> function($t, $v){
        return getAccess(\_::$CONFIG->SuperAccess)?"calendar":(isValid($v)?"hidden":false);
    },
    "MetaData"=>"json"
    ];
$mod->Draw();
?>