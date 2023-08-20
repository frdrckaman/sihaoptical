<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;
$total_orders=0;$pending=0;$confirmed=0;$received=0;
$orders = $override->get('lens_orders','staff_id',$user->data()->id);
$getStatus = $override->getData('order_status');
if($user->isLoggedIn()) {
        if ($user->data()->access_level == 4) {
            if ($getStatus) {
                foreach ($getStatus as $orderStatus) {
                    $orderDetail = $override->get('lens_orders', 'id', $orderStatus['order_id']);
                    if ($orderDetail[0]['staff_id'] == $user->data()->id) {
                        switch ($orderStatus['status']) {
                            case 0:
                                $pending++;
                                break;
                            case 1:
                                $confirmed++;
                                break;
                            case 2:
                                $received++;
                                break;
                        }
                    }
                    $total_orders++;
                }
            }
            if (Input::exists('post')) {
                if (Input::get('addOrder')) {
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'ref_no' => array(
                            'required' => true,
                            'min' => 2,
                            'unique' => 'lens_orders'
                        ),
                        'product' => array(
                            'required' => true,
                        ),
                        'order_from' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()) {
                        try {
                            $user->createRecord('lens_orders', array(
                                'ref_no' => Input::get('ref_no'),
                                'product' => Input::get('product'),
                                'order_from' => Input::get('order_from'),
                                'material' => Input::get('material'),
                                'eye' => Input::get('eye'),
                                'RE_sph' => Input::get('RE_sph'),
                                'RE_cyl' => Input::get('RE_cyl'),
                                'RE_axis' => Input::get('RE_axis'),
                                'RE_add' => Input::get('RE_add'),
                                'RE_qty' => Input::get('RE_qty'),
                                'LE_sph' => Input::get('LE_sph'),
                                'LE_cyl' => Input::get('LE_cyl'),
                                'LE_axis' => Input::get('LE_axis'),
                                'LE_add' => Input::get('LE_add'),
                                'LE_qty' => Input::get('LE_qty'),
                                'order_details' => Input::get('details'),
                                'order_date' => date('Y-m-d'),
                                'staff_id' => $user->data()->id,
                            ));
                            $status = $override->get('lens_orders', 'ref_no', Input::get('ref_no'));
                            $user->createRecord('order_status', array(
                                'name' => 'pending',
                                'order_id' => $status[0]['id'],
                            ));
                            $successMessage = 'Order have been Successful Registered';

                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
        } else {
            Redirect::to('index.php');
            }
}else{Redirect::to('index.php');}
?>
<!DOCTYPE html>
<html lang="en">

<head>        
        <!-- META SECTION -->
        <title> Siha Optical | Orders Panel</title>
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
                    
                  <?php include'menuBar.php'?>
                    <div class="x-content">
                        <div id="main-tab">
                            <div class="x-content-title">
                                <h1>NEW ORDER</h1>

                                <div class="pull-right">
                                    <a href="dashboard.php" class="btn btn-default">REFRESH</a>
                                    <button class="btn btn-default">TODAY: <?=date('d-M-Y')?></button>
                                </div>
                            </div>
                            <div class="row stacked">
                                <div class="col-md-12">
                                    <div class="x-chart-widget">

                                        <div class="x-chart-widget-content">
                                            <div class="x-chart-widget-content-head">
                                                <h4>SUMMARY</h4>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="x-chart-widget-informer">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="x-chart-widget-informer-item">
                                                                    <div class="count"><?=$total_orders?></div>
                                                                    <div class="title">Total Orders</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="x-chart-widget-informer-item">
                                                                    <div class="count"><?=$pending?></div>
                                                                    <div class="title">Pending Orders</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="x-chart-widget-informer-item">
                                                                    <div class="count"><?=$confirmed?></div>
                                                                    <div class="title">Confirmed Orders</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="x-chart-widget-informer-item last">
                                                                    <div class="count"><?=$received?></div>
                                                                    <div class="title">Received Orders</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><br><br>
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
                                                        <h3>Order details</h3>
                                                        <form role="form" class="form-horizontal" method="post">
                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label">Ref No.&nbsp;&nbsp;</label>
                                                                <div class="col-md-3">
                                                                    <input type="text" name="ref_no" class="form-control"/>
                                                                </div>
                                                                    <label class="col-md-1 control-label">Material : &nbsp;&nbsp;</label>
                                                                    <label class="col-md-0">&nbsp;&nbsp;&nbsp;</label>
                                                                    <label class="control-label">
                                                                        <input id="radio1" type="radio" name="material" value="CR" checked>
                                                                        <span class="outer"><span class="inner"></span></span>&nbsp;CR&nbsp;&nbsp;&nbsp;
                                                                    </label>
                                                                    <label class="control-label">
                                                                        <input id="radio2" type="radio" name="material" value="Glass">
                                                                        <span class="outer"><span class="inner"></span></span> &nbsp;Glass&nbsp;&nbsp;&nbsp;
                                                                    </label>
                                                                <label class="col-md-0">&nbsp;&nbsp;&nbsp;</label>
                                                                <label class="col-md-0">&nbsp;&nbsp;&nbsp;</label>
                                                                    <label class="col-md-0">&nbsp;&nbsp;&nbsp;&nbsp;Eye : </label>
                                                                <label class="col-md-0">&nbsp;&nbsp;&nbsp;</label>
                                                                <label class="control-label">
                                                                    <input id="BE" type="radio" name="eye" value="both" checked>
                                                                    <span class="outer"><span class="inner"></span></span>&nbsp;Both&nbsp;&nbsp;&nbsp;
                                                                </label>
                                                                <label class="control-label">
                                                                    <input id="RE" type="radio" name="eye" VALUE="RE">
                                                                    <span class="outer"><span class="inner"></span></span> &nbsp;Right&nbsp;&nbsp;&nbsp;
                                                                </label>
                                                                <label class="control-label">
                                                                    <input id="LE" type="radio" name="eye" value="LE">
                                                                    <span class="outer"><span class="inner"></span></span> &nbsp;Left&nbsp;&nbsp;&nbsp;
                                                                </label>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label">Product : &nbsp;</label>
                                                                <div class="col-md-9">
                                                                    <select name="product" class="form-control select" data-live-search="true">
                                                                        <option value="">Select Product</option>
                                                                        <?php foreach($override->getData('products') as $product){?>
                                                                        <option value="<?=$product['id']?>"><?=$product['name']?></option>
                                                                        <?php }?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label">Order From : &nbsp;</label>
                                                                <div class="col-md-6">
                                                                    <select name="order_from" class="form-control select" >
                                                                        <option value="">Select Order From</option>
                                                                        <option>GKB</option>
                                                                        <option>Tan Optic</option>
                                                                        <option>FEC Stock</option>
                                                                        <option>Local Supplier</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-offset-1 col-md-10">
                                                                    <div class="table-responsive" id="lens_desc">
                                                                        <table class="table table-bordered">
                                                                            <thead>
                                                                            <tr>
                                                                                <th>Eye</th>
                                                                                <th>Sph</th>
                                                                                <th>Cyl</th>
                                                                                <th>Axis</th>
                                                                                <th>Add</th>
                                                                                <th>Qty</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <tr>
                                                                                <td>Right</td>
                                                                                <td><input name="RE_sph" type="text" class="form-control"/></td>
                                                                                <td><input name="RE_cyl" type="text" class="form-control"/></td>
                                                                                <td><input name="RE_axis" type="text" class="form-control"/></td>
                                                                                <td><input name="RE_add" type="text" class="form-control"/></td>
                                                                                <td><input name="RE_qty" type="number" min="1" class="form-control" value="1"/></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>Left</td>
                                                                                <td><input name="LE_sph" type="text" class="form-control"/></td>
                                                                                <td><input name="LE_cyl" type="text" class="form-control"/></td>
                                                                                <td><input name="LE_axis" type="text" class="form-control"/></td>
                                                                                <td><input name="LE_add" type="text" class="form-control"/></td>
                                                                                <td><input name="LE_qty" type="number" min="1" class="form-control" value="1"/></td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="col-md-2 control-label">Order Details : &nbsp;</label>
                                                                <div class="col-md-9">
                                                                    <textarea name="details" class="form-control" rows="5"></textarea>
                                                                </div>
                                                            </div>
                                                            <div id="joy">

                                                            </div>
                                                            <div class="pull-right">
                                                                <input type="submit" name="addOrder" value="Save Order" class="btn btn-success">
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






