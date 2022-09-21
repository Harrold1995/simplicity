<?php
	foreach($messages as $message) { ?>
		<div class="message-wrapper <?php echo $message->profile_id == $ticket->tenant ? 'tenant' : ''?>">
			<div class="message-header">
				<div><?php echo $message->name?></div>
				<div><?php echo $message->date?> <?php echo $message->internal ? '<i class="fas fa-eye-slash ml-4"></i>' : ''?>  </div>
			</div>
			<div class="message-body">
				<?php echo $message->text?>
			</div>
		</div>
<?php	}
?>
