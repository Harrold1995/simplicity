
    <div class="dataTables_wrapper no-footer has-table-c mobile-hide b">

                  <table class="table-c b dt">
                    <thead>
                        <tr role="row">
                        <th width="7%" class="text-center"></th>
                          <th width="7%" class="text-center">Unit2</th>
                          <th width="7%" class="text-center">Floor</th>
                          <th width="7%" class="text-center">Type</th>
                          <th width="7%" class="text-center">SQ FT</th>
                          <th width="7%" class="text-center">Description</th>
                          <th width="7%" class="text-center">Market Rent</th>
                          <th width="7%" class="text-center">Status</th>
                          <th width="7%" class="text-center link-icon"><a href="#"><i class="icon-plus-circle addUnitButton table-button"></i> <span>Add</span></a></th>
                        </tr>
                      </thead>

					        <tbody>
                      <?php
                      if (isset($property) && $property->units != null)
                          foreach ($property->units as $unit) {
                              echo "<tr data-id='" . $unit->id . "' id='" . $unit->id . "' data-type='unit' data-mode='edit' data-parent='true' role='row'>
                                                              <td width='7%' class='text-center'><i class='icon-door'></i></td>
                                                              <td width='7%' class='text-center'>" . $unit->name . "</td>
                                                              <td width='7%' class='text-center'>" . $unit->floor . "</td>
                                                              <td width='7%' class='text-center' value='" . $unit->unit_type_id . "'>" . $unit->unit_type_name . "</td>
                                                              <td width='7%' class='text-center'>" . $unit->sq_ft . "</td>
                                                              <td width='7%' class='text-center'>" . $unit->memo . "</td>
                                                              <td width='7%' class='text-center'>" . $unit->market_rent . "</td>
                                                              <td width='7%' class='text-center'>" . $unit->status . '</td>
                                                              <td width="7%" class="text-center link-icon"><a href="units/deleteUnit/1"'.$unit->id.'" class="delete-row mr-auto"><i class="icon-x"></i></a></td>
                                                          </tr>';			                                            
                          }
                      ?>

                  </tbody>
                  <tfoot>
                   <tr></tr>
                  </tfoot>
                </table>

        </div>