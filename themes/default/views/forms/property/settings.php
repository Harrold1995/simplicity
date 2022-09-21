
        <section style="background-color: lightgray; color: black; border: none; box-shadow: none;">
            <div class="double e m20">
                <div>
                    <p class="select">Default LC Setup
                        <label for="default_LC_item"></label>
                                <select id="default_LC_item" class="editable-select" name="property[default_LC_item]" style="width: 100px; margin-left: 5px;">
                                <?php
                                        foreach ($lateCharges as $lateCharge) {
                                            echo '<option value="' . $lateCharge->id .'"' . (isset($property) && $property->default_LC_item == $lateCharge->id ? 'selected' : '') .'>' . $lateCharge->name .'</option>';
                                        } ?>
                                </select>    
                            </p>
                            <p class="select">Default RC Item                       
                            <label for="default_RC_item"></label>
                                <select id="default_RC_item" class="editable-select" name="property[default_RC_item]" style="width: 100px; margin-left: 5px;">
                                <?php
                                        foreach ($items as $item) {
                                            echo '<option value="' . $item->id .'"' . (isset($property) && $property->default_RC_item == $item->id ? 'selected' : '') .'>' . $item->name .'</option>';
                                        } ?>
                                </select>
                             </p>
                            <p class="select">Default SD Item
                            
                            <label for="default_SD_item"></label>
                                <select id="default_SD_item" class="editable-select" name="property[default_SD_item]" style="width: 100px; margin-left: 5px;">
                                <?php
                                        foreach ($items as $item) {
                                            echo '<option value="' . $item->id .'"' . (isset($property) && $property->default_SD_item == $item->id ? 'selected' : '') .'>' . $item->name .'</option>';
                                        } ?>
                                </select>
                            </p>
                            <p class="select">Default LMR Item  
                            <label for="default_lmr_item"></label>
                                <select id="default_lmr_item" class="editable-select" name="property[default_lmr_item]" style="width: 100px; margin-left: 5px;">
                                <?php
                                        foreach ($items as $item) {
                                            echo '<option value="' . $item->id .'"' . (isset($property) && $property->default_lmr_item == $item->id ? 'selected' : '') .'>' . $item->name .'</option>';
                                        } ?>
                                </select>
                             </p>
                    </div>
                    <div>
                          <p class="select">Default Lease Template
                            <label for="default_lease_template"></label>
                                <select id="default_lease_template" class="editable-select" name="property[default_lease_template]" style="width: 100px; margin-left: 5px;">
                                <?php
                                        foreach ($ltemplates as $ltemplate) {
                                            echo '<option value="' . $ltemplate->id .'"' . (isset($property) && $property->default_lease_template == $ltemplate->id ? 'selected' : '') .'>' . $ltemplate->name .'</option>';
                                        } ?>
                                </select>
                            </p>
                            <p class="select">Billing Entity 
                            
                            <label for="billing_entity"></label>
                                <select id="billing_entity" class="editable-select" name="property[billing_entity]" style="width: 100px; margin-left: 5px;">
                                <?php
                                        foreach ($entities as $entity) {
                                            echo '<option value="' . $entity->id . '"' . (isset($property) && $property->entity_id == $entity->id ? 'selected' : '') .'>' . $entity->name .'</option>';
                                        } ?>
                                </select>
                        </p>
                        
                        <p class="select">
                            SD Refund Account
                            <label for="sd_refund_account"></label>
                                <select id="sd_refund_account" class="editable-select" name="property[sd_refund_account]" style="width: 100px; margin-left: 5px;">
                                <?php
                                        foreach ($bankAccounts as $bankAccount) {
                                            echo '<option value="' . $bankAccount->id .'"' . (isset($property) && $property->sd_refund_account == $bankAccount->id ? 'selected' : '') .'>' . $bankAccount->name .'</option>';
                                        } ?>
                                </select>
                            </p>
                            <p>
                                    <label for="key_code" >Key Code</label>
                                        <textarea onkeyup="JS.textAreaAdjust(this)"  id="key_code" name="property[key_code]" style ="min-height:70px" rows="5"><?= isset($property) && isset($property->key_code) ? $property->key_code : '' ?></textarea>

                            </p>
                    </div>
                </div>
        </section>