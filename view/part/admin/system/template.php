<?php
inspect(\_::$Config->AdminAccess);
if(receiveGet("restore")) echo \MiMFa\Library\Revise::Restore(\_::$Front)
    ?\MiMFa\Library\Html::Success("Data Restored Successfully!")
    :\MiMFa\Library\Html::Warning("Data is restored!");
if(receivePost(null)) echo \MiMFa\Library\Revise::HandleForm(\_::$Front);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$Front);
    $form->Title = "Edit Template";
    $form->Id = "EditTemplate";
    $form->Buttons = \MiMFa\Library\Html::Button("Restore",\_::$Base->Path."?restore=true");
    $form->Render();
}
?>