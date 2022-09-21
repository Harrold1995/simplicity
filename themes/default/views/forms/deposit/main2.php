<div class="modal flexmodal fade deposit-modal <?php echo $edit ?>" id="depositModal" tabindex="-1" role="dialog" type="deposit" ref-id="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl theme-c " role="document">
        <div id="root">

            <div class="modal-content text-primary form-bills form-entry popup-a  shown " style=" width:100%; padding: 30px 30px 15px 30px;">
                <form action="<?php echo $target; ?>" method="post" type="8" autocomplete="off">
                    <?php if (isset($header) && isset($header->id)) {
                        echo '<input type="hidden" id="transNum" name="header[id]" value="' . $header->id . '"/>';
                    } ?>
                    <div class="<?php echo $hasRecId ?>">
                        <?php echo $hasRecIdHtml ?>
                    </div>
                    <header class="modal-h primary">
                        <h2>Deposit</h2>
                        <nav class="window-options">
                            <ul>
                                <li><span class="buttons" style=""><span class="min">_</span></span></li>
                                <li><span class="buttons" style=""><span class="max">[ ]</span></span></li>
                                <li><span class="buttons" style=""><span class="close2">X</span></span></li>
                            </ul>
                        </nav>

                        <nav class="window-buttons">
                            <ul>
                                <li><?= isset($header) ? '<a href="delete/deleteTransaction/' . $header->id . '" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
                                <!--li><a href="./"><i class="icon-envelope-outline" aria-hidden="true"></i> <span>Envelope</span></a></li-->
                                <li><a href="./"><i class="icon-brain" aria-hidden="true"></i> <span>Brain</span></a>
                                </li>
                                <li><a href="./"><i class="icon-documents" aria-hidden="true"></i> <span>Copy</span></a>
                                </li>
                                <li><a href="./"><i class="icon-paperclip" aria-hidden="true"></i>
                                        <span>Attach</span></a></li>
                                <!--li><a class="print" href="./"><i class="icon-print" aria-hidden="true"></i> <span>Print</span></a></li-->
                            </ul>
                        </nav>
                        <br>
                    </header>
                    <header class="modal-h secondary">

                        <div>
                            <p>
                                <label for="fba">Account</label>

                                <span stype="account" class='select'>
                                        
                                        <input  hidden-name = 'account_id' name="account_id" class="fastEditableSelect es-default" filter_key ='details' filter_value = 'BK' stype="account" value="<?php echo isset($account_id) && $account_id !='' ?  $account_id  : 'Default bank'; ?>"  key="accounts.name" modal="account"  default="<?php echo isset($account_id) && $account_id != '' ?  $account_id  : '-1'; ?>"></input>
                                </span>
                                </p>
                            <!-- have to look in to this if someone wants to change properties
                            <p>
                            <select stype="property" class="fastEditableSelect" key="properties.name" modal="property" id="property_select1" name="property_id"></select>
                            -->
                            <p> 
                            
                                <label for="memo">Memo</label>
                                <input type="text" value="<?= isset($header) && isset($header->memo) ? $header->memo : '' ?>" id="memo" name="header[memo]">
                            </p>
                            <p>
                                <label for="depositDate">Date</label>
                                <input data-toggle="datepicker" id="depositDate" value="<?php echo $header->date ?>" name="header[transaction_date]" required>
                            </p>
                        </div>

                    </header>

                    <div class="main-deposit-container">
                        <div class="cols-d">
                            <div id="select_check_section">
                                <table class="table-c d da db text-center">
                                    <thead style="border-radius:6px">
                                        <tr>
                                            <th width="4%" class="check-a" rowspan="1" colspan="1" >
                                                <label for="depositCheckAll" class="checkbox">
                                                    <input type="checkbox" id="depositCheckAll" class="hidden" onchange=" depositSelectAllDeposits($(this))" aria-hidden="true">
                                                </label>
                                            </th>
                                            <th width="16%">Property</th>
                                            <th width="16%">Reference</th>
                                            <th width="10%">Date</th>
                                            <th width="16%">Name</th>
                                            <th width="26%">Memo</th>
                                            <th width="12%">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="undepositedChecksBody" style="box-shadow: 1px 0 15px rgba(0,0,0,.16);border-radius: 6px;margin-top: 8px;">
                                        <?php echo "<script>console.log('" . json_encode($undepositedChecks) . "')</script>"; ?>

                                        <?php foreach ($undepositedChecks as $undepositedCheck): ?>

                                            <tr id="deposit_check"  data-type="<?php echo $undepositedCheck->type_id ?>" data-mode="edit" data-id="<?php echo $undepositedCheck->tid ?>" class="deposit_row" id="<?php echo $undepositedCheck->id ?>" property-id="<?php echo $undepositedCheck->property_id ?>" amount="<?php echo $undepositedCheck->amount ?>">
                                                <td style ="max-width:4%" class="<?php echo ($undepositedCheck->deposit_id != null) ? 'clickThis' : ''; ?>">
                                                    <i class="icon-check" id="deposit_icon_check" style="display :none"></i>
                                                    <input id="undeposited_id" type="hidden" value="<?php echo $undepositedCheck->amount ?>">
                                                    <input id="checked_id" type="hidden" value="">
                                                </td>
                                                <td style ="max-width:16%"><?php echo $undepositedCheck->property ?></td>
                                                <td style ="max-width:16%"><a href = '#' class='transLink' data-type='5' data-id='<?php echo $undepositedCheck->tid ?>'><?php echo $undepositedCheck->transaction_ref ?></a></td>
                                                <td style ="max-width:10%"><?php echo $undepositedCheck->date ?></td>
                                                <td style ="max-width:16%"><?php echo $undepositedCheck->tenant ?></td>
                                                <td style ="max-width:26%"><?php echo $undepositedCheck->memo ?></td>
                                                <td style ="max-width:12%"><span class="text-left">$</span>
                                                    <span class="deposit_row_amount"><a class='transLink' data-type='5' data-id='<?php echo $undepositedCheck->tid ?>'><?php echo $undepositedCheck->amount ?></a></span>
                                                </td>
                                            </tr>

                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <ul class="list-total a">
                                    <li><span>Totals:</span><span id="amount_of_selected_checks">0</span></li>
                                    <!-- make the payment amount a input name="amount" -->
                                    <li class="wide"><span>Payments:</span> <span class="text-left">$</span>
                                        <span id="total_of_deposit_checks"> 0.00</span></li>
                                </ul>
                            </div>
                            <aside >
                                <h3 class="header-th">
						<span class="check-a">
							<label for="dSelectAllProperties" class="checkbox active">
								 <input type="checkbox" id="dSelectAllProperties" class="selectAllCheckboxes" onchange="depositForm.selectAllProperties($(this))">
								 <div class="input"></div>
							</label>
						</span> Properties
                                </h3>
                                <ul class="check-a b" style="max-height: 88%;">
                                    <?php foreach ($properties as $property): ?>
                                        <li>
                                            <label for="d<?= $property->id ?>" class="checkbox active"><input type="checkbox" id="d<?= $property->id ?>" class="checkboxClicker allAccounts" name="property"
                                                        onchange="Checkbox($(this)); depositForm.filterProperty('<?= $property->id ?>', $(this))"><?php echo $property->name ?>
                                                <div class="input"></div>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </aside>
                            <!--WIP deposit form layout fix -->
                            <!--aside style="height: 34%;">
                                <ul class="check-a b" style ="zoom:.75">
                                <li>
                                    <label for="fba">Account</label>
                                    <span class="select ">
                                    <select  name="account_id" class="editable-select">
                                        <option value="-1" selected>Default account</option>
                                    < ?php foreach($banks as $bank)
                                    echo '<option value="' . $bank->id . '" ' . (isset($account_id) && $account_id == $bank->id ? 'selected' : '') . '>' . $bank->name . '</option>';
                                        ?>
                                    </select>
                                    </span>
                            </li>
                                <li>
                                   <label for="memo">Memo</label>
                                    <input type="text" value="< ?= isset($header) && isset($header->memo) ? $header->memo : '' ?>" id="memo"name="header[memo]" >
                            </li>
                                <li>
                                    <label for="depositDate">Date</label>
                                    <input data-toggle="datepicker" id="depositDate" value="< ?php echo $header->date  ?>" name="header[transaction_date]" required>
                            </li>
                                </ul>
                            </aside-->
                        </div>
                        <table class="table-c  da formGridTable" >
                            <thead style=" border-radius:6px">
                                <tr>
                                    <th>Account</th>
                                    <th>Property</th>
                                    <th>Unit</th>
                                    <th>Description</th>
                                    <th class="text-center">Amount</th>
                                    <th>Class</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody id="depositBody" style="box-shadow: 1px 0 15px rgba(0,0,0,.16);border-radius: 6px;margin-top: 8px;">

                            </tbody>
                        </table>
                    </div>

                    <footer>
                        <ul class="list-total">
                            <li><span>Totals other deposits</span>
                                <span class="text-left">$</span><span id="deposit-bottom-total">0.00</span></li>
                            <!-- make the total deposit of the tob and bottem totals and submit name="totalAmount" -->
                            <li><span>Totals Deposit</span> <span class="text-left">$</span>
                                <span id="deposit-total">0.00</span>
                            </li>
                        </ul>
                        <p>
                            <button type="submit" style="padding: 7px;">Deposit</button>
                            <button type="submit" style="padding: 7px;" after="mnew">Save &amp; New</button>
                            <button type="button" style="padding: 7px;">Cancel</button>
                        </p>
                        <?= $header ?
                            "<ul>
						<li style='list-style:none;'>Last Modified $header->modified</li>
						<li style='list-style:none;'>Last Modified by $header->user</li>
					</ul>" : '';
                        ?>

                    </footer>
                    <style type="text/css" onload="depositClick($(this).closest('.modal'))"></style>
                    <script src="<?php echo base_url(); ?>themes/default/assets/javascript/custom2.js"></script>
                    <input type="hidden" name="amount" id="hidden-deposit-top-amount" value="0">
                    <input type="hidden" name="totalAmount" id="hidden-deposit-total-amount" value="0">

                </form>
            </div>  <!-- modal-content -->
        </div> <!-- id="root" -->
    </div> <!-- modal-dialog -->
</div> <!-- modal -->

<style>

    .rec_row:hover {
        cursor: pointer;
    }

</style>

<script>

</script>

<script>
    var properties = <?php echo $jProperties ? $jProperties : '0'?>;
    var accounts = <?php echo $jAccounts ? $jAccounts : '0' ?>;//not used anymore, using subaccounts
    var subaccounts = <?php echo $jsubaccounts ? $jsubaccounts : '0' ?>;
    var units = <?php  echo $jsubunits ? $jsubunits : '0' ?>;
    var propertyAccounts = <?php echo $jPropertyAccounts; ?>;
    var names = <?php echo $jNames; ?>;
    var classes = <?php echo $jClasses; ?>;

    console.log('deposit form');

    //$(document).ready(function () {
    function depositClick(modal) {
        $(modal).find('.clickThis').trigger('click');
        $(modal).closest('.modal').find('#undepositedClick').find('.depositForm_amount').trigger('keyup');
        $(modal).closest('.modal').find('#depositBody').find('#amount').trigger('focusout');

        
    }

    //});

    var template = function (id, data = {}) {
        var newRow = '<tr class="checkRow" id="' + id + '" ' + (data.id ? 'tid="' + data.id + '"' : '') + ' ' + (data.property_id && data.property_id != '-1' ? 'property_id="' + data.property_id + '"' : '') + '>' +
            '<td class="formGridSelectTd" stype="account" ' + (data.account_id ? 'value="' + data.account_id + '"' : '') + '></td>' +
            '<td class="formGridSelectTd" stype="property" source="[sel-id=property_id]" ' + (data.property_id ? 'value="' + data.property_id + '"' : '') + '></td>' +
            '<td class="formGridSelectTd" stype="unit" ' + (data.unit_id ? 'value="' + data.unit_id + '"' : '') + '></td>' +
            '<td><input type="text" id="description" name="transactions[' + id + '][description]" value="' + (data.description ? data.description : '') + '"></td>' +
            '<td total="debit"><input type="text" source="#amount" class="decimal checkAmount total" id="amount" name="transactions[' + id + '][credit]" value="' + (data.amount ? data.amount : '') + '" placeholder="0"></td>' +
            '<td class="formGridSelectTd" stype="class" source="[sel-id=class_id]" ' + (data.class_id ? 'value="' + data.class_id + '"' : '') + '></td>' +
            '<td class="formGridSelectTd" stype="profile" ' + (data.profile_id ? 'value="' + data.profile_id + '"' : '') + '></td>' +
            '</tr>';
        return newRow;
    }
    var grid = $('.modal').last().formGrid({
        template: template,
        data: <?php echo $jTransactions ? $jTransactions : 0 ?>,
        minRows: 8
    });
    grid.addTotal('credit', null, '#amount', '#deposit-bottom-total');
</script>