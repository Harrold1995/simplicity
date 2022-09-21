
<div class="modal fade bank_transfer-modal <?php echo $edit ?>" id="bank_transferModal" tabindex="-1" role="dialog" main-id=<?= isset($bank_transfer) && isset($bank_transfer->id) ? $checks->id : '-1' ?> type="bank_transfer" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document" style="width: 500px;">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; height:530px;">
            <form style= "top:40px; position: initial; bottom:0; right:0; box-shadow: initial; width: 460px;"
                     action="<?php echo $target; ?>" method="post" class="form-fixed bank_transfer" type="bank_transfer"
                    formType=""><!-- formType used for js reload-->
       
                  <header class="modal-h">
					<h2 class="text-uppercase" style ="margin:0px; background-color: lightgreen; margin-left: 22%;"><?php echo $title; ?></h2>
                    <nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="#ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>		
				</header>

       

              <div class ="has-table-c">
				<table>
					<thead class="dataTables_scrollHead">
						<tr>
						</tr>
					</thead>
					<tbody>
                    <?php $userId = $this->ion_auth->get_user_id(); ?>

                                    <p>Transfer funds from
                                        <span>
                                        <label for="from_account_id"></label>
                                            <span class="select">
                                            <select class="form-control editable-select" id="from_account_id" name="bank_transfer[from_account_id]">
                                            <option value=""></option>
                                                    <?php
                                                    foreach ($banks as $bank) {
                                                        echo '<option value="' . $bank->id . '">' . $bank->name . '</option>';
                                                    } ?>
                                                </select>
                                            </span>
                                        </span><span id="formNames"></span>
                                    </p>
                                    <p>Transfer funds to
                                    <span>
                                        <label for="to_account_id"></label>
                                            <span class="select">
                                            <select class="form-control editable-select" id="to_account_id" name="bank_transfer[to_account_id]">
                                            <option value=""></option>
                                                <?php
                                                    foreach ($banks as $bank) {
                                                        echo '<option value="' . $bank->id . '">' . $bank->name . '</option>';
                                                    } ?>
                                                </select>
                                            </span>
                                    </span>
                                    </p>
                                    <p>Property
                                        <label for="property_id"></label>
                                        <span class="select">
                                        <select class="form-control editable-select" id="property_id" name="bank_transfer[property_id]">
                                                <option value="0"></option>
                                                    <?php
                                                    foreach ($properties as $propertie) {
                                                        echo '<option value="' . $propertie->id . '">' . $propertie->name . '</option>';
                                                    } ?>
                                                </select>
                                            </span>
                                    </p>
                                    <p>Date<span  id="dateSpan">
                                        <label for="charge_date"></label>
                                        <input data-toggle="datepicker"  id="charge_date" name="bank_transfer[date]" value="<?php echo date("m/d/Y");?>">
                                                </span>
                                    </p>
                                    <p>Amount
                                        <label for="amount"></label>
                                        <input type="text" id="amount" name="bank_transfer[amount]" value="">
                                    </p>
                                    <p>Memo
                                        <label for="memo"></label>
                                        <textarea id="memo" name="bank_transfer[memo]"></textarea>
                                    </p>
                                    <p><button type="submit" after="mclose" id="bank_transferSubmitButton">Save</button></p>
                                    <p><button id="bank_transferCancelButton" type="button">cancel</button></p>
                    </tbody>
						  
					<tfoot>
						<tr>
						</tr>
					</tfoot>
					
				</table>
			</div>

        <footer>
        <?= $header ? 
        "<div>Last Modified $header->modified<div>
         <div>Last Modified by <a href='#!'>$header->user</a><div>" : '';
        ?>
         
        </footer>
                            </form>   
                    </div>
		</div>

</div>
</div>

    <script>
        console.log('bank transfer model');
    </script>
