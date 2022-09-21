<div class="modal fade applyRefund-modal <?php echo $edit ?>" id="applyRefundModal" tabindex="-1" role="dialog"  doc-type="transactions" type="applyRefund" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style=" width:100%;  padding: 30px;">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
				  <form action="<?php echo $target; ?>" method="post" class=" ve" data-title="applyRefund-entry" type="10">
				  <?php if(isset($header) && isset($header->id)){
                                    echo '<input type="hidden" name="header[id]" value="' . $header->id . '"/>';
                        } ?>
			<div>
				<header class="modal-h">
					<h2>Apply/refund Security/LMR</h2>
					<nav class = "window-options">
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style=";padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
					<nav>
						<ul>
							<li><a href="#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
							<li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
                            <li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li class="get_send_email_form"><a href="./"><i class="icon-envelope-outline" aria-hidden="true"></i> <span>Envelope</span></a></li>
							<li><a href="./"><i class="icon-brain" aria-hidden="true"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents" aria-hidden="true"></i> <span>Copy</span></a></li>
							<li class="getDocuments" ><a href="#" class="uploadDocument"><i class="icon-paperclip" aria-hidden="true"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print" aria-hidden="true"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>
                <section class="d" style="z-index: 16;">
					<div class="double h">
						<div>
							<p id="tenantTest" class="select">
                            <label for="profile_id">Name</label>
								<select class="editable-select quick-add set-up" id="profile_id" name="leaseInfo[profile_id]" onchange="getApplyRefundLeases($(this).closest('p').find('input[type=hidden]').val(), $(this).closest('.modal'))">
								<option value="-1">Select Tenant</option>
                                <?php foreach($tenants as $tenant): 
									echo '<option value="' . $tenant->id . '" ' . (isset($header) && $header->profile_id == $tenant->id ? 'selected' : '') . '>' . $tenant->tenant . '</option>';
								  endforeach; ?>
                                </select>								
							</p>
							<p>
							<label for="transaction_date">Date</label>
								<input data-toggle="datepicker" id="transaction_date" name="header[transaction_date]" value="<?php echo $header->date?>" autocomplete="off">
							</p>
							<div class="double a">
								<p>
									<label for="sdApplyAmount">Apply SD: <span class="prefix">$</span></label>
									<input type="number" id="sdApplyAmount" class="sdValidate" name="sdApplyAmount" value="<?php echo $header->sdApplyAmount ? $header->sdApplyAmount : ''?>">
								</p>
								<p>
									<label for="sdRefundAmount">Refund SD: <span class="prefix">$</span></label>
									<input type="number" id="sdRefundAmount" class="sdValidate" name="sdRefundAmount" value="">
								</p>
							</div>
							<div class="double a">
								<p>
									<label for="lmrApplyAmount">Apply LMR: <span class="prefix">$</span></label>
									<input type="number" id="lmrApplyAmount" class="lmrValidate" name="lmrApplyAmount" value="<?php echo $header->lmrApplyAmount ? $header->lmrApplyAmount : ''?>">
								</p>
								<p>
									<label for="lmrRefundAmount">Refund LMR: <span class="prefix">$</span></label>
									<input type="number" id="lmrRefundAmount" class="lmrValidate" name="lmrRefundAmount" value="">
								</p>
							</div>
							<p id="checkingAccount" class="select">
							<!-- <label for="checkingAccount checking_account">Checking Account</label> -->
							<!-- <span  id="checkingAccount"></span> -->
								<!-- <select class="editable-select quick-add set-up" name="checkingAccount"> -->
								<!-- <option value="-1" selected >Select Account</option>
                                < ?php foreach($banks as $bank): 
									echo '<option value="' . $bank->id . '" ' . (isset($account_id) && $account_id == $bank->id ? 'selected' : '') . '>' . $bank->name . '</option>';
								  endforeach; ?> -->
                                <!-- </select>	 -->
							</p>
						</div>
						<div>
							<ul class="list-b text-right overlay-a">
							<li><span> SD:  </span>$<span id="sdTotal"></span></li>
							<li><span>LMR: </span>$<span id="lmrTotal"></span></li>
							<li><span>Account Balance: </span>$<span id="arBalance"></span></li>
							</ul>
						</div>
					</div>
                    <p id="profileLease" class="select">
                                </p>
								<span id="formNames"></span>
                    <p>
						<label for="blr">Memo:</label>
						<input type="text" id="memo" name="header[memo]" value="<?php echo $header->memo ? $header->memo : ''?>">
					</p>
				</section>
                <div id="DataTables_Table_12_wrapper" class="dataTables_wrapper has-table-c c text-center">
                    <div class="dataTables_scroll"><div class="dataTables_scrollHead" style="overflow: hidden; position: relative; border: 0px; width: 100%;">
                        <div class="dataTables_scrollHeadInner" style="box-sizing: content-box; padding-right: 5px;">
                            <table class="table-c c text-center dataTable" style="z-index: 15; margin-left: 0px;" role="grid">
                                <thead  id="applyRefundHeader">
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="dataTables_scrollBody" style="position: relative; overflow: auto; max-height: 371px; width: 100%; height: calc(100vh - 585px);">
                        <table class="table-c c text-center dataTable" style="z-index: 15; width: 100%;" id="DataTables_Table_12" role="grid" 
                        aria-describedby="DataTables_Table_12_info">
                    <thead>
					</thead>
					
					<tbody id="applyRefundTransactions">			
                        </tbody>
						<style type="text/css" onload="getApplyRefundHeader($(this).closest('#applyRefundModal').find('#applyRefundHeader') )"></style>
						<!-- <style type="text/css" onload="applyRefundTransactions($(this).closest('#applyRefundModal').find('#applyRefundTransactions') )"></style> -->

				</table></div><div><div class="dataTables_scrollFootInner" ><table class="table-c c text-center dataTable"role="grid"><tfoot>
						<tr>        <td style="text-align: right; padding-right: 0; overflow: visible">To be printed</td>
                                        <td class="check-a a">
											<label for="printCheck" class="checkbox ">
												<input type="hidden" name="refund[to_print]" value="0"/><input type="checkbox" value="1" id="printCheck" name="refund[to_print]" class="hidden" aria-hidden="true">
												<div style="margin-left: 0;" class="input"></div>
											</label>
                                        </td>
										<td><span class="text-left">Unapplied: </span><span id="unappliedAmount">0.00</span></td><td></td><td></td><td class="text-right">Total:</td><td><span id="applyRefund_total"></span></td><td><span id="totalAmount"><span class="text-left"></span>0.00</span></td></tr>
					</tfoot></table></div></div></div><div class="dataTables_info" id="DataTables_Table_12_info" role="status" aria-live="polite">Showing 1 to 13 of 13 entries</div></div>

                    <footer class="last-child">
                        <ul class="list-btn">
                            <li><button type="submit" after="mnew">Save &amp; New</button></li>
                            <li><button type="submit" after="mclose">Save &amp; Close</button></li>
                            <li><button type="submit" after="duplicate">Duplicate</button></li>
                            <li><button type="button">Cancel</button></li>                           
                        </ul>
						<?= $header->user || $header->modified ?
						"<ul>
							<li>Last Modified $header->modified</li>
							<li>Last Modified by $header->user</li>
						</ul>" : ''; ?>
                    </footer>
                </form>  
				<style type="text/css" onload="applyRefundInfo($(this).closest('.modal'))"></style>
				<!-- <style type="text/css" onload="applyRefundClick($(this).closest('.modal'))"></style>            -->
            </div>
        </div>
    </div>
</div>

<script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/custom2.js"></script>
<script>
		//gets transactions
		// var transactions = <?php echo $jTransactions ?>;
		var first = 1;
		var leases= <?php echo $leases ? json_encode($leases) : '0'?>;
		var tenants= <?php echo $tenants ? json_encode($tenants) : '0'?>;
		var lease_id = <?php echo $lease_id ? json_encode($lease_id) : '0'?>;
		var banks = <?php echo $banks ? json_encode($banks) : '0'?>;
		var header_id = <?php echo $header ? json_encode($header->id) : '0'?>;
		var headerprofile_id = <?php echo $header ? json_encode($header->profile_id) : '0'?>;
		var	profile_id = headerprofile_id > 0 ? headerprofile_id : tenants[0].id;
			console.log('here');
			console.log(headerprofile_id);
		//displays header
		function getApplyRefundHeader(body){
					var newRow = `<tr>
								<th style="width: 12%; text-align:center"></th>
								<th style="width: 12%; text-align:center">Description</th>
								<th style="width: 12%; text-align:center">date</th>
								<th style="width: 12%; text-align:center">Due date</th>
								<th style="width: 12%; text-align:center">Amount</th>
								<th style="width: 12%; text-align:center">Open Balance</th>
								<th style="width: 12%; text-align:center">Payment Amount</th>
							</tr>`;

					body.append(newRow);
		}
		//displays transactions
		function applyRefundTransactions(transactions, arBalance, sdBalance, lmrBalance, bank_id, body){
			arBalance = arBalance > 0 ? arBalance : '0.00';
			sdBalance = sdBalance > 0 ? sdBalance : '0.00';
			lmrBalance = lmrBalance > 0 ? lmrBalance : '0.00';
			console.log('bank id' + bank_id);
			var openBalance = 0;
			body.empty();
		var newRow = "";
		if(transactions){
				for (var i = 0; i < transactions.length; i++) {
					 newRow += `<tr role="row" class="odd">
							<td onClick="formsJs.greenCheckTd($(this), 'applyRefund');" class="greenCheckTd`;
							if(Number(transactions[i].received_payment) > 0){newRow += ` clickThis`}
					newRow +=`"><i class="icon-check" aria-hidden="true" id="greenCheck" style="display :none"></i><div class="shadow" style="width: 1189px;"></div></td>
							<input type="hidden" name="" value="`+ transactions[i].id +`" id="id">
							<td style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px">`+ transactions[i].description +`</td>
							<td>`+ transactions[i].date +`</td>
							<td>`+ transactions[i].due_date +`</td>
							<td>$<span>`+ Number(transactions[i].amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') +`</span></td>
							<td>$<span class="openBalance">`+ Number(transactions[i].open_balance).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') +`</span></td>
							<td>
								<span class="input-amount">
									<label for="tcaa"></label>
									<input type="number" name="" value="`+ Number(transactions[i].received_payment).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') +`" id="applyRefund_input_amount2" class="calculateTotal allInputAmounts">
								</span>
							</td>
						</tr>`;
						openBalance = openBalance + Number(transactions[i].open_balance);
						console.log(openBalance);
				}
					body.append(newRow);
					//formsJs.triggerCalculate($(body).find('.calculateTotal'));
					//applyRefundInfo($(body).closest('.modal'))
					//applyRefundClick($(body).closest('.modal'))
		}
				if(first != 1){
						$(body).closest('.modal').find('#sdApplyAmount').val('');
						$(body).closest('.modal').find('#sdRefundAmount').val('');
						$(body).closest('.modal').find('#lmrApplyAmount').val('');
						$(body).closest('.modal').find('#lmrRefundAmount').val('');
				}
				first++;
					$(body).closest('.modal').find('#applyRefund_total').empty();
					$(body).closest('.modal').find('#applyRefund_total').append( openBalance.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
					$(body).closest('.modal').find('#sdTotal').empty();
					$(body).closest('.modal').find('#sdTotal').append( Number(sdBalance).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
					$(body).closest('.modal').find('#lmrTotal').empty();
					$(body).closest('.modal').find('#lmrTotal').append( Number(lmrBalance).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
					$(body).closest('.modal').find('#arBalance').empty();
					$(body).closest('.modal').find('#arBalance').append( Number(arBalance).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
					formsJs.triggerCalculate($(body).find('.calculateTotal'));
					formsJs.updateUnapplied($(body).closest('.modal'), 'applyRefund');

					var allBanks = `<label for="checkingAccount checking_account">Checking Account</label>
									<select class="editable-select quick-add set-up" name="checkingAccount">`;
					for (var j = 0; j < banks.length; j++) {
						allBanks += `<option value='` + banks[j].id + `'`;
							if(bank_id == banks[j].id){ allBanks += ' selected'; }
							allBanks +=  `>` + banks[j].name + `</option>`;                             
					}   
					allBanks += `<select>`;                                                                                           
					$(body).closest('.modal').find('#checkingAccount').empty().append(allBanks);
					$(body).closest('.modal').find('#checkingAccount').find('.editable-select').editableSelect();
	}
		//gets transactions based on profile and lease
		function getApplyRefundTransactions(profile, lease, body, id = null){


		$.ajax({
			type: 'GET',
			url: JS.baseUrl+'Transactions/getApplyRefundTransactions/',
			data: { profile: profile, lease: lease, id : id },
			dataType: 'json',
			success: function(response) {
				console.log('response');
				console.log(response);
				console.log(response.transactions);
				console.log(response.arBalance);
				applyRefundTransactions(response.transactions, response.arBalance,response.sdBalance, response.lmrBalance,response.default_bank, body)
			}
		});
		}

		//$(document).ready(function () {
			function applyRefundInfo(modal){
			 	getApplyRefundLeases(profile_id, modal);
			}
        //});

	//gets leases based on profile
	function getApplyRefundLeases(profileId, modal){
				var leaseSpot = 0;
				var lease;
				var newRow = '';
				newRow = ` <label for="lease_id">Lease</label>
											<select class="form-control editable-select" id="lease_id" onchange="applyRefund.setNames($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal')); getApplyRefundTransactions(` + profileId +`, $(this).closest('.modal').find('#formNames').find('#leaseIdOnchange').val(), $(this).closest('.modal').find('#applyRefundTransactions'));">`;
														for (var j = 0; j < leases.length; j++) {
															if(profileId == leases[j].profile_id){
																newRow += `<option value='` + leases[j].id + `'`;
																if(lease_id > 0){                                                              
																	if(lease_id == leases[j].id){ newRow += ' selected'; applyRefund.setNames(leases[j].id, modal); getApplyRefundTransactions(leases[j].profile_id, leases[j].id, $(modal).find('#applyRefundTransactions'), header_id)}
																}else{
																	if(leaseSpot == 0){
																		newRow += ' selected'; leaseSpot++; applyRefund.setNames(leases[j].id, modal); getApplyRefundTransactions(leases[j].profile_id, leases[j].id, $(modal).find('#applyRefundTransactions'))
																		}
																	}
																newRow +=  `><span style="color: red;">` + leases[j].property + `</span><span style="color: blue;"> ` + leases[j].unit + `</span><span style="color: green;"> ` + leases[j].name + `</span></option>`;
															}
														}
													
									newRow += `</select>`;
								$(modal).find('#profileLease').empty();            
								$(modal).find('#profileLease').append(newRow);
								$(modal).find('#profileLease').find('.editable-select').editableSelect();
								//if(first == 1){getApplyRefundTransactions(profile, lease, body, headerprofile_id)}
								//$(modal).find('#profileLease').find('#lease_id').change();
								lease_id = 0;
			}


  </script>