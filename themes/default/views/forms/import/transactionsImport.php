<div class="modal fade transaction-import-modal" id="in_court" tabindex="-1" role="dialog"  type="transaction-import" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root" class="no-print">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 30px;">
                  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="transaction-import-entry" type="transaction-import">
			<div class="t_input_wrapper">
				<header class="m50">
					<h2>Transaction import</h2>
					<div>
						<p class="input-search">
							<label for="fsa">Search</label>
							<input type="text" id="fsa" name="fsa">
							<button type="submit">Submit</button>
							<a href="./"><i class="icon-microphone"></i> <span>Record</span></a>
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
        <div class ="has-table-c">
        
            <table class="table-c dc d da  billTable mobile-hide  no-footer" style="display: table;  margin:0 auto;  ">
                <thead id="1" class="dataTables_scrollHead" >
				<input type="file" name="file" id="file">
                            <!--<style type="text/css" onload="getIn_courtTHead($(this).closest('.modal'))"></style>-->
					</thead>
					<tbody id="tbody" class="dataTables_scrollBody testTable" style="width: 100%; display: block;height: calc(100vh - 300px);overflow: auto; box-shadow: 0 0px 0px; border-width: 0px;">
					</tbody>
					<!--<style type="text/css" onload="getIn_courtTBody($(this).closest('.modal'))"></style>-->
				</table>
				</div>
				<p style="display:none" class="strong scheme-b m20 size-a overlay-a" id="selectedCharges">0 Transactions selected</p>
				<footer class="a">
					<p class="m0">
						<button type="submit" class="grid"  after="mclose">Submit</button>
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

var accounts = ['', 'account_types_id', 'accno', 'parent_id'];
	document.getElementById('file').onchange = function(){

  var file = this.files[0];

  var reader = new FileReader();
  reader.onload = function(progressEvent){
    // Entire file
    //console.log(this.result);


    // By lines
    var lines = this.result.split('\n');
    //looping thru each row
    for(var line = 0; line < lines.length; line++){


        //splitting the line by commas
        var res = lines[line].split(",");
             //if this is the first line create dropdowns for each column
              if (line == 0) {
                      var th = document.createElement("TR");
                      var id = 2;
                       for (var column = 0; column < res.length; column++) {
                      //creating td for each cell
                      var td = document.createElement("TD");
                      
                      var select = document.createElement("SELECT");
                      select.setAttribute('id',id);
                      select.setAttribute('Class',"column_header");
                      id++;
                      th.appendChild(td);
                      td.appendChild(select);

                      for (var header = 0; header < accounts.length; header++) { 
                        var option = document.createElement("OPTION");
                      //putting data into the cell
                      var t = document.createTextNode(accounts[header]);

                      select.appendChild(option);
                      option.appendChild(t);

                      }
                      // select.setAttribute('id',id);
                      // id++;

                       document.getElementById('1').appendChild(th);
              }
             }
        //creating tr for each line
        var tr = document.createElement("TR");


             var classes = 1;
        //looping thru each cell
         for (var column = 0; column < res.length; column++) {
              //creating td for each cell
              var td = document.createElement("TD");
              td.setAttribute('class',classes);
              td.setAttribute('contenteditable','true');
              classes++;
              //putting data into the cell
              var t = document.createTextNode(res[column]);
              tr.appendChild(td);
              td.appendChild(t);
              }
    
    document.getElementById('tbody').appendChild(tr);
  
    }
    var button = document.createElement("BUTTON");
    button.innerHTML = 'Submit';
    button.setAttribute('id',"submitButton");
    document.getElementById('8').appendChild(button);
    // var submit = document.getElementById('submitButton');
    // submit.addEventListener("click", function(){getSelected();});
  };
  reader.readAsText(file);
};

    //  var in_court = < ?php echo $jIn_court; ?>;

//
//$(document).ready(function () {
	function getIn_courtTBody(modal){
		getAjaxAccount($(modal).find('#in_court_body'), in_court);
	}
//});
function getIn_courtTHead(modal){
		getAjaxHead($(modal).find('#in_court_head'), in_court);
	}

console.log('cc charges00');

        function getAjaxHead(body, transactions){
            var head ="";
            for (var i = 0; i < 1; i++) {
					head += `<tr role="row" class="allTransactions" style="display: table; width: 100%; table-layout: fixed;">
								<th style="width: 4% !important;">
								</th>
								<th style="width: 4%;" class="check-a">
									<label for="selectAllCheckboxes" class="checkbox">
										<input type="checkbox" id="selectAllCheckboxes" name="`+ i +`" class="selectAllCheckboxes">
											<div class="input"></div>
									</label>
								</th>`;
                            $.each(transactions[i], function(key, value) {
								//console.log(key, value);
								if(key != "id" && key != "notes"){
                                    console.log(key)
									head +=	`<th  style="width: 15% ">` + key +`</th>`;
								}
                            });
                            head +=	`</tr>`;
                } 
                body.append(head);
        }

//populates all transactions
	function getAjaxAccount(body, transactions){

		$(body).empty();
		selected = 0;
		selectedCharges();
        var newRow = "";   
        var newkey = "";
		if(transactions){

				for (var i = 0; i < transactions.length; i++) {
                    console.log("row");
					newRow += `<tr role="row" class="allTransactions getCourtNotes" style="display: table; width: 100%; table-layout: fixed; border-bottom: 1px solid #e0dddd;>
								<td style="width: 4% !important;">
								<input type="hidden" name="" value="`+ transactions[i].id +`" id="`+ transactions[i].id +`" >
									
								</td>
								<td style="width: 4%;" class="check-a">
									<label for="`+ i+`" class="checkbox">
										<input type="checkbox" id="`+ i +`" name="`+ i +`" class="hidden allAccounts" aria-hidden="true"
										onchange="Checkbox($(this),`+ transactions[i].id +`);">
											<div class="input"></div>
									</label>
								</td>`;
                            $.each(transactions[i], function(key, value) {
                                var checkboxNum = 0;
								//console.log(key, value);
								if(key != "id" && key != "notes"){
                                        //console.log(key);
                                        newKey = columnName(key);
                                        //console.log(newKey);
                                    if(key == "Follow up date"){ 
                                        newRow +=	`<td id="`+ newKey +`" style="width: 15% "><input type ="text" data-toggle="datepicker" name="" value="` + value +`"></td>`;
                                    }
                                    else if(key == "Warrant requested" || key == "Warrant issued"){
                                        //console.log('warrant here')
                                        newRow +=	`<td id="`+ newKey +`" class="check-a" style="width: 15% ">
                                                     <label for="`+ newKey + checkboxNum +`" class="checkbox `;
                                            newRow += value == 1 ?  ' active' : ''; 
                                            newRow += `"><input type="hidden" name="" value="0" /><input type="checkbox"`;
                                            newRow += value == 1 ? ' checked ' : '';
                                            newRow +=` id="`+ newKey +checkboxNum +`" value="1" name="" class="hidden" aria-hidden="true"/><div class="input"></div>
                                                        </label>
                                                     </td>`;
                                                     checkboxNum++;
                                    }else{
                                        newRow +=	`<td id="`+ newKey +`"  style="width: 15% "><input type="text" value="` + value +`"></td>`;
                                    }
								}
                            });
                            newRow +=	`</tr>`;
				} 
			}else{
				newRow = `<div style="width: 75%; height: 35px; text-align:center; font-size: 25px; color: red; border: 3px solid grey; margin: 0 auto; "><tr><td style="width:100%">No one in court.</td></tr></div>`;
            }
            body.append(newRow);
			JS.checkboxes(body);
            JS.datePickerInit(body);
	}
		//creates/deletes hidden input name based on checkbox
	//new: using dynamic function to loop through each td id and setting/unsetting the name attr
	function setAccountName(checkbox, id){

            if($(checkbox).parent('label').hasClass('active')){
                console.log("setting account name");

                $(checkbox).closest('tr').find('#id').attr('name', `in_court[` + id + `]`);	
                $(checkbox).closest('tr').find('td').each (function( column, td) {
                    var name = $(td).attr('id');
                    if(name == "follow_up_date"){
                        $(td).find('input[type=hidden]').attr('name', `in_court[` + id + `][` + name + `]`);
                    }else{
                        $(td).find('input').attr('name', `in_court[` + id + `][` + name + `]`);
                    }
                });
            }else{
                $(checkbox).closest('tr').find('input').removeAttr('name');
            }
    }




    </script>
