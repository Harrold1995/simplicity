<div class="modal flexmodal fade reconciliation-modal <?php echo $edit ?>" id="reconciliationModal" tabindex="-1" role="dialog" main-id=<?= isset($reconciliation) && isset($reconciliation->r_id) ? $reconciliation->r_id : '-1' ?> type="reconcilliation" ref-id="" aria-hidden="true" ">
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
                            <span style="margin-right:70px;">
                                            <span style="font-weight:600;">match</span>
                                            <label class="switch float-none" style="line-height:18px;" for="rec-mode-trigger" id="rec-mode-trigger-label">
                                                <input type="checkbox" value="1" class="no-js cr-trigger" id="rec-mode-trigger">
                                                <span class="slider round"></span>
                                                <span class="option-text">enter</span>
                                            </label>
                            </span>

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
                          
                            
                            
                        </form>
                    </header>
                    <input type="hidden" id="closeRec" name="closed" value="false">
                    <div id="recId">
                        <?php if ($reconciliation->r_id != null): ?>

                            <input type="hidden" name="reconciliation[id]" value="<?= $reconciliation->r_id ?>">
                        <?php endif; ?>
                    </div>

                    
                    <div class="rec-outer double">
                        <div>

                            <header>
                                <h2>Transactions in Chase</h2>
                                <p class="input-search mb-0">
                                    <label for="mrh">Search</label>
                                    <input type="text" id="crsearch" name="mrh" placeholder="search">
                                    <a href="./" class="btn">Search</a>
                                </p>
                            </header>
                            <div class="table-d-wrapper last-child has-data-table">
                                <div class="rec-table" id="credittable-<?php echo $account->id?>">
                                
                                </div>

                                <script>
                                    var credit = new RecSlick("#credittable-<?php echo $account->id?>", {
                                        dataUrl: 'reconciliations/getBankAjax/<?php echo $account->id ?>',
                                        total: '#payment_total',
                                        number: '#cleared_payments',
                                        difference: '#rec_diff',
                                        search: '#crsearch',
                                        differenceCallback: function() {}
                                    });
                                </script>
                            </div>
                            <p><span id="cleared_payments">0</span> <span> Bank Transactions  </span>
                                 <span id="payment_total">0</span></p>
                        </div>

                        <div>

                            <header>
                                <h2>Transactions in Simpli-city</h2>
                                <p class="input-search mb-0">
                                    <label for="mrh">Search</label>
                                    <input type="text" id="dsearch" name="mrh">
                                    <a href="./" class="btn">Search</a>
                                </p>

                            </header>
                            <div class="table-d-wrapper last-child has-data-table">
                                <div class="rec-table" id="debittable-<?php echo $account->id?>">

                                </div>

                                <script>
                                    var debit = new RecSlick("#debittable-<?php echo $account->id?>", {
                                        dataUrl: 'reconciliations/getAllAjax/<?php echo $account->id ?>',
                                        total: '#debit_total',
                                        number: '#cleared_deposits',
                                        difference: '#rec_diff',
                                        search: '#dsearch',
                                    });
                                   
                                    var callback = function() {
                                        
                                        var diff = Number(credit.total - debit.total );
                                        credit.modal.find('#rec_diff').text(`Difference: ${diff.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`)
                                        if(diff == 0 && (debit.total != 0 || credit.total!= 0 )){
                                            credit.modal.find('#closeRec').val('true')
                                            credit.modal.find('#match_submit').show();
                                            credit.modal.find('#rec_diff').hide();
                                            

                                        }else {
                                            credit.modal.find('#closeRec').val('false')
                                            credit.modal.find('#match_submit').hide();
                                            credit.modal.find('#rec_diff').show();
                                        }
                                    };
                                    credit.modal.on('input', 'input', callback);
                                    debit.setCallback(callback);
                                    credit.setCallback(callback);
                                </script>
                            </div>
                            <p><b><span id="cleared_deposits">0</span></b> <span> Simplicity transactions  </span>
                                 <span id="debit_total">0</span></p>
                        </div>

                    </div>



                       

                    <div class="submit">
                        <button type="submit" id="match_submit" class = "autoRecSubmit"  data-type = 'auto'>Save Match</button>
                        <span id = "rec_diff" style ="max-width: 444px; margin: 0 auto; padding: 13.5px; font-size: 25px; display: block; color: #4ec1fd; text-align: center;"></span>
                    </div>
                    <div id="refreshRec" data-type = 'auto'>refresh</div>

                </article>
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
    
    $('#rec-mode-trigger-label').change(function (e) {
        let mode = $('#rec-mode-trigger-label').hasClass('active') == true ? 'add':'match';
        if($('#rec-mode-trigger-label').hasClass('active') == false){
            console.log('match');
            let html= `<div class="rec-outer double">
                        <div>

                            <header>
                                <h2>Transactions in Chase</h2>
                                <p class="input-search mb-0">
                                    <label for="mrh">Search</label>
                                    <input type="text" id="crsearch" name="mrh" placeholder="search">
                                    <a href="./" class="btn">Search</a>
                                </p>
                            </header>
                            <div class="table-d-wrapper last-child has-data-table">
                                <div class="rec-table" id="credittable-<?php echo $account->id?>">
                                
                                </div>

                            </div>
                            <li><b><span id="cleared_payments">0</span></b> <span> Bank Transactions  </span>
                            <b><span id="payment_total">0</span></b></li>
                        </div>

                        <div>

                            <header>
                                <h2>Transactions in Simpli-city</h2>
                                <p class="input-search mb-0">
                                    <label for="mrh">Search</label>
                                    <input type="text" id="dsearch" name="mrh">
                                    <a href="./" class="btn">Search</a>
                                </p>

                            </header>
                            <div class="table-d-wrapper last-child has-data-table">
                                <div class="rec-table" id="debittable-<?php echo $account->id?>">

                                </div>

                            </div>
                            <li><b><span id="cleared_deposits">0</span></b> <span> Simplicity transactions  </span>
                            <b><span id="debit_total">0</span></b></li>
                        </div>

                    </div>`;
                    $(this).closest('.modal').find('#rec-add').replaceWith(html);
                                   var credit = new RecSlick("#credittable-<?php echo $account->id?>", {
                                        dataUrl: 'reconciliations/getBankAjax/<?php echo $account->id ?>',
                                        total: '#payment_total',
                                        number: '#cleared_payments',
                                        difference: '#rec_diff',
                                        search: '#crsearch',
                                        differenceCallback: function() {}
                                    });

                                    var debit = new RecSlick("#debittable-<?php echo $account->id?>", {
                                        dataUrl: 'reconciliations/getAllAjax/<?php echo $account->id ?>',
                                        total: '#debit_total',
                                        number: '#cleared_deposits',
                                        difference: '#rec_diff',
                                        search: '#dsearch',
                                    });
                                   
                                    var callback = function() {
                                        
                                        var diff = Number(credit.total - debit.total );
                                        credit.modal.find('#rec_diff').text(`Difference: ${diff.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`)
                                        if(diff == 0 && (debit.total != 0 || credit.total!= 0 )){
                                            credit.modal.find('#closeRec').val('true')
                                            credit.modal.find('#match_submit').show();
                                            credit.modal.find('#rec_diff').hide();
                                            

                                        }else {
                                            credit.modal.find('#closeRec').val('false')
                                            credit.modal.find('#match_submit').hide();
                                            credit.modal.find('#rec_diff').show();
                                        }
                                    };
                                    credit.modal.on('input', 'input', callback);
                                    debit.setCallback(callback);
                                    credit.setCallback(callback);
                                    
        $(this).closest('.modal').find('.submit').show();
        $(this).closest('.modal').find('#refreshrec').show();


                    
        } else {
            console.log('add');
            $(this).closest('.modal').find('.rec-outer').replaceWith( `
                           <div id = "rec-add">

                            <header  style ='padding-bottom: 15px'>
                                <h2>Transactions in Chase</h2>
                                <p class="input-search mb-0">
                                    <label for="mrh">Search</label>
                                    <input type="text" id="crsearch" name="mrh" placeholder="search">
                                    <a href="./" class="btn">Search</a>
                                </p>
                            </header>
                            <div class="formGridSlickTable mobile-hide utilities" style="z-index: 2; height: calc(100vh - 280px); ">

                            </div>

                        </div>
        ` );
        $(this).closest('.modal').find('.submit').hide();
        $(this).closest('.modal').find('#refreshrec').hide();

        var template = function (data) {
        var result = [];

        result.push(['row[' + data.id + '][id]', data.id]);
        result.push(['row[' + data.id + '][amount]', data.amount || 0 ]);
        result.push(['row[' + data.id + '][date]', data.date]);
        result.push(['row[' + data.id + '][num]', data.num]);
        result.push(['row[' + data.id + '][type]', data.type]);
        result.push(['row[' + data.id + '][account]', data.account]);
        result.push(['row[' + data.id + '][name]', data.name]);
        console.log(result);

        return result;
            
        };

       $('.modal').last().formGridSlick({
            
            template: template,
            dataUrl: JS.baseUrl+ 'reconciliations/getBankAddAjax/<?php echo $account->id ?>',
            iuUrl: 'properties/instantUpdateSettings',
            type: 'capital',
            search: '#crsearch',
            showFooterRow: false
        });
        }
        

        

    });



</script>