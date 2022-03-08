<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
$total_orders=0;$pending=0;$confirmed=0;$received=0;$contents=null;
if($user->isLoggedIn()){
    if($user->data()->access_level == 3){
        if(Input::exists('post')){
            if(Input::get('select')){
                $redirect = 'form.php?id='.$_GET['id'].'&p='.Input::get('patient');
                Redirect::to($redirect);
            }
        }
    }
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
                <div id="new_patient">
                    <div class="x-content-title">
                        <h1>SEARCH</h1>

                        <div class="pull-right">
                            <a href="search.php?id=<?=$_GET['id']?>" class="btn btn-default">REFRESH</a>
                            <button class="btn btn-default">TODAY: <?=date('d-M-Y')?></button>
                        </div>
                    </div>
                    <div class="row stacked">
                        <div class="col-md-12">
                            <div class="x-chart-widget">
                                <?php foreach($override->get('staff','branch_id',$user->data()->branch_id) as $bday){if($bday['birthday'] == date('Y-m-d') && !$user->data()->birthday == date('Y-m-d')){?>
                                    <div class="alert alert-warning" role="alert">
                                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                        <strong>Today is <?=$bday['firstname'].' '.$bday['middlename'].' '.$bday['lastname']?> Birthday&nbsp;</strong>
                                    </div>
                                <?php }}?>
                                <div class="x-chart-widget-content">
                                    <div class="x-chart-widget-content-head">
                                        <h4>PATIENT ON QUEUE : </h4>
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
                                                <?php /*if($_GET['id'] == 0){*/?>
                                                    <h3>SELECT PATIENT </h3>
                                                    <h3>&nbsp;</h3>
                                                    <!--<form role="form" class="form-horizontal" method="post">
                                                        <div class="form-group">
                                                            <div class="col-md-offset-1 col-md-10 col-md-offset-0">
                                                                <select name="patient" class="form-control select" data-live-search="true">
                                                                    <option value="">Select Patient</option>
                                                                    <?php if($_GET['id'] == 3){$getContent=$override->getSort2('payment','status',2,'branch_id',$user->data()->branch_id,'id');}else{$getContent=$override->getSort3('payment','payment',0,'status',0,'branch_id',$user->data()->branch_id,'id');}
                                                                    foreach($getContent as $data){$name=$override->get('patient','id',$data['patient_id']);?>
                                                                        <option value="<?=$data['patient_id']?>"><?=$name[0]['firstname'].' '.$name[0]['lastname'].'  '.$name[0]['phone_number']?></option>
                                                                    <?php }?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <input type="submit" name="select" value="Select" class="btn btn-info">
                                                            </div>
                                                        </div>
                                                    </form><br>-->
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <h3 class="panel-title"><strong><??></strong></h3>
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
                                                                        <th>Phone Number</th>
                                                                        <th>Cost</th>
                                                                        <?php if($_GET['id']==3){?>
                                                                            <th>Paid Amount</th>
                                                                            <th>Discount</th>
                                                                            <th>Remaining</th>
                                                                        <?php }?>
                                                                        <th>Checkup Date</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php if($_GET['id'] == 3){$getContent=$override->getSort2('payment','status',2,'branch_id',$user->data()->branch_id,'id');}else{$getContent=$override->getSort3('payment','payment',0,'status',0,'branch_id',$user->data()->branch_id,'id');}
                                                                    foreach($getContent as $content){$name=$override->get('patient','id',$content['patient_id'])?>
                                                                    <tr>
                                                                        <?php $checkUp=$override->getNews('test_performed','checkup_id',$content['checkup_id'],'patient_id',$content['patient_id']);$cCost=0;
                                                                        if($checkUp){foreach($checkUp as $chCost){
                                                                            $cPrice=$override->get('test_list','id',$chCost['test_id']);
                                                                            if($_GET['id'] == 1){
                                                                                $cCost += $cPrice[0]['insurance_price'];
                                                                            }else{
                                                                                $cCost += $cPrice[0]['cost'];
                                                                            }
                                                                        }}
                                                                        ?>
                                                                        <td><a href="form.php?id=<?=$_GET['id']?>&p=<?=$content['patient_id']?>&c=<?=$content['checkup_id']?>"><?=$name[0]['firstname'].' '.$name[0]['lastname']?></a></td>
                                                                        <td><?=$name[0]['phone_number']?></td>
                                                                        <td><?php if($_GET['id'] == 3){echo $content['cost'];}else{echo $cCost;}?></td>
                                                                        <?php if($_GET['id']==3){?>
                                                                            <td><?=$content['payment']?></td>
                                                                            <td><?=$content['discount']?></td>
                                                                            <td><?=$content['cost']-($content['payment'] + $content['discount'])?></td>
                                                                        <?php }?>
                                                                        <td><?=$content['checkup_date']?></td>
                                                                    </tr>
                                                                    <?php }?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php /*}*/?>

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

<script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script>
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

