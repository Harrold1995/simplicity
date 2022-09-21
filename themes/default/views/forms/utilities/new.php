<div class="modal fade newUtility-modal" id="newUtilityModal" tabindex="-1" role="dialog"  type="newUtility" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 30px; max-height:620px;">">
                  <!-- <form action="< ?php echo $target; ?>" method="post" autocomplete="off" email="bills/sendEmail" type="bill"> -->
                  <!-- <form action="./" method="post" data-title="credit-card-charge">	 -->
				  <form action="<?php echo $target; ?>" method="post" class=" ve form-bills" data-title="newUtility-entry" type="newUtility" style="height: 90%;">
				<header class="modal-h">
					<h2>New Utility</h2>
				<nav>
                  <ul>
                      <li><span class="buttons" style=""><span class="min" style="padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                      <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                      <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                  </ul>
              </nav>
					<p class="submit"><button type="button" id="exit">Exit</button></p>
                </header>
                <!--  -->
                <section class="plain modal-body" style="border-style: none; box-shadow:none;">
                <div class="d80 m20" id="class_modal_a">

                        <div>
                          <h2 class="header-a text-center">Add utility</h2>
                          <ul class="list-input m30 plain">

                            <li>
                              <label for="payee">Vendor:</label> 
                              <div class=" field-select">
                                <select style="padding-right: 12px !important;"   class="form-control editable-select quick-add set-up" id="payee" name="utility[payee]">
                                  <option value="-1"></option>
                                  <?php
                                  foreach ($vendors as $vendor) {
                                      echo '<option value="' . $vendor->id . '">' . $vendor->name . '</option>';
                                  } ?>
                                </select>
                              </div>
                            </li>

                            <li><label for="property">Property:</label> 
                            <span class="select">
                                    <select stype="property" class=" fastEditableSelect quick-add set-up " name="utility[property_id]" id="property_id">
                                    </select>
                                </span>
                            </li>

                            <li>
                              <label for="unit_id">Unit:</label> 
                              <div class=" field-select">
                                <select style="padding-right: 12px !important;" class="form-control editable-select quick-add set-up" id="unit_id" name="utility[unit_id]">
                                    <option value="0"></option>
                                    <?php
                                    foreach ($units as $unit) {                                     
                                        echo '<option value="' . $unit->id . '">' . $unit->name . '</option>';
                                    }                                  
                                     ?>
                               </select>
                             </div>
                            </li>

                            <li>
                              <label for="description">Description:</label>
                              <input id="description" name="utility[description]">
                            </li>

                            <li>
                              <label for="account">Account:</label>
                              <input id="account" name="utility[account]">
                            </li>

                            <li>
                                <label for="utility_type">Utility type:</label> 
                                <div class=" field-select">
                                  <select style="padding-right: 12px !important;"  class="form-control editable-select quick-add set-up" id="utility_type" name="utility[utility_type]">
                                    <option value="-1"></option>
                                    <?php

                                    foreach ($utilityTypes as $utilityType) {
                                        echo '<option value="' . $utilityType->id . '">' . $utilityType->name . '</option>';
                                    } ?>
                                  </select>
                                </div>
                            </li>

                            <li>
                              <label for="meter">Meter:</label> 
                              <input type="text" id="meter" name="utility[meter]">
                            </li>

                            <li>
                                <label for="default_expense_acct">Expense Acct:</label> 
                                <div class=" field-select">
                                  <select style="padding-right: 12px !important;"  class="form-control editable-select quick-add set-up" id="default_expense_acct" name="utility[default_expense_acct]">
                                    <option value="-1"></option>
                                    <?php

                                    foreach ($subaccounts as $subaccount) {
                                        echo '<option data-id="'.$subaccount->id.'" data-parent-id="'.$subaccount->parent_id.'" class="nested'.$subaccount->step.'"value="' . $subaccount->id .'"  >' . $subaccount->name . '</option>';
                                    } ?>
                                  </select>
                                </div>
                            </li>

                            <li>
                                <ul class="check-a a">
                                    <li><label for="direct_payment" class="checkbox ">Direct Payment<input type="checkbox" value="1"  id="direct_payment" name="utility[direct_payment]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                                </ul>
                            </li>

                                                           
                                                            
                           </ul>
                           
                        </div>
              </div>
              <p class="m35">
                <label for="memo">Memo:</label>
                <textarea type="text" id="memo" name="utility[memo]"></textarea>
              </p>           
            </section>
            <footer>
              <ul class="list-btn">
                <!-- <li><button type="submit" after="mnew">Save &amp; New</button></li> -->
                <li><button type="submit" after="mclose">Save &amp; Close</button></li>
                <!-- <li><button type="submit" after="duplicate">Duplicate</button></li> -->
                <li><button type="button">Cancel</button></li>
                
              </ul>
              <ul>
                <li>Last Modified 12:22:31 pm 1/10/2018</li>
                <li>Last Modified by <a href="#!">User</a></li>
              </ul>
            </footer>
            <!--  -->
                  </form>               
            </div>
        </div>
    </div>
</div>

<script>

         var utilityTypes = <?php echo json_encode($utilityTypes); ?>;
         var subaccounts= <?php echo json_encode($subaccounts); ?>;
         var units = <?php echo json_encode($units); ?>;
         var vendors = <?php echo json_encode($vendors); ?>;
	// var properties= < ?php echo $jProperties; ?>;
    //  var accounts = < ?php echo $jAccounts; ?>;
	//  var units = < ?php echo $jUnits; ?>;
	//  var names = < ?php echo $jNames; ?>;
	 
console.log('cc charges00');
  </script>