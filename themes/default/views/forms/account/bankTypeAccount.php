<h2 class="header-a text-center">Bank</h2>

	<ul class="list-a">
         
	  <li>
      <?php if(isset($specialAccount->custom)){
        $bankinfo = json_decode($specialAccount->custom); ?> 
      <label for="connect bank">connected to <?php echo $bankinfo->plaid_acct; ?></label>
      <a id ="linkButton"  onclick = "linkHandler.open();" >change</a>
      <?php } else {?>
		  <label for="connect bank">connect your bank:</label> 
		  <a id ="chooseBankButton"   >connect</a>
      <?php }?>
		</li>
		<li>
		  <label for="bank_name">Bank Name:</label> 
		  <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->bank_name) ? $specialAccount->bank_name : '' ?>" class="form-control" name="specialAccount[bank_name]" id="bank_name" >
		</li>

		<li>
		  <label for="bank_address">Bank Address:</label> 
		  <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->bank_address) ? $specialAccount->bank_address : '' ?>" class="form-control" name="specialAccount[bank_address]" id="bank_address" >
		</li>

		<li>
		  <label for="routing">Routing:</label> 
		  <input type="number" value="<?= isset($specialAccount) && isset($specialAccount->routing) ? $specialAccount->routing : '' ?>" class="form-control getBankName" name="specialAccount[routing]" id="routing"  >
		</li>

		<li>
		  <label for="account_number">Account Number:</label> 
		  <input type="number" value="<?= isset($specialAccount) && isset($specialAccount->account_number) ? $specialAccount->account_number : '' ?>" class="form-control" name="specialAccount[account_number]" id="account_number"  >
		</li>
		
		<li>
		<label for="next_check_num">Next Check Number:</label> 
		  <input type="number" value="<?= isset($specialAccount) && isset($specialAccount->next_check_num) ? $specialAccount->next_check_num : '' ?>" class="form-control" name="specialAccount[next_check_num]" id="next_check_num"  >
		</li>

		<li>
       
                  
                    <label for="property">Property:</label>
                    <span class = 'select'>
                      <select stype="property" class="fastEditableSelect" key="properties.name" modal="property" id="bankProperty" name="specialAccount[property]" value="<?= isset($specialAccount) ? $specialAccount->prop_name : '' ?>"></select>        
                    </span>
		
      
    </li>







	</ul>

  <style type="text/css" onload="loadSelect($(this).closest('.modal'))"></style>

<input type="hidden" name="table" value="banks"/>
<input type="hidden" name="specialAccount[id]" value="<?= isset($specialAccount) && isset($specialAccount->id) ? $specialAccount->id : '' ?>"/>

<script>

  function loadSelect(modal){
    $(modal).find('.fastEditableSelect').fastSelect();
  }
  
   var linkHandler = Plaid.create({
    selectAccount: true,
    env: '<?php echo $plaid_env ?>',
    apiVersion: 'v2',
    clientName: 'Client Name',
    key: '<?php echo $plaid_public ?>',
    product: ['auth', 'transactions', 'identity'],
    webhook: 'https://myurl.com/webhooks/p_responses.php',
    onEvent: function (event, metadata)  {
    // send event and metadata to self-hosted analytics
    //analytics.send(eventName, metadata);
    //console.log('selected');
    //console.log(event);
     },
    onLoad: function() {
      // The Link module finished loading.
    },
    onSuccess: function(public_token, metadata) {
    // The onSuccess function is called when the user has successfully
    // authenticated and selected an account to use. 
    console.log(metadata);   

      $.post( 'accounts/process_plaid_token', {pt:public_token,md:metadata,id:"<?php echo $account->id?>", bank:"<?php echo $specialAccount->id?>"}, function( data ) {                        
          console.log("data : "+data);
           if (data=="Success"){              
              console.log(data);
              alert("thankyou");//Let users know the process was successful 
           }else if (data=="duplicate"){
             console.log(data);
             alert("duplicate");//Let users know they already have a login
           }else{
             console.log(data);
             alert("error");//Let users know the process failed
           }
        });    
    },
    onExit: function(err, metadata) {
      // The user exited the Link flow. This is not an Error, so much as a user-directed exit   
      if (err != null) {
        console.log(err);
        console.log(metadata);        
      }
    },
  }); 
  </script>