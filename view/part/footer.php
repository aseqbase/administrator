<footer>
    <?php
    use MiMFa\Library\Style;
    use MiMFa\Library\HTML;
    MODULE("Shortcuts");
    $module = new \MiMFa\Module\Shortcuts();
    $module->Items = array(
		array("Name"=>"Instagram","Link"=>"/?page=https://www.instagram.com/aseqbase","Icon"=> "fa fa-instagram"),
		array("Name"=>"Telegram","Link"=>"https://t.me/aseqbase","Icon"=> "fa fa-telegram"),
		array("Name"=>"Email","Link"=>"mailto:aseqbase@mimfa.net","Icon"=> "fa fa-envelope"),
		array("Name"=>"Github","Link"=>"http://github.com/mimfa","Icon"=> "fa fa-github"),
		array("Name"=>"Forum","Link"=>"/chat","Image"=>"/file/symbol/chat.png","Icon"=> "fa fa-comments")
	);
    $module->Draw();
    MODULE("TemplateButton");
    $module = new MiMFa\Module\TemplateButton();
    //$module->DarkLabel = "Dark Mode";
    //$module->LightLabel = "Light Mode";
    $module->Style = new Style();
    $module->Style->Position = "absolute";
    $module->Style->Left = "var(--Size-1)";
    $module->Draw();
    echo HTML::Icon("arrow-up","scrollTo('body :nth-child(1)');",["style"=>"border: none; position: absolute; right: var(--Size-1);"]);
    PART("copyright");
    ?>
</footer>