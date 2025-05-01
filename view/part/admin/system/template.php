<?php
inspect(\_::$Config->AdminAccess);
if(\Req::Receive("restore","GET")) echo \MiMFa\Library\Revise::Restore(\_::$Front)
    ?\MiMFa\Library\Html::Success("Data Restored Successfully!")
    :\MiMFa\Library\Html::Warning("Data is restored!");
if(\Req::Receive(null,"POST")) echo \MiMFa\Library\Revise::HandleForm(\_::$Front);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$Front);
    $form->Title = "Edit Template";
    $form->Id = "EditTemplate";
    $form->Buttons = \MiMFa\Library\Html::Button("Restore",\Req::$Path."?restore=true");
    $form->Render();
}
?>