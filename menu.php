<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
?>
<ul class="x-navigation">
    <li class="xn-logo">
        <a href="admin.php">Family Eye Care</a>
        <a href="#" class="x-navigation-control"></a>
    </li>
    <li class="xn-profile">
        <a href="#" class="profile-mini">
            <img src="<?php if($user->data()->picture){echo $user->data()->picture;}else{echo 'assets/images/users/no-image.jpg';}?>" alt="<?=$user->data()->lastname?>"/>
        </a>
        <div class="profile">
            <div class="profile-image">
                <img src="<?php if($user->data()->picture){echo $user->data()->picture;}else{echo 'assets/images/users/no-image.jpg';}?>" alt="<?=$user->data()->lastname?>"/>
            </div>
            <div class="profile-data">
                <div class="profile-data-name"><?=$user->data()->firstname.' '.$user->data()->middlename.' '.$user->data()->lastname?></div>
                <div class="profile-data-title"><?=$user->data()->position?></div>
            </div>
            <div class="profile-controls">
                <a href="addInfo.php?id=33" class="profile-control-left"><span class="fa fa-info"></span></a>
                <a href="addInfo.php?id=15" class="profile-control-right"><span class="fa fa-envelope"></span></a>
            </div>
        </div>
    </li>
    <li class="xn-title">Navigation</li>
    <li class="xn-openable ">
        <a href="#"><span class="fa fa-dashboard"></span> <span class="xn-text">Dashboards</span></a>
        <ul>
            <li><a href="admin.php"><span class="fa fa-dashboard"></span>Home</a></li>
            <li><a href="addInfo.php?id=13"><span class="fa fa-plus-square"></span>Add Clinic Branch</a></li>
            <li><a href="information.php?id=10"><span class="fa fa-building"></span>View Clinic Branches</a></li>
        </ul>
    </li>
    <li class="xn-openable ">
        <a href="#"><span class="fa fa-file"></span> <span class="xn-text">Reports</span></a>
        <ul>
            <li><a href="report.php?id=0"><span class="fa fa-search"></span>Search Report</a></li>
        </ul>
    </li>
    <li class="xn-openable ">
        <a href="#"><span class="fa fa-file-text"></span> <span class="xn-text">Contracts</span></a>
        <ul>
            <li><a href="addInfo.php?id=23"><span class="fa fa-plus-square-o"></span>Add Contract</a></li>
            <li><a href="information.php?id=30&c=1"><span class="fa fa-users"></span>View Employees Contracts</a></li>
            <li><a href="information.php?id=30&c=2"><span class="fa fa-book"></span>View Others Contracts</a></li>
        </ul>
    </li>
    <li class="xn-openable ">
        <a href="#"><span class="fa fa-bank"></span> <span class="xn-text">Infrastructures</span></a>
        <ul>
            <li><a href="#"><span class="fa fa-plus-square-o"></span>Add Equipment</a></li>
            <li><a href="$"><span class="fa fa-users"></span>View Properties</a></li>
        </ul>
    </li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-user"></span> <span class="xn-text">Staff</span></a>
        <ul>
            <li><a href="addInfo.php?id=1"><span class="fa fa-user-plus"></span> Add New Staff</a></li>
            <li><a href="information.php?id=1"><span class="fa fa-gears"></span> Manage Staffs</a></li>
        </ul>
    </li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-users"></span> <span class="xn-text">Patient</span></a>
        <ul>
            <li><a href="addInfo.php?id=2"><span class="fa fa-user-plus"></span>Add New Patient</a></li>
            <li><a href="information.php?id=2"><span class="fa fa-list-alt"></span>View Patient</a></li>
            <li><a href="information.php?id=9"><span class="fa fa-medkit"></span>Medical Reports</a></li>
        </ul>
    </li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-envelope"></span> <span class="xn-text">SMS Bundle</span></a>
        <ul>
            <li><a href="addInfo.php?id=15"><span class="fa fa-pencil"></span>Compose SMS</a></li>
            <li><a href="addInfo.php?id=16"><span class="fa fa-pencil-square-o"></span>Compose Email</a></li>
            <li><a href="information.php?id=13"><span class="fa fa-comments-o"></span>Messages</a></li>
            <li><a href="information.php?id=14"><span class="fa fa-envelope-square"></span>Emails</a></li>
            <li><a href="addInfo.php?id=17"><span class="fa fa-plus-circle"></span>Add Bundles</a></li>
            <li><a href="#"><span class="fa fa-file-archive-o"></span>Bundles Records</a></li>
        </ul>
    </li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-dollar"></span> <span class="xn-text">Marketing</span></a>
        <ul>
            <li><a href="addInfo.php?id=18"><span class="fa fa-money"></span>Add Sales Batch</a></li>
            <li><a href="addInfo.php?id=21"><span class="fa fa-plus-square-o"></span>Add Sales Product</a></li>
            <li><a href="information.php?id=24"><span class="fa fa-align-center"></span>List Sales Products</a></li>
            <li><a href="information.php?id=19"><span class="fa fa-tasks"></span>View Sales Batch</a></li>
            <li><a href="information.php?id=23"><span class="fa fa-file-text-o"></span>View Sales Details</a></li>
        </ul>
    </li>
    <?php if($user->data()->employee_ID == 'FEC/337331'){?>
        <li class="xn-openable">
            <a href="#"><span class="fa fa-copy"></span> <span class="xn-text">Data Entry</span></a>
            <ul>
                <li><a href="addInfo.php?id=19"><span class="fa fa-money"></span>Set Price</a></li>
                <li><a href="addInfo.php?id=20"><span class="fa fa-plus-square-o"></span>Add Payment</a></li>
                <li><a href="information.php?id=21"><span class="fa fa-file-excel-o"></span>Payment Record</a></li>
                <li><a href="information.php?id=22"><span class="fa fa-windows"></span>Entry Control</a></li>
                <li><a href="information.php?id=20"><span class="fa fa-tasks"></span>View Data Entry Price</a></li>
            </ul>
        </li>
    <?php }?>
    <li class="xn-title">Components</li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-shopping-cart"></span> <span class="xn-text">Orders</span></a>
        <ul>
            <li><a href="addInfo.php?id=3"><span class="fa fa-plus"></span> Add Order</a></li>
            <li><a href="information.php?id=3"><span class="fa fa-list"></span> View Orders</a></li>
        </ul>
    </li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-eye"></span> <span class="xn-text">Lens</span></a>
        <ul>
            <li><a href="addInfo.php?id=4"><span class="fa fa-plus-square"></span>Add Lens</a></li>
            <li><a href="addInfo.php?id=5"><span class="fa fa-google-plus-square"></span>Add Lens Group</a></li>
            <li><a href="addInfo.php?id=6"><span class="fa fa-tags"></span>Add Lens Category</a></li>
            <li><a href="addInfo.php?id=7"><span class="fa fa-ticket"></span> Add Lens Type</a></li>
            <li><a href="information.php?id=4"><span class="fa fa-list"></span>  View Lens</a></li>
        </ul>
    </li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-binoculars"></span> <span class="xn-text">Frames</span></a>
        <ul>
            <li><a href="addInfo.php?id=8"><span class="fa fa-plus-square"></span> Add New Frame</a></li>
            <li><a href="addInfo.php?id=9"><span class="fa fa-bold"></span> Add Frame Brand</a></li>
            <li><a href="information.php?id=5"><span class="fa fa-list"></span> View Frames</a></li>
        </ul>
    </li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-medkit"></span> <span class="xn-text">Medicine</span></a>
        <ul>
            <li><a href="addInfo.php?id=10"><span class="fa fa-plus-square"></span> Add Medicine</a></li>
            <li><a href="information.php?id=6"><span class="fa fa-list"></span> View Medicines</a></li>
        </ul>
    </li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-bar-chart-o"></span> <span class="xn-text">Services</span></a>
        <ul>
            <li><a href="addInfo.php?id=11"><span class="fa fa-plus"></span>Add Test</a></li>
            <li><a href="addInfo.php?id=22"><span class="fa fa-plus-square-o"></span>Add Diagnosis</a></li>
            <li><a href="addInfo.php?id=12"><span class="fa fa-plus-square"></span>Add Product</a></li>
            <li><a href="addInfo.php?id=14"><span class="fa fa-plus-circle"></span>Add Insurance</a></li>
            <li><a href="information.php?id=25"><span class="fa fa-list-alt"></span>View Diagnosis</a></li>
            <li><a href="information.php?id=7"><span class="fa fa-list-alt"></span>View Test</a></li>
            <li><a href="information.php?id=8"><span class="fa fa-list"></span>View Product</a></li>
            <li><a href="information.php?id=11"><span class="fa fa-list"></span>View Insurance</a></li>
            <li><a href="information.php?id=26"><span class="fa fa-list-ol"></span>View Appointments</a></li>
        </ul>
    </li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-money"></span> <span class="xn-text">Finance</span></a>
        <ul>
            <li><a href="#">Pending Payments</a></li>
            <li><a href="#">Payment Records</a></li>
        </ul>
    </li>
    <li class="xn-openable">
        <a href="#"><span class="fa fa-database"></span> <span class="xn-text">Stock Records</span></a>
        <ul>
            <li><a href="#">Lens</a></li>
            <li><a href="#">Frames</a></li>
            <li><a href="#">Frames</a></li>
        </ul>
    </li>
</ul>