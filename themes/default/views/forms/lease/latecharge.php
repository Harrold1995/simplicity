<div class="modal fade unit-modal form-charge" data-type="unit" tabindex="-1" role="dialog" aria-hidden="true">
   
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="root">
        <div class="modal-content text-primary popup-a shown" style="padding: 25px">
            <form action="<?php echo $target; ?>" method="post">
               <header class="modal-h ui-draggable-handle">
                    <h2 class="modal-title" id="exampleModalLongTitle"><?php echo $title; ?></h2>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </header>
                <div class="modal-body">

                         <p class="w290" style="z-index: 58;">
                            <label for="name">Setup Name</label>
                            
                                <input type="text" value="<?= isset($latecharge) ? $latecharge->name : '' ?>" class="size-c" name="latecharge[name]" id="name" placeholder="Name">
                         </p>
                        
                        <p class="w290" style="z-index: 58;">
                            <label for="description" >Description</label>
                            <input type="text" value="<?= isset($latecharge) ? $latecharge->description : '' ?>" class="size-c" name="latecharge[description]" id="description" placeholder="Description">    
                        </p>
                       
                            <?php $ind = 0;
                            if (isset($latecharge)){
                           
                            foreach ($latecharge->rules as $rule) { ?>
                            <section>
                                <h3 class="title">Rule #<?=($ind + 1)?></h3>
                                <p>
                                    <label >On the</label>
                                    <input type="text" size="3" value="<?=$rule->day ?>" name="rules[<?=$rule->id ?>][day]">
                                    <label >of the month, charge</label>
                                    <input type="text" size="3" value="<?=$rule->amount ?>"  name="rules[<?=$rule->id ?>][amount]">
                                    <span class = "select">
                                        <select  name="rules[<?=$rule->id ?>][type]">
                                            <?php foreach ($amount_types as $id => $at)
                                                echo'<option value="'.$id.'" '.($rule->type == $id ? 'selected' : '').'>'.$at.'</option>';
                                            ?>
                                        </select>
                                    </span>

                                    <span>On</span>
                                    <span class="check-a">
                                <label for=<?php echo 'rule'. $ind.'-all'; ?> class="checkbox <?= $rule->all_types == 0 ? '' : 'active' ?>">All Charge Types
                                    <input type="hidden" name=<?php echo 'rules[' . $rule->id . '][all_types]"';?> value="0" />
                                    <input type="checkbox" value="1"  <?= $rule->all_types == 0 ? '' : 'checked' ?> id=<?php echo 'rule'. $ind.'-all'; ?>  name=<?php echo 'rules[' . $rule->id . '][all_types]"';?> >
                                    <span class="input"></span>
                                </label>
                    
                              </span>
                                    <span>or</span>
                                    <a class="btn cpopup-trigger" data-target="#rulepopup<?=$rule->id ?>" href="#">Choose Charge Types</a> 
                                </p>
                               


                                <div class="cpopup c-top c-right" id="rulepopup<?=$rule->id ?>">
                                    <p class="title">Charge types:</p>
                                    <div style = 'max-height:350px; overflow:auto'>
                                    <?php
                                    foreach ($ctypes as $ctype) {
                                        echo '<div class="custom-control custom-checkbox form-group mb-0">
                                        <input type="checkbox" ' . (in_array($ctype->id, $rule->ctypes) ? 'checked' : '') . ' value="1" class="custom-control-input" name="rules[' . $rule->id . '][ctypes][' . $ctype->id . ']" id="rules' . $ind . 'ctypes' . $ctype->id . '">
                                        <label class="custom-control-label checkbox-left text-left" for="rules' . $ind . 'ctypes' . $ctype->id . '"><span style="margin-left: 25px;">' . $ctype->name . '</span></label>
                                    </div>';
                                    } ?>
                                    </div>



                                    <a href="#" class="btn cpopup-trigger" data-target="#rulepopup<?=$ind ?>">done</a>

                                </div>
                                <br/><br/>
                                </section>
                            <?php $ind++;
                            }   
                            }
                            if ($ind == 0) {
                                echo '<section>
                                <h3>Rule #' . ($ind + 1) . '</h3>
                                <p>
                                    <label>On the</label>
                                    <input type="text" size="3" value=""  name="rules[' . $ind . '][day]">
                                    <label>th of the month, charge</label>
                                    <input type="text" size="3" value="" name="rules[' . $ind . '][amount]">
                                    <span class="select">
                                        <select  name="rules[' . $ind . '][type]">
                                                        ';
                                                foreach ($amount_types as $id => $at)
                                                        echo'<option value="'.$id.'" '.($rule->type == $id ? 'selected' : '').'>'.$at.'</option>';
                
                                                echo'   
                                        </select>
                                    </span>
                                    <label>On</label>
                                    <span class="check-a">
                                            <label for="rule' . $ind . '-all" class="checkbox">All Charge Types
                                                <input type="checkbox" value="0" name="rules[' . $ind . '][all_types]"  id="rule' . $ind . '-all" name="rules[' . $ind . '][all_types]" onchange="$(this).parent().next().prop(\'disabled\', function(i, v) { return !v; });">
                                                <span class="input"></span>
                                            </label>
                                    
                                    </span>
                                    <label>or</label>
                                    <a class="btn cpopup-trigger" data-target="#rulepopup' . $ind . '" href="#">Choose Charge Types</a>
                                </p>
                                <div class="cpopup c-top c-right" id="rulepopup' . $ind . '">
                                    <h4>Charge types:</h4> <ul class="check-a">';

                                    foreach ($ctypes as $ctype) {
                                        echo '<div class="custom-control custom-checkbox form-group mb-0">
                                        <input type="checkbox" value="1" class="custom-control-input" name="rules[' . $ind . '][ctypes][' . $ctype->id . ']" id="rules' . $ind . 'ctypes' . $ctype->id . '">
                                        <label class="custom-control-label checkbox-left text-left" for="rules' . $ind . 'ctypes' . $ctype->id . '"><span style="margin-left: 25px;">' . $ctype->name . '</span></label>
                                    </div>';
                                    }
                        echo '    </ul> 
                                  <p>  <a href="#" class="btn cpopup-trigger" data-target="#rulepopup' . $ind . '">done</a> </p>
                                </div>
                                <br/><br/>
                            </section>';
                            $ind++;
                        
                        
                        
                       }

                                
                                ?>
                        


                    </div>
                    <p class="row justify-content-center">
                            <a class="addLateChargeRule" href="#" onclick="JS.appendHtml('htmlapi/getLateChargeRow',$(this).closest('div').find('.modal-body'))"><i class="fas fa-plus-square"></i></a>
                        </p>

                <footer class ="root">
                  <ul class="list-btn">
                    <li><button type="submit" after="mclose">Save</button></li>
                    <li><button type="button"  data-dismiss="modal">Cancel</button></li>
                 </ul>
                </footer>
            </form>
        </div>
    </div>
                        </div>
</div>
