<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;$smsV=false;
$totalSMS=0;$bundle=null;$sms=0;$checkError=false;$attachment_file='';$attachment=null;$file_attach=null;
$check_frame=null;
if($user->isLoggedIn()) {
    if ($user->data()->access_level == 1) {
        if($_GET['id']) {
            if (Input::exists('post')) {
                if($_GET['id'] == 1) {
                    if (Input::get('addStaff')) {
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'clinic_branch' => array(
                                'required' => true,
                            ),
                            'firstname' => array(
                                'required' => true,
                                'min' => 3,
                            ),
                            'lastname' => array(
                                'required' => true,
                                'min' => 3,
                            ),
                            'position' => array(
                                'required' => true,
                            ),
                            'sex' => array(
                                'required' => true,
                            ),
                            'employee_ID' => array(
                                'required' => true,
                                'unique' => 'staff'
                            ),
                            'phone_number' => array(
                                'required' => true,
                                'unique' => 'staff'
                            ),
                        ));
                        if ($validate->passed()) {
                            $salt = Hash::salt(32);
                            $password = '123456';
                            switch(Input::get('position')){
                                case 'admin':
                                    $accessLevel = 1;
                                    break;
                                case 'Doctor':
                                    $accessLevel = 2;
                                    break;
                                case 'Receptionist':
                                    $accessLevel = 3;
                                    break;
                                case 'Store Keeper':
                                    $accessLevel = 4;
                                    break;
                                case 'Optician':
                                    $accessLevel = 5;
                                    break;
                                case 'IT Technician':
                                    $accessLevel = 1;
                                    break;
                                case 'Data Entry':
                                    $accessLevel = 6;
                                    break;
                                case 'Sales':
                                    $accessLevel = 7;
                                    break;
                            }
                            try {
                                $user->createRecord('staff', array(
                                    'firstname' => Input::get('firstname'),
                                    'middlename' => Input::get('middlename'),
                                    'lastname' => Input::get('lastname'),
                                    'position' => Input::get('position'),
                                    'employee_ID' => Input::get('employee_ID'),
                                    'gender' => Input::get('sex'),
                                    'password' => Hash::make($password, $salt),
                                    'salt' => $salt,
                                    'reg_date'=>date('Y-m-d'),
                                    'access_level' => $accessLevel,
                                    'phone_number' => Input::get('phone_number'),
                                    'branch_id' => Input::get('clinic_branch')
                                ));
                                $successMessage = 'Staff have been Successful Registered';

                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 2){
                    if(Input::get('addPatient')){
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
                            'health_insurance' => array(

                            ),
                            'dependent_no' => array(

                            ),
                            'address' => array(

                            ),
                            'occupation' => array(

                            ),
                            'email_address' => array(
                                'unique' => 'patient'
                            ),
                            'phone_number' => array(
                                'required' => true,
                            ),
                        ));
                        if ($validate->passed()) {
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
                                ));
                                $successMessage = 'Patient registered successful';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 3){
                    if(Input::get('addOrder')){
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
                                $status = $override->get('lens_orders','ref_no',Input::get('ref_no'));
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
                elseif($_GET['id'] == 33){
                    if(Input::get('changePassword')){
                        if (Input::exists('post')) {
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
                                    $salt = Hash::salt(32);
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
                elseif($_GET['id'] == 4) {
                    if (Input::get('lens_add')) {
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'clinic_branch' => array(
                                'required' => true,
                            ),
                            'lens_group' => array(
                                'required' => true,
                            ),
                            'lens_type' => array(
                                'required' => true,
                            ),
                            'lens_category' => array(
                                'required' => true,
                            ),
                            'lens_power' => array(
                                'required' => true,
                            ),
                            'price' => array(
                                'required' => true,
                            ),
                            'quantity' => array(
                                'required' => true,
                            ),
                            're_order' => array(
                                'required' => true,
                            ),

                        ));
                        if ($validate->passed()) {
                            $data = $override->selectData5('lens_power', 'lens_power', Input::get('lens_power'), 'cat_id', Input::get('lens_category'), 'type_id', Input::get('lens_type'), 'lens_id', Input::get('lens_group'),'branch_id',Input::get('clinic_branch'));
                            try {
                                if ($data){
                                    $quantity = $data[0]['quantity'];
                                    $totalLens = ($quantity) + (Input::get('quantity'));
                                    $user->updateRecord('lens_power', array(
                                        'quantity' => $totalLens,
                                        'price' => Input::get('price'),
                                        're_order' => Input::get('re_order'),
                                    ), $data[0]['id']);
                                    $successMessage = $successMessage = 'Lens Successful Added to Stock';
                                } else {
                                    $user->createRecord('lens_power', array(
                                        'lens_id' => Input::get('lens_group'),
                                        'type_id' => Input::get('lens_type'),
                                        'cat_id' => Input::get('lens_category'),
                                        'lens_power' => Input::get('lens_power'),
                                        'price' => Input::get('price'),
                                        'quantity' => Input::get('quantity'),
                                        're_order' => Input::get('re_order'),
                                        'branch_id' => Input::get('clinic_branch')
                                    ));
                                    $successMessage = $successMessage = 'Lens Successful Added to Stock..';
                                }
                                $user->createRecord('lens_record', array(
                                    'lens_id' => Input::get('lens_group'),
                                    'type_id' => Input::get('lens_type'),
                                    'cat_id' => Input::get('lens_category'),
                                    'lens_power' => Input::get('lens_power'),
                                    'price' => Input::get('price'),
                                    'quantity' => Input::get('quantity'),
                                    'enter_date' =>date('Y-m-d'),
                                    'staff_id' =>$user->data()->id,
                                    'branch_id' => Input::get('clinic_branch')
                                ));

                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 5){
                    if(Input::get('lens')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'name' => array(
                                'required' => true,
                            )
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('lens', array(
                                    'name' => Input::get('name'),
                                ));
                                $successMessage = 'Lens Group Successful Added to Stock';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 6){
                    if(Input::get('lens_cat')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'name' => array(
                                'required' => true,
                            ),
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('lens_category', array(
                                    'name' => Input::get('name'),
                                ));
                                $successMessage = 'Lens Category Successful Added to Stock';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 7){
                    if(Input::get('lens_type')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'name' => array(
                                'required' => true,
                            ),
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('lens_type', array(
                                    'name' => Input::get('name'),
                                ));
                                $successMessage = 'Lens Type Successful Added to Stock';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 8){
                    if(Input::get('frame')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'clinic_branch' => array(
                                'required' => true,
                            ),
                            'category' => array(
                                'required' => true,
                            ),
                            'brand' => array(
                                'required' => true,
                            ),
                            'model' => array(
                                'required' => true,
                            ),
                            'size' => array(
                                'required' => true,
                            ),
                            'quantity' => array(
                                'required' => true,
                            ),
                            'price' => array(
                                'required' => true,
                            ),
                        ));
                        if ($validate->passed()) {
                            try {
                                $model = $override->getNews('frame_model','model',Input::get('model'),'brand_id',Input::get('brand'));
                                if($model){$frame_model = $model[0]['id'];}else{
                                    $user->createRecord('frame_model', array(
                                        'model' => Input::get('model'),
                                        'brand_id' => Input::get('brand'),
                                    ));
                                    $frame_model = $override->get('frame_model','model',Input::get('model'))[0]['id'];
                                }
                                $getModel=$override->get('frame_model','model',Input::get('model'));
                                $check_frame = $override->selectData5('frames','brand_id',Input::get('brand'),'model',$getModel[0]['id'],'frame_size',Input::get('size'),'category',Input::get('category'),'branch_id',Input::get('clinic_branch'));
                                if($check_frame){
                                    $nw_frame = $check_frame[0]['quantity'] + Input::get('quantity');
                                    $user->updateRecord('frames',array(
                                        'quantity' => $nw_frame
                                    ),$check_frame[0]['id']);
                                    $successMessage = 'Frame Successful Added to Stock';
                                }else{
                                    $user->createRecord('frames', array(
                                        'model' => $frame_model,
                                        'frame_size' => Input::get('size'),
                                        'quantity' => Input::get('quantity'),
                                        'price' => Input::get('price'),
                                        'category' =>Input::get('category'),
                                        'brand_id' => Input::get('brand'),
                                        'branch_id' => Input::get('clinic_branch')
                                    ));
                                    $user->createRecord('frame_record', array(
                                        'brand' => Input::get('brand'),
                                        'model' => $frame_model,
                                        'frame_size' => Input::get('size'),
                                        'quantity' => Input::get('quantity'),
                                        'price' => Input::get('price'),
                                        'category' =>Input::get('category'),
                                        'record_date' => date('Y-m-d'),
                                        'staff_id' => $user->data()->id,
                                        'branch_id' => Input::get('clinic_branch'),
                                    ));
                                    $successMessage = 'Frame Successful Added to Stock';
                                }
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 9){
                    if(Input::get('add_brand')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'name' => array(
                                'required' => true,
                                'unique' =>'frame_brand'
                            )
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('frame_brand', array(
                                    'name' => Input::get('name'),
                                ));
                                $successMessage = 'Brand Successful Added';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 10){
                    if(Input::get('addMedicine')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'clinic_branch' => array(
                                'required' => true,
                            ),
                            'name' => array(
                                'required' => true,
                            ),
                            'quantity' => array(
                                'required' => true,
                            ),

                            'price' => array(
                                'required' => true,
                            ),
                            'man_date' => array(
                                'required' => true,
                            ),
                            'ex_date' => array(
                                'required' => true,
                            ),
                            're_order' => array(
                                'required' => true,
                            ),
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('medicine', array(
                                    'name' => Input::get('name'),
                                    'manufacture' => Input::get('manufacture'),
                                    'quantity' => Input::get('quantity'),
                                    'description'=>Input::get('description'),
                                    'man_date'=>Input::get('man_date'),
                                    'ex_date'=>Input::get('ex_date'),
                                    're_order'=>Input::get('re_order'),
                                    'price'=>Input::get('price'),
                                    'branch_id'=>Input::get('clinic_branch'),
                                ));
                                $successMessage = 'Medicine Successful Added';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 11){
                    if(Input::get('test')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'clinic_branch' => array(
                                'required' => true,
                            ),
                            'name' => array(
                                'required' => true,
                            ),
                            'price' => array(
                                'required' => true,
                            ),
                            'insurance_price' => array(
                                'required' => true,
                            ),
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('test_list', array(
                                    'name' => Input::get('name'),
                                    'cost' => Input::get('price'),
                                    'insurance_price' => Input::get('insurance_price'),
                                    'branch_id' => Input::get('clinic_branch')
                                ));
                                $successMessage = 'Service Successful Added';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 12){
                    if('add_product'){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'name' => array(
                                'required' => true,
                            )
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('products', array(
                                    'name' => Input::get('name'),
                                ));
                                $successMessage = 'Product Successful Added';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 13){
                    if('add_product'){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'name' => array(
                                'required' => true,
                            ),
                            'location' => array(

                            )
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('clinic_branch', array(
                                    'name' => Input::get('name'),
                                    'location' => Input::get('location'),
                                ));
                                $successMessage = 'Clinic Branch Successful Added';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 14){
                    if('add_insurance'){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'name' => array(
                                'required' => true,
                            ),
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('insurance', array(
                                    'name' => Input::get('name'),
                                ));
                                $successMessage = 'Insurance Successful Added';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 15){
                    if(Input::get('sendSMS')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'category' => array(
                                'required' => true,
                            ),
                            'subject' => array(
                                'required' => true,
                            ),
                            'message' => array(
                                'required' => true,
                            ),
                        ));
                        if (!empty($_FILES['attachment']["tmp_name"])) {
                            $attach_file = $_FILES['attachment']['type'];
                            if ($attach_file == "application/pdf" || $attach_file == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" || $attach_file == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                                $folderName = 'assets/attachments/';
                                $attachment_file = $folderName . basename($_FILES['attachment']['name']);
                                if (move_uploaded_file($_FILES['attachment']["tmp_name"], $attachment_file)) {
                                    $attachment=$attachment_file;
                                }else{$checkError=true;$errorMessage = 'not uploaded to a folder';}
                            }else{$checkError=true;$errorMessage = 'not supported format';}
                        }
                        if($attachment){$file_attach='<p><a href=read.php?path='.$attachment_file.' target="_blank">Click Here to download the attached file</a></p>';}
                        if ($validate->passed() && $checkError == false) {
                            $text = Input::get('message').$file_attach;
                            $noStaffM = count(Input::get('send_to'));if($noStaffM){if($user->validateBundle(Input::get('message'),$noStaffM)){}else{$smsV=true;}}
                            foreach(Input::get('send_to') as $sms){
                                if($sms == 'staff'){
                                    $staffNo=$override->getNo('staff');
                                    if($user->validateBundle($text,$staffNo)){
                                        $smsBundle=$override->getData('bundle_usage');
                                        $remainSms = $smsBundle[0]['sms'] - $user->countWords($text,$staffNo);
                                        $user->updateRecord('bundle_usage',array('sms' => $remainSms,),$smsBundle[0]['id']);
                                        foreach($override->getData('staff') as $staff){
                                            $user->sendSMS($staff['phone_number'],Input::get('message'));
                                           /*In future you should limit sms to be sent to all IT of Branch*/
                                                $user->createRecord('sms', array(
                                                    'subject' => Input::get('subject'),
                                                    'message' => Input::get('message').$file_attach,
                                                    'category' => Input::get('category'),
                                                    'sms_date' =>date('Y-m-d'),
                                                    'receiver_id' => $staff['id'],
                                                    'branch_id' => 0,
                                                    'attachment' =>$attachment_file,
                                                    'staff_id' =>$user->data()->id,
                                                ));
                                                $successMessage = 'SMS have been Sent Successful';
                                        }
                                    }
                                    else{
                                        $errorMessage = 'There is no sufficient sms to complete this request. Please buy sufficient amount of bulk sms and try again.';
                                    }
                                }
                                elseif($sms == 'patient'){
                                    $patientNo=$override->getNo('patient');
                                    $text = Input::get('message').$file_attach;
                                    if($user->validateBundle($text,$patientNo)){
                                        $smsBundle=$override->getData('bundle_usage');
                                        $remainSms = $smsBundle[0]['sms'] - $user->countWords($text,$patientNo);
                                        $user->updateRecord('bundle_usage',array('sms' => $remainSms,),$smsBundle[0]['id']);
                                        foreach($override->getData('patient') as $patient){
                                            $user->sendSMS($patient['phone_number'],Input::get('message'));
                                        }
                                        $user->createRecord('sms', array(
                                            'subject' => Input::get('subject'),
                                            'message' => Input::get('message').$file_attach,
                                            'category' => Input::get('category'),
                                            'sms_date' =>date('Y-m-d'),
                                            'receiver_id' => 0,
                                            'branch_id' => 0,
                                            'attachment' =>$attachment_file,
                                            'staff_id' =>$user->data()->id,
                                        ));
                                        $successMessage = 'SMS have been Sent Successful';
                                    }
                                    else{
                                        $errorMessage = 'There is no sufficient sms to complete this request. Please buy sufficient amount of bulk sms and try again.';
                                    }
                                }
                                else{ $phone=$override->get('staff','id',$sms);
                                    if($smsV == false){
                                        try {
                                            $smsBundle=$override->getData('bundle_usage');
                                            $remainSms = $smsBundle[0]['sms'] - $user->countWords($text,1);
                                            $user->updateRecord('bundle_usage',array('sms' => $remainSms,),$smsBundle[0]['id']);
                                            $user->sendSMS($phone[0]['phone_number'],Input::get('message'));
                                            $user->createRecord('sms', array(
                                                'subject' => Input::get('subject'),
                                                'message' => Input::get('message').$file_attach,
                                                'category' => Input::get('category'),
                                                'sms_date' =>date('Y-m-d'),
                                                'receiver_id' => $sms,
                                                'branch_id' => 0,
                                                'attachment' =>$attachment_file,
                                                'staff_id' =>$user->data()->id,
                                            ));
                                            $successMessage = 'SMS have been Sent Successful';
                                        } catch (Exception $e) {
                                            die($e->getMessage());
                                        }
                                    }else{$errorMessage = 'There is no sufficient sms to complete this request. Please buy sufficient amount of bulk sms and try again.';}
                                }

                            }
                        }
                        else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 16){
                    if(Input::get('sendEmail')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'subject' => array(
                                'required' => true,
                            ),
                            'message' => array(
                                'required' => true,
                            ),
                        ));
                        if (!empty($_FILES['attachment']["tmp_name"])) {
                            $attach_file = $_FILES['attachment']['type'];
                            if ($attach_file == "application/pdf" || $attach_file == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" || $attach_file == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet") {
                                $folderName = 'assets/attachments/';
                                $attachment_file = $folderName . basename($_FILES['attachment']['name']);
                                if (move_uploaded_file($_FILES['attachment']["tmp_name"], $attachment_file)) {
                                    $attachment=$attachment_file;
                                }else{$checkError=true;$errorMessage = 'not uploaded to a folder';}
                            }else{$checkError=true;$errorMessage = 'not supported format';}
                        }
                        if($attachment){$file_attach='<p><a href=read.php?path='.$attachment_file.' target="_blank">Click Here to download the attached file</a></p>';}
                        if ($validate->passed() && $checkError == false) {
                            foreach(Input::get('send_to') as $email){
                                if($email == 'staff'){
                                    foreach($override->getData('staff') as $staff){
                                        if(!$staff['access_level'] == 1){
                                            $user->createRecord('emails', array(
                                                'subject' => Input::get('subject'),
                                                'message' => Input::get('message').$file_attach,
                                                'category' => Input::get('category'),
                                                'email_date' =>date('Y-m-d'),
                                                'receiver_id' => $staff['id'],
                                                'branch_id' => 0,
                                                'attachment' =>$attachment_file,
                                                'staff_id' =>$user->data()->id,
                                            ));
                                            $successMessage = 'Email have been Sent Successful';
                                        }
                                    }
                                }else{
                                    try {
                                        $user->createRecord('emails', array(
                                            'subject' => Input::get('subject'),
                                            'message' => Input::get('message').$file_attach,
                                            'category' => Input::get('category'),
                                            'email_date' =>date('Y-m-d'),
                                            'receiver_id' => $email,
                                            'branch_id' => 0,
                                            'attachment' =>$attachment_file,
                                            'staff_id' =>$user->data()->id,
                                        ));
                                        $successMessage = 'Email have been Sent Successful';
                                    } catch (Exception $e) {
                                        die($e->getMessage());
                                    }
                                }

                            }
                        }
                        else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 17){
                    if (Input::get('addBundle')) {
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'sms_quantity' => array(
                                'required' => true,
                            ),
                            'price_per_sms' => array(
                                'required' => true,
                            ),
                            'total_price' => array(
                                'required' => true,
                            ),
                        ));
                        if ($validate->passed()) {
                            $bundle=$override->getData('bundle_usage');
                            try {
                                $user->createRecord('bundle_record', array(
                                    'quantity' => Input::get('sms_quantity'),
                                    'price_sms' => Input::get('price_per_sms'),
                                    'price' => Input::get('total_price'),
                                    'bundle_date' => date('Y-m-d'),
                                    'staff_id' => $user->data()->id,
                                ));
                                if($bundle){
                                    $sms=$bundle[0]['sms'];
                                    $totalSMS=$sms + Input::get('sms_quantity');
                                    $user->updateRecord('bundle_usage', array(
                                        'sms' => $totalSMS,
                                    ),$bundle[0]['id']);
                                }else{
                                    $user->createRecord('bundle_usage', array(
                                        'sms' => Input::get('sms_quantity'),
                                    ));
                                }
                                $successMessage = 'SMS Bundle have been Added Successful';

                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 18){
                    if(Input::get('addBatch')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'staff' => array(
                                'required' => true,
                            ),
                            'product' => array(
                                'required' => true,
                            ),
                            'price_per_product' => array(
                                'required' => true,
                            ),
                            'quantity' => array(
                                'required' => true,
                            ),
                        ));
                        if ($validate->passed()) {
                            try {
                                $sales = $override->selectData4('frame_sales', 'emp_id', Input::get('staff'), 'price_per', Input::get('price_per_product'), 'status', 0,'product_id',Input::get('product'));
                                if ($sales) {$quantity=$sales[0]['quantity'] + Input::get('quantity');
                                    $cost = (Input::get('quantity') * Input::get('price_per_product')) + $sales[0]['total_cost'];
                                    $user->updateRecord('frame_sales', array(
                                        'quantity' => $quantity,
                                        'total_cost' =>$cost,
                                    ),$sales[0]['id']);
                            }else{
                                $cost = Input::get('quantity') * Input::get('price_per_product');
                                $user->createRecord('frame_sales', array(
                                    'quantity' => Input::get('quantity'),
                                    'product_id' => Input::get('product'),
                                    'price_per' => Input::get('price_per_product'),
                                    'total_cost' =>$cost,
                                    'batch_date' => date('Y-m-d'),
                                    'emp_id' => Input::get('staff'),
                                    'staff_id' => $user->data()->id,
                                ));}
                                    $user->createRecord('frame_sales_rec', array(
                                        'quantity' => Input::get('quantity'),
                                        'product_id' => Input::get('product'),
                                        'price_per' => Input::get('price_per_product'),
                                        'total_cost' =>$cost,
                                        'batch_date' => date('Y-m-d'),
                                        'emp_id' => Input::get('staff'),
                                        'staff_id' => $user->data()->id,
                                    ));
                                $successMessage = 'Sales Batch have been Added Successful';

                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 19){
                    if(Input::get('addDataPrice')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'price' => array(
                                'required' => true,
                            )
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('data_entry_price', array(
                                    'price' => Input::get('price'),
                                ));
                                $successMessage = 'Data Entry Price have been Added Successful';

                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 20){
                    if(Input::get('addDataPay')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'staff' => array(
                                'required' => true,
                            ),
                            'price' => array(
                                'required' => true,
                            ),
                        ));
                        if ($validate->passed()) {
                            try {
                                $cst=$override->getData('data_entry_price');
                                $data=$override->get('data_payment','emp_id',Input::get('staff'));
                                $qty=ceil(Input::get('price')/$cst[0]['price']);
                                $user->createRecord('data_pay_rec', array(
                                    'quantity' => $qty,
                                    'price' => Input::get('price'),
                                    'pay_date' => date('Y-m-d'),
                                    'emp_id' => Input::get('staff'),
                                    'staff_id' => $user->data()->id,
                                ));
                                if($data){
                                    $payQty=$data[0]['pay_qty']+$qty;$payPrice=$data[0]['price']+Input::get('price');
                                    $user->updateRecord('data_payment',array(
                                        'pay_qty' => $payQty,
                                        'price' => $payPrice,
                                        'pay_date' => date('Y-m-d'),
                                        'staff_id' => $user->data()->id
                                    ),$data[0]['id']);
                                }else{
                                    $user->createRecord('data_payment', array(
                                        'quantity' => 0,
                                        'pay_qty' => $qty,
                                        'price' => Input::get('price'),
                                        'emp_id' => Input::get('staff'),
                                        'staff_id' => $user->data()->id,
                                    ));
                                }
                                $successMessage = 'Staff payment Added Successful';

                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 21){
                    if('add_sales_product'){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'name' => array(
                                'required' => true,
                            )
                        ));
                        if ($validate->passed()) {
                            try {
                                $user->createRecord('sales_product', array(
                                    'name' => Input::get('name'),
                                ));
                                $successMessage = 'Sales Product Successful Added';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        } else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 22){
                    if(Input::get('add_diagnosis')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'name' => array(
                                'required' => true,
                            )
                        ));
                        if ($validate->passed()){
                            try{
                                $user->createRecord('diagnosis', array(
                                    'name' => Input::get('name'),
                                ));
                                $successMessage = 'Diagnosis Added successful';
                            }
                            catch(PDOException $e){$e->getMessage();}
                        }else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                elseif($_GET['id'] == 23){
                    if(Input::get('contract')){
                        $validate = new validate();
                        $validate = $validate->check($_POST, array(
                            'name' => array(
                                'required' => true,
                            ),
                            'category' => array(
                                'required' => true,
                            ),
                            'start_date' => array(
                                'required' => true,
                            ),
                            'end_date' => array(
                                'required' => true,
                            ),
                        ));
                            if (!empty($_FILES['attachment']["tmp_name"])) {
                                $attach_file = $_FILES['attachment']['type'];
                                if ($attach_file == "application/pdf" || $attach_file == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" || $attach_file == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
                                    $folderName = 'attachments/contracts/';
                                $attachment_file = $folderName . basename($_FILES['attachment']['name']);
                                if (move_uploaded_file($_FILES['attachment']["tmp_name"], $attachment_file)) {
                                    $file = true;
                                } else {
                                    {
                                        $errorM = true;
                                        $errorMessage = 'Contract Not Uploaded ,';
                                    }
                                }
                            } else {
                                $errorM = true;
                                $errorMessage = 'None supported file format';
                            }//not supported format

                        if ($validate->passed() && $errorM == false){
                            try{
                                $user->createRecord('contracts', array(
                                    'name' => Input::get('name'),
                                    'category' => Input::get('category'),
                                    'start_date' => Input::get('start_date'),
                                    'end_date' => Input::get('end_date'),
                                    'description' => Input::get('description'),
                                    'attachment' => $attachment_file,
                                ));
                                $successMessage = 'Contract Added successful';
                            }
                            catch(PDOException $e){$e->getMessage();}
                        }else {
                            $pageError = $validate->errors();
                        }
                    }
                }
                else{Redirect::to('404.php');}
            }
        }else{Redirect::to('404.php');}
    }switch($user->data()->access_level){
        case 2:
            Redirect::to('doctor.php');
            break;
        case 3:
            Redirect::to('reception.php');
            break;
    }
}else{Redirect::to('index.php');}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- META SECTION -->
    <title> Siha Optical | Registration </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <!-- END META SECTION -->

    <!-- CSS INCLUDE -->
    <link rel="stylesheet" type="text/css" id="theme" href="css/theme-default.css"/>
    <!-- EOF CSS INCLUDE -->
</head>
<body>
<!-- START PAGE CONTAINER -->
<div class="page-container">

<!-- START PAGE SIDEBAR -->
<div class="page-sidebar">
    <!-- START X-NAVIGATION -->
    <?php include 'menu.php'?>
    <!-- END X-NAVIGATION -->
</div>
<!-- END PAGE SIDEBAR -->

<!-- PAGE CONTENT -->
<div class="page-content">

<!-- START X-NAVIGATION VERTICAL -->
<?php include 'topBar.php'?>
<!-- END X-NAVIGATION VERTICAL -->

<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="#">Home</a></li>
    <li class="active">Dashboard</li>
</ul>
<!-- END BREADCRUMB -->

<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <div class="col-md-12">
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
        <?php }?>
        <?php if($_GET['id'] == 1){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Register New Staff Member</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Staff Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Branch</label>
                                <div class="col-md-10">
                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                        <option value="">Select Clinic Branch</option>
                                        <?php foreach($override->getData('clinic_branch') as $branch){?>
                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">First Name</label>
                                <div class="col-md-10">
                                    <input type="text" name="firstname" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Middle Name</label>
                                <div class="col-md-10">
                                    <input type="text" name="middlename" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Last Name</label>
                                <div class="col-md-10">
                                    <input type="text" name="lastname" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Sex</label>
                                <div class="col-md-10">
                                    <select name="sex" class="form-control select" >
                                        <option value="">Select sex</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Position</label>
                                <div class="col-md-10">
                                    <select name="position" class="form-control select">
                                        <option value="">Select position</option>
                                        <option value="Doctor">Doctor</option>
                                        <option value="Receptionist">Receptionist</option>
                                        <option value="IT Technician">IT Technician</option>
                                        <option value="Store Keeper">Store Keeper</option>
                                        <option value="Optician">Optician</option>
                                        <option value="Data Entry">Data Entry</option>
                                        <option value="Sales">Sales</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Employee ID</label>
                                <div class="col-md-10">
                                    <input type="text" name="employee_ID" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Phone Number</label>
                                <div class="col-md-10">
                                    <input type="text" name="phone_number" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="addStaff" value="Add Staff" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 2){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Register New Patient Member</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Patient Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">First Name</label>
                                <div class="col-md-10">
                                    <input type="text" name="firstname" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Surname</label>
                                <div class="col-md-10">
                                    <input type="text" name="surname" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Sex</label>
                                <div class="col-md-10">
                                    <select name="sex" class="form-control select" >
                                        <option value="">Select sex</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Age</label>
                                <div class="col-md-10">
                                    <input type="number" min="1" name="age" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Health Insurance</label>
                                <div class="col-md-10">
                                    <input type="text" name="health_insurance" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Dependent No.</label>
                                <div class="col-md-10">
                                    <input type="text" name="dependent_no" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Address</label>
                                <div class="col-md-10">
                                    <input type="text" name="address" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Occupation</label>
                                <div class="col-md-10">
                                    <input type="text" name="occupation" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Email Address</label>
                                <div class="col-md-10">
                                    <input type="text" name="email_address" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Phone Number</label>
                                <div class="col-md-10">
                                    <input type="text" name="phone_number" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="addPatient" value="Add Patient" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 3){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Register New Order</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Patient Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-1 control-label">Ref No.</label>
                                <div class="col-md-2">
                                    <input type="text" name="ref_no" class="form-control" value="" />
                                </div>
                                <div class="col-md-1">
                                    <label class="check"><input type="radio" class="iradio" name="material" value="CR" checked/> CR</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="check"><input type="radio" class="iradio" name="material" VALUE="Glass"/> Glass</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="check"><input type="radio" class="iradio" name="eye" id="BE" value="both" checked/> Both Eyes</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="check"><input type="radio" class="iradio" name="eye" id="RE" value="RE" /> Right Eye</label>
                                </div>
                                <div class="col-md-2">
                                    <label class="check"><input type="radio" class="iradio" name="eye" id="LE" value="LE" /> Left Eye</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-1 control-label">Product</label>
                                <div class="col-md-10">
                                    <select name="product" class="form-control select" data-live-search="true">
                                        <option value="">Select Product</option>
                                        <?php foreach($override->getData('products') as $product){?>
                                            <option value="<?=$product['id']?>"><?=$product['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-1 control-label">Order</label>
                                <div class="col-md-10">
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
                                    <div class="table-responsive" >
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
                                <label class="col-md-1 control-label">Details</label>
                                <div class="col-md-10">
                                    <textarea name="details" class="form-control" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="addOrder" value="Add Order" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 33){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>My Profile Information</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
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
                                        <label class="col-md-4 col-xs-5 control-label">Reg. On</label>
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
        <?php }
        elseif($_GET['id'] == 4){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Lens</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Lens Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Clinic Branch</label>
                                <div class="col-md-10">
                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                        <option value="">Select Clinic Branch</option>
                                        <?php foreach($override->getData('clinic_branch') as $branch){?>
                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Lens Group</label>
                                <div class="col-md-10">
                                    <select name="lens_group" class="form-control select" data-live-search="true">
                                        <option value="">Select group</option>
                                        <?php foreach($override->getData('lens') as $lens){?>
                                            <option value="<?=$lens['id']?>"><?=$lens['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Lens Type</label>
                                <div class="col-md-10">
                                    <select name="lens_type" class="form-control select" data-live-search="true">
                                        <option value="">Select Type</option>
                                        <?php foreach($override->getData('lens_type') as $lensType){?>
                                            <option value="<?=$lensType['id']?>"><?=$lensType['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Lens Category</label>
                                <div class="col-md-10">
                                    <select name="lens_category" class="form-control select" data-live-search="true">
                                        <option value="">Select category</option>
                                        <?php foreach($override->getData('lens_category') as $lensCat){?>
                                            <option value="<?=$lensCat['id']?>"><?=$lensCat['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Lens Power</label>
                                <div class="col-md-10">
                                    <input type="text" name="lens_power" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Price</label>
                                <div class="col-md-10">
                                    <input type="number" name="price" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Quantity</label>
                                <div class="col-md-10">
                                    <input type="number" min="1" name="quantity" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Re-Order Level</label>
                                <div class="col-md-10">
                                    <input type="number" min="1" name="re_order" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="lens_add" value="Add Lens" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 5){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Lens Group</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Lens Group Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Lens Group</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="lens" value="Add Lens Group" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 6){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Lens Category</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Lens Category Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Lens Category</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="lens_cat" value="Add Lens Category" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 7){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Lens Type</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Lens Type Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Lens Type</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="lens_type" value="Add Lens Category" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 8){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Frame </h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Frame Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Clinic Branch</label>
                                <div class="col-md-10">
                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                        <option value="">Select Clinic Branch</option>
                                        <?php foreach($override->getData('clinic_branch') as $branch){?>
                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Select Category &nbsp;</label>
                                <div class="col-md-10">
                                    <select name="category" class="form-control select" data-live-search="true">
                                        <option value="">Select Category</option>
                                        <option value="1">Frame</option>
                                        <option value="2">Sun Glass</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Frame Brand</label>
                                <div class="col-md-10">
                                    <select name="brand" class="form-control select" data-live-search="true">
                                        <option value="">Select Brand</option>
                                        <?php foreach($override->getData('frame_brand') as $brand){?>
                                            <option value="<?=$brand['id']?>"><?=$brand['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Frame Model</label>
                                <div class="col-md-10">
                                    <input type="text" name="model" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Frame Size</label>
                                <div class="col-md-10">
                                    <input type="text" name="size" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Price</label>
                                <div class="col-md-10">
                                    <input type="number" name="price" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Quantity</label>
                                <div class="col-md-10">
                                    <input type="number" min="0" name="quantity" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="frame" value="Add Frame" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 9){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Frame Brand</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Brand Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Brand Name</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="add_brand" value="Add Brand" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 10){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Medicine</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Medicine Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Clinic Branch</label>
                                <div class="col-md-10">
                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                        <option value="">Select Clinic Branch</option>
                                        <?php foreach($override->getData('clinic_branch') as $branch){?>
                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Medicine Name</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Manufactured By</label>
                                <div class="col-md-10">
                                    <input type="text" name="manufacture" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Description</label>
                                <div class="col-md-10">
                                   <textarea name="description" rows="4" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Price</label>
                                <div class="col-md-10">
                                    <input type="number" name="price" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Quantity</label>
                                <div class="col-md-10">
                                    <input type="number" min="0" name="quantity" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Manufactured Date</label>
                                <div class="col-md-4">
                                    <input type="text" name="man_date" class="form-control datepicker" value="Select date">
                                </div>
                                <label class="col-md-2 control-label">Expired Date</label>
                                <div class="col-md-4">
                                    <input type="text" name="ex_date" class="form-control datepicker" value="Select date">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Re-Order Level</label>
                                <div class="col-md-10">
                                    <input type="number" min="0" name="re_order" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="addMedicine" value="Add Medicine" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 11){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Test</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Test Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Branch</label>
                                <div class="col-md-10">
                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                        <option value="">Select Clinic Branch</option>
                                        <?php foreach($override->getData('clinic_branch') as $branch){?>
                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Test name</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Cash Cost</label>
                                <div class="col-md-10">
                                    <input type="number" name="price" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Insurance Cost</label>
                                <div class="col-md-10">
                                    <input type="number" name="insurance_price" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="test" value="Add Test" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 12){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Product</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Product Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Product name</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="add_product" value="Add Product" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 13){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Clinic Branch</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Clinic Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Branch name</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Location</label>
                                <div class="col-md-10">
                                    <input type="text" name="location" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="add_branch" value="Add Clinic Branch" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 14){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Insurance</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Insurance Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Insurance Name</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="add_insurance" value="Add Insurance" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 15){?>
            <div class="content-frame">
                <!-- START CONTENT FRAME TOP -->
                <div class="content-frame-top">
                    <div class="page-title">
                        <h2><span class="fa fa-pencil"></span> Compose SMS</h2>
                    </div>

                    <div class="pull-right">
                        <button class="btn btn-default"><span class="fa fa-cogs"></span> Settings</button>
                        <button class="btn btn-default"><span class="fa fa-floppy-o"></span> Save</button>
                        <button class="btn btn-default content-frame-left-toggle"><span class="fa fa-bars"></span></button>
                    </div>
                </div>
                <!-- END CONTENT FRAME TOP -->

                <!-- START CONTENT FRAME LEFT -->
                <div class="content-frame-left">
                    <div class="block">
                        <div class="list-group border-bottom">
                            <a href="information.php?id=13" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
                            <a href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted <span class="badge badge-default"></span></a>
                        </div>
                    </div>

                </div>
                <!-- END CONTENT FRAME LEFT -->

                <!-- START CONTENT FRAME BODY -->
                <div class="content-frame-body">
                    <div class="block">
                        <form role="form" class="form-horizontal" enctype="multipart/form-data" METHOD="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Category :</label>
                                <div class="col-md-10">
                                    <select name="category" class="form-control select" title="Select Category">
                                        <option value="">Select Category</option>
                                        <option value="staff">Staff</option>
                                        <option value="patient">Patient</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Send To:</label>
                                <div class="col-md-10">
                                    <select name="send_to[]" multiple class="form-control select" data-live-search="true" title="Send SMS To" required="">
                                        <option value="staff">All Staff</option>
                                        <option value="patient">All Patient</option>
                                        <?php foreach($override->getData('staff') as $staff){?>
                                            <option value="<?=$staff['id']?>"><?=$staff['firstname'].' '.$staff['middlename'].' '.$staff['lastname']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Subject:</label>
                                <div class="col-md-10">
                                    <input type="text" name="subject" class="form-control" value="" placeholder="Enter SMS Subject"/>
                                </div>
                            </div>
                            <!--<div class="form-group">
                                <label class="col-md-2 control-label">Attachments:</label>
                                <div class="col-md-10">
                                    <input type="file" name="attachment" class="file" data-filename-placement="inside"/>
                                </div>
                            </div>-->
                            <div class="form-group">
                                <div class="col-md-12">
                                    <textarea name="message" rows="10" class="form-control" placeholder="Message"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <button class="btn btn-default"><span class="fa fa-trash-o"></span> Delete Draft</button>
                                    </div>
                                    <div class="pull-right">
                                        <input type="submit" name="sendSMS" value="Send SMS" class="btn btn-danger">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <!-- END CONTENT FRAME BODY -->
            </div>
        <?php }
        elseif($_GET['id'] == 16){?>
            <div class="content-frame">
                <!-- START CONTENT FRAME TOP -->
                <div class="content-frame-top">
                    <div class="page-title">
                        <h2><span class="fa fa-pencil"></span> Compose Email</h2>
                    </div>

                    <div class="pull-right">
                        <button class="btn btn-default"><span class="fa fa-cogs"></span> Settings</button>
                        <button class="btn btn-default"><span class="fa fa-floppy-o"></span> Save</button>
                        <button class="btn btn-default content-frame-left-toggle"><span class="fa fa-bars"></span></button>
                    </div>
                </div>
                <!-- END CONTENT FRAME TOP -->

                <!-- START CONTENT FRAME LEFT -->
                <div class="content-frame-left">
                    <div class="block">
                        <div class="list-group border-bottom">
                            <a href="#" class="list-group-item"><span class="fa fa-inbox"></span> Inbox <span class="badge badge-success"></span></a>
                            <a href="information.php?id=14" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
                            <a href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted <span class="badge badge-default"></span></a>
                        </div>
                    </div>

                </div>
                <!-- END CONTENT FRAME LEFT -->

                <!-- START CONTENT FRAME BODY -->
                <div class="content-frame-body">
                    <div class="block">
                        <form role="form" class="form-horizontal" enctype="multipart/form-data" METHOD="post">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <button class="btn btn-default"><span class="fa fa-trash-o"></span> Delete Draft</button>
                                    </div>
                                    <div class="pull-right">
                                        <input type="submit" name="sendEmail" value="Send Email" class="btn btn-danger">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Category :</label>
                                <div class="col-md-10">
                                    <select name="category" class="form-control select" title="Select Category">
                                        <option value="staff">Staff</option>
                                        <option value="patient">Patient</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Send To:</label>
                                <div class="col-md-10">
                                    <select name="send_to[]" multiple class="form-control select" data-live-search="true" title="Send Emails To" required="">
                                        <option value="staff">All Staff</option>
                                        <option value="patient">All Patient</option>
                                        <?php foreach($override->getData('staff') as $staff){?>
                                            <option value="<?=$staff['id']?>"><?=$staff['firstname'].' '.$staff['middlename'].' '.$staff['lastname']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Subject:</label>
                                <div class="col-md-10">
                                    <input type="text" name="subject" class="form-control" value="" placeholder="Enter Email Subject"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Attachments:</label>
                                <div class="col-md-10">
                                    <input type="file" name="attachment" class="file" data-filename-placement="inside"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <textarea name="message" class="summernote_email"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <button class="btn btn-default"><span class="fa fa-trash-o"></span> Delete Draft</button>
                                    </div>
                                    <div class="pull-right">
                                        <input type="submit" name="sendEmail" value="Send Email" class="btn btn-danger">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
                <!-- END CONTENT FRAME BODY -->
            </div>
        <?php }
        elseif($_GET['id'] == 17){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Register New Bundle</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Bundle Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">SMS Quantity</label>
                                <div class="col-md-10">
                                    <input type="number" name="sms_quantity" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Price Per SMS</label>
                                <div class="col-md-10">
                                    <input type="number" name="price_per_sms" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Total Price</label>
                                <div class="col-md-10">
                                    <input type="number" name="total_price" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="addBundle" value="Add Bundle" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 18){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Sales Batch</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Batch Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Staff</label>
                                <div class="col-md-10">
                                    <select name="staff" class="form-control select" data-live-search="true" title="Select Staff" required="">
                                        <option value="">Select Staff</option>
                                        <?php foreach($override->get('staff','access_level',7) as $staff){?>
                                            <option value="<?=$staff['id']?>"><?=$staff['firstname'].' '.$staff['middlename'].' '.$staff['lastname']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Product</label>
                                <div class="col-md-10">
                                    <select name="product" class="form-control select" data-live-search="true">
                                        <option value="">Select Product</option>
                                        <?php foreach($override->getData('sales_product') as $product){?>
                                            <option value="<?=$product['id']?>"><?=$product['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Quantity</label>
                                <div class="col-md-10">
                                    <input type="number" name="quantity" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Price per Product</label>
                                <div class="col-md-10">
                                    <input type="number" name="price_per_product" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="addBatch" value="Add Sales Batch" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 19){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Data Entry Price</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Payment Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Price Per Data</label>
                                <div class="col-md-10">
                                    <input type="number" name="price" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="addDataPrice" value="Add Data Entry Price" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 20){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Data Entry Payment</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Payment Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Staff</label>
                                <div class="col-md-10">
                                    <select name="staff" class="form-control select" data-live-search="true" title="Select Staff" required="">
                                        <option value="">Select Staff</option>
                                        <?php foreach($override->get('staff','access_level',6) as $staff){?>
                                            <option value="<?=$staff['id']?>"><?=$staff['firstname'].' '.$staff['middlename'].' '.$staff['lastname']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Price</label>
                                <div class="col-md-10">
                                    <input type="number" name="price" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="addDataPay" value="Add Payment" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 21){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Sales Product</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Sales Product Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Product name</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="add_sales_product" value="Add Product" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 22){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Diagnosis</h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Diagnosis Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Diagnosis Name</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="add_diagnosis" value="Add Diagnosis" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }
        elseif($_GET['id'] == 23){?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title-box">
                        <h3>Add Contract </h3>
                        <span></span>
                    </div>
                    <ul class="panel-controls" style="margin-top: 2px;">
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="fa fa-cog"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span> Collapse</a></li>
                                <li><a href="#" class="panel-remove"><span class="fa fa-times"></span> Remove</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Contract Details</h3>
                        <form class="form-horizontal" enctype="multipart/form-data" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Contract Name </label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Select Category &nbsp;</label>
                                <div class="col-md-10">
                                    <select name="category" class="form-control select" data-live-search="true">
                                        <option value="">Select Category</option>
                                        <option value="1">Employee</option>
                                        <option value="2">Others</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-0 control-label"></label>
                                <label class="col-md-2 control-label">Start Date </label>
                                <div class="col-md-4">
                                    <input name="start_date" type="text" class="form-control datepicker" placeholder="DATE" value="">
                                </div>
                                <label class="col-md-1 control-label">End Date </label>
                                <div class="col-md-4">
                                    <input name="end_date" type="text" class="form-control datepicker" placeholder="DATE" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label"></label>
                                <div class="col-md-10">
                                    <textarea name="description" rows="4" class="form-control" placeholder="Other Description"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label"></label>
                                <div class="col-md-10">
                                    <input type="file"  name="attachment"  />
                                </div>
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="contract" value="Add Contract" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php }?>
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
<script type="text/javascript" src="js/plugins/summernote/summernote.js"></script>
<script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
<script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
<script type="text/javascript" src="js/plugins/scrolltotop/scrolltopcontrol.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>

<script type="text/javascript" src="js/plugins/morris/raphael-min.js"></script>
<script type="text/javascript" src="js/plugins/morris/morris.min.js"></script>
<script type="text/javascript" src="js/plugins/rickshaw/d3.v3.js"></script>
<script type="text/javascript" src="js/plugins/rickshaw/rickshaw.min.js"></script>
<script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js'></script>
<script type='text/javascript' src='js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js'></script>
<script type='text/javascript' src='js/plugins/bootstrap/bootstrap-datepicker.js'></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
<script type="text/javascript" src="js/plugins/owl/owl.carousel.min.js"></script>

<script type="text/javascript" src="js/plugins/moment.min.js"></script>
<script type="text/javascript" src="js/plugins/daterangepicker/daterangepicker.js"></script>
<!-- END THIS PAGE PLUGINS-->

<!-- START TEMPLATE -->
<script type="text/javascript" src="js/settings.js"></script>

<script type="text/javascript" src="js/plugins.js"></script>
<script type="text/javascript" src="js/actions.js"></script>

<script type="text/javascript" src="js/demo_dashboard.js"></script>
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

<script>
if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    $(document).ready(function(){
        $('#BE').change(function(){
            var getEye1 = $(this).val();
            $.ajax({
                url:"process.php?content=eye",
                method:"GET",
                data:{eye:getEye1},
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
        $('#cat_g').change(function(){
            var cat = $(this).val();
            $.ajax({
                url:"process.php?content=cat_g",
                method:"GET",
                data:{cat:cat},
                dataType:"text",
                success:function(data){
                    $('#toooo').html(data);
                }
            });
        });
    });
</script>
</body>

</html>