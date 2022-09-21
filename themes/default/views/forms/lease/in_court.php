
				<table class="table table-c b dt" id="#courttable">
                        <thead>
							<tr role="row">
								<th>Name</th>
								<th>Case</th>
								<th>Attorney</th>
								<th>Follow Up Date</th>
                                <th>Follow Up Reason</th>
								<th>Warrant Requested</th>
								<th>Warrant Issued</th>
                                <th></th>
                            </tr>
						</thead>
            	<tbody>	
				<tr role="row">
                            <td>
                                <!-- <span class="input-amount">
                                    <label for="name"></label>tenants_on_lease
                                    <input type="text" id="name" name="temp[name]">
                                </span> -->

                                <label for="tenants_on_lease_name"></label>
                                <span class="select">
                                <select id="tenants_on_lease_name" class="editable-select" name="temp[tenants_on_lease_name]">
                                <?php
                                    foreach ($tenants_on_lease as $tenant) {
                                        echo '<option value="' . $tenant->id .'+' . $tenant->name .'">' . $tenant->name .'</option>';
                                        $jtenants_on_lease[$tenant->id] = $tenant->name;
                                    } ?>
                                </select>
                                </span>
                              </span>
								<input type="hidden" name="temp[ind]" value="<?php echo count($in_court);?>">
                            </td>
                            <td>
                            <span class="input-amount">
                                <label for="case_num"></label>
                                <input  type="text" id="case_num" name="temp[case_num]">
                              </td>
                              <td>
                              <span class="input-amount">
                                <label for="attorney"></label>
                                <input  type="text" id="attorney" name="temp[attorney]">
                              </td>
							  <td class="date-picker">
                            <span class="input-amount">
                                <label for="follow_up_date"></label>
                                <input data-toggle="datepicker" id="follow_up_date" name="temp[follow_up_date]">
                              </td>
                              <td>
                            <span class="input-amount">
                                <label for="follow_up_reason"></label>
                                <input  type="text" id="follow_up_reason" name="temp[follow_up_reason]">
                              </td>
                              <td>
                                <ul class="check-a a">
                                    <li><label for="warrant_requested" class="checkbox "><input type="checkbox" value="1"  id="warrant_requested" name="temp[warrant_requested]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                                </ul>
                              </td>
							  <td>
                                <ul class="check-a a">
                                    <li><label for="warrant_issued" class="checkbox "><input type="checkbox" value="1"  id="warrant_issued" name="temp[warrant_issued]" class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                                </ul>
                              </td>
                                <td style="width:2%;" class="dt-add">
                                    <a href='#' class="addToTable" source="tableapi/getIn_courtRow/in_court"><i class="fas fa-plus-circle"></i></a>
                                </td>
                          </tr>
          			<?php
                        if (isset($in_court) && $in_court != null) foreach ($in_court as $court) {
							echo '<tr id="' . $court->id . '"  role="row"  class="editTabTRs inCourt">
									<input name="in_court[' . $court->id . '][id]" type="hidden" value="' . $court->id . '"/>
									<td>' . $court->profile_name . '<input name="in_court[' . $court->id . '][profile_id]" type="hidden" value="' . $court->profile_id . '"/></td>
									<td>' . $court->case_num . '<input name="in_court[' . $court->id . '][case_num]" type="hidden" value="' . $court->case_num . '"/></td>
                                    <td>' . $court->attorney . '<input name="in_court[' . $court->id . '][attorney]" type="hidden" value="' . $court->attorney . '"/></td>
                                    <td class="date-picker">' . humanDate($court->follow_up_date) . '<input name="in_court[' . $court->id . '][follow_up_date]" type="hidden" value="' . $court->follow_up_date . '"/></td>
                                    <td>' . $court->follow_up_reason . '<input name="in_court[' . $court->id . '][follow_up_reason]" type="hidden" value="' . $court->follow_up_reason . '"/></td>
                                    <td>
                                        <ul class="check-a">
                                            <li><label for="warrant_requested" class="checkbox ';
                                            echo ($court->warrant_requested ==1) ? "active" : "";
                                            echo '"><input type="hidden" name="in_cour[' . $court->id . ']t[warrant_requested]" value="0" /><input type="checkbox" value="1"';
                                            echo ($court->warrant_requested ==1) ? "checked" : "";
                                            echo 'id="warrant_requested" name="in_court[' . $court->id . '][warrant_requested]"  class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                                        </ul>
                                    </td>
                                    <td>
                                        <ul class="check-a">
                                            <li><label for="warrant_issued" class="checkbox ';
                                            echo ($court->warrant_issued ==1) ? "active" : "";
                                            echo '"><input type="hidden" name="in_court[' . $court->id . '][warrant_issued]" value="0" /><input type="checkbox" value="1"';
                                            echo ($court->warrant_issued ==1) ? "checked" : "";
                                            echo 'id="warrant_issued" name="in_court[' . $court->id . '][warrant_issued]"  class="hidden" aria-hidden="true"><div class="input"></div></label></li>
                                        </ul>
                                    </td>
                                    <td style="width:2%;" class="text-center link-icon dt-delete"><a href="" class="delete-row"><i class="icon-x" style="margin-left:65%;"></i></a></td> 
                              </tr>';
                        } ?>	

						</tbody>
						<tfoot>
							<tr>
                            <input type="hidden" id="tenants_on_lease" value="<?php echo htmlspecialchars($jtenants_on_lease);?>">
							</tr>
						</tfoot>
				  </table>
      