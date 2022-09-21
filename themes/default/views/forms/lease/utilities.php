<table class="table table-c b dt" id="#utilitiestable">
                        <thead>
                            <tr>
                                <th width="9%">Type</th>
                                <th width="10%">Unit</th>
                                <th width="13%">Description</th>
                                <th width="14%">Account#</th>
                                <th width="11%">Meter#</th>
                                <th width="11%">Default Exp</th>
                                <th width="11%">Payee</th>
                                <th width="3%">DP?</th>
                                <th width="8%">Paid by</th>
                                <th width="5%"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr role="row">
                            <td>
                              <span >
                                <label for="utility_type" class="hidden">Label</label><!--rename labels-->
                                <span class="select">
                                  <select id="utility_type" class="editable-select" name="temp[utility_type]">
                                    <?php
                                        if (isset($utilityTypes))
                                            foreach ($utilityTypes as $type) {
                                                echo '<option value="' . $type->id .'+' . $type->name . '">' . $type->name . '</option>';
                                            }
                                    ?>
                                  </select>
                                </span>
                              </span>
                              <input type="hidden" name="temp[ind]" value="<?php echo count($utilities);?>">
                            </td>
                            <td>
                              <span >
                                <label for="unit_id" class="hidden">Label</label>
                                <span class="select">
                                <select id="unit_id" class="editable-select" name="temp[unit_id]">

                                <?php
                                    if (isset($units))
                                        foreach ($units as $unit) {
                                            echo '<option value="' . $unit->id .'+' . $unit->name . '">' . $unit->name . '</option>';
                                        }
                                ?>
                                </select>
                                </span>
                              </span>
                            </td>
                            <td>
                                <span class="input-amount">
                                    <label for="description"></label>
                                    <input type="text" id="description" name="temp[description]">
                                </span>
                            </td>
                            <td>
                                <span class="input-amount">
                                    <label for="account"></label>
                                    <input type="text" id="account" name="temp[account]">
                                </span>
                            </td>
                            <td>
                                <span class="input-amount">
                                    <label for="meter"></label>
                                    <input type="text" id="meter" name="temp[meter]">
                                </span>
                            </td>
                            <td>
                            <span >
                                <label for="default_expense_acct"></label>
                                <span class="select">
                                <select id="default_expense_acct" class="editable-select" name="temp[default_expense_acct]">
                                <?php
                                    echo '<option class="nested0" value="0"></option>'; 
                                    if (isset($subaccounts))
                                        foreach ($subaccounts as $subaccount) {
                                            echo '<option data-id="'.$subaccount->id.'" data-parent-id="'.$subaccount->parent_id.'" class="nested'.$subaccount->step.'"value="' . $subaccount->id . '+' . $subaccount->name .'"  >' . $subaccount->name . '</option>';
                                    } ?>
                                </select>
                                </span>
                              </span>
                            </td>
                            <td>
                            <span >
                                <label for="payee"></label>
                                <span class="select">
                                <select id="payee" class="editable-select" name="temp[payee]">
                                <?php
                                    foreach ($vendors as $vendor) {
                                        echo '<option value="' . $vendor->id .'+' . $vendor->vendor .'">' . $vendor->vendor .'</option>';
                                    } ?>
                                </select>
                                </span>
                              </span>
                              </td>
                            <td>
                                <ul class="check-a a">
                                    <li><label for="direct_payment" class="checkbox "><input type="checkbox" value="1"  id="direct_payment" name="temp[direct_payment]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                                </ul>
                            </td>
                            <td>
                            <span >
                                <label for="paid_by"></label>
                                <input type="text" id="paid_by" name="temp[paid_by]">
                              </td>
                                <td class="dt-add">
                                    <a href='#' class="addToTable" source="tableapi/getUtilitiesRow/utilities"><i class="fas fa-plus-circle"></i></a>
                                </td>
                          </tr>

                          <?php
                            if (isset($utilities))
                                foreach ($utilities as $utility) {
                                echo '<tr role="row"  id="' . $utility->id . '">
                                        <td>' . $utility->utility_type . '
                                        <input name="utilities[' .$utility->id . '][utility_type]" type="hidden" value="' . $utility_type . '"/>
                                        <input name="utilities[' . $utility->id . '][unit_id]" type="hidden" value="' . $utility->unit . '"/>
                                        <input name="utilities[' . $utility->id . '][description]" type="hidden" value="' . $utility->description . '"/>
                                        <input name="utilities[' . $utility->id . '][account]" type="hidden" value="' . $utility->account . '"/>
                                        <input name="utilities[' . $utility->id . '][meter]" type="hidden" value="' . $utility->meter . '"/>
                                        <input name="utilities[' . $utility->id . '][default_expense_acct]" type="hidden" value="' . $utility->default_expense_acct . '"/>
                                        <input name="utilities[' . $utility->id . '][payee]" type="hidden" value="' . $utility->payee . '"/>
                                        <input name="utilities[' . $utility->id . '][direct_payment]" type="hidden" value="' . $utility->direct_payment . '"/>
                                        <input name="utilities[' . $utility->id . '][paid_by]" type="hidden" value="' . $utility->paid . '"/>
                                        <input name="utilities[' . $utility->id . '][id]" type="hidden" value="' . $utility->id . '"/>
                                        </td>
                                        <td>' . $utility->unit  . '</td>
                                        <td>' . $utility->description  . '</td>
                                        <td>' . $utility->account  . '</td>
                                        <td>' . $utility->meter  . '</td>
                                        <td>' . $utility->default_expense_acct  . '</td>
                                        <td>' . $utility->payee  . '</td>
                                        <td>
                                        <div class="custom-control custom-checkbox form-group">
                                        <input id="dp" type="checkbox" class="custom-control-input" disabled ' . ($utility->direct_payment == 1 ? checked : '') . '>
                                        <label class="custom-control-label checkbox-left ' . ($utility->direct_payment == 1 ? active : '') . '" for="sp"></label>
                                        </div>
                                        </td>
                                        <td>' . $utility->paid  . '</td>
                                        <td class="text-center link-icon dt-delete"><a href="" class="delete-row"><i class="icon-x"></i></a></td>       
                                    </tr>';
                                }
                        ?>

                        </tbody>
                        <tfoot>
                            <tr></tr><!-- important: this tr is needed to make the add utilities work-->
                        </tfoot>
                    </table>