
<div class="modal fade vendor-modal" id="vendorModal" tabindex="-1" role="dialog" doc-type="profiles" main-id=<?= isset($vendor) && isset($vendor->id) ? $vendor->id : '-1' ?> type="vendor" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="vendor">
                  
				  	<header class="modal-h">
					<h2><?php echo $title; ?></h2>
					<nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
					<nav>
						<ul>
								<li><a href="#" class="switchModal" dir="prev"></i> <span>Previous</span></a></li>
								<li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>				
                                <li><?= isset($vendor) ? '<a href="delete/deleteName/'.$vendor->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
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
								<label for="vendor[first_name]">Company</label>
                                <input type="text" value="<?= isset($vendor) && isset($vendor->first_name) ? $vendor->first_name : '' ?>" class="form-control" name="vendor[first_name]" id="first_name" placeholder="">
							</p>
							<p>
								<label for="vendor[accno]">Account#</label>
                                <input type="text" value="<?= isset($vendor) && isset($vendor->accno) ? $vendor->accno : '' ?>" class="form-control" name="vendor[accno]" id="accno" placeholder="">
							</p>

							<p>
								<label for="vendor[email]"><i class="icon-envelope" aria-hidden="true"></i> <span class="hidden">Email</span></label>
								<input type="text" value="<?= isset($vendor) && isset($vendor->email) ? $vendor->email : '' ?>" class="form-control" name="vendor[email]" id="email" placeholder="">
							</p>
							<p>
								<label for="vendor[phone]"><i class="icon-phone" aria-hidden="true"></i> <span class="hidden">Phone</span></label>
								<input type="text" value="<?= isset($vendor) && isset($vendor->phone)  ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($vendor->phone)), 2) : '';?>" class="form-control" name="vendor[phone]" id="phone" placeholder="">
							</p>
							<p>
								<span >
									<label for="def_expense_acc">Default expense account</label>
									<span class="select">
									<select id="def_expense_acc" class="editable-select" name="vendor[def_expense_acc]">
									<?php
										echo '<option value="0"></option>'; 
										if (isset($default_expense_accounts))
											foreach ($default_expense_accounts as $default_expense_account) {
												echo '<option value="'.$default_expense_account->id.'" '. (isset($vendor) && $vendor->def_expense_acc == $default_expense_account->id ? ' selected' : '') .'  >' . $default_expense_account->name . '</option>';
										} ?>
									</select>
									</span>
                              </span>
							</p>
						</div>
						<div>
							<p>
								<label for="vendor[address_line_1]">Address</label>
								<input type="text" value="<?= isset($vendor) && isset($vendor->address_line_1) ? $vendor->address_line_1: '' ?>" class="form-control" name="vendor[address_line_1]" id="address_line_1" placeholder="">
							</p>
							<div class="triple a">
								<p>
									<label for="vendor[city]">City</label>
									<input type="text" value="<?= isset($vendor) && isset($vendor->city) ? $vendor->city : '' ?>" class="form-control" name="vendor[city]" id="city" placeholder="">
								</p>
								<p>
                                    <label for="vendor[state]">State</label>
                                    <input type="text" value="<?= isset($vendor) && isset($vendor->state) ? $vendor->state : '' ?>" class="form-control" name="vendor[state]" id="state" placeholder="">
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
									<label for="vendor[area_code]">Zip</label>
									<input type="text" value="<?= isset($vendor) && isset($vendor->area_code) ? $vendor->area_code : '' ?>" class="form-control" name="vendor[area_code]" id="area_code" placeholder="">
								</p>
							</div>
							<div class="double">
								<p>
									<label for="ss">SS#</label>
									<input type="text" value="<?= isset($vendor) && isset($vendor->tax_id) ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($vendor->tax_id)), 2) : '' ?>" class="form-control" name="vendor[tax_id]" id="ss" placeholder="">
								</p>
								<p>
									<label for="vendor[status]">Status</label>
									<input type="text" value="<?= isset($vendor) && isset($vendor->status) ? $vendor->status : '' ?>" class="form-control" name="vendor[status]" id="status" placeholder="">
								</p>
							</div>
							<!-- <p>
								<label for="dob">DOB</label>
								<input type="text" value="< ?= isset($vendor) && isset($vendor->dob) ? $vendor->dob : '' ?>" class="form-control" name="dob" id="dob" placeholder="">
							</p> -->
							<p>
								<label for="contact_method">Preferred contact Method</label>
								<select class="form-control editable-select quick-add" id="contact_method" name="vendor[preferred_contact_method]" type="setting" key="contact_method">
										<?php
											foreach ($contact_method_types as $k => $cmtype) {
												echo '<option value="' . $k . '" ' . (isset($vendor) && $vendor->preferred_contact_method == $k ? 'selected' : '') . '>' . $cmtype . '</option>';
										} ?>
								<select>
							</p>
						</div>
					</div>	
					<?php $contact_methods = explode(',', $vendor->contact_methods);?>
					<ul class="check-a text-right m5">
						<li><label for="is1099" class="checkbox <?= isset($vendor) && ($vendor->is1099 == 0) ? '' : 'active' ?>"><input type="hidden" name="vendor[is1099]" value="0" /><input type="checkbox"  value="1" <?= isset($vendor) && ($vendor->is1099 == 0) ? '' : 'checked' ?> id="is1099" name="vendor[is1099]"  class="hidden" aria-hidden="true"><div class="input"></div>1099?</label></li>
						<li><label for="active" class="checkbox <?= isset($vendor) && ($vendor->active == 0) ? '' : 'active' ?>"><input type="hidden" name="vendor[active]" value="0" /><input type="checkbox"  value="1" <?= isset($vendor) && ($vendor->active == 0) ? '' : 'checked' ?> id="active" name="vendor[active]"  class="hidden" aria-hidden="true"><div class="input"></div>Active?</label></li>		
					</ul>
					<p>
						<label for="memo">Memo:</label>
						<input type="text" value="<?= isset($vendor) && isset($vendor->memo) ? $vendor->memo : '' ?>" class="form-control" name="vendor[memo]" id="memo" placeholder="">
					</p>
					<div class="submit">
						<p class="input-file">
							<label for="p-image"><input class = "upload" type="file"  id="p-image" targetimg="#vendor-image" name="image"> <span class="img"><img id="vendor-image" src="<?= isset($vendor) && $vendor->image != '' ? base_url() . "uploads/images/" . $vendor->image : 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_162b2febc6d%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_162b2febc6d%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.5%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' ?>" alt="Placeholder" width="200" height="200"></span> <span>Attach Photo</span></label>
						</p>

					</div>
				</section>

                		<div class="tabFunction">
				<nav class="double center ">
            <ul class="list-horizontal nav" id="property-tabs3" role="tablist" style="margin: 0 auto">
              <li class = "tablinks active" id="defaultOpen"><a href="#units-tab" onclick="tabswitch(event, 'contacts',$(this))">Contacts</a></li>
              <li class="tablinks"><a  href="#addresses-tab" onclick="tabswitch(event, 'addresses',$(this))">Addresses</a></li>
              <li class = "tablinks" > <a href="#documents-tab" onclick="tabswitch(event, 'documents',$(this))">Documents</a></li>
              <li class = "tablinks"> <a  href="#utilities-tab" onclick="tabswitch(event, 'portal_login',$(this))">Portal Login</a></li>
            </ul>
          </nav>



            <!-- Tab content -->

<div id="contacts" class="active tabcontent defaultOpenTab" data-id = '12'>
<?php require_once VIEWPATH . 'forms/vendors/contacts.php';?>
</div>

<div id="addresses" class="tabcontent" style="display:none" data-id = '14'>
<?php require_once VIEWPATH . 'forms/vendors/addresses.php';?> 
</div>

<div id="documents" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'forms/vendors/documents.php';?>
</div>

<!-- <div id="portal_login" class="tabcontent" style="display:none">
< ?php require_once VIEWPATH . 'forms/vendors/contact.php';?>
</div> -->

</div>
				<footer>
					<ul class="list-btn ">
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

<script >

// $('input[type="checkbox"], input[type="radio"]').click(function(){
//         if($(this).parent().hasClass('radio')) { 
//                 $(this).parents('p, ul:first').find('label').removeClass('active');
//                 }
//                 $(this).parent('label').toggleClass('active'); 
// });



</script>



