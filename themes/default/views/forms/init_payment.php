            
<div class="modal fade notes-modal" id="notesModal" tabindex="-1" role="dialog" main-id='' type="init_payment" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document">
		  <div id="root">
            <div class="modal-content text-primary form-fixed popup-a form-entry shown" style="padding: 25px; height: 400px; width: 400px;">
                  <form action="<?php echo $target; ?>" method="post" type="init_payment" class ="no-submit">
                  <header class="modal-header">
					<h2 class="text-uppercase"><?php echo $title; ?></h2>

        </header>  
        <br>
        <h5>Process a payment for <b><?php echo $tenant; ?></b> using his/her <b><?php echo $payment_info->account_type; ?></b> account ending in <b><?php echo $payment_info->account_mask; ?></b></h5>   
    <?php $userId = $this->ion_auth->get_user_id(); ?>
        <p>Lease
          <span id="leasesSpan"> 
          <label for="lease_id"></label>
            <span class="select"  >
              <select class="form-control editable-select" id="lease_id" name="transactions[lease_id]">
                      <?php
                      foreach ($leases as $key=>$lease) {
                          echo '<option value="' . $lease->id . '"'. ($key == 0 ? 'selected' : '') .'>' . $lease->property . '  '. $lease->unit . '  '. $lease->name . '</option>';
                      } ?>
                  </select>
              </span>
          </span><span id="formNames"></span>
        </p>

        <p>Amount
          <span>
          <label for="lease_id">Amount</label>
            <input type="number" id="amount" name="title">
          </span><span id="formNames"></span>
        </p>

          <input type="hidden" id="bank_id" name="bank_id" value="<?=isset($payment_info->bank_account) ? $payment_info->bank_account : '';?>"/>
          <input type="hidden" id="profile_id" name="profile_id" value="<?=isset($payment_info->profile_id) ? $payment_info->profile_id : '';?>"/>
          <p><button type="submit" after="mclose" id="initPaymentSubmitButton">Process Payment</button></p>
          <p><button id="noteCancelButton" type="button">cancel</button></p>

            <footer>
            </footer>

          </form>

      </div>
		</div>
  </div>
</div>






<script>

      


</script>