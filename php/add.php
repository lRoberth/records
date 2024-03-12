<?php
require_once 'functions.php';
$functions = new Functions();

if(empty($_POST['inputType']) || empty($_POST['inputTransaction']) || empty($_POST['inputDescription']) || empty($_POST['inputMonths']) || empty($_POST['inputTotal'])){
    $functions->Redirect("../add.php?err=invalid");
    die();
}

if(!$functions->LoggedIn()){
    $functions->Redirect("../login.php");
    return;
}

$_POST['inputTotalPaid'] = $_POST['inputTotalPaid'] ?? '0';

$vars = [
    'uid' => $_SESSION['uid'],
    'type' => $_POST['inputType'],
    'transaction' => $_POST['inputTransaction'], // Inicio de Transaccion
    'description' => $_POST['inputDescription'],
    'months' => $_POST['inputMonths'],
    'total' => $_POST['inputTotal'],
    'total_paid' => $_POST['inputTotalPaid']
];

die($functions->AddTransaction($vars) ? $functions->Redirect("../index.php") : $functions->Redirect("../add.php?err=1"));