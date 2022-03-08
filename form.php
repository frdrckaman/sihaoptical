<?php
require_once'php/core/init.php';
$user = new User();
$override = new OverideData();
$pageError = null;$successMessage = null;$errorM = false;$errorMessage = null;$accessLevel=0;$lensError=false;
$total_orders=0;$pending=0;$confirmed=0;$received=0;$heading=null;$totalCost=0;$lensCost=0;$checkError=false;
$frameCost=0;$checkupCost=0;$totalMedCost=0;$medCn[]=null;$pr[]=null;$qt[]=null;$mID[]=null;$chckLens=false;
$medID[]=null;$bitError=false;$ins=0;$lensNotification=null;$lensTotalCost=0;$pay=0;$insurancePay=0;$insurance=0;
$branch_id=null;$dis=0;$srch=null;$searchValue=null;
if($user->isLoggedIn()){
    if($user->data()->access_level >1 && $user->data()->access_level <= 7){
        switch($_GET['id']){
            case 0:
                $heading = 'CASH PAYMENT';
                $patient = $override->get('patient','id',$_GET['p']);
                $checkupCost = $override->getNews('payment','patient_id',$_GET['p'],'status',0);
                break;
            case 33:
                $heading = 'RETURN PATIENT';
                break;
            case 1:
                $heading = 'INSURANCE PAYMENT';
                $patient = $override->get('patient','id',$_GET['p']);
                $checkupCost = $override->getNews('payment','patient_id',$_GET['p'],'status',0);
                break;
            case 3:
                $heading = 'PENDING PAYMENT';
                $patient = $override->get('patient','id',$_GET['p']);
                $checkupCost = $override->getNews('payment','patient_id',$_GET['p'],'status',2);
//if($checkupCost && !$checkupCost[0]['cost'] == $checkupCost[0]['payment']){
                $totalCost = $checkupCost[0]['cost']-($checkupCost[0]['payment'] + $checkupCost[0]['discount']);
                $dsc=$checkupCost[0]['discount'];
                $py=$checkupCost[0]['payment'];
// }
                break;
            case 20:
                $heading = 'PATIENT MEDICAL RECORDS';
                break;
        }
        if(Input::exists('post')){
            if($_GET['id'] == 0 || $_GET['id'] == 1){
                if(Input::get('calc')){$x=1;
                    $no=$override->getCounted('prescription','patient_id',$_GET['p'],'status',0,'checkup_id',$_GET['c']);
                    while($x<=$no){
                        $getM = 'med'.$x;
                        if(Input::get($getM)){$medCn[$x]=1;
                            $pMed = $override->selectData4('prescription','patient_id',$_GET['p'],'status',0,'medicine_id',Input::get($getM),'checkup_id',$_GET['c']);
                            $pr[$x] = $pMed[0]['id'];$medID[$x] = $pMed[0]['medicine_id'];
                            $medP = $override->selectData4('prescription','medicine_id',Input::get($getM),'patient_id',$_GET['p'],'status',0,'checkup_id',$_GET['c']);
                            $qt[$x] = $medP[0]['quantity'];
                            $medC = $override->get('medicine','id',Input::get($getM));
                            $mID[$x] = $medC[0]['id'];
                            $medCost = $medP[0]['quantity'] * $medC[0]['price'];
                            $totalMedCost +=  $medCost;
                        }else{$medCn[$x]=0;}
                        $x++;}
                    $totalLensPrice=0;
                    if($user->data()->branch_id == 9){$branch_id = 8;}else{$branch_id=$user->data()->branch_id;}
                    if(Input::get('eye') == 'BE' && Input::get('lens_cat')){$l_power=$override->get('lens_power','id',Input::get('lens_power'));
                        $checkLens=$override->selectData4('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power[0]['lens_power']);
                        //print_r(Input::get('lens_cat'));echo' k';print_r(Input::get('lensType'));echo' l';print_r(Input::get('lensGroup'));echo' ';print_r($l_power[0]['lens_power']);
                        //print_r($checkLens[0]['price']);
                        if($checkLens){
                            //Changes made here not to check the quantity
                       // if($checkLens[0]['quantity'] >= 2){
                            $lensTotalCost = $checkLens[0]['price'] * 2;
                        //}else{$errorMessage='No Sufficient Amount of Lens in stock to Fulfill this Request.Please place an order for it';}
                    }else{$errorMessage='This Lens is not Available in store.Please Place an order for it';}
                    }
                    elseif(Input::get('eye') == 'RE' && Input::get('lens_cat')){
                        $l_power1=$override->get('lens_power','id',Input::get('lens_power'));
                        $l_power2=$override->get('lens_power','id',Input::get('lens_power_other'));
                        $checkLens1=$override->selectData4('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power1[0]['lens_power']);
                        $checkLens2=$override->selectData4('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power2[0]['lens_power']);
                        if($checkLens1 && $checkLens2){
                            //if($l_power1[0]['quantity'] >= 1 && $l_power2[0]['quantity'] >= 1){
                                $lensTotalCost = $checkLens1[0]['price'] + $checkLens2[0]['price'];
                           // }else{$errorMessage='No Sufficient Amount of Lens in stock to Fulfill this Request.Please place an order for it';}
                        }else{$errorMessage='This Lens is not Available in store.Please Place an order for it';}
                    }
                    //if($lensCost){$lensTotalCost=$lensCost[0]['price'] * 2;}
                    //$lensCost = Input::get('lens_cost');
                    $frameCost = Input::get('frame_cost');
                    $totalCost = $lensTotalCost + $frameCost + Input::get('checkupCost') + $totalMedCost;
                }
                elseif(Input::get('submitCash')){$x=1;
                    $no=$override->getCounted('prescription','patient_id',$_GET['p'],'status',0,'checkup_id',$_GET['c']);
                    if($_GET['id'] == 0){$ins=0;}elseif($_GET['id'] == 1){$ins=1;}
                    while($x<=$no){
                        $getM = 'med'.$x;
                        if(Input::get($getM)){$medCn[$x]=1;
                            $pMed = $override->selectData4('prescription','patient_id',$_GET['p'],'status',0,'medicine_id',Input::get($getM),'checkup_id',$_GET['c']);
                            $pr[$x] = $pMed[0]['id'];$medID[$x] = $pMed[0]['medicine_id'];
                            $medP = $override->selectData4('prescription','medicine_id',Input::get($getM),'patient_id',$_GET['p'],'status',0,'checkup_id',$_GET['c']);
                            $qt[$x] = $medP[0]['quantity'];
                            $medC = $override->get('medicine','id',Input::get($getM));
                            $mID[$x] = $medC[0]['id'];
                            $medCost = $medP[0]['quantity'] * $medC[0]['price'];
                            $totalMedCost +=  $medCost;
                        }else{$medCn[$x]=0;}
                        $x++;}

                    $frameCost = Input::get('frame_cost');
                    if($user->data()->branch_id == 9){$branch_id = 8;}else{$branch_id=$user->data()->branch_id;}
                    if(Input::get('eye') == 'BE' && Input::get('lens_cat')){$l_power=$override->get('lens_power','id',Input::get('lens_power'));
                        $checkLens=$override->selectData4('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power[0]['lens_power']);
                        if($checkLens){
                            //change here so that it wont check quantity of lens but check price only
                            //if($l_power[0]['quantity'] >= 2){
                                $lensTotalCost = $checkLens[0]['price'] * 2;
                           // }else{$errorMessage='No Sufficient Amount of Lens in stock to Fulfill this Request.Please place an order for it';$chckLens=true;}
                        }else{$errorMessage='This Lens is not Available in store.Please Place an order for it';$chckLens=true;}
                    }
                    elseif(Input::get('eye') == 'RE' && Input::get('lens_cat')){
                        $l_power1=$override->get('lens_power','id',Input::get('lens_power'));
                        $l_power2=$override->get('lens_power','id',Input::get('lens_power_other'));
                        $checkLens1=$override->selectData4('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power1[0]['lens_power']);
                        $checkLens2=$override->selectData4('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power2[0]['lens_power']);
                        if($checkLens1 && $checkLens2){
                           // if($checkLens1[0]['quantity'] >= 1 && $checkLens2[0]['quantity'] >= 1){
                                $lensTotalCost = $checkLens1[0]['price'] + $checkLens2[0]['price'];
                           // }else{$errorMessage='No Sufficient Amount of Lens in stock to Fulfill this Request.Please place an order for it';$chckLens=true;}
                        }else{$errorMessage='This Lens is not Available in store.Please Place an order for it';$chckLens=true;}
                    }

                    $totalCost = $lensTotalCost + $frameCost + Input::get('checkupCost') + $totalMedCost;
                    $payAmount = Input::get('checkupCost') + $totalMedCost;
                    $validate = new validate();
                    if($_GET['id'] == 0){
                        $validate = $validate->check($_POST, array(
                            'payment' => array(
                                'required' => true,
                            ),
                        ));
                        if(Input::get('dscnt') == 1){
                            if((Input::get('payment') + Input::get('discount')) == Input::get('totalCost')){
                                $status = 1;$statusL=1;$pay=Input::get('payment');$dis=Input::get('discount');
                            }elseif((Input::get('payment') + Input::get('discount')) < $payAmount) {
                                $status = 2;$bitError=true;$statusL=0;
                            }elseif((Input::get('payment') + Input::get('discount')) >= $payAmount && (Input::get('payment')+ Input::get('discount')) < Input::get('totalCost')){$status = 2;$statusL=0;$pay=Input::get('payment');$dis=Input::get('discount');
                            }elseif((Input::get('payment')+ Input::get('discount')) > Input::get('totalCost')){//print_r(Input::get('totalCost'));
                                $checkError=true;$errorMessage='Received amount exceed required amount,Please verify the amount and try again';
                            }
                        }else{
                            if(Input::get('totalCost') == Input::get('payment')) {
                                $status = 1;$statusL=1;$pay=Input::get('payment');
                            }elseif(Input::get('payment') < $payAmount) {
                                $status = 2;$bitError=true;$statusL=0;
                            }elseif(Input::get('payment') >= $payAmount && Input::get('payment') < Input::get('totalCost')){$status = 2;$statusL=0;$pay=Input::get('payment');
                            }elseif(Input::get('payment') > Input::get('totalCost')){//print_r(Input::get('totalCost'));
                                $checkError=true;$errorMessage='Received amount exceed required amount,Please verify the amount and try again';
                            }
                        }
                    }
                    elseif($_GET['id'] == 1){
                        $validate = $validate->check($_POST, array(
                            'insurance_pay' => array(
                                'required' => true,
                            ),
                        ));
                        if(Input::get('totalCost') == Input::get('insurance_pay')){
                            $status = 1;$statusL=1;$pay=Input::get('insurance_pay');$insurancePay=Input::get('insurance_pay');
                        }elseif((Input::get('insurance_pay')+Input::get('payment')) == Input::get('totalCost')) {
                            $status=1;$statusL=1;$pay=Input::get('insurance_pay') + Input::get('payment');$insurancePay=Input::get('insurance_pay');
                        }elseif((Input::get('insurance_pay')+Input::get('payment')) < Input::get('totalCost')){
                            $status=2;$statusL=0;$pay=Input::get('insurance_pay') + Input::get('payment');$insurancePay=Input::get('insurance_pay');
                        }elseif((Input::get('insurance_pay')+Input::get('payment')) > Input::get('totalCost')){
                            $checkError=true;$errorMessage='Received amount exceed required amount,Please verify the amount and try again';
                        }
                    }
                    if ($validate->passed() && $bitError == false && $checkError == false && $chckLens == false) {
                        try {
                            $user->updateRecord('payment', array(
                                'cost' => Input::get('totalCost'),
                                'payment' => $pay,
                                'status' => $status,
                                'insurance' =>$ins,
                                'insurance_pay' =>$insurancePay,
                                'discount' =>$dis,
                                'ins_id'=>Input::get('insurance_no'),
                                'ins_no'=>Input::get('dependent_no'),
                                'pay_date'=>date('Y-m-d'),
                                'reception_id' => $user->data()->id
                            ), $checkupCost[0]['id']);$j=1;
                            while($j <= count($pr)-1){
                                $lessMed = $override->get('medicine','id',$medID[$j])[0]['quantity'];
                                $newQ = $lessMed - $qt[$j];
                                $user->updateRecord('prescription', array(
                                    'status' => 1, // 1
                                ),$pr[$j]);
                                $user->updateRecord('medicine', array(
                                    'quantity' => $newQ,
                                ),$mID[$j]);$j++;
                            }$f=0;$getLensPower=0;$checkLensPower=null;
                            //*********************************************** lens payment *********************************************************************/
                            if(Input::get('eye') == 'BE' && Input::get('lens_cat')){$l_power=$override->get('lens_power','id',Input::get('lens_power'));
                                $checkLens=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power[0]['lens_power'],'branch_id',$branch_id);
                                $newLq = 0;

                                $user->createRecord('lens_prescription',array(
                                    'lens_id' => Input::get('lens_cat'),
                                    'lens' => $l_power[0]['id'],
                                    'lens_power'=>$l_power[0]['lens_power'],
                                    'eye' =>Input::get('eye'),
                                    'cost' => $lensTotalCost,
                                    'status' => 0,
                                    'patient_id' => $_GET['p'],
                                    'branch_id' => $user->data()->branch_id,
                                    'checkup_id' => $_GET['c'],
                                    'checkup_date' => $checkupCost[0]['checkup_date']
                                ));
                            }
                            elseif(Input::get('eye') == 'RE' && Input::get('lens_cat')){
                                $l_power1=$override->get('lens_power','id',Input::get('lens_power'));
                                $l_power2=$override->get('lens_power','id',Input::get('lens_power_other'));
                                $checkLens1=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power1[0]['lens_power'],'branch_id',$branch_id);
                                $checkLens2=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power2[0]['lens_power'],'branch_id',$branch_id);

                                $newLq1 = 0;$newLq2 = 0;
                                /*$newLq1 = $checkLens1[0]['quantity'] - 1;
                                $newLq2 = $checkLens2[0]['quantity'] - 1;
                                $user->updateRecord('lens_power', array(
                                    'quantity' => $newLq1,
                                ),$checkLens1[0]['id']);
                                $user->updateRecord('lens_power', array(
                                    'quantity' => $newLq2,
                                ),$checkLens2[0]['id']);*/
                                $user->createRecord('lens_prescription',array(
                                    'lens_id' => Input::get('lens_cat'),
                                    'lens' => $checkLens1[0]['id'],
                                    'lens_power'=>$checkLens1[0]['lens_power'],
                                    'eye' =>Input::get('eye'),
                                    'cost' => $lensTotalCost,
                                    'status' => 0,
                                    'patient_id' => $_GET['p'],
                                    'checkup_date' => $checkupCost[0]['checkup_date'],
                                    'branch_id' => $user->data()->branch_id,
                                    'checkup_id' => $_GET['c']
                                ));
                                $user->createRecord('lens_prescription',array(
                                    'lens_id' => Input::get('lens_cat'),
                                    'lens' => $checkLens2[0]['id'],
                                    'lens_power'=>$checkLens2[0]['lens_power'],
                                    'eye' =>Input::get('eye'),
                                    'cost' => $lensTotalCost,
                                    'status' => 0,
                                    'patient_id' => $_GET['p'],
                                    'checkup_date' => $checkupCost[0]['checkup_date'],
                                    'branch_id' => $user->data()->branch_id,
                                    'checkup_id' => $_GET['c']
                                ));
                            }
                            //**************************************** frame payment ********************************************************************/
                            if(Input::get('frameBrand') && Input::get('frameModel') && Input::get('frameSize')){$frameS=$override->get('frames','id',Input::get('frameSize')); //print_r(Input::get('frameBrand')); echo' f '; print_r(Input::get('frameModel'));echo' g ';print_r(Input::get('frameSize'));echo' h ';
                                //$frame=$override->selectData4('frames','brand_id',Input::get('frameBrand'),'model',Input::get('frameModel'),'frame_size',$frameS[0]['frame_size'],'branch_id',$user->data()->branch_id);
                                $frameQ=$frameS[0]['quantity'];//
                                $frameQ -=1;
                                $user->updateRecord('frames', array(
                                    'quantity' => $frameQ,
                                ),$frameS[0]['id']);
                                $user->createRecord('frame_sold', array(
                                    'brand' => Input::get('frameBrand'),
                                    'model'=>Input::get('frameModel'),
                                    'size'=>Input::get('frameSize'),
                                    'price'=>Input::get('frame_cost'),
                                    'sold_date'=>date('Y-m-d'),
                                    'status'=>$statusL,
                                    'patient_id'=>$_GET['p'],
                                    'staff_id'=>$user->data()->id,
                                    'branch_id' => $user->data()->branch_id
                                ));
                            }
                            $successMessage = 'Patient Payment Received Successfully ';
                        }catch (Exception $e){
                            die($e->getMessage());
                        }
                    } else {
                        if($bitError){$errorMessage = 'Patient must at least pay for Medicine and Checkup Cost';}
                        $totalCost = Input::get('totalCost');
                        $pageError = $validate->errors();
                    }
                }
            }
            elseif($_GET['id'] == 33){
                if(Input::get('returnPatient')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'name' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()){
                        try {
                            switch(Input::get('criteria')){
                                case 'firstname':
                                    $searchValue=$override->getLike('patient','firstname',Input::get('name'));
                                    break;
                                case 'lastname':
                                    $searchValue=$override->getLike('patient','lastname',Input::get('name'));
                                    break;
                                case 'phone_number':
                                    $searchValue=$override->getLike('patient','phone_number',Input::get('name'));
                                    break;
                                case 'id':
                                    $searchValue=$override->getLike('patient','id',Input::get('name'));
                                    break;
                            }
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
                elseif(Input::get('sendDoctor')){
                    try {
                        if($override->get('wait_list','patient_id',Input::get('patient_name'))){
                            $user->updateRecord('wait_list',array(
                                'arrive_on' => date('Y-m-d')
                            ),Input::get('patient_name'));
                            $successMessage = 'Patient information have been sent to doctor';
                        }else {
                            $user->createRecord('wait_list', array(
                                'arrive_on' => date('Y-M-d h:m'),
                                'patient_id' => Input::get('patient_name'),
                                'staff_id' => $user->data()->id,
                                'branch_id'=>$user->data()->branch_id
                            ));
                            $successMessage = 'Patient information have been sent to doctor';
                        }
                    } catch (Exception $e) {
                        die($e->getMessage());
                    }
                }
                elseif(Input::get('updatePatientInfo')){
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
                        'phone_number' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()) {
                        try {
                            $user->updateRecord('patient', array(
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
                            ),Input::get('id'));
                            $successMessage = 'Patient Info Updated Successful';
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
            elseif($_GET['id'] == 3){
                if(Input::get('pendingCash')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'payment' => array(
                            'required' => true,
                        )
                    ));
                    if(!is_numeric(Input::get('payment'))){$bitError=true;$errorMessage='Payment must be of numerical value';}
                    if ($validate->passed() && $bitError == false){
                        if(Input::get('remainingCost') == Input::get('payment')) {
                            $status = 1;}else{$status = $checkupCost[0]['status'];}
                        $payment = $checkupCost[0]['payment'] + Input::get('payment');
                        try{
                            $user->updateRecord('payment', array(
                                'payment' => $payment,
                                'status' => $status,
                                'insurance' =>$ins,
                                'pay_date' => date('Y-m-d'),
                                'reception_id' => $user->data()->id
                            ), $checkupCost[0]['id']);
                            $successMessage = 'Payment received successful';
                        }
                        catch(PDOException $e){$e->getMessage();}
                    }else {
                        $pageError = $validate->errors();
                    }
                }
            }
            elseif($_GET['id'] == 4 && $user->data()->access_level == 4){
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
            elseif($_GET['id'] == 5 && $user->data()->access_level == 4){
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
                            }else {
                                $user->createRecord('frames', array(
                                    'model' => $frame_model,
                                    'frame_size' => Input::get('size'),
                                    'quantity' => Input::get('quantity'),
                                    'price' => Input::get('price'),
                                    'category' => Input::get('category'),
                                    'brand_id' => Input::get('brand'),
                                    'branch_id' => Input::get('clinic_branch')
                                ));
                                $user->createRecord('frame_record', array(
                                    'brand' => Input::get('brand'),
                                    'model' => $frame_model,
                                    'frame_size' => Input::get('size'),
                                    'quantity' => Input::get('quantity'),
                                    'price' => Input::get('price'),
                                    'category' => Input::get('category'),
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
            elseif($_GET['id'] == 6 && $user->data()->access_level == 4){
                if(Input::get('addMedicine')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'clinic_branch' => array(
                            'required' => true,
                        ),
                        'name' => array(
                            'required' => true,
                        ),
                        'manufacture' => array(
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
            elseif($_GET['id'] == 7){
                if(Input::get('calc')){$x=1;
                    //$no=$override->getCounted('prescription','patient_id',$_GET['p'],'status',0,'checkup_id',$_GET['c']);
                    /*while($x<=$no){
                        $getM = 'med'.$x;
                        if(Input::get($getM)){$medCn[$x]=1;
                            $pMed = $override->selectData4('prescription','patient_id',$_GET['p'],'status',0,'medicine_id',Input::get($getM),'checkup_id',$_GET['c']);
                            $pr[$x] = $pMed[0]['id'];$medID[$x] = $pMed[0]['medicine_id'];
                            $medP = $override->selectData4('prescription','medicine_id',Input::get($getM),'patient_id',$_GET['p'],'status',0,'checkup_id',$_GET['c']);
                            $qt[$x] = $medP[0]['quantity'];
                            $medC = $override->get('medicine','id',Input::get($getM));
                            $mID[$x] = $medC[0]['id'];
                            $medCost = $medP[0]['quantity'] * $medC[0]['price'];
                            $totalMedCost +=  $medCost;
                        }else{$medCn[$x]=0;}
                        $x++;}*/
                    $totalLensPrice=0;
                    if($user->data()->branch_id == 9){$branch_id = 8;}else{$branch_id=$user->data()->branch_id;}
                    if(Input::get('eye') == 'BE' && Input::get('lens_cat')){$l_power=$override->get('lens_power','id',Input::get('lens_power'));
                        $checkLens=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power[0]['lens_power'],'branch_id',$branch_id);
                        //print_r(Input::get('lens_cat'));echo' k';print_r(Input::get('lensType'));echo' l';print_r(Input::get('lensGroup'));echo' ';print_r($l_power[0]['lens_power']);
                        //print_r($checkLens[0]['price']);
                        if($checkLens){
                            if($checkLens[0]['quantity'] >= 2){
                                $lensTotalCost = $checkLens[0]['price'] * 2;
                            }else{$lensTotalCost = $checkLens[0]['price'] * 2;$errorMessage='No Sufficient Amount of Lens in stock to Fulfill this Request.Please place an order for it';}
                        }else{$lensTotalCost = $checkLens[0]['price'] * 2;$errorMessage='This Lens is not Available in store.Please Place an order for it';}
                    }
                    elseif(Input::get('eye') == 'RE' && Input::get('lens_cat')){
                        $l_power1=$override->get('lens_power','id',Input::get('lens_power'));
                        $l_power2=$override->get('lens_power','id',Input::get('lens_power_other'));
                        $checkLens1=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power1[0]['lens_power'],'branch_id',$branch_id);
                        $checkLens2=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power2[0]['lens_power'],'branch_id',$branch_id);
                        if($checkLens1 && $checkLens2){
                            if($l_power1[0]['quantity'] >= 1 && $l_power2[0]['quantity'] >= 1){
                                $lensTotalCost = $checkLens1[0]['price'] + $checkLens2[0]['price'];
                            }else{$lensTotalCost = $checkLens1[0]['price'] + $checkLens2[0]['price'];$errorMessage='No Sufficient Amount of Lens in stock to Fulfill this Request.Please place an order for it';}
                        }else{$lensTotalCost = $checkLens1[0]['price'] + $checkLens2[0]['price'];$errorMessage='This Lens is not Available in store.Please Place an order for it';}
                    }
                    //if($lensCost){$lensTotalCost=$lensCost[0]['price'] * 2;}
                    //$lensCost = Input::get('lens_cost');
                    //$frameCost = Input::get('frame_cost');
                    $totalCost = $lensTotalCost ;
                }
                elseif(Input::get('submitCash')){$x=1;
                    //$no=$override->getCounted('prescription','patient_id',$_GET['p'],'status',0,'checkup_id',$_GET['c']);
                    if($_GET['id'] == 0){$ins=0;}elseif($_GET['id'] == 1){$ins=1;}
                    //$lensId=$override->getNews('lens_prescription','patient_id',$_GET['p'],'status',0);

                    //$lensCost = Input::get('lens_cost');
                    //$frameCost = Input::get('frame_cost');
                    //$lensCost=$override->selectData('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'));
                    //$totalLensPrice=0;
                    if($user->data()->branch_id == 9){$branch_id = 8;}else{$branch_id=$user->data()->branch_id;}
                    if(Input::get('eye') == 'BE' && Input::get('lens_cat')){$l_power=$override->get('lens_power','id',Input::get('lens_power'));
                        $checkLens=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power[0]['lens_power'],'branch_id',$branch_id);
                        if($checkLens){
                            if($l_power[0]['quantity'] >= 2){
                                $lensTotalCost = $checkLens[0]['price'] * 2;
                            }else{$errorMessage='No Sufficient Amount of Lens in stock to Fulfill this Request.Please place an order for it';$chckLens=true;}
                        }else{$errorMessage='This Lens is not Available in store.Please Place an order for it';$chckLens=true;}
                    }
                    elseif(Input::get('eye') == 'RE' && Input::get('lens_cat')){
                        $l_power1=$override->get('lens_power','id',Input::get('lens_power'));
                        $l_power2=$override->get('lens_power','id',Input::get('lens_power_other'));
                        $checkLens1=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power1[0]['lens_power'],'branch_id',$branch_id);
                        $checkLens2=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power2[0]['lens_power'],'branch_id',$branch_id);
                        if($checkLens1 && $checkLens2){
                            if($checkLens1[0]['quantity'] >= 1 && $checkLens2[0]['quantity'] >= 1){
                                $lensTotalCost = $checkLens1[0]['price'] + $checkLens2[0]['price'];
                            }else{$errorMessage='No Sufficient Amount of Lens in stock to Fulfill this Request.Please place an order for it';$chckLens=true;}
                        }else{$errorMessage='This Lens is not Available in store.Please Place an order for it';$chckLens=true;}
                    }
                    // if($lensCost){$lensTotalCost=$lensCost[0]['price'];}
                    $totalCost = $lensTotalCost ;
                    $payAmount = $lensTotalCost;
                    $validate = new validate();
                    if(Input::get('pay') == 'C'){
                        $validate = $validate->check($_POST, array(
                            'payment' => array(
                                'required' => true,
                            ),
                        ));
                        if(Input::get('totalCost') == Input::get('payment')) {
                            $status = 1;$statusL=1;$pay=Input::get('payment');
                        }elseif(Input::get('payment') < $payAmount) {
                            $status = 2;$bitError=true;$statusL=0;
                        }elseif(Input::get('payment') >= $payAmount && Input::get('payment') < Input::get('totalCost')){$status = 2;$statusL=0;$pay=Input::get('payment');
                        }elseif(Input::get('payment') > Input::get('totalCost')){print_r(Input::get('totalCost'));
                            $checkError=true;$errorMessage='Received amount exceed required amount,Please verify the amount and try again';
                        }
                    }
                    elseif(Input::get('pay') == 'I'){
                        $validate = $validate->check($_POST, array(
                            'insurance_pay' => array(
                                'required' => true,
                            ),
                        ));
                        if(Input::get('totalCost') == Input::get('insurance_pay')){
                            $status = 1;$statusL=1;$pay=Input::get('insurance_pay');$insurancePay=Input::get('insurance_pay');$insurance=1;
                        }elseif((Input::get('insurance_pay')+Input::get('ins_payment')) == Input::get('totalCost')) {$insurance=1;
                            $status=1;$statusL=1;$pay=Input::get('insurance_pay') + Input::get('ins_payment');$insurancePay=Input::get('insurance_pay');
                        }elseif((Input::get('insurance_pay')+Input::get('payment')) < Input::get('totalCost')){$insurance=1;
                            $status=2;$statusL=0;$pay=Input::get('insurance_pay') + Input::get('ins_payment');$insurancePay=Input::get('insurance_pay');
                        }elseif((Input::get('insurance_pay')+Input::get('ins_payment')) > Input::get('totalCost')){
                            $checkError=true;$errorMessage='Received amount exceed required amount,Please verify the amount and try again';
                        }
                        $ins_pay = Input::get('ins_payment') + Input::get('insurance_pay');
                    }
                    if ($validate->passed() && $bitError == false && $checkError == false && $chckLens == false) {
                        try {
                            $user->createRecord('checkup_record', array(
                                'rx_OD_sphere' => Input::get('rx_od_sphere'),
                                'rx_cyl' => Input::get('rx_cyl'),
                                'rx_axis' => Input::get('rx_axis'),
                                'rx_va' => Input::get('rx_va'),
                                'rx_add' => Input::get('rx_add'),
                                'add_rx_OS_sphere' => Input::get('add_rx_os_sphere'),
                                'add_rx_cyl' => Input::get('add_rx_cyl'),
                                'add_rx_axis' => Input::get('add_rx_axis'),
                                'add_rx_va' => Input::get('add_rx_va'),
                                'add_rx_add' => Input::get('add_rx_add'),
                                'distance_glasses' => Input::get('distance_glasses'),
                                'reading_glasses' => Input::get('reading_glasses'),
                                'checkup_date' => date('Y-m-d'),
                                'patient_id' => Input::get('patient'),
                                'branch_id' => $user->data()->branch_id
                            ));
                            $checkup_id=$override->getNews('checkup_record','patient_id',Input::get('patient'),'checkup_date',date('Y-m-d'));
                            $user->createRecord('payment', array(
                                'cost' => Input::get('totalCost'),
                                'payment' => $pay,
                                'status' => $status,
                                'insurance' =>$insurance,
                                'insurance_pay' =>$insurancePay,
                                'patient_id' => Input::get('patient'),
                                'pay_date'=>date('Y-m-d'),
                                'checkup_id'=>$checkup_id[0]['id'],
                                'branch_id'=>$user->data()->branch_id,
                                'reception_id' => $user->data()->id
                            ));$j=1;
                            $f=0;$getLensPower=0;$checkLensPower=null;
                            //*********************************************** lens payment *********************************************************************/
                            if(Input::get('eye') == 'BE' && Input::get('lens_cat')){$l_power=$override->get('lens_power','id',Input::get('lens_power'));
                                $checkLens=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power[0]['lens_power'],'branch_id',$branch_id);
                                $newLq = 0;
                                $newLq = $checkLens[0]['quantity'] - 2;
                                $user->updateRecord('lens_power', array(
                                    'quantity' => $newLq,
                                ),$checkLens[0]['id']);
                                $user->createRecord('lens_prescription',array(
                                    'lens_id' => Input::get('lens_cat'),
                                    'lens' => $l_power[0]['id'],
                                    'lens_power'=>$l_power[0]['lens_power'],
                                    'eye' =>Input::get('eye'),
                                    'cost' => $lensTotalCost,
                                    'status' => 1,
                                    'patient_id' => Input::get('patient'),
                                    'checkup_date' => date('Y-m-d'),
                                    'branch_id' => $user->data()->branch_id,
                                    'checkup_id' => $checkup_id[0]['id']
                                ));
                            }
                            elseif(Input::get('eye') == 'RE' && Input::get('lens_cat')){
                                $l_power1=$override->get('lens_power','id',Input::get('lens_power'));
                                $l_power2=$override->get('lens_power','id',Input::get('lens_power_other'));
                                $checkLens1=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power1[0]['lens_power'],'branch_id',$branch_id);
                                $checkLens2=$override->selectData5('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'),'lens_power',$l_power2[0]['lens_power'],'branch_id',$branch_id);

                                $newLq1 = 0;$newLq2 = 0;
                                $newLq1 = $checkLens1[0]['quantity'] - 1;
                                $newLq2 = $checkLens2[0]['quantity'] - 1;
                                $user->updateRecord('lens_power', array(
                                    'quantity' => $newLq1,
                                ),$checkLens1[0]['id']);
                                $user->updateRecord('lens_power', array(
                                    'quantity' => $newLq2,
                                ),$checkLens2[0]['id']);
                                $user->createRecord('lens_prescription',array(
                                    'lens_id' => Input::get('lens_cat'),
                                    'lens' => $checkLens1[0]['id'],
                                    'lens_power'=>$checkLens1[0]['lens_power'],
                                    'eye' =>Input::get('eye'),
                                    'cost' => $lensTotalCost,
                                    'status' => 1,
                                    'patient_id' => Input::get('patient'),
                                    'checkup_date' => date('Y-m-d'),
                                    'branch_id' => $user->data()->branch_id,
                                    'checkup_id' => $checkup_id[0]['id']
                                ));
                                $user->createRecord('lens_prescription',array(
                                    'lens_id' => Input::get('lens_cat'),
                                    'lens' => $checkLens2[0]['id'],
                                    'lens_power'=>$checkLens2[0]['lens_power'],
                                    'eye' =>Input::get('eye'),
                                    'cost' => $lensTotalCost,
                                    'status' => 1,
                                    'patient_id' => Input::get('patient'),
                                    'checkup_date' => date('Y-m-d'),
                                    'branch_id' => $user->data()->branch_id,
                                    'checkup_id' => $checkup_id[0]['id']
                                ));
                            }
                            //**************************************** frame payment ********************************************************************/
                            $successMessage = 'Patient Payment Received Successfully ';
                        }catch (Exception $e){
                            die($e->getMessage());
                        }
                    }
                    else {
                        if($bitError){$errorMessage = 'Patient must at least pay for Medicine and Checkup Cost';}
                        $totalCost = Input::get('totalCost');
                        $pageError = $validate->errors();
                    }
                }
            }
            elseif($_GET['id'] == 8){
                if(Input::get('checkLens')){
                    /*$lensPrice=$override->getNews('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'));
                    $checkLens=$override->selectData('lens_power','cat_id',Input::get('lens_cat'),'type_id',Input::get('lensType'),'lens_id',Input::get('lensGroup'));
                    if($checkLens){
                    }else{
                        $lensNotification='Lens is currently not available in stock.Please make an Order';
                    }*/
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'patient' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()) {
                        try {
                            $user->createRecord('checkup_record', array(
                                'rx_OD_sphere' => Input::get('rx_od_sphere'),
                                'rx_cyl' => Input::get('rx_cyl'),
                                'rx_axis' => Input::get('rx_axis'),
                                'rx_va' => Input::get('rx_va'),
                                'rx_add' => Input::get('rx_add'),
                                'add_rx_OS_sphere' => Input::get('add_rx_os_sphere'),
                                'add_rx_cyl' => Input::get('add_rx_cyl'),
                                'add_rx_axis' => Input::get('add_rx_axis'),
                                'add_rx_va' => Input::get('add_rx_va'),
                                'add_rx_add' => Input::get('add_rx_add'),
                                'checkup_date' => date('Y-m-d'),
                                'patient_id' => Input::get('patient'),
                                'branch_id' => $user->data()->branch_id
                            ));

                            $getMedicine = array(Input::get('medicine'),Input::get('other_medicine'),Input::get('other_medicine_1'),Input::get('other_medicine_2'));
                            $dosage = array(Input::get('dosage'),Input::get('other_dosage'),Input::get('other_dosage_1'),Input::get('other_dosage_2'));$f=0;
                            foreach($getMedicine as $getMed){
                                if($getMed == null){

                                }else{
                                    $user->createRecord('prescription',array(
                                        'medicine_id' => $getMed,
                                        'quantity' => $dosage[$f],
                                        'given_date' => date('Y-m-d'),
                                        'patient_id' => Input::get('patient'),
                                        'doctor_id' => $user->data()->id,
                                        'branch_id' => $user->data()->branch_id
                                    ));
                                }$f++;
                            }
                            if(Input::get('eye') == 'Both'){
                                $user->createRecord('lens_prescription',array(
                                    'lens_id' => Input::get('lens'),
                                    'lens_power'=>Input::get('lens_power'),
                                    'eye' =>Input::get('eye'),
                                    'patient_id' => Input::get('patient'),
                                    'checkup_date' => date('Y-m-d'),
                                    'branch_id' => $user->data()->branch_id
                                ));
                            }elseif(Input::get('eye') == 'RE' || Input::get('eye') == 'LE'){
                                $user->createRecord('lens_prescription',array(
                                    'lens_id' => Input::get('lens'),
                                    'lens_power'=>Input::get('lens_power'),
                                    'eye' =>Input::get('eye'),
                                    'patient_id' => Input::get('patient'),
                                    'checkup_date' => date('Y-m-d'),
                                    'branch_id' => $user->data()->branch_id
                                ));$user->createRecord('lens_prescription',array(
                                    'lens_id' => Input::get('other_lens'),
                                    'lens_power'=>Input::get('other_power'),
                                    'eye' =>Input::get('other_eye'),
                                    'patient_id' => Input::get('patient'),
                                    'checkup_date' => date('Y-m-d'),
                                    'branch_id' => $user->data()->branch_id
                                ));
                            }$totalTest=0;
                            $p=$override->get('patient','id',Input::get('patient'));
                            $user->createRecord('payment',array(
                                'cost' => 0,
                                'checkup_date' => date('Y-m-d'),
                                'patient_id' => Input::get('patient'),
                                'branch_id' => $user->data()->branch_id
                            ));
                            $successMessage = 'Patient Information Successful Saved';
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
            elseif($_GET['id'] == 9){
                if(Input::get('submitFrame')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'patient' => array(
                            'required' => true,
                        ),
                        'payment' => array(
                            'required' => true,
                        ),
                    ));
                    if(Input::get('payment') == Input::get('frame_cost')){

                    }elseif(Input::get('payment') > Input::get('frame_cost')){
                        $checkError=true;$errorMessage='Received amount exceed Frame cost,Please verify the amount and try again';
                    }elseif(Input::get('payment') < Input::get('frame_cost')){
                        $checkError=true;$errorMessage='Received amount is less than Frame cost,Please verify the amount and try again';
                    }else{$checkError=true;$errorMessage='Payment information is incorrect,Please verify the amount and try again';}
                    if ($validate->passed() && $checkError == false) {
                        try {
                            $frame_size=$override->get('frames','id',Input::get('frameSize'));
                            $frame=$override->selectData4('frames','brand_id',Input::get('frameBrand'),'model',Input::get('frameModel'),'frame_size',$frame_size[0]['frame_size'],'branch_id',$user->data()->branch_id);
                            $frameQ=$frame[0]['quantity'];
                            $frameQ -=1;
                            $user->updateRecord('frames', array(
                                'quantity' => $frameQ,
                            ),$frame[0]['id']);
                            $user->createRecord('frame_sold', array(
                                'brand' => Input::get('frameBrand'),
                                'model'=>Input::get('frameModel'),
                                'size'=>$frame_size[0]['frame_size'],
                                'price'=>Input::get('payment'),
                                'sold_date'=>date('Y-m-d'),
                                'status'=>1,
                                'patient_id'=>Input::get('patient'),
                                'staff_id'=>$user->data()->id,
                                'branch_id' => $user->data()->branch_id
                            ));
                            $successMessage = 'Frame Payment Received Successfully ';
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
            elseif($_GET['id'] == 10 && $user->data()->access_level == 4){
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
            elseif($_GET['id'] == 12 && $user->data()->access_level == 7){
                if(Input::get('submitSales')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'customer_name' => array(
                            'required' => true,
                        ),
                        'product' => array(
                            'required' => true,
                        ),
                        'batch' => array(
                            'required' => true,
                        ),
                        'quantity' => array(
                            'required' => true,
                        ),
                        'phone_number' => array(
                            'required' => true,
                        ),
                        'address' => array(
                            'required' => true,
                        ),
                        'sale_date' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()) {
                        try {
                            $sales = $override->selectData4('frame_sales', 'emp_id', $user->data()->id, 'price_per', Input::get('batch'), 'status', 0,'product_id',Input::get('product'));$fin_date = null;
                            if($sales[0]['quantity'] >= ($sales[0]['sold_qty'] + Input::get('quantity'))){
                                $sales_amount = ($sales[0]['price_per'] * Input::get('quantity')) + $sales[0]['amount'];
                                $quantity=$sales[0]['sold_qty'] + Input::get('quantity');
                                if (($sales[0]['sold_qty'] + Input::get('quantity')) == $sales[0]['quantity']) {
                                    $status = 1;
                                    $fin_date = date('Y-m-d');
                                } else {
                                    $status = 0;
                                }
                                $user->createRecord('sales_details', array(
                                    'name' => Input::get('customer_name'),
                                    'phone_number' => Input::get('phone_number'),
                                    'email_address' => Input::get('email_address'),
                                    'location' => Input::get('address'),
                                    'batch' => Input::get('batch'),
                                    'sales_date' => Input::get('sale_date'),
                                    'quantity' => Input::get('quantity'),
                                    'other_note' => Input::get('notes'),
                                    'product_id' => Input::get('product'),
                                    'staff_id' => $user->data()->id,
                                ));
                                $user->updateRecord('frame_sales', array(
                                    'sold_qty' => $quantity,
                                    'amount' => $sales_amount,
                                    'finish_date' => $fin_date,
                                    'status' => $status,
                                ), $sales[0]['id']);
                                $successMessage = 'Sales Received Successfully ';
                            }else{$errorMessage='Amount entered exceeds the amount on sales batch,Please check it and try again';}
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
            elseif($_GET['id'] == 13 && $user->data()->access_level == 6){
                if(Input::get('frame')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
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
                            $user->createRecord('frames', array(
                                'model' => $frame_model,
                                'frame_size' => Input::get('size'),
                                'quantity' => Input::get('quantity'),
                                'price' => Input::get('price'),
                                'category' =>Input::get('category'),
                                'brand_id' => Input::get('brand'),
                                'branch_id' => $user->data()->branch_id
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
                                'branch_id' => $user->data()->branch_id
                            ));
                            $date=$override->getNews('data_rec','staff_id',$user->data()->id,'data_date',date('Y-m-d'));
                            $data=$override->get('data_payment','emp_id',$user->data()->id);
                            if($date){
                                $qty=$date[0]['quantity'] +1;
                                $user->updateRecord('data_rec',array(
                                    'quantity' => $qty
                                ),$date[0]['id']);
                            }else{
                                $user->createRecord('data_rec',array(
                                    'quantity' => 1,
                                    'data_date' => date('Y-m-d'),
                                    'staff_id' => $user->data()->id
                                ));
                            }
                            if($data){
                                $qty1=$data[0]['quantity'] + 1;
                                $user->updateRecord('data_payment',array(
                                    'quantity' => $qty1
                                ),$data[0]['id']);
                            }else{
                                $user->createRecord('data_payment',array(
                                    'quantity' => 1,
                                    'emp_id' => $user->data()->id
                                ));
                            }
                            $successMessage = 'Frame Successful Added to Stock';
                        } catch (Exception $e) {
                            die($e->getMessage());
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
                elseif(Input::get('addBrand')){
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
            elseif($_GET['id'] == 14){
                if(Input::get('appnt')){
                    $validate = new validate();
                    $validate = $validate->check($_POST, array(
                        'patient' => array(
                            'required' => true,
                        ),
                        'doctor' => array(
                            'required' => true,
                        ),
                        'date' => array(
                            'required' => true,
                        ),
                        'time' => array(
                            'required' => true,
                        ),
                    ));
                    if ($validate->passed()) {
                        if($override->getNews('appointment','patient_id',Input::get('patient'),'status',0)){
                            $errorMessage='Patient has pending appointment ,Please cancel or change it';
                        }else{
                            try {
                                $user->createRecord('appointment', array(
                                    'appnt_date' => Input::get('date'),
                                    'appnt_time' => Input::get('time'),
                                    'patient_id'=>Input::get('patient'),
                                    'doctor_id'=>Input::get('doctor'),
                                    'staff_id'=>$user->data()->id,
                                    'branch_id'=>$user->data()->branch_id,
                                ));
                                $successMessage = 'Appointment Settled Successful Added';
                            } catch (Exception $e) {
                                die($e->getMessage());
                            }
                        }
                    } else {
                        $pageError = $validate->errors();
                    }
                }
            }
            elseif($_GET['id'] == 15){
                if(Input::get('diagnosis')){
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
        }
    }else{
        Redirect::to('index.php');
    }
}else{Redirect::to('index.php');}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- META SECTION -->
    <title> Siha Optical </title>
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
                <h1><?=$heading?>&nbsp;&nbsp;</h1>
                <div class="pull-left">
                    <?php if(date('Y-m-d') == $user->data()->birthday){?>
                    <button class="btn btn-warning">The management and all staff of Siha Optical Eye Center wish you Happy Birthday</button>
                    <?php }?>
                </div>
                <div class="pull-right">
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
                <?php if($_GET['id'] == 33){?>
                    <h4>PATIENT ON QUEUE : <?=$override->getCount('wait_list','branch_id',$user->data()->branch_id)?></h4>
                <?php }?>
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
            <?php if($_GET['id'] == 0 && $user->data()->access_level == 3){?>
                <h1>Cash Payment Details</h1>
                <h3>&nbsp;</h3>
                <?php $checkUp=$override->getNews('test_performed','checkup_id',$_GET['c'],'patient_id',$_GET['p']);$cCost=0;
                  if($checkUp){foreach($checkUp as $chCost){
                      $cPrice=$override->get('test_list','id',$chCost['test_id']);
                      $cCost += $cPrice[0]['cost'];
                  }}
                ?>
                <form role="form" class="form-horizontal" method="post">
                <div class="form-group">
                    <label class="col-md-1"><strong>PATIENT NAME : </strong></label>
                    <div class="col-md-4">
                        <input name="firstname" type="text" class="form-control" placeholder="FIRSTNAME " VALUE="<?=$patient[0]['firstname'].' '.$patient[0]['lastname'].'  '.$patient[0]['phone_number']?>" disabled>
                    </div>
                    <label class="col-md-1"><strong></strong></label>
                    <label class="col-md-1"><strong>CHECKUP COST : </strong></label>
                    <div class="col-md-2">
                        <input name="checkupCost" type="hidden" class="form-control" placeholder="" VALUE="<?=$cCost?>">
                        <input name="" type="number" class="form-control" placeholder="" VALUE="<?=$cCost?>" disabled>
                    </div>
                    <label class="col-md-1"><strong></strong></label>
                </div><hr>
                <h2>Prescribed Medicine</h2>
                <div class="form-group">
                    <div class="col-md-offset-0 col-md-8">
                        <?php if(Input::get('calc')){$a=1; foreach($override->selectData('prescription','status',0,'patient_id',$_GET['p'],'checkup_id',$_GET['c']) as $med){
                            $getMed = $override->get('medicine','id',$med['medicine_id']);if($medCn[$a] ==1){?>
                                &nbsp;&nbsp;<hr><strong style="font-size: 14px"><input type="checkbox" name="med<?=$a?>" value="<?=$getMed[0]['id']?>" checked>&nbsp;&nbsp;<?=$getMed[0]['name'].' : ( '.$med['quantity'].' ) '.$med['dosage'].' ( '.$med['eyes'].' ) for '.$med['no_day'].' '.$med['days_group']?>&nbsp;</strong>
                            <?php }else{?>
                                &nbsp;&nbsp;<hr><strong style="font-size: 14px"><input type="checkbox" name="med<?=$a?>" value="<?=$getMed[0]['id']?>" checked>&nbsp;&nbsp;<?=$getMed[0]['name'].' : ( '.$med['quantity'].' ) '.$med['dosage'].' ( '.$med['eyes'].' ) for '.$med['no_day'].' '.$med['days_group']?>&nbsp;</strong>
                            <?php }$a++;}}else {?>
                            <?php if($override->selectData4('prescription','status',0,'patient_id',$_GET['p'],'status',0,'checkup_id',$_GET['c'])){
                                $a=1; foreach($override->selectData('prescription','status',0,'patient_id',$_GET['p'],'checkup_id',$_GET['c']) as $med){$ds[$a]=$med;$getMed = $override->get('medicine','id',$med['medicine_id']);$md[$a]=$getMed[0]['name'];?>
                                    &nbsp;&nbsp;<hr><strong style="font-size: 14px"><input type="checkbox" name="med<?=$a?>" value="<?=$getMed[0]['id']?>" checked>&nbsp;&nbsp;<?=$getMed[0]['name'].' : ( '.$med['quantity'].' ) '.$med['dosage'].' ( '.$med['eyes'].' ) for '.$med['no_day'].' '.$med['days_group']?>&nbsp;</strong>
                                    <?php $a++;}}else{echo 'No medicine Prescribed for this patient';}} ?>
                    <hr>
                    </div>
                    <hr>
                </div><hr>
                <h2>Prescribed Lens</h2>
                <div class="form-group">
                    <div class="col-md-offset-0 col-md-12">
                        <label></label>
                        <?php $rx=$override->get('checkup_record','id',$_GET['c'])?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Eye</th>
                                    <th>Sph</th>
                                    <th>Cyl</th>
                                    <th>Axis</th>
                                    <th>VA</th>
                                    <th>Add</th>
                                    <th>VA</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Right</td>
                                    <td><input name="rx_od_sphere" type="text" class="form-control" value="<?=$rx[0]['rx_OD_sphere']?>" disabled/></td>
                                    <td><input name="rx_cyl" type="text" class="form-control" value="<?=$rx[0]['rx_cyl']?>" disabled/></td>
                                    <td><input name="rx_axis" type="text" class="form-control" value="<?=$rx[0]['rx_axis']?>" disabled/></td>
                                    <td><input name="rx_va" type="text" class="form-control" value="<?=$rx[0]['rx_va']?>" disabled/></td>
                                    <td><input name="rx_add" type="text" class="form-control" value="<?=$rx[0]['rx_add']?>" disabled/></td>
                                    <td><input name="rx_va" type="text" class="form-control" value="<?=$rx[0]['rx_va_2']?>" disabled/></td>
                                </tr>
                                <tr>
                                    <td>Left</td>
                                    <td><input name="add_rx_os_sphere" type="text" class="form-control" value="<?=$rx[0]['add_rx_OS_sphere']?>" disabled/></td>
                                    <td><input name="add_rx_cyl" type="text" class="form-control" value="<?=$rx[0]['add_rx_cyl']?>" disabled/></td>
                                    <td><input name="add_rx_axis" type="text" class="form-control" value="<?=$rx[0]['add_rx_axis']?>" disabled/></td>
                                    <td><input name="add_rx_va" type="text" class="form-control" value="<?=$rx[0]['add_rx_va']?>" disabled/></td>
                                    <td><input name="add_rx_add" type="text" class="form-control" value="<?=$rx[0]['add_rx_add']?>" disabled/></td>
                                    <td><input name="add_rx_va" type="text" class="form-control" value="<?=$rx[0]['add_rx_va_2']?>" disabled/></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-3">
                        <label class="check"><input name="distance_glasses" type="checkbox" value="Distance Glasses" class="icheckbox" <?php if($rx[0]['distance_glasses']){?> checked <?php }?> disabled/> Distance Glasses</label>
                    </div>
                    <div class="col-md-3">
                        <label class="check"><input name="reading_glasses" type="checkbox" value="Reading Glasses" class="icheckbox" <?php if($rx[0]['reading_glasses']){?> checked <?php }?> disabled/> Reading Glasses</label>
                    </div>

                    <div class="col-md-4">
                        <input class="form-control" value="PD : <?=$rx[0]['PD']?>" disabled>
                    </div>
                </div>
                <h4>Doctor Note: </4>
                <div class="form-group">
                    <div class="col-md-12">
                        <textarea class="form-control" rows="3" disabled><?=$rx[0]['other_note']?></textarea>
                    </div>
                    <label class="col-md-12"></label>
                </div>
                <div class="form-group">
                    <div class="col-md-1">
                        <select name="lensGroup"  class="form-control select" >
                            <option value="<?=Input::get('lensGroup')?>"><?php if(Input::get('calc')){if($lens=$override->get('lens','id',Input::get('lensGroup'))){echo$lens[0]['name'];}else{echo 'Select Lens Group';}}else{echo 'Select Lens Group';}?></option>
                            <?php foreach($override->getData('lens') as $lens){?>
                                <option value="<?=$lens['id']?>"><?=$lens['name']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="lens_cat" id="lens_cat" class="form-control select" >
                            <option value="<?=Input::get('lens_cat')?>"><?php if(Input::get('calc')){if($lens=$override->get('lens_category','id',Input::get('lens_cat'))){echo $lens[0]['name'];}else{echo 'Select Lens';}}else{echo 'Select Lens';}?> </option>
                            <?php foreach($override->getDataTable('lens_power','cat_id') as $lensCat){$name=$override->get('lens_category','id',$lensCat['cat_id'])?>
                                <option value="<?=$lensCat['cat_id']?>"><?=$name[0]['name']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="lensType" id="lensType" class="form-control" >
                            <option value="<?=Input::get('lensType')?>"><?php if(Input::get('calc')){if($lens=$override->get('lens_type','id',Input::get('lensType'))){echo $lens[0]['name'];}else{echo 'Select Lens Type';}}else{echo 'Select Lens Type';}?></option>
                            <option value=""><?=Input::get('lensType')?></option>
                        </select>
                    </div>
                    <!--<div class="col-md-4">
                        <?php $powers=$override->getNews('lens_prescription','patient_id',$_GET['p'],'status',0)?>
                        <input type="text" name="power" value="<?php foreach($powers as $power){echo $power['eye'].' : '.$power['lens_power'].' , ';}?>" class="form-control" placeholder="Enter Lens Power">
                    </div>-->
                    <?php if($user->data()->branch_id == 9){$branch_id=8;}else{$branch_id = $user->data()->branch_id;}?>
                    <div class="col-md-3">
                        <select name="lens_power" class="form-control select" data-live-search="true" >
                            <option value="<?=Input::get('lens_power')?>"><?php if(Input::get('lens_power')){$power_l=$override->get('lens_power','id',Input::get('lens_power'));echo$power_l[0]['lens_power']?><?php }else {?>Select Lens Power<?php }?></option>
                            <?php foreach($override->get('lens_power','branch_id',$branch_id) as $lens_power){?>
                                <option value="<?=$lens_power['id']?>"><?=$lens_power['lens_power']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select name="eye" id="eyes" class="form-control select">
                            <?php if(Input::get('calc') || Input::get('submitCash')){?><option value="<?=Input::get('eye')?>"><?php if(Input::get('eye') == 'RE'){echo'Right';}else{echo'Both';}?></option> <?php }?>
                            <option value="BE">Both</option>
                            <option value="RE">Right</option>
                        </select>
                    </div>
                    <div id="other_power" style="display:none;">
                        <label class="col-md-12"></label>
                        <label class="col-md-12"></label>
                        <div class="col-md-offset-5 col-md-3">
                            <select name="lens_power_other" class="form-control select" data-live-search="true" >
                                <option value="<?=Input::get('lens_power_other')?>"><?php if(Input::get('lens_power_other')){$power_l=$override->get('lens_power','id',Input::get('lens_power_other'));echo$power_l[0]['lens_power']?><?php }else {?>Select Lens Power<?php }?></option>
                                <?php foreach($override->get('lens_power','branch_id',$branch_id) as $lens_power){?>
                                    <option value="<?=$lens_power['id']?>"><?=$lens_power['lens_power']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select name="eyes_other"  class="form-control select">
                                <option value="LE">Left</option>
                            </select>
                        </div>
                    </div>
                    <?php if(Input::get('calc')){?>
                        <div class="col-md-2" id="lensPrice">
                            <input type="hidden" name="lens_cost" value="<?=$lensTotalCost?>" class="form-control" placeholder="0">
                            <input type="number" name="lens_cost" value="<?=$lensTotalCost?>" class="form-control" placeholder="0" disabled>
                        </div>
                    <?php }?>
                </div>
                <hr>
                <h2>Frame</h2>
                <div class="form-group">
                    <div class="col-md-3">
                        <select name="frameBrand" id="brand" class="form-control select" data-live-search="true" title="Select Frame Brand">
                            <option value="<?=Input::get('frameBrand')?>"><?php if(Input::get('calc')){if($name=$override->get('frame_brand','id',Input::get('frameBrand'))){echo $name[0]['name'];}else{echo'Select Frame Brand';}?><?php }else{?>Select Frame Brand<?php }?></option>
                            <?php foreach($override->getData('frame_brand') as $brand){?>
                                <option value="<?=$brand['id']?>"><?=$brand['name']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <label class="col-md-1"></label>
                    <div class="col-md-3">
                        <select name="frameModel" id="model" class="form-control">
                            <?php if(Input::get('calc')){foreach($override->get('frame_model','id',Input::get('frameModel')) as $model){?>
                                <option value="<?=$model['id']?>"><?=$model['model']?></option>
                            <?php }}else {?>
                                <option value="">Frame Model</option>
                            <?php }?>
                        </select>
                    </div>
                    <label class="col-md-1"></label>
                    <div class="col-md-2">
                        <select name="frameSize" id="size" class="form-control">
                            <?php if(Input::get('calc')){foreach($override->get('frames','id',Input::get('frameSize')) as $size){?>
                                <option value="<?=$size['id']?>"><?=$size['frame_size']?></option>
                            <?php }}else {?>
                                <option value="">Frame Size</option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-md-2" id="frameCost">
                        <input type="hidden" name="frame_cost" class="form-control" value="<?=$frameCost?>">
                        <input type="number" name="frame_cost" class="form-control" value="<?=$frameCost?>" disabled>
                    </div>
                </div><hr>
                    <?php if($lensNotification){?>
                        <div class="alert alert-info" role="alert">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <strong>Oops!&nbsp;</strong> <?=$lensNotification?>
                        </div>
                    <?php }?>
                    <?php if(Input::get('calc')){?>
                        <h4>Payment Details</h4>
                        <div class="form-group">
                            <label class="col-md-1">Total Cost : </label>
                            <div class="col-md-3">
                                <input name="totalCost" type="hidden" class="form-control" placeholder="" value="<?=$totalCost?>">
                                <input name="totalCost" type="text" class="form-control" placeholder="" value="<?=$totalCost?>" disabled>
                            </div>
                            <label class="col-md-4"></label>
                            <label class="col-md-1">Payment : </label>
                            <div class="col-md-3">
                                <input name="payment" type="text" class="form-control" placeholder="" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <div class="col-md-offset-0 col-md-4">
                                    <label class="check"><input type="checkbox" class="" name="dscnt" id="discount" value="1"/> <strong>Give Discount</strong> </label>
                                </div>
                            </div>
                        </div>
                        <div id="ds" style="display:none;" class="col-md-offset-5 col-md-1"><img src='img/owl/AjaxLoader.gif' width="32" height="32" /><br>Loading..</div>
                        <div id="disc"></div>
                    <?php }?>
                    <div class="pull-right">
                        <?php if(Input::get('calc')){?>
                            <input type="submit" name="submitCash" value="Submit" class="btn btn-success">
                        <?php }?>
                        <input type="submit" name="calc" value="Calculate Cost" class="btn btn-info">
                    </div>
                </form>
            <?php }
            elseif($_GET['id'] == 1 && $user->data()->access_level == 3){?>
                <h1>Insurance Payment Details</h1>
                <h3>&nbsp;</h3>
                <?php $checkUp=$override->getNews('test_performed','checkup_id',$_GET['c'],'patient_id',$_GET['p']);$cCost=0;
                if($checkUp){foreach($checkUp as $chCost){
                    $cPrice=$override->get('test_list','id',$chCost['test_id']);
                    $cCost += $cPrice[0]['insurance_price'];
                }}
                ?>
                <form role="form" class="form-horizontal" method="post">
                <div class="form-group">
                    <label class="col-md-1"><strong>PATIENT NAME : </strong></label>
                    <div class="col-md-4">
                        <input name="firstname" type="text" class="form-control" placeholder="FIRSTNAME " VALUE="<?=$patient[0]['firstname'].' '.$patient[0]['lastname'].'  '.$patient[0]['phone_number']?>" disabled>
                    </div>
                    <label class="col-md-1"><strong></strong></label>
                    <label class="col-md-1"><strong>CHECKUP COST : </strong></label>
                    <div class="col-md-2">
                        <input name="checkupCost" type="hidden" class="form-control" placeholder="" VALUE="<?=$cCost?>" >
                        <input  type="number" class="form-control" placeholder="" VALUE="<?=$cCost?>" disabled>
                    </div>
                    <label class="col-md-1"><strong></strong></label>
                </div><hr>
                <div class="form-group">
                    <label class="col-md-1">Insurance : </label>
                    <div class="col-md-4">
                        <input type="text" name="insurance_no" class="form-control" value="<?=$patient[0]['health_insurance']?>" required="">
                    </div>
                    <label class="col-md-1"> </label>
                    <label class="col-md-1">Member No: </label>
                    <div class="col-md-4">
                        <input type="text" name="dependent_no" class="form-control" value="<?=$patient[0]['dependent_no']?>" required="">
                    </div>
                </div><hr>
                <h2>Prescribed Medicine</h2>
                <div class="form-group">
                    <div class="col-md-offset-0 col-md-8">
                        <?php if(Input::get('calc')){$a=1; foreach($override->selectData('prescription','status',0,'patient_id',$_GET['p'],'checkup_id',$_GET['c']) as $med){
                            $getMed = $override->get('medicine','id',$med['medicine_id']);if($medCn[$a] ==1){?>
                                &nbsp;&nbsp;<hr><strong style="font-size: 14px"><input type="checkbox" name="med<?=$a?>" value="<?=$getMed[0]['id']?>" checked>&nbsp;&nbsp;<?=$getMed[0]['name'].' : ( '.$med['quantity'].' ) '.$med['dosage'].' ( '.$med['eyes'].' ) for '.$med['no_day'].' '.$med['days_group']?>&nbsp;</strong>
                            <?php }else{?>
                                &nbsp;&nbsp;<hr><strong style="font-size: 14px"><input type="checkbox" name="med<?=$a?>" value="<?=$getMed[0]['id']?>" checked>&nbsp;&nbsp;<?=$getMed[0]['name'].' : ( '.$med['quantity'].' ) '.$med['dosage'].' ( '.$med['eyes'].' ) for '.$med['no_day'].' '.$med['days_group']?>&nbsp;</strong>
                            <?php }$a++;}}else {?>
                            <?php if($override->selectData4('prescription','status',0,'patient_id',$_GET['p'],'status',0,'checkup_id',$_GET['c'])){
                                $a=1; foreach($override->selectData('prescription','status',0,'patient_id',$_GET['p'],'checkup_id',$_GET['c']) as $med){$ds[$a]=$med;$getMed = $override->get('medicine','id',$med['medicine_id']);$md[$a]=$getMed[0]['name'];?>
                                    &nbsp;&nbsp;<hr><strong style="font-size: 14px"><input type="checkbox" name="med<?=$a?>" value="<?=$getMed[0]['id']?>" checked>&nbsp;&nbsp;<?=$getMed[0]['name'].' : ( '.$med['quantity'].' ) '.$med['dosage'].' ( '.$med['eyes'].' ) for '.$med['no_day'].' '.$med['days_group']?>&nbsp;</strong>
                                    <?php $a++;}}else{echo 'No medicine Prescribed for this patient';}} ?>
                        <hr>
                    </div>
                    <hr>
                </div><hr>
                <h2>Prescribed Lens</h2>
                <div class="form-group">
                    <div class="col-md-offset-0 col-md-12">
                        <label></label>
                        <?php $rx=$override->get('checkup_record','id',$_GET['c'])?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Eye</th>
                                    <th>Sph</th>
                                    <th>Cyl</th>
                                    <th>Axis</th>
                                    <th>VA</th>
                                    <th>Add</th>
                                    <th>VA</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Right</td>
                                    <td><input name="rx_od_sphere" type="text" class="form-control" value="<?=$rx[0]['rx_OD_sphere']?>" disabled/></td>
                                    <td><input name="rx_cyl" type="text" class="form-control" value="<?=$rx[0]['rx_cyl']?>" disabled/></td>
                                    <td><input name="rx_axis" type="text" class="form-control" value="<?=$rx[0]['rx_axis']?>" disabled/></td>
                                    <td><input name="rx_va" type="text" class="form-control" value="<?=$rx[0]['rx_va']?>" disabled/></td>
                                    <td><input name="rx_add" type="text" class="form-control" value="<?=$rx[0]['rx_add']?>" disabled/></td>
                                    <td><input name="rx_va" type="text" class="form-control" value="<?=$rx[0]['rx_va_2']?>" disabled/></td>
                                </tr>
                                <tr>
                                    <td>Left</td>
                                    <td><input name="add_rx_os_sphere" type="text" class="form-control" value="<?=$rx[0]['add_rx_OS_sphere']?>" disabled/></td>
                                    <td><input name="add_rx_cyl" type="text" class="form-control" value="<?=$rx[0]['add_rx_cyl']?>" disabled/></td>
                                    <td><input name="add_rx_axis" type="text" class="form-control" value="<?=$rx[0]['add_rx_axis']?>" disabled/></td>
                                    <td><input name="add_rx_va" type="text" class="form-control" value="<?=$rx[0]['add_rx_va']?>" disabled/></td>
                                    <td><input name="add_rx_add" type="text" class="form-control" value="<?=$rx[0]['add_rx_add']?>" disabled/></td>
                                    <td><input name="add_rx_va" type="text" class="form-control" value="<?=$rx[0]['add_rx_va_2']?>" disabled/></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-3">
                        <label class="check"><input name="distance_glasses" type="checkbox" value="Distance Glasses" class="icheckbox" <?php if($rx[0]['distance_glasses']){?> checked <?php }?> disabled/> Distance Glasses</label>
                    </div>
                    <div class="col-md-3">
                        <label class="check"><input name="reading_glasses" type="checkbox" value="Reading Glasses" class="icheckbox" <?php if($rx[0]['reading_glasses']){?> checked <?php }?> disabled/> Reading Glasses</label>
                    </div>
                    <div class="col-md-4">
                        <input class="form-control" value="PD : <?=$rx[0]['PD']?>" disabled>
                    </div>
                </div>
                <h4>Doctor Note: </h4>
                <div class="form-group">
                    <div class="col-md-12">
                        <textarea class="form-control" rows="3" disabled><?=$rx[0]['other_note']?></textarea>
                    </div>
                    <label class="col-md-12"></label>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        <select name="lensGroup"  class="form-control select" >
                            <option value="<?=Input::get('lensGroup')?>"><?php if(Input::get('calc')){if($lens=$override->get('lens','id',Input::get('lensGroup'))){echo$lens[0]['name'];}else{echo 'Select Lens Group';}}else{echo 'Select Lens Group';}?></option>
                            <?php foreach($override->getData('lens') as $lens){?>
                                <option value="<?=$lens['id']?>"><?=$lens['name']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="lens_cat" id="lens_cat" class="form-control select" >
                            <option value="<?=Input::get('lens_cat')?>"><?php if(Input::get('calc')){if($lens=$override->get('lens_category','id',Input::get('lens_cat'))){echo $lens[0]['name'];}else{echo 'Select Lens';}}else{echo 'Select Lens';}?> </option>
                            <?php foreach($override->getDataTable('lens_power','cat_id') as $lensCat){$name=$override->get('lens_category','id',$lensCat['cat_id'])?>
                                <option value="<?=$lensCat['cat_id']?>"><?=$name[0]['name']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="lensType" id="lensType" class="form-control" >
                            <option value="<?=Input::get('lensType')?>"><?php if(Input::get('calc')){if($lens=$override->get('lens_type','id',Input::get('lensType'))){echo $lens[0]['name'];}else{echo 'Select Lens Type';}}else{echo 'Select Lens Type';}?></option>
                            <option value=""><?=Input::get('lensType')?></option>
                        </select>
                    </div>
                    <!--<div class="col-md-4">
                        <?php $powers=$override->getNews('lens_prescription','patient_id',$_GET['p'],'status',0)?>
                        <input type="text" name="power" value="<?php foreach($powers as $power){echo $power['eye'].' : '.$power['lens_power'].' , ';}?>" class="form-control" placeholder="Enter Lens Power">
                    </div>-->
                    <?php if($user->data()->branch_id == 9){$branch_id=8;}else{$branch_id = $user->data()->branch_id;}?>
                    <div class="col-md-3">
                        <select name="lens_power" class="form-control select" data-live-search="true" >
                            <option value="<?=Input::get('lens_power')?>"><?php if(Input::get('lens_power')){$power_l=$override->get('lens_power','id',Input::get('lens_power'));echo$power_l[0]['lens_power']?><?php }else {?>Select Lens Power<?php }?></option>
                            <?php foreach($override->get('lens_power','branch_id',$branch_id) as $lens_power){?>
                                <option value="<?=$lens_power['id']?>"><?=$lens_power['lens_power']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <select name="eye" id="eyes" class="form-control select">
                            <?php if(Input::get('calc') || Input::get('submitCash')){?><option value="<?=Input::get('eye')?>"><?php if(Input::get('eye') == 'RE'){echo'Right';}else{echo'Both';}?></option> <?php }?>
                            <option value="BE">Both</option>
                            <option value="RE">Right</option>
                        </select>
                    </div>
                    <div id="other_power" style="display:none;">
                        <label class="col-md-12"></label>
                        <label class="col-md-12"></label>
                        <div class="col-md-offset-6 col-md-3">
                            <select name="lens_power_other" class="form-control select" data-live-search="true" >
                                <option value="<?=Input::get('lens_power_other')?>"><?php if(Input::get('lens_power_other')){$power_l=$override->get('lens_power','id',Input::get('lens_power_other'));echo$power_l[0]['lens_power']?><?php }else {?>Select Lens Power<?php }?></option>
                                <?php foreach($override->get('lens_power','branch_id',$user->data()->branch_id) as $lens_power){?>
                                    <option value="<?=$lens_power['id']?>"><?=$lens_power['lens_power']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select name="eyes_other"  class="form-control select">
                                <option value="LE">Left</option>
                            </select>
                        </div>
                    </div>
                    <?php if(Input::get('calc')){?>
                        <div class="col-md-2" id="lensPrice">
                            <input type="hidden" name="lens_cost" value="<?=$lensTotalCost?>" class="form-control" placeholder="0">
                            <input type="number" name="lens_cost" value="<?=$lensTotalCost?>" class="form-control" placeholder="0" disabled>
                        </div>
                    <?php }?>
                </div>
                <h2>Frame</h2>
                <div class="form-group">
                    <div class="col-md-3">
                        <select name="frameBrand" id="brand" class="form-control select" data-live-search="true" title="Select Frame Brand">
                            <option value="<?=Input::get('frameBrand')?>"><?php if(Input::get('calc')){if($name=$override->get('frame_brand','id',Input::get('frameBrand'))){echo $name[0]['name'];}else{echo'Select Frame Brand';}?><?php }else{?>Select Frame Brand<?php }?></option><?php foreach($override->getData('frame_brand') as $brand){?>
                                <option value="<?=$brand['id']?>"><?=$brand['name']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <label class="col-md-1"></label>
                    <div class="col-md-3">
                        <select name="frameModel" id="model" class="form-control">
                            <?php if(Input::get('calc')){foreach($override->get('frame_model','id',Input::get('frameModel')) as $model){?>
                                <option value="<?=$model['id']?>"><?=$model['model']?></option>
                            <?php }}else {?>
                                <option value="">Frame Model</option>
                            <?php }?>
                        </select>
                    </div>
                    <label class="col-md-1"></label>
                    <div class="col-md-2">
                        <select name="frameSize" id="size" class="form-control">
                            <?php if(Input::get('calc')){foreach($override->get('frames','id',Input::get('frameSize')) as $size){?>
                                <option value="<?=$size['id']?>"><?=$size['frame_size']?></option>
                            <?php }}else {?>
                                <option value="">Frame Size</option>
                            <?php }?>
                        </select>
                    </div>
                    <div class="col-md-2" id="frameCost">
                        <input type="hidden" name="frame_cost" class="form-control" value="<?=$frameCost?>">
                        <input type="number" name="frame_cost" class="form-control" value="<?=$frameCost?>" disabled>
                    </div>
                </div><hr>
                    <?php if(Input::get('calc')){?>
                        <h4>Payment Details</h4>
                        <div class="form-group">
                            <label class="col-md-1">Total Cost : </label>
                            <div class="col-md-3">
                                <input name="totalCost" type="hidden" class="form-control" placeholder="" value="<?=$totalCost?>">
                                <input name="totalCost" type="text" class="form-control" placeholder="" value="<?=$totalCost?>" disabled>
                            </div>
                            <label class="col-md-1">&nbsp;&nbsp;Insurance &nbsp;&nbsp;Pay : </label>
                            <div class="col-md-3">
                                <input name="insurance_pay" type="number" class="form-control" placeholder="" value="" >
                            </div>
                            <label class="col-md-1">&nbsp;&nbsp;Addition &nbsp;&nbsp;Payment : </label>
                            <div class="col-md-3">
                                <input name="payment" type="number" class="form-control" placeholder="" value="0">
                            </div>
                        </div>
                    <?php }if($lensNotification){?>
                        <div class="alert alert-info" role="alert">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <strong>Oops!&nbsp;</strong> <?=$lensNotification?>
                        </div>
                    <?php }?>
                    <div class="pull-right">
                        <?php if(Input::get('calc')){?>
                            <input type="submit" name="submitCash" value="Submit" class="btn btn-success">
                        <?php }?>
                        <input type="submit" name="calc" value="Calculate Cost" class="btn btn-info">
                    </div>
                </form>
            <?php }
            elseif($_GET['id'] == 33 && $user->data()->access_level == 3){?>
                <h3>SELECT PATIENT </h3>
                <h3>&nbsp;</h3>
                <form role="form" class="form-horizontal" method="post">
                    <div class="form-group">
                        <label class="col-md-2">Searching Criteria : </label>
                        <div class="col-md-10">
                            <input type="radio" name="criteria" value="firstname" checked>&nbsp;&nbsp;By Firstname&nbsp;&nbsp;
                            <input type="radio" name="criteria" value="lastname" >&nbsp;&nbsp;By Lastname&nbsp;&nbsp;
                            <input type="radio" name="criteria" value="phone_number" >&nbsp;&nbsp;By Phone Number&nbsp;&nbsp;
                            <input type="radio" name="criteria" value="id" >&nbsp;&nbsp;By PID&nbsp;&nbsp;
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-1 col-md-10 col-md-offset-0">
                            <!--<select name="patient_name" class="form-control select" data-live-search="true" required="">
                                <option value="">Select Patient</option>
                                <?php foreach($override->getData('patient') as $patient){?>
                                    <option value="<?=$patient['id']?>"><?=$patient['firstname'].' '.$patient['lastname'].'  '.$patient['phone_number']?></option>
                                <?php }?>
                            </select>-->
                            <input type="text" name="name" class="form-control" placeholder="Enter Patient Firstname / Lastname / Phone Number / PID" required="">
                        </div>
                        <div class="col-md-2">
                            <input type="submit" name="returnPatient" value="Search Patient" class="btn btn-info">
                        </div>
                    </div>
                </form><br>
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
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $x=0;if($searchValue){
                                foreach($searchValue as $patient){?>
                                    <tr>
                                        <td><?=$patient['firstname'].' '.$patient['lastname']?></td>
                                        <td><?=$patient['phone_number']?></td>
                                        <td><?=$patient['sex']?></td>
                                        <td><?=$patient['age']?></td>
                                        <td>
                                            <form method="post">
                                                <input type="hidden" name="patient_name" value="<?=$patient['id']?>">
                                                <input type="submit" name="sendDoctor" value="Send to Doctor" class="btn btn-info">
                                                <a href="#modal<?=$x?>" class="btn btn-info btn-rounded btn-condensed btn-sm" data-toggle="modal" ><span class="fa fa-info-circle"></span></a>
                                            </form>
                                        </td>
                                     </tr>
                                    <div class="modal" id="modal<?=$x?>" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                                    <h4 class="modal-title" id="defModalHead<?=$x?>">Patient Information</h4>
                                                </div>
                                                <form method="post">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">First Name</label>
                                                            <div class="col-md-10">
                                                                <input type="text" name="firstname" class="form-control" value="<?=$patient['firstname']?>" />
                                                            </div>
                                                        </div>
                                                        <label></label>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Surname</label>
                                                            <div class="col-md-10">
                                                                <input type="text" name="surname" class="form-control" value="<?=$patient['lastname']?>"/>
                                                            </div>
                                                        </div>
                                                        <label></label>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Sex</label>
                                                            <div class="col-md-10">
                                                                <select name="sex" class="form-control select" >
                                                                    <option value="<?=$patient['sex']?>"><?=$patient['sex']?></option>
                                                                    <option value="Male">Male</option>
                                                                    <option value="Female">Female</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <label></label>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Age</label>
                                                            <div class="col-md-10">
                                                                <input type="number" min="1" name="age" class="form-control" value="<?=$patient['age']?>"/>
                                                            </div>
                                                        </div>
                                                        <label></label>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Health Ins</label>
                                                            <div class="col-md-10">
                                                                <input type="text" name="health_insurance" class="form-control" value="<?=$patient['health_insurance']?>"/>
                                                            </div>
                                                        </div>
                                                        <label></label>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Insurance No.</label>
                                                            <div class="col-md-10">
                                                                <input type="text" name="dependent_no" class="form-control" value="<?=$patient['dependent_no']?>"/>
                                                            </div>
                                                        </div>
                                                        <label></label>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Address</label>
                                                            <div class="col-md-10">
                                                                <input type="text" name="address" class="form-control" value="<?=$patient['address']?>"/>
                                                            </div>
                                                        </div>
                                                        <label></label>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Occupation</label>
                                                            <div class="col-md-10">
                                                                <input type="text" name="occupation" class="form-control" value="<?=$patient['occupation']?>"/>
                                                            </div>
                                                        </div>
                                                        <label></label>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Phone</label>
                                                            <div class="col-md-10">
                                                                <input type="text"  name="phone_number" class="form-control" value="<?=$patient['phone_number']?>"/>
                                                            </div>
                                                        </div>
                                                        <label></label>
                                                        <div class="form-group">
                                                            <label class="col-md-2 control-label">Email</label>
                                                            <div class="col-md-10">
                                                                <input type="email" name="email_address" class="form-control" value="<?=$patient['email_address']?>"/>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="id" value="<?=$patient['id']?>">
                                                        <input type="submit" name="updatePatientInfo" value="Update Patient Info" class="btn btn-success" >
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php $x++;}}?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php }
            elseif($_GET['id'] == 3 && $user->data()->access_level == 3){?>
                <h3>Pending Payment</h3>
                <h3>&nbsp;</h3>
                <form role="form" class="form-horizontal" method="post">
                    <div class="form-group">
                        <label class="col-md-1"><strong>PATIENT NAME : </strong></label>
                        <div class="col-md-3">
                            <input name="firstname" type="text" class="form-control" placeholder="FIRSTNAME " VALUE="<?=$patient[0]['firstname'].' '.$patient[0]['lastname'].'  '.$patient[0]['phone_number']?>" disabled>
                        </div>
                        <label class="col-md-1"><strong></strong></label>
                        <label class="col-md-1"><strong>PAID AMOUNT : </strong></label>
                        <div class="col-md-2">
                            <input name="checkupCost" type="text" class="form-control" placeholder="" VALUE="<?=number_format($py)?>" disabled>
                        </div>
                        <label class="col-md-1"><strong></strong></label>
                        <label class="col-md-1"><strong>DISCOUNT AMOUNT: </strong></label>
                        <div class="col-md-2">
                            <input name="checkupCost" type="text" class="form-control" placeholder="" VALUE="<?=number_format($dsc)?>" disabled>
                        </div>
                    </div><hr>
                    <h4>Payment Details</h4>
                    <h4>&nbsp;</h4>
                    <div class="form-group">
                        <label class="col-md-1">REMAINING AMOUNT : </label>
                        <div class="col-md-3">
                            <input name="remainingCost" type="hidden" class="form-control" placeholder="" value="<?=$totalCost?>">
                            <input name="remainingCost" type="text" class="form-control" placeholder="" value="<?=number_format($totalCost)?>" disabled>
                        </div>
                        <label class="col-md-4"></label>
                        <label class="col-md-1">Payment : </label>
                        <div class="col-md-3">
                            <input name="payment" type="number" class="form-control" placeholder="" value="" min="0" required="">
                        </div>
                    </div>
                    <div class="pull-right">
                        <input type="submit" name="pendingCash" value="Submit" class="btn btn-success">
                    </div>
                </form>
            <?php }
            elseif($_GET['id'] == 4 && $user->data()->access_level == 4){?>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Lens Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Clinic Branch &nbsp;</label>
                                <div class="col-md-10">
                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                        <option value="">Select Clinic Branch</option>
                                        <?php foreach($override->get('clinic_branch','id',$user->data()->branch_id) as $branch){?>
                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Lens Group &nbsp;</label>
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
                                <label class="col-md-2 control-label">Lens Type &nbsp;</label>
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
                                <label class="col-md-2 control-label">Lens Category &nbsp;</label>
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
                                <label class="col-md-2 control-label">Lens Power &nbsp;</label>
                                <div class="col-md-10">
                                    <input type="text" name="lens_power" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Price &nbsp;</label>
                                <div class="col-md-10">
                                    <input type="number" name="price" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Quantity &nbsp;</label>
                                <div class="col-md-10">
                                    <input type="number" min="1" name="quantity" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Re-Order Level &nbsp;</label>
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
            <?php }
            elseif($_GET['id'] == 5 && $user->data()->access_level == 4){?>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Frame Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Clinic Branch &nbsp;</label>
                                <div class="col-md-10">
                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                        <option value="">Select Clinic Branch</option>
                                        <?php foreach($override->get('clinic_branch','id',$user->data()->branch_id) as $branch){?>
                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Select Category &nbsp;</label>
                                <div class="col-md-10">
                                    <select name="category" class="form-control select" data-live-search="true" required="">
                                        <option value="">Select Category</option>
                                        <option value="1">Frame</option>
                                        <option value="2">Sun Glass</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Frame Brand &nbsp;</label>
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
                                <label class="col-md-2 control-label">Frame Model &nbsp;</label>
                                <div class="col-md-10">
                                    <input type="text" name="model" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Frame Size &nbsp;</label>
                                <div class="col-md-10">
                                    <input type="text" name="size" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Price &nbsp;</label>
                                <div class="col-md-10">
                                    <input type="number" name="price" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Quantity &nbsp;</label>
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
            <?php }
            elseif($_GET['id'] == 6 && $user->data()->access_level == 4){?>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Medicine Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Clinic Branch &nbsp;</label>
                                <div class="col-md-10">
                                    <select name="clinic_branch" class="form-control select" data-live-search="true">
                                        <option value="">Select Clinic Branch </option>
                                        <?php foreach($override->get('clinic_branch','id',$user->data()->branch_id) as $branch){?>
                                            <option value="<?=$branch['id']?>"><?=$branch['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Medicine Name &nbsp;</label>
                                <div class="col-md-10">
                                    <input type="text" name="name" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Manufactured By &nbsp;</label>
                                <div class="col-md-10">
                                    <input type="text" name="manufacture" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Description &nbsp;</label>
                                <div class="col-md-10">
                                    <textarea name="description" rows="4" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Price &nbsp;</label>
                                <div class="col-md-10">
                                    <input type="number" name="price" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Quantity &nbsp;</label>
                                <div class="col-md-10">
                                    <input type="number" min="0" name="quantity" class="form-control" value=""/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Manufactured Date &nbsp;</label>
                                <div class="col-md-4">
                                    <input type="text" name="man_date" class="form-control datepicker" value="Select date">
                                </div>
                                <label class="col-md-2 control-label">Expired Date &nbsp;</label>
                                <div class="col-md-4">
                                    <input type="text" name="ex_date" class="form-control datepicker" value="Select date">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Re-Order Level &nbsp;</label>
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
            <?php }
            elseif($_GET['id'] == 7 && $user->data()->access_level == 3){?>
                <div class="panel-body padding-0">
                <div class="panel-body">
                <h3>Lens Repair</h3>
                <form class="form-horizontal" role="form" method="post">
                <div class="form-group">
                    <div class="col-md-8">
                        <select name="patient" class="form-control select" data-live-search="true">
                            <option value="<?=Input::get('patient')?>"><?php if(Input::get('calc')){$p=$override->get('patient','id',Input::get('patient'));echo $p[0]['firstname'].' '.$p[0]['lastname'].' '.$p[0]['phone_number'];}else{?>Select Patient<?php }?> </option>
                            <?php foreach($override->getData('patient') as $patient){?>
                                <option value="<?=$patient['id']?>"><?=$patient['firstname'].'  '.$patient['lastname'].'  '.$patient['phone_number']?></option>
                            <?php }?>
                        </select>
                    </div>
                    <label class="col-md-0">&nbsp;&nbsp;&nbsp;</label>
                    <label class="col-md-0">&nbsp;&nbsp;&nbsp;</label>
                    <label class="col-md-0">&nbsp;&nbsp;&nbsp;&nbsp;Eye : </label>
                    <label class="col-md-0">&nbsp;&nbsp;&nbsp;</label>
                    <label class="control-label">
                        <input id="BE_1" type="radio" name="eye_l" value="both" checked>
                        <span class="outer"><span class="inner"></span></span>&nbsp;Both&nbsp;&nbsp;&nbsp;
                    </label>
                    <label class="control-label">
                        <input id="RE_1" type="radio" name="eye_l" VALUE="RE">
                        <span class="outer"><span class="inner"></span></span> &nbsp;Right&nbsp;&nbsp;&nbsp;
                    </label>
                    <label class="control-label">
                        <input id="LE_1" type="radio" name="eye_l" value="LE">
                        <span class="outer"><span class="inner"></span></span> &nbsp;Left&nbsp;&nbsp;&nbsp;
                    </label>
                </div>
                    <label></label>
                    <div class="form-group">
                        <div class="col-md-offset-1 col-md-10">
                            <div id="waitM" style="display:none;" class="col-md-offset-5 col-md-1"><img src='img/owl/AjaxLoader.gif' width="32" height="32" /><br>Loading..</div>
                            <div class="table-responsive" id="lens_desc">
                                <table class="table table-bordered">
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
                                        <td><input name="rx_od_sphere" type="text" class="form-control" value="<?=Input::get('rx_od_sphere')?>"/></td>
                                        <td><input name="rx_cyl" type="text" class="form-control" value="<?=Input::get('rx_cyl')?>"/></td>
                                        <td><input name="rx_axis" type="text" class="form-control" value="<?=Input::get('rx_axis')?>"/></td>
                                        <td><input name="rx_va" type="text" class="form-control" value="<?=Input::get('rx_va')?>"/></td>
                                        <td><input name="rx_add" type="text" class="form-control" value="<?=Input::get('rx_add')?>"/></td>
                                    </tr>
                                    <tr>
                                        <td>Left</td>
                                        <td><input name="add_rx_os_sphere" type="text" class="form-control" value="<?=Input::get('add_rx_os_sphere')?>"/></td>
                                        <td><input name="add_rx_cyl" type="text" class="form-control" value="<?=Input::get('add_rx_cyl')?>"/></td>
                                        <td><input name="add_rx_axis" type="text" class="form-control" value="<?=Input::get('add_rx_axis')?>"/></td>
                                        <td><input name="add_rx_va" type="text" class="form-control" value="<?=Input::get('add_rx_va')?>"/></td>
                                        <td><input name="add_rx_add" type="text" class="form-control" value="<?=Input::get('add_rx_add')?>"/></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                            <label class="check"><input name="distance_glasses" type="checkbox" value="Distance Glasses" class="icheckbox"/> Distance Glasses</label>
                        </div>
                        <div class="col-md-2">
                            <label class="check"><input name="reading_glasses" type="checkbox" value="Reading Glasses" class="icheckbox"/> Reading Glasses</label>
                        </div>
                        <!--<div class="col-md-4">
                            <select name="lens" id="lens_pow" class="form-control select" data-live-search="true">
                                <option value="">Select Lens</option>
                                <?php foreach($override->getData('lens_category') as $lens_cat){?>
                                    <option value="<?=$lens_cat['id']?>"><?=$lens_cat['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <label id=""></label>
                        <div class="col-md-2" id="p">
                            <!--<select name="lens_power" class="form-control select">
                                <option value="">Lens Power</option>
                            </select>-->
                            <!--<input type="text" name="lens_power" class="form-control" placeholder="Enter Lens Power">
                        </div>
                        <div class="col-md-2">
                            <select name="eye" id="eye" class="form-control select">
                                <option value="">Select Eye</option>
                                <option value="Both">Both Eyes</option>
                                <option value="RE">Right Eye</option>
                                <option value="LE">Left Eye</option>
                            </select>
                        </div>-->
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                            <select name="lensGroup"  class="form-control select" >
                                <option value="<?=Input::get('lensGroup')?>"><?php if(Input::get('calc')){if($lens=$override->get('lens','id',Input::get('lensGroup'))){echo$lens[0]['name'];}else{echo 'Select Lens Group';}}else{echo 'Select Lens Group';}?></option>
                                <?php foreach($override->getData('lens') as $lens){?>
                                    <option value="<?=$lens['id']?>"><?=$lens['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="lens_cat" id="lens_cat" class="form-control select" >
                                <option value="<?=Input::get('lens_cat')?>"><?php if(Input::get('calc')){if($lens=$override->get('lens_category','id',Input::get('lens_cat'))){echo $lens[0]['name'];}else{echo 'Select Lens';}}else{echo 'Select Lens';}?> </option>
                                <?php foreach($override->getDataTable('lens_power','cat_id') as $lensCat){$name=$override->get('lens_category','id',$lensCat['cat_id'])?>
                                    <option value="<?=$lensCat['cat_id']?>"><?=$name[0]['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="lensType" id="lensType" class="form-control" >
                                <option value="<?=Input::get('lensType')?>"><?php if(Input::get('calc')){if($lens=$override->get('lens_type','id',Input::get('lensType'))){echo $lens[0]['name'];}else{echo 'Select Lens Type';}}else{echo 'Select Lens Type';}?></option>
                                <option value=""><?=Input::get('lensType')?></option>
                            </select>
                        </div>
                        <!--<div class="col-md-4">
                        <?php $powers=$override->getNews('lens_prescription','patient_id',$_GET['p'],'status',0)?>
                        <input type="text" name="power" value="<?php foreach($powers as $power){echo $power['eye'].' : '.$power['lens_power'].' , ';}?>" class="form-control" placeholder="Enter Lens Power">
                    </div>-->
                        <div class="col-md-3">
                            <select name="lens_power" class="form-control select" data-live-search="true" >
                                <option value="<?=Input::get('lens_power')?>"><?php if(Input::get('lens_power')){$power_l=$override->get('lens_power','id',Input::get('lens_power'));echo$power_l[0]['lens_power']?><?php }else {?>Select Lens Power<?php }?></option>
                                <?php foreach($override->get('lens_power','branch_id',$user->data()->branch_id) as $lens_power){?>
                                    <option value="<?=$lens_power['id']?>"><?=$lens_power['lens_power']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <select name="eye" id="eyes" class="form-control select">
                                <?php if(Input::get('calc') || Input::get('submitCash')){?><option value="<?=Input::get('eye')?>"><?php if(Input::get('eye') == 'RE'){echo'Right';}else{echo'Both';}?></option> <?php }?>
                                <option value="BE">Both</option>
                                <option value="RE">Right</option>
                            </select>
                        </div>
                        <div id="other_power" style="display:none;">
                            <label class="col-md-12"></label>
                            <label class="col-md-12"></label>
                            <div class="col-md-offset-6 col-md-3">
                                <select name="lens_power_other" class="form-control select" data-live-search="true" >
                                    <option value="<?=Input::get('lens_power_other')?>"><?php if(Input::get('lens_power_other')){$power_l=$override->get('lens_power','id',Input::get('lens_power_other'));echo$power_l[0]['lens_power']?><?php }else {?>Select Lens Power<?php }?></option>
                                    <?php foreach($override->get('lens_power','branch_id',$user->data()->branch_id) as $lens_power){?>
                                        <option value="<?=$lens_power['id']?>"><?=$lens_power['lens_power']?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <select name="eyes_other"  class="form-control select">
                                    <option value="LE">Left</option>
                                </select>
                            </div>
                        </div>
                        <?php if(Input::get('calc')){?>
                            <div class="col-md-2" id="lensPrice">
                                <input type="hidden" name="lens_cost" value="<?=$lensTotalCost?>" class="form-control" placeholder="0">
                                <input type="number" name="lens_cost" value="<?=$lensTotalCost?>" class="form-control" placeholder="0" disabled>
                            </div>
                        <?php }?>
                    </div>
                    <div id="wait" style="display:none;" class="col-md-offset-5 col-md-1"><img src='img/owl/AjaxLoader.gif' width="32" height="32" /><br>Loading..</div>
                    <div class="col-md-offset-4" id="other_eye"></div>
                    <?php if(Input::get('calc')){?>
                        <br>
                        <h4>Payment Details</h4><br>
                        <div class="form-group">
                            <label class="control-label">
                                <input id="csh" type="radio" name="pay" VALUE="C" checked>
                                <span class="outer"><span class="inner"></span></span> &nbsp;CASH&nbsp;&nbsp;&nbsp;
                            </label>
                            <label class="control-label">
                                <input id="ins" type="radio" name="pay" value="I">
                                <span class="outer"><span class="inner"></span></span> &nbsp;INSURANCE&nbsp;&nbsp;&nbsp;
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="col-md-1">Total Cost : </label>
                            <div class="col-md-3">
                                <input name="totalCost" type="hidden" class="form-control" placeholder="" value="<?=$totalCost?>">
                                <input name="totalCost" type="text" class="form-control" placeholder="" value="<?=$totalCost?>" disabled>
                            </div>
                            <div id="ins_pay" style="display:none;" >
                                <label class="col-md-1">&nbsp;&nbsp;Insurance &nbsp;&nbsp;Pay : </label>
                                <div class="col-md-3">
                                    <input name="insurance_pay" type="number" class="form-control" placeholder="" value="0" >
                                </div>
                                <label class="col-md-1">&nbsp;&nbsp;Addition &nbsp;&nbsp;Payment : </label>
                                <div class="col-md-3">
                                    <input name="ins_payment" type="number" class="form-control" placeholder="" value="0">
                                </div>
                            </div>
                            <div id="pay">
                                <label class="col-md-4"></label>
                                <label class="col-md-1">Payment : </label>
                                <div class="col-md-3">
                                    <input name="payment" type="text" class="form-control" placeholder="" value="">
                                </div>
                            </div>
                        </div>
                    <?php }?>
                    <div class="pull-right">
                        <?php if(Input::get('calc')){?>
                            <input type="submit" name="submitCash" value="Submit" class="btn btn-success">
                        <?php }?>
                        <input type="submit" name="calc" value="Calculate Cost" class="btn btn-info">
                    </div>
                </form>
                </div>
                </div>
            <?php }
            elseif($_GET['id'] == 8 && $user->data()->access_level == 3){?>
                <div class="panel-body padding-0">
                <div class="panel-body">
                    <h3>Patient Prescription Information</h3>
                    <form class="form-horizontal" role="form" method="post">
                        <div class="form-group">
                            <div class="col-md-12">
                                <select name="patient" class="form-control select" data-live-search="true">
                                    <option value="<?=Input::get('patient')?>"><?php if(Input::get('checkLens')){$patient=$override->get('patient','id',Input::get('patient'))?><?=$patient[0]['firstname'].'  '.$patient[0]['lastname'].'  '.$patient[0]['phone_number']?><?php }else{?>Select Patient<?php }?> </option>
                                    <?php foreach($override->getData('patient') as $patient){?>
                                        <option value="<?=$patient['id']?>"><?=$patient['firstname'].'  '.$patient['lastname'].'  '.$patient['phone_number']?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div>
                        <label></label>
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
                                            <th>VA</th>
                                            <th>Add</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>Right</td>
                                            <td><input name="rx_od_sphere" type="text" class="form-control"/></td>
                                            <td><input name="rx_cyl" type="text" class="form-control"/></td>
                                            <td><input name="rx_axis" type="text" class="form-control"/></td>
                                            <td><input name="rx_va" type="text" class="form-control"/></td>
                                            <td><input name="rx_add" type="text" class="form-control"/></td>
                                        </tr>
                                        <tr>
                                            <td>Left</td>
                                            <td><input name="add_rx_os_sphere" type="text" class="form-control"/></td>
                                            <td><input name="add_rx_cyl" type="text" class="form-control"/></td>
                                            <td><input name="add_rx_axis" type="text" class="form-control"/></td>
                                            <td><input name="add_rx_va" type="text" class="form-control"/></td>
                                            <td><input name="add_rx_add" type="text" class="form-control"/></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-2">
                                <label class="check"><input name="distance_glasses" type="checkbox" value="Distance Glasses" class="icheckbox"/> Distance Glasses</label>
                            </div>
                            <div class="col-md-2">
                                <label class="check"><input name="reading_glasses" type="checkbox" value="Reading Glasses" class="icheckbox"/> Reading Glasses</label>
                            </div>
                            <div class="col-md-4">
                                <select name="lens" id="lens_pow" class="form-control select" data-live-search="true">
                                    <option value="">Select Lens</option>
                                    <?php foreach($override->getData('lens_category') as $lens_cat){?>
                                        <option value="<?=$lens_cat['id']?>"><?=$lens_cat['name']?></option>
                                    <?php }?>
                                </select>
                            </div>
                            <label id=""></label>
                            <div class="col-md-2" id="p">
                                <!--<select name="lens_power" class="form-control select">
                                    <option value="">Lens Power</option>
                                </select>-->
                                <input type="text" name="lens_power" class="form-control" placeholder="Enter Lens Power">
                            </div>
                            <div class="col-md-2">
                                <select name="eye" id="eye" class="form-control select">
                                    <option value="">Select Eye</option>
                                    <option value="Both">Both Eyes</option>
                                    <option value="RE">Right Eye</option>
                                    <option value="LE">Left Eye</option>
                                </select>
                            </div>
                        </div>
                        <div id="wait" style="display:none;" class="col-md-offset-5 col-md-1"><img src='img/owl/AjaxLoader.gif' width="32" height="32" /><br>Loading..</div>
                        <div class="col-md-offset-4" id="other_eye"></div>
                        <div class="pull-right">
                            <input type="submit"  name="checkLens" value="Submit" class="btn btn-info">
                        </div>
                    </form>
                </div>
                </div>
            <?php }
            elseif($_GET['id'] == 9 && $user->data()->access_level == 3){?>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Patient Information</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <select name="patient" class="form-control select" data-live-search="true" required="">
                                        <option value="<?=Input::get('patient')?>"><?php if(Input::get('submitFrame')){$patient=$override->get('patient','id',Input::get('patient'));echo $patient[0]['firstname'].'  '.$patient[0]['lastname'].'  '.$patient[0]['phone_number'];}else{?>Select Patient <?php }?></option>
                                        <?php foreach($override->getData('patient') as $patient){?>
                                            <option value="<?=$patient['id']?>"><?=$patient['firstname'].'  '.$patient['lastname'].'  '.$patient['phone_number']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                            </div>
                            <label></label>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <select name="frameBrand" id="brand" class="form-control select" data-live-search="true" title="Select Frame Brand">
                                        <option value="<?=Input::get('frameBrand')?>"><?php if(Input::get('calc')){if($name=$override->get('frame_brand','id',Input::get('frameBrand'))){echo $name[0]['name'];}else{echo'Select Frame Brand';}?><?php }else{?>Select Frame Brand<?php }?></option>
                                        <?php foreach($override->getData('frame_brand') as $brand){?>
                                            <option value="<?=$brand['id']?>"><?=$brand['name']?></option>
                                        <?php }?>
                                    </select>
                                </div>
                                <label class="col-md-1"></label>
                                <div class="col-md-3">
                                    <select name="frameModel" id="model" class="form-control">
                                        <?php if(Input::get('calc')){foreach($override->get('frame_model','id',Input::get('frameModel')) as $model){?>
                                            <option value="<?=$model['id']?>"><?=$model['model']?></option>
                                        <?php }}else {?>
                                            <option value="">Frame Model</option>
                                        <?php }?>
                                    </select>
                                </div>
                                <label class="col-md-1"></label>
                                <div class="col-md-2">
                                    <select name="frameSize" id="size" class="form-control">
                                        <?php if(Input::get('calc')){foreach($override->get('frames','id',Input::get('frameSize')) as $size){?>
                                            <option value="<?=$size['frame_size']?>"><?=$size['frame_size']?></option>
                                        <?php }}else {?>
                                            <option value="">Frame Size</option>
                                        <?php }?>
                                    </select>
                                </div>
                                <div class="col-md-2" id="frameCost">
                                    <input type="hidden" name="frame_cost" class="form-control" value="<?=$frameCost?>">
                                    <input type="number" name="frame_cost" class="form-control" value="<?=$frameCost?>" disabled>
                                </div>
                            </div>
                            <div class="pull-left">
                                <input type="number"  name="payment" placeholder="Enter Payment" class="form-control" required="">
                            </div>
                            <div class="pull-right">
                                <input type="submit"  name="submitFrame" value="Submit" class="btn btn-success">
                            </div>
                        </form>
                    </div>
                </div>
            <?php }
            elseif($_GET['id'] == 10 && $user->data()->access_level == 4){?>
                <div class="panel-body padding-0">
                    <div class="panel-body">
                        <h3>Brand Details</h3>
                        <form class="form-horizontal" role="form" method="post">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Brand Name &nbsp;</label>
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
            <?php }
            elseif($_GET['id'] == 11){?>
                <div class="content-frame">
                    <!-- START CONTENT FRAME TOP -->
                    <div class="content-frame-top">
                        <div class="page-title">
                            <h2><span class="fa fa-pencil"></span> Compose SMS/Emails</h2>
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
                        <div class="page-title">
                            <h4><span class="fa fa-inbox"></span> SMS <small></small></h4>
                        </div>
                        <div class="block">
                            <div class="list-group border-bottom">
                                <a href="info.php?id=11" class="list-group-item"><span class="fa fa-inbox"></span> Inbox &nbsp;(<?=$override->getCount('sms','receiver_id',$user->data()->id)?>)<span class="badge badge-success"><?=$override->rowCounted('sms','receiver_id',$user->data()->id,'status',0)?> Unread</span></a>
                                <a href="#" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
                                <a href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted <span class="badge badge-default">1.4k</span></a>
                            </div>
                        </div>
                        <div class="page-title">
                            <h4><span class="fa fa-inbox"></span> Email <small></small></h4>
                        </div>
                        <div class="block">
                            <div class="list-group border-bottom">
                                <a href="info.php?id=13" class="list-group-item"><span class="fa fa-inbox"></span> Inbox &nbsp;(<?=$override->getCount('emails','receiver_id',$user->data()->id)?>)<span class="badge badge-success"><?=$override->rowCounted('emails','receiver_id',$user->data()->id,'status',0)?> Unread</span></a>
                                <a href="#" class="list-group-item"><span class="fa fa-rocket"></span> Sent</a>
                                <a href="#" class="list-group-item"><span class="fa fa-trash-o"></span> Deleted <span class="badge badge-default">1.4k</span></a>
                            </div>
                        </div>
                    </div>
                    <!-- END CONTENT FRAME LEFT -->

                    <!-- START CONTENT FRAME BODY -->
                    <div class="content-frame-body">
                        <div class="block">
                            <form role="form" class="form-horizontal">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="pull-left">
                                            <button class="btn btn-default"><span class="fa fa-trash-o"></span> Delete Draft</button>
                                        </div>
                                        <div class="pull-right">
                                            <button class="btn btn-danger"><span class="fa fa-envelope"></span> Send Message</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">From:</label>
                                    <div class="col-md-10">
                                        <select class="form-control select">
                                            <option>admin@familyeyecare.co.tz</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">To:</label>
                                    <div class="col-md-9">
                                        <input type="text" class="tagsinput" value="" data-placeholder="add email"/>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-link toggle" data-toggle="mail-cc">Cc</button>
                                    </div>
                                </div>
                                <div class="form-group hidden" id="mail-cc">
                                    <label class="col-md-2 control-label">Cc:</label>
                                    <div class="col-md-10">
                                        <input type="text" class="tagsinput" value="" data-placeholder="add email"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Subject:</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" value="Message Subject"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Attachments:</label>
                                    <div class="col-md-10">
                                        <input type="file" class="file" data-filename-placement="inside"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <textarea class="summernote_email"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="pull-left">
                                            <button class="btn btn-default"><span class="fa fa-trash-o"></span> Delete Draft</button>
                                        </div>
                                        <div class="pull-right">
                                            <button class="btn btn-danger"><span class="fa fa-envelope"></span> Send Message</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                    <!-- END CONTENT FRAME BODY -->
                </div>
            <?php }
            elseif($_GET['id'] == 12 && $user->data()->access_level == 7){?>
                <form role="form" class="form-horizontal" method="post">
                    <div class="form-group">
                        <div class="col-md-4">
                            <input name="customer_name" type="text" class="form-control" placeholder="CUSTOMER NAME" value="">
                        </div>
                        <div class="col-md-3">
                            <select name="product" class="form-control select">
                                <option value="">Select Product</option>
                                <?php foreach($override->getSelectNoRepeat('frame_sales','product_id','emp_id',$user->data()->id,'status',0) as $product){$name=$override->get('sales_product','id',$product['product_id'])?>
                                <option value="<?=$product['product_id']?>"><?=$name[0]['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="batch" class="form-control select">
                                <option value="">Select Batch</option>
                                <?php foreach($override->getSelectNoRepeat('frame_sales','price_per','emp_id',$user->data()->id,'status',0) as $batch){?>
                                    <option value="<?=$batch['price_per']?>"><?=$batch['price_per']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input name="quantity" type="number" min="1" class="form-control" placeholder="Quantity" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <input name="email_address" type="text" class="form-control" placeholder="EMAIL ADDRESS" value="">
                        </div>
                        <label class="col-md-2"></label>
                        <div class="col-md-5">
                            <input name="phone_number" type="text" class="form-control" placeholder="PHONE NUMBER" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <input name="address" type="text" class="form-control" placeholder="ADDRESS" value="">
                        </div>
                        <label class="col-md-2"></label>
                        <div class="col-md-5">
                            <input name="sale_date" type="text" class="form-control datepicker" placeholder="DATE" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <textarea name="notes" class="form-control" rows="5" placeholder="Other Notes"></textarea>
                        </div>
                    </div>
                    <div class="pull-right">
                        <input type="submit" name="submitSales" value="Submit" class="btn btn-success">
                    </div>
                </form>
            <?php }
            elseif($_GET['id'] == 13 && $user->data()->access_level == 6){?>
                <form role="form" class="form-horizontal" method="post">
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
                        <label class="col-md-2 control-label">Frame Brand &nbsp;</label>
                        <div class="col-md-7">
                            <select name="brand" class="form-control select" data-live-search="true">
                                <option value="">Select Brand</option>
                                <?php foreach($override->getData('frame_brand') as $brand){?>
                                    <option value="<?=$brand['id']?>"><?=$brand['name']?></option>
                                <?php }?>
                            </select>
                        </div>
                        <label class="col-md-1"></label>
                        <div class="col-md-2">
                            <a href="#modal" class="btn btn-default" data-toggle="modal">Add Brand</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Frame Model &nbsp;</label>
                        <div class="col-md-10">
                            <input type="text" name="model" class="form-control" value=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Frame Size &nbsp;</label>
                        <div class="col-md-10">
                            <input type="text" name="size" class="form-control" value=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Price &nbsp;</label>
                        <div class="col-md-10">
                            <input type="number" name="price" class="form-control" value=""/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Quantity &nbsp;</label>
                        <div class="col-md-10">
                            <input type="number" min="0" name="quantity" class="form-control" value=""/>
                        </div>
                    </div>
                    <div class="pull-right">
                        <input type="submit"  name="frame" value="Add Frame" class="btn btn-success">
                    </div>
                </form>
                <!--add brand modal-->
                <div class="modal" id="modal" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title" id="defModalHead">Add New Brand</h4>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Brand Name</label>
                                        <div class="col-md-10">
                                            <input type="text" name="name" class="form-control" value="" required=""/>
                                        </div>
                                    </div>
                                    <label></label>
                                </div>
                                <div class="modal-footer">
                                    <input type="submit" name="addBrand" value="Add Brand" class="btn btn-success" >
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php }
            elseif($_GET['id'] == 14){?>
                <h3>SET PATIENT APPOINTMENT</h3>
                <h2>&nbsp;</h2>
                <form role="form" class="form-horizontal" method="post">
                    <?php if($user->data()->access_level == 2){$patients=$override->getData('wait_list');}else{$patients=$override->getData('patient');}?>
                    <div class="form-group">
                        <label class="col-md-1 control-label">Patient:&nbsp;&nbsp;</label>
                        <div class="col-md-11">
                            <select name="patient" class="form-control select" data-live-search="true">
                                <option value="">Select Patient</option>
                                <?php foreach($patients as $patient){if($user->data()->access_level == 2){$patientName=$override->get('patient','id',$patient['patient_id'])?>
                                    <option value="<?=$patientName[0]['id']?>"><?=$patientName[0]['firstname'].' '.$patientName[0]['lastname'].' '.$patientName[0]['phone_number']?></option>
                                <?php }else{?>
                                    <option value="<?=$patient['id']?>"><?=$patient['firstname'].' '.$patient['lastname'].' '.$patient['phone_number']?></option>
                                <?php }}?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 control-label">Doctor:&nbsp;&nbsp;</label>
                        <div class="col-md-11">
                            <select name="doctor" class="form-control select" data-live-search="true">
                                <option value="">Select Doctor</option>
                                <?php foreach($override->getNews('staff','branch_id',$user->data()->branch_id,'access_level',2) as $doctor){?>
                                    <option value="<?=$doctor['id']?>">DR. <?=$doctor['firstname'].' '.$doctor['lastname']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-1 control-label">Date : &nbsp;</label>
                        <div class="col-md-5">
                            <input type="text" name="date" class="form-control datepicker" value="<?=date('Y-m-d')?>">
                        </div>
                        <label class="col-md-1 control-label">Time : &nbsp;</label>
                        <div class="col-md-5">
                            <div class="input-group bootstrap-timepicker">
                                <input type="text" name="time" class="form-control timepicker24"/>
                                <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="pull-right">
                        <input type="submit" name="appnt" value="Set Appointment" class="btn btn-success">
                    </div>
                </form>
            <?php }
            elseif($_GET['id'] == 15){?>
                <h3>ADD DIAGNOSIS</h3>
                <h2>&nbsp;</h2>
                <form role="form" class="form-horizontal" method="post">
                    <?php if($user->data()->access_level == 2){$patients=$override->getData('wait_list');}else{$patients=$override->getData('patient');}?>
                    <div class="form-group">
                        <label class="col-md-1 control-label">Diagnosis:&nbsp;&nbsp;</label>
                        <div class="col-md-11">
                            <input name="name" class="form-control" placeholder="Enter Diagnosis">
                        </div>
                    </div>
                    <div class="pull-right">
                        <input type="submit" name="diagnosis" value="ADD" class="btn btn-success">
                    </div>
                </form>
            <?php }
            else{?>
                <div class="alert alert-danger" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <strong>OOPS ERROR!!&nbsp;</strong> YOU EITHER DON'T HAVE ACCESS TO THIS PAGE OR THIS PAGE DON'T EXIST....PLEASE CHECK YOUR OPTION AND TRY AGAIN
                </div>
            <?php }?>
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
<script type="text/javascript" src="js/plugins/summernote/summernote.js"></script>
<script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
<script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-file-input.js"></script>
<script type="text/javascript" src="js/plugins/bootstrap/bootstrap-select.js"></script>
<script type="text/javascript" src="js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
<script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
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
    $('#patient').change(function(){
        var patient_id = $(this).val();
        $.ajax({
            url:"process.php?content=cash",
            method:"GET",
            data:{patient_id:patient_id},
            dataType:"text",
            success:function(data){
                $('#cash_form').html(data);
            }
            });
        });
    $('#eye').change(function(){
        var getEye = $(this).val();
        $('#wait').show();
        $.ajax({
            url:"process.php?content=eyes",
            method:"GET",
            data:{getEye:getEye},
            dataType:"text",
            success:function(data){
                $('#other_eye').html(data);
                $('#wait').hide();
            }
        });
    });
    $('#eyes').change(function(){
        if($(this).val() == 'RE'){
            $('#other_power').show();
        }else{$('#other_power').hide();}
    });
    $('#lens_cost').change(function(){
        var getCost = $(this).val();
        $.ajax({
            url:"process.php?content=lens_cost",
                method:"GET",
                data:{cost:getCost},
                dataType:"text",
                success:function(data){
                    $('#cost').html(data);
                }
            });
    });
    $('#brand').change(function(){
        var brand_id = $(this).val();
        $.ajax({
            url:"process.php?content=model",
            method:"GET",
            data:{model:brand_id},
            dataType:"text",
            success:function(data){
                $('#model').html(data);
                }
            });
        });
        $('#model').change(function(){
            var model_id = $(this).val();
            $.ajax({
                url:"process.php?content=size",
                method:"GET",
                data:{size:model_id},
                dataType:"text",
                success:function(data){
                    $('#size').html(data);
                }
            });
        });
        $('#size').change(function(){
            var cst = $(this).val();
            $.ajax({
                url:"process.php?content=frame_price",
                method:"GET",
                data:{cst:cst},
                dataType:"text",
                success:function(data){
                    $('#frameCost').html(data);
                }
            });
        });
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
            $('#waitM').show();
            $.ajax({
                url:"process.php?content=eye",
                method:"GET",
                data:{eye:getEye},
                dataType:"text",
                success:function(data){
                    $('#lens_desc').html(data);
                    $('#waitM').hide();
                }
            });
        });
        $('#LE').change(function(){
            var getEye = $(this).val();
            $('#waitM').show();
            $.ajax({
                url:"process.php?content=eye",
                method:"GET",
                data:{eye:getEye},
                dataType:"text",
                success:function(data){
                    $('#lens_desc').html(data);
                    $('#waitM').hide();
                }
            });
        });
    $('#BE_').change(function(){
        var getEye = $(this).val();
        $.ajax({
            url:"process.php?content=eye_l",
            method:"GET",
            data:{eye:getEye},
            dataType:"text",
            success:function(data){
                $('#lens_desc').html(data);
            }
        });
    });
    $('#RE_').change(function(){
        var getEye = $(this).val();
        $.ajax({
            url:"process.php?content=eye_l",
            method:"GET",
            data:{eye:getEye},
            dataType:"text",
            success:function(data){
                $('#lens_desc').html(data);
            }
        });
    });
    $('#LE_').change(function(){
        var getEye = $(this).val();
        $.ajax({
            url:"process.php?content=eye_l",
            method:"GET",
            data:{eye:getEye},
            dataType:"text",
            success:function(data){
                $('#lens_desc').html(data);
            }
        });
    });
        $('#lens_cat').change(function(){
            var lensCat = $(this).val();
            $.ajax({
                url:"process.php?content=lensCat",
                method:"GET",
                data:{lensCat:lensCat},
                dataType:"text",
                success:function(data){
                    $('#lensType').html(data);
                }
            });
        });
        $('#lensType').change(function(){
            var lensType = $(this).val();
            $.ajax({
                url:"process.php?content=lensPrice",
                method:"GET",
                data:{lensType:lensType},
                dataType:"text",
                success:function(data){
                    $('#lensPrice').html(data);
                }
            });
        });
    $('#discount').change(function(){
        var disAmount = $(this).val();
        $('#ds').show();
        $.ajax({
            url:"process.php?content=discount",
            method:"GET",
            data:{dis:disAmount},
            dataType:"text",
            success:function(data){
                $('#disc').html(data);
                $('#ds').hide();
            }
        });
    });
    $('#csh').change(function(){
        var lensType = $(this).val();
        $('#ins_pay').hide();
        $('#pay').show();
    });
    $('#ins').change(function(){
        var lensType = $(this).val();
        $('#ins_pay').show();
        $('#pay').hide();
    });

    });
</script>
<!-- END SCRIPTS -->
<!--<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','../../../../www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-36783416-1', 'auto');
    ga('send', 'pageview');
</script>
<!-- Yandex.Metrika counter -->
<!--<script type="text/javascript">
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
