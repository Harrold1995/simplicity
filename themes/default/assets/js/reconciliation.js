var recclicked = false;
var recclickdelay = 300;
function toggleCheck(row, paymentType){

	var rowIcon = row.find('#rec-icon-check');
	var rowInput = row.find('.rec_input')

	if( rowIcon.css("visibility") == "hidden"){
		
		rowIcon.css('visibility', 'visible');;
		rowInput.attr('value', '1');
	

	}else{
	
		rowIcon.css( 'visibility', 'hidden');
		rowInput.attr('value', '0');

  } 
  

}

function calcDiff( biginning, payments, deposits, interest , serviceCharge , endingBal, rectype ){
   
   var bal = biginning - payments + deposits + interest - serviceCharge;
   if(rectype == 6){
    bal = biginning + payments - deposits + interest - serviceCharge;
   }
   return  Math.round((endingBal - bal)* 100) / 100;

   // + interest - service charge 
   // Difference is the total above - the ending bal 

}

function updateAllTotals(form){

  
          var biginBal = getBiginBal(form)
          var payment_total = getPaymentTotal(form)
          var debit_total = getDebitTotal(form)
          var interest = getInterest(form)
          var serviceCharge = getServiceCharge(form)
          var endingBal = getEndingBal(form)
          var rectype = getRectype(form)




        //update differance
        var diff = calcDiff(biginBal, payment_total , debit_total, interest , serviceCharge, endingBal, rectype)
        form.closest('#reconciliationModal').find('#rec_diff').text(diff.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))
        if(diff == 0){
          form.closest('#reconciliationModal').find('#closeRec').val('true')
          form.closest('#reconciliationModal').find('#rec_submit').text('Submit Reconciliation')

        }else {
          form.closest('#reconciliationModal').find('#closeRec').val('false')
          form.closest('#reconciliationModal').find('#rec_submit').text('Save for later')
        }

}

function getBiginBal(row){
      var biginBal = row.closest('#reconciliationModal').find('#rec_begin_bal').val()
      return Number(biginBal.replace(/,/g, ''), 10)
}

function getDebitTotal(row){
  var debit_total = row.closest('#reconciliationModal').find('#debit_total').text()
  return Number(debit_total.replace(/,/g, ''), 10) 
}

function getPaymentTotal(row){
     var payment_total = row.closest('#reconciliationModal').find('#payment_total').text()
      return Number(payment_total.replace(/,/g, ''), 10) 
}

function getInterest(row){
   var interest = row.closest('#reconciliationModal').find('#interest_earned').val()
    return interest = Number(interest.replace(/,/g, ''), 10) 
}

function getServiceCharge(row){
  var serviceCharge = row.closest('#reconciliationModal').find('#service_charge').val()
  return serviceCharge = Number(serviceCharge.replace(/,/g, ''), 10) 
}

function getEndingBal(row){
  var ending_balance = row.closest('#reconciliationModal').find('#ending_balance').val()
  return ending_balance = Number(ending_balance.replace(/,/g, ''), 10) 
}

function getRectype(row){
  return row.closest('#reconciliationModal').attr('data-rec-type');
}


$(document).ready(function () {

    $('body').on('click', '#credit_row', function () {
        if(recclicked)
            return false;
        else {
            recclicked = true;
            setTimeout(function () {
                recclicked = false;
            }, recclickdelay);
        }

      var row = $(this)
      var rowIcon = row.find('#rec-icon-check');
      var rowInput = row.find('.rec_input')

      var rowAmount = row.find('.credit_amount').html()
      rowAmount = rowAmount.replace(/,/g, '');
      
      var cleared_payments = row.closest('#reconciliationModal').find('#cleared_payments').text()

      var payment_total = getPaymentTotal(row)
      var biginBal = getBiginBal(row)
      var debit_total = getDebitTotal(row)
      var interest = getInterest(row)
      var serviceCharge = getServiceCharge(row)
      var endingBal = getEndingBal(row)
      var rectype = getRectype(row)

      if( rowIcon.css("visibility") == "hidden"){
        
        // updates visibility and value
        rowIcon.css('visibility', 'visible');;
        rowInput.attr('value', '1');

        //updates clear total
        cleared_payments++
        row.closest('#reconciliationModal').find('#cleared_payments').text(cleared_payments)


        //updates total amount  
        payment_total += Number(rowAmount);
        row.closest('#reconciliationModal').find('#payment_total').text(payment_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))

      }else{
        // updates visibility and value
        rowIcon.css( 'visibility', 'hidden');
        rowInput.attr('value', '0');

        //updates clear total
        cleared_payments--
        row.closest('#reconciliationModal').find('#cleared_payments').text(cleared_payments)

        //updates total amount
        payment_total -= Number(rowAmount);
        row.closest('#reconciliationModal').find('#payment_total').text(payment_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))
       
      }  
      
      //update differance
      var diff = calcDiff(biginBal, payment_total , debit_total, interest , serviceCharge, endingBal, rectype)
      row.closest('#reconciliationModal').find('#rec_diff').text(diff.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))
      if(diff == 0){
        row.closest('#reconciliationModal').find('#closeRec').val('true')
        row.closest('#reconciliationModal').find('#rec_submit').text('Submit Reconciliation')

     }else {
        row.closest('#reconciliationModal').find('#closeRec').val('false')
        row.closest('#reconciliationModal').find('#rec_submit').text('Save for later')
     }


    });

    $('body').on('click', '#debit_row', function () {
      if(recclicked)
          return false;
      else {
          recclicked = true;
          setTimeout(function () {
              recclicked = false;
          }, recclickdelay);
      }
     
     
      var row = $(this)
      var rowIcon = row.find('#rec-icon-check');
      var rowInput = row.find('.rec_input')

      var rowAmount = row.find('.debit_amount').html()
      rowAmount = rowAmount.replace(/,/g, '');

      var cleared_deposits = row.closest('#reconciliationModal').find('#cleared_deposits').text()

      

      var payment_total = getPaymentTotal(row) 
      var biginBal = getBiginBal(row)
      var debit_total = getDebitTotal(row)
      var interest = getInterest(row)
      var serviceCharge = getServiceCharge(row)
      var endingBal = getEndingBal(row)

      if( rowIcon.css("visibility") == "hidden"){

        
        
        
        // updates visibility and value
        rowIcon.css('visibility', 'visible');;
        rowInput.attr('value', '1');

        //updates clear total
        cleared_deposits++
        row.closest('#reconciliationModal').find('#cleared_deposits').text(cleared_deposits)


        //updates total amount  
        debit_total += Number(rowAmount);
        row.closest('#reconciliationModal').find('#debit_total').text(debit_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))
      

      }else{


        // updates visibility and value
        rowIcon.css( 'visibility', 'hidden');
        rowInput.attr('value', '0');

        //updates clear total
        cleared_deposits--
        row.closest('#reconciliationModal').find('#cleared_deposits').text(cleared_deposits)


        //updates total amount
        debit_total -= Number(rowAmount);
        row.closest('#reconciliationModal').find('#debit_total').text(debit_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))
      
       
      } 

      //update differance
      var diff = calcDiff(biginBal, payment_total , debit_total, interest , serviceCharge, endingBal, rectype)
      row.closest('#reconciliationModal').find('#rec_diff').text(diff.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))
      if(diff == 0){      
        row.closest('#reconciliationModal').find('#closeRec').val('true')
        row.closest('#reconciliationModal').find('#rec_submit').text('Submit Reconciliation')

     }else {
        row.closest('#reconciliationModal').find('#closeRec').val('false')
        row.closest('#reconciliationModal').find('#rec_submit').text('Save for later')
     }

    });

    $('body').on('keyup', '#rec_begin_bal', function () {
 
      updateAllTotals($(this))

    });

    $('body').on('keyup', '#ending_balance', function () {
 
      updateAllTotals($(this))

    });

    $('body').on('keyup', '#interest_earned', function () {
      console.log('keyup!');
 
      updateAllTotals($(this))

    });

    $('body').on('keyup', '#service_charge', function () {
 
      updateAllTotals($(this))

    });

    
    $('body').on('click', '#rec_debit_select_all', function () {
        

        var rows = $(this).closest('#reconciliationModal').find('.debit_rec_row')
        var row = $(this)
        var rowIcon = $(this).find('#rec-icon-check');
        var rowInput = $(this).find('.rec_input')
        var cleared_deposits = 0
        
        
        //  var payment_total = getPaymentTotal(row)
        //  var biginBal = getBiginBal(row)
          var debit_total = 0
        //  var interest = getInterest(row)
        //  var serviceCharge = getServiceCharge(row)
        //  var endingBal = getEndingBal(row)


         


          if($(this).val() == 'unselect'){

            
            $(this).val('select')
            $(this).text('Select All')
          
            rows.each( function () {

              var row = $(this)
              var rowIcon = $(this).find('#rec-icon-check');
              var rowInput = $(this).find('.rec_input')

                var rowAmount = $(this).find('.debit_amount').html()
                rowAmount = parseInt(rowAmount.replace(/,/g, ''), 10)


                // updates visibility and value
                rowIcon.css( 'visibility', 'hidden');
                rowInput.attr('value', '0');

                //updates clear total
                //cleared_deposits--
                $(this).closest('#reconciliationModal').find('#cleared_deposits').text(cleared_deposits)


                //updates total amount
                //debit_total -= Number(rowAmount);
                $(this).closest('#reconciliationModal').find('#debit_total').text(debit_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))
              
           })
           

          } else {

            $(this).val('unselect')
            $(this).text('Unselect All')
          
            rows.each( function () {

              var row = $(this)
              var rowIcon = $(this).find('#rec-icon-check');
              var rowInput = $(this).find('.rec_input')

            

                var rowAmount = $(this).find('.debit_amount').html()
                rowAmount = parseInt(rowAmount.replace(/,/g, ''), 10) 

                   // updates visibility and value
                  rowIcon.css('visibility', 'visible');;
                  rowInput.attr('value', '1');

                  //updates clear total
                  cleared_deposits++
                  $(this).closest('#reconciliationModal').find('#cleared_deposits').text(cleared_deposits)


                  //updates total amount  
                  debit_total += Number(rowAmount);
                  $(this).closest('#reconciliationModal').find('#debit_total').text(debit_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))

              
           })

            


          }

          updateAllTotals($(this))
              

    });

    $('body').on('click', '#rec_credit_select_all', function () {
        

      var rows = $(this).closest('#reconciliationModal').find('.credit_rec_row')
      var row = $(this)
      var rowIcon = $(this).find('#rec-icon-check');
      var rowInput = $(this).find('.rec_input')
      var cleared_payments = 0
      
      
        var payment_total = 0
      //  var biginBal = getBiginBal(row)
       // var debit_total = 0
      //  var interest = getInterest(row)
      //  var serviceCharge = getServiceCharge(row)
      //  var endingBal = getEndingBal(row)


       


        if($(this).val() == 'unselect'){

          
          $(this).val('select')
          $(this).text('Select All')
        
          rows.each( function () {

            var row = $(this)
            var rowIcon = $(this).find('#rec-icon-check');
            var rowInput = $(this).find('.rec_input')

              var rowAmount = $(this).find('.credit_amount').html()
              rowAmount = parseInt(rowAmount.replace(/,/g, ''), 10)


              // updates visibility and value
              rowIcon.css( 'visibility', 'hidden');
              rowInput.attr('value', '0');

              //updates clear total
              //cleared_payments--
              $(this).closest('#reconciliationModal').find('#cleared_payments').text(cleared_payments)


              //updates total amount
              //payment_total -= Number(rowAmount);
              $(this).closest('#reconciliationModal').find('#payment_total').text(payment_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))
            
         })
         

        } else {

          $(this).val('unselect')
          $(this).text('Unselect All')
        
          rows.each( function () {

            var row = $(this)
            var rowIcon = $(this).find('#rec-icon-check');
            var rowInput = $(this).find('.rec_input')

          

              var rowAmount = $(this).find('.credit_amount').html()
              rowAmount = parseInt(rowAmount.replace(/,/g, ''), 10) 

                 // updates visibility and value
                rowIcon.css('visibility', 'visible');;
                rowInput.attr('value', '1');

                //updates clear total
                cleared_payments++
                $(this).closest('#reconciliationModal').find('#cleared_payments').text(cleared_payments)


                //updates total amount  
                payment_total += Number(rowAmount);
                $(this).closest('#reconciliationModal').find('#payment_total').text(payment_total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'))

            
         })

          


        }

        updateAllTotals($(this))
            

  });




  

});