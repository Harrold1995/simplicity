
				<table class="table table-c b dt" id="#sect_8table">
                        <thead>
							<tr role="row">
								<th >Voucher Amount</th>
								<th >Voucher #</th>
								<th>Start Date</th>
								<th>End Date</th>
								<th>Notes</th>
								<th style="width:2%;"></th>
								</tr>
						</thead>
            	<tbody>	
				<tr role="row">
                            <td>
                                <span class="input-amount">
                                    <label for="voucher_amount"></label>
                                    <input type="number" id="voucher_amount" name="temp[voucher_amount]">
                                </span>
								<input type="hidden" name="temp[ind]" value="<?php echo count($section_8);?>">
                            </td>
                            <td>
                                <span class="input-amount">
                                    <label for="voucher_num"></label>
                                    <input type="text" id="voucher_num" name="temp[voucher_num]">
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
                                </span>
                              </td>
							  <td>
                            <span class="input-amount">
                                <label for="notes"></label>
                                <input type="text" id="notes" name="temp[notes]">
                              </td>
                                <td style="width:2%;" class="dt-add">
                                    <a href='#' class="addToTable" source="tableapi/getSection8Row/section8"><i class="fas fa-plus-circle"></i></a>
                                </td>
                          </tr>
          			<?php
                        if (isset($section_8) && $section_8 != null) foreach ($section_8 as $sect_8) {
							echo '<tr id="' . $sect_8->id . '"  role="row"  class="editTabTRs">
									<input name="sect_8[' . $sect_8->id . '][id]" type="hidden" value="' . $sect_8->id . '"/>
									<td>' . $sect_8->voucher_amount . '<input name="sect_8[' . $sect_8->id . '][voucher_amount]" type="hidden" value="' . $sect_8->voucher_amount . '"/></td>
									<td>$' . $sect_8->voucher_num . '<input name="sect_8[' . $sect_8->id . '][voucher_num]" type="hidden" value="' . $sect_8->voucher_num . '"/></td>
									<td class="date-picker">' . humanDate($sect_8->start_date) . '<input name="sect_8[' . $sect_8->id . '][voucher_amount]" type="hidden" value="' . $sect_8->start_date . '"/></td>
									<td class="date-picker">' . humanDate($sect_8->end_date) . '<input name="sect_8[' . $sect_8->id . '][voucher_amount]" type="hidden" value="' . $sect_8->end_date . '"/></td>
									<td>' . $sect_8->notes . '<input name="sect_8[' . $sect_8->id . '][voucher_amount]" type="hidden" value="' . $sect_8->notes . '"/></td>
									<td style="width:2%;" class="text-center link-icon dt-delete"><a href="" class="delete-row"><i class="icon-x" style="margin-left:65%;"></i></a></td> 
                              </tr>';
                        } ?>	

						</tbody>
						<tfoot>
							<tr>

							</tr>
						</tfoot>
				  </table>
      