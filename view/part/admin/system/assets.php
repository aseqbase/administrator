<?php
$module = new (module("Storage"))(
    \_::$Address->AssetAddress,
    rtrim(array_values(\_::$Sequence)[\_::$Back->AdminOrigin?:1], "\\\/").\_::$Address->AssetRoot
);
$module->Render();