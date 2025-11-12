<?php
part("footer", $data, origin: (\_::$Config->AdminOrigin??1)+1);
response(
    MiMFa\Library\Struct::Form([
        ["Type" => "text", "Key" => "BASE", "Title" => "BASE  ", "Value" => takeValid($_COOKIE, "BASE", null), "Style" => "background-color: var(--back-color-input); color: var(--fore-color-input);"],
        ["Type" => "submit", "Value" => "SWITCH", "Style" => "background-color: var(--back-color-output); color: var(--fore-color-output);"],
    ], "/", ["class" => "be center aseqbase-administrator-area view unprintable", "method" => "GET"])
);