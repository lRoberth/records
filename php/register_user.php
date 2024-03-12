<?php
require_once 'functions.php';
$functions = new Functions();
if(empty($_POST['inputUsername']) || empty($_POST['inputPassword']) || empty($_POST['inputPasswordConfirm']) || empty($_POST['inputPIN']) || empty($_POST['inputCredit']) || empty($_POST['inputInterests']) || empty($_POST['inputCut']) || empty($_POST['inputLimit'])){
    $functions->Redirect("../register.php?err=invalid");
    die();
}

die($functions->CreateAccount($_POST['inputPIN'], $_POST['inputUsername'], $_POST['inputPassword'], $_POST['inputPasswordConfirm'], $_POST['inputCredit'], $_POST['inputInterests'], $_POST['inputCut'], $_POST['inputLimit']));
