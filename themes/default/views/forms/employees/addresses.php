<table class="table table-c b dt" id="#employeesstable">
                        <thead>
                            <tr>
									<th width="7%" class="text-center">Address Line 1</th>
                                    <th width="7%" class="text-center">Address Line 2</th>
									<th width="7%" class="text-center">City</th>
									<th width="7%" class="text-center">State</th>
									<th width="7%" class="text-center">Zip</th>
                                    <th width="7%" class="text-center">Apt</th>
                                    <th width="7%" class="text-center">Country</th>
									<th width="7%" class="text-center link-icon"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr role="row">
                            <td width="7%">
                                <span class="input-amount">
                                    <label for="address_line_1"></label>
                                    <input type="text" id="address_line_1" name="temp[address_line_1]" placeholder="address line 1">
                                    <input type="hidden" name="temp[ind]" value="<?php echo count($addresses);?>">
                                    <input type="hidden" name="temp[profile_id]" value="<?= isset($vendor) && isset($vendor->id) ? $vendor->id : '' ?>">
                                </span>
                            </td>
                            <td width="7%">
                                <span class="input-amount">
                                    <label for="address_line_2"></label>
                                    <input type="text" id="address_line_2" name="temp[address_line_2]" placeholder="address line 2">
                                </span>
                            </td>
                            <td width="7%">
                                <span class="input-amount">
                                    <label for="city"></label>
                                    <input type="text" id="city" name="temp[city]" placeholder="city">
                                </span>
                            </td>
                            <td width="7%">
                                <span class="input-amount">
                                    <label for="state"></label>
                                    <input type="text" id="state" name="temp[state]" placeholder="state">
                                </span>
                            </td>
                            <td width="7%">
                                <span class="input-amount">
                                    <label for="zip"></label>
                                    <input type="text" id="zip" name="temp[zip]" placeholder="zip">
                                </span>
                            </td>
                            <td width="7%">
                                <label for="tbc" class="hidden">Label</label><!--rename labels-->
                                <span class="input-amount">
                                    <label for="apt"></label>
                                    <input type="text" id="apt" name="temp[apt]" placeholder="apt">
                                </span>
                            </td>
                            <td width="7%">
                                <label for="tbc" class="hidden">Label</label><!--rename labels-->
                                <span class="input-amount">
                                    <label for="country"></label>
                                    <input type="text" id="country" name="temp[country]" placeholder="country">
                                </span>
                            </td>
                                <td width="7%" class="dt-add link-icon">
                                    <a href='#' class="addToTable" source="tableapi/getVendorsRow/address"><i class="icon-plus-circle"></i></a>
                                </td>
                          </tr>

                        <?php  if (isset($addresses)){
                            foreach ($addresses  as $key=>$address) {?>					
								
							<tr role="row" id="<?=$address->id ?>">
									<!--<td width="7%" class="text-center">Primary</td>-->
                                    <td width="7%" class="text-center"><?= isset($addresses) && isset($address->address_line_1) ? $address->address_line_1 : '' ?></td>
                                        <input name="address[<?=$address->id ?>][profile_id]" type="hidden" value="<?=$address->profile_id ?>"/>
                                        <input name="address[<?=$address->id ?>][address_line_1]" type="hidden" value="<?=$address->address_line_1 ?>"/>
                                        <input name="address[<?=$address->id ?>][address_line_2]" type="hidden" value="<?= $address->address_line_2 ?>"/>
                                        <input name="address[<?=$address->id ?>][city]" type="hidden" value="<?=$address->city ?>"/>
                                        <input name="address[<?=$address->id ?>][state]" type="hidden" value="<?= $address->state ?>"/>
                                        <input name="address[<?=$address->id ?>][zip]" type="hidden" value="<?= $address->zip ?>"/>
                                        <input name="address[<?=$address->id ?>][apt]" type="hidden" value="<?=$address->apt ?>"/>
                                        <input name="address[<?=$address->id ?>][country]" type="hidden" value="<?= $address->country ?>"/>
                                        <input name="address[<?=$address->id ?>][id]" type="hidden" value="<?=$address->id ?>"/>
                                    <!--</td>-->
									<!--<td width="7%" class="text-center">-->
                                    </td>
                                    <td width="7%" class="text-center"><?= isset($addresses) && isset($address->address_line_2) ? $address->address_line_2 : '' ?></td>
									<td width="7%" class="text-center"><?= isset($addresses) && isset($address->city) ? $address->city : '' ?></td>
                                    <td width="7%" class="text-center"><?= isset($addresses) && isset($address->state) ? $address->state : '' ?></td>
                                    <td width="7%" class="text-center"><?= isset($addresses) && isset($address->zip) ? $address->zip : '' ?></td>
									<td width="7%" class="text-center"><?= isset($addresses) && isset($address->apt) ? $address->apt : '' ?></td>
                                    <td width="7%" class="text-center"><?= isset($addresses) && isset($address->country) ? $address->country : '' ?></td>
									<td class="link-icon dt-delete"><a href="" class="delete-row"><i class="icon-x"></i> <span>Remove</span></a></td>
								</tr>
                                <?php }} ?>

                        </tbody>
                        <tfoot>
                            <tr></tr><!-- important: this tr is needed to make the add contact work-->
                        </tfoot>
                    </table>