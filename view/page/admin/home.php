<?php
use \MiMFa\Library\Struct;


module("RingTabs");
$module = new \MiMFa\Module\RingTabs();
$module->Image = \_::$Front->LogoPath;
$module->Items = array(
	array("Name" => "USERS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/user/users", "Image" => "user"),
	array("Name" => "CONTENTS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/content/contents", "Image" => "th-large"),
	array("Name" => "TAGS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/content/tags", "Image" => "tags"),
	array("Name" => "TEMPLATE", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/system/template", "Image" => "quote-left"),
	array("Name" => "TRANSLATIONS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/system/translation", "Image" => "language"),
	array("Name" => "SYSTEMS", "Access" => \_::$User->AdminAccess, "Path" => "/administrator/system/information", "Image" => "cog")
);
pod($module, $data);
response(
	Struct::Style("
		.page-home {
			padding: 10px 10px 50px;
		}
	") .
	Struct::Page(
		part("small-header", print: false) .
		$module->Handle() .
		(!\_::$User->AllowSigning || \_::$User->HasAccess(\_::$User->UserAccess) ? "" :
			Struct::Center(
				Struct::SmallSlot(
					Struct::Button("Sign In", \_::$User->InHandlerPath) .
					Struct::Button("Sign up", \_::$User->UpHandlerPath)
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