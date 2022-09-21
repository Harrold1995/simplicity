<div class="modal fade reconciliation-modal <?php echo $edit ?>" id="reconciliationModal" tabindex="-1" role="dialog" main-id=<?= isset($reconciliation) && isset($reconciliation->r_id) ? $reconciliation->r_id : '-1' ?> type="reconcilliation" ref-id="" aria-hidden="true" ">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
            <div id="root">

                <div class="modal-content text-primary popup-a  shown theme-c" >
				<form action="<?php echo $target; ?>" method="post" type="reconciliations">
                    <article class="module-rec">
					<header class="modal-h" style="padding-bottem:20px">
						<form>
						<h1>Reconciliation</h1>
				<nav>
                  <ul>
                      <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                      <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                      <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                  </ul>
              </nav>

							<p>
								<label for="account_id">Account</label>
								<!-- <input type="text" id="account_id"  required="" value="< ?= $account->name?>" >
								<input type="hidden"  name="reconciliation[account_id]" required="" value="< ?= $account->id?>"> -->
								<select id="account_id" class="editable-select" name="reconciliation[account_id]" onchange="reconciliation2.getAccountInfo($(this).closest('p').find('input[type=hidden]').val(), $(this).closest('.modal'), 'first');">
									<?php
											foreach ($accounts as $singleAccount) {
												echo '<option value="' . $singleAccount->id .'" '. (isset($account) && $account->id == $singleAccount->id  ? 'selected' : '') .'>' . $singleAccount->name .'</option>';
											} ?>
									</select>
							</p>
							<p class="is-date" >
								<label for="statement_end_date">Statement end date</label>
								<input data-toggle="datepicker" id="statement_end_date" value="<?= isset($reconciliation) && isset($reconciliation->statement_end_date) ? $reconciliation->statement_end_date :  date('Y-m-d') ?>" name="reconciliation[statement_end_date]" required="">
							</p>
							<ul class="check-a" >
								<li><label for="mrc" class="radio"><input type="radio" id="mrc" name="mrc" class="hidden" aria-hidden="true"><div class="input"></div> Manual</label></li>
								<li><label for="mrd" class="radio"><input type="radio" id="mrd" name="mrc" class="hidden" aria-hidden="true"><div class="input"></div> Import</label></li>
							</ul>
							<?php if(isset($reconciliation) && ($reconciliation->statement_attachment != "")){ ?>
							<div>
								<a href="<?php echo base_url() . 'uploads/documents/'. $reconciliation->statement_attachment  ?>" target="_blank"><?=isset($reconciliation->statement_attachment) ? $reconciliation->statement_attachment : '';?>
								</a>
								<p class="input-file">
									<label for="p-image"><input type="file" name="statement_attachment"  id="p-image" targetimg="#statement_attachment-lease"> <span>Edit</span></label>
								</p>
								</div>
							<?php }else{ ?>
								<!-- <p><button type="submit">Attach Statement</button></p> -->
								<p class="input-file">
									<label for="p-image"><input type="file" name="statement_attachment"  id="p-image" targetimg="#statement_attachment-lease"> <span>Attach Statement</span></label>
								</p>
							<?php }?>
					</form>
					</header>
                        <input type="hidden" id="closeRec" name="closed" value="false">
                        <div id="recId">
                            <?php if($reconciliation->r_id != null): ?>

                                <input type="hidden"  name="reconciliation[id]"  value="<?= $reconciliation->r_id ?>">
                            <?php endif; ?>
                        </div>

						<ul class="list-rec" >
						 
							<li><label for="rec_begin_bal">Beginning Balance</label>
								<input type="text" id="rec_begin_bal" name="reconciliation[beginning_bal]" value="<?= $reconciliation->beginning_bal?>" readonly  >
						   </li>
							<li> <p id="cleared_payments">0</p> <span> Cleared Payments  </span> <p id="payment_total">0</p> </li>
							<li> <p id="cleared_deposits">0</p> <span> Cleared Deposits  </span> <p id="debit_total">0</p> </li>
							<li>
								<label for="ending_balance">Ending Balance</label>
								<input type="text" id="ending_balance" name="reconciliation[ending_bal]" value="<?= $reconciliation->statement_bal?>" required="">
							</li>
							<li>
							    <?php if (!isset($account->property) or $account->property == 0) {echo '<span class ="withProp" style = "display:none">'; } else {echo '<span class ="withProp">'; } ?>
								<?php if (isset($account->property) && $account->property !=0) {echo '<input type ="hidden" name = "property" value="'.$account->property.'">'; } ?>
									<label for="interest_earned">Interest Earned</label>
									<input type="text" id="interest_earned" name="reconciliation[interest_earned]" value="<?= $reconciliation->interest_earned ?>" required="" >
								</span>
								<?php if (!isset($account->property) or $account->property == 0) {echo '<span class ="withProp" style = "display:none">'; } else {echo '<span class ="withProp">'; } ?>
									<label for="service_charge">Service Charge</label>
									<input type="text" id="service_charge" name="reconciliation[service_charge]" value="<?= $reconciliation->service_charge ?>" required="">
								</span>
								<?php if (isset($account->property) && $account->property !=0) {echo '<span class ="withNoProp" hidden>'; } else {echo '<span class ="withNoProp">'; } ?>
									<label for="property">choose a property to be associated with this bank in order to record Service Charge & interest</label>
									<select stype="property" class="fastEditableSelect" key="properties.name" modal="property" id="property_select1" name="property_id"></select>
									
								</span>
								<a id="" class ="withNoProp property_select">Ok</a>
								<?php if (isset($account->property) && $account->property !=0) {echo '<input name="property_id" hidden value = "'.$account->property.'">'; } ?>
							</li>
							<li><span>Difference</span><p id="rec_diff">00.00</p></li>
						</ul>
						<div class="double a t_input_wrapper">
							<div>

								<header>
									<h2>Payments/charges</h2>
									<p class="input-search">
										<label for="mrh">Search</label>
										<input type="text" id="mrh" name="mrh" placeholder="search">
										<a href="./" class="btn">Search</a>
									</p>
								</header>
			<div class="table-wrapper tabindex= table-d-wrapper last-child has-data-table" >
				<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper no-footer">
					<div class="dataTables_scroll"> <!-- closed -->
						<div class="dataTables_scrollHead" ><!-- closed-->
							<div class="dataTables_scrollHeadInner" > <!-- closed-->
								<table class="table-d">
									<thead>
										<tr>
										    <th style="margin:0;padding:0" ><a href="#/" id="rec_credit_select_all" value="select">Select All</a></th>
											<th>Date</th>
											<th>Type</th>
											<th>Num</th>
											<th>Name</th>
											<th>Amount</th>
										</tr>
									</thead>
								</table>
							</div> <!-- class="dataTables_scrollHeadInner"  -->
						</div> <!-- class="dataTables_scrollHead"  -->
								<div class="dataTables_scrollBody" style="position: relative; overflow: auto; max-height: 420.828px; width: 100%;"><!-- closed-->
								    <table class="table-d dataTable no-footer clickable2" tabindex="-1" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info" >
									<tbody id="creditBody">

										 <?php 
										 $totalNeg =0;
										 $negCount =0;
										 foreach($credits as $credit ): 
										 if (isset($credit->rec_id)){
											$negCount ++;
											 $totalNeg = $totalNeg + $credit->amount;
											}
										 ?>
											<tr id="credit_row" v-on:click="updateCreditTotal" class="rec_row credit_rec_row" data-id="<?php echo $credit->th_id ?>" data-type="<?php echo $credit->type_id ?>">
												<td ><input  class="rec_input" type="hidden"  name="transactions[<?php echo $credit->id ?>]" value="<?= isset($credit->rec_id)  ? '1' : '0' ?>"><i id="rec-icon-check" class="icon-check" style=" <?= !isset($credit->rec_id) ? ' visibility: hidden;' : ''?> "></i> <span class="hidden">Yes</span></td>
												<td><?php echo $credit->date ?></td>
												<td><?php echo $credit->type ?></td>
												<td><?php echo $credit->num ?></td>
												<td><?php echo $credit->vendor ?></td>
												<td class="credit_amount"><?php echo $credit->amount ?></td>
											</tr>

											<?php endforeach; ?>
										</tbody>
									</table>
								</div> <!-- class=""dataTables_scrollBody"  -->
					</div>	<!--  class="dataTables_scroll" -->
				</div>
			</div>
		  </div>

							<div class="t_input_wrapper">

								<header>
									<h2>Deposits/debits</h2>
									<p class="input-search">
										<label for="mrh">Search</label>
										<input type="text" id="mrh" name="mrh"  >
										<a href="./" class="btn">Search</a>
									</p>

								</header>
			<div class="table-wrapper tabindex= table-d-wrapper last-child has-data-table" >
				<div id="DataTables_Table_0_wrapper" class="dataTables_wrapper no-footer">
					<div class="dataTables_scroll"> <!-- closed -->
						<div class="dataTables_scrollHead" ><!-- closed-->
							<div class="dataTables_scrollHeadInner" > <!-- closed-->
								<table class="table-d">
									<thead>
										<tr>

										    <th  style="margin:0;padding:0"><a href="#/" id="rec_debit_select_all" value="select">Select All</a></th>
											<th>Date</th>
											<th>Type</th>
											<th>Num</th>
											<th>Name</th>
											<th>Amount</th>
										</tr>
									</thead>
								</table>
							</div> <!-- class="dataTables_scrollHeadInner"  -->
						</div> <!-- class="dataTables_scrollHead"  -->
								<div class="dataTables_scrollBody" style="position: relative; overflow: auto; max-height: 420.828px; width: 100%;"><!-- closed-->
								    <table class="table-d dataTable no-footer clickable2" tabindex="-1" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info" >
										<tbody id="debitBody">
										<?php 
											$totalPos =0;
											$posCount =0;
										    foreach($debits as $debit ): 
											if (isset($debit->rec_id)){
												$posCount ++;
												$totalPos = $totalPos + $debit->amount;
												}?>
											<tr id="debit_row" class=" rec_row debit_rec_row" data-id="<?php echo $debit->th_id ?>" data-type="<?php echo $debit->type_id ?>">
												<td><input  class="rec_input " type="hidden"  name="transactions[<?php echo  $debit->id ?>]" value="<?= isset($debit->rec_id)  ? '1' : '0' ?>"> <i id="rec-icon-check" class="icon-check" style="<?= !isset($debit->rec_id) ? ' visibility: hidden; ' : ''?>"></i> <span class="hidden">Yes</span></td>
												<td><?php echo $debit->date ?></td>
												<td><?php echo $debit->type ?></td>
												<td><?php echo $debit->num ?></td>
												<td><?php echo $debit->vendor ?></td>
												<td class="debit_amount"><?php echo $debit->amount ?></td>
											</tr>

											<?php endforeach; ?>
										</tbody>
									</table>
								</div> <!-- class=""dataTables_scrollBody"  -->
					</div>	<!--  class="dataTables_scroll" -->
				</div>
			</div>
		  </div>
		</div>
					  <style type="text/css" onload="recClick($(this).closest('.modal'))"></style>
		              <style type="text/css" onload="updateAllTotals($(this))"></style>

						<p class="submit"><button type="submit" id="rec_submit">Save for later</button></p>
						<p id="refreshRec" data-type = 'auto'>refresh</p>

				</article>
				</form>


                </div>  <!-- modal-content -->
            </div> <!-- id="root" -->
        </div> <!-- modal-dialog -->
</div> <!-- modal -->


<script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/custom2.js"></script>

<style>

.rec_row:hover{
	cursor: pointer;
}

</style>

<script>

				function recClick(modal){
					var neg = <?php echo $totalNeg ?>;
					var pos = <?php echo $totalPos ?>;
					var negCount = <?php echo $negCount ?>;
					var posCount = <?php echo $posCount ?>;
					var begBal = <?php echo $reconciliation->beginning_bal ?>;
					var endBal = <?php echo $reconciliation->statement_bal ?>;
					var interest = <?php echo $reconciliation->interest_earned ?>;
					var sc = <?php echo $reconciliation->service_charge ?>;
					var diff = endBal-(begBal+pos-neg-sc+interest);
					console.log("neg:", neg, " pos:", pos);
					$(modal).closest('#reconciliationModal').find('#debit_total').text(pos.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
					$(modal).closest('#reconciliationModal').find('#payment_total').text(neg.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
					$(modal).closest('#reconciliationModal').find('#cleared_payments').text(negCount);
					$(modal).closest('#reconciliationModal').find('#cleared_deposits').text(posCount);
					$(modal).closest('#reconciliationModal').find('#rec_diff').text(diff);
					 //$(modal).find('.clickThis').trigger('click');
					//  $(modal).closest('.modal').find('#undepositedClick').find('.depositForm_amount').trigger('keyup');
				}


				
   	     $('.property_select').click(function(){
			if ($(this).closest('.modal').find('#property_select1').val().length >0){
				$(this).closest('.modal').find('.withNoProp').hide();
				$(this).closest('.modal').find('.withProp').show();
			} else {
                alert('you need to select a property first');
			}
		});

		$('input[type="file"]').change(function(e){
            var fileName = e.target.files[0].name;
			$(this).closest('label').find('span').html("Edit");
			$(this).closest('div').find("a").remove();
			$(this).closest('p').find('.display_name').remove();
			$(this).closest('p').prepend("<span class='display_name'>"+ fileName+"</span>");
		});




</script>