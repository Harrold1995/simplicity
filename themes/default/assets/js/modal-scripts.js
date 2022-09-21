var MS = function () {};
MS.prototype = {
    run: function(type){
        switch(type){
            case 'check': this.checkScript(); break;
        }
    },
    checkScript(){
        var classes = <?php echo $jClasses ? $jClasses : '0' ?>;
        var properties= <?php echo $jProperties ? $jProperties : '0'?>;
        var accounts = <?php echo $jAccounts ? $jAccounts : '0' ?>;//not used anymore, using subaccounts
        var subaccounts = <?php echo $jsubaccounts ? $jsubaccounts : '0' ?>;
        var units = <?php  echo $jsubunits ? $jsubunits : '0' ?>;
        var names = <?php  echo $jNames ? $jNames : '0' ?>;
        var transactionsArray = <?php  echo $jTransactions ? $jTransactions : '' ?>;
        var propertyAccounts = <?php echo $jPropertyAccounts; ?>;
        if(transactionsArray){
            var numOfTotalTransactions = transactionsArray.length;
        } else {
            var numOfTotalTransactions = 0
        }

        function addRowToCheckForm(body, row, id) {

            if (row == null || $(row).is(':last-child')) {
                console.log(id + "id")
                if(row){
                    id++
                }
                var newRow = '<tr class="checkRow fillNextRow createNewRow"  id="' + id + '" >' +
                    ' <td id="accountId"  class="formGridAccountTd">' +
                    '<span class="select">'+
                    '<select class="editable-select"  id="account_id" name="transactions[' + id + '][account_id]" id="account_types_id"  modal="account" type="table" key="account.name">' +
                    ' <option value="-1" selected ></option>'
                // for (var i = 0; i < accounts.length; i++) {
                // 	newRow += '<option value=' + accounts[i].id + '>' + accounts[i].name + '</option>';
                // }
                for (var i = 0; i < subaccounts.length; i++) {
                    newRow += '<option data-id="'+ subaccounts[i].id + '" data-parent-id="' + subaccounts[i].parent_id + '" class="nested' + subaccounts[i].step + '"value="' + subaccounts[i].id + '">' + subaccounts[i].name + '</option>';
                }
                newRow += ' </select>' +
                    '  </span>'+
                    '</td>' +

                    ' <td>' +
                    '<span class="select">';
                newRow += '<select class="editable-select formGridPropertySelected"  modal="property" type="table" key="properties.name" id="property_id" name="transactions[' + id + '][property_id]" >';
                //  'onChange="JS.loadList(\'api/getUnitsProperty\', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , \'#unit_id\',  $(this).closest(\'.checkRow\')) ;'+
                //    'JS.loadList(\'api/getAccountsProperty\', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , \'#account_id\',  $(this).closest(\'.checkRow\')) "  >' +
                newRow += ' <option value="-1" selected ></option>'
                for (var i = 0; i < properties.length; i++) {
                    newRow += '<option value=' + properties[i].id + '>' + properties[i].name + '</option>';
                }
                newRow += ' </select>' +
                    '</span>'+
                    '</td>' +

                    ' <td>' +
                    '<span class="select">'+
                    '<select class=" editable-select formGridUnitSelect" id="unit_id"  name="transactions[' + id + '][unit_id]">' +
                    ' <option value="-1" selected ></option>'
                for (var i = 0; i < units.length; i++) {
                    newRow += '<option value=' + units[i].id + '>' + units[i].name + '</option>';
                }
                newRow += ' </select>' +
                    '</span>'+
                    '</td>' +


                    ' <td>' +
                    '<input type="text" id="description" name="transactions[' + id + '][description]"  >' +
                    '</td>' +

                    ' <td>' +
                    '<input type="text" class="decimal checkAmount formatCurrency calculateTotal" id="amount"  name="transactions[' + id + '][debit]" placeholder="0.00" >' +
                    '</td>' +

                    ' <td>' +
                    '<span class="select">'+
                    '<select class="editable-select class_id"  id="class_id" name="transactions[' + id + '][class_id]">' +
                    ' <option value="-1" selected ></option>'
                for (var i = 0; i < classes.length; i++) {
                    newRow += '<option value=' + classes[i].id + '>' + classes[i].description + '</option>';
                }
                newRow += ' </select>' +
                    '</span>'+
                    '</td>' +
                    ' <td>' +
                    '<span class="select">'+
                    '<select class="editable-select" id="profile_id" name="transactions[' + id + '][profile_id]">' +
                    ' <option value="-1" selected ></option>'
                for (var i = 0; i < names.length; i++) {
                    newRow += '<option value=' + names[i].id + '>' + names[i].vendor + '</option>';
                }
                newRow += ' </select>' +
                    '</span>'+
                    '</td>' +
                    '  </tr>'

                body.append(newRow)

            }

            newRow.find('.editable-select').editableSelect()
            newRow.find('input.decimal').calculadora({decimals: 2, useCommaAsDecimalMark: false});

        }
        $('body').on('keydown', '.checkRow > td', function (){

            var columnNum = $(this).index();
            //alert($(this).find('input').first().attr('key'));

            //console.log($(this).closest('tr').next("tr"));
            var keyPressed = event.which || event.keyCode;
            switch (keyPressed) {
                case 37:
                    alert('left');
                    break;
                case 38:
                    $(this).closest('tr').prev("tr").find("td:nth-child("+(columnNum+1)+")").find("input:first-child").focus();
                    break;
                case 39:
                    alert('right');
                    break;
                case 40:
                    $(this).closest('tr').next("tr").find("td:nth-child("+(columnNum+1)+")").find("input:first-child").focus();

                    break;
            }
        });

        function checkprint(body){
            var body2 = body[0];
            var checkId = $(body2).find("#transNum").val();
            var acctId = $(body2).find("input[name='headerTransaction[account_id]']").val();
            console.log(checkId + "checkId" );
            var stub = $(body2).find(".stub");
            data = [{ "id" : acctId, "th_id" : checkId }];

            $.ajax({
                url: '<?php echo base_url('checks/onPrint');?>',
                type: 'GET',
                data: {accounts:data},
            dataType: 'json',
                success: function(response)
            {
                function pad(n,a) { return ("000000" + n).slice(-a); }
                $(body2).find("#checkNumber").html(pad(response[0].next_check_num,4)) ;
                $(body2).find("#check_num").html(response[0].next_check_num) ;
                $(body2).find("#acctNum").html(pad(response[0].routing,9)) ;
                $(body2).find("#routingNum").html(pad(response[0].account_number,9)) ;
                console.log(response[0].account_number.length);
                $(body2).find("#transaction_ref").val(response[0].next_check_num) ;
                $(body2).find("#cNum").html(response[0].next_check_num) ;
                checkprint2();
            }
        });


            function checkprint2(){
                //check if there's a change on the form
                if ($(body2).hasClass( "changed" ) == true ) {
                    alert("please save changes before printing");
                    return;
                }

                //get the details for the chack
                var details = $(body2).find('.checkRow');

                for(i = 0; i < details.length; i++) {
                    var detailLine = details[i];
                    var stub = $(body2).find(".stub");

                    for(t = 0; t < stub.length; t++) {

                        var row = stub[t].insertRow(i);
                        var cell1 = row.insertCell(0);
                        var cell2 = row.insertCell(1);
                        cell1.innerHTML = $(details[i]).find("#account_id").val()+ " "+ $(details[i]).find("#property_id").val()+ " "+$(details[i]).find("#description").val();
                        cell2.innerHTML = $(details[i]).find(".checkAmount").val();
                    }

                }




                //console.log($(body2).hasClass( "changed" ));

                //console.log(body2);
                //setting date for print
                var date = $(body2).find("#checkDate").val();
                $(body2).find("cDate").html(date) ;

                var payTo = $(body2).find("#profile_id").val();
                $(body2).find("#payToName").html(payTo);
                console.log(payTo+" payTo payTo payTo payTo ")
                //setting amount for print
                var amount = $(body2).find("#checkAmount")[0];
                $(body2).find("#Amount_num").html($(amount).val());
                //displaying amount as text
                var ones = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine"];
                var teens = ["Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"];
                var tens = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];


                var numtochange = $(amount).val().toString();
                var decimal;
                if (numtochange.indexOf(".")<0) {decimal=numtochange.length} else{decimal= numtochange.indexOf(".")};

                var dollars = numtochange.slice(0,decimal);
                var cents = numtochange.slice(decimal+1,numtochange.length);
                var numtext ="";


                for (var i = 0; i < dollars.length; i++) {

                    console.log(dollars.length - i );

                    switch(dollars.length - i) {
                        case 15: case 12: case 9: case 6: case 3:
                        numtext = numtext + " " + ones[dollars.charAt(i)] + " hundred";
                        break;
                        case 14: case 11: case 8: case 5: case 2:
                        numtext = numtext + " " + tens[dollars.charAt(i)];
                        break;
                        case 13:
                            if (dollars.charAt(i-1) ==1) {
                                numtext = numtext + " " + teens[dollars.charAt(i)] + " Trillion,";
                            }
                            else{
                                numtext = numtext + " " + ones[dollars.charAt(i)] + " Trillion,";
                            }

                            break;


                        case 10:
                            if (dollars.charAt(i-1) ==1) {
                                numtext = numtext + " " + teens[dollars.charAt(i)] + " Billion,";
                            }
                            else{
                                numtext = numtext + " " + ones[dollars.charAt(i)] + " Biliion!,";
                            }

                            break;


                        case 7:
                            if (dollars.charAt(i-1) ==1) {
                                numtext = numtext + " " + teens[dollars.charAt(i)] + " million,";
                            }
                            else{
                                numtext = numtext + " " + ones[dollars.charAt(i)] + " million,";
                            }

                            break;


                        case 4:
                            if (dollars.charAt(i-1) ==1) {
                                numtext = numtext + " " + teens[dollars.charAt(i)] + " Thousand,";
                            }
                            else{
                                numtext = numtext + " " + ones[dollars.charAt(i)] + " Thousand,";
                            }

                            break;


                        case 1:
                            if (dollars.charAt(i-1) ==1) {
                                numtext = numtext + " " + teens[dollars.charAt(i)] + " dollars and";
                            }
                            else{
                                numtext = numtext + " " + ones[dollars.charAt(i)] + " dollars and";
                            }


                    }


                }
                if (cents.length ==0){
                    numtext = numtext+" 00 cents";
                }

                for (var i = 0; i <cents.length; i++) {



                    switch(cents.length-i){
                        case 2:

                            numtext = numtext+" "+ tens[cents.charAt(i)];

                            break;

                        case 1:

                            if (cents.charAt(i -1) ==1) {
                                numtext = numtext+" "+ teens[cents.charAt(i)]+" cents";
                            }
                            else{
                                numtext = numtext+" "+ ones[cents.charAt(i)]+" cents" ;
                            }

                            break;



                    }



                }


                $(body2).find('#textAmount').html(numtext);
                $(body2).find("#check_print").addClass('print-section');

                window.print();

                $(body2).find("#check_print").removeClass('print-section');
                $(body2).find(".stub").empty();
                $(body2).find(".stub").empty();
            }
        }
    }
};

$(document).ready(function () {
    var MS = new MS();
});