<h2 class="header-a text-center">Credit Card Info</h2>



  <ul class="list-a">

    <li>
      <label for="account_type" >Credit card Type:</label>
     <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->account_type) ? $specialAccount->account_type : '' ?>"  class="form-control" name="specialAccount[account_type]" id="account_type" placeholder="Enter Credit card Type">
    </li>

    <li>
      <label for="cc_num">Credit card Number:</label> 
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

    <!-- <li>
      <label for="profile_id">Card Holder:</label> 
       <input type="text" value="< ?= isset($specialAccount) && isset($specialAccount->profile_id) ? $specialAccount->profile_id : '' ?>" class="form-control" name="specialAccount[profile_id]" id="profile_id" placeholder="Enter Card holder">
    </li> -->
    <li id="allProfiles">
    <?php if(isset($specialAccount)){ ?>
        <label for="profile_id" >Profile:</label>  
      <span class="">
        <select class="editable-select quick-add set-up inputInfo" id="profile_id" name="specialAccount[profile_id]">
        <?php
                  foreach ($profiles as $profile) {
                      echo '<option value="' . $profile->id .'"' . (isset($specialAccount) && $specialAccount->profile_id == $profile->id ? 'selected' : '') . '>' . $profile->name . '</option>';
                  } ?>
        </select>
        </span>
        <?php } ?>
    </li>

    <li id="allVendors">
    <?php if(isset($specialAccount)){ ?>
        <label for="vendor" >Vendor:</label>  
      <span class="">
        <select class="editable-select quick-add set-up inputInfo" id="vendor" name="specialAccount[vendor]">
        <?php
                  foreach ($vendors as $vendor) {
                      echo '<option value="' . $vendor->id .'"' . (isset($specialAccount) && $specialAccount->vendor == $vendor->id ? 'selected' : '') . '>' . $vendor->name . '</option>';
                  } ?>
        </select>
        </span>
        <?php } ?>
    </li>


    <li id="allProperties">
    <?php if(isset($specialAccount)){ ?>
    <label for="property_id" >Property:</label>  
        <span class="">
          <select class="editable-select quick-add set-up inputInfo" id="property_id" name="specialAccount[property_id]">
              <?php
                  foreach ($allProperties as $property) {
                      echo '<option value="' . $property->id .'"' . (isset($specialAccount) && $specialAccount->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
                  } ?>
          </select>
          </span>
    <?php } ?>
    </li>

  </ul>



      

  <input type="hidden" name="table" value="credit_cards"/>
  <input type="hidden" name="specialAccount[id]" value="<?= isset($specialAccount) && isset($specialAccount->id) ? $specialAccount->id : '' ?>"/>

<style type="text/css" onload="<?= isset($specialAccount)? '' : 'getVendorsAndPropeties()';?>"></style>
<script>

function getVendorsAndPropeties(){
      var newRow = `<label for="vendor" >Vendor:</label>  
                      <span>
                        <select class="editable-select quick-add set-up inputInfo" id="vendor" name="specialAccount[vendor]">`;
            for (var i = 0; i < vendors.length; i++) {
		           newRow += `<option value="` + vendors[i].id +`"`;
               newRow += ` >` + vendors[i].name + `</option>`;
	      }
      newRow += `</select>
                  </span>`;
      $("#allVendors").empty();
      $("#allVendors").append(newRow);
      $('#allVendors').find('.editable-select').editableSelect();

      var propertyRow = `<label for="property_id" >Property:</label>  
                      <span>
                        <select class="editable-select quick-add set-up inputInfo" id="property_id" name="specialAccount[property_id]">`;
            for (var i = 0; i < allProperties.length; i++) {
		           propertyRow += `<option value="` + allProperties[i].id +`"`;
              propertyRow += `>` + allProperties[i].name + `</option>`;
	      }
      propertyRow += `</select>
                  </span>`;
      $("#allProperties").empty();
      $("#allProperties").append(propertyRow);
      $('#allProperties').find('.editable-select').editableSelect();

      var profileRow = `<label for="profile_id" >Profile:</label>  
                      <span>
                        <select class="editable-select quick-add set-up inputInfo" id="profile_id" name="specialAccount[profile_id]">`;
            for (var i = 0; i < profiles.length; i++) {
              profileRow += `<option value="` + profiles[i].id +`"`;
              profileRow += `>` + profiles[i].name + `</option>`;
	      }
        profileRow += `</select>
                  </span>`;
      $("#allProfiles").empty();
      $("#allProfiles").append(profileRow);
      $('#allProfiles').find('.editable-select').editableSelect();
}
</script>

<!--// (isset($specialAccount) && $specialAccount->vendor == $vendor->id ? 'selected' : '')-->
    


 


  
 


