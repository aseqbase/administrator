<?php
module("SideMenu");
$module = new MiMFa\Module\SideMenu();
$module->Title = \_::$Front->Name;
$module->Description = \_::$Front->Owner;
$module->Items = [...\_::$Front->AdminMenus, 
        "Main" => array(
            "Name" => \_::$Front->Name,
            "Path" => \_::$Front->DirectPath,
            "Description" => "The main menu of the website",
            "Image" => "globe",
            "Items" => \_::$Front->MainMenus
        )];
$module->Image = \_::$Front->LogoPath;
$module->AllowHide = false;
$module->AllowHoverable = true;
$module->Shortcuts = \_::$Front->Contacts;
pod($module, $data);
$module->Render();
?>
<script type="text/javascript">
	function viewSideMenu(show){
		<?php echo $module->MainClass."_ViewSideMenu(show);"; ?>
	}
</script>