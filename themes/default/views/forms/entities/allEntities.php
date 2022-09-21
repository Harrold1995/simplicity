<div class="modal fade allEntities-modal" id="allEntities" tabindex="-1" role="dialog"  type="all-entities" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root" class="no-print">
            <div class="modal-content text-primary popup-a form-entry shown" style=" width:1467px;  padding: 30px;">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
				  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="allEntities-entry" type="allEntities">
			<div class="t_input_wrapper">
				<header class="m50">
					<h2>Entities</h2>
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
        <div class ="has-table-c">
            <table class="table-c dc d da  billTable mobile-hide dataTable no-footer" style="display: table;  margin:0 auto;  ">
                <thead id="cc_grid_charge_head" class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden; border-radius: 6px; margin-bottom: 6px;">
				<th style="width: 4%;" class="check-a"><label for="selectAllCheckboxes" class = "checkbox"><input type="checkbox" id="selectAllCheckboxes" class="selectAllCheckboxes" name="fbm2"><div class="input"></div></label></th>
							<th style="width: 9%;">name</th>
							<th style="width: 9%;">Address</th>
							<th style="width: 9%;">City</th>
							<th style="width: 9%;">State</th>
							<th style="width: 9%;">Zip</th>
							<th style="width: 9%;">Email</th>
							<th style="width: 9%;">Phone</th>
							<th style="width: 9%;">Tax #</th>
							<th style="width: 9%;">Description</th>
							<th style="width: 9%;">Closing Date</th>
					</thead>
					<tbody id="allEntities_body" class="dataTables_scrollBody testTable clickable2" style=" display: block;height: calc(100vh - 300px);overflow: auto; box-shadow: 0 0px 0px; border-width: 0px;">
					</tbody>
					<style type="text/css" onload="getEntitiesBody($(this).closest('.modal'))"></style>
					</table>
				</div>
			
		</div>
                  </form>               
            </div>
        </div>
    </div>

</div>
</div>

<script>

     var entities = <?php echo $jentities; ?>;

//$(document).ready(function () {
	function getEntitiesBody(modal){
		formsJs.getAjaxAccount($(modal).find('#allEntities_body'), entities, 'all_entities');
	}
//});
console.log('entities00');
</script>
