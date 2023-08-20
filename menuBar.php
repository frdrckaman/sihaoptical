<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
if($user->data()->access_level == 3) {?>
    <div class="x-hnavigation">
        <div class="x-hnavigation-logo">
            <a href="#">Family</a>
        </div>
        <ul>
            <li class="">
                <a href="reception.php">New Patient</a>
            </li>
            <li class="">
                <a href="form.php?id=33">Return Patient</a>
            </li>
            <li class="xn-openable">
                <a href="#">Payments</a>
                <ul>
                    <li><a href="search.php?id=0"><span class="fa fa-money"></span>Cash Payment</a></li>
                    <li><a href="search.php?id=1"><span class="fa fa-life-ring"></span>Insurance Payment</a></li>
                    <!--<li><a href="search.php?id=2"><span class="fa fa-recycle"></span> Other Payment</a></li>-->
                    <li><a href="search.php?id=3"><span class="fa fa-database"></span> Pending Payment</a></li>
                </ul>
            </li>
            <li class="xn-openable">
                <a href="#">Other Services</a>
                <ul>
                    <li><a href="form.php?id=7"><span class="fa fa-gear"></span>Lens Fix</a></li>
                    <li><a href="form.php?id=8"><span class="fa fa-user"></span>Out Patient</a></li>
                    <li><a href="form.php?id=9"><span class="fa fa-binoculars"></span> Frames & Sun Glasses</a></li>
                </ul>
            </li>
            <li class="xn-openable">
                <a href="#">Appointment</a>
                <ul>
                    <li><a href="form.php?id=14"><span class="fa fa-calendar-plus-o"></span>Set Appointment</a></li>
                    <li><a href="info.php?id=18"><span class="fa fa-th-list"></span>View Appointments</a></li>
                </ul>
            </li>
            <li class="xn-openable">
                <a href="#">Stock</a>
                <ul>
                    <li><a href="info.php?id=8" target="_blank"><span class="fa fa-eye"></span>Lens Available</a></li>
                    <li><a href="info.php?id=9" target="_blank"><span class="fa fa-medkit"></span>Medicine Available</a></li>
                </ul>
            </li>
            <li class="xn-openable">
                <a href="#">Reports</a>
                <ul>
                    <li><a href="info.php?id=20"><span class="fa fa-clipboard"></span>Patient Info</a></li>
                    <li><a href="info.php?id=4"><span class="fa fa-clipboard"></span>Patient Receipt</a></li>
                    <li><a href="#"><span class="fa fa-file-text"></span>Reports</a></li>
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
                    <?php if($user->data()->picture){?>
                        <img src="<?=$user->data()->picture?>">
                    <?php }else{?>
                        <img src="assets/images/users/no-image.jpg">
                    <?php }?>
                    <ul class="xn-drop-left animated zoomIn">
                        <li><a href="profile.php"><span class="fa fa-user"></span>My Profile</a></li>
                        <li><a href="info.php?id=11"><span class="fa fa-envelope"></span>Notifications</a></li>
                        <li><a href="lock.php"><span class="fa fa-lock"></span> Lock Screen</a></li>
                        <li><a href="logout.php" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span> Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="x-content-tabs">
        <ul>
            <li><a href="#new_patient" class="icon active"><span class="fa fa-desktop"><strong>&nbsp;&nbsp;</strong></span></a></li>
            <!--<li><a href="#return_patient"><span class="fa fa-life-ring"></span><span>Second tab</span></a></li>
            <li><a href="#third-tab"><span class="fa fa-microphone"></span><span>Third tab</span></a></li>
            <li><a href="#new-tab" class="icon"><span class="fa fa-plus"></span></a></li>-->
        </ul>
    </div>
   <?php }
elseif($user->data()->access_level == 2){?>
    <div class="x-hnavigation">
        <div class="x-hnavigation-logo">
            <a href="dashboard.php">Family</a>
        </div>
        <ul>
            <li class="">
                <a href="doctor.php">Home</a>
            </li>
            <li class="">
                <a href="info.php?id=20">Medical Records</a>
            </li>
            <li class="xn-openable">
                <a href="#">Appointment</a>
                <ul>
                    <li><a href="form.php?id=14"><span class="fa fa-calendar-plus-o"></span>Set Appointment</a></li>
                    <li><a href="info.php?id=18"><span class="fa fa-th-list"></span>My Appointments</a></li>
                </ul>
            </li>
            <li class="xn-openable">
                <a href="#">Reports</a>
                <ul>
                    <li><a href="info.php?id=14"><span class="fa fa-file-text"></span>My Report</a></li>
                    <li><a href="#modal" data-toggle="modal"><span class="fa fa-file-text"></span>Clinic</a></li>
                </ul>
            </li>
            <li class="xn-openable">
                <a href="#">Stock</a>
                <ul>
                    <li><a href="info.php?id=8" target="_blank"><span class="fa fa-eye"></span>Lens Available</a></li>
                    <li><a href="info.php?id=9" target="_blank"><span class="fa fa-medkit"></span>Medicine Available</a></li>
                </ul>
            </li>
            <li class="xn-openable">
                <a href="#">Others</a>
                <ul>
                    <li><a href="form.php?id=15" ><span class="fa fa-plus-square"></span>Add Diagnosis</a></li>
                    <li><a href="info.php?id=19" ><span class="fa fa-sort-amount-desc"></span>Waiting List</a></li>
                    <li><a href="info.php?id=21" ><span class="fa fa-list-alt"></span>Diagnosis List</a></li>
                    <li class="xn-openable">
                        <a href="#"><span class="fa fa-file-archive-o"></span>Medical Forms</a>
                        <ul>
                            <li><a href="doctor.php"><span class="fa fa-file"></span>Normal Checkup</a></li>
                            <li><a href="special_form.php?id=1"><span class="fa fa-file-text"></span>Visual Therapy</a></li>
                            <li><a href="special_form.php?id=2"><span class="fa fa-file-o"></span> Cycloplegic Refraction</a></li>
                        </ul>
                    </li>
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
                    <?php if($user->data()->picture){?>
                        <img src="<?=$user->data()->picture?>">
                    <?php }else{?>
                        <img src="assets/images/users/no-image.jpg">
                    <?php }?>
                    <ul class="xn-drop-left animated zoomIn">
                        <li><a href="profile.php"><span class="fa fa-user"></span>My Profile</a></li>
                        <li><a href="info.php?id=11"><span class="fa fa-envelope"></span>Notifications</a></li>
                        <li><a href="lock.php"><span class="fa fa-lock"></span> Lock Screen</a></li>
                        <li><a href="logout.php" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span> Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="x-content-tabs">
        <ul>
            <li><a href="#main-tab" class="icon active"><span class="fa fa-desktop"></span></a></li>
            <!--<li><a href="#second-tab"><span class="fa fa-life-ring"></span><span>Second tab</span></a></li>
            <li><a href="#third-tab"><span class="fa fa-microphone"></span><span>Third tab</span></a></li>
            <li><a href="#new-tab" class="icon"><span class="fa fa-plus"></span></a></li>-->
        </ul>
    </div>
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
                                </select>
                            </div>
                            <label class="col-md-1 control-label">Report &nbsp;</label>
                            <div class="col-md-2">
                                <select name="category" class="form-control select" data-live-search="true">
                                    <option value="">Select Category</option>
                                    <option value="1">Clinic</option>
                                    <!--<option value="2">Doctor</option>
                                    <option value="3">Insurance</option>
                                    <option value="4">Cash</option>-->
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
<?php }
elseif($user->data()->access_level == 4){?>
    <div class="x-hnavigation">
        <div class="x-hnavigation-logo">
            <a href="#">Family</a>
        </div>
        <ul>
            <li class="">
                <a href="dashboard.php">Place Order</a>
            </li>
            <li class="xn-openable">
                <a href="#">Orders</a>
                <ul>
                    <li><a href="info.php?id=0"><span class="fa fa-cube"></span>Pending Order</a></li>
                    <li><a href="info.php?id=1"><span class="fa fa-life-ring"></span> Confirmed Orders</a></li>
                    <li><a href="info.php?id=2"><span class="fa fa-recycle"></span> Received Orders</a></li>
                    <li><a href="info.php?id=3"><span class="fa fa-database"></span> All Orders</a></li>
                </ul>
            </li>
            <li class="">
                <a href="info.php?id=10">Workshop Orders</a>
            </li>
            <li class="xn-openable">
                <a href="#">Stock</a>
                <ul>
                    <li><a href="form.php?id=4"><span class="fa fa-cube"></span>Add Lens</a></li>
                    <li><a href="form.php?id=5"><span class="fa fa-life-ring"></span> Add Frame</a></li>
                    <li><a href="form.php?id=10"><span class="fa fa-life-ring"></span> Add Frame Brand</a></li>
                    <li><a href="form.php?id=6"><span class="fa fa-recycle"></span> Add Medicine</a></li>
                    <li><a href="info.php?id=5"><span class="fa fa-database"></span> View Lens</a></li>
                    <li><a href="info.php?id=7"><span class="fa fa-database"></span> View Medicine</a></li>
                    <li><a href="info.php?id=6"><span class="fa fa-database"></span> View Frames</a></li>
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
                <?php if($override->getNews('order_request','status',4,'branch_id',$user->data()->branch_id)){?>
                    <div class="x-features-profile">
                        <span class="label label-danger">Notification</span>
                        <ul class="xn-drop-left animated zoomIn">
                            <li><a href="info.php?id=10" class="btn btn-info">&nbsp;&nbsp;Your have pending orders</a></li>
                        </ul>
                    </div>
                <?php }?>
                <div class="x-features-profile">
                    <?php if($user->data()->picture){?>
                        <img src="<?=$user->data()->picture?>">
                    <?php }else{?>
                        <img src="assets/images/users/no-image.jpg">
                    <?php }?>
                    <ul class="xn-drop-left animated zoomIn">
                        <li><a href="profile.php"><span class="fa fa-user"></span>My Profile</a></li>
                        <li><a href="info.php?id=11"><span class="fa fa-envelope"></span>Notifications</a></li>
                        <li><a href="lock.php"><span class="fa fa-lock"></span> Lock Screen</a></li>
                        <li><a href="logout.php" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span> Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="x-content-tabs">
        <ul>
            <li><a href="#main-tab" class="icon active"><span class="fa fa-desktop"></span></a></li>
            <!--<li><a href="#second-tab"><span class="fa fa-life-ring"></span><span>Second tab</span></a></li>
            <li><a href="#third-tab"><span class="fa fa-microphone"></span><span>Third tab</span></a></li>
            <li><a href="#new-tab" class="icon"><span class="fa fa-plus"></span></a></li>-->
        </ul>
    </div>
<?php }
elseif($user->data()->access_level == 5){?>
    <div class="x-hnavigation">
        <div class="x-hnavigation-logo">
            <a href="#">Family</a>
        </div>
        <ul>
            <li class="">
                <a href="dashboard.php">Defect Report</a>
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
                    <?php if($user->data()->picture){?>
                        <img src="<?=$user->data()->picture?>">
                    <?php }else{?>
                        <img src="assets/images/users/no-image.jpg">
                    <?php }?>
                    <ul class="xn-drop-left animated zoomIn">
                        <li><a href="profile.php"><span class="fa fa-user"></span>My Profile</a></li>
                        <li><a href="info.php?id=11"><span class="fa fa-envelope"></span>Notifications</a></li>
                        <li><a href="lock.php"><span class="fa fa-lock"></span> Lock Screen</a></li>
                        <li><a href="logout.php" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span> Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="x-content-tabs">
        <ul>
            <li><a href="#main-tab" class="icon active"><span class="fa fa-desktop"></span></a></li>
            <!--<li><a href="#second-tab"><span class="fa fa-life-ring"></span><span>Second tab</span></a></li>
            <li><a href="#third-tab"><span class="fa fa-microphone"></span><span>Third tab</span></a></li>
            <li><a href="#new-tab" class="icon"><span class="fa fa-plus"></span></a></li>-->
        </ul>
    </div>
<?php }
elseif($user->data()->access_level == 6){?>
    <div class="x-hnavigation">
        <div class="x-hnavigation-logo">
            <a href="#">Family</a>
        </div>
        <ul>
            <li class="">
                <a href="data.php">Home</a>
            </li>
            <li class="xn-openable">
                <a href="#">Other Entry</a>
                <ul>
                    <li><a href="form.php?id=13"><span class="fa fa-align-left"></span>Frames</a></li>
                </ul>
            </li>
            <li class="xn-openable">
                <a href="#">Details</a>
                <ul>
                    <li><a href="info.php?id=16"><span class="fa fa-align-left"></span>Entry Record</a></li>
                    <li><a href="info.php?id=17"><span class="fa fa-money"></span>Payment Record</a></li>
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
                    <?php if($user->data()->picture){?>
                        <img src="<?=$user->data()->picture?>">
                    <?php }else{?>
                        <img src="assets/images/users/no-image.jpg">
                    <?php }?>
                    <ul class="xn-drop-left animated zoomIn">
                        <li><a href="profile.php"><span class="fa fa-user"></span>My Profile</a></li>
                        <li><a href="info.php?id=11"><span class="fa fa-envelope"></span>Notifications</a></li>
                        <li><a href="lock.php"><span class="fa fa-lock"></span> Lock Screen</a></li>
                        <li><a href="logout.php" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span> Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="x-content-tabs">
        <ul>
            <li><a href="#main-tab" class="icon active"><span class="fa fa-desktop"></span></a></li>
            <!--<li><a href="#second-tab"><span class="fa fa-life-ring"></span><span>Second tab</span></a></li>
            <li><a href="#third-tab"><span class="fa fa-microphone"></span><span>Third tab</span></a></li>
            <li><a href="#new-tab" class="icon"><span class="fa fa-plus"></span></a></li>-->
        </ul>
    </div>
<?php }
elseif($user->data()->access_level == 7){?>
    <div class="x-hnavigation">
        <div class="x-hnavigation-logo">
            <a href="#">Family</a>
        </div>
        <ul>
            <li class="">
                <a href="form.php?id=12">Home</a>
            </li>
            <li class="xn-openable">
                <a href="#">Sales</a>
                <ul>
                    <li><a href="info.php?id=14"><span class="fa fa-align-left"></span>My Stock</a></li>
                    <li><a href="info.php?id=15"><span class="fa fa-list-alt"></span>Sales Details</a></li>
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
                    <?php if($user->data()->picture){?>
                        <img src="<?=$user->data()->picture?>">
                    <?php }else{?>
                        <img src="assets/images/users/no-image.jpg">
                    <?php }?>
                    <ul class="xn-drop-left animated zoomIn">
                        <li><a href="profile.php"><span class="fa fa-user"></span>My Profile</a></li>
                        <li><a href="info.php?id=11"><span class="fa fa-envelope"></span>Notifications</a></li>
                        <li><a href="lock.php"><span class="fa fa-lock"></span> Lock Screen</a></li>
                        <li><a href="logout.php" class="mb-control" data-box="#mb-signout"><span class="fa fa-sign-out"></span> Sign Out</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="x-content-tabs">
        <ul>
            <li><a href="#main-tab" class="icon active"><span class="fa fa-desktop"></span></a></li>
            <!--<li><a href="#second-tab"><span class="fa fa-life-ring"></span><span>Second tab</span></a></li>
            <li><a href="#third-tab"><span class="fa fa-microphone"></span><span>Third tab</span></a></li>
            <li><a href="#new-tab" class="icon"><span class="fa fa-plus"></span></a></li>-->
        </ul>
    </div>
<?php }?>