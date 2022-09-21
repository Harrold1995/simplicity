<h2 class="header-a text-center">Mortgage Info</h2>



  <ul class="list-a">

                  <li>
                  <label for="loan_num" >Loan #:</label>
                  <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->loan_num) ? $specialAccount->loan_num : '' ?>" name="specialAccount[loan_num]" id="loan_num" >
                  </li>

                  <li id="vendor_li">
                  <label for="vendor">Vendor:</label>                  
                      <select style="padding-right: 12px !important;"   class=" editable-select quick-add set-up" id="vendor" name="specialAccount[vendor]" modal="vendor" type="table" key="vendor.name">
                      <?php if(isset($vendors)){ ?>
                      <option value="0"></option>
                        <?php
                        foreach ($vendors as $vendor) {
                            echo '<option value="' . $vendor->id . '" ' . (isset($specialAccount) && $specialAccount->vendor == $vendor->id ? 'selected' : '') . '>' . $vendor->name . '</option>';
                        } ?>
                        <?php } ?>
                      </select>
                  
                  <!-- <input type="number" value="<?= isset($specialAccount) && isset($specialAccount->vendor) ? $specialAccount->vendor : '' ?>"  name="specialAccount[vendor]" id="vendor" > -->
                  </li>
                  <li id="default_interest_acct_li">
                  <label for="default_interest_acct">Interest Account</label>
                      <select style="padding-right: 12px !important;"   class=" editable-select quick-add set-up" id="default_interest_acct" name="specialAccount[default_interest_acct]" modal="account" type="table" key="account.name">
                      <?php if(isset($accounts)){ ?>
                      <option value="0"></option>
                        <?php
                        foreach ($accounts as $account) {
                            echo '<option value="' . $account->id . '" ' . (isset($specialAccount) && $specialAccount->default_interest_acct == $account->id ? 'selected' : '') . '>' . $account->name . '</option>';
                        } ?>
                      <?php } ?>
                      </select>
                      <!-- <input type="text" value="< ?= isset($specialAccount) && isset($specialAccount->default_interest_acct) ? $specialAccount->default_interest_acct : '' ?>"  name="specialAccount[default_interest_acct]" id="default_interest_acct" > -->
                  </li>

                  <li>
                  <label for="loan_date">Loan Date</label>
                  <input data-toggle="datepicker" value="<?= isset($specialAccount) && isset($specialAccount->loan_date) ? $specialAccount->loan_date : '' ?>"  name="specialAccount[loan_date]" id="loan_date">
                  </li>
                  <li>
                  <label for="maturity_date">Maturity Date</label>
                  <input data-toggle="datepicker" value="<?= isset($specialAccount) && isset($specialAccount->maturity_date) ? $specialAccount->maturity_date : '' ?>"  name="specialAccount[maturity_date]" id="maturity_date">
                  </li>

                  <li>
                  <label for="final_cutoff_date">Final Cutoff Date</label>
                  <input data-toggle="datepicker" value="<?= isset($specialAccount) && isset($specialAccount->final_cutoff_date) ? $specialAccount->final_cutoff_date : '' ?>" name="specialAccount[final_cutoff_date]" id="final_cutoff_date">
                  </li>
                  <li>
                  <label for="loan_amount">Loan Amount:<span class="prefix">$</span></label> 
                  <input type="decimal" value="<?= isset($specialAccount) && isset($specialAccount->loan_amount) ? number_format($specialAccount->loan_amount, 2) : '' ?>" name="specialAccount[loan_amount]" id="loan_amount" class="formatCurrency">
                  </li>
                  <li>
                  <label for="interest_rate">Interest Rate:</label> 
                  <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->interest_rate) ? $specialAccount->interest_rate : '' ?>"  name="specialAccount[interest_rate]" id="interest_rate" >
                  </li>
                  <li>
                  <label for="monthly_pmt">Monthly Payment:<span class="prefix">$</span></label> 
                  <input type="decimal" value="<?= isset($specialAccount) && isset($specialAccount->monthly_pmt) ? number_format($specialAccount->monthly_pmt, 2) : '' ?>"  name="specialAccount[monthly_pmt]" id="monthly_pmt" class="formatCurrency">
                  </li>
                  <li>
                  <label for="extension_options">Extension Options</label> 
                  <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->extension_options) ? $specialAccount->extension_options : '' ?>"  name="specialAccount[extension_options]" id="extension_options" >
                  </li>
                  <li>
                  <label for="Memo">Memo</label> 
                  <input type="text" value="<?= isset($specialAccount) && isset($specialAccount->memo) ? $specialAccount->memo : '' ?>"  name="specialAccount[memo]" id="memo" >
                  </li>
                  <style type="text/css" onload="mortgageDatepicker($(this).closest('ul'))"></style>
                  <style type="text/css" onload="<?= isset($specialAccount)? '' : 'getMortgageVendorsAndAccounts($(this).closest(\'ul\'))';?>"></style>
  </ul>



      

  <input type="hidden" name="table" value="mortgages "/>
  <input type="hidden" name="specialAccount[id]" value="<?= isset($specialAccount) && isset($specialAccount->id) ? $specialAccount->id : '' ?> "/>
  
<script>

function getMortgageVendorsAndAccounts(ul){
  console.log('!!!' + accounts)
      var newRow = `<label for="default_interest_acct" >Interest Account:</label>  
                      <span>
                        <select class="editable-select quick-add set-up inputInfo" id="default_interest_acct" name="specialAccount[default_interest_acct]">`;
            for (var i = 0; i < accounts.length; i++) {
		           newRow += `<option value="` + accounts[i].id +`"`;
               newRow += ` >` + accounts[i].name + `</option>`;
	      }
      newRow += `</select>
                  </span>`;
      $(ul).find("#default_interest_acct_li").empty();
      $(ul).find("#default_interest_acct_li").append(newRow);
      $(ul).find('#default_interest_acct_li').find('.editable-select').editableSelect();

      var newRow = `<label for="vendor" >vendor:</label>  
                      <span>
                        <select class="editable-select quick-add set-up" id="vendor" name="specialAccount[vendor]">`;
            for (var i = 0; i < vendors.length; i++) {
		           newRow += `<option value="` + vendors[i].id +`"`;
               newRow += ` >` + vendors[i].name + `</option>`;
	      }
      newRow += `</select>
                  </span>`;
      $(ul).find("#vendor_li").empty();
      $(ul).find("#vendor_li").append(newRow);
      $(ul).find('#vendor_li').find('.editable-select').editableSelect();
}



      function mortgageDatepicker(ul){
          JS.datePickerInit(ul);
      }
  </script>

    


 


  
 


