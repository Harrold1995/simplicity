<div class="modal fade utilities-modal <?php echo $edit ?>" id="utilitiesModal" tabindex="-1" role="dialog"  type="utilities" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style=" width: 112%;  padding: 30px;">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
				  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="utilities-grid-entry" type="utilities">
			<div class="t_input_wrapper">
				<header class="modal-h">
					<h2>Utilities Grid</h2>
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
					<p class="submit"><button type="button" id="exit">Exit</button></p>
				</header>
				<ul class="list-choose a">
					<li style="margin-right: 0 !important">
						<ul>
							<li class="select" style="width:290px !important">
							<label for="property">Property</label>
							<select class="editable-select quick-add set-up" id="property" name="property" onchange="getUtilities($(this).closest('#utilitiesModal').find('#utilities_body'),$(this).closest('.select').find('input[type=hidden]').val())">
								 <option value="-1"  ></option>
									<?php
									$selected = json_decode($properties);
										foreach ($properties as $property) {
											// echo '<option value="-1" selected >' . "Select Apples" . '</option>';
											echo '<option value="' . $property->id . '" >' . $property->name . '</option>';
											//' . (isset($properties) && $selected[0]->id == $property->id  ? 'selected' : '') . '
									} ?>   
                                </select>
							</li>
						</ul>
					</li>
					<li>
						<ul>
							<li>
								<label for="inbetweenDates"><input type="radio" id="inbetweenDates" name="inbetweenDates"><div class="input"></div>From</label>
								<span><input type="text" data-toggle="datepicker" id="fromDate"  class="leaveEmpty" ></span>
								<label for="to" class="">To</label>
								<span><input type="text" data-toggle="datepicker" id="toDate" class="leaveEmpty"></span>
							</li>
							<li><label for="alldates"><input type="radio" id="alldates" name="inbetweenDates" checked>All dates<div class="input"></div></label></li>
						</ul>
					</li>
					<li>
						<ul>
							<li>
								<label for="chooseVendor"><input type="radio" id="chooseVendor" name="chooseVendor"><div class="input"></div> Vendor</label>
								<select stype="vendors" id="profile_id" name="profile_id" class="fastEditableSelect">
								<!-- <option value="-1" selected >Select Vendor</option>
								< ?php foreach($names as $name): 
								if($name->profile_type_id == 1){
									echo '<option value="' . $name->id . '" >' . $name->vendor . '</option>';								  
								}
									endforeach; ?> -->
                                </select>
							</li>
							<li><label for="allVendors"><input type="radio" id="allVendors" name="chooseVendor" checked> All vendors<div class="input"></div></label></li>
						</ul>
					</li>
				</ul>
		<div class ="has-table-c" style=" display:flex;">
		<div style="overflow:scroll;">
            <table class="table-c dc d da  billTable mobile-hide  no-footer" style="display: table;  margin-left:0; min-width: 1300px; width: 100%;"  id="sortable" >
                <thead id="thItems" class="dataTables_scrollHead" style="display: table; table-layout: fixed; border-radius: 6px; margin-bottom: 6px; width: 100%; overflow: hidden;">
						<tr>
							<!-- <th style="width: 3%;"></th> -->
							<th  class="text-center link-icon"><a href="#"><i id="addUtilityButton" class="icon-plus-circle addUtilityButton table-button" style="font-size: 24px;"></i> <span>Add</span></a></th>
							<th style="" class="check-a"><label for="selectAllCheckboxes" class = "checkbox"><input type="checkbox" id="selectAllCheckboxes" class="selectAllCheckboxes" name="fbm2"><div class="input"></div></label></th>
							<th style="">Vendor</th>
							<th style="">Property</th>
							<th style="">Unit </th>
							<th style="">Description </th>
							<th style="">Account</th>
							<th style="">Utility Type</th>
							<th style="">Last Payment Date</th>							
							<th style="">Direct Payment</th>
							<th style="">Expense Acct</th>
							<th style="">Billable</th>
							<th style="">Date</th>
							<th style="">Amount</th>
							<th style="">Usage</th>
							<th style="">Estimate</th>
							<th style="">Memo</th>
						</tr>
					</thead>
					<tbody id="utilities_body" class="dataTables_scrollBody testTable" style=" display: block;height: calc(100vh - 500px); box-shadow: 0 0px 0px; border-width: 0px;">
					</tbody>
					<style type="text/css" onload="getUtilitiesBody($(this).closest('.modal'))"></style>
				</table>
				</div>
				<div id="utilityNotesDiv" style="background-color:#f6f8f9; padding: 15px; border-radius: 15px; display: none; height: 75%; width: 25%; right: -100px; position: fixed; z-index: 3000; height: calc(100vh - 300px); overflow: hidden;">
					<span class="buttons" style="right: 0%; position: absolute; top: 1%;"><span class="closeCourtDiv" style="padding: 8px 20px;cursor: pointer;">X</span></span>
					<h3 style="text-align: center; ">Notes</h3>
					<div id="noteForm" style="padding-bottom: 10px;"></div>
					<div id="utilityNotes" style="height: 100%; color: #919090;font-size: 11px; margin: 10px; overflow: auto; margin: -12px;"></div> </div>
				</div>
				<p class="strong scheme-b m20 size-a overlay-a" id="selectedCharges">0 bills selected</p>
				<footer class="a">
					<p class="m0">
						<button type="submit" class="grid">Record bills</button>
						<button type="submit" id="deleteUtilities">Delete</button>
					</p>
				</footer>
			
		</div>
                  </form>               
            </div>
        </div>
    </div>
</div>

<!--script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/custom2.js"></script-->
<script>
	//$('body').off( "click", ".addRow" );
	 //var propertyId= 1;//< ?php echo $propertiesId; ?>;
	 var properties= <?php echo $jProperties; ?>;
     var accounts = <?php echo $jAccounts; ?>;
	 var units = <?php echo $jUnits; ?>;
	 var names = <?php echo $jNames; ?>;
	 var propertyAccounts = <?php echo $jPropertyAccounts; ?>; 
	 var userId = <?php echo $this->ion_auth->get_user_id(); ?>;
	 //var estimateSpot = 0;
	 //var checkboxSpot = 0;

//triggers click on first account
//$(document).ready(function () {
	function getUtilitiesBody(modal){
		console.log("get utiliteis");
	 	getUtilities($(modal).find('#utilities_body'));
	}
//});

	// $(document).ready(function () {
	// 		$('#sortable').DataTable( {
	// 		"order": []
	// 	} );
	// });

// var allDates = document.getElementById('alldates');
// var inbetweenDates = document.getElementById('inbetweenDates');
	 
console.log('cc charges00');

//populates all transactions
	// function getAjaxAccount(body, utilities){
	// 	$(body).empty();
	// 	selected = 0;
	// 	//selectedCharges();
	// 	formsJs.selectedCharges(0, "Bills", $(body).closest('.modal'));
	// 	var newRow = "";
	// 	if(utilities){
	// 			// var checkboxSpot = 0;
	// 					$(utilities).each(function(){
							
	// 						var that = this;
	// 						newRow += `<tr id="`+ that.id +`" role="row" class="allTransactions">
	// 						<input type="hidden" name="" value="`+ that.id +`" id="id">
	// 							<td style="width: 3% !important;" class="link">
	// 				 				<a href="#" class="selectTransactionClass" onclick="expand($(this).closest('td'))"><span class="hidden">More</span></a>
	// 								<div class="shadow" style="width: 1337px; height: 36px;"></div>
	// 				 			</td>
	// 							<td style="width: 4%;" class="check-a">
	// 				 				<label for="`+ checkboxSpot +`" class="checkbox">
	// 				 					<input type="checkbox" id="`+ checkboxSpot +`" name="`+ checkboxSpot +`" class="hidden allAccounts" aria-hidden="true"
	// 				 					onchange="formsJs.Checkbox($(this),`+ that.id +`);">
	// 									 <div class="input"></div>
	// 				 				</label>
	// 				 			</td>`;
	// 						$.each(that, function( index, value ) {
	// 							if(index != "id"){
	// 								if(index == "unit_id" || index == "property_id"|| index == "account_id"|| index == "profile_id"){
	// 									newRow += whichTd(index, value, that.id)
	// 								}else{
	// 									if(index == "direct_payment" || index == "billable"){
	// 										newRow += `<td id="` + index +`"  class="check-a" style="width: 5%;">`;
	// 										newRow += `<label for="` +index + `_` + checkboxSpot +`" class="checkbox`;
	// 											if(value == 1)newRow +=  ' active';
	// 										newRow += `"><input type="hidden" name="" value="0" /><input type="checkbox"  value="1" `;
	// 											if(value == 1)newRow +=' checked';
	// 										newRow +=  ` id="`+index + `_` + checkboxSpot +`" name=""  class="hidden" aria-hidden="true"><div class="input"></div></label></td>`;	
	// 									}else{
	// 										newRow += `<td id="` + index +`" style="width: 5%;">` + value ;
	// 										newRow += `<input type="hidden"  name="" value="`+ value +`"></td>`;
	// 									}
	// 								}
									
	// 							}
	// 							//console.log(index);
	// 							//console.log(value);
	// 						});
	// 						newRow += `
	// 							<td id="last_paid_date" style="width: 5%;">
	// 								<input data-toggle="datepicker" id="last_paid_date" class="selectTransactionClass" type="text"  name="" value="` + new Date() + `">
	// 							</td>
	// 							<td id="amount" style="width: 5%;">
	// 							<input type="text" class="selectTransactionClass"  name="" value="" placeholder="Enter Amount">			
	// 							</td>
	// 							<td id="util_usage" style="width: 5%;">
	// 								<input type="text" class="selectTransactionClass"  name="" value="" placeholder="Enter Usage">									
	// 							</td>
	// 							<td id="estimate" class="check-a" style="width: 5%;">
	// 								<label for="estimate` + checkboxSpot +`" class="checkbox">
	// 								<input type="hidden" name="" value="0" /><input type="checkbox"  value="1"  id="estimate` + checkboxSpot +`" name=""  class="hidden" aria-hidden="true">
	// 								<div class="input"></div></label></td></tr>`;
	// 						checkboxSpot++;
	// 						//estimateSpot++;
							
	// 					});
					
	// 		 	} 
	// 		// }else{
	// 		// 	newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No Transactions for this account.</td></tr></div>`;
	// 		// 	//<td style="width:30%; text-align:center"> No Transactions for this account.</td><td style="width:30%"></td></tr>`;
	// 		// }
	// 		body.append(newRow);
	// 		$(body).find('.editable-select').editableSelect();
	// 		JS.checkboxes();
	// 		JS.datePickerInit(body);
	// }
//gets the transactions for an account
//var propertyutility = 1;
	function getUtilities(body, propertyutility = null){
		console.log("get utiliteis2");
		$("#fromDate").text('');
		$.ajax({  
			type: 'GET',
			url: JS.baseUrl+'properties/getPropertyUtilitiesForBill/', 
			data: { id: propertyutility },
			dataType: 'json',
			success: function(response) {
				formsJs.getAjaxAccount(body, response, 'utilities');
				//dateSearch(response, body);
				//allDatesButton(response, body);
				console.log("get utiliteis3");
			}
		});
	}
	// //from and to date eventlistner
	// function dateSearch(account, body){
	// 	inbetweenDates.onclick = function() {
	// 		checkDates(account, body);
 	// 	}
	// 	document.getElementById("fromDate").oninput = function() {
	// 		checkDates(account, body);
	// 		$("#lcab").trigger("click");
	// 		console.log('fromDate');
	// 	};
	// 	document.getElementById("toDate").oninput = function() {
	// 		checkDates(account);
	// 		$("#lcab").trigger("click");
	// 		console.log("toDate");
	// 	};
	// }

	// $("input[name='inbetweenDates']").change(function(){
	// 	console.log('testing');
	// 	console.log($(this).attr('id'));
	// });
	// //filters transactions based on to and from dates
	// function checkDates(account, body){
	// 	var dateSearchAccount = [];
	// 		var fromDate = document.getElementById("fromDate");
	// 		var toDate = document.getElementById("toDate");
	// 		for (var i = 0; i < account.length; i++) {
	// 			var oldDate = new Date(account[i].old_last_paid_date);
	// 			var newFromDate = new Date(fromDate.value);
	// 			var newToDate = new Date(toDate.value);
	// 				if(oldDate >= newFromDate && oldDate <= newToDate){
	// 					//console.log('greater and less than')
	// 					dateSearchAccount.push(account[i]);
	// 				}
	// 		}
	// 		console.log(dateSearchAccount);
	// 		//getAjaxAccount($('#cc_grid_charge_body'), dateSearchAccount);
	// 		//formsJs.getAjaxAccount(body, response, 'utilities');
	// 		formsJs.getAjaxAccount(body, dateSearchAccount, 'utilities');
	// }
	// function allDatesButton(account, body){
	// 	allDates.onclick = function() {
	// 		//getAjaxAccount(body, account);
	// 		// $("#fromDate").text('');
	// 		// $("#toDate").empty();
	// 		formsJs.getAjaxAccount(body, account, 'utilities');
	// 		console.log('all dates clicked');
 	// 	}
	// }
	//used for details
	//var spot;
	//var billableSpot = 0;
	// //makes new rows for details
	// function expandCCCharge(e)
	// {
	// var allIdNames2 = [];		
	// var tdInfo = [];
	// var thisTR = e.closest('tr');
	//  if(e.closest('tr').hasClass('allTransactions')){
	// 		var getTR = e.closest('tr');
	// 	 console.log('yes class');
	// 	}else{
	// 		var getTR = $(e.closest('tr')).prevAll( "tr.allTransactions:first");
	// 		console.log('no class');
	// 	}
 
	// var clonedRow = $(getTR).clone();
	// var newRow2 = "";
	// 	 newRow2 = `<tr id="`+ getTR.attr('id') +`" class="details `+ getTR.attr('id') +`" role="row">
	// 									<td style="width: 3% !important;" class="link"></td>
	 									
	// 									<td class="remove" onclick="formsJs.removeTR($(this));"><a href="#" class="remove"><i class="icon-x"></i> <span>Remove</span></a></td>`;
	//  var tdNum = 0;									
	// $(getTR).find('td').each (function( column, td) {
	// 		if($(td).attr('id')){allIdNames2.push($(td).attr('id') );}
		 	
	// 	if(tdNum > 1){
	// 		//for editable select
	// 		if($(td).find('input').hasClass('editable-select')){
	// 			var esValue = $(td).closest('td').find('input[type=hidden]').val();
	// 			tdInfo.push(esValue);
	// 			newRow2 += UtilitywhichTd($(td).attr('id'), esValue, getTR.attr('id') );
	// 			// newRow2 += `<td>` + esValue ;
	// 			// newRow2 += `<input type="hidden"  name="details[`+ getTR.attr('id') +`][`+ spot +`][description]" value="`+ description +`"></td>`;
	// 		}else{
	// 			//for last paid date
	// 			if($(td).attr('id') == 'last_paid_date'){
	// 				//console.log($(td).find('input[type=hidden]').val());
	// 				var newDate = new Date($(td).find('input[type=hidden]').val());
	// 				newDate = (newDate.getMonth() + 1) + '/' + newDate.getDate() + '/' +  newDate.getFullYear();
	// 				//console.log(newDate);
	// 				newRow2 += `<td><input type="hidden">`+ newDate + `</td>`;
	// 			}else{
	// 					var oldTd = $(td).clone();
	// 					//console.log($(td).attr('id') );
	// 					//console.log($(oldTd).html());
	// 					if($(oldTd).html()){
	// 						newRow2 += `<td `;
	// 						if($(td).attr('id') == 'billable' || $(td).attr('id') == 'estimate'){
	// 							newRow2 += `<td class="check-a">
	// 								<label for="D` + $(td).attr('id') + checkboxSpot +`" class="checkbox">
	// 								<input type="hidden" name="" value="0" /><input type="checkbox"  value="1"  id="D` + $(td).attr('id') + checkboxSpot +`" name=""  class="hidden" aria-hidden="true">
	// 								<div class="input"></div></label></td>`;
	// 								//billableSpot++;
	// 						}else{
	// 							//used for finding this td to add class to input for adding row
	// 							if($(td).attr('id') == 'amount'){newRow2 += `class="forAdding"`}
	// 							newRow2 +=`>` + $(oldTd).html() + `</td>`;
	// 						}
	// 					}else{
	// 						newRow2 += `<td><input id="`+ $(td).attr('id') +`" type="hidden"></td>`;
	// 					}
	// 			}
	// 		}
	// 	}
	// 	tdNum++;
	// 	checkboxSpot++;
	// });
	// newRow2 += `</tr>`;
	// // var selectedProperty = getTR.find('#property_id').closest('td').find('input[type=hidden]').val();
	// // var selectedAccount = getTR.find('#accountId').closest('td').find('input[type=hidden]').val();
	// // var selectedUnit = getTR.find('#unitId').val(); 
	// //console.log(tdInfo[7]);
	// var total = tdInfo[4];
	// //console.log(selectedProperty);

	// $(thisTR).after(newRow2);
	// $(thisTR).next('tr').find('.forAdding').find('input').addClass('addRow');
	// $('body').find('.editable-select').editableSelect();
	// //formsJs.setUtilitiesName(getTR, getTR.attr('id'), allIdNames2);
	// //JS.datePickerInit();
	// //addDetailRow();
	// //sets hidden input for details for all editable select
	// // $(getTR).next('tr').find('#property_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][property_id]`);
	// // $(getTR).next('tr').find('#account_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][account_id]`);
	// // $(getTR).next('tr').find('#unit_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][unit_id]`);
	// // $(getTR).next('tr').find('#profile_id').closest('td').find('input[type=hidden]').attr('name', `details[` + getTR.attr('id') + `][`+ spot +`][profile_id]`);
	// spot++;
	// //JS.loadList('api/getUnitsProperty', selectedProperty , '#unitId',  $(getTR).closest('.allTransactions'));
	
	// console.log('expandCCCharge clicked');
	// detailsDescription();
	// } 
	//fills the hidden input with the value
	// function detailsDescription(){
	// 	$('.detailsDescription').keyup(function() {
	// 		$(this).next('input').val($(this).val());
	// 	});
	// }

	// //toggles +/- symbol and calls expandCCCharge(td) if there are no details
	// function expand(td)
	// {
	// 	spot = 1;
	// 	$(td).toggleClass("toggle");
	// 	var getId = td.closest('tr').attr('id');
	// 	var id = "." + getId;
	// 	console.log(id);
	// 	$( id ).toggle();
	// 	formsJs.selectTransaction(td);
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
	//creates/deletes hidden input name based on checkbox
	//new: using dynamic function to loop through each td id and setting/unsetting the name attr
	// function setAccountName(checkbox, id){
	//  			var spot = 0;
	// 			var allIdNames = [];
	// 	        if($(checkbox).parent('label').hasClass('active')){
	// 				console.log("setting account name");
	// 				//sets the hidden input for id
	// 				var idName = $(checkbox).closest('tr').find('input:hidden:first').attr('id');
	// 				$(checkbox).closest('tr').find('input:hidden:first').attr('name', `row[` + id + `][transactions][` + spot + `][` + idName + `]`);
	// 				//sets the hidden input for all other tds
	// 				$(checkbox).closest('tr').find('td').each (function( column, td) {
	// 					var name = $(td).attr('id');
	// 					if(name){
	// 						allIdNames.push(name);
	// 						if($( td).children().hasClass("editable-select")){
	// 							$(checkbox).closest('tr').find('#' + name).closest('td').find('input[type=hidden]').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
	// 						//console.log(name + "this is an editable-select");
	// 					}else{
	// 						//for direct payment
	// 						if(name == 'direct_payment' || name == 'last_paid_date' || name == 'billable'){
	// 							if(name == 'direct_payment'){
	// 								$(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][utilities][` + name + `]`);
	// 								$(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][utilities][` + name + `]`);
	// 							}else{
	// 								if(name == 'billable'){
	// 									$(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
	// 									$(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
	// 								}else{$(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][utilities][` + name + `]`);}}
								
	// 						}else{
	// 								if(name == 'util_usage' || name == 'estimate'){
	// 									$(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][transactions][` + spot + `][utility_trans][` + name + `]`);
	// 								}else{
	// 									//if they have a hidden input
	// 									if($(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').length > 0){
	// 										$(checkbox).closest('tr').find('#' + name).find('input[type=hidden]').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
	// 									}else{
	// 										//if they don't have a hidden input
	// 										$(checkbox).closest('tr').find('#' + name).find('input').attr('name', `row[` + id + `][transactions][` + spot + `][` + name + `]`);
	// 									}
	// 								}
	// 							}
								
	// 						}
	// 					}
	// 				});	
	// 				setDetailsName($(checkbox).closest('tr'), id, allIdNames);				
	// 			}else{
	// 				//$(checkbox).closest('tr').find('input[type=hidden]').removeAttr('name');
	// 				$(checkbox).closest('tr').find('input').removeAttr('name');
	// 				console.log("deleting account name");
	// 				unSetDetailsName($(checkbox).closest('tr'));
	// 			}
	// }
		//for total charges selected
		// var selected = 0;
		// //checks and unchecks checkbox, also calls function to set/unset hidden input
		// function Checkbox(checkbox, id){
		// 	//$(checkbox).closest('label').toggleClass('active'); 
		// 	setAccountName(checkbox,id);
		// 	//update total charges selected when checkbox is clicked
		// 	if($(checkbox).closest('label').hasClass('active')){
		// 		selected++;
		// 		selectedCharges();
		// 	}else{
		// 		selected--;
		// 		selectedCharges();
		// 	}
		// }
		// function setDetailsName(tr, id, idnames){
		// 	spot = 1;
		// 	console.log(idnames);
		// 	$(tr).nextUntil("tr.allTransactions").each(function(index, value) { 
		// 		var arraySpot = 0;
		// 		   $(value).each(function(td, value2){
		// 				$(value2).find('td').each(function(td, value3){
		// 					if($(value3).find('input').length > 0){
		// 						if(idnames[arraySpot] == "direct_payment" || idnames[arraySpot] == "last_paid_date"){
		// 							$(value3).find('input').removeAttr('name');
		// 							//$(value3).find('input').attr('name', `row[`+id+`][utilities][`+idnames[arraySpot]+`]`);
		// 						}else{
		// 							if(idnames[arraySpot] == "billable"){
		// 								//$(value3).find('label').attr('for', `Dbillable_` + billableSpot);
		// 								//$(value3).find('input:not(:hidden)').attr('id', `Dbillable_` + billableSpot);
		// 								$(value3).find('input').attr('name', `row[`+id+`][transactions][`+spot+`][`+idnames[arraySpot]+`]`);
		// 								billableSpot++;
		// 							}else{
		// 								if(idnames[arraySpot] == "util_usage" ||idnames[arraySpot] == "estimate"){
		// 									$(value3).find('input').attr('name', `row[`+id+`][transactions][`+spot+`][utility_trans][`+idnames[arraySpot]+`]`);
		// 								}else{
		// 									$(value3).find('input').attr('name', `row[`+id+`][transactions][`+spot+`][`+idnames[arraySpot]+`]`);
		// 								}
		// 							}
								
		// 						}
		// 						arraySpot++;
		// 					}
		// 				});
		// 			  	console.log(value2);
		// 		   });
		// 		   spot++;
		// 		});
		// 		JS.checkboxes();
		// }
		// function unSetDetailsName(tr){
		// 	$(tr).nextUntil("tr.allTransactions").each(function(index, value) { 
		// 		   $(value).find('input').removeAttr('name');
		// 		});
		// }
		// function addDetailRow(){
		// 	$('body').on('click', '.addRow', function(){
		// 		expandCCCharge($(this));
		// 		$(this).removeClass("addRow");
		// 	});
		// }
		//addDetailRow();
		//formsJs.selectTransactionClass();

		//  function whichTd(tdId, value, trId){

        //     switch (tdId) {
        //         case 'property_id' :
        //             var propertyNewRow = '';
        //             propertyNewRow += `<td id="property_id" style=" text-align:center; width: 5%;">`
                                       
		// 									for (var a = 0; a < properties.length; a++) {
		// 										//unitNewRow += `<option value='` + units[a].id + `'`;
		// 											if(value == properties[a].id){ 
		// 												propertyNewRow +=   properties[a].name ;
		// 												propertyNewRow += `<input type="hidden"  name="" value="`+ value +`">`;
		// 											}
		// 									}
                                              
        //                             `</td>`;
        //             return propertyNewRow;
        //         case 'account_id':
        //                 var accountNewRow = '';
        //                 accountNewRow +=  `<td style="text-align:center; width: 5%;" id="account_id" class="formGridAccountTd">
        //                 <span class="select">
        //                     <select class=" editable-select quick-add set-up "  id="account_id" name=""  modal="" type="table" key="">
        //                         <option value="-1" selected ></option>`
        //                     for (var a = 0; a < accounts.length; a++) {
        //                         accountNewRow += `<option value='` + accounts[a].id + `'`;
        //                         if(value == accounts[a].id){ accountNewRow += 'selected'};
        //                         accountNewRow += `>` + accounts[a].name + `</option>`;
        //                     }
        //                     accountNewRow +=`	</select>
        //                     </span>
        //             </td>`;
        //             return accountNewRow;
        //         case 'unit_id':
        //                 var unitNewRow = '';
        //                 unitNewRow +=  `<td id="unit_id" style="text-align:center; width: 5%;">`;
        //                 // <span class="select">
        //                 //     <select class="w135 editable-select quick-add set-up formGridUnitSelect"  id="unit_id" name="transactions[`+trId+`][unit_id]"  modal="" type="table" key="">
        //                 //         <option value="-1" selected ></option>`
        //                     for (var a = 0; a < units.length; a++) {
        //                         //unitNewRow += `<option value='` + units[a].id + `'`;
		// 							if(value == units[a].id){ 
		// 								unitNewRow +=   units[a].name ;
		// 								unitNewRow += `<input type="hidden"  name="" value="`+ value +`">`;
		// 							}
        //                     }
        //                     //unitNewRow +=`	</select>`
        //                     // </span>
        //            		unitNewRow +=`</td>`;
        //             return unitNewRow;
        //         case 'class_id':
        //             var classNewRow = '<td>' + value +'</td>';
        //             var profileNewRow = '';
        //             profileNewRow +=  `<td style=" text-align:center; width: 5%;">
        //                                 <span class="select">
        //                                     <select class=" editable-select quick-add set-up "  id="class_id" name="transactions[`+trId+`][class_id]"  modal="" type="table" key="">
        //                                         <option value="-1" selected ></option>`
        //                                     for (var a = 0; a < classes.length; a++) {
        //                                         profileNewRow += `<option value='` + classes[a].id + `'`;
        //                                         if(value == classes[a].id){ profileNewRow += 'selected'};
        //                                         profileNewRow += `>` + classes[a].description + `</option>`;
        //                                     }
        //                                     profileNewRow +=`	</select>
        //                                     </span>
        //                             </td>`;
        //             return profileNewRow;
        //         case 'profile_id':
        //             var profileNewRow = '';
        //             profileNewRow +=  `<td id="profile_id" style=" text-align:center; width: 5%;">`;
		// 							for (var a = 0; a < names.length; a++) {
		// 										if(value == names[a].id){ 
		// 											profileNewRow +=   names[a].name ;
		// 											profileNewRow += `<input type="hidden"  name="" value="`+ value +`">`;
		// 										}
		// 								}
        //                            profileNewRow += `</td>`;
        //             return profileNewRow;

        //     }
            
        // }


				// $(" td.link").css({"width": "40px !important", "max-width": "40px !important", "font-size": "11px"});
				// $("td.link a").css({"display": "block", "position": "relative", "width": "33.23px", "font-size": "11px", "text-decoration": "none"});
				//  $("td.link a:before").css({"content": "\e909", "max-position": "relative", "top": "2px", "width": "auto", "margin": "0", "line-height": "11px"});
				//  $("td.link.toggle a:before").css("content", "\e914");

				//  $(this).find('.shadow').each(function () {
				// 					$(this).css('width', $(this).parents('tr').outerWidth());
				// 					var iiF = false;
				// 					var tn = $(this).parents('tr').nextAll().filter(function (k, v) {
				// 						if (iiF === true) return false;
				// 						if (!$(v).hasClass('details')) {
				// 							iiF = true;
				// 							return false;
				// 						}
				// 						return true;
				// 					});
				// 					$(this).css('height', ($(this).parents('tr').outerHeight() * (tn.length + 1)) - 4);
				// 				});

  </script>