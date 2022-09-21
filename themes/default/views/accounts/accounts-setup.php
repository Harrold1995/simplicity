
          <form action="./" method="post" class="module-box">
						<div>
							<h2 class="header-a text-center">General</h2>
							<?php if($locked){echo '<h3 style="text-align: center; color: red;">No modification allowed</h3>';}  ?>
							<ul id="setupprint" class="list-a">
								<li><label for="laa">Account Name:</label> <input type="text" id="laa" name="laa" value="<?php echo $getSingleAccount->name ?>"></li>
								<li><label for="lab">Account type:</label> <input type="text" id="lab" name="lab" value="<?php echo $getSingleAccount->type ?>"></li>
								<li><label for="lac">GL #:</label> <input type="text" id="lac" name="lac" value="<?php echo $getSingleAccount->accno ?>"></li>
								<li><label for="lad">Parent Account:</label> <input type="text" id="lad" name="lad" value="<?php echo $getSingleAccount->parent_id ?>"></li>
								<li><label for="lae">Default Class:</label> <input type="text" id="lae" name="lae" value="<?php echo $getSingleAccount->class ?>"></li>
								<li><label for="laf">Tax line Mapping:</label> <input type="text" id="laf" name="laf" value="<?php echo $getSingleAccount->tax_line_id ?>"></li>
								<li><label for="lag">RPIE line Mapping:</label> <input type="text" id="lag" name="lag" value="<?php echo $getSingleAccount->rpie_line_id ?>"></li>
								<li><label for="lah">Active?:</label> <input type="text" id="lah" name="lah" value="<?php echo $getSingleAccount->active ?>"></li>
								<li><span>Created by:</span> User 1</li>
								<li><span>Created on:</span> 12/18/2017</li>
							</ul>
							<p class="overlay-i size-b"><span class="semi size-up">Description:</span><?php echo $getSingleAccount->description ?></p>
							<p class="submit"><button type="submit">Save changes</button></p>
						</div>
					</form>