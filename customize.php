<?php
\_::$Config->AllowSigning = true;
\_::$Config->ReportError = E_ALL;
\_::$Config->DisplayError = 1;
\_::$Config->DisplayStartupError = 1;
\_::$Config->DataBaseError = 3;
if (auth(\_::$Config->AdminAccess)) {
    \_::$Aseq->PublicDirectory = (new Address(null, array_keys(\_::$Sequences)[1]))->PublicDirectory;
    \_::$Info->MainMenus = \_::$Info->SideMenus = array(
        array("Name" => "DASHBOARD", "Path" => "/home", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/home.png"),
        array(
            "Name" => "CONTENTS",
            "Path" => "/admin/content/contents",
            "Access" => \_::$Config->AdminAccess,
            "Image" => "/asset/symbol/document.png",
            "Items" => array(
                array("Name" => "CONTENTS", "Path" => "/admin/content/contents", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/document.png"),
                array("Name" => "TAGS", "Path" => "/admin/content/tags", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/directory.png"),
                array("Name" => "CATEGORIES", "Path" => "/admin/content/categories", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/category.png"),
                array("Name" => "COMMENTS", "Path" => "/admin/content/comments", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/chat.png")
            )
        ),
        array(
            "Name" => "USERS",
            "Path" => "/admin/user/users",
            "Access" => \_::$Config->AdminAccess,
            "Image" => "/asset/symbol/user.png",
            "Items" => array(
                array("Name" => "USERS", "Path" => "/admin/user/users", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/user.png"),
                array("Name" => "GROUPS", "Path" => "/admin/user/groups", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/team.png")
            )
        ),
        array(
            "Name" => "PLUGINS",
            "Path" => "/admin/plugin/plugins",
            "Access" => \_::$Config->AdminAccess,
            "Image" => "/asset/symbol/plugin.png",
            "Items" => array(
                array("Name" => "PLUGINS", "Path" => "/admin/plugin/plugins", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/plugin.png"),
                array("Name" => "MARKET", "Path" => "http://github.com/aseqbase", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/market.png")
            )
        ),
        array(
            "Name" => "APPEARANCES",
            "Path" => "/admin/system/template",
            "Access" => \_::$Config->AdminAccess,
            "Image" => "/asset/symbol/dashboard.png",
            "Items" => array(
                array("Name" => "TEMPLATES", "Path" => "/admin/system/templates", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/package.png"),
                array("Name" => "EDIT", "Path" => "/admin/system/template", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/info.png")
            )
        ),
        array(
            "Name" => "SYSTEMS",
            "Path" => "/admin/system/information",
            "Access" => \_::$Config->AdminAccess,
            "Image" => "/asset/symbol/operation.png",
            "Items" => array(
                array("Name" => "INFORMATIONS", "Path" => "/admin/system/information", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/info.png"),
                array("Name" => "TRANSLATIONS", "Path" => "/admin/system/translation", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/web.png"),
                array("Name" => "CONFIGURATIONS", "Path" => "/admin/system/configuration", "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/operation.png")
            )
        ),
        array("Name" => \_::$Info->Name, "Path"=>\_::$Info->Path, "Access" => \_::$Config->AdminAccess, "Image" => "/asset/symbol/website.png", "Items"=>\_::$Info->MainMenus)
    );
    \_::$Info->Shortcuts = array(
        array("Name" => "Menu", "Path" => "", "Image" => getFullUrl("/asset/symbol/menu.png"), "Attributes" => "onclick='viewSideMenu()'"),
        array("Name" => "CONTENTS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/content/contents", "Image" => getFullUrl("/asset/symbol/document.png")),
        array("Name" => "HOME", "Access" => \_::$Config->AdminAccess, "Path" => "/home", "Image" => getFullUrl("/asset/symbol/home.png")),
        array("Name" => "USERS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/user/users", "Image" => getFullUrl("/asset/symbol/user.png")),
        array("Name" => "SYSTEMS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/information", "Image" => getFullUrl("/asset/symbol/operation.png")),
    );
    \_::$Info->Services = array(
        array("Name" => "USERS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/user/users", "Image" => "/asset/symbol/user.png"),
        array("Name" => "CONTENTS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/content/contents", "Image" => "/asset/symbol/document.png"),
        array("Name" => "TAGS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/content/tags", "Image" => "/asset/symbol/directory.png"),
        array("Name" => "TEMPLATE", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/template", "Image" => "/asset/symbol/info.png"),
        array("Name" => "TRANSLATIONS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/translation", "Image" => "/asset/symbol/web.png"),
        array("Name" => "SYSTEMS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/information", "Image" => "/asset/symbol/operation.png")
    );
}
?>