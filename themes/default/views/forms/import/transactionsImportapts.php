<div class="modal fade transaction-import-modal" id="transaction-import" tabindex="-1" role="dialog"  type="transaction-import" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root" class="no-print">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 30px;">
                  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="transaction-import-entry" type="transaction-import">
			<div class="t_input_wrapper">
				<header  class="modal-h">
					<h2>Transaction import</h2>
					<div>
						<p class="input-search">
							<label for="fsa">Search</label>
							<input type="text" id="fsa" name="fsa">
							<button type="submit">Submit</button>
							<!--a href="./"><i class="icon-microphone"></i> <span>Record</span></a-->
						</p>
					</div>
						<nav style="margin-left: 0;">
							<ul>
								<li><span class="buttons" style=""><span class="min" style="padding: 8px 20px;cursor: pointer;">_</span></span> </li>
								<li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
								<li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
							</ul>
						</nav>
					<p class="submit"><button type="button" id="exit">Exit</button></p>
				</header>
         <p>
            <input style ="border-bottom:none; display:inline-block; max-width:200px" type="file" name="file" id="file">
            <input style =" display:inline-block; max-width:200px" id="transaction date" name="transaction_date" class = "datepickerright" data-toggle="datepicker" type="text" value ="<?= date("Y-m-d");?>">
            <input type="hidden" name="importType" value="apts">	
         </p>
                  
        <div class ="has-table-c">
        
            <table class="table-c dc d da  billTable mobile-hide dataTable no-footer" style="display: table;  margin:0 auto;  ">
                <thead id="transactionsImport_thead" class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden; border-radius: 6px; margin-bottom: 6px;">
                    <tr> 
                        
                        <th style="width: 4%;" class="check-a">
                            <label for="selectAllCheckboxes" class="checkbox">
                                <input type="checkbox" id="selectAllCheckboxes" class="selectAllCheckboxes" >
                                <div class="input"></div>
                            </label>
                        </th>
                        <th>Type</th>
                        <th>Memo</th>
                        <th>Status</th>
                        <th>Initiated On</th>
                        <th>Completed On</th>
                        <th>Credit Amt</th>
                        <th>Debit Amt</th> 
                        <th>Initiated By</th>
                        <th>Property</th>
                        <th>City</th>
                        <th>State</th>
                        <th>Zip</th>
                        <th>Country</th> 
                        <th>ReferenceID</th>    
                        <th>Unit</th>
                        <th>TransactionID</th> 
                        <th>ReferenceID</th>      
                </tr>				
                </thead>
                <tbody id="transactionsImport_tbody" class="dataTables_scrollBody testTable" style="display: block; height: calc(100vh - 300px);overflow: auto; box-shadow: 0 0px 0px; border-width: 0px;">
                </tbody>
            </table>
				</div>
				<p class="strong scheme-b m20 size-a overlay-a" id="selectedCharges">0 Transactions selected</p>
				<footer class="a">
					<p class="m0">
						<button type="submit" after="mclose"  class="grid">Submit</button>
					</p>
				</footer>
			
		</div>
                  </form>               
            </div>
        </div>
    </div>

</div>
</div>

<script>

	document.getElementById('file').onchange = function(){
        var fileButton = this;
        var modal = $(fileButton).closest('.modal');
        selected = 0;
        console.log(fileButton);
        console.log(modal);
  var file = this.files[0];
  //var filename = file.name
  //filename = filename.split('.').slice(0, -1).join('.');

  //getting the date from filename
   // if (filename.match(/^[0-9]+$/) != null && filename.length == 8) {
           // newdate = filename.slice(0, 4)+"-"+(0+filename.slice(4, 6)).slice(-2)+"-"+(0+filename.slice(6, 9)).slice(-2);
           // $( "input[name='transaction_date']" ).val(newdate);
   // }


 // console.log(newdate);
  var newRow = "";

  var reader = new FileReader();
  reader.onload = function(progressEvent){

    // By lines
    var lines = this.result.split('\n');
    //looping thru each row
    for(var line = 1; line < lines.length; line++){
        //console.log(lines[line]);

        var rowId = line + 1;
        //splitting the line by commas
        if(lines[line].length != 0){
            var res = lines[line].split(",");
            console.log(res);
            if (res[0]=='Payment' //&& res[2]=='Completed'
            ){
                newRow += `<tr id="`+ rowId +`" role="row" style="display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;" >
                                <td style="width: 1% !important;">
                                <input type="hidden" name="" value="`+ rowId +`" id="id">	
								</td>
								<td style="width: 4%;" class="check-a">
									<label for="`+ rowId+`" class="checkbox">
										<input type="checkbox" id="`+ rowId +`" name="`+ rowId +`" class="hidden allAccounts" aria-hidden="true"
										onchange="formsJs.Checkbox($(this));">
											<div class="input"></div>
									</label>
                                </td>`;
                                var names = ['type', 'description', 'status', 'initiatedOn', 'completedOn','credit','debit','profile','property', 'city', 'state', 'zip', 'country','unit','transactionID', 'ReferenceID'];
                                for (var column = 0; column < res.length; column++) {
                                    newRow += `<td style = "width:20%" id="`+ names[column] +`" name="" value="`+ res[column] +`">
                                    <input type="hidden" name="" value='`+ res[column].replace(/['"]+/g, '') +`'>`+ res[column] +`</td>`;
                                }
                                newRow += `</tr>`;

            }
             
  
    }
        }

    $(modal).find('#transactionsImport_tbody').empty();
    $(modal).find('#transactionsImport_tbody').append(newRow);
    JS.checkboxes(modal);

  };
  reader.readAsText(file);
};


console.log('cc charges00');

    </script>
