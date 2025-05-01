<?php
inspect(\_::$Config->AdminAccess);
use MiMFa\Library\Convert;
use MiMFa\Library\Html;
use MiMFa\Library\Router;
use MiMFa\Library\Script;
use MiMFa\Module\Form;
use MiMFa\Module\Table;
module("Form");
$form = new Form();
$form->BlockTimeout = 2000;
$form->SuccessHandler = "Your reply message sent successfuly!";
(new Router())->Post(function() use(&$form) {
    $res = $form->Handle();
    if($form->Result) {
        $rec = \Req::ReceivePost();
        table("Comment")->Update("`Id`=:Id", [":Id"=>$rec["Id"], "Status"=>$rec["Status"], "Content"=>$rec["MailMessage"], "UpdateTime"=>\_::$Config->CurrentDateTime]);
        \Res::Flip($res);
    }
    else \Res::End($res);
})->Patch(function() use(&$form) {
    $r = \Req::ReceivePatch();
    $isadmin = \_::$Back->User->Access(\_::$Config->AdminAccess);
    $form->Set(
        title: "Reply to ".$r["Name"],
        method: "POST",
        children: [
            Html::Field("hidden", "Id", $r["Id"]),
            Html::Field("number", "Status", $r["Status"]<1?1:$r["Status"]+1, "To indicate how many reply sent them", "Reply Time"),
            Html::Field($isadmin?"email":"hidden", "SenderEmail", $isadmin?\_::$Info->ReceiverEmail:\_::$Back->User->Email, "Email sender", "From"),
            Html::Field("email", "ReceiverEmail",  $r["Contact"], "Email receiver", "To"),
            Html::Field("text", "MailSubject", "Reply to your message: ".between($r["Subject"], "in ".\_::$Info->Name), "Reply subject", "Subject"),
            Html::Field("content", "MailMessage", "Dear ".$r["Name"].","."\n\r\n\r\n\r".
            join("\n\r", [
                \_::$Back->User->MakeSign("Sincerely"),
                "",
                "On ".Convert::ToShownDateTimeString($r["CreateTime"])." ".$r["Name"]." &amp;lt;". $r["Contact"]."&amp;gt; wrote:",
                "\"\"", $r["Content"], "\"\""
            ]), "Reply answer", "Message")
        ]
    );
    $form->SenderEmail = \_::$Info->SenderEmail;
    $form->ReceiverEmail = $r["Contact"];
    $form->Image = "reply";
    $form->Template = "s";
    $form->Router->Get()->Switch();
    return \Res::End($form->ToString());
})->Handle();
if($form->Status) return;

module("Table");
$module = new Table(table("Comment"));
$module->SelectQuery = "
    SELECT *
    FROM {$module->DataTable->Name}
    WHERE Relation REGEXP '^[^0-9]'
    ORDER BY `CreateTime` DESC
";
$module->KeyColumns = ["Subject"];
$module->IncludeColumns = ["Name", "Relation", "Subject", "Content", "Contact", "Status" , "CreateTime"];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->UpdateAccess = \_::$Config->AdminAccess;
$module->CreateModal();
$module->AppendControlsCreator = function($id, $r) use($module){
    $st = intval($r["Status"]??0);
    $d = "sendPatch(null, {
        Id:".Script::Convert($r["Id"]).",
        Status:".Script::Convert($st = $st<0?0:$st).",
        Name:".Script::Convert($r["Name"]).",
        Contact:".Script::Convert($r["Contact"]).",
        Subject:".Script::Convert($r["Subject"]).",
        Content:".Script::Convert($r["Content"]).",
        CreateTime:".Script::Convert($r["CreateTime"])."
    }, 'form',
    (data, err) => {
        if(!err) ".$module->Modal->ShowScript(null, null, '${data}')."
    });";
    return [Html::Icon("reply", $d), $st?"#$st":""];
};
$module->CellsValues = [
    "Contact" =>fn($v)=> Html::Link($v, "mailto:$v")
];
$module->CellsTypes = [
    "Id" =>auth(\_::$Config->SuperAccess)?"disabled":false,
    "UserId" =>"number",
    "Name" =>"string",
    "Subject" =>"string",
    "Content" =>"Content",
    "Contact"=>"Email",
    "Attach" =>"json",
    "Status" => function(){
        $std = new stdClass();
        $std->Title = "Replyed Times";
        $std->Type = "number";
        $std->Options = ["min"=>-1, "max"=> 999999999];
        return $std;
    },
    "GroupId" => function(){
        $std = new stdClass();
        $std->Title = "User Group Access";
        $std->Type = "select";
        $std->Options = table("UserGroup")->SelectPairs("Id" , "Title" );
        return $std;
    },
    "Access" =>function(){
        $std = new stdClass();
        $std->Title = "Minimum Access";
        $std->Type="number";
        $std->Attributes=["min"=>\_::$Config->BanAccess,"max"=>\_::$Config->SuperAccess];
        return $std;
    },
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