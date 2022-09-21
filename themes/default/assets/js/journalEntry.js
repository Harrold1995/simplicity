function calcTotal(form){
    // totalDebit = 0;
    // totalCredit = 0;

    // var debit = $(form).closest('#journalEntryModal').find('#journalEntryBody #JEdebit')
    // var credit = $(form).closest('#journalEntryModal').find('#journalEntryBody #JEcredit')
    // debit.each(
    //     function () {
    //         var value = Number($(this).val().replace(',', ''));
    //         if (value) {
    //             totalDebit += value;
    //         }
    //     }
    // )
    
    // credit.each(
    //     function () {
    //         var value = Number($(this).val().replace(',', ''));
    //         if (value) {
    //             totalCredit += value;
    //         }
    //     }
    // )
    // console.log($(form).closest('#journalEntryModal').find('#debitTotal').html())
    // totalDebit = totalDebit.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
    // totalCredit = totalCredit.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')
    // $(form).closest('#journalEntryModal').find('#debitTotal').text(totalDebit);
    // $(form).closest('#journalEntryModal').find('#creditTotal').text(totalCredit);
    // var total = [totalDebit, totalCredit]
    // return total;
}

function fillEmptyRow(row , feildArr){
}


$(document).ready(function () {
   
    $('body').on('keyup focusout', '#journalEntryBody #JEdebit', function () {

        // var form = $(this).closest('#journalEntryBody')
        // //emptie the credits  if debits get populated
        // var thisAmount = $(this).val();
        //     if(thisAmount > 0){
        //         var tr = $(this).closest('tr');
        //         tr.find('#JEcredit').val('.00');
        //     }
        //     //recalculates total
        // calcTotal(form);
       
     });

     $('body').on('keyup focusout', '#journalEntryBody #JEcredit', function () {

        // var form = $(this).closest('#journalEntryBody')
        //  //emptie the debits  if credits get populated
        // var thisAmount = $(this).val();
        //     if(thisAmount > 0){
        //         var tr = $(this).closest('tr')
        //         tr.find('#JEdebit').val('.00')
        //     }
        // calcTotal(form)
     });

        $('body').on('click', '#journalEntryBody #JEcredit', function () {
             //autofills the amount missing
            //   var tr = $(this).closest('tr');
            //   fillEmptyRow(tr);

            //   var input =  tr.find('#JEdebit').val();

            //   if(input < 1){
            //         var modal =  $(this).closest('#journalEntryBody')
            //         var totals = calcTotal(modal);
            //         totalDebit = totals[0]
            //         totalCredit = totals[1]
            //         var diff = totalDebit - totalCredit;
            //         if (diff > 0) {
            //             $(this).val(diff)
            //         }
            //         calcTotal(modal);
            //     }

        });

        $('body').on('click', '#journalEntryBody #JEdebit', function () {
              //autofills the amount missing
            //   var tr = $(this).closest('tr');
            //   fillEmptyRow(tr);

            // var input =  tr.find('#JEcredit').val();
            // if(input < 1){
            //     var modal =  $(this).closest('#journalEntryBody')
            //     var totals = calcTotal(modal);
            //     totalDebit = totals[0]
            //     totalCredit = totals[1]
            //     var diff = totalCredit - totalDebit;
            //     if (diff > 0) {
            //         $(this).val(diff)
            //     }
            //     calcTotal(modal);


            // }
        });

      

        // $('body').on('click', '#billFormBody tr', function () {

        //     fillEmptyRow($(this), ["#property_id","#unit_id","#description","#profile_id","#class_id"]);

        //  });

});