var payBill = function () {

    this.allShown = [];
    this.allHidden = [];
    this.allVendorsShown = [];
    this.allPropertiesShown = [];
    this.spot = 0;
}
payBill.prototype = {

    pbcheck: function (row ){

            var rowIcon = $(row).find('#pay_bill_icon_check');
            var rowInput = $(row).find('.pay_bill_input')
            var rowAmount = $(row).closest('.pay_bill_row').find('#pay_bill_input_amount')

            // updates visibility and value
            rowIcon.css('visibility', 'visible');;
            rowInput.attr('value', '1');
            rowAmount.attr("disabled", false)

            let amount = currency($(row).closest('.pay_bill_row').find('#open_balance').text());
            $(row).closest('.pay_bill_row').find('#pay_bill_input_amount').val(amount.format())

            payBill.setAccountName1($(row).closest('.pay_bill_row'))

        },

        pbuncheck: function (row ){

            var rowIcon = $(row).find('#pay_bill_icon_check');
            var rowInput = $(row).find('.pay_bill_input')
            var rowAmount = $(row).closest('.pay_bill_row').find('#pay_bill_input_amount')

            // updates visibility and value
            rowIcon.css( 'visibility', 'hidden');
            rowInput.attr('value', '0');
        rowAmount.attr("disabled", true)

        $(row).closest('.pay_bill_row').find('#pay_bill_input_amount').val('0.00')

        payBill.removeName($(row).closest('.pay_bill_row'))
        
        },

        // function pbSetAccount(text ,value, form ){

        //   var rows = form.closest('#vendorPayBillModal').find('.pay_bill_row')

        //   rows.each(function(){
        //       $(this).find('#Paybill_payment_accounts').val(text)
        //       $(this).find('#Paybill_payment_accounts_row').find('input[type=hidden]').val(value) 
            
        //       if($(this).find('#pay_bill_icon_check').css("visibility") == "visible"){
        //         setAccountName($(this).closest('.pay_bill_row')) 
        //       }
        //   })

        // }
        pbSetAccount: function (text ,value, form ){

            var rows = $(form).closest('#vendorPayBillModal').find('.pay_bill_row')
        
            rows.each(function(){
                $(this).find('#Paybill_payment_accounts_row').find('input').val(text);
                $(this).find('#Paybill_payment_accounts_row').find('input[type=hidden]').val(value) 
                
                if($(this).find('#pay_bill_icon_check').css("visibility") == "visible"){
                    payBill.setAccountName1($(this).closest('.pay_bill_row')) 
                }
            })
        
        },

        resetName: function (row){

            if($(row).find('#pay_bill_icon_check').css("visibility") == "visible"){
                payBill.setAccountName1($(row)) 
            }
        },

        setAccountName1: function (row){

            var vendorVal = $(row).find('.paybill_vendor').val()
            var accountVal = $(row).find('#Paybill_payment_accounts_row').find('input[type=hidden]').val()
            var propertyVal = $(row).find('.paybill_property').val() 
            var transactionId = $(row).find('#transactionId').val()
            

            console.log(vendorVal+accountVal+propertyVal)

            $(row).find('#transactionId').attr('name', `transactions1[${vendorVal+accountVal+propertyVal}][${transactionId}][transaction_id_b]"`)
            $(row).find('.paybill_vendor').attr('name', `transactions1[${vendorVal+accountVal+propertyVal}][${transactionId}][profile_id]"`)
            $(row).find('#Paybill_payment_accounts_row').find('input[type=hidden]').attr('name', `transactions1[${vendorVal+accountVal+propertyVal}][${transactionId}][account_id]"`)
            $(row).find('.paybill_property').attr('name', `transactions1[${vendorVal+accountVal+propertyVal}][${transactionId}][property_id]"`)
            $(row).find('#pay_bill_input_amount').attr('name', `transactions1[${vendorVal+accountVal+propertyVal}][${transactionId}][amount]"`)

        },

        removeName: function (row){

            $(row).find('#transactionId').removeAttr('name')
            $(row).find('.paybill_vendor').removeAttr('name')
            $(row).find('#Paybill_payment_accounts_row').find('input[type=hidden]').removeAttr('name')
            $(row).find('.paybill_property').removeAttr('name')
            $(row).find('#pay_bill_input_amount').removeAttr('name')

        },

        pbcalcTotalAmountToPay:function (form){

        var rows = $(form).closest('#vendorPayBillModal').find('.pay_bill_row')

        var pay_bill_total = 0;

        rows.each(function(index , element ){

                var rowVal = $(element).find('.pay_bill_input')

                if(rowVal.attr('value') == 1  ){
                
                    let amount = currency($(this).find('#pay_bill_input_amount').val())
            
                    
                    pay_bill_total =  currency(pay_bill_total).add(amount).format();
                    
                }
            
        })
        // if(pay_bill_total > 0){
            $(form).closest('#vendorPayBillModal').find('#total_paybill_amount_to_pay').text(pay_bill_total)
        //    } else {
        //     form.closest('#vendorPayBillModal').find('#total_paybill_amount_to_pay').text('0.00')
        //    }
        
        },

        setSearchDate: function ( row , term ){
            $(row).closest('#searchByDate').attr('searchTerm' ,  term)
        },

        setSearchVendor: function ( row , term ){
            $(row).closest('#searchByVend').attr('searchTerm' ,  term)
        },

        setSearchProperty: function ( row , term ){
            $(row).closest('#searchByProp').attr('searchTerm' ,  term)
        },

        getApiData:function ( row ){

            var date = null 
            var vendor = null
            var property = null
            var body = $(row).closest('#vendorPayBillModal')

            //check to see what date to search
            var dsearchTerm = $(row).find('#searchByDate').attr('searchTerm') 
            if(dsearchTerm !== 'all'){
                date = $(row).find('#pay_bill_due-date').val()
            }

            //check to see what vendor to search
            var vsearchTerm = $(row).find('#searchByVend').attr('searchTerm') 
            if(vsearchTerm !== 'all'){
                vendor = $(row).find('#vendorSelect').find('input[type=hidden]').val()
            }
            //check to see what property to search
            var psearchTerm = $(row).find('#searchByProp').attr('searchTerm') 
            if(psearchTerm !== 'all'){
                property = $(row).find('#propertySelect').find('input[type=hidden]').val()
            }



            JS.loadBills('api/getBillTransactions', date, vendor, property , '#payBillBody', body );


        },

        confirmChangeAndFilterApi: function ( row , trigger, trigger2 ){

            if(trigger2){
                
                if($(trigger2).closest('ul').attr('searchTerm') === 'selected'){
                    if (confirm('Filtering bills will delete all selected bills, Do you want to proceed?')) {
                        
                        payBill.getApiData( row )
                
                    }
                }

            } else {

                if (confirm('Filtering bills will delete all selected bills, Do you want to proceed?')) {
                    console.log('search here');
                    console.log($(trigger).closest('ul').find('input.editable-select').val());
                    $(trigger).closest('ul').find('input.editable-select').val('');
                    payBill.getApiData( row )
            
                }
                

            };  
            
        },

        pbcalcTotal: function (form){

            var rows = $(form).closest('#vendorPayBillModal').find('.pay_bill_row')

            var pay_bill_total_row_amount = 0;
            var pay_bill_total_open_bal = 0;
        
            rows.each(function(index , element ){
        
                
                
                    let pay_bill_row_amount = currency($(this).find('#bill_amount').text())          
                    pay_bill_total_row_amount =  currency(pay_bill_total_row_amount).add(pay_bill_row_amount).format();

                    let pay_bill_row_open_amount = currency($(this).find('#open_balance').text())          
                    pay_bill_total_open_bal =  currency(pay_bill_total_open_bal).add(pay_bill_row_open_amount).format();

            })

            $(form).closest('#vendorPayBillModal').find('#total_paybill_bill_amount').text(pay_bill_total_row_amount)
            $(form).closest('#vendorPayBillModal').find('#total_paybill_open_balance').text(pay_bill_total_open_bal)

        },
         selectAll: function(checkBox){
            var checkBox = checkBox;
            if($(checkBox).closest('label').hasClass('active')){
                var rows =  $(checkBox).closest('div').find('.allAccounts');
                rows.each(function(){           
                    if(!$(this).closest('label').hasClass('active')){
                      $(this).click();
                  } 
                });
            } else {
              console.log('not active')
                var rows =  $(checkBox).closest('div').find('.allAccounts')
                rows.each(function(){ 
                  if($(this).closest('label').hasClass('active')){
                      $(this).click();
                  }          
                });
            }
        },

        setTransactionsProperties: function(transactions){
            $(transactions).each(function(){
                payBill.allShown[payBill.spot] = $(this)[0];
                payBill.allPropertiesShown.push($(this)[0].property_id);
                payBill.allVendorsShown.push($(this)[0].profile_id);
                payBill.spot++;
            })
            //payBill.allShown = transactions;
            console.log(payBill.allPropertiesShown);
            console.log(payBill.allVendorsShown);
        },
        // filterDates: function(){
        //                    //from and to date eventlistner
        //                    function filters(timesheet, body){
        //                     var fromDate = $(body).find('#fromDate');
        //                      var toDate = $(body).find('#toDate');
        //                      var allDates = $(body).find('#alldates');
        //                      var timesheetBody = $(body).find('#employeeTimesheet');
        //                      $(fromDate).on('input',  function(){
        //                                              console.log('fromDate');
        //                          if($(allDates).closest('label').hasClass('active')){
        //                              $(allDates).closest('label').trigger('click');
        //                          }
        //                          filterTimesheet(timesheet, timesheetBody, fromDate, toDate);
        //                      });
        //                      $(toDate).on('input',  function(){
        //                                              console.log('toDate');
        //                          if($(allDates).closest('label').hasClass('active')){
        //                            $(allDates).closest('label').trigger('click');
        //                          }
        //                          filterTimesheet(timesheet, timesheetBody, fromDate, toDate);
        //                     });
         
        //                      $(allDates).closest('label').on('click',  function(){
        //                          console.log('allDates');
        //                          var checked = $(allDates).prop("checked");
        //                          console.log(checked);
        //                          if(checked){
        //                              $(toDate).val('');
        //                              $(fromDate).val('');
        //                          }
        //                          filterTimesheet(timesheet, timesheetBody, fromDate, toDate);
        //                     });
        //                  }

        // },
        filterVendor: function(id, checkbox, modal){
            var filtered_date = $(modal).find('#pay_bill_due-date').val();
            //console.log(filtered_date);

            var new_filtered_date = (filtered_date != "" ? new Date(filtered_date) : '');
            if($(checkbox).closest('label').hasClass('active')){
                $(payBill.allHidden).each(function(key, value){
                    var that = this;
                    var date = $(modal).find('#pay_bill_due-date').val();
                    //console.log(date);
                    if(that.profile_id == id && payBill.allPropertiesShown.includes(that.property_id) && (new_filtered_date != '' ? new Date(that.due_date) < new_filtered_date : true)){
                        var tr_id = that.id;
                     payBill.allShown[key] = $(payBill.allHidden[key])[0];
                     delete payBill.allHidden[key];
                     
                    payBill.displayTransaction(tr_id, modal);
                    }
                })
                payBill.allVendorsShown.push(id);
                //console.log(payBill.allVendorsShown);
            }else{
                $(payBill.allShown).each(function(key, value){
                    var that = this;
                    if(that.profile_id == id){
                        var tr_id = that.id;
                     payBill.allHidden[key] = $(payBill.allShown[key])[0];
                     delete payBill.allShown[key];
                    payBill.hideTransaction(tr_id, modal);
                    }
                })
                payBill.allVendorsShown = payBill.allVendorsShown.filter(e => e !== id);
                //console.log(payBill.allVendorsShown);
            }
        },

        filterProperty: function(id, checkbox, modal){

                    var filtered_date = $(modal).find('#pay_bill_due-date').val();
                    console.log(filtered_date);

                    var new_filtered_date = (filtered_date != "" ? new Date(filtered_date) : '');
                    if($(checkbox).closest('label').hasClass('active')){
                        $(payBill.allHidden).each(function(key, value){
                            var that = this;
                            if(that.property_id == id && payBill.allVendorsShown.includes(that.profile_id) && (new_filtered_date != '' ? new Date(that.due_date)  < new_filtered_date : true)){
                                var tr_id = that.id;
                             payBill.allShown[key] = $(payBill.allHidden[key])[0];
                             delete payBill.allHidden[key];
                             
                            payBill.displayTransaction(tr_id, modal);
                            }
                        })
                        payBill.allPropertiesShown.push(id);
                        console.log(payBill.allPropertiesShown);
                    }else{
                        $(payBill.allShown).each(function(key, value){
                            var that = this;
                            if(that.property_id == id){
                                var tr_id = that.id;
                             payBill.allHidden[key] = $(payBill.allShown[key])[0];
                             delete payBill.allShown[key];
                            payBill.hideTransaction(tr_id, modal);
                            
                            }
                        })
                        payBill.allPropertiesShown = payBill.allPropertiesShown.filter(e => e !== id);
                        console.log(payBill.allPropertiesShown);
                    }
                    
                //}
            
            //console.log($(payBill.allShown).length);
            //console.log($(this.allShown[0]).length);
            //for(var i =0; i < $(payBill.allShown).length; i++){
               // console.log('got in if')
                //console.log($(payBill.allShown[i])[0].property_id);
                // if($(payBill.allShown[i])[0].property_id == id){
                //     payBill.allHidden[i] = $(payBill.allShown[i])[0];
                //     delete payBill.allShown[i];
                //     if($(checkbox).closest('label').hasClass('active')){
                //         payBill.displayTransaction()
                //     }else{
                //         payBill.hideTransaction()
                //     }
                //     //console.log($(payBill.allShown[i])[0]);
                //     console.log('match')
                // }
            //}
                // $(payBill.allShown).each(function(key, value){
                //     var that = this;
                //     if(that.property_id == id){
                //         var tr_id = that.id;
                //      payBill.allHidden[key] = $(payBill.allShown[key])[0];
                //      delete payBill.allShown[key];
                //         if($(checkbox).closest('label').hasClass('active')){
                //             payBill.displayTransaction(tr_id, modal)
                //         }else{
                //             payBill.hideTransaction(tr_id, modal)
                //         }
                //     }
                // })

            
            // console.log(payBill.allShown);
            // console.log(payBill.allHidden);

        },
        displayTransaction: function(tr_id, modal){
            var tr = $(modal).find('#payBillBody tr[tid='+tr_id+']');
            console.log('displayTransaction');
            //console.log(tr);
            tr.show();
        },
        hideTransaction: function(tr_id, modal){
            var tr = $(modal).find('#payBillBody tr[tid='+tr_id+']');
            // if($(tr).find('#pay_bill_icon_check:visible')){
            //     tr.find('#pay_bill_check').click();
            // }
            console.log('hideTransaction');
            tr.hide();
            //console.log(tr);
        }

    }
    var payBill = new payBill();
    $(document).ready(function () {
        // payBill.pbcheck();
        // payBill.pbuncheck();
        // payBill.pbSetAccount();
        // payBill.resetName();
        // payBill.setAccountName1();
        // payBill.removeName();
        // payBill.pbcalcTotalAmountToPay();
        // payBill.setSearchDate();
        // payBill.setSearchVendor();
        // payBill.setSearchProperty();
        // payBill.getApiData();
        //payBill.confirmChangeAndFilterApi();
        //payBill.pbcalcTotal();

                  // $('x#pay_bill_select_all').click(function(){	
            //     var rows = $(this).closest('#vendorPayBillModal').find('.pay_bill_row')
            //     console.log('ghyyyyyyyyyyy')

            //     if($(this).parent('th').hasClass('active')){
            //         rows.each(function(){
            //             pbuncheck($(this))
            //         })
            //     }else{
            //         pbcheck($this)
            //     }
            // });

            $('body').on('click', '#pay_bill_check', function () {

                var row = $(this)
                var rowIcon = row.find('#pay_bill_icon_check');
                

            if( rowIcon.css("visibility") == "hidden"){
            
                payBill.pbcheck(row)
                
                
            }else{

                payBill.pbuncheck(row)

            }  

            payBill.pbcalcTotalAmountToPay(row)


    });

    $('body').on('change', '#pay_bill_select_all', function () {

    //  $(this).parent('th').toggleClass('active');  
        var rows = $(this).closest('#vendorPayBillModal').find('.pay_bill_row')

        if($(this).parent('label').hasClass('active')){
            rows.each(function(){
                var row = $(this)
                payBill.pbcheck(row)
            })

        }else{

            rows.each(function(){
                var row = $(this)
                payBill.pbuncheck(row)
            })
        }

        payBill.pbcalcTotalAmountToPay($(this))

    });

    $('body').on('keyup', '#pay_bill_input_amount', function () {

        let openAmount = currency($(this).closest('.pay_bill_row').find('#open_balance').text());
        let inputAmount = currency($(this).val())

        

        if( openAmount > 0 && inputAmount.intValue > openAmount.intValue ){
            JS.showAlert('danger', 'Payment amount can not exceed the open balance!')
            $(this).val('0.00')
        } else if(openAmount < 0 &&  inputAmount != 0   ){
            if(inputAmount.intValue > 0  || inputAmount.intValue < openAmount.intValue  ){
                JS.showAlert('danger', 'Credit can not be greater then open balance!')
                $(this).val('0.00')
            }
        
        }


        payBill.pbcalcTotalAmountToPay($(this))

    
    });

    });

