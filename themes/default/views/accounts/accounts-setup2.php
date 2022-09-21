<form action="<?php echo $target ?>" method="post" class="module-box">
					<div id="setupprint" class="double a">
					<?php if($locked){echo '<h3 style="text-align: center; color: red;">No modification allowed</h3>';}  ?>
						<div>
							<h2 class="header-a text-center">General</h2>
							<ul class="list-a">
								<li><label for="name">Account Name:</label> <input type="text" value="<?= isset($getSingleAccount) && isset($getSingleAccount->name) ? $getSingleAccount->name : '' ?>" name="account[name]" id="name" class="inputStyle" placeholder="Enter Name" <?php if($locked) echo 'style="background-color: #e9ecef;" readonly="readonly"'?>></li>
								<!--<li><label for="laa">Account Name:</label> <input type="text" id="laa" name="laa" value="< ?php echo $getSingleAccount->name ?>"></li>-->
								<li>
									<label for="account_types_id">Account Type:</label> 
									<div>
										<select style="padding-right: 12px !important;" onchange="JS.loadList('api/getParentsList',$('[name=\'account[account_types_id]\']:last').val(), '#parent_id',  $(this).closest('form'));"  class="editable-select quick-add set-up inputStyle" id="account_types_id" name="account[account_types_id]" modal="account" type="table" key="account.name" value = "<?=isset($account) ? $account->account_types_id: ''; ?>" <?php if($locked) echo 'readonly="readonly"'?>>
										<option value="0"></option>
										<?php
										foreach ($account_types as $account_type) {
											echo '<option value="' . $account_type->id . '" ' . (isset($getSingleAccount) && $getSingleAccount->account_types_id == $account_type->id ? 'selected' : '') . '>' . $account_type->name . '</option>';
										} ?>
										</select>
									</div>
                           		</li>
								<!--<li><label for="lab">Account type:</label> <input type="text" id="lab" name="lab" value="<?php echo $getSingleAccount->type ?>"></li>-->
								<li><label for="accno">GL #:</label> <input type="number" value="<?= isset($getSingleAccount) && isset($getSingleAccount->accno) ? $getSingleAccount->accno : '' ?>" name="account[accno]" id="accno" class="inputStyle" placeholder=""></li>
								<!--<li><label for="lac">GL #:</label> <input type="text" id="lac" name="lac" value="<?php echo $getSingleAccount->accno ?>"></li>-->
								                            <li>
                              <label for="parent_id">Parent Account:</label> 
                              <div>
                                <select style="padding-right: 12px !important;" class=" editable-select quick-add set-up inputStyle" id="parent_id" name="account[parent_id]" modal="account" type="table" key="account.name" <?php if($locked) echo 'readonly="readonly"'?>><!-- value="< ?=isset($account) ? $account->parent_id: ''; ?>"-->
                                    <option value="0"></option>
                                    <?php
                                    foreach ($parents as $parent) {  
										if($parent->id != $getSingleAccount->id){
											echo '<option value="' . $parent->id . '" ' . (isset($getSingleAccount) && $getSingleAccount->parent_id == $parent->id ? 'selected' : '') . '>' . $parent->name . '</option>';
										}                                   
                                    }                                  
                                     ?>
                               </select>
                             </div>
                            </li>
								<!--<li><label for="lad">Parent Account:</label> <input type="text" id="lad" name="lad" value="<?php echo $getSingleAccount->parent_id ?>"></li>-->
								<li>
									<label for="class">Default Class:</label> 
									<div>
									<select style="padding-right: 12px !important;" onchange="JS.loadList('api/getDefaultClassList',$('[name=\'class\']:last').val(), '#account_id',  $(this).closest('form'))" class="editable-select quick-add set-up inputStyle" id="class" name="account[class]" modal="account" type="table" key="account.name" value = "<?=isset($getSingleAccount) ? $getSingleAccount->class: ''; ?>">
										<option value="0"></option>
										<?php

										foreach ($classes as $class) {
											echo '<option value="' . $class->id . '" ' . (isset($getSingleAccount) && $getSingleAccount->class == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
										} ?>
									</select>
									</div>
                           		 </li>
								<!--<li><label for="lae">Default Class:</label> <input type="text" id="lae" name="lae" value="< ?php echo $getSingleAccount->class ?>"></li>-->
								<li><label for="tax_line_id">Tax line Mapping:</label> <input value="<?= isset($getSingleAccount) && isset($getSingleAccount->tax_line_id) ? $getSingleAccount->tax_line_id : '' ?>" id="tax_line_id" name="account[tax_line_id]" class="inputStyle"></li>
								<li><label for="rpie_line_id">RPIE line Mapping:</label> <input value="<?= isset($getSingleAccount) && isset($getSingleAccount->rpie_line_id) ? $getSingleAccount->rpie_line_id : '' ?>"  id="rpie_line_id" name="account[rpie_line_id]" class="inputStyle"></li>
								<!--<li><label for="laf">Tax line Mapping:</label> <input type="text" id="laf" name="laf" value="< ?php echo $getSingleAccount->tax_line_id ?>"></li>
								<li><label for="lag">RPIE line Mapping:</label> <input type="text" id="lag" name="lag" value="< ?php echo $getSingleAccount->rpie_line_id ?>"></li>-->
								<li><label for="lah">Active?:</label> <input type="text" id="lah" name="lah" value="<?php echo $getSingleAccount->active ?>"></li>
								<li><span>Created by:</span> User 1</li>
								<li><span>Created on:</span> 12/18/2017</li>
							</ul>
							<p class="overlay-i size-b"><span class="semi size-up">Description:</span><?php echo $getSingleAccount->description ?></p>
						</div>
						<?php if($getSingleAccount->type == "Credit Card"){?>
							<div>
								<!-- <h2 class="header-a text-center">Credit Card</h2>
								<ul class="list-a">
									<li><label for="lai">CC#:</label> <input type="text" id="lai" name="lai" value="< ?php echo $getCreditCard->cc_num ?>"></li>
									<li><label for="laj">type:</label> <input type="text" id="laj" name="laj" value="< ?php echo $getCreditCard->vendor ?>"></li>
									<li><label for="lak">Exp #:</label> <input type="text" id="lak" name="lak" value="< ?php echo $getCreditCard->expiration ?>"></li>
									<li><label for="lal">Security:</label> <input type="text" id="lal" name="lal" value="< ?php echo $getCreditCard->security_code ?>"></li>
									<li><label for="lam">Billing Address:</label> <input type="text" id="lam" name="lam" value="CitiCard"></li>
									<li><label for="lan">Card holder:</label> <input type="text" id="lan" name="lan" value="< ?php echo $getCreditCard->title ?>"></li>
									<li><label for="lao">Username:</label> <input type="text" id="lao" name="lao" value="yes"></li>
									<li><label for="lap">Password:</label> <input type="text" id="lap" name="lap" value="< ?php echo $getCreditCard->login_info ?>"></li>
								</ul> -->
								<h2 class="header-a text-center">Credit Card Info</h2>
									<ul class="list-a">
										<li>
											<label for="vendor" >Credit card Type:</label>
										<input type="text" value="<?= isset($specialAccount) && isset($specialAccount->vendor) ? $specialAccount->vendor : '' ?>"  class="form-control" name="specialAccount[vendor]" id="vendor" placeholder="Enter Credit card Type">
										</li>
										<li>
											<label for="cc_num">Credit card Number:</label> 
											<input type="number" value="<?= isset($specialAccount) && isset($specialAccount->cc_num) ? $specialAccount->cc_num : '' ?>" class="form-control" name="specialAccount[cc_num]" id="cc_num" placeholder="Enter Credit card Number">
										</li>
										<li>
											<label for="security_code">Security Code:</label>
											<input type="number" value="<?= isset($specialAccount) && isset($specialAccount->security_code) ? $specialAccount->security_code : '' ?>" class="form-control" name="specialAccount[security_code]" id="security_code" placeholder=" ">
										</li>
										<li>
											<label for="expiration">Expiration Date:</label>
											<input type="text" value="<?= isset($specialAccount) && isset($specialAccount->expiration) ? $specialAccount->expiration : '' ?>"  class="form-control" name="specialAccount[expiration]" id="expiration" placeholder="MM / YY">
										</li>
										<li>
											<label for="billing_address">Billing Address:</label>
											<input type="number" value="<?= isset($specialAccount) && isset($specialAccount->billing_address) ? $specialAccount->billing_address : '' ?>" class="form-control" name="billing_address" id="billing_address" placeholder=" ">
										</li>
										<li>
											<label for="title">title:</label>
											<input type="text" value="<?= isset($specialAccount) && isset($specialAccount->title) ? $specialAccount->title : '' ?>"  class="form-control" name="specialAccount[title]" id="title" placeholder="title">
										</li>
										<li>
											<label for="title">title:</label>
											<input type="text" value="<?= isset($specialAccount) && isset($specialAccount->title) ? $specialAccount->title : '' ?>"  class="form-control" name="specialAccount[title]" id="title" placeholder="title">
										</li>
										<li>
											<label for="login_info">Login info:</label>
											<input type="text" value="<?= isset($specialAccount) && isset($specialAccount->login_info) ? $specialAccount->login_info : '' ?>"  class="form-control" name="specialAccount[login_info]" id="login_info" placeholder="Login info">
										</li>
									</ul>
									<input type="hidden" name="table" value="credit_cards "/>
							</div>
							<?php } ?>
							<?php if($getSingleAccount->type == "Bank"){?>
							 <div>
									<h2 class="header-a text-center">Bank</h2>
									<ul class="list-a">
										<li>
										<label for="vendor">Bank Name:</label> 
										<input type="text" value="<?= isset($specialAccount) && isset($specialAccount->vendor) ? $specialAccount->vendor : '' ?>" class="form-control" name="specialAccount[vendor]" id="vendor" >
										</li>
										<li>
										<label for="vendor">Routing:</label> 
										<input type="number" value="<?= isset($specialAccount) && isset($specialAccount->routing) ? $specialAccount->routing : '' ?>" class="form-control" name="specialAccount[routing]" id="routing"  >
										</li>
									</ul>
								<input type="hidden" name="table" value="banks"/>
							</div>
							<?php } ?>
							<?php if($getSingleAccount->type == "Mortgages"){?>
							 <div>
									<h2 class="header-a text-center">Mortgage Info</h2>
											<ul class="list-a">
												<li>
												<label for="loan_num" >Loan #:</label>
												<input type="text" value="<?= isset($specialAccount) && isset($specialAccount->loan_num) ? $specialAccount->loan_num : '' ?>" name="specialAccount[loan_num]" id="loan_num" >
												</li>

												<li>
												<label for="vendor">Vendor:</label>                  
													<select style="padding-right: 12px !important;"   class=" editable-select quick-add set-up" id="vendor" name="specialAccount[vendor]" modal="vendor" type="table" key="vendor.name">
													<?php if(isset($vendors)){ ?>
													<option value="0"></option>
														<?php
														foreach ($vendors as $vendor) {
																echo '<option value="' . $vendor->id . '" ' . (isset($specialAccount) && $specialAccount->vendor == $vendor->id ? 'selected' : '') . '>' . $vendor->name . '</option>';
														} ?>
														<?php } ?>
													</select>
											
												<!-- <label for="vendor">Vendor:</label> 
												<input type="number" value="< ?= isset($specialAccount) && isset($specialAccount->vendor) ? $specialAccount->vendor : '' ?>"  name="specialAccount[vendor]" id="vendor" > -->
												</li>
												<li>
												<label for="default_interest_acct">Interest Account</label>
													<select style="padding-right: 12px !important;"   class=" editable-select quick-add set-up" id="default_interest_acct" name="specialAccount[default_interest_acct]" modal="account" type="table" key="account.name">
													<?php if(isset($accounts)){ ?>
													<option value="0"></option>
														<?php
														foreach ($accounts as $account) {
																echo '<option value="' . $account->id . '" ' . (isset($specialAccount) && $specialAccount->default_interest_acct == $account->id ? 'selected' : '') . '>' . $account->name . '</option>';
														} ?>
													<?php } ?>
													</select>
											
												<!-- <label for="default_interest_acct">Interest Account</label>
												<input type="text" value="< ?= isset($specialAccount) && isset($specialAccount->default_interest_acct) ? $specialAccount->default_interest_acct : '' ?>"  name="specialAccount[default_interest_acct]" id="default_interest_acct" > -->
												</li>

												<li>
												<label for="loan_date">Loan Date</label>
												<input data-toggle="datepicker" value="<?= isset($specialAccount) && isset($specialAccount->loan_date) ? $specialAccount->loan_date : '' ?>"  name="specialAccount[loan_date]" id="loan_date">
												</li>
												<li>
												<label for="maturity_date">Maturity Date</label>
												<input data-toggle="datepicker" value="<?= isset($specialAccount) && isset($specialAccount->maturity_date) ? $specialAccount->maturity_date : '' ?>"  name="specialAccount[maturity_date]" id="maturity_date">
												</li>

												<li>
												<label for="final_cutoff_date">Final Cutoff Date</label>
												<input data-toggle="datepicker" value="<?= isset($specialAccount) && isset($specialAccount->final_cutoff_date) ? $specialAccount->final_cutoff_date : '' ?>" name="specialAccount[final_cutoff_date]" id="final_cutoff_date">
												</li>
												<li>
												<label for="loan_amount">Loan Amount:<span class="prefix">$</span></label> 
												<input type="decimal" value="<?= isset($specialAccount) && isset($specialAccount->loan_amount) ? number_format($specialAccount->loan_amount, 2) : '' ?>" name="mortgages[loan_amount]" id="loan_amount" class="formatCurrency">
												</li>
												<li>
												<label for="interest_rate">Interest Rate:</label> 
												<input type="text" value="<?= isset($specialAccount) && isset($specialAccount->interest_rate) ? $specialAccount->interest_rate : '' ?>"  name="specialAccount[interest_rate]" id="interest_rate" >
												</li>
												<li>
												<label for="monthly_pmt">Monthly Payment:<span class="prefix">$</span></label> 
												<input type="decimal" value="<?= isset($specialAccount) && isset($specialAccount->monthly_pmt) ? number_format($specialAccount->monthly_pmt, 2) : '' ?>"  name="specialAccount[monthly_pmt]" id="monthly_pmt" class="formatCurrency">
												</li>
												<li>
												<label for="extension_options">Extension Options</label> 
												<input type="text" value="<?= isset($specialAccount) && isset($specialAccount->extension_options) ? $specialAccount->extension_options : '' ?>"  name="specialAccount[extension_options]" id="extension_options" >
												</li>
												<li>
												<label for="Memo">Memo</label> 
												<input type="text" value="<?= isset($specialAccount) && isset($specialAccount->memo) ? $specialAccount->memo : '' ?>"  name="specialAccount[memo]" id="memo" >
												</li>
											</ul>
											<input type="hidden" name="table" value="mortgages "/>
										</div>
								<?php } ?>
    
						</div>
						<p class="submit sticky submitAccount" style="width: 200px; margin: 0 auto;"><button type="submit">Save changes</button></p>
					</form>