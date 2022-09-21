<?php $userId = $this->ion_auth->get_user_id(); ?>
<form style= "display:none; z-index: 4000;width: 218px;position: fixed;right: 15%;top: 28%; background: white; height:600px;"
 id="addChargeForm" action="Charges/NewCharge" method="post" class="form-fixed chargeForm" type="newCharge"
   formType="<?=isset($addNoteForm->target) ? $addNoteForm->target : '';?>"><!-- formType used for js reload-->
				<h2>New Charge</h2>
				<!-- <p>Tenant
					<label for="tenant"></label>
					<input type="text" id="tenant" name="tenant">
				</p> -->
                <nav>
						<ul>
							<li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
						</ul>
					</nav>
                <p>Tenant
                    <label for="profile_id"></label>
                    <span class="select">
                    <select class="form-control editable-select" id="profile_id" name="newCharge[profile_id]">
                            <option value="0"></option>
                                <?php
                                foreach ($tenants as $tenant) {
                                    echo '<option value="' . $tenant->id . '">' . $tenant->name . '</option>';
                                } ?>
                            </select>
                        </span>
                </p>
                <p>Unit
                    <label for="unit_id"></label>
                    <span class="select">
                    <select class="form-control editable-select" id="unit_id" name="newCharge[unit_id]">
                            <option value="0"></option>
                                <?php
                                foreach ($units as $unit) {
                                    echo '<option value="' . $unit->id . '">' . $unit->name . '</option>';
                                } ?>
                            </select>
                        </span>
                </p>
                <p>Items
                    <label for="item_id"></label>
                    <span class="select">
                    <select class="form-control editable-select" id="item_id" name="newCharge[item_id]">
                            <option value="0"></option>
                                <?php
                                foreach ($items as $item) {
                                    echo '<option value="' . $item->id . '">' . $item->name . '</option>';
                                } ?>
                            </select>
                        </span>
                </p>
                <p>Date
					<label for="transaction_date"></label>
					<input type="date" id="transaction_date" name="newCharge[transaction_date]">
				</p>
                <p>Amount
					<label for="amount"></label>
					<input type="text" id="amount" name="newCharge[amount]">
				</p>
				<p style="height:145px;">Description
					<label for="description"></label>
					<textarea  style="height:130px;" id="description" name="newCharge[description]"></textarea>
				</p>
				<input type="hidden" name="newCharge[userId]" value="<?=isset($userId) ? $userId : '';?>"/>
				<p><button type="submit" after="mclose" id="chargeSubmitButton">Save</button></p>
				<p><button id="newChargeCancelButton" type="button">cancel</button></p>
			</form>