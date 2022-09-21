
<div class="modal fade check-modal <?php echo $edit ?>" id="editPaybill" doc-type="transactions" tabindex="-1" role="dialog"  type="bill-payment" ref-id="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root" class="no-print">
    		<div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				<form action="<?php echo $target; ?>" method="post" autocomplete="off" type="paybill">
				<input type="hidden" id="saveAndPrint" name="saveAndPrint"  value="0"/>
		
				<?php if (isset($headerTransaction->rec_id)) {
							echo '<input type="hidden" name="headerTransaction[rec_id]" id="rec_id"  value="' . $headerTransaction->rec_id . '"/>';
						}
						if (isset($headerTransaction->clr)) {
							echo '<input type="hidden" name="headerTransaction[clr]" id="clr"  value="' . 1 . '"/>';
						} ?>
       
                <header class="modal-h">
					<h2>Bill payment</h2>
					<p>Bank Balance : $<span id="bankBalance"> <?php echo $header->balance   ?></span></p>
					<nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
					<nav>
						<ul>
							<li><a href="#"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
							<li><a href="#"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
							<li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li class="get_send_email_form"><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
							<li><a href="./"><i class="icon-brain"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
							<li class="getDocuments"><a href="#" class="uploadDocument"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
							<li><a class="print editBillPrint" href="#"><i class="icon-print"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>
                <section class="d">
										<div class="<?php echo $hasRecId ?>">
												<?php echo $hasRecIdHtml ?>
										</div>
					<div class="double h">
					
						<div>
						   <p class="select" id="bankInfo">
								<label for="accountName">Bank:</label>
								<span stype="account" class='select'>
									
									<input  hidden-name = 'bank' name="bank" class="fastEditableSelect" filter_key ='details' filter_value = 'BK' stype="account" value="<?php echo (isset($header->account_id)) ?  $header->account_id  : ''; ?>"  key="accounts.name" modal="account"  default="<?php echo (isset($header->account_id)) ?  $header->account_id  : ''; ?>"></input>
							    </span>
							</p>
<!-- 							<p id="bankInfo" class="select">
								<label for="bank" >Bank:</label>
								<select  class="editable-select" name="bank" onchange="JS.loadList('api/getBalance', $(this).closest('#bankInfo').find('input[type=hidden]').val() , '#bankBalance' ,$(this).closest('#editPaybill'))">
								< ?php foreach($bankAccounts as $bankAccount): ?>
								  <option value="< ?= $bankAccount->id ?>" < ?php echo  ($header->account_id === $bankAccount->id) ? 'selected' : '' ?>  ><?php echo  $bankAccount->name ?></option>
								< ?php endforeach; ?>
								</select>
							</p> -->
							<p id="editBillproperty_id" class="select">
								<label for="property_id">Property:</label>
								<select id="property_id" class="editable-select" name="property" onChange="JS.loadBills('api/getTransactionsEdit', null,  {vendor: $($(this).closest('#editBillVendorAddress')[0]).find('input[type=hidden]')[0]).val(), th_id: <?= isset($header) && isset($header->id) ? $header->id : '' ?> , property_id: $(this).closest('#propertyId').find('input[type=hidden]').val()} , null ,'#editBillBody',  $(this).closest('#editPaybill'));">
								<?php foreach($properties as $property): ?>
								  <option value="<?= $property->id ?>"  <?php echo  ($header->property_id === $property->id) ? 'selected' : '' ?> ><?php echo  $property->name ?></option>
								<?php endforeach; ?>
								</select>
							</p>
							<p id="editBillVendorAddress" class="select">
								<label for="payee">Payee:</label>
								<select id="editBillName" class="editable-select" name="vendor"

								id="profile_id"  resetId="<?=  $header->profile_id ?>" >
									<?php foreach($vendors as $vendor): ?>
									<option value="<?= $vendor->id ?>"  <?php echo  ($header->profile_id === $vendor->id)  ? 'selected' : '' ?>><?php echo $vendor->vendor ?></option>
									<?php endforeach; ?>
								</select>
							</p>
							<p class="overlay-a text-indent" id="editBillAddress"><?php echo  $header->address_line_1 ?> <br><?php echo  $header->address_line_2 . ' '. $header->city ?></p>
						</div>
						<div>
							<p>
								<label for="reference">Reference:</label>
								<input type="text" id="transaction_ref" name="header[transaction_ref]" value="<?php echo $header->transaction_ref ?>" placeholder="Enter Ref #">
							</p>
							<p>
								<label for="date">Date:</label>
								<input type="text" data-toggle="datepicker" id="editBillDate" name="header[transaction_date]"  value="<?= isset($header) && isset($header->date) ?  $header->date:date('m/d/Y') ?>">
							</p>
							<p>
								<label for="amount">Amount: <span class="prefix">$</span></label>
								<input type="text" id="editBillAmount" 	resetVal="<?php echo $header->credit ?>"   name="amount" value="<?php echo $header->credit ?>">
							</p>
							<p class="select">
								<label for="class_id">Class</label>
								<select id="class_id" name="class" class="editable-select">>
								<option value="-1" selected >Select Class</option>
                                <?php foreach($classes as $class): 
								echo '<option value="' . $class->id . '" ' . (isset($header) && $header->class_id == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
		
                                 endforeach; ?>
                                </select>
							</p>
							<?php echo $RecIdHtml ?>
						</div>
					</div>
					<p class="m0">
						<label for="memo">Memo:</label>
						<input type="text"  name="header[memo]" value="<?php echo $header->memo ?>">
						<input type="hidden"  name="header[id]" value="<?php echo $header->id ?>">
					</p>
					</p>
				</section>
				<input type="hidden" id="th_id" value="<?php echo $header->id ?>"/>

             <div id="DataTables_Table_9_wrapper" class="dataTables_wrapper has-table-c mobile-hide c text-center">
			 	<div class="dataTables_scroll">
				 <div class="dataTables_scrollHead" style="overflow: hidden; position: relative; border: 0px; width: 100%;">
				  <div class="dataTables_scrollHeadInner" style="box-sizing: content-box; padding-right: 5px;">
					<table class="table-c c text-center mobile-hide dataTable" style="z-index: 16; margin-left: 0px; " role="grid">
							<thead>
								<tr role="row">
									<th></th>
									<th>Reference</th>
									<th>Account</th>
									<th>Property</th>
									<th>Description</th>
									<th>Due Date</th>
									<th>Original Amount</th>
									<th>Open</th>
									<th>Amount</th>
								</tr>
							</thead>
						</table>
					</div>
					</div>
					<div class="dataTables_scrollBody" style="position: relative; overflow-y: auto; overflow-x: hidden; max-height: 375px; width: 100%;">
					<table class="table-c c text-center mobile-hide dataTable clickable2" style="z-index: 16; width: 100%;" id="DataTables_Table_9" role="grid" aria-describedby="DataTables_Table_9_info">
					
					
					<tbody id="editBillBody">
						
					<?php foreach($transactions as $transaction): // for($i = 0; $i <5 ; $i++): // foreach($transactions as $transaction): ?>					
						
						<tr class="edit_bill_row" data-id="<?php echo $transaction->th_id ?>" data-type="<?php echo $transaction->type ?>">
								<td  id="edit_bill_check" >
								<input  class="editBill_input" type="hidden" name="applied_payments[<?php echo $transaction->id ?>][applied]" value="<?php echo ($transaction->payment != 0  &&  $transaction->payment > 0   )  ? '1' : '0' ; ?>">
								<input type="hidden" name="applied_payments[<?php echo $transaction->id ?>][transaction_id_b]" value="<?php echo $transaction->id ?>"> 
								<i id="edit_bill_icon_check" style="visibility :<?php echo  ( $transaction->payment != 0  &&  $transaction->payment > 0   ) ? 'visible' : 'hidden' ; ?>" class="icon-check" aria-hidden="true"></i>
									<div class="shadow" style="width: 1215px;"></div>
								</td>
								<td><?php echo $transaction->transaction_ref ?></td>
								<td><?php echo $transaction->account_name ?></td>
								<td><?php echo $transaction->property_name ?></td>
								<td><?php echo $transaction->description  ?></td>
								<td><?php echo $transaction->due_date  ?></td>
								<td><?php echo $transaction->amount ?></td>
								<td><span class="wrapper"><span class="text-left">$</span> <span class="edit_bill_open"> <?php echo $transaction->open_balance ?>  </span>   </span></td>

								<td class="edit_bill_row_input_amount">
								   <span class="input-amount">
										<label >$</label>
										<input type="text" id="edit_bill_input_amount" 
										
											name="applied_payments[<?php echo $transaction->id ?>][amount]"  
											value="<?php echo $transaction->payment ?>" 
											<?php echo  ( $transaction->payment != 0  ) ? '' : 'disabled' ; 
										?> > 
									</span>
								</td>
						</tr>

					<?php endforeach; // endfor; // endforeach; ?>		


					</tbody>
					<style type="text/css" onload="ebCalcAmount($(this).closest('#editPaybill') ); ebCalcOpen( $(this).closest('#editPaybill') )"></style>
					
				</table>
				</div>
				<div class="dataTables_scrollFoot" style="overflow: hidden; border: 0px; width: 100%;">
				<div class="dataTables_scrollFootInner" style=" padding-right: 5px;">
				<table class="table-c c text-center mobile-hide dataTable" style="z-index: 16; margin-left: 0px;" role="grid">
				<tfoot>					
					<tr>
						<td rowspan="1" colspan="1" style="width: 46px;"><div class="shadow" style="width: 1014.67px;"></div></td>
						<td style="text-align: right; padding-right: 0; overflow: visible">To be printed</td>
						<td class="check-a a">
							<label for="printEditBill" class="checkbox">
									<input type="hidden" name="printEditBill" value="0"/><input type="checkbox" value="1" id="printEditBill" name="printEditBill" class="hidden" aria-hidden="true">
									<div style="margin-left: 0;" class = "input"></div>
							</label>
						</td>
						<td rowspan="1" colspan="1" style="width: 136px;"></td>
						<td class="text-right" rowspan="1" colspan="1" style="width: 186px;">Total:</td>
						<td rowspan="1" colspan="1" style="width: 122px;"><span class="wrapper"><span class="text-left">$</span> <span id="edit_bill_total_open"> 1,200.00  </span></span></td>
						<td rowspan="1" colspan="1" style="width: 124px;"><span class="wrapper"><span class="text-left">$</span> <span id="edit_bill_total_amount"> 1,200.00  </span></span></td>
					</tr>
					</tfoot>
					</table>
					</div>
					</div>
					
					<div class="dataTables_info" id="DataTables_Table_9_info" role="status" aria-live="polite">Showing 1 to 36 of 36 entries</div></div>
					
				</table>
			</div>


     
				 <footer>
					<ul class="list-btn">
					    <li><button class="saveAndPrint" type="submit" after="mclose">Save &amp; print</button></li>
						<li><button type="submit" after="mnew">Save &amp; New</button></li>
						<li><button type="submit"  after="mclose">Save &amp; Close</button></li>
						<li><button type="button">Duplicate</button></li>
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
<div id="check_print" style ="display:none;" > </div>
</div>









    