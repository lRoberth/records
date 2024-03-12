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
                        <h1 class="mt-4">Realizar Pago</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Pagar</li>
                        </ol>
                        <div class="card mb-4">
                        <?=$query_error == false ? '' : '<div class="card-body"><div class="mb-0"><div class="form-floating mb-3 error">'.$query_error.'</div></div></div>'?>
                            <?php if(isset($_GET['id'])){ 
                                $transaction = $functions->getTransactions($_GET['id'], false);
                                if(count($transaction) < 1){
                                    die('<script>window.location.href = "edit.php";</script>');
                                }
                                $transaction = $transaction[0];

                                $msc = [
                                    '1' => '',
                                    '2' => ''
                                ];

                                $msc[$transaction['type']] = 'selected';
                                
                                $mss = [
                                    '1' => '',
                                    '3' => '',
                                    '6' => '',
                                    '9' => '',
                                    '12' => '',
                                    '15' => '',
                                    '18' => '',
                                    '24' => '',
                                    '27' => '',
                                    '30' => '',
                                    '33' => '',
                                    '36' => '',
                                    '39' => '',
                                    '42' => '',
                                    '45' => '',
                                    '48' => ''
                                ];

                                $mss[$transaction['months']] = 'selected';
                                ?>
                                
                                <div class="card-body">
                                    <table id="datatablesSimple">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Fecha de Compra</th>
                                                <th>Termino de Pagos</th>
                                                <th>Descripcion</th>
                                                <th>Mensualidades</th>
                                                <th>Total</th>
                                                <th>Total Pagado</th>
                                                <th>Restante</th>
                                                <th>Pagos Mensuales</th>
                                                <th>Por cubrir al <?=date('m-20')?></th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Fecha de Compra</th>
                                                <th>Termino de Pagos</th>
                                                <th>Descripcion</th>
                                                <th>Mensualidades</th>
                                                <th>Total</th>
                                                <th>Total Pagado</th>
                                                <th>Restante</th>
                                                <th>Pagos Mensuales</th>
                                                <th>Por cubrir al <?=date('m-20')?></th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <?php
                                                $transactions = $functions->getTransactions($_GET['id'], false);
                                                for($i=0; $i<count($transactions); $i++) {
                                                    $transaction = $transactions[$i];
                                                    $is_paid_dicc = [
                                                        0 => '(Pago Pend.)',
                                                        1 => '(Pagado)',
                                                        2 => '(Finiquitado)'
                                                    ];
                                                ?>
                                                    <tr>
                                                        <td><?=($transaction['type'] == '1' ? 'MSI' : 'MCI')?>
                                                        <td><?=date('d/m/Y', strtotime($transaction['start_date']))?></td>
                                                        <td><?=date('d/m/Y', strtotime($transaction['end_date']))?></td>
                                                        <td><?=$transaction['description']?></td>
                                                        <td><?=$transaction['remaining_months/months']?></td>
                                                        <td><?=$functions->FormatCurrency($transaction['total'])?></td>
                                                        <td><?=$functions->FormatCurrency($transaction['total_paid'])?></td>
                                                        <td><?=$functions->FormatCurrency($transaction['total'] - $transaction['total_paid'])?></td>
                                                        <td><?=$functions->FormatCurrency($transaction['total'] / $transaction['months'])?> <?=$is_paid_dicc[$transaction['is_paid']] ?? ''?></td>
                                                        <td><?=$functions->FormatCurrency($transaction['minimum_pay_to_current_month'])?></td>
                                                    </tr>
                                                <?php
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <div class="card-body">
                                    <p class="mb-0">
                                        <form action='php/pays.php' method='post'>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputTotalPaid" name="inputTotalPaid" type="number" min=1 value="<?= $transaction['minimum_pay_to_current_month'] != 0 ? $transaction['minimum_pay_to_current_month'] : 100 ?>" step="any"/>
                                                <label for="inputTotalPaid">Cantidad del Pago</label>
                                            </div>
                                            
                                            <input type="hidden" id="id" name="id" value="<?=$transaction['id']?>"/>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button type="submit" class="btn btn-primary">Registrar Pago</a>
                                            <button type="button" onclick='window.location.href = "pays.php"' class="btn btn-secondary">Cancelar Pago</a>
                                            
                                            </div>
                                        </form>
                                    </p>
                                </div>
                            <?php } else { ?>
                        <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Fecha de Compra</th>
                                            <th>Termino de Pagos</th>
                                            <th>Descripcion</th>
                                            <th>Mensualidades</th>
                                            <th>Total</th>
                                            <th>Total Pagado</th>
                                            <th>Restante</th>
                                            <th>Pagos Mensuales</th>
                                            <th>Por cubrir al <?=date('m-20')?></th>
                                            <th>Pagar</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Fecha de Compra</th>
                                            <th>Termino de Pagos</th>
                                            <th>Descripcion</th>
                                            <th>Mensualidades</th>
                                            <th>Total</th>
                                            <th>Total Pagado</th>
                                            <th>Restante</th>
                                            <th>Pagos Mensuales</th>
                                            <th>Por cubrir al <?=date('m-20')?></th>
                                            <th>Pagar</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                            $transactions = $functions->getTransactions($_SESSION['uid']);
                                            for($i=0; $i<count($transactions); $i++) {
                                                $transaction = $transactions[$i];
                                            ?>
                                                <tr>
                                                    <td><?=($transaction['type'] == '1' ? 'MSI' : 'MCI')?>
                                                    <td><?=date('d/m/Y', strtotime($transaction['start_date']))?></td>
                                                    <td><?=date('d/m/Y', strtotime($transaction['end_date']))?></td>
                                                    <td><?=$transaction['description']?></td>
                                                    <td><?=$transaction['remaining_months/months']?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['total'])?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['total_paid'])?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['total'] - $transaction['total_paid'])?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['total'] / $transaction['months'])?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['minimum_pay_to_current_month'])?></td>
                                                    <td><a class="btn btn-primary" href="pays.php?id=<?=$transaction['id']?>">Pagar</a></td>
                                                </tr>
                                            <?php
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php } ?>
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
