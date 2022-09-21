var formGrid = function() {
	//$ = jQuery.noConflict();
};
/** class methods **/
formGrid.prototype = {

    api: function(){
        $('body').on('change', '.formGridPropertySelected', function () {
            var thisTr = $(this).closest('tr');
            var valueTd = $(this).closest('td').find('input[type=hidden]').val();
            var accountTd = thisTr.find('.formGridAccountTd');
            var unitTd = thisTr.find('.formGridUnitSelect');
            var multiple = null;
            if($(this).hasClass('multiple')){
                multiple = 'multiple';
                console.log('has multiple!!!');
            }
            formGrid.loadList2(valueTd, accountTd, unitTd, multiple)
        });

    },
    loadList2: function (value, accountId, unitId, multiple = null) {
                formGrid.unitsApi(value, unitId, multiple);
                formGrid.accountsApi(value, accountId, multiple);
                console.log('new js file');
                //onChange="formGrid.loadList2($(this).closest(\'.select\').find(\'input[type=hidden]\').val() , $(this).closest('tr').find('#account_id'), $(this).closest('tr').find('#unitId'));"
    },

    unitsApi: function(value, input, multiple = null){
                                    // $(td).empty();
                                    // var unitsDropdown = "";
                                    // 	unitsDropdown +="<option value='0'>None</option>";
                                    // for (var j = 0; j < units.length; j++) {
                                    // 	if(units[j].property_id == value){
                                    // 		unitsDropdown += `<option value='` + units[j].id + `'>` + units[j].name + `</option>`;
                                    // 	}
                                    // }
                                    // unitsDropdown += "</select>";
                                    // //$(td).append(unitsDropdown);
                                    // td.editableSelect('resetSelect',unitsDropdown);
                                    // //$('body').find('.editable-select').editableSelect();
                                    // 	//unitsDropdown +=`	</select>`;
            var td = $(input).closest('td');
            var oldUnit = $(input).closest('td').find('input[type=hidden]').val();
            var oldName =  $(input).closest('td').find('input[type=hidden]').attr('name');
            $(td).empty();
            var unitsDropdown = `<span class="select">
            <select class="w135 editable-select quick-add set-up formGridUnitSelect "  id="unit_id" name="`+oldName+`"  modal="" type="table" key=""`;
             if(multiple){ unitsDropdown += `onselect="editMultipleUnits($(this));"`;}
             unitsDropdown +=` >`;
             unitsDropdown +="<option value='-1'>None</option>";
             for (var j = 0; j < units.length; j++) {
                if( units[j].property_id == value){
                    unitsDropdown += `<option value='` + units[j].id + `'`;
                    if(units[j].id == oldUnit){unitsDropdown += ` selected`;}
                    unitsDropdown += `>` + units[j].name + `</option>`;
                }
            }
            unitsDropdown += "</select></span>";
            $(td).append(unitsDropdown);
            //td.editableSelect('resetSelect',accountsDropdown);
            //$('body').find('.editable-select').editableSelect();
            $(td).closest('td').find('.editable-select').editableSelect();
    },

        accountsApi: function(value, td, multiple = null){
            var oldAccount = td.closest('td').find('input[type=hidden]').val();
            //var transId =  td.closest('tr').attr('id')
            var oldName =  td.closest('td').find('input[type=hidden]').attr('name');
            console.log(oldAccount);
            $(td).empty();
            var accountsDropdown = `<span class="select">
                <select class="w135 editable-select quick-add set-up "  id="account_id" name="`+oldName+`"  modal="" type="table" key=""`;
                 if(multiple){ accountsDropdown += `onselect="editMultipleAccounts($(this));"`;}
                accountsDropdown +=` >`;
            accountsDropdown +="<option value='-1'>None</option>";
        var propertyAccountsArray = [];
            for (var i = 0; i < propertyAccounts.length; i++) {
                if(propertyAccounts[i].property_id == value){
                    propertyAccountsArray.push(propertyAccounts[i].account_id);
                    //console.log(propertyAccounts[i]);
                    //console.log('second if works!!');
                }
            }	
        for (var j = 0; j < accounts.length; j++) {
            if(accounts[j].all_props == 1 || propertyAccountsArray.includes(accounts[j].id)){
                accountsDropdown += `<option value='` + accounts[j].id + `'`;
                if(accounts[j].id == oldAccount){accountsDropdown += ` selected`;}
                accountsDropdown += `>` + accounts[j].name + `</option>`;
            }		
        }
        
        accountsDropdown += "</select></span>";
        $(td).append(accountsDropdown);
        //td.editableSelect('resetSelect',accountsDropdown);
        //$('body').find('.editable-select').editableSelect();
        $(td).closest('td').find('.editable-select').editableSelect();
            //unitsDropdown +=`	</select>`;
    },
        //fills next row with the last tr info
        fillNextRow: function (){
            $('body').on('click', '.fillNextRow', function () {

                    var row = $(this);
                    // if($(this).hasClass('filled'))return;
                    // $(this).addClass('filled');
                    if($(row).prev('tr').find("#property_id").closest('td').find('input[type=hidden]').val() < 1)return;
                    var rowItem = $(row).find(":input[type!='hidden']");
                    
                    var empty = true;
                    var value;
                 var prevTrIdNames = [];
                 var prevRowIds = $(row).prev('tr').find(":input[type!='hidden']");
                 //var prevRowIds = $(row).prev('tr').find(":input[class='inputInfo']");
                 prevRowIds.each( function(){ if($(this).attr('id') != 'account_id'){
                            prevTrIdNames.push("#" +$(this).attr('id'));
                        }  
                    })
                 //prevRowIds.each( function(){ prevTrIdNames.push($(this).find('.inputInfo')); })
                    rowItem.each(
                        function(){          
                                value = $(this).val();
                                if(value === "-1" || value == 0  ){
                                    empty = true;                   
                                }   else {
                                    empty = false;
                                    return false;
                                }              
                        }         
                    )
                
                    if(empty == false){
                        
                    }else {
                    
                        if(!$(row).is(':first-child')){
                        //var prevRow = $(row).prev('tr').find(":input");
                        //var prevRowId = $(row).prev('tr').find(":input").attr('id');
                            
                        $.each(prevTrIdNames, function( index, value ) {
                
                           // var prevRowValue = $(row).prev('tr').find(value).val();
                            var prevRow = $(row).prev('tr').find(value);
                            //for selects - finds hidden input
                            if(prevRow.closest('td').find('input').is(":hidden")){
                                console.log('hidden');
                                var editPropertySelectedValue = $(prevRow).closest('td').find('input[type=hidden]').val();
                                //var editPropertySelectedText = $(prevRow).closest('td').find('input[type=hidden]').attr("text");
                                var editPropertySelectedText = $(prevRow).closest('td').find('input').val();
                    
                                inputFields = $(row).find(value).closest('td').find('input');
                                $(inputFields[1]).val(editPropertySelectedValue).change();
                                $(inputFields[0]).val(editPropertySelectedText).change();
                                //$(inputFields[0]).change();
                                //$(inputFields[1]).attr("text", editPropertySelectedText);
                            }
                            else{
                                //for journal entry
                                if($(row).find(value).closest('td').find('input').attr('id') == 'JEcredit' || $(row).find(value).closest('td').find('input').attr('id') == 'JEdebit')
                                {
                                    var amountId = $(row).find(value).closest('td').find('input').attr('id');
                                    //var creditDebitAmount = formGrid.journalEntryTotal($(row).find(value).closest('td').find('input'));
                                    var creditDebitAmount = formGrid.jeCreditDebitTotal($(row).find(value).closest('td').find('input'));
                                    var credit = 0.00
                                    var debit = 0.00
                                    if(creditDebitAmount[3] == 'debit'){credit = creditDebitAmount[2]}
                                    if(creditDebitAmount[3] == 'credit'){debit = creditDebitAmount[2]}
                                    //updates credit and debit. updates total with keyup
                                    if(amountId == 'JEcredit'){$(row).find(value).closest('td').find('input').val(credit); $(row).find(value).closest('td').find('input').keyup();}
                                    if(amountId == 'JEdebit'){$(row).find(value).closest('td').find('input').val(debit); $(row).find(value).closest('td').find('input').keyup();}
                                }else{
                                    //totals for other forms
                                    if($(row).find(value).closest('td').find('input').hasClass('calculateTotal')){
                                        var amountToInput = formGrid.fillAmountNeeded($(row));
                                        $(row).find(value).closest('td').find('input').val(amountToInput);
                                        $(row).find(value).closest('td').find('input').keyup();
                                    }else{
                                        //all other inputs that are not hidden
                                        var inputText = $(prevRow).closest('td').find('input').val();
                                        $(row).find(value).closest('td').find('input').val(inputText);
                                        $(row).find(value).closest('td').find('input').keyup();
                                    }
                                }
                                
                            }
                        });       
                    }
                    
                }
                
            });
        },
        //get amount needed
        fillAmountNeeded: function(row){
            var modal = $(row).closest('.modal');
            var topAmount = modal.find('.topAmount').val();
            var allAmounts = modal.find('.calculateTotal');
            var totalAmount =0;
            allAmounts.each(
                function(){
                    var value = Number($(this).val().replace(',', ''));
                    if (value) {
                        totalAmount += value;
                    }       
                })
            if(topAmount != totalAmount){
                //var amountNeeded = (topAmount > totalAmount)? topAmount-totalAmount : totalAmount-topAmount;
                var amountNeeded = topAmount-totalAmount;
                    return amountNeeded;
            }
        },
        //on keyup to update forms
        calculateTotal:function(){
            $('body').on('keyup focusout', '.calculateTotal', function () {
                var that = $(this);
                formGrid.calculate(that);    
            });
        },
        //calculate and update totals for all forms except je
        calculate: function(clicked){
            var modal = $(clicked).closest('.modal');
                
                var allAmounts = modal.find('.calculateTotal');
                var totalAmount =0;
    
                allAmounts.each(
                    function(){
                        var value = Number($(this).val().replace(',', ''));
                        if (value) {
                            totalAmount += value;
                        }           
                    });
                if($(modal).attr('id') == 'applyRefundModal'){formGrid.sdLmrCalculate(modal, clicked, totalAmount); console.log(clicked.val())}
                var total = $(modal).find('#totalAmount');
                console.log(total);
                total.html(totalAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') ) ;
        },
        //journal entry update total credit and debit
        journalEntryTotal: function(){
            $('body').on('keyup focusout', '#journalEntryBody #JEdebit, #journalEntryBody #JEcredit', function () {
                var modal = $(this).closest('.modal');
                var debitTotal = modal.find('#debitTotal');
                var creditTotal = modal.find('#creditTotal');
                var creditDebitAmount = formGrid.jeCreditDebitTotal(this);
                debitTotal.text(creditDebitAmount[0]);
                creditTotal.text(creditDebitAmount[1]);
            });


        },
        //get credit and debit totals
        jeCreditDebitTotal: function(input){
            //var id = input.attr('id');
            var modal = $(input).closest('.modal');
            var debit = modal.find('.JEdebit');
            var credit = modal.find('.JEcredit');
            var debitTotal = 0;
            var creditTotal = 0;
            debit.each(
                function () {
                    var value = Number($(this).val().replace(',', ''));
                    if (value) {
                        debitTotal += value;
                    }
                }
            )
            credit.each(
                function () {
                    var value = Number($(this).val().replace(',', ''));
                    if (value) {
                        creditTotal += value;
                    }
                }
            )
            var greater = (creditTotal > debitTotal)? "credit" : "debit";
            var diff = (creditTotal > debitTotal)? creditTotal-debitTotal :  debitTotal-creditTotal;
            var amount = [debitTotal, creditTotal, diff, greater];
            return amount;
        },
        sdLmrCalculate : function(modal, clicked, total){
            var lmrAmount =  Number($(modal).find('#sdApplyAmount').val());
            var sdAmount = Number($(modal).find('#lmrApplyAmount').val());
            var lmrSdTotal = lmrAmount + sdAmount;
            if( lmrSdTotal < total){
                console.log("more");
                JS.showAlert('danger', 'Applied amount can not be greater then payment amount')
                $(clicked).val('0.00')
            }else{
                if( $(clicked).closest('tr').find('.greenCheckTd').find('i').css('display') == 'none' ){
                    if(clicked.val() > 0){
                        $(clicked).closest('tr').find('.greenCheckTd').trigger('click');
                    }
				}
            }
        },
        customContext: function(id,e,body){
            //function customContext(id,e,body) {
                var trid = id;
                var tid = id;
                var e = e;
                var body = body;

                  // prevent default browser context
              event.preventDefault();
  
              // get position of modal (not used)
              //var offset = body[0].getClientRects()[0];
  
  
              var positx = e.clientX ; //position from left	
              var posity = e.clientY ;  //position from top
              var menu = document.querySelector("#context-menu");
  
              menu.classList.add( "context-menu--active" ); // make context menu appear
              menu.style.top = posity +"px"  //position top
              menu.style.left = positx +"px"  //position left
  
              document.addEventListener("click", hideContextMenu);  //listen for user clicking away
              document.addEventListener("keypress", hideContextMenu); //listen for user pressing key

                $("a.context-menu__link").click(function(id,e,body) {
                    console.log(id);
                    var option = $(this)[0];
                    var functionToCall = $(option).attr("data-action");
                    //alert( functionToCall );
                    eval("formGrid." + functionToCall + '('+tid+');');
                    document.removeEventListener("click", $("a.context-menu__link"));
                    });
                    // helper function to hide menu when something else is done
                  function hideContextMenu(){
                    console.log("listening");
                    menu.classList.remove( "context-menu--active" );
                    document.removeEventListener("click", hideContextMenu);
                    document.removeEventListener("keypress", hideContextMenu);
                  }
  
        },     
              // helper function to hide menu when something else is done
        //   hideContextMenu:  function (){
        //           console.log("listening");
        //           menu.classList.remove( "context-menu--active" );
        //           document.removeEventListener("click", hideContextMenu);
        //           document.removeEventListener("keypress", hideContextMenu);

        //       },
  
              
              deleteline: function (a= null) {
  
                      var input = document.createElement("input");
  
                      input.setAttribute("type", "hidden");
  
                      input.setAttribute("name", 'delete['+id+']');
  
                      input.setAttribute("value", id);
  
                      //append to form element that you want .
                      form = document.getElementById(id).closest("form");
  
                      
                      form.appendChild(input);
  
                      document.getElementById(id).remove();
              },

               insertLine: function(id) {

                var id2 = document.getElementById(id);
                var a = String(id2);
                var row = $(id2).closest('tr');
                
                //id2.insertAfter('#' + id);
                console.log('insert@!@!');
                //console.log(id2);
                //console.log(row);
                //console.log(row.html());
                //var aa = row.html();
                var tds = row.find('td');
                var newRow = '';
                    newRow += '<tr>';
                tds.each(
                    function () {
                        if($(this).find('input').is(":hidden")){
                         var hiddenValue = $(this).find('input[type=hidden]').val();
                         var thisId = $(this).find('input').attr('id');
                         var thisname = $(this).find('input[type=hidden]').attr('name');
                         console.log(thisId);
                         var aa = formGrid.whichTd(thisId, hiddenValue, thisId);
                         newRow += aa;
                         console.log(hiddenValue);
                         console.log(thisname);
                        }else{
                            var tdid = $(this).find('input').attr('id');
                            var tdname = $(this).find('input').attr('name');
                            var tdValue = $(this).find('input').val();
                            newRow += '<td>' + tdValue+ '</td>';
                            console.log(tdValue);
                            console.log(tdname);
                            console.log(tdid);
                        }
                    }
                )
                newRow += '</tr>';
                console.log(newRow);
                $('#'+ id).after(newRow);
                $('body').find('.editable-select').editableSelect();
                document.removeEventListener("click", this);
        },

        whichTd: function (tdId, value, trId){

            switch (tdId) {
                case 'property_id' :
                    var propertyNewRow = '';
                    propertyNewRow += `<td>
                                        <span class="select">
                                            <select class=" editable-select quick-add set-up formGridPropertySelected" id="property_id" name="transactions[`+trId+`][property_id]"  modal="" type="table" key=""
                                                    >
                                                    <option value="-1" selected ></option>`
                                                for (var j = 0; j < properties.length; j++) {
                                                    propertyNewRow += `<option value='` + properties[j].id + `'`;
                                                        if(value == properties[j].id){ propertyNewRow += 'selected'};
                                                        propertyNewRow +=`>` + properties[j].name + `</option>`;
                                                }
                                                propertyNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return propertyNewRow;
                case 'account_id':
                        var accountNewRow = '';
                        accountNewRow +=  `<td style="text-align:center" id="accountId" class="formGridAccountTd">
                        <span class="select">
                            <select class=" editable-select quick-add set-up "  id="account_id" name="transactions[`+trId+`][account_id]"  modal="" type="table" key="">
                                <option value="-1" selected ></option>`
                            for (var a = 0; a < accounts.length; a++) {
                                accountNewRow += `<option value='` + accounts[a].id + `'`;
                                if(value == accounts[a].id){ accountNewRow += 'selected'};
                                accountNewRow += `>` + accounts[a].name + `</option>`;
                            }
                            accountNewRow +=`	</select>
                            </span>
                    </td>`;
                    return accountNewRow;
                case 'unit_id':
                        var unitNewRow = '';
                        unitNewRow +=  `<td style="text-align:center">
                        <span class="select">
                            <select class="w135 editable-select quick-add set-up formGridUnitSelect"  id="unit_id" name="transactions[`+trId+`][unit_id]"  modal="" type="table" key="">
                                <option value="-1" selected ></option>`
                            for (var a = 0; a < units.length; a++) {
                                unitNewRow += `<option value='` + units[a].id + `'`;
                                if(value == units[a].id){ unitNewRow += 'selected'};
                                unitNewRow += `>` + units[a].name + `</option>`;
                            }
                            unitNewRow +=`	</select>
                            </span>
                    </td>`;
                    return unitNewRow;
                case 'class_id':
                    var classNewRow = '<td>' + value +'</td>';
                    var profileNewRow = '';
                    profileNewRow +=  `<td>
                                        <span class="select">
                                            <select class=" editable-select quick-add set-up "  id="class_id" name="transactions[`+trId+`][class_id]"  modal="" type="table" key="">
                                                <option value="-1" selected ></option>`
                                            for (var a = 0; a < classes.length; a++) {
                                                profileNewRow += `<option value='` + classes[a].id + `'`;
                                                if(value == classes[a].id){ profileNewRow += 'selected'};
                                                profileNewRow += `>` + classes[a].description + `</option>`;
                                            }
                                            profileNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return profileNewRow;
                case 'profile_id':
                    var profileNewRow = '';
                    profileNewRow +=  `<td>
                                        <span class="select">
                                            <select class=" editable-select quick-add set-up "  id="profile_id" name="transactions[`+trId+`][profile_id]"  modal="" type="table" key="">
                                                <option value="-1" selected ></option>`
                                            for (var a = 0; a < names.length; a++) {
                                                profileNewRow += `<option value='` + names[a].id + `'`;
                                                if(value == names[a].id){ profileNewRow += 'selected'};
                                                profileNewRow += `>` + names[a].vendor + `</option>`;
                                            }
                                            profileNewRow +=`	</select>
                                            </span>
                                    </td>`;
                    return profileNewRow;

            }
            
        },
        createNewRow: function(){


                $('body').on('focus', '.createNewRow', function () {
                    console.log('createNewRow');
                    var thisTr = $(this).closest('tr');
                    var tdCount = $(thisTr).find('td').length;
                    var tds = $(thisTr).find('td');
                    console.log(tdCount);
                    if($(thisTr).is(':last-child')){
                        var classNames =  $(thisTr).attr('class');
                        var trId =  $(thisTr).attr('id');
                        trId++;
                        var newTr = '<tr id="' + trId + '" class="' + classNames +'" style="display: table; width: 100%; table-layout: fixed; ">';
                        tds.each(function(){
                            var tdId = $(this).find('input').attr('id');
                            if($(this).find('input').hasClass("editable-select")){

                                var hiddenValue = $(this).find('input[type=hidden]').val();
                                var aa = formGrid.whichTd(tdId, hiddenValue, trId);
                                //var names =  $(this).find('input[type=hidden]').attr('name');
                                //console.log(names);
                                newTr += aa;
                            }else{
                                var className =  $(this).find('input').attr('class');
                                var names =  $(this).find('input').attr('name');
                                console.log(names);
                                newTr += `<td><input type="text" id="`+ tdId +`" class="`+className+`" name="transactions[`+trId+`][`+tdId+`]"  ></td>`;
                            }

                        })
                        newTr += `</tr>`;
                        $(thisTr).after(newTr);
                        $(thisTr).next('tr').find('.editable-select').editableSelect();
                    }
                });
                // $('body').on('focus', '.createNewRow', function () {
                //     console.log('createNewRow');
                //     var thisTr = $(this).closest('tr');
                //                             // var trId =  $(thisTr).attr('id');
                //                             // trId++;
                //                             // var test = thisTr.parent();
                //                             // console.log(thisTr);
                //                             // console.log(test);
                //                              if($(thisTr).is(':last-child')){
                //                             //     console.log('last Row');
                //                             //     var clonedRow = $(thisTr).clone();
                //                             //     console.log(clonedRow);
                //                             //     clonedRow.attr("id",trId);
                //                             //     clonedRow.find(":input").val("");
                //                             //     console.log(clonedRow);
                //                             //     var tds = clonedRow.find('td');
                //                             //     tds.each(function(){
                //                             //         var tdname = $(this).find('input').attr('name');
                //                             //         console.log(tdname)
                //                             //     })
                //                             //     $(thisTr).after(clonedRow);
                //     var Id =  $(this).closest('tr').attr('id');
                //     var classNames =  $(this).closest('tr').attr('class');
                //         console.log(thisTr);
                //         console.log(Id);
                //         console.log(classNames);
                //         var tds = thisTr.find('td');
                //         var newTr = `<tr id='`+ Id+ `' class='` + classNames + `' >`;
                //         tds.each(function(){
                //             if($(this).find('input').is(":hidden")){
                //                 var hiddenValue = $(this).find('input[type=hidden]').val();
                //                 var thisId = $(this).find('input').attr('id');
                //                 var aa = formGrid.whichTd(thisId, hiddenValue);
                //                 newTr += aa;
                //             }
                //             // var td = $(this).val();
                //             // console.log(td);
                //             // newTr +=`<td>` + td + `</td>`;
                //         })
                //         newTr += `</tr>`;
                //         $(thisTr).after(newTr);
                //     }
                // });
        },
        checkAll: function(){
            // check all and uncheck all checkboxes
            $('body').on('change', '.selectAllCheckboxes', function () { 	
                    var selectAll = $(this).closest('label');
                var rows = $(this).closest('.modal').find('.allAccounts');
                var checked = $(this).prop("checked");
                

                    rows.each(function(){
                        console.log(checked);
                        if($(this).prop("checked") != checked){
                            $(this).prop("checked",checked);
                            $(this).closest('label').toggleClass('active');
                            $(this).change();
                        }
                        
                    })
            });
        },
        radioButtons: function(body){
                body.find('label').each(function () {
                }).children(':checkbox, :radio').after('<div class="input"></div>').addClass('hidden').attr('aria-hidden', true);   
        },
        fillFirstRow: function(){
            $('body').on('click', '.fillFirstRow', function () {
                var row = $(this);
                //for editable-select
                var property_id = $(row).closest('.modal').find('section').find("#property_id").closest('p').find('input[type=hidden]').val();
                var property_text = $(row).closest('.modal').find('section').find("#property_id").closest('p').find('input').val();
                var class_id = $(row).closest('.modal').find('section').find("#class_id").closest('p').find('input[type=hidden]').val();
                var class_text = $(row).closest('.modal').find('section').find("#class_id").closest('p').find('input').val();
                //for other inputs
                var amount = $(row).closest('.modal').find('section').find("#amount").val();
                var memo = $(row).closest('.modal').find('section').find("#memo").val();
                //set editable-selects
                propertyInputFields = $(row).find("#property_id").closest('td').find('input');
                $(propertyInputFields[1]).val(property_id).change();
                $(propertyInputFields[0]).val(property_text).change();
                classInputFields = $(row).find("#class_id").closest('td').find('input');
                $(classInputFields[1]).val(class_id).change();
                $(classInputFields[0]).val(class_text).change();
                //set other inputs
                $(row).find("#amount").val(amount);
                $(row).find("#description").val(memo);
                $(row).removeClass("fillFirstRow");
            });
    }
        		
  //}
        //}
       /*  ,datepickerFunction: function (input, dateName = null){
            $('[data-toggle="datepicker"]').datepicker();
            var inputValue = $(input).val();
            console.log('newest datepicker');
            console.log(inputValue);
            var date = new Date(inputValue);
           var newDate = date.getFullYear()  + '/' + (date.getMonth() + 1) + '/' + date.getDate();
           console.log(newDate);
            if($(input).next().is('input')){
               var hiddenInput = $(input).next('input');
            hiddenInput.val(newDate);
            }else{
                if(dateName){
                    var hiddenInput = `<input type="hidden" name="`+ dateName +`" value="` + newDate + `"/>`;
                }else{
                    var hiddenInput = `<input type="hidden" name="header[transaction_date]" value="` + newDate + `"/>`;
                }
               $(input).after(hiddenInput);
            }
           } */


};

var formGrid = new formGrid();
$(document).ready(function () {
    //formGrid.api();
    //formGrid.fillNextRow();
    //formGrid.calculateTotal();
    //formGrid.journalEntryTotal();
    //formGrid.createNewRow();
    //formGrid.checkAll();
    //formGrid.fillFirstRow();
    //formGrid.radioButtons();
    

});