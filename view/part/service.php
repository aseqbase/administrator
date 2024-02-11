<?php
MODULE("RingSlide");
$module = new \MiMFa\Module\RingSlide();
$module->Image = \_::$INFO->FullLogoPath;
$module->Items = array(
		array("Name"=>"USERS","Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/user/users","Image"=>"/file/symbol/user.png"),
		array("Name"=>"POSTS","Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/content/posts","Image"=>"/file/symbol/document.png"),
		array("Name"=>"TAGS", "Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/content/tags","Image"=>"/file/symbol/directory.png"),
		array("Name"=>"TEMPLATE","Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/system/template","Image"=>"/file/symbol/info.png"),
		array("Name"=>"TRANSLATIONS","Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/system/translation","Image"=>"/file/symbol/web.png"),
		array("Name"=>"SYSTEMS","Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/system/information","Image"=>"/file/symbol/operation.png")
	);
$module->Draw();
?>