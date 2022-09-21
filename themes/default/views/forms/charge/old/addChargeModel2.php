
<div class="modal fade newCharge-modal" id="newChargeModal" tabindex="-1" role="dialog" main-id=<?= isset($checks) && isset($checks->id) ? $checks->id : '-1' ?> type="charge" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document" style="width: 500px;">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; height:520px;">
            <form style= "top:40px; position: initial; bottom:0; right:0; box-shadow: initial; width: 460px;"
                    id="addChargeForm" action="<?php echo $target; ?>" method="post" class="form-fixed chargeForm" type="newCharge"
                    formType=""><!-- formType used for js reload-->
       
                  <header class="modal-h">
					<h2 class="text-uppercase"><?php echo $title; ?></h2>
                    <nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
					<nav>
						<ul>
							<li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
						</ul>
					</nav>		
				</header>

       

              <div class ="has-table-c">
				<table>
					<thead class="dataTables_scrollHead">
						<tr>
						</tr>
					</thead>
					<tbody>
                    <?php $userId = $this->ion_auth->get_user_id(); ?>

                                    <p>Lease<span id="leasesSpan"></span><span id="formNames"></span>
                                    </p>
                                    <p>Tenant<span id="tenantsSpan"></span>
                                    </p>
                                    <p>Charge type
                                        <label for="item_id"></label>
                                        <span class="select">
                                        <select class="form-control editable-select" id="item_id" name="transactions[item_id]" onchange="changeDescription($(this).closest('.select').find('input[type=hidden]').val() , $(this).closest('.modal'))">
                                                <option value="0"></option>
                                                    <?php
                                                    foreach ($items as $item) {
                                                        echo '<option value="' . $item->id . '"'. (isset($transaction) && $transaction->item_id == $item->id ? 'selected' : '') .'>' . $item->name . '</option>';
                                                    } ?>
                                                </select>
                                            </span>
                                    </p>
                                    <!-- < ?php  if($header && $header->transaction_date){ $originalDate = $header->transaction_date;}  ;
                                    $newDate = date("m/d/Y", strtotime($originalDate));?> -->
                                    <p>Date<span  id="dateSpan">
                                        <label for="charge_date"></label>
                                        <input data-toggle="datepicker"  id="charge_date" name="header[transaction_date]" value="<?php if($header->date) echo $header->date;?>">
                                                </span>
                                    </p>
                                    <p>Amount
                                        <label for="amount"></label>
                                        <input type="text" id="amount" name="transactions[credit]" value="<?php if($transaction && $transaction->credit) echo $transaction->credit;?>">
                                    </p>
                                    <p>Description
                                        <label for="description"></label>
                                        <textarea id="description" name="transactions[description]"><?php if($transaction && $transaction->description) echo  $transaction->description; ?></textarea>
                                    </p>
                                    <p><button type="submit" after="mclose" id="chargeSubmitButton">Save</button></p>
                                    <p><button id="newChargeCancelButton" type="button">cancel</button></p>
                                    <style type="text/css" onload="getChargeInfo($(this).closest('.modal'))"></style>
                    </tbody>
						  
					<tfoot>
						<tr>
						</tr>
					</tfoot>
					
				</table>
			</div>

        <footer>
        <?= $header ? 
        "<div>Last Modified $header->modified<div>
         <div>Last Modified by <a href='#!'>$header->user</a><div>" : '';
        ?>
         
        </footer>
                            </form>   
                    </div>
		</div>

</div>
</div>

    <script>
        console.log('charges model');
             var transactions = <?php echo $jtransaction ? $jtransaction : '0' ?>;
             var tenants= <?php echo $jtenants ? $jtenants : '0'?>;
            var leases= <?php echo $jleases ? $jleases : '0'?>;
            var items= <?php echo $jItems ? $jItems : '0'?>;
            var newChargeInfo = <?php echo $newChargeInfo ? json_encode($newChargeInfo) : '0'?>;  
            var thisLease = newChargeInfo.lease ? newChargeInfo.lease : 0;                                         

        //$(document).ready(function () {
            function getChargeInfo(modal){
            //var thisProfile = transactions.profile_id ? transactions.profile_id : newChargeInfo.profile ? newChargeInfo.profile : tenants[0].id;
            var thisLease = transactions.lease_id ? transactions.lease_id : newChargeInfo.lease;
            //getTenants(thisProfile, modal);
            getLeases1(thisLease, modal);
            function getInfo(modal){
            var thisProfile = transactions.profile_id ? transactions.profile_id : newChargeInfo.profile;
            var thisLease = transactions.lease_id ? transactions.lease_id : newChargeInfo.lease;
            //getTenants(thisProfile, modal);
                if(thisProfile > 0 && thisLease == 0){
                    getTenants3(thisProfile, modal);
                }else{
                    getLeases(thisLease, modal);
                }

            }
        //});
        function getTenants1(lease_id, modal){
            console.log('modal 11' + modal);
            var newRow = '';
            newRow = `<label for="profile_id"></label>
                                        <span class="select">
                                        <select class="form-control editable-select" id="profile_id" name="transactions[profile_id]" onblur="getLeases2($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal'));">`;
                                                    for (var j = 0; j < tenants.length; j++) {
                                                        if(lease_id){
                                                            var selectedChargesArray = tenants[j].profile_leases.split(",");
                                                            if(selectedChargesArray.includes(lease_id)){ 
                                                            //if(lease_id == tenants[j].lease_id){
                                                                newRow += `<option value='` + tenants[j].id + `'`;
                                                                    if(transactions.profile_id == tenants[j].id){ newRow += ' selected';}
                                                                newRow +=  `>` + tenants[j].name + `</option>`;
                                                             }
                                                        }else{
                                                            newRow += `<option value='` + tenants[j].id + `'
                                                            >` + tenants[j].name + `</option>`;
                                                        }
                                                        // if(lease_id == tenants[j].lease_id){
                                                        //     newRow += `<option value='` + tenants[j].id + `'`;
                                                        //     if(transactions > 0 || thisLease > 0){                                                              
                                                        //         if(transactions.lease_id == tenants[j].id || thisLease == tenants[j].id){ newRow += ' selected'; setNames(leases[j].id, modal);}
                                                        //      }else{
                                                        //          if(leaseSpot == 0){
                                                        //              newRow += ' selected'; leaseSpot++; setNames(tenants[j].id, modal);
                                                        //             }
                                                        //         }
                                                        //     newRow +=  `><span style="color: red;">` + tenants[j].property + `</span><span style="color: blue;"> ` + leases[j].unit + `</span><span style="color: green;"> ` + leases[j].name + `</span></option>`;
                                                        // }
                                                    }


                                                    // for (var j = 0; j < tenants.length; j++) {
                                                    //         newRow += `<option value='` + tenants[j].id + `'`;
                                                    //            if(profileId == tenants[j].id){ 
                                                    //                 newRow += ' selected';
                                                    //             }
                                                    //            newRow +=  `>` + tenants[j].name + `</option>`;
                                                           
                                                    // }
                                newRow += `</select>
                                            </span>`;
                            $(modal).find('#tenantsSpan').empty();            
                            $(modal).find('#tenantsSpan').append(newRow);
                            $(modal).find('#tenantsSpan').find('.editable-select').editableSelect();
                            transactions = 0;
                            thisLease = 0;
                            //getLeases(profileId, modal);
        }
        //gets leases based on profile
        function getLeases1(thisLease, modal){
            console.log('modal 22' + modal);
            var leaseSpot = 0;
            var lease;
            var newRow = '';
            newRow = ` <label for="lease_id"></label>
                                        <span class="select">
                                        <select class="form-control editable-select" id="lease_id"  onblur="getTenants2($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal')); setNames($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal'));">`;
                                                    for (var j = 0; j < leases.length; j++) {
                                                            newRow += `<option value='` + leases[j].id + `'`;
                                                               if(thisLease == leases[j].id){ 
                                                                    newRow += ' selected';
                                                                    setNames(thisLease, modal);
                                                                }
                                                               newRow +=  `><span style="color: red;">` + leases[j].property + `</span><span style="color: blue;"> ` + leases[j].unit + `</span><span style="color: green;"> ` + leases[j].name + `</span></option>`;                                                         
                                                    }
                                                
                                newRow += `</select>
                                            </span>`;
                            $(modal).find('#leasesSpan').empty();            
                            $(modal).find('#leasesSpan').append(newRow);
                            $(modal).find('#leasesSpan').find('.editable-select').editableSelect();

                            getTenants1(thisLease, modal);
        }

                //gets leases based on profile
                function getLeases2(profile, modal){
                    var selectedLease = $(modal).find('#lease_id').closest('.select').find('input[type=hidden]').val();
            console.log('modal 222' + modal);
            var leaseSpot = 0;
            var lease;
            var newRow = '';
            newRow = ` <label for="lease_id"></label>
                                        <span class="select">
                                        <select class="form-control editable-select" id="lease_id"  onblur="setNames($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal'));">`;
                                                    for (var j = 0; j < leases.length; j++) {
                                                            if(profile){
                                                                var selectedChargesArray = leases[j].profile_leases.split(",");
                                                                if(selectedChargesArray.includes(profile)){ 
                                                                    newRow += `<option value='` + leases[j].id + `'`;
                                                                    if(selectedLease == leases[j].id){ newRow += ' selected';}
                                                                //    if(profile == leases[j].id){ 
                                                                //         newRow += ' selected';
                                                                //         setNames(thisLease, modal);
                                                                //     }
                                                                newRow +=  `><span style="color: red;">` + leases[j].property + `</span><span style="color: blue;"> ` + leases[j].unit + `</span><span style="color: green;"> ` + leases[j].name + `</span></option>`;                                                         
                                                                }
                                                            }else{
                                                                newRow += `<option value='` + leases[j].id + `'
                                                                ><span style="color: red;">` + leases[j].property + `</span><span style="color: blue;"> ` + leases[j].unit + `</span><span style="color: green;"> ` + leases[j].name + `</span></option>`;                                                         
                                                             }

                                                        }
                                                
                                newRow += `</select>
                                            </span>`;
                            $(modal).find('#leasesSpan').empty();            
                            $(modal).find('#leasesSpan').append(newRow);
                            $(modal).find('#leasesSpan').find('.editable-select').editableSelect();

                            //getTenants(thisLease, modal);
        }

        function getTenants2(lease_id, modal){
            console.log('modal 11' + modal);
            var selectedProfile = $(modal).find('#profile_id').closest('.select').find('input[type=hidden]').val();
            var newRow = '';
            newRow = `<label for="profile_id"></label>
                                        <span class="select">
                                        <select class="form-control editable-select" id="profile_id" name="transactions[profile_id]" >`;
                                                    for (var j = 0; j < tenants.length; j++) {
                                                        if(lease_id){
                                                            var selectedChargesArray = tenants[j].profile_leases.split(",");
                                                            if(selectedChargesArray.includes(lease_id)){ 
                                                            //if(lease_id == tenants[j].lease_id){
                                                                newRow += `<option value='` + tenants[j].id + `'`;
                                                                    if(selectedProfile == tenants[j].id){ newRow += ' selected';}
                                                                newRow +=  `>` + tenants[j].name + `</option>`;
                                                             }
                                                        }else{
                                                            newRow += `<option value='` + tenants[j].id + `'
                                                            >` + tenants[j].name + `</option>`;
                                                        }
                                                    }
                                newRow += `</select>
                                            </span>`;
                            $(modal).find('#tenantsSpan').empty();            
                            $(modal).find('#tenantsSpan').append(newRow);
                            $(modal).find('#tenantsSpan').find('.editable-select').editableSelect();
                            transactions = 0;
                            thisLease = 0;
        }

        function getTenants3(profile, modal){
            console.log('modal 11' + modal);
            var selectedProfile = $(modal).find('#profile_id').closest('.select').find('input[type=hidden]').val();
            var newRow = '';
            newRow = `<label for="profile_id"></label>
                                        <span class="select">
                                        <select class="form-control editable-select" id="profile_id" name="transactions[profile_id]" >`;
                                            for (var j = 0; j < tenants.length; j++) {
                                                newRow += `<option value='` + tenants[j].id + `'`;
                                                    if(profile == tenants[j].id){ newRow += ' selected';}
                                                newRow +=  `>` + tenants[j].name + `</option>`;
                                            }
                                newRow += `</select>
                                            </span>`;
                            $(modal).find('#tenantsSpan').empty();            
                            $(modal).find('#tenantsSpan').append(newRow);
                            $(modal).find('#tenantsSpan').find('.editable-select').editableSelect();
                            getLeases2(profile, modal);
        }
        
        function setNames(leaseId, modal){
            console.log('modal set names' + modal);
            for (var j = 0; j < leases.length; j++) {
                if(leaseId == leases[j].id){ 
                     var  names = `<input type="hidden" name="transactions[lease_id]" value="`+ leases[j].id +`">
                                <input type="hidden" name="transactions[property_id]" value="`+ leases[j].property_id +`">
                                <input type="hidden" name="transactions[unit_id]" value="`+ leases[j].unit_id +`">`;
                }
            }
            $(modal).find('#formNames').empty();
            $(modal).find('#formNames').append(names);
        }

        function changeDescription(id, modal){
            var description = "";
            for (var j = 0; j < items.length; j++) {
                if(id == items[j].id){
                    description += items[j].sales_description;
                }
            }
            $(modal).find('#description').val(description);
        }
    </script>
