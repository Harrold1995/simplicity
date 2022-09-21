	<div class="maintenance-page">
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
										<option value="6" selected>Due Date</option>
									</select></span>
				</p>
			</form>
			<p><a id="addMaintenanceButton" data-url = 'maintenance/getModal' href="#maintenanceModal"><i class="fas fa-plus"></i> <span class="text-uppercase">New</span> Ticket</a></p>
		</header>
		<div class="table-f-wrapper vtab div1">
			<div class="rightslick-table1"></div>
		</div>
	</div>
	<script src="<?php echo base_url(); ?>/themes/default/assets/js/plugins/slick.maintenance.js"></script>
	<script>
		$(document).ready( async function () {
			let slickrender = new Promise(function(resolve, reject) {
				var slick = new SlickMaintenance('.rightslick-table1', {dataUrl: "maintenance/getTickets"});
				resolve(slick);
			})
			
			$('.maintenance-page').data('slickgrid', await slickrender);
		});
	</script>
