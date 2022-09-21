


<div id="banktrans" class="table-wrapper table-b-wrapper rightslick-table">

</div>

<!-- <div class="table-wrapper" tabindex="-1">
					<table  id="statementsprint" class="table-b a">
					    <thead>
						<tr>
						    <th>Date</th>  
							<th>Description</th>     
							<th>Type</th>
							<th>Amount</th>
							<th>Balance</th>
							<th class="text-right">Statement</th>

						</tr>	
						</thead>
						<tbody>
										
						
						< ?php  foreach ($specialAccount->bankTrans as $banktran){
                          echo '<tr><td>'.$banktran->date.'</td><td>'.$banktran->name.'</td><td>'.$banktran->payment_channel.'</td><td>'.$banktran->amount.'</td><td>'.$banktran->date.'</td></tr>';
						} ?>
						
						

						</tbody>
						
						
					</table>
				</div> -->
<script>
    var ItemId = '<?php echo $specialAccount->finins->account_id ?>';
    var filter = "t.account_id";
	var rightSlick = new SlickRight('#banktrans', {key: filter, dataUrl: "transactions1/getBankTransData/" + ItemId + "/" + filter, tableName:'bankTrans'});
	$('#banktrans').data('grid', rightSlick);

</script>