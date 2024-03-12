<?php
require_once 'functions.php';
$functions = new Functions();

if(empty($_POST['action']) || empty($_POST['inputTotalPaid']) || empty($_POST['inputTransaction'])){
    $functions->Redirect("../payments.php?id=".$_POST['id'] ?? ''."&err=invalid");
    die();
}

require_once 'functions.php';
$functions = new Functions();
if(!$functions->LoggedIn()){
    $functions->Redirect("../login.php");
    return;
}

$_POST['inputTotalPaid'] = $_POST['inputTotalPaid'] ?? '0';

if($_POST['action'] == 'edit'){
    $vars = [
        'id' => $_POST['id'],
        'uid' => $_SESSION['uid'],
        'amount' => $_POST['inputTotalPaid'], // Inicio de Transaccion
        'trans_date' => $_POST['inputTransaction'],
    ];

    die($functions->EditPayment($vars) ? $functions->Redirect("../payments.php") : $functions->Redirect("../payments.php?err=1"));
} elseif($_POST['action'] == 'remove'){
    die($functions->RemovePayment(['id' => $_POST['id'], 'uid' => $_SESSION['uid']]) ? $functions->Redirect("../payments.php") : $functions->Redirect("../payments.php?err=2"));
}