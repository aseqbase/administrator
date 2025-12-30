<?php
$module = new (module("SideMenu"))();
$module->Title = \_::$Front->Name;
$module->Description = \_::$Front->Owner;
$module->Items = \_::$Front->SideMenus;
$module->Image = \_::$Front->LogoPath;
$module->Shortcuts = \_::$Front->Contacts;
pod($module, $data);
$module->Render();
?>
<script type="text/javascript">
	function viewSideMenu(show){
		<?php echo $module->Name."_ViewSideMenu(show);"; ?>
	}
</script>