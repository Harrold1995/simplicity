<div class="modal fade cc-modal" id="ccModal" tabindex="-1" role="dialog"  type="cc-grid" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style=" padding: 30px;">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
									

				  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="cc-charges-grid-entry" type="cc_grid_charge">
			<div class="t_input_wrapper">
				<header class="modal-h">
					<h2>CC charges grid entry</h2>
					<div>
						<p class="input-search">
							<label for="fsa">Search</label>
							<input type="text" id="fsa" name="fsa">
							<button type="submit">Submit</button>
							<a href="./"><i class="icon-microphone"></i> <span>Record</span></a>
						</p>
					</div>
				<nav style="margin-left: 0">
					<ul>
						<li><span class="buttons" style=""><span class="min" style="padding: 8px 20px;cursor: pointer;">_</span></span> </li>
						<li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
						<li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
					</ul>
				</nav>
					<p class="submit"><button type="button" id="exit">Exit</button></p>
				</header>
				<ul class="list-choose a">
					<li style="margin-right: 0 !important">
						<ul>
							<li class="select" style="width:290px !important">
							<label for="account">Account</label>
							<select class="editable-select quick-add set-up" id="account" name="ccAccount_id" onchange="getCcGridAccount($(this).closest('#ccModal').find('#cc_grid_charge_body'),$(this).closest('.select').find('input[type=hidden]').val())">
								 <option value="-1"  ></option>
									<?php
									$selected = json_decode($creditCards);
										foreach (json_decode($creditCards) as $creditCard) {
											// echo '<option value="-1" selected >' . "Select Apples" . '</option>';
											echo '<option value="' . $creditCard->id . '" ' . (isset($creditCards) && $selected[0]->id == $creditCard->id  ? 'selected' : '') . ' >' . $creditCard->name . '</option>';
									} ?>   
                                </select>
							</li>
						</ul>
					</li>
					<li>
						<ul>
							<li>
								<label for="lcab" class="small radio"><input type="radio" id="lcab" name="lcab"></label>
								<span class="is-date"><input type="date" id="fromDate" name="lcac" class="date"></span>
								<label for="toDate" class="">To</label>
								<span class="is-date"><input type="date" id="toDate" name="lcad" class="date" value=""></span>
							</li>
							<li><label for="alldates"><input type="radio" id="alldates" name="lcab"> All dates</label></li>
						</ul>
					</li>
					<li>
						<ul>
							<li>
								<!--<label for="lcaf"><input type="radio" id="lcaf" name="lcaf"> Vendor</label>
								<select id="lcag" name="lcag">
									<option>R&amp;D Plumbing</option>
									<option>Position #1</option>
									<option>Position #2</option>
									<option>Position #3</option>
									<option>Position #4</option>
									<option>Position #5</option>
								</select>-->
								<label for="lcaf"><input type="radio" id="lcaf" name="lcaf"> Vendor</label>
								<select id="profile_id" name="profile_id">
								<option value="-1" selected >Select Vendor</option>
                                <?php foreach($names as $name): 
									echo '<option value="' . $name->id . '" ' . (isset($headerTransaction) && $headerTransaction->profile_id == $name->id ? 'selected' : '') . '>' . $name->vendor . '</option>';
								  endforeach; ?>
                                </select>
							</li>
							<li><label for="lcah"><input type="radio" id="lcah" name="lcaf"> All vendors</label></li>
						</ul>
					</li>
				</ul>
        <div class ="has-table-c">
            <table class="table-c dc d da  billTable mobile-hide dataTable no-footer" style="display: table;  margin:0 auto; border-collapse: collapse; ">
                <thead class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden; border-radius: 6px; margin-bottom: 6px;">
						<tr>
							<th style="width: 3%;"></th>
							<th style="width: 4%;" class="check-a"><label for="selectAllCheckboxes" class = "checkbox"><input type="checkbox" id="selectAllCheckboxes" class="selectAllCheckboxes" name="fbm2"><div class="input"></div></label></th>
							<th style="width: 7%;" class="">Date</th>
							<th style="width: 7%;">Amount</th>
							<th style="width: 11%;">Description</th>
							<th style="width: 11%;">Reference</th>
							<th style="width: 9%;">Card Member</th>
							<th style="width: 8%;">Property</th>
							<th style="width: 8%;">Account</th>
							<th style="width: 8%;">Unit</th>
							<th style="width: 8%;">Reciept</th>
						</tr>
					</thead>
					<tbody id="cc_grid_charge_body" class="dataTables_scrollBody testTable" style=" display: block;height: calc(100vh - 500px);overflow: auto; box-shadow: 0 0px 0px; border-width: 0px;">							
					</tbody>
					<style type="text/css" onload="getFirstAccount($(this).closest('.modal'))"></style>
				</table>
				</div>
				<p class="strong scheme-b m20 size-a overlay-a" id="selectedCharges">0 Charges selected</p>
				<footer class="a">
					<p class="m0" style="margin-right: 5%;">
						<button id="editMultiple">Edit Multiple</button>
						<button type="submit" after="mclose" class="grid">Record charges</button>
						<button type="reset">Delete</button>
						<span  id="editMultipleDropdowns"></span>
					</p>
				</footer>
			
		</div>
                  </form>               
            </div>
        </div>
    </div>
</div>

<script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/custom2.js"></script>
<script>

	 var properties= <?php echo $jProperties; ?>;
     var accounts = <?php echo $jAccounts; ?>;
	 var units = <?php echo $jUnits; ?>;
	 var names = <?php echo $jNames; ?>;
	 var propertyAccounts = <?php echo $jPropertyAccounts; ?>;
	 var error = '<?php echo $error ? $error : ""; ?>';
	 console.log(error)
	//  < ? php if($error){echo "<h1>$error</h1>";} ?>

//triggers click on first account
//$(document).ready(function () {
	function getFirstAccount(modal){
		if(error == true){
			var first = $(modal).find("#account").find('option:selected').val();
			getCcGridAccount($(modal).find('#cc_grid_charge_body'), first);
		}else{
			var errorDiv = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">`+ error +`</td></tr></div>`;
			getCcGridAccount($(modal).find('#cc_grid_charge_body'), first);
			$(modal).find('footer').append(errorDiv);
		}

	}
//});

var allDates = document.getElementById('alldates');
	 //var firstAccount = < ?php echo $ofxImports; ?>;
console.log('cc charges00');

//populates all transactions
	// function getCcGridAjaxAccount(body, account){
	// 	//onChange="JS.loadList('api/getUnitsProperty', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , '#unitId',  $(this).closest('.allTransactions')) ;
	// 	//JS.loadList('api/getAccountsProperty', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , '#accountId',  $(this).closest('.allTransactions'));"
	// 	//onChange="unitsApi($(this).closest(\'.select\').find(\'input[type=hidden]\').val() , $(this).closest('tr').find('#unitId'));
	// 	//accountsApi($(this).closest(\'.select\').find(\'input[type=hidden]\').val() , $(this).closest('tr').find('#accountId'));"
	// 	$(body).empty();
	// 	selected = 0;
	// 	formsJs.selectedCharges(0, "Charges", $(body).closest('.modal'));
	// 	var newRow = "";
	// 	if(account){
	// 			for (var i = 0; i < account.length; i++) {
	// 				newRow += `<tr id="`+ account[i].id +`" role="row" class="allTransactions">
	// 							<input type="hidden" name="" value="`+ account[i].id +`" id="ofxId">
	// 							<td style="width: 3% !important;" class="link">
	// 								<a href="#" class="selectTransactionClass" onclick="expand($(this).closest('td'))"><span class="hidden">More</span></a>
	// 								<div class="shadow" style="width: 1390px; height: 36px;"></div>
	// 							</td>
	// 							<td style="width: 4%;" class="check-a">
	// 								<label for="`+ i+`" class="checkbox">
	// 									<input type="checkbox" id="`+ i +`" name="`+ i +`" class="hidden allAccounts" aria-hidden="true"
	// 									onchange="formsJs.Checkbox($(this),`+ account[i].id +`);">
	// 										<div class="input"></div>
	// 								</label>
	// 							</td>
	// 							<td style="width: 7%; text-align:center" id="transaction_date">`+ account[i].date +`
	// 								<input type="hidden" value="`+ account[i].date +`">
	// 							</td>
	// 							<td name="" style="width: 7%; text-align:center" id="amount">`+ account[i].amount +`
	// 								<input id="totalAmount"  class="amount" type="hidden" name="" value="`+ account[i].amount +`">
	// 							</td>
	// 							<td style="width: 11%; text-align:center; overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px" id="description">`+ account[i].description +`
	// 								<input type="hidden"  name="" value="`+ account[i].description +`">
	// 							</td>
	// 							<td style="width: 11%; text-align:center" id="transaction_ref">`+ account[i].ref +`
	// 								<input type="hidden"  name="" value="`+ account[i].ref +`">
	// 							</td>
	// 							<td style="width: 9%; text-align:center">`+ account[i].card_member +`</td>
	// 							<td style="width: 8%; text-align:center" id="property_id">
	// 								<span class="select">
	// 									<select class="w135 editable-select quick-add set-up formGridPropertySelected"  name="property_id"  modal="" type="table" key="">
	// 											<option value="-1" selected ></option>`
	// 										for (var j = 0; j < properties.length; j++) {
	// 											newRow += `<option value='` + properties[j].id + `'>` + properties[j].name + `</option>`;
	// 										}
	// 										newRow +=`	</select>
	// 									</span>
	// 							</td>
	// 							<td style="width: 8%; text-align:center" id="account_id" class="formGridAccountTd">
	// 								<span class="select">
	// 									<select class="w135 editable-select quick-add set-up "  id="accountId" name="account_id"  modal="" type="table" key="">
	// 										<option value="-1" selected ></option>`
	// 									for (var a = 0; a < accounts.length; a++) {
	// 										newRow += `<option value='` + accounts[a].id + `'>` + accounts[a].name + `</option>`;
	// 									}
	// 									newRow +=`	</select>
	// 									</span>
	// 							</td>
	// 							<td style="width: 8%; text-align:center" id="unit_id">
	// 								<span class="select">
	// 									<select class="w135 editable-select quick-add set-up formGridUnitSelect"  id="unitId" name="unit_id"  modal="" type="table" key="">
	// 										<option value="-1" selected ></option>
	// 									</select>
	// 								</span>
	// 							</td>
	// 							<td style="width: 8%; text-align:center; padding-top: 5px;">
	// 								<span class="input-file">
	// 										<label for="p-image" style = "margin-bottom: 0px"><input type="file" style ="display:none;" name="original"  id="p-image" targetimg="#original-lease"> <a class="receipt" style="text-decoration: underline; font-size: 13px; color: green;">Reciept</a></label>
	// 								</span>
	// 							</td>
	// 						</tr>`;
	// 			} 
	// 		}else{
	// 			newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No Transactions for this account.</td></tr></div>`;
	// 			//<td style="width:30%; text-align:center"> No Transactions for this account.</td><td style="width:30%"></td></tr>`;
	// 		}
	// 		body.append(newRow);
	// 		$(body).find('.editable-select').editableSelect();
	// 		editMultipleBox();
	// 		JS.checkboxes();
	// }
//gets the transactions for an account
	function getCcGridAccount(body, accountId){

		$("#fromDate").text('');
		$.ajax({  
			type: 'GET',
			url: JS.baseUrl+'CreditCard/getAccount/', 
			data: { id: accountId },
			dataType: 'json',
			success: function(response) {
				formsJs.getAjaxAccount(body, response, 'cc_grid_charge');
				//getCcGridAjaxAccount(body, response);
				ccDateSearch(response);
				allDatesButton(response);
			}
		});
	}
	//from and to date eventlistner
	function ccDateSearch(account){
		document.getElementById("fromDate").oninput = function() {
			checkDates(account);
			$("#lcab").trigger("click");
		};
		document.getElementById("toDate").oninput = function() {
			checkDates(account);
			$("#lcab").trigger("click");
		};
	}
	//filters transactions based on to and from dates
	function checkDates(account){
		var dateSearchAccount = [];
			var fromDate = document.getElementById("fromDate");
			var toDate = document.getElementById("toDate");
			for (var i = 0; i < account.length; i++) {
					if(account[i].date >= fromDate.value && account[i].date <= toDate.value){
						dateSearchAccount.push(account[i]);
					}
			}
			formsJs.getAjaxAccount($('#cc_grid_charge_body'), dateSearchAccount, 'cc_grid_charge');
	}
	function allDatesButton(account){
		allDates.onclick = function() {
			//getCcGridAjaxAccount($('#cc_grid_charge_body'), account);
			formsJs.getAjaxAccount($('#cc_grid_charge_body'), account, 'cc_grid_charge');
			// $("#fromDate").text('');
			// $("#toDate").empty();
			console.log('all dates clicked');
 		}
	}
	//used for details
	//var spot;
	//makes new rows for details
	// function expandCCCharge(e)
	// {
			
	// var tdInfo = [];
	// var getTR = e.closest('tr'); 
	// $(getTR).find('td').each (function( column, td) {

	// 	if($(td).hasClass('description')){
	// 		var description = $(td).closest('td').find('input[type=hidden]').val();
	// 		tdInfo.push(description);
	// 	}else{
	// 		tdInfo.push($(td).text());
	// 	}
	// });
	// var selectedProperty = getTR.find('#property_id').closest('td').find('input[type=hidden]').val();
	// var selectedAccount = getTR.find('#accountId').closest('td').find('input[type=hidden]').val();
	// var selectedUnit = getTR.find('#unitId').val(); 
	// //console.log(tdInfo[7]);
	// var total = tdInfo[4];
	// console.log(selectedProperty);
	// //onChange="JS.loadList('api/getUnitsProperty', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , '#unitId',  $(this).closest('.allTransactions')) ;
	// //JS.loadList('api/getAccountsProperty', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , '#accountId',  $(this).closest('.allTransactions'));"
	// var newRow2 = `<tr id="`+ getTR.attr('id') +`" class="details `+ getTR.attr('id') +`" role="row">
	
	// 					<td style="width: 3% !important;" class="link">
	// 					</td>
	// 					<td class="remove" onclick="removeTR($(this));"><a href="#" class="remove"><i class="icon-x"></i> <span>Remove</span></a></td>
	// 					<td style="width: 7%; text-align:center">`+ tdInfo[2] +`</td>
	// 					<td style="width: 9%; text-align:center"><input type="text" id="amountToAdd" style="width: 130px;" name="details[`+ getTR.attr('id') +`][`+ spot +`][amount]" class="amount"
	// 					 onmousedown="expandCCCharge($(this)); this.onmousedown=null;" onfocusout="formsJs.amountInput($(this).closest('tr'), $(this).closest('.modal').attr('id'))"></td>
	// 					<td class="description" style="width: 11%; text-align:center; overflow: hidden; text-overflow: ellipsis; white-space:nowrap; max-width: 180px">
	// 						<input type="text" class="detailsDescription"  value="`+ tdInfo[4] +`">
	// 						<input type="hidden"  name="details[`+ getTR.attr('id') +`][`+ spot +`][description]" value="`+ tdInfo[4] +`">
	// 					</td>
	// 					<td style="width: 11%; text-align:center">`+ tdInfo[5] +`</td>
						
	// 					<td style="width: 9%; text-align:center">`+ tdInfo[6] +`</td>
	// 					<td style="width: 8%; text-align:center" id="property_id">
	// 								<span class="select">
	// 									<select class="w135 editable-select quick-add set-up formGridPropertySelected" id="property_id" name="property_id"  modal="" type="table" key=""
	// 										 >
	// 											<option value="-1" selected ></option>`
	// 										for (var j = 0; j < properties.length; j++) {
	// 											newRow2 += `<option value='` + properties[j].id + `'`;
	// 											 if(selectedProperty == properties[j].id){ newRow2 += 'selected'};
	// 											 newRow2 +=`>` + properties[j].name + `</option>`;
	// 										}
	// 										newRow2 +=`	</select>
	// 									</span>
	// 							</td>
	// 							<td style="width: 8%; text-align:center" id="account_id" class="formGridAccountTd">
	// 								<span class="select">
	// 									<select class="w135 editable-select quick-add set-up "  id="accountId" name="account_id"  modal="" type="table" key="">
	// 										<option value="-1" selected ></option>`
	// 									for (var a = 0; a < accounts.length; a++) {
	// 										newRow2 += `<option value='` + accounts[a].id + `'`;
	// 										if(selectedAccount == accounts[a].id){ newRow2 += 'selected'};
	// 										newRow2 += `>` + accounts[a].name + `</option>`;
	// 									}
	// 									newRow2 +=`	</select>
	// 									</span>
	// 							</td>
	// 							<td style="width: 8%; text-align:center" id="unit_id">
	// 								<span class="select">
	// 									<select class="w135 editable-select quick-add set-up formGridUnitSelect"  id="unitId" name="unit_id"  modal="" type="table" key="">
	// 										<option value="-1" selected ></option>`
	// 										//if(selectedUnit){ newRow2 += selectedUnit};
	// 										//newRow2 += `</option>
	// 								newRow2 += `</select>
	// 								</span>
	// 							</td>
	// 					<td style="width: 8%; text-align:center"></td>
	// 				</tr>`;
	// $(getTR).after(newRow2);
	// $(getTR).next('tr').find('.editable-select').editableSelect();
	// //sets hidden input for details for all editable select
	// $(getTR).next('tr').find('#property_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][property_id]`);
	// $(getTR).next('tr').find('#account_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][account_id]`);
	// $(getTR).next('tr').find('#unit_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][unit_id]`);
	// $(getTR).next('tr').find('#profile_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][profile_id]`);
	// spot++;
	// JS.loadList('api/getUnitsProperty', selectedProperty , '#unitId',  $(getTR).closest('.allTransactions'));
	// //$("#forceOnChange").trigger("change");//JS.loadList('api/getUnitsProperty', selectedProperty , '#unitId',  $(getTR).closest('.allTransactions'));
	// //$("#forceOnChange").trigger("change");//JS.loadList('api/getAccountsProperty', selectedProperty , '#accountId',  $(getTR).closest('.allTransactions'));
	// console.log('expandCCCharge clicked');
	// detailsDescription();
	// 	//blows away the inputs for the top line when details opens
	// 	// $(getTR).find('#property_id').closest('td').empty();
	// 	// $(getTR).find('#account_id').closest('td').empty();
	// 	// $(getTR).find('#unit_id').closest('td').empty();
	// } 
	// //fills the hidden input with the value
	// function detailsDescription(){
	// 	$('.detailsDescription').keyup(function() {
	// 		$(this).next('input').val($(this).val());
	// 	});
	// }

	//toggles +/- symbol and calls expandCCCharge(td) if there are no details
	// function expand(td)
	// {
	// 	spot = 1;
	// 	$(td).toggleClass("toggle");
	// 	var getId = td.closest('tr').attr('id');
	// 	var id = "." + getId;
	// 	console.log(id);
	// 	$( id ).toggle();
	// 	selectTransaction(td);
	// 	if(!td.closest('tr').next('tr').hasClass("details")){
	// 		expandCCCharge(td);
	// 	}
				
	// }	
	// function removeTR(td){
	// 	//changes symbol if ther are no more details
	// 	if(!$(td).closest('tr').next('tr').hasClass("details") && !td.closest('tr').prev('tr').hasClass("details")){
	// 		console.log("changing symbol");
	// 		$(td).closest('tr').prev('tr').find('td:first').toggleClass("toggle");
	// 	}
	// 	if(!$(td).closest('tr').next('tr').hasClass("details")){
	// 			console.log("adding click event");
	// 			//creates click event on prev tr
	// 			$($(td).closest('tr').prev('tr')).one('click', function(){ 
	// 				expandCCCharge($(this));
	// 				console.log("click event clicked");
	// 			});
	// 	}
	// 	$(td).closest("tr").remove();				
	// }

		// $('body').on('click', '#editMultiple', function (e) { e.preventDefault(); $( "#multipleDisplay" ).css("display", "block"); $( "#multipleDisplay" ).show(); });
			//onChange="JS.loadList('api/getUnitsProperty', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , '#unitId',  $(this).closest('.allTransactions')) ;
			//JS.loadList('api/getAccountsProperty', $(this).closest(\'.select\').find(\'input[type=hidden]\').val() , '#accountId',  $(this).closest('.allTransactions'));" 
			//onselect="editMultipleProperties($(this));"
		// function editMultipleBox(){
		// 	var editMultipleDropdown = `<tr id="multipleDisplay" style="display: none; margin-top: 8px; border: 2px solid black;">
		// 									<td id="property_id">
		// 										<span class="select">
		// 											<select class="w135 editable-select quick-add set-up formGridPropertySelected multiple"  name="property_id"  modal="" type="table" key=""
		// 											onselect="editMultipleProperties($(this));"> 
		// 													<option value="-1" selected ></option>`
		// 												for (var j = 0; j < properties.length; j++) {
		// 													editMultipleDropdown += `<option value='` + properties[j].id + `'>` + properties[j].name + `</option>`;
		// 												}
		// 												editMultipleDropdown +=`	</select>
		// 											</span>
		// 									</td>
		// 									<td style="" id="account_id"  class="formGridAccountTd">
		// 										<span class="select">
		// 											<select class="w135 editable-select quick-add set-up "  id="accountId2" name="account_id"  modal="" type="table" key=""
		// 											onselect="editMultipleAccounts($(this));">
		// 												<option value="-1" selected ></option>`
		// 												for (var a = 0; a < accounts.length; a++) {
		// 												editMultipleDropdown += `<option value='` + accounts[a].id + `'>` + accounts[a].name + `</option>`;
		// 											}
		// 											editMultipleDropdown +=`	</select>
		// 											</span>
		// 									</td>
		// 									<td style="" id="unit_id">
		// 										<span class="select">
		// 											<select class="w135 editable-select quick-add set-up formGridUnitSelect"  id="unitId2" name="unit_id"  modal="" type="table" key=""
		// 											onselect="editMultipleUnits($(this));">
		// 												<option value="-1" selected ></option>
		// 											</select>
		// 										</span>
		// 									</td>
		// 									<td class="remove" onclick="closeMultiplePopup();"><a href="#" class="remove"><i class="icon-x"></i></a></td>
		// 								</tr>`;
		// 	$('#editMultipleDropdowns').append(editMultipleDropdown);
		// 	$('#editMultipleDropdowns').find('.editable-select').editableSelect();          
		// 	//editMultiple($(this));
		// }
		
		
		// $('body').on('click', '#exit', function () {$('body').off( "click", "#editMultipleDropdowns" );});
		// function closeMultiplePopup(){ $('#multipleDisplay').hide();}

		// function editMultipleProperties(model){
		// 	var editPropertySelectedValue = $(model).closest('tr').find('#property_id').closest('td').find('input[type=hidden]').val();
		// 	var editPropertySelectedText = $(model).closest('tr').find('#property_id').closest('td').find('input[type=hidden]').attr("text");
		// 	//var editPropertySelectedText = $(model).closest('tr').find('#property_id').siblings('.es-list').find('li.selected').text();
		// 	//var editPropertySelectedText = $(model).closest('tr').find('#property_id').closest('td').find("li:selected").text();
		// 	var rows = $(model).closest('#ccModal').find('.allAccounts');
		// 		rows.each(function(){
		// 			 var row = $(this);
		// 			 if($(row).closest('label').hasClass('active')){
		// 				inputFields = $(row).closest('tr').find('#property_id').closest('td').find('input');
		// 				$(inputFields[1]).val(editPropertySelectedValue).change();
		// 				$(inputFields[0]).val(editPropertySelectedText).change();
		// 				//console.log(model);
		// 				//$(row).closest('tr').find('#property_id').siblings('.es-list').find('li.selected').data('value');
		// 				//$(row).closest('tr').find('#property_id').find('li.selected').text()
		// 				console.log(editPropertySelectedText);
		// 			 }
					
		// 		})
		// }

		// function editMultipleAccounts(model){
		// 	var editAccountSelectedValue = $(model).closest('tr').find('#account_id').closest('td').find('input[type=hidden]').val();
		// 	var editAccountSelectedText = $(model).closest('tr').find('#account_id').closest('td').find('input[type=hidden]').attr("text");
		// 	var rows = $(model).closest('#ccModal').find('.allAccounts');
		// 		rows.each(function(){
		// 			 var row = $(this);
		// 			 if($(row).closest('label').hasClass('active')){
		// 				inputFields = $(row).closest('tr').find('#account_id').closest('td').find('input');
		// 				$(inputFields[1]).val(editAccountSelectedValue).change();
		// 				$(inputFields[0]).val(editAccountSelectedText).change();
		// 				//console.log(model);
		// 				//$(row).closest('tr').find('#property_id').siblings('.es-list').find('li.selected').data('value');
		// 				//$(row).closest('tr').find('#property_id').find('li.selected').text()
		// 				console.log(editAccountSelectedText);
		// 			 }
					
		// 		})
		// }
		// function editMultipleUnits(model){
		// 	var editUnitSelectedValue = $(model).closest('tr').find('#unit_id').closest('td').find('input[type=hidden]').val();
		// 	var editUnitSelectedText = $(model).closest('tr').find('#unit_id').closest('td').find('input[type=hidden]').attr("text");
		// 	var rows = $(model).closest('#ccModal').find('.allAccounts');
		// 	rows.each(function(){
		// 			 var row = $(this);
		// 			 if($(row).closest('label').hasClass('active')){
		// 				inputFields = $(row).closest('tr').find('#unit_id').closest('td').find('input');
		// 				$(inputFields[1]).val(editUnitSelectedValue).change();
		// 				$(inputFields[0]).val(editUnitSelectedText).change();
		// 				//console.log(model);
		// 				//$(row).closest('tr').find('#property_id').siblings('.es-list').find('li.selected').data('value');
		// 				//$(row).closest('tr').find('#property_id').find('li.selected').text()
		// 				console.log(editUnitSelectedText);
		// 			 }
					
		// 		})
		// }
		// function unitsApi(value, td){
		// 	$(td).empty();
		// 	var unitsDropdown = "";
		// 		unitsDropdown +="<option value='0'>None</option>";
		// 	for (var j = 0; j < units.length; j++) {
		// 		if(units[j].property_id == value){
		// 			unitsDropdown += `<option value='` + units[j].id + `'>` + units[j].name + `</option>`;
		// 		} 	
		// 	}
		// 	unitsDropdown += "</select>";
		// 	//$(td).append(unitsDropdown);
		// 	td.editableSelect('resetSelect',unitsDropdown);
		// 	$(td).find('.editable-select').editableSelect();
		// 		//unitsDropdown +=`	</select>`;
		// }
		// function accountsApi(value, td){
		// 	$(td).empty();
		// 	var accountsDropdown = "";
		// 		accountsDropdown +="<option value='0'>None</option>";
		// 	var propertyAccountsArray = [];
		// 		for (var i = 0; i < propertyAccounts.length; i++) {
		// 			if(propertyAccounts[i].property_id == value){
		// 				propertyAccountsArray.push(propertyAccounts[i].account_id);
		// 				//console.log(propertyAccounts[i]);
		// 				//console.log('second if works!!');
		// 			}
		// 		}	
		// 	for (var j = 0; j < accounts.length; j++) {
		// 		if(accounts[j].all_props == 1 || propertyAccountsArray.includes(accounts[j].id)){
		// 			accountsDropdown += `<option value='` + accounts[j].id + `'>` + accounts[j].name + `</option>`;
		// 			//console.log('first if!!');
		// 		}
		// 		// else{
		// 		// 		if(propertyAccountsArray.includes(accounts[j].id )){
		// 		// 		accountsDropdown += `<option value='` + accounts[j].id + `'>` + accounts[j].name + `</option>`;
		// 		// 		//console.log('third if works!!' + accounts[j].name);
		// 		// }	
		// 		// } 		
		// 	}
			
		// 	accountsDropdown += "</select>";
		// 	//$(td).append(unitsDropdown);
		// 	td.editableSelect('resetSelect',accountsDropdown);
		// 	$(td).find('.editable-select').editableSelect();
		// 		//unitsDropdown +=`	</select>`;
		// }

		// function getUnitsProperty(value, td){
		// 	$(td).empty();
		// 	var propertyunits = "";
		// 	propertyunits +="<option value='0'>None</option>";
		// 		for (var a = 0; a < units.length; a++) {
		// 			if(units[a].property_id == value){
		// 				propertyunits += `<option value='` + units[a].id + `'>` + units[a].name + `</option>`;
		// 			}
		// 		}
		// 	//$(td).append(propertyunits);
		// 	td.editableSelect('resetSelect',propertyunits);
		// 	$('body').find('.editable-select').editableSelect();
		// 	console.log('new api');				
		// }

		// function getAccountsProperty(value, td){
		// 	$(td).empty();
		// 	var propertyAccounts2 = "";
		// 	propertyAccounts2 +="<option value='0'>None</option>";
		// 		for (var a = 0; a < accounts.length; a++) {
		// 			if(accounts[a].all_props == 1 && accounts[a].active == 1){
		// 				propertyAccounts2 += `<option value='` + accounts[a].id + `'>` + accounts[a].name + `</option>`;
		// 			}
		// 		}
		// 	//$(td).append(propertyunits);
		// 	td.editableSelect('resetSelect',propertyAccounts2);
		// 	$('body').find('.editable-select').editableSelect();
		// 	console.log('new api');				
		// }


  </script>