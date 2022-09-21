<div class="modal flexmodal fade utilities-modal <?php echo $edit ?>" id="utilitiesModal" tabindex="-1" role="dialog" type="utilities" ref-id="" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style=" width: 100%;  padding: 30px;">
                <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
                <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="utilities-grid-entry" type="utilities">

                        <header class="modal-h">
                            <h2>Utilities Grid</h2>
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
                            <div>
                                <p class="input-search">
                                    <label for="fsa">Search</label>
                                    <input type="text" id="fsa" name="fsa">
                                    <button type="submit">Submit</button>
                                    <a href="./"><i class="icon-microphone"></i> <span>Record</span></a>
                                </p>
                            </div>
                            <p class="submit">
                                <button type="button" id="exit">Exit</button>
                            </p>
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
                            <div style="width: 33%; margin: 10px;" class="checkboxGroup" id="propertyBlockUt">

                            </div>
                            <script>
                                var prcheck = new CheckboxBlock('#propertyBlockUt', {source: 'property', filter: 'property_id', title: 'Properties'});
                            </script>

                            <div style="width: 33%; margin: 10px;" class="checkboxGroup" id="vendorsBlockUt">
                            </div>
                            <script>
                                var vcheck = new CheckboxBlock('#vendorsBlockUt', {source: 'vendors', filter: 'profile_id', title: 'Vendors'});
                            </script>
                        </div>

                        <div class="formGridSlickTable mobile-hide utilities" style="z-index: 2;height:100%; ">

                        </div>

                        <div id="utilityNotesDiv" style="background-color:#f6f8f9; padding: 15px; border-radius: 15px; display: none; height: 75%; width: 25%; right: 15px; position: fixed; z-index: 3000; height: calc(100vh - 300px); overflow: hidden;">
                            <span class="buttons" style="right: 0%; position: absolute; top: 1%;"><span class="closeCourtDiv" style="padding: 8px 20px;cursor: pointer;">X</span></span>
                            <h3 style="text-align: center; ">Notes</h3>
                            <div id="noteForm" style="padding-bottom: 10px;"></div>
                            <div id="utilityNotes" style="height: 100%; color: #919090;font-size: 11px; margin: 10px; overflow: auto; margin: -12px;"></div>
                        </div>

                    <p class="d-none strong scheme-b m20 size-a overlay-a" id="selectedCharges">0 bills selected</p>
                    <footer class="a">
                        <p class="m0">
                            <button type="submit" class="slgrid">Record bills</button>
                            <button type="submit" id="deleteUtilities">Delete</button>
                        </p>
                    </footer>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    var template = function (data) {
        var result = [];
        if(data.mainid) data.id = data.mainid;
        if(!data.subid) data.subid = 0;
        if(data.parent === undefined) {
            var newFormat =  data.date.split("/");//.reverse().join("/");
            var newDate = newFormat[2] + '-' + newFormat[0] + '-' + newFormat[1];
            result.push(['row[' + data.id + '][transactions][' + data.subid + '][id]', data.id]);
            result.push(['row[' + data.id + '][utilities][direct_payment]', data.direct_payment || 0]);
            result.push(['row[' + data.id + '][header][transaction_date]', newDate]);
            result.push(['row[' + data.id + '][header][transaction_ref]', data.account]);
            result.push(['row[' + data.id + '][utilities][last_paid_date]', newDate]);
            result.push(['row[' + data.id + '][transactions][' + data.subid + '][profile_id]', data.profile_id]);
        }
        result.push(['row[' + data.id + '][transactions][' + data.subid + '][billable]', data.billable || 0]);
        result.push(['row[' + data.id + '][transactions][' + data.subid + '][account_id]', data.account_id]);
        result.push(['row[' + data.id + '][transactions][' + data.subid + '][account]', data.account]);
        result.push(['row[' + data.id + '][transactions][' + data.subid + '][property_id]', data.property_id]);
        result.push(['row[' + data.id + '][transactions][' + data.subid + '][amount]', data.amount || 0]);
        result.push(['row[' + data.id + '][transactions][' + data.subid + '][utility_trans][util_usage]', data.usage || '']);
        result.push(['row[' + data.id + '][transactions][' + data.subid + '][utility_trans][estimate]', data.estimate || 0]);
        return result;
    };
    var grid = $('.modal').last().formGridSlick({
        template: template,
        dataUrl: 'properties/getPropertyUtilitiesForBillSlick',
        iuUrl: 'transactions/updateUtility',
        type: 'utilities'
    });
    prcheck.addChangeCallback(grid.checkboxChanged.bind(grid));
    vcheck.addChangeCallback(grid.checkboxChanged.bind(grid));
    //payBill.setTransactionsProperties(< ?php echo $jTransactions; ?>);
    //console.log('pay bills')

</script>