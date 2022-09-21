
             <table class="table table-c" id="#unitsUtilitiestable">
                        <thead>
                            <tr>
                                <th width="9%">Type</th>
                                <th width="13%">Description</th>
                                <th width="14%">Account#</th>
                                <th width="11%">Meter#</th>
                                <th width="11%">Default Exp</th>
                                <th width="11%">Payee</th>
                                <th width="3%">DP?</th>
                                <th width="8%">Paid by</th>
                            </tr>
                        </thead>
                        <tbody>

                          <?php
                            if (isset($utilities))
                                foreach ($utilities as $utility) {
                                echo '<tr role="row"  id="' . $utility->id . '" class="editTabTRs">
                                <input name="utilities[' . $utility->id . '][id]" type="hidden" value="' . $utility->id . '"/>
                                        <td>' . $utility->utName . '
                                        <input name="utilities[' .$utility->id . '][utility_type]" type="hidden" value="' . $utility->utility_type . '"/>
                                        </td>
                                        <td>' . $utility->description  . ' <input name="utilities[' . $utility->id . '][description]" type="hidden" value="' . $utility->description . '"/></td>
                                        <td>' . $utility->account  . '<input name="utilities[' . $utility->id . '][account]" type="hidden" value="' . $utility->account . '"/></td>
                                        <td>' . $utility->meter  . ' <input name="utilities[' . $utility->id . '][meter]" type="hidden" value="' . $utility->meter . '"/></td>
                                        <td>' . $utility->aname  . '<input name="utilities[' . $utility->id . '][default_expense_acct]" type="hidden" value="' . $utility->default_expense_acct . '"/></td>
                                        <td>' . $utility->payeeName  . '<input name="utilities[' . $utility->id . '][payee]" type="hidden" value="' . $utility->payee . '"/></td>
                                        <td>
                                            <ul class="check-a a">
                                                <li><label for="direct_payment" class="checkbox ' . (isset($utility) && ($utility->direct_payment == 1) ? "active" : "" ).'"><input type="hidden" id="direct_payment" name="utilities[' . $utility->id . '][direct_payment]" value="0"><input type="checkbox"  value="1" disabled ' . (isset($utility) && ($utility->direct_payment == 1) ? "checked" : "") . ' id="direct_payment" name="utilities[' . $utility->id . '][direct_payment]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                                            </ul> 
                                        </td>
                                        <td>' . $utility->paid_by  . '<input name="utilities[' . $utility->id . '][paid_by]" type="hidden" value="' . $utility->paid_by . '"/></td>      
                                    </tr>';
                                }
                                
                        ?>
                                <!-- <style type="text/css" onload="editable($(this).closest('#unitsUtilitiestable'));"></style> -->

                              
                        </tbody>
                        <tfoot>

                            <tr>                               
                           <?php echo'  <input name="unit_id" type="hidden" value="' . $unit->id . '"/>';?>
                            </tr><!-- important: this tr is needed to make the add utilities work-->
                        </tfoot>
                  
                </table>

    <script>
         var utilityTypes = <?php echo json_encode($utilityTypes); ?>;
        var subaccounts= <?php echo json_encode($subaccounts); ?>;
        var vendors = <?php echo json_encode($vendors); ?>;
        console.log('utilities');
        $(document).ready(function () {
            console.log('editable select');
            $('body').find('.editable-select').editableSelect();
            JS.checkboxes();
        });
    </script>