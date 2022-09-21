<div class="modal fade capital-modal" id="capital" tabindex="-1" role="dialog"  type="capital" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root" class="no-print">
            <div class="modal-content text-primary popup-a form-entry shown" style="width: 112%;  padding: 30px;">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
				  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="capital-entry" type="capital">
			<div class="t_input_wrapper">
				<header>
					<h2>Capital</h2>
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
                <div style="padding: 10px;">
                        <span style="margin-right: 12px;">As of </span>
                        <input style="width: 18%; display: inline;" data-toggle="datepicker" value="" id="capital_as_of_date" onchange="formsJs.getCapitalAjax($(this).val(), $(this).closest('.modal').find('#capital_body'))">

                        <span style="margin-right: 12px;">Due date </span>
                        <input style="width: 18%; display: inline;" data-toggle="datepicker" value="" id="capital_due_date" onchange="formsJs.capital_due_date($(this));">
                </div>
        <!-- div class ="has-table-c" style=" height: calc(100vh - 300px); overflow: scroll;">
            <table class="table-c dc d da  billTable mobile-hide dataTable no-footer" style="display: table;  margin:0 auto;  ">
                <thead id="cc_grid_charge_head" class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden; border-radius: 6px; margin-bottom: 6px;">
				            <th style="width: 4%;" class="check-a"><label for="selectAllCheckboxes" class = "checkbox"><input type="checkbox" id="selectAllCheckboxes" class="selectAllCheckboxes" name="fbm2"><div class="input"></div></label></th>
							<th>name</th>
							<th>Bank account balance</th>
							<th>Open Ap</th>
							<th>Loans receivable</th>
							<th>Mortgage payment</th>
							<th>Additional anticipated expenses</th>
							<th>Included in payables</th>
							<th>Notes</th>
							<th>Left over before NOI</th>
							<th>Avg expenses for the last 3 months</th>
                            <th>Income amount</th>
							<th>Left over after NOI</th>
							<th>Capital call amount</th>
					</thead>
					<tbody id="capital_body" class="dataTables_scrollBody testTable clickable2" style="display: table; width: 100%; table-layout: fixed; overflow: hidden; border-radius: 6px; margin-bottom: 6px; height: calc(100vh - 300px);" >
					</tbody>
					</table>
				</div> -->
                <p class="strong scheme-b m20 size-a overlay-a" id="selectedCharges">0 Properties selected</p>
                <button type="submit" after="mclose" class="">Generate Capital Calls</button>
				<button type="submit" after="mclose" id = 'exportCapital' class="">Generate Capital Calls</button>
		        </div>
                  </form>               
            </div>
        </div>
    </div>

</div>
</div>

<script>
console.log('capital');
</script>
