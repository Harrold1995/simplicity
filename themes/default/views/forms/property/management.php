
<table class="table table-c b dt" id="#managementtable">

<thead>
  <tr>
    <th >Frequency</th>
    <th >Vendor</th>
    <th >Unit</th>
    <th >Amount</th>
    <th >Start Date</th>
    <th >End Date</th>
    <th >Charge Type</th>
    <th >Percentage/fixed</th>
    <th >accounts</th>
    <th ></th>
  </tr>
</thead>
<tbody>
<tr role="row">
      <td>
      <span class="">
              <label for="frequency"></label>
          <select id="frequency" class="editable-select" name="temp[frequency]" style="width: 100px; margin-left: 5px;">
          <?php
                foreach ($frequencies as $frequency) {
                    echo '<option value="' . $frequency->id .'+' . $frequency->name .'">' . $frequency->name .'</option>';
                } ?>
          </select>
          </span>
        <input type="hidden" name="temp[ind]" value="<?php echo count($managements);?>">
      </td>
      <td>
        <span class="">
          <label for="vendor"></label>
          <select id="vendor" class="editable-select" name="temp[vendor]"style="width: 100px; margin-left: 5px;">
          <?php
                foreach ($vendors as $vendor) {
                    echo '<option value="' . $vendor->id .'+' . $vendor->vendor .'">' . $vendor->vendor .'</option>';
                } ?>
          </select>
          </span>
      </td>
      <td>
        <span class="">
          <label for="unit_id"></label>
          <select id="unit_id" class="editable-select" name="temp[unit_id]"style="width: 100px; margin-left: 5px;">
          <?php
                foreach ($units as $unit) {
                    echo '<option value="' . $unit->id .'+' . $unit->name .'">' . $unit->name .'</option>';
                } ?>
          </select>
          </span>
      </td>
      <td>
          <span class="input-amount">
              <label for="amount"></label>
              <input type="text" id="amount" name="temp[amount]">
          </span>
      </td>
      <td>
          <span>
              <label for="start_date"></label>
              <input  data-toggle="datepicker" id="start_date" name="temp[start_date]">
          </span>
      </td>
      <td>
          <span>
              <label for="end_date"></label>
              <input data-toggle="datepicker" id="end_date" name="temp[end_date]">
          </span>
      </td>
      <td>
      <span>
      <span class="">
          <label for="item_id"></label>
            <select id="item_id" class="editable-select" name="temp[item_id]"style="width: 100px; margin-left: 5px;">
            <?php
                foreach ($items as $item) {
                  echo '<option value="' . $item->id .'+' . $item->item_name .'">' . $item->item_name .'</option>';
              } ?>
            </select>
          </span>
      </td>
      <td>
            <ul class="check-a a">
                <li><label for="percentage_fixed" class="checkbox "><input type="checkbox" value="1"  id="percentage_fixed" name="temp[percentage_fixed]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
            </ul>
        </td>
        <td style ="overflow: visible;">
      <!-- <span class="">
          <label for="account_id"></label>
            <select id="account_id" class="editable-select" name="temp[account_id]"style="width: 100px; margin-left: 5px;">
            < ?php
									echo '<option class="nested0" value="0"></option>'; 
									if (isset($subexpenseAccounts))
										foreach ($subexpenseAccounts as $subexpenseAccount) {
											echo '<option data-id="'.$subexpenseAccount->id.'" data-parent-id="'.$subexpenseAccount->parent_id.'" class="nested'.$subexpenseAccount->step.'"value="' . $subexpenseAccount->id . '+' . $subexpenseAccount->name .'"  >' . $subexpenseAccount->name . '</option>';
									} ?>
            </select>
          </span> -->

          <span class="label"></span>
                              <span class="check-a a"><label for="properties" class="checkbox ">
                                <input type="hidden" name="temp[all_props]" value="0" /><input type="checkbox" value="1"  id="properties" name="temp[all_props]"  class="hidden" aria-hidden="true">
                                 </label>
                              </span>
                                or 
                                
                                <button id="chooseAccounts" class="btn-xm" style="min-width:90px !important;">Choose Accounts </button>
                                 <div id="chooseAccountsDiv"  class="popup-checkbox-div-management" style=" display:block;  " >

                                    <h4>Choose Accounts</h4>
                                    <ul class="check-a">

                                      <?php foreach ($subexpenseAccounts as $subexpenseAccount){ ?>
                                        <li>
                                          <label for="<?=$subexpenseAccount->id?>" class="checkbox"><input type="checkbox" value="<?=$subexpenseAccount->id?>" class="hidden" aria-hidden="true" id="<?=$subexpenseAccount->id?>" name="temp[account_id][<?=$subexpenseAccount->id?>]"><div class="input"></div> <?=$subexpenseAccount->name?></label>
                                      <?php }?>
                                    </li>
                                    </ul>                 

                                    <div id="chooseAccountsFooter">
                                      <a href="#" id="choosePropertiesOkBtn" class="btn">done</a> 
                                       
                                    </div>                                                                                   
                                 </div>
        </td>
          <td class="dt-add">
              <a href='#' class="addToTable" source="tableapi/getmanagementsRow/managements"><i class="fas fa-plus-circle"></i></a>
          </td>
    </tr>

       <?php
          if (isset($managements))
              foreach ($managements as $management) {
               
                echo '<tr role="row" id="' . $management->id . '" class="editTabTRs">
                      <input name="managements[' . $management->id . '][id]" type="hidden" value="' . $management->id . '"/>
                      <td>' . $management->fname . '
                      <input name="managements[' . $management->id . '][frequency]" type="hidden" value="' . $management->frequency . '"/>
                      </td>
                      <td>' . $management->name  . '<input name="managements[' . $management->id . '][vendor]" type="hidden" value="' . $management->vendor . '"/></td>
                      <td>' . $management->uname  . '<input name="managements[' . $management->id . '][unit_id]" type="hidden" value="' . $management->unit_id . '"/></td>
                      <td>' . $management->amount  . '<input name="managements[' . $management->id . '][amount]" type="hidden" value="' . $management->amount . '"/></td>
                      <td>' . $management->start_date  . '<input name="managements[' . $management->id . '][start_date]" type="hidden" value="' . $management->start_date . '"/></td>
                      <td>' . $management->end_date  . '<input name="managements[' . $management->id . '][end_date]" type="hidden" value="' . $management->end_date . '"/></td>
                      <td>' . $management->iname  . '<input name="managements[' . $management->id . '][item_id]" type="hidden" value="' . $management->item_id . '"/></td>
                      <td>
                         <ul class="check-a a">
                            <li><label for="percentage_fixed" class="checkbox ' . (isset($management) && ($management->percentage_fixed == 1) ? "active" : "" ).'"><input type="checkbox"  disabled ' . (isset($management) && ($management->percentage_fixed == 1) ? "checked" : "") . ' id="percentage_fixed" name="managements[' . $management->id . '][percentage_fixed]" class="hidden" aria-hidden="true"></label></li>
                        </ul> 
                      </td>
                      <td>' . $management->aname  . '<input name="managements[' . $management->id . '][account_id]" type="hidden" value="' . $management->account_id . '"/></td>
                      <td class="text-center link-icon dt-delete"><a href="" class="delete-row"><i class="icon-x"></i></a></td>       
                    </tr>';
              }


              
        ?>

</tbody>
<tfoot>
  <tr></tr>
</tfoot>
</table>

    <script>
         var frequencies = <?php echo json_encode($frequencies); ?>;
        var vendors= <?php echo json_encode($vendors); ?>;
        var subexpenseAccounts = <?php echo json_encode($subexpenseAccounts); ?>;
        var items = <?php echo json_encode($items); ?>;
        var units = <?php echo json_encode($units); ?>;
        console.log('management');
    </script>