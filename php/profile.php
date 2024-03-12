<?php
require_once 'functions.php';
$functions = new Functions();

if(!$functions->LoggedIn()){
    $functions->Redirect("../login.php");
    return;
}

if(empty($_POST['inputCredit']) || empty($_POST['inputInterests']) || empty($_POST['inputCut']) || empty($_POST['inputLimit'])){
    $functions->Redirect("../profile.php?err=invalid");
    die();
}

$profile = [
    "limiteCredito" => $_POST['inputCredit'],
    "fechaCorte" => $_POST['inputCut'],
    "fechaLimite" => $_POST['inputLimit'],
    "intereses" => $_POST['inputInterests']
];

die($functions->UpdateProfile($_SESSION['uid'], $profile) ? $functions->Redirect("../index.php") : $functions->Redirect("../profile.php?err=2"));
