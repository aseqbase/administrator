<?php
$module = new (module("SideMenu"))();
$module->Title = \_::$Info->Name;
$module->Description = \_::$Info->Owner;
$module->Items = \_::$Info->SideMenus;
$module->Image = \_::$Info->LogoPath;
$module->Shortcuts = \_::$Info->Contacts;
dip($module, $data);
$module->Render();
?>
<script type="text/javascript">
	function viewSideMenu(show){
		<?php echo $module->Name."_ViewSideMenu(show);"; ?>
	}
</script>