<?php
require_once 'php/functions.php';
$functions = new Functions();
if(!$functions->LoggedIn()){
    $functions->Redirect("login.php");
    return;
}

$total_in_months = 0;
$total_in_1exp = 0;
$lastTransactionEndDate = null;

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
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <?=$functions->showNavMenu()?>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Transacciones
                            </div>
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
                                            <th>Por cubrir al <?=$functions->dia_limite?>-<?=$functions->GetCurrentTime()->format('m')?></th>
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
                                            <th>Por cubrir al <?=$functions->dia_limite?>-<?=$functions->GetCurrentTime()->format('m')?></th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
					                        $total_paid_all_time = 0;
					                        $total_to_pay_all_time = 0;
                                            $transactions = $functions->getTransactions($_SESSION['uid']);
                                            $sum_to_pay = 0;
                                            $total_this_month = 0;
                                            $total_monthly = 0;
                                            for($i=0; $i<count($transactions); $i++) {
                                                $transaction = $transactions[$i];
						                        if($transaction['total'] - $transaction['total_paid'] > 1)
                                                	$sum_to_pay += $transaction['minimum_pay_to_current_month'];
                                                    $total_this_month += $transaction['total'] / $transaction['months'];

                                                    if($transaction['months'] > 1){
                                                        $total_monthly += $transaction['total'] / $transaction['months'];
                                                    }
                                                    
						                            $total_paid_all_time += $transaction['total_paid'];
						                            $total_to_pay_all_time += $transaction['total'];

                                                    $plazo = $transaction['months'] == 1 ? '1 EXP' : ($transaction['type'] == '1' ? 'MSI' : 'MCI');

                                                    if($plazo == "1 EXP")
                                                        $total_in_1exp += $transaction['total'] - $transaction['total_paid'];
                                                    else
                                                        $total_in_months += $transaction['total'] - $transaction['total_paid'];

                                                    //if($lastTransactionEndDate)
                                                    $end_date = $transaction['end_date'];
                                                    if ($lastTransactionEndDate === null || $end_date > $lastTransactionEndDate) {
                                                        $lastTransactionEndDate = $end_date;
                                                    }
                                            ?>
                                                <tr>
                                                    <td><?=$plazo?>
                                                    <td><?=date('d/m/Y', strtotime($transaction['start_date']))?></td>
                                                    <td><?=date('d/m/Y', strtotime($transaction['end_date']))?></td>
                                                    <td><?=$transaction['description']?></td>
                                                    <td><?=$transaction['remaining_months/months']?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['total'])?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['total_paid'])?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['total'] - $transaction['total_paid'])?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['total'] / $transaction['months'])?> <?=$functions->isPaidDictionary($transaction['is_paid']) ?? ''?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['minimum_pay_to_current_month'])?></td>
                                                </tr>
                                            <?php
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Informacion
                                    </div>
                                    <div class="card-body">
                                        <p><b>Total esta mensualidad:</b> <?=$functions->FormatCurrency($total_this_month)?></p>
                                        <p><b>Total pagos fijos mensuales:</b> <?=$functions->FormatCurrency($total_monthly)?>
                                        <p><b>Total a pagar antes del limite:</b> <?=$functions->FormatCurrency($sum_to_pay)?></p>
                                        <p><b>Cortes cada:</b> <?=$functions->dia_corte?> de cada mes</p>
                                        <p><b>Limites de pago:</b> <?=$functions->dia_limite?> de cada mes</p>
                                        <p><b>Total restante a pagar del credito:</b> <?=$functions->FormatCurrency($total_to_pay_all_time - $total_paid_all_time)?></p>
					                    <p><b>Total pagado:</b> <?=$functions->FormatCurrency($total_paid_all_time)?></p>
					                    <p><b>Total transacciones registradas:</b> <?=$functions->FormatCurrency($total_to_pay_all_time)?></p>
                                        <p><b>Credito total:</b> <?=$functions->FormatCurrency($functions->limite_credito)?></p>
                                        <p><b>Credito disponible:</b> <?=$functions->FormatCurrency($functions->limite_credito - $total_to_pay_all_time)?></p>
                                        <p><b>Fin de deuda:</b> <?=date('d/m/Y', strtotime($lastTransactionEndDate))?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-pie me-1"></i>
                                        Desglose de Credito
                                    </div>
                                    <div class="card-body"><canvas id="myPieChart" width="100%" height="50"></canvas></div>
                                    <div class="card-footer small text-muted">Segun el limite proporcionado y transacciones proporcionadas.</div>
                                </div>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <script>
            // Set new default font family and font color to mimic Bootstrap's default styling
            Chart.defaults.global.defaultFontFamily = '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
            Chart.defaults.global.defaultFontColor = '#292b2c';

            // Pie Chart Example
            var ctx = document.getElementById("myPieChart");
            var myPieChart = new Chart(ctx, {
              type: 'pie',
              data: {
                labels: ["Disponible", "Meses", "1EXP"],
                datasets: [{
                  data: [<?=$functions->limite_credito - $total_to_pay_all_time?>, <?=$total_in_months?>, <?=$total_in_1exp?>],
                  backgroundColor: ['#007bff', '#dc3545', '#28a745'],
                }],
              },
            });
        </script>
    </body>
</html>
