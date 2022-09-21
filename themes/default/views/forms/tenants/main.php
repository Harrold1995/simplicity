
<div class="modal fade tenant-modal" id="tenantModal" tabindex="-1" role="dialog" doc-type="profiles" main-id=<?= isset($tenant) && isset($tenant->id) ? $tenant->id : '-1' ?> type="tenant" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="tenant">
                  
				  	<header class="modal-h">
                      <!-- </header> -->
				<!-- <header style="z-index: 17;"> -->
					<h2>Tenant</h2>
					<nav>
						<ul>
							<li><span class="buttons" style=""><span class="min" style=";padding: 8px 20px;cursor: pointer;">_</span></span> </li>
							<li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
							<li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
						</ul>
					</nav>
					<nav>
						<ul>
								<li><a href="#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
								<li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>				
                                <li><?= isset($tenant) ? '<a href="delete/deleteName/'.$tenant->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li class="get_send_email_form"><a href="./"><i class="icon-envelope-outline" aria-hidden="true"></i> <span>Envelope</span></a></li>
							<li><a href="./"><i class="icon-brain" aria-hidden="true"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents" aria-hidden="true"></i> <span>Copy</span></a></li>
							<li class="getDocuments"><a href="#" class="uploadDocument"><i class="icon-paperclip" aria-hidden="true"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print" aria-hidden="true"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>
				<section class="c" style="z-index: 16;">
					<div class="double g">
						<div>
							<p> 
								<label for="tenant[first_name]">First Name</label>
                                <input type="text" value="<?= isset($tenant) && isset($tenant->first_name) ? $tenant->first_name : (isset($tenant) && isset($tenant->name) ? preg_split("/\s+(?=\S*+$)/",$tenant->name)[0] : '') ?>" class="form-control" name="tenant[first_name]" id="first_name" placeholder="">
							</p>
							<p>
								<label for="tenant[mi]">MI</label>
								<input type="text" value="<?= isset($tenant) && isset($tenant->mi) ? $tenant->mi : '' ?>" class="form-control" name="tenant[mi]" id="mi" placeholder="">
							</p>
							<p>
								<label for="tenant[last_name]">Last Name</label>
                                <input type="text" value="<?= isset($tenant) && isset($tenant->last_name) ? $tenant->last_name : isset($tenant) && isset($tenant->name) ? preg_split("/\s+(?=\S*+$)/",$tenant->name)[1] : '' ?>" class="form-control" name="tenant[last_name]" id="last_name" placeholder="">
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
							<div class="double">
								<p style="width: 43%;">
									<label for="address_line_1">Address Line 1</label>
									<input type="text" value="<?= isset($tenant) && isset($tenant->address_line_1) ? $tenant->address_line_1 : '' ?>" name="tenant[address_line_1]" id="address_line_1" placeholder="">
								</p>
								<p style="width: 43%;">
									<label for="address_line_2">Address Line 2</label>
									<input type="text" value="<?= isset($tenant) && isset($tenant->address_line_2) ? $tenant->address_line_2 : '' ?>"  name="address_line_2" id="address_line_2" placeholder="">
								</p>
							</div>
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
									<label for="tax_id">SS#</label>
									<input type="text" value="<?= isset($tenant) && isset($tenant->tax_id) ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($tenant->tax_id)), 2) : '' ?>" class="form-control" name="tenant[tax_id]" id="tax_id" placeholder="">
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
                            <label for="contact_method">Preferred contact Method</label>
								<select class="form-control editable-select quick-add" id="contact_method" name="tenant[preferred_contact_method]" type="setting" key="contact_method">
										<?php
											foreach ($contact_method_types as $k => $cmtype) {
												echo '<option value="' . $k . '" ' . (isset($tenant) && $tenant->preferred_contact_method == $k ? 'selected' : '') . '>' . $cmtype . '</option>';
										} ?>
								<select>
							</p>
						</div>
					</div>	
					<ul class="check-a text-right m5">
						<li><label for="email_statements" class="checkbox <?= isset($tenant) && ($tenant->email_statements == 0) ? '' : 'active' ?>"><input type="hidden" name="tenant[email_statements]" value="0" /><input type="checkbox"  value="1" <?= isset($tenant) && ($tenant->email_statements == 0) ? '' : 'checked' ?> id="email_statements" name="tenant[email_statements]"  class="hidden" aria-hidden="true"><div class="input"></div>Email Statements</label></li>
						<li><label for="mail_statements" class="checkbox <?= isset($tenant) && ($tenant->mail_statements == 0) ? '' : 'active' ?>"><input type="hidden" name="tenant[mail_statements]" value="0" /><input type="checkbox"  value="1" <?= isset($tenant) && ($tenant->mail_statements == 0) ? '' : 'checked' ?> id="mail_statements" name="tenant[mail_statements]"  class="hidden" aria-hidden="true"><div class="input"></div>Mail Statements</label></li>
						<li><label for="text_notifications" class="checkbox <?= isset($tenant) && ($tenant->text_notifications == 0) ? '' : 'active' ?>"><input type="hidden" name="tenant[text_notifications]" value="0" /><input type="checkbox"  value="1" <?= isset($tenant) && ($tenant->text_notifications == 0) ? '' : 'checked' ?> id="text_notifications" name="tenant[text_notifications]"  class="hidden" aria-hidden="true"><div class="input"></div>Text Notifications</label></li>
						<li><label for="email_charge_notifications" class="checkbox <?= isset($tenant) && ($tenant->email_charge_notifications == 0) ? '' : 'active' ?>"><input type="hidden" name="tenant[email_charge_notifications]" value="0" /><input type="checkbox"  value="1" <?= isset($tenant) && ($tenant->email_charge_notifications == 0) ? '' : 'checked' ?> id="email_charge_notifications" name="tenant[email_charge_notifications]"  class="hidden" aria-hidden="true"><div class="input"></div>Email Charge Notifications</label></li>
						<li><label for="email_pay_notifications" class="checkbox <?= isset($tenant) && ($tenant->email_pay_notifications == 0) ? '' : 'active' ?>"><input type="hidden" name="tenant[email_pay_notifications]" value="0" /><input type="checkbox"  value="1" <?= isset($tenant) && ($tenant->email_pay_notifications == 0) ? '' : 'checked' ?> id="email_pay_notifications" name="tenant[email_pay_notifications]"  class="hidden" aria-hidden="true"><div class="input"></div>Email Pay Notifications</label></li>	
					</ul>
					<p>
						<label for="memo">Memo:</label>
						<input type="text" value="<?= isset($tenant) && isset($tenant->memo) ? $tenant->memo : '' ?>" class="form-control" name="tenant[memo]" id="memo" placeholder="">
					</p>
					<div class="submit">
						<p class="input-file">
							<label for="p-image"><input class = "upload" type="file"  id="p-image" targetimg="#tenant-image" name="image"> <span class="img"><img id="tenant-image" src="<?= isset($tenant) && $tenant->image != '' ? base_url() . "uploads/images/" . $tenant->image : 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_162b2febc6d%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_162b2febc6d%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.5%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' ?>" alt="Placeholder" width="200" height="200"></span> <span>Attach Photo</span></label>
						</p>
						<ul class="check-a a">
							<li>
								<label for="active" class="checkbox <?= isset($tenant->active) && ($tenant->active == 0) ? '' : 'active' ?>">
									<input type="hidden" name="tenant[active]" value="0" />
									<input type="checkbox"  value="1" <?= isset($tenant->active) && ($tenant->active == 0) ? '' : 'checked' ?> id="active" name="tenant[active]" class="hidden" aria-hidden="true">
									<div class="input"></div> Active?
								</label>
							</li>
						</ul>
					</div>
				</section>
                <div class="tabFunction">
				<nav class="double center ">
            <ul class="list-horizontal nav" id="property-tabs3" role="tablist" style="margin: 0 auto">
              <li class = "tablinks active" id="defaultOpen"><a href="#units-tab" onclick="tabswitch(event, 'contacts',$(this))">Contacts</a></li>
              <li class="tablinks"><a  href="#addresses-tab" onclick="tabswitch(event, 'addresses',$(this))">Addresses</a></li>
              <li class = "tablinks" > <a href="#documents-tab" onclick="tabswitch(event, 'documents',$(this))">Documents</a></li>
			  <li class = "tablinks"> <a  href="#utilities-tab" onclick="tabswitch(event, 'portal_login',$(this))">Portal Login</a></li>
			  <li class = "tablinks"> <a  href="#paymethods-tab" onclick="tabswitch(event, 'paymethods_tab',$(this))">Payment Methods</a></li>
            </ul>
          </nav>



            <!-- Tab content -->

<div id="contacts" class="active tabcontent defaultOpenTab">
<?php require_once VIEWPATH . 'forms/tenants/contacts.php';?>
</div>

<div id="addresses" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'forms/tenants/addresses.php';?> 
</div>

<div id="portal_login" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'forms/tenants/documents.php';?>
</div>

<div id="paymethods_tab" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'forms/tenants/paymentMethods.php';?>
</div>

<!-- <div id="portal_login" class="tabcontent" style="display:none">
< ?php require_once VIEWPATH . 'forms/tenants/contact.php';?>
</div> -->

</div>
				<footer class="last-child" style="z-index: 13;">
					<ul class="list-btn">
						<li><button type="submit" after="mnew">Save &amp; New</button></li>
						<li><button type="submit"  after="mclose">Save &amp; Close</button></li>
						<li><button type="submit" after="duplicate">Duplicate</button></li>
						<li><button type="button">Cancel</button></li>
					</ul>
					<ul>
						<li>Last Modified 12:22:31 pm 1/10/2018</li>
						<li>Last Modified by User</li>
					</ul>
                </footer>
                </form>
        </div>
	</div>
    </div>
</div>
