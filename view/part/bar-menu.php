<?php MODULE("BarMenu");
$module = new \MiMFa\Module\BarMenu();
$module->Items = array(
		array("Name"=>"Menu","Link"=>"","Image"=>getFullUrl("/file/symbol/menu.png"), "Attributes"=>"onclick='viewSideMenu()'"),
		array("Name"=>"POSTS", "Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/content/posts","Image"=>getFullUrl("/file/symbol/document.png")),
		array("Name"=>"HOME", "Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/home","Image"=>getFullUrl("/file/symbol/home.png")),
		array("Name"=>"USERS", "Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/user/users","Image"=>getFullUrl("/file/symbol/user.png")),
		array("Name"=>"SYSTEMS", "Access"=>\_::$CONFIG->AdminAccess,"Link"=>"/system/information","Image"=>getFullUrl("/file/symbol/operation.png")),
		);
$module->Draw();
?>