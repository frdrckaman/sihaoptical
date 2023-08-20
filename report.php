<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
$header=null;$tLens=0;$tPatient=0;$tFrame=0;$tL=0;$iPayment=0;$cPayment=0;$tPayment=0;$tPay=0;
$tDis=0;
if($user->isLoggedIn()){
    if($user->data()->access_level == 1) {
        switch($_GET['id']){
            case 1:
                $header='CLINIC';
                break;
            case 2:
                $header='DOCTOR';
                break;
            case 3:
                $header='INSURANCE';
                break;
            case 4:
                $header='CASH';
                break;
        }
        if(Input::exists('post')){
            if( Input::get('search_report')){
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
                        $redirect = 'report.php?id=1&b='.Input::get('clinic_branch').'&cat='.Input::get('category').'&from='.Input::get('from').'&to='.Input::get('to');
                    }elseif(Input::get('category') == 2){
                        $redirect = 'report.php?id=2&b='.Input::get('clinic_branch').'&cat='.Input::get('category').'&from='.Input::get('from').'&to='.Input::get('to');
                    }elseif(Input::get('category') == 3){
                        $redirect = 'report.php?id=3&b='.Input::get('clinic_branch').'&cat='.Input::get('category').'&from='.Input::get('from').'&to='.Input::get('to');
                    }elseif(Input::get('category') == 4){
                        $redirect = 'report.php?id=4&b='.Input::get('clinic_branch').'&cat='.Input::get('category').'&from='.Input::get('from').'&to='.Input::get('to');
                    }
                    Redirect::to($redirect);
                } else {
                    $pageError = $validate->errors();
                }
            }
        }
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
    <title> Siha Optical | Reports </title>
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
    <li>
        <?php if(!$_GET['id'] == 0){?>
            <a href="#">
                <div class="push-down-10 pull-left">
                    <button class="btn btn-info btn-rounded btn-condensed btn-sm" onclick="window.print()"><span class="fa fa-print"></span> Print</button>
                    <a href="#modal" class="btn btn-info btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-search"></span> Search</a>
                </div>
            </a>
        <?php }?>
    </li>
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
<?php if($_GET['id'] == 0){?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title-box">
                <h3>REPORTS </h3>
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
            <div class="panel-body">
                <h3>Report Details</h3>
                <form class="form-horizontal" role="form" method="post">
                    <div class="form-group">
                        <label class="col-md-1 control-label">Clinic</label>
                        <div class="col-md-2">
                            <select name="clinic_branch" class="form-control select" data-live-search="true">
                                <option value="">Select Clinic</option>
                                <option value="a">All</option>
                                <?php foreach($override->getData('clinic_branch') as $branch){?>
                                    <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <label class="col-md-1 control-label">Report &nbsp;</label>
                        <div class="col-md-2">
                            <select name="category" class="form-control select" data-live-search="true">
                                <option value="">Select Category</option>
                                <option value="1">Clinic</option>
                                <option value="2">Doctor</option>
                                <option value="3">Insurance</option>
                                <option value="4">Cash</option>
                            </select>
                        </div>
                        <label class="col-md-1 control-label">From &nbsp;</label>
                        <div class="col-md-2">
                            <input name="from" type="text" class="form-control datepicker" placeholder="START DATE">
                        </div>
                        <label class="col-md-1 control-label">To &nbsp;</label>
                        <div class="col-md-2">
                            <input name="to" type="text" class="form-control datepicker" placeholder="END DATE">
                        </div>
                    </div>
                    <div class="pull-right">
                        <input type="submit"  name="search_report" value="Search" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php }
elseif($_GET['id'] == 1){if($_GET['b'] == 'a'){$clinics=$override->getData('clinic_branch');}else{$clinics=$override->get('clinic_branch','id',$_GET['b']);}?>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1><?=$header?> REPORT<strong><span class="pull-right"><img src="img/famly%20eye%20care.png"></span></strong></h1>
                        <br><br><p class="pull-right"><strong>P.O.BOX 15 Msasani,DSM, TEL : 0713 870855 / 0784 728583 / 0757 977542</strong></p>
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
                                            <td class="text-center"><a href="information.php?id=29&from=<?=$_GET['from']?>&to=<?=$_GET['to']?>&b=<?=$clinic['id']?>"><?=$p=$override->countRange('checkup_record','branch_id',$clinic['id'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']);$tPatient +=$p?></a></td>
                                            <td class="text-center"><a href="information.php?id=27&from=<?=$_GET['from']?>&to=<?=$_GET['to']?>&b=<?=$clinic['id']?>"> <?php foreach($override->getRange('lens_prescription','branch_id',$clinic['id'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']) as $no_lens){if($no_lens['eye'] == 'BE'){$tLens +=2;}else{$tLens +=1;}}echo$tLens;$tL +=$tLens;$tLens=0;?></a></td>
                                            <td class="text-center"><a href="information.php?id=28&from=<?=$_GET['from']?>&to=<?=$_GET['to']?>&b=<?=$clinic['id']?>"><?=$f=$override->countRange('frame_sold','branch_id',$clinic['id'],'sold_date',$_GET['from'],'sold_date',$_GET['to']);$tFrame +=$f?></a></td>
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
                            <div class="row">
                                <div class="col-md-6 pull-right">
                                    <h4>Payment Details</h4>
                                    <?php if($_GET['b'] == 'a'){$payments=$override->range('payment','pay_date',$_GET['from'],'pay_date',$_GET['to']);
                                    }else{$payments=$override->getRange('payment','branch_id',$_GET['b'],'pay_date',$_GET['from'],'pay_date',$_GET['to']);}
                                       foreach($payments as $payment){if($payment['insurance_pay']){
                                           $iPayment +=$payment['insurance_pay'];$cPayment +=$payment['payment'] - $payment['insurance_pay'];}else{$cPayment +=$payment['payment'];}$tPayment +=$payment['cost'];$tPay +=$payment['payment'];$tDis +=$payment['discount'];}?>
                                    <table class="table table-striped">
                                        <tr>
                                            <td width="200"><strong>Cash:</strong></td><td class="text-right"><?=number_format($cPayment)?></td>
                                        </tr>
                                        <tr>
                                            <td width="200"><strong>Insurance:</strong></td><td class="text-right"><?=number_format($iPayment)?></td>
                                        </tr>
                                        <tr>
                                            <td width="200"><strong>Pending:</strong></td><td class="text-right"><?=number_format($tPayment - ($tPay+$tDis))?></td>
                                        </tr>
                                        <tr>
                                            <td width="200"><strong>Discount:</strong></td><td class="text-right"><?=number_format($tDis)?></td>
                                        </tr>
                                        <tr class="total">
                                            <td>Total Amount:</td><td class="text-right"><?=number_format($tPayment)?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- END INVOICE  START OF MODAL-->
                        <div class="modal" id="modal" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form class="form-horizontal" role="form" method="post">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <h4 class="modal-title" id="largeModalHead">Search Report</h4>
                                    </div>
                                    <div class="modal-body">
                                            <div class="form-group">
                                                <label class="col-md-1 control-label">Clinic</label>
                                                <div class="col-md-2">
                                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                                        <option value="">Select Clinic</option>
                                                        <option value="a">All</option>
                                                        <?php foreach($override->getData('clinic_branch') as $branch){?>
                                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <label class="col-md-1 control-label">Report &nbsp;</label>
                                                <div class="col-md-2">
                                                    <select name="category" class="form-control select" data-live-search="true">
                                                        <option value="">Select Category</option>
                                                        <option value="1">Clinic</option>
                                                        <option value="2">Doctor</option>
                                                        <option value="3">Insurance</option>
                                                        <option value="4">Cash</option>
                                                    </select>
                                                </div>
                                                <label class="col-md-1 control-label">From &nbsp;</label>
                                                <div class="col-md-2">
                                                    <input name="from" type="text" class="form-control datepicker" placeholder="START DATE">
                                                </div>
                                                <label class="col-md-1 control-label">To &nbsp;</label>
                                                <div class="col-md-2">
                                                    <input name="to" type="text" class="form-control datepicker" placeholder="END DATE">
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="submit"  name="search_report" value="Search" class="btn btn-success">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END OF MODAL -->
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php }
elseif($_GET['id'] == 2){if($_GET['b'] == 'a'){$clinics=$override->getData('clinic_branch');}else{$clinics=$override->get('clinic_branch','id',$_GET['b']);}?>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1><?=$header?> REPORT<strong><span class="pull-right"><img src="img/famly%20eye%20care.png"></span></strong></h1>
                        <br><br><p class="pull-right"><strong>P.O.BOX 5054 MWANZA, TEL : +255 785 360 107 , +255 743 501 700</strong></p>
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
                                        <th class="text-center">NAME</th>
                                        <th class="text-center">CLINIC</th>
                                        <th class="text-center">PATIENTS</th>
                                    </tr>
                                    <?php foreach($clinics as $clinic){$doctors=$override->getNoRepeat('checkup_record','doctor_id','branch_id',$clinic['id']);
                                            foreach($doctors as $doctor){$name=$override->get('staff','id',$doctor['doctor_id']);
                                                $patients = $override->countRange('checkup_record','doctor_id',$doctor['doctor_id'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']);
                                                ?>
                                                <tr>
                                                    <td class="text-center"><strong><?=$name[0]['firstname'].' '.$name[0]['middlename'].' '.$name[0]['lastname']?></strong></td>
                                                    <td class="text-center"><?=$clinic['name']?></td>
                                                    <td class="text-center"><strong><a href="information.php?id=31&from=<?=$_GET['from']?>&to=<?=$_GET['to']?>&d=<?=$name[0]['id']?>"><?=$patients?></a></strong></td>
                                                </tr>
                                    <?php }}?>
                                </table>
                            </div>
                        </div>
                        <!-- END INVOICE START OF MODAL-->
                        <div class="modal" id="modal" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form class="form-horizontal" role="form" method="post">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title" id="largeModalHead">Search Report</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="col-md-1 control-label">Clinic</label>
                                                <div class="col-md-2">
                                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                                        <option value="">Select Clinic</option>
                                                        <option value="a">All</option>
                                                        <?php foreach($override->getData('clinic_branch') as $branch){?>
                                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <label class="col-md-1 control-label">Report &nbsp;</label>
                                                <div class="col-md-2">
                                                    <select name="category" class="form-control select" data-live-search="true">
                                                        <option value="">Select Category</option>
                                                        <option value="1">Clinic</option>
                                                        <option value="2">Doctor</option>
                                                        <option value="3">Insurance</option>
                                                        <option value="4">Cash</option>
                                                    </select>
                                                </div>
                                                <label class="col-md-1 control-label">From &nbsp;</label>
                                                <div class="col-md-2">
                                                    <input name="from" type="text" class="form-control datepicker" placeholder="START DATE">
                                                </div>
                                                <label class="col-md-1 control-label">To &nbsp;</label>
                                                <div class="col-md-2">
                                                    <input name="to" type="text" class="form-control datepicker" placeholder="END DATE">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="submit"  name="search_report" value="Search" class="btn btn-success">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END OF MODAL-->
                    </div>
                </div>

            </div>
        </div>

    </div>
<?php }
elseif($_GET['id'] == 3){if($_GET['b'] == 'a'){$clinics=$override->getData('clinic_branch');}else{$clinics=$override->get('clinic_branch','id',$_GET['b']);}?>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1><?=$header?> REPORT<strong><span class="pull-right"><img src="img/famly%20eye%20care.png"></span></strong></h1>
                        <br><br><p class="pull-right"><strong>P.O.BOX 15 Msasani,DSM, TEL : 0713 870855 / 0784 728583 / 0757 977542</strong></p>
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
                                        <th class="text-center">NAME</th>
                                        <th class="text-center">CLINIC</th>
                                        <th class="text-center">PATIENTS</th>
                                        <th class="text-center">AMOUNT</th>
                                    </tr>
                                    <?php $t_p=0;$t_c=0;foreach($clinics as $clinic){
                                        $insurance=$override->getNewsNoRepeat('payment','ins_id','branch_id',$clinic['id'],'insurance',1);
                                        foreach($insurance as $ins){$name=$override->get('insurance','name',$ins['ins_id']);
                                            $patients = $override->countRang('payment','ins_id',$ins['ins_id'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']);$t_p+=$patients;
                                            $sum=$override->getSum('payment','insurance_pay','ins_id',$ins['ins_id'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']);$t_c+=$sum[0]['SUM(insurance_pay)']?>
                                            <tr>
                                                <td class="text-center"><strong><?=$name[0]['name']?></strong></td>
                                                <td class="text-center"><strong><?=$clinic['name']?></strong></td>
                                                <td class="text-center"><strong><a href="information.php?id=32&from=<?=$_GET['from']?>&to=<?=$_GET['to']?>&ins=<?=$ins['ins_id']?>"><?=$patients?></a></strong></td>
                                                <td class="text-center"><strong><?=number_format($sum[0]['SUM(insurance_pay)'])?></strong></td>
                                            </tr>
                                        <?php }}?>
                                    <tr>
                                        <th class="text-center">TOTAL</th>
                                        <th class="text-center"></th>
                                        <th class="text-center"><?=number_format($t_p)?></th>
                                        <th class="text-center"><?=number_format($t_c)?></th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- END INVOICE START OF MODAL-->
                        <div class="modal" id="modal" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form class="form-horizontal" role="form" method="post">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title" id="largeModalHead">Search Report</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="col-md-1 control-label">Clinic</label>
                                                <div class="col-md-2">
                                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                                        <option value="">Select Clinic</option>
                                                        <option value="a">All</option>
                                                        <?php foreach($override->getData('clinic_branch') as $branch){?>
                                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <label class="col-md-1 control-label">Report &nbsp;</label>
                                                <div class="col-md-2">
                                                    <select name="category" class="form-control select" data-live-search="true">
                                                        <option value="">Select Category</option>
                                                        <option value="1">Clinic</option>
                                                        <option value="2">Doctor</option>
                                                        <option value="3">Insurance</option>
                                                        <option value="4">Cash</option>
                                                    </select>
                                                </div>
                                                <label class="col-md-1 control-label">From &nbsp;</label>
                                                <div class="col-md-2">
                                                    <input name="from" type="text" class="form-control datepicker" placeholder="START DATE">
                                                </div>
                                                <label class="col-md-1 control-label">To &nbsp;</label>
                                                <div class="col-md-2">
                                                    <input name="to" type="text" class="form-control datepicker" placeholder="END DATE">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="submit"  name="search_report" value="Search" class="btn btn-success">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END OF MODAL-->
                    </div>
                </div>

            </div>
        </div>

    </div>
<?php }
elseif($_GET['id'] == 4){if($_GET['b'] == 'a'){$clinics=$override->getData('clinic_branch');}else{$clinics=$override->get('clinic_branch','id',$_GET['b']);}?>
    <div class="page-content-wrap">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h1><?=$header?> REPORT<strong><span class="pull-right"><img src="img/famly%20eye%20care.png"></span></strong></h1>
                        <br><br><p class="pull-right"><strong>P.O.BOX 15 Msasani,DSM, TEL : 0713 870855 / 0784 728583 / 0757 977542</strong></p>
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
                                        <th class="text-center">CLINIC</th>
                                        <th class="text-center">PATIENTS</th>
                                        <th class="text-center">AMOUNT</th>
                                    </tr>
                                    <?php $t_p=0;$t_c=0;foreach($clinics as $clinic){
                                        $cash=$override->getNews('payment','branch_id',$clinic['id'],'insurance',0);
                                            $patients = $override->countRange2('payment','insurance',0,'branch_id',$clinic['id'],'checkup_date',$_GET['from'],'checkup_date',$_GET['to']);$t_p+=$patients;
                                            $sum=$override->getSum2('payment','payment','branch_id',$clinic['id'],'insurance',0,'checkup_date',$_GET['from'],'checkup_date',$_GET['to']);$t_c+=$sum[0]['SUM(payment)']?>
                                            <tr>
                                                <td class="text-center"><strong><?=$clinic['name']?></strong></td>
                                                <td class="text-center"><strong><a href="information.php?id=33&from=<?=$_GET['from']?>&to=<?=$_GET['to']?>&c=<?=$clinic['id']?>"><?=$patients?></a></strong></td>
                                                <td class="text-center"><strong><?=number_format($sum[0]['SUM(payment)'])?></strong></td>
                                            </tr>
                                        <?php $patients=0;$sum=0;}?>
                                    <tr>
                                        <th class="text-center">TOTAL</th>
                                        <th class="text-center"><?=number_format($t_p)?></th>
                                        <th class="text-center"><?=number_format($t_c)?></th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <!-- END INVOICE START OF MODAL-->
                        <div class="modal" id="modal" tabindex="-1" role="dialog" aria-labelledby="largeModalHead" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form class="form-horizontal" role="form" method="post">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <h4 class="modal-title" id="largeModalHead">Search Report</h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label class="col-md-1 control-label">Clinic</label>
                                                <div class="col-md-2">
                                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                                        <option value="">Select Clinic</option>
                                                        <option value="a">All</option>
                                                        <?php foreach($override->getData('clinic_branch') as $branch){?>
                                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                                        <?php }?>
                                                    </select>
                                                </div>
                                                <label class="col-md-1 control-label">Report &nbsp;</label>
                                                <div class="col-md-2">
                                                    <select name="category" class="form-control select" data-live-search="true">
                                                        <option value="">Select Category</option>
                                                        <option value="1">Clinic</option>
                                                        <option value="2">Doctor</option>
                                                        <option value="3">Insurance</option>
                                                        <option value="4">Cash</option>
                                                    </select>
                                                </div>
                                                <label class="col-md-1 control-label">From &nbsp;</label>
                                                <div class="col-md-2">
                                                    <input name="from" type="text" class="form-control datepicker" placeholder="START DATE">
                                                </div>
                                                <label class="col-md-1 control-label">To &nbsp;</label>
                                                <div class="col-md-2">
                                                    <input name="to" type="text" class="form-control datepicker" placeholder="END DATE">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <input type="submit"  name="search_report" value="Search" class="btn btn-success">
                                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- END OF MODAL-->
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