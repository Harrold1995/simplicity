
<div class="modal fade nsf-modal <?php echo $edit ?>" id="nsfModal" style="display: none;" tabindex="-1" role="dialog" main-id=<?= isset($checks) && isset($checks->id) ? $checks->id : '-1' ?> type="nsf" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document" style="width: 500px;">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; height:600px;">
            <form style= "top:40px; position: initial; bottom:0; right:0; box-shadow: initial; width: 460px;"
                    id="nsfForm" action="<?php echo $target; ?>" method="post" class="form-fixed chargeForm" type="nsf"
                    formType=""><!-- formType used for js reload-->
       <?php if($header){
           echo '<input type="hidden" name="nsf[header_id]" value ="' . $header->id . '"/>';
       }?>
                  <header class="modal-h">
					<h2 class="text-uppercase" style ="margin:0px; "><?php echo $title; ?></h2>
                    <nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
					<nav>
						<ul>
							<li><?= isset($header) ? '<a href="delete/deleteTransaction/'.$header->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
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
                    <input type="hidden" name="nsf[trans_id]" value ="<?php if($id) echo $id;?>"/>
                    <input type="hidden" name="nsf[transaction_id_a]" value ="<?php if($transaction_id_a) echo $transaction_id_a;?>"/>
                                    <p>Profile
                                        
                                        <span stype="profile">
                                            <label for="profile"></label>
                                            <select stype="profile" hidden-name = 'profile' class="fastEditableSelect" key="profiles.first_name" modal="tenant"  default="<?php if($profile) echo $profile.'-'.$lease;?>" id="profile" name="nsf[profile]">
                                            </select>
                                        </span>
                                    </p>
                                    <p>Bank
                                        <label for="bank"></label>
                                        <span>
                                            <select stype="account" class="fastEditableSelect" key="accounts.name" modal="account"  default="<?php if($account_id2) echo $account_id2;?>" id="account" name="nsf[deposit_bank_id]">
                                            </select>
                                        </span>
                                    </p>
                                    <p>Date<span  id="dateSpan">
                                        <label for="charge_date"></label>
                                        <input data-toggle="datepicker"  id="charge_date" name="nsf[transaction_date]" value="<?php if($header->date) echo $header->date;?>">
                                                </span>
                                    </p>
                                    <p>Check number
                                        <label for="check_number"></label>
                                        <input type="text" id="check_number" name="nsf[check_number]" value="<?php if($ref) echo $ref;?>" <?php if($header) echo 'readonly'?>>
                                    </p>
                                    <p><?php echo $header ?  'Amount' : 'fee';?>
                                        <label for="fee"></label>
                                        <input type="text" id="fee" name="nsf[fee]" value="<?php if($header->amount)echo $header->amount;?>" <?php if($header) echo 'readonly'?> >
                                    </p>
                                    <p>Description
                                        <label for="description"></label>
                                        <textarea id="description" name="nsf[description]">Check #<?php if($ref) echo $ref;?> bounced on <?php if($deposit_date) echo $deposit_date;?></textarea>
                                    </p>
                                    <p><button type="submit" after="mclose" id="nsfSubmitButton">Save</button></p>
                                    <p><button id="nsfCancelButton" type="button">cancel</button></p>
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
        console.log('nsf model');
                                     
    </script>
