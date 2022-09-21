var depositForm = (function(){

      function check(row){

      //   var rowIcon = row.find('#deposit_icon_check');
      //   var rowInput = row.find('.deposit_input')
      //   var rowAmount = row.closest('.deposit_row').find('.deposit_row_amount')
    
      //   // updates visibility and value
      //   rowIcon.css('visibility', 'visible');;
      //   rowInput.attr('value', '1');
    
      //  var amount = currency(row.closest('.deposit_row').find('.deposit_row_amount').text());
      //  var totalAmount = currency(row.closest('#select_check_section').find('#total_of_deposit_checks').text());
      //  var totalSelectedChecks = row.closest('#select_check_section').find('#amount_of_selected_checks').text();
      //  totalAmount = currency(totalAmount).add(amount).format()

      //  row.closest('#select_check_section').find('#total_of_deposit_checks').text(totalAmount)
      //  row.closest('#depositModal').find('#hidden-deposit-top-amount').val(totalAmount)
      //  row.closest('#select_check_section').find('#amount_of_selected_checks').text(++totalSelectedChecks)
       
    
      }

      function uncheck(row ){

      //   var rowIcon = row.find('#deposit_icon_check');
      //   var rowInput = row.find('.deposit_input')
      //   var rowAmount = row.closest('.deposit_row').find('.deposit_row_amount')
    
      //   // updates visibility and value
      //   rowIcon.css( 'visibility', 'hidden');
      //   rowInput.attr('value', '0');

      //   var amount = currency(row.closest('.deposit_row').find('.deposit_row_amount').text());
      //  var totalAmount = currency(row.closest('#select_check_section').find('#total_of_deposit_checks').text());
      //  var totalSelectedChecks = row.closest('#select_check_section').find('#amount_of_selected_checks').text();
      //  totalAmount = currency(totalAmount).subtract(amount).format()

      //  row.closest('#select_check_section').find('#total_of_deposit_checks').text(totalAmount)
      //  row.closest('#depositModal').find('#hidden-deposit-top-amount').val(totalAmount)
      //  row.closest('#select_check_section').find('#amount_of_selected_checks').text(--totalSelectedChecks)
    
      
    
      
     }

      function selectAllChecks(checkBox){
          var rows = $(checkBox).closest('#select_check_section').find('.deposit_row')
          rows.each(function(){
            var row = $(this)

            var rowIcon = $(row).find('#deposit_check').find('#deposit_icon_check');
            if( $(row).css('display') != 'none'  &&  rowIcon.css("visibility") == "hidden" ){
              check(row)
            }
           
          })
          depositForm.depositTotal(checkBox)
      }

      function unSelectAllChecks(checkBox){
        var rows = $(checkBox).closest('#select_check_section').find('.deposit_row')
        rows.each(function(){
          var row = $(this)
          var rowIcon = $(this).find('#deposit_icon_check');
          if(rowIcon.css("visibility") == "visible" ){
            uncheck(row)
          }
        })
        depositForm.depositTotal(checkBox)
    }

      function filterProperty( propertyId , form){
        console.log('gdgdg')
       var body =  $(form).closest('#depositModal').find('#select_check_section')
        var rows =$(body).find('.deposit_row')
        if($(form).closest('label').hasClass('active')){
          // finding a matching property and showing it

            rows.each(function(){
              if($(this).attr('property-id') == propertyId){
                console.log('found match for ' + propertyId);
                $(this).show();
              }
            })
        

        }else{

        // if the user is unchecking a property, the loop finds the property to remove it

          rows.each(function(){
            if($(this).attr('property-id') == propertyId){
              console.log('found match for ' + propertyId);
              if($(this).find('#deposit_icon_check').css('display') != 'none'){
                $(this).find('#deposit_check').trigger('click');
              }
              $(this).hide();

             var rowIcon = $(this).find('#deposit_icon_check');
              
              // if the property being removed is checked, the loop below will uncheck it first
              // if(rowIcon.css("visibility") != "hidden"){
              //    depositForm.uncheck($(this))
              // }
           
            }
          })

        }
      }

      function selectAllProperties(checkBox){
        var checkBox = checkBox;
        if($(checkBox).closest('label').hasClass('active')){
            var rows =  $(checkBox).closest('.modal').find('.allAccounts');
            rows.each(function(){           
                if(!$(this).closest('label').hasClass('active')){
                  $(this).click();
              } 
            });
            //for deposits that the property is not active- shows those deposits
            var depositRows =  $(checkBox).closest('.modal').find('.deposit_row');
            $(depositRows).show();
        } else {
          console.log('not active')
            var rows =  $(checkBox).closest('.modal').find('.allAccounts')
            rows.each(function(){ 
              if($(this).closest('label').hasClass('active')){
                  $(this).click();
              }          
            });
            //for deposits that the property is not active- clicks the ckecked ones and hides all of them
            var depositRowsChecked =  $(checkBox).closest('.modal').find('.deposit_row #deposit_icon_check:visible');
            $(depositRowsChecked).click();
            var depositRowsUnckecked =  $(checkBox).closest('.modal').find('.deposit_row');
            depositRowsUnckecked.hide();
        }

        depositForm.depositTotal(checkBox)
    }

    function depositBottomTotal(form){
  
      totalAmount = 0;

      var amountInputs = $(form).closest('#depositModal').find('.depositForm_amount ')
      amountInputs.each(
          function () {
              let value = currency($(this).val())
              if (value) {  
                  totalAmount = currency(totalAmount).add(value).format();
              }
          }
      )
      $(form).closest('#depositModal').find('#deposit-bottom-total').html(totalAmount);
      return totalAmount;
  }

  function depositTotal(form){
  
    totalAmount = 0;

    var depositTopTotal = $(form).closest('#depositModal').find('#total_of_deposit_checks').html()
    var depositBottomTotal = $(form).closest('#depositModal').find('#deposit-bottom-total').html()

    totalAmount = currency(depositTopTotal).add(depositBottomTotal).format()
    $(form).closest('#depositModal').find('#deposit-total').html(totalAmount);
    $(form).closest('#depositModal').find('#hidden-deposit-total-amount').val(totalAmount);
    return totalAmount;
}


    return {
      selectAllProperties: selectAllProperties,
      check: check,
      uncheck: uncheck,
      selectAllChecks: selectAllChecks,
      unSelectAllChecks: unSelectAllChecks,
      filterProperty: filterProperty,
      depositBottomTotal: depositBottomTotal,
      depositTotal: depositTotal

    }
     
    


})()




function depositSelectAllDeposits(checkBox){
  var checkBox = checkBox;
  if($(checkBox).closest('label').hasClass('active')){
        depositForm.selectAllChecks(checkBox)
  } else {
       depositForm.unSelectAllChecks(checkBox)
  }
 
}

function Checkbox(checkbox){
  //$(checkbox).closest('label').toggleClass('active'); 
}

function depositSelectAllProperties(checkBox){
    var checkBox = checkBox;
    $(checkBox).closest('label').toggleClass('active'); 
    if($(checkBox).closest('label').hasClass('active')){
        $(checkBox).closest('aside').find('label').addClass('active')
    } else {
      console.log('not acive')
      $(checkBox).closest('aside').find('label').removeClass('active')
    }
}

      function setDepositTotalsOnload(modal){
        var amounts = $(modal).find('#depositBody #amount');
        var totalbottomAmount = 0;
        amounts.each(function(){
          var amount = $(this);
          totalbottomAmount += Number(amount.val().replace(',', ''));
        })
        $(modal).find('#deposit-bottom-total').html(totalbottomAmount);
        var modal = $(modal);
        var topTotal = Number($(modal).find('#total_of_deposit_checks').html().replace(',', ''));
          var bottomTotal = Number($(modal).find('#deposit-bottom-total').html().replace(',', ''));
          var allTotal = topTotal + bottomTotal;
          console.log('setDepositTotalsOnload')
          $(modal).find('#hidden-deposit-total-amount').val(allTotal);
          //$(modal).find('#deposit-total').html(allTotal);
          $(modal).find('#deposit-total').text(allTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
      }

$(document).ready(function () {

      //   $('body').on('click', '#deposit_check', function () {

      //     var row = $(this)
      //     var rowIcon = row.find('#deposit_icon_check');
          
      //   if( rowIcon.css("visibility") == "hidden"){

      //     depositForm.check(row)
             
      //   }else{

      //     depositForm.uncheck(row)

      //   }  

      //   depositForm.depositTotal($(this))

      // });
    $('body').on('click', '#deposit_check', function () {
            var thisTR = $(this).closest('tr');
            var greenCheck = thisTR.find('#deposit_icon_check');
            greenCheck.toggle();
            var oldTotal = $(thisTR).closest('.modal').find('#total_of_deposit_checks').text();
            var oldCount = $(thisTR).closest('.modal').find('#amount_of_selected_checks').text();
            var modal = $(thisTR).closest('.modal');
            var amount = thisTR.attr('amount');
            if(greenCheck.css('display') === 'none')
            {
              //removes name attr
              $(thisTR).find('input').removeAttr('name');
              console.log('unchecked'); 
              
              var totalAmount = currency(oldTotal).subtract(amount).format();
              $(modal).find('#total_of_deposit_checks').text(totalAmount); 
              $(modal).find('#amount_of_selected_checks').text(oldCount - 1); 
              
              
            }
            else
            {
              //sets name attr
              var id = thisTR.attr('data-id');
              var property_id = thisTR.attr('property-id');
              $(thisTR).find('#undeposited_id').attr('name', 'row[' + property_id+'][undeposited]['+ id +']');
              var totalAmount = currency(oldTotal).add(amount).format();
              $(modal).find('#total_of_deposit_checks').text(totalAmount); 
              $(modal).find('#amount_of_selected_checks').text(oldCount + 1); 
            }
            setDepositTotalsOnload(modal);
      });

      $('body').on('keyup', '.depositForm_amount', function () {
          depositForm.depositBottomTotal($(this))
          depositForm.depositTotal($(this))
      });

      			//function allTotals(modal){
           // console.log('$^%^%^%^%')
/*            $('body').on('focusout', '#depositBody #amount', function () {
              var modal = $(this).closest('.modal');
               updateBottomTotals(modal);

              //})
            });

            $('body').on('click', '.context-menu__link[data-action="delete"]', function () {
              var modal = $(this).closest('.modal');
              updateBottomTotals(modal);
           });

            function updateBottomTotals(modal){
              
              var amounts = $(modal).find('#depositBody #amount');
              var totalbottomAmount = 0;
              amounts.each(function(){
                var amount = $(this);
                totalbottomAmount += Number(amount.val().replace(',', ''));
              })
              $(modal).find('#deposit-bottom-total').html(totalbottomAmount);
               //$(modal).closest('.modal').find('#depositBody').find('#amount').on('focusout', function(){
                 console.log('changed@@');
                 var topTotal = Number($(modal).find('#total_of_deposit_checks').html().replace(',', ''));
                 var bottomTotal = Number($(modal).find('#deposit-bottom-total').html().replace(',', ''));
                 var allTotal = topTotal + bottomTotal;
                 $(modal).find('#hidden-deposit-total-amount').val(allTotal);
                 //$(modal).find('#deposit-total').html(allTotal);
                 $(modal).find('#deposit-total').text(allTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
            } */
			//}

          $('body').on('click', '#undepositedChecksBody .clickThis', function () {
            console.log('undepositedChecksBody clickThis');
            var thisTR = $(this).closest('tr');
            var greenCheck = thisTR.find('#deposit_icon_check');
            var id = thisTR.attr('id');
            if(greenCheck.css('display') === 'none')
            {
              //console.log('unchecked clickThis');
              thisTR.find('#checked_id').attr('name', 'checked_id['+ id +']').val(1);            
            }
            else
            {
              //console.log('checked clickThis');  
              thisTR.find('#checked_id').attr('name', 'checked_id['+ id +']').val(0);
            }
      });

});