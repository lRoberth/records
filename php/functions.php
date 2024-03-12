<?php
/*
Agregar mensaje de confirmacion antes de borrar
Finalizar Pie Chart

"REVIEW" getTransactionByID iphone error
*/
class Functions{
    private $db = [
        'host' => '192.168.2.5',
        'db' => 'records',
        'user' => 'records',
        'password' => ''
    ];

    private $ACCESS_PIN = '1293';

    public $timeDebugging = [
        'enable' => false,
        'y' => null,
        'm' => 7,
        'd' => 11
    ];

    public $mysqli = null;

    public $dia_corte = 11;
    public $dia_limite = 20;
    public $limite_credito = 0;
    public $intereses = 0;

    function __construct($db_array = null){
        $this->db = $db_array ?? $this->db;
        $dbvar = $this->db;
        $this->mysqli = new mysqli($dbvar['host'], $dbvar['user'], $dbvar['password'], $dbvar['db'], 3306);
        if($this->mysqli === false){
            die(`MySQL Error: {$this->mysqli->error}`);
        }
        if(session_status() != PHP_SESSION_ACTIVE)
            session_start();

        if($this->LoggedIn()){
            $profile = $this->GetProfile($_SESSION['uid']);
            $this->dia_corte = $profile['fechaCorte'];
            $this->dia_limite = $profile['fechaLimite'];
            $this->limite_credito = $profile['limiteCredito'];
            $this->intereses = $profile['intereses'];
        }
    }

    function GetCurrentTime(){
        $now = new DateTime();
        if($this->timeDebugging['enable']){
            $year = $this->timeDebugging['y'] ?? $now->format("Y");
            $month = $this->timeDebugging['m'] ?? $now->format("m");
            $day = $this->timeDebugging['d'] ?? $now->format("d");

            $now->setDate($year, $month, $day);
        }

        return $now;
    }

    function LoggedIn(){
        return isset($_SESSION['uid'], $_SESSION['username']);
    }

    function Home(){
        header("Location: index.php");
    }

    function Redirect($url){
        header("Location: $url");
    }

    function InsertInto($what, $data, $table, $total_data = 0){
        $string = "INSERT INTO $table ($what) VALUES (";
        $explode = explode("||", $data);

        if($total_data != 0 && count($explode) != $total_data)
            die("Failed to call InsertInto(): Provided \$data doesn't match \$total_data. (".count($explode)."/$total_data)");
        
        for($i = 0; $i < count($explode); $i++){
            $string .= '?,';
        }
        if(substr($string, -1) == ",")
            $string = rtrim($string, ",");
        $string .= ");";
        
        $stmt = $this->mysqli->prepare($string);


        $params = array_map('trim', $explode);
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            die("Error: " . $stmt->error);
        }
    }

    function SelectThing($table, $what, $where){
        $query = "SELECT $what FROM $table $where";
        
        $stmt = $this->mysqli->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }

    function DeleteThing($table, $where, $total_data){
        $query = "DELETE FROM $table WHERE $where";
        $explode = explode("AND", $where);

        if($total_data == 0 || count($explode) != $total_data)
        die("Failed to call DeleteThing(): Provided \$where doesn't match \$total_data. (".count($explode)."/$total_data)");
        
        $stmt = $this->mysqli->prepare($query);
        $return = $stmt->execute();
        $stmt->close();
        return $return;
    }

    function UpdateThing($table, $data_table, $where, $total_data = 0){
        $string = "UPDATE `$table` SET ";

        if($total_data != 0 && count($data_table) != $total_data)
            die("Failed to call UpdateThing(): Provided \$data_table doesn't match \$total_data. (".count($data_table)."/$total_data)");
        
        foreach($data_table as $k=>$v){
            if(is_string($v))
                $v = "'$v'";
            
            $string .= "`$k` = $v, ";
        }
        
        if(substr($string, -2) == ", ")
            $string = rtrim($string, ", ");
        $string .= " $where;";

        $stmt = $this->mysqli->prepare($string);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            die("Error: " . $stmt->error);
        }
    }

    function EncryptPassword($password){
        return password_hash($password, PASSWORD_DEFAULT);
    }

    function TestPassword($password, $hash){
        return password_verify($password, $hash);
    }

    function GetAccountByUsername($username){
        $query = $this->SelectThing("users", "*", "where username = '$username'");
        return $query;
    }

    function GetAccountByID($id){
        $query = $this->SelectThing("users", "*", "where uid = '$id'");
        return $query;
    }

    function CreateAccount($pin, $username, $password1, $password2, $credit, $interests, $cut, $limit){
        if($pin != $this->ACCESS_PIN){
            $this->Redirect("../register.php?err=pin");
            return false;
        }

        if($username == "null" || strlen($username) < 3){
            $this->Redirect("../register.php?err=username");
            return false;
        }
        
        $getacc = $this->GetAccountByUsername($username);

        if(count($getacc) > 0){
            $this->Redirect("../register.php?err=exists");
            return false;
        }

        if(strlen($password1) < 2 || strlen($password2) < 2){
            $this->Redirect("../register.php?err=password");
            return false;
        }

        if($password1 !== $password2){
            $this->Redirect("../register.php?err=match");
            return false;
        }

        $password1 = $this->EncryptPassword($password1);

        $insertacc = $this->InsertInto("username, password", "$username|| $password1", "users");

        if($insertacc == true){
            $uid = $this->GetAccountByUsername($username)[0]['uid'];
            if($uid){
                $profile = [
                    "limiteCredito" => $credit ?? 0,
                    "fechaCorte" => $cut ?? 1,
                    "fechaLimite" => $limit ?? 20,
                    "intereses" => $interests ?? 0
                ];

                $this->CreateProfile($uid, $profile);
                $this->Redirect("../login.php");
                return false;
            }
        }

    }

    function CreateProfile($uid, $data){
        /*
        "limiteCredito" => $limiteCredito,
        "fechaCorte" => $fechaCorte,
        "fechaLimite" => $fechaLimite,
        "intereses" => $intereses
        */
        $json = json_encode($data);
        $insertprofile = $this->InsertInto("uid, json","$uid|| $json", "profiles");
    }

    function UpdateProfile($uid, $data){
        $data = [
            "json" => json_encode($data)
        ];
        return $this->UpdateThing("profiles", $data, "WHERE `uid` = $uid", 1);
    }

    function GetProfile($uid){
        $profile = $this->SelectThing("profiles", "json", "WHERE `uid` = $uid;");
        $profile = json_decode($profile[0]['json'], true);
        return $profile;
    }

    function Login($username, $password){
        $acc = $this->GetAccountByUsername($username);
        if(count($acc) < 1){
            $this->Redirect("../login.php?err=invalid");
            return false;
        }
        
        if(!$this->TestPassword($password, $acc[0]['password'])){
            $this->Redirect("../login.php?err=invalid");
            return false;
        }

        $_SESSION['uid'] = $acc[0]['uid'];
        $_SESSION['username'] = $acc[0]['username'];

        $this->Redirect("../index.php");
        return false;
        
    }

    function EndSession(){
        unset($_SESSION);
        session_destroy();
        $this->Redirect("index.php");
    }

    function CalculateMonths($date_str, $end = false) {
        $now = $this->GetCurrentTime();
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date_str);
        $date->setDate($date->format('Y'), $date->format('m'), $this->dia_corte);
        $months = $now->diff($date)->format("%m");
        $days = $now->diff($date)->format("%d");
    
        if($days >= 1 && $months == 0) $months++;
        if($end) return $months;
    
        if($months == 0) $months = 1;
        return $months;
    }
    
    function CalculateTranscurredMonths($months, $start_date_str, $escaped = false){
        $transcurred_months = $this->CalculateMonths($start_date_str);
        if(intval($transcurred_months) > intval($months)) $transcurred_months = $months;
        if($escaped) return "$transcurred_months de ".$months.' meses'; 
        else return $transcurred_months;
    }
    
    function CalculateRemainingMonths($end_date_str){
        return $this->CalculateMonths($end_date_str, true);
    }

    function CalculateMinPay($transaction, $last_payment){
        $current_date = date('Y-m-d H:i:s');
        $payment_date_start = $transaction['start_date'];
        $transaction_months = $transaction['months'];
        $monthly_payment = $transaction['total'] / $transaction_months;
        $transcurred_months = $this->CalculateTranscurredMonths($transaction_months, $payment_date_start);
        $min_pay = $monthly_payment * $transcurred_months;
        return round($min_pay, 2);
    }

    function isPaidDictionary($id){
        return [
            0 => '(Pago Pend.)',
            1 => '(Pagado)',
            2 => '(Finiquitado)',
            3 => '(Prox. Corte)'
        ][$id];
    }

    function getTransactions($id, $uidorid = true){
        //SelectThing($table, $what, $where){
        $uidorid = $uidorid ? 'uid' : 'id';
        $transactions = $this->SelectThing("transactions", "*", "where $uidorid = $id;");
        
        foreach($transactions as $k=>$v){
            // Set end day to 20th (AMEX cut)
            $transactions[$k]['end_date'] = date('Y-m-20 H:i:s', strtotime($transactions[$k]['end_date']));

            // Get the total money paid to every transaction
            $payments = $this->getPaymentsByTransaction($v['id']);

            $total_paid = 0;
            $last_payment = null;
            if(count($payments) > 0){
                foreach($payments as $vv){
                    $total_paid += $vv['amount'];
                }

                // Sort payments by date
                $timestamps = [];
                foreach($payments as $kk=>$vv){
                    $timestamps[$kk] = $vv['trans_date'];
                }
                array_multisort($timestamps, SORT_DESC, $payments);

                // Get the latest payment made
                $last_payment = $payments[0];
            }

            // Assign total paid
            $transactions[$k]['total_paid'] = $total_paid;
            
            // Escaped string to show transcurred months, ex: 4 de 6 meses
            $transactions[$k]['remaining_months/months'] = $this->CalculateTranscurredMonths($v['months'], $v['start_date'], true);

            $now = $this->GetCurrentTime();

            $start_date = $transactions[$k]['start_date'];
            $start_date = DateTime::createFromFormat('Y-m-d H:i:s', $start_date);

            $calculatePaidVal = false;
            if($start_date->format('m') == $now->format('m') && $start_date->format('d') <= $this->dia_corte)
                $calculatePaidVal = true;
            elseif($start_date->format('m') != $now->format('m'))
                $calculatePaidVal = true;

            // DEBUGGING
            /*
            if($v['description'] == "internet"){
                echo "Purchase month: '" . $start_date->format('m') . "'";
                echo "<br>Current month: '" . $now->format('m') . "'";
                echo "<br><br>ST 1: ";
                if($start_date->format('m') == $now->format('m'))
                    echo 'true';
                else
                    echo 'false';
                echo "<br><br>Purchase day: " . $start_date->format('d');
                echo "<br>Current day: " . $now->format("d");
                echo "<br>ST 2 (P.D <= 11): ";
                if($start_date->format('d') <= 11)
                    echo 'true';
                else
                    echo 'false';
            }*/

            if($calculatePaidVal){
                $minimum_pay = $this->CalculateMinPay($transactions[$k], $last_payment) ?? 0;
                if($transactions[$k]['total'] - $total_paid <= 0.10)
                    $transactions[$k]['is_paid'] = 2; // transaction finished
                else {
                    if($total_paid >= $minimum_pay)
                        $transactions[$k]['is_paid'] = 1; // month completely covered
                    else{
                        $transactions[$k]['is_paid'] = 0; // month not covered
                    }
                }
                // is_paid =
                // 0 = month not paid
                // 1 = month paid
                // 2 = totally paid
                // 3 = doesn't apply in current cut

                // Assign the minimum payment to today before 20th
                $pending_pay = $minimum_pay - $total_paid;
                $transactions[$k]['minimum_pay_to_current_month'] = $pending_pay > 0 ? $pending_pay : 0;
            }
            else
            {
                $transactions[$k]['is_paid'] = 3;
                $transactions[$k]['minimum_pay_to_current_month'] = 0;
            }
            
            if($transactions[$k]['is_paid'] == 2)
                $transactions[$k]['minimum_pay_to_current_month'] = 0;
        }
        return $transactions;
    }

    // DEPRACATED: USE getTransactions($id, $uidorid = true) instead.
    function getTransactionByID($id){
        trigger_error('DEPRACATED', E_USER_ERROR);
        $transactions = $this->SelectThing("transactions", "*", "where id = $id;");
        foreach($transactions as $k=>$v){
            $transactions[$k]['remaining_months/months'] = $this->CalculateTranscurredMonths($v['months'], $v['start_date']);
            $transactions[$k]['is_paid'] = floatval($transactions[$k]['total_paid']) >= floatval($transactions[$k]['total']) ? true : false;
            //die($transactions[$k]['total_paid'] . ' | ' . $transactions[$k]['total']); // REVIEW
        }
        return $transactions;
    }

    function AddTransaction($vars){
        //$insertacc = $this->InsertInto("username, password", "$username|| $password1", "users");
        //function InsertInto($what, $data, $table)
        if(!$this->LoggedIn())
            return false;
        if(empty($vars['uid']))
            return false;
        if(count($this->GetAccountByID($vars['uid'])) < 1)
            return false;
        
        $uid = $vars['uid'];
        $type = $vars['type'];
        $description = $vars['description'];

        $start_date = date('Y-m-d', strtotime($vars['transaction']));
        $months = intval($vars['months']);
        $end_date = date('Y-m-d', strtotime("+$months months", strtotime($start_date)));

        $total = floatval(str_replace("MX$", "", str_replace(",", "", $vars['total'])));
        $total_paid = str_replace("MX$", "", str_replace(",", "", $vars['total_paid']));

        $insert = $this->InsertInto("uid, type, start_date, end_date, description, months, total, total_paid", "$uid|| $type|| $start_date|| $end_date|| $description|| $months|| $total|| $total_paid", "transactions", 8);
        return $insert;
    }

    function RemoveTransaction($vars){ // uid, id
         //DeleteThing(table, where)
        if(!$this->LoggedIn())
            return false;
        if(empty($vars['uid']) || empty($vars['id']))
            return false;
        if(count($this->GetAccountByID($vars['uid'])) < 1)
            return false;

        $uid = $vars['uid'];
        $id = $vars['id'];

        if($uid != $_SESSION['uid'])
            return false;
        
        $this->DropPaymentsFromTransaction($id, $uid);
        return $this->DeleteThing("transactions", "id = $id AND uid = $uid", 2);
    }

    function EditTransaction($vars){
         //UpdateThing($table, $data_table, $where, $total_data = 0){
         if(!$this->LoggedIn())
            return false;
        if(empty($vars['uid']) || empty($vars['id']))
            return false;
        if(count($this->GetAccountByID($vars['uid'])) < 1)
            return false;
        
        $uid = $vars['uid'];
        if($uid != $_SESSION['uid'])
            return false;
        
        $type = intval($vars['type']);
        $description = $vars['description'];

        $start_date = date('Y-m-d', strtotime($vars['transaction']));
        $months = intval($vars['months']);
        $end_date = date('Y-m-d', strtotime("+$months months", strtotime($start_date)));

        $total = floatval(str_replace("MX$", "", str_replace(",", "", $vars['total'])));
        $total_paid = floatval(str_replace("MX$", "", str_replace(",", "", $vars['total_paid'])));

        $data_table = [
            'type' => $type,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'description' => $description,
            'months' => $months,
            'total' => $total,
            'total_paid' => $total_paid
        ];

        return $this->UpdateThing('transactions', $data_table, "WHERE `uid` = ".$vars['uid']." AND `id` = ".$vars['id'], 7);
    }

    function GetPaymentsByUID($id = null){
        $query = $this->SelectThing("payments", "*", $id == null ? '' : "WHERE uid = '$id'");
        return $query;
    }

    function GetPaymentsByID($id = null){
        $query = $this->SelectThing("payments", "*", $id == null ? '' : "WHERE id = '$id'");
        return $query;
    }

    function GetPaymentsByTransaction($id){
        $query = $this->SelectThing("payments", "*", "WHERE trans_id = '$id'");
        return $query;
    }

    function PayTransaction($vars){
        if(!$this->LoggedIn())
            return false;
        if(empty($vars['uid']) || empty($vars['id']) || empty($vars['total_paid']))
            return false;
        if(count($this->GetAccountByID($vars['uid'])) < 1)
            return false;
        
        if($vars['uid'] != $_SESSION['uid'])
            return false;

        $transaction = $this->getTransactions($vars['id'], false);
        
        if(count($transaction) < 1)
            return false;
        
        $transaction = $transaction[0];
        
        $total_paid = $transaction['total_paid'] + floatval($vars['total_paid']);

        $data_table = [
            'uid' => $vars['uid'],
            'id' => $vars['id'],
            'total_paid' => $total_paid
        ];
        
        $insert = $this->InsertInto("uid, trans_id, amount", $vars['uid']."|| ".$vars['id']."|| ".$vars['total_paid'], "payments");
        if($insert == false){
            $this->Redirect("../payments.php?err=3");
            return false;
        }

        return $this->UpdateThing('transactions', $data_table, "WHERE `uid` = ".$vars['uid']." AND `id` = ".$vars['id'], 3);

    }

    function EditPayment($vars){
        //UpdateThing($table, $data_table, $where, $total_data = 0){
        if(!$this->LoggedIn())
           return false;
       if(empty($vars['uid']) || empty($vars['id']))
           return false;
       if(count($this->GetAccountByID($vars['uid'])) < 1)
           return false;
       
       $uid = $vars['uid'];
       if($uid != $_SESSION['uid'])
           return false;
       
       $amount = intval($vars['amount']);

       $payment_date = date('Y-m-d', strtotime($vars['trans_date']));
       
       $data_table = [
           'amount' => $amount,
           'trans_date' => $payment_date,
       ];

       return $this->UpdateThing('payments', $data_table, "WHERE `uid` = ".$vars['uid']." AND `id` = ".$vars['id'], 2);
   }

   function DropPaymentsFromTransaction($transaction_id, $uid){
    if(!$this->LoggedIn())
        return false;
    if(empty($transaction_id) || empty($uid))
        return false;
    if(count($this->GetAccountByID($uid)) < 1)
        return false;
    return $this->DeleteThing("payments", "uid = $uid AND trans_id = $transaction_id", 2);
   }

   function RemovePayment($vars){
    //DeleteThing(table, where)
    if(!$this->LoggedIn())
       return false;
    if(empty($vars['uid']) || empty($vars['id']))
       return false;
    if(count($this->GetAccountByID($vars['uid'])) < 1)
       return false;

    $uid = $vars['uid'];
    $id = $vars['id'];

    if($uid != $_SESSION['uid'])
       return false;
    return $this->DeleteThing("payments", "id = $id AND uid = $uid", 2);
    }

    function FormatCurrency($number){
        $number = $number ?? 0;
        $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
        return 'MX'.$fmt->format($number);
    }

    function showNavMenu(){
        echo '
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Inicio</div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-money-check"></i></div>
                            Transacciones
                        </a>
                        <div class="sb-sidenav-menu-heading">Pagos</div>
                        <a class="nav-link" href="pays.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                            Realizar Pago
                        </a>
                        <a class="nav-link" href="payments.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                            Consultar Pagos
                        </a>
                        <div class="sb-sidenav-menu-heading">Editar</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Editar o Añadir
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="edit.php"><div class="sb-nav-link-icon"><i class="fas fa-pen-to-square"></i></div>Editar Transaccion</a>
                                <a class="nav-link" href="add.php"><div class="sb-nav-link-icon"><i class="fas fa-plus"></i></div>Añadir Transaccion</a>
                            </nav>
                        </div>
                        <a class="nav-link" href="profile.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                            Perfil
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Bienvenido,</div>
                    '.$_SESSION['username'].'
                </div>
            </nav>
        </div>';
    }
}