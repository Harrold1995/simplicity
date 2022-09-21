
          <ul id="notesprint" class="list-notes"  >
			<?php if($notes): ?>
					<?php foreach($notes as $note): ?>

						            <li>
										<header>
											<!--<h3>Please note that.....</h3>-->
											<h3><?=isset($note->title) ? $note->title : '';?></h3>
											<ul>
												<li><?=isset($note->name) ? $note->name : '';?></li>
												<!--<li><  	?=isset($note->profile_id) ? $note->profile_id : '';?></li>-->
												<li><?=isset($note->note_date) ? $note->note_date : '';?></li>
											</ul>
										</header>
										<p><?=isset($note->note) ? $note->note : '';?></p>
										<ul class="list-square">
											<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
											<li><a href="./"><i class="icon-notes"></i> <span>Notes</span></a></li>
											<li><a href="./" class="print"><i class="icon-print"></i> <span>Print</span></a></li>
										</ul>
									</li>


			        <?php endforeach; ?>
			<?php else :   ?>
								<!-- look at line 1 of of each file for to find the following variables -->
								<li class='text-center' style='color: #f37ce4;font-size: large;'><strong>No notes for 
									<?=isset($propertyName) ? $propertyName : '';?>
									<?=isset($unitName) ? $unitName : '';?> 
									<?=isset($accountName) ? $accountName : '';?>
									.</strong>
								</li>

			<?php endif;   ?>
