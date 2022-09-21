
<div class="modal fade bank_transfer-modal <?php echo $edit ?>" id="bank_transferModal" tabindex="-1" role="dialog" main-id=<?= isset($header) && isset($header->id) ? $header->id : '-1' ?> type="bank_transfer" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document" style="width: 595px; max-width: 595px;">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; height:550px;">
            <form style= "top:40px; position: initial; bottom:0; right:0; box-shadow: initial; width: 550px;"
                     action="<?php echo $target; ?>" method="post" class="form-fixed bank_transfer" type="bank_transfer"
                    formType=""><!-- formType used for js reload-->
       
                  <header class="modal-h">
					<h2 class="text-uppercase" style ="margin:0px; "><?php echo $title; ?></h2>
                    <nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="#ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
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
                 <?= isset($header) && isset($header->id) ? '<input type = "hidden" name ="id" value="'.$header->id.'">':'';?>
				<table>
					<thead class="dataTables_scrollHead">
						<tr>
						</tr>
					</thead>
					<tbody>
                    <?php $userId = $this->ion_auth->get_user_id(); ?>


          

                                    <p>Transfer balance from
                                        <span class="select" stype = 'profile'>
                                            <label for="transfer1" class="hidden">Label</label>
                                            <select stype="profile" hidden-name = 'transfer1' class="fastEditableSelect" key="profiles.first_name" modal="tenant"  default="<?= isset($from)? $from: ''; ?>" id="profile_id1" onchange="getBalances(this)" name="profile1">
                                            </select>
                                        </span>
                                    </p>

                                    <p>Transfer balance to
                                        <span class="select" stype = 'profile'>
                                            <label for="transfer2" class="hidden">Label</label>
                                            <select stype="profile" hidden-name = 'transfer2' class="fastEditableSelect" key="profiles.first_name" modal="tenant" default="<?= isset($to)? $to: ''; ?>" id="profile_id2" name="profile2">
                                            </select>
                                        </span>
                                    </p>
                                

                                    <p>Date<span  id="dateSpan">
                                        <label for="charge_date"></label>
                                        <input data-toggle="datepicker"  id="charge_date" name="date" value="<?= isset($header)? $header->date : date("m/d/Y"); ?>">
                                                </span>
                                    </p>




                                    <ul class="check-a">

                                            <li>
                                                <label for="rent"> Balance</label>
                                                <span>Rent Balance</span>
                                                <input type="text" style = "width:135px" id="rent" name="rent" value="<?= isset($arAmt) ? $arAmt: 0; ?>" >
                                                <?= isset($ar) ? '<input type="hidden" name ="ar1" value = "'.$ar[0].'"><input type="hidden" name ="ar2" value = "'.$ar[1].'">': ''; ?>                           
                                            </li>
                                            <li>
                                               <label for="sd"> SD</label>
                                               <span>Security</span>
                                               <input type="text" style = "width:135px" id="sd" name="sd" value="<?= isset($sdAmt) ? $sdAmt: 0; ?>">
                                               <?= isset($sd) ? '<input type="hidden" name ="sd1" value = "'.$sd[0].'"><input type="hidden" name ="sd2" value = "'.$sd[1].'">': ''; ?>
                                            </li>
                                            <li>
                                               <label for="lmr"> LMR</label>
                                               <span>LMR </span>
                                               <input type="text" style = "width:135px" id="lmr" name="lmr" value="<?= isset($lmrAmt) ? $lmrAmt: 0; ?>">
                                               <?= isset($lmr) ? '<input type="hidden" name ="lmr1" value = "'.$lmr[0].'"><input type="hidden" name ="lmr2" value = "'.$lmr[1].'">': ''; ?>
                                            </li>
                                        </ul>

                                    <p>Memo
                                        <label for="memo"></label>
                                        <textarea id="memo" name="memo"><?= isset($header->memo)? $header->memo : ""; ?></textarea>
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


