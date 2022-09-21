
				<table class="table table-c b dt" id="#rstable">
                        <thead>
							<tr role="row">
								<th class="text-center">Legal Rent</th>
								<th class="text-center">Preferencial Rent</th>
								<th class="text-center">Start Date</th>
								<th class="text-center">End Date</th>
								<th class="text-center">Notes</th>
								<th class="text-center"></th>
							</tr>
						</thead>
            	<tbody>	
				<tr role="row">
                            <td>
                                <span class="input-amount">
                                    <label for="legal_rent"></label>
                                    <input type="number" id="legal_rent" name="temp[legal_rent]">
                                </span>
								<input type="hidden" name="temp[ind]" value="<?php echo count($rent_stabilized);?>">
                            </td>
                            <td>
                                <span class="input-amount">
                                    <label for="preferencial_rent"></label>
                                    <input type="number" id="preferencial_rent" name="temp[preferencial_rent]">
                                </span>
                            </td>
                            <td class="date-picker">
                                <span class="input-amount">
                                    <label for="start_date"></label>
                                    <input data-toggle="datepicker" id="start_date" name="temp[start_date]">
                                </span>
                            </td>

                            <td class="date-picker">
                            <span class="input-amount">
                                <label for="end_date"></label>
                                <input data-toggle="datepicker" id="end_date" name="temp[end_date]">
                              </td>
							  <td>
                            <span class="input-amount">
                                <label for="notes"></label>
                                <input type="text" id="notes" name="temp[notes]">
                              </td>
                                <td style="width:2%;" class="dt-add">
                                    <a href='#' class="addToTable" source="tableapi/getRsRow/rs"><i class="fas fa-plus-circle"></i></a>
                                </td>
                          </tr>
          			<?php
                        if (isset($rent_stabilized) && $rent_stabilized != null) foreach ($rent_stabilized as $rs) {
							echo '<tr id="' . $rs->id . '" role="row" class="editTabTRs">
								<input name="rs[' . $rs->id . '][id]" type="hidden" value="' . $rs->id . '"/>
                              	<td  class="text-center">' . $rs->legal_rent . '<input name="rs[' . $rs->id . '][legal_rent]" type="hidden" value="' . $rs->legal_rent . '"/></td>
                                <td  class="text-center">$' . $rs->preferencial_rent . '<input name="rs[' . $rs->id . '][preferencial_rent]" type="hidden" value="' . $rs->preferencial_rent . '"/></td>
                                <td  class="text-center date-picker">' . humanDate($rs->start_date) . '<input name="rs[' . $rs->id . '][start_date]" type="hidden" value="' . $rs->start_date . '"/></td>
                                <td class="text-center date-picker">' . humanDate($rs->end_date) . '<input name="rs[' . $rs->id . '][end_date]" type="hidden" value="' . $rs->end_date . '"/></td>
								<td  class="text-center">' . $rs->notes . '<input name="rs[' . $rs->id . '][notes]" type="hidden" value="' . $rs->notes . '"/></td>
								<td style="width:2%;" class="text-center link-icon dt-delete"><a href="" class="delete-row"><i class="icon-x" style="margin-left:65%;"></i></a></td> 
                              </tr>';
                        } ?>	

						</tbody>
						<tfoot>
							<tr>

							</tr>
						</tfoot>
				  </table>
      