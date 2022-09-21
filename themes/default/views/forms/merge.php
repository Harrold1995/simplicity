
<div class="modal fade bank_transfer-modal <?php echo $edit ?>" id="mergeaccounts" tabindex="-1" role="dialog" main-id=<?= isset($bank_transfer) && isset($bank_transfer->id) ? $checks->id : '-1' ?> type="Merge" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document" style="width: 500px;">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; height:530px;">
            <form style= "top:40px; position: initial; bottom:0; right:0; box-shadow: initial; width: 460px;"
                     action="accounts/mergeAccounts" method="post" class="form-fixed bank_transfer" type="bank_transfer"
                    formType=""><!-- formType used for js reload-->
       
                  <header class="modal-h">
					
                    <nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="#ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>		
				</header>

                <h2 class="text-uppercase">Merge Two Accounts</h2>
                <h2><?php echo $message ?></h2>

              <div class ="has-table-c">
				<table>
					<thead class="dataTables_scrollHead">
						<tr>
						</tr>
					</thead>
					<tbody>

                                    
                                    
                                    
                                    <p><button type="submit" after="mclose" id="bank_transferSubmitButton">Merge</button></p>
                                    <p><button id="bank_transferCancelButton" type="button">cancel</button></p>
                    </tbody>
                    <input name="accounta" type="hidden" value='<?php echo $confirm_a->id ?>'>
                    <input name="accountb" type="hidden" value='<?php echo $confirm_b->id ?>'>
						  
					<tfoot>
						<tr>
						</tr>
					</tfoot>
					
				</table>
			</div>

        <footer>
         
        </footer>
                            </form>   
                    </div>
		</div>

</div>
</div>