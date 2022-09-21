<div class="modal flexmodal fade vendorPayBill-modal" id="vendorPayBillModal" tabindex="-1" role="dialog" main-id=<?= isset($reconciliation) && isset($reconciliation->r_id) ? $reconciliation->r_id : '-1' ?> type="pay-bills" ref-id="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl theme-c " role="document">
        <div id="root" class="no-print">
            <div class="modal-content text-primary form-bills form-entry popup-a  shown" style=" width:100%; padding: 30px; padding-top: 2px; ">
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
								<select id="paybill_accounts" name="accounts" class="editable-select">
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
                    <div class="flex-container header" style="display: flex; width:100%">
                        <div style="width: 30%; margin: 10px;">
                            <ul id="radioSelectDiv">
                                <li class="list-choose">
                                    <ul id="searchByDate" searchTerm="all">
                                        <li id="dueDateLi">
                                            <label for="dueDate" class="radio">
                                                <input type="radio" id="dueDate" value="1" name="dueDate" class="hidden" aria-hidden="true">
                                                <div class="input"></div>
                                                Due date before:
                                            </label>
                                            <span>
                                                <input data-toggle="datepicker" id="pay_bill_due_date" class="leaveEmpty"/>
                                            </span>
                                        </li>
                                        <li>
                                            <label for="fbf" class="radio">
                                                <input type="radio" id="fbf" value="0" name="dueDate" class="hidden" aria-hidden="true" checked>
                                                <div class="input"></div>
                                                All bills
                                            </label>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <div style="width: 33%; margin: 10px;" class="checkboxGroup" id="propertyBlock">

                        </div>
                        <script>
                            var prcheck = new CheckboxBlock('#propertyBlock', {source: 'eproperty', filter: 'property_id', title: 'Properties', modal: $(this).closest('.modal')});
                        </script>

                        <div style="width: 33%; margin: 10px;" class="checkboxGroup" id="vendorsBlock">
                        </div>
                        <script>
                            var vcheck = new CheckboxBlock('#vendorsBlock', {source: 'vendors', filter: 'profile_id', title: 'Vendors', modal: $(this).closest('.modal')});
                        </script>

                    </div>

                    <div class="formGridSlickTable mobile-hide" style="z-index: 2;height:99%; ">

                    </div>

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
    var template = function(data) {
        var result = [];
        result.push(['transactions1['+(data.profile_id+data.account_id+data.property_id)+']['+data.id+'][transaction_id_b]', data.id]);
        result.push(['transactions1['+(data.profile_id+data.account_id+data.property_id)+']['+data.id+'][profile_id]', data.profile_id]);
        result.push(['transactions1['+(data.profile_id+data.account_id+data.property_id)+']['+data.id+'][account_id]', data.account_id]);
        result.push(['transactions1['+(data.profile_id+data.account_id+data.property_id)+']['+data.id+'][property_id]', data.property_id]);
        result.push(['transactions1['+(data.profile_id+data.account_id+data.property_id)+']['+data.id+'][amount]', data.amount]);

        return result;
    };
    var grid = $('.modal').last().formGridSlick({
        template:  template,
        dataUrl: 'transactions/payBillsGetData',
        type: 'payBills',
        showFooterRow :true
    });
    prcheck.addChangeCallback(grid.checkboxChanged.bind(grid));
    vcheck.addChangeCallback(grid.checkboxChanged.bind(grid));
    //payBill.setTransactionsProperties(< ?php echo $jTransactions; ?>);
    //console.log('pay bills')


</script>

