<?php
inspect(\_::$Config->SuperAccess);
if(receiveGet("restore")) echo \MiMFa\Library\Revise::Restore(\_::$Front)
    ?\MiMFa\Library\Html::Success("Data Restored Successfully!")
    :\MiMFa\Library\Html::Warning("Data is restored!");
if(receivePost(null)) echo \MiMFa\Library\Revise::HandleForm(\_::$Config);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$Config);
    $form->Title = "Edit Configuration";
    $form->Id = "EditConfiguration";
    $form->Buttons = \MiMFa\Library\Html::Button("Restore",\_::$Path."?restore=true");
    $form->Render();
}
?>