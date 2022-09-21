
<div class="modal fade tenant-modal" id="tenantModal" tabindex="-1" role="dialog" main-id=<?= isset($tenant) && isset($tenant->id) ? $tenant->id : '-1' ?> type="tenant" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="tenant">
                  
				  	<header class="modal-header"></header>
				<header style="z-index: 17;">
					<h2>Tenant</h2>
					<nav>
						<ul>
								<li><a href="#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
								<li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>				
                                <li><?= isset($tenant) ? '<a href="tenant/deleteTenant/'.$tenant->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li><a href="./"><i class="icon-envelope-outline" aria-hidden="true"></i> <span>Envelope</span></a></li>
							<li><a href="./"><i class="icon-brain" aria-hidden="true"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents" aria-hidden="true"></i> <span>Copy</span></a></li>
							<li><a href="./"><i class="icon-paperclip" aria-hidden="true"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print" aria-hidden="true"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>
				<section class="c" style="z-index: 16;">
					<div class="double g">
						<div>
							<p>
								<label for="tenant[first_name]">First Name</label>
                                <input type="text" value="<?= isset($tenant) && isset($tenant->first_name) ? $tenant->first_name : '' ?>" class="form-control" name="tenant[first_name]" id="first_name" placeholder="">
							</p>
							<p>
								<label for="tenant[mi]">MI</label>
								<input type="text" value="<?= isset($tenant) && isset($tenant->mi) ? $tenant->mi : '' ?>" class="form-control" name="tenant[mi]" id="mi" placeholder="">
							</p>
							<p>
								<label for="tenant[last_name]">Last Name</label>
                                <input type="text" value="<?= isset($tenant) && isset($tenant->last_name) ? $tenant->last_name : '' ?>" class="form-control" name="tenant[last_name]" id="last_name" placeholder="">
							</p>
							<p>
								<label for="tenant[email]"><i class="icon-envelope" aria-hidden="true"></i> <span class="hidden">Email</span></label>
								<input type="text" value="<?= isset($tenant) && isset($tenant->email) ? $tenant->email : '' ?>" class="form-control" name="tenant[email]" id="email" placeholder="">
							</p>
							<p>
								<label for="tenant[phone]"><i class="icon-phone" aria-hidden="true"></i> <span class="hidden">Phone</span></label>
								<input type="text" value="<?= isset($tenant) && isset($tenant->phone)  ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($tenant->phone)), 2) : '';?>" class="form-control" name="tenant[phone]" id="phone" placeholder="">
							</p>
						</div>
						<div>
							<!-- <div class="double"> -->
								<p>
									<label for="address_line_1">Address</label>
									<input type="text" value="<?= isset($tenant) && isset($tenant->address_line_1) ? $tenant->address_line_1 . ' '. $tenant->address_line_2: '' ?>" class="form-control" name="address_line_1" id="address_line_1" placeholder="">
								</p>
								<!-- <p>
									<label for="address_line_1"></label>
									<input type="text" value="< ?= isset($tenant) && isset($tenant->address_line_1) ? $tenant->address_line_1 . ' '. $tenant->address_line_2: '' ?>" class="form-control" name="address_line_1" id="address_line_1" placeholder="">
								</p>
							</div> -->
							<div class="triple a">
								<p>
									<label for="tenant[city]">City</label>
									<input type="text" value="<?= isset($tenant) && isset($tenant->city) ? $tenant->city : '' ?>" class="form-control" name="tenant[city]" id="city" placeholder="">
								</p>
								<p>
                                    <label for="tenant[state]">State</label>
                                    <input type="text" value="<?= isset($tenant) && isset($tenant->state) ? $tenant->state : '' ?>" class="form-control" name="tenant[state]" id="state" placeholder="">
									<!-- <span class="select"><select id="fib" name="fib">
										<option>NY</option>
										<option>Position #1</option>
										<option>Position #2</option>
										<option>Position #3</option>
										<option>Position #4</option>
										<option>Position #5</option>
									</select></span> -->
								</p>
								<p>
									<label for="tenant[area_code]">Zip</label>
									<input type="text" value="<?= isset($tenant) && isset($tenant->area_code) ? $tenant->area_code : '' ?>" class="form-control" name="tenant[area_code]" id="area_code" placeholder="">
								</p>
							</div>
							<div class="double">
								<p>
									<label for="ss">SS#</label>
									<input type="text" value="<?= isset($tenant) && isset($tenant->ss) ? $tenant->ss : '' ?>" class="form-control" name="ss" id="ss" placeholder="">
								</p>
								<p>
									<label for="tenant[status]">Status</label>
									<input type="text" value="<?= isset($tenant) && isset($tenant->status) ? $tenant->status : '' ?>" class="form-control" name="tenant[status]" id="status" placeholder="">
								</p>
							</div>
							<p>
								<label for="dob">DOB</label>
								<input type="text" value="<?= isset($tenant) && isset($tenant->dob) ? $tenant->dob : '' ?>" class="form-control" name="dob" id="dob" placeholder="">
							</p>
							<p>
								<label for="fig">Preferred contact Method</label>
								<input type="text" id="fig" name="fig" value="Email">
							</p>
						</div>
					</div>	
					<ul class="check-a text-right m5">
						<li><label for="fih" class="checkbox active"><input type="checkbox" id="fih" name="fih" checked="" class="hidden" aria-hidden="true"><div class="input"></div> Email Statements</label></li>
						<li><label for="fii" class="active checkbox"><input type="checkbox" id="fii" name="fii" checked="" class="hidden" aria-hidden="true"><div class="input"></div> Mail Statements</label></li>
						<li><label for="fij" class="active checkbox"><input type="checkbox" id="fij" name="fij" checked="" class="hidden" aria-hidden="true"><div class="input"></div> Text Notifications</label></li>
					</ul>
					<p>
						<label for="memo">Memo:</label>
						<input type="text" value="<?= isset($tenant) && isset($tenant->memo) ? $tenant->memo : '' ?>" class="form-control" name="memo" id="memo" placeholder="">
					</p>
					<div class="submit">
						<p class="input-file">
							<label for="fil"><input type="file" id="fil" name="fil"> <span class="img"><img src="http://placehold.it/103" alt="Placeholder" width="103" height="103"></span> <span>Attach Photo</span></label>
						</p>
						<ul class="check-a a">
							<li><label for="active" class="checkbox <?= isset($tenant) && ($tenant->active == 0) ? '' : 'active' ?>"><input type="hidden" name="active" value="0" /><input type="checkbox"  value="1" <?= isset($tenant) && ($tenant->active == 0) ? '' : 'checked' ?> id="active" name="active" class="hidden" aria-hidden="true"><div class="input"></div> Active?</label></li>
						</ul>
					</div>
				</section>
				<ul class="list-horizontal" style="z-index: 15;">
					<li class="active"><a href="./">Contacts</a></li>
					<li><a href="./">Adresses</a></li>
					<li><a href="./">Documents</a></li>
					<li><a href="./">Portal login</a></li>
				</ul>
			<div id="DataTables_Table_7_wrapper" class="dataTables_wrapper no-footer has-table-c mobile-hide b text-center">
				<div class="dataTables_scroll">
					<div class="dataTables_scrollHead">
						<div class="dataTables_scrollHeadInner">
							<table class="table-c b text-center mobile-hide dataTable no-footer" role="grid"><thead>
								<tr role="row">
								<!--<th width="7%" class="text-center">Primary</th>-->
									<th width="7%" class="text-center">First Name</th>
									<th width="7%" class="text-center">Last Name</th>
									<th width="7%" class="text-center">Relationship</th>
									<th width="7%" class="text-center">Home Phone</th>
									<th width="7%" class="text-center">Cell</th>
									<th width="7%" class="text-center">Work Phone</th>
									<th width="7%" class="text-center">Ext</th>
									<th width="7%" class="text-center">Email</th>
									<th width="7%" class="text-center link-icon"><a href="#"><i class="icon-plus-circle addTenantButton table-button"></i> <span>Add</span></a></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
					<div class="dataTables_scrollBody" >
						<div class="table-wrapper"  tabindex="-1" style="max-height: 180px;">
							<table class="table-c  text-center mobile-hide dataTable no-footer" id="DataTables_Table_7" role="grid" aria-describedby="DataTables_Table_7_info">
								<thead>
								</thead>
								<tbody>						
								
						<tr role="row">
                            <td width="7%" class="text-center">
                                <span class="input-amount">
                                    <label for="tbh"></label>
                                    <input type="text" id="tbh" name="tbh" value="first name">
                                </span>
                            </td>
                            <td width="7%" class="text-center">
                                <span class="input-amount">
                                    <label for="tbh"></label>
                                    <input type="text" id="tbh" name="tbh" value="last name">
                                </span>
                            </td>
                            <td width="7%" class="text-center">
                                <span class="input-amount">
                                    <label for="tbh"></label>
                                    <input type="text" id="tbh" name="tbh" value="relationship">
                                </span>
                            </td>
                            <td width="7%" class="text-center">
                                <span class="input-amount">
                                    <label for="tbh"></label>
                                    <input type="text" id="tbh" name="tbh" value="home phone">
                                </span>
                            </td>
                            <td width="7%" class="text-center">
                                <span class="input-amount">
                                    <label for="tbh"></label>
                                    <input type="text" id="tbh" name="tbh" value="cell">
                                </span>
                            </td>
                            <td width="7%" class="text-center">
                                <span class="input-amount">
                                    <label for="tbh"></label>
                                    <input type="text" id="tbh" name="tbh" value="work phone">
                                </span>
                            </td>
                            <td width="7%" class="text-center">
                                <span class="input-amount">
                                    <label for="tbh"></label>
                                    <input type="text" id="tbh" name="tbh" value="ext">
                                </span>
                            </td>
                            <td width="7%" class="text-center">
                                <span class="input-amount">
                                    <label for="tbh"></label>
                                    <input type="text" id="tbh" name="tbh" value="email">
                                </span>
                            </td>
                                <td width="7%" class="dt-add text-center">
                                    <a href='#' class="addToTable" source="tableapi/getVendorsRow"><i class="fas fa-plus-circle"></i></a>
                                </td>
                          </tr>
								
						<?php  if (isset($contacts)){
								foreach ($contacts as $contact) {?>					
								
									<tr role="row">
											<!--<td width="7%" class="text-center">Primary</td>-->
											<td width="7%" class="text-center"><?= isset($contact) && isset($contact->first_name) ? $contact->first_name : '' ?></td>
											<td width="7%" class="text-center"><?= isset($contact) && isset($contact->last_name) ? $contact->last_name : '' ?></td>
											<td width="7%" class="text-center"><?= isset($contact) && isset($contact->relation) ? $contact->relation : '' ?></td>
											<td width="7%" class="text-center"><?= isset($contact) && isset($contact->home) ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($contact->home)), 2) : '' ?></td>
											<td width="7%" class="text-center"><?= isset($contact) && isset($contact->cell)  ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($contact->cell)), 2) : '';?></td>
											<td width="7%" class="text-center"><?= isset($contact) && isset($contact->work)  ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($contact->work)), 2) : '';?></td>
											<td width="7%" class="text-center"><?= isset($contact) && isset($contact->ext) ? $contact->ext : '' ?></td>
											<td width="7%" class="text-center" class="email" href="mailto:Hannah123@gmail.com"><?= isset($contact) && isset($contact->email) ? $contact->email : '' ?></td>
											<td width="7%" class="text-center" class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
									</tr>
                                <?php }} ?>
                                <!-- < ?php if(!isset($contacts)){
                                    echo "<tr role='row'><td class='text-center' style='color: #f37ce4;font-size: large;'><strong>No contacts for this vendor.</strong></td></tr>";
                                }?> -->
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
				<footer class="last-child" style="z-index: 13;">
					<ul class="list-btn">
						<li><button type="submit" after="mnew">Save &amp; New</button></li>
						<li><button type="submit"  after="mclose">Save &amp; Close</button></li>
						<li><button type="button">Duplicate</button></li>
						<li><button type="button">Cancel</button></li>
					</ul>
					<ul>
						<li>Last Modified 12:22:31 pm 1/10/2018</li>
						<li>Last Modified by <a href="./">User</a></li>
					</ul>
                </footer>
                </form>
        </div>
	</div>
    </div>
</div>

<script >

$('input[type="checkbox"], input[type="radio"]').click(function(){
		if($(this).parent().hasClass('radio')) { 
				$(this).parents('p, ul:first').find('label').removeClass('active');
				}
				$(this).parent('label').toggleClass('active'); 
});



</script>


<!--
<tr role="row" class="even">
							<td><div class="shadow" style="width: 1178px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="odd">
							<td><div class="shadow" style="width: 1178px;"></div></td>
							<td>
								<span class="w90 has-input">
									<label for="tbm" class="hidden">Label</label>
									<input type="text" id="tbm" name="tbm" value="Cathy">
								</span>
							</td>
							<td>
								<span class="w120 has-input">
									<label for="tbn" class="hidden">Label</label>
									<input type="text" id="tbn" name="tbn" value="Newlin">
								</span>
							</td>
							<td>
								<span class="w90 has-input">
									<label for="tbo" class="hidden">Label</label>
									<input type="text" id="tbo" name="tbo" value="BFF">
								</span>
							</td>
							<td>
								<span class="w120 has-input">
									<label for="tbp" class="hidden">Label</label>
									<input type="text" id="tbp" name="tbp" value="347-575-6952">
								</span>
							</td>
							<td>
								<span class="w120 has-input">
									<label for="tbq" class="hidden">Label</label>
									<input type="text" id="tbq" name="tbq" value="347-575-6952">
								</span>
							</td>
							<td>
								<span class="w120 has-input">
									<label for="tbr" class="hidden">Label</label>
									<input type="text" id="tbr" name="tbr" value="347-575-6952">
								</span>
							</td>
							<td>
								<span class="w180 has-input">
									<label for="tbs" class="hidden">Label</label>
									<input type="text" id="tbs" name="tbs" value="cathy12@gmail.com">
								</span>
							</td>
							<td></td>
						</tr><tr role="row" class="even">
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1178px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="odd">
							<td><div class="shadow" style="width: 1178px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="even">
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1178px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="odd">
							<td><div class="shadow" style="width: 1178px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="even">
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1178px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="odd">
							<td><div class="shadow" style="width: 1178px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="even">
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1178px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="odd">
							<td><div class="shadow" style="width: 1178px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="even">
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1178px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="odd">
							<td><div class="shadow" style="width: 1178px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr><tr role="row" class="even">
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1178px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						<tr>
							<td><div class="shadow" style="width: 1080px;"></div></td>
							<td>
								<span class="w90 has-input">
									<label for="tbm" class="hidden">Label</label>
									<input type="text" id="tbm" name="tbm" value="Cathy">
								</span>
							</td>
							<td>
								<span class="w120 has-input">
									<label for="tbn" class="hidden">Label</label>
									<input type="text" id="tbn" name="tbn" value="Newlin">
								</span>
							</td>
							<td>
								<span class="w90 has-input">
									<label for="tbo" class="hidden">Label</label>
									<input type="text" id="tbo" name="tbo" value="BFF">
								</span>
							</td>
							<td>
								<span class="w120 has-input">
									<label for="tbp" class="hidden">Label</label>
									<input type="text" id="tbp" name="tbp" value="347-575-6952">
								</span>
							</td>
							<td>
								<span class="w120 has-input">
									<label for="tbq" class="hidden">Label</label>
									<input type="text" id="tbq" name="tbq" value="347-575-6952">
								</span>
							</td>
							<td>
								<span class="w120 has-input">
									<label for="tbr" class="hidden">Label</label>
									<input type="text" id="tbr" name="tbr" value="347-575-6952">
								</span>
							</td>
							<td>
								<span class="w180 has-input">
									<label for="tbs" class="hidden">Label</label>
									<input type="text" id="tbs" name="tbs" value="cathy12@gmail.com">
								</span>
							</td>
							<td></td>
						</tr>
						<tr>
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1080px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						<tr>
							<td><div class="shadow" style="width: 1080px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						<tr>
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1080px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						<tr>
							<td><div class="shadow" style="width: 1080px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						<tr>
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1080px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						<tr>
							<td><div class="shadow" style="width: 1080px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						<tr>
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1080px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						<tr>
							<td><div class="shadow" style="width: 1080px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						<tr>
							<td><i class="icon-check" aria-hidden="true"></i> <span class="hidden">Yes</span><div class="shadow" style="width: 1080px;"></div></td>
							<td>Hanna</td>
							<td>Cunningham</td>
							<td>Self</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Hannah123@gmail.com">Hannah123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						<tr>
							<td><div class="shadow" style="width: 1080px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>
						
						<tr>
							<td><div class="shadow" style="width: 1080px;"></div></td>
							<td>Allen</td>
							<td>Cunningham</td>
							<td>Spouse</td>
							<td>718-388-4536</td>
							<td>347-888-3333</td>
							<td>212-562-3652</td>
							<td class="email" href="mailto:Allen123@gmail.com">Allen123@gmail.com</td>
							<td class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
						</tr>-->