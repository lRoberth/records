<?php
require_once 'functions.php';
$functions = new Functions();

if(empty($_POST['inputType']) || empty($_POST['inputTransaction']) || empty($_POST['inputDescription']) || empty($_POST['inputMonths']) || empty($_POST['inputTotal'])){
    $functions->Redirect("../edit.php?id=".$_POST['id'] ?? ''."&err=invalid");
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
        'type' => $_POST['inputType'],
        'transaction' => $_POST['inputTransaction'], // Inicio de Transaccion
        'description' => $_POST['inputDescription'],
        'months' => $_POST['inputMonths'],
        'total' => $_POST['inputTotal'],
        'total_paid' => $_POST['inputTotalPaid']
    ];

    die($functions->EditTransaction($vars) ? $functions->Redirect("../edit.php") : $functions->Redirect("../edit.php?err=1"));
} elseif($_POST['action'] == 'remove'){
    die($functions->RemoveTransaction(['id' => $_POST['id'], 'uid' => $_SESSION['uid']]) ? $functions->Redirect("../edit.php") : $functions->Redirect("../edit.php?err=2"));
}