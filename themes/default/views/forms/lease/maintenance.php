<section style="background-color: #efebeb; color: black; border: none; box-shadow: none;">
            <div class="double e m20">
                <div>
                    <ul class="check-a a">
                            <li><label for="child_under6" class="checkbox <?= isset($lease) && ($lease->child_under6 == 0) ? '' : 'active' ?>"><input type="hidden" name="lease[child_under6]" value="0" /><input type="checkbox" value="1" <?= isset($lease) && ($lease->child_under6 == 0) ? '' : 'checked' ?> id="child_under6"  name="lease[child_under6]"  class="hidden" aria-hidden="true"><div class="input"></div>child under 6?</label></li>
                            <li><label for="child_under11" class="checkbox <?= isset($lease) && ($lease->child_under11 == 0) ? '' : 'active' ?>"><input type="hidden" name="lease[child_under11]" value="0" /><input type="checkbox" value="1" <?= isset($lease) && ($lease->child_under11 == 0) ? '' : 'checked' ?> id="child_under11"  name="lease[child_under11]"  class="hidden" aria-hidden="true"><div class="input"></div>child under 11?</label></li>
                    </ul> 
				           <p>
                      <span>
                         <label for="child_under6">Last notice sent on </label>
                         <input   data-toggle="datepicker" value="<?= isset($lease) && isset($lease->last_notice_date) ? $lease->last_notice_date : '' ?>" name="lease[last_notice_date]" id="last_notice_date" class = "leaveEmpty">
                       </span>
                    </p>
                    <p>
                    <span>
                         <label for="annual_Inspection_date">Annual Inspection Date</label>
                         <input   data-toggle="datepicker" value="<?= isset($lease) && isset($lease->annual_Inspection_date) ? $lease->annual_Inspection_date : '' ?>" name="lease[annual_Inspection_date]" id="annual_Inspection_date" class = "leaveEmpty">
                       </span>
                    </p>
                    <p>
                     <span>
                         <label for="annual_Inspection_name">Annual Inspection Name</label>
                         <input    value="<?= isset($lease) && isset($lease->annual_Inspection_name) ? $lease->annual_Inspection_name : '' ?>" name="lease[annual_Inspection_name]" id="annual_Inspection_name" >
                       </span>
                    </p>


                </div>
            </div>
</section>


      