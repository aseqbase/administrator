<?php
auth(\_::$User->AdminAccess);

use MiMFa\Library\Contact;
use MiMFa\Library\Convert;
use MiMFa\Library\Struct;
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
        table("Message")->Update("`Id`=:Id", [":Id" => $rec["Id"], "Status" => $rec["Status"], "UpdateTime" => \_::$Back->CurrentDateTime]);
        table("Message")->Insert([
            "RootId" => $rec["Id"],
            "UserId" => \_::$User ? \_::$User->Id : null,
            "Name" => \_::$User ? \_::$User->Name : null,
            "From" => $rec["SenderEmail"],
            "To" => $rec["ReceiverEmail"],
            "Subject" => $rec["MailSubject"],
            "Content" => $rec["MailMessage"],
            "Relation" => \_::$User->Url,
            "Access" => \_::$User->AdminAccess,
            "Status" => -1
        ]);
        deliverBreaker($res);
    } else
        deliver($res);
})->Patch(function () use (&$form) {
    $r = receivePatch();
    $isadmin = \_::$User->HasAccess(\_::$User->AdminAccess);
    $sender = $isadmin ? ($r["To"] ?? \_::$Front->ReceiverEmail) : \_::$User->Email;
    $form->Set(
        title: "Reply to " . $r["Name"],
        method: "POST",
        children: [
            Struct::Field("hidden", "Id", $r["Id"]),
            Struct::Field("number", "Status", $r["Status"] < 1 ? 1 : $r["Status"] + 1, "To indicate how many reply sent them", "Reply Time"),
            Struct::Field($isadmin ? "email" : "hidden", "SenderEmail", $sender, "Email sender", "From"),
            Struct::Field("email", "ReceiverEmail", $r["From"], "Email recipient", "To"),
            Struct::Field("text", "MailSubject", "Reply to your message: " . between($r["Subject"], "in " . \_::$Front->Name), "Reply subject", "Subject"),
            Struct::Field("content", "MailMessage", "Dear " . $r["Name"] . "," . "\n\r\n\r\n\r" .
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
    SELECT *, RootId AS 'ReplyTo'
    FROM {$module->DataTable->Name}
    ORDER BY `CreateTime` DESC
";
$module->KeyColumns = ["Subject"];
$module->IncludeColumns = ["ReplyTo", "Name", "Subject", "Content", "From", "To", "Relation", "CreateTime"];
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
    return [Struct::Icon("reply", $d), $st ? "#$st" : ""];
};
$module->CellsValues = [
    "ReplyTo" => fn($v) => $v?Struct::Icon("eye", "{$module->Modal->Name}_View('$v');"):"",
    "From" => fn($v) => $v?Struct::Button($v, "{$module->Modal->Name}_Create({Name:'".\_::$User->Name."', From:'".\_::$User->Email."', To:'$v'});"):"",
    "To" => fn($v) => $v?Struct::Button($v, "{$module->Modal->Name}_Create({Name:'".\_::$User->Name."', From:'".\_::$User->Email."', To:'$v'});"):"",
    "CreateTime"=>fn($v)=> Convert::ToShownDateTimeString($v)
];
$issuper = \_::$User->HasAccess(\_::$User->SuperAccess);
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
        $std->Type = "text";
        if(!$r["Subject"] && !$r["Content"]) $std->Value = $v ? $v : \_::$User->Name;
        return $std;
    },
    "Subject" => function ($t, $v, $k, $r) {
        $std = new stdClass();
        $std->Type = "text";
        if(!$r["Subject"] && !$r["Content"]) $std->Value = $v ? $v : \_::$Front->FullName;
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
        $std->Type = "text";
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
    "Relation" => "text",
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
        $std->Type = \_::$User->HasAccess(\_::$User->SuperAccess) ? "calendar" : "hidden";
        $std->Value = Convert::ToDateTimeString();
        return $std;
    },
    "CreateTime" => function ($t, $v) {
        return \_::$User->HasAccess(\_::$User->SuperAccess) ? "calendar" : (isValid($v) ? "hidden" : false);
    },
    "MetaData" => "json"
];
pod($module, $data);
$module->Render();
?>