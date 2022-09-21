
<div class="modal fade newCharge-modal" id="newChargeModal" tabindex="-1" role="dialog" main-id=<?= isset($checks) && isset($checks->id) ? $checks->id : '-1' ?> type="charge" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; height:520px; width: 600px;">
            <form style= "top:40px; position: initial; bottom:0; right:0; box-shadow: initial; width: 550px;"
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

                                    <p>Tenant<span id="tenantsSpan"></span>
                                    </p>
                                    <p>Lease<span id="leasesSpan"></span><span id="formNames"></span>
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
                                    <style type="text/css" onload="getInfo($(this).closest('.modal'))"></style>
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
            function getInfo(modal){
            var thisProfile = transactions.profile_id ? transactions.profile_id : newChargeInfo.profile ? newChargeInfo.profile : tenants[0].id;
            getTenants(thisProfile, modal);
            }
        //});
        function getTenants(profileId, modal){
            console.log('modal 1' + modal);
            var newRow = '';
            newRow = `<label for="profile_id"></label>
                                        <span class="select">
                                        <select class="form-control editable-select" id="profile_id" name="transactions[profile_id]" onchange="getLeases($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal'))">
                                                <option value="0"></option>`;
                                                    for (var j = 0; j < tenants.length; j++) {
                                                            newRow += `<option value='` + tenants[j].id + `'`;
                                                               if(profileId == tenants[j].id){ 
                                                                    newRow += ' selected';
                                                                }
                                                               newRow +=  `>` + tenants[j].name + `</option>`;
                                                           
                                                    }
                                newRow += `</select>
                                            </span>`;
                            $(modal).find('#tenantsSpan').empty();            
                            $(modal).find('#tenantsSpan').append(newRow);
                            $(modal).find('#tenantsSpan').find('.editable-select').editableSelect();

                            getLeases(profileId, modal);
        }
        //gets leases based on profile
        function getLeases(profileId, modal){
            console.log('modal 2' + modal);
            var leaseSpot = 0;
            var lease;
            var newRow = '';
            newRow = ` <label for="lease_id"></label>
                                        <span class="select">
                                        <select class="form-control editable-select" id="lease_id" onchange="setNames($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal'));">`;
                                                    for (var j = 0; j < leases.length; j++) {
                                                        if(profileId == leases[j].profile_id){
                                                            newRow += `<option value='` + leases[j].id + `'`;
                                                            if(transactions > 0 || thisLease > 0){                                                              
                                                                if(transactions.lease_id == leases[j].id || thisLease == leases[j].id){ newRow += ' selected'; setNames(leases[j].id, modal);}
                                                             }else{
                                                                 if(leaseSpot == 0){
                                                                     newRow += ' selected'; leaseSpot++; setNames(leases[j].id, modal);
                                                                    }
                                                                }
                                                            newRow +=  `><span style="color: red;">` + leases[j].property + `</span><span style="color: blue;"> ` + leases[j].unit + `</span><span style="color: green;"> ` + leases[j].name + `</span></option>`;
                                                        }
                                                    }
                                                
                                newRow += `</select>
                                            </span>`;
                            $(modal).find('#leasesSpan').empty();            
                            $(modal).find('#leasesSpan').append(newRow);
                            $(modal).find('#leasesSpan').find('.editable-select').editableSelect();
                            transactions = 0;
                            thisLease = 0;
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
