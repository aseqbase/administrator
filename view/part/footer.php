<footer>
    <?php
    use MiMFa\Library\Style;
    use MiMFa\Library\Html;
    module("Shortcuts");
    $module = new \MiMFa\Module\Shortcuts();
    $module->Items = \_::$Info->Contacts;
    $module->Render();
    echo Html::Form([
        ["Type" => "text", "Key" => "BASE", "Title" => "BASE  ", "Value" => takeValid($_COOKIE, "BASE", null), "Style"=>"background-color: var(--back-color-1); color: var(--fore-color-1);"],
        ["Type" => "submit", "Value" => "SWITCH", "Style"=>"background-color: var(--back-color-2); color: var(--fore-color-2);"],
    ], "/", ["class"=> "be center", "method" => "post"]);
    module("TemplateButton");
    $module = new MiMFa\Module\TemplateButton();
    $module->Style = new Style();
    $module->Style->Position = "absolute";
    $module->Style->Left = "var(--size-1)";
    $module->Render();
    echo Html::Icon("arrow-up", "scrollTo('body :nth-child(1)');", ["style" => "border: none; position: absolute; right: var(--size-1);"]);
    part("copyright");
    ?>
</footer>