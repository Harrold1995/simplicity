             <table class="table table-c b dt" id="#employeestable">
                        <thead>
                            <tr>
                                    <!--<th width="1%"></th>-->
                                    <th width="7%" class="text-center">First Name</th>
									<th width="7%" class="text-center">Last Name</th>
									<th width="7%" class="text-center">Relationship</th>
									<th width="7%" class="text-center">Home Phone</th>
									<th width="7%" class="text-center">Cell</th>
                                    <th width="7%" class="text-center">Work Phone</th>
                                    <th width="7%" class="text-center">Ext</th>
									<th width="7%" class="text-center">Email</th>
									<th width="7%" class="text-center link-icon"></th>
                            </tr>
                        </thead>
                        <tbody>
                        <tr role="row">
                            <!--<td width="1%"></td>-->
                            <td width="7%">
                                <span class="input-amount">
                                    <label for="first_name"></label>
                                    <input type="text" id="first_name" name="temp[first_name]" placeholder="first name">
                                    <input type="hidden" name="temp[ind]" value="<?php echo count($contacts);?>">
                                </span>
                            </td>
                            <td width="7%">
                                <span class="input-amount">
                                    <label for="last_name"></label>
                                    <input type="text" id="last_name" name="temp[last_name]" placeholder="last name">
                                </span>
                            </td>
                            <td width="7%">
                                <span class="input-amount">
                                    <label for="relation"></label>
                                    <input type="text" id="relation" name="temp[relation]" placeholder="relationship">
                                </span>
                            </td>
                            <td width="7%">
                                <span class="input-amount">
                                    <label for="home"></label>
                                    <input type="text" id="home" name="temp[home]" placeholder="home phone">
                                </span>
                            </td>
                            <td width="7%">
                                <span class="input-amount">
                                    <label for="cell"></label>
                                    <input type="text" id="cell" name="temp[cell]" placeholder="cell">
                                </span>
                            </td>
                            <td width="7%">
                                <label for="tbc" class="hidden">Label</label><!--rename labels-->
                                <span class="input-amount">
                                    <label for="work"></label>
                                    <input type="text" id="work" name="temp[work]" placeholder="work phone">
                                </span>
                            </td>
                            <td width="7%">
                                <label for="tbc" class="hidden">Label</label><!--rename labels-->
                                <span class="input-amount">
                                    <label for="ext"></label>
                                    <input type="text" id="ext" name="temp[ext]" placeholder="ext">
                                </span>
                            </td>
                            <td width="7%">
                                <label for="tbc" class="hidden">Label</label><!--rename labels-->
                                <span class="input-amount">
                                <!--important!! you can't do label for"email" , as this will try to email it.-->
                                    <label for="newEmail"></label>
                                    <input type="text" id="newEmail" name="temp[email]" placeholder="email">
                                </span>
                            </td>
                                <td width="7%" class="dt-add link-icon">
                                    <a href='#' class="addToTable" source="tableapi/getVendorsRow/contact"><i class="icon-plus-circle"><span>add</span></i></a>
                                </td>
                          </tr>

                        <?php  if (isset($contacts)){
                            foreach ($contacts  as $key=>$contact) {?>					
								
							<tr role="row" id="<?=$contact->id ?>">
									<!--<td width="7%" class="text-center">Primary</td>-->
                                    <td width="7%" class="text-center">
                                        <?= isset($contact) && isset($contact->first_name) ? $contact->first_name : '' ?>
                                        <input name="contact[<?=$contact->id ?>][first_name]" type="hidden" value="<?=$contact->first_name ?>"/>
                                        <input name="contact[<?=$contact->id ?>][last_name]" type="hidden" value="<?= $contact->last_name ?>"/>
                                        <input name="contact[<?=$contact->id ?>][relation]" type="hidden" value="<?= $contact->relation ?>"/>
                                        <input name="contact[<?=$contact->id ?>][home]" type="hidden" value="<?=$contact->home ?>"/>
                                        <input name="contact[<?=$contact->id ?>][cell]" type="hidden" value="<?= $contact->cell ?>"/>
                                        <input name="contact[<?=$contact->id ?>][work]" type="hidden" value="<?=$contact->work ?>"/>
                                        <input name="contact[<?=$contact->id ?>][ext]" type="hidden" value="<?= $contact->ext ?>"/>
                                        <input name="contact[<?=$contact->id ?>][email]" type="hidden" value="<?=$contact->email ?>"/>
                                        <input name="contact[<?=$contact->id ?>][id]" type="hidden" value="<?=$contact->id ?>"/>
                                        
                                    <!--</td>-->
									<!--<td width="7%" class="text-center">-->
                                    </td>
									<td width="7%" class="text-center"><?= isset($contact) && isset($contact->last_name) ? $contact->last_name : '' ?></td>
									<td width="7%" class="text-center"><?= isset($contact) && isset($contact->relation) ? $contact->relation : '' ?></td>
									<td width="7%" class="text-center"><?= isset($contact) && isset($contact->home) ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($contact->home)), 2) : '' ?></td>
									<td width="7%" class="text-center"><?= isset($contact) && isset($contact->cell)  ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($contact->cell)), 2) : '';?></td>
                                    <td width="7%" class="text-center"><?= isset($contact) && isset($contact->work)  ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($contact->work)), 2) : '';?></td>
                                    <td width="7%" class="text-center"><?= isset($contact) && isset($contact->ext) ? $contact->ext : '' ?></td>
									<td width="7%" class="text-center" class="email" href="mailto:Hannah123@gmail.com"><?= isset($contact) && isset($contact->email) ? $contact->email : '' ?></td>
                                    <!-- <td width="7%" class="text-center" class="link-icon"><a href="./"><i class="icon-x" aria-hidden="true"></i> <span>Remove</span></a></td> -->
                                    <td class="link-icon dt-delete"><a href="" class="delete-row"><i class="icon-x"></i> <span>Remove</span></a></td> 
								</tr>
                                <?php }} ?>

                        </tbody>
                        <tfoot>
                            <tr></tr><!-- important: this tr is needed to make the add contact work-->
                        </tfoot>
                    </table>