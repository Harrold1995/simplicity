<div class="modal fade maintenance-modal" tabindex="-1" role="dialog" aria-hidden="true" type = 'Maintenance-Ticket' data-id ='<?php echo isset($ticket->id) ? $ticket->id : 0; ?>'>
	<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/html5lightbox.js"></script>
	<div class="modal-dialog modal-dialog-centered modal-lg ui-draggable" role="document">
		<div class="modal-content">
			<!--<div id="infoMessage"><?php echo $message; ?></div>-->
			<form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type = 'maintenance'>

				<div class="modal-header modal-h">
					<ul class="list-plain inline text-uppercase" style="z-index: 11;">
						<li><?php if(isset ($ticket)) { ?>
							<label for="ch1" <?php echo (isset($ticket) && $ticket->status == 1) ? 'class="active"' : ''; ?>><i class="far fa-check-square"></i><i class="far fa-square"></i> Mark as complete<input type="checkbox" name="complete" id="ch1" <?php echo (isset($ticket) && $ticket->status == 1) ? 'checked' : ''; ?>></label>
							<?php } ?>
							<label for="ch2" <?php echo (isset($ticket) && $ticket->attention == 1) ? 'class="active"' : ''; ?>><i class="far fa-check-square"></i><i class="far fa-square"></i> Needs Attention<input type="checkbox" name="attention" id="ch2" <?php echo (isset($ticket) && $ticket->attention == 1) ? 'checked' : ''; ?>></label></li>

						<li><a class="actionsLink" href="#actions">Actions...</a>
							<ul class="list-sub" style="display: none">
								<li id = 'deleteTicket' data-id ='<?php echo isset($ticket->id) ? $ticket->id : 0; ?>' class="br overlay-l"  ><a href="#">Delete</a></li>
							</ul>
						</li>
					</ul>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
				<div class="box-inset">

					<header class="heading-b" style="z-index: 10;">
						<div>
							<?php if(isset ($ticket)) { ?>
								<h2>Ticket# : T<?php echo str_pad($ticket->id, 7, "0", STR_PAD_LEFT)?></h2>
							<?php } ?>
							<p class="m10" style="z-index: 8;">
								<h3 class="label" style="z-index: 5;">Title</h3>
								<input type="text" name="title" class="plain" placeholder="Ticket title" value="<?php echo (isset($ticket) && $ticket->title) ? $ticket->title : ''; ?>"/>
							</p>
						</div>
						<ul class="list-aside">
							<li><span>Due on </span> <i class="ml-2 far fa-calendar-alt"></i> <input data-toggle="datepicker" id="dueDate" name="due_date" value="<?= isset($ticket) && isset($ticket->due_date) ? $ticket->due_date : date(time()) ?>" name="due_date" autocomplete="off">
							</li>
						</ul>
						<ul class="list-aside">
							<li><span>Building</span><div><select id="propertyselect" name="property"></select></div></li>
							<li><span>Unit</span><div><select id="unitselect" name="unit"></select></div></li>
							<li><span>Tenant</span><div><select id="tenantselect" name="tenant"></select></div></li>
						</ul>
					</header>
					<ul class="list-icons" style="z-index: 9;">
						<!--li>Assigned to <div><select id="assigned_to" name="assigned_to"></select></div></li-->
						<li>Assigned to <select <?php echo (isset($ticket) ) ? 'onchange="updateStatus(event.target);"' : ''; ?> id="assigned_to" name="assigned_to"><?php foreach($employees as $employee) { echo'<option value="'.$employee->id.'" '.(isset($ticket) && $ticket->assigned_to == $employee->id ? 'selected' : '').'>'.$employee->first_name.' '.$employee->last_name.'</option>';}?></select></li>
						<li>Category <select id="category" name="category"><?php foreach($categories as $cat) { echo'<option value="'.$cat->id.'" '.(isset($ticket) && $ticket->category == $cat->id ? 'selected' : '').'>'.$cat->name.'</option>';}?></select></li>
						<li>Status <select <?php echo (isset($ticket) ) ? 'onchange="updateStatus(event.target);"' : ''; ?> id="status" name="status"><?php foreach($statuses as $status) { echo'<option value="'.$status->id.'" '.(isset($ticket) && $ticket->status == $status->id ? 'selected' : '').'>'.$status->name.'</option>';}?></select></li>
						<li>Priority <select id="priority" name="priority">
							<option value='0' <?php echo (isset($ticket) && $ticket->priority == 0) ? 'selected' : ''; ?>>Low</option><option value='1' <?php echo (! isset($ticket) || (isset($ticket) && $ticket->priority == 1)) ? 'selected' : ''; ?>>Normal</option><option value='2' <?php echo (isset($ticket) && $ticket->priority == 2) ? 'selected' : ''; ?>>High</option></select></li>
						<li>Tags <input class="multiple-select" name="tags" value="<?php echo isset($ticket) && $ticket->tags ? $ticket->tags : ''; ?>"/></li>
					</ul>
					<p class="m10" style="z-index: 8;">
						<h3 class="label" style="z-index: 5;">Description</h3>
						<textarea id="description" name="description" class="plain" style="min-height: 120px;" placeholder="Ticket description"><?php echo (isset($ticket) && $ticket->description) ? $ticket->description : ''; ?></textarea>
					</p>

					<h3 class="label" style="z-index: 5;">Attachments</h3>
					<div class="attachments-list">
						<?php foreach($attachments as $file) {
							$extension = strtolower(pathinfo($file->url, PATHINFO_EXTENSION));
							$isimage = in_array($extension, ['png', 'jpg', 'gif', 'jpeg']);?>
							<div class="attachment-wrapper">
								<a href="<?php echo base_url().$file->url?>" class="html5lightbox" data-group="ticket<?php echo $ticket->id?>" title="Ticket attachment">
									<img src="<?php echo $isimage ? base_url().$file->url : base_url().'themes/default/assets/images/video_placeholder.jpg'?>">
								</a>
								<i class="fas fa-times-circle" id="<?php echo $file->id?>"></i>
							</div>
						<?php } ?>
					</div>
					<ul class="list-plain" style="z-index: 4;">
						<li><label for="upload"><i aria-hidden="true" class="icon-plus-circle-outline"></i><input accept="image/*,video/*" multiple="true" type="file" id="upload" style="display: none;"/> Upload your attachment</label></li>
					</ul>

					<?php if(isset ($ticket)) { ?>
					<p style="z-index: 2;">
						<h3 class="label" style="z-index: 5;">Messages</h3>
						<div class="message-block"></div>
						<textarea id="mmessage" name="message" placeholder="Your message"></textarea>
					</p>
					<?php } else { ?>
					<div class="m10" style="z-index: 8;">
					<h3 class="label" style="z-index: 5;">Messages</h3>
					<textarea id="fqa" name="message" class="plain" placeholder="Initial message"></textarea>
					<label for="internal"><input type="checkbox" name="internal" id="internal"/> Internal</label>
					</div>
					<?php } ?>

				</div>

				<div class="modal-footer">
					<?php if(isset ($ticket)) { ?>
						<p class="submit last-child" style="z-index: 1;flex:auto">
							<button type="message" class="internal">Send as internal</button>
							<button type="message">Send</button>
						</p>
					<?php } ?>
					<p class="submit last-child" style="z-index: 1">
						<button class ='submitAttachements' type="submit" after="mclose">SAVE</button>

					</p>

				</div>

			</form>
		</div>
	</div>

	<script>
		var statuses = <?php echo json_encode($statuses); ?>;
		var assigned_to = <?php echo isset($ticket) && $ticket->assigned_to ? $ticket->assigned_to : 1; ?>;
		function loadMessages(ticket_id) {
			//const ticket_id = $(this).closest('.modal').attr('data-id');
			block = $('.modal[data-id="<?php echo isset($ticket) && $ticket->id ? $ticket->id : 0?>"]').find('.message-block');
			$(block).load('maintenance/getMessages/<?php echo isset($ticket) && $ticket->id ? $ticket->id : 0?>');
		}

		function updateStatus(item){
			console.log(item);
				intval = $(item).val();
				changedField = $(item).attr('id');
				ticket = <?php echo isset($ticket->id)? $ticket->id :'null'; ?>;
				tenant = <?php echo isset($ticket) && $ticket->tenant || $tenant_id ? ($tenant_id ? $tenant_id : $ticket->tenant) : "null";?> ;
				owner = <?php echo isset($ticket) && $ticket->owner ? $ticket->owner : "null";?> ;
				actionText = '';
				
				newValue ='';
				if (changedField == 'status' ){
					//newValue = statuses[intval-1]['name'];
					newValue = $(item).children(':selected').text();
					actionText = `Ticket #${ticket}'s ${changedField} was updated to ${newValue}`;
				} 
				 else if (changedField == 'assigned_to'){
/* 					newValue = $(item).closest('li').find('input[type=hidden]').attr('text');
					intval = $(item).closest('li').find('input[type=hidden]').val(); */
					newValue = $(item).children(':selected').text();
					actionText = `Ticket #${ticket}' was assigned to you`;
				} 
				
				console.log(intval);

				



				
				$.post( JS.baseUrl + "/maintenance/instantUpdate/<?php echo $ticket->id ?>", { changedField: changedField, newValue:intval}, function( result ) {
					if (result) {
						inputOptions = [{
									text: 'Email staff member who is assigned to this task.',
									value: '1',
								}];
						if (tenant !=null && changedField == 'status'){ 
							var obj = {}; 
							obj['text'] = 'Send an email notifiaction to the tenant';
							obj['value'] = 2;
							inputOptions.push(obj);
						}

						if (owner !=null){ 
							var obj = {}; 
							obj['text'] = 'Send an email notifiaction to the property owner';
							obj['value'] = 3;
							inputOptions.push(obj);
						}

							bootbox.prompt({
								title: "Whom do you want to alert about the change?",
								value: ['1'],
								inputType: 'checkbox',
								inputOptions: inputOptions,
								callback: function (result) {
									//todo send emails based on choice
									console.log({result, ticket, tenant});

                                  $.post( JS.baseUrl + "/maintenance/updateTicketEmail/"+ticket, { result: JSON.stringify(result), tenant: tenant, action: actionText}, function( result ) {
										if (result) {alert('sent');};
								    }); 
							    }
							});

							$(item).closest('.modal').removeClass('changed');
							//alert('do you want to alert tenant about the status change?');
						 
					}
				});
				
			}

		$(document).ready(function () {
			var fileCounter = 0;
			var filelist = [];
			function saveFilelist(filelist) {
				console.log(filelist);
				$('.attachments-list').data('files', filelist);
				console.log($('.attachments-list').data('files'));
			}
			$('#upload').change(function(){
				const files = $(this).prop('files');
				console.log();
				for (let i = 0; i < files.length; i++) {
					let file = files.item(i);
					$('.attachments-list').append('<div class="attachment-wrapper"><img src="'+URL.createObjectURL(file)+'"/><i class="fas fa-times-circle" id="0" newid="'+fileCounter+'"></i></div>');
					filelist.push({id: fileCounter++, file: file});
				}
				saveFilelist(filelist);
			});
			$('.attachments-list').on('click', 'i', function(){
				let id = $(this).attr('id');
				let newid = $(this).attr('newid');
				$(this).closest('.attachment-wrapper').remove();
				if(id == 0) {
					filelist = filelist.filter(item => item.id != newid);
					saveFilelist(filelist);
				} else {
					$('.attachments-list').append('<input type="hidden" name="filedelete[]" value="'+id+'">');
				}
			});
			var timeout;
			<?php if(isset($ticket)) { ?> loadMessages(); timeout = setInterval(loadMessages, 20000);<?php } ?>
			$('.actionsLink').click(function(){
				$('.list-sub').toggle();
			});

			$('button.close').click(function(e){
				clearTimeout(timeout);
			});
			
			modal = $('.modal[data-id="<?php echo isset($ticket) && $ticket->id ? $ticket->id : 0?>"]');

			var pselect = $(modal).find('#propertyselect')[0];
			var uselect = $(modal).find('#unitselect')[0];
			var tselect = $(modal).find('#tenantselect')[0];
			//var aselect = '#assigned_to';
			var pparent = $(pselect).parent();
			var uparent = $(uselect).parent();
			var tparent = $(tselect).parent();
			//var aparent = $(aselect).parent();
			//aselect = $(aparent).find('input[type=hidden]')[0];
			pvalue = <?php echo isset($ticket) && $ticket->property || $property_id ? ($property_id ? $property_id :$ticket->property) : 0; ?>;
			uvalue = <?php echo isset($ticket) && $ticket->unit || $unit_id ? ($unit_id ? $unit_id : $ticket->unit) : 0; ?>;

			$(pselect).fastSelect({type: 'property', fastinit: true <?php echo isset($ticket) && $ticket->property || $property_id ? ', default: '.($property_id ? $property_id :$ticket->property) : ''; ?>});
			$(uselect).fastSelect({type: 'unit', filter_key :'property_id', filter_value : pvalue, fastinit: true <?php echo isset($ticket) && $ticket->unit || $unit_id ? ', default: '.($unit_id ? $unit_id : $ticket->unit) : ''; ?>});
			$(tselect).fastSelect({type: 'tenant', filter_key :'unit_id', filter_value : uvalue, fastinit: true <?php echo isset($ticket) && $ticket->tenant || $tenant_id ? ', default: '.($tenant_id ? $tenant_id : $ticket->tenant) : ''; ?>});
			//$(aselect).fastSelect({type: 'profile', fastinit: true, filter_key: 'profile_type_id', filter_value: 4  < ?php echo isset($ticket) && $ticket->assigned_to ? ', default: '.$ticket->assigned_to : ''; ?>});
			pparent.on('change', pselect, function() {
				var value = $($(this).find('input[type="hidden"]')[0]).val();
				if(!value) return;
				uparent.html('<select id="unitselect" name="unit"></select>');
				tparent.html('<select id="tenantselect" name="tenant"></select>');
				uselect = $(modal).find('#unitselect')[0];
			    tselect = $(modal).find('#tenantselect')[0];
				$(uselect).fastSelect({type: 'unit', fastinit: true, filter_key: 'property_id', filter_value: value});
				$(tselect).fastSelect({type: 'tenant', fastinit: true, filter_key: 'prop_id', filter_value: value});
			});

			uparent.on('change', uselect, function() {
				var value = $($(this).find('input[type="hidden"]')[0]).val();
				if(!value) return;
				tparent.html('<select id="tenantselect" name="tenant"></select>');
				tselect = $(modal).find('#tenantselect')[0];
				$(tselect).fastSelect({type: 'tenant', fastinit: true, filter_key: 'unit_id', filter_value: value});
			});

 			/* aparent.on('blur', aselect, function() {
				var value = $(this).closest('div').find('input[type=hidden]').val();
				
				console.log(this);
				console.log(assigned_to);
				console.log(value);
				if(!value) return;
				console.log();
				if(assigned_to != value){
					assigned_to = value;
				    updateStatus(this);
				}

			});  */




 
			
			$('.multiple-select').selectize({
				persist: false,
				maxItems: null,
				valueField: 'id',
				labelField: 'name',
				searchField: 'name',
				options: JS.sdata['mtags'],
				create:function (input, callback){
					$.post( JS.baseUrl + "/maintenance/addTag/", {text: input}, function( result ) {
						if (result) {
							callback({ id: result, name: input });
						}
					});
				}
			});
		});
	</script>
</div>
