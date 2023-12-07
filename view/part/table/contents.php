<?php
ACCESS(\_::$CONFIG->AdminAccess);
use MiMFa\Module\Table;
use MiMFa\Library\DataBase;
MODULE("Table");
$mod = new Table(\_::$CONFIG->DataBasePrefix."Content");
$table1 = \_::$CONFIG->DataBasePrefix."User";
$table2 = \_::$CONFIG->DataBasePrefix."Category";
$mod->SelectQuery = "
    SELECT A.{$mod->ColumnKey}, D.Title AS 'Category', A.Type, A.Image, A.Title, A.Description, A.Status, A.Access, B.Name AS 'Author', C.Name AS 'Editor', A.UpdateTime
    FROM {$mod->Table} AS A
    LEFT OUTER JOIN $table1 AS B ON A.AuthorID=B.ID
    LEFT OUTER JOIN $table1 AS C ON A.EditorID=C.ID
    LEFT OUTER JOIN $table2 AS D ON A.CategoryID=D.ID
";
$mod->RowLabelsKeys = ["Image", "Title"];
$mod->IncludeColumnKeys = ['Category', 'Type', 'Image', 'Title', 'Description', 'Status', 'Access', 'Author', 'Editor', 'UpdateTime'];
$mod->Updatable = true;
$mod->UpdateAccess = \_::$CONFIG->AdminAccess;
$mod->CellTypes = [
    "AuthorID"=>function($t, $v){
        $std = new stdClass();
        $std->Type = getAccess(\_::$CONFIG->SuperAccess)?"number":"hidden";
        if(!isValid($v)) $std->Value = \_::$INFO->User->ID;
        return $std;
    },
    "EditorID"=>function($t, $v){
        $std = new stdClass();
        $std->Type = getAccess(\_::$CONFIG->SuperAccess)?"number":"hidden";
        $std->Value = \_::$INFO->User->ID;
        return $std;
    },
    "ID"=>getAccess(\_::$CONFIG->SuperAccess)?"disabled":false,
    "CategoryID"=> function(){
        $std = new stdClass();
        $std->Title = "Category";
        $std->Type = "select";
        $std->Options = DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."Category", "ID", "Title");
        return $std;
    },
    "GroupIDs"=>function(){
        $std = new stdClass();
        $std->Title = "Groups";
        $std->Type = "array";
        $std->Options = [
            "type"=>"select",
            "options"=>DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."Group", "ID", "Title")
        ];
        return $std;
    },
    "TagIDs"=>function(){
        $std = new stdClass();
        $std->Title = "Tags";
        $std->Type = "array";
        $std->Options = [
            "type"=>"select",
            "options"=>DataBase::DoSelectPairs(\_::$CONFIG->DataBasePrefix."Tag", "ID", "Title")
        ];
        return $std;
    },
    "Type"=>['Post','Text','Image','Animation','Video','Audio','File','Service','Product','News','Article','Book','Collection','Course','Query','Form','Advertisement'],
    "Status"=>[-1=>"Unpublished",0=>"Drafted",1=>"Published"],
    "Image"=>"image",
    "Access"=>function(){
        $std = new stdClass();
        $std->Type="number";
        $std->Attributes=["min"=>\_::$CONFIG->BanAccess,"max"=>\_::$CONFIG->UserAccess];
        return $std;
    },
    "Description"=>"strings",
    "Content"=>"content",
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