<?php
$module = new (module("Storage"))(
    \_::$Address->PublicAddress,
    rtrim(array_values(\_::$Sequence)[\_::$Back->AdminOrigin?:1], "\\\/").\_::$Address->PublicRoot
);
$module->Render();