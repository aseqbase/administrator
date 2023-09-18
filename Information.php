<?php
class Information extends InformationBase{
	public $Owner = "MiMFa";
	public $FullOwner = "Minimal Member Factory";
	public $Product = "aseqbase Admin";
	public $FullProduct = "aseqbase Administration";
	public $Name = "aseqbase Administration";
	public $FullName = "MiMFa aseqbase Administration";
	public $Slogan = "<u>a seq</u>uence-<u>base</u>d framework";
	public $FullSlogan = "Develop websites by <u>a seq</u>uence-<u>base</u>d framework";
	public $Description = "A default content management system is special for an aseqbase website...";
	public $FullDescription = "A special framework for web development called \"aseqbase\" (a sequence-based framework) has been developed to implement safe, flexible, fast, and strong pure websites based on that, since 2018 so far.";

	public $Path = "https://aseqbase.ir";
	public $DownloadPath = "https://github.com/aseqbase/aseqbase";
	public $Location = null;

	public $MainMenus = array(
		array("Name"=>"DASHBOARD","Link"=>"/home","Image"=>"/file/symbol/home.png"),
		array("Name"=>"CONTENTS","Link"=>"/content/posts","Image"=>"/file/symbol/document.png", "Items"=> array(
		    	array("Name"=>"POSTS","Link"=>"/content/posts","Image"=>"/file/symbol/document.png"),
		    	array("Name"=>"GROUPS","Link"=>"/content/groups","Image"=>"/file/symbol/directory.png"),
		    	array("Name"=>"CATEGORIES","Link"=>"/content/categories","Image"=>"/file/symbol/category.png")
		    )),
		array("Name"=>"USERS","Link"=>"/user/users","Image"=>"/file/symbol/user.png", "Items"=> array(
		    	array("Name"=>"USERS","Link"=>"/user/users","Image"=>"/file/symbol/user.png"),
		    	array("Name"=>"GROUPS","Link"=>"/user/groups","Image"=>"/file/symbol/team.png")
		    )),
		array("Name"=>"PLUGINS","Link"=>"/plugin/plugins","Image"=>"/file/symbol/plugin.png", "Items"=> array(
		    	array("Name"=>"PLUGINS","Link"=>"/plugin/plugins","Image"=>"/file/symbol/plugin.png"),
		    	array("Name"=>"MARKET","Link"=>"http://github.com/aseqbase","Image"=>"/file/symbol/market.png")
		    )),
		array("Name"=>"GIT","Link"=>"http://github.com/mimfa/aseqbase","Image"=>"/file/symbol/git.png"),
		array("Name"=>"FORUM","Link"=>"https://github.com/aseqbase/aseqbase/issues","Image"=>"/file/symbol/chat.png"),
		array("Name"=>"ABOUT","Link"=>"/about","Image"=>"/file/symbol/about.png")
		);

	public $Shortcuts = array(
		array("Name"=>"Menu","Link"=>"","Image"=>"/file/symbol/menu.png", "Attributes"=>"onclick='viewSideMenu()'"),
		array("Name"=>"Market","Link"=>"#embed","Image"=>"/file/symbol/market.png","Attributes"=> "class='embed-link' onclick='viewEmbed(\"https://github.com/aseqbase/aseqbase\",\"fade\"); viewSideMenu(false);'"),
		array("Name"=>"Home","Link"=>"#internal","Image"=>"/file/symbol/home.png","Attributes"=> "class='internal-link' onclick='viewInternal(\"home\",\"fade\"); viewSideMenu(false);'"),
		array("Name"=>"Products","Link"=>"#internal","Image"=>"/file/symbol/product.png", "Attributes"=>"class='internal-link' onclick='viewInternal(\"https://github.com/mimfa\",\"fade\"); viewSideMenu(false);'"),
		array("Name"=>"Chat","Link"=>"#internal","Image"=>"/file/symbol/chat.png","Attributes"=> "class='internal-link' onclick='viewInternal(\"https://github.com/aseqbase/aseqbase/issues\",\"fade\"); viewSideMenu(false);'")
		);

	public $Contacts = array(
		array("Name"=>"Instagram","Link"=>"/?page=https://www.instagram.com/aseqbase","Icon"=> "fa fa-instagram"),
		array("Name"=>"Telegram","Link"=>"https://t.me/aseqbase","Icon"=> "fa fa-telegram"),
		array("Name"=>"Email","Link"=>"mailto:aseqbase@mimfa.net","Icon"=> "fa fa-envelope"),
		array("Name"=>"Github","Link"=>"http://github.com/mimfa","Icon"=> "fa fa-github"),
		array("Name"=>"Forum","Link"=>"/chat","Image"=>"/file/symbol/chat.png","Icon"=> "fa fa-comments")
	);
}
?>
