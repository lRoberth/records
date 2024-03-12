<?php
require_once 'functions.php';
$functions = new Functions();
if(empty($_POST['inputUsername']) || empty($_POST['inputPassword'])){
    $functions->Redirect("../login.php?err=invalid");
    die();
}
    
die($functions->Login($_POST['inputUsername'], $_POST['inputPassword']));
