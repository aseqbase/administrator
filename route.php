<?php
\_::$Router
    ->On()->Reset()
    ->On(urlencode(\_::$Back->SecretKey ?? "admin"))->Get(function () {
        $up = \_::$Back->SecretKey ?? "admin";
        $pairs = \_::$User->GroupDataTable->SelectPairs("Id", "Access", "Access>900000000");
        if (!$pairs)
            deliverError("There is not at least one admin access group!");
        if (!\_::$User->DataTable->Exists("GroupId IN (" . join(",", array_keys($pairs)) . ")")) {
            sort($pairs, SORT_DESC | SORT_NUMERIC);
            if(\_::$User->SignUp(
                $un = receiveGet("UserName") ?? $up,
                $ps = receiveGet("Password") ?? $up,
                $em = receiveGet("Email") ?? \_::$User->GenerateEmail(fake: true),
                groupId: receiveGet("GroupId") ?? first($pairs),
                status: \_::$User->ActiveStatus,
            )!= false){
                view(\_::$Front->DefaultViewName, ["Content"=>
                MiMFa\Library\Html::Heading1("Your Admin Account Created Successfully").
                MiMFa\Library\Html::Table([
                    ["Name", "Value",  "Description"],
                    ["UserName", $un,  ""],
                    ["Password", $ps,  "Please change it immediately"],
                    ["Email", $em, isEmail($em)?"":"It is a fake email, Please change it immediately"]
                ]).
                (\_::$User->SignIn($un, $ps)!==false?MiMFa\Library\Html::Success("You are signed in now!"):"")
            ]);
        }
        } else
            route(404);
    })
    ->if(!\_::$User->GetAccess(\_::$User->AdminAccess))
    ->On("$|admin")->Default(fn() => view("part", ["Name" => \_::$User->InHandlerPath]))
    ->On()->Default(\_::$Router->DefaultRouteName)
    ->else()
    ->On("admin")->Reset()->Default(\_::$Address->Direction, alternative: \_::$Router->DefaultRouteName)
    ->On()->Default(\_::$Router->DefaultRouteName);
?>