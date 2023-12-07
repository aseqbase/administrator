<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
use MiMFa\Library\DataBase;
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."User");
$table1 = \_::$CONFIG->DataBasePrefix."UserGroup";
$mod->SelectQuery = "
    SELECT A.{$mod->ColumnKey}, B.Title AS 'Group', A.Image, A.Name, A.Bio, A.Signature, A.Email, A.Status, A.CreateTime
    FROM {$mod->Table} AS A
    LEFT OUTER JOIN $table1 AS B ON A.GroupID=B.ID;
";
$mod->RowLabelsKeys = ["Name", "Signature"];
$mod->ExcludeColumnKeys = ["MetaData"];
$mod->Updatable = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->CellTypes = [
    "ID"=>"number",
    "GroupID"=> function(){
        $std = new stdClass();
        $std->Title = "Group";
        $std->Type = "select";
        $std->Options = DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."UserGroup", "ID", "Title");
        return $std;
    },
    "Gender"=>["Male"=>"Male","Female"=>"Female","X"=>"X"],
    "Status"=>[-1=>"Blocked",0=>"Deactivated",1=>"Activated"],
    "Image"=>"image",
    "Bio"=>"strings",
    "Contact"=>"tel",
    "Email"=>"email",
    "Password"=>"password",
    "MetaData"=>"json",
    "CreateTime"=>function($t, $v){
        return getAccess(\_::$CONFIG->SuperAccess)?"datetime":(isValid($v)?"disabled":"hidden");
    },
    "UpdateTime"=>function($t, $v){
        $std = new stdClass();
        $std->Type = getAccess(\_::$CONFIG->SuperAccess)?"datetime":"hidden";
        $std->Value = \_::$CONFIG->GetFormattedDateTime();
        return $std;
    }
    ];
$mod->Draw();
?>