<div class="modal fade property-modal <?php echo $edit ?>" id="receive_paymentModel" doc-type="transactions" tabindex="-1" role="dialog" main-id=<?= isset($deposit) && isset($deposit->id) ? $deposit->id : '-1' ?> type="receive_payment" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="5" autocomplete="off">
                  
				<header class="modal-h" style="z-index: 17;">
					<h2>Receive Payment</h2>
					<nav>
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
							<li class="getDocuments"><a href="#" class="uploadDocument"><i class="icon-paperclip" aria-hidden="true"></i> <span>Attach</span></a></li>
							<li><a class="print" href="#" onclick ='printReceipt(event);'><i class="icon-print" aria-hidden="true"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>
				<section class="d" style="z-index: 11;">
				            <div class="<?php echo $hasRecId ?>">
                                <?php echo $hasRecIdHtml ?>
                            </div>
					<div class="<?php echo $hasDepositId ?>">
					    <?php echo $hasDepositIdHtml ?>
					</div>
					<div class="double h">
						<div> 
							<p>
								<?php echo (isset($header->id)) ? '<input type="hidden" id="trans_id" name="header[id]" value="'. $header->id .'" >' : "" ;   ?>
								<?php echo (isset($header->transaction_id_a)) ? '<input type="hidden" id="transaction_id_a" name="transaction_id_a" value="'. $header->transaction_id_a .'" >' : "" ;   ?>
								<?php echo (isset($header->cp_tid)) ? '<input type="hidden" name="customer_payments[trans_id]" value="'. $header->cp_tid .'" >' : "" ;   ?>
								<input type="hidden" id="deposit_bank_id" name="" value="<?php echo (isset($header->deposit_bank_id)) ?  $header->deposit_bank_id  : $header->deposit_to ;   ?> ">
								<input type="hidden" id="deposit_bank_name" name="" value="<?php echo (isset($header->deposit_bank_name)) ?  $header->deposit_bank_name  : $header->deposit_to_name ;   ?> ">
								<input type="hidden" id="deposit_date" name="" value="<?php echo (isset($header->deposit_date)) ?  $header->deposit_date  : '' ;   ?> ">
								<label id="thisProfile" for="profile">Payment from:</label>
								<span stype="profile" class='select'>
									
									<input  hidden-name = 'profile' name="profile_id" filter_key ='profile_type_id' filter_value = '3' onchange = "getReceivePaymentsTransactions($(this).attr('sel-value'), $(this).closest('span').find('#hidden_lease_id').val(), $(this).closest('.modal').find('#received_payment_table'));" class="fastEditableSelect" stype="profile" value="<?php echo (isset($header->profile_id)) ?  $header->profile_id  : ''; echo (isset($lease_id)) ?  '-'.$lease_id  : '';  ?>"  key="profiles.first_name" modal="tenant"  default="<?php echo (isset($header->profile_id)) ?  $header->profile_id  : ''; echo (isset($lease_id)) ?  '-'.$lease_id  : '';  ?>"></input>
								</span>
								

								<!-- 
									old lease input. removed and combined with profile
									<span class="select" >
								<select id="profile_id" name="profile_id" onchange="getRpLeases($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal'))"
								class="editable-select">
								    <option value="0"></option>
									< ?php $tenantSelected = $header->profile_id ? $header->profile_id : $tenants[0]->id; ?>
									< ?php  foreach($tenants as $tenant): ?>
									   <option  value="< ?= $tenant->id ?>"  < ?php echo ( $tenantSelected == $tenant->id) ? ' selected="selected" ' : '' ; ?>   >< ?= $tenant->tenant ?></option>
									< ?php  endforeach; ?>
								</select>
                                </span> -->
							</p>
							<p>
								<label for="payment_method_id">Pay method:</label>
								<span class="select">
								<select class="editable-select quick-add" id="payment_method_id" name="customer_payments[payment_method]" type="setting" key="payment_methods">
                                    <?php
                                    foreach ($payment_methods as $k => $payment_method) {
                                        echo '<option value="' . $k . '" ' . (isset($header) && $header->payment_method == $k ? 'selected' : '') . '>' . $payment_method . '</option>';
                                    } ?>
                                </select>
                                <!-- <select  name="customer_payments[payment_method]" class="editable-select">
                                     
									  
									   
									   <option value="ACH" < ?php echo ( $header->payment_method == "ACH" ) ? ' selected="selected" ' : '' ; ?>> ACH</option>
									   <option value="Cash" < ?php echo ( $header->payment_method == "Cash" ) ? ' selected="selected" ' : '' ; ?>> Cash</option>
									   <option value="Check" < ?php echo ( $header->payment_method == "Check" ) ? ' selected="selected" ' : '' ; ?>> Check</option>
									   <option value="Customer Payment" < ?php echo ( $header->payment_method == "Customer Payment" ) ? ' selected="selected" ' : '' ; ?>> Customer Payment</option>
									   <option value="Direct Deposit" < ?php echo ( $header->payment_method == "Direct Deposit" ) ? ' selected="selected" ' : '' ; ?>> Direct Deposit</option>
									   <option value="Money Order" < ?php echo ( $header->payment_method == "Money Order" ) ? ' selected="selected" ' : '' ; ?>> Money Order</option>
                                       <option value="Prepaid Rent" < ?php echo ( $header->payment_method == "Prepaid Rent" ) ? ' selected="selected" ' : '' ; ?>> Prepaid Rent</option>
									   <option value="Security" < ?php echo ( $header->payment_method == "Security" ) ? ' selected="selected" ' : '' ; ?>> Security</option>
									   <option value="T" < ?php echo ( $header->payment_method == "T" ) ? ' selected="selected" ' : '' ; ?>> T</option>
									
								</select> -->
                                </span>
							</p>
							<p>
								<label >Amount: <span class="prefix">$</span></label>
								<input id="received_amount" type="text"  class = "decimal" name="amount" value="<?php echo ( isset($header->credit)) ?  $header->credit  : '' ; ?>">
							</p>
							<p>
								<label for="memo">Memo:</label>
								<input type="text" id="memo" name="header[memo]" value="<?php echo ( isset($header->memo)) ?  $header->memo  : '' ; ?>">
							</p>
								<?php if($header->nsf){?>
									<p style="color: red">Bounced on <?= $header->deposit_date; ?></p>
								<?php }
								
								if(isset($header->id)){?>
									<button id="nsf">Nsf</button>
								<?php }?>
							
							<!-- <ul class="check-a a">
								<li><label for="bouncedCheck" class="checkbox"><input type="hidden" name="" value="0" /><input type="checkbox" value="1" id="bouncedCheck"  name=""  class="hidden" aria-hidden="true"><div class="input bouncedCheck"></div>Check bounced</label></li>
							</ul> -->
						</div>
						<div>
						<p  id="rpProperties2">
							</p>
							<span id="formNames"></span>
							<p>
								<label for="reference">Ref #:</label>
								<input type="text" id="reference" name="header[transaction_ref]" value="<?php echo ( isset($header->transaction_ref)) ?  $header->transaction_ref  : '' ; ?>">
							</p>
							<p>
								<label for="received_payment_date">Date:</label>
								<input data-toggle="datepicker" name="header[transaction_date]" id="received_payment_date"  value="<?php echo ( isset($header->date)) ?  $header->date  : date() ; ?>">
							</p>
							<p>
								<label for="payment_deposit_on_date">Deposit On:</label>
								<input data-toggle="datepicker" name="customer_payments[deposit_on]" id="payment_deposit_on_date" value="<?php echo ( isset($header->deposit_on)) ?  $header->deposit_on  : '' ; ?>" >
							</p>
							<p>
							<?php if($header->deposit_bank_name){?>
									<p style="color: ##97cf97; font-weight: 500"> <a class = "clickable" data-type = 8 data-id = <?= $header->deposit_id2; ?>> Deposited to <?= $header->deposit_bank_name; ?> on <?= $header->deposit_date; ?></a></p><input type="hidden" value=  <?= $this->site->settings->undeposited_funds; ?>  name="account_id"><input type="hidden" value= <?= $header->deposit_bank_id; ?> name="account_id2">
								<?php }else{?>
									<label for="account_id">Deposit To:</label>

								<span stype="account" class='select' id='account_id'>
									
									<input  hidden-name = 'account_id' name="account_id" class="fastEditableSelect" filter_key ='depositable' filter_value = '1' stype="account" value="<?php echo (isset($header->deposit_to)) ?  $header->deposit_to  : ''; ?>"  key="accounts.name" modal="account"  default="<?php echo (isset($header->deposit_to)) ?  $header->deposit_to  : ''; ?>"></input>
							    </span>
								<input type="hidden" value= <?= $header->deposit_to; ?> name="account_id2">
								<?php }?>
							</p>
							<?php echo $RecIdHtml ?>
						</div>
					</div>
					
				</section>
				<div id="DataTables_Table_8_wrapper" class="dataTables_wrapper has-table-c mobile-hide c text-center">
					<div class="dataTables_scroll">
						<div class="dataTables_scrollHead" style="overflow: hidden; position: relative; border: 0px; width: 100%;">
							<div class="dataTables_scrollHeadInner" style="box-sizing: content-box; padding-right: 5px;">
								<table class="table-c c text-center mobile-hide dataTable" style="z-index: 24; margin-left: 0px; " role="grid">
									<thead>
										<tr role="row">
											<th class="check-a"><label for="received_payment_select_all2" class = "checkbox"><input type="checkbox" id="received_payment_select_all2" class=""><div class="input"></div></label></th>
											<th>Description</th>
											<th>Date</th>
											<th>Amount</th>
											<th>Open Balance</th>
											<th>Payment Amount</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
						<div class="dataTables_scrollBody" style="position: relative; overflow: auto; max-height: 448px; width: 100%;">
							<table class="table-c c text-center mobile-hide dataTable" style="z-index: 24; width: 100%;" id="DataTables_Table_8" role="grid" aria-describedby="DataTables_Table_8_info">

				
					
					<tbody id="received_payment_table">	
						
					</tbody>
					<style type="text/css" onload="receivedPayments.firstUpdateUnapliedAmount($(this))"></style>
					
				</table>
			</div>
			<div class="dataTables_scrollFoot" style="overflow: hidden; border: 0px; width: 100%;">
				<div class="dataTables_scrollFootInner" style="width: 100%">
					<table class="table-c c text-center mobile-hide dataTable" style="z-index: 24; margin-left: 0px; width: 100%;" role="grid">
						<tfoot>					
							<tr>
								<td rowspan="1" colspan="1" style="width: 46px;">
									<div class="shadow" style="width: 1015px;"></div>
								</td>
								<td class="overlay-k" rowspan="1" colspan="1" style="width: 289px;">
									<span class="text-left">Unapplied: <span class="text-right">$</span></span><span id="unappliedAmount"></span>
								</td>
								<td></td>
								<td class="text-right" rowspan="1" colspan="1" style="width: 113px;">Total:</td>
								<td><span ><span class="text-left">$</span > <span id="allOriginalAmount"></span> </span></td>
								<td><span><span class="text-left">$</span> <span id="totalOpenBalance"></span> </span></td>
								<td><span><span class="text-left">$</span> <span id="totalAmount"> </span> </span></td>
							</tr>
					   </tfoot>
					</table>
				</div>
			</div></div><div class="dataTables_info" id="DataTables_Table_8_info" role="status" aria-live="polite">Showing 1 to 13 of 13 entries</div></div>
				
				<footer>
					<ul class="list-btn">
						<li><button type="submit" after="mnew">Save &amp; New</button></li>
						<li><button type="submit"  after="mclose">Save &amp; Close</button></li>
						<li><button type="button">Cancel</button></li>
					</ul>
					<?= $header->user || $header->modified ? 
					"<ul>
						<li>Last Modified $header->modified</li>
						<li>Last Modified by $header->user</li>
					</ul>" : '';?>
				</footer>
			<a class="close" href="./">Close</a></div>
			</form>
			<style type="text/css" onload="receivedPaymentInfo($(this).closest('.modal'))"></style> 
			<!-- <style type="text/css" onload="receivePaymentsClick($(this).closest('.modal'))"></style> -->
			<script>
				console.log(<?php echo $header->deposit_id2; ?>);
				console.log('header');
			var first = 1;
			var leases= <?php echo $leases ? json_encode($leases) : '0'?>;
			var lease_id = <?php echo $lease_id ? json_encode($lease_id) : '0'?>; 
			var tenants= <?php echo $tenants ? json_encode($tenants) : '0'?>;
			var header_id = <?php echo $header ? json_encode($header->id) : '0'?>;
			var headerprofile_id = <?php echo $header ? json_encode($header->profile_id) : '0'?>; 
			var	profile_id = headerprofile_id > 0 ? headerprofile_id : tenants[0].id; 
			// var transactions = < ?php echo $transactions ? json_encode($transactions) : '0'?>; 			

			$( function() {
					 $( "#received_payment_date" ).datepicker();
					 $( "#payment_deposit_on_date" ).datepicker();
	
			} );
			// var hasProperties = < ?php echo $profileI ? $profileI : '0'?>;

				// var person = hasProperties ? hasProperties : < ?= $profile->id ?>;
			function receivedPaymentInfo(modal){
				//getRpLeases(profile_id, modal);
				getReceivePaymentsTransactions(profile_id, lease_id, $(modal).find('#received_payment_table'));
				console.log('profile',profile_id);
				//IMPORTANT-- The green check on the receive payment form gets triggered from applyRefund.js line 107 receivedPayments.triggerCheck(that);
			}

	//gets leases based on profile
	/* function getRpLeases(profileId, modal){
		console.log('getLease');
				var leaseSpot = 0;
				var lease;
				var newRow = '';
				newRow = ` <label for="lease_id">Lease</label>
											<span class="select">
											<select class="form-control editable-select" id="lease_id" onchange="setRpNames($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal')); getReceivePaymentsTransactions(` + profileId +`, $(this).closest('.modal').find('#formNames').find('#leaseIdOnchange').val(), $(this).closest('.modal').find('#received_payment_table'));">`;
														for (var j = 0; j < leases.length; j++) {
															if(profileId == leases[j].profile_id){
																newRow += `<option value='` + leases[j].id + `'`;
																if(lease_id > 0){
																	console.log(lease_id +"lease id");                                                              
																	if(lease_id == leases[j].id){ newRow += ' selected'; setRpNames(leases[j].id, modal);getReceivePaymentsTransactions(leases[j].profile_id, leases[j].id, $(modal).find('#received_payment_table'), header_id);}
																}else{
																	if(leaseSpot == 0){
																		newRow += ' selected'; leaseSpot++; setRpNames(leases[j].id, modal); getReceivePaymentsTransactions(leases[j].profile_id, leases[j].id, $(modal).find('#received_payment_table'));
																		}
																	}
																newRow +=  `><span style="color: red;">` + leases[j].property + `</span><span style="color: blue;"> ` + leases[j].unit + `</span><span style="color: green;"> ` + leases[j].name + `</span></option>`;
															}
														}
													
									newRow += `</select>
												</span>`;
								$(modal).find('#rpProperties2').empty();            
								$(modal).find('#rpProperties2').append(newRow);
								$(modal).find('#rpProperties2').find('.editable-select').editableSelect();
								lease_id = 0;
			}
			function setRpNames(leaseId, modal){
            for (var j = 0; j < leases.length; j++) {
                if(leaseId == leases[j].id){ 
                   var names = `<input type="hidden" id="leaseIdOnchange" name="lease_id" value="`+ leases[j].id +`">
                                <input type="hidden" name="property_id" value="`+ leases[j].property_id +`">
                                <input type="hidden" name="unit_id" value="`+ leases[j].unit_id +`">`;
                }
            }
            $(modal).find('#formNames').empty();
            $(modal).find('#formNames').append(names);
		} */

		function getReceivePaymentsTransactions(profile, lease, body, id = null){
		  id = <?= isset($header->id) ? $header->id : 0; ?>;
		  console.log("profile -"+profile);
		  console.log("lease -"+lease);

		$.ajax({  
			type: 'GET',
			url: JS.baseUrl+'Transactions/getReceivePaymentsTransactions/', 
			data: { profile: profile, lease: lease, id : id },
			dataType: 'json',
			success: function(response) {
				console.log('response');
				console.log(response);
				getRpTransactions(response.transactions, body)
			}
		});
		}
		
		function getRpTransactions(transactions, body){

			body.empty();
			var rpOpenBalance = 0;
			var rpAmount = 0;
		var transactionsList = "";
		if(transactions){
			console.log(transactions);
				for (var i = 0; i < transactions.length; i++) {
                               console.log(transactions[i].received_payment);
			 transactionsList += `<tr role="row"  id="received_payment_row" class="received_payament_row2 received_payment_row" data-type="` + transactions[i].transaction_type +`" data-id="` + transactions[i].th_id +`" data-lease="` + transactions[i].lease_id +`"  data-profile="` + transactions[i].profile_id +`">
									<td onClick="formsJs.greenCheckTd($(this), 'receivePayment');" class="greenCheckTd `;
									if(Number(transactions[i].received_payment) > 0){transactionsList += ` clickThis`}						 
				transactionsList += `">	<i class="icon-check" aria-hidden="true" id="greenCheck" style="display :none"></i><div class="shadow" style="width: 1200px;"></div>
									</td>
									<input type="hidden" id="id" name="" value="` + transactions[i].id +`">
									<td style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px">` + transactions[i].description +`</td>
									<td>` + transactions[i].date +`</td>
									<td>$<span class="paymentAmount">` + transactions[i].amount +`</span></td>
									<td>$<span class="openBalance" >`+ transactions[i].open_balance +` </span></td>
									<td id="received_payament_row_input_amount2">
										<span class="input-amount">
											<label for="tcaa">$</label>
											<input type="number" id="received_payament_input_amount2" name="" value="` + transactions[i].received_payment+`" class="calculateTotal allInputAmounts"  >
										</span>
									</td>
							</tr>`;
							rpOpenBalance = rpOpenBalance + Number(transactions[i].open_balance);
							rpAmount = rpAmount + Number(transactions[i].amount);
				}
/* 				if(first != 1){
						$(body).closest('.modal').find('#received_amount').val('');
						$(body).closest('.modal').find('#amount').val('');
						$(body).closest('.modal').find('#received_payment_date').val('');
						$(body).closest('.modal').find('#payment_deposit_on_date').val('');
						$(body).closest('.modal').find('#reference').val('');
						$(body).closest('.modal').find('#reference').html('');
						$(body).closest('.modal').find('#memo').val('');
				} */
				first++;
				//$(body).find('#received_payment_table').empty();
				$(body).append(transactionsList);
				formsJs.triggerCalculate($(body).find('.calculateTotal'));
				$(body).closest('.modal').find('#totalOpenBalance').empty();
				$(body).closest('.modal').find('#totalOpenBalance').append('$' + rpOpenBalance.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
				$(body).closest('.modal').find('#allOriginalAmount').empty();
				$(body).closest('.modal').find('#allOriginalAmount').append('$' + rpAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
		}
	}

	function printReceipt(e){
		target = e.target;
		form = $(target).closest('.modal');
		ref = $(form).find("input[name='header[transaction_ref]']").val();
		date = $(form).find("input[name='header[transaction_date]']").val();
		amount = $(form).find("input[name='amount']").val();
		profile = $(form).find("input[name='profile[lease_id]']").attr('text');
		property = $(form).find("input[name='profile[prop_id]']").attr('text');
		unit = $(form).find("input[name='profile[unit_id]']").attr('text');
		pay_method = $(form).find("input[name='customer_payments[payment_method]']").attr('text');
		account = $(form).find("input[name='profile[lease_id]']").val();
		logo = sessionStorage.getItem('company_logo'); 

		var newPrintDiv = "<div style ='margin-top: 20px;'>";

		newPrintDiv +=  ` <div class="page-break"> 
		           
				<div id="payment-reciept" style='font-family: system-ui;'>
				  <div id="top-container" >
					<img src="${JS.baseUrl}uploads/images/${logo}" alt="Logo" width="150" height="150">
					<div id="name" style ="float: right; width: 45%; text-align: right; margin-right: 50px; display: inline-block;}">
							<strong> ${sessionStorage.getItem('company_name')}</strong>
							<br> ${sessionStorage.getItem('company_phone')}
							<br> ${sessionStorage.getItem('company_email')}
					</div>
					<div id="title" style="width:100%; display: inline-block; border-top: #266897 5px solid;">
						<h2 style = "
							display: inline-block;
							height: 100%;
							padding: 15px;
							margin-top: 0;
							margin-bottom: 0;
							margin-left: 50px;
							color: #266897;"
						>Payment Reciept</h2>
						</div>
					

				  </div>
					
				<div id="heading" style="display: inline-block;padding:50px;">
					<table>
					  <tr>
						<td><strong>Account:</strong></td>
						<td>${account}</td>
					  </tr>
					  <tr>
						<td><strong>Date:</strong></td>
						<td>${date}</td>
					  </tr>
					  <tr>
						<td><strong>Property:</strong></td>
						<td>${property}</td>
					  </tr>
					  <tr>
						<td><strong>Unit:</strong></td>
						<td>${unit}</td>
					  </tr>
					</table>

				</div>
				<br>
				<div style="display: inline-block;padding:50px 30px 400px 170px;">
				<table>
				  <thead>
				     <tr>
						<td style ="width:200px">Paid By</td>
						<td style ="width:200px">Pay Method</td>
						<td style ="width:200px">Amount</td>
					  </tr>
				  </thead>
				  <tbody>
				     <tr>
						<td style ="width:200px">${profile}</td>
						<td style ="width:200px">${pay_method}</td>
						<td style ="width:200px">${amount}</td>
					  </tr>
				  </tbody>

				</table>


				</div>

				<div id="title" style="width:100%; display: inline-block;">
						<h2 style = "
							display: inline-block;
							height: 100%;
							padding: 15px;
							margin-top: 0;
							margin-bottom: 0;
							margin-left: 50px;
							"
						>Thank You For your Payment!</h2>
						</div>
`;
				/* if(data.eCity && data.eState){
					newPrintDiv  += data.eCity +`, ` + data.eState;
				}else{
					if(data.eCity){newPrintDiv  += data.eCity;}
					if(data.eState){newPrintDiv  += data.eState;}
				}
				if(data.Ezip){newPrintDiv  += " " +data.Ezip;} */

				/* newPrintDiv  += `<br></div>`;
				newPrintDiv  += `<div id="bankName" >
				<strong>` + data.bank_name;
				newPrintDiv  += `</strong><br>` + data.bank_address;
				newPrintDiv  += `</div>
				
				
							
					<div id="check_info">
						<table>
							<tbody>
								<tr>
									<th>Check Date__</th> 
									<th>Check No.</th>
								</tr>
								<tr>
								<td id = cDate>` + today; 
								newPrintDiv  += `</td>
								<td id="cNum"> &nbsp; &nbsp; &nbsp; `;
								newPrintDiv  += data.next_check_num;
								newPrintDiv  += `</td>
							</tr>
							</tbody>
						</table>                                         
					</div>
							
					<div style="position: absolute;top: 100px;">
					<small style="position: absolute;left: 25px;">Pay To The Order Of</small>
					<input style="border-bottom:1px solid black;width:480px;position: absolute;left: 150px;">
					<span style="position: absolute; left: 660px;">$</span>
					<input style="border: 2px solid #a3afae33;width:100px;position: absolute;left: 670px;">
					<br>
					<br>
					<input style="border-bottom:1px solid black;width: 680px;">   
					<span>Dollars</span>
					</div>

					<div id="bottum" style ="top:180px;">
						<div>
							<div>
								<span style="position: absolute;left: 30px;">Memo</span>
								<input style="border-bottom:1px solid black;width:300px;position: absolute;left: 90px;">
								<input style="border-bottom:1px solid black;width:250px;position: absolute;left: 510px;">
							</div>
						</div>
					</div>
					<div id="accountNumber" style=" font-family: micr37; top:165px;">
						<span id ="checkNumber">C` + data.next_check_num +"C";
						newPrintDiv  += `</span>
						<span id ="routingNum">A` + data.routing + `A`;
						newPrintDiv  += `</span> <span id ="acctNum">`  + data.account_number + `C`;
						newPrintDiv  += `</span> 
					</div>
				</div>
			</div>
			`; */
			$('#Checkarea').empty();
		    $('#Checkarea').append(newPrintDiv);
			$('#Checkarea').addClass('print-section');
			
			//have to set timeout in order for logo image to print
			setTimeout(function() { // wait until all resources loaded 
				window.print();
            }, 250);

                
        
       //$('#Checkarea').empty();
	}
			
			</script>