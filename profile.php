<?php
require_once'php/core/init.php';
$user = new User();
$random = new Random();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
$total_orders=0;$pending=0;$confirmed=0;$received=0;$attachment_file='';$errorM=false;
$orders = $override->get('lens_orders','staff_id',$user->data()->id);
$getStatus = $override->getData('order_status');
if($user->isLoggedIn()) {
    if (!$user->data()->access_level == null) {
        if (Input::exists('post')) {
            if (Input::get('changePassword')) {
                $validate = new validate();
                $validate = $validate->check($_POST, array(
                    'old_password' => array(
                        'required' => true,
                    ),
                    'new_password' => array(
                        'required' => true,
                        'min' => 6,
                    ),
                    're-type_password' => array(
                        'required' => true,
                        'matches' => 'new_password'
                    )
                ));
                if ($validate->passed()) {
                    if (Hash::make(Input::get('old_password'), $user->data()->salt) !== $user->data()->password) {
                        $errorMessage = 'Your current password is wrong';
                    } else {
                        $salt = $random->get_rand_alphanumeric(32);
                        $user->update(array(
                            'password' => Hash::make(Input::get('new_password'), $salt),
                            'salt' => $salt
                        ));
                        $successMessage = 'Password changed successfully';
                    }
                } else {
                    $pageError = $validate->errors();
                }
            }
            elseif(Input::get('profile')){
                $user->updateRecord('staff',array(
                    'phone_number' => Input::get('phone_number'),
                    'email_address' => Input::get('email_address'),
                    'birthday' => Input::get('birthday')
                ),$user->data()->id);
                $successMessage = 'Your profile information changed successfully';
            }
            elseif(Input::get('photo')){
                $attachment_file = Input::get('pic');
                if (!empty($_FILES['image']["tmp_name"])) {
                    $attach_file = $_FILES['image']['type'];
                    if ($attach_file == "image/jpeg" || $attach_file == "image/jpg" || $attach_file == "image/png" || $attach_file == "image/gif") {$successMessage = 'Jesus';
                        $folderName = 'assets/images/users/';
                        $attachment_file = $folderName . basename($_FILES['image']['name']);
                        if (move_uploaded_file($_FILES['image']["tmp_name"], $attachment_file)) {
                            $file = true;

                        } else {
                            {
                                $errorM = true;
                                $errorMessage = 'Your profile Picture Not Uploaded ,';
                            }
                        }
                    } else {
                        $errorM = true;
                        $errorMessage = 'None supported file format';
                    }//not supported format
                    if($errorM == false){
                        $user->updateRecord('staff',array(
                            'picture' => $attachment_file,
                        ),$user->data()->id);
                        $successMessage = 'Your profile Picture Uploaded successfully';
                    }
                }else{
                    $errorMessage = 'You have not select any picture to upload';
                }
            }
        }
    } else {
        switch ($user->data()->access_level) {
            case 2:
                Redirect::to('doctor.php');
                break;
            case 3:
                Redirect::to('reception.php');
                break;
        }
    }
}else{Redirect::to('index.php');}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- META SECTION -->
    <title> Siha Optical | Profile</title>
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
            <h1>PROFILE INFORMATION</h1>

            <div class="pull-right">
                <a href="profile.php" class="btn btn-default">REFRESH</a>
                <button class="btn btn-default">TODAY: <?=date('d-M-Y')?></button>
            </div>
        </div>
        <div class="row stacked">
            <div class="col-md-12">
                <div class="x-chart-widget">

                    <div class="x-chart-widget-content">
                        <div class="col-md-offset-1 col-md-10">
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
                                    <h3></h3>
                                    <div class="col-md-3 col-sm-4 col-xs-5">
                                        <form action="#" class="form-horizontal">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <h3><span class="fa fa-user">&nbsp;</span><?=$user->data()->firstname.' '.$user->data()->middlename.' '.$user->data()->lastname?></h3>
                                                    <p><strong>Your Position : <?=$user->data()->position?></strong></p>
                                                    <div class="text-center" id="user_image">
                                                        <?php if($user->data()->picture){?>
                                                            <img src="<?=$user->data()->picture?>" class="img-thumbnail"/>
                                                        <?php }else{?>
                                                        <img src="assets/images/users/no-image.jpg" class="img-thumbnail"/>
                                                        <?php }?>
                                                    </div>
                                                </div>
                                                <div class="panel-body form-group-separated">

                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <a href="#" class="btn btn-primary btn-block btn-rounded" data-toggle="modal" data-target="#modal_change_photo">Change Photo</a>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="col-md-3 col-xs-5 control-label">#ID</label>
                                                        <div class="col-md-9 col-xs-7">
                                                            <input type="text" value="<?=$user->data()->employee_ID?>" class="form-control" disabled/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <a href="#" class="btn btn-danger btn-block btn-rounded" data-toggle="modal" data-target="#modal_change_password">Change password</a>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-6 col-sm-8 col-xs-7">
                                        <form  class="form-horizontal" method="post">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <h3><span class="fa fa-pencil"></span> Profile</h3>
                                                 </div>
                                                <div class="panel-body form-group-separated">
                                                    <div class="form-group">
                                                        <label class="col-md-3 col-xs-5 control-label">Name</label>
                                                        <div class="col-md-9 col-xs-7">
                                                            <input type="text" name="name" value="<?=$user->data()->firstname.'  '.$user->data()->middlename.'  '.$user->data()->lastname?>" class="form-control" disabled/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 col-xs-5 control-label">Phone Number</label>
                                                        <div class="col-md-9 col-xs-7">
                                                            <input type="text" name="phone_number" value="<?=$user->data()->phone_number?>" class="form-control"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 col-xs-5 control-label">Email Address</label>
                                                        <div class="col-md-9 col-xs-7">
                                                            <input type="email" name="email_address" placeholder="Enter your Email Address" value="<?=$user->data()->email_address?>" class="form-control"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-md-3 col-xs-5 control-label">Birth Date</label>
                                                        <div class="col-md-9 col-xs-7">
                                                            <input name="birthday" type="text" class="form-control datepicker" placeholder="Enter your Birthday" value="<?=$user->data()->birthday?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-5">
                                                            <input type="submit" name="profile" value="SAVE" class="btn btn-primary btn-rounded pull-right">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="panel panel-default form-horizontal">
                                            <div class="panel-body">
                                                <h3><span class="fa fa-info-circle"></span> Quick Info</h3>
                                                <p>Some quick info about this user</p>
                                            </div>
                                            <div class="panel-body form-group-separated">
                                                <div class="form-group">
                                                    <label class="col-md-4 col-xs-5 control-label">Last visit</label>
                                                    <div class="col-md-8 col-xs-7 line-height-30"><?=$user->data()->last_login?></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-4 col-xs-5 control-label">Reg.Date</label>
                                                    <div class="col-md-8 col-xs-7 line-height-30"><?=$user->data()->reg_date?></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-4 col-xs-5 control-label">Branch</label>
                                                    <?php $branch=$override->get('clinic_branch','id',$user->data()->branch_id)?>
                                                    <div class="col-md-8 col-xs-7"><?=$branch[0]['name']?></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-4 col-xs-5 control-label">Birthday</label>
                                                    <div class="col-md-8 col-xs-7 line-height-30"><?=$user->data()->birthday?></div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <!------------------------------------------------------ modals start here --------------------------------------------------------------->
                                    <div class="modal animated fadeIn" id="modal_change_photo" tabindex="-1" role="dialog" aria-labelledby="smallModalHead" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                    <h4 class="modal-title" id="smallModalHead">Change photo</h4>
                                                </div>
                                                <form  method="post">
                                                    <div class="modal-body">
                                                        <div class="text-center" id="cp_target">Use form below to upload file. Only .jpg files.</div>
                                                        <input type="hidden" name="cp_img_path" id="cp_img_path"/>
                                                        <input type="hidden" name="ic_x" id="ic_x"/>
                                                        <input type="hidden" name="ic_y" id="ic_y"/>
                                                        <input type="hidden" name="ic_w" id="ic_w"/>
                                                        <input type="hidden" name="ic_h" id="ic_h"/>
                                                    </div>
                                                </form>
                                                <form id="cp_upload" method="post" enctype="multipart/form-data" >
                                                    <div class="modal-body form-horizontal form-group-separated">
                                                        <div class="form-group">
                                                            <label class="col-md-4 control-label">Select New Profile Photo</label>
                                                            <div class="col-md-4">
                                                                <input type="file" class="fileinput btn-info" name="image" id="cp_photo" data-filename-placement="inside" title="Select Profile Photo"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="submit" name="photo" value="Accept" class="btn btn-success">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal animated fadeIn" id="modal_change_password" tabindex="-1" role="dialog" aria-labelledby="smallModalHead" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                    <h4 class="modal-title" id="smallModalHead">Change password</h4>
                                                </div>
                                                <form method="post">
                                                    <div class="modal-body form-horizontal form-group-separated">
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label">Old Password</label>
                                                            <div class="col-md-9">
                                                                <input type="password" class="form-control" name="old_password"/>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label">New Password</label>
                                                            <div class="col-md-9">
                                                                <input type="password" class="form-control" name="new_password"/>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-md-3 control-label">Repeat New</label>
                                                            <div class="col-md-9">
                                                                <input type="password" class="form-control" name="re-type_password"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="submit" name="changePassword" value="Change Password" class="btn btn-danger">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!------------------------------------------------------ modals ends here --------------------------------------------------------------->
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
        $('#BE').change(function(){
            var getEye = $(this).val();
            $.ajax({
                url:"process.php?content=eye",
                method:"GET",
                data:{eye:getEye},
                dataType:"text",
                success:function(data){
                    $('#lens_desc').html(data);
                }
            });
        });
        $('#RE').change(function(){
            var getEye = $(this).val();
            $.ajax({
                url:"process.php?content=eye",
                method:"GET",
                data:{eye:getEye},
                dataType:"text",
                success:function(data){
                    $('#lens_desc').html(data);
                }
            });
        });
        $('#LE').change(function(){
            var getEye = $(this).val();
            $.ajax({
                url:"process.php?content=eye",
                method:"GET",
                data:{eye:getEye},
                dataType:"text",
                success:function(data){
                    $('#lens_desc').html(data);
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






