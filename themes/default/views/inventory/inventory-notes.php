
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
									<?=isset($inventoryName) ? $inventoryName : '';?>
									.</strong>
								</li>

			<?php endif;   ?>
</ul>







<!--<div class="column-body pb-3 no-overflow">
  <div class="row">
    <div class="col-12">
      <table class="table table-a">
        <thead class="thead-light">
          <tr>
            <th scope="col">Notes</th>
          </tr>
        </thead>
        <tbody>

          <tr>
            <td>
              <div class="d-flex bd-highlight">
                <div class="p-2 w-100 bd-highlight">
                  <h5 class="accountNavColor">Please note that</h5>
                  <p class="copyText">Hashem is 1!</p>
                </div>
                <p style="cursor: pointer; height: 5px;"><i class="fas fa-paste p-2 flex-shrink-1 bd-highlight"></i></p>
                <p class="clipboard2" style="cursor: pointer; height: 5px;">
                  <i class="fas fa-clipboard-list p-2 flex-shrink-1 bd-highlight clipboard2"></i></p>
                <p style="cursor: pointer; height: 5px;">
                  <i onClick="window.print()" class="fas fa-print p-2 flex-shrink-1 bd-highlight"></i></p>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="d-flex bd-highlight">
                <div class="p-2 w-100 bd-highlight">
                  <h5 class="accountNavColor">Please note that</h5>
                  <p>inventory blah blah blah....!</p>
                </div>
                <p style="cursor: pointer; height: 5px;"><i class="fas fa-paste p-2 flex-shrink-1 bd-highlight"></i></p>
                <p id="clipboard" style="cursor: pointer; height: 5px;">
                  <i class="fas fa-clipboard-list p-2 flex-shrink-1 bd-highlight"></i></p>
                <p style="cursor: pointer; height: 5px;">
                  <i onClick="window.print()" class="fas fa-print p-2 flex-shrink-1 bd-highlight"></i></p>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="d-flex bd-highlight">
                <div class="p-2 w-100 bd-highlight">
                  <h5 class="accountNavColor">Please note that</h5>
                  <p>The world is .....</p>
                </div>
                <p style="cursor: pointer; height: 5px;"><i class="fas fa-paste p-2 flex-shrink-1 bd-highlight"></i></p>
                <p id="clipboard" style="cursor: pointer; height: 5px;">
                  <i class="fas fa-clipboard-list p-2 flex-shrink-1 bd-highlight"></i></p>
                <p style="cursor: pointer; height: 5px;">
                  <i onClick="window.print()" class="fas fa-print p-2 flex-shrink-1 bd-highlight"></i></p>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="d-flex bd-highlight">
                <div class="p-2 w-100 bd-highlight">
                  <h5 class="accountNavColor">Please note that</h5>
                  <p>chaim inventory</p>
                </div>
                <p style="cursor: pointer; height: 5px;"><i class="fas fa-paste p-2 flex-shrink-1 bd-highlight"></i></p>
                <p id="clipboard" style="cursor: pointer; height: 5px;">
                  <i class="fas fa-clipboard-list p-2 flex-shrink-1 bd-highlight"></i></p>
                <i style="cursor: pointer;height: 5px;" onClick="window.print()" class="fas fa-print p-2 flex-shrink-1 bd-highlight"></i>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="d-flex bd-highlight">
                <div class="p-2 w-100 bd-highlight">
                  <h5 class="accountNavColor">Please note that</h5>
                  <p>Hashem is 1!</p>
                </div>
                <p style="cursor: pointer; height: 5px;"><i class="fas fa-paste p-2 flex-shrink-1 bd-highlight"></i></p>
                <p id="clipboard" style="cursor: pointer; height: 5px;">
                  <i class="fas fa-clipboard-list p-2 flex-shrink-1 bd-highlight"></i></p>
                <p style="cursor: pointer; height: 5px;">
                  <i onClick="window.print()" class="fas fa-print p-2 flex-shrink-1 bd-highlight"></i></p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>-->