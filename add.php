<?php
require_once 'php/functions.php';
$functions = new Functions();
if(!$functions->LoggedIn()){
    $functions->Redirect("login.php");
    return;
}

$query_error = false;
if(isset($_GET['err'])){
    $errors = [
        'invalid' => 'Todos los campos son requeridos',
        '1' => 'Ocurrio un error, contacta al administrador (1)',
    ];
    $query_error = $errors[$_GET['err']] ?? false;
}
?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Dashboard - SB Admin</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.php">Transacciones</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i><?=$_SESSION['username']?></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <?=$functions->showNavMenu()?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Añadir Transaccion</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Añadir</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-body">
                                <p class="mb-0">
                                <?=$query_error == false ? '' : '<div class="form-floating mb-3 error">'.$query_error.'</div>'?>
                                    <form action='php/add.php' method='post'>
                                        <div class="form-floating mb-3">
                                            <select class="form-control" id="inputType" name="inputType">
                                                <option value="1">MSI</option>
                                                <option value="2">MCI</option>
                                            </select>
                                            <label for="inputType">Especifica la tipo de transaccion</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputTransaction" name="inputTransaction" type="date" />
                                            <label for="inputTransaction">Fecha de Transaccion</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputDescription" name="inputDescription" type="text" />
                                            <label for="inputDescription">Descripcion Breve</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <select class="form-control" id="inputMonths" name="inputMonths">
                                                <option value="1">Una exposicion</option>
                                                <option value="3">3 meses</option>
                                                <option value="6">6 meses</option>
                                                <option value="9">9 meses</option>
                                                <option value="12">12 meses</option>
                                                <option value="15">15 meses</option>
                                                <option value="18">18 meses</option>
                                                <option value="24">24 meses</option>
                                                <option value="27">27 meses</option>
                                                <option value="30">30 meses</option>
                                                <option value="33">33 meses</option>
                                                <option value="36">36 meses</option>
                                                <option value="39">39 meses</option>
                                                <option value="42">42 meses</option>
                                                <option value="45">45 meses</option>
                                                <option value="48">48 meses</option>
                                            </select>
                                            <label for="inputMonths">Diferido a cuantos meses</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputTotal" name="inputTotal" type="number" min=1 step="any"/>
                                            <label for="inputTotal">Total de la Transaccion</label>
                                        </div>
                                        <!--
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputTotalPaid" name="inputTotalPaid" type="number" min=0 value=0 step=any />
                                            <label for="inputTotalPaid">Total Pagado</label>
                                        </div>-->
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button type="submit" class="btn btn-primary">Guardar</a>
                                        </div>
                                    </form>
                                </p>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="js/input_currency.js"></script>
    </body>
</html>
