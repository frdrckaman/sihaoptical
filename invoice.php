<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
$info=null;$patient=null;$checkup=null;$checkupCost=null;$payment=null;$medicine=null;
$prescription=null;$frameSold=null;$frameBrand=null;$frameModel=null;$medCost=null;$totalCheckupCost=0;
$date=null;
switch($_GET['c']){
    case 0:
        $date = date('Y-m-d');
        break;
    case 1:
        $date = $_GET['date'];
        break;
    case 2:
        $date = $_GET['date'];
        break;
}
$patient=$override->get('patient','id',$_GET['id']);
$payment=$override->getNews('payment','patient_id',$_GET['id'],'pay_date',$date);
$checkup=$override->get('test_list','id',$payment[0]['checkup']);
$prescription=$override->getNews('prescription','patient_id',$_GET['id'],'given_date',$date);
$frameSold=$override->getNews('frame_sold','sold_date',$date,'patient_id',$_GET['id']);
if($frameSold){
    $frameBrand=$override->get('frame_brand','id',$frameSold[0]['brand']);
    $frameModel=$override->get('frame_model','id',$frameSold[0]['model']);
}
$len_prescription=$override->getNews('lens_prescription','patient_id',$_GET['id'],'checkup_date',$date);
if($len_prescription){
    $lensPrice=$override->get('lens_power','id',$len_prescription[0]['lens']);
    $lensGroup=$override->get('lens','id',$lensPrice[0]['lens_id']);
    $lensCat=$override->get('lens_category','id',$lensPrice[0]['cat_id']);
    $lensType=$override->get('lens_type','id',$lensPrice[0]['type_id']);
}
$checkupRecord=$override->getNews('checkup_record','patient_id',$_GET['id'],'checkup_date',$date);
$testPerformed=$override->getNews('test_performed','patient_id',$_GET['id'],'date_performed',$_GET['date']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- META SECTION -->
    <title> Siha Optical Eye Center </title>
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




<div class="x-content">
<div id="main-tab">
<div class="row stacked">
<div class="col-md-12">
<div class="x-chart-widget">
<div class="x-chart-widget-content">
    <div class="row">
        <div class="col-md-12">
            <?php if($_GET['c'] == 0){?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h6>RECEIPT No. <strong><?=$payment[0]['id']?><span class="pull-right"><img src="img/famly%20eye%20care.png"></span></strong></h6>
                        <br><br><p class="pull-right"><strong>P.O.BOX 5054 MWANZA, TEL : +255 785 360 107 , +255 743 501 700</strong></p>
                        <div class="push-down-10 pull-left">
                            <button class="btn btn-default" onclick="window.print()"><span class="fa fa-print"></span> Print</button>
                        </div>
                        <!-- INVOICE -->
                        <div class="invoice">

                            <div class="row">
                                <div class="col-md-8">

                                    <div class="invoice-address">
                                        <h5>Patient Information</h5><p class="pull-right">Health Insurance : <?=$patient[0]['health_insurance']?></p>
                                        <p>Name : <?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></p><p class="pull-right">Member No : <?=$patient[0]['dependent_no']?></p>
                                        <p>Age : <?=$patient[0]['age']?></p><p class="pull-right">Occupation : <?=$patient[0]['occupation']?></p>
                                        <p>Sex : <?=$patient[0]['sex']?></p><p class="pull-right">Address : <?=$patient[0]['address']?></p>
                                        <p>Phone : <?=$patient[0]['phone_number']?></p><p class="pull-right">Date : <?=date('Y-m-d')?></p>
                                        <p>Email : <?=$patient[0]['email_address']?></p>
                                    </div>

                                </div>
                            </div>

                            <div class="table-invoice">
                                <table class="table">
                                    <tr>
                                        <th>Prescription Description</th>
                                        <th class="text-center">Cost</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Checkup : </strong><?php foreach($testPerformed as $test){$name=$override->get('test_list','id',$test['test_id']);if($name){echo $name[0]['name'].' , ';}}?>
                                        </td>
                                        <td class="text-center"><?php foreach($testPerformed as $test){$name=$override->get('test_list','id',$test['test_id']);if($name){if($patient[0]['health_insurance']){$totalCheckupCost +=$name[0]['insurance_price'];}else{$totalCheckupCost +=$name[0]['cost'];}}} echo $totalCheckupCost;?></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Medicine : </strong><?php if($prescription){
                                                foreach($prescription as $med){$medicine=$override->get('medicine','id',$med['medicine_id']);$medCost+=($med['quantity']*$medicine[0]['price']);echo $medicine[0]['name'].' ( '.$prescription[0]['quantity'].' )  ,';}}else{echo'None';}?>
                                        </td>
                                        <td class="text-center"><?=$medCost?></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Lens : </strong><?=$lensCat[0]['name'].' '.$lensType[0]['name']?>
                                        </td>
                                        <td class="text-center"><?=$len_prescription[0]['cost']?></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Frame : </strong> Brand : <?=$frameBrand[0]['name'].'  ,  '?> Model : <?=$frameModel[0]['model'].'  ,  '?> Size : <?=$frameSold[0]['size']?>
                                        </td>
                                        <td class="text-center"><?=$frameSold[0]['price']?></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6 pull-right">
                                    <table class="table table-striped">
                                        <tr class="total">
                                            <td>Total Amount:</td><td class="text-right"><?=$payment[0]['cost']?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <!-- END INVOICE -->

                    </div>
                </div>
            <?php }
            elseif($_GET['c'] == 1){?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h6>RECEIPT No. <strong><?=$payment[0]['id']?><span class="pull-right"><img src="img/famly%20eye%20care.png"></span></strong></h6>
                        <br><br><p class="pull-right"><strong>P.O.BOX 5054 MWANZA, TEL : +255 785 360 107 , +255 743 501 700</strong></p>
                        <div class="push-down-10 pull-left">
                            <button class="btn btn-default" onclick="window.print()"><span class="fa fa-print"></span> Print</button>
                        </div>
                        <!-- INVOICE -->
                        <div class="invoice">
                            <div class="row">
                                <div class="col-md-8">

                                    <div class="invoice-address">
                                        <h5>Patient Information</h5><p class="pull-right">Health Insurance : <?=$patient[0]['health_insurance']?></p>
                                        <p>Name : <?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></p><p class="pull-right">Member No : <?=$patient[0]['dependent_no']?></p>
                                        <p>Age : <?=$patient[0]['age']?></p><p class="pull-right">Occupation : <?=$patient[0]['occupation']?></p>
                                        <p>Sex : <?=$patient[0]['sex']?></p><p class="pull-right">Address : <?=$patient[0]['address']?></p>
                                        <p>Phone : <?=$patient[0]['phone_number']?></p><p class="pull-right">Date : <?=date('Y-m-d')?></p>
                                        <p>Email : <?=$patient[0]['email_address']?></p>
                                    </div>

                                </div>
                            </div>
                            <div class="table-invoice">
                                <table class="table">
                                    <tr>
                                        <th>Prescription Description</th>
                                        <th class="text-center">Cost</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Checkup : </strong><?php foreach($testPerformed as $test){$name=$override->get('test_list','id',$test['test_id']);if($name){echo $name[0]['name'].' , ';}}?>
                                        </td>
                                        <td class="text-center"><?php foreach($testPerformed as $test){$name=$override->get('test_list','id',$test['test_id']);if($name){if($patient[0]['health_insurance']){$totalCheckupCost +=$name[0]['insurance_price'];}else{$totalCheckupCost +=$name[0]['cost'];}}} echo $totalCheckupCost;?></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Medicine : </strong><?php if($prescription){
                                                foreach($prescription as $med){$medicine=$override->get('medicine','id',$med['medicine_id']);$medCost+=($med['quantity']*$medicine[0]['price']);echo $medicine[0]['name'].' ( '.$prescription[0]['quantity'].' )  ,';}}else{echo'None';}?>
                                        </td>
                                        <td class="text-center"><?=$medCost?></td>
                                    </tr>
                                    <?php if($len_prescription){?>
                                        <tr>
                                            <td>
                                                <strong>Lens : </strong><?=$lensCat[0]['name'].' '.$lensType[0]['name']?>
                                            </td>
                                            <td class="text-center"><?=$len_prescription[0]['cost']?></td>
                                        </tr>
                                    <?php }?>
                                    <?php if($frameSold){?>
                                        <tr>
                                            <td>
                                                <strong>Frame : </strong> Brand : <?=$frameBrand[0]['name'].'  ,  '?> Model : <?=$frameModel[0]['model'].'  ,  '?> Size : <?=$frameSold[0]['size']?>
                                            </td>
                                            <td class="text-center"><?=$frameSold[0]['price']?></td>
                                        </tr>
                                    <?php }?>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6 pull-right">
                                    <table class="table table-striped">
                                        <tr class="total">
                                            <td>Total Amount:</td><td class="text-right"><?=$payment[0]['cost']?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <!-- END INVOICE -->

                    </div>
                </div>
            <?php }
            elseif($_GET['c'] == 2){?>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h6>INVOICE No. <strong><?=$payment[0]['id']?><span class="pull-right"><img src="img/famly%20eye%20care.png"></span></strong></h6>
                        <br><br><p class="pull-right"><strong>P.O.BOX 5054 MWANZA, TEL : +255 785 360 107 , +255 743 501 700</strong></p>
                        <div class="push-down-10 pull-left">
                            <button class="btn btn-default" onclick="window.print()"><span class="fa fa-print"></span> Print</button>
                        </div>
                        <!-- INVOICE -->
                        <div class="invoice">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="invoice-address">
                                        <h5>Patient Information</h5><p class="pull-right">Health Insurance : <?=$patient[0]['health_insurance']?></p>
                                        <p>Name : <?=$patient[0]['firstname'].' '.$patient[0]['lastname']?></p><p class="pull-right">Member No : <?=$patient[0]['dependent_no']?></p>
                                        <p>Age : <?=$patient[0]['age']?></p><p class="pull-right">Occupation : <?=$patient[0]['occupation']?></p>
                                        <p>Sex : <?=$patient[0]['sex']?></p><p class="pull-right">Address : <?=$patient[0]['address']?></p>
                                        <p>Phone : <?=$patient[0]['phone_number']?></p><p class="pull-right">Date : <?=$date?></p>
                                        <p>Email : <?=$patient[0]['email_address']?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="table-invoice">
                                <div class="col-md-offset-2 col-md-8">
                                    <table class="table">
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
                                            <td><input type="text" class="form-control" value="<?=$checkupRecord[0]['rx_OD_sphere']?>" disabled/></td>
                                            <td><input type="text" class="form-control" value="<?=$checkupRecord[0]['rx_cyl']?>" disabled/></td>
                                            <td><input type="text" class="form-control" value="<?=$checkupRecord[0]['rx_axis']?>" disabled/></td>
                                            <td><input type="text" class="form-control" value="<?=$checkupRecord[0]['rx_va']?>" disabled/></td>
                                            <td><input type="text" class="form-control" value="<?=$checkupRecord[0]['rx_add']?>" disabled/></td>
                                        </tr>
                                        <tr>
                                            <td>Left</td>
                                            <td><input type="text" class="form-control" value="<?=$checkupRecord[0]['add_rx_OS_sphere']?>" disabled/></td>
                                            <td><input type="text" class="form-control" value="<?=$checkupRecord[0]['add_rx_cyl']?>" disabled/></td>
                                            <td><input type="text" class="form-control" value="<?=$checkupRecord[0]['add_rx_axis']?>" disabled/></td>
                                            <td><input type="text" class="form-control" value="<?=$checkupRecord[0]['add_rx_va']?>" disabled/></td>
                                            <td><input type="text" class="form-control" value="<?=$checkupRecord[0]['add_rx_add']?>" disabled/></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- END INVOICE -->

                    </div>
                </div>
            <?php }?>
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
<noscript><div><img src="https://mc.yandex.ru/watch/25836617" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>

</html>






