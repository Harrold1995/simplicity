
<div class="modal fade account-modal <?php echo $edit ?>" id="accountModal" tabindex="-1" role="dialog" main-id=<?= isset($account) && isset($account->id) ? $account->id : '-1' ?> type="account" doc-type="accounts" ref-id="" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
    <div id="root" class="no-print">
      <div class="modal-content  text-primary  popup-a form-entry shown" style="padding: 25px;max-height:620px;">
         <form action="<?php echo $target; ?>" method="post"type="account" specialType="<?php if(isset($table) && isset($table->name))if($table->name == "Credit Card" or $table->name == "Bank" or $table->name == "Mortgages" )echo $table->name ?>">
            <header class="modal-h">
              <h2 class="text-uppercase">Accounts</h2>
                <nav class = "window-options">
                  <ul>
                      <li><span class="buttons" style=""><span class="min" >_</span></span> </li>
                      <li><span class="buttons" style=""><span class="max" >[ ]</span></span></li>
                      <li><span class="buttons" style=""><span class="close2">X</span></span> </li>
                  </ul>
              </nav>
              <nav>
                <ul>       
                  <li><?= isset($account) ? '<a href="delete/deleteAccount/'.$account->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
                  <li class="getDocuments"><a href="#" class="uploadDocument"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
                  <li><a href="#" id="printVoidCheck" data-account-id = "<?= isset($account) && isset($account->id) ? $account->id : '-1' ?>"><i class="icon-print"></i> <span>Print</span></a></li>
                </ul>
              </nav>
            </header>
            <section class="plain modal-body" style="border-style: none; box-shadow:none;">
                <div class="<?php if(isset($table) && isset($table->name))if($table->name == "Credit Card" or $table->name == "Bank" or $table->name == "Mortgages" ) echo 'double a'; ?> 
                d80 m20" id="class_modal_a">

                        <div>
                          <h2 class="header-a text-center">General</h2>
                          <?php if($locked){echo '<h3 style="text-align: center; color: red;">No modification allowed</h3>';}  ?>
                          <ul id="setupprint" class="list-input m30 plain">

                            <li><label for="name">Account Name:</label> <input type="text" value="<?= isset($account) && isset($account->name) ? $account->name : '' ?>" name="account[name]" id="name" class="inputStyle" placeholder="Enter Name" <?php if($locked) echo 'style="background-color: #e9ecef;" readonly="readonly"'?>>
                            </li>

                            <li>
                              <label for="account_types_id">Account Type:</label> 
                              <span class=" select">
                                <select style="padding-right: 12px !important;" onchange="JS.loadList('api/getParentsList',$('[name=\'account[account_types_id]\']:last').val(), '#parent_id',  $(this).closest('.modal-body'));accountTypeForm($(this).closest('#account_types_id').val(), $(this).closest('.modal-body'),'#acountTypeInfo')"  class="form-control editable-select quick-add set-up inputStyle" id="account_types_id" name="account[account_types_id]" modal="account" type="table" key="account.name" value = "<?=isset($account) ? $account->account_types_id: ''; ?>" <?php if($locked) echo 'readonly="readonly"'?>>
                                  <option value="0"></option>
                                  <?php
                                  foreach ($account_types as $account_type) {
                                      echo '<option value="' . $account_type->id . '" ' . (isset($account) && $account->account_types_id == $account_type->id ? 'selected' : '') . '>' . $account_type->name . '</option>';
                                  } ?>
                                </select>
                                </span>
                            </li>

                            <li><label for="accno">GL #:</label> <input type="number" value="<?= isset($account) && isset($account->accno) ? $account->accno : $maxId ?>" name="account[accno]" id="accno" class="inputStyle" placeholder="">
                            </li>

                            <li>
                              <label for="parent_id">Parent Account:</label> 
                              <span class=" select">
                                <select style="padding-right: 12px !important;" class="form-control editable-select quick-add set-up inputStyle" id="parent_id" name="account[parent_id]" modal="account" type="table" key="account.name" <?php if($locked) echo 'readonly="readonly"'?>><!-- value="< ?=isset($account) ? $account->parent_id: ''; ?>"-->
                                    <option value="0"></option>
                                    <?php
                                    foreach ($parents as $parent) {                                     
                                        echo '<option value="' . $parent->id . '" ' . (isset($account) && $account->parent_id == $parent->id ? 'selected' : '') . '>' . $parent->name . '</option>';
                                    }                                  
                                     ?>
                                     <!-- < ?php
                                        echo '<option class="nested0" value="0"></option>'; //' . (isset($unit) && $unit->parent_id == 0 ? 'selected' : '') . '
                                        if (isset($subaccounts))
                                          foreach ($subaccounts as $saccount) {
                                            //if ($unit->id == $sunit->id || $unit->name == $sunit->name) continue;
                                            echo '<option data-id="'.$saccount->id.'" data-parent-id="'.$saccount->parent_id.'" class="nested'.$saccount->step.'"value="' . $saccount->id . '" ' . (isset($account) && $account->parent_id == $saccount->id ? 'selected' : '') . '>' . $saccount->accno . "-".$saccount->name  .  '</option>';
                                        } ?> -->
                               </select>
                              </span>
                            </li>

                            <li>
                                <label for="class">Default Class:</label> 
                                <span class="select">
                                  <select style="padding-right: 12px !important;" onchange="JS.loadList('api/getDefaultClassList',$('[name=\'class\']:last').val(), '#account_id',  $(this).closest('.modal-body'))" class="form-control editable-select quick-add set-up inputStyle" id="class" name="account[class]" modal="account" type="table" key="account.name" value = "<?=isset($account) ? $account->class: ''; ?>">
                                    <option value="0"></option>
                                    <?php

                                    foreach ($classes as $class) {
                                        echo '<option value="' . $class->id . '" ' . (isset($account) && $account->class == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
                                    } ?>
                                  </select>
                                  </span>
                            </li>

                            <li>
                              <label for="tax_line_id">Tax line Mapping:</label>
                              <input value="<?= isset($account) && isset($account->tax_line_id) ? $account->tax_line_id : '' ?>" id="tax_line_id" name="account[tax_line_id]" class="inputStyle">
                            </li>

                            <li>
                              <label for="rpie_line_id">RPIE line Mapping:</label> 
                              <input value="<?= isset($account) && isset($account->rpie_line_id) ? $account->rpie_line_id : '' ?>"  id="rpie_line_id" name="account[rpie_line_id]" class="inputStyle">
                            </li>

                            <li>
                              <span class="label">Properties:</span>
                              <span class="check"><label for="properties" class="checkbox <?= isset($account) && ($account->all_props == 1) ? 'active' : '' ?><?php if($target == "accounts/addAccount")echo 'active'; ?>">
                               <input type="checkbox" value="1" <?= isset($account) && ($account->all_props == 1) ? 'checked' : '' ?><?php if($target == "accounts/addAccount")echo 'checked'; ?> id="properties" name="account[all_props]"  class="hidden" aria-hidden="true"><div class="input"></div>
                              apply to all</label>
                              </span>
                                or 
                                <a href="#" id="chooseProperties" class="btn">Choose Properties</a> 
                                 <div id="choosePropertiesDiv"  class="popup-checkbox-div" style=" display:none;  " >

                                    <h4>Choose Properties</h4>
                                    <ul class="check-a">

                                      <?php foreach ($properties as $property){ ?>
                                        <li>
                                          <label for="<?=$property->id?>" class="checkbox <?= isset($properties) && ($property->property_id !==null ) ? 'active' : '' ?> "><input type="checkbox" <?= isset($properties) && ($property->property_id !==null ) ? 'checked' : '' ?> value="<?=$property->id?>" class="hidden" aria-hidden="true" id="<?=$property->id?>" name="propertyAccounts[<?=$property->id?>][property_id]"><div class="input"></div> <?=$property->name?></label>
                                      <?php }?>
                                    </li>
                                    </ul>                 

                                    <div id="choosePropertiesFooter">
                                      <a href="#" id="choosePropertiesOkBtn" class="btn">done</a> 
                                       
                                    </div>                                                                                   
                                 </div>
                            </li>

                            <li>
                              <span class="label">Active:</span>
                              <label for="active" class="checkbox <?= isset($account) && ($account->active == 1) ? 'active' : '' ?><?php if($target == "accounts/addAccount")echo 'active'; ?>">
                                <input type="checkbox"  value="1" <?= isset($account) && ($account->active == 1) ? 'checked' : '' ?><?php if($target == "accounts/addAccount")echo 'checked'; ?> id="active" name="account[active]"  class="hidden" aria-hidden="true">
                                <div class="input"></div>
                              </label>
                            </li>

                                                           
                                                            
                           </ul>
                           
                        </div>


                  


                                    
                                            
                                    <!-- <div>
                                        <h2 class="header-a text-center">Credit Card</h2>
                                        <ul class="list-a">
                                            <li><label for="lai">CC#:</label> <input type="text" id="lai" name="lai" value="123456789098"></li>
                                            <li><label for="laj">type:</label> <input type="text" id="laj" name="laj" value="credit card"></li>
                                            <li><label for="lak">Exp #:</label> <input type="text" id="lak" name="lak" value="6/19"></li>
                                            <li><label for="lal">Security:</label> <input type="text" id="lal" name="lal" value="123"></li>
                                            <li><label for="lam">Billing Address:</label> <input type="text" id="lam" name="lam" value="CitiCard"></li>
                                            <li><label for="lan">Card holder:</label> <input type="text" id="lan" name="lan" value="Dovid gray"></li>
                                            <li><label for="lao">Username:</label> <input type="text" id="lao" name="lao" value="yes"></li>
                                            <li><label for="lap">Password:</label> <input type="text" id="lap" name="lap" value="1234"></li>
                                        </ul>
                                    </div> -->
                                            <div id="acountTypeInfo">
                                                         <?php
                                                          if( $account->account_types_id ){

                                                              switch($account->account_types_id){
                                                                  case "6":
                                                                  require_once('themes/default/views/forms/account/creditCardTypeAccount.php');
                                                                  break;
                                                                  case "1":
                                                                  require_once('themes/default/views/forms/account/bankTypeAccount.php');
                                                                  break;
                                                                  case "9":
                                                                  require_once('themes/default/views/forms/account/Mortgage.php');
                                                                  break;
                                                                }
                                                              }
                                                        ?>                                                   
                                                </div> 

              </div>
              <p class="m35">
                <label for="description">Description:</label>
                <input type="text" value="<?= isset($account) && isset($account->description) ? $account->description : '' ?>" name="account[description]" id="description" placeholder="Enter Description">
              </p>           
            </section>
                    
                  
                
            <footer>
              <ul class="list-btn">
                <li><button type="submit" after="mnew">Save &amp; New</button></li>
                <li><button type="submit" after="mclose">Save &amp; Close</button></li>
                <li><button type="submit" after="duplicate">Duplicate</button></li>
                <li><button type="button">Cancel</button></li>
                
              </ul>
              <ul>
                <li>Last Modified 12:22:31 pm 1/10/2018</li>
                <li>Last Modified by <a href="#!">User</a></li>
              </ul>
            </footer>

          </form>

      </div>
    </div>
  </div>
</div>





<?php if(isset($table) && isset($table->name))if($table->name == "Credit Card" or $table->name == "Bank" or $table->name == "Mortgages" ){$tableType = "special";} ?>
<style type="text/css" onload="<?= isset($TableType)? 'specialForm()' : '';?>"></style>

<script>
var vendors = <?php echo $jvendors; ?>;
var allProperties = <?php echo $jallProperties; ?>;
var profiles = <?php echo $jallProfiles; ?>;
var accounts = <?php echo $jaccounts; ?>;
var vendors = <?php echo $jvendors; ?>;
console.log(vendors);
   function accountTypeForm(value, body, target) {
        if (value == "Credit Card") {
          var vendors2 = 5;
            specialForm();
            $(body).find(target).load('themes/default/views/forms/account/creditCardTypeAccount.php')
        } else if (value == "Bank") {
            specialForm();
            $(body).find(target).load('themes/default/views/forms/account/bankTypeAccount.php')
        } else if (value == "Mortgages") {
            specialForm();
            $(body).find(target).load('themes/default/views/forms/account/Mortgage.php')
        } else {
            $( "#class_modal_a" ).removeClass( "double a" );
            //$(".form-entry section input").css("cssText", "width: 600px !important;");
            $(body).find(target).html('');

        }
    }
     function setWidth(){
        $( "#class_modal_a" ).removeClass( "double a" );
        //$(".form-entry section input").css("cssText", "width: 600px !important;");
        //$(".inputStyle").css("cssText", "width: 600px !important;");
        //$(".form-entry section input").css("width","");
     }

    function specialForm(){
        $( "#class_modal_a" ).addClass( "double a" );
        //$(".inputStyle").css("cssText", "width: 200px !important;");
        //$(".form-entry section input").css("cssText", "width: 200px !important;");
        //$(".form-entry section input").css("width","auto");
    }

    function regularForm(){
        //$(".form-entry section input").css("width","");
    }

 
</script>