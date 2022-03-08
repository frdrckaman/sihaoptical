<?php require_once('php/core/init.php');
$user = new User();
$override = new OverideData();
if($_GET['content'] == 'eye'){
 if($_GET['eye'] == 'both'){?>
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
 <?php }
 elseif($_GET['eye'] == 'RE'){?>
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
         </tbody>
     </table>
 <?php }
 elseif($_GET['eye'] == 'LE'){?>
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
             <td>Left</td>
             <td><input name="LE_sph" type="text" class="form-control"/></td>
             <td><input name="LE_cyl" type="text" class="form-control"/></td>
             <td><input name="LE_axis" type="text" class="form-control"/></td>
             <td><input name="LE_add" type="text" class="form-control"/></td>
             <td><input name="LE_qty" type="number" min="1" class="form-control" value="1"/></td>
         </tr>
         </tbody>
     </table>
 <?php }}
elseif($_GET['content'] == 'eyes'){
     if($_GET['getEye'] == 'RE' || $_GET['getEye'] == 'LE'){?>
             <div class="form-group">
                 <div class="col-md-6">
                     <select name="other_lens" id="other_lens_power" class="form-control select"  title="Lens" required="">
                         <option value="">Select Lens</option>
                         <?php foreach($override->getData('lens_category') as $lens_cat){?>
                             <option value="<?=$lens_cat['id']?>"><?=$lens_cat['name']?></option>
                         <?php }?>
                     </select>
                 </div>
                 <div class="col-md-3" >
                    <!-- <select name="other_power" id="op" class="form-control select"  title="Lens Power" >
                         <option value="">Lens Power</option>
                     </select>-->
                     <input type="text" name="other_power" class="form-control" placeholder="Enter Lens Power">
                 </div>
                 <div class="col-md-3">
                     <select name="other_eye" id="eye" class="form-control select"  title="Eye" required="">
                         <?php if($_GET['getEye'] == 'RE'){?>
                             <option value="LE">Left Eye</option>
                         <?php }elseif($_GET['getEye'] == 'LE'){?>
                             <option value="RE">Right Eye</option>
                         <?php }?>
                     </select>
                 </div>
             </div>
         <script>
             $(document).ready(function(){
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
 <?php }}
elseif($_GET['content'] == 'power'){
    if($_GET['cat_id']){?>
<select name="lens_power" class="form-control select">
    <option value="">Lens Power</option>
    <?php foreach($override->getLensPowerNoRepeat('lens_power','lens_power','cat_id',$_GET['cat_id'],'quantity') as $power){?>
        <option value="<?=$power['lens_power']?>"><?=$power['lens_power']?></option>
    <?php }?>
    </select>
<?php }}
elseif($_GET['content'] == 'other_power'){
    if($_GET['cat_id']){?>
    <option value="">Lens Power</option>
    <?php foreach($override->getLensPowerNoRepeat('lens_power','lens_power','cat_id',$_GET['cat_id'],'quantity') as $power){?>
        <option value="<?=$power['lens_power']?>"><?=$power['lens_power']?></option>
<?php }}}
elseif($_GET['content'] == 'multiple'){
    if($_GET['getMed'] == 'multiple'){?>
        <div class="form-group">
            <div class="col-md-offset-0 col-md-3">
                <select name="other_medicine" class="form-control"  title="Lens" >
                    <option value="">Select Medicine</option>
                    <?php foreach($override->getMedicine('medicine','quantity') as $medicine){?>
                        <option value="<?=$medicine['id']?>"><?=$medicine['name']?></option>
                    <?php }?>
                </select>
            </div>
            <div class="col-sm-2">
                <select name="other_quantity" class="form-control"  title="Lens Power" >
                    <option value="">Quantity</option>
                    <?php $x=1;while($x <= 10){?>
                        <option value="<?=$x?>"><?=$x?></option>
                        <?php $x++;}?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="other_dosage" class="form-control select" >
                    <option value="">FREQUENCY</option>
                    <option value="OD 1 times a day">OD 1 times a day</option>
                    <option value="BID 2 times a day">BID 2 times a day</option>
                    <option value="TID 3 times a day">TID 3 times a day</option>
                    <option value="QID 4times a day">QID 4times a day</option>
                    <option value="1 hourly">1 hourly</option>
                    <option value="2 after two hours">2 after two hours</option>
                    <option value="3 after three hours">3 after three hours</option>
                    <option value="4 after four hours">4 after four hours</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="other_eyes" class="form-control select" >
                    <option value="BOTH EYES">BOTH EYES</option>
                    <option value="RIGHT EYES">RIGHT EYES</option>
                    <option value="LEFT EYES">LEFT EYES</option>
                    <option value="ORAL">ORAL</option>
                    <option value="TROPICAL APPLICATION">TROPICAL APPLICATION</option>
                    <option value="APPLIED">APPLIED</option>
                </select>
            </div>
            <div class="col-md-1">
                <input type="number" name="other_day" class="form-control" placeholder="No.">
            </div>
            <div class="col-md-2">
                <select name="other_days" class="form-control select" >
                    <option value="DAY">DAYS</option>
                    <option value="WEEK">WEEK</option>
                    <option value="MONTH">MONTH</option>
                    <option value="YEAR">YEAR</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-offset-0 col-md-3">
                <select name="other_medicine_1" class="form-control"  title="Lens" >
                    <option value="">Select Medicine</option>
                    <?php foreach($override->getMedicine('medicine','quantity') as $medicine){?>
                        <option value="<?=$medicine['id']?>"><?=$medicine['name']?></option>
                    <?php }?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="other_quantity_1" class="form-control"  title="Lens Power" >
                    <option value="">Quantity</option>
                    <?php $x=1;while($x <= 10){?>
                        <option value="<?=$x?>"><?=$x?></option>
                        <?php $x++;}?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="other_dosage_1" class="form-control select" >
                    <option value="">FREQUENCY</option>
                    <option value="OD 1 times a day">OD 1 times a day</option>
                    <option value="BID 2 times a day">BID 2 times a day</option>
                    <option value="TID 3 times a day">TID 3 times a day</option>
                    <option value="QID 4times a day">QID 4times a day</option>
                    <option value="1 hourly">1 hourly</option>
                    <option value="2 after two hours">2 after two hours</option>
                    <option value="3 after three hours">3 after three hours</option>
                    <option value="4 after four hours">4 after four hours</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="other_eyes_1" class="form-control select" >
                    <option value="BOTH EYES">BOTH EYES</option>
                    <option value="RIGHT EYES">RIGHT EYES</option>
                    <option value="LEFT EYES">LEFT EYES</option>
                    <option value="ORAL">ORAL</option>
                    <option value="TROPICAL APPLICATION">TROPICAL APPLICATION</option>
                    <option value="APPLIED">APPLIED</option>
                </select>
            </div>
            <div class="col-md-1">
                <input type="number" name="other_day_1" class="form-control" placeholder="No.">
            </div>
            <div class="col-md-2">
                <select name="other_days_1" class="form-control select" >
                    <option value="DAY">DAYS</option>
                    <option value="WEEK">WEEK</option>
                    <option value="MONTH">MONTH</option>
                    <option value="YEAR">YEAR</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-offset-0 col-md-3">
                <select name="other_medicine_2" class="form-control select"  title="Lens" >
                    <option value="">Select Medicine</option>
                    <?php foreach($override->getMedicine('medicine','quantity') as $medicine){?>
                        <option value="<?=$medicine['id']?>"><?=$medicine['name']?></option>
                    <?php }?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="other_quantity_2" class="form-control"  title="Lens Power" >
                    <option value="">Quantity</option>
                    <?php $x=1;while($x <= 10){?>
                        <option value="<?=$x?>"><?=$x?></option>
                        <?php $x++;}?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="other_dosage_2" class="form-control select" >
                    <option value="">FREQUENCY</option>
                    <option value="OD 1 times a day">OD 1 times a day</option>
                    <option value="BID 2 times a day">BID 2 times a day</option>
                    <option value="TID 3 times a day">TID 3 times a day</option>
                    <option value="QID 4times a day">QID 4times a day</option>
                    <option value="1 hourly">1 hourly</option>
                    <option value="2 after two hours">2 after two hours</option>
                    <option value="3 after three hours">3 after three hours</option>
                    <option value="4 after four hours">4 after four hours</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="other_eyes_2" class="form-control select" >
                    <option value="BOTH EYES">BOTH EYES</option>
                    <option value="RIGHT EYES">RIGHT EYES</option>
                    <option value="LEFT EYES">LEFT EYES</option>
                    <option value="ORAL">ORAL</option>
                    <option value="TROPICAL APPLICATION">TROPICAL APPLICATION</option>
                    <option value="APPLIED">APPLIED</option>
                </select>
            </div>
            <div class="col-md-1">
                <input type="number" name="other_day_2" class="form-control" placeholder="No.">
            </div>
            <div class="col-md-2">
                <select name="other_days_2" class="form-control select" >
                    <option value="DAY">DAYS</option>
                    <option value="WEEK">WEEK</option>
                    <option value="MONTH">MONTH</option>
                    <option value="YEAR">YEAR</option>
                </select>
            </div>
        </div>
<?php }}
elseif($_GET['content'] == 'lens_cost'){
    if($_GET['cost']){$lens_cost = $override->get('lens_power','id',$_GET['cost'])?>
    <input name="lens_cost" class="form-control required" type="hidden" value="<?=$lens_cost[0]['price']?>" aria-required="true" >
    <input  class="form-control required" type="text" value="<?=$lens_cost[0]['price']?>" aria-required="true" disabled>
<?php }}
elseif($_GET['content'] == 'model'){?>
<option value="">Select Frame Model</option>
<?php foreach($override->get('frame_model','brand_id',$_GET['model']) as $model){?>
    <option value="<?=$model['id']?>"><?=$model['model']?></option>
<?php }}
elseif($_GET['content'] == 'size'){$frameSize=$override->getNews('frames','model',$_GET['size'],'branch_id',$user->data()->branch_id);?>
<option value=""><?php if($frameSize){?>Select Frame Size<?php }else{echo'Not Available';}?></option>
<?php foreach($frameSize as $size){?>
    <option value="<?=$size['id']?>"><?=$size['frame_size']?></option>
<?php }}
elseif($_GET['content'] == 'frame_price'){
    if($_GET['cst']){$frameCost = $override->get('frames','id',$_GET['cst'])?>
    <input name="frame_cost" class="form-control required" type="hidden" value="<?=$frameCost[0]['price']?>" aria-required="true" >
    <input class="form-control required" type="text" value="<?=$frameCost[0]['price']?>" aria-required="true" disabled>
<?php }}
elseif($_GET['content'] == 'lensCat'){
    if($_GET['lensCat']){?>
    <option value="">Select Lens Type</option>
    <?php foreach($override->getNoRepeat('lens_power','type_id','cat_id',$_GET['lensCat']) as $lensCat){$catName=$override->get('lens_type','id',$lensCat['type_id'])?>
        <option value="<?=$catName[0]['id']?>"><?=$catName[0]['name']?></option>
<?php }}}
elseif($_GET['content'] == 'cat_g'){?>
    <option value="">Select To</option>
    <?php if($_GET['cat'] == 1){echo'Jesus';foreach($override->getData('staff') as $staff){?>
        <option value="<?=$staff['id']?>"><?=$staff['firstname'].' '.$staff['middlename'].' '.$staff['lastname']?></option>
<?php }}}
elseif($_GET['content'] == 'eye_l'){?>
    <?php if($_GET['eye'] == 'both'){?>
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
<?php }
elseif($_GET['eye'] == 'RE'){?>
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
        </tbody>
    </table>
<?php }
elseif($_GET['eye'] == 'LE'){?>
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
            <td>Left</td>
            <td><input name="add_rx_os_sphere" type="text" class="form-control"/></td>
            <td><input name="add_rx_cyl" type="text" class="form-control"/></td>
            <td><input name="add_rx_axis" type="text" class="form-control"/></td>
            <td><input name="add_rx_va" type="text" class="form-control"/></td>
            <td><input name="add_rx_add" type="text" class="form-control"/></td>
        </tr>
        </tbody>
    </table>
<?php }}
elseif($_GET['content'] == 'p_details'){
    if($_GET['p_id']){$pd=$override->get('patient','id',$_GET['p_id'])?>
    <div class="form-group">
        <label class="col-md-1 control-label">Address : &nbsp;</label>
        <div class="col-md-2">
            <input type="text" class="form-control" value="<?=$pd[0]['address']?>" >
        </div>
        <label class="col-md-2 control-label">Occupation : &nbsp;</label>
        <div class="col-md-2">
            <input type="text" class="form-control" value="<?=$pd[0]['occupation']?>" >
        </div>
        <label class="col-md-1 control-label">Age : &nbsp;</label>
        <div class="col-md-2">
            <input type="text" class="form-control" value="<?=$pd[0]['age']?>" >
        </div>
        <div class="col-md-2">
            &nbsp;<a href="info.php?id=22&p=<?=$_GET['p_id']?>" class="btn btn-info">Previous Records</a>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-1 control-label">Insurance : &nbsp;</label>
        <div class="col-md-3">
            <input type="text" class="form-control" value="<?=$pd[0]['health_insurance']?>" >
        </div>

        <label class="col-md-1 control-label">&nbsp;Member No : &nbsp;</label>
        <div class="col-md-3">
            <input type="text" class="form-control" value="<?=$pd[0]['dependent_no']?>" >
        </div>
        <label class="col-md-1 control-label"></label>
        <h3 style="color: #009900;font-weight: bolder">PID : <?=$_GET['p_id']?></h3>
    </div>
    <br>
<?php }}
elseif($_GET['content'] == 'discount'){ if($_GET['dis'] == true){?>
    <div class="form-group">
        <label class="col-md-2 control-label">Discount Amount : &nbsp;</label>
        <div class="col-md-4">
            <input type="number" name="discount" min="0" class="form-control">
        </div>
    </div>
<?php }}?>
