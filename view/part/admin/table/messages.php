<?php
inspect(\_::$User->AdminAccess);

use MiMFa\Library\Contact;
use MiMFa\Library\Convert;
use MiMFa\Library\Html;
use MiMFa\Library\Script;
use MiMFa\Module\Form;
use MiMFa\Module\Table;
module("Form");
$form = new Form();
$form->BlockTimeout = 2000;
$form->SuccessHandler = "Your reply message sent successfuly!";
(new Router())->Post(function () use (&$form) {
    $res = $form->Handle();
    if ($form->Result) {
        $rec = receivePost();
        table("Message")->Update("`Id`=:Id", [":Id" => $rec["Id"], "Status" => $rec["Status"], "UpdateTime" => \_::$Config->CurrentDateTime]);
        table("Message")->Insert([
            "ReplyId" => $rec["Id"],
            "UserId" => \_::$User ? \_::$User->Id : null,
            "Name" => \_::$User ? \_::$User->Name : null,
            "From" => $rec["SenderEmail"],
            "To" => $rec["ReceiverEmail"],
            "Subject" => $rec["MailSubject"],
            "Content" => $rec["MailMessage"],
            "Type" => \_::$Address->Url,
            "Access" => \_::$User->AdminAccess,
            "Status" => -1
        ]);
        deliverBreaker($res);
    } else
        deliver($res);
})->Patch(function () use (&$form) {
    $r = receivePatch();
    $isadmin = \_::$User->GetAccess(\_::$User->AdminAccess);
    $sender = $isadmin ? ($r["To"] ?? \_::$Info->ReceiverEmail) : \_::$User->Email;
    $form->Set(
        title: "Reply to " . $r["Name"],
        method: "POST",
        children: [
            Html::Field("hidden", "Id", $r["Id"]),
            Html::Field("number", "Status", $r["Status"] < 1 ? 1 : $r["Status"] + 1, "To indicate how many reply sent them", "Reply Time"),
            Html::Field($isadmin ? "email" : "hidden", "SenderEmail", $sender, "Email sender", "From"),
            Html::Field("email", "ReceiverEmail", $r["From"], "Email recipient", "To"),
            Html::Field("text", "MailSubject", "Reply to your message: " . between($r["Subject"], "in " . \_::$Info->Name), "Reply subject", "Subject"),
            Html::Field("content", "MailMessage", "Dear " . $r["Name"] . "," . "\n\r\n\r\n\r" .
                join("\n\r", [
                    \_::$User->GenerateSign("Sincerely"),
                    "",
                    "On " . Convert::ToShownDateTimeString($r["CreateTime"]) . " " . $r["Name"] . " &amp;lt;" . $r["From"] . "&amp;gt; wrote:",
                    "\"\"",
                    $r["Content"],
                    "\"\""
                ]), "Reply answer", "Message")
        ]
    );
    $form->SenderEmail = $sender;
    $form->ReceiverEmail = $r["From"];
    $form->Image = "reply";
    $form->Template = "s";
    $form->Router->Get()->Switch();
    return deliver($form->ToString());
})->Handle();
if ($form->Status)
    return;

module("Table");
$module = new Table(table("Message"));
$module->SelectQuery = "
    SELECT *, ReplyId AS 'ReplyTo'
    FROM {$module->DataTable->Name}
    ORDER BY `CreateTime` DESC
";
$module->KeyColumns = ["Subject"];
$module->IncludeColumns = ["ReplyTo", "Name", "Subject", "Content", "From", "To", "Type", "CreateTime"];
$module->AllowServerSide = true;
$module->Updatable = true;
$module->ModifyAccess = \_::$User->SuperAccess;
$module->UpdateAccess = \_::$User->AdminAccess;
$module->CreateModal();
$module->ControlHandler = function ($r, $func) {
    switch ($func) {
        case 'AddRow':
            Contact::SendHtmlEmail($r["From"], $r["To"], $r["Subject"], $r["Content"]);
            break;
        default:
            break;
    }
    return null;
};
$module->AppendControlsCreator = function ($id, $r) use ($module) {
    $st = intval($r["Status"] ?? 0);
    $d = "sendPatch(null, {
        Id:" . Script::Convert($r["Id"]) . ",
        Status:" . Script::Convert($st = $st < 0 ? 0 : $st) . ",
        Name:" . Script::Convert($r["Name"]) . ",
        From:" . Script::Convert($r["From"]) . ",
        To:" . Script::Convert($r["To"]) . ",
        Subject:" . Script::Convert($r["Subject"]) . ",
        Content:" . Script::Convert($r["Content"]) . ",
        CreateTime:" . Script::Convert($r["CreateTime"]) . "
    }, 'form',
    (data, err) => {
        if(!err) " . $module->Modal->InitializeScript(null, null, '${data}') . "
    });";
    return [Html::Icon("reply", $d), $st ? "#$st" : ""];
};
$module->CellsValues = [
    "ReplyTo" => fn($v) => $v?Html::Icon("eye", "{$module->Modal->Name}_View('$v');"):"",
    "From" => fn($v) => $v?Html::Button($v, "{$module->Modal->Name}_Create({Name:'".\_::$User->Name."', From:'".\_::$User->Email."', To:'$v'});"):"",
    "To" => fn($v) => $v?Html::Button($v, "{$module->Modal->Name}_Create({Name:'".\_::$User->Name."', From:'".\_::$User->Email."', To:'$v'});"):""
];
$issuper = \_::$User->GetAccess(\_::$User->SuperAccess);
$module->CellsTypes = [
    "Id" =>  $issuper? "disabled" : false,
    "UserId" => function ($t, $v, $k, $r) use($issuper) {
        $std = new stdClass();
        $std->Type = $issuper ? "disabled" : "number";
        if(!$r["Subject"] && !$r["Content"]) $std->Value = $v ? $v : \_::$User->Id;
        return $std;
    },
    "Name" => function ($t, $v, $k, $r) {
        $std = new stdClass();
        $std->Type = "string";
        if(!$r["Subject"] && !$r["Content"]) $std->Value = $v ? $v : \_::$User->Name;
        return $std;
    },
    "Subject" => function ($t, $v, $k, $r) {
        $std = new stdClass();
        $std->Type = "string";
        if(!$r["Subject"] && !$r["Content"]) $std->Value = $v ? $v : \_::$Info->FullName;
        return $std;
    },
    "From" => function ($t, $v, $k, $r) {
        $std = new stdClass();
        $std->Type = "email";
        if(!$r["Subject"] && !$r["Content"]) $std->Value = $v ? $v : \_::$User->Email;
        return $std;
    },
    "To" => function () {
        $std = new stdClass();
        $std->Type = "string";
        $std->Description = "Your message recipient(s), separate each emails by a comma for a bulk sending...";
        return $std;
    },
    "Content" => function ($t, $v, $k, $r) {
        $std = new stdClass();
        $std->Title = "Message";
        $std->Type = "content";
        if(!$r["Subject"] && !$r["Content"]) $std->Value = $v ? $v : \_::$User->GenerateSign("Sincerely");
        return $std;
    },
    "Attach" => "json",
    "Type" => "string",
    "Status" => function () {
        $std = new stdClass();
        $std->Title = "Replyed Times";
        $std->Type = "number";
        $std->Options = ["min" => -1, "max" => 999999999];
        return $std;
    },
    "Access" => function () {
        $std = new stdClass();
        $std->Title = "Minimum Access";
        $std->Type = "number";
        $std->Attributes = ["min" => \_::$User->BanAccess, "max" => \_::$User->SuperAccess];
        return $std;
    },
    "UpdateTime" => function ($t, $v) {
        $std = new stdClass();
        $std->Type = \_::$User->GetAccess(\_::$User->SuperAccess) ? "calendar" : "hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function ($t, $v) {
        return \_::$User->GetAccess(\_::$User->SuperAccess) ? "calendar" : (isValid($v) ? "hidden" : false);
    },
    "MetaData" => "json"
];
pod($module, $data);
$module->Render();
?>