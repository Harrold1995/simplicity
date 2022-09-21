
<div class="modal fade employee-modal" id="employeeModal" tabindex="-1" role="dialog" doc-type="profiles" main-id=<?= isset($employee) && isset($employee->id) ? $employee->id : '-1' ?> type="employee" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="employee">
                  
				  	<header class="modal-h">
					<h2><?php echo $title; ?> </h2>
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
                                <li><?= isset($employee) ? '<a href="delete/deleteName/'.$employee->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
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
								<label for="employee[first_name]">First name</label>
                                <input type="text" value="<?= isset($employee) && isset($employee->first_name) ? $employee->first_name : '' ?>" class="form-control" name="employee[first_name]" id="first_name" placeholder="">
							</p>
							<p>
								<label for="employee[mi]">Mi</label>
                                <input type="text" value="<?= isset($employee) && isset($employee->mi) ? $employee->mi : '' ?>" class="form-control" name="employee[mi]" id="mi" placeholder="">
							</p>
							<p>
								<label for="employee[last_name]">Last name</label>
                                <input type="text" value="<?= isset($employee) && isset($employee->last_name) ? $employee->last_name : '' ?>" class="form-control" name="employee[last_name]" id="last_name" placeholder="">
							</p>
							<p>
								<label for="employee[company_name]">Company</label>
                                <input type="text" value="<?= isset($employee) && isset($employee->company_name) ? $employee->company_name : '' ?>" class="form-control" name="employee[company_name]" id="company_name" placeholder="">
							</p>
							<p>
								<label for="employee[email]"><i class="icon-envelope" aria-hidden="true"></i> <span class="hidden">Email</span></label>
								<input type="text" value="<?= isset($employee) && isset($employee->email) ? $employee->email : '' ?>" class="form-control" name="employee[email]" id="email" placeholder="">
							</p>
						</div>
						<div>
							<p>
								<label for="employee[address_line_1]">Address</label>
								<input type="text" value="<?= isset($employee) && isset($employee->address_line_1) ? $employee->address_line_1: '' ?>" class="form-control" name="employee[address_line_1]" id="address_line_1" placeholder="">
							</p>
							<div class="triple a">
								<p>
									<label for="employee[city]">City</label>
									<input type="text" value="<?= isset($employee) && isset($employee->city) ? $employee->city : '' ?>" class="form-control" name="employee[city]" id="city" placeholder="">
								</p>
								<p>
                                    <label for="employee[state]">State</label>
                                    <input type="text" value="<?= isset($employee) && isset($employee->state) ? $employee->state : '' ?>" class="form-control" name="employee[state]" id="state" placeholder="">
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
									<label for="employee[area_code]">Zip</label>
									<input type="text" value="<?= isset($employee) && isset($employee->area_code) ? $employee->area_code : '' ?>" class="form-control" name="employee[area_code]" id="area_code" placeholder="">
								</p>
							</div>
							<div class="double">
								<p>
									<label for="ss">SS#</label>
									<input type="text" value="<?= isset($employee) && isset($employee->tax_id) ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($employee->tax_id)), 2) : '' ?>" class="form-control" name="employee[tax_id]" id="ss" placeholder="">
								</p>
								<p>
									<label for="employee[status]">Status</label>
									<input type="text" value="<?= isset($employee) && isset($employee->status) ? $employee->status : '' ?>" class="form-control" name="employee[status]" id="status" placeholder="">
								</p>
							</div>
							<!-- <p>
								<label for="dob">DOB</label>
								<input type="text" value="< ?= isset($employee) && isset($employee->dob) ? $employee->dob : '' ?>" class="form-control" name="dob" id="dob" placeholder="">
							</p> -->
							<p>
								<label for="contact_method">Preferred contact Method</label>
								<select class="form-control editable-select quick-add" id="contact_method" name="employee[preferred_contact_method]" type="setting" key="contact_method">
										<?php
											foreach ($contact_method_types as $k => $cmtype) {
												echo '<option value="' . $k . '" ' . (isset($employee) && $employee->preferred_contact_method == $k ? 'selected' : '') . '>' . $cmtype . '</option>';
										} ?>
								<select>
							</p>
							<p>
								<label for="employee[phone]"><i class="icon-phone" aria-hidden="true"></i> <span class="hidden">Phone</span></label>
								<input type="text" value="<?= isset($employee) && isset($employee->phone)  ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($employee->phone)), 2) : '';?>" class="form-control" name="employee[phone]" id="phone" placeholder="">
							</p>
						</div>
					</div>	
					<?php $contact_methods = explode(',', $employee->contact_methods);?>
					<ul class="check-a text-right m5">
						<li><label for="is1099" class="checkbox <?= isset($employee) && ($employee->is1099 == 0) ? '' : 'active' ?>"><input type="hidden" name="employee[is1099]" value="0" /><input type="checkbox"  value="1" <?= isset($employee) && ($employee->is1099 == 0) ? '' : 'checked' ?> id="is1099" name="is1099"  class="hidden" aria-hidden="true"><div class="input"></div>1099?</label></li>
						<li><label for="active" class="checkbox <?= isset($employee) && ($employee->active == 0) ? '' : 'active' ?>"><input type="hidden" name="employee[active]" value="0" /><input type="checkbox"  value="1" <?= isset($employee) && ($employee->active == 0) ? '' : 'checked' ?> id="active" name="employee[active]"  class="hidden" aria-hidden="true"><div class="input"></div>Active?</label></li>		
					</ul>
					<p>
						<label for="memo">Memo:</label>
						<input type="text" value="<?= isset($employee) && isset($employee->memo) ? $employee->memo : '' ?>" class="form-control" name="employee[memo]" id="memo" placeholder="">
					</p>
					<div class="submit">
						<p class="input-file">
							<label for="p-image"><input class = "upload" type="file"  id="p-image" targetimg="#employee-image" name="image"> <span class="img"><img id="employee-image" src="<?= isset($employee) && $employee->image != '' ? base_url() . "uploads/images/" . $employee->image : 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_162b2febc6d%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_162b2febc6d%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.5%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' ?>" alt="Placeholder" width="200" height="200"></span> <span>Attach Photo</span></label>
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
<?php require_once VIEWPATH . 'forms/employees/contacts.php';?>
</div>

<div id="addresses" class="tabcontent" style="display:none" data-id = '14'>
<?php require_once VIEWPATH . 'forms/employees/addresses.php';?> 
</div>

<div id="documents" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'forms/employees/documents.php';?>
</div>

<div id="portal_login" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'forms/employees/login.php';?>
</div>

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



