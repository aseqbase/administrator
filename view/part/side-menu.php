<?php MODULE("SideMenu");
$module = new MiMFa\Module\SideMenu();
$module->Title = \_::$INFO->Product;
$module->Description = \_::$INFO->Owner;
$module->Items = array(
		array("Name"=>"DASHBOARD","Link"=>"/home", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/home.png"),
		array("Name"=>"CONTENTS","Link"=>"/content/posts", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/document.png", "Items"=> array(
		    	array("Name"=>"POSTS","Link"=>"/content/posts", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/document.png"),
		    	array("Name"=>"TAGS","Link"=>"/content/tags", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/directory.png"),
		    	array("Name"=>"CATEGORIES","Link"=>"/content/categories", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/category.png"),
		    	array("Name"=>"COMMENTS","Link"=>"/content/comments", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/chat.png")
		    )),
		array("Name"=>"USERS","Link"=>"/user/users", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/user.png", "Items"=> array(
		    	array("Name"=>"USERS","Link"=>"/user/users", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/user.png"),
		    	array("Name"=>"GROUPS","Link"=>"/user/groups", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/team.png")
		    )),
		array("Name"=>"PLUGINS","Link"=>"/plugin/plugins", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/plugin.png", "Items"=> array(
		    	array("Name"=>"PLUGINS","Link"=>"/plugin/plugins", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/plugin.png"),
		    	array("Name"=>"MARKET","Link"=>"http://github.com/aseqbase", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/market.png")
		    )),
		array("Name"=>"APPEARANCES","Link"=>"/system/template", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/dashboard.png", "Items"=> array(
		    	array("Name"=>"TEMPLATES","Link"=>"/system/templates", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/package.png"),
		    	array("Name"=>"EDIT","Link"=>"/system/template", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/info.png")
		    )),
		array("Name"=>"SYSTEMS","Link"=>"/system/information", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/operation.png", "Items"=> array(
		    	array("Name"=>"INFORMATIONS","Link"=>"/system/information", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/info.png"),
		    	array("Name"=>"TRANSLATIONS","Link"=>"/system/translation", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/web.png"),
		    	array("Name"=>"CONFIGURATIONS","Link"=>"/system/configuration", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/operation.png")
		    )),
		array("Name"=>"ABOUT","Link"=>"/about", "Access"=>\_::$CONFIG->AdminAccess, "Image"=>"/asset/symbol/about.png")
		);
$module->Image = \_::$INFO->LogoPath;
$module->Shortcuts = [];
$module->Draw();
?>
<script type="text/javascript">
	function viewSideMenu(show){
		<?php echo $module->Name."_ViewSideMenu(show);"; ?>
	}
</script>