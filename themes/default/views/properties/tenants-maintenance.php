<div class="maintenance-page table-b-wrapper">
	<header class="header-triple">
		<form action="./" method="post" class="form-inline">
			<p style="z-index: 1;">
				<label for="fta">Group by:</label>
				<span class="select"><select name="fta" id="mgrouping">
										<option value="0">None</option>
										<option value="1">Property</option>
										<option value="2">Status</option>
										<option value="3">Type</option>
										<option value="4">Assigned to</option>
										<option value="5">Priority</option>
									</select></span>
			</p>
		</form>
		<p><a id="addMaintenanceButton" data-url = 'maintenance/getModal?property_id=<?php echo $tenant->property_id?>&unit_id=<?php echo $tenant->unit_id?>&tenant_id=<?php echo $tenant->id?>' href="#maintenanceModal"><i class="fas fa-plus"></i> <span class="text-uppercase">New</span> Ticket</a></p>
	</header>
	<div class="table-f-wrapper vtab div1">
		<div class="rightslick-tabletm"></div>
	</div>
</div>
<script>
	$(document).ready(function () {
		var slick = new SlickMaintenance('.rightslick-tabletm', {nofilter:true, dataUrl: "maintenance/getTickets?tenant_id=<?php echo $tenant->id?>"});
	});
</script>
