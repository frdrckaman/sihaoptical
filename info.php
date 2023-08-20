<?php
require_once'php/core/init.php';
$user = new User();
$random = new Random();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
$total_orders=0;$pending=0;$confirmed=0;$received=0;$RE=null;$LE=null;$message=null;
$tLens=0;$tPatient=0;$tFrame=0;$tL=0;$searchValue=null;$medRec=null;
$orders = $override->get('lens_orders','staff_id',$user->data()->id);$lens_orders1=null;
$getStatus = $override->getData('order_status');
switch($_GET['id']){
    case 0:
        $head = 'ORDERS INFORMATION';
        break;
    case 1:
        $head = 'ORDERS INFORMATION';
        break;
    case 2:
        $head = 'ORDERS INFORMATION';
        break;
    case 3:
        $head = 'ORDERS INFORMATION';
        break;
    case 20:
        $head = 'PATIENT RECORDS';
        break;
    case 4:
        $head = 'SEARCH INFORMATION';
        break;
    case 5:
        $head = 'LENS IN STOCK';
        break;
    case 6:
        $head = 'FRAMES IN STOCK';
        break;
    case 7:
        $head = 'MEDICINE IN STOCK';
        break;
    case 8:
        $head = 'LENS IN STOCK';
        break;
    case 9:
        $head = 'MEDICINE IN STOCK';
        break;
    case 10:
        $head = 'ORDER NOTIFICATION';
        break;
    case 11:
        $head = 'My Notifications';
        break;
    case 12:
        $head = 'My Notifications';
        break;
    case 13:
        $head = 'My Notifications';
        break;
    case 14:
        $head = 'My Stock';
        break;
    case 15:
        $head = 'Sales Details';
        break;
    case 16:
        $head = 'Enter Record';
        break;
    case 17:
        $head = 'Payment Record';
        break;
    case 18:
        $head = 'Appointments';
        break;
    case 19:
        $head = 'Waiting List';
        break;
    case 21:
        $head = 'Diagnosis List';
        break;
    case 22:
        $head = 'Patient Records';
        break;
    case 23:
        $head = 'Clinics Report';
        break;
}
if($user->isLoggedIn()) {
    if ($user->data()->access_level > 1 && $user->data()->access_level <= 7) {
        if ($getStatus) {
            foreach ($getStatus as $orderStatus) {
                $orderDetail = $override->get('lens_orders', 'id', $orderStatus['order_id']);
                if ($orderDetail[0]['staff_id'] == $user->data()->id) {
                    switch ($orderStatus['status']) {
                        case 0:
                            $pending++;
                            break;
                        case 1:
                            $confirmed++;
                            break;
                        case 2:
                            $received++;
                            break;
                    }
                }
                $total_orders++;
            }
        }
        if ($getStatus) {
            foreach ($getStatus as $orderStatus) {
                $orderDetail = $override->get('lens_orders', 'id', $orderStatus['order_id']);
                if ($orderDetail[0]['staff_id'] == $user->data()->id) {
                    switch ($orderStatus['status']) {
                        case 0:
                            $pending++;
                            break;
                        case 1:
                            $confirmed++;
                            break;
                        case 2:
                            $received++;
                            break;
                    }
                }
                $total_orders++;
            }
        }
        if (Input::exists('post')) {
            if (Input::get('addOrder')) {
                $validate = new validate();
                $validate = $validate->check($_POST, array(
                    'ref_no' => array(
                        'required' => true,
                        'min' => 2,
                        'unique' => 'lens_orders'
                    ),
                    'product' => array(
                        'required' => true,
                    ),
                    'order_from' => array(
                        'required' => true,
                    ),
                ));
                if ($validate->passed()) {
                    try {
                        $user->createRecord('lens_orders', array(
                            'ref_no' => Input::get('ref_no'),
                            'product' => Input::get('product'),
                            'order_from' => Input::get('order_from'),
                            'material' => Input::get('material'),
                            'eye' => Input::get('eye'),
                            'RE_sph' => Input::get('RE_sph'),
                            'RE_cyl' => Input::get('RE_axis'),
                            'RE_axis' => Input::get('RE_axis'),
                            'RE_add' => Input::get('RE_add'),
                            'RE_qty' => Input::get('RE_qty'),
                            'LE_sph' => Input::get('LE_sph'),
                            'LE_cyl' => Input::get('LE_cyl'),
                            'LE_axis' => Input::get('LE_axis'),
                            'LE_add' => Input::get('LE_add'),
                            'LE_qty' => Input::get('LE_qty'),
                            'order_details' => Input::get('details'),
                            'order_date' => date('Y-m-d'),
                            'staff_id' => $user->data()->id,
                        ));
                        $status = $override->get('lens_orders', 'ref_no', Input::get('ref_no'));
                        $user->createRecord('order_status', array(
                            'name' => 'pending',
                            'order_id' => $status[0]['id'],
                        ));
                        $successMessage = 'Order have been Successful Registered';

                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                } else {
                    $pageError = $validate->errors();
                }
            }
            elseif(Input::get('updateOrderStatus')){
                $validate = new validate();
                $validate = $validate->check($_POST, array(
                    'status' => array(
                        'required' => true,
                    ),
                ));
                if ($validate->passed()) {
                    try {
                        $user->updateRecord('lens_prescription', array(
                            'status' => Input::get('status'),
                        ),Input::get('id'));
                        $successMessage = 'Patient Lens Order have been Successful Updated';

                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                } else {
                    $pageError = $validate->errors();
                }
            }
            elseif(Input::get('assignOrder')){
                if(Input::get('assignment')){
                    try {
                        $checkLens=$override->get('lens_power','id',Input::get('lens_id'));
                        if($checkLens){
                            if(Input::get('eye') == 'BE' && $checkLens[0]['quantity'] >= 2){$newLq = 0;
                                $newLq = $checkLens[0]['quantity'] - 2;
                                $user->updateRecord('lens_power', array(
                                    'quantity' => $newLq,
                                ),$checkLens[0]['id']);
                                $user->updateRecord('lens_prescription', array(
                                    'status' => 1,
                                ),Input::get('id'));
                                $successMessage = 'Lens have been Successful Assigned';
                            }elseif(Input::get('eye') == 'RE' || Input::get('eye') == 'LE' && $checkLens[0]['quantity'] >= 1){$newLq1=0;
                                $newLq1 = $checkLens[0]['quantity'] - 1;
                                $user->updateRecord('lens_power', array(
                                    'quantity' => $newLq1,
                                ),$checkLens[0]['id']);
                                $user->updateRecord('lens_prescription', array(
                                    'status' => 1,
                                ),Input::get('id'));
                                $successMessage = 'Lens have been Successful Assigned';
                            }else{
                                $errorMessage='Lens not available in stock,Please make an order';
                            }
                        }

                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                }
            }
            elseif(Input::get('updateAppointment')){
                try {
                    $user->updateRecord('appointment', array(
                        'appnt_date' => Input::get('date'),
                        'appnt_time' => Input::get('time'),
                        'doctor_id' => Input::get('doctor')
                    ),Input::get('id'));
                    $successMessage = 'Appointment Updated Successful';

                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }
            elseif(Input::get('deleteAppointment')){
                try{
                    $user->deleteRecord('appointment','id',Input::get('id'));
                }catch (PDOException $e){
                    $e->getMessage();
                }
            }
            elseif(Input::get('deleteWait')){
                try{
                    $user->deleteRecord('wait_list','id',Input::get('id'));
                }catch (PDOException $e){
                    $e->getMessage();
                }
            }
            elseif(Input::get('updateDiagnosis')){
                try {
                    $user->updateRecord('diagnosis', array(
                        'name' => Input::get('name'),
                    ),Input::get('id'));
                    $successMessage = 'Diagnosis Updated Successful';

                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }
            elseif(Input::get('deleteDiagnosis')){
                try{
                    $user->deleteRecord('diagnosis','id',Input::get('id'));
                }catch (PDOException $e){
                    $e->getMessage();
                }
            }
            elseif(Input::get('search_report')){
                $validate = new validate();
                $validate = $validate->check($_POST, array(
                    'clinic_branch' => array(
                        'required' => true,
                    ),
                    'category' => array(
                        'required' => true,
                    ),
                    'from' => array(
                        'required' => true,
                    ),
                    'to' => array(
                        'required' => true,
                    ),
                ));
                if ($validate->passed()) {
                    if(Input::get('category') == 1){
                        $redirect = 'info.php?id=23&b='.Input::get('clinic_branch').'&cat='.Input::get('category').'&from='.Input::get('from').'&to='.Input::get('to');
                    }
                    Redirect::to($redirect);
                } else {
                    $pageError = $validate->errors();
                }
            }
            elseif($_GET['id'] == 20){
                if(Input::get('searchPatient')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'name' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()){
                        try {
                            switch(Input::get('criteria')){
                                case 'firstname':
                                    $searchValue=$override->getLike('patient','firstname',Input::get('name'));
                                    break;
                                case 'lastname':
                                    $searchValue=$override->getLike('patient','lastname',Input::get('name'));
                                    break;
                                case 'phone_number':
                                    $searchValue=$override->getLike('patient','phone_number',Input::get('name'));
                                    break;
                                case 'id':
                                    $searchValue=$override->getLike('patient','id',Input::get('name'));
                                    break;
                            }
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
            //  }
        } else {
            switch ($user->data()->access_level) {
                case 2:
                    //Redirect::to('dashboard.php');
                    break;
                case 3:
                    // Redirect::to('dashboard.php');
                    break;
            }
        }
    } else {
        Redirect::to('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- META SECTION -->
    <title> Siha Optical | Info</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <!-- END META SECTION -->

    <!-- CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
    <!-- EOF CSS INCLUDE -->
</head>
<body class="x-dashboard">
<!-- START PAGE CONTAINER -->
<div class="page-container">
<!-- PAGE CONTENT -->
<div class="page-content">
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">

<?php include 'menuBar.php'?>
<div class="x-content">
    <div id="main-tab">
        <div class="x-content-title">
            <h1><?=$head?>&nbsp;&nbsp;</h1>
            <div class="pull-left">
                <?php if(date('Y-m-d') == $user->data()->birthday){?>
                    <button class="btn btn-warning">The management and all staff of Siha Opticsl Eye Center wish you Happy Birthday</button>
                <?php }?>
            </div>
            <div class="pull-right">
                <a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>" class="btn btn-default">REFRESH</a>
                <button class="btn btn-default">TODAY: <?=date('d-M-Y')?></button>
            </div>
        </div>
        <div class="row stacked">
            <div class="col-md-12">
            <?php foreach($override->get('staff','branch_id',$user->data()->branch_id) as $bday){if($bday['birthday'] == date('Y-m-d') && !$user->data()->birthday == date('Y-m-d')){?>
                <div class="alert alert-warning" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <strong>Today is <?=$bday['firstname'].' '.$bday['middlename'].' '.$bday['lastname']?> Birthday&nbsp;</strong>
                </div>
            <?php }}?>
                <div class="x-chart-widget">
                <?php if($successMessage){?>
                    <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <strong>Well done!&nbsp;</strong> <?=$successMessage?>
                    </div>
                <?php }elseif($errorMessage){?>
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <strong>Oops Error!&nbsp;</strong> <?=$errorMessage?>
                    </div>
                <?php }elseif($pageError){?>
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <strong>Oops Error!&nbsp;</strong> <?php foreach($pageError as $error){echo $error.' , ';}?>
                    </div>
                <?php }?><br>
                    <div class="x-chart-widget-content">
                    <?php if($_GET['id'] == 0){ $heading ='PENDING ORDERS'?>
                        <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong><?=$heading?></strong></h3>
                            <div class="btn-group pull-right">
                                <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                </ul>
                            </div>

                        </div>
                        <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="customers2" class="table datatable">
                                    <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Reference No.</th>
                                        <th>Ordered From</th>
                                        <th>Material</th>
                                        <th>Eye</th>
                                        <th>Quantity</th>
                                        <th>Details</th>
                                        <th>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                        <th>Status</th>
                                        <th>More Info</th>
                                    </tr>
                                    </thead>
                                        <tbody>
                                        <?php foreach($override->getRecOrderBy('order_status','status',0) as $pendingOrder){
                                            $order = $override->get('lens_orders','id',$pendingOrder['order_id']);$x=0;?>
                                            <tr><?php $product = $override->get('products','id',$order[0]['product'])?>
                                                <td><?=$product[0]['name']?></td>
                                                <td><?=$order[0]['ref_no']?></td>
                                                <td><?=$order[0]['order_from']?></td>
                                                <td><?=$order[0]['material']?></td>
                                                <td><?=$order[0]['eye']?></td>
                                                <td><?=($order[0]['RE_qty']+ $order[0]['LE_qty'])?></td>
                                                <td><?=$order[0]['order_details']?></td>
                                                <td><?=$order[0]['order_date']?></td>
                                                <?php $os = $override->get('order_status','order_id',$order[0]['id']);
                                                if($os[0]['status'] == 0){?>
                                                    <td><span class="label label-warning">Pending</span></td>
                                                <?php }elseif($os[0]['status'] == 1){?>
                                                    <td><span class="label label-info">Confirmed</span></td>
                                                <?php }elseif($os[0]['status'] == 2){?>
                                                    <td><span class="label label-success">Received</span></td>
                                                <?php }?>
                                                <td>
                                                    <a href="#modal<?=$x?>" class="btn btn-default btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info"></span></a>
                                                </td>
                                            </tr>
                                            <div class="modal" id="modal<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about Order</h4>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label class="col-md-2 control-label">EYE</label>
                                                                    <label class="col-md-2 control-label">SPH</label>
                                                                    <label class="col-md-2 control-label">CYL</label>
                                                                    <label class="col-md-2 control-label">AXIS</label>
                                                                    <label class="col-md-2 control-label">ADD</label>
                                                                    <label class="col-md-2 control-label">QTY</label>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-md-2 control-label">RIGHT</label>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_sph']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_cyl']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_axis']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_add']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_qty']?>" disabled/>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-md-2 control-label">LEFT</label>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_sph']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_cyl']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_axis']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_add']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_qty']?>" disabled/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $x++;}?>
                                        </tbody>
                                    </table>
                                  </div>
                             </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 1){$heading ='CONFIRMED ORDERS'?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong><?=$heading?></strong></h3>
                                <div class="btn-group pull-right">
                                    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Reference No.</th>
                                            <th>Ordered From</th>
                                            <th>Material</th>
                                            <th>Eye</th>
                                            <th>Quantity</th>
                                            <th>Details</th>
                                            <th>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th>Status</th>
                                            <th>More Info</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($override->getRecOrderBy('order_status','status',1) as $pendingOrder){
                                            $order = $override->get('lens_orders','id',$pendingOrder['order_id']);$x=0;?>
                                            <tr><?php $product = $override->get('products','id',$order[0]['product'])?>
                                                <td><?=$product[0]['name']?></td>
                                                <td><?=$order[0]['ref_no']?></td>
                                                <td><?=$order[0]['order_from']?></td>
                                                <td><?=$order[0]['material']?></td>
                                                <td><?=$order[0]['eye']?></td>
                                                <td><?=($order[0]['RE_qty']+ $order[0]['LE_qty'])?></td>
                                                <td><?=$order[0]['order_details']?></td>
                                                <td><?=$order[0]['order_date']?></td>
                                                <?php $os = $override->get('order_status','order_id',$order[0]['id']);
                                                if($os[0]['status'] == 0){?>
                                                    <td><span class="label label-warning">Pending</span></td>
                                                <?php }elseif($os[0]['status'] == 1){?>
                                                    <td><span class="label label-info">Confirmed</span></td>
                                                <?php }elseif($os[0]['status'] == 2){?>
                                                    <td><span class="label label-success">Received</span></td>
                                                <?php }?>
                                                <td>
                                                    <a href="#modal<?=$x?>" class="btn btn-default btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info"></span></a>
                                                </td>
                                            </tr>
                                            <div class="modal" id="modal<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about Order</h4>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label class="col-md-2 control-label">EYE</label>
                                                                    <label class="col-md-2 control-label">SPH</label>
                                                                    <label class="col-md-2 control-label">CYL</label>
                                                                    <label class="col-md-2 control-label">AXIS</label>
                                                                    <label class="col-md-2 control-label">ADD</label>
                                                                    <label class="col-md-2 control-label">QTY</label>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-md-2 control-label">RIGHT</label>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_sph']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_cyl']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_axis']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_add']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_qty']?>" disabled/>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-md-2 control-label">LEFT</label>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_sph']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_cyl']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_axis']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_add']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_qty']?>" disabled/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $x++;}?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 2){$heading ='RECEIVED ORDERS'?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong><?=$heading?></strong></h3>
                                <div class="btn-group pull-right">
                                    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Reference No.</th>
                                            <th>Ordered From</th>
                                            <th>Material</th>
                                            <th>Eye</th>
                                            <th>Quantity</th>
                                            <th>Details</th>
                                            <th>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                            <th>Status</th>
                                            <th>More Info</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($override->getRecOrderBy('order_status','status',2) as $pendingOrder){
                                            $order = $override->get('lens_orders','id',$pendingOrder['order_id']);$x=0;?>
                                            <tr><?php $product = $override->get('products','id',$order[0]['product'])?>
                                                <td><?=$product[0]['name']?></td>
                                                <td><?=$order[0]['ref_no']?></td>
                                                <td><?=$order[0]['order_from']?></td>
                                                <td><?=$order[0]['material']?></td>
                                                <td><?=$order[0]['eye']?></td>
                                                <td><?=($order[0]['RE_qty']+ $order[0]['LE_qty'])?></td>
                                                <td><?=$order[0]['order_details']?></td>
                                                <td><?=$order[0]['order_date']?></td>
                                                <?php $os = $override->get('order_status','order_id',$order[0]['id']);
                                                if($os[0]['status'] == 0){?>
                                                    <td><span class="label label-warning">Pending</span></td>
                                                <?php }elseif($os[0]['status'] == 1){?>
                                                    <td><span class="label label-info">Confirmed</span></td>
                                                <?php }elseif($os[0]['status'] == 2){?>
                                                    <td><span class="label label-success">Received</span></td>
                                                <?php }?>
                                                <td>
                                                    <a href="#modal<?=$x?>" class="btn btn-default btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info"></span></a>
                                                </td>
                                            </tr>
                                            <div class="modal" id="modal<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about Order</h4>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label class="col-md-2 control-label">EYE</label>
                                                                    <label class="col-md-2 control-label">SPH</label>
                                                                    <label class="col-md-2 control-label">CYL</label>
                                                                    <label class="col-md-2 control-label">AXIS</label>
                                                                    <label class="col-md-2 control-label">ADD</label>
                                                                    <label class="col-md-2 control-label">QTY</label>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-md-2 control-label">RIGHT</label>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_sph']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_cyl']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_axis']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_add']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['RE_qty']?>" disabled/>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="col-md-2 control-label">LEFT</label>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_sph']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_cyl']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_axis']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_add']?>" disabled/>
                                                                    </div>
                                                                    <div class="col-md-2">
                                                                        <input type="text" class="form-control" value="<?=$order[0]['LE_qty']?>" disabled/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $x++;}?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 3){$heading ='ALL ORDERS'?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3>List of Orders</h3>
                                    <span></span>
                                </div>
                                <ul class="panel-controls" style="margin-top: 2px;">
                                    <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                    <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="panel-body padding-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"></h3>
                                        <div class="btn-group pull-right">
                                            <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="customers2" class="table datatable table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Reference No.</th>
                                                    <th>Ordered From</th>
                                                    <th>Material</th>
                                                    <th>Eye</th>
                                                    <th>Quantity</th>
                                                    <th>Details</th>
                                                    <th>Date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                    <th>Status</th>
                                                    <th>More Info&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $x=0;foreach($override->getDataOrderBy('lens_orders') as $order){?>
                                                    <tr><?php $product = $override->get('products','id',$order['product'])?>
                                                        <td><?=$product[0]['name']?></td>
                                                        <td><?=$order['ref_no']?></td>
                                                        <td><?=$order['order_from']?></td>
                                                        <td><?=$order['material']?></td>
                                                        <td><?=$order['eye']?></td>
                                                        <td><?=($order['RE_qty']+ $order['LE_qty'])?></td>
                                                        <td><?=$order['order_details']?></td>
                                                        <td><?=$order['order_date']?></td>
                                                        <?php $os = $override->get('order_status','order_id',$order['id']);
                                                        if($os[0]['status'] == 0){?>
                                                            <td><span class="label label-warning">Pending</span></td>
                                                        <?php }elseif($os[0]['status'] == 1){?>
                                                            <td><span class="label label-info">Confirmed</span></td>
                                                        <?php }elseif($os[0]['status'] == 2){?>
                                                            <td><span class="label label-success">Received</span></td>
                                                        <?php }?>
                                                        <td>
                                                            <form method="post">
                                                                <a href="#modal<?=$x?>" class="btn btn-default btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info"></span></a>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <div class="modal" id="modal<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about Order</h4>
                                                                </div>
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">EYE</label>
                                                                            <label class="col-md-2 control-label">SPH</label>
                                                                            <label class="col-md-2 control-label">CYL</label>
                                                                            <label class="col-md-2 control-label">AXIS</label>
                                                                            <label class="col-md-2 control-label">ADD</label>
                                                                            <label class="col-md-2 control-label">QTY</label>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">RIGHT</label>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" value="<?=$order['RE_sph']?>" disabled/>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" value="<?=$order['RE_cyl']?>" disabled/>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" value="<?=$order['RE_axis']?>" disabled/>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" value="<?=$order['RE_add']?>" disabled/>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" value="<?=$order['RE_qty']?>" disabled/>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">LEFT</label>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" value="<?=$order['LE_sph']?>" disabled/>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" value="<?=$order['LE_cyl']?>" disabled/>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" value="<?=$order['LE_axis']?>" disabled/>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" value="<?=$order['LE_add']?>" disabled/>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input type="text" class="form-control" value="<?=$order['LE_qty']?>" disabled/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php $x++;}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 20){$heading ='SEARCH PATIENT RECORDS'?>
                        <div class="container">
                            <form role="form" class="form-horizontal" method="post">
                                <div class="form-group">
                                    <label class="col-md-2">Searching Criteria : </label>
                                    <div class="col-md-10">
                                        <input type="radio" name="criteria" value="firstname" checked>&nbsp;&nbsp;By Firstname&nbsp;&nbsp;
                                        <input type="radio" name="criteria" value="lastname" >&nbsp;&nbsp;By Lastname&nbsp;&nbsp;
                                        <input type="radio" name="criteria" value="phone_number" >&nbsp;&nbsp;By Phone Number&nbsp;&nbsp;
                                        <input type="radio" name="criteria" value="id" >&nbsp;&nbsp;By PID&nbsp;&nbsp;
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-1 col-md-10 col-md-offset-0">
                                        <input type="text" name="name" class="form-control" placeholder="Enter Patient Firstname / Lastname / Phone Number / PID" required="">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="submit" name="searchPatient" value="Search Patient" class="btn btn-info">
                                    </div>
                                </div>
                            </form><br>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3><?=$heading?></h3>
                                    <span></span>
                                </div>
                                <ul class="panel-controls" style="margin-top: 2px;">
                                    <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                    <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>

                            <div class="panel-body padding-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"></h3>
                                        <div class="btn-group pull-right">
                                            <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="customers2" class="table datatable table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Patient Name</th>
                                                    <th>PID</th>
                                                    <th>Sex</th>
                                                    <th>Age</th>
                                                    <th>Occupation</th>
                                                    <th>Checkup Date</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $x=0;if($searchValue){foreach($searchValue as $patient){$medRec=$override->get('checkup_record','patient_id',$patient['id']);if($medRec){?>
                                                    <tr>
                                                        <td><a href="info.php?id=24&p=<?=$patient['id']?>&c=<?=$medRec[0]['id']?>"><?=$patient['firstname'].' '.$patient['lastname']?></a> </td>
                                                        <td><?=$patient['id']?></td>
                                                        <td><?=$patient['sex']?></td>
                                                        <td><?=$patient['age']?></td>
                                                        <td><?=$patient['occupation']?></td>
                                                        <td><?=$medRec[0]['checkup_date']?></td>

                                                    </tr>

                                                    <?php }$x++;}}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }elseif($_GET['id'] == 24) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3><?=$heading?></h3>
                                    <span></span>
                                </div>
                                <ul class="panel-controls" style="margin-top: 2px;">
                                    <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                    <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>

                            <div class="panel-body padding-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"></h3>
                                        <div class="btn-group pull-right">
                                            <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="customers2" class="table datatable table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Checkup Date</th>
                                                    <th>Action Performed</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($override->getSort('checkup_record','patient_id',$_GET['p'], 'checkup_date') as $medRec){?>
                                                    <tr>
                                                        <td><?=$medRec['checkup_date']?></td>
                                                        <td>
                                                            <form method="post">
                                                                <?php if ($user->data()->access_level == 2){?>
                                                                    <a href="#modal-info<?=$x?>" class="btn btn-default btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info"></span></a>
                                                                    <a href="#lens<?=$x?>" class="btn btn-default btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-eye"></span></a>
                                                                    <a href="#med<?=$x?>" class="btn btn-default btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-medkit"></span></a>
                                                                    <a href="editInfo.php?id=0&p=<?=$medRec['id']?>&n=" class="btn btn-default btn-rounded btn-condensed btn-sm" ><span class="fa fa-pencil"></span></a>
                                                                <?php }?>
                                                                <?php if ($user->data()->access_level == 3){?>
                                                                    <a href="form.php?id=0&p=<?=$_GET['p']?>&c=<?=$_GET['c']?>" class="btn btn-default btn-rounded btn-condensed btn-sm" > Cash </a>
                                                                    <a href="form.php?id=1&p=<?=$_GET['p']?>&c=<?=$_GET['c']?>" class="btn btn-default btn-rounded btn-condensed btn-sm" > Insurance </a>
                                                                <?php }?>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <?php if ($user->data()->access_level == 2){?><?php }?>
                                                    <div class="modal" id="modal-info<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4 class="modal-title" id="defModalHead<?=$x?>">Prescription Details</h4>
                                                                </div>
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label class="col-md-1 control-label"></label>
                                                                            <div class="col-md-10">
                                                                                <textarea name="cc" class="form-control" rows="1" disabled>CC : <?=$medRec['CC']?></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2"></label>
                                                                            <div class="col-md-2">
                                                                                <input name="oh" type="text" class="form-control" value="OH: <?=$medRec['OH']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="gh" type="text" class="form-control" value="GH: <?=$medRec['GH']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="foh" type="text" class="form-control" value="FOH: <?=$medRec['FOH']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input name="fgh" type="text" class="form-control" value="FGH: <?=$medRec['FGH']?>" disabled>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2"></label>
                                                                            <div class="col-md-2">
                                                                                <input name="nfc" type="text" class="form-control" value="NPC: <?=$medRec['NPC']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="eom" type="text" class="form-control" value="EOM: <?=$medRec['EOM']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="pupils" type="text" class="form-control" value="Pupils: <?=$medRec['pupils']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <input name="confrontation" type="text" class="form-control" value="Confrontation: <?=$medRec['confrontation']?>" disabled>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-md-offset-1 col-md-1"></label>
                                                                            <div class="col-md-2">
                                                                                <input name="re" type="text" class="form-control" value="V_RE: <?=$medRec['V_RE']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="le" type="text" class="form-control" value="V_LE: <?=$medRec['V_LE']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="ph" type="text" class="form-control" value="PH_RE: <?=$medRec['PH_RE']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="ph" type="text" class="form-control" value="PH_LE: <?=$medRec['PH_LE']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="pd" type="text" class="form-control" value="PD: <?=$medRec['PD']?>" disabled>
                                                                            </div>
                                                                        </div>
                                                                        <!--Auto Ref and Rx goes Here-->
                                                                        <div class="form-group">
                                                                            <div class="col-md-offset-1 col-md-10">
                                                                                <textarea name="ext_oc_exam" class="form-control" rows="1" placeholder="External Ocular Examination: <?=$medRec['external_ocular_exam']?>" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2"></label>
                                                                            <div class="col-md-3">
                                                                                <input name="iop" type="text" class="form-control" value="IOP: <?=$medRec['IOP']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="iop_re" type="text" class="form-control" value="RE: <?=$medRec['IOP_RE']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="iop_le" type="text" class="form-control" value="LE: <?=$medRec['IOP_LE']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="iop_time" type="text" class="form-control" value="Time: <?=$medRec['IOP_time']?>" disabled>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2"></label>
                                                                            <div class="col-md-3">
                                                                                <input name="iop_post_dilation" type="text" class="form-control" value="IOP:POST Dilation <?=$medRec['IOP_POST_dilation']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="iop_post_re" type="text" class="form-control" value="RE: <?=$medRec['IOP_POST_RE']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="iop_post_le" type="text" class="form-control" value="LE: <?=$medRec['IOP_POST_LE']?>" disabled>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input name="iop_post_time" type="text" class="form-control" value="Time: <?=$medRec['IOP_POST_time']?>" disabled>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-offset-1 col-md-10">
                                                                                <input type="text" name="mydriatic_agent_used" class="form-control" value="Mydriatic Agent used: <?=$medRec['mydriatic_agent_used']?>" disabled>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-offset-1 col-md-10">
                                                                                <textarea name="internal_exam" class="form-control" rows="1" placeholder="Internal Examination Dilated/Undilated: <?=$medRec['internal_exam']?>" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-offset-1 col-md-10">
                                                                                <textarea name="diagnosis" class="form-control" rows="1" placeholder="Diagnosis: <?=$medRec['diagnosis']?>" disabled>Diagnosis: <?php foreach($override->get('diagnosis_prescription','checkup_id',$medRec['id']) as $d){$dg=$override->get('diagnosis','id',$d['diagnosis_id']);echo$dg[0]['name'].' , ';}?></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-offset-1 col-md-10">
                                                                                <?php $test=$override->get('test_list','id',$medRec['other_test'])?>
                                                                                <textarea name="" class="form-control" placeholder="Test Performed: <?=$test[0]['name']?>" disabled>Test Performed: <?php foreach($override->get('test_performed','checkup_id',$medRec['id']) as $tst){$t=$override->get('test_list','id',$tst['test_id']);echo$t[0]['name'].' , ';}?></textarea>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <div class="col-md-offset-1 col-md-10">
                                                                                <textarea name="other_note" class="form-control" rows="5" placeholder="Other Note: <?=$medRec[0]['other_note']?>" disabled></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal" id="lens<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4 class="modal-title" id="defModalHead<?=$x?>">Lens Prescription Details</h4>
                                                                </div>
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                        <div class="col-md-offset-1">
                                                                            <h4 class="col-md-3"><strong>Auto Ref</strong></h4>
                                                                            <h2>&nbsp;</h2>
                                                                            <div class="form-group">
                                                                                <div class="col-md-10">
                                                                                    <label class="col-md-2 control-label">EYE</label>
                                                                                    <label class="col-md-2 control-label">SPH</label>
                                                                                    <label class="col-md-2 control-label">CYL</label>
                                                                                    <label class="col-md-2 control-label">AXIS</label>
                                                                                    <label class="col-md-2 control-label">VA</label>
                                                                                    <label class="col-md-2 control-label">ADD</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <div class="col-md-10">
                                                                                    <label class="col-md-2 control-label">RIGHT</label>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['ref_OD_sphere']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['ref_cyl']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['ref_axis']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['ref_va']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['ref_add']?>" disabled/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <div class="col-md-10">
                                                                                    <label class="col-md-2 control-label">LEFT</label>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['add_ref_OD_sphere']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['add_ref_cyl']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['add_ref_axis']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['add_ref_va']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['add_ref_add']?>" disabled/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <h1>&nbsp;</h1>
                                                                            <h1>&nbsp;</h1>
                                                                            <h4 class="col-md-3"><strong>RX</strong></h4>
                                                                            <h2>&nbsp;</h2>
                                                                            <div class="form-group">
                                                                                <div class="col-md-10">
                                                                                    <label class="col-md-2 control-label">EYE</label>
                                                                                    <label class="col-md-2 control-label">SPH</label>
                                                                                    <label class="col-md-2 control-label">CYL</label>
                                                                                    <label class="col-md-2 control-label">AXIS</label>
                                                                                    <label class="col-md-2 control-label">VA</label>
                                                                                    <label class="col-md-2 control-label">ADD</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <div class="col-md-10">
                                                                                    <label class="col-md-2 control-label">RIGHT</label>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['rx_OD_sphere']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['rx_cyl']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['rx_axis']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['rx_va']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['rx_add']?>" disabled/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <div class="col-md-10">
                                                                                    <label class="col-md-2 control-label">LEFT</label>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['add_rx_OS_sphere']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['add_rx_cyl']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['add_rx_axis']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['add_rx_va']?>" disabled/>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <input type="text" class="form-control" value="<?=$medRec['add_rx_add']?>" disabled/>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <h1>&nbsp;</h1><h1>&nbsp;</h1>
                                                                        <div class="col-md-offset-2 form-group">
                                                                            <div class="col-md-10">
                                                                                <input type="text" class="form-control" value="Management : <?=$medRec['distance_glasses'].' , '.$medRec['reading_glasses']?>" disabled/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal" id="med<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4 class="modal-title" id="defModalHead<?=$x?>">Medicine Prescription</h4>
                                                                </div>
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <?php $medicines=$override->getNews('prescription','patient_id',$medRec['patient_id'],'given_date',$medRec['checkup_date']);
                                                                            if($medicines){foreach($medicines as $medicine){$med=$override->get('medicine','id',$medicine['medicine_id'])?>
                                                                                <div class="col-md-offset-1 col-md-10">
                                                                                    <input type="text" name="" class="form-control" placeholder="" value="<?=$med[0]['name']?>" disabled>
                                                                                </div>
                                                                                <div class="col-md-1">
                                                                                    <input type="text" name="" class="form-control" placeholder="" value="<?=$medicine['quantity']?>" disabled>
                                                                                </div>
                                                                            <?php }}else{?>
                                                                                <h2> No Medicine Prescribed </h2>
                                                                            <?php }?>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php if ($user->data()->access_level == 3){?>

                                                        <?php }?>
                                                <?php $x++;}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }elseif ($_GET['id'] == 25){ ?>

                    <?php }
                    elseif($_GET['id'] == 4){$heading ='SEARCH FOR INFORMATION'?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3>information</h3>
                                    <span></span>
                                </div>
                                <ul class="panel-controls" style="margin-top: 2px;">
                                    <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                    <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="panel-body padding-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"></h3>
                                        <div class="btn-group pull-right">
                                            <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="customers2" class="table datatable table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Patient Name</th>
                                                    <th>Receipt No.</th>
                                                    <th>Checkup Date</th>
                                                    <th>Payment Date</th>
                                                    <th>Total Cost</th>
                                                    <th>Paid Amount</th>
                                                    <th>Status</th>
                                                    <th>More Info&nbsp;&nbsp;</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $x=0;foreach($override->getSort('payment','branch_id',$user->data()->branch_id,'pay_date') as $paymentId){?>
                                                    <tr><?php $patient=$override->get('patient','id',$paymentId['patient_id'])?>
                                                        <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></td>
                                                        <td><?=$paymentId['id']?></td>
                                                        <td><?=$paymentId['checkup_date']?></td>
                                                        <td><?=$paymentId['pay_date']?></td>
                                                        <td><?=$paymentId['cost']?></td>
                                                        <td><?=$paymentId['payment']?></td>
                                                        <?php if($paymentId['status'] == 0){?>
                                                        <td><span class="label label-danger">Unpaid</span></td>
                                                        <?php }elseif($paymentId['status'] == 1){?>
                                                            <td><span class="label label-success">Paid</span></td>
                                                        <?php }elseif($paymentId['status'] == 2){?>
                                                            <td><span class="label label-warning">Pending</span></td>
                                                        <?php }?>
                                                        <td>
                                                            <form method="post">
                                                                <a href="invoice.php?id=<?=$paymentId['patient_id']?>&c=1&date=<?=$paymentId['pay_date']?>" target="_blank" class="btn btn-default btn-rounded btn-condensed btn-sm"><span class="fa fa-info"></span></a>
                                                                <a href="invoice.php?id=<?=$paymentId['patient_id']?>&c=2&date=<?=$paymentId['pay_date']?>" target="_blank" class="btn btn-default btn-rounded btn-condensed btn-sm"><span class="fa fa-medkit"></span></a>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                     <?php $x++;}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 5 && $user->data()->access_level == 4){$heading ='LENS IN STOCK'?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3>List of Lens Available in Stock</h3>
                                    <span></span>
                                </div>
                                <ul class="panel-controls" style="margin-top: 2px;">
                                    <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                    <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="panel-body padding-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"></h3>
                                        <div class="btn-group pull-right">
                                            <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="customers2" class="table datatable table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Lens Group</th>
                                                    <th>Lens Type &nbsp;&nbsp;&nbsp;</th>
                                                    <th>Lens Category</th>
                                                    <th>Lens Power</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                    <th>Branch</th>
                                                    <th>Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $x=0;foreach($override->getData('lens_power') as $lens){
                                                    $group = $override->get('lens','id',$lens['lens_id']);
                                                    $type = $override->get('lens_type','id',$lens['type_id']);
                                                    $category = $override->get('lens_category','id',$lens['cat_id']);?>
                                                    <tr><?php $branchName=$override->get('clinic_branch','id',$lens['branch_id'])?>
                                                        <td><?=$group[0]['name']?></td>
                                                        <td><?=$type[0]['name']?></td>
                                                        <td><?=$category[0]['name']?></td>
                                                        <td><?=$lens['lens_power']?></td>
                                                        <td><?=$lens['quantity']?></td>
                                                        <td><?=$lens['price']?></td>
                                                        <td><?=$branchName[0]['name']?></td>
                                                        <?php if($lens['quantity'] < ($lens['re_order'] + 10) && $lens['quantity'] > 10){?>
                                                            <td><span class="label label-info">Low</span></td>
                                                        <?php }elseif($lens['quantity'] < 10 && $lens['quantity'] > 0){?>
                                                            <td><span class="label label-warning">Critical Low</span></td>
                                                        <?php }elseif($lens['quantity'] <= 0){?>
                                                            <td><span class="label label-danger">Finished</span></td>
                                                        <?php }else{?>
                                                            <td><span class="label label-success">Enough</span></td>
                                                        <?php }?>
                                                    </tr>
                                                    <?php $x++;}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 6 && $user->data()->access_level == 4){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3>List of Frames Available in Stock</h3>
                                    <span></span>
                                </div>
                                <ul class="panel-controls" style="margin-top: 2px;">
                                    <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                    <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="panel-body padding-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"></h3>
                                        <div class="btn-group pull-right">
                                            <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="customers2" class="table datatable table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Frame Brand</th>
                                                    <th>Frame Model</th>
                                                    <th>Frame Size</th>
                                                    <th>Frame Category</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $x=0;foreach($override->getData('frames','branch_id',$user->data()->branch_id) as $frames){
                                                    $model = $override->get('frame_model','id',$frames['model']);
                                                    $brand = $override->get('frame_brand','id',$frames['brand_id']);
                                                    $branchName=$override->get('clinic_branch','id',$frames['branch_id'])?>
                                                    <tr>
                                                        <td><?=$brand[0]['name']?></td>
                                                        <td><?=$model[0]['model']?></td>
                                                        <td><?=$frames['frame_size']?></td>
                                                        <td><?=$frames['category']?></td>
                                                        <td><?=$frames['quantity']?></td>
                                                        <td><?=$frames['price']?></td>
                                                    </tr>
                                                    <div class="modal" id="dlt<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE FRAME</h4>
                                                                </div>
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                <span style="color: #ff0000">
                                                                    <strong>ARE YOU SURE YOU WANT TO DELETE THIS FRAME ?</strong>
                                                                </span>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="frameId" value="<?=$frames['id']?>">
                                                                        <input type="hidden" name="frameM" value="<?=$frames['model']?>">
                                                                        <input type="submit" name="deleteFrame" value="DELETE FRAME" class="btn btn-danger" >
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php $x++;}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 7 && $user->data()->access_level == 4){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3>List of Medicine Available in Stock</h3>
                                    <span></span>
                                </div>
                                <ul class="panel-controls" style="margin-top: 2px;">
                                    <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                    <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="panel-body padding-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"></h3>
                                        <div class="btn-group pull-right">
                                            <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="customers2" class="table datatable table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Manufacture</th>
                                                    <th>Clinic Branch</th>
                                                    <th>Quantity</th>
                                                    <th>Manufactured Date</th>
                                                    <th>Expiring Date</th>
                                                    <th>Re-order</th>
                                                    <th>Price</th>
                                                    <th>Status</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $x=0;foreach($override->get('medicine','branch_id',$user->data()->branch_id) as $info){?>
                                                    <tr><?php $branchName=$override->get('clinic_branch','id',$info['branch_id'])?>
                                                        <td><?=$info['name']?></td>
                                                        <td><?=$info['manufacture']?></td>
                                                        <td><?=$branchName[0]['name']?></td>
                                                        <td><?=$info['quantity']?></td>
                                                        <td><?=$info['man_date']?></td>
                                                        <td><?=$info['ex_date']?></td>
                                                        <td><?=$info['re_order']?></td>
                                                        <td><?=$info['price']?></td>
                                                        <?php if($info['quantity'] <= $info['re_order'] && !$info['quantity'] == 0){?>
                                                            <td><span class="label label-warning">Re-Order</span></td>
                                                        <?php }elseif($info['quantity'] == 0){?>
                                                            <td><span class="label label-danger">Finished</span></td>
                                                        <?php }else{?>
                                                            <td><span class="label label-success">Good</span></td>
                                                        <?php }?>
                                                    </tr>
                                                    <?php $x++;}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 8){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3>List of Lens Available in Stock</h3>
                                    <span></span>
                                </div>
                                <ul class="panel-controls" style="margin-top: 2px;">
                                    <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                    <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="panel-body padding-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"></h3>
                                        <div class="btn-group pull-right">
                                            <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="customers2" class="table datatable table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Lens Group</th>
                                                    <th>Lens Type &nbsp;&nbsp;&nbsp;</th>
                                                    <th>Lens Category</th>
                                                    <th>Lens Power</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php if($user->data()->branch_id == 9){$branch_id=8;}else{$branch_id = $user->data()->branch_id;}$x=0;
                                                foreach($override->get('lens_power','branch_id',$branch_id) as $lens){
                                                    $group = $override->get('lens','id',$lens['lens_id']);
                                                    $type = $override->get('lens_type','id',$lens['type_id']);
                                                    $category = $override->get('lens_category','id',$lens['cat_id']);?>
                                                    <tr><?php $branchName=$override->get('clinic_branch','id',$lens['branch_id'])?>
                                                        <td><?=$group[0]['name']?></td>
                                                        <td><?=$type[0]['name']?></td>
                                                        <td><?=$category[0]['name']?></td>
                                                        <td><?=$lens['lens_power']?></td>
                                                        <td><?=$lens['quantity']?></td>
                                                        <td><?=$lens['price']?></td>
                                                    </tr>
                                                    <?php $x++;}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 9){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3>List of Medicine Available in Stock</h3>
                                    <span></span>
                                </div>
                                <ul class="panel-controls" style="margin-top: 2px;">
                                    <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                    <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="panel-body padding-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"></h3>
                                        <div class="btn-group pull-right">
                                            <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="customers2" class="table datatable table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Manufacture</th>
                                                    <th>Clinic Branch</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $x=0;foreach($override->get('medicine','branch_id',$user->data()->branch_id) as $info){?>
                                                    <tr><?php $branchName=$override->get('clinic_branch','id',$info['branch_id'])?>
                                                        <td><?=$info['name']?></td>
                                                        <td><?=$info['manufacture']?></td>
                                                        <td><?=$branchName[0]['name']?></td>
                                                        <td><?=$info['quantity']?></td>
                                                        <td><?=$info['price']?></td>
                                                    </tr>
                                                    <?php $x++;}?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 10){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title-box">
                                    <h3>List of Orders Notification</h3>
                                    <span></span>
                                </div>
                                <ul class="panel-controls" style="margin-top: 2px;">
                                    <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                    <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                            <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="panel-body padding-0">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title"></h3>
                                        <div class="btn-group pull-right">
                                            <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                            <ul class="dropdown-menu">
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                                <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table id="customers2" class="table datatable table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Lens</th>
                                                    <th>Lens Power</th>
                                                    <th>Quantity</th>
                                                    <th>Patient Name</th>
                                                    <th>Order Placed At</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php if($user->data()->branch_id == 8){$lens_orders=$override->selectOr('lens_prescription','branch_id',$user->data()->branch_id,'status',1);
                                                    $lens_orders1=$override->selectOr('lens_prescription','branch_id',9,'status',1);
                                                }else{$lens_orders=$override->selectOr('lens_prescription','branch_id',$user->data()->branch_id,'status',1);}
                                                $x=0;foreach($lens_orders as $order){
                                                    $patient=$override->get('patient','id',$order['patient_id']);
                                                    $lens_id=$override->get('lens_power','id',$order['lens']);
                                                    //$group = $override->get('lens','id',$order['lens_group']);
                                                    $type = $override->get('lens_type','id',$lens_id[0]['type_id']);
                                                    $category = $override->get('lens_category','id',$order['lens_id']);?>
                                                    <tr>
                                                        <td><?=$category[0]['name'].' '.$type[0]['name']?></td>
                                                        <td><?=$order['lens_power']?></td>
                                                        <td><?php if($order['eye'] == 'BE'){echo 2;}else{echo 1;}?></td>
                                                        <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname'].'  '.$patient[0]['phone_number']?></td>
                                                        <td><?=$order['checkup_date']?></td>
                                                        <?php if($order['status'] == 0){?>
                                                            <td><span class="label label-danger">Pending</span></td>
                                                        <?php }elseif($order['status'] == 1){?>
                                                            <td><span class="label label-success">Order Complete</span></td>
                                                        <?php }elseif($order['status'] == 2){?>
                                                            <td><span class="label label-warning">Order Placed</span></td>
                                                        <?php }elseif($order['status'] == 3){?>
                                                            <td><span class="label label-info">Wait to be Assign</span></td>
                                                        <?php }?>
                                                        <td>
                                                            <form method="post">
                                                                <a href="#assign<?=$x?>" class="btn btn-info btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-user"></span></a>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <div class="modal" id="assign<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4 class="modal-title" id="defModalHead<?=$x?>">Assign Lens to Patient</h4>
                                                                </div>
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                        <label></label>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2">Patient Name </label>
                                                                            <div class="col-md-10">
                                                                                <input type="text" name="" class="form-control" value="<?=$patient[0]['firstname'].' '.$patient[0]['lastname']?>" disabled>
                                                                            </div>
                                                                        </div>
                                                                        <label></label>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2">Phone Number </label>
                                                                            <div class="col-md-10">
                                                                                <input type="text" name="" class="form-control" value="<?=$patient[0]['phone_number']?>" disabled>
                                                                            </div>
                                                                        </div>
                                                                        <label></label>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">Assign Lens To Patient</label>
                                                                            <div class="col-md-10">
                                                                                <select name="assignment" class="form-control select">
                                                                                    <option value="">Select Status</option>
                                                                                    <option value="1">Lens Available and Sent Workshop</option>
                                                                                    <option value="2">Ordered</option>
                                                                                    <option value="">Not Yet Received</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <label></label>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="lens_id" value="<?=$order['lens']?>">
                                                                        <input type="hidden" name="id" value="<?=$order['id']?>">
                                                                        <input type="hidden" name="eye" value="<?=$order['eye']?>">
                                                                        <input type="submit" name="assignOrder" value="Assign Order" class="btn btn-success" >
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php $x++;}
                                                if($lens_orders1){
                                                    $x=0;foreach($lens_orders1 as $order){
                                                        $patient=$override->get('patient','id',$order['patient_id']);
                                                        $lens_id=$override->get('lens_power','id',$order['lens']);
                                                        //$group = $override->get('lens','id',$order['lens_group']);
                                                        $type = $override->get('lens_type','id',$lens_id[0]['type_id']);
                                                        $category = $override->get('lens_category','id',$order['lens_id']);?>
                                                        <tr>
                                                            <td><?=$category[0]['name'].' '.$type[0]['name']?></td>
                                                            <td><?=$order['lens_power']?></td>
                                                            <td><?php if($order['eye'] == 'BE'){echo 2;}else{echo 1;}?></td>
                                                            <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname'].'  '.$patient[0]['phone_number']?></td>
                                                            <td><?=$order['checkup_date']?></td>
                                                            <?php if($order['status'] == 0){?>
                                                                <td><span class="label label-danger">Pending</span></td>
                                                            <?php }elseif($order['status'] == 1){?>
                                                                <td><span class="label label-success">Order Complete</span></td>
                                                            <?php }elseif($order['status'] == 2){?>
                                                                <td><span class="label label-warning">Order Placed</span></td>
                                                            <?php }elseif($order['status'] == 3){?>
                                                                <td><span class="label label-info">Wait to be Assign</span></td>
                                                            <?php }?>
                                                            <td>
                                                                <form method="post">
                                                                    <a href="#assign<?=$x?>" class="btn btn-info btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-user"></span></a>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                        <div class="modal" id="assign<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                        <h4 class="modal-title" id="defModalHead<?=$x?>">Assign Lens to Patient</h4>
                                                                    </div>
                                                                    <form method="post">
                                                                        <div class="modal-body">
                                                                            <label></label>
                                                                            <div class="form-group">
                                                                                <label class="col-md-2">Patient Name </label>
                                                                                <div class="col-md-10">
                                                                                    <input type="text" name="" class="form-control" value="<?=$patient[0]['firstname'].' '.$patient[0]['lastname']?>" disabled>
                                                                                </div>
                                                                            </div>
                                                                            <label></label>
                                                                            <div class="form-group">
                                                                                <label class="col-md-2">Phone Number </label>
                                                                                <div class="col-md-10">
                                                                                    <input type="text" name="" class="form-control" value="<?=$patient[0]['phone_number']?>" disabled>
                                                                                </div>
                                                                            </div>
                                                                            <label></label>
                                                                            <div class="form-group">
                                                                                <label class="col-md-2 control-label">Assign Lens To Patient</label>
                                                                                <div class="col-md-10">
                                                                                    <select name="assignment" class="form-control select">
                                                                                        <option value="">Select Status</option>
                                                                                        <option value="1">Lens Available and Sent Workshop</option>
                                                                                        <option value="2">Ordered</option>
                                                                                        <option value="">Not Yet Received</option>
                                                                                    </select>
                                                                                </div>
                                                                            </div>
                                                                            <label></label>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <input type="hidden" name="lens_id" value="<?=$order['lens']?>">
                                                                            <input type="hidden" name="id" value="<?=$order['id']?>">
                                                                            <input type="hidden" name="eye" value="<?=$order['eye']?>">
                                                                            <input type="submit" name="assignOrder" value="Assign Order" class="btn btn-success" >
                                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php $x++;}
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 11){?>
                        <div class="content-frame">
                        <!-- START CONTENT FRAME TOP -->
                        <div class="content-frame-top">
                            <div class="page-title">
                                <h2><span class="fa fa-inbox"></span> SMS Notifications <small></small></h2>
                            </div>

                            <div class="pull-right">
                                <button class="btn btn-default"><span class="fa fa-cogs"></span> Settings</button>
                                <button class="btn btn-default content-frame-left-toggle"><span class="fa fa-bars"></span></button>
                            </div>
                        </div>
                        <!-- END CONTENT FRAME TOP -->

                        <!-- START CONTENT FRAME LEFT -->
                        <div class="content-frame-left">
                            <div class="page-title">
                                <h4><span class="fa fa-inbox"></span> SMS <small></small></h4>
                            </div>
                            <div class="block">
                                <div class="list-group border-bottom">
                                    <a href="info.php?id=11" class="list-group-item"><span class="fa fa-inbox"></span> Inbox &nbsp;(<?=$override->getCount('sms','receiver_id',$user->data()->id)?>)<span class="badge badge-success"><?=$override->rowCounted('sms','receiver_id',$user->data()->id,'status',0)?> Unread</span></a>
                                    <a href="#" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
                                    <a href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted <span class="badge badge-default"></span></a>
                                </div>
                            </div>
                            <div class="page-title">
                                <h4><span class="fa fa-inbox"></span> Email <small></small></h4>
                            </div>
                            <div class="block">
                                <div class="list-group border-bottom">
                                    <a href="info.php?id=13" class="list-group-item"><span class="fa fa-inbox"></span> Inbox &nbsp;(<?=$override->getCount('emails','receiver_id',$user->data()->id)?>)<span class="badge badge-success"><?=$override->rowCounted('emails','receiver_id',$user->data()->id,'status',0)?> Unread</span></a>
                                    <a href="#" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
                                    <a href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted <span class="badge badge-default"></span></a>
                                </div>
                            </div>
                        </div>
                        <!-- END CONTENT FRAME LEFT -->

                        <!-- START CONTENT FRAME BODY -->
                        <div class="content-frame-body">

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <label class="check mail-checkall">
                                        <input type="checkbox" class="icheckbox"/>
                                    </label>
                                    <div class="btn-group">
                                        <button class="btn btn-default"><span class="fa fa-mail-reply"></span></button>
                                        <button class="btn btn-default"><span class="fa fa-mail-reply-all"></span></button>
                                        <button class="btn btn-default"><span class="fa fa-mail-forward"></span></button>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-default"><span class="fa fa-star"></span></button>
                                        <button class="btn btn-default"><span class="fa fa-flag"></span></button>
                                    </div>
                                    <button class="btn btn-default"><span class="fa fa-warning"></span></button>
                                    <button class="btn btn-default"><span class="fa fa-trash-o"></span></button>
                                    <div class="pull-right" style="width: 150px;">
                                        <div class="input-group">
                                            <div class="input-group-addon"><span class="fa fa-calendar"></span></div>
                                            <input class="form-control datepicker" type="text" data-orientation="left"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body mail">
                                    <?php if($override->getNews('sms','category','staff','receiver_id',$user->data()->id)){
                                        foreach($override->getNewsSort('sms','category','staff','receiver_id',$user->data()->id,'id')  as $mySms){?>
                                            <div class="mail-item <?php if($mySms['status'] == 0){ echo 'mail-unread';}else{echo 'mail-success';}?> mail-info">
                                                <div class="mail-checkbox">
                                                    <input type="checkbox" class="icheckbox"/>
                                                </div>
                                                <div class="mail-star">
                                                    <span class="fa fa-star-o"></span>
                                                </div>
                                                <?php $sender=$override->get('staff','id',$mySms['staff_id'])?>
                                                <div class="mail-user"><?=$sender[0]['position']?></div>
                                                <a href="info.php?id=12&msg=<?=$mySms['id']?>&cat=sms" class="mail-text"><?=$mySms['subject']?></a>
                                                <div class="mail-date"><?php if(date('Y-m-d') == $mySms['sms_date']){echo 'Today';}else{echo $mySms['sms_date'];}?></div>
                                            </div>
                                        <?php }}else{?>
                                        <div class="alert alert-info" role="alert">
                                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <strong>Oops!&nbsp;</strong> Your Inbox is Empty
                                        </div>
                                    <?php }?>
                                </div>
                                <div class="panel-footer">
                                    <div class="btn-group">
                                        <button class="btn btn-default"><span class="fa fa-mail-reply"></span></button>
                                        <button class="btn btn-default"><span class="fa fa-mail-reply-all"></span></button>
                                        <button class="btn btn-default"><span class="fa fa-mail-forward"></span></button>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-default"><span class="fa fa-star"></span></button>
                                        <button class="btn btn-default"><span class="fa fa-flag"></span></button>
                                    </div>

                                    <button class="btn btn-default"><span class="fa fa-warning"></span></button>
                                    <button class="btn btn-default"><span class="fa fa-trash-o"></span></button>

                                    <ul class="pagination pagination-sm pull-right">
                                        <li class="disabled"><a href="#">«</a></li>
                                        <li class="active"><a href="#">1</a></li>
                                        <li><a href="#">2</a></li>
                                        <li><a href="#">3</a></li>
                                        <li><a href="#">4</a></li>
                                        <li><a href="#">»</a></li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <!-- END CONTENT FRAME BODY -->
                        </div>
                    <?php }
                    elseif($_GET['id'] == 12){
                      if($_GET['cat'] == 'sms'){
                            $messageN=$override->get('sms','id',$_GET['msg']);if($messageN[0]['status']==0){$user->updateRecord('sms', array('status' => 1),$_GET['msg']);}
                            if($messageN){$message=$messageN[0]['message'];}
                        }elseif($_GET['cat'] == 'email'){
                             $messageN=$override->get('emails','id',$_GET['msg']);if($messageN[0]['status']==0){$user->updateRecord('emails', array('status' => 1),$_GET['msg']);}
                             if($messageN){$message=$messageN[0]['message'];}
                         }else{Redirect::to('info.php?id=11');}?>
                        <div class="content-frame">
                            <!-- START CONTENT FRAME TOP -->
                            <div class="content-frame-top">
                                <div class="page-title">
                                    <h2><span class="fa fa-file-text"></span> Message</h2>
                                </div>

                                <div class="pull-right">
                                    <button class="btn btn-default" onclick="window.print()"><span class="fa fa-print"></span> Print</button>
                                    <button class="btn btn-default content-frame-left-toggle"><span class="fa fa-bars"></span></button>
                                </div>
                            </div>
                            <!-- END CONTENT FRAME TOP -->

                            <!-- START CONTENT FRAME LEFT -->
                            <div class="content-frame-left">
                                <div class="block">
                                    <a href="form.php?id=11" class="btn btn-danger btn-block btn-lg"><span class="fa fa-edit"></span> COMPOSE</a>
                                </div>
                                <div class="page-title">
                                    <h4><span class="fa fa-inbox"></span> SMS <small></small></h4>
                                </div>
                                <div class="block">
                                    <div class="list-group border-bottom">
                                        <a href="info.php?id=11" class="list-group-item"><span class="fa fa-inbox"></span> Inbox &nbsp;(<?=$override->getCount('sms','receiver_id',$user->data()->id)?>)<span class="badge badge-success"><?=$override->rowCounted('sms','receiver_id',$user->data()->id,'status',0)?> Unread</span></a>
                                        <a href="#" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
                                        <a href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted </a>
                                    </div>
                                </div>
                                <div class="page-title">
                                    <h4><span class="fa fa-inbox"></span> Email <small></small></h4>
                                </div>
                                <div class="block">
                                    <div class="list-group border-bottom">
                                        <a href="info.php?id=13" class="list-group-item"><span class="fa fa-inbox"></span> Inbox &nbsp;(<?=$override->getCount('emails','receiver_id',$user->data()->id)?>)<span class="badge badge-success"><?=$override->rowCounted('emails','receiver_id',$user->data()->id,'status',0)?> Unread</span></a>
                                        <a href="#" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
                                        <a href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted </a>
                                    </div>
                                </div>

                            </div>
                            <!-- END CONTENT FRAME LEFT -->

                            <!-- START CONTENT FRAME BODY -->
                            <div class="content-frame-body">

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <div class="pull-left">
                                            <?php $sender=$override->get('staff','id',$messageN[0]['staff_id'])?>
                                            <img src="<?php if($sender[0]['picture']){echo$sender[0]['picture'];}else{echo 'assets/images/users/no-image.jpg';}?>" class="panel-title-image" alt="<?=$sender[0]['lastname']?>"/>
                                            <h3 class="panel-title"><?=$sender[0]['position']?> <small> &nbsp; <?=$sender[0]['lastname']?></small></h3>
                                        </div>
                                        <div class="pull-right">
                                            <button class="btn btn-default"><span class="fa fa-mail-reply"></span></button>
                                            <button class="btn btn-default"><span class="fa fa-warning"></span></button>
                                            <button class="btn btn-default"><span class="fa fa-trash-o"></span></button>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <?=$message?>
                                    </div>

                                </div>
                            </div>
                            <!-- END CONTENT FRAME BODY -->
                        </div>
                    <?php }
                    elseif($_GET['id'] == 13){?>
                        <div class="content-frame">
                            <!-- START CONTENT FRAME TOP -->
                            <div class="content-frame-top">
                                <div class="page-title">
                                    <h2><span class="fa fa-inbox"></span> Email Notifications <small></small></h2>
                                </div>

                                <div class="pull-right">
                                    <button class="btn btn-default"><span class="fa fa-cogs"></span> Settings</button>
                                    <button class="btn btn-default content-frame-left-toggle"><span class="fa fa-bars"></span></button>
                                </div>
                            </div>
                            <!-- END CONTENT FRAME TOP -->

                            <!-- START CONTENT FRAME LEFT -->
                            <div class="content-frame-left">
                                <div class="page-title">
                                    <h4><span class="fa fa-inbox"></span> SMS <small></small></h4>
                                </div>
                                <div class="block">
                                    <div class="list-group border-bottom">
                                        <a href="info.php?id=11" class="list-group-item"><span class="fa fa-inbox"></span> Inbox &nbsp;(<?=$override->getCount('sms','receiver_id',$user->data()->id)?>)<span class="badge badge-success"><?=$override->rowCounted('sms','receiver_id',$user->data()->id,'status',0)?> Unread</span></a>
                                        <a href="#" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
                                        <a href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted <span class="badge badge-default"></span></a>
                                    </div>
                                </div>
                                <div class="page-title">
                                    <h4><span class="fa fa-inbox"></span> Email <small></small></h4>
                                </div>
                                <div class="block">
                                    <div class="list-group border-bottom">
                                        <a href="info.php?id=13" class="list-group-item"><span class="fa fa-inbox"></span> Inbox &nbsp;(<?=$override->getCount('emails','receiver_id',$user->data()->id)?>)<span class="badge badge-success"><?=$override->rowCounted('emails','receiver_id',$user->data()->id,'status',0)?> Unread</span></a>
                                        <a href="#" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
                                        <a href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted <span class="badge badge-default"></span></a>
                                    </div>
                                </div>
                            </div>
                            <!-- END CONTENT FRAME LEFT -->

                            <!-- START CONTENT FRAME BODY -->
                            <div class="content-frame-body">

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <label class="check mail-checkall">
                                            <input type="checkbox" class="icheckbox"/>
                                        </label>
                                        <div class="btn-group">
                                            <button class="btn btn-default"><span class="fa fa-mail-reply"></span></button>
                                            <button class="btn btn-default"><span class="fa fa-mail-reply-all"></span></button>
                                            <button class="btn btn-default"><span class="fa fa-mail-forward"></span></button>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-default"><span class="fa fa-star"></span></button>
                                            <button class="btn btn-default"><span class="fa fa-flag"></span></button>
                                        </div>
                                        <button class="btn btn-default"><span class="fa fa-warning"></span></button>
                                        <button class="btn btn-default"><span class="fa fa-trash-o"></span></button>
                                        <div class="pull-right" style="width: 150px;">
                                            <div class="input-group">
                                                <div class="input-group-addon"><span class="fa fa-calendar"></span></div>
                                                <input class="form-control datepicker" type="text" data-orientation="left"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-body mail">
                                        <?php if($override->getNews('emails','category','staff','receiver_id',$user->data()->id)){
                                            foreach($override->getNewsSort('emails','category','staff','receiver_id',$user->data()->id,'id')  as $mySms){?>
                                                <div class="mail-item <?php if($mySms['status'] == 0){ echo 'mail-unread';}else{echo 'mail-success';}?> mail-info">
                                                    <div class="mail-checkbox">
                                                        <input type="checkbox" class="icheckbox"/>
                                                    </div>
                                                    <div class="mail-star">
                                                        <span class="fa fa-star-o"></span>
                                                    </div>
                                                    <?php $sender=$override->get('staff','id',$mySms['staff_id'])?>
                                                    <div class="mail-user"><?=$sender[0]['position']?></div>
                                                    <a href="info.php?id=12&msg=<?=$mySms['id']?>&cat=email" class="mail-text"><?=$mySms['subject']?></a>
                                                    <div class="mail-date"><?php if(date('Y-m-d') == $mySms['email_date']){echo 'Today';}else{echo $mySms['email_date'];}?></div>
                                                </div>
                                            <?php }}else{?>
                                            <div class="alert alert-info" role="alert">
                                                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                <strong>Oops!&nbsp;</strong> Your mailbox is Empty
                                            </div>
                                        <?php }?>
                                    </div>
                                    <div class="panel-footer">
                                        <div class="btn-group">
                                            <button class="btn btn-default"><span class="fa fa-mail-reply"></span></button>
                                            <button class="btn btn-default"><span class="fa fa-mail-reply-all"></span></button>
                                            <button class="btn btn-default"><span class="fa fa-mail-forward"></span></button>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-default"><span class="fa fa-star"></span></button>
                                            <button class="btn btn-default"><span class="fa fa-flag"></span></button>
                                        </div>

                                        <button class="btn btn-default"><span class="fa fa-warning"></span></button>
                                        <button class="btn btn-default"><span class="fa fa-trash-o"></span></button>

                                        <ul class="pagination pagination-sm pull-right">
                                            <li class="disabled"><a href="#">«</a></li>
                                            <li class="active"><a href="#">1</a></li>
                                            <li><a href="#">2</a></li>
                                            <li><a href="#">3</a></li>
                                            <li><a href="#">4</a></li>
                                            <li><a href="#">»</a></li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                            <!-- END CONTENT FRAME BODY -->
                        </div>
                    <?php }
                    elseif($_GET['id'] == 14 && $user->data()->access_level == 7){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong><?=$head?></strong></h3>
                                <div class="btn-group pull-right">
                                    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                        <tr>
                                            <th>Batch Type</th>
                                            <th>Batch Price</th>
                                            <th>Quantity</th>
                                            <th>Sold Quantity</th>
                                            <th>Remain Quantity</th>
                                            <th>Batch Cost</th>
                                            <th>Sold Amount</th>
                                            <th>Batch Date</th>
                                            <th>Finish on Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($override->getSort('frame_sales','emp_id',$user->data()->id,'id') as $myStock){$product=$override->get('sales_product','id',$myStock['product_id'])?>
                                            <tr>
                                                <td><?=$product[0]['name']?></td>
                                                <td><?=$myStock['price_per']?></td>
                                                <td><?=$myStock['quantity']?></td>
                                                <td><?=$myStock['sold_qty']?></td>
                                                <td><?php if($myStock['quantity']-$myStock['sold_qty'] == 0){?><span class="label label-success">Finished</span><?php }else{echo$myStock['quantity']-$myStock['sold_qty'];}?></td>
                                                <td><?=$myStock['total_cost']?></td>
                                                <td><?=$myStock['amount']?></td>
                                                <td><?=$myStock['batch_date']?></td>
                                                <td><?=$myStock['finish_date']?></td>
                                            </tr>
                                            <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 15 && $user->data()->access_level == 7){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong><?=$head?></strong></h3>
                                <div class="btn-group pull-right">
                                    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                        <tr>
                                            <th>Customer Name</th>
                                            <th>Phone Number</th>
                                            <th>Email Address</th>
                                            <th>Location</th>
                                            <th>Product</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Date&nbsp;&nbsp;</th>
                                            <th>Notes</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($override->getSort('sales_details','staff_id',$user->data()->id,'id') as $myStock){$product=$override->get('sales_product','id',$myStock['product_id'])?>
                                            <tr>
                                                <td><?=$myStock['name']?></td>
                                                <td><?=$myStock['phone_number']?></td>
                                                <td><?=$myStock['email_address']?></td>
                                                <td><?=$myStock['location']?></td>
                                                <td><?=$product[0]['name']?></td>
                                                <td><?=$myStock['quantity']?></td>
                                                <td><?=$myStock['quantity']*$myStock['batch']?></td>
                                                <td><?=$myStock['sales_date']?></td>
                                                <td><?=$myStock['other_note']?></td>
                                            </tr>
                                        <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 16 && $user->data()->access_level == 6){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong><?=$head?></strong></h3>
                                <div class="btn-group pull-right">
                                    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                        <tr>
                                            <th>Staff ID</th>
                                            <th>Data Entered</th>
                                            <th>Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($override->getSort('data_rec','staff_id',$user->data()->id,'id') as $myData){$staff=$override->get('staff','id',$myData['staff_id'])?>
                                            <tr>
                                                <td><?=$staff[0]['employee_ID']?></td>
                                                <td><?=$myData['quantity']?></td>
                                                <td><?=$myData['data_date']?></td>
                                            </tr>
                                        <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 17 && $user->data()->access_level == 6){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong><?=$head?></strong></h3>
                                <div class="btn-group pull-right">
                                    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                        <tr>
                                            <th>Employee Name</th>
                                            <th>Employee_Id</th>
                                            <th>Data Entered</th>
                                            <th>Data Cost</th>
                                            <th>Payed Amount</th>
                                            <th>Quantity payed</th>
                                            <th>Amount Remained</th>
                                            <th>Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $cost=$override->getData('data_entry_price');foreach($override->getSort('data_payment','emp_id',$user->data()->id,'id') as $myData){$staff=$override->get('staff','id',$myData['emp_id']);?>
                                            <tr>
                                                <td><?=$staff[0]['firstname'].' '.$staff[0]['middlename'].' '.$staff[0]['lastname']?></td>
                                                <td><?=$staff[0]['employee_ID']?></td>
                                                <td><?=$myData['quantity']?></td>
                                                <td><?=($myData['quantity']*$cost[0]['price'])?></td>
                                                <td><?=$myData['price']?></td>
                                                <td><?=ceil($myData['price']/$cost[0]['price'])?></td>
                                                <td><?=($myData['quantity']*$cost[0]['price'])-$myData['price']?></td>
                                                <td><?=$myData['pay_date']?></td>
                                            </tr>
                                        <?php }?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 18){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong><?=$head?></strong></h3>
                                <div class="btn-group pull-right">
                                    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="customers2" class="table datatable">
                                        <?php if($user->data()->access_level == 2){$appointments=$override->getSort3('appointment','branch_id',$user->data()->branch_id,'status',0,'doctor_id',$user->data()->id,'id');}else{$appointments=$override->getSort2('appointment','branch_id',$user->data()->branch_id,'status',0,'id');}?>
                                        <thead>
                                        <tr>
                                            <th>Patient Name</th>
                                            <th>Phone Number</th>
                                            <th>Appointment</th>
                                            <th>Doctor</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $x=0;foreach($appointments as $appointment){if($appointment['status'] == 0 && date('Y-m-d') <= $appointment['appnt_date']){
                                            $patient=$override->get('patient','id',$appointment['patient_id']);$doctor=$override->get('staff','id',$appointment['doctor_id'])?>
                                            <tr>
                                                <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></td>
                                                <td><?=$patient[0]['phone_number']?></td>
                                                <td><?=$appointment['appnt_date'].' , '.$appointment['appnt_time']?></td>
                                                <td><?=$doctor[0]['firstname'].' '.$doctor[0]['lastname']?></td>
                                                <td><?php if($appointment['status'] == 0 && date('Y-m-d') <= $appointment['appnt_date']){?><span class="label label-info">Pending</span><?php }elseif($appointment['status'] == 0 && date('Y-m-d') > $appointment['appnt_date']){?><span class="label label-danger">Unattended</span><?php }else{?><span class="label label-success">Done</span><?php }?></td>
                                                <td><?php if($user->data()->access_level == 3){?>
                                                        <form method="post">
                                                            <a href="#modal<?=$x?>" class="btn btn-info btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info-circle"></span></a>
                                                            <a href="#dlt<?=$x?>" class="btn btn-danger btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-remove"></span></a>
                                                        </form>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                            <div class="modal" id="modal<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title" id="defModalHead<?=$x?>">Information About Appointment</h4>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label class="col-md-1 control-label">Doctor:&nbsp;&nbsp;</label>
                                                                    <div class="col-md-11">
                                                                        <select name="doctor" class="form-control">
                                                                            <option value="<?=$appointment['doctor_id']?>">DR. <?=$doctor[0]['firstname'].' '.$doctor[0]['lastname']?></option>
                                                                            <?php foreach($override->getNews('staff','branch_id',$user->data()->branch_id,'access_level',2) as $doctor){?>
                                                                                <option value="<?=$doctor['id']?>">DR. <?=$doctor['firstname'].' '.$doctor['lastname']?></option>
                                                                            <?php }?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <label></label>
                                                                <div class="form-group">
                                                                    <label class="col-md-1 control-label">Date : &nbsp;</label>
                                                                    <div class="col-md-5">
                                                                        <input type="text" name="date" class="form-control datepicker" value="<?=$appointment['appnt_date']?>">
                                                                    </div>
                                                                    <label class="col-md-1 control-label">Time : &nbsp;</label>
                                                                    <div class="col-md-5">
                                                                        <div class="input-group bootstrap-timepicker">
                                                                            <input type="text" name="time" class="form-control timepicker24" value="<?=$appointment['appnt_time']?>"/>
                                                                            <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?=$appointment['id']?>">
                                                                <input type="submit" name="updateAppointment" value="Update Appointment" class="btn btn-success" >
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal" id="dlt<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE APPOINTMENT</h4>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <span style="color: #ff0000">
                                                                    <strong>ARE YOU SURE YOU WANT TO DELETE THIS APPOINTMENT ?</strong>
                                                                </span>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?=$appointment['id']?>">
                                                                <input type="submit" name="deleteAppointment" value="DELETE" class="btn btn-danger" >
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }$x++;}?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 19){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong><?=$head?></strong></h3>
                                <div class="btn-group pull-right">
                                    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="customers2" class="table datatable">
                                       <thead>
                                        <tr>
                                            <th>Patient Name</th>
                                            <th>Arrival Time</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $x=0;foreach($override->get('wait_list','branch_id',$user->data()->branch_id) as $wait){$patient=$override->get('patient','id',$wait['patient_id'])?>
                                            <tr>
                                                <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></td>
                                                <td><?=$wait['arrive_on']?></td>
                                                <td>
                                                    <form method="post">
                                                        <a href="#dlt<?=$x?>" class="btn btn-danger btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-remove"></span></a>
                                                    </form>
                                                </td>
                                            </tr>
                                            <div class="modal" id="dlt<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE APPOINTMENT</h4>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <span style="color: #ff0000">
                                                                    <strong>ARE YOU SURE YOU WANT TO REMOVE THIS PATIENT FROM WAITING LIST ?</strong>
                                                                </span>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?=$wait['id']?>">
                                                                <input type="submit" name="deleteWait" value="DELETE" class="btn btn-danger" >
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $x++;}?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 21 && $user->data()->access_level == 2){?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title"><strong><?=$head?></strong></h3>
                                <div class="btn-group pull-right">
                                    <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                        <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                    </ul>
                                </div>

                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="customers2" class="table datatable">
                                        <thead>
                                        <tr>
                                            <th>Diagnosis Name</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $x=0;foreach($override->getData('diagnosis') as $diagnosis){?>
                                            <tr>
                                                <td><?=$diagnosis['name']?></td>
                                                <td>
                                                    <form method="post">
                                                        <a href="#modal<?=$x?>" class="btn btn-info btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info-circle"></span></a>
                                                        <a href="#dlt<?=$x?>" class="btn btn-danger btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-remove"></span></a>
                                                    </form>
                                                </td>
                                            </tr>
                                            <div class="modal" id="modal<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title" id="defModalHead<?=$x?>">Information About Diagnosis</h4>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label class="col-md-1 control-label">Diagnosis:&nbsp;&nbsp;</label>
                                                                    <div class="col-md-11">
                                                                       <input name="name" class="form-control" value="<?=$diagnosis['name']?>">
                                                                    </div>
                                                                </div>
                                                                <label></label>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?=$diagnosis['id']?>">
                                                                <input type="submit" name="updateDiagnosis" value="Update Diagnosis" class="btn btn-success" >
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal" id="dlt<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                            <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE DIAGNOSIS</h4>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <span style="color: #ff0000">
                                                                    <strong>ARE YOU SURE YOU WANT TO DELETE THIS DIAGNOSIS ?</strong>
                                                                </span>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?=$diagnosis['id']?>">
                                                                <input type="submit" name="deleteDiagnosis" value="DELETE" class="btn btn-danger" >
                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $x++;}?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 22 && $user->data()->access_level == 2){?>
                        <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3></h3>
                                <span></span>
                            </div>
                            <ul class="panel-controls" style="margin-top: 2px;">
                                <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                                <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                        <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div class="panel-body padding-0">
                        <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"></h3>
                            <div class="btn-group pull-right">
                                <button class="btn btn-danger dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i> Export Data</button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false'});"><img src='img/icons/json.png' width="24"/> JSON</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'false',ignoreColumn:'[2,3]'});"><img src='img/icons/json.png' width="24"/> JSON (ignoreColumn)</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'json',escape:'true'});"><img src='img/icons/json.png' width="24"/> JSON (with Escape)</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'xml',escape:'false'});"><img src='img/icons/xml.png' width="24"/> XML</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'sql'});"><img src='img/icons/sql.png' width="24"/> SQL</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'csv',escape:'false'});"><img src='img/icons/csv.png' width="24"/> CSV</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'txt',escape:'false'});"><img src='img/icons/txt.png' width="24"/> TXT</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'excel',escape:'false'});"><img src='img/icons/xls.png' width="24"/> XLS</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'doc',escape:'false'});"><img src='img/icons/word.png' width="24"/> Word</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'powerpoint',escape:'false'});"><img src='img/icons/ppt.png' width="24"/> PowerPoint</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'png',escape:'false'});"><img src='img/icons/png.png' width="24"/> PNG</a></li>
                                    <li><a href="#" onClick ="$('#customers2').tableExport({type:'pdf',escape:'false'});"><img src='img/icons/pdf.png' width="24"/> PDF</a></li>
                                </ul>
                            </div>

                        </div>
                        <div class="panel-body">
                        <div class="table-responsive">
                        <table id="customers2" class="table datatable table-bordered">
                        <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Sex</th>
                            <th>Age</th>
                            <th>Occupation</th>
                            <th>Checkup Date</th>
                            <th>Action Performed</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $x=0;foreach($override->getSort('checkup_record','patient_id',$_GET['p'],'id') as $medRec){?>
                            <tr><?php $patient=$override->get('patient','id',$medRec['patient_id'])?>
                                <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></td>
                                <td><?=$patient[0]['sex']?></td>
                                <td><?=$patient[0]['age']?></td>
                                <td><?=$patient[0]['occupation']?></td>
                                <td><?=$medRec['checkup_date']?></td>
                                <td>
                                    <form method="post">
                                        <a href="#modal<?=$x?>" class="btn btn-default btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info"></span></a>
                                        <a href="#lens<?=$x?>" class="btn btn-default btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-eye"></span></a>
                                        <a href="#med<?=$x?>" class="btn btn-default btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-medkit"></span></a>
                                        <a href="editInfo.php?id=0&p=<?=$medRec['id']?>&n=" class="btn btn-default btn-rounded btn-condensed btn-sm" ><span class="fa fa-pencil"></span></a>
                                    </form>
                                </td>
                            </tr>
                            <div class="modal" id="modal<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title" id="defModalHead<?=$x?>">Prescription Details</h4>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label class="col-md-1 control-label"></label>
                                                    <div class="col-md-10">
                                                        <textarea name="cc" class="form-control" rows="1" disabled>CC : <?=$medRec['CC']?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2"></label>
                                                    <div class="col-md-2">
                                                        <input name="oh" type="text" class="form-control" value="OH: <?=$medRec['OH']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="gh" type="text" class="form-control" value="GH: <?=$medRec['GH']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="foh" type="text" class="form-control" value="FOH: <?=$medRec['FOH']?>" disabled>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input name="fgh" type="text" class="form-control" value="FGH: <?=$medRec['FGH']?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2"></label>
                                                    <div class="col-md-2">
                                                        <input name="nfc" type="text" class="form-control" value="NPC: <?=$medRec['NPC']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="eom" type="text" class="form-control" value="EOM: <?=$medRec['EOM']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="pupils" type="text" class="form-control" value="Pupils: <?=$medRec['pupils']?>" disabled>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input name="confrontation" type="text" class="form-control" value="Confrontation: <?=$medRec['confrontation']?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-offset-1 col-md-1"></label>
                                                    <div class="col-md-2">
                                                        <input name="re" type="text" class="form-control" value="V_RE: <?=$medRec['V_RE']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="le" type="text" class="form-control" value="V_LE: <?=$medRec['V_LE']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="ph" type="text" class="form-control" value="PH_RE: <?=$medRec['PH_RE']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="ph" type="text" class="form-control" value="PH_LE: <?=$medRec['PH_LE']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="pd" type="text" class="form-control" value="PD: <?=$medRec['PD']?>" disabled>
                                                    </div>
                                                </div>
                                                <!--Auto Ref and Rx goes Here-->
                                                <div class="form-group">
                                                    <div class="col-md-offset-1 col-md-10">
                                                        <textarea name="ext_oc_exam" class="form-control" rows="1" placeholder="External Ocular Examination: <?=$medRec['external_ocular_exam']?>" disabled></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2"></label>
                                                    <div class="col-md-3">
                                                        <input name="iop" type="text" class="form-control" value="IOP: <?=$medRec['IOP']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="iop_re" type="text" class="form-control" value="RE: <?=$medRec['IOP_RE']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="iop_le" type="text" class="form-control" value="LE: <?=$medRec['IOP_LE']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="iop_time" type="text" class="form-control" value="Time: <?=$medRec['IOP_time']?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2"></label>
                                                    <div class="col-md-3">
                                                        <input name="iop_post_dilation" type="text" class="form-control" value="IOP:POST Dilation <?=$medRec['IOP_POST_dilation']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="iop_post_re" type="text" class="form-control" value="RE: <?=$medRec['IOP_POST_RE']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="iop_post_le" type="text" class="form-control" value="LE: <?=$medRec['IOP_POST_LE']?>" disabled>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input name="iop_post_time" type="text" class="form-control" value="Time: <?=$medRec['IOP_POST_time']?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-offset-1 col-md-10">
                                                        <input type="text" name="mydriatic_agent_used" class="form-control" value="Mydriatic Agent used: <?=$medRec['mydriatic_agent_used']?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-offset-1 col-md-10">
                                                        <textarea name="internal_exam" class="form-control" rows="1" placeholder="Internal Examination Dilated/Undilated: <?=$medRec['internal_exam']?>" disabled></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-offset-1 col-md-10">
                                                        <textarea name="diagnosis" class="form-control" rows="1" placeholder="Diagnosis: <?php foreach($override->get('diagnosis_prescription','checkup_id',$medRec['id']) as $diag){$name=$override->get('diagnosis','id',$diag['diagnosis_id']);echo$name[0]['name'].' , ';}?>" disabled></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-offset-1 col-md-10">
                                                        <input type="text" name="" class="form-control" value="Test Performed: <?php foreach($override->get('test_performed','checkup_id',$medRec['id']) as $test){$name=$override->get('test_list','id',$test['test_id']);echo$name[0]['name'].' , ';}?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-offset-1 col-md-10">
                                                        <textarea name="other_note" class="form-control" rows="5" placeholder="Other Note: <?=$medRec['other_note']?>" disabled></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal" id="lens<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title" id="defModalHead<?=$x?>">Lens Prescription Details</h4>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <div class="col-md-offset-1">
                                                    <h4 class="col-md-3"><strong>Auto Ref</strong></h4>
                                                    <h2>&nbsp;</h2>
                                                    <div class="form-group">
                                                        <div class="col-md-10">
                                                            <label class="col-md-2 control-label">EYE</label>
                                                            <label class="col-md-2 control-label">SPH</label>
                                                            <label class="col-md-2 control-label">CYL</label>
                                                            <label class="col-md-2 control-label">AXIS</label>
                                                            <label class="col-md-2 control-label">VA</label>
                                                            <label class="col-md-2 control-label">ADD</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-10">
                                                            <label class="col-md-2 control-label">RIGHT</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['ref_OD_sphere']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['ref_cyl']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['ref_axis']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['ref_va']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['ref_add']?>" disabled/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-10">
                                                            <label class="col-md-2 control-label">LEFT</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['add_ref_OD_sphere']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['add_ref_cyl']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['add_ref_axis']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['add_ref_va']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['add_ref_add']?>" disabled/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h1>&nbsp;</h1>
                                                    <h1>&nbsp;</h1>
                                                    <h4 class="col-md-3"><strong>RX</strong></h4>
                                                    <h2>&nbsp;</h2>
                                                    <div class="form-group">
                                                        <div class="col-md-10">
                                                            <label class="col-md-2 control-label">EYE</label>
                                                            <label class="col-md-2 control-label">SPH</label>
                                                            <label class="col-md-2 control-label">CYL</label>
                                                            <label class="col-md-2 control-label">AXIS</label>
                                                            <label class="col-md-2 control-label">VA</label>
                                                            <label class="col-md-2 control-label">ADD</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-10">
                                                            <label class="col-md-2 control-label">RIGHT</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['rx_OD_sphere']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['rx_cyl']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['rx_axis']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['rx_va']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['rx_add']?>" disabled/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-10">
                                                            <label class="col-md-2 control-label">LEFT</label>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['add_rx_OS_sphere']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['add_rx_cyl']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['add_rx_axis']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['add_rx_va']?>" disabled/>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="text" class="form-control" value="<?=$medRec['add_rx_add']?>" disabled/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <h1>&nbsp;</h1><h1>&nbsp;</h1>
                                                <div class="col-md-offset-2 form-group">
                                                    <div class="col-md-10">
                                                        <input type="text" class="form-control" value="Management : <?=$medRec['distance_glasses'].' , '.$medRec['reading_glasses']?>" disabled/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal" id="med<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title" id="defModalHead<?=$x?>">Medicine Prescription</h4>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <?php $medicines=$override->getNews('prescription','patient_id',$medRec['patient_id'],'given_date',$medRec['checkup_date']);
                                                    if($medicines){foreach($medicines as $medicine){$med=$override->get('medicine','id',$medicine['medicine_id'])?>
                                                        <div class="col-md-offset-1 col-md-10">
                                                            <input type="text" name="" class="form-control" placeholder="" value="<?=$med[0]['name']?>" disabled>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <input type="text" name="" class="form-control" placeholder="" value="<?=$medicine['quantity']?>" disabled>
                                                        </div>
                                                    <?php }}else{?>
                                                        <h2> No Medicine Prescribed </h2>
                                                    <?php }?>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php $x++;}?>
                        </tbody>
                        </table>
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
                    <?php }
                    elseif($_GET['id'] == 23 && $user->data()->access_level == 2){if($_GET['b'] == 'a'){$clinics=$override->getData('clinic_branch');}?>
                        <div class="page-content-wrap">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body">
                                            <h1><?=$head?> REPORT<strong><span class="pull-right"><img src="img/famly%20eye%20care.png"></span></strong></h1>
                                            <!-- INVOICE -->
                                            <div class="invoice">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="invoice-address">
                                                            <!--<h5>CLINIC</h5>-->
                                                            <table class="table table-striped">
                                                                <tr>
                                                                    <th width="200" style="color: #009900"> From : <?=$_GET['from'].' '?> &nbsp;&nbsp;To : <?=$_GET['to']?></th>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="table-invoice">
                                                    <table class="table">
                                                        <tr>
                                                            <th>CLINIC</th>
                                                            <th class="text-center">Patient</th>
                                                            <th class="text-center">Lens</th>
                                                            <th class="text-center">Frames & Sun Glasses</th>
                                                            <th class="text-center">Accessory</th>
                                                        </tr>
                                                        <?php foreach($clinics as $clinic){?>
                                                            <tr>
                                                                <td><strong><?=$clinic['name']?></strong></td>
                                                                <td class="text-center"><a href="#"><?=$p=$override->countRange('checkup_record','branch_id',$clinic['id'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']);$tPatient +=$p?></a></td>
                                                                <td class="text-center"><a href="#"> <?php foreach($override->getRange('lens_prescription','branch_id',$clinic['id'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']) as $no_lens){if($no_lens['eye'] == 'BE'){$tLens +=2;}else{$tLens +=1;}}echo$tLens;$tL +=$tLens;$tLens=0;?></a></td>
                                                                <td class="text-center"><a href="#"><?=$f=$override->countRange('frame_sold','branch_id',$clinic['id'],'sold_date',$_GET['from'],'sold_date',$_GET['to']);$tFrame +=$f?></a></td>
                                                                <td class="text-center">0</td>
                                                            </tr>
                                                        <?php }?>
                                                        <!-- Encase More than one branch -->
                                                        <?php if($_GET['b'] == 'a'){?>
                                                            <tr>
                                                                <th class="text-center">Total</th>
                                                                <th class="text-center"><?=$tPatient?></th>
                                                                <th class="text-center"><?=$tL?> pc</th>
                                                                <th class="text-center"><?=$tFrame?></th>
                                                                <th class="text-center">0</th>
                                                            </tr>
                                                        <?php }?>
                                                    </table>
                                                </div>

                                            </div>
                                            <!-- END INVOICE  START OF MODAL-->
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--<div id="second-tab"></div>
    <div id="third-tab"></div>
    <div id="fourth-tab"></div>-->
</div>
<div class="x-content-footer">
    Copyright © 2018 Siha Optical Eye Center. All rights reserved
</div>
</div>
<!-- END PAGE CONTENT WRAPPER -->
</div>
<!-- END PAGE CONTENT -->
</div>
<!-- END PAGE CONTAINER -->

<!-- MESSAGE BOX-->
<?php include 'signout.php'?>
<!-- END MESSAGE BOX-->

<!-- START PRELOADS -->
<audio id="audio-alert" src="audio/alert.mp3" preload="auto"></audio>
<audio id="audio-fail" src="audio/fail.mp3" preload="auto"></audio>
<!-- END PRELOADS -->

<!-- START SCRIPTS -->
<!-- START PLUGINS -->
<script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
<script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>
<!-- END PLUGINS -->

<!-- START THIS PAGE PLUGINS-->
<script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script>
<!-- END THIS PAGE PLUGINS-->

<!-- START TEMPLATE -->


<script type="text/javascript" src="js/plugins.js"></script>
<script type="text/javascript" src="js/actions.js"></script>
<script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
</script>
</body>

</html>