<div class="modal fade propertyTax-modal" id="propertyTaxModal" tabindex="-1" role="dialog"  type="propertyTax-grid" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="  padding: 30px;">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
									<?php if($error){echo "<h1>$error</h1>";} ?>

				  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="propertyTax-grid-entry" type="propertyTax">
			<div class="t_input_wrapper">
				<header class="modal-h">
					<h2>Property Tax grid entry</h2>
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
				
        <div class ="has-table-c">
            <table class="table-c dc d da  billTable mobile-hide dataTable no-footer" style="display: table;  margin:0 auto;  ">
                <thead id="propertyTax_head" class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden; border-radius: 6px; margin-bottom: 6px;">							
					</thead>
					<tbody id="propertyTax_body" class="dataTables_scrollBody testTable" style=" display: block;height: calc(100vh - 500px);overflow: auto; box-shadow: 0 0px 0px; border-width: 0px;">
					</tbody>
					<style type="text/css" onload="getPropertyTax_body($(this).closest('.modal'))"></style>
				</table>
				</div>
				<p class="strong scheme-b m20 size-a overlay-a" id="selectedCharges">0 property taxes selected</p>
				<footer class="a">
					<p class="m0" style="margin-right: 5%;">
						<button type="submit" after="mclose" class="grid">Submit</button>
					</p>
				</footer>
			
		</div>
                  </form>               
            </div>
        </div>
    </div>
</div>
<script>

	 var propertyTaxes= <?php echo $jPropertyTaxes; ?>;
	 var accounts= <?php echo $accounts; ?>;

var allDates = document.getElementById('alldates');
	 //var firstAccount = < ?php echo $ofxImports; ?>;
console.log('cc charges00');
	function getPropertyTax_body(modal){
		getHead($(modal).find('#propertyTax_head'), propertyTaxes);
		getBody($(modal).find('#propertyTax_body'), propertyTaxes);
		lastPaidDateBackground(modal)
	}

	function getHead(body, propertyTaxes){
		$(body).empty();
		var newRow = "";
		if(propertyTaxes){
								
					newRow += `<tr role="row" style="display: table; width: 100%; table-layout: fixed;">
								<th style="width: 4% !important;">
								<input type="hidden" id="">
									 
								</th>
								<th style="width: 4%;" class="check-a"><label for="selectAllCheckboxes" class = "checkbox"><input type="checkbox" id="selectAllCheckboxes" class="selectAllCheckboxes" name="fbm2">
								<div class="input"></div></label></th>`;
                            $.each(propertyTaxes[0], function(key, value) {
								if(key != "id"){
									newRow +=	`<th  style="width: 9% ">` + capitalizeFirstLetter(key) +`</th>`;
								}
                            });
                            newRow +=	`</tr>`; 
			}else{
			}
            body.append(newRow);
	}
//populates all transactions
	function getBody(body, propertyTaxes){
		$(body).empty();
		selected = 0;
		var checkboxSpot = 0;
		formsJs.selectedCharges(0, "property taxes", $(body).closest('.modal'));
		var newRow = "";
		if(propertyTaxes){
								for (var i = 0; i < propertyTaxes.length; i++) {
					newRow += `<tr id="`+ propertyTaxes[i].id +`" data-id="`+ propertyTaxes[i].id +`" data-type="propertyTaxes" role="row" style="display: table; width: 100%; table-layout: fixed;">
								<td style="width: 4% !important;">
								<input type="hidden" name="" value="`+ propertyTaxes[i].id +`" id="id">
									 
								</td>
								<td style="width: 4%;" class="check-a">
					 				<label for="`+ checkboxSpot +`" class="checkbox">
					 					<input type="checkbox" id="`+ checkboxSpot +`" name="`+ checkboxSpot +`" class="hidden allAccounts" aria-hidden="true"
					 					onchange="formsJs.Checkbox($(this),`+ propertyTaxes[i].id +`);">
										 <div class="input"></div>
					 				</label>
					 			</td>`;
                            $.each(propertyTaxes[i], function(key, value) {
								if(key != "id"){
									if(key == "amount"){
										newRow +=	`<td  style="width: 9% "><input id="amount" name="" class="selectTransactionClass" value="` + value +`"></td>`;
									}else if(key == "start date"){
										newRow +=	`<td style="width: 9%;"><input style="color: lightgrey; text-align: center;" data-toggle="datepicker" id="start_date" class="start_date leaveEmpty" name="" value="" placeholder="` + value +`"></td>`;
									}else if(key == "pay date"){
										newRow +=	`<td style="width: 9%;"><input style="color: lightgrey; text-align: center;" data-toggle="datepicker" id="last_pay_date" class="last_pay_date leaveEmpty" name="" value="" placeholder="Last paid on ` + value +`"></td>`;
									}else if(key == "Payment acct"){
										newRow += `
										<td style="text-align:center; width: 5%;" id="" class="formGridAccountTd">
                                                 <span class="select">
                                                     <select stype="account" default="` + (accounts[i].id ? accounts[i].id: '') +`" class=" fastEditableSelect quick-add set-up "  id="payment_acct" name=""  modal="" type="table" key="">
                                                        <option value="-1" selected ></option>	</select>
                                                 </span>
                                            </td>`
									}else{
										newRow +=	`<td  style="width: 9% ">` + value +`</td>`;
									}
								}
                            });
                            newRow +=	`</tr>`;
							checkboxSpot++;
				} 
			}else{
				newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No property taxes.</td></tr></div>`;
			}
            body.append(newRow);
			JS.checkboxes(body);
			JS.datePickerInit(body);
			$(body).find('.fastEditableSelect').fastSelect();
	}
		//formsJs.selectTransactionClass();
		function lastPaidDateBackground(modal){
			$(modal).find('[data-toggle="datepicker"]').on('change', function(){
				$(this).css("color", "");
			})
		}
		function capitalizeFirstLetter(string) {
			return string.charAt(0).toUpperCase() + string.slice(1);
		}

	

		




  </script>