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
        '2' => 'Ocurrio un error, contacta al administrador (2)',
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
                        <h1 class="mt-4">Editar Transaccion</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                            <li class="breadcrumb-item active">Editar</li>
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
                                    <p class="mb-0">
                                        <form action='php/edit.php' method='post'>
                                            <div class="form-floating mb-3">

                                                <select class="form-control" id="inputType" name="inputType">
                                                    <option <?=$msc['1']?> value="1">MSI</option>
                                                    <option <?=$msc['2']?> value="2">MCI</option>
                                                </select>
                                                <label for="inputType">Especifica la tipo de transaccion</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputTransaction" name="inputTransaction" type="date" />
                                                <label for="inputTransaction">Fecha de Transaccion</label>

                                                <script>document.getElementById("inputTransaction").valueAsDate = new Date('<?=$transaction['start_date']?>')</script>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputDescription" name="inputDescription" value="<?=$transaction['description']?>" type="text" />
                                                <label for="inputDescription">Descripcion Breve</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <select class="form-control" id="inputMonths" name="inputMonths">
                                                    <option <?=$mss['1']?> value="1">Una exposicion</option>
                                                    <option <?=$mss['3']?> value="3">3 meses</option>
                                                    <option <?=$mss['6']?> value="6">6 meses</option>
                                                    <option <?=$mss['9']?> value="9">9 meses</option>
                                                    <option <?=$mss['12']?> value="12">12 meses</option>
                                                    <option <?=$mss['15']?> value="12">12 meses</option>
                                                    <option <?=$mss['18']?> value="18">18 meses</option>
                                                    <option <?=$mss['24']?> value="24">24 meses</option>
                                                    <option <?=$mss['27']?> value="27">27 meses</option>
                                                    <option <?=$mss['30']?> value="30">30 meses</option>
                                                    <option <?=$mss['33']?> value="33">33 meses</option>
                                                    <option <?=$mss['36']?> value="36">36 meses</option>
                                                    <option <?=$mss['39']?> value="39">39 meses</option>
                                                    <option <?=$mss['42']?> value="42">42 meses</option>
                                                    <option <?=$mss['45']?> value="45">45 meses</option>
                                                    <option <?=$mss['48']?> value="48">48 meses</option>
                                                </select>
                                                <label for="inputMonths">Diferido a cuantos meses</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputTotal" name="inputTotal" value="<?=$transaction['total']?>" type="number" min=0/>
                                                <label for="inputTotal">Total de la Transaccion</label>
                                            </div>
                                            <!--<div class="form-floating mb-3">
                                                <input class="form-control" id="inputTotalPaid" name="inputTotalPaid" value="<?=$transaction['total_paid']?>" type="number" min=0 value=0 step=any />
                                                <label for="inputTotalPaid">Total Pagado</label>
                                            </div>-->
                                            <input type="hidden" id="id" name="id" value="<?=$transaction['id']?>"/>
                                            <input type="hidden" id="action" name="action" value=""/>
                                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button type="submit" onclick="document.getElementById('action').value = 'edit';" class="btn btn-primary">Guardar</a>
                                            <button type="button" onclick="window.location.href = 'edit.php'" class="btn btn-secondary">Cancelar Edicion</a>
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
                                            <th>Fecha</th>
                                            <th>Descripcion</th>
                                            <th>Total</th>
                                            <th>Por pagar</th>
                                            <th>Editar</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Descripcion</th>
                                            <th>Total</th>
                                            <th>Por pagar</th>
                                            <th>Editar</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                            $transactions = $functions->getTransactions($_SESSION['uid']);
                                            for($i=0; $i<count($transactions); $i++) {
                                                $transaction = $transactions[$i];
                                                $toPay = $transaction['total'] - $transaction['total_paid'];
                                            ?>
                                                <tr>
                                                    <td><?=date('d/m/Y', strtotime($transaction['start_date']))?></td>
                                                    <td><?=$transaction['description']?></td>
                                                    <td><?=$functions->FormatCurrency($transaction['total'])?></td>
                                                    <td><?=$toPay <= 0.5 ? 'Finiquitado' : $functions->FormatCurrency($toPay)?></td>
                                                    <td><a class="btn btn-primary" href="edit.php?id=<?=$transaction['id']?>">Editar</a></td>
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
        <script src="js/input_currency.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>
