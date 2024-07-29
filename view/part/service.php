<?php
MODULE("RingSlide");
$module = new \MiMFa\Module\RingSlide();
$module->Image = \_::$INFO->FullLogoPath;
$module->Items = array(
		array("Name"=>"USERS","Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/user/users","Image"=>"/asset/symbol/user.png"),
		array("Name"=>"POSTS","Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/content/posts","Image"=>"/asset/symbol/document.png"),
		array("Name"=>"TAGS", "Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/content/tags","Image"=>"/asset/symbol/directory.png"),
		array("Name"=>"TEMPLATE","Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/system/template","Image"=>"/asset/symbol/info.png"),
		array("Name"=>"TRANSLATIONS","Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/system/translation","Image"=>"/asset/symbol/web.png"),
		array("Name"=>"SYSTEMS","Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/system/information","Image"=>"/asset/symbol/operation.png")
	);
$module->Draw();
?>