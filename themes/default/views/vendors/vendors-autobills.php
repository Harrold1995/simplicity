
			<div class="table-wrapper" tabindex="-1">
				<form action="transactions/deleteMemorizedTransactions" method="post" formtype="vendors" class=" ve form-bills pageForm" data-title="memorized-transactions-entry" type=" memorized-transactions" d-type="delete-auto-bills">
				<input type="hidden" name="" value="<?=isset($typeId) ?$typeId : '';?>" id="object_id">
				<table  id="autoBillsTable" class="table-b a">
					    <thead>
						<tr>
						    <th></th> 
							<th>Type</th>     
							<th>Amount</th>
							<th>Next Date</th>
							<th>Property</th>
							<th>frequency</th>
							<th>Auto </th>

						</tr>	
						</thead>
						<tbody>
						<?php  if (isset($autobills)){
                       foreach ($autobills as $autobill) {?>						
						<tr id ="<?=isset($autobill->id) ?$autobill->id : '';?>">
						    <td style="width: 4%;" class="check-a">
							<input type="hidden" name="" value="<?=isset($autobill->id) ?$autobill->id : '';?>" id="id">
									<label for="<?=isset($autobill->id) ?$autobill->id : '';?>" class="checkbox">
										<input type="checkbox" id="<?=isset($autobill->id) ?$autobill->id : '';?>" class="hidden allAccounts" aria-hidden="true"
										onchange="formsJs.Checkbox($(this),<?=isset($autobill->id) ?$autobill->id : '';?>);">
											<div class="input"></div>
									</label>
							</td>
							<td class="overlay-f"><?=isset($autobill->Type) ?$autobill->Type : '';?></td>
							<td><?=isset($autobill->amount) ?'$' . number_format( $autobill->amount, 2) : '';?></td>
							<td class="overlay-f"><?=isset($autobill->next_trans_date) ?$autobill->next_trans_date : '';?></td>
							<td ><?=isset($autobill->property) ?$autobill->property : '';?></td>							
							<td class="overlay-f"><?=isset($autobill->frequency) ?$autobill->frequency : '';?></td>
							<td class="overlay-f"><?=isset($autobill->auto) ?$autobill->auto : '';?></td>


						<?php }} ?>

						</tbody>
						
						
					</table>
					<p class="strong scheme-b m20 size-a overlay-a" id="selectedCharges">0 Auto bills selected</p>
					   <center><p class="link-btn strong scheme-b m20 size-a overlay-a"><button type="submit" disabled class="autobills grid" style="" >Delete auto bills</button><button type="submit" class="autobills grid" disabled id="postSelected" >Post Selected</button></p></center> 
					   </form>
				</div>

				<script>
                  
				  JS.checkboxes($('#autoBillsTable'));
				</script>