<?php
ACCESS(\_::$CONFIG->SuperAccess);
if(RECEIVE("restore","GET")) echo \MiMFa\Library\Revise::Restore(\_::$INFO)
    ?\MiMFa\Library\HTML::Success("Data Restored Successfully!")
    :\MiMFa\Library\HTML::Warning("Data is restored!");
\MiMFa\Library\User::$Active = false;
\_::$INFO = new Information();
if(RECEIVE(null,"POST")) echo \MiMFa\Library\Revise::HandleForm(\_::$INFO);
else {
    $form = \MiMFa\Library\Revise::GetForm(\_::$INFO);
    $form->Title = "Edit Information";
    $form->Id = "EditInformation";
    $form->Buttons = \MiMFa\Library\HTML::Button("Restore",\_::$PATH."?restore=true");
    $form->Draw();
}
\MiMFa\Library\User::$Active = true;
?>