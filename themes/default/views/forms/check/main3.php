<div class="modal flexmodal fade check-modal formGrid" id="checkModal" tabindex="-1" role="dialog" main-id=<?= isset($checks) && isset($checks->id) ? $checks->id : '-1' ?> type="check" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
        <div id="root" class="no-print">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				  <form action="<?php echo $target; ?>" method="post" autocomplete="off" type="4">
				  <?php if(isset($headerTransaction) && isset($headerTransaction->id)){
									echo '<input type="hidden" name="headerTransaction[id]" value="' . $headerTransaction->id . '"/>';
									echo '<input type="hidden" id="transNum"  value="' . $header->id . '"/>';
                                    } ?>

                  <header class="modal-h">
					<h2 class="text-uppercase">Check</h2>

                            <label for="bank"><h5>Bank Balance : $<span id="bankBalance"><?php echo $balance ? $balance : '.00' ?> </span> </h5></label>




					<nav >
						<ul>
							<li><a href="!#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
							<li><a href="!#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
							<li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li><a href="!#"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
							<li><a href="!#"><i class="icon-brain"></i> <span>Brain</span></a></li>
							<li><a href="1#"><i class="icon-documents"></i> <span>Copy</span></a></li>
							<li><a href="!#"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
							<li id="checkButtonClicked"><a class="" href="!#"><i class="icon-print" onclick ="checkprintInit(this, event)"></i> <span>Print</span></a></li>
						</ul>
					</nav>
					
				</header>
                <section class="a " style="padding-right:50px;">
					<div class="double d m20">
						<div style="margin-left: 30px;">
						    <p>
							<?php echo $checks ?>
								<label for="bank">Bank: </label>
								<span class="select"> 
								<select id="account_id" name="headerTransaction[account_id]" class="editable-select" 
								onchange="JS.loadList('api/getBankBalance', $(this).closest('.select').find('input[type=hidden]').val() , '#bankBalance',  $(this).closest('#checkModal')) ;">
								<option value="-1" selected > Select Bank </option>
								<?php foreach($banks as $bank): ?>
										<option value="<?= $bank->id ?>" <?php echo isset($headerTransaction) && $headerTransaction->account_id == $bank->id ? 'selected' : '' ?> ><?= $bank->name ?></option>
								<?php endforeach; ?>
								
								</select>	
								</span>
                                </p>

                                <p>
                                    <label for="property_id">Property</label>
                                    <span class="select">
								<select id="property_id" name="headerTransaction[property_id]" class="editable-select">
								 <option value="-1" selected></option>
                                    <?php
                                    foreach ($properties as $property) {
                                        // echo '<option value="-1" selected >' . "Select Apples" . '</option>';
                                        echo '<option  value="' . $property->id . '" ' . (isset($headerTransaction) && $headerTransaction->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
                                    } ?>
                                
                                </select>
                                </span>
                                </p>
                                <p>
                                    <label for="profile_id">Payee</label>
                                    <span class="select">
								<select onChange="JS.loadList('api/getAddress', $(this).closest('.select').find('input[type=hidden]').val() , '#address',  $(this).closest('#checkModal')) ; console.log($(  'option:selected').text())" id="profile_id" name="headerTransaction[profile_id]" class="editable-select">
								<option value="-1" selected>Select Vendor</option>
                                    <?php foreach ($names as $name):
                                        echo '<option  id="' . $name->id . '" value="' . $name->id . '" ' . (isset($headerTransaction) && $headerTransaction->profile_id == $name->id ? 'selected' : '') . '>' . $name->vendor . '</option>';
                                    endforeach; ?>
                                </select>
								</span>
                                </p>
                                <div id="address" class="overlay-a text-indent">
                                    <?php if ($address): ?>
                                        <p> <?php echo $address->address_line_1 . ' ' . $address->address_line_2 . '<br>' . $address->city ?> </p>

                                    <?php else: ?>
                                        <p>Please Select Vendor</p>
                                    <?php endif; ?>

                                </div>

                            </div>
                            <div style="margin-left: 15px;">
                                <p>
                                    <!-- Important! should be changed to transaction_ref. -->

                                    <label for="check_num">Reference</label>
                                    <input type="text" value="<?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" id="transaction_ref" name="header[transaction_ref]" placeholder="Check #">
                                </p>
                                <p>
                                    <label for="transaction_date">Date</label>

                                    <input data-toggle="datepicker" value="<?= isset($header) && isset($header->date) ? $header->date : '' ?>" id="checkDate" name="header[transaction_date]">
                                    <!-- <input type="hidden" name="header[transaction_date]" value=""/> -->
                                </p>
                                <p>
                                    <label for="amount">Amount <span class="prefix">$</span></label>
                                    <input type="text" lang="en" value="<?= isset($headerTransaction) && isset($headerTransaction->credit) ? $headerTransaction->credit : '' ?>" id="amount" name="headerTransaction[credit]" placeholder="0.00" class="decimal formatCurrency topAmount">
                                </p>
                                <p>
                                    <label for="class_id">Class</label>
                                    <span class="select">
                                    <select id="class_id" name="headerTransaction[class_id]" class="editable-select">
                                    <?php foreach ($classes as $class):
                                        echo '<option value="' . $class->id . '" ' . (isset($headerTransaction) && $headerTransaction->class_id == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
                                    endforeach; ?>
                                    </select>
                                </span>
                                </p>
                            </div>
                        </div>
                        <p>
                            <label for="memo">Memo:</label>
                            <input type="memo" value="<?= isset($header) && isset($header->memo) ? $header->memo : '' ?>" id="memo" name="header[memo]" placeholder="Enter Memo">
                        </p>

                    </section>

                    <div class="has-table-c">

                        <table class="table-c billTable mobile-hide dataTable no-footer formGridTable">
                            <thead class="dataTables_scrollHead">
                                <tr>
                                    <th>Account</th>
                                    <th>Property</th>
                                    <th>Unit</th>
                                    <th>Description</th>
                                    <th class="text-center">Amount</th>
                                    <th>Class</th>
                                    <th>Name</th>
                                </tr>
                            </thead>

                            <tbody id="checkFormBody" class="dataTables_scrollBody">
                            </tbody>
                            <tfoot>

                                <tr>
                                    <td>
                                        <ul class="check-a a">
                                            <li>
                                                <label for="printCheck" class="checkbox <?= isset($header) && ($header->to_print == 0) ? '' : 'active' ?>">
                                                        <input type="hidden" name="header[to_print]" value="0"/><input type="checkbox" value="1" <?= isset($header) && ($header->to_print == 0) ? '' : 'checked' ?>  id="printCheck" name="header[to_print]" class="hidden" aria-hidden="true">
                                                        To be printed
                                                        <div class = "input"></div>
                                                </label>
                                            </li>

                                            <!-- <li><label for="printCheck" class ="checkbox active">
                                              <input type="checkbox"  id="printCheck" name="headerTransaction[to_print]"  class="hidden" aria-hidden="true">
                                              To be printed

                                            </label> -->
                                            </li>
                                        </ul>

                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">Total:</td>
                                    <td class="text-center"><span class="text-left">$</span>
                                        <span id="totalAmount"><?php echo $checkTotal > 0 ? $checkTotal
                                                : '.00 ' ?></span></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>

                        </table>
                    </div>



         
     
        <footer>
          <ul class="list-btn">
            <li><button type="submit" after="mnew">Save &amp; New</button></li>
            <li><button type="submit" after="mclose">Save &amp; Close</button></li>
            <li><button type="submit" after="duplicate">Duplicate</button></li>
            <li><button type="button">Cancel</button></li>
            
          </ul>
		  <?= $header ?
          "<ul>
		  	<li>Last Modified $header->modified</li>
            <li>Last Modified by <a href='#!'>$header->user</a></li>
          </ul>" : ''; ?>
                    </footer>
                </form>

            </div>
        </div>

    </div>
  <!-- print section -->

<div id="check_print" style ="display:none;" > </div>

        </div>

        <script type="text/javascript">
            var template = function (id, data = {}) {
                var newRow = '<tr class="checkRow" id="' + id + '" ' + (data.id ? 'tid="' + data.id + '"' : '') + ' ' + (data.property_id && data.property_id != '-1' ? 'property_id="' + data.property_id + '"' : '') + '>' +
                    '<td class="formGridSelectTd" stype="account" source="[sel-id=account_id]" ' + (data.account_id ? 'value="' + data.account_id + '"' : '') + '></td>' +
                    '<td class="formGridSelectTd" stype="property" source="[sel-id=property_id]" ' + (data.property_id ? 'value="' + data.property_id + '"' : '') + '></td>' +
                    '<td class="formGridSelectTd" stype="unit" ' + (data.unit_id ? 'value="' + data.unit_id + '"' : '') + '></td>' +
                    '<td><input type="text" id="description" name="transactions[' + id + '][description]" value="' + (data.description ? data.description : '') + '"></td>' +
                    '<td total="debit"><input type="text" source="#amount" class="decimal checkAmount total" id="amount" name="transactions[' + id + '][debit]" value="' + (data.debit || data.credit ? data.debit - data.credit : '') + '" placeholder="0"></td>' +
                    '<td class="formGridSelectTd" stype="class" source="[sel-id=class_id]" ' + (data.class_id ? 'value="' + data.class_id + '"' : '') + '></td>' +
                    '<td class="formGridSelectTd" stype="profile" source="[sel-id=profile_id]" ' + (data.profile_id ? 'value="' + data.profile_id + '"' : '') + '></td>' +
                    '</tr>';
                return newRow;
            }
            var grid = $('.modal').last().formGrid({
                template: template,
                data: <?php echo $jTransactions ? $jTransactions : 0 ?>,
                minRows: 8
            });
            grid.addTotal('debit', '#amount', '#amount', '#totalAmount');


            $('body').on('keydown', '.checkRow > td', function () {

                var columnNum = $(this).index();
                //alert($(this).find('input').first().attr('key'));

                //console.log($(this).closest('tr').next("tr"));
                var keyPressed = event.which || event.keyCode;
                switch (keyPressed) {
                    case 37:
                        alert('left');
                        break;
                    case 38:
                        $(this).closest('tr').prev("tr").find("td:nth-child(" + (columnNum + 1) + ")").find("input:first-child").focus();
                        break;
                    case 39:
                        alert('right');
                        break;
                    case 40:
                        $(this).closest('tr').next("tr").find("td:nth-child(" + (columnNum + 1) + ")").find("input:first-child").focus();

                        break;
                }
            });
        </script>

        <script type="text/javascript">

           
function checkPrintInit (this, e)
  {
	var transactions2 = [];
	e.preventDefault();
	var that2 = this;
	//console.log(that2);
    var headerTransaction_id = $(this).closest('.modal').find("section:first").find('#account_id').closest('.select').find('input[type="hidden"]').val();
    var transNum = $(this).closest('.modal').find('#transNum').val(); 

				transactions2.push({'th_id': transNum, 'id': headerTransaction_id });
 

		$.post(JS.baseUrl+"transactions/onPrint", {
                'params': JSON.stringify(transactions2)
            }, function (result) {
				console.log(result);
				console.log("result");
			var result2 = JSON.parse(result);
			console.log(result2[0].next_check_num);
			 getcheckNumber(result2[0].next_check_num, result2, that2);
        })
		
		console.log(transactions2);

  }
	
function checkprint(result){
	var data = result;
	var newPrintDiv = "<div>";
	for(var i =0; i < data.length; i++){

		 newPrintDiv += `<div  class="page-break">    
					<div id="check">
						<div id="name" >
							<strong>DK Technology NY inc</strong><br>
							37 Lee Ave<br>
							Brooklyn NY 11211<br>
						</div>

						<div id="check_info">
							<table>
								<tbody>
									<tr>
										<th>Check Date__</th> 
										<th>Check No.</th>
									</tr>
									<tr>
										<td id = cDate>` + data[i].date; +`</td>
										<td id="cNum">` + data[i].next_check_num;
										newPrintDiv  += `</td>
									</tr>
								</tbody>
							</table>
							
							<table >
								<tbody>
									<tr>
										<th>Amount</th>`;
					newPrintDiv  += `</tr>
									<tr>
										<td id="Amount_num">` + data[i].credit;
										newPrintDiv  += `</td>
									</tr>
								</tbody>
							</table>
						</div>

					<div id="textAmount">`+textNum(data[i].credit)+`</div>
					<div id="bottum">
						<div >
							<small style ="position: relative; left: -150px;">Pay To The Order Of</small><br>
							<span style ="position: relative; left: -150px;">`+ data[i].profile;
							newPrintDiv  += `</span><br>
							<span id="payToAddress">262 Keap st</span><br>
							<span>Brooklyn NY 11211</span>
						</div>
						<div id="signature">

							<div id="authsig"><small>Authorized Signature</small></div>
						</div>

						
					</div>
					<div id="accountNumber" style=" font-family: micr37;" >
					<span id ="checkNumber">` + data[i].next_check_num;
						newPrintDiv  += `</span>
						 <span id ="routingNum">:` + data[i].routing + `:`;
						newPrintDiv  += `</span> <span id ="acctNum">`  + data[i].account_number + `'`;
						newPrintDiv  += `</span> </div>
				</div>
				<div id="stub" style="border-bottom: 1px dashed gray;"  class="page-break">
					<h6 >JB NY REALTY LLC</h6>

					<table align="center";  class="stub"><thead><tr><th>Account </th><th> Property </th><th> Description</th><th style='text-align:right;'>Amount</th></tr></thead>`;
					var details = data[i].details;
					for(var j = 0; j < details.length; j++){
					
						newPrintDiv  +=  "<tr><td>" + details[j].account + "</td><td> " + details[j].property + " </td><td> " + details[j].description + "</td><td style='text-align:right;'>"+ details[j].debit +"</td></tr>";		
					}
					newPrintDiv  += `</table>
				</div>
				<div id="stub">
					<h6 >JB NY REALTY LLC</h6>

					<table style="text-align:center";  class="stub"> <thead><tr><th>Account </th><th> Property </th><th> Description</th><th style='text-align:right;'>Amount</th></tr></thead>`;
					var details = data[i].details;
					for(var j = 0; j < details.length; j++){
						newPrintDiv  +=  "<tr><td>" + details[j].account + " </td><td>" + details[j].property + " </td><td>" + details[j].description + "</td><td style='text-align:right;'>"+ details[j].debit +"</td></tr>";	
					}
					newPrintDiv  += `
					</table>
				</div>
			</div>`;
   
					
	}
				newPrintDiv += `</div>`;
				$('#check_print').append(newPrintDiv);
				function pad(n,a) { return ("000000" + n).slice(-a); }
			 	var body2 = $('body').find('#checkModal'); 				
			   checkprint2();
			   console.log(textNum('123.52'));
			   
	   function checkprint2(){			

			   $(body2).find("#check_print").addClass('print-section');

			   window.print();

		   }
}

function getcheckNumber(number, checkInfo, modal){

bootbox.prompt({
			title: "Select Check Number",
			inputType: "number",
			value: number,
			buttons: {
				confirm: {
					label: 'Print',
					className: 'btn-danger'
				},
				cancel: {
					label: 'Cancel',
					className: 'btn'
				}
			},
			callback: function (result) {
				if (result) {
					console.log(checkInfo[0].next_check_num);
					checkInfo[0].next_check_num = result;
					console.log(checkInfo[0].next_check_num);
					$(modal).closest('body').find('.bootbox').hide();
					checkprint(checkInfo);
				}
			}
		});
}

function textNum (num) {
	//displaying amount as text
	            var ones = ["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine"];
				var teens = ["Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"];
				var tens = ["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"];


				var numtochange = num;
				var decimal;
				if (numtochange.indexOf(".")<0) {decimal=numtochange.length} else{decimal= numtochange.indexOf(".")};

				var dollars = numtochange.slice(0,decimal);
				var cents = numtochange.slice(decimal+1,numtochange.length);
				var numtext ="";


					for (var i = 0; i < dollars.length; i++) {
						
							console.log(dollars.length - i );

							switch(dollars.length - i) {
								case 15: case 12: case 9: case 6: case 3:
									numtext = numtext + " " + ones[dollars.charAt(i)] + " hundred";
									break;
								case 14: case 11: case 8: case 5: case 2:
									numtext = numtext + " " + tens[dollars.charAt(i)];
									break;
								case 13:
									if (dollars.charAt(i-1) ==1) {
											numtext = numtext + " " + teens[dollars.charAt(i)] + " Trillion,";
									}
									else{
										numtext = numtext + " " + ones[dollars.charAt(i)] + " Trillion,";
									}
									
									break; 

								
								case 10:
									if (dollars.charAt(i-1) ==1) {
											numtext = numtext + " " + teens[dollars.charAt(i)] + " Billion,";
									}
									else{
										numtext = numtext + " " + ones[dollars.charAt(i)] + " Biliion!,";
									}
									
									break;    

								
								case 7:
									if (dollars.charAt(i-1) ==1) {
											numtext = numtext + " " + teens[dollars.charAt(i)] + " million,";
									}
									else{
										numtext = numtext + " " + ones[dollars.charAt(i)] + " million,";
									}
									
									break;                        

								
								case 4:
									if (dollars.charAt(i-1) ==1) {
											numtext = numtext + " " + teens[dollars.charAt(i)] + " Thousand,";
									}
									else{
										numtext = numtext + " " + ones[dollars.charAt(i)] + " Thousand,";
									}
									
									break;

								
								case 1:
									if (dollars.charAt(i-1) ==1) {
											numtext = numtext + " " + teens[dollars.charAt(i)] + " dollars and";
									}
									else{
										numtext = numtext + " " + ones[dollars.charAt(i)] + " dollars and";
									}
									

							}
			
							
					}

					if (cents.length ==0){
						numtext = numtext+" 00 cents";
						} else {
                            for (var i = 0; i <cents.length; i++) {


				
								switch(cents.length-i){
									case 2:
									
									numtext = numtext+" "+ tens[cents.charAt(i)];

									break; 

									case 1:

									if (cents.charAt(i -1) ==1) {
									numtext = numtext+" "+ teens[cents.charAt(i)]+" cents";
									}
									else{
									numtext = numtext+" "+ ones[cents.charAt(i)]+" cents" ; 	
									}

									break;

									
									
								} 
						}
						
				    
				
    
			}

			return numtext;
}  
        </script>
