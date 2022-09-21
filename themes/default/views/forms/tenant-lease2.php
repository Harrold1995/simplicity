
<div class="modal fade account-modal" id="accountModal" tabindex="-1" role="dialog" main-id=<?= isset($account) && isset($account->id) ? $account->id : '-1' ?> type="account" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px;">
                  <form action="<?php echo $target; ?>" method="post"type="account">
                  <header class="modal-header">
					<h2 class="text-uppercase">Add People to lease</h2>

                </header>
                <?php if(isset($table) && isset($table->name))if($table->name == "Credit Card" or $table->name == "Bank" or $table->name == "Mortgages" ) $formType ="special;" ?>
                <section class="a modal-body" style="border-style: none; box-shadow:none;">
                <div class="<?php if(isset($table) && isset($table->name))if($table->name == "Credit Card" or $table->name == "Bank" or $table->name == "Mortgages" ) echo 'double a'; ?>" id="class_modal_a">
                <div>
							<h2 class="header-a text-center">General</h2>
							<ul id="setupprint" class="list-a">
								<li><label for="name">Account Name:</label> <input type="text" style="<?=isset($formType)? '':'width: 600px !important;' ?>" value="<?= isset($account) && isset($account->name) ? $account->name : '' ?>" name="account[name]" id="name" class="inputStyle" placeholder="Enter Name"></li>
								<li><label for="account_types_id">Account Type:</label> 

                                                  <div class=" field-select">
                                                    <select style="padding-right: 12px !important;" onchange="JS.loadList('api/getParentsList',$('[name=\'account[account_types_id]\']:last').val(), '#parent_id',  $(this).closest('.modal-body'));accountTypeForm($(this).closest('#account_types_id').val(), $(this).closest('.modal-body'),'#acountTypeInfo')"  class="form-control editable-select quick-add set-up inputStyle" id="account_types_id" name="account[account_types_id]" modal="account" type="table" key="account.name" value = "<?=isset($account) ? $account->account_types_id: ''; ?>" style="<?=isset($formType)? '':'width: 600px !important;' ?>">
                                                    <option value="0"></option>
                                                        <?php
                                                        foreach ($account_types as $account_type) {
                                                            echo '<option value="' . $account_type->id . '" ' . (isset($account) && $account->account_types_id == $account_type->id ? 'selected' : '') . '>' . $account_type->name . '</option>';
                                                        } ?>
                                                    </select>
                                                  </div>

                                </li>
                                <li><label for="accno">GL #:</label> <input type="number" value="<?= isset($account) && isset($account->accno) ? $account->accno : '' ?>" name="account[accno]" id="accno" class="inputStyle" placeholder="" style="<?=isset($formType)? '':'width: 600px !important;' ?>"></li>
								<li>
                                                  <label for="parent_id">Parent Account:</label> 
                                                  <div class=" field-select">
                                                    <select style="padding-right: 12px !important;" class="form-control editable-select quick-add set-up inputStyle" id="parent_id" name="account[parent_id]" modal="account" type="table" key="account.name" style="<?=isset($formType)? '':'width: 600px !important;' ?>"><!-- value="< ?=isset($account) ? $account->parent_id: ''; ?>"-->
                                                        <option value="0"></option>
                                                        <?php
                                                        foreach ($parents as $parent) {
                                                          
                                                            echo '<option value="' . $parent->id . '" ' . (isset($account) && $account->parent_id == $parent->id ? 'selected' : '') . '>' . $parent->name . '</option>';
                                                        }
                                                       
                                                         ?>
                                                   </select>
                                                 </div>
                                </li>
								<li>
                                                  <label for="class">Default Class:</label> 
                                                  <div class=" field-select">
                                                    <select style="padding-right: 12px !important;" onchange="JS.loadList('api/getDefaultClassList',$('[name=\'class\']:last').val(), '#account_id',  $(this).closest('.modal-body'))" class="form-control editable-select quick-add set-up inputStyle" id="class" name="account[class]" modal="account" type="table" key="account.name" value = "<?=isset($account) ? $account->class: ''; ?>" style="<?=isset($formType)? '':'width: 600px !important;' ?>">
                                                    <option value="0"></option>
                                                        <?php

                                                        foreach ($classes as $class) {
                                                            echo '<option value="' . $class->id . '" ' . (isset($account) && $account->class == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
                                                        } ?>
                                                    </select>
                                                  </div>
                                </li>
								<li><label for="tax_line_id">Tax line Mapping:</label> <input value="<?= isset($account) && isset($account->tax_line_id) ? $account->tax_line_id : '' ?>" id="tax_line_id" name="account[tax_line_id]" class="inputStyle" style="<?=isset($formType)? '':'width: 600px !important;' ?>"></li>
								<li><label for="rpie_line_id">RPIE line Mapping:</label> <input value="<?= isset($account) && isset($account->rpie_line_id) ? $account->rpie_line_id : '' ?>"  id="rpie_line_id" name="account[rpie_line_id]" class="inputStyle" style="<?=isset($formType)? '':'width: 600px !important;' ?>"></li>
                            </ul>
                            <ul class="check-a a" style="float: left !important;">
                                                <li><label for="active" class="checkbox <?= isset($account) && ($account->active == 1) ? 'active' : '' ?>"><input type="hidden" name="account[active]" value="0" /><input type="checkbox"  value="1" <?= isset($account) && ($account->active == 1) ? 'checked' : '' ?> id="active" name="account[active]"  class="hidden" aria-hidden="true"><div class="input"></div> Active?</label></li>
                                                <br/>
                                                <!-- needs name and to be hooked up -->
                                                <li><label for="properties" class="checkbox <?= isset($account) && ($account->properties == 1) ? 'active' : '' ?>"><input type="hidden" name="properties" value="0" /><input type="checkbox" value="1" <?= isset($lease) && ($lease->stabilized == 1) ? 'checked' : '' ?> id="properties" name="account[properties]"  class="hidden" aria-hidden="true"><div class="input"></div> Properties</label></li>
                                                
                            </ul>
                                
                         <div>            
                </section>
                <li style="list-style:none;"><label style="display: inline !important; color: black !important;" for="description">Description:</label> <input type="text" style="width: 650px !important; display: inline !important; color: black !important;" value="<?= isset($account) && isset($account->description) ? $account->description : '' ?>" name="account[description]" id="description" placeholder="Enter Description"></li>								                                                
                   <br/>
							
						
                               <footer>
          <ul class="list-btn">
            <li><button type="submit" after="mclose">Save &amp; Close</button></li>

            <li><button type="button">Cancel</button></li>
            
          </ul>
          <ul>
            <li>Last Modified 12:22:31 pm 1/10/2018</li>
            <li>Last Modified by <a href="#!">User</a></li>
          </ul>
        </footer>
      </form>
                                      

                              
                    </div>
		</div>

</div>
</div>
