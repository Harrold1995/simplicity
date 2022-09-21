var receivedPayments = function () {
}
receivedPayments.prototype = {

            check: function (row , bool){

                var rowIcon = $(row).find('#received_payament_icon_check');
                var rowInput = $(row).find('.received_payament_input')
                var rowAmount = $(row).closest('#received_payament_row').find('#received_payament_input_amount')

                // updates visibility and value
                rowIcon.css('visibility', 'visible');;
                rowInput.attr('value', '1');
                rowAmount.attr("disabled", false)

                if(bool){
                  let amount = currency(row.closest('#received_payament_row').find('.open_balance').text());
                  row.closest('#received_payament_row').find('#received_payament_input_amount').val(amount.format())
                }

            },

            uncheck:function (row){

              var rowIcon = $(row).find('#received_payament_icon_check');
              var rowInput = $(row).find('.received_payament_input')
              var rowAmount = $(row).closest('#received_payament_row').find('#received_payament_input_amount')

              // updates visibility and value
              rowIcon.css( 'visibility', 'hidden');
              rowInput.attr('value', '0');
              rowAmount.attr("disabled", true)

              row.closest('#received_payament_row').find('#received_payament_input_amount').val('0.00')

            },


            updateUnapliedAmount: function (form){


              var charges = $(form).closest('#receive_paymentModel').find('.received_payament_row')



              var received_amount =  currency(form.closest('#receive_paymentModel').find('#received_amount').val())
              


              charges.each(function(){

                if($(this).find('.received_payament_input').attr('value') == 1  ){
                  
                  let amount = currency($(this).find('#received_payament_input_amount').val())

                  
                  received_amount =  currency(received_amount).subtract(amount);
                  
                }
                

              })

              form.closest('#receive_paymentModel').find('#unapplied_amount').text(received_amount.format())

              return received_amount;

            },

            updateAppliedAmount: function ( form ){

              var charges = $(form).closest('#receive_paymentModel').find('.received_payament_row')

              var total_applied_amount = 0;

              charges.each(function(){

                row_applied_amount = currency($(this).find('#received_payament_input_amount').val()) 
                total_applied_amount = currency(total_applied_amount).add(row_applied_amount).format()

              })

              $(form).closest('#receive_paymentModel').find('#received_payment_applied_total').text(total_applied_amount)

            },

            firstUpdateUnapliedAmount: function (form){


              receivedPayments.updateUnapliedAmount(form)

              var charges = $(form).closest('#receive_paymentModel').find('.received_payament_row')

              var payment_amount = 0;

              var open_balance = 0;

              var total_applied_amount = 0;

              charges.each(function(){

                row_payment_amount = currency($(this).find('.payment_amount').text()) 
                payment_amount = currency(payment_amount).add(row_payment_amount).format()

                row_open_bal = currency($(this).find('.open_balance').text()) 
                open_balance = currency(open_balance).add(row_open_bal).format()

                row_applied_amount = currency($(this).find('#received_payament_input_amount').val()) 
                total_applied_amount = currency(total_applied_amount).add(row_applied_amount).format()

              })

              $(form).closest('#receive_paymentModel').find('#received_payment_amount_total').text(payment_amount)
              $(form).closest('#receive_paymentModel').find('#received_payment_open_total').text(open_balance)
              $(form).closest('#receive_paymentModel').find('#received_payment_applied_total').text(total_applied_amount)
              

            },
            //trigger green check for payment amount > 0
            triggerCheck: function (clicked){

                  if( $(clicked).closest('tr').find('.greenCheckTd').find('i').css('display') == 'none' ){
                    if(clicked.val() != 0){
                      $(clicked).closest('tr').find('.greenCheckTd').trigger('click');
                    }
                  }
              }
}
            $(document).ready(function () {

                $('body').on('click', '#received_payament_check', function () {

                    var row = $(this)
                    var rowIcon = row.find('#received_payament_icon_check');
                    

                  if( rowIcon.css("visibility") == "hidden"){
                    var unapplied_amount = currency(receivedPayments.updateUnapliedAmount($(this)));
                    let rowAmount = currency($(this).closest('#received_payament_row').find('.open_balance').text());
                  // var rowAmount = currency($(this).closest('#received_payament_row').find('#received_payament_input_amount').val()).intValue;

                    if(rowAmount.intValue <= unapplied_amount.intValue ){

                      receivedPayments.check(row, true)

                    }else if (unapplied_amount.intValue > 0 ){

                      receivedPayments.check(row , false)
                      $(this).closest('#received_payament_row').find('#received_payament_input_amount').val(unapplied_amount.format())


                    } else {

                      JS.showAlert('danger', 'Applied amount can not be greater then payment amount')

                    }
                    
                    
                  }else{

                    var unapplied_amount = currency(receivedPayments.updateUnapliedAmount($(this)));
                    let rowAmount = currency($(this).closest('#received_payament_row').find('.open_balance').text());


                    if( rowAmount.intValue < 0 && unapplied_amount <= 0  ){

                  
                        JS.showAlert('danger', 'Applied amount can not be less then 0')
                    

                    }else {

                      receivedPayments.uncheck(row)

                    }



                  }  


                  receivedPayments.updateUnapliedAmount(row)
                  receivedPayments.updateAppliedAmount( row)
                  
                
                });

              //   $('body').on('focusout', '#received_amount', function () {

              //       let received_amount =  currency($(this).val())

              //       var charges = $(this).closest('#receive_paymentModel').find('.received_payament_row')

              //       var appliedAmounts = false ;

              //       charges.each(function(){
                  
              //         if($(this).find('.received_payament_input').attr('value') == 1  ){
                        
              //           appliedAmounts = true ;
                        
              //         }
                  
              //       })
                  
              //       if(appliedAmounts){
                  
              //         charges.each( function(){

              //           let amount = currency($(this).find('.open_balance').text());
                        
            
              //           if(received_amount.intValue > amount.intValue && received_amount.intValue > 0){
                          
              //             // $(this).find('#received_payament_input_amount').val(amount.format())
            
              //               receivedPayments.check($(this), true)
              //               received_amount =  currency(received_amount).subtract(amount); 
            
              //           } else if (received_amount > 0 ) {
            
            
              //             receivedPayments.check($(this) , false)
              //             $(this).find('#received_payament_input_amount').val(received_amount.format())
              //             received_amount = 0
                          
              //           } else {
            
              //           receivedPayments.uncheck($(this))
            
              //           }
                        
            
              //       })

              //       receivedPayments.updateUnapliedAmount($(this))

                  
              //       }else{

              //         var foundExact = false;
                  
              //         charges.each(function(){

              //           var thisRowAmount = currency($(this).find('.open_balance').text())

                        
                  
              //           if(thisRowAmount.intValue == received_amount.intValue ){
                          
              //             receivedPayments.check($(this) , true )
              //             received_amount =  currency(received_amount).subtract(thisRowAmount);
              //             return false; 
              //             foundExact = true;
              //           }

                        
                    
              //         })

              //         if(!foundExact){

              //           charges.each( function(){

              //             let amount = currency($(this).find('.open_balance').text());
                          
              
              //             if(received_amount.intValue > amount.intValue && received_amount.intValue > 0){
                            
              //               // $(this).find('#received_payament_input_amount').val(amount.format())
              
              //                 receivedPayments.check($(this), true)
              //                 received_amount =  currency(received_amount).subtract(amount); 
              
              //             } else if (received_amount > 0 ) {
              
              
              //               receivedPayments.check($(this) , false)
              //               $(this).find('#received_payament_input_amount').val(received_amount.format())
              //               received_amount = 0
                            
              //             } else {
              
              //             receivedPayments.uncheck($(this))
              
              //             }
                          
              
              //         })

              //         }
                  
              //         receivedPayments.updateUnapliedAmount($(this))
                      
              //       }

                  

                  

                  

              // });

              $('body').on('keyup', '#received_payament_input_amount', function () {

                  let inputAmount = currency($(this).val())
                  let unapplied_amount = currency(receivedPayments.updateUnapliedAmount($(this)));

                  if(unapplied_amount.intValue >= 0){
                    receivedPayments.updateUnapliedAmount($(this))
                    receivedPayments.updateAppliedAmount($(this))
                  } else {
                    JS.showAlert('danger', 'Applied amount can not be greater then payment amount')
                    $(this).val('0.00')
                  }

                });
              //as of 5/16/2019 not used anymore instead using next function
              $('body').on('click', '#received_payment_select_all', function () {

                var selectAll = $(this).closest('label');
                var rows = $(this).closest('#receive_paymentModel').find('.received_payament_row')

                if($(selectAll).hasClass('active')){
                  rows.each(function(){   
                    receivedPayments.check($(this))
                  })
                }else{
                  rows.each(function(){   
                    receivedPayments.uncheck($(this))
                  })
                }

                receivedPayments.updateUnapliedAmount($(this))

              });
              //as of 5/16/2019 using anymore this function instead of previos one
              $('body').on('change', '#received_payment_select_all2', function () {

                var selectAll = $(this).closest('label');
                var rows = $(this).closest('.modal').find('td i#greenCheck');
                var checked = $(this).prop("checked");
                console.log(checked);
                rows.each(function(){
                    if(checked){
                      if($(this).css('display') == 'none'){
                          $(this).closest('td').trigger('click');
                      }
                    }else{
                      if($(this).css('display') != 'none'){
                        $(this).closest('td').trigger('click');
                      }
                    }
                });

              });
              
                // //when user puts in amount it populates 
                // $('body').on('focusout', '#received_amount', function () {
                //     var that = $(this);
                //     var received_amount = $(this).val();
                //     var charges = $(this).closest('.modal').find('.received_payment_row');
                //     charges.each(function(){
                //           var greenCheck = $(this).find('#greenCheck');
                //           var openBalance = Number($(this).find('.openBalance').html());
                //           var amount = $(this).find('#received_payament_input_amount2').val();
                //           if(received_amount <= 0 ){return;}
                //           if(greenCheck.css('display') === 'none'){
                //             if(received_amount <= openBalance){
                //                 $(this).find('.greenCheckTd').trigger('click');
                //                 received_amount = received_amount - openBalance;
                //                 return;
                //             }else{
                //                 $(this).find('.greenCheckTd').trigger('click');
                //                 received_amount = received_amount - openBalance;
                //             }
                //           }else{
                //             if(received_amount == amount){return;}
                //             else{
                //               var balance = openBalance - amount;
                //               if(received_amount <= openBalance){
                //                 $(this).find('#received_payament_input_amount2').val(received_amount);
                //                 $(this).find('#received_payament_input_amount2').trigger('keyup');
                //                 received_amount = received_amount - openBalance;
                //                 return;
                //               }else{
                //                   $(this).find('#received_payament_input_amount2').val(openBalance);
                //                   $(this).find('#received_payament_input_amount2').trigger('keyup');
                //                   received_amount = received_amount - openBalance;
                //               }
                //             }
                //           }
                //     })
                // });

                              //   //when user puts in amount it populates 
                              //   $('body').on('focusout', '#received_amount', function () {
                              //     var that = $(this);
                              //     var received_amount = $(this).val();
                              //     var charges = $(this).closest('.modal').find('.received_payment_row');
                              //     charges.each(function(){
                              //           var greenCheck = $(this).find('#greenCheck');
                              //           if(greenCheck.css('display') != 'none'){$(this).find('.greenCheckTd').trigger('click');}
                              //           var openBalance = Number($(this).find('.openBalance').html());
                              //           var amount = $(this).find('#received_payament_input_amount2').val();
                              //           if(received_amount <= 0 ){return;}
                              //             if(received_amount <= openBalance){
                              //                 $(this).find('.greenCheckTd').trigger('click');
                              //                 received_amount = received_amount - openBalance;
                              //                 return;
                              //             }else{
                              //                 $(this).find('.greenCheckTd').trigger('click');
                              //                 received_amount = received_amount - openBalance;
                              //             }
                              //     })
                              // });

                    $('#received_amount').on('keyup', function(){ formsJs.updateUnapplied($(this).closest('.modal'), 'receivePayment')});
                //$('body').on('keyup', '.allInputAmounts', function(){ formsJs.updateUnapplied($(this).closest('.modal'), 'receivePayment')});  
                $('body').on('keyup', '#received_payament_input_amount2', function(){ formsJs.updateUnapplied($(this).closest('.modal'), 'receivePayment')});        
          //IMPORTANT-- The green check on the receive payment form gets triggered from applyRefund.js line 107 receivedPayments.triggerCheck(that);
            });


    var receivedPayments = new receivedPayments();
    $(document).ready(function () {
    });