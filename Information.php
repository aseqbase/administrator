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

	public $GitPath = "https://github.com/aseqbase";
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
		array("Name"=>"APPEARANCES","Link"=>"/system/template","Image"=>"/file/symbol/dashboard.png", "Items"=> array(
		    	array("Name"=>"TEMPLATES","Link"=>"/system/templates","Image"=>"/file/symbol/package.png"),
		    	array("Name"=>"EDITS","Link"=>"/system/template","Image"=>"/file/symbol/info.png")
		    )),
		array("Name"=>"SYSTEMS","Link"=>"/system/information","Image"=>"/file/symbol/operation.png", "Items"=> array(
		    	array("Name"=>"INFORMATIONS","Link"=>"/system/information","Image"=>"/file/symbol/info.png"),
		    	array("Name"=>"CONFIGURATIONS","Link"=>"/system/configuration","Image"=>"/file/symbol/operation.png")
		    )),
		array("Name"=>"ABOUT","Link"=>"/about","Image"=>"/file/symbol/about.png")
		);

	public $Shortcuts = array(
		array("Name"=>"Menu","Link"=>"","Image"=>"/file/symbol/menu.png", "Attributes"=>"onclick='viewSideMenu()'"),
		array("Name"=>"POSTS","Access"=>10,"Link"=>"/content/posts","Image"=>"/file/symbol/document.png"),
		array("Name"=>"HOME","Access"=>10,"Link"=>"/home","Image"=>"/file/symbol/home.png"),
		array("Name"=>"USERS","Access"=>10,"Link"=>"/user/users","Image"=>"/file/symbol/user.png"),
		array("Name"=>"SYSTEMS","Access"=>10,"Link"=>"/system/information","Image"=>"/file/symbol/operation.png"),
		);
	
	public $Services = array(
		array("Name"=>"POSTS","Access"=>10,"Link"=>"/content/posts","Image"=>"/file/symbol/document.png"),
		array("Name"=>"USERS","Access"=>10,"Link"=>"/user/users","Image"=>"/file/symbol/user.png"),
		array("Name"=>"SYSTEMS","Access"=>10,"Link"=>"/system/information","Image"=>"/file/symbol/operation.png")
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
