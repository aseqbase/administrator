<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Library\DataBase;
use MiMFa\Module\Table;
LIBRARY("Query");
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."Content");
$table1 = \_::$CONFIG->DataBasePrefix."User";
$mod->SelectQuery = "
    SELECT A.{$mod->KeyColumn}, A.Type, A.Image, A.Title, A.CategoryIDs AS 'Category', A.Priority, A.Status, A.Access, B.Name AS 'Author', C.Name AS 'Editor', A.CreateTime, A.UpdateTime
    FROM {$mod->Table} AS A
    LEFT OUTER JOIN $table1 AS B ON A.AuthorID=B.ID
    LEFT OUTER JOIN $table1 AS C ON A.EditorID=C.ID
    ORDER BY A.`Priority` DESC, A.`CreateTime` DESC
";
$mod->KeyColumns = ["Image", "Title"];
$mod->IncludeColumns = ['Type', 'Image', 'Title', 'Category', 'Priority', 'Status', 'Access', 'Author', 'Editor', 'CreateTime', 'UpdateTime'];
$mod->AllowServerSide = true;
$mod->Updatable = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$users = DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."User", "ID", "Name");
$mod->CellsValues = [
    "Category"=>function($v, $k, $r){
        $val = \MiMFa\Library\Query::GetCategoryDirection(first(\MiMFa\Library\Convert::FromJSON($v)));
        if(isValid($val)) return \MiMFa\Library\HTML::Link($val,"/cat".$val);
        return $v;
    }
];
$mod->CellsTypes = [
    "ID"=>getAccess(\_::$CONFIG->SuperAccess)?"disabled":false,
    "Name"=>"string",
    "Type"=>"enum",
    "Title"=>"string",
    "Image"=>"image",
    "Description"=>"strings",
    "Content"=>"content",
    "CategoryIDs"=> function(){
        $std = new stdClass();
        $std->Title = "Categories";
        $std->Type = "array";
        $std->Options = [
            "type"=>"select",
            "key"=>"CategoryIDs",
            "options"=>DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."Category", "`ID`", "`Name`", "TRUE ORDER BY `ParentID` ASC")
        ];
        return $std;
    },
    "TagIDs"=>function(){
        $std = new stdClass();
        $std->Title = "Tags";
        $std->Type = "array";
        $std->Options = [
            "type"=>"select",
            "key"=>"TagIDs",
            "options"=>DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."Tag", "`ID`", "`Name`")
        ];
        return $std;
    },
    "Status"=>[-1=>"Unpublished",0=>"Drafted",1=>"Published"],
    "Access"=>function(){
        $std = new stdClass();
        $std->Type="number";
        $std->Attributes=["min"=>\_::$CONFIG->BanAccess,"max"=>\_::$CONFIG->UserAccess];
        return $std;
    },
    "Attach"=>"json",
    "Path"=>"string",
    "Priority"=>"number",
    "AuthorID"=>function($t, $v) use($users){
        $std = new stdClass();
        $std->Title = "Author";
        $std->Type = getAccess(\_::$CONFIG->SuperAccess)?"select":"hidden";
        $std->Options = $users;
        if(!isValid($v)) $std->Value = \_::$INFO->User->ID;
        return $std;
    },
    "EditorID"=>function($t, $v) use($users){
        $std = new stdClass();
        $std->Title = "Editor";
        $std->Type = getAccess(\_::$CONFIG->SuperAccess)?"select":"hidden";
        $std->Options = $users;
        if(!isValid($v)) $std->Value = \_::$INFO->User->ID;
        return $std;
    },
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