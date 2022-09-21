<div class="modal fade memorized-transactions-modal" id="memorizedTransactions" tabindex="-1" role="dialog"  type="memorized-transactions" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root" class="no-print">
            <div class="modal-content text-primary popup-a form-entry shown" style=" width:1467px;  padding: 30px;">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
				  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="memorized-transactions-entry" type=" memorized-transactions">
			<div class="t_input_wrapper">
				<header class="m50">
					<h2>Memorized Transactions</h2>
					<div>
						<p class="input-search">
							<label for="fsa">Search</label>
							<input type="text" id="fsa" name="fsa">
							<button type="button">Submit</button>
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
        <div class ="has-table-c">
            <table class="table-c dc d da  billTable mobile-hide dataTable no-footer" style="display: table;  margin:0 auto;  ">
                <thead id="cc_grid_charge_head" class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden; border-radius: 6px; margin-bottom: 6px;">
							<th style="width: 4%;"></th>
							<th style="width: 4%;" class="check-a"><label for="selectAllCheckboxes" class = "checkbox"><input type="checkbox" id="selectAllCheckboxes" class="selectAllCheckboxes" name="fbm2"><div class="input"></div></label></th>
							<th style="width: 15%;">Name</th>
							<th style="width: 15%;">Type</th>
							<th style="width: 15%;">Next Date</th>
							<th style="width: 15%;">Frequency</th>
							<th style="width: 15%;">auto?</th>
							<th style="width: 15%;">Property</th>
							<th style="width: 15%;">Amount</th>
					</thead>
					<tbody id="checks_body" class="dataTables_scrollBody testTable" style=" display: block;height: calc(100vh - 300px);overflow: auto; box-shadow: 0 0px 0px; border-width: 0px;">
					</tbody>
					<style type="text/css" onload="getMemorizedTBody($(this).closest('.modal'))"></style>
				</table>
				</div>
				<p class="strong scheme-b m20 size-a overlay-a" id="selectedCharges">0 Transactions selected</p>
				<footer class="a">
					<p class="m0">
						<button class ="grid" type="submit" after="mclose">Submit Transactions</button>
						<button id="deleteMemorizedTransactions" type="submit" after="mclose">Delete Transactions</button>
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

     var memorizedTransactions = <?php echo $jMemorizedTransactions; ?>;

//
//$(document).ready(function () {
	function getMemorizedTBody(modal){
		formsJs.getAjaxAccount($(modal).find('#checks_body'), memorizedTransactions, 'memorizedTransactions');
		JS.checkboxes(modal);
	}
//});

console.log('cc charges00');


</script>
