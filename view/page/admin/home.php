<?php
use \MiMFa\Library\Html;


module("RingTabs");
$module = new \MiMFa\Module\RingTabs();
$module->Image = \_::$Info->LogoPath;
$module->Items = array(
	"Admin-1" => array("Name" => "USERS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/user/users", "Image" => "user"),
	"Admin-2" => array("Name" => "CONTENTS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/content/contents", "Image" => "th-large"),
	"Admin-3" => array("Name" => "TAGS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/content/tags", "Image" => "tags"),
	"Admin-4" => array("Name" => "TEMPLATE", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/template", "Image" => "quote-left"),
	"Admin-5" => array("Name" => "TRANSLATIONS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/translation", "Image" => "language"),
	"Admin-6" => array("Name" => "SYSTEMS", "Access" => \_::$Config->AdminAccess, "Path" => "/admin/system/information", "Image" => "cog")
);
swap($module, $data);
render(
	Html::Style("
		.page-home {
			padding: 10px 10px 50px;
		}
	") .
	Html::Page(
		part("small-header", print: false) .
		$module->Handle() .
		(!\_::$Config->AllowSigning || auth(\_::$Config->UserAccess) ? "" :
			Html::Center(
				Html::SmallSlot(
					Html::Button("Sign In", \User::$InHandlerPath) .
					Html::Button("Sign up", \User::$UpHandlerPath)
					,
					["data-aos" => "zoom-out", "data-aos-duration" => "600"]
				),
				["class" => "sign"]
			)
		)
		,
		["class" => "page-home"]
	)
);
?>