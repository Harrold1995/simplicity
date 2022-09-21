<div class="modal fade flexmodal bank_trans-modal" id="bank_trans-modal" tabindex="-1" role="dialog"  doc-type="transactions" type="Bank_Trans" ref-id="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <form action="<?php echo $target; ?>" method="post" data-title="Bank-Transaction"  type="bank_trans">
				  <?php if(isset($header) && isset($header->id)){
                                    echo '<input type="hidden" name="header[id]" value="' . $header->id . '"/>';
						} 
						
						if (isset($headerTransaction->rec_id)) {
							echo '<input type="hidden" name="headerTransaction[rec_id]" id="rec_id"  value="' . $headerTransaction->rec_id . '"/>';
						}
						if (isset($headerTransaction->clr)) {
							echo '<input type="hidden" name="headerTransaction[clr]" id="clr"  value="' . 1 . '"/>';
						}?>
						
						

				  <?php if(isset($headerTransaction) && isset($headerTransaction->id)){
                                    echo '<input type="hidden" name="headerTransaction[id]" value="' . $headerTransaction->id . '"/>';
                        } ?>
				<header class="modal-h">
					<h2>Bank Transaction</h2>

					<ul class="check-a">
					<?php $normal_radio_button = 'checked'; if(isset($headerTransaction) && isset($headerTransaction->credit) && $headerTransaction->credit > 0){$normal_radio_button = ''; $credit_radio_button = 'checked';}
					?>
						<li><label for="radioButton">Debit(Deposit)</label><input type="radio" id="charge" value="normal" name="radioButton" <?php echo $normal_radio_button; ?>></li>
						<li><label for="credit">Credit(Check)</label><input type="radio" id="credit" value="credit" name="radioButton"<?php echo $credit_radio_button; ?>></li>
					</ul>
					<nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
					<nav>
						<ul>
							<li><a href="./"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
							<li><a href="./"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
							<li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
							<li><a href="./"><i class="icon-brain"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
							<li class="getDocuments" ><a href="#" class="uploadDocument"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print"></i> <span>Print</span></a></li>
						</ul>
					</nav>

				</header>
				<section class="d">
				<div class="<?php echo $hasRecId ?>">
                                <?php echo $hasRecIdHtml ?>
                            </div>
					<div class="double">
						<div style= "margin-left: 30px; width: 40%;">
							<p class="select">
								<label for="accountName">Bank:</label>
								<span stype="account" class='select'>
									
									<input  hidden-name = 'headerTransaction[account_id]' name="headerTransaction[account_id]" class="fastEditableSelect" filter_key ='details' filter_value = 'BK' stype="account" value="<?php echo (isset($headerTransaction->account_id)) ?  $headerTransaction->account_id  : ''; ?>"  key="accounts.name" modal="account"  default="<?php echo (isset($headerTransaction->account_id)) ?  $headerTransaction->account_id  : ''; ?>"></input>
							    </span>
							</p>
							
							<p class="select"><!-- id="property_id"  used for javascript-->
							<label for="property_id">Property</label>
							<span stype="property" class='select'>
									
									<input  hidden-name = 'headerTransaction[property_id]' name="headerTransaction[property_id]" id = 'property_id' class="fastEditableSelect" data-filter-key =  'profile_type_id' data-filter-value = '1' stype="property" value="<?php echo (isset($headerTransaction->property_id)) ?  $headerTransaction->property_id  : ''; ?>"  key="properties.name" modal="property"  default="<?php echo (isset($headerTransaction->property_id)) ?  $headerTransaction->property_id  : ''; ?>"></input>
							</span>

							</p>
							<p id="vendorTest" class="select">  
                            <label for="profile_id">Vendor</label>
							<span stype="vendors" class='select'>
									
									<input  hidden-name = 'headerTransaction[profile_id]' name="headerTransaction[profile_id]" class="fastEditableSelect es-add es-setup" stype="vendors" type="table" value="<?php echo (isset($headerTransaction->profile_id)) ?  $headerTransaction->profile_id  : ''; ?>"  key="vendors.name" modal="vendor"  default="<?php echo (isset($headerTransaction->profile_id)) ?  $headerTransaction->profile_id  : ''; ?>"></input>
							</span>							
							</p>
						</div>
						<div>
							<p>
								<label for="transaction_ref">Reference:</label>
								<input type="text" value="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" id="transaction_ref" name="header[transaction_ref]">
							</p>
							<p>
								<label for="date">Date:</label>
								<input data-toggle="datepicker" id="ccDate" name="header[transaction_date]" value="<?= isset($header) && isset($header->date) ? $header->date : '' ?>">
							</p>
							<p>
								<label for="amount">Amount:  <span class="prefix">$</span></label>
								<input type="text" id="amount" class="topAmount toggleRadio" name="headerTransaction[credit]" value="<?= isset($headerTransaction) && isset($headerTransaction->credit) || isset($headerTransaction->debit) ? str_replace("-","",$headerTransaction->credit)+ $headerTransaction->debit: '' ?>">
							</p>
							<p class="select">
								<label for="class">Class</label>
								<select class="editable-select quick-add set-up" id="class" name="headerTransaction[class_id]">
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
						<input type="text" id="memo" name="header[memo]" value="<?= isset($header) && isset($header->memo) ? $header->memo : '' ?>">
					</p>
				</section>
        <div class ="has-table-c">
            <table class="table-c billTable mobile-hide dataTable no-footer formGridTable" style="display: table; width: 100%;">
                <thead class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden;">
                    <tr>
                        <th>Account</th>
                        <th>Property</th>
                        <th>Unit</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Class</th>
                        <th>Name</th>
                    </tr>
                </thead>
                    <tbody  class="dataTables_scrollBody" style=" display: block;height: calc(100vh - 580px);overflow: auto;">

					</tbody>
					
					<tfoot style="display: table; width: 100%; table-layout: fixed;">					
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td class="text-right">Total:</td>
							<td class="text-center"><span class="text-left">$</span> <span id="totalAmount"></span> </td>
							<td></td>
							<td></td>
						</tr>
					</tfoot>
                    </table>
			</div>
				<footer>
					 <ul class="list-btn">
						<li><button type="submit" after="mclose">Save &amp; Close</button></li>
						<li><button type="button">Cancel</button></li>						
					</ul>
					<?= $header ?
					"<ul>
						<li>Last Modified $header->modified</li>
						<li>Last Modified by <a href='./'>$header->user</a></li>
					</ul>" : ''; ?>
				</footer>
			</form>          
		</div>
	</div>
  </div>
</div>
<script>
var type = <?php echo '"'.$normal_radio_button.'"'; ?>;

      var template = function (id, data = {}) {
                var newRow = '<tr class="checkRow" id="' + id + '" ' + (data.id ? 'tid="' + data.id + '"' : '') + ' ' + (data.property_id && data.property_id != '-1' ? 'property_id="' + data.property_id + '"' : '') + '>' +
                    '<td class="formGridSelectTd" stype="account" source="[sel-id=account_id]" ' + (data.account_id ? 'value="' + data.account_id + '"' : '') + '></td>' +
                    '<td class="formGridSelectTd" stype="property" source="[sel-id=property_id]" ' + (data.property_id ? 'value="' + data.property_id + '"' : '') + '></td>' +
                    '<td class="formGridSelectTd" stype="unit" ' + (data.unit_id ? 'value="' + data.unit_id + '"' : '') + '></td>' +
                    '<td><input type="text" id="description" name="transactions[' + id + '][description]" value="' + (data.description ? data.description : '') + '"></td>' +
                    '<td total="debit" source="#amount" ><input type="text" class="decimal checkAmount total" id="amount" name="transactions[' + id + '][debit]" value="' + (data.debit || data.credit ? (type == "checked" ? data.credit - data.debit : data.debit - data.credit) : '') + '" placeholder="0"></td>' +
                    '<td class="formGridSelectTd" stype="class" source="[sel-id=class_id]" ' + (data.class_id ? 'value="' + data.class_id + '"' : '') + '></td>' +
                    '<td class="formGridSelectTd" stype="profile" source="[sel-id=profile_id]" ' + (data.profile_id ? 'value="' + data.profile_id + '"' : '') + '></td>' +
                    '</tr>';
                return newRow;
            }
            var grid = $('.modal').last().formGrid({
                template: template,
                data: <?php echo $transactions ? json_encode($transactions) : 0 ?>,
                minRows: 8
            });
            grid.addTotal('debit', '#amount', '#amount', '#totalAmount');
			

  </script>