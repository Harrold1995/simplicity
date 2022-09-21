function ebcheck(row){

    var rowIcon = row.find('#edit_bill_icon_check');
    var rowInput = row.find('.editBill_input')
    var rowAmount = row.closest('.edit_bill_row').find('#edit_bill_input_amount')

    // updates visibility and value
    rowIcon.css('visibility', 'visible');;
    rowInput.attr('value', '1');
    rowAmount.attr("disabled", false)


}


function ebuncheck(row){

  var rowIcon = row.find('#edit_bill_icon_check');
  var rowInput = row.find('.editBill_input')
  var rowAmount = row.closest('.edit_bill_row').find('#edit_bill_input_amount')

   // updates visibility and value
   rowIcon.css( 'visibility', 'hidden');
   rowInput.attr('value', '0');
   rowAmount.attr("disabled", true)


   // update row and total
   row.closest('.edit_bill_row').find('#edit_bill_input_amount').val('0.00')

}

function ebCalcAmount(form ){

   var amounts =  $(form).find('.edit_bill_row_input_amount')
   var amountTotal = 0;

   amounts.each(function(){
       rowAmount = currency($(this).find('#edit_bill_input_amount').val()) 
       amountTotal = currency(amountTotal).add(rowAmount).format()
   })

   $(form).find('#edit_bill_total_amount').text(amountTotal)
   return amountTotal;
}

function ebCalcOpen( form ){

    var amounts =  $(form).find('.edit_bill_open')
    var openAmountTotal = 0;
 
    amounts.each(function(){
        rowAmount = currency($(this).text()) 
        openAmountTotal = currency(openAmountTotal).add(rowAmount).format()
    })
 
    $(form).find('#edit_bill_total_open').text(openAmountTotal)
    

}

function confirmEditBillNameChange( payee ){

    
    let form = $(payee).closest('.modal')
    let rows = $(payee).closest('#editPaybill').find('.edit_bill_row')
    let checkedRows = false;

    // cheach if anything is applied
    rows.each(function( index, value){
        var num = $(value).find('.editBill_input').val()
        if(num == 1){
            checkedRows = true;
        }
    })
    if(checkedRows){
        if(!confirm('Changeing the Amount or Name on the form will effect previous submitted information.')){
           
            let resetId = $(payee).attr('resetId')
            let resetName = ""
            let rows = $(payee).closest('#editPaybill').find('li')
            rows.each(function(index, value){
               if($(value).val() == resetId){
                   resetName = $(value).text()
                   // console.log('reset val is ' + $(value).text())
               }
            })

            $(payee).val(resetName)
            $(payee).closest('#editBillVendorAddress').find('input[type=hidden]').val(resetId) 
                
        }  

    }
   

}


$(document).ready(function () {

    $('body').on('click', '#edit_bill_check', function () {
        console.log('!!!!!!!!!!!!')

        var row = $(this)
        var rowIcon = row.find('#edit_bill_icon_check');
        var form = $(row).closest('#editPaybill')


        

      if( rowIcon.css("visibility") == "hidden"){

         let editBillAmount = currency(form.find('#editBillAmount').val())
         let rowAmount = currency($(this).closest('.edit_bill_row').find('.edit_bill_open').text());
         let remainingAmount = currency(editBillAmount).subtract(ebCalcAmount($(form)));

         if(rowAmount.intValue <= remainingAmount.intValue ){

            ebcheck(row)
            row.closest('.edit_bill_row').find('#edit_bill_input_amount').val(rowAmount)
            ebCalcAmount($(form))

         }else if ( remainingAmount.intValue > 0 ) {

            ebcheck(row)
            row.closest('.edit_bill_row').find('#edit_bill_input_amount').val(remainingAmount)
            ebCalcAmount($(form))

         }else if ( $(form).find('#editBillAmount').val() == null || $(form).find('#editBillAmount').val() == 0) {
            $(form).find('#editBillAmount').val(rowAmount);
            ebcheck(row)
            row.closest('.edit_bill_row').find('#edit_bill_input_amount').val(rowAmount)
            ebCalcAmount($(form))

         }else {

            JS.showAlert('danger', 'Bill Payments can not exceed payment amount')

         } 
        
      }else{

       
          ebuncheck(row)
          ebCalcAmount(form)

      }  

     // ebCalcAmount(form)
     // ebCalcOpen( form )


    });


    $('body').on('keyup', '#edit_bill_input_amount', function () {
      

        var row = $(this)
        var form = $(row).closest('#editPaybill')

        let inputAmount = currency($(this).val())
        let editBillAmount = currency(form.find('#editBillAmount').val())
        let remainingAmount = currency(editBillAmount).subtract(ebCalcAmount($(form)));

        if( remainingAmount.intValue < 0){

            JS.showAlert('danger', 'Bill Payments can not exceed payment amount')
            row.closest('.edit_bill_row').find('#edit_bill_input_amount').val('0.00')

        }
        
        ebCalcAmount($(form))
        

    });

    $('body').on('keyup', '#editBillAmount', function () {
        let input = $(this)
        let form = $(this).closest('.modal')
        if (!form.hasClass('changed')) {
            if(!confirm('Changeing the Amount or Name on the form will effect previous submitted information.')){
                let reset = $(input).attr('resetVal')
                 $(input).val(reset)
            } 
        }    
    });
    
    $('body').on('change', 'x#editBillName', function () {

       
        let form = $(this).closest('.modal')
        let rows = $(this).closest('#editPaybill').find('.edit_bill_row')
        let checkedRows = false;

        // cheach if anything is applied
        rows.each(function( index, value){
            var num = $(value).find('.editBill_input').val()
            if(num == 1){
                checkedRows = true;
            }
        })
        if(checkedRows){
            if(!confirm('Changeing the Amount or Name on the form will effect previous submitted information.')){

                

                $(this).val(text)
                $(this).find('input[type=hidden]').val(value) 
                    
            }  

        }
       
     
    });
    

   

});