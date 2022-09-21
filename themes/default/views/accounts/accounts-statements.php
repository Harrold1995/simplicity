			<div class="table-wrapper" tabindex="-1">
					<table  id="statementsprint" class="table-b a">
					    <thead>
						<tr>
							
							<th>Closing Date</th>     
							<th>Statement Balance</th>
							<th>Book Balance</th>
							<th>Rec#</th>
							<th class="text-right">Statement</th>
							<th></th>
							<th></th>
							<th>
								<ul class="list-square">
									<li><a href="./" class="print"><i class="icon-plus-thin"></i> <span>Add</span></a></li>
								</ul>
							</th>
						</tr>	
						</thead>
						<tbody>
						<?php  if (isset($allReconciliation)){
								$spot =1;
								$spot2 =1;
                       foreach ($allReconciliation as $reconciliation) {?>						
						<tr>
							<td class="overlay-f"><?=isset($reconciliation->statement_end_date) ? $reconciliation->statement_end_date : 'Not Reconciled';?>
								<div class="bg" style="width: 1051px;"></div>
							</td>
							<td class="<?php echo ($reconciliation->ending_bal > -1) ? 'overlay-c' : 'overlay-d' ;?>"><?=isset($reconciliation->ending_bal) ?'$' . number_format( $reconciliation->ending_bal, 2) : '';?></td>
							<td class="<?php echo ($reconciliation->bookBalance > -1) ? 'overlay-c' : 'overlay-d' ;?>"><?=isset($reconciliation->bookBalance) ?'$' . number_format($reconciliation->bookBalance, 2) : '';?></td>
							<td data-id="50" class="reportLink" rtype="report" defaults="<?php echo $reconciliation->id ?>"><a href="#" style="color: orange;"><?php  if($reconciliation->closed == 0){echo 'Not Reconciled';}else{if($reconciliation->id) echo  $reconciliation->id ;}?></a></td>
							<?php if($reconciliation->statement_attachment) {?>
								<td class="overlay-g text-right"><a href="<?=isset($reconciliation->statement_attachment) ? base_url() . "uploads/documents/" . $reconciliation->statement_attachment : '';?>" target="_blank">View</a></td>
							<?php }else{?>
								<td rec-id="<?php echo $reconciliation->id ?>" class="overlay-g text-right"><a href="#" id="uploadRecDocument">Attach</a></td>
							<?php } ?>
							<?php if($spot == 1) {?>
								<td id="deleteRec" rec-id="<?php echo $reconciliation->id ?>" data-type="<?php echo $reconciliation->type ?>" class="overlay-g text-right"><a href="#" style="color: red;">Delete</a></td>
							<?php 	$spot =0; }else{?>
								<td id="" rec-id="<?php echo $reconciliation->id ?>" class="overlay-g text-right deleteTd"></td>
							<?php } ?>
							<?php if($recDisplay == "start" && $spot2 == 1){?>
								<td id="reopenRec" rec-id="<?php echo $reconciliation->id ?>" class="overlay-g text-right"><a href="#" style="color: green;">Reopen</a></td>
							<?php  $spot2 =0; }else{?>
								<td id="" rec-id="<?php echo $reconciliation->id ?>" class="overlay-g text-right openTd"></td>
							<?php } ?>
							
							<td>
								<ul class="list-square">
									<li id="deleteRec" rec-id="<?php echo $reconciliation->id ?>"><a href="#"><i class="icon-documents"></i> <span>Documents</span></a></li>
									<li><a href="./"><i class="icon-notes"></i> <span>Notes</span></a></li>
									<li><a href="./" class="print"><i class="icon-print"></i> <span>Print</span></a></li>
								</ul>
							</td>
						</tr>
						<?php }} ?>

						</tbody>
						
						
					</table>
				</div>


<!--tr id="statementsprint">
            <td>1/10/2018</td>
            <td>-$1,200.00</td>
            <td>abcdefgh</td>
            <td>RC1561</td>
            <td>RC1564</td>
            <td>
              <i style="cursor: pointer" onClick="window.print()" class="fas fa-print p-2 flex-shrink-1 bd-highlight"></i>
            </td>
  </tr-->