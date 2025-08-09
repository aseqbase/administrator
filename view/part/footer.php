<?php
part("footer", $data, origin: (\_::$Config->AdminOrigin??1)+1);
\Res::Render(
    MiMFa\Library\Html::Form([
        ["Type" => "text", "Key" => "BASE", "Title" => "BASE  ", "Value" => takeValid($_COOKIE, "BASE", null), "Style" => "background-color: var(--back-color-inside); color: var(--fore-color-inside);"],
        ["Type" => "submit", "Value" => "SWITCH", "Style" => "background-color: var(--back-color-outside); color: var(--fore-color-outside);"],
    ], "/", ["class" => "be center aseqbase-administrator-area view unprintable", "method" => "GET"])
);