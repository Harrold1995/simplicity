<h2 class="header-a text-center">Credit Card Info</h2>



  <ul class="list-a">

    <li>
      <label for="cc_num" >Credit card Type:</label>
     <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->title) ? $specialAccount->title : '' ?>"  class="form-control" name="specialAccount[title]" id="title" placeholder="Enter Credit card Type">
    </li>

    <li>
      <label for="vendor">Credit card Number:</label> 
       <input type="number" value="<?= isset($specialAccount) && isset($specialAccount->cc_num) ? $specialAccount->cc_num : '' ?>" class="form-control" name="specialAccount[cc_num]" id="cc_num" placeholder="Enter Credit card Number">
    </li>

    <li>
      <label for="security_code">Security Code:</label>
      <input type="number" value="<?= isset($specialAccount) && isset($specialAccount->security_code) ? $specialAccount->security_code : '' ?>" class="form-control" name="specialAccount[security_code]" id="security_code" placeholder=" ">
    </li>

    <li>
      <label for="expiration">Expiration Date:</label>
      <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->expiration) ? $specialAccount->expiration : '' ?>"  class="form-control" name="specialAccount[expiration]" id="expiration" placeholder="MM / YY">
    </li>

    <li>
      <label for="billing_address" >Billing Address:</label>
     <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->billing_address) ? $specialAccount->billing_address : '' ?>"  class="form-control" name="specialAccount[billing_address]" id="billing_address" placeholder="Enter billing address">
    </li>

    <li>
      <label for="card_holder">Card Holder:</label> 
       <input type="number" value="<?= isset($specialAccount) && isset($specialAccount->card_holder) ? $specialAccount->card_holder : '' ?>" class="form-control" name="specialAccount[card_holder]" id="card_holder" placeholder="Enter Card holder">
    </li>

    <li>
      <label for="statement_url">Statement URL:</label>
      <input type="number" value="<?= isset($specialAccount) && isset($specialAccount->statement_url) ? $specialAccount->statement_url : '' ?>" class="form-control" name="specialAccount[statement_url]" id="statement_url" placeholder=" ">
    </li>

    <li>
      <label for="username">Username:</label>
      <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->username) ? $specialAccount->username : '' ?>"  class="form-control" name="specialAccount[username]" id="username" placeholder="Username">
    </li>

     <li>
      <label for="password">Password:</label>
      <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->password) ? $specialAccount->password : '' ?>"  class="form-control" name="specialAccount[password]" id="password" placeholder="Enter Password">
    </li>


  </ul>



      

  <input type="hidden" name="table" value="credit_cards"/>


    


 


  
 


