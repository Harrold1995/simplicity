<div class="modal fade tenanttolease-modal" type="Tenant-to-lease" data-type="tenanttolease" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-height: 200px;" role="document">
        <div id="root">
        <div class="modal-content form-entry" style = 'padding: 25px'>
            <form action="<?php echo $target; ?>" method="post">
                <header class="modal-h ui-draggable-handle">
                    <h2 class="text-uppercase" id="exampleModalLongTitle"><?php echo $title; ?></h2>
                    <input type="hidden" id="lease_id" name="lease_id" value="<?= isset($lease_id)  ? $lease_id : 0 ?>">
                    <nav class= 'window-options'>
                                <ul>
                                    <li>
                                        <span class="buttons" style=""><span class="min" style="padding: 8px 20px;cursor: pointer;">_</span></span>
                                    </li>
                                    <li>
                                        <span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span>
                                    </li>
                                    <li>
                                        <span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span>
                                    </li>
                                </ul>
                            </nav>
                </header>
                <section class="plain modal-body" style="border-style: none; box-shadow:none;">
                <br>
                <h2 class="header-a text-center"></h2>
                        <div class=" d80 m20 double a" id="class_modal_a">
                        
                            <div>
                                <ul id="setupprint" class="list-input m30 plain">
                                    <li>
                                        <label for="tenant" class="col-auto">Tenant</label>
                                        <span stype="vendors" class='select'>
                                            <input  hidden-name = 'profile_id' name="profile_id" class="fastEditableSelect es-setup" stype="profile2" type="table" value="<?php echo (isset($ttl->profile_id)) ?  $ttl->profile_id  : ''; ?>"  key="tenant.name" modal="tenant"  default="<?php echo (isset($ttl->profile_id)) ?  $ttl->profile_id  : ''; ?>"></input>
                                        </span>
                                        <?php if (isset($ttl) && isset($ttl->id)) echo '<input type="hidden" name="id" value="' . $ttl->id . '"/>'; ?>
                                    </li>
                                    <li>
                                        <label for="unit_id" class="col-auto">Unit</label>
                                            <select class="form-control" id="unit_id" name="unit_id">
                                                <?php
                                                if (isset($units))
                                                    foreach ($units as $unit) {
                                                        echo '<option value="' . $unit->id . '" ' . (isset($ttl) && $ttl->unit_id == $unit->id ? 'selected' : '') . '>' . $unit->name . '</option>';
                                                    } ?>
                                            </select>
                                    
                                    </li>
                                    <li>
                                        <label for="amount" class="col-auto">Rent Amount</label>
                                        <input type="text" value="<?= isset($ttl) && isset($ttl->amount) ? number_format(str_replace(',', '', $ttl->amount), 2) : '' ?>" class="form-control" name="amount" id="amount">
                                    </li>                                    
                                    <li>
                                        <label for="deposit" class="col-auto">Security Deposit</label>
                                        <input type="text" value="<?= isset($ttl) && isset($ttl->deposit) ? number_format(str_replace(',', '', $ttl->deposit), 2) : '' ?>" class="form-control" name="deposit" id="deposit">
                                    
                                    </li>
                                    <li>
                                        <label for="last_month" class="col-auto">Last Month's Rent</label>
                                            <input type="text" value="<?= isset($ttl) && isset($ttl->last_month) ? number_format(str_replace(',', '', $ttl->last_month), 2) : '' ?>" class="form-control" name="last_month" id="last_month">
                                        
                                    </li>
                                    <li>
                                    <label for="late_charge" class="col-auto">Late Charge Setup</label>
                                    
                                    <select class="form-control" id="late_charge" name="late_charge">
                                    <option value="-1"></option>
                                            <?php
                                            if (isset($lateCharges))
                                                foreach ($lateCharges as $lateCharge) {
                                                    echo '<option value="' . $lateCharge->id . '" ' . (isset($ttl) && $ttl->late_charge == $lateCharge->id ? 'selected' : '') . '>' . $lateCharge->name . '</option>';
                                                } ?>
                                        </select>
                                   
                                    </li>
                                </ul>
                            </div>
                            
                            <div>
                                <ul id="setupprint" class="list-input m30 plain">
                                    <li>
                                        <label for="move_in" class="col-auto">Move In Date</label>
                                        <div class="col field-input">
                                            <input class="form-control leaveEmpty" type="text" data-toggle="datepicker" value="<?= isset($ttl) && isset($ttl->move_in) ? $ttl->move_in : date('Y-m-d') ?>" name="move_in" id="move_in">
                                        </div>
                                    </li>
                                    <li>
                                        <label for="move_out" class="col-auto">Move Out Date</label>
                                        <div class="col field-input">
                                            <input class="form-control leaveEmpty " type="text" data-toggle="datepicker" value="<?= isset($ttl) && isset($ttl->move_out) ? $ttl->move_out : '' ?>" name="move_out" id="move_out">
                                        </div>
                                    </li>
                                    <li>
                                        <label for="tenant" class="col-auto">Pet Type</label>
                                        <div class="col field-select">
                                            <select class="form-control" id="pets" name="pets"
                                                    onchange="$($(this).closest('.modal').find('#pet-deposit')[0]).toggle($(this).val() > 0).find('input').val($(this).find('option:selected').attr('deposit'));">
                                                <?php
                                                if (isset($pet_types))
                                                    foreach ($pet_types as $id => $pet) {
                                                        echo '<option value="' . $id . '" ' . (isset($ttl) && $ttl->pets == $id ? 'selected' : '') . ' deposit="'.$pet['deposit'].'">' . $pet['name'] . '</option>';
                                                    } ?>
                                            </select>

                                        </div>
                                    </li>
                                    <li id ='pet-deposit' <?= !isset($ttl) || $ttl->pets == 0 ? ' style="display:none;"' : '' ?>>
                                            <label for="pet_deposit" class="col-auto">Pet Deposit</label>
                                            <div class="col field-input">
                                                <input type="text" value="<?= isset($ttl) ? number_format($ttl->pet_deposit, 2) : '' ?>" class="form-control" name="pet_deposit" id="last_month">
                                            </div>
                                        
                                    </li>


                                <ul class="check-a a">
                                                <li><label for="restrict_payments" style ="padding-right: 40px; width: 100%;" class="checkbox <?= isset($ttl) && ($ttl->restrict_payments == 1) ? 'active' : '' ?>"><input type="hidden" name="restrict_payments" value="0" /><input type="checkbox"  value="1" <?= isset($ttl) && ($ttl->restrict_payments == 1) ? 'checked' : '' ?> id="restrict_payments" name="restrict_payments"  class="hidden" aria-hidden="true"><div class="input"></div> Restrict Payments</label></li>
                                                <br/>
                                                <!-- needs name and to be hooked up -->
                                                <li><label for="active" style ="padding-right: 40px; width: 100%;" class="checkbox <?= isset($ttl) && ($ttl->active == 0) ? '' : 'active' ?>"><input type="hidden" name="active" value="0" /><input type="checkbox" value="1" <?= isset($ttl) && ($ttl->active == 0) ? '' : 'checked' ?> id="active" name="active"  class="hidden" aria-hidden="true"><div class="input"></div> Active</label></li>
                                                
                            </ul>
                                </ul>
                            </div>
                        </div>
                        <label for="memo" class="col-auto">Memo</label>
                                    <div class="col field-input">
                                        <textarea onkeyup="JS.textAreaAdjust(this)" class="form-control" id="memo" name="memo" rows="1"><?= isset($ttl) && isset($ttl->memo) ? $ttl->memo : '' ?></textarea>
                                    </div>
                </section>
                
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary tenantToLeaseClose">
                        Save
                    </button>
                    <button type="button" class="btn btn-secondary tenantToLeaseClose" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
        </div>
        
    </div>
</div>