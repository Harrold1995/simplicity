<div class="modal fade in_court-modal" id="in_court" tabindex="-1" role="dialog"  type="in_court" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root" class="no-print">
            <div class="modal-content text-primary popup-a form-entry shown" style="  padding: 30px;">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
				  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="in_court-entry" type="in_court">
			<div class="t_input_wrapper">
				<header class="modal-h">
					<h2>In Court</h2>
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
        
            <table class="table-c dc d da  billTable mobile-hide dataTable no-footer" style="display: table;  margin:0 auto;  ">
            <div id="courtNotesDiv" style='     background-color:#f6f8f9; padding: 15px; border-radius: 15px; display: none; height: 75%; width: 25%; right: 15px; position: fixed; z-index: 3000; height: calc(100vh - 300px); overflow: hidden;'>
            <span class="buttons" style="right: 0%; position: absolute; top: 1%;"><span class="closeCourtDiv" style="padding: 8px 20px;cursor: pointer;">X</span></span>
                <h3 style="text-align: center; ">Notes</h3>
				<div id="noteForm" style="padding-bottom: 10px;"></div>
                <div id="courtNotes" style="height: 100%; color: #919090;font-size: 11px; margin: 10px; overflow: auto; margin: -12px;"></div> 
            </div>
                <thead id="in_court_head" class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden; border-radius: 6px; margin-bottom: 6px;">
                            <style type="text/css" onload="getIn_courtTHead($(this).closest('.modal'))"></style>
					</thead>
					<tbody id="in_court_body" class="dataTables_scrollBody testTable" style="width: 100%; display: block;height: calc(100vh - 300px);overflow: auto; box-shadow: 0 0px 0px; border-width: 0px;">
					</tbody>
					<style type="text/css" onload="getIn_courtTBody($(this).closest('.modal'))"></style>
				</table>
				</div>
				<p style="display:none" class="strong scheme-b m20 size-a overlay-a" id="selectedCharges">0 Transactions selected</p>
				<footer class="a">
					<p class="m0">
						<button type="submit" after="mclose">Submit</button>
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

     var in_court = <?php echo $jIn_court; ?>;

//
//$(document).ready(function () {
	function getIn_courtTBody(modal){
		formsJs.getAjaxAccount($(modal).find('#in_court_body'), in_court, 'in_court');
		JS.checkboxes(modal);
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

    
    var userId = <?php echo $this->ion_auth->get_user_id(); ?>;


    </script>
