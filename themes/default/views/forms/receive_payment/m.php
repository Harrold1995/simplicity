<div class="modal fade property-modal" id="receive_paymentModel" tabindex="-1" role="dialog" main-id=<?= isset($deposit) && isset($deposit->id) ? $deposit->id : '-1' ?> type="deposit" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="receivePayments" autocomplete="off">
                  
				<header class="modal-h" style="z-index: 17;">
					<h2>Receive Payment</h2>
					<nav>
						<ul>
								<li><a href="#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
								<li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>				
                                <li><?= isset($tenant) ? '<a href="deposit/deleteDeposit/'.$deposit->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li><a href="./"><i class="icon-envelope-outline" aria-hidden="true"></i> <span>Envelope</span></a></li>
							<li><a href="./"><i class="icon-brain" aria-hidden="true"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents" aria-hidden="true"></i> <span>Copy</span></a></li>
							<li><a href="./"><i class="icon-paperclip" aria-hidden="true"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print" aria-hidden="true"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>
				<section class="d" style="z-index: 11;">
					<div class="double h">
						<div>
							<p>
								<?php echo (isset($header->id)) ? '<input type="hidden" name="header[id]" value="'. $header->id .'" >' : "" ;   ?>
								<?php echo (isset($header->cp_tid)) ? '<input type="hidden" name="customer_payments[trans_id]" value="'. $header->cp_tid .'" >' : "" ;   ?>
								<label id="thisProfile" for="fin">Payment from:</label>
								<span class="select" >
								<select id="profile_id" name="profile_id" onchange="JS.loadList('api/getTransactions', {profile:$(this).closest('section').find('input[name=profile_id]').val(),lease:$(this).closest('section').find('#formNames').find('input[name=lease_id]').val()} , '#received_payment_table' ,  $(this).closest('#receive_paymentModel'))";
								class="editable-select">
								    <option value="0"></option>
									<?php  foreach($tenants as $tenant): ?>
									   <option  value="<?= $tenant->id ?>"  <?php echo ( $header->profile_id == $tenant->id || $profile->id ==  $tenant->id) ? ' selected="selected" ' : '' ; ?>   ><?= $tenant->tenant ?></option>
									<?php  endforeach; ?>
								</select>
                                </span>
							</p>
							<p>
								<label for="fio">Pay method:</label>
								<span class="select">
                                <select  name="customer_payments[payment_method]" class="editable-select">
                                     <?php foreach($transaction_types as $transaction_type): ?>
									   <option value="<?= $transaction_type->id ?>" <?php echo ( $header->payment_method == $transaction_type->id ) ? ' selected="selected" ' : '' ; ?>  ><?= $transaction_type->name ?></option>
									<?php endforeach; ?>
								</select>
                                </span>
							</p>
							<p>
								<label >Amount: <span class="prefix">$</span></label>
								<input id="received_amount" type="number"  name="amount" class ="decimal" value="<?php echo ( isset($header->credit)) ?  $header->credit  : '0.00' ; ?>">
							</p>
							<p>
								<label for="fir">Memo:</label>
								<input type="text" id="fir" name="header[memo]" value="<?php echo ( isset($header->memo)) ?  $header->memo  : '' ; ?>">
							</p>
						</div>
						<div>
						<p  id="rpProperties2">
								<!-- <label for="rpProperties">Property:</label>
								<span class="select">
                                <select id="rpProperties" name="property" onchange="JS.loadList('api/getTransactions', {profile: $(this).closest('#receive_paymentModel').find('#profile_id').closest('.select').find('input[type=hidden]').val() ,property:$(this).closest('.select').find('input[type=hidden]').val()} , '#received_payment_table' ,  $(this).closest('#receive_paymentModel'))" class="editable-select">
									<option value="0"></option>
									< ?php  foreach($properties as $property): ?>
									   <option  value="< ?= $property->id ?>"  < ?php echo (isset($properties) && $properties[0]->id == $property->id ? ' selected' : '') ?>   >< ?= $property->name ?></option>
									< ?php  endforeach; ?>
								</select>
                                </span> -->
							</p>
							<span id="formNames"></span>
							<p>
								<label for="fiq">Ref #:</label>
								<input type="text" id="fiq" name="header[transaction_ref]" placeholder="Enter Ref #" value="<?php echo ( isset($header->transaction_ref)) ?  $header->transaction_ref  : '' ; ?>">
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
								<label for="fiq">Deposit To:</label>
								<span class="select">
                                <select name="customer_payments[deposit_to]" class="editable-select">
                                     <?php foreach($accounts as $account): ?>
									   <option  value="<?= $account->id ?>"  <?php echo ( $header->deposit_to == $account->id ) ? ' selected="selected" ' : '' ; ?>><?= $account->name ?></option>
									<?php endforeach; ?>
								</select>
                                </span>
							</p>
						</div>
					</div>
					
				</section>
				<div id="DataTables_Table_8_wrapper" class="dataTables_wrapper has-table-c mobile-hide c text-center">
					<div class="dataTables_scroll">
						<div class="dataTables_scrollHead" style="overflow: hidden; position: relative; border: 0px; width: 100%;">
							<div class="dataTables_scrollHeadInner" style="box-sizing: content-box; width: 1015px; padding-right: 5px;">
								<table class="table-c c text-center mobile-hide dataTable" style="z-index: 24; margin-left: 0px; width: 1225px;" role="grid">
									<thead>
										<tr role="row">
											<!-- <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 46px;"><a href="#/" id="received_payment_unselect_all" >Unselect</a></th> -->
											<th  rowspan="1" colspan="1"  style="width: 5%;" class="check-a"><label for="received_payment_select_all" class = "checkbox"><input type="checkbox" id="received_payment_select_all" class=""></label></th>
											<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 289px;">Description</th>
											<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 107px;">Date</th>
											<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 107px;">Due Date</th>
											<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 113px;">Amount</th>
											<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 156px;">Open Balance</th>
											<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 197px;">Payment Amount</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
						<div class="dataTables_scrollBody" style="position: relative; overflow: auto; max-height: 448px; width: 100%;">
							<table class="table-c c text-center mobile-hide dataTable" style="z-index: 24; width: 100%;" id="DataTables_Table_8" role="grid" aria-describedby="DataTables_Table_8_info">
								<thead>
						<tr role="row" style="height: 0px;">
							<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 46px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;">
								<div class="dataTables_sizing" style="height:0;overflow:hidden;"></div>
							</th>
								<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 289px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;">
									<div class="dataTables_sizing" style="height:0;overflow:hidden;">Description</div>
								</th>
								<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 107px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;">
									<div class="dataTables_sizing" style="height:0;overflow:hidden;">Date</div>
								</th>
								<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 107px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;">
									<div class="dataTables_sizing" style="height:0;overflow:hidden;">Due Date</div>
								</th>
								<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 113px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;">
									<div class="dataTables_sizing" style="height:0;overflow:hidden;">Amount</div>
								</th>
								<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 156px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;"><div class="dataTables_sizing" style="height:0;overflow:hidden;">Open Balance</div>
							</th>
							<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 197px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;">
								<div class="dataTables_sizing" style="height:0;overflow:hidden;">Payment Amount</div>
							</th>
						</tr>
					</thead>
				
					
					<tbody id="received_payment_table">	
					    
						<?php foreach($transactions as $transaction) :   ?>
						
						<tr role="row"  id="received_payament_row" class="received_payament_row">
								<td  id="received_payament_check" > 
								<input  class="received_payament_input" type="hidden"  name="applied_payments[<?php echo $transaction->id ?>][applied]" value="<?php echo ($transaction->received_payment != 0 )  ? '1' : '0' ; ?>">
								<input type="hidden" name="applied_payments[<?php echo $transaction->id ?>][transaction_id_b]" value="<?php echo $transaction->id ?>"> 
								<i id="received_payament_icon_check" style="visibility :<?php echo  ( $transaction->received_payment != 0   ) ? 'visible' : 'hidden' ; ?>" class="icon-check" aria-hidden="true"></i><div class="shadow" style="width: 1200px;"></div>
								</td>
								<td><?= $transaction->description ?></td>
								<td><?= $transaction->date ?></td>
								<td><?= $transaction->due_date ?></td>
								<td><span class="wrapper"><span class="text-left">$</span >     <span class="payment_amount"><?= $transaction->amount ?></span>         </span></td>
								<td><span class="wrapper"><span class="text-left">$</span>     <span class="open_balance" ><?= $transaction->open_balance ?> </span>    </span></td>
								<td id="received_payament_row_input_amount">
									<span class="input-amount">
										<label for="tcaa">$</label>
										<input type="text" id="received_payament_input_amount" name="applied_payments[<?php echo $transaction->id ?>][amount]" value="<?php echo $transaction->received_payment ?>" 
										<?php echo  ( $transaction->received_payment != 0   ) ? '' : 'disabled' ; ?> >
									</span>
								</td>
						</tr>

						<?php endforeach;    ?>
						
					</tbody>
					<style type="text/css" onload="firstUpdateUnapliedAmount($(this))"></style>
					
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
									<span class="text-left">Unapplied: <span class="text-right">$</span></span><span id="unapplied_amount"> 22,695.06</span>
								</td>
								<td rowspan="1" colspan="1" style="width: 107px;"></td>
								<td class="text-right" rowspan="1" colspan="1" style="width: 113px;">Total:</td>
								<td rowspan="1" colspan="1" style="width: 156px;"><span class="wrapper"><span class="text-left">$</span > <span id="received_payment_amount_total"> 22,695.06</span> </span></td>
								<td rowspan="1" colspan="1" style="width: 156px;"><span class="wrapper"><span class="text-left">$</span> <span id="received_payment_open_total">22,695.06 </span> </span></td>
								<td rowspan="1" colspan="1" style="width: 197px;"><span class="wrapper"><span class="text-left">$</span> <span id="received_payment_applied_total">22,695.06 </span> </span></td>
							</tr>
					   </tfoot>
					</table>
				</div>
			</div></div><div class="dataTables_info" id="DataTables_Table_8_info" role="status" aria-live="polite">Showing 1 to 13 of 13 entries</div></div>
				
				<footer>
					<ul class="list-btn">
						<li><button type="submit" after="mnew">Save &amp; New</button></li>
						<li><button type="submit"  after="mclose">Save &amp; Close</button></li>
						<li><button type="button">Duplicate</button></li>
						<li><button type="button">Cancel</button></li>
					</ul>
					<?= $header ? 
					"<ul>
						<li>Last Modified $header->modified</li>
						<li>Last Modified by <a href='./'>$header->user</a></li>
					</ul>" : '';?>
				</footer>
			<a class="close" href="./">Close</a></div>
			</form>
			<style type="text/css" onload="applyRefundInfo($(this).closest('.modal'))"></style>    
			<script>
			var leases= <?php echo $leases ? json_encode($leases) : '0'?>;
			var lease_id = <?php echo $lease_id ? json_encode($lease_id) : '0'?>; 			

			$( function() {
					 $( "#received_payment_date" ).datepicker();
					 $( "#payment_deposit_on_date" ).datepicker();
	
			} );
			var hasProperties = <?php echo $profile ? $profile : '0'?>;
			//profile_id = profile_id > 0 ? profile_id : tenants[0].id;
			//gets profile onload
			$(document).ready(function () {
				if(hasProperties > 0){
					JS.loadList('api/getTransactions', {profile: hasProperties, lease:<?= $lease ?>} , '#received_payment_table' , '#receive_paymentModel');
				}
			});
			// {profile:< ?php echo $profile->id ?>, property:< ?php echo $properties[0]->id ?>} 

			//$(document).ready(function () {
				function applyRefundInfo(modal){
			 getLeases(hasProperties, modal);
			 console.log('profile',hasProperties);
			}
        //});

	//gets leases based on profile
	function getLeases(profileId, modal){
				var leaseSpot = 0;
				var lease;
				var newRow = '';
				newRow = ` <label for="lease_id"></label>
											<span class="select">
											<select class="form-control editable-select" id="lease_id" onchange="setNames($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal'));">`;
														for (var j = 0; j < leases.length; j++) {
															if(profileId == leases[j].profile_id){
																newRow += `<option value='` + leases[j].id + `'`;
																if(lease_id > 0){                                                              
																	if(lease_id == leases[j].id){ newRow += ' selected'; setNames(leases[j].id, modal);}
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
								$(modal).find('#rpProperties2').empty();            
								$(modal).find('#rpProperties2').append(newRow);
								$(modal).find('#rpProperties2').find('.editable-select').editableSelect();
								lease_id = 0;
			}
			function setNames(leaseId, modal){
            for (var j = 0; j < leases.length; j++) {
                if(leaseId == leases[j].id){ 
                   var names = `<input type="hidden" name="lease_id" value="`+ leases[j].id +`">
                                <input type="hidden" name="property_id" value="`+ leases[j].property_id +`">
                                <input type="hidden" name="unit_id" value="`+ leases[j].unit_id +`">`;
                }
            }
            $(modal).find('#formNames').empty();
            $(modal).find('#formNames').append(names);
        }
  $( function() {
	//  $( "#fromDate" ).datepicker();
	//  $( "#toDate" ).datepicker();
  } );
			
			</script>