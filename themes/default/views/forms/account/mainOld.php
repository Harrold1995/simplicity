<div class="modal fade account-modal form-charge" id="accountModal" tabindex="-1" role="dialog" main-id=<?= isset($account) && isset($account->id) ? $account->id : '-1' ?> type="account" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg " role="document">
    <div  class="modal-content text-primary popup-a form-entry shown">
                  <form action="<?php echo $target; ?>" method="post" type="account">

                    

        <header class="modal-header">
          <h2>Account</h2>
       
      
          <nav>
            <ul>
              <!-- <li><a href="./"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
              <li><a href="./"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
              <li><a href="./"><i class="icon-trash"></i> <span>Delete</span></a></li> -->
              <li><a href="#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
                <li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>        
              <li><?= isset($account) ? '<a href="accounts/deleteAccount/'.$account->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
              <li><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
              <li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
              <li><a href="./"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
              <li><a href="#" id="printId"><i class="icon-print"></i> <span>Print</span></a></li>
            </ul>
          </nav>
        </header>

                        <div class="modal-body">
                          <div class="container-fluid">
                            <div class="row mt-3">
                              <div class="col-6 p-5">
                                        <div >
                                              <h2 class="header-a text-center">General</h2>

                                              <ul class="list-a">
                                                <li>
                                                  <label for="name">Account Name:</label> <input type="text" value="<?= isset($account) && isset($account->name) ? $account->name : '' ?>" class="form-control " name="account[name]" id="name" placeholder="Enter Name" >
                                                </li>

                                                <li><label for="account_types_id">Account Type:</label> 

                                                  <div class=" field-select">
                                                    <select onchange="JS.loadList('api/getParentsList',$('[name=\'account[account_types_id]\']:last').val(), '#parent_id',  $(this).closest('.modal-body'));accountTypeForm($(this).closest('#account_types_id').val(), $(this).closest('.modal-body'),'#acountTypeInfo')"  class="form-control editable-select quick-add set-up" id="account_types_id" name="account[account_types_id]" modal="account" type="table" key="account.name" value = "<?=isset($account) ? $account->account_types_id: ''; ?>">
                                                    <option value="0"></option>
                                                        <?php
                                                        foreach ($account_types as $account_type) {
                                                            echo '<option value="' . $account_type->id . '" ' . (isset($account) && $account->account_types_id == $account_type->id ? 'selected' : '') . '>' . $account_type->name . '</option>';
                                                        } ?>
                                                    </select>
                                                  </div>

                                                </li>

                                                <li>
                                                  <label for="accno">GL #:</label> <input type="number" value="<?= isset($account) && isset($account->accno) ? $account->accno : '' ?>" class="form-control" name="account[accno]" id="accno" placeholder="" >
                                                </li>

                                                <li>
                                                  <label for="parent_id">Parent Account:</label> 
                                                  <div class=" field-select">
                                                    <select  class="form-control editable-select quick-add set-up" id="parent_id" name="account[parent_id]" modal="account" type="table" key="account.name"><!-- value="< ?=isset($account) ? $account->parent_id: ''; ?>"-->
                                                        <option value="0"></option>
                                                        <?php
                                                        foreach ($parents as $parent) {
                                                          
                                                            echo '<option value="' . $parent->id . '" ' . (isset($account) && $account->parent_id == $parent->id ? 'selected' : '') . '>' . $parent->name . '</option>';
                                                        }
                                                       
                                                         ?>
                                                   </select>
                                                 </div>
                                                </li>

                                                <li>
                                                  <label for="class">Default Class:</label> 
                                                  <div class=" field-select">
                                                    <select onchange="JS.loadList('api/getDefaultClassList',$('[name=\'class\']:last').val(), '#account_id',  $(this).closest('.modal-body'))" class="form-control editable-select quick-add set-up" id="class" name="account[class]" modal="account" type="table" key="account.name" value = "<?=isset($account) ? $account->class: ''; ?>">
                                                    <option value="0"></option>
                                                        <?php

                                                        foreach ($classes as $class) {
                                                            echo '<option value="' . $class->id . '" ' . (isset($account) && $account->class == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
                                                        } ?>
                                                    </select>
                                                  </div>
                                                </li>

                                                <li><label for="tax_line_id">Tax line Mapping:</label> <input value="<?= isset($account) && isset($account->tax_line_id) ? $account->tax_line_id : '' ?>" id="tax_line_id" name="account[tax_line_id]"></li>

                                                <li><label for="rpie_line_id">RPIE line Mapping:</label> <input value="<?= isset($account) && isset($account->rpie_line_id) ? $account->rpie_line_id : '' ?>"  id="rpie_line_id" name="account[rpie_line_id]"></li>
                                              </ul>

                                                <br>

                                                <div class="col-auto col-lg-12 custom-control custom-checkbox form-group">
                                                            <input type='hidden' value='0' name='account[active]'>
                                                            <input type="checkbox" <?= isset($account) && ($account->active==1) ? 'checked' : '' ?> value="1" class="custom-control-input" id="active" name="account[active]">
                                                            <label class="custom-control-label checkbox-right" for="active">Active?:</label>
                                                 </div>



                                                <h6>Account will by default apply to all properties unless specific propeties are chosen</h6>


                         
                                              </div>
                                          <div class="col-sm-4">

                                            <h4 id="chooseProperties"><a href="#" class="btn">Choose Properties</a></h4>
                                            <div id="choosePropertiesDiv"  style=" display:none; position: absolute; top: 40px; right: 0; z-index: 200; background-color: white; padding: 20px;     border-radius: 12px;   box-shadow: 0 5px 38px rgba(0,0,0,.16);  " >
                                                  <div id="choosePropertiesHeader">
                                                  
                                                  </div>
                                                  <div id="choosePropertiescontent" style =" height: 125px; overflow: auto; width:200px;">
                                                                <?php foreach ($properties as $property){ ?>
                                                          <div class="col-auto col-lg-12 custom-control custom-checkbox form-group mr-3">
                                                                        
                                                                        <input type="checkbox" <?= isset($properties) && ($property->property_id !==null ) ? 'checked' : '' ?> value="<?=$property->id?>" class="custom-control-input" id="<?=$property->id?>" name="propertyAccounts[<?=$property->id?>][property_id]">
                                                                        <label class="custom-control-label checkbox-right" for="<?=$property->id?>"><?=$property->name?></label>
                                                          </div>
                                                                    <?php }?>
                                                        
                                                      
                                                    
                                                  </div>
                                                  <div id="choosePropertiesFooter">
                                                    <span id="choosePropertiesOkBtn" class="btn btn-primary btn-small btn-block mt-2">ok</span>
                                                  </div>
                                                                        
                                            </div>

                                          </div>



                                        </div>
                                                                                    <div class="col-6  p-5 ">

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

              </ul>


              <div class="form-group row ">






              </div>

              </div>

            <!-- <div class="form-group row">

                                          <div class="col-sm-8 field-input">
                                            <textarea type="text"  class="form-control" name="account[description]" id="description" >
                                            < ?= isset($account) && isset($account->description) ? $account->description : '' ?>
                                            </textarea>
                                          </div>
            </div> -->

          </div>

              <ul>

                <li><span>Created by:</span> User 1</li>
                <li><span>Created on:</span> 12/18/2017</li>
              </ul>
                  
              

                                







          <div class="modal-footer">
                    <button type="submit" after="mnew" class="btn btn-primary ">
                      Save & New
                    </button>
                    <button type="submit" after="mclose" class="btn btn-primary ">
                      Save & Close
                    </button>
                    <button type="button" class="btn btn-primary ">
                      Duplicate
                    </button>
                    <button type="button" class="btn btn-secondary" >
                      Cancel
                    </button>
            </div>
      
    </form>

   </div>

  </div>
</div>

<script>
   function accountTypeForm(value, body, target) {
        if (value == "Credit Card") {
            $(body).find(target).load('themes/default/views/forms/account/creditCardTypeAccount.php')
        } else if (value == "Bank") {
            $(body).find(target).load('themes/default/views/forms/account/bankTypeAccount.php')
        } else if (value == "Mortgages") {
            $(body).find(target).load('themes/default/views/forms/account/Mortgage.php')
        } else {
            $(body).find(target).html('')
        }
    }

</script>

