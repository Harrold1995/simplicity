<div class="modal flexmodal fade reconciliation-modal <?php echo $edit ?>" data-rec-type = <?php echo $rectype ?> id="reconciliationModal" tabindex="-1" role="dialog" main-id=<?= isset($reconciliation) && isset($reconciliation->r_id) ? $reconciliation->r_id : '-1' ?> type="reconcilliation" ref-id="" aria-hidden="true" ">
<div class="modal-dialog modal-dialog-centered modal-xl " role="document">
    <div id="root">

        <div class="modal-content text-primary popup-a  shown theme-c">
            <form action="<?php echo $target; ?>" method="post" type="reconciliations">
                <article class="module-rec">
                    <header class="modal-h" style="padding-bottem:20px">
                        <form>
                            <h1>Reconciliation</h1>
                            <nav>
                                <ul>
                                    <li>
                                        <span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span>
                                    </li>
                                    <li>
                                        <span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span>
                                    </li>
                                    <li>
                                        <span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span>
                                    </li>
                                </ul>
                            </nav>

                            <p>
                                <label for="account_id">Account</label>
                                <!-- <input type="text" id="account_id"  required="" value="< ?= $account->name?>" >
                                <input type="hidden"  name="reconciliation[account_id]" required="" value="< ?= $account->id?>"> -->
                                <select id="account_id" class="editable-select" name="reconciliation[account_id]" onchange="reconciliation2.getAccountInfo($(this).closest('p').find('input[type=hidden]').val(), $(this).closest('.modal'), 'first');">
                                    <?php
                                    foreach ($accounts as $singleAccount) {
                                        echo '<option value="' . $singleAccount->id . '" ' . (isset($account) && $account->id == $singleAccount->id ? 'selected' : '') . '>' . $singleAccount->name . '</option>';
                                    } ?>
                                </select>
                            </p>
                            <p class="is-date">
                                <label for="statement_end_date">Statement end date</label>
                                <input data-toggle="datepicker" id="statement_end_date" value="<?= isset($reconciliation) && isset($reconciliation->statement_end_date) ? $reconciliation->statement_end_date : date('Y-m-d') ?>" name="reconciliation[statement_end_date]" required="">
                            </p>
                            <!--ul class="check-a">
                                <li>
                                    <label for="mrc" class="radio"><input type="radio" id="mrc" name="mrc" class="hidden" aria-hidden="true">
                                        <div class="input"></div>
                                        Manual</label></li>
                                <li>
                                    <label for="mrd" class="radio"><input type="radio" id="mrd" name="mrc" class="hidden" aria-hidden="true">
                                        <div class="input"></div>
                                        Import</label></li>
                            </ul -->
                            <?php if (isset($reconciliation) && ($reconciliation->statement_attachment != "")) { ?>
                                <div>
                                    <a href="<?php echo base_url() . 'uploads/documents/' . $reconciliation->statement_attachment ?>" target="_blank"><?= isset($reconciliation->statement_attachment) ? $reconciliation->statement_attachment : ''; ?>
                                    </a>
                                    <p class="input-file">
                                        <label for="p-image"><input type="file" name="statement_attachment" id="p-image" targetimg="#statement_attachment-lease">
                                            <span>Edit</span></label>
                                    </p>
                                </div>
                            <?php } else { ?>
                                <!-- <p><button type="submit">Attach Statement</button></p> -->
                                <p class="input-file">
                                    <label for="p-image"><input type="file" name="statement_attachment" id="p-image" targetimg="#statement_attachment-lease">
                                        <span>Attach Statement</span></label>
                                </p>
                            <?php } ?>
                        </form>
                    </header>
                    <input type="hidden" id="closeRec" name="closed" value="false">
                    <div id="recId">
                        <?php if ($reconciliation->r_id != null): ?>

                            <input type="hidden" name="reconciliation[id]" value="<?= $reconciliation->r_id ?>">
                        <?php endif; ?>
                    </div>

                    <ul class="list-rec">

                        <li><label for="rec_begin_bal">Beginning Balance</label>
                            <input type="text" id="rec_begin_bal" name="reconciliation[beginning_bal]" value="<?= $reconciliation->beginning_bal ?>" readonly>
                        </li>
                        <li><p id="cleared_payments">0</p> <span> <?= $rectype == 1 ? 'Cleared Payments' : 'Cleared Charges' ?>  </span>
                            <p id="payment_total">0</p></li>
                        <li><p id="cleared_deposits">0</p> <span> <?= $rectype == 1 ? 'Cleared Deposits' : 'Cleared Credits/Payments' ?>  </span>
                            <p id="debit_total">0</p></li>
                        <li>
                            <label for="ending_balance">Ending Balance</label>
                            <input type="text" id="ending_balance" name="reconciliation[ending_bal]" value="<?= $reconciliation->statement_bal ?>" required="">
                        </li>
                        <?php if ($rectype == 1) { ?>
                        <li>
                            <?php if (!isset($account->property) or $account->property == 0) {
                                echo '<span class ="withProp" style = "display:none">';
                            } else {
                                echo '<span class ="withProp">';
                            } ?>
                            <?php if (isset($account->property) && $account->property != 0) {
                                echo '<input type ="hidden" name = "property" value="' . $account->property . '">';
                            } ?>
                            <label for="interest_earned">Interest Earned</label>
                            <input type="text" id="interest_earned" name="reconciliation[interest_earned]" value="<?= $reconciliation->interest_earned ?>" required="">
                            </span>
                            <?php if (!isset($account->property) or $account->property == 0) {
                                echo '<span class ="withProp" style = "display:none">';
                            } else {
                                echo '<span class ="withProp">';
                            } ?>
                            <label for="service_charge">Service Charge</label>
                            <input type="text" id="service_charge" name="reconciliation[service_charge]" value="<?= $reconciliation->service_charge ?>" required="">
                            </span>
                            <?php if (isset($account->property) && $account->property != 0) {
                                echo '<span class ="withNoProp" hidden>';
                            } else {
                                echo '<span class ="withNoProp">';
                            } ?>
                            <label for="property">choose a property to be associated with this bank in order to record Service Charge & interest</label>
                            <select stype="property" class="fastEditableSelect" key="properties.name" modal="property" id="property_select1" name="property_id"></select>
                            <a id="" class="withNoProp property_select">Ok</a>

                            </span>

                            <?php if (isset($account->property) && $account->property != 0) {
                                echo '<input name="property_id" hidden value = "' . $account->property . '">';
                            } ?>
                        </li>

                        <?php } ?>
                        <li><span>Difference</span>
                            <p id="rec_diff">00.00</p></li>
                    </ul>
                    <div class="rec-outer double">
                        <div>

                            <header>
                                <h2><?= $rectype == 1 ? 'Payments/charges' : 'Charges' ?></h2>
                                <p class="input-search mb-0">
                                    <label for="mrh">Search</label>
                                    <input type="text" id="crsearch" name="mrh" placeholder="search">
                                    <a href="./" class="btn">Search</a>
                                </p>
                            </header>
                            <div class="table-wrapper table-d-wrapper last-child has-data-table">
                                <div class="rec-table" id="credittable-<?php echo $account->id?>">
                                </div>
                                <script>
                                    var credit = new RecSlick("#credittable-<?php echo $account->id?>", {
                                        dataUrl: 'reconciliations/getCreditsAjax/<?php echo $account->id ?>',
                                        total: '#payment_total',
                                        number: '#cleared_payments',
                                        difference: '#rec_diff',
                                        search: '#crsearch',
                                        differenceCallback: function() {}
                                    });
                                </script>
                            </div>
                        </div>

                        <div>

                            <header>
                                <h2><?= $rectype == 1 ? 'Deposits/debits' : 'Credits/Payments' ?></h2>
                                <p class="input-search mb-0">
                                    <label for="mrh">Search</label>
                                    <input type="text" id="dsearch" name="mrh">
                                    <a href="./" class="btn">Search</a>
                                </p>

                            </header>
                            <div class="table-wrapper table-d-wrapper last-child has-data-table">
                                <div class="rec-table" id="debittable-<?php echo $account->id?>">
                                </div>
                                <script>
                                    var debit = new RecSlick("#debittable-<?php echo $account->id?>", {
                                        dataUrl: 'reconciliations/getDebitsAjax/<?php echo $account->id ?>',
                                        total: '#debit_total',
                                        number: '#cleared_deposits',
                                        difference: '#rec_diff',
                                        search: '#dsearch',
                                        differenceCallback: function() {}
                                    });
                                   
                                    var callback = function() {
                                        var rectype = <?php echo $rectype ?>;
                                        var diff = 0 +  (Number(credit.modal.find('#rec_begin_bal').val()) - Number(credit.modal.find('#ending_balance').val()) - (credit.total ? credit.total : 0) + (debit.total ? debit.total : 0) + Number(credit.modal.find('#interest_earned').val()) - Number(credit.modal.find('#service_charge').val()) + 0);
                                        if(rectype == 6){diff = 0 +  (Number(credit.modal.find('#rec_begin_bal').val()) - Number(credit.modal.find('#ending_balance').val()) - (debit.total ? debit.total : 0) + (credit.total ? credit.total : 0) + 0);} 
                                        credit.modal.find('#rec_diff').text(diff.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
                                        if(diff.toFixed(2) == 0){
                                            credit.modal.find('#closeRec').val('true')
                                            credit.modal.find('#rec_submit').text('Submit Reconciliation')

                                        }else {
                                            credit.modal.find('#closeRec').val('false')
                                            credit.modal.find('#rec_submit').text('Save for later')
                                        }
                                    };
                                    credit.modal.on('input', 'input', callback);
                                    debit.setCallback(callback);
                                    credit.setCallback(callback);
                                </script>
                            </div>
                        </div>
                    </div>

                    <div class="submit">
                        <button type="submit" id="rec_submit"  data-type = 'auto'>Save for later</button>
                    </div>
                    <div id="refreshRec" data-type = 'manual'>refresh</div>

                </article>
            </form>

        </div>  <!-- modal-content -->
    </div> <!-- id="root" -->
</div> <!-- modal-dialog -->
</div> <!-- modal -->

<script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/custom2.js"></script>

<style>

    .rec_row:hover {
        cursor: pointer;
    }

</style>

<script>

    function recClick(modal) {
        /*var neg = <?php echo $totalNeg ?>;
        //var pos = <?php echo $totalPos ?>;
        //var negCount = <?php echo $negCount ?>;
        //var posCount = <?php echo $posCount ?>;
        var begBal = <?php echo $reconciliation->beginning_bal ?>;
        var endBal = <?php echo $reconciliation->statement_bal ?>;
        var interest = <?php echo $reconciliation->interest_earned ?>;
        var sc = <?php echo $reconciliation->service_charge ?>;
        var diff = endBal - (begBal + pos - neg - sc + interest);
        console.log("neg:", neg, " pos:", pos);
        $(modal).closest('#reconciliationModal').find('#debit_total').text(pos.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
        $(modal).closest('#reconciliationModal').find('#payment_total').text(neg.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
        $(modal).closest('#reconciliationModal').find('#cleared_payments').text(negCount);
        $(modal).closest('#reconciliationModal').find('#cleared_deposits').text(posCount);
        $(modal).closest('#reconciliationModal').find('#rec_diff').text(diff);
        //$(modal).find('.clickThis').trigger('click');
        //  $(modal).closest('.modal').find('#undepositedClick').find('.depositForm_amount').trigger('keyup');*/
    }


    $('.property_select').click(function () {
        if ($(this).closest('.modal').find('#property_select1').val().length > 0) {
            $(this).closest('.modal').find('.withNoProp').hide();
            $(this).closest('.modal').find('.withProp').show();
        } else {
            alert('you need to select a property first');
        }
    });

    $('input[type="file"]').change(function (e) {
        var fileName = e.target.files[0].name;
        $(this).closest('label').find('span').html("Edit");
        $(this).closest('div').find("a").remove();
        $(this).closest('p').find('.display_name').remove();
        $(this).closest('p').prepend("<span class='display_name'>" + fileName + "</span>");
    });


</script>