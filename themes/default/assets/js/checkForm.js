
let checkForm = (function(){

    function getDiff(form){
    //    let amount = currency($(form).find('#checkAmount').val())
    //    let amountTotal = checkTotal(form)

    //    let difference = currency(amount).subtract(amountTotal)

    //    console.log('Amount ' + amount)
    //    console.log('Total Amount' + amountTotal)
    //    return difference
       
    }

    // function ifEmpty(row){

    //             var empty = false;
    //             var val;

    //            let inputs = $(row).find(":input")
    //            inputs.each(
    //                     function(index, input){          
    //                         val = $(input).val();
    //                         if(val === "-1" || val == 0  ){
    //                             empty = true;                   
    //                         }   else {
    //                             empty = false;
    //                             return false;
    //                         }              
    //                     }
    //            )
    //            if(empty){
    //                return true;
    //            } else {
    //                return false;
    //            }
    // }

    function fillEmptyRow(row , valArr ){
    }

    function checkTotal(form){
  
        // totalAmount = 0;
         
        // var amountInputs = $(form).closest('#checkModal').find('.checkAmount')
        // amountInputs.each(
        //     function () {
        //         let value = currency($(this).val())

        //         if (value) {
                    
        //             totalAmount = currency(totalAmount).add(value).format();
                   
        //         }
        //     }
        // )
        
        // $(form).closest('#checkModal').find('#totalAmount').html(totalAmount);
        // return totalAmount;
    
    }

    function getRows(item){
       
           do{
             addRowToCheckForm(item ,null, numOfTotalTransactions)
             numOfTotalTransactions++
           }
           while(numOfTotalTransactions < 8)
          $(item).find('select.editable-select').editableSelect();
          $(item).find('input.decimal').calculadora({decimals: 2, useCommaAsDecimalMark: false});
    }

    function nameApi(val , body){
        if(val === '451' || val === '454'){
            JS.loadList('api/getNames', val , '#profile_id',  body ) ;
        }
    }


   return {

    checkTotal : checkTotal,
    getRows : getRows,
    getDiff : getDiff,
    //ifEmpty : ifEmpty,
    fillEmptyRow : fillEmptyRow,
    nameApi : nameApi

   }


})()