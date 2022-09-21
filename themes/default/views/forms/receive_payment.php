<div class="modal fade property-modal" id="tenantModal" tabindex="-1" role="dialog" main-id=<?= isset($deposit) && isset($deposit->id) ? $deposit->id : '-1' ?> type="deposit" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; ">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data'>
                  
				  	<header class="modal-header"></header>
				<header style="z-index: 17;">
					<h2>Receive Payment</h2>
					<nav>
						<ul>
								<li><a href="#" class="switchModal" dir="prev"></i> <span>Previous</span></a></li>
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
								<label for="fin">Payment from:</label>
								<span class="select"><select id="fin" name="fin">
									<?php foreach($tenants as $tenant): ?>
									   <option>Position #1</option>
									<?php endforeach; ?>
								</select></span>
							</p>
							<p>
								<label for="fio">Pay method:</label>
								<span class="select"><select id="fio" name="fio">
									<option>Cash</option>
									<option>Position #1</option>
									<option>Position #2</option>
									<option>Position #3</option>
									<option>Position #4</option>
									<option>Position #5</option>
								</select></span>
							</p>
							<p>
								<label >Amount: <span class="prefix">$</span></label>
								<input id="received_amount" type="text"  class = "decimal" value="0.00">
							</p>
							<p>
								<label for="fir">Memo:</label>
								<input type="text" id="fir" name="fir">
							</p>
						</div>
						<div>
							<p>
								<label for="fiq">Ref #:</label>
								<input type="number" id="fiq" name="fiq" value="4101">
							</p>
							<p class="is-date">
								<label for="fir">Date:</label>
								<input type="text" id="received_payment_date"  >
							</p>
							<p class="is-date">
								<label for="fiq">Deposit On:</label>
								<input type="text" id="payment_deposit_on_date" name="fiq" >
							</p>
							<p>
								<label for="fiq">Deposit To:</label>
								<span class="select"><select id="fio" name="fio">
									<option>Cash</option>
									<option>Position #1</option>
									<option>Position #2</option>
									<option>Position #3</option>
									<option>Position #4</option>
									<option>Position #5</option>
								</select></span>
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
											<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 46px;"></th>
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
				
					
					<tbody>	
					    
						<?php for($i = 0; $i < 10; $i++ ) :  ?>
						
						<tr role="row"  id="received_payament_row" class="received_payament_row">
								<td  id="received_payament_check" ><input  class="received_payament_input" type="hidden"  name="" value="0"> <i id="received_payament_icon_check" style="visibility :hidden" class="icon-check" aria-hidden="true"></i><div class="shadow" style="width: 1200px;"></div></td>
								<td>Rent Charge (5/5/2018)</td>
								<td>5/1/2013</td>
								<td>5/1/2013</td>
								<td><span class="wrapper"><span class="text-left">$</span class="payment_amonut"> 1,560.00</span></td>
								<td><span class="wrapper"><span class="text-left">$</span> 1,560.00</span></td>
								<td id="received_payament_row_input_amount">
									<span class="input-amount">
										<label for="tcaa">$</label>
										<input type="text" id="received_payament_input_amount" value="1,560.00" disabled="disabled" >
									</span>
								</td>
						</tr>

						<?php endfor;    ?>
						
					</tbody>
					
				</table>
			</div>
			<div class="dataTables_scrollFoot" style="overflow: hidden; border: 0px; width: 100%;">
				<div class="dataTables_scrollFootInner" style="width: 1015px; padding-right: 5px;">
					<table class="table-c c text-center mobile-hide dataTable" style="z-index: 24; margin-left: 0px; width: 1015px;" role="grid">
						<tfoot>					
							<tr>
								<td rowspan="1" colspan="1" style="width: 46px;">
									<div class="shadow" style="width: 1015px;"></div>
								</td>
								<td class="overlay-k" rowspan="1" colspan="1" style="width: 289px;">
									<span class="text-left">Unapplied: <span class="text-right">$</span></span><span id="unapplied_amount"> 22,695.06</span>
								</td>
								<td rowspan="1" colspan="1" style="width: 107px;"></td>
								<td rowspan="1" colspan="1" style="width: 107px;"></td>
								<td class="text-right" rowspan="1" colspan="1" style="width: 113px;">Total:</td>
								<td rowspan="1" colspan="1" style="width: 156px;"><span class="wrapper"><span class="text-left">$</span> 22,695.06</span></td>
								<td rowspan="1" colspan="1" style="width: 197px;"><span class="wrapper"><span class="text-left">$</span> 22,695.06</span></td>
							</tr>
					   </tfoot>
					</table>
				</div>
			</div></div><div class="dataTables_info" id="DataTables_Table_8_info" role="status" aria-live="polite">Showing 1 to 13 of 13 entries</div></div>
				
				<footer>
					<ul class="list-btn">
						<li><button type="submit" after="mnew">Save &amp; New</button></li>
						<li><button type="submit"  after="mnew">Save &amp; Close</button></li>
						<li><button type="button">Duplicate</button></li>
						<li><button type="button">Cancel</button></li>
					</ul>
					<ul>
						<li>Last Modified 12:22:31 pm 1/10/2018</li>
						<li>Last Modified by <a href="./">User</a></li>
					</ul>
				</footer>
			<a class="close" href="./">Close</a></div>

			<script>
			$( function() {
					 $( "#received_payment_date" ).datepicker();
					 $( "#payment_deposit_on_date" ).datepicker();
	
			} );

			
			</script>