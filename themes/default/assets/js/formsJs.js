var formsJs = function () {
    this.checkboxSpot = 0;
}
formsJs.prototype = {
        //populates all the forms through ajax calls
		getAjaxAccount: function(body, response, type){
            switch (type) {
                case 'applyRefund' :
                formsJs.getApplyRefundAjax(body, response);
                break;
                case 'check_to_print' :
                formsJs.getCheck_to_printAjax(body, response);
                break;
                case 'all_entities' :
                formsJs.getAllEntitiesAjax(body, response);
                break;
                case 'transactions_import' :
                //formsJs.setIn_courtName(checkbox, id);
                break;
                case 'in_court' :
                formsJs.getIn_courtAjax(body, response);
				break;
				case 'email_invoice' :
                formsJs.getEmail_invoiceAjax(body, response);
                break;
                case 'memorizedTransactions' :
                formsJs.getMemorizedTransactionsAjax(body, response);
                break;
                case 'utilities' :
                formsJs.getUtilitiesAjax(body, response);
                formsJs.utilitiesFilter(body, response);
                break;
                case 'cc_grid_charge' :
                formsJs.getCcGridChargeAjax(body, response);
                break;
            }
		},
        //show total charges selected
        selectedCharges: function (selectedCharges, type, modal){
            
            if(selectedCharges < 0){selectedCharges = 0;}
            if(type == 'Auto bills' && selectedCharges > 0){
                $(modal).find( "button[type ='submit']" ).prop("disabled", false);
            }
            if(type == 'Auto bills' && selectedCharges == 0){
                $(modal).find( "button[type ='submit']" ).prop("disabled", true);
            }
            $(modal).find( "#selectedCharges" ).html(selectedCharges +' ' + type +' selected');
        },
        //in all forms when checkboxes are clicked it sets the name and the count
        Checkbox: function Checkbox(checkbox, id){
            if($(checkbox).closest('table').attr('id') == 'autoBillsTable'){
                var selectedChargesHtml = $(checkbox).closest('.tabcontent').find( "#selectedCharges" ).html();
                var modal = $(checkbox).closest('.tabcontent');
                var modalType = 'autoBills';
            }else{
                var selectedChargesHtml = $(checkbox).closest('.modal').find( "#selectedCharges" ).html();
                var modal = $(checkbox).closest('.modal');
                var modalType = $(modal).attr('type');
            }
                var selectedChargesArray = selectedChargesHtml.split(" ");
                var selectedCharges = selectedChargesArray[0];
                var type = selectedChargesArray[1];
                if(selectedChargesArray[2] == "taxes"){type = type + selectedChargesArray[2]}
                if(selectedChargesArray[1] == "Auto"){type = type + ' ' + selectedChargesArray[2]}
                //console.log(selectedCharges);
            
            formsJs.setAccountName(checkbox, modalType, id);
            //update total charges selected when checkbox is clicked
            if($(checkbox).closest('label').hasClass('active')){
                selectedCharges++;
            }else{
                selectedCharges--;
            }
            formsJs.selectedCharges(selectedCharges, type, modal);
        },
        //sets the name in the forms
        setAccountName: function(checkbox, modalType, id){
            switch (modalType) {
                case 'propertyTax-grid' :
                formsJs.setpropertyTaxGridName(checkbox, id);
                break;
                case 'transaction-import' :
                formsJs.setImportName(checkbox, id);
                break;
                case 'cc-grid' :
                formsJs.setCcGridName(checkbox, id);
                break;
                case 'in_court' :
                formsJs.setIn_courtName(checkbox, id);
                break;
                case 'utilities' :
                formsJs.setUtilitiesName(checkbox, id);
                break;
				case 'check-grid' :
                formsJs.setCheckGridName(checkbox, id);
				break;
				case 'memorized-transactions' :
				formsJs.setMemorizedTransactionsName(checkbox, id);
                break;
                case 'autoBills' :
                formsJs.setAutoBillsName(checkbox, id);
                break;
                case 'capital' :
                formsJs.setCapitalName(checkbox, id);
                break;
                case 'Disburse' :
                    formsJs.setDisburseName(checkbox, id);
                break;
            }
        },
        setImportName: function(checkbox, id){

                if($(checkbox).parent('label').hasClass('active')){
                    console.log("setting account name");
                    var id = $(checkbox).closest('tr').attr('id');
                    $(checkbox).closest('tr').find('#id').attr('name', `import[` + id + `][id]`);	
                    $(checkbox).closest('tr').find('td').each (function( column, td) {
                        if($(td).hasClass('check-a')){return;}
                        var name = $(td).attr('id');
                        if(name){
                            $(td).find('input').attr('name', `import[` + id + `][` + name + `]`);
                        }
                    });
                }else{
                    $(checkbox).closest('tr').find('input').removeAttr('name');
                    console.log("deleting account name");
                }
        },
        setpropertyTaxGridName: function (checkbox, id){

            if($(checkbox).parent('label').hasClass('active')){
                  $(checkbox).closest('tr').find('#id').attr('name', `propertyTaxes[` + id+ `][id]`);
                  $(checkbox).closest('tr').find('#payment_acct').closest('td').find('input[type=hidden]').attr('name', `propertyTaxes[` + id+ `][payment_acct]`);
                  $(checkbox).closest('tr').find('#amount').attr('name', `propertyTaxes[` + id+ `][amount]`);
                  $(checkbox).closest('tr').find('#start_date').next('input').attr('name', `propertyTaxes[` + id+ `][start_date]`);
                  $(checkbox).closest('tr').find('#last_pay_date').next('input').attr('name', `propertyTaxes[` + id+ `][last_pay_date]`);
                console.log("setting account name");					
            }else{
                $(checkbox).closest('tr').find('input').removeAttr('name');
                console.log("deleting account name");
            }
                     
        },
        setCcGridName: function (checkbox, id){

            if($(checkbox).parent('label').hasClass('active')){
                console.log("setting account name");
                //sets the hidden input for id
                var idName = $(checkbox).closest('tr').find('input:hidden:first').attr('id');
                $(checkbox).closest('tr').find('input:hidden:first').attr('name', `transactions[` + id + `][` + idName + `]`);
                //sets the hidden input for all other tds
                $(checkbox).closest('tr').find('td').each (function( column, td) {
                    var name = $(td).attr('id');
                    if(name){
                        if($( td).children().hasClass("editable-select")){
                            $(checkbox).closest('tr').find('#' + name).closest('td').find('input[type=hidden]').attr('name', `transactions[` + id + `][` + name + `]`);
                        //console.log(name + "this is an editable-select");
                        }else{
                            $(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `transactions[` + id + `][` + name + `]`);
                        }
                    }
                });					
            }else{
                $(checkbox).closest('tr').find('input[type=hidden]').removeAttr('name');
                console.log("deleting account name");
            }
        },
        setIn_courtName: function (checkbox, id){

            if($(checkbox).parent('label').hasClass('active')){
                console.log("setting account name");

                $(checkbox).closest('tr').find('#id').attr('name', `in_court[` + id + `][id]`);
                $(checkbox).closest('tr').find('#case_num').find('input').attr('name', `in_court[` + id + `][case_num]`);
                $(checkbox).closest('tr').find('#attorney').find('input').attr('name', `in_court[` + id + `][attorney]`);
                $(checkbox).closest('tr').find('#follow_up_reason').find('input').attr('name', `in_court[` + id + `][follow_up_reason]`);
                $(checkbox).closest('tr').find('#warrant_requested').find('input').attr('name', `in_court[` + id + `][warrant_requested]`);
                $(checkbox).closest('tr').find('#warrant_issued').find('input').attr('name', `in_court[` + id + `][warrant_issued]`);
                $(checkbox).closest('tr').find('#follow_up_date').find('input[type=hidden]').attr('name', `in_court[` + id + `][follow_up_date]`);
                // var first = 0;
                // $(checkbox).closest('tr').find('td').each (function( column, td) {
                //     if(first != 0 && name != 'Property' && name != 'unit'){
                //         var name = $(td).attr('id');
                //         if(name == "follow_up_date"){
                //             $(td).find('input[type=hidden]').attr('name', `in_court[` + id + `][` + name + `]`);
                //         }else{
                //             $(td).find('input').attr('name', `in_court[` + id + `][` + name + `]`);
                //         }
                //     }
                //     first++;
                // });
            }else{
                $(checkbox).closest('tr').find('input').removeAttr('name');
                console.log("deleting account name");
            }
        },
        // 	setUtilitiesName:function (checkbox, id){
	 	// 		var spot = 0;
		// 		var allIdNames = [];
		//         if($(checkbox).parent('label').hasClass('active')){
		// 			console.log("setting account name");
		// 			//sets the hidden input for id
		// 			var idName = $(checkbox).closest('tr').find('input:hidden:first').attr('id');
		// 			$(checkbox).closest('tr').find('input:hidden:first').attr('name', `row[` + id + `][transactions][` + spot + `][` + idName + `]`);
		// 			//sets the hidden input for all other tds
		// 			$(checkbox).closest('tr').find('td').each (function( column, td) {
		// 				var name = $(td).attr('id');
		// 				if(name){
		// 					allIdNames.push(name);
		// 					if($( td).children().hasClass("editable-select")){
		// 						$(checkbox).closest('tr').find('#' + name).closest('td').find('input[type=hidden]').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
		// 					//console.log(name + "this is an editable-select");
		// 				}else{
		// 					//for direct payment
		// 					if(name == 'direct_payment' || name == 'last_paid_date' || name == 'billable'){
		// 						if(name == 'direct_payment'){
		// 							$(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][utilities][` + name + `]`);
		// 							$(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][utilities][` + name + `]`);
		// 						}else{
		// 							if(name == 'billable'){
		// 								$(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
		// 								$(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
		// 							}else{$(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][utilities][` + name + `]`);}}

		// 					}else{
		// 							if(name == 'util_usage' || name == 'estimate'){
		// 								$(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][transactions][` + spot + `][utility_trans][` + name + `]`);
		// 							}else{
		// 								//if they have a hidden input
		// 								if($(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').length > 0){
		// 									$(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
		// 								}else{
		// 									//if they don't have a hidden input
		// 									$(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
		// 								}
		// 							}
		// 						}

		// 					}
		// 				}
		// 			});
		// 			formsJs.setDetailsName($(checkbox).closest('tr'), id, allIdNames);
		// 		}else{
		// 			//$(checkbox).closest('tr').find('input[type=hidden]').removeAttr('name');
		// 			$(checkbox).closest('tr').find('input').removeAttr('name');
		// 			console.log("deleting account name");
		// 			formsJs.unSetDetailsName($(checkbox).closest('tr'));
		// 		}
	    // },
        //     setDetailsName:function (tr, id, idnames){
		// 	spot = 1;
		// 	console.log(idnames);
		// 	$(tr).nextUntil("tr.allTransactions").each(function(index, value) {
		// 		var arraySpot = 0;
		// 		   $(value).each(function(td, value2){
		// 				$(value2).find('td').each(function(td, value3){
		// 					if($(value3).find('input').length > 0){
		// 						if(idnames[arraySpot] == "direct_payment" || idnames[arraySpot] == "last_paid_date"){
		// 							$(value3).find('input').removeAttr('name');
		// 							//$(value3).find('input').attr('name', `row[`+id+`][utilities][`+idnames[arraySpot]+`]`);
		// 						}else{
		// 							if(idnames[arraySpot] == "billable"){
		// 								//$(value3).find('label').attr('for', `Dbillable_` + billableSpot);
		// 								//$(value3).find('input:not(:hidden)').attr('id', `Dbillable_` + billableSpot);
		// 								$(value3).find('input').attr('name', `row[`+id+`][transactions][`+spot+`][`+idnames[arraySpot]+`]`);
		// 								billableSpot++;
		// 							}else{
		// 								if(idnames[arraySpot] == "util_usage" ||idnames[arraySpot] == "estimate"){
		// 									$(value3).find('input').attr('name', `row[`+id+`][transactions][`+spot+`][utility_trans][`+idnames[arraySpot]+`]`);
		// 								}else{
		// 									$(value3).find('input').attr('name', `row[`+id+`][transactions][`+spot+`][`+idnames[arraySpot]+`]`);
		// 								}
		// 							}

		// 						}
		// 						arraySpot++;
		// 					}
		// 				});
		// 			  	console.log(value2);
		// 		   });
		// 		   spot++;
		// 		});
		// 		JS.checkboxes();
		// },
		// unSetDetailsName: function (tr){
		// 	$(tr).nextUntil("tr.allTransactions").each(function(index, value) {
		// 		   $(value).find('input').removeAttr('name');
		// 		});
		// },
		setCheckGridName: function(checkbox, id){
				//function setAccountName(checkbox, id, account_id){

			if($(checkbox).parent('label').hasClass('active')){
				console.log("setting account name");
				//sets the hidden input for id
				var idName = $(checkbox).closest('tr').find('input:hidden:first').attr('id');
				$(checkbox).closest('tr').find('#th_id').attr('name', `transactions[` + id + `][th_id]`);
				$(checkbox).closest('tr').find('#id').attr('name', `transactions[` + id + `][id]`);
				$(checkbox).closest('tr').addClass('setName');
			}else{
				$(checkbox).closest('tr').find('input[type=hidden]').removeAttr('name');
				console.log("deleting account name");
			}
			//}
        },
        setCapitalName: function(checkbox, id){

            if($(checkbox).parent('label').hasClass('active')){
                console.log("setting account name");
                //sets the hidden input for id
                $(checkbox).closest('tr').find('#id').attr('name', `capital[` + id + `][id]`);
                $(checkbox).closest('tr').find('#property_name').attr('name', `capital[` + id + `][property_name]`);
                $(checkbox).closest('tr').find('#capital_call_amount').attr('name', `capital[` + id + `][capital_call_amount]`);
                $(checkbox).closest('tr').find('.as_of_date').attr('name', `capital[` + id + `][as_of_date]`);
                $(checkbox).closest('tr').find('.due_date').attr('name', `capital[` + id + `][due_date]`);
                // $(checkbox).closest('tr').addClass('setName');
            }else{
                $(checkbox).closest('tr').find('input').removeAttr('name');
                console.log("deleting account name");
            }
         },

         setDisburseName: function(checkbox, id){

            if($(checkbox).parent('label').hasClass('active')){
                $(checkbox).closest('tr').find('input').each(function(){
                    $(this).attr('name', `capital[` + id + `][` + this.id + `]`)
                });                
            }else{
                $(checkbox).closest('tr').find('input').removeAttr('name');
            }
         },

			setMemorizedTransactionsName: function (checkbox, id){

            if($(checkbox).parent('label').hasClass('active')){
                console.log("setting account name");
                //sets the hidden input for id
                var idName = $(checkbox).closest('tr').find('input:hidden:first').attr('id');
                //allIds.push(id);
                //console.log(allIds);
                $(checkbox).closest('tr').find('#id').attr('name', `transactions[` + id + `]`);
            }else{
                $(checkbox).closest('tr').find('input[type=hidden]').removeAttr('name');
                //console.log(allIds);
                //var position = $.inArray(id, allIds);

            //if ( ~position ) allIds.splice(position, 1);
                console.log("deleting account name");
                //console.log(allIds);
            }
    	},
        setUtilitiesName: function(checkbox, id){
            //function setAccountName(checkbox, id){
                var spot = 0;
               var allIdNames = [];
               if($(checkbox).parent('label').hasClass('active')){
                   console.log("setting account name");
                   //sets the hidden input for id
                   var idName = $(checkbox).closest('tr').find('input:hidden:first').attr('id');
                   $(checkbox).closest('tr').find('input:hidden:first').attr('name', `row[` + id + `][transactions][` + spot + `][` + idName + `]`);
                   //sets the hidden input for all other tds
                   $(checkbox).closest('tr').find('td').each (function( column, td) {
                       var name = $(td).attr('id');
                       if(name){
                           allIdNames.push(name);
                           if($( td).children().hasClass("editable-select") || $( td).children().hasClass("fastEditableSelect")){
                               $(checkbox).closest('tr').find('#' + name).closest('td').find('input[type=hidden]').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
                           //console.log(name + "this is an editable-select");
                       }else{
                           //for direct payment
                           if(name == 'direct_payment' || name == 'last_paid_date' || name == 'billable'){
                               if(name == 'direct_payment'){
                                   $(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][utilities][` + name + `]`);
                                   $(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][utilities][` + name + `]`);
                               }else{
                                   if(name == 'billable'){
                                       $(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
                                       $(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
                                   }else{$(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][utilities][` + name + `]`);}}

                           }else{
                                   if(name == 'util_usage' || name == 'estimate'){
                                       $(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][transactions][` + spot + `][utility_trans][` + name + `]`);
                                   }else{
                                       //if they have a hidden input
                                       if($(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').length > 0){
                                           $(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
                                       }else{
                                           //if they don't have a hidden input
                                           $(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
                                       }
                                   }
                               }

                           }
                       }
                   });
                   formsJs.setUtilitiesDetailsName($(checkbox).closest('tr'), id, allIdNames);
               }else{
                   //$(checkbox).closest('tr').find('input[type=hidden]').removeAttr('name');
                   $(checkbox).closest('tr').find('input').removeAttr('name');
                   console.log("deleting account name");
                   formsJs.unSetUtilitiesDetailsName($(checkbox).closest('tr'));
               }
   //}
        },
        setUtilitiesDetailsName: function(tr, id, idnames){
            //function setDetailsName(tr, id, idnames){
                var spot = 1;
                console.log(idnames);
                $(tr).nextUntil("tr.allTransactions").each(function(index, value) {
                    var arraySpot = 0;
                       $(value).each(function(td, value2){
                            $(value2).find('td').each(function(td, value3){
                                if($(value3).find('input').length > 0){
                                    if(idnames[arraySpot] == "direct_payment" || idnames[arraySpot] == "last_paid_date"){
                                        $(value3).find('input').removeAttr('name');
                                        //$(value3).find('input').attr('name', `row[`+id+`][utilities][`+idnames[arraySpot]+`]`);
                                    }else{
                                        if(idnames[arraySpot] == "billable"){
                                            //$(value3).find('label').attr('for', `Dbillable_` + billableSpot);
                                            //$(value3).find('input:not(:hidden)').attr('id', `Dbillable_` + billableSpot);
                                            $(value3).find('input').attr('name', `row[`+id+`][transactions][`+spot+`][`+idnames[arraySpot]+`]`);
                                            //billableSpot++;
                                        }else{
                                            if(idnames[arraySpot] == "util_usage" ||idnames[arraySpot] == "estimate"){
                                                $(value3).find('input').attr('name', `row[`+id+`][transactions][`+spot+`][utility_trans][`+idnames[arraySpot]+`]`);
                                            }else{
                                                $(value3).find('input').attr('name', `row[`+id+`][transactions][`+spot+`][`+idnames[arraySpot]+`]`);
                                            }
                                        }

                                    }
                                    arraySpot++;
                                }
                            });
                              console.log(value2);
                       });
                       spot++;
                    });
                    JS.checkboxes();
            //}
        },
        unSetUtilitiesDetailsName: function(tr){
            $(tr).nextUntil("tr.allTransactions").each(function(index, value) {
                $(value).find('input').removeAttr('name');
             });
        },
        setAutoBillsName: function(checkbox, id){
			if($(checkbox).parent('label').hasClass('active')){
				console.log("setting account name");
				//sets the hidden input for id
				$(checkbox).closest('tr').find('#id').attr('name', `transactions[` + id + `]`);
			}else{
				$(checkbox).closest('tr').find('#id').removeAttr('name');
			}
        },
        //when input is entered it triggers the checkbox
		selectTransactionClass: function (){
			$('body').on('keyup', '.selectTransactionClass', function() {
				formsJs.selectTransaction(this);
			});
		},
		//selectTransactionClass();
		selectTransaction: function (input){
				if(!$(input).closest('tr').find('.allAccounts').closest('td').find('label').hasClass('active')){
						$(input).closest('tr').find('.allAccounts').closest('td').find('label').addClass('active');
						$(input).closest('tr').find('.allAccounts').change();
				}
		},
        //checks/unchecks green check, and sets/unsets hidden input name upon click 
		greenCheckTd: function (check, type){
			console.log('????????????????????????');
			var thisTR = $(check).closest('tr');
			var greenCheck = thisTR.find('#greenCheck');
			greenCheck.toggle();
			if(greenCheck.css('display') === 'none')
			{
				//removes name attr
				$(thisTR).find('#id').removeAttr('name');
                if(type == "applyRefund"){
				    $(thisTR).find('.allInputAmounts').removeAttr('name');
                }
                if(type == "receivePayment"){
				    $(thisTR).find('#received_payament_input_amount2').removeAttr('name');
                }
				console.log('unchecked');
				formsJs.emptyInput(check);
				
			}
			else
			{
				//sets name attr
				var id = thisTR.find('#id').val();
				console.log(id);
				$(thisTR).find('#id').attr('name', `applied_payments[` + id + `][transaction_id_b]`);
                if(type == "applyRefund"){
				    $(thisTR).find('.allInputAmounts').attr('name', `applied_payments[` + id + `][amount]`);
                }
                if(type == "receivePayment"){
				    $(thisTR).find('#received_payament_input_amount2').attr('name', `applied_payments[` + id + `][amount]`);
                }
				
				console.log('checked');
				formsJs.fillInput(check, type);
			}
			formsJs.updateUnapplied($(check).closest('.modal'), type);
			formsJs.triggerCalculate(check);
        },
        //updates unapplied for apply refund and receive payment
        updateUnapplied: function (modal, type){
            var trs,formTotal;
            if(type == 'applyRefund'){
                var lmrAmount =  Number($(modal).find('#sdApplyAmount').val());
                var sdAmount = Number($(modal).find('#lmrApplyAmount').val());
                 formTotal = lmrAmount + sdAmount;
                 trs = $(modal).find('#applyRefundTransactions').find('tr');
            }
            if(type == 'receivePayment'){
                 formTotal =  Number($(modal).find('#received_amount').val());
                 trs = $(modal).find('#received_payment_table').find('tr');
            }
                        
                        
			
			var total = 0;
			trs.each(function(){
				if( $(this).find('.greenCheckTd').find('i').css('display') != 'none' ){
					var amount = Number($(this).find('.allInputAmounts').val());
					total += amount;
				}
			});
			var unApplied = currency(formTotal - total);
			$(modal).find('#unappliedAmount').empty();
			if(unApplied < 0){
				unApplied = '$' +0.00; $(trs).find('.allInputAmounts').val('$' + 0.00);$(trs[0]).find('.allInputAmounts').trigger('keyup');$(trs).find('.greenCheckTd').find('i').css('display', "none");}
            //$(modal).find('#unappliedAmount').html('$' + Number(unApplied).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
            //$(modal).find('#unappliedAmount').html('$' + Number(unApplied).toFixed(2));
            $(modal).find('#unappliedAmount').html('$' + Number(unApplied));
    },
		fillInput: function (check, type){
            var trs, formTotal;
			if($(check).closest('tr').find('.allInputAmounts').val() != 0){return};
			var openBalance = Number($(check).closest('tr').find('.openBalance').html());
            if(type == "applyRefund"){
                 var lmrAmount =  Number($(check).closest('.modal').find('#sdApplyAmount').val());
                 var sdAmount = Number($(check).closest('.modal').find('#lmrApplyAmount').val());
                 formTotal = lmrAmount + sdAmount;
                 trs = $(check).closest('.modal').find('#applyRefundTransactions').find('tr');
            }
             if(type == 'receivePayment'){
                 formTotal = Number($(check).closest('.modal').find('#received_amount').val());
                 trs = $(check).closest('.modal').find('#received_payment_table').find('tr');
             }
			var total = 0;
			trs.each(function(){
				if( $(this).find('.greenCheckTd').find('i').css('display') != 'none' ){
					var amount = Number($(this).find('.allInputAmounts').val());
					total += amount;
				}
			});
			var unApplied = formTotal - total;
			if(openBalance <= unApplied){
				$(check).closest('tr').find('.allInputAmounts').val(currency(openBalance));
                //$(check).closest('tr').find('.allInputAmounts').val(Number(openBalance));
			}else{
				$(check).closest('tr').find('.allInputAmounts').val(currency(unApplied));
                //$(check).closest('tr').find('.allInputAmounts').val(Number(unApplied));
			}
		},
		emptyInput: function (check){
			$(check).closest('tr').find('.allInputAmounts').val(currency(0));
		},
        triggerCalculate: function (td){
			$(td).closest('tr').find('.allInputAmounts').trigger('keyup');
		},
        //fills in next input with amount still needed
		amountInput: function (tr, type){
			var topTRvalue = $(tr).prevAll( "tr.allTransactions:first").find('#totalAmount').val();
			var topTR = $(tr).prevAll( "tr.allTransactions:first");
			var total = 0;
			  $(topTR).nextUntil(".allTransactions", 'tr' ).each(function(index, value) { 
				   total += Number($(value).find('.amount').val());
				});
				var mainTotal = Number(topTRvalue);
				//total += Number(amount);
				if(total !== mainTotal){
					if(total > mainTotal){
						var minusAmount = total - mainTotal;
						var nextDetailsTr = $(tr).next('tr');
                        //if(type == 'ccModal'){
						 $(nextDetailsTr).find('#amountToAdd').val("-" + minusAmount.toFixed(2));
                        // }if(type == 'utilitiesModal'){
                        //     $(nextDetailsTr).find('#amountToAdd').val("Totals don't equal!");
                        // }
						console.log(total + " total is more " + mainTotal);
					}if(total < mainTotal){
						var addAmount = mainTotal - total;
						var nextDetailsTr = $(tr).next('tr');
						 $(nextDetailsTr).find('#amountToAdd').val(addAmount.toFixed(2));
					}
				}
        },
        //switches in court column name 
		columnName: function (key){
			var newKey = key;
			switch (key) {
				case 'Warrant issued' :
						newKey = "warrant_issued";
					break;
				case 'Follow up date' :
						newKey = "follow_up_date";
					break;
				case 'Follow up reason' :
						newKey = "follow_up_reason";
					break;
				case 'Warrant requested' :
						newKey = "warrant_requested";
					break;
			}
			return newKey;
		},
		getCheck_to_printAjax: function(body, transactions){
			//function getAjaxAccount(body, transactions){

		$(body).empty();
		selected = 0;
		formsJs.selectedCharges();
        var newRow = "";
		if(transactions){

				for (var i = 0; i < transactions.length; i++) {
					newRow += `<tr id="`+ transactions[i].id +`" role="row" class="allTransactions">
								<td style="width: 4% !important;">
								<input type="hidden" name="" value="`+ transactions[i].id +`" id="th_id">
								<input type="hidden" name="" value="`+ transactions[i].account_id +`" id="id">
									 
								</td>
								<td style="width: 4%;" class="check-a">
									<label for="`+ i+`" class="checkbox">
										<input type="checkbox" id="`+ i +`" name="`+ i +`" class="hidden allAccounts" aria-hidden="true"
										onchange="formsJs.Checkbox($(this),`+ transactions[i].id +`, 'check_to_print');">
											<div class="input"></div>
									</label>
								</td>`;
                            $.each(transactions[i], function(key, value) {
								//console.log(key, value);
								if(key != "id" && key != "date" && key != "account_id"){
									newRow +=	`<td id="`+ key +`" style="width: 11% ">` + value +`</td>`;
								}
                            });
                            newRow +=	`</tr>`;
				} 
			}else{
				newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No Checks for this account.</td></tr></div>`;
				//<td style="width:30%; text-align:center"> No Transactions for this account.</td><td style="width:30%"></td></tr>`;
			}
            body.append(newRow);
			//$(body).find('.editable-select').editableSelect();
			JS.checkboxes();
	//}
		},
		getAllEntitiesAjax: function(body, entities){
			//function getAjaxAccount(body, entities){

			$(body).empty();
			var newRow = "";
			if(entities){

					for (var i = 0; i < entities.length; i++) {
						newRow += `<tr id="`+ entities[i].id +`" data-id="`+ entities[i].id +`" data-type="entities" role="row" style="display: table; width: 100%; table-layout: fixed;">
									<td style="width: 4% !important;">
									<input type="hidden" name="" value="`+ entities[i].id +`" id="id">
										
									</td>`;
								$.each(entities[i], function(key, value) {
									if(key != "id"){
										newRow +=	`<td  style="width: 9% ">` + value +`</td>`;
									}
								});
								newRow +=	`</tr>`;
					} 
				}else{
					newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No Entities.</td></tr></div>`;
				}
				body.append(newRow);
		//}
    },
    getcapitalAjax: function(date, body){
        var sqldate =  date.replace(/\//g, '-');
        console.log(date);
        $.get(JS.baseUrl+ 'disburse/capitalFunction/'+sqldate, function(data) {
            var data = JSON.parse(data);
            console.log(data);
            var newRow = '';
            $(data).each(function(index, value) { 
                var that = $(value)[0];
                //console.log(that.name);
                var expense = Number(that.expense);
                var income = Number(that.income);
                var calculatedDist = Number(that.bank_balance) - Number(that.mortgage) - Number(that.payables) - Number(that.additional_expense)-Number(that.sd)-Number(that.lmr);
                var ni = income - expense;
                //console.log(that.name + ' bank' + that.bank_balance + ' mortgage' + that.mortgage + ' payables' + that.payables + ' sd' + that.sd  + ' lmr' + that.lmr);
                //console.log(calculatedDist);
                newRow += `<tr>
                            <td style="width: 4% !important;" class="check-a">
                                <label for="`+ formsJs.checkboxSpot +`" class="checkbox">
                                    <input type="checkbox" id="`+ formsJs.checkboxSpot +`" name="`+ formsJs.checkboxSpot +`" class="hidden allAccounts" aria-hidden="true"
                                    onchange="formsJs.Checkbox($(this),`+ that.id + `);">
                                        <div class="input"></div>
                                </label>
                            </td>
                            <td id="`+ that.id + `">`+ that.name + `</td>
                            <td><input type="text" id="bank" readonly class="updateDisb" value="`+that.bank_balance + `"/></td>
                            <td><input type="text" id="payables" readonly class="updateDisb minus" value="`+that.payables + `"/></td>
                            <td><input type="text" id="mortgage" class="updateres minus" value="`+ that.mortgage + `"/></td>
                            <td><input type="text" id="additional_expense" class="updateres minus" value="`+ that.additional_expense + `"/></td>
                            <td><input type="text" id="included_in_payables" class="updateres" /></td>
                            <td><input type="text" id="memo"/></td>
                            <td><input type="text" id="sd" readonly class="updateDisb minus" value="`+ that.sd + `"/></td>
                            <td><input type="text" id="lmr" readonly class="updateDisb minus" value="`+ that.lmr + `"/></td>
                            <td><input type="text" id="ni" readonly class="updateres ifneg" value="`+number_format(ni.toFixed(2)) + `"/></td>
                            <td id="updateDisb">$`+ number_format(calculatedDist.toFixed(2)) + `</td>
                            <td><input type="text" id="capital_call_amount" value ="`+number_format(calculatedDist.toFixed(2))+`"/></td>
                            <input type="hidden" id="id" value="`+ that.id + `"/>
                            <input type="hidden" id="property_name" value="`+ that.name + `"/>
                            <input type="hidden" id = "updateres" value="`+ that.mortgage + `"/>
                         </tr>`;
                         formsJs.checkboxSpot++;
             });
             $(body).empty().append(newRow);
             $(body).find('.as_of_date').val(date);
             var due_date = $(body).closest('.modal').find('#capital_due_date').val();
             $(body).find('.due_date').val(due_date);
             JS.checkboxes();
        });
    },

    getDisburseAjax: function(date, body){
        var sqldate =  date.replace(/\//g, '-');
        console.log('disbiurse');
        $.get(JS.baseUrl+ 'disburse/capitalFunction/'+sqldate, function(data) {
            var data = JSON.parse(data);
            console.log(data);
            var newRow = '';
            $(data).each(function(index, value) { 
                var that = $(value)[0];
                //console.log(that.name);
                var expense = Number(that.expense);
                var income = Number(that.income);
                var calculatedDist = Number(that.bank_balance) - Number(that.mortgage) - Number(that.payables) - Number(that.additional_expense)-Number(that.sd)-Number(that.lmr);
                var ni = income - expense;
                //console.log(that.name + ' bank' + that.bank_balance + ' mortgage' + that.mortgage + ' payables' + that.payables + ' sd' + that.sd  + ' lmr' + that.lmr);
                //console.log(calculatedDist);
                newRow += `<tr>
                            <td style="width: 4% !important;" class="check-a">
                                <label for="`+ formsJs.checkboxSpot +`" class="checkbox">
                                    <input type="checkbox" id="`+ formsJs.checkboxSpot +`" name="`+ formsJs.checkboxSpot +`" class="hidden allAccounts" aria-hidden="true"
                                    onchange="formsJs.Checkbox($(this),`+ that.id + `);">
                                        <div class="input"></div>
                                </label>
                            </td>
                            <td id="`+ that.id + `">`+ that.name + `</td>
                            <td><input type="text" id="bank" readonly class="updateDisb" value="`+that.bank_balance + `"/></td>
                            <td><input type="text" id="payables" readonly class="updateDisb minus" value="`+that.payables + `"/></td>
                            <td><input type="text" id="mortgage" class="updateres minus" value="`+ that.mortgage + `"/></td>
                            <td><input type="text" id="additional_expense" class="updateres minus" value="`+ that.additional_expense + `"/></td>
                            <td><input type="text" id="included_in_payables" class="updateres" /></td>
                            <td><input type="text" id="memo"/></td>
                            <td><input type="text" id="sd" readonly class="updateDisb minus" value="`+ that.sd + `"/></td>
                            <td><input type="text" id="lmr" readonly class="updateDisb minus" value="`+ that.lmr + `"/></td>
                            <td><input type="text" id="ni" readonly class="updateres ifneg" value="`+number_format(ni.toFixed(2)) + `"/></td>
                            <td id="updateDisb">$`+ number_format(calculatedDist.toFixed(2)) + `</td>
                            <td><input type="text" id="capital_call_amount" value ="`+number_format(calculatedDist.toFixed(2))+`"/></td>
                            <input type="hidden" id="id" value="`+ that.id + `"/>
                            <input type="hidden" id="property_name" value="`+ that.name + `"/>
                            <input type="hidden" id = "updateres" value="`+ that.mortgage + `"/>
                         </tr>`;
                         formsJs.checkboxSpot++;
             });
             $(body).empty().append(newRow);
             $(body).find('.as_of_date').val(date);
             var due_date = $(body).closest('.modal').find('#capital_due_date').val();
             $(body).find('.due_date').val(due_date);
             JS.checkboxes();
        });
    },  
	getIn_courtAjax: function(body, transactions){
			//function getAjaxAccount(body, transactions){

		$(body).empty();
		selected = 0;
		formsJs.selectedCharges(0, "Transactions", $(body).closest('.modal'));
        var newRow = "";
        var newkey = "";
		if(transactions){

				for (var i = 0; i < transactions.length; i++) {
					newRow += `<tr id="`+ transactions[i].id +`" role="row" class="allTransactions getCourtNotes" style="display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;">
								<td style="width: 4% !important;">
								<input type="hidden" name="" value="`+ transactions[i].id +`" id="id">
									
								</td>
								<td style="width: 4%;" class="check-a">
									<label for="`+ i+`" class="checkbox">
										<input type="checkbox" id="`+ i +`" name="`+ i +`" class="hidden allAccounts" aria-hidden="true"
										onchange="formsJs.Checkbox($(this),`+ transactions[i].id +`);">
											<div class="input"></div>
									</label>
								</td>`;
                            $.each(transactions[i], function(key, value) {
                                var checkboxNum = 0;
								//console.log(key, value);
								if(key != "id" && key != "notes"){
                                        //console.log(key);
                                        newKey = formsJs.columnName(key);
                                        //console.log(newKey);
                                    if(key == "Follow up date"){ 
                                        newRow +=	`<td id="`+ newKey +`" style="width: 15% "><input type ="text" data-toggle="datepicker" name="" value="` + value +`"></td>`;
                                    }
                                    else if(key == "Warrant requested" || key == "Warrant issued"){
                                        //console.log('warrant here')
                                        newRow +=	`<td id="`+ newKey +`" class="check-a" style="width: 15% ">
                                                     <label for="`+ newKey + checkboxNum +`" class="checkbox `;
                                            newRow += value == 1 ?  ' active' : ''; 
                                            newRow += `"><input type="hidden" name="" value="0" /><input type="checkbox"`;
                                            newRow += value == 1 ? ' checked ' : '';
                                            newRow +=` id="`+ newKey +checkboxNum +`" value="1" name="" class="hidden" aria-hidden="true"/><div class="input"></div>
                                                        </label>
                                                     </td>`;
                                                     checkboxNum++;
                                    }else if(key == 'Name' || key == 'Property' || key == 'unit'){
                                        newRow +=	`<td  style="width: 15% ">` + value +`</td>`;
                                    }else{
                                        newRow +=	`<td id="`+ newKey +`"  style="width: 15% "><input type="text" value="` + value +`"></td>`;
                                    }
								}
                            });
                            newRow +=	`</tr>`;
				} 
			}else{
				newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No one in court.</td></tr></div>`;
            }
            body.append(newRow);
			JS.checkboxes();
            JS.datePickerInit(body);
	//}
},
	getMemorizedTransactionsAjax: function (body, transactions){

		$(body).empty();
		selected = 0;
		formsJs.selectedCharges(0, "Transactions", $(body).closest('.modal'));
        var newRow = "";
		if(transactions){

				for (var i = 0; i < transactions.length; i++) {
					newRow += `<tr id="`+ transactions[i].id +`" role="row" class="allTransactions" style="display: table; width: 100%; table-layout: fixed;">
								<td style="width: 4% !important;">
								<input type="hidden" name="" value="`+ transactions[i].id +`" id="id">
									 
								</td>
								<td style="width: 4%;" class="check-a">
									<label for="`+ i+`" class="checkbox">
										<input type="checkbox" id="`+ i +`" name="`+ i +`" class="hidden allAccounts" aria-hidden="true"
										onchange="formsJs.Checkbox($(this),`+ transactions[i].id +`);">
											<div class="input"></div>
									</label>
								</td>`;
                            $.each(transactions[i], function(key, value) {
								//console.log(key, value);
								if(key != "id"){
									newRow +=	`<td  style="width: 15% ">` + value +`</td>`;
								}
                            });
                            newRow +=	`</tr>`;
				} 
			}else{
				newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px;  margin: 0 auto; "><tr><td style="width:100%">No memorized Transactions.</td></tr></div>`;
				//<td style="width:30%; text-align:center"> No Transactions for this account.</td><td style="width:30%"></td></tr>`;
			}
            body.append(newRow);
			//$(body).find('.editable-select').editableSelect();
			JS.checkboxes();
	},
	getEmail_invoiceAjax: function(body, profiles){
		//function getAjaxAccount(body, profiles){
        console.log(profiles);
		$(body).empty();
		var newRow = "";
		if(profiles){

				for (var i = 0; i < profiles.length; i++) {
					newRow += `<tr id="`+ i +`" role="row" class="allTransactions" style="display: table; width: 100%; table-layout: fixed;">
								<td style="width: 4% !important;">
								<input type="hidden" name="" value="`+ profiles[i].profile_id +`" id="profile_id">
                                <input type="hidden" name="" value="`+ profiles[i].lease_id +`" id="lease_id">
                                <input type="hidden" name="" value="`+ profiles[i].profile_id +`-`+ profiles[i].lease_id +`" id="profile_lease_id">
									 
								</td>
								<td style="width: 6%;" class="check-a emailCheckBox">
								<label for="email" class="checkbox `;
									newRow += profiles[i].email_statements == 1 ?  ' active' : ''; 
									newRow += `"><input type="hidden" name="email" value="0" /><input type="checkbox" value="1" `;
									newRow += profiles[i].email_statements == 1 ? 'checked' : '';
									newRow +=` id="email" name="email" class="hidden allAccounts" aria-hidden="true"><div class="input"></div></label>
								</td>
								<td style="width: 6%;" class="check-a">
								<label for="mail" class="checkbox `;
									newRow += profiles[i].mail_statements == 1 ?  ' active' : ''; 
									newRow += `"><input type="hidden" name="mail" value="0" /><input type="checkbox" value="1" `;
									newRow += profiles[i].mail_statements == 1 ? 'checked' : '';
									newRow +=` id="mail" name="mail" class="hidden allAccounts" aria-hidden="true"><div class="input"></div></label>
								</td>
								<td style="width: 8%;" class="check-a">
								<label for="in_court" class="checkbox `;
									newRow += profiles[i].in_court == 1 ?  ' active' : ''; 
                                    newRow += `"><input type="hidden" name="in_court" value="0" /><input type="checkbox" value="1" `;
									newRow += profiles[i].in_court == 1 ? 'checked' : '';
									newRow +=` id="in_court" name="in_court" class="hidden allAccounts" aria-hidden="true"><div class="input inCourtCheckbox"></div></label>
								</td>`;
                            $.each(profiles[i], function(key, value) {
								//console.log(key, value);
								if(key != "id" && key != "profile_id" && key != "lease_id" && key != "mail_statements" && key != "email_statements" && key != "in_court"){
									newRow +=	`<td  style="width: 15% ">` + value +`</td>`;
								}
                            });
                            newRow +=	`</tr>`;
				} 
			}else{
				newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No Profiles with a balance.</td></tr></div>`;
			}
            body.append(newRow);
			JS.checkboxes();
	//}

	//}
        },
        // getUtilitiesAjax: function(body, utilities){
        //     //function getAjaxAccount(body, utilities){
        //         $(body).empty();
        //         selected = 0;
        //         //selectedCharges();
        //         formsJs.selectedCharges(0, "Bills", $(body).closest('.modal'));
        //         var newRow = "";
        //         var newRow2 = "<tr><td>helloo</td></tr>";
        //         if(utilities){
        //                 // var checkboxSpot = 0;
        //                         //$(utilities).each(function(){
        //                             for(var i =0; i < utilities.length; i++){

        //                             var that = utilities[i];
        //                             newRow += `<tr id="`+ that.id +`" role="row" class="allTransactions editing" style="display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;">
        //                             <input type="hidden" name="" value="`+ that.id +`" id="id">
        //                                 <td style="width: 4% !important;" class="link">
        //                                      <a href="#" class="selectTransactionClass" onclick="formsJs.expand($(this).closest('td'))"><span class="hidden">More</span></a>
        //                                  </td>
        //                                 <td style="width: 4%;" class="check-a">
        //                                      <label for="`+ formsJs.checkboxSpot +`" class="checkbox">
        //                                          <input type="checkbox" id="`+ formsJs.checkboxSpot +`" name="`+ formsJs.checkboxSpot +`" class="hidden allAccounts" aria-hidden="true"
        //                                          onchange="formsJs.Checkbox($(this),`+ that.id +`);">
        //                                          <div class="input"></div>
        //                                      </label>
        //                                  </td>`;
        //                             $.each(that, function( index, value ) {
        //                                 if(index != "id"){
        //                                     if(index == "unit_id" || index == "property_id"|| index == "account_id"|| index == "profile_id"){
        //                                         if(index != 'account_id' && value < 1){
        //                                             newRow += '<td style="width: 5%;"></td>';
        //                                         }else{
        //                                             //newRow += formsJs.UtilitywhichTd(index, value, that.id)
        //                                             newRow += `<td id="` + index +`" style="width: 5%;"><input type="hidden"  name="" value="`+ value +`"></td>`;
        //                                         }
        //                                     }else{
        //                                         if(index == "direct_payment" || index == "billable"){
        //                                             newRow += `<td id="` + index +`"  class="check-a" style="width: 5%;">`;
        //                                             newRow += `<label for="` +index + `_` + formsJs.checkboxSpot +`" class="checkbox`;
        //                                                 if(value == 1)newRow +=  ' active';
        //                                             newRow += `"><input type="hidden" name="" value="0" /><input type="checkbox"  value="1" `;
        //                                                 if(value == 1)newRow +=' checked';
        //                                             newRow +=  ` id="`+index + `_` + formsJs.checkboxSpot +`" name=""  class="hidden" aria-hidden="true"><div class="input"></div></label></td>`;
        //                                         }else{
        //                                             newRow += `<td id="` + index +`" style="width: 5%;">` + value ;
        //                                             newRow += `<input type="hidden"  name="" value="`+ value +`"></td>`;
        //                                         }
        //                                     }

        //                                 }
        //                                 //console.log(index);
        //                                 //console.log(value);
        //                             });
        //                             newRow += `
        //                                 <td id="last_paid_date" style="width: 5%;">
        //                                     <input data-toggle="datepicker" id="last_paid_date" class="selectTransactionClass" type="text"  name="" value="` + new Date() + `">
        //                                 </td>
        //                                 <td id="amount" style="width: 5%;">
        //                                 <input type="text" class="selectTransactionClass"  name="" value="" placeholder="Enter Amount">			
        //                                 </td>
        //                                 <td id="util_usage" style="width: 5%;">
        //                                     <input type="text" class="selectTransactionClass"  name="" value="" placeholder="Enter Usage">									
        //                                 </td>
        //                                 <td id="estimate" class="check-a" style="width: 5%;">
        //                                     <label for="estimate` + formsJs.checkboxSpot +`" class="checkbox">
        //                                     <input type="hidden" name="" value="0" /><input type="checkbox"  value="1"  id="estimate` + formsJs.checkboxSpot +`" name=""  class="hidden" aria-hidden="true">
        //                                     <div class="input"></div></label></td></tr>`;
        //                                 formsJs.checkboxSpot++;
        //                             //estimateSpot++;

        //                         }//);

        //                  }
        //             // }else{
        //             // 	newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No Transactions for this account.</td></tr></div>`;
        //             // 	//<td style="width:30%; text-align:center"> No Transactions for this account.</td><td style="width:30%"></td></tr>`;
        //             // }
        //             //console.log(newRow);
        //             body.append(newRow);
        //             //$(body).find('.editable-select').editableSelect();
        //             $(body).find('.fastEditableSelect').fastSelect();
        //             //JS.checkboxes();
        //             //JS.datePickerInit(body);
        //     //}
        // },
        getUtilitiesAjax: function(body, utilities){
            //function getAjaxAccount(body, utilities){
                $(body).empty();
                selected = 0;
                //selectedCharges();
                formsJs.selectedCharges(0, "Bills", $(body).closest('.modal'));
                var newRow = "";
                if(utilities){
                        // var checkboxSpot = 0;
                                //$(utilities).each(function(){
                                    for(var i =0; i < utilities.length; i++){

                                    var that = utilities[i];
                                    newRow += `<tr id="`+ that.id +`" role="row" class="allTransactions editing" style="display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;">
                                    <input type="hidden" name="" value="`+ that.id +`" id="id">
                                        <td style="width: 4% !important;" class="link">
                                             <a href="#" class="selectTransactionClass" onclick="formsJs.expand($(this).closest('td'))"><span class="hidden">More</span></a>
                                         </td>
                                        <td style="width: 4%;" class="check-a">
                                             <label for="`+ formsJs.checkboxSpot +`" class="checkbox">
                                                 <input type="checkbox" id="`+ formsJs.checkboxSpot +`" name="`+ formsJs.checkboxSpot +`" class="hidden allAccounts" aria-hidden="true"
                                                 onchange="formsJs.Checkbox($(this),`+ that.id +`);">
                                                 <div class="input"></div>
                                             </label>
                                         </td>`;
                                         newRow +=`<td id="profile_id" style="width: 5%;">` + (that.profileName ? that.profileName: '') +`<input type="hidden"  name="" value="` + (that.profile_id ? that.profile_id: '') +`"></td>`;
                                         newRow +=`<td id="property_id" style="width: 5%;">` + (that.propertyName ? that.propertyName: '') +`<input type="hidden"  name="" value="` + (that.property_id ? that.property_id: '') +`"></td>`;
                                         newRow +=`<td id="unit_id" style="width: 5%;">` + (that.unitName ? that.unitName: '') +`<input type="hidden"  name="" value="` + (that.unit_id ? that.unit_id: '') +`"></td>`;
                                         newRow +=`<td id="description" style="width: 5%;"><input type="text" class="instantUpdate" value="` + (that.description ? that.description: '') +`"></td>`;
                                         newRow +=`<td id="account" style="width: 5%;">` + (that.account ? that.account: '') +`<input type="hidden"  name="" value="` + (that.account ? that.account: '') +`"></td>`;
                                         newRow +=`<td id="utility_type" style="width: 5%;">` + (that.utility_type ? that.utility_type: '') +`<input type="hidden"  name="" value="` + (that.utility_type_id ? that.utility_type: '') +`"></td>`;
                                         newRow +=`<td id="old_last_paid_date" style="width: 5%;">` + (that.old_last_paid_date ? that.old_last_paid_date: '') +`<input type="hidden"  name="" value="` + (that.old_last_paid_date ? that.old_last_paid_date: '') +`"></td>`;
                                         newRow += `<td id="direct_payment"  class="check-a" style="width: 5%;">`;
                                         newRow += `<label for="direct_payment_` + formsJs.checkboxSpot +`" class="checkbox`;
                                         if(that.direct_payment == 1)newRow +=  ' active';
                                         newRow += `"><input type="hidden" name="" value="0" /><input type="checkbox"  value="1" `;
                                         if(that.direct_payment == 1)newRow +=' checked';
                                         newRow +=  ` id="direct_payment_` + formsJs.checkboxSpot +`" name=""  class="hidden" aria-hidden="true"><div class="input"></div></label></td>`;
                                         newRow +=  `<td style="text-align:center; width: 5%;" id="account_id" class="formGridAccountTd">
                                                 <span class="select">
                                                     <select stype="account" default="` + (that.account_id ? that.account_id: '') +`" class=" fastEditableSelect quick-add set-up "  id="account_id" name=""  modal="" type="table" key="">
                                                        <option value="-1" selected ></option>	</select>
                                                 </span>
                                            </td>`
                                        newRow += `<td id="billable"  class="check-a" style="width: 5%;">`;
                                         newRow += `<label for="billable_` + formsJs.checkboxSpot +`" class="checkbox`;
                                         if(that.billable == 1)newRow +=  ' active';
                                         newRow += `"><input type="hidden" name="" value="0" /><input type="checkbox"  value="1" `;
                                         if(that.billable == 1)newRow +=' checked';
                                         newRow +=  ` id="billable_` + formsJs.checkboxSpot +`" name=""  class="hidden" aria-hidden="true"><div class="input"></div></label></td>`;
                                    newRow += `
                                        <td id="last_paid_date" style="width: 5%;">
                                            <input data-toggle="datepicker" id="last_paid_date" class="selectTransactionClass" type="text"  name="" value="` + new Date() + `">
                                        </td>
                                        <td id="amount" style="width: 5%;">
                                        <input type="text" class="selectTransactionClass"  name="" value="" placeholder="Enter Amount">			
                                        </td>
                                        <td id="util_usage" style="width: 5%;">
                                            <input type="text" class="selectTransactionClass"  name="" value="" placeholder="Enter Usage">									
                                        </td>
                                        <td id="estimate" class="check-a" style="width: 5%;">
                                            <label for="estimate` + formsJs.checkboxSpot +`" class="checkbox">
                                            <input type="hidden" name="" value="0" /><input type="checkbox"  value="1"  id="estimate` + formsJs.checkboxSpot +`" name=""  class="hidden" aria-hidden="true">
                                            <div class="input"></div></label></td>
                                            <td id="memo" style="width: 5%;">
                                            <input type="text"  class="instantUpdate" value="` + (that.memo ? that.memo: '') +`">									
                                        </td></tr>`;
                                        formsJs.checkboxSpot++;
                                    //estimateSpot++;

                                }//);

                         }
                    // }else{
                    // 	newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No Transactions for this account.</td></tr></div>`;
                    // 	//<td style="width:30%; text-align:center"> No Transactions for this account.</td><td style="width:30%"></td></tr>`;
                    // }
                    //console.log(newRow);
                    body.append(newRow);
                    //$(body).find('.editable-select').editableSelect();
                    $(body).find('.fastEditableSelect').fastSelect();
                    JS.checkboxes();
                    JS.datePickerInit(body);
            //}
        },
        getCcGridChargeAjax: function(body, account){
            //function getCcGridAjaxAccount(body, account){
                //onChange="JS.loadList('api/getUnitsProperty', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , '#unitId',  $(this).closest('.allTransactions')) ;
                //JS.loadList('api/getAccountsProperty', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , '#accountId',  $(this).closest('.allTransactions'));"
                //onChange="unitsApi($(this).closest(\'.select\').find(\'input[type=hidden]\').val() , $(this).closest('tr').find('#unitId'));
                //accountsApi($(this).closest(\'.select\').find(\'input[type=hidden]\').val() , $(this).closest('tr').find('#accountId'));"
                $(body).empty();
                selected = 0;
                formsJs.selectedCharges(0, "Charges", $(body).closest('.modal'));
                var newRow = "";
                if(account){
                        for (var i = 0; i < account.length; i++) {
                            newRow += `<tr id="`+ account[i].id +`" role="row" class="allTransactions">
                                        <input type="hidden" name="" value="`+ account[i].id +`" id="ofxId">
                                        <td style="width: 3% !important;" class="link">
                                            <a href="#" class="selectTransactionClass" onclick="formsJs.expand($(this).closest('td'))"><span class="hidden">More</span></a>
                                        </td>
                                        <td style="width: 4%;" class="check-a">
                                            <label for="`+ i+`" class="checkbox">
                                                <input type="checkbox" id="`+ i +`" name="`+ i +`" class="hidden allAccounts" aria-hidden="true"
                                                onchange="formsJs.Checkbox($(this),`+ account[i].id +`);">
                                                    <div class="input"></div>
                                            </label>
                                        </td>
                                        <td style="width: 7%; text-align:center" id="transaction_date">`+ account[i].date +`
                                            <input type="hidden" value="`+ account[i].date +`">
                                        </td>
                                        <td name="" style="width: 7%; text-align:center" id="amount">`+ account[i].amount +`
                                            <input id="totalAmount"  class="amount" type="hidden" name="" value="`+ account[i].amount +`">
                                        </td>
                                        <td style="width: 11%; text-align:center; overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px" id="description">`+ account[i].description +`
                                            <input type="hidden"  name="" value="`+ account[i].description +`">
                                        </td>
                                        <td style="width: 11%; text-align:center" id="transaction_ref">`+ account[i].ref +`
                                            <input type="hidden"  name="" value="`+ account[i].ref +`">
                                        </td>
                                        <td id="card_member" style="width: 9%; text-align:center">`+ account[i].card_member +`
                                            <input type="hidden"  name="" value="`+ account[i].card_member +`">
                                        </td>
                                        <td style="width: 8%; text-align:center" id="property_id">
                                            <span class="select">
                                                <select class="w135 editable-select quick-add set-up formGridPropertySelected"  name="property_id"  modal="" type="table" key="">
                                                        <option value="-1" selected ></option>`
                                                    for (var j = 0; j < properties.length; j++) {
                                                        newRow += `<option value='` + properties[j].id + `'>` + properties[j].name + `</option>`;
                                                    }
                                                    newRow +=`	</select>
                                                </span>
                                        </td>
                                        <td style="width: 8%; text-align:center" id="account_id" class="formGridAccountTd">
                                            <span class="select">
                                                <select class="w135 editable-select quick-add set-up "  id="accountId" name="account_id"  modal="" type="table" key="">
                                                    <option value="-1" selected ></option>`
                                                for (var a = 0; a < accounts.length; a++) {
                                                    newRow += `<option value='` + accounts[a].id + `'>` + accounts[a].name + `</option>`;
                                                }
                                                newRow +=`	</select>
                                                </span>
                                        </td>
                                        <td style="width: 8%; text-align:center" id="unit_id">
                                            <span class="select">
                                                <select class="w135 editable-select quick-add set-up formGridUnitSelect"  id="unitId" name="unit_id"  modal="" type="table" key="">
                                                    <option value="-1" selected ></option>
                                                </select>
                                            </span>
                                        </td>
                                        <td style="width: 8%; text-align:center; padding-top: 5px;">
                                            <span class="input-file">
                                                    <label for="p-image" style = "margin-bottom: 0px"><input type="file" style ="display:none;" name="original"  id="p-image" targetimg="#original-lease"> <a class="receipt" style="text-decoration: underline; font-size: 13px; color: green;">Reciept</a></label>
                                            </span>
                                        </td>
                                    </tr>`;
                        }
                    }else{
                        newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No Transactions for this account.</td></tr></div>`;
                        //<td style="width:30%; text-align:center"> No Transactions for this account.</td><td style="width:30%"></td></tr>`;
                    }
                    body.append(newRow);
                    $(body).find('.editable-select').editableSelect();
                    formsJs.editMultipleBox();
                    JS.checkboxes();
            //}
        },
        UtilitywhichTd: function(tdId, value, trId){
            //function whichTd(tdId, value, trId){

                switch (tdId) {
                    case 'property_id' :
                            var property = properties.find(x => x.id == value);
                            //console.log(property);
                        var propertyNewRow = '';
                        propertyNewRow += `<td id="property_id" style=" text-align:center; width: 5%;">`
                        propertyNewRow +=   property.name;
                        propertyNewRow += `<input type="hidden"  name="" value="`+ value +`">`;
                                                // for (var a = 0; a < properties.length; a++) {
                                                //     //unitNewRow += `<option value='` + units[a].id + `'`;
                                                //         if(value == properties[a].id){
                                                //             propertyNewRow +=   properties[a].name ;
                                                //             propertyNewRow += `<input type="hidden"  name="" value="`+ value +`">`;
                                                //         }
                                                // }

                                        `</td>`;
                        return propertyNewRow;
                    case 'account_id':
                            var accountNewRow = '';
                            accountNewRow +=  `<td style="text-align:center; width: 5%;" id="account_id" class="formGridAccountTd">
                            <span class="select">
                                <select stype="account" class=" fastEditableSelect quick-add set-up "  id="account_id" name=""  modal="" type="table" key="">
                                    <option value="-1" selected ></option>	</select>
                                </span>
                        </td>`;
                        return accountNewRow;
                    case 'unit_id':
                            var unit = units.find(x => x.id == value);
                            //console.log(test);
                            var unitNewRow = '';
                            unitNewRow +=  `<td id="unit_id" style="text-align:center; width: 5%;">`;
                            unitNewRow +=   unit.name;
                            unitNewRow += `<input type="hidden"  name="" value="`+ value +`">`;
                            // <span class="select">
                            //     <select class="w135 editable-select quick-add set-up formGridUnitSelect"  id="unit_id" name="transactions[`+trId+`][unit_id]"  modal="" type="table" key="">
                            //         <option value="-1" selected ></option>`
                                // for (var a = 0; a < units.length; a++) {
                                //     //unitNewRow += `<option value='` + units[a].id + `'`;
                                //         if(value == units[a].id){
                                //             unitNewRow +=   units[a].name ;
                                //             unitNewRow += `<input type="hidden"  name="" value="`+ value +`">`;
                                //         }
                                // }
                                //unitNewRow +=`	</select>`
                                // </span>
                               unitNewRow +=`</td>`;
                        return unitNewRow;
                    case 'class_id':
                        var classNewRow = '<td>' + value +'</td>';
                        var profileNewRow = '';
                        profileNewRow +=  `<td style=" text-align:center; width: 5%;">
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
                            var name = names.find(x => x.id == value);
                        var profileNewRow = '';
                        profileNewRow +=  `<td id="profile_id" style=" text-align:center; width: 5%;">`;
                        profileNewRow +=   name.name;
                        profileNewRow += `<input type="hidden"  name="" value="`+ value +`">`;
                                        // for (var a = 0; a < names.length; a++) {
                                        //             if(value == names[a].id){
                                        //                 profileNewRow +=   names[a].name ;
                                        //                 profileNewRow += `<input type="hidden"  name="" value="`+ value +`">`;
                                        //             }
                                        //     }
                                       profileNewRow += `</td>`;
                        return profileNewRow;

                }

            //}
        },
        instantUpdate: function(){
            $('body').on('focusout', '.instantUpdate', function (e){
                var type = $(this).closest('td').attr('id');
                var id = $(this).closest('tr').attr('id');
                var value = $(this).val();
                console.log(id);
                console.log(type);
                $.post(JS.baseUrl +'transactions/updateUtility/',
                    {
                        'type': type,
                        'value': value,
                        'id': id
                    }, function (data) {
                        console.log('success' + data);
                    }
                );
            })
        },
        expand: function(td){
            //toggles +/- symbol and calls expandCCCharge(td) if there are no details
            //function expand(td)
           // {
               var modal = $(td).closest('.modal');
                spot = 1;
                $(td).toggleClass("toggle");
                var getId = td.closest('tr').attr('id');
                var id = "." + getId;
                console.log(id);
                $( id ).toggle();
                formsJs.selectTransaction(td);
                if(!td.closest('tr').next('tr').hasClass("details")){
                    if(modal.attr('type') == 'utilities'){
                        formsJs.expandUtilities(td);
                    }
                    if(modal.attr('type') == 'cc-grid'){
                        formsJs.expandCcGridCharge(td);
                    }
                }

            //}
        },
        expandUtilities: function(e){
            	//makes new rows for details
            //function expandCCCharge(e)
            //{
            var allIdNames2 = [];
            var tdInfo = [];
            var thisTR = e.closest('tr');
            if(e.closest('tr').hasClass('allTransactions')){
                    var getTR = e.closest('tr');
                console.log('yes class');
                }else{
                    var getTR = $(e.closest('tr')).prevAll( "tr.allTransactions:first");
                    console.log('no class');
                }

            var clonedRow = $(getTR).clone();
            var newRow2 = "";
                newRow2 = `<tr id="`+ getTR.attr('id') +`" class="details `+ getTR.attr('id') +` editing" role="row" style="display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;">
                                                <td style="width: 3% !important;" class="link"></td>
                                                
                                                <td class="remove" onclick="formsJs.removeTR($(this));"><a href="#" class="remove"><i class="icon-x"></i> <span>Remove</span></a></td>`;
            var tdNum = 0;
            $(getTR).find('td').each (function( column, td) {
                    if($(td).attr('id')){allIdNames2.push($(td).attr('id') );}

                if(tdNum > 1){
                    //for editable select
                    if($(td).find('input').hasClass('fastEditableSelect')){
                        var esValue = $(td).closest('td').find('input[type=hidden]').val();
                        tdInfo.push(esValue);
                        newRow2 += formsJs.UtilitywhichTd($(td).attr('id'), esValue, getTR.attr('id') );
                        // newRow2 += `<td>` + esValue ;
                        // newRow2 += `<input type="hidden"  name="details[`+ getTR.attr('id') +`][`+ spot +`][description]" value="`+ description +`"></td>`;
                    }else{
                        //for last paid date
                        if($(td).attr('id') == 'last_paid_date'){
                            //console.log($(td).find('input[type=hidden]').val());
                            var newDate = new Date($(td).find('input[type=hidden]').val());
                            newDate = (newDate.getMonth() + 1) + '/' + newDate.getDate() + '/' +  newDate.getFullYear();
                            //console.log(newDate);
                            newRow2 += `<td><input type="hidden">`+ newDate + `</td>`;
                        }else{
                                var oldTd = $(td).clone();
                                //console.log($(td).attr('id') );
                                //console.log($(oldTd).html());
                                if($(oldTd).html()){
                                    newRow2 += `<td `;
                                    if($(td).attr('id') == 'billable' || $(td).attr('id') == 'estimate'){
                                        newRow2 += `<td class="check-a">
                                            <label for="D` + $(td).attr('id') + formsJs.checkboxSpot +`" class="checkbox">
                                            <input type="hidden" name="" value="0" /><input type="checkbox"  value="1"  id="D` + $(td).attr('id') + formsJs.checkboxSpot +`" name=""  class="hidden" aria-hidden="true">
                                            <div class="input"></div></label></td>`;
                                            //billableSpot++;
                                    }else{
                                        //used for finding this td to add class to input for adding row
                                        if($(td).attr('id') == 'amount'){newRow2 += `class="forAdding"`}
                                        newRow2 +=`>` + $(oldTd).html() + `</td>`;
                                    }
                                }else{
                                    newRow2 += `<td><input id="`+ $(td).attr('id') +`" type="hidden"></td>`;
                                }
                        }
                    }
                }
                tdNum++;
                formsJs.checkboxSpot++;
            });
            newRow2 += `<td></td></tr>`;
            var total = tdInfo[4];


            $(thisTR).after(newRow2);
            $(thisTR).next('tr').find('.forAdding').find('input').addClass('addRow');
            $('body').find('.editable-select').editableSelect();
            $(thisTR).next('tr').find('.fastEditableSelect').fastSelect();
            formsJs.setUtilitiesDetailsName(getTR, getTR.attr('id'), allIdNames2);
            spot++;

            console.log('expandCCCharge clicked');
            //detailsDescription();
            //}
        },
        expandCcGridCharge: function(e){
                    //function expandCCCharge(e)
            //{

            var tdInfo = [];
            var getTR = e.closest('tr');
            $(getTR).find('td').each (function( column, td) {

                if($(td).hasClass('description')){
                    var description = $(td).closest('td').find('input[type=hidden]').val();
                    tdInfo.push(description);
                }else{
                    tdInfo.push($(td).text());
                }
            });
            var selectedProperty = getTR.find('#property_id').closest('td').find('input[type=hidden]').val();
            var selectedAccount = getTR.find('#accountId').closest('td').find('input[type=hidden]').val();
            var selectedUnit = getTR.find('#unitId').val();
            //console.log(tdInfo[7]);
            var total = tdInfo[4];
            console.log(selectedProperty);
            //onChange="JS.loadList('api/getUnitsProperty', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , '#unitId',  $(this).closest('.allTransactions')) ;
            //JS.loadList('api/getAccountsProperty', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , '#accountId',  $(this).closest('.allTransactions'));"
            var newRow2 = `<tr id="`+ getTR.attr('id') +`" class="details `+ getTR.attr('id') +`" role="row">
            
                                <td style="width: 3% !important;" class="link">
                                </td>
                                <td class="remove" onclick="formsJs.removeTR($(this));"><a href="#" class="remove"><i class="icon-x"></i> <span>Remove</span></a></td>
                                <td style="width: 7%; text-align:center">`+ tdInfo[2] +`</td>
                                <td style="width: 9%; text-align:center"><input type="text" id="amountToAdd" style="width: 130px;" name="details[`+ getTR.attr('id') +`][`+ spot +`][amount]" class="amount"
                                onmousedown="formsJs.expandCcGridCharge($(this)); this.onmousedown=null;" onfocusout="formsJs.amountInput($(this).closest('tr'), $(this).closest('.modal').attr('id'))"></td>
                                <td class="description" style="width: 11%; text-align:center; overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px">
                                    <input type="text" class="detailsDescription"  value="`+ tdInfo[4] +`">
                                    <input type="hidden"  name="details[`+ getTR.attr('id') +`][`+ spot +`][description]" value="`+ tdInfo[4] +`">
                                </td>
                                <td style="width: 11%; text-align:center">`+ tdInfo[5] +`</td>
                                
                                <td style="width: 9%; text-align:center">`+ tdInfo[6] +`</td>
                                <td style="width: 8%; text-align:center" id="property_id">
                                            <span class="select">
                                                <select class="w135 editable-select quick-add set-up formGridPropertySelected" id="property_id" name="property_id"  modal="" type="table" key=""
                                                    >
                                                        <option value="-1" selected ></option>`
                                                    for (var j = 0; j < properties.length; j++) {
                                                        newRow2 += `<option value='` + properties[j].id + `'`;
                                                        if(selectedProperty == properties[j].id){ newRow2 += 'selected'};
                                                        newRow2 +=`>` + properties[j].name + `</option>`;
                                                    }
                                                    newRow2 +=`	</select>
                                                </span>
                                        </td>
                                        <td style="width: 8%; text-align:center" id="account_id" class="formGridAccountTd">
                                            <span class="select">
                                                <select class="w135 editable-select quick-add set-up "  id="accountId" name="account_id"  modal="" type="table" key="">
                                                    <option value="-1" selected ></option>`
                                                for (var a = 0; a < accounts.length; a++) {
                                                    newRow2 += `<option value='` + accounts[a].id + `'`;
                                                    if(selectedAccount == accounts[a].id){ newRow2 += 'selected'};
                                                    newRow2 += `>` + accounts[a].name + `</option>`;
                                                }
                                                newRow2 +=`	</select>
                                                </span>
                                        </td>
                                        <td style="width: 8%; text-align:center" id="unit_id">
                                            <span class="select">
                                                <select class="w135 editable-select quick-add set-up formGridUnitSelect"  id="unitId" name="unit_id"  modal="" type="table" key="">
                                                    <option value="-1" selected ></option>`
                                                    //if(selectedUnit){ newRow2 += selectedUnit};
                                                    //newRow2 += `</option>
                                            newRow2 += `</select>
                                            </span>
                                        </td>
                                <td style="width: 8%; text-align:center"></td>
                            </tr>`;
            $(getTR).after(newRow2);
            $(getTR).next('tr').find('.editable-select').editableSelect();
            //sets hidden input for details for all editable select
            $(getTR).next('tr').find('#property_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][property_id]`);
            $(getTR).next('tr').find('#account_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][account_id]`);
            $(getTR).next('tr').find('#unit_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][unit_id]`);
            $(getTR).next('tr').find('#profile_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][profile_id]`);
            spot++;
            JS.loadList('api/getUnitsProperty', selectedProperty , '#unitId',  $(getTR).closest('.allTransactions'));

           // }

        },
        removeTR: function(td){
            //function removeTR(td){
                //changes symbol if ther are no more details
                var modalType = $(td).closest('.modal').attr('type');
                if(!$(td).closest('tr').next('tr').hasClass("details") && !td.closest('tr').prev('tr').hasClass("details")){
                    console.log("changing symbol");
                    $(td).closest('tr').prev('tr').find('td:first').toggleClass("toggle");
                }
                if(!$(td).closest('tr').next('tr').hasClass("details")){
                        console.log("adding click event");
                        //creates click event on prev tr
                        $($(td).closest('tr').prev('tr')).one('click', function(){
                            if(modalType == 'utilities'){
                                expandCCCharge($(this));
                            }
                            if(modalType == 'cc-grid'){
                                expandCcGridCharge($(this));
                            }
                            console.log("click event clicked");
                        });
                }
                $(td).closest("tr").remove();
            //}
        },
        detailsDescription: function(){
        	//fills the hidden input with the value
            //function detailsDescription(){
                $('.detailsDescription').keyup(function() {
                    $(this).next('input').val($(this).val());
                });
           // }

        },
        addDetailRow: function(){
			$('body').on('click', '.addRow', function(){
				formsJs.expandUtilities($(this));
				$(this).removeClass("addRow");
			});
        },
        utilitiesFilter: function(body, account){
                console.log('utilities filter');
            
            filters(account, body);
               //from and to date eventlistner
                function filters(account, body){
                    var inbetweenDates = $(body).closest('.modal').find('#inbetweenDates');
                    var fromDate = $(body).closest('.modal').find('#fromDate');
                    var toDate = $(body).closest('.modal').find('#toDate');
                    var allDates = $(body).closest('.modal').find('#alldates');
                    //profile does nothing
                    var profile = $(body).closest('.modal').find('.list-choose #profile_id');
                    var chooseVendor = $(body).closest('.modal').find('#chooseVendor');
                    var allVendors = $(body).closest('.modal').find('#allVendors');
                    $(inbetweenDates).closest('label').on('click',  function(){
                         console.log('inbetweenDates');
                         var profile = $(this).closest('.modal').find('.list-choose #profile_id');
                         formsJs.filterUtilities(account, body, fromDate, toDate, profile);
                    });
                    $(fromDate).on('input',  function(){
                        $(inbetweenDates).closest('label').trigger('click');
                    });
                    $(toDate).on('input',  function(){
                        $(inbetweenDates).closest('label').trigger('click');
                   });

                    $(allDates).closest('label').on('click',  function(){
                        console.log('allDates');
                        $(toDate).val('');
                        $(fromDate).val('');
                        var profile = $(this).closest('.modal').find('.list-choose #profile_id');
                        formsJs.filterUtilities(account, body, fromDate, toDate, profile);
                   });
                   $(allVendors).closest('label').on('click',  function(){
                        $('body').find('#utilitiesModal').find('#profile_id').closest('li').find('input').val('');
                        //profile being passed in does nothing
                        formsJs.filterUtilities(account, body, fromDate, toDate, profile);
                    });
                   $('body').on('change','#utilitiesModal .list-choose #profile_id',  function(){
                        console.log('profile changed');
                        $(chooseVendor).closest('label').trigger('click');
                    });
                    $(chooseVendor).closest('label').on('click',  function(){
                        console.log('chooseVendor');
                        var profile = $(this).closest('.modal').find('.list-choose #profile_id');
                        formsJs.filterUtilities(account, body, fromDate, toDate, profile);
                   });
                }

        },
        //filters utilities based on dates and profiles
        filterUtilities: function(account, body, fromDate, toDate, profile){
                //filters transactions based on to and from dates
                //function filterUtilities(account, body, fromDate, toDate, profile){
                    console.log(fromDate);
                    //console.log(fromDate.value);
                    var dateSearchAccount = [];
                        for (var i = 0; i < account.length; i++) {
                            var oldDate = new Date(account[i].old_last_paid_date);
                            var accountProfile = account[i].profile_id;
                            var profilea = profile.closest('li').find('input[type=hidden]').val();
                            var newFromDate = new Date(fromDate.val());
                            var newToDate = new Date(toDate.val());
                                if((newFromDate > 0 ? oldDate >= newFromDate : true) && (newToDate > 0 ? oldDate <= newToDate : true)  && (profilea > 0 ? accountProfile == profilea : true)){
                                    dateSearchAccount.push(account[i]);
                                    console.log('showing');
                                    console.log($(body).find('tr#' + account[i].id));
                                    $(body).find('tr#' + account[i].id).show();
                                }else{
                                    console.log('hiding');
                                    console.log($(body).find('tr#' + account[i].id));
                                    $(body).find('tr#' + account[i].id).hide();
                                }
                        }
                        console.log(dateSearchAccount);
                        //formsJs.getUtilitiesAjax(body, dateSearchAccount);
                //}
        },
        //used to edit multiple lines on the cc grid form for accounts, properties and units
        editMultipleBox: function(){
			var editMultipleDropdown = `<tr id="multipleDisplay" style="display: none; margin-top: 8px; border: 2px solid black;">
											<td id="property_id">
												<span class="select">
													<select class="w135 editable-select quick-add set-up formGridPropertySelected multiple"  name="property_id"  modal="" type="table" key=""
													onselect="formsJs.editMultipleProperties($(this));"> 
															<option value="-1" selected ></option>`
														for (var j = 0; j < properties.length; j++) {
															editMultipleDropdown += `<option value='` + properties[j].id + `'>` + properties[j].name + `</option>`;
														}
														editMultipleDropdown +=`	</select>
													</span>
											</td>
											<td style="" id="account_id"  class="formGridAccountTd">
												<span class="select">
													<select class="w135 editable-select quick-add set-up "  id="accountId2" name="account_id"  modal="" type="table" key=""
													onselect="formsJs.editMultipleAccounts($(this));">
														<option value="-1" selected ></option>`
														for (var a = 0; a < accounts.length; a++) {
														editMultipleDropdown += `<option value='` + accounts[a].id + `'>` + accounts[a].name + `</option>`;
													}
													editMultipleDropdown +=`	</select>
													</span>
											</td>
											<td style="" id="unit_id">
												<span class="select">
													<select class="w135 editable-select quick-add set-up formGridUnitSelect"  id="unitId2" name="unit_id"  modal="" type="table" key=""
													onselect="formsJs.editMultipleUnits($(this));">
														<option value="-1" selected ></option>
													</select>
												</span>
											</td>
											<td class="remove" onclick="formsJs.closeMultiplePopup();"><a href="#" class="remove"><i class="icon-x"></i></a></td>
										</tr>`;
			$('#editMultipleDropdowns').append(editMultipleDropdown);
			$('#editMultipleDropdowns').find('.editable-select').editableSelect();
			//editMultiple($(this));
        },
        closeMultiplePopup:function (){
            $('#multipleDisplay').hide();
        },
		editMultipleProperties: function (model){
			var editPropertySelectedValue = $(model).closest('tr').find('#property_id').closest('td').find('input[type=hidden]').val();
			var editPropertySelectedText = $(model).closest('tr').find('#property_id').closest('td').find('input[type=hidden]').attr("text");
			//var editPropertySelectedText = $(model).closest('tr').find('#property_id').siblings('.es-list').find('li.selected').text();
			//var editPropertySelectedText = $(model).closest('tr').find('#property_id').closest('td').find("li:selected").text();
			var rows = $(model).closest('#ccModal').find('.allAccounts');
				rows.each(function(){
					 var row = $(this);
					 if($(row).closest('label').hasClass('active')){
						inputFields = $(row).closest('tr').find('#property_id').closest('td').find('input');
						$(inputFields[1]).val(editPropertySelectedValue).change();
						$(inputFields[0]).val(editPropertySelectedText).change();
						//console.log(model);
						//$(row).closest('tr').find('#property_id').siblings('.es-list').find('li.selected').data('value');
						//$(row).closest('tr').find('#property_id').find('li.selected').text()
						console.log(editPropertySelectedText);
					 }

				})
        },
        editMultipleAccounts: function (model){
			var editAccountSelectedValue = $(model).closest('tr').find('#account_id').closest('td').find('input[type=hidden]').val();
			var editAccountSelectedText = $(model).closest('tr').find('#account_id').closest('td').find('input[type=hidden]').attr("text");
			var rows = $(model).closest('#ccModal').find('.allAccounts');
				rows.each(function(){
					 var row = $(this);
					 if($(row).closest('label').hasClass('active')){
						inputFields = $(row).closest('tr').find('#account_id').closest('td').find('input');
						$(inputFields[1]).val(editAccountSelectedValue).change();
						$(inputFields[0]).val(editAccountSelectedText).change();
						//console.log(model);
						//$(row).closest('tr').find('#property_id').siblings('.es-list').find('li.selected').data('value');
						//$(row).closest('tr').find('#property_id').find('li.selected').text()
						console.log(editAccountSelectedText);
					 }

				})
		},
		editMultipleUnits: function (model){
			var editUnitSelectedValue = $(model).closest('tr').find('#unit_id').closest('td').find('input[type=hidden]').val();
			var editUnitSelectedText = $(model).closest('tr').find('#unit_id').closest('td').find('input[type=hidden]').attr("text");
			var rows = $(model).closest('#ccModal').find('.allAccounts');
			rows.each(function(){
					 var row = $(this);
					 if($(row).closest('label').hasClass('active')){
						inputFields = $(row).closest('tr').find('#unit_id').closest('td').find('input');
						$(inputFields[1]).val(editUnitSelectedValue).change();
						$(inputFields[0]).val(editUnitSelectedText).change();
						//console.log(model);
						//$(row).closest('tr').find('#property_id').siblings('.es-list').find('li.selected').data('value');
						//$(row).closest('tr').find('#property_id').find('li.selected').text()
						console.log(editUnitSelectedText);
					 }

				})
        },
        unitsApi: function (value, td){
			$(td).empty();
			var unitsDropdown = "";
				unitsDropdown +="<option value='0'>None</option>";
			for (var j = 0; j < units.length; j++) {
				if(units[j].property_id == value){
					unitsDropdown += `<option value='` + units[j].id + `'>` + units[j].name + `</option>`;
				}
			}
			unitsDropdown += "</select>";
			//$(td).append(unitsDropdown);
			td.editableSelect('resetSelect',unitsDropdown);
			$(td).find('.editable-select').editableSelect();
				//unitsDropdown +=`	</select>`;
		},
		accountsApi: function (value, td){
			$(td).empty();
			var accountsDropdown = "";
				accountsDropdown +="<option value='0'>None</option>";
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
					accountsDropdown += `<option value='` + accounts[j].id + `'>` + accounts[j].name + `</option>`;
					//console.log('first if!!');
				}
				// else{
				// 		if(propertyAccountsArray.includes(accounts[j].id )){
				// 		accountsDropdown += `<option value='` + accounts[j].id + `'>` + accounts[j].name + `</option>`;
				// 		//console.log('third if works!!' + accounts[j].name);
				// }
				// }
			}

			accountsDropdown += "</select>";
			//$(td).append(unitsDropdown);
			td.editableSelect('resetSelect',accountsDropdown);
			$(td).find('.editable-select').editableSelect();
				//unitsDropdown +=`	</select>`;
        },

        //on capital form updates 'Left over before NOI' on input
        updateBeforeNoi: function(){
			$('body').on('keyup', '.updateBeforeNoi', function(){
                 var beforeNoi = $(this).closest('tr').find('#beforeNoi');
                 var bank_balance = Number($(this).closest('tr').find('#bank_balance').html().replace(/\$|,/g, ''));
                 var payables = Number($(this).closest('tr').find('#payables').html().replace(/\$|,/g, ''));
                 var mortgage = Number($(this).closest('tr').find('#mortgage').val().replace(/\$|,/g, ''));
                 var additional_expense = Number($(this).closest('tr').find('#additional_expense').val().replace(/\$|,/g, ''));
                 var included_in_payables = Number($(this).closest('tr').find('#included_in_payables').val().replace(/\$|,/g, ''));
                 var total = bank_balance - payables - mortgage - additional_expense + included_in_payables;
                 beforeNoi.empty().html('$' + number_format(Number(total).toFixed(2)));
                 var afterNoi = $(this).closest('tr').find('#afterNoi');
                 var expense = Number($(this).closest('tr').find('#expense').html().replace(/\$|,/g, ''));
                 var income = Number($(this).closest('tr').find('#income').html().replace(/\$|,/g, ''));
                 var aftertotal = total - expense  + income;
                 afterNoi.empty().html('$' + number_format(Number(aftertotal).toFixed(2)));
			})
        },
        //capital_due_date onchange updates all the hidden inputs for due_date
        capital_due_date: function(input){
            $(input).closest('.modal').find('.due_date').val($(input).val());          
        },

        updateDisb: function(){
			$('body').on('keyup', '.updateDisb, .updateres', function(){
                console.log("changed");
                 var updateRes = $(this).closest('tr').find('#updateres');
                 var updateDisb = $(this).closest('tr').find('#updateDisb');
                 var row = $(this).closest('tr');
                 var calcDist = 0;
                 var calcRes = 0;

                     $(row).find('.updateres').each(
                        function(){

                            var numVal = Number($(this).val().replace(/\$|,/g, ''));

                            if ( !$(this).hasClass('ifneg') || numVal < 0){

                                    if ($(this).hasClass('minus')){
                                        calcDist -= numVal;
                                        calcRes +=  numVal;
                                    } else {
                                        calcDist += numVal;
                                        calcRes -=  numVal;
                                    }
                            }
                        }
                     );

                     $(row).find('.updateDisb').each(
                        function(){
                            var numVal = Number($(this).val().replace(/\$|,/g, ''));

                            if ( !$(this).hasClass('ifneg') || numVal < 0){

                                if ($(this).hasClass('minus')){
                                    calcDist -= numVal;
                                } else {
                                    calcDist += numVal;
                                }
                            }


                             
                        }
                     );

                     updateDisb.empty().html('$' + number_format(Number(calcDist).toFixed(2)));
                     updateRes.val(calcRes);
/*                  var bank_balance = Number($(this).closest('tr').find('#bank_balance').html().replace(/\$|,/g, ''));
                 var payables = Number($(this).closest('tr').find('#payables').html().replace(/\$|,/g, ''));
                 var mortgage = Number($(this).closest('tr').find('#mortgage').val().replace(/\$|,/g, ''));
                 var additional_expense = Number($(this).closest('tr').find('#additional_expense').val().replace(/\$|,/g, ''));
                 var included_in_payables = Number($(this).closest('tr').find('#included_in_payables').val().replace(/\$|,/g, ''));
                 var total = bank_balance - payables - mortgage - additional_expense + included_in_payables;
                 beforeNoi.empty().html('$' + number_format(Number(total).toFixed(2)));
                 var afterNoi = $(this).closest('tr').find('#afterNoi');
                 var expense = Number($(this).closest('tr').find('#expense').html().replace(/\$|,/g, ''));
                 var income = Number($(this).closest('tr').find('#income').html().replace(/\$|,/g, ''));
                 var aftertotal = total - expense  + income;
                 afterNoi.empty().html('$' + number_format(Number(aftertotal).toFixed(2))); */
			})
        },
        //capital_due_date onchange updates all the hidden inputs for due_date
        capital_due_date: function(input){
            $(input).closest('.modal').find('.due_date').val($(input).val());          
        }

}
    var formsJs = new formsJs();
    $(document).ready(function () {
         formsJs.selectTransactionClass();
         formsJs.detailsDescription();
         formsJs.addDetailRow();
         formsJs.instantUpdate();
         formsJs.updateBeforeNoi();
         formsJs.updateDisb();
         //formsJs.greenCheckTd();

		  $('body').on('click', '.closeCourtDiv', function (){;
				$('.table-c').css("width", "100%");
				//$('#in_court_body').css("width", "100%");
                $('#courtNotesDiv').css("display", "none");
                $('#utilityNotesDiv').css("display", "none");
			});
                    // printTemplate
        //choose invoices form prints/emails/updates in court
		$('body').on('click', '#printTemplate', function (e)
	{
		var invoicesSelected = [];
		e.preventDefault();
		var that = this;
		var trs = $(this).closest('.modal').find('#emailInvoice_body').find('tr');
			trs.each( function(){ 
				var court = $(this).find('input[name=in_court]:checked').length > 0 ? 1 : 0;
				var email = $(this).find('input[name=email]:checked').length > 0 ? 1 : 0;
				var print = $(this).find('input[name=mail]:checked').length > 0 ? 1 : 0;
				//if(email == 1 || print == 1 || court == 1){
					invoicesSelected.push({profile_id: $(this).find('input[id=profile_id]').val(),lease_id: $(this).find('input[id=lease_id]').val(), email: email, print: print, court:court});
				//}
				//console.log(invoicesSelected); 
			});
			if(invoicesSelected === undefined || invoicesSelected.length == 0){alert('Select  email or print.'); return;}
			$.post(JS.baseUrl+"invoice/sendInvoices", {
					'params': JSON.stringify(invoicesSelected)
				}, function (result) {
                    var endResult = true;
                    var result = JSON.parse(result);
                    if(result.emailTrs){
                        //console.log(result.emailTrs);
                        $(that).closest('.modal').find(".errors").remove();
                        $.each(result.emailTrs, function (i,item) {
                            var tr = $(that).closest('.modal').find('input[value="' + item.id+'"]').closest('tr');
                            if (item.type == 'danger') {
                                var tdCount = tr.children('td').length;
                                tr.css("border", "2px solid red");
                                tr.after("<tr class='errors' style='display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;'><td colspan='"+ tdCount +"' style='color:red;'>"+item.message+"</td><tr>");
                                endResult = false;
                                return;
                            }
                            if(item.type == 'success'){
                                tr.css("border" ,"2px solid #1bde1b");
                                tr.find('.emailCheckBox').html('<i class="icon-check" aria-hidden="true" ></i>');
                            }
                        })
                    }
                    // if(result.court){
                    //     var message = result.court;
                    //     JS.showAlert(message.type, message.message);
                    //     $(that).closest('.modal').hide();
                    // }
				//console.log(result);
				if(result.print){
                    $('body').find('#Checkarea').empty();
					$('body').find('#Checkarea').append(result.print);
					$('body').find("#Checkarea").addClass('print-section');
                    window.print();
                    $('body').find('#Checkarea').empty();
					// var typeId = $(that).closest('.modal').attr('type');
					// var openId = $(that).closest('.modal').attr('openModal-id');
					// $(that).closest('.modal').hide();
					// JS.openModalsObjectRemove(typeId, openId);
                }
                if(endResult == true){
                    if(result.court){
                        var message = result.court;
                        JS.showAlert(message.type, message.message);
                    }
                    var typeId = $(that).closest('.modal').attr('type');
					var openId = $(that).closest('.modal').attr('openModal-id');
					$(that).closest('.modal').hide();
					JS.openModalsObjectRemove(typeId, openId);
                }
                //$(that).closest('.modal').hide();
			});	
    });
        //choose invoice form- if user checks a tenant for in court it checks anyone else on that lease
		$('body').on('change', '#in_court', function (e){
			var trs = $(this).closest('.modal').find('#emailInvoice_body').find('tr');
			var that = this;
			var checked = $(this).prop("checked");
			var id = $(this).closest('tr').attr('id');
			console.log(checked);

			trs.each(function(){
				if(id != $(this).attr('id')){
					// console.log($(that).closest('tr').find('input[id=lease_id]').val());
					// console.log($(this).find('input[id=lease_id]').val());
					if($(that).closest('tr').find('input[id=lease_id]').val() == $(this).find('input[id=lease_id]').val()){
						if($(this).find('input#in_court').prop("checked") != checked){
								$(this).find('input#in_court').prop("checked",checked);
								$(this).find('input#in_court').closest('label').toggleClass('active');
								//$(this).find('input#in_court').change();
							}
					}
				}
			})
        });
        //prints the invoice table
		$('body').on('click', '#printInvoiceTable', function (){
			var thead = $(this).closest('form').find('table').find('thead').html();
			var tbody = $(this).closest('form').find('table').find('tbody').html();
			// console.log(thead);
			// console.log(tbody);
			$(this).closest('.modal').find('#printInvoiceTableDiv').find('#printInvoiceTableThead').empty().append(thead);
			$(this).closest('.modal').find('#printInvoiceTableDiv').find('#printInvoiceTableBody').empty().append(tbody);
			$(this).closest('.modal').find("#printInvoiceTableDiv").addClass('print-section');
			window.print();
			var typeId = $(this).closest('.modal').attr('type');
			var openId = $(this).closest('.modal').attr('openModal-id');
			$(this).closest('.modal').hide();
			JS.openModalsObjectRemove(typeId, openId);
        });
        $('body').on('click', '#editMultiple', function (e) { e.preventDefault(); $( "#multipleDisplay" ).css("display", "block"); $( "#multipleDisplay" ).show(); });
        $('body').on('click', '#exit', function () {$('body').off( "click", "#editMultipleDropdowns" );});





            //when user puts in amount it populates for receive payment
            $('body').on('focusout', '#received_amount', function () {
                var that = $(this);
                var received_amount = $(this).val();
                var charges = $(this).closest('.modal').find('.received_payment_row');
                var foundMatch = false;
                charges.each(function(){
                    var greenCheck = $(this).find('#greenCheck');
                    if(greenCheck.css('display') != 'none'){$(this).find('.greenCheckTd').trigger('click');}
                    var openBalance = Number($(this).find('.openBalance').html());
                    if(foundMatch == false && received_amount == openBalance){
                        $(this).find('.greenCheckTd').trigger('click');
                        foundMatch = true;
                    }
                })
                if(!foundMatch){
                    charges.each(function(){
                        // var greenCheck = $(this).find('#greenCheck');
                        // if(greenCheck.css('display') != 'none'){$(this).find('.greenCheckTd').trigger('click');}
                        var openBalance = Number($(this).find('.openBalance').html());
                        var amount = $(this).find('#received_payament_input_amount2').val();
                        if(received_amount <= 0 ){return;}
                        if(received_amount <= openBalance){
                            $(this).find('.greenCheckTd').trigger('click');
                            received_amount = received_amount - openBalance;
                            return;
                        }else{
                            $(this).find('.greenCheckTd').trigger('click');
                            received_amount = received_amount - openBalance;
                        }
                    })
                }
            });

    });




