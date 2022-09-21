<div class="modal fade email-invoice-modal" id="email-invoice" tabindex="-1" role="dialog"  type="email-invoice" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root" class="no-print">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 30px; height: calc(100vh - 70px)">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
				  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="email-invoice-entry" type="email-invoice">
			<div class="t_input_wrapper">
				<header class="modal-h">
					<h2>Choose Invoices</h2>
				<nav>
                  <ul>
                      <li><span class="buttons" style=""><span class="min" style="padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                      <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                      <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                  </ul>
              </nav>
					<div>
						<p class="input-search">
							<label for="fsa">Search</label>
							<input type="text" id="fsa" name="fsa">
							<button type="submit">Submit</button>
							<a href="./"><i class="icon-microphone"></i> <span>Record</span></a>
						</p>
					</div>
			  <nav>
				<ul>
					<li id="printInvoiceTable"><a class="print" href="#"><i class="icon-print"></i> <span>Print</span></a></li>
				</ul>
			  </nav>
					<p class="submit"><button type="button" id="exit">Exit</button></p>
				</header>
        <div class ="has-table-c">
            <table class="table-c dc d da  billTable mobile-hide dataTable no-footer" style="display: table;  margin:0 auto;  ">
                <thead id="cc_grid_charge_head" class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden; border-radius: 6px; margin-bottom: 6px;">
						<tr style="display: table; width: 100%; table-layout: fixed;">	
							<th style="width: 4%;" class="check-a"><label for="selectAllCheckboxes" class = "checkbox"><input type="checkbox" id="selectAllCheckboxes" class="selectAllCheckboxes" name="fbm2"><div class="input"></div></label></th>
							<th style="width: 6%;">Email</th>
							<th style="width: 6%;">Mail</th>
							<th style="width: 8%;">In court</th>
							<th style="width: 15%;">Name</th>
							<th style="width: 15%;">Lease</th>
							<th style="width: 15%;">Property</th>
							<th style="width: 15%;">Unit</th>
							<th style="width: 15%;">Balance</th>
						</tr>
					</thead>
					<tbody id="emailInvoice_body" class="dataTables_scrollBody testTable" style=" display: block;height: calc(100vh - 450px);overflow: auto; box-shadow: 0 0px 0px; border-width: 0px;">
					</tbody>
					<style type="text/css" onload="getProfiles($(this).closest('.modal'))"></style>
				</table>
				</div>
				<footer class="a">
					<p class="m0">
						<button after="mclose" id="printTemplate">Print Invoices</button>
					</p>
				</footer>
			
		</div>
                  </form>               
            </div>
        </div>
	</div>
	<!-- print -->
	<div id="printInvoice" style="display: none"></div>
	<div id="printInvoiceTableDiv" style="display: none">
	<h3>Invoice Grid</h3>
		<table style="display: table; margin:0 auto; border: 2px solid black;">
			<thead id="printInvoiceTableThead" style="display: table; width: 100%; table-layout: fixed;"></thead>
				<tbody id="printInvoiceTableBody"></tbody>
		</table>
	</div>
	<!-- end print -->
</div>
</div>

<script>

        var profiles = <?php echo $profiles ? json_encode($profiles) : '0'?>;
    console.log(profiles);
//
//$(document).ready(function () {
	function getProfiles(modal){
		formsJs.getAjaxAccount($(modal).find('#emailInvoice_body'), profiles, 'email_invoice');
		JS.checkboxes(modal);
	}
//});

console.log('cc charges00');

//populates all transactions
	// function getAjaxAccount(body, profiles){
    //     console.log(profiles);
	// 	$(body).empty();
	// 	var newRow = "";
	// 	if(profiles){

	// 			for (var i = 0; i < profiles.length; i++) {
	// 				newRow += `<tr id="`+ i +`" role="row" class="allTransactions" style="display: table; width: 100%; table-layout: fixed;">
	// 							<td style="width: 4% !important;">
	// 							<input type="hidden" name="" value="`+ profiles[i].profile_id +`" id="profile_id">
	// 							<input type="hidden" name="" value="`+ profiles[i].lease_id +`" id="lease_id">
									 
	// 							</td>
	// 							<td style="width: 6%;" class="check-a">
	// 							<label for="email" class="checkbox `;
	// 								newRow += profiles[i].email_statements == 1 ?  ' active' : ''; 
	// 								newRow += `"><input type="hidden" name="email" value="0" /><input type="checkbox" value="1" `;
	// 								newRow += profiles[i].email_statements == 1 ? 'checked' : '';
	// 								newRow +=` id="email" name="email" class="hidden allAccounts" aria-hidden="true"><div class="input"></div></label>
	// 							</td>
	// 							<td style="width: 6%;" class="check-a">
	// 							<label for="mail" class="checkbox `;
	// 								newRow += profiles[i].mail_statements == 1 ?  ' active' : ''; 
	// 								newRow += `"><input type="hidden" name="mail" value="0" /><input type="checkbox" value="1" `;
	// 								newRow += profiles[i].mail_statements == 1 ? 'checked' : '';
	// 								newRow +=` id="mail" name="mail" class="hidden allAccounts" aria-hidden="true"><div class="input"></div></label>
	// 							</td>
	// 							<td style="width: 8%;" class="check-a">
	// 							<label for="in_court" class="checkbox `;
	// 								newRow += profiles[i].in_court == 1 ?  ' active' : ''; 
	// 								newRow += `"><input type="checkbox"`;
	// 								newRow += profiles[i].in_court == 1 ? 'checked' : '';
	// 								newRow +=` id="in_court" name="in_court" class="hidden allAccounts" aria-hidden="true"><div class="input inCourtCheckbox"></div></label>
	// 							</td>`;
    //                         $.each(profiles[i], function(key, value) {
	// 							//console.log(key, value);
	// 							if(key != "id" && key != "profile_id" && key != "lease_id" && key != "mail_statements" && key != "email_statements" && key != "in_court"){
	// 								newRow +=	`<td  style="width: 15% ">` + value +`</td>`;
	// 							}
    //                         });
    //                         newRow +=	`</tr>`;
	// 			} 
	// 		}else{
	// 			newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No Profiles with a balance.</td></tr></div>`;
	// 		}
    //         body.append(newRow);
	// 		JS.checkboxes();
	// }

	// 	// printTemplate
	// 	$('body').on('click', '#printTemplate', function (e)
	// {
	// 	var invoicesSelected = [];
	// 	e.preventDefault();
	// 	var that = this;
	// 	var trs = $(this).closest('.modal').find('#emailInvoice_body').find('tr');
	// 		trs.each( function(){ 
	// 			var court = $(this).find('input[name=in_court]:checked').length > 0 ? 1 : 0;
	// 			var email = $(this).find('input[name=email]:checked').length > 0 ? 1 : 0;
	// 			var print = $(this).find('input[name=mail]:checked').length > 0 ? 1 : 0;
	// 			if(email == 1 || print == 1 || court == 1){
	// 				invoicesSelected.push({profile_id: $(this).find('input[id=profile_id]').val(),lease_id: $(this).find('input[id=lease_id]').val(), email: email, print: print, court:court});
	// 			}
	// 			//console.log(invoicesSelected); 
	// 		});
	// 		if(invoicesSelected === undefined || invoicesSelected.length == 0){alert('Select  email or print.'); return;}
	// 		$.post(JS.baseUrl+"< ?php echo $target ?>", {
	// 				'params': JSON.stringify(invoicesSelected)
	// 			}, function (result) {
	// 			//console.log(result);
	// 			if(result){
	// 				$(that).closest('.modal').find('#printInvoice').append(result);
	// 				$(that).closest('.modal').find("#printInvoice").addClass('print-section');
	// 				window.print();
	// 				var typeId = $(that).closest('.modal').attr('type');
	// 				var openId = $(that).closest('.modal').attr('openModal-id');
	// 				$(that).closest('.modal').hide();
	// 				JS.openModalsObjectRemove(typeId, openId);
	// 			}
	// 		});	
	// });
	// 	$('body').on('change', '#in_court', function (e){
	// 		var trs = $(this).closest('.modal').find('#emailInvoice_body').find('tr');
	// 		var that = this;
	// 		var checked = $(this).prop("checked");
	// 		var id = $(this).closest('tr').attr('id');
	// 		console.log(checked);

	// 		trs.each(function(){
	// 			if(id != $(this).attr('id')){
	// 				// console.log($(that).closest('tr').find('input[id=lease_id]').val());
	// 				// console.log($(this).find('input[id=lease_id]').val());
	// 				if($(that).closest('tr').find('input[id=lease_id]').val() == $(this).find('input[id=lease_id]').val()){
	// 					if($(this).find('input#in_court').prop("checked") != checked){
	// 							$(this).find('input#in_court').prop("checked",checked);
	// 							$(this).find('input#in_court').closest('label').toggleClass('active');
	// 							//$(this).find('input#in_court').change();
	// 						}
	// 				}
	// 			}
	// 		})
	// 	});
	// 	$('body').on('click', '#printInvoiceTable', function (){
	// 		var thead = $(this).closest('form').find('table').find('thead').html();
	// 		var tbody = $(this).closest('form').find('table').find('tbody').html();
	// 		// console.log(thead);
	// 		// console.log(tbody);
	// 		$(this).closest('.modal').find('#printInvoiceTableDiv').find('#printInvoiceTableThead').empty().append(thead);
	// 		$(this).closest('.modal').find('#printInvoiceTableDiv').find('#printInvoiceTableBody').empty().append(tbody);
	// 		$(this).closest('.modal').find("#printInvoiceTableDiv").addClass('print-section');
	// 		window.print();
	// 		var typeId = $(this).closest('.modal').attr('type');
	// 		var openId = $(this).closest('.modal').attr('openModal-id');
	// 		$(this).closest('.modal').hide();
	// 		JS.openModalsObjectRemove(typeId, openId);
	// 	});


</script>
