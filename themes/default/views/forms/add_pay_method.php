          
<div class="modal fade notes-modal" id="notesModal" tabindex="-1" role="dialog" main-id='' type="add_pay_method" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document">
		  <div id="root">
            <div class="modal-content text-primary form-fixed popup-a form-entry shown" style="padding: 25px; height: 600px; width: 400px;">
                  <form action="<?php echo $target; ?>" method="post" ptype="<?php echo $type; ?>" type="add_pay_method" id="add_pay_method" class ="no-submit">
                  <header class="modal-header">
					<h2 class="text-uppercase"><?php echo $title; ?></h2>

        </header>  
        <br>
        <h5>Add a <?php echo $type; ?> payment Method for <b><?php echo $tenant; ?></h5>   
    <?php $userId = $this->ion_auth->get_user_id(); ?>
    <p>Lease
          <span id="leasesSpan"> 
          <label for="lease_id"></label>
            <span class="select"  >
              <select class="form-control editable-select" id="lease_id" name="lid">
                      <?php
                      foreach ($leases as $key=>$lease) {
                          echo '<option value="' . $lease->id . '"'. ($key == 0 ? 'selected' : '') .'>' . $lease->property . '  '. $lease->unit . '  '. $lease->name . '</option>';
                      } ?>
                  </select>
              </span>
          </span><span id="formNames"></span>
        </p>
        
    <?php if ($type == 'Bank'){ ?>
        <p>Account Nickname
          <span>
          <label for="nickname">Amount</label>
            <input type="text" id="nickname" name="nickname">
          </span><span id="formNames"></span>
        </p>

        <p>Account Holder Name
          <span>
          <label for="name">name</label>
            <input type="text" id="name" name="name" value="<?php echo $tenant ?>">
          </span><span id="formNames"></span>
        </p>

        <p>Account Number
          <span>
          <label for="lease_id">Amount</label>
          <iframe style ="max-height: 30px; width:100%" data-ifields-id="ach" data-ifields-placeholder="Checking Account Number" src="https://cdn.cardknox.com/ifields/2.5.1905.0801/ifield.htm"></iframe> 
          </span><span id="formNames"></span>
        </p>





        <p>Routing Number
          <span>
          <label for="routing">routing</label>
            <input type="number" id="routing" name="routing">
          </span><span id="formNames"></span>
        </p>

        <p>Account Type
          <span>
          <label for="type_checking">Amount</label>
          <select name="type_checking" id="type_checking">
            <option value="1">Checking</option>
            <option value="2">Saving</option>
          </select>
          </span><span id="formNames"></span>
        </p>

        <input name="xACH" data-ifields-id="ach-token" type="hidden" /> 

    <?php } else { ?>
       
        <p>Card Nickname
          <span>
          <label for="nickname">Amount</label>
            <input type="text" id="nickname" name="nickname">
          </span><span id="formNames"></span>
        </p>

        <p>Name On Card
          <span>
          <label for="name">Amount</label>
            <input type="text" id="name" name="name" value='<?php echo $tenant ?>'>
          </span><span id="formNames"></span>
        </p>

        <p>Credit Card Number
          <span>
          <label for="lease_id">Amount</label>
          <iframe  style ="max-height: 30px; width:100%" data-ifields-id="card-number" data-ifields-placeholder="Card Number" src="https://cdn.cardknox.com/ifields/2.5.1905.0801/ifield.htm"></iframe> 
          </span><span id="formNames"></span>
        </p>

        <p>Expiration (MMYY)
          <span>
          <label for="exp">Amount</label>
            <input type="number" id="exp" name="exp">
          </span><span id="formNames"></span>
        </p>

        <p>Security
          <span>
          <label for="lease_id">Amount</label>
          <iframe style ="max-height: 30px; width:100%" data-ifields-id="cvv" data-ifields-placeholder="CVV" src="https://cdn.cardknox.com/ifields/2.5.1905.0801/ifield.htm" ></iframe>
          </span><span id="formNames"></span>
        </p>

        
        <input name="xCVV" type="hidden" data-ifields-id="cvv-token" /> 
        <input name="xCardNum" type="hidden" data-ifields-id="card-number-token" />

        <?php } ?>
          <label data-ifields-id="card-data-error" style="color: red;"></label>

          
          <input type="hidden" id="profile_id" name="profile_id" value="<?=isset($profile_id) ? $profile_id : '';?>"/>
          <p><button type="submit" after="mclose" id="new_pay_method">Add Payment Method</button></p>
          <p><button id="noteCancelButton" type="button">cancel</button></p>

            <footer>
            </footer>

          </form>

      </div>
		</div>
  </div>
</div>






<script>

   setAccount("ifields_simplicitydev1b808aac56ac42c4b2591ef9", "cardknox-ifields", "1.0.0");
   

    let style1 = {
      'border-radius': '12px',
      'border-width': '0',
      'background': 'rgba(147, 148, 225, 0.04)',
      'color': '#7581A3',
      'width': '90%',
      'height': '16px',
      'padding': '5px 12px',
      'font-size': '.7em'

    };
    setIfieldStyle('card-number', style1);
    setIfieldStyle('cvv', style1);
    setIfieldStyle('ach', style1);


      


</script>