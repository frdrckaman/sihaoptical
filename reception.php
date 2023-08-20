<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
$total_orders=0;$pending=0;$confirmed=0;$received=0;$ins=0;
//$orders = $override->get('lens_orders','staff_id',$user->data()->id);
$getStatus = $override->getData('order_status');
if($user->isLoggedIn()){
    if($user->data()->access_level == 3){
        if(Input::exists('post')){
            if(Input::get('sendToDoctor') || Input::get('submit')){
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
                    'email_address' => array(
                        'unique' => 'patient'
                    ),
                    'phone_number' => array(
                        'required' => true,
						
                    ),
                   
                ));
                if ($validate->passed()) {
                    if(Input::get('health_insurance')){$ins=1;}
                    try {
                        $user->createRecord('patient', array(
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
                            'insurance' =>$ins,
                        ));
                        if(Input::get('sendToDoctor')){
                            $patient=$override->get('patient','phone_number',Input::get('phone_number'));
                            $user->createRecord('wait_list', array(
                                'arrive_on' => date('Y-M-d h:m'),
                                'patient_id' => $patient[0]['id'],
                                'staff_id' => $user->data()->id,
                                'branch_id'=>$user->data()->branch_id
                            ));
                            $successMessage = 'Patient registered successful and information have been sent to doctor <strong>PID : '.$patient[0]['id'].'</strong>';
                        }else{
                            $successMessage = 'Patient registered successful';
                        }
                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                } else {
                    $pageError = $validate->errors();
                }
            }
        }
    }
    else{Redirect::to('index.php');}
}else{Redirect::to('index.php');}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- META SECTION -->
    <title> Siha Optical | Reception Panel </title>
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
                <div id="new_patient">
                    <div class="x-content-title">
                        <h1>Patient Registration &nbsp;&nbsp;</h1>
                        <div class="pull-left">
                            <?php if(date('Y-m-d') == $user->data()->birthday){?>
                                <button class="btn btn-warning">The management and all staff of Siha Optical Eye Center wish you Happy Birthday</button>
                            <?php }?>
                        </div>
                        <div class="pull-right">
                            <?php $appointment=$override->getNews('appointment','branch_id',$user->data()->branch_id,'appnt_date',date('Y-m-d'));if($appointment){?>
                                <a href="info.php?id=18" class="btn btn-warning">Your Have Patient Appointment</a>
                            <?php }?>
                            <a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>" class="btn btn-default">REFRESH</a>
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
                                                <h3>Patient Details</h3>
                                                <h3>&nbsp;</h3>
                                                <form role="form" class="form-horizontal" method="post">
                                                    <div class="form-group">
                                                        <div class="col-md-3">
                                                            <input name="firstname" type="text" class="form-control" placeholder="FIRSTNAME" value="<?=Input::get('firstname')?>">
                                                        </div>
                                                        <label class="col-md-1"></label>
                                                        <div class="col-md-3">
                                                            <input name="surname" type="text" class="form-control" placeholder="SURNAME" value="<?=Input::get('surname')?>">
                                                        </div>
                                                        <label class="col-md-1"></label>
                                                        <div class="col-md-1">
                                                            <select name="sex" class="form-control select">
                                                                <option value="<?=Input::get('sex')?>"><?php if( Input::get('sex')){echo Input::get('sex')?><?php }else{?>SEX<?php }?></option>
                                                                <option value="Male">Male</option>
                                                                <option value="Female">Female</option>
                                                            </select>
                                                        </div>
                                                        <label class="col-md-1"></label>
                                                        <div class="col-md-2">
                                                            <input name="age" type="text"  class="form-control" placeholder="AGE" value="<?=Input::get('age')?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-3">
                                                            <select name="health_insurance" class="form-control select">
                                                                <option value="">Select Insurance</option>
                                                                <?php foreach($override->getData('insurance') as $insurance){?>
                                                                    <option value="<?=$insurance['name']?>"><?=$insurance['name']?></option>
                                                                <?php }?>
                                                            </select>
                                                        </div>
                                                        <label class="col-md-1"></label>
                                                        <div class="col-md-3">
                                                            <input name="dependent_no" type="text" class="form-control" placeholder="MEMBER No." value="<?=Input::get('dependent_no')?>">
                                                        </div>
                                                        <label class="col-md-1"></label>
                                                        <div class="col-md-4">
                                                            <input name="address" type="text" class="form-control" placeholder="ADDRESS" value="<?=Input::get('address')?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <input name="email_address" type="text" class="form-control" placeholder="EMAIL ADDRESS" value="<?=Input::get('email_address')?>">
                                                        </div>
                                                        <label class="col-md-2"></label>
                                                        <div class="col-md-5">
                                                            <input name="phone_number" type="text" class="form-control" placeholder="PHONE NUMBER" value="<?=Input::get('phone_number')?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <input name="occupation" type="text" class="form-control" placeholder="OCCUPATION" value="<?=Input::get('occupation')?>">
                                                        </div>
                                                        <label class="col-md-2"></label>
                                                        <div class="col-md-5">
                                                            <input name="arrive_date" type="text" class="form-control datepicker" placeholder="DATE" value="<?=Input::get('arrive_date')?>">
                                                        </div>
                                                    </div>
                                                    <div class="pull-right">
                                                        <input type="submit" name="submit" value="Submit" class="btn btn-success">
                                                        <input type="submit" name="sendToDoctor" value="Submit and Send to Doctor" class="btn btn-info">
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
            $.ajax({
                url:"process.php?content=eyes",
                method:"GET",
                data:{getEye:getEye},
                dataType:"text",
                success:function(data){
                    $('#other_eye').html(data);
                }
            });
        });
        $('#multiple').change(function(){
            var getMed = $(this).val();
            $.ajax({
                url:"process.php?content=multiple",
                method:"GET",
                data:{getMed:getMed},
                dataType:"text",
                success:function(data){
                    $('#other_med').html(data);
                }
            });
        });
        $('#single').change(function(){
            var getMed = $(this).val();
            $.ajax({
                url:"process.php?content=multiple",
                method:"GET",
                data:{getMed:getMed},
                dataType:"text",
                success:function(data){
                    $('#other_med').html(data);
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