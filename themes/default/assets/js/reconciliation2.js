var reconciliation2 = function () {
}
reconciliation2.prototype = {

    totalneg :0,
    totalpos :0,
    countneg :0,
    countpos :0,
			

    //gets the rec for this account
    getAccountInfo: function(id, modal, type, clicked = null){
    var form = $(modal).find('form')[0];
    console.log(form);
    var formData1 = new FormData(form);
    formData1.append('refresh',true);
    console.log(formData1);
    
    //$("#fromDate").text('');
    $.ajax({
        type: "POST",
        url: JS.baseUrl+'reconciliations/editReconciliation/',
        data: formData1,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            //console.log("response");
            //console.log(response);
            var credits = response.credits;
            var debits = response.debits;
            var cleared = response.cleared;
            var rec = response.reconciliation.reconciliation;
            //console.log(credits);
            reconciliation2.creditBody(credits, modal);
            reconciliation2.debitBody(debits, modal);
            reconciliation2.topPart(cleared, rec, modal);
            if(type == 'refresh'){
                //reconciliation2.triggerClick(modal, clicked);
            }
            $(modal).find('form').attr('action',response.target );
            $(modal).find('#recId').empty();
            $(modal).find('#recId').append('<input type="hidden"  name="reconciliation[id]"  value="' + rec.r_id +'">');
            
        }
    });
    },
    //populates the credit part of the reconciliation form
     creditBody: function(credit, modal){
        var creditBody = "";
        this.totalneg =0;
        this.countneg =0;
        for(var i = 0; i < credit.length; i++){
            if (credit[i].rec_id){
                this.totalneg += parseFloat(credit[i].amount);
                this.countneg ++;
            }

                 creditBody +=`<tr id="credit_row" v-on:click="updateCreditTotal" class="rec_row credit_rec_row" data-id="`+ credit[i].th_id +`" data-type="`+ credit[i].type_id +`">
                                            <td ><input  class="rec_input `;
                                            creditBody +=`" type="hidden"  name="transactions[`+ credit[i].id +`]"`;
                                            creditBody += credit[i].rec_id ? "value='1'" : "value='0'";
                                            creditBody +=`><i id="rec-icon-check" class="icon-check" `;
                                            creditBody += credit[i].rec_id ? "" : "style='visibility: hidden'";
                                            creditBody +=`></i> <span class="hidden">Yes</span></td>
                                            <td>`+ credit[i].date +`</td>
                                            <td>`+ credit[i].type +`</td>
                                            <td>`+ credit[i].num +`</td>
                                            <td>`+ credit[i].vendor +`</td>
                                            <td class="credit_amount">`+ credit[i].amount +`</td>
                                        </tr>`;
            }
                $(modal).find('#creditBody').empty();
                $(modal).find('#creditBody').append(creditBody);
    },
    //populates the debit part of the reconciliation form
     debitBody: function(debit, modal){
        var debitBody = "";
        this.totalpos =0;
        this.countpos =0;
        for(var i = 0; i < debit.length; i++){
            if (debit[i].rec_id){
                this.totalpos += parseFloat(debit[i].amount);
                this.countpos ++;
            }
            debitBody +=`<tr id="debit_row" class="rec_row debit_rec_row" data-id="`+ debit[i].th_id +`" data-type="`+ debit[i].type_id +`">
                                            <td ><input  class="rec_input `;
                                            //debitBody += debit[i].rec_id ? "clickThis" : "";
                                            debitBody +=`" type="hidden"  name="transactions[`+ debit[i].id +`]"`;
                                            debitBody += debit[i].rec_id ? "value='1'" : "value='0'";
                                            debitBody +=`><i id="rec-icon-check" class="icon-check"`; 
                                            debitBody += debit[i].rec_id ? "" : "style='visibility: hidden'";
                                            debitBody +=`></i> <span class="hidden">Yes</span></td>
                                            <td>`+ debit[i].date +`</td>
                                            <td>`+ debit[i].type +`</td>
                                            <td>`+ debit[i].num +`</td>
                                            <td>`+ debit[i].vendor +`</td>
                                            <td class="debit_amount">`+ debit[i].amount +`</td>
                                        </tr>`;
            }
                $(modal).find('#debitBody').empty();
                $(modal).find('#debitBody').append(debitBody);
    },
    //populates the top part of the reconciliation form
     topPart: function(cleared, rec, modal){

        // $('#cleared_payments').html(cleared.cc);
        // $('#cleared_deposits').html(cleared.cd);
        // $('#payment_total').html(cleared.sc);
        // $('#debit_total').html(cleared.sd);
                    endBal = $(modal).closest('#reconciliationModal').find('#ending_balance').val();
                    diff = parseFloat(endBal)-(parseFloat(rec.beginning_bal)+this.totalpos-this.totalneg-parseFloat(rec.service_charge)+parseFloat(rec.interest_earned));
                    console.log('totalneg: ', this.totalneg, ' totalpos', this.totalpos);
					$(modal).closest('#reconciliationModal').find('#debit_total').text(this.totalpos.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
					$(modal).closest('#reconciliationModal').find('#payment_total').text(this.totalneg.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
					$(modal).closest('#reconciliationModal').find('#cleared_payments').text(this.countneg);
					$(modal).closest('#reconciliationModal').find('#cleared_deposits').text(this.countpos);
					$(modal).closest('#reconciliationModal').find('#rec_diff').text(parseFloat(diff).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));
        //$(modal).find('#cleared_payments').html('0');
        //$(modal).find('#cleared_deposits').html('0');
        //$(modal).find('#payment_total').html('0');
        //$(modal).find('#debit_total').html('0');
        $(modal).find('#interest_earned').html(rec.interest);
        $(modal).find('#service_charge').html(rec.sc);
        $(modal).find('#rec_begin_bal').html(rec.begBal);
    },
    //clicks all transactions that were clicked(by refresh), and all transactions onload that were already clicked
    triggerClick: function(modal, clicked){
        for(var i =0; i < clicked.length; i++){
            console.log('clicked array' + clicked[i])
            $(modal).find('tr[data-id='+ clicked[i]+']').trigger('click');
        }
    }

}
var reconciliation2 = new reconciliation2();
$(document).ready(function () {
});
        //refreshes a rec so it gets transactions that were just added
        $('body').on('click', '#refreshRec1', function(e){
            var modal = $(this).closest('.modal');
            e.stopPropagation();
            var clicked = [];
            var that = this;
            var ids = $(this).closest('.modal').find('tr:has(input.rec_input[value=1])');
            ids.each(function(){
                clicked.push($(this).attr('data-id'));
            });
            //console.log(clicked);
            reconciliation2.getAccountInfo($(that).closest('.modal').find('#account_id').closest('p').find('input[type=hidden]').val(), $(that).closest('.modal'), 'refresh', clicked);
        })