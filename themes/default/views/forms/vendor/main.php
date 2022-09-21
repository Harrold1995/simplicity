<div class="modal fade vendorPayBill-modal" id="vendorPayBillModal" tabindex="-1" role="dialog" main-id=<?= isset($reconciliation) && isset($reconciliation->r_id) ? $reconciliation->r_id : '-1' ?> type="account" aria-hidden="true" style="left:-325px">
        <div class="modal-dialog modal-dialog-centered modal-xl theme-c " role="document">
            <div id="root">
            
                <div class="modal-content text-primary form-bills popup-a  shown " style=" width:100%; padding: 30px; background:#F4F4F4;     border: 1px solid #dfe5e5;">
				  <form action="<?php echo $target; ?>" method="post" type="paybills">

                    <header  style="z-index: 4;" class="modal-h">
					<h2>Pay Bills</h2>
					<div>
						<p>
							<label for="accounts">Account</label>
							<span class="select">
								<select id="Paybill_accounts" name="accounts" class="editable-select" onchange="payBill.pbSetAccount($(this).val(), $(this).closest('.select').find('input[type=hidden]').val()  , $(this))">
									<?php foreach($bankAccounts as $account): ?>
										<option value="<?= $account->id ?>"><?php echo $account->name ?></option> 
									<?php endforeach; ?>
									<?php foreach($CcAccounts as $account): ?>
										<option value="<?= $account->id ?>"><?php echo $account->name ?></option> 
									<?php endforeach; ?>
								</select>
							</span>
						</p>
						<p>
							<label for="payment_type">Payment Method</label>
							<span class="select">
								<select id="Paybill_method" name="payment_type">
								<?php foreach($paymentMethods as $paymentMethod): ?>
										<option value="<?= $paymentMethod->id ?>"><?php echo $paymentMethod->name ?></option> 
									<?php endforeach; ?>
								</select>
							</span>
						</p>
						<p >
							<label for="pay_bill_date">Date</label>
							<input type="text" data-toggle="datepicker" name="header[transaction_date]"  id="pay_bill_date">
						</p>
					</div>
				</header>

				<ul id="radioSelectDiv" class="list-choose" ">
					<li>
						<ul  id="searchByDate"  searchTerm="all">
							<li id="dueDateLi">
								<label for="dueDate" class="radio" >
									<input  onchange="payBill.setSearchDate($(this), 'selected')"  type="radio" id="dueDate" name="dueDate" class="hidden" aria-hidden="true" ><div class="input"></div> Due date before:
								</label> 
								<span class="is-date">
									<input type="text" id="pay_bill_due-date" name=".." class="date" 
									onchange="payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv'), $(this))" >
								</span>
							</li>
							<li>
								<label for="fbf" class="radio">
								<input   value="all" type="radio" id="fbf" name="dueDate" class="hidden" aria-hidden="true" checked
								onchange="payBill.setSearchDate($(this), 'all'); payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv')) "
								><div class="input"></div> All bills
								</label>
							</li>
						</ul>
					</li>
					<li>
						<ul  id="searchByVend"  searchTerm="all">
							<li >
								<label for="searchByVendor" class="radio">
								<input onchange="payBill.setSearchVendor($(this), 'selected');" type="radio" id="searchByVendor" name="searchByVendor" class="hidden" aria-hidden="true" > <div class="input"></div> Vendor
								</label>
								<span id="vendorSelect" class="select">
									<select id="selecetedVendor" class="editable-select" onchange="payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv'), $(this) )">
									<?php foreach($vendors as $vendor ): ?>
										<option value="<?= $vendor->id ?>"><?= $vendor->vendor ?></option>
									<?php endforeach; ?>
									</select>
								</span>
							</li>
							<li><label for="allVendors" class="radio"><input type="radio" id="allVendors" name="searchByVendor" class="hidden" aria-hidden="true" checked 
							onchange="payBill.setSearchVendor($(this), 'all'); payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv')) "><div class="input"></div> All vendors</label></li>
						</ul>
					</li>
					<li>
						<ul  id="searchByProp"  searchTerm="all">
							<li id="searchByPropertyLi">
								<label for="searchByProperty" class="radio">
								<input onchange="payBill.setSearchProperty($(this), 'selected');" type="radio" id="searchByProperty" name="searchByProperty" class="hidden" aria-hidden="true" ><div class="input"></div> Property
								</label>
								<span id="propertySelect" class="select">
									<select id="selectedProperty" class="editable-select" onchange="payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv'), $(this) )">
									<?php foreach($properties as $property ): ?>
										<option value="<?= $property->id ?>"><?= $property->name ?></option>
									<?php endforeach; ?>
									</select>
								</span>
							</li>
							<li><label for="fbl" class="radio"><input type="radio" id="fbl" name="searchByProperty" class="hidden" aria-hidden="true" checked 
							onchange="payBill.setSearchProperty($(this), 'all'); payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv'))"><div class="input"></div> All properties</label></li>
						</ul>
					</li>
				</ul>


<div id="DataTables_Table_11_wrapper" class="dataTables_wrapper has-table-c mobile-hide d">
	<div class="dataTables_scroll">
		<div class="dataTables_scrollHead" style="overflow: hidden; position: relative; border: 0px; width: 100%;">
			<div class="dataTables_scrollHeadInner" style="box-sizing: content-box; ">
				<table class="table-c  mobile-hide dataTable" style="z-index: 2; " role="grid">
				<thead>
						<tr >
						    <th class="check-a"><label for="pay_bill_select_all" class="checkbox"><input type="checkbox" id="pay_bill_select_all" ></label></th>
							<th>Vendor</th>
							<th>Pmt Account</th>
							<th>Property</th>
							<th>Reference</th>
							<th>Bill date</th>
							<th>Due Date</th>
							<th>Bill amount</th>
							<th>Open balance</th>
							<th>Pay Amount</th>
						</tr>
					</thead>

							
					<tbody id="payBillBody" >

					<?php foreach($transactions as $transaction): ?>
						<tr class="pay_bill_row" xonclick="setAccountName($(this))">
								<td id="pay_bill_check">
									<i class="icon-check" id="pay_bill_icon_check" style="visibility :hidden" ></i>
									<input  class="pay_bill_input" type="hidden"   value="0">
								</td>

								<td class="strong" > 
								<input type="hidden" id="transactionId" value="<?= $transaction->id  ?>">
								<input type="hidden" class="paybill_vendor"  value="<?php echo $transaction->profile_id  ?>">
								<span><?php echo $transaction->vendor  ?></span>	 
								
								</td>

								<td  id="Paybill_payment_accounts_row">
									<select id="Paybill_payment_accounts" onchange="payBill.resetName($(this).closest('.pay_bill_row'))"  class="editable-select">
										<?php foreach($accounts as $account): ?>
											<option value="<?= $account->id ?>" <?php if($account->id == $transaction->default_bank) echo" selected ";?>><?php echo $account->name; if($account->id == $transaction->default_bank) echo" (Default)"; ?></option> 
										<?php endforeach; ?>
										
									</select>
								</td>
								<td class="strong">
									<input type="hidden" class="paybill_property" value="<?php echo $transaction->property_id  ?>">
									<span><?php echo $transaction->name  ?></span>	
								</td>
								<td class="strong"><?= $transaction->transaction_ref ?></td>
								<td><?= $transaction->transaction_date ?></td>
								<td><?= $transaction->due_date ?></td>
								<td class="strong text-center">$<span id="bill_amount"><?php echo  number_format($transaction->amount, 2); ?></span></td>
								<td>$<span id="open_balance"><?php echo  number_format($transaction->open_balance, 2); ?> </span> </td>
								<td><span class="input-amount">
										<input type="text" placeholder="0.00" id="pay_bill_input_amount"  disabled="disabled">
									</span>
								</td>
							</tr>
						<?php endforeach ; ?>

										<style type="text/css" onload="payBill.pbcalcTotal($(this))"></style>
					
					</tbody>
					

					<tfoot>
									<tr>
									<td><div class="shadow" style="width: 1557px;"></div></td>
									<td></td><td></td>
									<td></td><td></td>
									<td></td>
									<td>Totals:</td>
									<td>$<span id="total_paybill_bill_amount">0.00</span> </td>
									<td>$<span id="total_paybill_open_balance">0.00 </span> </td>
									<td>$<span id="total_paybill_amount_to_pay">0.00 </span> </td>
									</tr>
					</tfoot>
				</table>
			</div>
		</div>
		</div>
		<div class="dataTables_info" id="DataTables_Table_11_info" role="status" aria-live="polite">Showing 1 to 29 of 29 entries</div></div>


		<footer class="last-child" style="z-index: 1;">
					<p>
						<button type="submit" class="last-child">Pay Bills</button>
						<button type="button">Cancel</button>
					</p>
		</footer>

              
				</form>
                </div>  <!-- modal-content -->  
            </div> <!-- id="root" -->                                                
        </div> <!-- modal-dialog --> 
</div> <!-- modal --> 



<style>


.rec_row:hover{
	cursor: pointer;
}

</style>
   

<script>



</script>

