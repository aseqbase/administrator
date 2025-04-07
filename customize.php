<?php
\_::$Config->AllowSigning = true;
\_::$Config->ReportError = E_ALL;
\_::$Config->DisplayError = 1;
\_::$Config->DisplayStartupError = 1;
\_::$Config->DataBaseError = 3;
if (auth(\_::$Config->AdminAccess)) {
    $origin = array_keys(\_::$Sequences)[0] == __DIR__.DIRECTORY_SEPARATOR?1:2;
    $name = \_::$Aseq->Name ?? "qb";
    \_::$Aseq = new Address(isset($_COOKIE["BASE"]) ? $_COOKIE["BASE"] : null, array_keys(\_::$Sequences)[$origin], array_values(\_::$Sequences)[$origin]);
    if(\_::$Config->DataBaseAddNameToPrefix) \_::$Config->DataBasePrefix = str_replace("{$name}_", (\_::$Aseq->Name ?? "qb")."_", \_::$Config->DataBasePrefix);
    \_::$Info->SenderEmail = "do-not-reply@" . getDomain(\_::$Aseq->Route);
    \_::$Info->ReceiverEmail = "info@" . getDomain(\_::$Aseq->Route);
    \_::$Info->MainMenus = \_::$Info->SideMenus = array(
        array("Name" => "DASHBOARD", "Path" => "/", "Access" => \_::$Config->AdminAccess, "Image" => "home"),
        array(
            "Name" => "CONTENTS",
            "Path" => "/admin/content/contents",
            "Access" => \_::$Config->AdminAccess,
            "Image" => "th-large",
            "Items" => array(
                array("Name" => "CONTENTS", "Path" => "/admin/content/contents", "Access" => \_::$Config->AdminAccess, "Image" => "th-large"),
                array("Name" => "TAGS", "Path" => "/admin/content/tags", "Access" => \_::$Config->AdminAccess, "Image" => "tags"),
                array("Name" => "CATEGORIES", "Path" => "/admin/content/categories", "Access" => \_::$Config->AdminAccess, "Image" => "code-fork")
            )
        ),
        array(
            "Name" => "USERS",
            "Path" => "/admin/user/users",
            "Access" => \_::$Config->AdminAccess,
            "Image" => "user",
            "Items" => array(
                array("Name" => "USERS", "Path" => "/admin/user/users", "Access" => \_::$Config->AdminAccess, "Image" => "user"),
                array("Name" => "GROUPS", "Path" => "/admin/user/groups", "Access" => \_::$Config->AdminAccess, "Image" => "group"),
                array("Name" => "COMMENTS", "Path" => "/admin/user/comments", "Access" => \_::$Config->AdminAccess, "Image" => "comment"),
                array("Name" => "MESSAGES", "Path" => "/admin/user/messages", "Access" => \_::$Config->AdminAccess, "Image" => "envelope"),
                array("Name" => "PAYMENTS", "Path" => "/admin/user/payments", "Access" => \_::$Config->AdminAccess, "Image" => "credit-card")
            )
        ),
        array(
            "Name" => "PLUGINS",
            "Path" => "/admin/plugin/plugins",
            "Access" => \_::$Config->AdminAccess,
            "Image" => "puzzle-piece",
            "Items" => array(
                array("Name" => "PLUGINS", "Path" => "/admin/plugin/plugins", "Access" => \_::$Config->AdminAccess, "Image" => "puzzle-piece"),
                array("Name" => "MARKET", "Path" => "http://github.com/aseqbase", "Access" => \_::$Config->AdminAccess, "Image" => "shopping-bag")
            )
        ),
        array(
            "Name" => "APPEARANCES",
            "Path" => "/admin/system/template",
            "Access" => \_::$Config->AdminAccess,
            "Image" => "th",
            "Items" => array(
                array("Name" => "TEMPLATES", "Path" => "/admin/system/templates", "Access" => \_::$Config->AdminAccess, "Image" => "eye"),
                array("Name" => "EDIT", "Path" => "/admin/system/template", "Access" => \_::$Config->AdminAccess, "Image" => "edit")
            )
        ),
        array(
            "Name" => "SYSTEMS",
            "Path" => "/admin/system/information",
            "Access" => \_::$Config->AdminAccess,
            "Image" => "cog",
            "Items" => array(
                array("Name" => "INFORMATIONS", "Path" => "/admin/system/information", "Access" => \_::$Config->AdminAccess, "Image" => "info"),
                array("Name" => "TRANSLATIONS", "Path" => "/admin/system/translation", "Access" => \_::$Config->AdminAccess, "Image" => "language"),
                array("Name" => "CONFIGURATIONS", "Path" => "/admin/system/configuration", "Access" => \_::$Config->AdminAccess, "Image" => "cog")
            )
        ),
        array("Name" => \_::$Info->Name, "Path" => \_::$Info->Path, "Access" => \_::$Config->AdminAccess, "Image" => "th", "Items" => \_::$Info->MainMenus)
    );
    \_::$Info->Shortcuts = array(
        array("Name" => "MENU", "Path" => "viewSideMenu()", "Image" => "bars"),
        array("Name" => "CONTENTS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/content/contents", "Image" => "th-large"),
        array("Name" => "HOME", "Access" => \_::$Config->AdminAccess, "Path" => "/home", "Image" => "home"),
        array("Name" => "USERS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/user/users", "Image" => "user"),
        array("Name" => "SYSTEMS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/information", "Image" => "cog"),
    );
    \_::$Info->Services = array(
        array("Name" => "USERS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/user/users", "Image" => "user"),
        array("Name" => "CONTENTS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/content/contents", "Image" => "th-large"),
        array("Name" => "TAGS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/content/tags", "Image" => "tags"),
        array("Name" => "TEMPLATE", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/template", "Image" => "quote-left"),
        array("Name" => "TRANSLATIONS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/translation", "Image" => "language"),
        array("Name" => "SYSTEMS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/information", "Image" => "cog")
    );
}
?>