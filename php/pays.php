<?php
require_once 'functions.php';
$functions = new Functions();

if(empty($_POST['id'])){
    $functions->Redirect("../pays.php?err=1");
    die();
}

if(empty($_POST['inputTotalPaid'])){
    $functions->Redirect("../pays.php?id=".$_POST['id']."&err=invalid");
    die();
}


if(!$functions->LoggedIn()){
    $functions->Redirect("../login.php");
    return;
}


$vars = [
    'id' => $_POST['id'],
    'uid' => $_SESSION['uid'],
    'total_paid' => $_POST['inputTotalPaid']
];


die($functions->PayTransaction($vars) ? $functions->Redirect("../pays.php") : $functions->Redirect("../pays.php?err=2"));
