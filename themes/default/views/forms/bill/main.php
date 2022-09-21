<?php $billTotal = 0; ?>

<div class="modal fade bills-modal" id="billsModal" tabindex="-1" role="dialog" main-id=<?= isset($bills) && isset($bills->id) ? $bills->id : '-1' ?> type="bill" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
                  <form action="<?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="2">
                  
				  <?php if(isset($headerTransaction) && isset($headerTransaction->id)){
                                    echo '<input type="hidden" name="headerTransaction[id]" value="' . $headerTransaction->id . '"/>';
                            } ?>
                            
                  <header class="modal-h">
					<h2 class="text-uppercase"><?php if(isset($headerTransaction) && isset($headerTransaction->id)){
                                    					echo 'Edit bill';
                            						 } else{ echo 'New bill';} ?>
					</h2>
					<ul class="check-a">
					<?php $normal_radio_button = 'checked'; if(isset($headerTransaction) && isset($headerTransaction->credit) && $headerTransaction->credit < 0){$normal_radio_button = ''; $credit_radio_button = 'checked';}
					?>
						<li><label for="radioButton"> Bill</label><input type="radio" id="bill" name="radioButton" value="normal"<?php echo $normal_radio_button; ?>></li>
						<li><label for="credit"> Credit</label><input type="radio" id="credit" name="radioButton" value="credit"<?php echo $credit_radio_button; ?>></li>
					</ul>
					<nav>
						<ul>
							<li><a href="./" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
							<li><a href="./" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
							<li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li><a href="#!" id="email"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
							<li><a href="./"><i class="icon-brain"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
							<li><a href="./"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>
                <section class="a modal-body">
					<div class="double d m20">
						<div>
							<p class="select">
								<label for="property_id">Property</label>
								<select id="property_id" name="headerTransaction[property_id]" class="editable-select">
								 <option value="-1" selected >Select Property</option>
								<?php
                            foreach ($properties as $property) {
                                // echo '<option value="-1" selected >' . "Select Apples" . '</option>';
                                echo '<option value="' . $property->id . '" ' . (isset($headerTransaction) && $headerTransaction->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
                            } ?>
                                
                                </select>
							</p>
							<p class="select">  
                                <label for="profile_id">Vendor</label>
								<select id="profile_id" name="headerTransaction[profile_id]" class="editable-select">>
								<option value="-1" selected >Select Vendor</option>
                                <?php foreach($names as $name): 
									echo '<option value="' . $name->id . '" ' . (isset($headerTransaction) && $headerTransaction->profile_id == $name->id ? 'selected' : '') . '>' . $name->vendor . '</option>';
								  endforeach; ?>
                                </select>
								
							</p>
							<p  class="select">
								<label for="class_id">Class</label>
								<select id="class_id" name="headerTransaction[class_id]" class="editable-select">>
								<option value="-1" selected >Select Class</option>
                                <?php foreach($classes as $class): 
								echo '<option value="' . $class->id . '" ' . (isset($headerTransaction) && $headerTransaction->class_id == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
		
                                 endforeach; ?>
                                </select>
							</p>
							<p  class="select">
								<label for="terms">Terms</label>
								<select id="terms" name="bills[terms]">
								    <option value="-1" selected>Select Terms</option>
									<option>30 days</option>
									<option>40 days</option>
									<option>40% deposit</option>
									<option>15 days</option>
									<option>10 days</option>
								<?php if(isset($bills) && isset($bills->terms)){
								echo "<option selected>$bills->terms</option>";
							 } ?>
							 
								</select>
							</p>
							
						</div>
						<div>
							<p>
								<label for="transaction_ref">Reference</label>
								<input type="text" value="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" id="transaction_ref" name="header[transaction_ref]" placeholder="Enter Ref#">
							</p>
							<p>
								<label for="transaction_date">Bill Date</label>
								<input data-toggle="datepicker" id="billDate" name="header[transaction_date]" value="<?php echo $header->date?>" autocomplete="off">
							</p>
							<p>
								<label for="amount">Amount <span class="prefix">$</span></label>
								<input type="text"  class="decimal topAmount toggleRadio" value="<?= isset($headerTransaction) && isset($headerTransaction->credit) ? str_replace("-","",$headerTransaction->credit)  : '' ?>" id="amount" name="headerTransaction[credit]" placeholder="Enter Amount">
							</p>
							<p>
								<label for="date">Due Date</label>
								<input data-toggle="datepicker" id="dueDate" value="<?php echo $bills->due_date?>" name="bills[due_date]" autocomplete="off">
							</p>
						</div>
					</div>	
					<p>
						<label for="memo">Memo:</label>
						<input type="memo" value="<?= isset($header) && isset($header->memo) ? $header->memo : '' ?>" id="memo" name="header[memo]" placeholder="Enter Memo">
					</p>
					<div class="submit">
						<span>
							<label for="fer">Request approval from</label>
							<select id="fer" name="fer">
								<option>none</option>
								<option>Position #1</option>
								<option>Position #2</option>
								<option>Position #3</option>
								<option>Position #4</option>
								<option>Position #5</option>
							</select>
						</span>
						<p class="input-file">
                        	<label for="p-image"><input type="file" name="original"  id="p-image" targetimg="#original-bill"> <span>Attach Original</span></label>
						</p>
					</div>

                 
				</section>

				<div class="has-table-c">
				<table class="table-c billTable dataTable no-footer" >
					<thead class="dataTables_scrollHead" >
						<tr>
							<th width="10%">Account</th>
							<th width="10%">Property</th>
							<th width="10%">Unit</th>
							<th width="10%">Description</th>
							<th width="10%" class="text-center">Amount</th>
							<th width="10%">Name</th>
							<th width="10%">Class</th>
						</tr>
					</thead>

					<tbody id="billFormBody" class="dataTables_scrollBody" style=" display: block;height: calc(100vh - 580px);overflow: auto;" >

					<?php if(isset($transactions)): ?>
					<?php foreach ($transactions as $transaction )  : ?>
                        <?php if(isset($transaction) && isset($transaction->id)){
                                    echo '<input type="hidden" name="transactions[' .$transaction->id . '][id]" value="' . $transaction->id . '"/>';
                                    } ?>

							<tr id="<?php echo $transaction->id  ?>" class="fillNextRow" onclick="addRowToBillsForm($(this).closest('#billFormBody'),$(this) ,$(this).closest('tr').attr('id') )"
							oncontextmenu="formGrid.customContext(<?php echo $transaction->id  ?>,event,$(this).closest('.modal'))">
							<td  class="formGridAccountTd">
							<span class="select">
								<select class=" editable-select quick-add set-up" id="account_id" name="transactions[<?php echo $transaction->id ?>][account_id]">
								<!-- <option value="-1" selected ></option>
                                < ?php foreach($accounts as $account): 
                                echo '<option value="' . $account->id . '" ' . (isset($transaction) && $transaction->account_id == $account->id ? 'selected' : '') . '>' . $account->name . '</option>';
                                 
								  endforeach; ?> -->
								  <?php
									echo '<option class="nested0" value="0"></option>'; //' . (isset($unit) && $unit->parent_id == 0 ? 'selected' : '') . '
									if (isset($subaccounts))
										foreach ($subaccounts as $saccount) {
											//if ($unit->id == $sunit->id || $unit->name == $sunit->name) continue;
											echo '<option data-id="'.$saccount->id.'" data-parent-id="'.$saccount->parent_id.'" class="nested'.$saccount->step.'"value="' . $saccount->id . '" ' . (isset($transaction) && $transaction->account_id == $saccount->id ? 'selected' : '') . '>' . $saccount->name . '</option>';
									} ?>
								</select>
								</span>
							</td>
							<td>
							<span class="select">
								<select class=" editable-select quick-add set-up formGridPropertySelected" id="property_id" name="transactions[<?php echo $transaction->id ?>][property_id]">
								<option value="-1" selected ></option>
                                <?php foreach($properties as $property): 
                                echo '<option value="' . $property->id . '" ' . (isset($transaction) && $transaction->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
                                  endforeach; ?>
								</select>
								</span>
							</td>
							<td>
							<span class="select">
								<select class="editable-select quick-add set-up formGridUnitSelect" id="unit_id" modal ="unit" name="transactions[<?php echo $transaction->id ?>][unit_id]">
								<option value="-1" selected ></option>
                                <!-- < ?php foreach($units as $unit): 
                                echo '<option value="' . $unit->id . '" ' . (isset($transaction) && $transaction->unit_id == $unit->id ? 'selected' : '') . '>' . $property->name . '</option>';
								 endforeach; ?> -->
								<?php
									echo '<option class="nested0" value="0"></option>'; //' . (isset($unit) && $unit->parent_id == 0 ? 'selected' : '') . '
									if (isset($subunits))
										foreach ($subunits as $unit) {
											//if ($unit->id == $sunit->id || $unit->name == $sunit->name) continue;
											echo '<option data-id="'.$unit->id.'" data-parent-id="'.$unit->parent_id.'" class="nested'.$unit->step.'"value="' . $unit->id . '" ' . (isset($transaction) && $transaction->unit_id == $unit->id ? 'selected' : '') . '>' . $unit->name . '</option>';
									} ?>
								</select>
								</span>
							</td>
							<td>
                                <input type="text" value="<?= isset($transaction) && isset($transaction->description) ? $transaction->description : '' ?>" id="description" name="transactions[<?php echo $transaction->id ?>][description]"   >
                            </td>
							<td class="text-right"> <input type="text"  class="decimal calculateTotal" value="<?= isset($transaction) && isset($transaction->debit) ? $transaction->debit : '' ?>" id="amount" name="transactions[<?php echo $transaction->id ?>][debit]" ></td>
							<?php if(isset($transaction->debit)){ $billTotal += $transaction->debit; } ?>
							<td>
							<span class="select">
								<select class="editable-select quick-add set-up" id="profile_id" name="transactions[<?php echo $transaction->id ?>][profile_id]">
								<option value="-1" selected ></option>
                                <?php foreach($names as $name): 
								echo '<option value="' . $name->id . '" ' . (isset($transaction) && $transaction->profile_id == $name->id ? 'selected' : '') . '>' . $name->vendor . '</option>';
                                  endforeach; ?>
								</select>
								</span>
							</td>
							<td>
							<span class="select">
								<select class="editable-select quick-add set-up"id="class_id" name="transactions[<?php echo $transaction->id ?>][class_id]">
								<option value="-1" selected ></option>
                                <?php foreach($classes as $class): 
								echo '<option value="' . $class->id . '" ' . (isset($transaction) && $transaction->class_id == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
                                  endforeach; ?>
							
								</select>
								</span>
							</td>
						</tr>


									<?php endforeach; ?>
					<?php endif; ?>

                       

						<tr id="1" class="fillNextRow <?php if(!$transactions){echo ' fillFirstRow';}?> " onclick="addRowToBillsForm($(this).closest('#billFormBody'),$(this) ,$(this).closest('tr').attr('id') )">
							<td class="formGridAccountTd">
							<span class="select">
								<select class=" editable-select quick-add set-up" id="account_id" name="transactions[1][account_id]">
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
								</span>
							</td>
							<td>
							<span class="select">
								<select class="editable-select quick-add set-up formGridPropertySelected" id="property_id" name="transactions[1][property_id]">
								<option value="-1" selected ></option>
                                <?php foreach($properties as $property): ?>
									<option value="<?php echo  $property->id ?>"  ><?php echo  $property->name ?></option>		
                                 <?php endforeach; ?>
								</select>
								</span>
							</td>
							<td>
							<span class="select">
								<select class="editable-select quick-add set-up formGridUnitSelect" id="unit_id" name="transactions[1][unit_id]">
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
							<td>
                                <input type="text" id="description" name="transactions[1][description]"  >
                            </td>
							<td class="text-right"> <input type="text"  class="decimal calculateTotal" id="amount" name="transactions[1][debit]"></td>
							<td>
							<span class="select">
								<select class="editable-select quick-add set-up" id="profile_id" name="transactions[1][profile_id]">
								<option value="-1" selected ></option>
                                <?php foreach($names as $name): ?>
									<option value="<?php echo $name->id ?>"  ><?php echo  $name->vendor ?></option>		
                                 <?php endforeach; ?>
								</select>
								</span>
							</td>
							<td>
							<span class="select">
								<select class="editable-select quick-add set-up"id="class_id" name="transactions[1][class_id]">
								<option value="-1" selected ></option>
                                <?php foreach($classes as $class): ?>
									<option value="<?php echo $class->id ?>"  ><?php echo  $class->description ?></option>		
                                 <?php endforeach; ?>
							
								</select>
								</span>
							</td>
						</tr>
					
					
					
						

					</tbody>

					<style type="text/css" onload="getRows($(this).closest('#billsModal').find('#billFormBody') )"></style>
					<style type="text/css" onload="formGrid.calculate($(this).closest('.modal'))"></style>
					
				  <tfoot  style="display: table; width: 100%; table-layout: fixed;">
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td class="text-right">Total:</td>
							<td class="text-center"><span class="text-left">$</span> <span id="totalAmount"><?php echo  $billTotal> 0 ? $billTotal
							 : '.00 ' ?></span> </td>
							<td></td>
							<td></td>
						</tr>
					</tfoot>

				  </table>
				  </div>

         
     
        <footer>
          <ul class="list-btn">
            <li><button type="submit" after="mnew">Save &amp; New</button></li>
            <li><button type="submit" after="mclose">Save &amp; Close</button></li>
            <li><button type="submit" after="duplicate">Duplicate</button></li>
            <li><button type="button">Cancel</button></li>
            
          </ul>
		  <?= $header ?
          "<ul>
            <li>Last Modified $header->modified</li>
            <li>Last Modified by <a href='#!'>$header->user</a></li>
          </ul>" : ''; ?>
        </footer>
      </form>
                                      

                              
                    </div>
		</div>

</div>
</div>


<!--<script defer src="< ?php echo base_url(); ?>themes/default/assets/javascript/custom2.js"></script>-->

 <script type="text/javascript">
     var classes = <?php echo $jClasses; ?>;
     var properties= <?php echo $jProperties; ?>;
	 var accounts = <?php echo $jAccounts; ?>;//not used anymore instead using subaccounts
	 var subaccounts = <?php echo $jsubaccounts; ?>;
	//  var units = < ?php echo $jUnits; ?>;//not used anymore instead using subunits
	 var subunits = <?php echo $jsubunits; ?>;
	 var units = <?php echo $jsubunits; ?>;//new added 2/7 
	 var names = <?php echo $jNames; ?>;
	var transactionsArray = <?php echo $jTransactions; ?>;
	var propertyAccounts = <?php echo $jPropertyAccounts; ?>;
	 
	 if(transactionsArray){
        var t = transactionsArray.length
     }else{
        var t = 1
   }
	 



	 //getRows();

   function getRows(item){
	if(t < 6){
			for(t ; t <7; t++){
				addRowToBillsForm(item,null ,t );
				
			}
	}else{
		t++;
		addRowToBillsForm(item,null ,t );
	}

  }
		
  $( function() {
	//  $( "#billDate" ).datepicker();
	//  $( "#dueDate" ).datepicker();
	 

  } );




function addRowToBillsForm(body, row, id) {

if (row == null || $(row).is(':last-child')) {
	console.log(id + "id")
	id++


	newRow = ' <tr id="' + id + '" class="fillNextRow createNewRow" >' +
		'<td class="formGridAccountTd"><span class="select">' +
		'<select class="editable-select quick-add set-up" id="account_id" name="transactions[' + id + '][account_id]">' +
		' <option value="-1" selected ></option>'
	// for (var i = 0; i < accounts.length; i++) {
	// 	newRow += '<option value=' + accounts[i].id + '>' + accounts[i].name + '</option>';
	// }
	for (var i = 0; i < subaccounts.length; i++) {
		newRow += '<option data-id="'+ subaccounts[i].id + '" data-parent-id="' + subaccounts[i].parent_id + '" class="nested' + subaccounts[i].step + '"value="' + subaccounts[i].id + '">' + subaccounts[i].name + '</option>';
	}
	newRow += ' </select>' +


		'</span></td>' +
		'<td><span class="select">' +
		'	<select class="editable-select quick-add set-up formGridPropertySelected" id="property_id"  name="transactions[' + id + '][property_id]">' +
		' <option value="-1" selected  ></option>'
	for (var i = 0; i < properties.length; i++) {
		newRow += '<option value=' + properties[i].id + '>' + properties[i].name + '</option>';
	}
	newRow += ' </select>' +
		'</span></td>' +
		'<td><span class="select">' +
		'<select class="editable-select quick-add set-up formGridUnitSelect" id="unit_id" name="transactions[' + id + '][unit_id]">' +
		' <option value="-1" selected ></option>'
	// for (var i = 0; i < units.length; i++) {
	// 	newRow += '<option  value=' + units[i].id + '>' + units[i].name + '</option>';
	// }
	for (var i = 0; i < subunits.length; i++) {
		newRow += '<option data-id="'+ subunits[i].id + '" data-parent-id="' + subunits[i].parent_id + '" class="nested' + subunits[i].step + '"value="' + subunits[i].id + '">' + subunits[i].name + '</option>';
	}
	newRow += ' </select>' +
		'</span></td>' +
		'<td>' +
		'<input type="text" id="description" name="transactions[' + id + '][description]"  >' +
		'</td>' +
		' <td class="text-right"> ' +
		' <input type="text"  class="decimal calculateTotal" id="amount" name="transactions[' + id + '][debit]"  >' +
		' </td>' +
		'<td><span class="select">' +
		'<select class="editable-select quick-add set-up" id="profile_id" class=" "  name="transactions[' + id + '][profile_id]" >' +
		' <option value="-1" selected ></option>'
	for (var i = 0; i < names.length; i++) {
		newRow += '<option value=' + names[i].id + '>' + names[i].vendor + '</option>';
	}
	newRow += ' </select>' +
		'</span></td>' +
		'<td><span class="select">' +
		'<select class="editable-select quick-add set-up" id="class_id" class=" "  name="transactions[' + id + '][class_id]">' +
		' <option value="-1" selected ></option>'
	for (var i = 0; i < classes.length; i++) {
		newRow += '<option value=' + classes[i].id + '>' + classes[i].description + '</option>';
	}
	newRow += ' </select>' +
		'</span></td>' +
		'	</tr>'
	body.append(newRow)

}
}
    

</script>




    