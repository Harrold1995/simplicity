<div class="modal flexmodal fade vendorPayBill-modal" id="vendorPayBillModal" tabindex="-1" role="dialog" main-id=<?= isset($reconciliation) && isset($reconciliation->r_id) ? $reconciliation->r_id : '-1' ?> type="pay-bills" ref-id="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl theme-c " role="document">
        <div id="root" class="no-print">

            <div class="modal-content text-primary form-bills form-entry popup-a  shown" style=" width:100%; padding: 30px; padding-top: 2px; background:#F4F4F4;     border: 1px solid #dfe5e5;">
                <form action="<?php echo $target; ?>" method="post" type="paybills">
                    <div style="width: 200px; height: 1px; margin: 0 auto;">
                        <nav style="width: 200px; height: 1px; margin: 0 auto;">
                            <ul style="list-style: none; width: 200px; height: 1px; margin: 0 auto;">
                                <li style="float: left;">
                                    <span class="buttons" style=""><span class="min" style="padding: 4px 10px;cursor: pointer;">-</span></span>
                                </li>
                                <li style="float: left;">
                                    <span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 4px 10px;cursor: pointer;">[ ]</span></span>
                                </li>
                                <li style="float: left;">
                                    <span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 4px 10px;cursor: pointer;">X</span></span>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <header class="modal-h" style="clear: both; margin-top: 20px;">
                        <h2>Pay Bills</h2>
                        <div style="margin-left:180px;">
                            <p>
                                <label for="accounts">Account</label>
                                <span class="select">
								<select id="Paybill_accounts" name="accounts" class="editable-select" onchange="payBill.pbSetAccount($(this).val(), $(this).closest('.select').find('input[type=hidden]').val()  , $(this))">
									<?php foreach ($bankAccounts as $account): ?>
                                        <option value="<?= $account->id ?>"><?php echo $account->name ?></option>
                                    <?php endforeach; ?>
                                    <?php foreach ($CcAccounts as $account): ?>
                                        <option value="<?= $account->id ?>"><?php echo $account->name ?></option>
                                    <?php endforeach; ?>
								</select>
							</span>
                            </p>
                            <p>
                                <label for="payment_type">Payment Method</label>
                                <span class="select">
								<select id="Paybill_method" name="payment_type">
								<?php foreach ($paymentMethods as $paymentMethod): ?>
                                    <option value="<?= $paymentMethod->id ?>"><?php echo $paymentMethod->name ?></option>
                                <?php endforeach; ?>
								</select>
							</span>
                            </p>
                            <p>
                                <label for="pay_bill_date">Date</label>
                                <input type="text" data-toggle="datepicker" name="header[transaction_date]" id="pay_bill_date">
                            </p>
                        </div>
                        <!-- <nav>
                          <ul>
                              <li><span class="buttons" style=""><span class="min" style="padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                              <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                              <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                          </ul>
                      </nav> -->
                    </header>

                    <ul id="radioSelectDiv" class="list-choose">
                        <li>
                            <ul id="searchByDate" searchTerm="all">
                                <li id="dueDateLi">
                                    <label for="dueDate" class="radio">
                                        <input onchange="payBill.setSearchDate($(this), 'selected')" type="radio" id="dueDate" name="dueDate" class="hidden" aria-hidden="true">
                                        <div class="input"></div>
                                        Due date before:
                                    </label>
                                    <span>
									<input data-toggle="datepicker" id="pay_bill_due-date" name=".."
                                            onchange="payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv'), $(this), $(this))">
								</span>
                                </li>
                                <li>
                                    <label for="fbf" class="radio">
                                        <input value="all" type="radio" id="fbf" name="dueDate" class="hidden" aria-hidden="true" checked
                                                onchange="payBill.setSearchDate($(this), 'all'); payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv'), $(this) ) "
                                        >
                                        <div class="input"></div>
                                        All bills
                                    </label>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <ul id="searchByVend" searchTerm="all">
                                <li>
                                    <label for="searchByVendor" class="radio">
                                        <input onchange="payBill.setSearchVendor($(this), 'selected');" type="radio" id="searchByVendor" name="searchByVendor" class="hidden" aria-hidden="true">
                                        <div class="input"></div>
                                        Vendor
                                    </label>
                                    <span id="vendorSelect" class="select">
									<select id="selecetedVendor" class="editable-select" onchange="payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv'), $(this), $(this) )">
									<?php foreach ($vendors as $vendor): ?>
                                        <option value="<?= $vendor->id ?>"><?= $vendor->vendor ?></option>
                                    <?php endforeach; ?>
									</select>
								</span>
                                </li>
                                <li>
                                    <label for="allVendors" class="radio"><input type="radio" id="allVendors" name="searchByVendor" class="hidden" aria-hidden="true" checked
                                                onchange="payBill.setSearchVendor($(this), 'all'); payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv'), $(this) ) ">
                                        <div class="input"></div>
                                        All vendors</label></li>
                            </ul>
                        </li>
                        <li>
                            <ul id="searchByProp" searchTerm="all">
                                <li id="searchByPropertyLi">
                                    <label for="searchByProperty" class="radio">
                                        <input onchange="payBill.setSearchProperty($(this), 'selected');" type="radio" id="searchByProperty" name="searchByProperty" class="hidden" aria-hidden="true">
                                        <div class="input"></div>
                                        Property
                                    </label>
                                    <span id="propertySelect" class="select">
									<select id="selectedProperty" class="editable-select" onchange="payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv'), $(this), $(this) )">
									<?php foreach ($properties as $property): ?>
                                        <option value="<?= $property->id ?>"><?= $property->name ?></option>
                                    <?php endforeach; ?>
									</select>
								</span>
                                </li>
                                <li>
                                    <label for="fbl" class="radio"><input type="radio" id="fbl" name="searchByProperty" class="hidden" aria-hidden="true" checked
                                                onchange="payBill.setSearchProperty($(this), 'all'); payBill.confirmChangeAndFilterApi($(this).closest('#radioSelectDiv'), $(this) )">
                                        <div class="input"></div>
                                        All properties</label></li>
                            </ul>
                        </li>
                    </ul>

                    <div class="has-table-c">

                        <table class="table-c formGridTable mobile-hide dataTable no-footer" style="z-index: 2; " role="grid">
                            <thead class="dataTables_scrollHead">
                                <tr>
                                    <th class="check-a">
                                        <label for="pay_bill_select_all" class="checkbox"><input type="checkbox" id="pay_bill_select_all">
                                            <div class="input"></div>
                                        </label></th>
                                    <th>Vendor</th>
                                    <th>Pmt Account</th>
                                    <th>Property</th>
                                    <th>Reference</th>
                                    <th>Bill date</th>
                                    <th>Due Date</th>
                                    <th>Bill amount</th>
                                    <th>Open balance</th>
                                    <th>Pmt Amount</th>
                                </tr>
                            </thead>

                            <tbody id="payBillBody" class="clickable2" style="">
                            <?php foreach($transactions as $transaction): ?>
						<tr class="pay_bill_row" xonclick="setAccountName($(this))">
								<td id="pay_bill_check">
									<i class="icon-check" id="pay_bill_icon_check" style="visibility :hidden" ></i>
									<input  class="pay_bill_input" type="hidden"   value="0">
								</td>

								<td  style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px" class="" > 
								<input type="hidden" id="transactionId" value="<?= $transaction->id  ?>">
								<input type="hidden" class="paybill_vendor"  value="<?php echo $transaction->profile_id  ?>">
								<span><?php echo $transaction->vendor  ?></span>	 
								
								</td>

								<td  id="Paybill_payment_accounts_row">
                                    <span class="select">
                                        <select id="Paybill_payment_accounts" onchange="payBill.resetName($(this).closest('.pay_bill_row'))" default="<?php echo  $transaction->default_bank;?>" stype="account" class="fastEditableSelect">
                                            
                                        </select>
                                    </span>
								</td>
								<td class="">
									<input type="hidden" class="paybill_property" value="<?php echo $transaction->property_id  ?>">
									<span style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px"><?php echo $transaction->name  ?></span>	
								</td>
								<td style="overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px" class=""><?= $transaction->transaction_ref ?></td>
								<td><?= $transaction->transaction_date ?></td>
								<td><?= $transaction->due_date ?></td>
								<td class=" text-center">$<span id="bill_amount"><?php echo  number_format($transaction->amount, 2); ?></span></td>
								<td>$<span id="open_balance"><?php echo  number_format($transaction->open_balance, 2); ?> </span> </td>
								<td><span class="input-amount">
										<input type="text" placeholder="0.00" id="pay_bill_input_amount"  disabled="disabled">
									</span>
								</td>
							</tr>
						<?php endforeach ; ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td style="text-align: right; padding-right: 0; overflow: visible">Print Checks</td>
                                    <td class="check-a a">
                                        <label for="printPayBillChecks" class="checkbox">
                                            <input type="hidden" name="printPayBillChecks" value="0"/><input type="checkbox" value="1" id="printPayBillChecks" name="printPayBillChecks" class="hidden" aria-hidden="true">
                                            <div style="margin-left: 0;" class="input"></div>
                                        </label>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Totals:</td>
                                    <td>$<span id="total_paybill_bill_amount">0.00</span></td>
                                    <td>$<span id="total_paybill_open_balance">0.00 </span></td>
                                    <td>$<span id="total_paybill_amount_to_pay">0.00 </span></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- <style type="text/css" onload="fastEditableSelect($(this))"></style> -->



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
<div id="check_print" style="display:none;"></div>
</div> <!-- modal -->

<style>

    .rec_row:hover {
        cursor: pointer;
    }

</style>

<script>
        // function fastEditableSelect(t){
        //     var modal = $(t).closest('.modal');
        //     console.log('before');
        //     modal.find('.editableSelect').fastSelect({type: 'account', default: 0});
        //     console.log('after');
        // }

    // var template = function (id, data = {}) {
    //     var newRow = '<tr  class="checkRow pay_bill_row" xonclick="setAccountName($(this))"  id="' + id + '" ' + (data.id ? 'tid="' + data.id + '"' : '') + (data.th_id ? ' data-id="' + data.th_id + '"' : '') + (data.type ? ' data-type="' + data.type + '"' : '') + '>' +
    //         '<td class="just-text" id="pay_bill_check">' +
    //         '<i class="icon-check" id="pay_bill_icon_check" style="visibility :hidden" ></i>' +
    //         '<input  class="pay_bill_input" type="hidden"   value="0">' +
    //         '</td>' +
    //         '<td class="just-text"><input type="hidden" id="transactionId" value="' + (data.id ? data.id : '') + '"><input type="hidden" class="paybill_vendor"  value="' + (data.profile_id ? data.profile_id : '') + '">' + (data.vendor ? data.vendor : '') + '</td>' +
    //         '<td id="Paybill_payment_accounts_row" class="formGridSelectTd" stype="account" source="[sel-id=default_bank]" ' + (data.default_bank ? 'value="' + data.default_bank + '"' : '') + '></td>' +
    //         '<td class="just-text"><input type="hidden" id="name" class="paybill_property" value="' + (data.property_id ? data.property_id : '') + '">' + (data.name ? data.name : '') + '</td>' +
    //         '<td class="just-text" id="transaction_ref"  value="' + (data.transaction_ref ? data.transaction_ref : '') + '">' + (data.transaction_ref ? data.transaction_ref : '') + '</td>' +
    //         '<td class="just-text" id="transaction_date"  value="' + (data.transaction_date ? data.transaction_date : '') + '">' + (data.transaction_date ? data.transaction_date : '') + '</td>' +
    //         '<td class="just-text" id="due_date"  value="' + (data.due_date ? data.due_date : '') + '">' + (data.due_date ? data.due_date : '') + '</td>' +
    //         '<td class="just-text" id="bill_amount" value="' + (data.amount ? data.amount : '') + '">' + (data.amount ? data.amount : '') + '</td>' +
    //         '<td class="just-text" id="open_balance"  value="' + (data.open_balance ? data.open_balance : '') + '">' + (data.open_balance ? data.open_balance : '') + '</td>' +
    //         '<td total="debit"><input type="text" id="pay_bill_input_amount" class="decimal checkAmount total"    placeholder="0"></td>' +
    //         '</tr>';
    //     return newRow;
    // }
    // var grid = $('.modal').last().formGrid({
    //     template: template,
    //     data: < ?php echo $jTransactions; ?>,
    //     minRows: 8
    // });
    // grid.addTotal('debit', '#amount', '#pay_bill_input_amount', '#total_paybill_amount_to_pay');

</script>

