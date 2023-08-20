<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
$total_orders=0;$pending=0;$confirmed=0;$received=0;
$orders = $override->get('lens_orders','staff_id',$user->data()->id);
$getStatus = $override->getData('order_status');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- META SECTION -->
    <title> Siha Optical | Error </title>
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

            <div class="x-hnavigation">
                <div class="x-hnavigation-logo">
                    <a href="dashboard.php">Family</a>
                </div>
                <ul>
                    <li class="active">
                        <a href="reception.php">New Patient</a>
                    </li>
                    <li class="">
                        <a href="form.php?id=0">Return Patient</a>
                    </li>
                    <li class="xn-openable">
                        <a href="#">Payments</a>
                        <ul>
                            <li><a href="form.php?id=1"><span class="fa fa-cube"></span>Cash Payment</a></li>
                            <li><a href="form.php?id=2"><span class="fa fa-life-ring"></span>Insurance Payment</a></li>
                            <li><a href="form.php?id=3"><span class="fa fa-recycle"></span> Other Payment</a></li>
                            <li><a href="form.php?id=4"><span class="fa fa-database"></span> Pending Payment</a></li>
                        </ul>
                    </li>
                </ul>

                <div class="x-features">
                    <div class="x-features-nav-open">
                        <span class="fa fa-bars"></span>
                    </div>
                    <div class="pull-right">
                        <div class="x-features-search">
                            <input type="text" name="search">
                            <input type="submit">
                        </div>
                        <div class="x-features-profile">
                            <img src="assets/images/users/no-image.jpg">
                            <ul class="xn-drop-left animated zoomIn">
                                <li><a href="profile.php"><span class="fa fa-refresh"></span>Change Password</a></li>
                                <li><a href="lock.php"><span class="fa fa-lock"></span> Lock Screen</a></li>
                                <li><a href="logout.php" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span> Sign Out</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="x-content-tabs">
                <ul>
                    <li><a href="#new_patient" class="icon active"><span class="fa fa-desktop"><strong>&nbsp;&nbsp;ERROR</strong></span></a></li>
                    <!--<li><a href="#return_patient"><span class="fa fa-life-ring"></span><span>Second tab</span></a></li>
                    <li><a href="#third-tab"><span class="fa fa-microphone"></span><span>Third tab</span></a></li>
                    <li><a href="#new-tab" class="icon"><span class="fa fa-plus"></span></a></li>-->
                </ul>
            </div>
            <div class="x-content">
                <div id="new_patient">
                    <div class="x-content-title">
                        <h1>ERROR OCCURRED</h1>

                        <div class="pull-right">
                            <button class="btn btn-default">TODAY: <?=date('d-M-Y')?></button>
                        </div>
                    </div>
                    <div class="row stacked">
                        <div class="col-md-12">
                            <div class="x-chart-widget">

                                <div class="x-chart-widget-content">
                                    <div class="x-chart-widget-content-head">
                                        <h4> </h4>
                                    </div>

                                    <div class="col-md-offset-2 col-md-8">
                                        <div class="panel panel-default">
                                            <div class="error-container">
                                                <div class="error-code">404</div>
                                                <div class="error-text">page not found</div>
                                                <div class="error-subtext">Unfortunately we're having trouble loading the page you are looking for. Please wait a moment and try again or use action below.</div>
                                                <div class="error-actions">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <button class="btn btn-info btn-block btn-lg" onClick="document.location.href = 'index.php';">Back to dashboard</button>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <button class="btn btn-primary btn-block btn-lg" onClick="history.back();">Previous page</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="error-subtext"></div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="input-group">

                                                        </div>
                                                    </div>
                                                </div>
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
                Copyright © 2017 Family Eye Care. All rights reserved
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



