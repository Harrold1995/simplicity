<article class="right-side"></article>
<aside class="left-side" data-type="maintenance" style="opacity:1">
	<div class="accordion-a">
	<div class='checklist-a'><span><label></label><span></div>
		<h4 class="toggle">Special veiws</h4>
		<div class="custom-view-container">
		  <ul class="checklist-a">
			 <li data-filterField = 'assigned_to' class ='customView' data-filterid = '<?php echo $this->session->userdata('profileId'); ?>'> <i class ='icon-user'></i><span>Assigned to me</span> </li>
			 <li data-filterField = 'status' class ='customView' data-filterid = '1'> <i class ='fa fa-circle'></i><span>New</span> </li>
			 <li data-filterField = 'priority' class ='customView' data-filterid = '2'> <i class ='fa fa-exclamation'></i><span>Urgent</span> </li>
			 <li data-filterField = 'assigned_to' class ='customView' data-filterid = '0'> <i class ='icon-users'></i><span>Unassigned</span> </li>
			 <li data-filterField = 'due_date_calc' class ='customView' data-filterid = '2,4,6,8'> <i class ='icon-time'></i><span>Overdue</span> </li>
			 <li data-filterField = 'status' class ='customView' data-filterid = '1,2'> <i class ='icon-documents'></i><span>Open</span> </li>
			 <li data-filterField = 'all' class ='customView' data-filterid = '8'> <i class ='fa-bars'></i><span>Show All</span> </li>		 
		  </ul>
    </div>

		
		
		<?php foreach ($data as $group) { ?>

            <div class='checklist-a'><span><label for="<?php echo $group->field; ?>"><input class = 'group-check' type="checkbox" dtype="<?php echo $group->field; ?>" id="<?php echo $group->field; ?>" value="<?php echo $group->field; ?>" checked></label><span></div>
			<h4 class="toggle"><?php echo $group->column_name; ?></h4>
			<div class="checkbox-group" field="<?php echo $group->field; ?>">
				<ul class="checklist-a">
					<?php foreach ($group->data as $box) { ?>
						<li><label for="cl<?php echo $i; ?>"><input type="checkbox" id="cl<?php echo $i++; ?>" value="<?php echo $box->value; ?>" dtype="<?php echo $group->field; ?>" checked><span><?php echo $box->name; ?></span></label>
						</li>
					<?php } ?>
				</ul>
			</div>

		<?php } ?>
	</div>
	<script>
		$('input[type="checkbox"]:not(".no-js")').each(function (i) {
			if ($(this).is('[checked]')) {
				$(this).prop('checked', true).parent('label').addClass('active');
			} else {
				$(this).prop('checked', false).removeAttr('checked');
			}
			$(this).after('<div class="input"></div>').addClass('hidden').attr('aria-hidden', true).on('click', function (e) {

				$(this).parent('label').toggleClass('active');
				if($(this).hasClass('group-check')){
					isChecked = $(this).is(":checked");
					dtype = $(this).attr('dtype');
					$(this).closest('.accordion-a').find(`input[dtype="${dtype}"]`).each( function() {
						if (isChecked == true){
							$(this).prop('checked', true).parent('label').addClass('active');
						} else {
							$(this).prop('checked', false).removeAttr('checked').parent('label').removeClass('active');
						}
					});
				}
			});
		});
		$(document).ready(function() {

			$('.accordion-a').semanticAccordion().children(':header.toggle').next().show();
		});
	</script>
</aside>
<script>
document.addEventListener('DOMContentLoaded', function () {
	//JS.loadLeft($('.left-side'), 'layout/getLeftColumn?type=maintenance', 'maintenance');
	JS.loadRight($('.right-side'), 'layout/getRightColumn?type=maintenance', 'maintenance');
});
</script>
