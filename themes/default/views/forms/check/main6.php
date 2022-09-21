<div class="modal flexmodal fade check-modal formGrid <?php echo $edit ?> " id="checkModal" tabindex="-1" role="dialog" main-id=<?= isset($headerTransaction) && isset($header->id) ? $header->id : '-1' ?> type="check" doc-type="transactions" aria-hidden="true" ref-id="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>">
    <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
        <div id="root" class="no-print">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
                <div class="modalContent">
                    <form action="<?php echo $target; ?>" method="post" autocomplete="off" type="4">
                    <input type="hidden" id="saveAndPrint" name="saveAndPrint"  value="0"/>
                        <?php if (isset($headerTransaction) && isset($headerTransaction->id)) {
                            echo '<input type="hidden" name="headerTransaction[id]" value="' . $headerTransaction->id . '"/>';
                            echo '<input type="hidden" name="header[id]" id="transNum"  value="' . $header->id . '"/>';
                            if (isset($headerTransaction->rec_id)) {
                                echo '<input type="hidden" name="headerTransaction[rec_id]" id="rec_id"  value="' . $headerTransaction->rec_id . '"/>';
                            }
                            if (isset($headerTransaction->clr)) {
                                echo '<input type="hidden" name="headerTransaction[clr]" id="clr"  value="' . 1 . '"/>';
                            }
                        } ?>

                        <header class="modal-h">
                            <h2 class="text-uppercase">Check</h2>

                            <label for="bank">
                                <h5>Bank Balance : $<span id="bankBalance"><?php echo $balance ? $balance : '.00' ?> </span>
                                </h5></label>

                            <nav class= 'window-options'>
                                <ul>
                                    <li>
                                        <span class="buttons" style=""><span class="min" style="padding: 8px 20px;cursor: pointer;">_</span></span>
                                    </li>
                                    <li>
                                        <span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span>
                                    </li>
                                    <li>
                                        <span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span>
                                    </li>
                                </ul>
                            </nav>
                            <nav>
                                <ul>
                                    <li><a href="!#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i>
                                            <span>Previous</span></a></li>
                                    <li><a href="!#" class="switchModal" dir="next"><i class="icon-chevron-right"></i>
                                            <span>Next</span></a></li>
                                    <li><?= isset($header) ? '<a href="delete/deleteTransaction/' . $header->id . '" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
                                    <li class="get_send_email_form"><a href="!#"><i class="icon-envelope-outline"></i> <span>Envelope</span></a>
                                    </li>
                                    <li><a href="!#"><i class="icon-brain"></i> <span>Brain</span></a></li>
                                    <li><a href="1#"><i class="icon-documents"></i> <span>Copy</span></a></li>
                                    <li><?= isset($header) ? '<a href="transactions/voidCheck/' . $header->id . '" class="voidCheckButton mr-auto"><i class="fas fa-ban"></i><span>void</span></a>' : '' ?></li>
                                    <li class="getDocuments"><a href="#" class="uploadDocument"><i class="icon-paperclip"></i>
                                            <span>Attach</span></a></li>
                                    <li id="checkButtonClicked">
                                        <a class="" href="!#"><i class="icon-print"></i> <span>Print</span></a></li>
                                </ul>
                            </nav>
                        </header>
                        <section class="a" style="padding-right:50px;">
                            <div class="<?php echo $hasRecId ?>">
                                <?php echo $hasRecIdHtml ?>
                            </div>
                            <div class="double d m20">
                                <div style="margin-left: 30px;">

                                    <p>
                                        <label for="property_id">Property</label>
                                        <span class="select">
								<select id="property_id" name="headerTransaction[property_id]" class="editable-select" onchange="getBank($(this).closest('.select').find('input[type=hidden]').val(), $(this).closest('.modal'));">
								 <option value="-1" selected></option>
                                    <?php
                                    foreach ($properties as $property) {
                                        // echo '<option value="-1" selected >' . "Select Apples" . '</option>';
                                        echo '<option  value="' . $property->id . '" ' . (isset($headerTransaction) && $headerTransaction->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
                                    } ?>
                                
                                </select>
                                </span>
                                    </p>
                                    <p>
                                        <?php echo $checks ?>
                                        <label for="bank">Bank: </label>
                                        <span class="select">
                                    <select id="account_id" name="headerTransaction[account_id]" class="editable-select"
                                            onchange="JS.loadList('api/getBankBalance', $(this).closest('.select').find('input[type=hidden]').val() , '#bankBalance',  $(this).closest('#checkModal')) ;">
                                        <option value="-1" selected> Select Bank </option>
                                        <?php foreach ($banks as $bank): ?>
                                            <option value="<?= $bank->id ?>" <?php echo isset($headerTransaction) && $headerTransaction->account_id == $bank->id ? 'selected' : '' ?> ><?= $bank->name ?></option>
                                        <?php endforeach; ?>
								
								    </select>	
								</span>
                                    </p>
                                    <p>
                                        <label for="profile_id">Payee</label>
                                        <span class="select">
								<select onChange="var id = $(this).closest('.select').find('input[type=hidden]').val();
								$(this).closest('p').find('input#defaccount').val(_.result(_.find(JS.sdata['profile'], ['id', id]),'defaccount'));
								JS.loadList('api/getAddress', id, '#address',  $(this).closest('#checkModal')) ; " id="profile_id" key="profiles.first_name" type="table" name="headerTransaction[profile_id]" modal = "vendor" class="editable-select quick-add set-up">
								<option value="-1" selected>Select Vendor</option>
                                    <?php foreach ($names as $name):
                                        echo '<option  id="' . $name->id . '" value="' . $name->id . '" ' . (isset($headerTransaction) && $headerTransaction->profile_id == $name->id ? 'selected' : '') . '>' . $name->vendor . '</option>';
                                    endforeach; ?>
                                </select>
								</span>
                                        <input type="hidden" id="defaccount" value="-1">
                                    </p>
                                    <div id="address" class="overlay-a text-indent">
                                        <?php if ($address): ?>
                                            <p> <?php echo $address->address_line_1 . ' ' . $address->address_line_2 . '<br>' . $address->city ?> </p>

                                        <?php else: ?>
                                            <p>Please Select Vendor</p>
                                        <?php endif; ?>

                                    </div>

                                </div>
                                <div style="margin-left: 15px;">
                                    <p>
                                        <!-- Important! should be changed to transaction_ref. -->

                                        <label for="check_num">Reference</label>
                                        <input type="text" value="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" id="transaction_ref" name="header[transaction_ref]" placeholder="Check #">
                                    </p>
                                    <p>
                                        <label for="transaction_date">Date</label>

                                        <input data-toggle="datepicker" value="<?= isset($header) && isset($header->date) ? $header->date : '' ?>" id="checkDate" name="header[transaction_date]">
                                        <!-- <input type="hidden" name="header[transaction_date]" value=""/> -->
                                    </p>
                                    <p>
                                        <label for="amount">Amount <span class="prefix">$</span></label>
                                        <input type="text" lang="en" value="<?= isset($headerTransaction) && isset($headerTransaction->credit) ? $headerTransaction->credit : '' ?>" id="amount" name="headerTransaction[credit]" placeholder="0.00" class="decimal formatCurrency topAmount">
                                    </p>
                                    <p>
                                        <label for="class_id">Class</label>
                                        <span class="select">
                                    <select id="class_id" name="headerTransaction[class_id]" class="editable-select">
                                    <?php foreach ($classes as $class):
                                        echo'<option value="-1" selected> Select Class </option>';
                                        echo '<option value="' . $class->id . '" ' . (isset($headerTransaction) && $headerTransaction->class_id == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
                                    endforeach; ?>
                                    </select>
                                </span>
                                    </p>
                                    
                                    <?php echo $RecIdHtml ?>
                                    
                                </div>
                            </div>
                            <p>
                                <label for="memo">Memo:</label>
                                <input type="memo" value="<?= isset($header) && isset($header->memo) ? $header->memo : '' ?>" id="memo" name="header[memo]" placeholder="Enter Memo">
                            </p>

                        </section>

                        <div class="has-table-c">

                            <table class="table-c billTable mobile-hide dataTable no-footer formGridTable">
                                <thead class="dataTables_scrollHead">
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

                                <tbody id="checkFormBody" class="dataTables_scrollBody">
                                </tbody>
                                <tfoot>

                                    <tr>
                                    <td style="text-align: right; padding-right: 0; overflow: visible">To be printed</td>
                                        <td class="check-a a">
                                            <!--<ul class="check-a a">
                                                <li>-->
                                                    <label for="printCheck" class="checkbox <?= isset($header) && ($header->to_print == 0) ? '' : 'active' ?>">
                                                        <input type="hidden" name="header[to_print]" value="0"/><input type="checkbox" value="1" <?= isset($header) && ($header->to_print == 0) ? '' : 'checked' ?> id="printCheck" name="header[to_print]" class="hidden" aria-hidden="true">
                                                        <div style="margin-left: 0;" class="input"></div>
                                                    </label>
                                                <!--</li>-->

                                                <!-- <li><label for="printCheck" class ="checkbox active">
                                                  <input type="checkbox"  id="printCheck" name="headerTransaction[to_print]"  class="hidden" aria-hidden="true">
                                                  To be printed

                                                </label> -->
                                                <!--</li>
                                            </ul>-->

                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">Total:</td>
                                        <td class="text-center"><span class="text-left">$</span>
                                            <span id="totalAmount"><?php echo $checkTotal > 0 ? $checkTotal
                                                    : '.00 ' ?></span></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>


         
     
        <footer>
          <ul class="list-btn">
          <li><button class="saveAndPrint" type="submit" after="mclose">Save &amp; print</button></li>
            <li><button type="submit" after="mnew">Save &amp; New</button></li>
            <li><button type="submit" after="mclose">Save &amp; Close</button></li>
            <li><button type="submit" after="duplicate">Duplicate</button></li>
            <li><button type="button">Cancel</button></li>
            
          </ul>
		  <?= $header ?
          "<ul>
		  	<li>Last Modified $header->modified</li>
            <li>Last Modified by $header->user</li>
          </ul>" : ''; ?>
                    </footer>
                </form>
             </div>

            </div>
        </div>

    </div>
    <!-- print section -->



</div>

<script type="text/javascript">
    var template = function (id, data = {}) {
        var newRow = '<tr class="checkRow" id="' + id + '" ' + (data.id ? 'tid="' + data.id + '"' : '') + ' ' + (data.property_id && data.property_id != '-1' ? 'property_id="' + data.property_id + '"' : '') + '>' +
            '<td class="formGridSelectTd" stype="account" source="#defaccount" ' + (data.account_id ? 'value="' + data.account_id + '"' : '') + '></td>' +
            '<td class="formGridSelectTd" stype="property" source="[sel-id=property_id]" ' + (data.property_id ? 'value="' + data.property_id + '"' : '') + '></td>' +
            '<td class="formGridSelectTd" stype="unit" sclasses="es-setup" smodal="units" skey="units.name" ' + (data.unit_id ? 'value="' + data.unit_id + '"' : '') + '></td>' +
            '<td><input type="text" id="description" name="transactions[' + id + '][description]" value="' + (data.description ? data.description : '') + '"></td>' +
            '<td total="debit" source="#amount"><input type="text" class="decimal checkAmount total" id="amount" name="transactions[' + id + '][debit]" value="' + (data.debit || data.credit ? data.debit - data.credit : '') + '" placeholder="0"></td>' +
            '<td class="formGridSelectTd" stype="class" sclasses="es-add" source="[sel-id=class_id]" ' + (data.class_id ? 'value="' + data.class_id + '"' : '') + '></td>' +
            '<td class="formGridSelectTd" stype="profile" value="' + (data.profile_id ?  data.profile_id + (data.lease_id ? "-" + data.lease_id  : '') : '') + '"><span class="select"><input></span></td>' +
            '</tr>';
        return newRow;
    }
    var grid = $('.modal').last().formGrid({
        template: template,
        data: <?php echo $jTransactions ? $jTransactions : 0 ?>,
        minRows: 8
    });
    grid.addTotal('debit', '#amount', '#amount', '#totalAmount');

    

</script>

<script type="text/javascript">

    function checkPrintInit(e) {
        var that = e.target;
        var transactions2 = [];
        var that2 = that;
        //console.log(that2);
        var headerTransaction_id = $(that).closest('.modal').find("section:first").find('#account_id').closest('.select').find('input[type="hidden"]').val();
        var transNum = $(that).closest('.modal').find('#transNum').val();

        transactions2.push({'th_id': transNum, 'id': headerTransaction_id});


        $.post(JS.baseUrl + "transactions/onPrint", {
            'params': JSON.stringify(transactions2)
        }, function (result) {
            //console.log(result);
            //console.log("result");
            var result2 = JSON.parse(result);
            //console.log(result2[0].next_check_num);
            //getcheckNumber(result2[0].next_check_num, result2, that2);
            //checkprint(result2)
            printChecks.checkPrint(result2, $(that2).closest('.modal'), $("#Checkarea"));

        })

        //console.log(transactions2);

    }

    function getBank(propertyId, modal){
                $.get(JS.baseUrl + "checks/getDefaultBank/"+ propertyId,  function (result) {
            console.log(result);
            var result2 = JSON.parse(result);
            $(modal).find('#account_id').closest('.select').find('input').val(result2.name);
            $(modal).find('#account_id').closest('.select').find('input[type=hidden]').val(result2.id);
        })
    }

</script>
