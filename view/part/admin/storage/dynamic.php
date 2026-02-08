<?php
$module = new (module("Storage"))(
    \_::$Address->PublicAddress,
    rtrim(array_values(\_::$Sequence)[\_::$Back->AdminOrigin==0?1:0], "\\\/").\_::$Address->PublicRoot
);
$module->Render();