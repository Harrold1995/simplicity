<table class="table table-c b" id="paymethodstable">
                        <thead>
                            <tr>
									<th width="20%" class="text-center">Account Nickname</th>
                                    <th width="20%" class="text-center">Account Number</th>
									<th width="20%" class="text-center">Type</th>
                                    <th width="20%" class="text-center">Default?</th>
                                    <th width="10%" class="text-center"><a href="#" data-type = "Bank" id ="addPayMethod">Add Bank</a></th>
                                    <th width="10%" class="text-center"><a href="#" data-type = "CC" id ="addPayMethod">Add CC</a></th>
                            </tr>
                        </thead>
                        <tbody>
                        

                        <?php  if (isset($paymethods)){
                            foreach ($paymethods  as $key=>$paymethods) {?>					
								
							<tr role="row" data-accno = "<?=$paymethods->account ?>" data-acctype = "<?= isset($paymethods) && ($paymethods->cc == 1) ? 'Credit Card' : 'Bank Account' ?>">
									<!--<td width="7%" class="text-center">Primary</td>-->
                                    <td width="20%" class="text-center"><?= isset($paymethods) && isset($paymethods->nickname) ? $paymethods->nickname : '' ?></td>
                                    <td width="20%" class="text-center">******<?= isset($paymethods) && isset($paymethods->account) ? $paymethods->account : '' ?></td>
									<td width="20%" class="text-center"><?= isset($paymethods) && ($paymethods->cc == 1) ? 'Credit Card' : 'Bank Account' ?></td>
                                    <td width="20%" class="text-center"><?= isset($paymethods) && ($paymethods->isDefault == 1) ? 'Default' : '' ?></td>
                                    <td width="10%" class="text-center"><a href="#" data-id ='<?= $paymethods->id ?>' id="processPayment">Process payment</a>
                                    <?php  if (isset($paymethods->schedule_id)){?>
                                            <a href="#" data-schedule = "<?=$paymethods->schedule_id ?>" data-id ='<?= $paymethods->id ?>' id="deleteAutoPay"> Delete Auto Pay</a>
                                        <?php } else {?>
                                            <a href="#" data-id ='<?= $paymethods->id ?>' id="addAutoPay"> add Auto Pay</a>
                                        <?php } ?>
                                        
                                    </td>
                                    <td width="10%" class="text-center"><a href="#" data-id ='<?= $paymethods->id ?>' id="deletePayMethod">Delete</a></td>
								</tr>
                                <?php }} ?>

                        </tbody>
                        <tfoot>
                            <tr></tr><!-- important: this tr is needed to make the add contact work-->
                        </tfoot>
                    </table>