<?php
$module = new (module("Storage"))(
    \_::$Address->AssetAddress,
    \_::$Address->AssetRoot
);
$module->Render();