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
          if(Input::get('search_report')){
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
          else{
              $validate = new validate();
              $validate = $validate->check($_POST, array(
                  'patient_name' => array(
                      'required' =>true,
                  ),
                  'other_test' => array(

                  )
              ));
              if ($validate->passed()) {
                  try {
                      $user->createRecord('checkup_record', array(
                          'CC' => Input::get('cc'),
                          'OH' => Input::get('oh'),
                          'GH' => Input::get('gh'),
                          'FOH' => Input::get('foh'),
                          'FGH' => Input::get('fgh'),
                          'NPC' => Input::get('nfc'),
                          'EOM' => Input::get('eom'),
                          'pupils' => Input::get('pupils'),
                          'confrontation' => Input::get('confrontation'),
                          'vision' => '',
                          'V_RE' => Input::get('v_re'),
                          'V_LE' => Input::get('v_le'),
                          'PH_RE' => Input::get('ph_re'),
                          'PH_LE' => Input::get('ph_le'),
                          'UN_RE' => Input::get('un_re'),
                          'UN_LE' => Input::get('un_le'),
                          'PD' => Input::get('pd'),
                          'PH' => '',
                          'ref_OD_sphere' => Input::get('ref_od_sphere'),
                          'ref_cyl' => Input::get('ref_cyl'),
                          'ref_axis' => Input::get('ref_axis'),
                          'ref_va' => Input::get('ref_va'),
                          'ref_add' => Input::get('ref_add'),
                          'add_ref_OD_sphere' => Input::get('add_ref_od_sphere'),
                          'add_ref_cyl' => Input::get('add_ref_cyl'),
                          'add_ref_axis' => Input::get('add_ref_axis'),
                          'add_ref_va' => Input::get('add_ref_va'),
                          'add_ref_add' => Input::get('add_ref_add'),
                          'rx_OD_sphere' => Input::get('rx_od_sphere'),
                          'rx_cyl' => Input::get('rx_cyl'),
                          'rx_axis' => Input::get('rx_axis'),
                          'rx_va' => Input::get('rx_va'),
                          'rx_add' => Input::get('rx_add'),
                          'rx_va_2' => Input::get('rx_va_2'),
                          'add_rx_OS_sphere' => Input::get('add_rx_os_sphere'),
                          'add_rx_cyl' => Input::get('add_rx_cyl'),
                          'add_rx_axis' => Input::get('add_rx_axis'),
                          'add_rx_va' => Input::get('add_rx_va'),
                          'add_rx_add' => Input::get('add_rx_add'),
                          'add_rx_va_2' => Input::get('add_rx_va_2'),
                          'external_ocular_exam' => Input::get('ext_oc_exam'),
                          'IOP' => Input::get('iop'),
                          'IOP_RE' => Input::get('iop_re'),
                          'IOP_LE' => Input::get('iop_le'),
                          'IOP_time' => Input::get('iop_time'),
                          'IOP_POST_IOP' => '',
                          'IOP_POST_dilation' => Input::get('iop_post_dilation'),
                          'IOP_POST_RE' => Input::get('iop_post_re'),
                          'IOP_POST_LE' => Input::get('iop_post_le'),
                          'IOP_POST_time' => Input::get('iop_post_time'),
                          'mydriatic_agent_used' => Input::get('mydriatic_agent_used'),
                          'internal_exam' => Input::get('internal_exam'),
                          'f_od' => Input::get('f_od'),
                          'f_os' => Input::get('f_os'),
                          'f_vessels' => Input::get('f_vessels'),
                          'f_macula' => Input::get('f_macula'),
                          'f_retina' => Input::get('f_retina'),
                          'diagnosis' => '',
                          'management' => '',
                          'distance_glasses' => Input::get('distance_glasses'),
                          'reading_glasses' => Input::get('reading_glasses'),
                          'lens' => Input::get('lens'),
                          'other_note' => Input::get('other_note'),
                          'other_test' => '',
                          'checkup_date' => date('Y-m-d'),
                          'patient_id' => Input::get('patient_name'),
                          'doctor_id' => $user->data()->id,
                          'branch_id' => $user->data()->branch_id
                      ));
                      //get check up ID
                      $checkup_id =$override->getNews('checkup_record','patient_id',Input::get('patient_name'),'checkup_date',date('Y-m-d'));

                      $getMedicine = array(Input::get('medicine'),Input::get('other_medicine'),Input::get('other_medicine_1'),Input::get('other_medicine_2'));
                      $quantity = array(Input::get('quantity'),Input::get('other_quantity'),Input::get('other_quantity_1'),Input::get('other_quantity_2'));
                      $dosage = array(Input::get('dosage'),Input::get('other_dosage'),Input::get('other_dosage_1'),Input::get('other_dosage_2'));
                      $eyes = array(Input::get('eyes'),Input::get('other_eyes'),Input::get('other_eyes_1'),Input::get('other_eyes_2'));
                      $day= array(Input::get('day'),Input::get('other_day'),Input::get('other_day_1'),Input::get('other_day_2'));
                      $days= array(Input::get('days'),Input::get('other_days'),Input::get('other_days_1'),Input::get('other_days_2'));$f=0;
                      foreach($getMedicine as $getMed){
                          if($getMed == null){

                          }else{
                              $user->createRecord('prescription',array(
                                  'medicine_id' => $getMed,
                                  'quantity' => $quantity[$f],
                                  'dosage' => $dosage[$f],
                                  'eyes' => $eyes[$f],
                                  'no_day' => $day[$f],
                                  'days_group' => $days[$f],
                                  'given_date' => date('Y-m-d'),
                                  'patient_id' => Input::get('patient_name'),
                                  'doctor_id' => $user->data()->id,
                                  'branch_id' => $user->data()->branch_id,
                                  'checkup_id' => $checkup_id[0]['id']
                              ));
                          }$f++;
                      }
                      $totalTest=0;
                      $p=$override->get('patient','id',Input::get('patient_name'));
                      foreach(Input::get('other_test') as $test){
                          $user->createRecord('test_performed',array(
                              'test_id' => $test,
                              'date_performed'=>date('Y-m-d'),
                              'patient_id' => Input::get('patient_name'),
                              'doctor_id' => $user->data()->id,
                              'branch_id' => $user->data()->branch_id,
                              'checkup_id' => $checkup_id[0]['id']
                          ));
                          $testPerformed = $override->get('test_list','id',$test);
                          if($p[0]['health_insurance']){
                              $totalTest +=$testPerformed[0]['insurance_price'];
                          }else{
                              $totalTest +=$testPerformed[0]['cost'];
                          }
                      }

                      $user->createRecord('payment',array(
                          'checkup' => $testPerformed[0]['id'],
                          'checkup_date' => date('Y-m-d'),
                          'patient_id' => Input::get('patient_name'),
                          'doctor_id' => $user->data()->id,
                          'branch_id' => $user->data()->branch_id,
                          'checkup_id' => $checkup_id[0]['id'],
                      ));
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
                      $user->deleteRecord('wait_list', 'patient_id', Input::get('patient_name'));
                      $successMessage = 'Patient Information Successful Saved';
                  } catch (Exception $e) {
                      die($e->getMessage());
                  }
              }
              else {
                  $pageError = $validate->errors();
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
    <title> Siha Optical | Doctor Panel</title>
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
                                    <h3>Examination Details</h3>
                                    <h3>&nbsp;</h3>
                                    <form role="form" class="form-horizontal" method="post">
                                        <div class="form-group">
                                            <label class="col-md-1 control-label">Patient:&nbsp;&nbsp;</label>
                                            <div class="col-md-11">
                                                <select name="patient_name" id="p" class="form-control select" data-live-search="true">
                                                    <option value="">Select Patient</option>
                                                    <?php $x=1;foreach($override->getSort('wait_list','branch_id',$user->data()->branch_id,'id') as $patient){$getPatient = $override->get('patient','id',$patient['patient_id'])?>
                                                        <option value="<?=$getPatient[0]['id']?>"><?=$getPatient[0]['firstname'].' '.$getPatient[0]['lastname'].' '.$getPatient[0]['phone_number'].' ( '.$x.' )'?></option>
                                                    <?php $x++;}?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="p_details"></div>
                                        <div class="form-group">
                                            <label class="col-md-1 control-label">CC : &nbsp;</label>
                                            <div class="col-md-11">
                                                <textarea name="cc" class="form-control" rows="5"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="oh" type="text" class="form-control" placeholder="OH: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="gh" type="text" class="form-control" placeholder="GH: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="foh" type="text" class="form-control" placeholder="FOH: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="fgh" type="text" class="form-control" placeholder="FGH: ">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="nfc" type="text" class="form-control" placeholder="NPC: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="eom" type="text" class="form-control" placeholder="EOM: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="pupils" type="text" class="form-control" placeholder="Pupils: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="confrontation" type="text" class="form-control" placeholder="Confrontation: ">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-10">
                                                <label></label>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th></th>
                                                            <th></th>
                                                            <th>Right</th>
                                                            <th>Left</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td><strong>DISTANCE  V/A</strong></td>
                                                            <td>
                                                                <table class="table table-bordered">
                                                                    <tr>
                                                                        <td><strong>VISION</strong></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><strong>PH</strong></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td>
                                                                <table class="table table-bordered">
                                                                    <tr>
                                                                        <td><input name="v_re" type="text" class="form-control"/></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><input name="ph_re" type="text" class="form-control"/></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td>
                                                                <table class="table table-bordered">
                                                                    <tr>
                                                                        <td><input name="v_le" type="text" class="form-control"/></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><input name="ph_le" type="text" class="form-control"/></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>NEAR  V/A</strong></td>
                                                            <td><strong>UNAIDED  V/A</strong></td>
                                                            <td><input name="un_re" type="text" class="form-control"/></td>
                                                            <td><input name="un_le" type="text" class="form-control"/></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-1"></label>
                                            <div class="col-md-4">
                                                <input name="pd" type="text" class="form-control" placeholder="PD: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-10">
                                                <textarea name="ext_oc_exam" class="form-control" rows="5" placeholder="External Ocular Examination:"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-10">
                                                <label>Auto Ref</label>
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
                                                            <td><input name="ref_od_sphere" type="text" class="form-control"/></td>
                                                            <td><input name="ref_cyl" type="text" class="form-control"/></td>
                                                            <td><input name="ref_axis" type="text" class="form-control"/></td>
                                                            <td><input name="ref_va" type="text" class="form-control"/></td>
                                                            <td><input name="ref_add" type="text" class="form-control"/></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Left</td>
                                                            <td><input name="add_ref_od_sphere" type="text" class="form-control"/></td>
                                                            <td><input name="add_ref_cyl" type="text" class="form-control"/></td>
                                                            <td><input name="add_ref_axis" type="text" class="form-control"/></td>
                                                            <td><input name="add_ref_va" type="text" class="form-control"/></td>
                                                            <td><input name="add_ref_add" type="text" class="form-control"/></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-10">
                                                <label>RX</label>
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
                                                            <th>VA</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>Right</td>
                                                            <td><input name="rx_od_sphere" type="text" class="form-control"/></td>
                                                            <td><input name="rx_cyl" type="text" class="form-control"/></td>
                                                            <td><input name="rx_axis" type="text" class="form-control"/></td>
                                                            <td><input name="rx_va" type="text" class="form-control"/></td>
                                                            <td><input name="rx_add" type="text" class="form-control"/></td>
                                                            <td><input name="rx_va_2" type="text" class="form-control"/></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Left</td>
                                                            <td><input name="add_rx_os_sphere" type="text" class="form-control"/></td>
                                                            <td><input name="add_rx_cyl" type="text" class="form-control"/></td>
                                                            <td><input name="add_rx_axis" type="text" class="form-control"/></td>
                                                            <td><input name="add_rx_va" type="text" class="form-control"/></td>
                                                            <td><input name="add_rx_add" type="text" class="form-control"/></td>
                                                            <td><input name="add_rx_va_2" type="text" class="form-control"/></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="iop" type="text" class="form-control" placeholder="IOP: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="iop_re" type="text" class="form-control" placeholder="RE: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="iop_le" type="text" class="form-control" placeholder="LE: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="iop_time" type="text" class="form-control" placeholder="Time: ">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="iop_post_dilation" type="text" class="form-control" placeholder="IOP:POST Dilation ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="iop_post_re" type="text" class="form-control" placeholder="RE: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="iop_post_le" type="text" class="form-control" placeholder="LE: ">
                                            </div>
                                            <label class="col-md-1"></label>
                                            <div class="col-md-2">
                                                <input name="iop_post_time" type="text" class="form-control" placeholder="Time: ">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-10">
                                                <input type="text" name="mydriatic_agent_used" class="form-control" placeholder="Mydriatic Agent used:">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-10">
                                                <textarea name="internal_exam" class="form-control" rows="5" placeholder="Internal Examination Dilated/Undilated:"></textarea>
                                            </div>
                                        </div>
                                        <!-- Start Fundus -->
                                        <hr><h2>FUNDUS</h2>
                                        <div class="form-group">
                                            <label class="col-md-1 control-label">CD Ration:&nbsp;&nbsp;</label>
                                            <div class="col-md-offset-0 col-md-5">
                                                <input type="text" name="f_od" class="form-control" placeholder="OD:">
                                            </div>
                                            <label class="col-md-1 control-label"></label>
                                            <div class="col-md-offset-0 col-md-5">
                                                <input type="text" name="f_os" class="form-control" placeholder="OS:">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-1 control-label">Vessels:&nbsp;&nbsp;</label>
                                            <div class="col-md-offset-0 col-md-10">
                                                <input type="text" name="f_vessels" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-1 control-label">Macula:&nbsp;&nbsp;</label>
                                            <div class="col-md-offset-0 col-md-10">
                                                <input type="text" name="f_macula" class="form-control" placeholder="">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-1 control-label">Peripheral retina:&nbsp;&nbsp;</label>
                                            <div class="col-md-offset-0 col-md-10">
                                                <textarea name="f_retina" class="form-control" rows="5" placeholder=""></textarea>
                                            </div>
                                        </div>
                                        <hr>
                                        <!-- End Fundus -->
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-10">
                                                <select name="diagnosis[]" class="form-control select" multiple data-live-search="true" title="Diagnosis: ">
                                                    <?php foreach($override->getData('diagnosis') as $diagnosis){?>
                                                        <option value="<?=$diagnosis['id']?>"><?=$diagnosis['name']?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-2">
                                                <label class="check"><input name="distance_glasses" type="checkbox" value="Distance Glasses" class="icheckbox"/> Distance Glasses</label>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="check"><input name="reading_glasses" type="checkbox" value="Reading Glasses" class="icheckbox"/> Reading Glasses</label>
                                            </div>
                                            <label class="col-md-12"></label><label class="col-md-12"></label>
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-10">
                                                <select name="other_test[]" multiple class="form-control select" data-live-search="true" title="Procedures Performed" required="">
                                                    <?php foreach($override->get('test_list','branch_id',$user->data()->branch_id) as $test){?>
                                                        <option value="<?=$test['id']?>"><?=$test['name']?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-10">
                                                <textarea name="other_note" class="form-control" rows="5" placeholder="Other Note:"></textarea>
                                            </div>
                                        </div><hr>
                                        <h4><strong>Medicine Prescription</strong></h4>
                                        <div class="form-group">
                                            <div class="col-md-offset-1 col-md-4">
                                                <label class="check"><input type="radio" class="" name="get_med" id="single" value="single" checked/> Single</label>
                                            </div>
                                            <div class="col-md-4 col-md-offset-0">
                                                <label class="check"><input type="radio" class="" name="get_med" id="multiple" value="multiple"/> Multiple</label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-offset-0 col-md-3">
                                                <select name="medicine" class="form-control select" data-live-search="true">
                                                    <option value="">Select Medicine</option>
                                                    <?php foreach($override->getMedicine('medicine','quantity') as $medicine){?>
                                                        <option value="<?=$medicine['id']?>"><?=$medicine['name']?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select name="quantity" class="form-control select" >
                                                    <option value="">Quantity</option>
                                                    <?php $x=1;while($x < 10){?>
                                                        <option value="<?=$x?>"><?=$x?></option>
                                                        <?php $x++;}?>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select name="dosage" class="form-control select" >
                                                    <option value="">FREQUENCY</option>
                                                    <option value="OD 1 times a day">OD 1 times a day</option>
                                                    <option value="BID 2 times a day">BID 2 times a day</option>
                                                    <option value="TID 3 times a day">TID 3 times a day</option>
                                                    <option value="QID 4times a day">QID 4times a day</option>
                                                    <option value="1 hourly">1 hourly</option>
                                                    <option value="2 after two hours">2 after two hours</option>
                                                    <option value="3 after three hours">3 after three hours</option>
                                                    <option value="4 after four hours">4 after four hours</option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <select name="eyes" class="form-control select" >
                                                    <option value="BOTH EYES">BOTH EYES</option>
                                                    <option value="RIGHT EYES">RIGHT EYES</option>
                                                    <option value="LEFT EYES">LEFT EYES</option>
                                                    <option value="ORAL">ORAL</option>
                                                    <option value="TROPICAL APPLICATION">TROPICAL APPLICATION</option>
                                                    <option value="APPLIED">APPLIED</option>
                                                </select>
                                            </div>
                                            <div class="col-md-1">
                                                <input type="number" name="day" class="form-control" placeholder="No.">
                                            </div>
                                            <div class="col-md-2">
                                                <select name="days" class="form-control select" >
                                                    <option value="DAY">DAYS</option>
                                                    <option value="WEEK">WEEK</option>
                                                    <option value="MONTH">MONTH</option>
                                                    <option value="YEAR">YEAR</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="waitM" style="display:none;" class="col-md-offset-5 col-md-1"><img src='img/owl/AjaxLoader.gif' width="32" height="32" /><br>Loading..</div>
                                        <div id="other_med"></div><br><br>
                                        <div class="pull-right">
                                            <input type="submit" name="addOrder" value="Submit" class="btn btn-success">
                                        </div>
                                    </form>
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
if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
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