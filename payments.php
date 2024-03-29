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
        '1' => 'Ocurrio un error, contacta al administrador (1)',
        '2' => 'Ocurrio un error, contacta al administrador (2)',
        '3' => 'Ocurrio un error, contacta al administrador (3)',
        'invalid' => 'Por favor especifica el valor a pagar',
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
                        <h1 class="mt-4">Consultar Pagos</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Pagos</li>
                        </ol>
                        <div class="card mb-4">
                        <?=$query_error == false ? '' : '<div class="card-body"><div class="mb-0"><div class="form-floating mb-3 error">'.$query_error.'</div></div></div>'?>
                        <?php if(isset($_GET['id'])){
                            $payment = $functions->GetPaymentsByID($_GET['id']);
                            if(count($payment) < 1){
                                die('<script>window.location.href = "edit.php";</script>');
                            }
                            $payment = $payment[0];
                            ?>
                            <div class="card-body">
                                <p class="mb-0">
                                    <form action='php/payments.php' method='post'>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputTransaction" name="inputTransaction" type="date" />
                                            <label for="inputTransaction">Fecha del Pago</label>

                                            <script>document.getElementById("inputTransaction").valueAsDate = new Date('<?=$payment['trans_date']?>')</script>
                                        </div>

                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputTotalPaid" name="inputTotalPaid" type="number" min=1 value="<?=$payment['amount']?>" step="any"/>
                                            <label for="inputTotalPaid">Cantidad del Pago</label>
                                        </div>
                                        
                                        <input type="hidden" id="id" name="id" value="<?=$payment['id']?>"/>
                                        <input type="hidden" id="action" name="action" value=""/>

                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button type="submit" onclick="document.getElementById('action').value = 'edit';" class="btn btn-primary">Guardar</a>
                                            <button type="button" onclick="window.location.href = 'payments.php'" class="btn btn-secondary">Cancelar Edicion</a>
                                            <button type="submit" onclick="document.getElementById('action').value = 'remove';" class="btn btn-danger">Eliminar</a>
                                        </div>
                                    </form>
                                </p>
                            </div>
                            <hr>
                            <?php } ?>
                        <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Descripcion del Articulo</th>
                                            <th>Total del Articulo</th>
                                            <th>Fecha del Pago</th>
                                            <th>Total del Pago</th>
                                            <th>Editar</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Descripcion del Articulo</th>
                                            <th>Total del Articulo</th>
                                            <th>Fecha del Pago</th>
                                            <th>Total del Pago</th>
                                            <th>Editar</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                            $payments = $functions->GetPaymentsByUID($_SESSION['uid']);
                                            foreach($payments as $payments){
                                                $transaction = $functions->getTransactions($payments['trans_id'], false);
                                                $transaction = $transaction[0];
                                            ?>
                                                <tr>
                                                    <td><?=$transaction['description']?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['total'])?></td>
                                                    <td><?=date('d/m/Y', strtotime($payments['trans_date']))?></td>
                                                    <td><?=$functions->FormatCurrency($payments['amount'])?></td>
                                                    <td><a class="btn btn-primary" href="payments.php?id=<?=$payments['id']?>">Editar</a></td>
                                                </tr>
                                            <?php
                                            }
                                        ?>
                                    </tbody>
                                </table>
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
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>
