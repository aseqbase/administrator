<?php
$module = new (module("Storage"))(
    \_::$Address->PublicAddress,
    \_::$Address->PublicRoot
);
$module->Render();