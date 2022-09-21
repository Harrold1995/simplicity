
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
									<?=isset($tenantName) ? $tenantName : '';?>
									<?=isset($leaseStart) ? $leaseStart : '';?> 
									.</strong>
								</li>

			<?php endif;   ?>


		 
							
							<!-- < ?php if(!isset($notes)){
                              
								//echo "<tr><td class='text-center' style='color: #f37ce4;font-size: large;'><strong>No transactions for  .</strong></td></tr>";
								
						 } ?> -->
</ul>

						<!-- <li>
							<header>
								<h3>Please note that.....</h3>
								<ul>
									<li>Mary Jane </li>
									<li>1/1/2018</li>
								</ul>
							</header>
							<p>< ?=isset($notes) ? $note->note : '';?></p>
							<ul class="list-square">
								<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
								<li><a href="./"><i class="icon-notes"></i> <span>Notes</span></a></li>
								<li><a href="./" class="print"><i class="icon-print"></i> <span>Print</span></a></li>
							</ul>
						</li>


						<li>
							<header>
								<h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum</h3>
								<ul>
									<li>Mary Jane </li>
									<li>1/1/2018</li>
								</ul>
							</header>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum</p>
							<ul class="list-square">
								<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
								<li><a href="./"><i class="icon-notes"></i> <span>Notes</span></a></li>
								<li><a href="./" class="print"><i class="icon-print"></i> <span>Print</span></a></li>
							</ul>
						</li>
						<li>
							<header>
								<h3>Please note that.....</h3>
								<ul>
									<li>Mary Jane </li>
									<li>1/1/2018</li>
								</ul>
							</header>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum</p>
							<ul class="list-square">
								<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
								<li><a href="./"><i class="icon-notes"></i> <span>Notes</span></a></li>
								<li><a href="./" class="print"><i class="icon-print"></i> <span>Print</span></a></li>
							</ul>
						</li>
						<li>
							<header>
								<h3>Please note that.....</h3>
								<ul>
									<li>Mary Jane </li>
									<li>1/1/2018</li>
								</ul>
							</header>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum</p>
							<ul class="list-square">
								<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
								<li><a href="./"><i class="icon-notes"></i> <span>Notes</span></a></li>
								<li><a href="./" class="print"><i class="icon-print"></i> <span>Print</span></a></li>
							</ul>
						</li>
						<li>
							<header>
								<h3>Please note that.....</h3>
								<ul>
									<li>Mary Jane </li>
									<li>1/1/2018</li>
								</ul>
							</header>
							<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum</p>
							<ul class="list-square">
								<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
								<li><a href="./"><i class="icon-notes"></i> <span>Notes</span></a></li>
								<li><a href="./" class="print"><i class="icon-print"></i> <span>Print</span></a></li>
							</ul>
						</li>
					</ul> -->
