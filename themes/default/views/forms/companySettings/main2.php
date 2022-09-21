<?php if(isset($property) && isset($property->name)) $propertyName = $property->name;?> <!--used for notes-->
<div class="modal flexmodal1 fade company-settings-modal hide" id="companySettingsModal" tabindex="-1" role="dialog" main-id=<?= isset($property) && isset($property->id) ? $property->id : '-1' ?> type="companySettings" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px;">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="companySettings">
                  
				  	<!-- <header ></header> -->
                       <header class="modal-h">
                            <h2 class="text-uppercase"><?php echo $title; ?></h2>
                        
							<nav>
								<ul>
									<li><span class="buttons" style=""><span class="min" style=";padding: 8px 20px;cursor: pointer;">_</span></span> </li>
									<li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
									<li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
								</ul>
							</nav>
							</header>
                <section class="b">
                    <h4>Company Info</h4>
                    <div class="double e m20" style="white-space:nowrap; margin-left: 0;">
						<div>
                        <p>
                                <label for="company_name">company_name</label>
                                <input type="text" name="companySettings[company_name]" id="company_name"  value="<?= isset($companySettings) && isset($companySettings->company_name) ? $companySettings->company_name : '' ?>">
                            </p>
                            <p>
                                <label for="company_phone">company_phone</label>
                                <input type="text" name="companySettings[company_phone]" id="company_phone"  value="<?= isset($companySettings) && isset($companySettings->company_phone) ? $companySettings->company_phone : '' ?>">
                            </p>
                            <p>
                                <label for="company_email">company_email</label>
                                <input type="text" name="companySettings[company_email]" id="company_email"  value="<?= isset($companySettings) && isset($companySettings->company_email) ? $companySettings->company_email : '' ?>">
                            </p>					
                        </div>
                        <div class="col-12 col-md-3 col-lg-2 mb-3 lg-mb-0">
                                <div class="p-4">
                                    <img class="rounded w-100" id="company_logo" src="<?= isset($companySettings) && $companySettings->company_logo != '' ? base_url() . "uploads/images/" . $companySettings->company_logo : 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_162b2febc6d%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_162b2febc6d%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.5%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' ?>" data-holder-rendered="true">
                                </div>
                                <label class="btn btn-primary btn-block mt-3" onclick="$(this).next().trigger('click')">
                                    Attach Logo
                                </label>
                                <input type="file" name="image" class="upload d-none form-control" id="p-image" targetimg="#company_logo">
                            </div>

                    </div>
                </section>
				<section class="b">
					<div class="double e m20" style="white-space:nowrap; margin-left: 0;">
						<div>
                        <h4>Default Accounts</h4>
                        <?php if(isset($companySettings) && isset($companySettings->id)){echo '<input type="hidden" name="companySettings[id]"  value="' . $companySettings->id . ' ">';}?>

                            
                            <p>
                                <label for="accounts_receivable">Accounts receivable</label>
                                <span class = 'select'>
                                    <select stype="account" class="fastEditableSelect" key="accounts.name" modal="account" id="accounts_receivable" name="companySettings[accounts_receivable]" default="<?= isset($companySettings->accounts_receivable) ? $companySettings->accounts_receivable : '' ?>"></select>        
                                </span>
                            </p>
                            <p>
                                <label for="accounts_payable">Accounts payable</label>
                                <span class = 'select'>
                                    <select stype="account" class="fastEditableSelect" key="accounts.name" modal="account" id="accounts_payable" name="companySettings[accounts_payable]" default="<?= isset($companySettings->accounts_payable) ? $companySettings->accounts_payable : '' ?>"></select>        
                                </span>
                            </p>
                            <p>
                                <label for="undeposited_funds">Undeposited funds</label>
                                <span class = 'select'>
                                    <select stype="account" class="fastEditableSelect" key="accounts.name" modal="account" id="undeposited_funds" name="companySettings[undeposited_funds]" default="<?= isset($companySettings->undeposited_funds) ? $companySettings->undeposited_funds : '' ?>"></select>        
                                </span>
                            </p>
                            <p>
                                <label for="security_deposits">Security deposits</label>
                                <span class = 'select'>
                                    <select stype="account" class="fastEditableSelect" key="accounts.name" modal="account" id="security_deposits" name="companySettings[security_deposits]" default="<?= isset($companySettings->security_deposits) ? $companySettings->security_deposits : '' ?>"></select>        
                                </span>
                            </p>
                        </div>
                        <div>
                        <p>
                                <label for="lmr">Lmr</label>
                                <span class = 'select'>
                                    <select stype="account" class="fastEditableSelect" key="accounts.name" modal="account" id="lmr" name="companySettings[lmr]" default="<?= isset($companySettings->lmr) ? $companySettings->lmr : '' ?>"></select>        
                                </span>
                            </p>
                            <p>
                                <label for="net_income">Net income</label>
                                <span class = 'select'>
                                    <select stype="account" class="fastEditableSelect" key="accounts.name" modal="account" id="net_income" name="companySettings[net_income]" default="<?= isset($companySettings->net_income) ? $companySettings->net_income : '' ?>"></select>        
                                </span>
                            </p>
                            <p>
                                <label for="bank_fees">Bank fees</label>
                                <span class = 'select'>
                                    <select stype="account" class="fastEditableSelect" key="accounts.name" modal="account" id="bank_fees" name="companySettings[bank_fees]" default="<?= isset($companySettings->bank_fees) ? $companySettings->bank_fees : '' ?>"></select>        
                                </span>
                            </p>
                            <p>
                                <label for="interest_income">Interest income</label>
                                <span class = 'select'>
                                    <select stype="account" class="fastEditableSelect" key="accounts.name" modal="account" id="interest_income" name="companySettings[interest_income]" default="<?= isset($companySettings->interest_income) ? $companySettings->interest_income : '' ?>"></select>        
                                </span>
                            </p>
                        </div>
                           

                           
						<div>
                          
                    </section>
                    <section class="b">
					  <div class="double e m20" style="white-space:nowrap; margin-left: 0;">
                        <div>
                        <h4>Default Items</h4>
                            <p>
                                <label for="Default_LC_item">Default_LC_item</label>
                                <span class = 'select'>
                                    <select stype="item" class="fastEditableSelect" key="items.item_name" modal="account" id="Default_LC_item" name="companySettings[Default_LC_item]" default="<?= isset($companySettings->Default_LC_item) ? $companySettings->Default_LC_item : '' ?>"></select>        
                                </span>
                            </p>					

                            <p>
                                <label for="default_RC_item">Default RC item</label>
                                <span class = 'select'>
                                    <select stype="item" class="fastEditableSelect" key="items.item_name" modal="account" id="default_RC_item" name="companySettings[default_RC_item]" default="<?= isset($companySettings->default_RC_item) ? $companySettings->default_RC_item : '' ?>"></select>        
                                </span>
                            </p>
                            <p>
                                <label for="default_sd_item">Default sd item</label>
                                <span class = 'select'>
                                    <select stype="item" class="fastEditableSelect" key="items.item_name" modal="account" id="default_sd_item" name="companySettings[default_sd_item]" default="<?= isset($companySettings->default_sd_item) ? $companySettings->default_sd_item : '' ?>"></select>        
                                </span>
                            </p>
                            <p>
                                <label for="default_lmr_item">Default lmr item</label>
                                <span class = 'select'>
                                    <select stype="item" class="fastEditableSelect" key="items.item_name" modal="account" id="default_lmr_item" name="companySettings[default_lmr_item]" default="<?= isset($companySettings->default_lmr_item) ? $companySettings->default_lmr_item : '' ?>"></select>        
                                </span>
                            </p>
                        </div>
                        <div>
                               <h4>Other settings</h4>

                                <p>
                                    <label for="Closing Date Password">Closing Date Password</label>
                                    <input type="text" name="companySettings[password]" id="password"  value="<?= isset($companySettings) && isset($companySettings->password) ? $companySettings->password : '' ?>">
                                </p>		
                                <p>
                                    <label for="Default_LC_setup">Default Late Charge Setup</label>
                                    <select id="late_charge" name="companySettings[Default_LC_setup]" class="form-control editable-select set-up" key="latecharge.name" modal="custom|leases/getLateChargeModal" value = "<?=isset($companySettings->Default_LC_setup) ? $companySettings->Default_LC_setup: ''; ?>">
                                                    <option value="0">None</option>
                                                    <?php
                                                    foreach ($lcsetups as $setup) {
                                                        echo '<option value="' . $setup->id . '" ' . ($companySettings->Default_LC_setup == $setup->id ? 'selected' : '') . '>' . $setup->name . '</option>';
                                                    } ?>
                                                </select>
                                        </select>
                                </p>



                                <p>
                                    <label for="memorized_transaction_entry">Amount of Days in advance to Enter Memorized Transaction </label>
                                    <input type="text" name="companySettings[memorized_transaction_entry]" id="memorized_transaction_entry"  value="<?= isset($companySettings) && isset($companySettings->memorized_transaction_entry) ? $companySettings->memorized_transaction_entry : 0 ?>">
                                </p>
                                <p>
                                    <label for="email_autocharge_notices">email_autocharge_notices </label>
                                    <input type="text" name="companySettings[email_autocharge_notices]" id="email_autocharge_notices"  value="<?= isset($companySettings) && isset($companySettings->email_autocharge_notices) ? $companySettings->email_autocharge_notices : 0 ?>">
                                </p>
                                <p>
                                    <label for="email_payment_notices">email_payment_notices </label>
                                    <input type="text" name="companySettings[email_payment_notices]" id="email_payment_notices"  value="<?= isset($companySettings) && isset($companySettings->email_payment_notices) ? $companySettings->email_payment_notices : 0 ?>">
                                </p>
                                <p>
                                    <label for="tenant_notification_user">tenant_notification_user </label>

                                    <p>
                                    <select id="tenant_notification_user" name="companySettings[tenant_notification_user]" class="form-control editable-select set-up" key="user.name" modal="user" value = "<?=isset($companySettings->tenant_notification_user) ? $companySettings->tenant_notification_user: ''; ?>">
                                                    <option value="0">None</option>
                                                    <?php
                                                    foreach ($users as $user) {
                                                        echo '<option value="' . $user->id . '" ' . ($companySettings->tenant_notification_user == $user->id ? 'selected' : '') . '>' . $user->username .'('.$user->email .')</option>';
                                                    } ?>
                                                </select>
                                        </select>
                                </p>
                                </p>

                        </div>
                        


                            
					</div>	
					
				</section>

                <footer>
					<ul class="list-btn ">
						<li><button type="submit"  after="mclose">Save &amp; Close</button></li>
						<li><button type="button">Cancel</button></li>
					</ul>
				</footer>
            </form>
        </div>
	</div>
    </div>
</div>