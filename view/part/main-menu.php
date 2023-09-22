<?php MODULE("MainMenu");
$module = new MiMFa\Module\MainMenu();
$module->Title = \_::$INFO->Product;
$module->Description = \_::$INFO->Owner;
$module->Image = \_::$INFO->LogoPath;
$module->Items = array(
		array("Name"=>"DASHBOARD","Link"=>"/home", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/home.png"),
		array("Name"=>"CONTENTS","Link"=>"/content/posts", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/document.png", "Items"=> array(
		    	array("Name"=>"POSTS","Link"=>"/content/posts", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/document.png"),
		    	array("Name"=>"GROUPS","Link"=>"/content/groups", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/directory.png"),
		    	array("Name"=>"CATEGORIES","Link"=>"/content/categories", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/category.png")
		    )),
		array("Name"=>"USERS","Link"=>"/user/users", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/user.png", "Items"=> array(
		    	array("Name"=>"USERS","Link"=>"/user/users", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/user.png"),
		    	array("Name"=>"GROUPS","Link"=>"/user/groups", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/team.png")
		    )),
		array("Name"=>"PLUGINS","Link"=>"/plugin/plugins", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/plugin.png", "Items"=> array(
		    	array("Name"=>"PLUGINS","Link"=>"/plugin/plugins", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/plugin.png"),
		    	array("Name"=>"MARKET","Link"=>"http://github.com/aseqbase", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/market.png")
		    )),
		array("Name"=>"APPEARANCES","Link"=>"/system/template", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/dashboard.png", "Items"=> array(
		    	array("Name"=>"TEMPLATES","Link"=>"/system/templates", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/package.png"),
		    	array("Name"=>"EDIT","Link"=>"/system/template", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/info.png")
		    )),
		array("Name"=>"SYSTEMS","Link"=>"/system/information", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/operation.png", "Items"=> array(
		    	array("Name"=>"INFORMATIONS","Link"=>"/system/information", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/info.png"),
		    	array("Name"=>"CONFIGURATIONS","Link"=>"/system/configuration", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/operation.png")
		    )),
		array("Name"=>"ABOUT","Link"=>"/about", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/file/symbol/about.png")
		);
$module->Draw();
?>