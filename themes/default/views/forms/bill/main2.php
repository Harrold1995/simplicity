<?php $billTotal = 0; ?>

<div class="modal fade bills-modal <?php echo $edit ?>" id="billsModal" tabindex="-1" role="dialog" doc-type="transactions" main-id=<?= isset($header) && isset($header->id) ? $header->id : '-1' ?> type="bill" ref-id="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
               <div class="modalContent">
                  <form action="<?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="2">
                  
				  <?php if(isset($headerTransaction) && isset($headerTransaction->id)){
									echo '<input type="hidden" name="headerTransaction[id]" value="' . $headerTransaction->id . '"/>';
                            } ?>

                   <?php if(isset($headerTransaction) && $paidStatus != ""){
					    $origAmt = isset($headerTransaction) && $headerTransaction->credit > 0  ? str_replace("-","",$headerTransaction->credit)  : (isset($headerTransaction) && isset($headerTransaction->debit) ? str_replace("-","",$headerTransaction->debit)  : "");
						  echo '<input type="hidden" name="applied" value="1"/>';
						  echo '<input type="hidden" name="origAmt" value="'.$origAmt. '"/>';
                   } ?>
                            
                  <header class="modal-h">
					<h2 class="text-uppercase"><?php if(isset($headerTransaction) && isset($headerTransaction->id)){
                                    					echo 'Edit bill';
                            						 } else{ echo 'New bill';} ?>
					</h2>
					<ul class="check-a">
					<?php $normal_radio_button = 'checked'; if(isset($headerTransaction) && isset($headerTransaction->debit) && $headerTransaction->debit > 0){$normal_radio_button = ''; $credit_radio_button = 'checked';}
					?>
						<li><label for="radioButton"> Bill</label><input type="radio" id="bill" name="radioButton" value="normal"<?php echo $normal_radio_button; ?>></li>
						<li><label for="credit"> Credit</label><input type="radio" id="credit" name="radioButton" value="credit"<?php echo $credit_radio_button; ?>></li>
                    </ul>
                    <nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style=";padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
					<nav>
						<ul>
							<li><a href="./" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
							<li><a href="./" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
							<li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li class="get_send_email_form"><a href="#!" id="email"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
							<li><a href="#"><i class="icon-brain"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
							<li class='getDocuments tooltipstered'><a href="#" class="uploadDocument"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>
                <section class="a modal-body">
					<div class="<?php echo $paidStatus ?>">
					<?php echo $paidStatusHtml ?>
					</div>
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
								<select id="profile_id" onchange="var id = $(this).closest('.select').find('input[type=hidden]').val();
								$(this).closest('div').find('input#defaccount').val(_.result(_.find(JS.sdata['profile'], ['id', id]),'defaccount'));"
                                        name="headerTransaction[profile_id]" class="editable-select">>
								<option value="-1" selected >Select Vendor</option>
                                <?php foreach($names as $name): 
									echo '<option value="' . $name->id . '" ' . (isset($headerTransaction) && $headerTransaction->profile_id == $name->id ? 'selected' : '') . '>' . $name->vendor . '</option>';
								  endforeach; ?>
                                </select>

							</p>
                            <input type="hidden" id="defaccount" value="-1">
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
									<option selected>30 days</option>
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
								<input type="text"  class="decimal topAmount toggleRadio" value="<?= isset($headerTransaction) && $headerTransaction->credit > 0  ? str_replace("-","",$headerTransaction->credit)  : (isset($headerTransaction) && isset($headerTransaction->debit) ? str_replace("-","",$headerTransaction->debit)  : '') ?>" id="amount" name="headerTransaction[credit]" placeholder="Enter Amount">
							</p>
							<p>
								<?php $dueDate = date('Y-m-d', strtotime('+ 30 days'));?>
								<label for="date">Due Date</label>
								<input data-toggle="datepicker" id="dueDate" value="<?= isset($bills) && isset($bills->due_date) ? $bills->due_date : $dueDate ?>" name="bills[due_date]" autocomplete="off">
							</p>
						</div>
					</div>	
					<p>
						<label for="memo">Memo:</label>
						<input type="memo" value="<?= isset($header) && isset($header->memo) ? $header->memo : '' ?>" id="memo" name="header[memo]" placeholder="Enter Memo">
					</p>
					<div class="submit">
						<span class="select">
								<label for="request_approval_from">Request approval from</label>
								<select id="request_approval_from" name="bills[request_approval_from]" class="editable-select">>
                                <?php foreach($employees as $employee): 
									echo '<option value="' . $employee->id . '" ' . (isset($bills) && $bills->request_approval_from == $employee->id ? 'selected' : '') . ' >' . $employee->employee . '</option>';
								  endforeach; ?>
                                </select>
						</span>
						<?php if(isset($bills) && ($bills->original != "")){ ?>
								<a href="<? base_url() . "uploads/images/" . $bills->original; ?>">Original is attached</a>
						<?php } else{?>
							<p class="input-file">
								<label for="p-image">
									<input type="file" name="bills[original]"  id="p-image" targetimg="#original-bill"> 
									<span>Attach Original</span>
								</label>
							</p>
						<?php } ?>
					</div>

                 
				</section>

				<div class="has-table-c">
				<table class="table-c billTable mobile-hide dataTable no-footer formGridTable" >
					<thead class="dataTables_scrollHead" >
						<tr>
							<th width="10%">Account</th>
							<th width="10%">Property</th>
							<th width="10%">Unit</th>
							<th width="10%">Description</th>
							<th width="10%" class="text-center">Amount</th>
                            <th width="10%">Class</th>
							<th width="10%">Name</th>
						</tr>
					</thead>

					<tbody id="billFormBody" class="dataTables_scrollBody"  >

					
					
						

					</tbody>

					
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
			<li><button class="saveAndPay" type="submit" after="mclose">Save &amp; Pay</button></li>
            <li><button type="submit" after="duplicate">Duplicate</button></li>
            <li><button type="button">Cancel</button></li>
            
          </ul>
		  <?= $header ?
          "<ul>
            <li>Last Modified $header->modified</li>
            <li>Last Modified by $header->user</li>
          </ul>" : ''; ?>
        </footer>
		<input type="hidden" id="saveAndPay" name="saveAndPay" value="0"/>
      </form>
    </div>
                                      

                              
                    </div>
		</div>

</div>
</div>


<!--<script defer src="< ?php echo base_url(); ?>themes/default/assets/javascript/custom2.js"></script>-->

 <script type="text/javascript">
    
	var transactionsArray = <?php echo $jTransactions; ?>;
	var type = <?php echo '"'.$normal_radio_button.'"'; ?>;
	console.log(transactionsArray);


	
	 

            var template = function (id, data = {}) {
                var newRow = '<tr class="checkRow" id="' + id + '" ' + (data.id ? 'tid="' + data.id + '"' : '') + ' ' + (data.property_id && data.property_id != '-1' ? 'property_id="' + data.property_id + '"' : '') + ' ' + (data.account_id && data.account_id != '-1' ? 'account_id="' + data.account_id + '"' : '') + '>' +
                    '<td class="formGridSelectTd" sclasses = "es-setup" stype="account" source="#defaccount" ' + (data.account_id ? 'value="' + data.account_id + '"' : '') + '></td>' +
                    '<td class="formGridSelectTd"  sclasses = "es-setup"stype="property" source="[sel-id=property_id]" ' + (data.property_id ? 'value="' + data.property_id + '"' : '') + '></td>' +
                    '<td class="formGridSelectTd" sclasses = "es-setup" stype="unit" ' + (data.unit_id ? 'value="' + data.unit_id + '"' : '') + '></td>' +
                    '<td><input type="text" id="description" name="transactions[' + id + '][description]" value="' + (data.description ? data.description : '') + '"></td>' +
                    '<td source="#amount" total="debit"><input type="text" class="decimal checkAmount total" id="amount" name="transactions[' + id + '][debit]" value="' + (data.debit || data.credit ? (type == "checked" ? data.debit - data.credit : data.credit - data.debit) : '') + '" placeholder="0"></td>' +
                    '<td class="formGridSelectTd " sclasses = "es-add" stype="class" source="[sel-id=class_id]" ' + (data.class_id ? 'value="' + data.class_id + '"' : '') + '></td>' +
					'<td class="formGridSelectTd quick-add setup" stype="profile" value="' + (data.profile_id ?  data.profile_id + (data.lease_id ? "-" + data.lease_id  : '') : '') + '"><span class="select"><input></span></td>' +
                    '</tr>';
                return newRow;
            }
            var grid = $('.modal').last().formGrid({
                template: template,
                data: <?php echo $jTransactions ? $jTransactions : 0 ?>,
                minRows: 8
            });
            grid.addTotal('debit', '#amount', '#amount', '#totalAmount');

    

</script>




    