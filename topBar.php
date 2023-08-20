<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
 $no = $override->getCount('order_status','status',0);
 $notification = $override->get('order_status','status',0);
 $sms = $override->getData('bundle_usage');
 $patient = $override->countValue('patient');
?>
<ul class="x-navigation x-navigation-horizontal x-navigation-panel">
    <!-- TOGGLE NAVIGATION -->
    <li class="xn-icon-button">
        <a href="#" class="x-navigation-minimize"><span class="fa fa-dedent"></span></a>
    </li>
    <!-- END TOGGLE NAVIGATION -->
    <!-- SEARCH -->
    <li class="xn-search">
        <form role="form">
            <input type="text" name="search" placeholder="Search..."/>
        </form>
    </li>
    <!-- END SEARCH -->
    <!-- POWER OFF -->
    <li class="xn-icon-button pull-right last">
        <a href="#"><span class="fa fa-power-off"></span></a>
        <ul class="xn-drop-left animated zoomIn">
            <li><a href="addInfo.php?id=33"><span class="fa fa-user"></span> My Profile</a></li>
            <li><a href="lock.php"><span class="fa fa-lock"></span> Lock Screen</a></li>
            <li><a href="#" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span> Sign Out</a></li>
        </ul>
    </li>
    <!-- END POWER OFF -->
    <!-- MESSAGES -->
    <li class="xn-icon-button pull-right">
        <a href="#"><span class="fa fa-comments"></span></a>
        <?php if($no){?>
            <div class="informer informer-danger"><?=$no?></div>
        <?php }else{?>
        <?php }?>
        <div class="panel panel-primary animated zoomIn xn-drop-left xn-panel-dragging">
            <div class="panel-heading">
                <h3 class="panel-title"><span class="fa fa-comments"></span> Orders</h3>
                <div class="pull-right">
                    <span class="label label-danger"><?=$no?> Pending</span>
                </div>
            </div>
            <div class="panel-body list-group list-group-contacts scroll" style="height: 200px;">
                <?php foreach($notification as $note){
                    $orderD = $override->get('lens_orders','id',$note['order_id']);
                    $total = $orderD[0]['RE_qty'] + $orderD[0]['LE_qty'];
                    $product = $override->get('products','id',$orderD[0]['product'])?>
                    <a href="information.php?id=3" class="list-group-item">
                        <div class="list-group-status status-online"></div>
                        <span class="contacts-title"><?=$product[0]['name'].'  '?>&nbsp;<strong>Date :</strong> <?=$orderD[0]['order_date']?></span>
                        <p><strong>Material :</strong> <?=$orderD[0]['material']?>, <strong>Order :</strong> <?=$orderD[0]['order_from']?>, <strong>Eye : </strong><?=$orderD[0]['eye']?>, <strong>Qty :</strong> <?=$total?></p>
                    </a>
                <?php }?>
            </div>
            <div class="panel-footer text-center">
                <a href="information.php?id=3">Show all messages</a>
            </div>
        </div>
    </li>
    <li class="xn-icon-button pull-right">
        <a href="#"><span class="fa fa-envelope"></span></a>
        <?php if($sms){if($sms[0]['sms'] > $patient){?>
            <div class="informer informer-success"><?=$sms[0]['sms']?></div>
        <?php }elseif($sms[0]['sms'] > ceil($patient/2)){?>
            <div class="informer informer-info"><?=$sms[0]['sms']?></div>
        <?php }elseif($sms[0]['sms'] > ceil($patient/4)){?>
            <div class="informer informer-warning"><?=$sms[0]['sms']?></div>
        <?php }else {?>
            <div class="informer informer-danger"><?=$sms[0]['sms']?></div>
        <?php }}else{?>
            <div class="informer informer-danger">0</div>
        <?php }?>
    </li>
    <li class="xn-icon-button pull-right">
        <a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];?>" class="btn btn-info"><span class="fa fa-refresh"></span></a>
    </li>
</ul>