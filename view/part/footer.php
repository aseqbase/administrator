<?php
part("footer", $data, origin: \_::$Config->AdminOrigin??1);
\Res::Render(
    MiMFa\Library\Html::$Break .
    MiMFa\Library\Html::Form([
        ["Type" => "text", "Key" => "BASE", "Title" => "BASE  ", "Value" => takeValid($_COOKIE, "BASE", null), "Style" => "background-color: var(--back-color-1); color: var(--fore-color-1);"],
        ["Type" => "submit", "Value" => "SWITCH", "Style" => "background-color: var(--back-color-2); color: var(--fore-color-2);"],
    ], "/", ["class" => "be center", "method" => "GET"])
);
?>