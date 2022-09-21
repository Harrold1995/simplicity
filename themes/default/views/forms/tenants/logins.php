<table class="table table-c b dt" id="#loginstable">
                        <thead>
                            <tr>
									<th width="25%" class="text-center">Portal</th>
                                    <th width="25%" class="text-center">Username</th>
									<th width="18%" class="text-center">Pass</th>
									<th width="25%" class="text-center">Notes</th>
									<th width="7%" class="text-center link-icon"><a id="addVendorButton" href="#addVendor"><i class="icon-plus-circle addVendorButton table-button"></i> <span>Add</span></a></th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr role="row">
                            <td width="25%">
                                <span class="input-amount">
                                    <label for="logins_line_1"></label>
                                    <input type="text" id="logins_line_1" name="temp[logins_line_1]" placeholder="logins line 1">
                                    <input type="hidden" name="temp[ind]" value="<?php echo count($logins);?>">
                                    <input type="hidden" name="temp[profile_id]" value="<?= isset($vendor) && isset($vendor->id) ? $vendor->id : '' ?>">
                                </span>
                            </td>
                            <td width="25%">
                                <span class="input-amount">
                                    <label for="logins_line_2"></label>
                                    <input type="text" id="logins_line_2" name="temp[logins_line_2]" placeholder="logins line 2">
                                </span>
                            </td>
                            <td width="18%">
                                <span class="input-amount">
                                    <label for="city"></label>
                                    <input type="text" id="city" name="temp[city]" placeholder="city">
                                </span>
                            </td>
                            <td width="25%">
                                <span class="input-amount">
                                    <label for="state"></label>
                                    <input type="text" id="state" name="temp[state]" placeholder="state">
                                </span>
                            </td>

                                <td width="7%" class="dt-add">
                                    <a href='#' class="addToTable" source="tableapi/getVendorsRow/logins"><i class="fas fa-plus-circle"></i></a>
                                </td>
                          </tr>

                        <?php  if (isset($logins)){
                            foreach ($logins  as $key=>$logins) {?>					
								
							<tr role="row">
									<!--<td width="7%" class="text-center">Primary</td>-->
                                    <td width="7%" class="text-center"><?= isset($logins) && isset($logins->logins_line_1) ? $logins->logins_line_1 : '' ?></td>
                                        <input name="login[<?=$key ?>][portal]" type="hidden" value="<?=$logins->id ?>"/>
                                        <input name="login[<?=$key ?>][username]" type="hidden" value="<?=$logins->logins_line_1 ?>"/>
                                        <input name="login[<?=$key ?>][password]" type="hidden" value="<?= $logins->logins_line_2 ?>"/>
                                        <input name="login[<?=$key ?>][note]" type="hidden" value="<?=$logins->city ?>"/>
                                        <input name="login[<?=$key ?>][id]" type="hidden" value="<?=$logins->id ?>"/>
                                    <!--</td>-->
									<!--<td width="7%" class="text-center">-->
                                    </td>
                                    <td width="7%" class="text-center"><?= isset($logins) && isset($login->portal) ? $logins->logins_line_2 : '' ?></td>
									<td width="7%" class="text-center"><?= isset($logins) && isset($login->username) ? $logins->city : '' ?></td>
                                    <td width="7%" class="text-center"><?= isset($logins) && isset($login->password) ? $logins->state : '' ?></td>
                                    <td width="7%" class="text-center"><?= isset($logins) && isset($login->note) ? $logins->zip : '' ?></td>>
									<td width="7%" class="text-center" class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td>
								</tr>
                                <?php }} ?>

                        </tbody>
                        <tfoot>
                            <tr></tr><!-- important: this tr is needed to make the add contact work-->
                        </tfoot>
                    </table>