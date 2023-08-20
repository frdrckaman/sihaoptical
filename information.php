<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
if($user->isLoggedIn()){
    if($user->data()->access_level == 1){
        if($_GET['id'] == 3) {
            if(Input::exists('post')){
                if(Input::get('updateStatus')){
                    try{
                        $user->updateRecord('order_status',array(
                            'status' => Input::get('status'),
                            'staff_id' => $user->data()->id,
                            'date_modified' =>date('Y-m-d')
                        ),Input::get('id'));
                        $successMessage = 'Order Status Updated Successful';
                    }catch (PDOException $e){
                        die($e->getMessage());
                    }
                }
                elseif(Input::get('deleteOrder')){
                    try{
                        $user->deleteRecord('lens_orders','id',Input::get('orderId'));
                        $user->deleteRecord('order_status','order_id',Input::get('orderId'));
                        $successMessage = 'Order Deleted Successful';
                    }catch (PDOException $e){
                        $e->getMessage();
                    }
                }
            }
        }
        elseif($_GET['id'] == 2){
            if(Input::get('updatePatientInfo')){
                $validate = new validate();
                $validate = $validate->check($_POST, array(
                    'firstname' => array(
                        'required' => true,
                        'min' => 2,
                    ),
                    'surname' => array(
                        'required' => true,
                        'min' => 2,
                    ),
                    'sex' => array(
                        'required' => true,
                    ),
                    'age' => array(
                        'required' => true,
                    ),
                    'phone_number' => array(
                        'required' => true,
                    ),
                ));
                if ($validate->passed()) {
                    try {
                        $user->updateRecord('patient', array(
                            'firstname' => Input::get('firstname'),
                            'lastname' => Input::get('surname'),
                            'sex' => Input::get('sex'),
                            'age' => Input::get('age'),
                            'health_insurance' => Input::get('health_insurance'),
                            'dependent_no' => Input::get('dependent_no'),
                            'address' => Input::get('address'),
                            'email_address' => Input::get('email_address'),
                            'phone_number' => Input::get('phone_number'),
                            'occupation' => Input::get('occupation'),
                            'registered_date' => date('Y-m-d'),
                        ),Input::get('id'));
                        $successMessage = 'Patient Info Updated Successful';
                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                } else {
                    $pageError = $validate->errors();
                }
            }
            elseif(Input::get('deletePatient')){
                try{
                    $user->deleteRecord('patient','id',Input::get('pId'));
                    foreach($override->get('checkup_record','patient_id',Input::get('pId')) as $p){
                        $user->deleteRecord('checkup_record','patient_id',$p['id']);
                    }
                    foreach($override->get('payment','patient_id',Input::get('pId')) as $p){
                        $user->deleteRecord('payment','patient_id',$p['id']);
                    }
                    foreach($override->get('prescription','patient_id',Input::get('pId')) as $p){
                        $user->deleteRecord('prescription','patient_id',$p['id']);
                    }
                    foreach($override->get('lens_prescription','patient_id',Input::get('pId')) as $p){
                        $user->deleteRecord('lens_prescription','patient_id',$p['id']);
                    }
                    $successMessage = 'Order Deleted Successful';
                }catch (PDOException $e){
                    $e->getMessage();
                }
            }
        }
        elseif($_GET['id'] == 1){
            if(Input::get('updateStaffInfo')){
                $validate = new validate();
                $validate = $validate->check($_POST, array(
                    'clinic_branch' => array(
                        'required' => true,
                    ),
                    'firstname' => array(
                        'required' => true,
                        'min' => 3,
                    ),
                    'lastname' => array(
                        'required' => true,
                        'min' => 3,
                    ),
                    'position' => array(
                        'required' => true,
                    ),
                    'sex' => array(
                        'required' => true,
                    ),
                    'employee_ID' => array(
                        'required' => true,
                    ),
                    'phone_number' => array(
                        'required' => true,
                    ),
                ));
                if ($validate->passed()) {
                    if (Input::get('position') == 'admin') {
                        $accessLevel = 1;
                    } elseif (Input::get('position') == 'Doctor') {
                        $accessLevel = 2;
                    }elseif(Input::get('position') == 'Receptionist'){
                        $accessLevel = 3;
                    }
                    try {
                        $user->updateRecord('staff', array(
                            'firstname' => Input::get('firstname'),
                            'middlename' => Input::get('middlename'),
                            'lastname' => Input::get('lastname'),
                            'position' => Input::get('position'),
                            'employee_ID' => Input::get('employee_ID'),
                            'gender' => Input::get('sex'),
                            'access_level' => $accessLevel,
                            'phone_number' => Input::get('phone_number'),
                            'branch_id' =>Input::get('clinic_branch')
                        ),Input::get('id'));
                        $successMessage = 'Staff Info Updated Successful';

                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                } else {
                    $pageError = $validate->errors();
                }
            }
            elseif(Input::get('resetPassword')){
                $salt = Hash::salt(32);
                $password = '123456';
                try{
                    $user->updateRecord('staff',array(
                        'password' => Hash::make($password, $salt),
                        'salt' => $salt,
                    ),Input::get('id'));
                    $successMessage = 'Password Reset to Default Successful';
                }
                catch (PDOException $e){
                    $e->getMessage();
                }
            }
            elseif(Input::get('deleteStaff')){
                try{
                    $user->deleteRecord('staff','id',Input::get('id'));
                }catch (PDOException $e){
                    $e->getMessage();
                }
            }
        }
        elseif($_GET['id'] == 4){
            if(Input::exists('post')){
            if(Input::get('deleteLens')){
                    try{
                        $user->deleteRecord('lens_power','id',Input::get('lensId'));
                        $successMessage = 'Lens Deleted Successful';
                    }catch (PDOException $e){
                        $e->getMessage();
                    }
                }
            elseif(Input::get('editPrice')){
                try{
                    $user->updateRecord('lens_power',array(
                        'price' => Input::get('price'),
                    ),Input::get('id'));
                    $successMessage = 'Lens Price Updated Successful';
                }catch (PDOException $e){
                    die($e->getMessage());
                }
            }
            }
        }
        elseif($_GET['id'] == 5){
            if(Input::exists('post')){
                if(Input::get('deleteFrame')){
                    try{
                        $user->deleteRecord('frames','id',Input::get('frameId'));
                        $successMessage = 'Frame Deleted Successful';
                    }catch (PDOException $e){
                        $e->getMessage();
                    }
                }elseif(Input::get('updateFramePrice')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'category' => array(
                            'required' => true,
                        ),
                        'brand' => array(
                            'required' => true,
                        ),
                        'model' => array(
                            'required' => true,
                        ),
                        'size' => array(
                            'required' => true,
                        ),
                        'quantity' => array(
                            'required' => true,
                        ),
                        'price' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()) {
                        $frame_model = $override->get('frame_model','model',Input::get('model'))[0]['id'];
                        try{
                            $user->updateRecord('frames',array(
                                'model' => $frame_model,
                                'frame_size' => Input::get('size'),
                                'quantity' => Input::get('quantity'),
                                'price' => Input::get('price'),
                                'category' =>Input::get('category'),
                                'brand_id' => Input::get('brand'),
                            ),Input::get('id'));
                            $successMessage = 'Frame Price Updated Successful';
                        }catch (PDOException $e){
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
        }
        elseif($_GET['id'] == 6){
            if(Input::exists('post')){
                if(Input::get('deleteMedicine')){
                    try{
                        $user->deleteRecord('medicine','id',Input::get('medId'));
                        $successMessage = 'Medicine Deleted Successful';
                    }catch (PDOException $e){
                        $e->getMessage();
                    }
                }
            }
        }
        elseif($_GET['id'] == 7){
            if(Input::exists('post')){
                if(Input::get('deleteTest')){
                    try{
                        $user->deleteRecord('test_list','id',Input::get('testId'));
                        $successMessage = 'Test Deleted Successful';
                    }catch (PDOException $e){
                        $e->getMessage();
                    }
                }
                elseif(Input::get('updateTestInfo')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'name' => array(
                            'required' => true,
                        ),
                        'cost' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()) {
                        try {
                            $user->updateRecord('test_list', array(
                                'name' => Input::get('name'),
                                'cost' => Input::get('cost'),
                                'insurance_price' => Input::get('insurance_cost')
                            ),Input::get('id'));
                            $successMessage = 'Test Info Updated Successful';

                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
        }
        elseif($_GET['id'] == 8){
            if(Input::exists('post')){
                if(Input::get('deleteProduct')){
                    try{
                        $user->deleteRecord('products','id',Input::get('prodId'));
                        $successMessage = 'Product Deleted Successful';
                    }catch (PDOException $e){
                        $e->getMessage();
                    }
                }
                elseif(Input::get('updateProductInfo')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'name' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()) {
                        try {
                            $user->updateRecord('products', array(
                                'name' => Input::get('name'),
                            ),Input::get('id'));
                            $successMessage = 'Product Info Updated Successful';

                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
        }
        elseif($_GET['id'] == 9){}
        elseif($_GET['id'] == 10){
            if(Input::exists('post')){
                if(Input::get('updateClinic')){
                    try{
                        $user->updateRecord('clinic_branch',array(
                            'name' => Input::get('name'),
                            'location' => Input::get('location'),
                        ),Input::get('id'));
                        $successMessage = 'Clinic Info Updated Successful';
                    }catch (PDOException $e){
                        die($e->getMessage());
                    }
                }
                elseif(Input::get('deleteClinic')){
                    try{
                        $user->deleteRecord('clinic_branch','id',Input::get('id'));
                        $successMessage = 'Clinic Deleted Successful';
                    }catch (PDOException $e){
                        $e->getMessage();
                    }
                }
            }
        }
        elseif($_GET['id'] == 11){
            if(Input::exists('post')){
                if(Input::get('updateInsurance')){
                    try{
                        $user->updateRecord('insurance',array(
                            'name' => Input::get('name'),
                        ),Input::get('id'));
                        $successMessage = 'Insurance Info Updated Successful';
                    }catch (PDOException $e){
                        die($e->getMessage());
                    }
                }
                elseif(Input::get('deleteInsurance')){
                    try{
                        $user->deleteRecord('insurance','id',Input::get('id'));
                        $successMessage = 'Insurance Deleted Successful';
                    }catch (PDOException $e){
                        $e->getMessage();
                    }
                }
            }
        }
        elseif($_GET['id'] == 12){}
        elseif($_GET['id'] == 13){}
        elseif($_GET['id'] == 14){}
        elseif($_GET['id'] == 15){}
        elseif($_GET['id'] == 16){}
        elseif($_GET['id'] == 17){}
        elseif($_GET['id'] == 18){}
        elseif($_GET['id'] == 19){}
        elseif($_GET['id'] == 20){
            if(Input::exists('post')){
                if(Input::get('updateDataEntry')){
                    try{
                        $user->updateRecord('data_entry_price',array(
                            'price' => Input::get('price'),
                        ),Input::get('id'));
                        $successMessage = 'Data Entry Price Updated Successful';
                    }catch (PDOException $e){
                        die($e->getMessage());
                    }
                }
                elseif(Input::get('deleteDataPrice')){
                    try{
                        $user->deleteRecord('data_entry_price','id',Input::get('id'));
                        $successMessage = 'Data Entry Price Deleted Successful';
                    }catch (PDOException $e){
                        $e->getMessage();
                    }
                }
            }
        }
        elseif($_GET['id'] == 21){}
        elseif($_GET['id'] == 22){}
        elseif($_GET['id'] == 23){}
        elseif($_GET['id'] == 24){
            if(Input::exists('post')){
                if(Input::get('deleteSalesProduct')){
                    try{
                        $user->deleteRecord('sales_product','id',Input::get('prodId'));
                        $successMessage = 'Product Deleted Successful';
                    }catch (PDOException $e){
                        $e->getMessage();
                    }
                }
                elseif(Input::get('updateSalesProductInfo')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'name' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()) {
                        try {
                            $user->updateRecord('sales_product', array(
                                'name' => Input::get('name'),
                            ),Input::get('id'));
                            $successMessage = 'Product Info Updated Successful';

                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
        }
        elseif($_GET['id'] == 25){
            if(Input::get('updateDiagnosis')){
                try {
                    $user->updateRecord('diagnosis', array(
                        'name' => Input::get('name'),
                    ),Input::get('id'));
                    $successMessage = 'Diagnosis Updated Successful';

                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }elseif(Input::get('deleteDiagnosis')){
                try{
                    $user->deleteRecord('diagnosis','id',Input::get('id'));
                }catch (PDOException $e){
                    $e->getMessage();
                }
            }
        }
        elseif($_GET['id'] == 26){
            if(Input::get('updateAppointment')){
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
            }elseif(Input::get('deleteAppointment')){
                try{
                    $user->deleteRecord('appointment','id',Input::get('id'));
                }catch (PDOException $e){
                    $e->getMessage();
                }
            }
        }
        elseif($_GET['id'] == 27){}
        elseif($_GET['id'] == 28){}
        elseif($_GET['id'] == 29){}
        elseif($_GET['id'] == 30){}
        elseif($_GET['id'] == 31){}
        elseif($_GET['id'] == 32){}
        elseif($_GET['id'] == 33){}
        else{Redirect::to('404.php');}
    }else{
    switch($user->data()->access_level){
        case 2:
            Redirect::to('dashboard.php');
            break;
        case 3:
            Redirect::to('dashboard.php');
            break;
    }
}
}else{Redirect::to('index.php');}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- META SECTION -->
    <title> Siha Optical | Info </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <!-- END META SECTION -->

    <!-- CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
    <!-- EOF CSS INCLUDE -->
</head>
<body>
<!-- START PAGE CONTAINER -->
<div class="page-container">

    <!-- START PAGE SIDEBAR -->
    <div class="page-sidebar">
        <!-- START X-NAVIGATION -->
        <?php include 'menu.php'?>
        <!-- END X-NAVIGATION -->
    </div>
    <!-- END PAGE SIDEBAR -->

    <!-- PAGE CONTENT -->
    <div class="page-content">

        <!-- START X-NAVIGATION VERTICAL -->
        <?php include 'topBar.php'?>
        <!-- END X-NAVIGATION VERTICAL -->

        <!-- START BREADCRUMB -->
        <ul class="breadcrumb">
            <li><a href="#">Home</a></li>
            <li class="active">Dashboard</li>
        </ul>
        <!-- END BREADCRUMB -->

        <!-- PAGE CONTENT WRAPPER -->
        <div class="page-content-wrap">
            <div class="col-md-12">
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
                <?php }?>
                <?php if($_GET['id'] == 1){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>List of Staff</h3>
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
                                                        <th>Staff ID</th>
                                                        <th>Sex</th>
                                                        <th>Position</th>
                                                        <th>Phone Number</th>
                                                        <th>Action Performed</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php $x=0;foreach($override->getDataAsc('staff','position') as $staff){?>
                                                    <tr>
                                                        <td><?=$staff['firstname'].' '.$staff['middlename'].' '.$staff['lastname']?></td>
                                                        <td><?=$staff['employee_ID']?></td>
                                                        <td><?=$staff['gender']?></td>
                                                        <td><?=$staff['position']?></td>
                                                        <td><?=$staff['phone_number']?></td>
                                                        <td>
                                                            <form method="post">
                                                                <a href="#modal<?=$x?>" class="btn btn-info btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info-circle"></span></a>
                                                                <a href="#pswd<?=$x?>" class="btn btn-warning btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-refresh"></span></a>
                                                                <a href="#dlt<?=$x?>" class="btn btn-danger btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-remove"></span></a>
                                                             </form>
                                                        </td>
                                                    </tr>
                                                    <div class="modal" id="modal<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about staff</h4>
                                                                </div>
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">Branch &nbsp;</label>
                                                                            <div class="col-md-10">
                                                                                <select name="clinic_branch" class="form-control select" data-live-search="true">
                                                                                    <?php $branch=$override->get('clinic_branch','id',$staff['branch_id'])?>
                                                                                    <option value="<?=$staff['branch_id']?>"><?=$branch[0]['name']?></option>
                                                                                    <?php foreach($override->get('clinic_branch','id',$user->data()->branch_id) as $branch){?>
                                                                                        <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                                                                    <?php }?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <label></label>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">F Name</label>
                                                                            <div class="col-md-10">
                                                                                <input type="text" name="firstname" class="form-control" value="<?=$staff['firstname']?>" />
                                                                            </div>
                                                                        </div>
                                                                        <label></label>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">M Name</label>
                                                                            <div class="col-md-10">
                                                                                <input type="text" name="middlename" class="form-control" value="<?=$staff['middlename']?>"/>
                                                                            </div>
                                                                        </div>
                                                                        <label></label>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">L Name</label>
                                                                            <div class="col-md-10">
                                                                                <input type="text" name="lastname" class="form-control" value="<?=$staff['lastname']?>"/>
                                                                            </div>
                                                                        </div>
                                                                        <label></label>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">Sex</label>
                                                                            <div class="col-md-10">
                                                                                <select name="sex" class="form-control select" >
                                                                                    <option value="<?=$staff['gender']?>"><?=$staff['gender']?></option>
                                                                                    <option value="Male">Male</option>
                                                                                    <option value="Female">Female</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <label></label>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">Position</label>
                                                                            <div class="col-md-10">
                                                                                <select name="position" class="form-control select">
                                                                                    <option value="<?=$staff['position']?>"><?=$staff['position']?></option>
                                                                                    <option value="admin">Administrator</option>
                                                                                    <option value="Doctor">Doctor</option>
                                                                                    <option value="Receptionist">Receptionist</option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <label></label>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">Staff ID</label>
                                                                            <div class="col-md-10">
                                                                                <input type="text" name="employee_ID" class="form-control" value="<?=$staff['employee_ID']?>"/>
                                                                            </div>
                                                                        </div>
                                                                        <label></label>
                                                                        <div class="form-group">
                                                                            <label class="col-md-2 control-label">Phone</label>
                                                                            <div class="col-md-10">
                                                                                <input type="text" name="phone_number" class="form-control" value="<?=$staff['phone_number']?>"/>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id" value="<?=$staff['id']?>">
                                                                        <input type="submit" name="updateStaffInfo" value="Update Staff Info" class="btn btn-success" >
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal" id="pswd<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                                    <h4 class="modal-title" id="defModalHead<?=$x?>">RESET PASSWORD</h4>
                                                                </div>
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                        <span style="color: #ff0000">
                                                                            <strong>ARE YOU SURE YOU WANT TO RESET PASSWORD FOR THIS USER?</strong>
                                                                        </span>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id" value="<?=$staff['id']?>">
                                                                        <input type="submit" name="resetPassword" value="Reset Password" class="btn btn-warning" >
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
                                                                    <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE STAFF</h4>
                                                                </div>
                                                                <form method="post">
                                                                    <div class="modal-body">
                                                                        <span style="color: #ff0000">
                                                                            <strong>ARE YOU SURE YOU WANT TO DELETE THIS STAFF ?</strong>
                                                                        </span>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <input type="hidden" name="id" value="<?=$staff['id']?>">
                                                                        <input type="submit" name="deleteStaff" value="DELETE STAFF" class="btn btn-danger" >
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
                elseif($_GET['id'] == 2){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>List of Patient</h3>
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
                                                <th>Sex</th>
                                                <th>Age</th>
                                                <th>Health Insurance</th>
                                                <th>Dependent No.</th>
                                                <th>Address</th>
                                                <th>Occupation</th>
                                                <th>Phone Number</th>
                                                <th>Email Address</th>
                                                <th>Action Performed</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $x=0;foreach($override->getDataAsc('patient','firstname') as $patient){?>
                                                <tr>
                                                    <td><?=$patient['firstname'].' '.$patient['lastname']?></td>
                                                    <td><?=$patient['sex']?></td>
                                                    <td><?=$patient['age']?></td>
                                                    <td><?=$patient['health_insurance']?></td>
                                                    <td><?=$patient['dependent_no']?></td>
                                                    <td><?=$patient['address']?></td>
                                                    <td><?=$patient['occupation']?></td>
                                                    <td><?=$patient['phone_number']?></td>
                                                    <td><?=$patient['email_address']?></td>
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about Order</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">First Name</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="firstname" class="form-control" value="<?=$patient['firstname']?>" />
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Surname</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="surname" class="form-control" value="<?=$patient['lastname']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Sex</label>
                                                                        <div class="col-md-10">
                                                                            <select name="sex" class="form-control select" >
                                                                                <option value="<?=$patient['sex']?>"><?=$patient['sex']?></option>
                                                                                <option value="Male">Male</option>
                                                                                <option value="Female">Female</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Age</label>
                                                                        <div class="col-md-10">
                                                                            <input type="number" min="1" name="age" class="form-control" value="<?=$patient['age']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Health Ins</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="health_insurance" class="form-control" value="<?=$patient['health_insurance']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Dep No.</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="dependent_no" class="form-control" value="<?=$patient['dependent_no']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Address</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="address" class="form-control" value="<?=$patient['address']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Occupation</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="occupation" class="form-control" value="<?=$patient['occupation']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Phone</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text"  name="phone_number" class="form-control" value="<?=$patient['phone_number']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Email</label>
                                                                        <div class="col-md-10">
                                                                            <input type="email" name="email_address" class="form-control" value="<?=$patient['email_address']?>"/>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?=$patient['id']?>">
                                                                    <input type="submit" name="updatePatientInfo" value="Update Patient Info" class="btn btn-success" >
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE PATIENT</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                    <span style="color: #ff0000">
                                                                        <strong>ARE YOU SURE YOU WANT TO DELETE THIS PATIENT ?</strong>
                                                                    </span>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="pId" value="<?=$patient['id']?>">
                                                                    <input type="submit" name="deletePatient" value="DELETE STAFF" class="btn btn-danger" >
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
                elseif($_GET['id'] == 3){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>List of Orders</h3>
                                <span></span>

                            </div>
                            <div class="push-down-10 pull-right">
                                <a href="information.php?id=18" class="btn btn-default"><span class="fa fa-print"></span> Print All Orders</a>
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
                                                <th>Detail about Order</th>
                                                <th>Date Placed</th>
                                                <th>Status</th>
                                                <th>Action Performed</th>
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
                                                            <a href="#modal<?=$x?>" class="btn btn-info btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info-circle"></span></a>
                                                            <a href="information.php?id=16&ord=<?=$order['id']?>" class="btn btn-info btn-rounded btn-condensed btn-sm" ><span class="fa fa-print"></span></a>
                                                            <a href="#dlt<?=$x?>" class="btn btn-danger btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-remove"></span></a>
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
                                                                    <h2>&nbsp;</h2>
                                                                    <?php $staff = $override->get('staff','id',$order['staff_id'])?>
                                                                    <div class="form-group">
                                                                        <label class="col-md-4 control-label">Employee Who place this Order : </label>
                                                                        <div class="col-md-8">
                                                                            <input type="text" class="form-control" value="<?=$staff[0]['firstname'].' '.$staff[0]['middlename'].' '.$staff[0]['lastname']?>" disabled/>
                                                                        </div>
                                                                    </div>
                                                                    <h4>&nbsp;</h4>
                                                                    <h4>Change Order Status</h4>
                                                                    <h4>&nbsp;</h4>
                                                                    <div class="form-group">
                                                                        <input type="hidden" value="<?=$os[0]['id']?>" name="id">
                                                                        <label class="col-md-2 control-label">Status : </label>
                                                                        <div class="col-md-10">
                                                                            <select name="status" class="form-control select" required>
                                                                                <option value="">Select Order Status</option>
                                                                                <option value="0">Pending</option>
                                                                                <option value="1">Confirmed</option>
                                                                                <option value="2">Received</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <h4>&nbsp;</h4>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="submit" name="updateStatus" value="Update Status" class="btn btn-success" >
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE ORDER</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                        <span style="color: #ff0000">
                                                                            <strong>ARE YOU SURE YOU WANT TO DELETE THIS ORDER ?</strong>
                                                                        </span>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="orderId" value="<?=$order['id']?>">
                                                                    <input type="submit" name="deleteOrder" value="DELETE ORDER" class="btn btn-danger" >
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
                elseif($_GET['id'] == 4){?>
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
                                            <th>Action Performed</th>
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
                                                            <h4 class="modal-title" id="defModalHead<?=$x?>">Edit Lens Information</h4>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <label></label>
                                                                <div class="form-group">
                                                                    <label class="col-md-2 control-label">Lens Cost</label>
                                                                    <div class="col-md-10">
                                                                        <input type="text" name="price" class="form-control" value="<?=$lens['price']?>" />
                                                                    </div>
                                                                </div>
                                                                <label></label>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="id" value="<?=$lens['id']?>">
                                                                <input type="submit" name="editPrice" value="Edit Info" class="btn btn-success" >
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
                                                            <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE LENS</h4>
                                                        </div>
                                                        <form method="post">
                                                            <div class="modal-body">
                                                                <span style="color: #ff0000">
                                                                    <strong>ARE YOU SURE YOU WANT TO DELETE THIS LENS ?</strong>
                                                                </span>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <input type="hidden" name="lensId" value="<?=$lens['id']?>">
                                                                <input type="submit" name="deleteLens" value="DELETE ORDER" class="btn btn-danger" >
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
                elseif($_GET['id'] == 5){?>
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
                                                <th>Quantity</th>
                                                <th>Price</th>
                                                <th>Clinic Branch</th>
                                                <th>Action Performed</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $x=0;if($user->data()->power == 1){$frame=$override->getData('frames');}else{$frame=$override->get('frames','branch_id',$user->data()->branch_id);} foreach($frame as $frames){
                                                $model = $override->get('frame_model','id',$frames['model']);
                                                $brand = $override->get('frame_brand','id',$frames['brand_id']);
                                                $branchName=$override->get('clinic_branch','id',$frames['branch_id'])?>
                                                <tr>
                                                    <td><?=$brand[0]['name']?></td>
                                                    <td><?=$model[0]['model']?></td>
                                                    <td><?=$frames['frame_size']?></td>
                                                    <td><?=$frames['quantity']?></td>
                                                    <td><?=$frames['price']?></td>
                                                    <td><?=$branchName[0]['name']?></td>
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">Edit Frame Price</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Select Category &nbsp;</label>
                                                                        <div class="col-md-10">
                                                                            <select name="category" class="form-control select" data-live-search="true">
                                                                                <option value="<?=$frames['category']?>"><?php if($frames['category'] == 1){echo'Frame';}elseif($frames['category'] == 2){echo'Sun Glass';}else{echo'Select Category';}?></option>
                                                                                <option value="1">Frame</option>
                                                                                <option value="2">Sun Glass</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Frame Brand</label>
                                                                        <div class="col-md-10">
                                                                            <?php $brand=$override->get('frame_brand','id',$frames['brand_id'])?>
                                                                            <select name="brand" class="form-control select" data-live-search="true">
                                                                                <option value="<?=$frames['brand_id']?>"><?php if($brand){echo $brand[0]['name'];}else{echo'Select Brand';}?></option>
                                                                                <?php foreach($override->getData('frame_brand') as $brand){?>
                                                                                    <option value="<?=$brand['id']?>"><?=$brand['name']?></option>
                                                                                <?php }?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Frame Model</label>
                                                                        <div class="col-md-10">
                                                                            <?php $model=$override->get('frame_model','id',$frames['model'])?>
                                                                            <input type="text" name="model" class="form-control" value="<?=$model[0]['model']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Frame Size</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="size" class="form-control" value="<?=$frames['frame_size']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Price</label>
                                                                        <div class="col-md-10">
                                                                            <input type="number" name="price" class="form-control" value="<?=$frames['price']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Quantity</label>
                                                                        <div class="col-md-10">
                                                                            <input type="number" min="0" name="quantity" class="form-control" value="<?=$frames['quantity']?>"/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?=$frames['id']?>">
                                                                    <input type="submit" name="updateFramePrice" value="Update" class="btn btn-success" >
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
                elseif($_GET['id'] == 6){?>
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
                                                <th>Action Performed</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $x=0;foreach($override->getData('medicine') as $info){?>
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE MEDICINE</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                <span style="color: #ff0000">
                                                                    <strong>ARE YOU SURE YOU WANT TO DELETE THIS MEDICINE ?</strong>
                                                                </span>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="medId" value="<?=$info['id']?>">
                                                                    <input type="submit" name="deleteMedicine" value="DELETE MEDICINE" class="btn btn-danger" >
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
                elseif($_GET['id'] == 7){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>List of Test Performed</h3>
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
                                                <th>Cash Cost</th>
                                                <th>Insurance Cost</th>
                                                <th>Action Performed</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $x=0;foreach($override->getData('test_list') as $info){?>
                                                <tr>
                                                    <td><?=$info['name']?></td>
                                                    <td><?=$info['cost']?></td>
                                                    <td><?=$info['insurance_price']?></td>
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about Test</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Name</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="name" class="form-control" value="<?=$info['name']?>" required=""/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Cost</label>
                                                                        <div class="col-md-10">
                                                                            <input type="number" min="0" name="cost" class="form-control" value="<?=$info['cost']?>" required=""/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Insurance Cost</label>
                                                                        <div class="col-md-10">
                                                                            <input type="number" min="0" name="insurance_cost" class="form-control" value="<?=$info['insurance_price']?>" required=""/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?=$info['id']?>">
                                                                    <input type="submit" name="updateTestInfo" value="Update Test Info" class="btn btn-success" >
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE TEST</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                <span style="color: #ff0000">
                                                                    <strong>ARE YOU SURE YOU WANT TO DELETE THIS TEST ?</strong>
                                                                </span>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="testId" value="<?=$info['id']?>">
                                                                    <input type="submit" name="deleteTest" value="DELETE TEST" class="btn btn-danger" >
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
                elseif($_GET['id'] == 8){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>List of Product</h3>
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
                                                <th>Action Performed</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $x=0;foreach($override->getData('products') as $info){?>
                                                <tr>
                                                    <td><?=$info['name']?></td>
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about Product</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Name</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="name" class="form-control" value="<?=$info['name']?>" required=""/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?=$info['id']?>">
                                                                    <input type="submit" name="updateProductInfo" value="Update Product Info" class="btn btn-success" >
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE PRODUCT</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                <span style="color: #ff0000">
                                                                    <strong>ARE YOU SURE YOU WANT TO DELETE THIS PRODUCT ?</strong>
                                                                </span>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="prodId" value="<?=$info['id']?>">
                                                                    <input type="submit" name="deleteProduct" value="DELETE PRODUCT" class="btn btn-danger" >
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
                elseif($_GET['id'] == 9){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>Patient Records</h3>
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
                                <?php $x=0;foreach($override->getDataOrderBy('checkup_record') as $medRec){?>
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
                                                            <label class="col-md-2"></label>
                                                            <div class="col-md-2">
                                                                <input name="re" type="text" class="form-control" value="RE: <?=$medRec['RE']?>" disabled>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input name="le" type="text" class="form-control" value="LE: <?=$medRec['LE']?>" disabled>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input name="pd" type="text" class="form-control" value="PD: <?=$medRec['PD']?>" disabled>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <input name="ph" type="text" class="form-control" value="PH: <?=$medRec['PH']?>" disabled>
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
                                                                <textarea name="diagnosis" class="form-control" rows="1" placeholder="Diagnosis: <?=$medRec['diagnosis']?>" disabled></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-offset-1 col-md-10">
                                                                <?php $test=$override->get('test_list','id',$medRec['other_test'])?>
                                                                <input type="text" name="" class="form-control" value="Test Performed: <?=$test[0]['name']?>" disabled>
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
                                                                <div class="col-md-12">
                                                                    <label class="col-md-2 control-label">EYE</label>
                                                                    <label class="col-md-2 control-label">SPH</label>
                                                                    <label class="col-md-2 control-label">CYL</label>
                                                                    <label class="col-md-2 control-label">AXIS</label>
                                                                    <label class="col-md-2 control-label">VA</label>
                                                                    <label class="col-md-2 control-label">ADD</label>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-12">
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
                                                                <div class="col-md-12">
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
                                                            <h4 class="col-md-3"><strong>RX</strong></h4>
                                                            <h2>&nbsp;</h2>
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <label class="col-md-2 control-label">EYE</label>
                                                                    <label class="col-md-2 control-label">SPH</label>
                                                                    <label class="col-md-2 control-label">CYL</label>
                                                                    <label class="col-md-2 control-label">AXIS</label>
                                                                    <label class="col-md-2 control-label">VA</label>
                                                                    <label class="col-md-2 control-label">ADD</label>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-12">
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
                                                                <div class="col-md-12">
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
                                                            <div class="col-md-10">
                                                                <?php $lens=$override->get('lens_category','id',$medRec['lens']);?>
                                                                <input type="text" class="form-control" value="Lens : <?=$lens[0]['name']?>" disabled/>
                                                            </div>
                                                            <?php $eyes=$override->getNews('lens_prescription','patient_id',$medRec['patient_id'],'checkup_date',$medRec['checkup_date']);
                                                            foreach($eyes as $eye){if($eye['eye'] == 'Both') {
                                                                $RE = $eye['lens_power'];
                                                                $LE = $eye['lens_power'];
                                                            }elseif($eye['eye'] == 'RE') {
                                                                $RE = $eye['lens_power'];
                                                            }elseif($eye['eye'] == 'LE'){
                                                                $LE = $eye['lens_power'];}}?>
                                                            <?php ?>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control" value="RE Power : <?=$RE?>" disabled/>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <input type="text" class="form-control" value="LE Power : <?=$LE?>" disabled/>
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
                elseif($_GET['id'] == 10){?>
                    <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="panel-title-box">
                            <h3>Clinic Branches</h3>
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
                        <th>Branch Name</th>
                        <th>Location</th>
                        <th>Action Performed</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $x=0;foreach($override->getDataOrderBy('clinic_branch') as $branch){?>
                        <tr>
                            <td><?=$branch['name']?></td>
                            <td><?=$branch['location']?></td>
                            <td>
                                <form method="post">
                                    <a href="#modal<?=$x?>" class="btn btn-info btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info-circle"></span></a>
                                    <?php if($user->data()->employee_ID == 'FEC/337331'){?>
                                        <a href="#dlt<?=$x?>" class="btn btn-danger btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-remove"></span></a>
                                    <?php }?>
                                </form>
                            </td>
                        </tr>
                        <div class="modal" id="modal<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about staff</h4>
                                    </div>
                                    <form method="post">
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Branch Name</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="name" class="form-control" value="<?=$branch['name']?>" />
                                                </div>
                                            </div>
                                            <label></label>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Location</label>
                                                <div class="col-md-10">
                                                    <input type="text" name="location" class="form-control" value="<?=$branch['location']?>"/>
                                                </div>
                                            </div>
                                            <label></label>

                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="id" value="<?=$branch['id']?>">
                                            <input type="submit" name="updateClinic" value="Update Clinic Branch Info" class="btn btn-success" >
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
                                        <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE CLINIC BRANCH</h4>
                                    </div>
                                    <form method="post">
                                        <div class="modal-body">
                                            <span style="color: #ff0000">
                                                <strong>ARE YOU SURE YOU WANT TO DELETE THIS CLINIC BRANCH ?</strong>
                                            </span>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="hidden" name="id" value="<?=$branch['id']?>">
                                            <input type="submit" name="deleteClinic" value="DELETE STAFF" class="btn btn-danger" >
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
                elseif($_GET['id'] == 11){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>Health Insurance</h3>
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
                                                <th>Insurance Name</th>
                                                <th>Action Performed</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $x=0;foreach($override->getDataOrderBy('insurance') as $ins){?>
                                                <tr>
                                                    <td><?=$ins['name']?></td>
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about Insurance</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Insurance Name</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="name" class="form-control" value="<?=$ins['name']?>" />
                                                                        </div>
                                                                    </div>
                                                                    <label></label>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?=$ins['id']?>">
                                                                    <input type="submit" name="updateInsurance" value="Update Insurance Info" class="btn btn-success" >
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE INSURANCE</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                    <span style="color: #ff0000">
                                                                        <strong>ARE YOU SURE YOU WANT TO DELETE THIS INSURANCE ?</strong>
                                                                    </span>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?=$ins['id']?>">
                                                                    <input type="submit" name="deleteInsurance" value="DELETE INSURANCE" class="btn btn-danger" >
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
                elseif($_GET['id'] == 12){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>Daily Report</h3>
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
                                                <th>Name of Item</th>
                                                <th>Open Stock(Pcs)</th>
                                                <th>Purchase(Pcs)</th>
                                                <th>Sales(Pcs)</th>
                                                <th>Selling Price</th>
                                                <th>Sale value</th>
                                                <th>Closing Stock(Pcs)</th>
                                                <th>Stock Value : TZS</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 13){?>
                    <div class="content-frame">
                        <!-- START CONTENT FRAME TOP -->
                        <div class="content-frame-top">
                            <div class="page-title">
                                <h2><span class="fa fa-comments"></span> Sent Messages</h2>
                            </div>
                            <div class="pull-right">
                                <a href="addInfo.php?id=15" class="btn btn-danger"><span class="fa fa-envelope"></span> Compose Sms</a>
                                <button class="btn btn-default content-frame-right-toggle"><span class="fa fa-bars"></span></button>
                            </div>
                        </div>
                        <!-- END CONTENT FRAME TOP -->

                        <!-- START CONTENT FRAME BODY -->
                        <div class="content-frame-body-left">
                            <div class="messages messages-img">
                                <?php if($override->get('sms','staff_id',$user->data()->id)){
                                    foreach($override->getSort('sms','staff_id',$user->data()->id,'id') as $sms){
                                        $receiver=$override->get('staff','id',$sms['receiver_id']);if($receiver){$rec_id=$receiver[0]['branch_id'];}else{$rec_id=0;}$branchC=$override->get('clinic_branch','id',$rec_id);?>
                                        <div class="item">
                                            <div class="image">
                                                <img src="<?php if($receiver && $receiver[0]['picture']){echo$receiver[0]['picture'];}else{echo 'assets/images/users/no-image.jpg';}?>" alt="<?=$receiver[0]['lastname']?>">
                                            </div>
                                            <div class="text">
                                                <div class="heading">
                                                    <a href="#"><?php if($receiver){echo $receiver[0]['firstname'].' '.$receiver[0]['middlename'].' '.$receiver[0]['lastname'].' ( '.$receiver[0]['position'].' : '.$branchC[0]['name'].' )';}else{echo'Patients';}?></a>
                                                    <span class="date"><?=$sms['sms_date']?></span>
                                                </div>
                                               <?=$sms['message']?>
                                            </div>
                                        </div>
                                <?php }}else {?>
                                    <div class="alert alert-info" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <strong>Empty!&nbsp;</strong> No Sent SMS Available
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                        <!-- END CONTENT FRAME BODY -->
                    </div>
                <?php }
                elseif($_GET['id'] == 14){?>
                    <div class="content-frame">
                    <!-- START CONTENT FRAME TOP -->
                    <div class="content-frame-top">
                        <div class="page-title">
                            <h2><span class="fa fa-inbox"></span> Sent Emails <small></small></h2>
                        </div>

                        <div class="pull-right">
                            <button class="btn btn-default"><span class="fa fa-cogs"></span> Settings</button>
                            <button class="btn btn-default content-frame-left-toggle"><span class="fa fa-bars"></span></button>
                        </div>
                    </div>
                    <!-- END CONTENT FRAME TOP -->

                    <!-- START CONTENT FRAME LEFT -->
                    <div class="content-frame-left">
                        <div class="block">
                            <a href="addInfo.php?id=16" class="btn btn-danger btn-block btn-lg"><span class="fa fa-edit"></span> COMPOSE EMAIL</a>
                        </div>
                        <div class="block">
                            <div class="list-group border-bottom">
                                <a href="#" class="list-group-item"><span class="fa fa-inbox"></span> Inbox <span class="badge badge-success"></span></a>
                                <a href="information.php?id=14" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
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
                                <?php if($override->get('emails','staff_id',$user->data()->id)){
                                    foreach($override->getSort('emails','staff_id',$user->data()->id,'id') as $emails){$receiver=$override->get('staff','id',$emails['receiver_id']);$branchC=$override->get('clinic_branch','id',$receiver[0]['branch_id']);?>
                                        <div class="mail-item mail-unread mail-info">
                                            <div class="mail-checkbox">
                                                <input type="checkbox" class="icheckbox"/>
                                            </div>
                                            <div class="mail-user"><?=$receiver[0]['lastname'].' ( '.$receiver[0]['position'].' : '.$branchC[0]['name'].' )'?></div>
                                            <a href="information.php?id=15&msg=<?=$emails['id']?>" class="mail-text">&nbsp;&nbsp;&nbsp;&nbsp;<?=$emails['subject']?></a>
                                            <div class="mail-date"><?=$emails['email_date']?></div>
                                        </div>
                                <?php }}else {?>
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
                elseif($_GET['id'] == 15){
                    if($message=$override->get('emails','id',$_GET['msg'])){$receiver=$override->get('staff','id',$message[0]['receiver_id']);$branchC=$override->get('clinic_branch','id',$receiver[0]['branch_id']);?>
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
                                <button href="addInfo.php?id=16" class="btn btn-danger btn-block btn-lg"><span class="fa fa-edit"></span> COMPOSE EMAIL</button>
                            </div>
                            <div class="block">
                                <div class="list-group border-bottom">
                                    <button href="#" class="list-group-item"><span class="fa fa-inbox"></span> Inbox <span class="badge badge-success"></span></button>
                                    <button href="information.php?id=14" class="list-group-item"><span class="fa fa-rocket"></span> Sent</button>
                                    <button href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted <span class="badge badge-default"></span></button>
                                </div>
                            </div>

                        </div>
                        <!-- END CONTENT FRAME LEFT -->

                        <!-- START CONTENT FRAME BODY -->
                        <div class="content-frame-body">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <div class="pull-left">
                                        <img src="<?php if($receiver[0]['picture']){echo$receiver[0]['picture'];}else{echo 'assets/images/users/no-image.jpg';}?>" class="panel-title-image" alt="<?=$receiver[0]['lastname']?>"/>
                                        <h3 class="panel-title"><?=$receiver[0]['firstname'].' '.$receiver[0]['middlename'].' '.$receiver[0]['lastname']?> <small>( <?=$receiver[0]['position'].' : '.$branchC[0]['name']?> )</small></h3>
                                    </div>
                                    <div class="pull-right">
                                        <button class="btn btn-default"><span class="fa fa-mail-reply"></span></button>
                                        <button class="btn btn-default"><span class="fa fa-warning"></span></button>
                                        <button class="btn btn-default"><span class="fa fa-trash-o"></span></button>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?=$message[0]['message']?>
                                </div>

                            </div>
                        </div>
                        <!-- END CONTENT FRAME BODY -->
                    </div>
                <?php }}
                elseif($_GET['id'] == 16){
                    $order=$override->get('lens_orders','id',$_GET['ord']);$staff=$override->get('staff','id',$order[0]['staff_id']);
                    $product=$override->get('products','id',$order[0]['product']);$branch=$override->get('clinic_branch','id',$staff[0]['branch_id'])?>
                    <div class="page-content-wrap">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h2>ORDER ID : <?=$order[0]['id']?><strong> Ref No : <?=$order[0]['ref_no']?></strong></h2>
                                        <div class="push-down-10 pull-right">
                                            <button class="btn btn-default" onclick="window.print()"><span class="fa fa-print"></span> Print</button>
                                        </div>
                                        <!-- INVOICE -->
                                        <div class="invoice">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="invoice-address">
                                                        <h5></h5>
                                                        <h6>Organization Name</h6>
                                                        <p><strong>Place by : </strong>&nbsp;<?=$staff[0]['firstname'].' '.$staff[0]['middlename'].' '.$staff[0]['lastname'].' ( '.$staff[0]['position'].' : '.$branch[0]['name'].' ) '?></p>
                                                        <p><strong>Order to : </strong>&nbsp;<?=$order[0]['order_from']?></p>
                                                        <p><strong>Product : </strong>&nbsp;<?=$product[0]['name']?></p>
                                                        <p><strong>Material : </strong>&nbsp;<?=$order[0]['material']?></p>
                                                        <p><strong>Quantity : </strong>&nbsp;<?=$order[0]['RE_qty'] + $order[0]['LE_qty']?></p>
                                                        <p><strong>Date : </strong>&nbsp;<?=$order[0]['order_date']?></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="invoice-address">
                                                        <h5></h5>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Eye</th>
                                                                    <th>Sph</th>
                                                                    <th>Cyl</th>
                                                                    <th>Axis</th>
                                                                    <th>VA</th>
                                                                    <th>Add</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr>
                                                                    <td>Right</td>
                                                                    <td><input name="ref_od_sphere" type="text" value="<?=$order[0]['RE_sph']?>" class="form-control" disabled/></td>
                                                                    <td><input name="ref_cyl" type="text" value="<?=$order[0]['RE_cyl']?>" class="form-control" disabled/></td>
                                                                    <td><input name="ref_axis" type="text" value="<?=$order[0]['RE_axis']?>" class="form-control" disabled/></td>
                                                                    <td><input name="ref_va" type="text" value="<?=$order[0]['RE_add']?>" class="form-control" disabled/></td>
                                                                    <td><input name="ref_add" type="text" value="<?=$order[0]['RE_qty']?>" class="form-control" disabled/></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Left</td>
                                                                    <td><input name="add_ref_od_sphere" type="text" value="<?=$order[0]['LE_sph']?>" class="form-control" disabled/></td>
                                                                    <td><input name="add_ref_cyl" type="text" value="<?=$order[0]['LE_cyl']?>" class="form-control" disabled/></td>
                                                                    <td><input name="add_ref_axis" type="text" value="<?=$order[0]['LE_axis']?>" class="form-control" disabled/></td>
                                                                    <td><input name="add_ref_va" type="text" value="<?=$order[0]['LE_add']?>" class="form-control" disabled/></td>
                                                                    <td><input name="add_ref_add" type="text" value="<?=$order[0]['LE_qty']?>" class="form-control" disabled/></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="table-invoice">
                                                <table class="table">
                                                    <tr>
                                                        <td><p><?=$order[0]['order_details']?></p></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- END INVOICE -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 17){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>Doctor Report</h3>
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
                                                <th>From</th>
                                                <th>To</th>
                                                <th>No Patient</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 18){?>
                    <div class="push-down-10 pull-right">
                        <button class="btn btn-default" onclick="window.print()"><span class="fa fa-print"></span> Print</button>
                    </div>
                    <?php foreach($override->getData('lens_orders') as $order){$staff=$override->get('staff','id',$order['staff_id']);
                        $product=$override->get('products','id',$order['product']);$branch=$override->get('clinic_branch','id',$staff[0]['branch_id'])?>

                        <div class="page-content-wrap">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <h2>ORDER ID : <?=$order['id']?><strong> Ref No : <?=$order['ref_no']?></strong></h2>

                                        <!--INVOICE -->
                                        <div class="invoice">
                                            <div class="row">
                                                <div class="col-md-10">
                                                    <div class="invoice-address">
                                                        <h5></h5>
                                                        <h6>Organization Name</h6>
                                                        <p><strong>Place by : </strong>&nbsp;<?=$staff[0]['firstname'].' '.$staff[0]['middlename'].' '.$staff[0]['lastname'].' ( '.$staff[0]['position'].' : '.$branch[0]['name'].' ) '?></p>
                                                        <p><strong>Order to : </strong>&nbsp;<?=$order['order_from']?></p>
                                                        <p><strong>Product : </strong>&nbsp;<?=$product[0]['name']?></p>
                                                        <p><strong>Material : </strong>&nbsp;<?=$order['material']?></p>
                                                        <p><strong>Quantity : </strong>&nbsp;<?=$order['RE_qty'] + $order['LE_qty']?></p>
                                                        <p><strong>Date : </strong>&nbsp;<?=$order['order_date']?></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="invoice-address">
                                                        <h5></h5>
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Eye</th>
                                                                    <th>Sph</th>
                                                                    <th>Cyl</th>
                                                                    <th>Axis</th>
                                                                    <th>VA</th>
                                                                    <th>Add</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                <tr>
                                                                    <td>Right</td>
                                                                    <td><input name="ref_od_sphere" type="text" value="<?=$order['RE_sph']?>" class="form-control" disabled/></td>
                                                                    <td><input name="ref_cyl" type="text" value="<?=$order['RE_cyl']?>" class="form-control" disabled/></td>
                                                                    <td><input name="ref_axis" type="text" value="<?=$order['RE_axis']?>" class="form-control" disabled/></td>
                                                                    <td><input name="ref_va" type="text" value="<?=$order['RE_add']?>" class="form-control" disabled/></td>
                                                                    <td><input name="ref_add" type="text" value="<?=$order['RE_qty']?>" class="form-control" disabled/></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Left</td>
                                                                    <td><input name="add_ref_od_sphere" type="text" value="<?=$order['LE_sph']?>" class="form-control" disabled/></td>
                                                                    <td><input name="add_ref_cyl" type="text" value="<?=$order['LE_cyl']?>" class="form-control" disabled/></td>
                                                                    <td><input name="add_ref_axis" type="text" value="<?=$order['LE_axis']?>" class="form-control" disabled/></td>
                                                                    <td><input name="add_ref_va" type="text" value="<?=$order['LE_add']?>" class="form-control" disabled/></td>
                                                                    <td><input name="add_ref_add" type="text" value="<?=$order['LE_qty']?>" class="form-control" disabled/></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="table-invoice">
                                                <table class="table">
                                                    <tr>
                                                        <td><p><?=$order['order_details']?></p></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <!-- END INVOICE -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }}
                elseif($_GET['id'] == 19){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>Frame Sales Report</h3>
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
                                                <th>Staff</th>
                                                <th>Product</th>
                                                <th>Batch</th>
                                                <th>Available</th>
                                                <th>Sold</th>
                                                <th>Total</th>
                                                <th>Cash in Hand</th>
                                                <th>Remaining Cash</th>
                                                <th>Total Cost</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($override->get('frame_sales','status',0) as $sales){$staff=$override->get('staff','id',$sales['emp_id']);$product=$override->get('sales_product','id',$sales['product_id'])?>
                                                <tr>
                                                    <td><?=$staff[0]['firstname'].' '.$staff[0]['middlename'].' '.$staff[0]['lastname']?></td>
                                                    <td><?=$product[0]['name']?></td>
                                                    <td><?=$sales['price_per']?></td>
                                                    <td><?=$sales['quantity'] - $sales['sold_qty']?></td>
                                                    <td><?=$sales['sold_qty']?></td>
                                                    <td><?=$sales['quantity']?></td>
                                                    <td><?=$sales['sold_qty'] * $sales['price_per']?></td>
                                                    <td><?=$sales['total_cost'] - ($sales['sold_qty'] * $sales['price_per'])?></td>
                                                    <td><?=$sales['total_cost']?></td>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 20){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>Data Entry Price</strong></h3>
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
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $x=1;foreach($override->getData('data_entry_price') as $dataEntry){?>
                                        <tr>
                                            <td><?=$dataEntry['price'].'.Tsh '?>&nbsp;&nbsp; Per Data</td>
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
                                                        <h4 class="modal-title" id="defModalHead<?=$x?>">Edit Data Entry Price</h4>
                                                    </div>
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label">Price </label>
                                                                <div class="col-md-10">
                                                                    <input type="number" min="1" name="price" class="form-control" value="<?=$dataEntry['price']?>" />
                                                                </div>
                                                            </div>
                                                            <label></label>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="id" value="<?=$dataEntry['id']?>">
                                                            <input type="submit" name="updateDataEntry" value="Update Data Entry Price" class="btn btn-success" >
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
                                                        <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE DATA ENTRY PRICE</h4>
                                                    </div>
                                                    <form method="post">
                                                        <div class="modal-body">
                                                            <span style="color: #ff0000">
                                                                <strong>ARE YOU SURE YOU WANT TO DELETE THIS DATA ?</strong>
                                                            </span>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <input type="hidden" name="id" value="<?=$dataEntry['id']?>">
                                                            <input type="submit" name="deleteDataPrice" value="DELETE PRICE" class="btn btn-danger" >
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
                elseif($_GET['id'] == 21){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>Data Entry Payment Record</strong></h3>
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
                                        <th>Data Clerk</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Date</th>
                                        <th>Paid By</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($override->getDataDesc('data_pay_rec','id') as $payRec){$emp=$override->get('staff','id',$payRec['emp_id']);$staff=$override->get('staff','id',$payRec['staff_id'])?>
                                        <tr>
                                            <td><?=$emp[0]['firstname'].' '.$emp[0]['middlename'].' '.$emp[0]['lastname']?></td>
                                            <td><?=$payRec['quantity']?></td>
                                            <td><?=$payRec['price']?></td>
                                            <td><?=$payRec['pay_date']?></td>
                                            <td><?=$staff[0]['firstname'].' '.$staff[0]['middlename'].' '.$staff[0]['lastname']?></td>
                                        </tr>
                                    <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 22){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>Data Entry Payment Panel</strong></h3>
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
                                        <th>Data Clerk</th>
                                        <th>Quantity</th>
                                        <th>Paid Quantity</th>
                                        <th>Paid Amount</th>
                                        <th>Remain Amount</th>
                                        <th>Total Cost</th>
                                        <th>Date</th>
                                        <th>Paid By</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $price=$override->getData('data_entry_price');foreach($override->getDataDesc('data_payment','id') as $payRec){$emp=$override->get('staff','id',$payRec['emp_id']);$staff=$override->get('staff','id',$payRec['staff_id'])?>
                                        <tr>
                                            <td><?=$emp[0]['firstname'].' '.$emp[0]['middlename'].' '.$emp[0]['lastname']?></td>
                                            <td><?=$payRec['quantity']?></td>
                                            <td><?=$payRec['pay_qty']?></td>
                                            <td><?=$payRec['price']?></td>
                                            <td><?php if(($payRec['quantity']*$price[0]['price'])-$payRec['price'] <= 0){?><span class="label label-info">Paid advance <?=$payRec['price']?></span><?php }else{echo($payRec['quantity']*$price[0]['price'])-$payRec['price'];}?></td>
                                            <td><?=$payRec['quantity']*$price[0]['price']?></td>
                                            <td><?=$payRec['pay_date']?></td>
                                            <td><?=$staff[0]['firstname'].' '.$staff[0]['middlename'].' '.$staff[0]['lastname']?></td>
                                        </tr>
                                    <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 23){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>Frames Sales Details</strong></h3>
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
                                        <th>Customer Name</th>
                                        <th>Phone Number</th>
                                        <th>Email Address</th>
                                        <th>Location</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Date</th>
                                        <th>Notes</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($override->getDataDesc('sales_details','id') as $myStock){$product=$override->get('sales_product','id',$myStock['product_id'])?>
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
                elseif($_GET['id'] == 24){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>List of Sales Product</h3>
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
                                                <th>Action Performed</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $x=0;foreach($override->getData('sales_product') as $info){?>
                                                <tr>
                                                    <td><?=$info['name']?></td>
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">More Information about Product</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label class="col-md-2 control-label">Name</label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="name" class="form-control" value="<?=$info['name']?>" required=""/>
                                                                        </div>
                                                                    </div>
                                                                    <label></label>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?=$info['id']?>">
                                                                    <input type="submit" name="updateSalesProductInfo" value="Update Product Info" class="btn btn-success" >
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
                                                                <h4 class="modal-title" id="defModalHead<?=$x?>">DELETE PRODUCT</h4>
                                                            </div>
                                                            <form method="post">
                                                                <div class="modal-body">
                                                                <span style="color: #ff0000">
                                                                    <strong>ARE YOU SURE YOU WANT TO DELETE THIS PRODUCT ?</strong>
                                                                </span>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="prodId" value="<?=$info['id']?>">
                                                                    <input type="submit" name="deleteSalesProduct" value="DELETE PRODUCT" class="btn btn-danger" >
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
                elseif($_GET['id'] == 25){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>List of Diagnosis</h3>
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
                                                <th>Diagnosis Name</th>
                                                <th>Action Performed</th>
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
                                                                        <label class="col-md-2 control-label">Diagnosis </label>
                                                                        <div class="col-md-10">
                                                                            <input type="text" name="name" class="form-control" value="<?=$diagnosis['name']?>" />
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
                                                                        <strong>ARE YOU SURE YOU WANT TO DELETE THIS DIAGNOSIS??</strong>
                                                                    </span>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <input type="hidden" name="id" value="<?=$diagnosis['id']?>">
                                                                    <input type="submit" name="deleteDiagnosis" value="DELETE DIAGNOSIS" class="btn btn-danger" >
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
                elseif($_GET['id'] == 26){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h3>List of Appointments</h3>
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
                                                <th>Phone Number</th>
                                                <th>Appointment Date and Time</th>
                                                <th>Doctor</th>
                                                <th>Status</th>
                                                <th>Action To Perform</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $x=0;foreach($override->record('appointment','id') as $appointment){$patient=$override->get('patient','id',$appointment['patient_id']);$doctor=$override->get('staff','id',$appointment['doctor_id'])?>
                                                <tr>
                                                    <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></td>
                                                    <td><?=$patient[0]['phone_number']?></td>
                                                    <td><?=$appointment['appnt_date'].' , '.$appointment['appnt_time']?></td>
                                                    <td><?=$doctor[0]['firstname'].' '.$doctor[0]['lastname']?></td>
                                                    <td><?php if($appointment['status'] == 0){?><span class="label label-info">Pending</span><?php }else{?><span class="label label-success">Done</span><?php }?></td>
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
                                                                        <label class="col-md-1 control-label">Date </label>
                                                                        <div class="col-md-5">
                                                                            <input type="text" name="date" class="form-control datepicker" value="<?=$appointment['appnt_date']?>">
                                                                        </div>
                                                                        <label class="col-md-1 control-label">Time </label>
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
                                                <?php $x++;}?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 27){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h2>LENS REPORT FROM <i style="color: green;font-weight: bold"><?=$_GET['from']?></i> TO <i style="color: green;font-weight: bold"><?=$_GET['to']?></i> ( <?=$override->get('clinic_branch','id',$_GET['b'])[0]['name']?> )</h2>
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
                                                <th>Stock </th>
                                                <th>Price</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $x=0;$ttl=0;foreach($override->getRangeD('payment','checkup_date','branch_id',$_GET['b'],'pay_date',$_GET['from'],'pay_date',$_GET['to']) as $lens){
                                                $lens_pres=$override->getNoRepeat('lens_prescription','lens','checkup_date',$lens['checkup_date']);
                                                foreach($lens_pres as $lp){
                                                    $l_p=$override->get('lens_prescription','lens',$lp['lens']);$t=0;
                                                    foreach($l_p as $lpr){if($lpr['eye'] == 'BE'){$t +=2;}else{$t +=1;}}
                                                $p_lens=$override->get('lens_power','id',$lp['lens']);
                                                $group = $override->get('lens','id',$p_lens[0]['lens_id']);
                                                $type = $override->get('lens_type','id',$p_lens[0]['type_id']);
                                                    $ttl +=($t * $p_lens[0]['price']);
                                                $category = $override->get('lens_category','id',$p_lens[0]['cat_id']);?>
                                                <tr><?php $branchName=$override->get('clinic_branch','id',$_GET['b'])?>
                                                    <td><?=$group[0]['name']?></td>
                                                    <td><?=$type[0]['name']?></td>
                                                    <td><?=$category[0]['name']?></td>
                                                    <td><?=$p_lens[0]['lens_power']?></td>
                                                    <td><?=$t?></td>
                                                    <td><?=$p_lens[0]['quantity']?></td>
                                                    <td><?=number_format($p_lens[0]['price'] * $t)?></td>
                                                </tr>
                                                <?php }$x++;}if($lens){?>
                                                <tr>
                                                    <th>Total</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th><?=number_format($ttl)?></th>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 28){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h2>FRAMES REPORT FROM <i style="color: green;font-weight: bold"><?=$_GET['from']?></i> TO <i style="color: green;font-weight: bold"><?=$_GET['to']?></i> ( <?=$override->get('clinic_branch','id',$_GET['b'])[0]['name']?> )</h2>
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
                                                <th>Quantity</th>
                                                <th>Price</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $x=0;$ttl=0;foreach($override->getRangeD('frame_sold','sold_date','branch_id',$_GET['b'],'sold_date',$_GET['from'],'sold_date',$_GET['to']) as $fr){
                                                $frame_s=$override->getNoRepeat('frame_sold','size','sold_date',$fr['sold_date']);
                                                foreach($frame_s as $frames){
                                                    $frame=$override->get('frames','id',$frames['size']);$t=0;
                                                    foreach($override->get('frame_sold','size',$frames['size']) as $fr){$t +=1;}
                                                $model = $override->get('frame_model','id',$frame[0]['model']);
                                                $brand = $override->get('frame_brand','id',$frame[0]['brand_id']);
                                                    $ttl += ($t * $frame[0]['price']);
                                                $branchName=$override->get('clinic_branch','id',$_GET['b'])?>
                                                <tr>
                                                    <td><?=$brand[0]['name']?></td>
                                                    <td><?=$model[0]['model']?></td>
                                                    <td><?=$frame[0]['frame_size']?></td>
                                                    <td><?=$t?></td>
                                                    <td><?=number_format(($t * $frame[0]['price']))?></td>
                                                </tr>
                                                <?php }$x++;}if($fr){?>
                                                <tr>
                                                    <th>Total</th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th><?=number_format($ttl)?></th>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 29){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h2>PATIENT REPORT FROM <i style="color: green;font-weight: bold"><?=$_GET['from']?></i> TO <i style="color: green;font-weight: bold"><?=$_GET['to']?></i> ( <?=$override->get('clinic_branch','id',$_GET['b'])[0]['name']?> )</h2>
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
                                                <th>Sex</th>
                                                <th>Phone Number</th>
                                                <th>Test</th>
                                                <th>Diagnosis</th>
                                                <th>Medicine</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($override->getRange('checkup_record','branch_id',$_GET['b'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']) as $p_id){
                                                $patient=$override->get('patient','id',$p_id['patient_id']);?>
                                                <tr>
                                                    <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></td>
                                                    <td><?=$patient[0]['sex']?></td>
                                                    <td><?=$patient[0]['phone_number']?></td>
                                                    <td><?php foreach($override->getNews('test_performed','patient_id',$p_id['patient_id'],'date_performed',$p_id['checkup_date']) as $test_id){$test_name=$override->get('test_list','id',$test_id['test_id']);echo $test_name[0]['name'].' , ';}?></td>
                                                    <td><?php foreach($override->getNews('diagnosis_prescription','patient_id',$p_id['patient_id'],'checkup_id',$p_id['id']) as $d_id){$d_name=$override->get('diagnosis','id',$d_id['diagnosis_id']);echo $d_name[0]['name'].' , ';}?></td>
                                                    <td><?php foreach($override->getNews('prescription','patient_id',$p_id['patient_id'],'checkup_id',$p_id['id']) as $m_id){$m_name=$override->get('medicine','id',$m_id['medicine_id']);echo $m_name[0]['name'].' , ';}?></td>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 30){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><strong>List of Contract</strong></h3>
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
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Days Remain</th>
                                        <th>Status</th>
                                        <th>Description</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($override->get('contracts','category',$_GET['c']) as $empCon){
                                        $remainTime = $override->getDate(date('Y-m-d'),$empCon['end_date']);?>
                                        <tr>
                                            <td><?=$empCon['name']?></td>
                                            <td><?=$empCon['start_date']?></td>
                                            <td><?=$empCon['end_date']?></td>
                                            <td><?php if($remainTime[0]['endDate'] <=0){echo 0;}else{echo $remainTime[0]['endDate'];}?></td>
                                            <td><?php if($remainTime[0]['endDate'] <= 90 && $remainTime[0]['endDate'] > 30){?>
                                                    <span class="label label-info">About To Expire</span>
                                                <?php }elseif($remainTime[0]['endDate'] <= 30 && $remainTime[0]['endDate'] >= 1){?>
                                                    <span class="label label-warning">About To Expire</span>
                                                <?php }elseif($remainTime[0]['endDate'] <= 0){?>
                                                    <span class="label label-danger">Expired</span>
                                                <?php }?></td>
                                            <td><?=$empCon['description']?></td>
                                        </tr>
                                    <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 31){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <?php $doctor=$override->get('staff','id',$_GET['d'])?>
                                <h2>Dr. <?=$doctor[0]['firstname'].' '.$doctor[0]['lastname']?> Report from <i style="color: green;font-weight: bold"><?=$_GET['from']?></i> to <i style="color: green;font-weight: bold"><?=$_GET['to']?></i></h2>
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
                                                <th>Sex</th>
                                                <th>Phone Number</th>
                                                <th>Test</th>
                                                <th>Diagnosis</th>
                                                <th>Medicine</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($override->getRange('checkup_record','doctor_id',$_GET['d'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']) as $p_id){
                                                $patient=$override->get('patient','id',$p_id['patient_id']);?>
                                                <tr>
                                                    <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></td>
                                                    <td><?=$patient[0]['sex']?></td>
                                                    <td><?=$patient[0]['phone_number']?></td>
                                                    <td><?php foreach($override->getNews('test_performed','patient_id',$p_id['patient_id'],'date_performed',$p_id['checkup_date']) as $test_id){$test_name=$override->get('test_list','id',$test_id['test_id']);echo $test_name[0]['name'].' , ';}?></td>
                                                    <td><?php foreach($override->getNews('diagnosis_prescription','patient_id',$p_id['patient_id'],'checkup_id',$p_id['id']) as $d_id){$d_name=$override->get('diagnosis','id',$d_id['diagnosis_id']);echo $d_name[0]['name'].' , ';}?></td>
                                                    <td><?php foreach($override->getNews('prescription','patient_id',$p_id['patient_id'],'checkup_id',$p_id['id']) as $m_id){$m_name=$override->get('medicine','id',$m_id['medicine_id']);echo $m_name[0]['name'].' , ';}?></td>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 32){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h2><?=$_GET['ins']?> INSURANCE  REPORT FROM <i style="color: green;font-weight: bold"><?=$_GET['from']?></i> TO <i style="color: green;font-weight: bold"><?=$_GET['to']?></i></h2>
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
                                                <th>Sex</th>
                                                <th>Insurance ID</th>
                                                <th>Phone Number</th>
                                                <th>Test</th>
                                                <th>Diagnosis</th>
                                                <th>Medicine</th>
                                                <th>Doctor</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($override->getRange('payment','ins_id',$_GET['ins'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']) as $p_id){
                                                $patient=$override->get('patient','id',$p_id['patient_id']);$doctor=$override->get('staff','id',$p_id['doctor_id'])?>
                                                <tr>
                                                    <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></td>
                                                    <td><?=$patient[0]['sex']?></td>
                                                    <td><?=$p_id['ins_no']?></td>
                                                    <td><?=$patient[0]['phone_number']?></td>
                                                    <td><?php foreach($override->getNews('test_performed','patient_id',$p_id['patient_id'],'date_performed',$p_id['checkup_date']) as $test_id){$test_name=$override->get('test_list','id',$test_id['test_id']);echo $test_name[0]['name'].' , ';}?></td>
                                                    <td><?php foreach($override->getNews('diagnosis_prescription','patient_id',$p_id['patient_id'],'checkup_id',$p_id['id']) as $d_id){$d_name=$override->get('diagnosis','id',$d_id['diagnosis_id']);echo $d_name[0]['name'].' , ';}?></td>
                                                    <td><?php foreach($override->getNews('prescription','patient_id',$p_id['patient_id'],'checkup_id',$p_id['id']) as $m_id){$m_name=$override->get('medicine','id',$m_id['medicine_id']);echo $m_name[0]['name'].' , ';}?></td>
                                                    <td><?=$doctor[0]['firstname'].' '.$doctor[0]['lastname']?></td>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }
                elseif($_GET['id'] == 33){?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="panel-title-box">
                                <h2>CASH  REPORT FROM <i style="color: green;font-weight: bold"><?=$_GET['from']?></i> TO <i style="color: green;font-weight: bold"><?=$_GET['to']?></i></h2>
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
                                                <th>Sex</th>
                                                <th>Phone Number</th>
                                                <th>Test</th>
                                                <th>Diagnosis</th>
                                                <th>Medicine</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach($override->getRange2('payment','branch_id',$_GET['c'],'insurance',0,'checkup_date',$_GET['from'],'checkup_date',$_GET['to']) as $p_id){
                                                $patient=$override->get('patient','id',$p_id['patient_id']);$doctor=$override->get('staff','id',$p_id['doctor_id'])?>
                                                <tr>
                                                    <td><?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></td>
                                                    <td><?=$patient[0]['sex']?></td>
                                                    <td><?=$patient[0]['phone_number']?></td>
                                                    <td><?php foreach($override->getNews('test_performed','patient_id',$p_id['patient_id'],'date_performed',$p_id['checkup_date']) as $test_id){$test_name=$override->get('test_list','id',$test_id['test_id']);echo $test_name[0]['name'].' , ';}?></td>
                                                    <td><?php foreach($override->getNews('diagnosis_prescription','patient_id',$p_id['patient_id'],'checkup_id',$p_id['id']) as $d_id){$d_name=$override->get('diagnosis','id',$d_id['diagnosis_id']);echo $d_name[0]['name'].' , ';}?></td>
                                                    <td><?php foreach($override->getNews('prescription','patient_id',$p_id['patient_id'],'checkup_id',$p_id['id']) as $m_id){$m_name=$override->get('medicine','id',$m_id['medicine_id']);echo $m_name[0]['name'].' , ';}?></td>
                                                    <td><?=$doctor[0]['firstname'].' '.$doctor[0]['lastname']?></td>
                                                </tr>
                                            <?php }?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php }?>
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
<script type="text/javascript" src="js/plugins/scrolltotop/scrolltopcontrol.js"></script>

<script type="text/javascript" src="js/plugins/morris/raphael-min.js"></script>
<script type="text/javascript" src="js/plugins/morris/morris.min.js"></script>
<script type="text/javascript" src="js/plugins/rickshaw/d3.v3.js"></script>
<script type="text/javascript" src="js/plugins/rickshaw/rickshaw.min.js"></script>
<script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'></script>
<script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'></script>
<script type='text/javascript' src='js/plugins/bootstrap/bootstrap-datepicker.js'></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
<script type="text/javascript" src="js/plugins/owl/owl.carousel.min.js"></script>

<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-timepicker.min.js"></script>

<!---- Table Action -------->
<script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script>

<script type="text/javascript" src="js/plugins/moment.min.js"></script>
<script type="text/javascript" src="js/plugins/daterangepicker/daterangepicker.js"></script>
<!-- END THIS PAGE PLUGINS-->

<!-- START TEMPLATE -->
<script type="text/javascript" src="js/settings.js"></script>

<script type="text/javascript" src="js/plugins.js"></script>
<script type="text/javascript" src="js/actions.js"></script>

<script type="text/javascript" src="js/demo_dashboard.js"></script>
<!-- END TEMPLATE -->
<!-- END SCRIPTS -->
<script>
if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','../../../../www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-36783416-1', 'auto');
    ga('send', 'pageview');
</script>
<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter25836617 = new Ya.Metrika({
                    id:25836617,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "../../../../mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>

</body>

</html>