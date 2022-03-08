<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
$total_orders=0;$pending=0;$confirmed=0;$received=0;
$orders = $override->get('lens_orders','staff_id',$user->data()->id);
$getStatus = $override->getData('order_status');
if($user->isLoggedIn()){
    if($user->data()->access_level == 2){
        if(Input::exists('post')){
            if($_GET['id'] == 1){
                if(Input::get('submit1')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'patient_name' => array(
                            'required' =>true,
                        ),
                    ));
                    if ($validate->passed()) {
                        try {
                            $user->createRecord('vision_therapy', array(
                                'history' => Input::get('history'),
                                'un_va_re' => Input::get('un_va_re'),
                                'un_va_le' => Input::get('un_va_le'),
                                'un_va_ph_re' => Input::get('un_va_ph_re'),
                                'un_va_ph_le' => Input::get('un_va_ph_le'),
                                'a_va_re' => Input::get('a_va_re'),
                                'a_va_le' => Input::get('a_va_le'),
                                'o_rx_re_sph' => Input::get('o_rx_re_sph'),
                                'o_rx_re_cyl' => Input::get('o_rx_re_cyl'),
                                'o_rx_re_axis' => Input::get('o_rx_re_axis'),
                                'o_rx_re_va' => Input::get('o_rx_re_va'),
                                'o_rx_re_add' => Input::get('o_rx_re_add'),
                                'o_rx_le_sph' => Input::get('o_rx_le_sph'),
                                'o_rx_le_cyl' => Input::get('o_rx_le_cyl'),
                                'o_rx_le_axis' => Input::get('o_rx_le_axis'),
                                'o_rx_le_va' => Input::get('o_rx_le_va'),
                                'o_rx_le_add' => Input::get('o_rx_le_add'),
                                'b_vision' => Input::get('b_vision'),
                                'w_floating' => Input::get('w_floating'),
                                'd_vision' => Input::get('d_vision'),
                                'eye_strain' => Input::get('eye_strain'),
                                'fatigue' => Input::get('fatigue'),
                                'poor_w_memory' => Input::get('poor_w_memory'),
                                'headache' => Input::get('headache'),
                                'tracking_words' => Input::get('tracking_words'),
                                'head_posture' => Input::get('head_posture'),
                                'close_1_eye' => Input::get('close_1_eye'),
                                'f_blinking' => Input::get('f_blinking'),
                                'skipping' => Input::get('skipping'),
                                'squiting' => Input::get('squiting'),
                                'use_finger' => Input::get('use_finger'),
                                'muscle' => Input::get('muscle'),
                                'd_re' => Input::get('d_re'),
                                'd_le' => Input::get('d_le'),
                                'n_re' => Input::get('n_re'),
                                'n_le' => Input::get('n_le'),
                                'npc' => Input::get('npc'),
                                'npa' => Input::get('npa'),
                                'm_rod_re' => Input::get('m_rod_re'),
                                'm_rod_le' => Input::get('m_rod_le'),
                                'test_for' => Input::get('test_for'),
                                'eye_focusing' => Input::get('eye_focusing'),
                                'depth_perception' => Input::get('depth_perception'),
                                'fussion' => Input::get('fussion'),
                                'eye_movement' => Input::get('eye_movement'),
                                'p_w_memory' => Input::get('p_w_memory'),
                                'ocular_posture' => Input::get('ocular_posture'),
                                'processing_speed' => Input::get('processing_speed'),
                                'diagnosis' => Input::get('diagnosis'),
                                'pencil_push' => Input::get('pencil_push'),
                                'brock_string' => Input::get('brock_string'),
                                'flipper' => Input::get('flipper'),
                                'prinsel_rule' => Input::get('prinsel_rule'),
                                'recommend' => Input::get('recommend'),
                                'min_t' => Input::get('min_t'),
                                'no_days' => Input::get('no_days'),
                                'nxt_appointment' => Input::get('nxt_appointment'),
                                'checkup_date' => date('Y-m-d'),
                                'patient_id' => Input::get('patient_name'),
                                'doctor_id' => $user->data()->id,
                                'branch_id' => $user->data()->branch_id
                            ));
                            $checkup_id =$override->getNews('vision_therapy','patient_id',Input::get('patient_name'),'checkup_date',date('Y-m-d'));
                            if(Input::get('diagnosis')){
                                foreach(Input::get('diagnosis') as $diagnosisP){
                                    $user->createRecord('diagnosis_prescription',array(
                                        'diagnosis_id' => $diagnosisP,
                                        'patient_id' => Input::get('patient_name'),
                                        'doctor_id' => $user->data()->id,
                                        'checkup_id' => $checkup_id[0]['id']
                                    ));
                                }
                            }
                            //$user->deleteRecord('wait_list', 'patient_id', Input::get('patient_name'));
                            $successMessage = 'Patient Information Successful Saved';
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
            elseif($_GET['id'] == 2){
                if(Input::get('submit2')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'patient_name' => array(
                            'required' =>true,
                        ),
                    ));
                    if ($validate->passed()) {
                        try {
                            $user->createRecord('cyclo_refraction', array(
                                'cyclo_15m' => Input::get('cyclo_15m'),
                                'trop_15' => Input::get('trop_15'),
                                'cyclo_15m2' => Input::get('cyclo_15m2'),
                                'd_oc_od_sph' => Input::get('d_oc_od_sph'),
                                'd_oc_od_cyl' => Input::get('d_oc_od_cyl'),
                                'd_oc_od_axis' => Input::get('d_oc_od_axis'),
                                'd_oc_od_va' => Input::get('d_oc_od_va'),
                                //'d_oc_od_add' => Input::get('d_oc_od_add'),
                                'd_oc_os_sph' => Input::get('d_oc_os_sph'),
                                'd_oc_os_cyl' => Input::get('d_oc_os_cyl'),
                                'd_oc_os_axis' => Input::get('d_oc_os_axis'),
                                'd_oc_os_va' => Input::get('d_oc_os_va'),
                                //'d_oc_os_add' => Input::get('d_oc_os_add'),
                                'w_oc_od_sph' => Input::get('w_oc_od_sph'),
                                'w_oc_od_cyl' => Input::get('w_oc_od_cyl'),
                                'w_oc_od_axis' => Input::get('w_oc_od_axis'),
                                'w_oc_od_va' => Input::get('w_oc_od_va'),
                                //'w_oc_od_add' => Input::get('w_oc_od_add'),
                                'w_oc_os_sph' => Input::get('w_oc_os_sph'),
                                'w_oc_os_cyl' => Input::get('w_oc_os_cyl'),
                                'w_oc_os_axis' => Input::get('w_oc_os_axis'),
                                'w_oc_os_va' => Input::get('w_oc_os_va'),
                                //'w_oc_os_add' => Input::get('w_oc_os_add'),
                                'p_reduce' => Input::get('p_reduce'),
                                'e_oc_od_sph' => Input::get('e_oc_od_sph'),
                                'e_oc_od_cyl' => Input::get('e_oc_od_cyl'),
                                'e_oc_od_axis' => Input::get('e_oc_od_axis'),
                                'e_oc_od_va' => Input::get('e_oc_od_va'),
                                //'e_oc_od_add' => Input::get('e_oc_od_add'),
                                'e_oc_os_sph' => Input::get('e_oc_os_sph'),
                                'e_oc_os_cyl' => Input::get('e_oc_os_cyl'),
                                'e_oc_os_axis' => Input::get('e_oc_os_axis'),
                                'e_oc_os_va' => Input::get('e_oc_os_va'),
                                //'e_oc_os_add' => Input::get('e_oc_os_add'),
                                'd_od' => Input::get('d_od'),
                                'd_os' => Input::get('d_os'),
                                'checkup_date' => date('Y-m-d'),
                                'patient_id' => Input::get('patient_name'),
                                'doctor_id' => $user->data()->id,
                                'branch_id' => $user->data()->branch_id
                            ));
                            $checkup_id =$override->getNews('checkup_record','patient_id',Input::get('patient_name'),'checkup_date',date('Y-m-d'));
                            if(Input::get('diagnosis')){
                                foreach(Input::get('diagnosis') as $diagnosisP){
                                    $user->createRecord('diagnosis_prescription',array(
                                        'diagnosis_id' => $diagnosisP,
                                        'patient_id' => Input::get('patient_name'),
                                        'doctor_id' => $user->data()->id,
                                        'checkup_id' => $checkup_id[0]['id']
                                    ));
                                }
                            }
                            //$user->deleteRecord('wait_list', 'patient_id', Input::get('patient_name'));
                            $successMessage = 'Patient Information Successful Saved';
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
        }
    }else{Redirect::to('index.php');}
}else{Redirect::to('index.php');}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- META SECTION -->
    <title> Siha Optical | Doctor Panel </title>
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
    <h1>Patient Examination &nbsp;&nbsp;</h1>
    <div class="pull-left">
        <?php if(date('Y-m-d') == $user->data()->birthday){?>
            <button class="btn btn-warning">The management and all staff of Siha Optical Eye Center wish you Happy Birthday</button>
        <?php }?>
    </div>
    <div class="pull-right">
        <?php $appointment=$override->getNews('appointment','doctor_id',$user->data()->id,'appnt_date',date('Y-m-d'));if($appointment){?>
            <a href="#" class="btn btn-warning">Your Have an Appointment</a>
        <?php }?>
        <a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>" class="btn btn-default">REFRESH </a>
        <button class="btn btn-default">TODAY: <?=date('d-M-Y')?></button>
    </div>
</div>
<div class="row stacked">
<div class="col-md-12">
<div class="x-chart-widget">

<div class="x-chart-widget-content">
<?php foreach($override->get('staff','branch_id',$user->data()->branch_id) as $bday){if($bday['birthday'] == date('Y-m-d') && !$user->data()->birthday == date('Y-m-d')){?>
    <div class="alert alert-warning" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <strong>Today is <?=$bday['firstname'].' '.$bday['middlename'].' '.$bday['lastname']?> Birthday&nbsp;</strong>
    </div>
<?php }}?>
<div class="x-chart-widget-content-head">
    <h4>PATIENT ON QUEUE : <?=$override->getCount('wait_list','branch_id',$user->data()->branch_id)?></h4>
</div>

<div class="col-md-offset-2 col-md-8">
<div class="panel panel-default">
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
<div class="panel-body">
<?php if($_GET['id'] == 1){?>
    <h3>Visual Therapy Evaluation</h3>
    <h3>&nbsp;</h3>
    <form role="form" class="form-horizontal" method="post">
    <div class="form-group">
        <label class="col-md-1 control-label">Patient:&nbsp;&nbsp;</label>
        <div class="col-md-11">
            <select name="patient_name" id="p" class="form-control select" data-live-search="true">
                <option value="">Select Patient</option>
                <?php foreach($override->get('wait_list','branch_id',$user->data()->branch_id) as $patient){$getPatient = $override->get('patient','id',$patient['patient_id'])?>
                    <option value="<?=$getPatient[0]['id']?>"><?=$getPatient[0]['firstname'].' '.$getPatient[0]['lastname'].' '.$getPatient[0]['phone_number']?></option>
                <?php }?>
            </select>
        </div>
    </div>
    <div id="p_details"></div>
    <div class="form-group">
        <label class="col-md-2 control-label">Patient History : &nbsp;</label>
        <div class="col-md-10">
            <textarea name="history" class="form-control" rows="5"></textarea>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-1 col-md-10">
            <h4>UNAIDED</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Right</th>
                        <th>Left</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>VISUAL ACUITY</strong></td>
                        <td><input name="un_va_re" type="text" class="form-control"/></td>
                        <td><input name="un_va_le" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td><strong>PH</strong></td>
                        <td><input name="un_va_ph_re" type="text" class="form-control"/></td>
                        <td><input name="un_va_ph_le" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-1 col-md-10">
            <h4>AIDED</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Right</th>
                        <th>Left</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>VISUAL ACUITY</strong></td>
                        <td><input name="a_va_re" type="text" class="form-control"/></td>
                        <td><input name="a_va_le" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-1 col-md-10">
            <label>OLD RX</label>
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
                        <td><input name="o_rx_re_sph" type="text" class="form-control"/></td>
                        <td><input name="o_rx_re_cyl" type="text" class="form-control"/></td>
                        <td><input name="o_rx_re_axis" type="text" class="form-control"/></td>
                        <td><input name="o_rx_re_va" type="text" class="form-control"/></td>
                        <td><input name="o_rx_re_add" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>Left</td>
                        <td><input name="o_rx_le_sph" type="text" class="form-control"/></td>
                        <td><input name="o_rx_le_cyl" type="text" class="form-control"/></td>
                        <td><input name="o_rx_le_axis" type="text" class="form-control"/></td>
                        <td><input name="o_rx_le_va" type="text" class="form-control"/></td>
                        <td><input name="o_rx_le_add" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-1 col-md-10">
            <h4>RELATIVE SYMPTOM FOR BINOCULAR VISION ASSESSMENT</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td>BLURRED VISION/FLUCTUATING VISION</td>
                        <td><input name="b_vision" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>WORDS FLOATING</td>
                        <td><input name="w_floating" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>DOUBLE VISION</td>
                        <td><input name="d_vision" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>EYE STRAIN</td>
                        <td><input name="eye_strain" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>FATIGUE</td>
                        <td><input name="fatigue" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>POOR WORKING MEMORY</td>
                        <td><input name="poor_w_memory" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>HEADACHE</td>
                        <td><input name="headache" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>TRACKING WORDS</td>
                        <td><input name="tracking_words" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-1 col-md-10">
            <h4>SOMETHING SEEN FROM A PATIENT DURING NEAR TASK</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td>HEAD POSTURE</td>
                        <td><input name="head_posture" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>CLOSING/COVERING ONE EYE</td>
                        <td><input name="close_1_eye" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>FREQUENT BLINKING</td>
                        <td><input name="f_blinking" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>SKIPPING / RE-READING WORD</td>
                        <td><input name="skipping" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>SQUINTING</td>
                        <td><input name="squiting" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>USING OF FINGER DURING READING</td>
                        <td><input name="use_finger" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <h4 class="col-md-offset-1">BINOCULAR VISION ASSESSMENT</h4>
        <label class="col-md-offset-1 col-md-1">H-TEST </label>
        <label class="col-md-1"></label>
        <label class="col-md-1">MUSCLE :  </label>
        <div class="col-md-4">
            <input type="text" name="muscle" class="form-control" placeholder="">
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-1 col-md-10">
            <h4>COVER TEST</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Right</th>
                        <th>Left</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>DISTANCE</strong></td>
                        <td><input name="d_re" type="text" class="form-control"/></td>
                        <td><input name="d_le" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Right</th>
                        <th>Left</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>NEAR</strong></td>
                        <td><input name="n_re" type="text" class="form-control"/></td>
                        <td><input name="n_le" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-offset-0 col-md-4 control-label">NEAR POINT OF CONVERGENCE(NPC): &nbsp;</label>
        <div class="col-md-3">
            <input type="text" name="npc" class="form-control" placeholder="">
        </div>
        <label class="control-label">&nbsp;CENTIMETERS</label>
    </div>
    <div class="form-group">
        <label class="col-md-offset-0 col-md-4 control-label">NEAR POINT OF ACCOMODATION(NPA): &nbsp;</label>
        <div class="col-md-3">
            <input type="text" name="npa" class="form-control" placeholder="">
        </div>
        <label class="control-label">&nbsp;DIOPTERS</label>
    </div>
    <div class="form-group">
        <label class="col-md-offset-1 col-md-2 control-label">MADDOX ROD(NEAR) &nbsp;</label>
        <label class="col-md-1"> </label>
        <label class="col-md-1 control-label">RE : </label>
        <div class="col-md-2">
            <input type="text" name="m_rod_re" class="form-control" placeholder="">
        </div>
        <label class="col-md-1 control-label">&nbsp;LE : </label>
        <div class="col-md-2">
            <input type="text" name="m_rod_le" class="form-control" placeholder="">
        </div>
    </div>
    <hr><br>
    <div class="form-group">
        <div class="col-md-offset-1 col-md-10">
            <h4>EVALUATION OF BINOCULAR VISION ASSESSMENT</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <td>TEST FOR</td>
                        <td>SIGNS / SYMPTOMS</td>
                        <td><input name="test_for" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>ACCOMMODATION(EYE FOCUSING SKILLS)</td>
                        <td>STRENGTH , FLEXIBILITY , ACCURACY </td>
                        <td><input name="eye_focusing" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>DEPTH PERCEPTION</td>
                        <td>HEADACHE / EYE STRAIN</td>
                        <td><input name="depth_perception" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>FUSSION</td>
                        <td>DOUBLE VISION</td>
                        <td><input name="fussion" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>OCULAR MOTILITY(EYE MOVEMENT)</td>
                        <td>S-A-F-E</td>
                        <td><input name="eye_movement" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>POOR WORKING MEMORY</td>
                        <td>BLURRED VISION/FLUCTUATING VISION</td>
                        <td><input name="p_w_memory" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>OCULAR POSTURE</td>
                        <td>TURNING LEFT/RIGHT</td>
                        <td><input name="ocular_posture" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>PROCESSING SPEED</td>
                        <td>HIGH/LOW</td>
                        <td><input name="processing_speed" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-offset-1 col-md-1 control-label">Diagnosis : </label>
        <div class="col-md-9">
            <select name="diagnosis[]" class="form-control select" multiple data-live-search="true" title="Diagnosis: ">
                <?php foreach($override->getData('diagnosis') as $diagnosis){?>
                    <option value="<?=$diagnosis['id']?>"><?=$diagnosis['name']?></option>
                <?php }?>
            </select>
        </div>
    </div>
    <hr><br>
    <h4>MANAGEMENT SELF TEST ASSESSMENT</h4>
    <div class="form-group">
        <label class="col-md-offset-1 col-md-2 control-label">PENCIL PUSH METHOD : </label>
        <div class="col-md-8">
            <input type="text" name="pencil_push" class="form-control">
        </div>
        <label class="col-md-12">&nbsp;</label>
        <label class="col-md-offset-1 col-md-2 control-label">BROCK STRING METHOD : </label>
        <div class="col-md-8">
            <input type="text" name="brock_string" class="form-control">
        </div>
        <label class="col-md-12">&nbsp;</label>
        <label class="col-md-offset-1 col-md-2 control-label">FLIPPER METHOD : </label>
        <div class="col-md-8">
            <input type="text" name="flipper" class="form-control">
        </div>
        <label class="col-md-12">&nbsp;</label>
        <label class="col-md-offset-1 col-md-2 control-label">PRINSEL RULE TECHNIQUE : </label>
        <div class="col-md-8">
            <input type="text" name="prinsel_rule" class="form-control">
        </div>
    </div>
    <hr><br>
    <div class="form-group">
        <label class="col-md-offset-1 col-md-2 label-control">I RECOMMEND FOR : </label>
        <div class="col-md-2">
            <input type="text" name="recommend" class="form-control">
        </div>
        <label class="col-md-1 label-control">MINUTES TIMES : </label>
        <div class="col-md-2">
            <input type="text" name="min_t" class="form-control">
        </div>
        <label class="col-md-1 label-control">EVERYDAY FOR : </label>
        <div class="col-md-1">
            <input type="text" name="no_days" class="form-control">
        </div>
        <label class="col-md-1 label-control">DAYS</label>
    </div>
    <hr><br>
    <div class="form-group">
        <label class="col-md-offset-1 col-md-3 label-control">NEXT APPOINTMENT DATE : </label>
        <div class="col-md-4">
            <input name="nxt_appointment" type="text" class="form-control datepicker" placeholder="DATE" value="">
        </div>
    </div>
    <div class="pull-right">
        <input type="submit" name="submit1" value="Submit" class="btn btn-success">
    </div>
    </form>
<?php }
elseif($_GET['id'] == 2){?>
    <h3>Cycloplegic Refraction</h3>
    <h3>&nbsp;</h3>
    <form role="form" class="form-horizontal" method="post">
    <div class="form-group">
        <label class="col-md-1 control-label">Patient:&nbsp;&nbsp;</label>
        <div class="col-md-11">
            <select name="patient_name" id="p" class="form-control select" data-live-search="true">
                <option value="">Select Patient</option>
                <?php foreach($override->get('wait_list','branch_id',$user->data()->branch_id) as $patient){$getPatient = $override->get('patient','id',$patient['patient_id'])?>
                    <option value="<?=$getPatient[0]['id']?>"><?=$getPatient[0]['firstname'].' '.$getPatient[0]['lastname'].' '.$getPatient[0]['phone_number']?></option>
                <?php }?>
            </select>
        </div>
    </div>
    <div id="p_details"></div>
    <br>
    <div class="form-group">
        <div class="col-md-offset-1 col-md-10">
            <h4>ADMINISTERED DRUG TIME </h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>CYCLOPENTOLATE</th>
                        <th>TROPICAMIDE</th>
                        <th>CYCLOPENTOLATE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>15MINUTES</td>
                        <td>15MINUTES</td>
                        <td>15MINUTES</td>
                    </tr>
                    <tr>
                        <td><input name="cyclo_15m" type="text" class="form-control"/></td>
                        <td><input name="trop_15" type="text" class="form-control"/></td>
                        <td><input name="cyclo_15m2" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-1 col-md-10">
            <label>DRY RENTIONSCOPE</label>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>OCULUS</th>
                        <th>SPHERE</th>
                        <th>CYL</th>
                        <th>AXIS</th>
                        <th>VA</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>OD</td>
                        <td><input name="d_oc_od_sph" type="text" class="form-control"/></td>
                        <td><input name="d_oc_od_cyl" type="text" class="form-control"/></td>
                        <td><input name="d_oc_od_axis" type="text" class="form-control"/></td>
                        <td><input name="d_oc_od_va" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>OS</td>
                        <td><input name="d_oc_os_sph" type="text" class="form-control"/></td>
                        <td><input name="d_oc_os_cyl" type="text" class="form-control"/></td>
                        <td><input name="d_oc_os_axis" type="text" class="form-control"/></td>
                        <td><input name="d_oc_os_va" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-offset-1 col-md-10">
            <label>WET RENTIONSCOPE</label>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>OCULUS</th>
                        <th>SPHERE</th>
                        <th>CYL</th>
                        <th>AXIS</th>
                        <th>VA</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>OD</td>
                        <td><input name="w_oc_od_sph" type="text" class="form-control"/></td>
                        <td><input name="w_oc_od_cyl" type="text" class="form-control"/></td>
                        <td><input name="w_oc_od_axis" type="text" class="form-control"/></td>
                        <td><input name="w_oc_od_va" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>OS</td>
                        <td><input name="w_oc_os_sph" type="text" class="form-control"/></td>
                        <td><input name="w_oc_os_cyl" type="text" class="form-control"/></td>
                        <td><input name="w_oc_os_axis" type="text" class="form-control"/></td>
                        <td><input name="w_oc_os_va" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group"><hr><br>
        <div class="col-md-offset-1 col-md-10">
            <h4>EFFECT OF ADMINISTERED DRUGS TO THE ACCOMMODATION</h4><br>
            <div class="form-group">
                <label class="label-control col-md-4">PRACTITIONER SHOULD REDUCE &nbsp;</label>
                <div class="col-md-2">
                    <input type="text" name="p_reduce" class="form-control">
                </div>
                <label class="label-control col-md-5">&nbsp;DIOPTERS FROM THE WET RENTINSCOPE</label>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>OCULUS</th>
                        <th>SPHERE</th>
                        <th>CYL</th>
                        <th>AXIS</th>
                        <th>VA</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>OD</td>
                        <td><input name="e_oc_od_sph" type="text" class="form-control"/></td>
                        <td><input name="e_oc_od_cyl" type="text" class="form-control"/></td>
                        <td><input name="e_oc_od_axis" type="text" class="form-control"/></td>
                        <td><input name="e_oc_od_va" type="text" class="form-control"/></td>
                    </tr>
                    <tr>
                        <td>OS</td>
                        <td><input name="e_oc_os_sph" type="text" class="form-control"/></td>
                        <td><input name="e_oc_os_cyl" type="text" class="form-control"/></td>
                        <td><input name="e_oc_os_axis" type="text" class="form-control"/></td>
                        <td><input name="e_oc_os_va" type="text" class="form-control"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
        <h4>DIAGNOSIS</h4>
        <label class="col-md-offset-0 col-md-1 control-label">OD:&nbsp;</label>
        <div class="col-md-3">
            <input type="text" name="d_od" class="form-control" placeholder="">
        </div>
        <label class="control-label col-md-1">&nbsp;OS</label>
        <div class="col-md-3">
            <input type="text" name="d_os" class="form-control" placeholder="">
        </div>
    </div>
    <hr><br>
        <div class="pull-right">
            <input type="submit" name="submit2" value="Submit" class="btn btn-success">
        </div>
    </form>
<?php }?>
</div>
</div>
</div>
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

<!-- THIS PAGE PLUGINS -->
<script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>

<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
<script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
<!-- END THIS PAGE PLUGINS -->

<!-- START TEMPLATE -->

<script type="text/javascript" src="js/plugins.js"></script>
<script type="text/javascript" src="js/actions.js"></script>
<!-- END TEMPLATE -->
<script>
    pageLoadingFrame("show");
    window.onload = function () {
        setTimeout(function(){
            pageLoadingFrame("hide");
        },100);
    }
</script>
<script>
    $(function(){
        //Spinner
        $(".spinner_default").spinner()
        $(".spinner_decimal").spinner({step: 0.01, numberFormat: "n"});
        //End spinner

        //Datepicker
        $('#dp-2').datepicker();
        $('#dp-3').datepicker({startView: 2});
        $('#dp-4').datepicker({startView: 1});
        //End Datepicker
    });
</script>
<script>
    $(document).ready(function(){
        $('#eye').change(function(){
            var getEye = $(this).val();
            $('#wait').show();
            $.ajax({
                url:"process.php?content=eyes",
                method:"GET",
                data:{getEye:getEye},
                dataType:"text",
                success:function(data){
                    $('#other_eye').html(data);
                    $('#wait').hide();
                }
            });

        });
        $('#multiple').change(function(){
            var getMed = $(this).val();
            $('#waitM').show();
            $.ajax({
                url:"process.php?content=multiple",
                method:"GET",
                data:{getMed:getMed},
                dataType:"text",
                success:function(data){
                    $('#other_med').html(data);
                    $('#waitM').hide();
                }
            });
        });
        $('#single').change(function(){
            var getMed = $(this).val();
            $('#waitM').show();
            $.ajax({
                url:"process.php?content=multiple",
                method:"GET",
                data:{getMed:getMed},
                dataType:"text",
                success:function(data){
                    $('#other_med').html(data);
                    $('#waitM').hide();
                }
            });
        });
        $('#lens_power').change(function(){
            var getCat = $(this).val();
            $.ajax({
                url:"process.php?content=power",
                method:"GET",
                data:{cat_id:getCat},
                dataType:"text",
                success:function(data){
                    $('#p').html(data);
                }
            });
        });
        $('#other_lens_power').change(function(){
            var getCat = $(this).val();
            $.ajax({
                url:"process.php?content=other_power",
                method:"GET",
                data:{cat_id:getCat},
                dataType:"text",
                success:function(data){
                    $('#op').html(data);
                }
            });
        });
        $('#p').change(function(){
            var getP = $(this).val();
            $.ajax({
                url:"process.php?content=p_details",
                method:"GET",
                data:{p_id:getP},
                dataType:"text",
                success:function(data){
                    $('#p_details').html(data);
                }
            });
        });
    });
</script>

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
<noscript><div><img src="https://mc.yandex.ru/watch/25836617" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>

</html>






