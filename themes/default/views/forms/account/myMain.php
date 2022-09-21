<div class="modal fade account-modal" id="accountModal" tabindex="-1" role="dialog" main-id=<?= isset($account) && isset($account->id) ? $account->id : '-1' ?> type="account" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg " role="document">
            <div class="modal-content text-primary">
                  <form action="<?php echo $target; ?>" method="post">

                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLongTitle"><?php echo $title; ?></h5>

                          <button type="button" class="close" >
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>



                        <div class="modal-body">
                          <div class="container-fluid">
                            <div class="row mt-3">
                              <div class="col-6 p-5">

                                <div class="form-group row">
                                  <label for="account[name]" class="col-sm-4 text-right">Account Name:</label>
                                  <div class="col-sm-8 field-input">
                                    <input type="text" value="<?= isset($account) && isset($account->name) ? $account->name : '' ?>" class="form-control" name="account[name]" id="name" placeholder="Enter Name">
                                  </div>
                                </div>
                                <div class="form-group row">
                                  <label for="type" class="col-sm-4 text-right">Account Type:</label>
                                  <div class="col-sm-8 field-select">
                                    <select class="form-control account_types_id" id="account_types_id"  name="account[account_types_id]" onchange="JS.accountTypeForm($(this).closest('#account_types_id').val(), $(this).closest('.modal-body'),'#acountTypeInfo')">
                                        <?php
                                        echo '<option value="-1" selected >' . "Select Account Type" . '</option>';
                                        foreach ($account_types as $account_type) {
                                            echo '<option value="' . $account_type->id . '" ' . (isset($account) && $account->account_types_id == $account_type->id ? 'selected' : '') . '>' . $account_type->name . '</option>';
                                        }
                                       
                                         ?>
                                    </select>
                                  </div>
                                </div>


                              <div class="form-group row">
                                  <label for="accno" class="col-sm-4 text-right">GL# (Accno):</label>
                                  <div class="col-sm-8 field-input">
                                    <input type="number" value="<?= isset($account) && isset($account->accno) ? $account->accno : '' ?>" class="form-control" name="account[accno]" id="accno" placeholder="">
                                  </div>
                                </div>
                                <div class="form-group row">
                                  <label for="type" class="col-sm-4 text-right">Parent Account:</label>
                                  <div class="col-sm-8 field-select">
                                    <select onchange="JS.loadList('api/getAccountList', $('[name=\'parent\']:last').val(), '#account_id',  $(this).closest('.modal-body'))" class="form-control editable-select quick-add set-up" id="parent" name="account[parent_id]" modal="account" type="table" key="account.name">
                                        <?php
                                        foreach ($parents as $parent) {
                                            echo '<option value="' . $parent->id . '" ' . (isset($account) && $account->parent_id == $parent->id ? 'selected' : '') . '>' . $parent->name . '</option>';
                                        } ?>
                                    </select>
                                  </div>
                                </div>
                      
                                
                                <div class="form-group row">
                                          <label for="type" class="col-sm-4 text-right">Default Class:</label>
                                          <div class="col-sm-8 field-select">
                                            <select onchange="JS.loadList('api/getDefaultClassList',$('[name=\'class\']:last').val(), '#account_id',  $(this).closest('.modal-body'))" class="form-control editable-select quick-add set-up" id="class" name="account[class]" modal="account" type="table" key="account.name">
                                                <?php

                                                foreach ($classes as $class) {
                                                    echo '<option value="' . $class->id . '" ' . (isset($account) && $account->class == $class->id ? 'selected' : '') . '>' . $class->description . '</option>';
                                                } ?>
                                            </select>
                                          </div>
                                        </div>

                                        <div class="form-group row">
                                          <label for="type" class="col-sm-4 text-right">Tax line Mapping:</label>
                                          <div class="col-sm-8 field-select">
                                            <select class="form-control" id="tax_line_id" name="account[tax_line_id]">
                                              <option value="1">1 - Standard</option>
                                                    <option value = "2">2 - Multi Family Complex</option>
                                              <option value="3">3 - Tax Exempt</option>
                                            </select>
                                          </div>
                                        </div>

                                        <div class="form-group row">
                                          <label for="type" class="col-sm-4 text-right">RPIE line Mapping:</label>
                                          <div class="col-sm-8 field-select">
                                            <select class="form-control" id="rpie_line_id" name="account[rpie_line_id]">
                                              <option value="1">1 - Standard</option>
                                              <option value="2">2 - Multi Family Complex</option>
                                              <option value="3">3 - Tax Exempt</option>
                                            </select>
                                          </div>

                                        </div>

                                          <div class="col-auto col-lg-12 custom-control custom-checkbox form-group">
                                                            <input type='hidden' value='0' name='account[active]'>
                                                            <input type="checkbox" <?= isset($account) && ($account->active==1) ? 'checked' : '' ?> value="1" class="custom-control-input" id="active" name="account[active]">
                                                            <label class="custom-control-label checkbox-right" for="active">Active?:</label>
                                          </div>

                                        <div class="form-group row ">
                                          <div class="col-sm-8">

                                            <h6>Accouct will by default apply to all properties unless spicific propeties are choosen</h6>
                                            
                                            <!-- <div class="form-check form-check-inline ">

                                              <label class="form-check-label " for="prperties">
                                                <h4><span class="badge badge-pill badge-primary mt-2">Applies for all properties</span></h4>
                                              </label>
                                              <input class="form-check-input   ml-2" type="checkbox" id="activeCheckbox" checked>

                                            </div> -->

                                          </div>
                                        
                                          <div class="col-sm-4">
                                            <h4 id="chooseProperties"><a href="#"><span class="badge badge-pill badge-primary mt-2">Choose Properties</span></a></h4>
                                            <div id="choosePropertiesDiv"  
 style=" display:none; position: absolute; top: 40px; right: 0; z-index: 200; background-color: white; padding: 20px;     border-radius: 12px;   box-shadow: 0 5px 38px rgba(0,0,0,.16);  " >
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

                                        <div class="form-group row">
                                          <label for="decription" class="col-sm-4">Account Description:</label>
                                          <div class="col-sm-8 field-input">
                                            <textarea type="text"  class="form-control" name="account[description]" id="description" >
                                            <?= isset($account) && isset($account->description) ? $account->description : '' ?>
                                            </textarea>
                                          </div>
                                        </div>
                                      </div>

                                                  <!-- Account Type  --> 
                                                    <!-- Account Type Info comes Here with Jquery function JS.loadList on the Account Type Select  --> 
                                                  <div class="col-6  p-5 ">
                                                        <div id="acountTypeInfo">

                                                         <?php
                                                          if( $account->account_types_id ){

                                                              switch($account->account_types_id){
                                                                  case "1":
                                                                  require_once('themes/default/views/forms/account/creditCardTypeAccount.php');
                                                                  break;
                                                                  case "2":
                                                                  require_once('themes/default/views/forms/account/bankTypeAccount.php');
                                                                  break;
                                                                }
                                                              }
                       
                                                        ?>
                                                        
                                                        </div>                            
                                                  </div>
                                              


                                        </div>
                                      </div>
                                    </div>



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
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                      Cancel
                                                    </button>
                                        </div>
                                      
                    </form>
                              
                    </div>

</div>
</div>


 <script>
  //  if(isset($account) && $account->account_types->id == $account_type->id){
  //                                         JS.accountTypeForm($(this).closest('#account_types_id').val(), $(this).closest('.modal-body'),'#acountTypeInfo');
   //   }
  //JS.accountTypeForm($(this).closest('#account_types_id').val(), $(this).closest('.modal-body'),'#acountTypeInfo');
</script>