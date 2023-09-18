<style>
	.page-home {
		padding: 10px 10px 50px;
	}
</style>

<div class="page-home">
<?php PART("small-header"); ?>
<?php MODULE("RingSlide");
$module = new \MiMFa\Module\RingSlide();
$module->Image = \_::$INFO->FullLogoPath;
$module->Items = \_::$INFO->Services;
$module->Draw();
?>
</div>
