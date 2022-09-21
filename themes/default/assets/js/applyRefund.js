var applyRefund = function () {
}
applyRefund.prototype = {

    applyRefundFunctions: function(){

        $('#sdApplyAmount').on('keyup', function(){ formsJs.updateUnapplied($(this).closest('.modal'), 'applyRefund')});
		$('#lmrApplyAmount').on('keyup', function(){ formsJs.updateUnapplied($(this).closest('.modal'), 'applyRefund')});
		$('body').on('keyup', '#applyRefund_input_amount2', function(){ formsJs.updateUnapplied($(this).closest('.modal'), 'applyRefund')});
		//validation- the sd refund can't be more than sd amount
        $('body').on('keyup', '.sdValidate', function(){
			var sdApplyAmount = Number($(this).closest('section').find('#sdApplyAmount').val());
			var sdRefundAmount = Number($(this).closest('section').find('#sdRefundAmount').val());
			var sdtotal = Number($(this).closest('section').find('#sdTotal').html().replace(',', ''));
			var totalApplyRefund = sdApplyAmount + sdRefundAmount;
			if(totalApplyRefund > sdtotal){
				JS.showAlert('danger', 'Applied and refund amount can not be greater then sd amount')
				$(this).val('0.00');
				$(this).trigger('keyup');
			}
		});
		//validation- the lmr refund can't be more than lmr amount
		$('body').on('keyup', '.lmrValidate', function(){
			var lmrApplyAmount = Number($(this).closest('section').find('#lmrApplyAmount').val());
			var lmrRefundAmount = Number($(this).closest('section').find('#lmrRefundAmount').val());
			var lmrtotal = Number($(this).closest('section').find('#lmrTotal').html().replace(',', ''));
			var totalApplyRefund = lmrApplyAmount + lmrRefundAmount;
			if(totalApplyRefund > lmrtotal){
				JS.showAlert('danger', 'Applied and refund amount can not be greater then lmr amount')
				$(this).val('0.00');
				$(this).trigger('keyup');
			}
		});
		//sd or lmr on input apply to the transactions for that amount(if exact amount apply to that one) 
		$('body').on('focusout', '#sdApplyAmount, #lmrApplyAmount', function(){
			//var sdApplyAmount = Number($(this).val());
			var sdApplyAmount = Number($(this).closest('section').find('#sdApplyAmount').val());
			var lmrApplyAmount = Number($(this).closest('section').find('#lmrApplyAmount').val());
			var totalApplyRefund = lmrApplyAmount + sdApplyAmount;
			var trs = $(this).closest('.modal').find('#applyRefundTransactions').find('tr');

			trs.each(function(){
				 $(this).find('.allInputAmounts').val('');
				 if( $(this).find('.greenCheckTd').find('i').css('display') != 'none' ){$(this).find('.greenCheckTd').trigger('click');}
			});
			var sdmatchFound = false;
			var lmrmatchFound = false;
			trs.each(function(){
				if(!sdmatchFound){
						if(Number($(this).find('.openBalance').html().replace(',', '')) == Number(sdApplyAmount)){
						//$(this).find('.openBalance').html(Number(sdApplyAmount));
						$(this).find('.greenCheckTd').trigger('click');
						sdmatchFound = true;
						totalApplyRefund = totalApplyRefund - sdApplyAmount;
					}
				}
				if(!lmrmatchFound){
					if(Number($(this).find('.openBalance').html().replace(',', '')) == Number(lmrRefundAmount)){
						$(this).find('.greenCheckTd').trigger('click');
						lmrmatchFound = true;
						totalApplyRefund = totalApplyRefund - lmrRefundAmount;
					}
				 }
			});
			if(totalApplyRefund > 0){
				var remainingTotal = sdApplyAmount;
				trs.each(function(){
					//if( $(this).find('.greenCheckTd').find('i').css('display') == 'none' ){
						$(this).find('.greenCheckTd').trigger('click');
						totalApplyRefund = totalApplyRefund - Number($(this).find('.openBalance').html().replace(',', ''));
						console.log(totalApplyRefund)
						if(totalApplyRefund < 0){
							return false;
						}
					//}
				});
			}
		});
		//validation- Checking Account is required for refund
		$('body').on('keyup', '#lmrRefundAmount, #sdRefundAmount', function(){
			var checkingAccount = $(this).closest('.modal').find('#checkingAccount').closest('.select').find('input[type=hidden]').val();
			if(checkingAccount < 1){
				JS.showAlert('danger', 'Checking Account is required for refund.')
				$(this).val('0.00');
				//$(this).trigger('keyup');
			}
		});
		//calculates total for apply refund and receive payments
		     $('body').on('keyup focusout', '.calculateTotal', function () {
                var that = $(this);
            	var modal = $(that).closest('.modal');
                
                var allAmounts = modal.find('.calculateTotal');
                var totalAmount =0;
    
                allAmounts.each(
                    function(){
                        var value = Number($(this).val().replace(',', ''));
                        if (value) {
                            totalAmount += value;
                        }           
                    });
                if($(modal).attr('id') == 'applyRefundModal'){
					sdLmrCalculate(modal, that, totalAmount); 
					console.log(that.val());
				}
                if($(modal).attr('id') == 'receive_paymentModel'){
					receivedPayments.triggerCheck(that);
                }
                var total = $(modal).find('#totalAmount');
                console.log(total);
                total.html(totalAmount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') ) ;

			});

			function sdLmrCalculate(modal, clicked, total){
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
            }
    },
        setNames: function (leaseId, modal){
            for (var j = 0; j < leases.length; j++) {
                if(leaseId == leases[j].id){ 
                   var names = `<input type="hidden" id="leaseIdOnchange" name="leaseInfo[lease_id]" value="`+ leases[j].id +`">
                                <input type="hidden" name="leaseInfo[property_id]" value="`+ leases[j].property_id +`">
                                <input type="hidden" name="leaseInfo[unit_id]" value="`+ leases[j].unit_id +`">`;
                }
            }
            $(modal).find('#formNames').empty();
            $(modal).find('#formNames').append(names);
		}


};
    var applyRefund = new applyRefund();
    $(document).ready(function () {
         applyRefund.applyRefundFunctions();
    });




