<?php
auth(\_::$User->SuperAccess);
if(receiveGet("restore")) echo \MiMFa\Library\Revise::Restore(\_::$Front)
    ?\MiMFa\Library\Struct::Success("Data Restored Successfully!")
    :\MiMFa\Library\Struct::Warning("Data is restored!");
if(receivePost(null)) echo \MiMFa\Library\Revise::HandleForm(\_::$Back);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$Back);
    $form->Title = "Edit Configuration";
    $form->Id = "EditConfiguration";
    $form->Buttons = \MiMFa\Library\Struct::Button("Restore",\_::$User->Path."?restore=true");
    $form->Render();
}
?>