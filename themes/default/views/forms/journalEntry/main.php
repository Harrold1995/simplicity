<?php
$debitTotal = 0;
$creditTotal = 0;

?>
<div class="modal flexmodal fade journalEntry-modal formGrid" id="journalEntryModal" tabindex="-1" doc-type="transactions" role="dialog" main-id=<?= isset($journalEntry) && isset($journalEntry->id) ? $journalEntry->id : '-1' ?> type="JournalEntry" ref-id="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
        <div id="root">

            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
                  <form action="<?php echo $target; ?>" method="post" autocomplete="off" type="1">
                      <input type="hidden" name="header[transaction_type]" value="1"/>
                      <?php if(isset($header) && isset($header->id)){
                                    echo '<input type="hidden" name="header[id]" value="' . $header->id . '"/>';
                        } ?>

                      <header class="modal-h">
          <h2>Journal entry</h2>

          <ul class="check-a">
          <?php $both = 'checked';
                $cash = '';
                $accural = '';
           if(isset($header) && isset($header->basis) && $header->basis == 2){$both = ''; $cash = 'checked';}
           if(isset($header) && isset($header->basis) && $header->basis == 1){$both = ''; $accural = 'checked';}
					?>
            <li><label for="fea"> Cash</label><input type="radio" id="fea" name="header[basis]" value="2" <?php echo $cash; ?>></li>
            <li><label for="feb"> Accural</label><input type="radio" id="feb" name="header[basis]" value="1"<?php echo $accural; ?>></li>
            <li><label for="fec"> Both</label><input type="radio" id="fec" name="header[basis]" value="0"<?php echo $both; ?>></li>
          </ul>
          <nav>
              <ul>
                  <li><span class="buttons" style=""><span class="min" style=";padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                  <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                  <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
              </ul>
          </nav>
          <nav>
            <ul>
              <li><a href="#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
              <li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
              <li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
              <li class="get_send_email_form"><a href="#"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
              <li><a href="#"><i class="icon-brain"></i> <span>Brain</span></a></li>
              <li><a href="#"><i class="icon-documents"></i> <span>Copy</span></a></li>
              <li class="getDocuments"><a href="#" class="uploadDocument"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
              <li><a class="print" href="#"><i class="icon-print"></i> <span>Print</span></a></li>
            </ul>
          </nav>
        </header>
        <section>
          <div class="double c">
            <div>
              <p>

                <label for="transaction_date">Date:</label>
                <input data-toggle="datepicker" value="<?= isset($header) && isset($header->date) ? $header->date : '' ?>" id="transaction_date" name="header[transaction_date]" >
              
              </p>
            </div>
            <div>
              <p>
                <label for="transaction_ref">Reference:</label>
                <input type="text" value="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" id="transaction_ref" name="header[transaction_ref]">
              </p>
            </div>
          </div>
          <p>
            <label for="memo">Memo:</label>
            <input type="text" value="<?= isset($header) && isset($header->memo) ? $header->memo : '' ?>" id="memo" name="header[memo]" value="">
          </p>
          <p class="submit list-btn"><button type="submit" class="" id="createReversal" after = "duplicate">Create Reversal</button></p>
        </section>

                    <!-- <table class="mobile-hide dataTable no-footer table-c" role="grid">
            <thead> -->
                    <div class="has-table-c">
                        <table class="table-c formGridTable mobile-hide dataTable no-footer">
                            <thead class="dataTables_scrollHead" style=" width: 100%;">
                                <tr>
                                    <th width="10%">Account</th>
                                    <th width="10%">Property</th>
                                    <th width="10%">Unit</th>
                                    <th width="10%">Description</th>
                                    <th width="10%">Debit</th>
                                    <th width="10%">Credit</th>
                                    <th width="10%">Class</th>
                                    <th width="10%">Name</th>
                                </tr>
                            </thead>
                            <tbody id="journalEntryBody" class="dataTables_scrollBody" >
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">Total:</td>
                                    <td class="text-center">
                                        <span class="text-left">$</span><span id="debitTotal"><?php echo $debitTotal > 0 ? $debitTotal : '.00 ' ?></span>
                                    </td>
                                    <td class="text-center"><span class="text-left">$</span>
                                        <span id="creditTotal"><?php echo $creditTotal > 0 ? $creditTotal : '.00 ' ?></span>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>

                  <footer>
                    <ul class="list-btn">
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

        <script type="text/javascript">
            var template = function (id, data = {}) {
                  var newRow = '<tr class="" id="' + id + '" ' + (data.id ? 'tid="' + data.id + '"' : '') + ' ' + (data.property_id && data.property_id != '-1' ? 'property_id="' + data.property_id + '"' : '') + '>' +
                  '<td class="formGridSelectTd" source = "#bankTransAcct" stype="account" ' + (data.account_id ? 'value="' + data.account_id + '"' : '') + '><span class="select"><input></span></td>' +
                  '<td class="formGridSelectTd" source = "#bankTransProp" stype="property" ' + (data.property_id ? 'value="' + data.property_id + '"' : '') + '><span class="select"><input></span></td>' +
                  '<td class="formGridSelectTd" stype="unit" ' + (data.unit_id ? 'value="' + data.unit_id + '"' : '') + '><span class="select"><input></span></td>' +
                  '<td><input type="text" source = "#bankTransDesc" id="description" name="transactions[' + id + '][description]" value="' + (data.description ? data.description : '') + '"></td>' +
                  '<td total="debit" source = "#headerDebit"><input type="text" class="decimal total" id="debit" name="transactions[' + id + '][debit]" value="' + (data.debit ? data.debit : '') + '" placeholder="0"></td>' +
                  '<td total="credit" source = "#headerCredit"><input type="text" class="decimal total" id="credit" name="transactions[' + id + '][credit]" value="' + (data.credit ? data.credit : '') + '" placeholder="0"></td>' +
                  '<td class="formGridSelectTd" stype="class" source="[sel-id=class_id]" ' + (data.class_id ? 'value="' + data.class_id + '"' : '') + '><span class="select"><input></span></td>' +
                  '<td class="formGridSelectTd" stype="profile" value="' + (data.profile_id ?  data.profile_id + (data.lease_id ? "-" + data.lease_id  : '') : '') + '"><span class="select"><input></span></td>' +
                  '</tr>';
                  return newRow;
              }
              var grid = $('.modal').last().formGrid({template: template, data: <?php echo $jTransactions; ?>, minRows: 8});
              console.log(grid.body);
              grid.addTotal('debit', null, 'input#debit', '#debitTotal');
              grid.addTotal('credit', null, 'input#credit', '#creditTotal');



            

        </script>