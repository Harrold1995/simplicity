<div class="modal fade cc-modal" id="ccModal" tabindex="-1" role="dialog"  type="cc" main-id="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" ref-id="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <form action="<?php echo $target; ?>" method="post" data-title="credit-card-charge"  type="cc_charge">
				  <?php if(isset($header) && isset($header->th_id)){
                                    echo '<input type="hidden" name="header[id]" value="' . $header->th_id . '"/>';
                        } ?>

				  <?php if(isset($header) && isset($header->ccTransaction_id)){
                                    echo '<input type="hidden" name="credit_card[id]" value="' . $header->ccTransaction_id . '"/>';
                        } ?>
				<header class="modal-h">
					<h2>Credit Card Charge</h2>
					<ul class="check-a">
					<?php $normal_radio_button = 'checked'; if(isset($header) && isset($header->amount) && $header->amount < 0){$normal_radio_button = ''; $credit_radio_button = 'checked';}
					?>
						<li><label for="radioButton">Charge</label><input type="radio" id="charge" value="normal" name="radioButton" <?php echo $normal_radio_button; ?>></li>
						<li><label for="credit">Credit</label><input type="radio" id="credit" value="credit" name="radioButton"<?php echo $credit_radio_button; ?>></li>
					</ul>
					<nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
					<nav>
						<ul>
							<li><a href="./"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
							<li><a href="./"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
							<li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li class="get_send_email_form"><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
							<li><a href="./"><i class="icon-brain"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
							<li><a href="./"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>
				<section class="">
					<div class="double h">
						<div>
							<p class="select">
								<label for="accountName">Credit Card:</label>
								<select class="editable-select quick-add set-up" id="accountName" name="credit_card[account_id]">
								 <option value="-1" selected >Select Credit Card</option>
									<?php
										foreach ($creditCard as $singleCC) {
											// echo '<option value="-1" selected >' . "Select Apples" . '</option>';
											echo '<option value="' . $singleCC->id . '" ' . (isset($header) && $header->account_id == $singleCC->id ? 'selected' : '') . '>' . $singleCC->name . '</option>';
									} ?>   
                                </select>
								<!-- <input type="text" id="accountName" name="credit_card[account_id]" value="< ?= isset($accountName) && isset($accountName->name) ? $accountName->name : '' ?>"> -->
							</p>
							<p class="select"><!-- id="property_id"  used for javascript-->
                            <label for="property_id">Property</label>
								<select class="editable-select quick-add set-up" id="property_id" name="credit_card[property_id]">
								 <option value="-1" selected >Select Property</option>
									<?php
										foreach ($properties as $property) {
											// echo '<option value="-1" selected >' . "Select Apples" . '</option>';
											echo '<option value="' . $property->id . '" ' . (isset($header) && $header->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
									} ?>   
                                </select>
							</p>
							<p id="vendorTest" class="select">  
                            <label for="profile_id">Vendor</label>
								<select class="editable-select quick-add set-up" id="profile_id" name="credit_card[profile_id]">
								<option value="-1" selected >Select Vendor</option>
                                <?php foreach($vendors as $vendor): 
									echo '<option value="' . $vendor->id . '" ' . (isset($header) && $header->profile_id == $vendor->id ? 'selected' : '') . '>' . $vendor->vendor . '</option>';
								  endforeach; ?>
                                </select>								
							</p>
						</div>
						<div>
							<p>
								<label for="transaction_ref">Reference:</label>
								<input type="text" value="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" id="transaction_ref" name="header[transaction_ref]">
								<!--<label for="blx">Reference:</label>
								<input type="number" id="blx" name="blx" value="4101">-->
							</p>
							<p>
								<label for="date">Date:</label>
								<input data-toggle="datepicker" id="ccDate" name="header[transaction_date]" value="<?= isset($header) && isset($header->date) ? $header->date : '' ?>">
							</p>
							<p>
								<label for="amount">Amount: <span class="prefix">$</span></label>
								<input type="text" id="amount" class="topAmount toggleRadio" name="credit_card[credit]" value="<?= isset($header) && isset($header->amount) ? str_replace("-","",$header->amount) : '' ?>">
							</p>
							<p class="select">
								<label for="class">Class</label>
								<select class="editable-select quick-add set-up" id="class" name="credit_card[class_id]">
								<option value="-1" selected >Select Class</option>
                                <?php foreach($classes as $class): 
								echo '<option value="' . $class->id . '" ' . (isset($header) && $header->class_id == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
		
                                 endforeach; ?>
                                </select>
							</p>
						</div>
					</div>
					<p class="m0">
						<label for="memo">Memo:</label>
						<input type="text" id="memo" name="header[memo]" value="<?= isset($header) && isset($header->memo) ? $header->memo : '' ?>">
					</p>
				</section>
				<!-- <table class="table-c c text-center">
					<thead>
						<tr>
							<th>Account</th>
							<th>Property</th>
							<th>Description</th>
							<th>Amount</th>
							<th>Name</th>
							<th>Class</th>
						</tr>
					</thead> -->
        <div class ="has-table-c">
            <table class="table-c billTable mobile-hide dataTable no-footer" style="display: table; width: 100%;">
                <thead class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden;">
                    <tr>
                        <th>Account</th>
                        <th>Property</th>
                        <th>Unit</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Class</th>
                        <th>Name</th>
                    </tr>
                </thead>
                    <tbody  class="dataTables_scrollBody" style=" display: block;height: calc(100vh - 580px);overflow: auto;">
					<?php $step = 1; ?>
                    <?php  if (isset($transactions)){
                       foreach ($transactions as $transaction) {?>
	                    	<?php if(isset($transaction->id)){
                                    echo '<input type="hidden" name="transactions[' . $step . '][id]" value="' . $transaction->id . '"/>';
                                    } ?>
                        <tr class="fillNextRow createNewRow" id="<?php echo $step?>">
                            <td width="10%" class="formGridAccountTd">
							<span class="select">
                                <select class=" editable-select quick-add set-up" id="account_id" name="transactions[<?=$step?>][account_id]">
                                            <option value="-1" selected ></option>
                                            <!-- < ?php foreach($accounts as $account): ?>
                                                <option value="< ?php echo  $account->id ?>"  >< ?php echo  $account->name ?></option>		
                                            < ?php endforeach; ?> -->
								 <?php
									echo '<option class="nested0" value="0"></option>'; //' . (isset($unit) && $unit->parent_id == 0 ? 'selected' : '') . '
									if (isset($subaccounts))
										foreach ($subaccounts as $saccount) {
											//if ($unit->id == $sunit->id || $unit->name == $sunit->name) continue;
											echo '<option data-id="'.$saccount->id.'" data-parent-id="'.$saccount->parent_id.'" class="nested'.$saccount->step.'"value="' . $saccount->id . '"  ' . (isset($transaction) && $transaction->account_id == $saccount->id ? 'selected' : '') . '>' . $saccount->name . '</option>';
									} ?>
								</select>
								</span>
							</td>
                            <td width="10%">
							<span class="select">
							<select class="editable-select quick-add set-up formGridPropertySelected" id="property_id" name="transactions[<?=$step?>][property_id]"> -->
								 <option value="-1" selected ></option>
									<?php
										foreach ($properties as $property) {
											// echo '<option value="-1" selected >' . "Select Apples" . '</option>';
											echo '<option value="' . $property->id . '" ' . (isset($transaction) && $transaction->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
									} ?>   
                                </select>
								</span>
							</td>
                            <td width="10%">
							<span class="select">
								<select class="editable-select quick-add set-up formGridUnitSelect" id="unit_id" name="transactions[<?=$step?>][unit_id]">
								<option value="-1" selected ></option>
                                <!-- < ?php foreach($units as $unit): ?>
									<option value="< ?php echo  $unit->id ?>"  >< ?php echo  $unit->name ?></option>		
								 < ?php endforeach; ?> -->
								 <?php
									echo '<option class="nested0" value="0"></option>'; //' . (isset($unit) && $unit->parent_id == 0 ? 'selected' : '') . '
									if (isset($subunits))
										foreach ($subunits as $unit) {
											//if ($unit->id == $sunit->id || $unit->name == $sunit->name) continue;
											echo '<option data-id="'.$unit->id.'" data-parent-id="'.$unit->parent_id.'" class="nested'.$unit->step.'"value="' . $unit->id . '"' . (isset($transaction) && $transaction->unit_id == $unit->id ? 'selected' : '') .'>' . $unit->name . '</option>';
									} ?>
								</select>
								</span>
							</td>
                          <td width="10%" style="text-align: center;">
						  <input type="text" id="description" value="<?= isset($transaction->description) ? $transaction->description : '' ?>" name="transactions[<?=$step?>][description]" >
						  </td>
                          <td width="10%">
								<input type="text" id="debit" class="calculateTotal" value="<?= isset($header) && isset($header->amount) && $header->amount < 0 ? $transaction->credit -  $transaction->debit: $transaction->debit - $transaction->credit ?>" name="transactions[<?=$step?>][debit]" >
						 </td>
                          <td width="10%" style="text-align: center;">
						  <span class="select">
							<select class="editable-select quick-add set-up" id="class_id" name="transactions[<?=$step?>][class_id]">
									<?php foreach($classes as $class): 
									echo '<option value="' . $class->id . '"' . (isset($transaction) && $transaction->class_id == $class->id ? 'selected' : '') . ' >' . $class->description . '</option>';
			
									endforeach; ?>
									</select>
						  
						  <!-- < ?=isset($transaction->class_id) ? $transaction->class_id : '';?>
						  	<input type="hidden"  name="transactions[< ?=$step?>][class_id]" value="< ?=$transaction->class_id?>"> -->
						  </span>
						  </td>
						  <td width="10%" id="names">
						  <span class="select">
								<select class="editable-select quick-add set-up" id="profile_id" name="transactions[<?=$step?>][profile_id]">
                                <?php foreach($names as $name): 
									echo '<option value="' . $name->id . '" ' . (isset($transaction) && $transaction->profile_id == $name->id ? 'selected' : '') . '>' . $name->name . '</option>';
								  endforeach; ?>
                                </select>
								</span>
							</td>
                          <!-- <td width="10%" style="text-align: center;">< ?=isset($transaction->name) ? $transaction->name : '';?>
						  	<input type="hidden"  name="transactions[< ?=$step?>][profile_id]" value="< ?=$transaction->name?>">
						  </td> -->
                        </tr> 
                      <?php $step++; }}
					//   else {echo "<tr><td class='text-center' style='color: #f37ce4;font-size: large;'><strong>No transactions for this item</strong></td></tr>";
                    // } 
					?>
						 <?php for ($x = $step; $x <= 12; $x++) { ?>
						<tr  class="fillNextRow createNewRow" id="<?php echo $step?>">
							<td width="10%"  class="formGridAccountTd">
							<span class="select">
							<select class=" editable-select quick-add set-up" id="account_id" name="transactions[<?=$step?>][account_id]"
							onChange="getNames($(this.closest('tr')), $(this).closest('tr').find('input[type=hidden]').val(), <?=$step?>)">
								<option value="-1" selected ></option>
                                <!-- < ?php foreach($accounts as $account): ?>
									<option value="< ?php echo  $account->id ?>"  >< ?php echo  $account->name ?></option>		
								 < ?php endforeach; ?> -->
								 <?php
									echo '<option class="nested0" value="0"></option>'; //' . (isset($unit) && $unit->parent_id == 0 ? 'selected' : '') . '
									if (isset($subaccounts))
										foreach ($subaccounts as $saccount) {
											//if ($unit->id == $sunit->id || $unit->name == $sunit->name) continue;
											echo '<option data-id="'.$saccount->id.'" data-parent-id="'.$saccount->parent_id.'" class="nested'.$saccount->step.'"value="' . $saccount->id . '">' . $saccount->name . '</option>';
									} ?>
								</select>
								<span>
							</td>
							<td width="10%">
							<span class="select">
							<select class="editable-select quick-add set-up formGridPropertySelected" id="property_id" name="transactions[<?=$step?>][property_id]"> -->
								 <option value="-1" selected ></option>
									<?php
										foreach ($properties as $property) {
											// echo '<option value="-1" selected >' . "Select Apples" . '</option>';
											echo '<option value="' . $property->id . '" >' . $property->name . '</option>';
									} ?>   
                                </select>
								</span>
							</td>
                            <td width="10%">
							<span class="select">
								<select class="editable-select quick-add set-up formGridUnitSelect" id="unit_id" name="transactions[<?=$step?>][unit_id]">
								<option value="-1" selected ></option>
                                <!-- < ?php foreach($units as $unit): ?>
									<option value="< ?php echo  $unit->id ?>"  >< ?php echo  $unit->name ?></option>		
								 < ?php endforeach; ?> -->
								 <?php
									echo '<option class="nested0" value="0"></option>'; //' . (isset($unit) && $unit->parent_id == 0 ? 'selected' : '') . '
									if (isset($subunits))
										foreach ($subunits as $unit) {
											//if ($unit->id == $sunit->id || $unit->name == $sunit->name) continue;
											echo '<option data-id="'.$unit->id.'" data-parent-id="'.$unit->parent_id.'" class="nested'.$unit->step.'"value="' . $unit->id . '">' . $unit->name . '</option>';
									} ?>
								</select>
								</span>
							</td>
							<td width="10%">
								<input type="text" value="" id="description" name="transactions[<?=$step?>][description]" placeholder="">
							</td>
							<td width="10%">
						  		<input type="text" value="" id="debit" class="calculateTotal" name="transactions[<?=$step?>][debit]" placeholder="0.00">
						    </td>
							<td width="10%">
							<span class="select">																																	
								<select class="editable-select quick-add set-up" id="class_id" name="transactions[<?=$step?>][class_id]" >
								<option value="-1" selected ></option>
                                <?php foreach($classes as $class): 
								echo '<option value="' . $class->id . '" >' . $class->description . '</option>';
		
                                 endforeach; ?>
                                </select>
							</td>
							<td width="10%" id="names">
							<span class="select">
								<select class="editable-select quick-add set-up" id="profile_id" name="transactions[<?=$step?>][profile_id]">
								<option value="-1" selected ></option>
                                <?php foreach($names as $name): 
									echo '<option value="' . $name->id . '" >' . $name->name . '</option>';
								  endforeach; ?>
                                </select>
								</span>
							</td>
						</tr>
								<?php $step++; } ?>
					</tbody>
					<!-- <style type="text/css" onload="formGrid.calculate($(this).closest('.modal'))"></style> -->

					<tfoot style="display: table; width: 100%; table-layout: fixed;">					
						<tr>
							<!-- <td></td>
							<td></td>
							<td class="text-right">Total:</td>
							<td><span class="wrapper"><span class="text-left">$</span> 1,200.00</span></td>
							<td></td>
							<td></td> -->
							<td></td>
							<td></td>
							<td></td>
							<td class="text-right">Total:</td>
							<td class="text-center"><span class="text-left">$</span> <span id="totalAmount"></span> </td>
							<td></td>
							<td></td>
						</tr>
					</tfoot>
                    </table>
			</div>
				<footer>
					 <ul class="list-btn">
						<!--<li><button type="submit" after="mnew">Save &amp; New</button></li>-->
						<li><button type="submit" after="mclose">Save &amp; Close</button></li>
						<li><button type="button">Cancel</button></li>						
					</ul>
					<?= $header ?
					"<ul>
						<li>Last Modified $header->modified</li>
						<li>Last Modified by $header->user</li>
					</ul>" : ''; ?>
				</footer>
			</form>          
		</div>
	</div>
  </div>
</div>

<script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/custom2.js"></script>
<script>

     var properties= <?php echo $jProperties; ?>;
     var names = <?php echo $jNames; ?>;
     var accounts = <?php echo $jAccounts; ?>;
    //  var units = <?php echo $jUnits; ?>;
	 var classes = <?php echo $jClasses; ?>;
	 var propertyAccounts = <?php echo $jPropertyAccounts; ?>;
     //var header = < ?php echo $header; ?>;
     console.log('got here');
	//filters names based on accounts
	function getNames(tr, accountId, step){
		var names =  $(tr).find('#names');
		$(names).empty();
		var filteredNames = filterNames(accountId);
		var newRow = `<span class="select"><select class="editable-select quick-add set-up" id="profile_id" name="transactions[`+step+`][profile_id]">
								<option value="-1" selected >Select Name</option>`
								for (var j = 0; j < filteredNames.length; j++) {
									newRow += `<option value='` + filteredNames[j].id + `'>` + filteredNames[j].name + `</option>`;
								}
                                newRow += `</select></span>`;
		$(names).append(newRow);
		$(names).find('.editable-select').editableSelect();
		console.log(accountId);
	}

	function filterNames(accountId){
		//accounts receivable - tenants
		if(accountId == 451){
			var vendorNames= [];
			for (var j = 0; j < names.length; j++) {
				if(names[j].profile_type_id == 3){
					vendorNames.push(names[j]);
				}
			}
			return vendorNames;
			//accounts payable - vendors
		}else if(accountId == 454){
			var tenantNames = [];
			for (var j = 0; j < names.length; j++) {
				if(names[j].profile_type_id == 1){
					tenantNames.push(names[j]);
				}
			}
			return tenantNames;
			//all profiles
		}else{
			return names;
		}
	}
			
        // var newRow = `Property
		// 								<select class="w135 editable-select quick-add set-up "  id="accountId" name="property_id"  modal="" type="table" key="">
		// 									<option value="-1" selected ></option>`
		// 								for (var a = 0; a < properties.length; a++) {
        //                                     newRow += `<option value='` + properties[a].id + `'`;
        //                                     if(header.property_id == properties[a].id){ newRow += 'selected'};
        //                                     newRow += `>` + properties[a].name + `</option>`;
		// 								}
		// 								newRow +=`	</select>
        //                                 `;
        //     // $('#property_id').append(newRow);
        //     // $('#property_id').find('.editable-select').editableSelect();
            
        //     var newRow2 = `Vendor
		// 								<select class="w135 editable-select quick-add set-up "  id="vendor2" name="account_id"  modal="" type="table" key="">
		// 									<option value="-1" selected ></option>`
		// 								for (var a = 0; a < names.length; a++) {
        //                                     newRow2 += `<option value='` + names[a].id + `'`;
        //                                     if(header.profile_id == names[a].id){ newRow2 += 'selected'};
        //                                     newRow2 += `>` + names[a].vendor + `</option>`;
		// 								}
		// 								newRow2 +=`	</select>
        //                                 `;
            // $('#vendorTest').append(newRow2);
            // $('#vendorTest').find('.editable-select').editableSelect();
            // console.log(newRow);
            // console.log(newRow2);
  </script>