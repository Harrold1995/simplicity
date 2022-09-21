<section style="background-color: #efebeb; color: black; border: none; box-shadow: none;">
            <div class="double e m20">
                <div>
				           <p>
                      <span>
                         <label for="Turnover_Inspection_date">Turnover Inspection date</label>
                         <input   data-toggle="datepicker" value="<?= isset($unit) && isset($unit->Turnover_Inspection_date) ? $unit->Turnover_Inspection_date : '' ?>" name="<?php echo str_replace("field","Turnover_Inspection_date", $fieldFormat);?>" id="Turnover_Inspection_date" class = "leaveEmpty">
                       </span>
                    </p>
                    <p>
                     <span>
                         <label for="annual_Inspection_name">Turnover Inspection Name</label>
                         <input    value="<?= isset($unit) && isset($unit->Turnover_Inspection_name) ? $unit->Turnover_Inspection_name : '' ?>" name="<?php echo str_replace("field","Turnover_Inspection_name", $fieldFormat);?>" id="Turnover_Inspection_name" >
                       </span>
                    </p>
                     <p>
                        <span>
                         <label for="turnover_note">Turnover note</label>
                         <input    value="<?= isset($unit) && isset($unit->turnover_note) ? $unit->turnover_note : '' ?>" name="<?php echo str_replace("field","turnover_note", $fieldFormat);?>" id="turnover_note" >
                       </span>
                    </p>
                    
                    <p>
                      <span>
                         <label for="lead_test_date">lead_test_date</label>
                         <input   data-toggle="datepicker" value="<?= isset($unit) && isset($unit->lead_test_date) ? $unit->lead_test_date : '' ?>" name="<?php echo str_replace("field","lead_test_date", $fieldFormat);?>" id="lead_test_date" class = "leaveEmpty">
                       </span>
                    </p>
                    <ul class="check-a a">
                    <li><label for="Lead_Exemption" class="checkbox <?= isset($unit) && ($unit->Lead_Exemption == 0) ? '' : 'active' ?>"><input type="hidden" name="<?php echo str_replace("field","Lead_Exemption", $fieldFormat);?>" value="0" /><input type="checkbox" value="1" <?= isset($unit) && ($unit->Lead_Exemption == 0) ? '' : 'checked' ?> id="Lead_Exemption"  name="<?php echo str_replace("field","Lead_Exemption", $fieldFormat);?>"  class="hidden" aria-hidden="true"><div class="input"></div>Lead_Exemption?</label></li>
                    <li><label for="Lead_Paint_Testing" class="checkbox <?= isset($unit) && ($unit->Lead_Paint_Testing == 0) ? '' : 'active' ?>"><input type="hidden" name="<?php echo str_replace("field","Lead_Paint_Testing", $fieldFormat);?>" value="0" /><input type="checkbox" value="1" <?= isset($unit) && ($unit->Lead_Paint_Testing == 0) ? '' : 'checked' ?> id="Lead_Paint_Testing"  name="<?php echo str_replace("field","Lead_Paint_Testing", $fieldFormat);?>"  class="hidden" aria-hidden="true"><div class="input"></div>Lead_Paint_Testing?</label></li>
						</ul>
                </div>
            </div>
</section>

