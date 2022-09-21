<!-- <div class="page-info" data-title="Leases"/>
<div class="column-header">
    <div class="form-row align-items-center">
        <div class="col-12 mb-2 ">
            <a id="addLeaseButton" class="left" href="#addLease"><i class="far fa-plus-square"></i></a>
            <div class="input-group mb-2" style="width:calc(100% - 45px);float:right;">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <input type="text" id="psearch" class="form-control" id="inlineFormInputGroup" placeholder="Search">
            </div>
        </div>
    </div>
</div>
<div class="column-body">
    <table class="tree-table" id="properties-table" style="display:none;">
        <tr>
            <th>Property</th>
            <th>Unit</th>
            <th>Start Date</th>
            <th>End Date</th>
        </tr>
        < ?php
        if (isset($leases))
            foreach ($leases as $lease) {
                echo '<tr data-tt-id="' . $lease->type . "-" . $lease->id . '" data-id="' . $lease->id . '" data-type="lease">
                          <td>' . $lease->property . '</td>
                          <td>' . $lease->unit . '</td>
                          <td style="color:#0fa531;">' . $lease->start . '</td>
                          <td style="color:#0fa531;">' . $lease->end . '</td>
                      </tr>';

            }
        ?>
    </table>
</div> -->






<!--aside-->
<form action="./" method="post" class="form-search double">
            <ul class="list-square">
              <li class="a"><a id="addLeaseButton" href="#addLease"><i class="icon-plus"></i> <span>Add</span></a></li>
            </ul>
            <p>
              <label for="fsa">Search</label>
              <input type="text" id="psearch" name="fsa" >
              <button type="submit">Submit</button>
              <a href="./"><i class="icon-microphone"></i> <span>Record</span></a>
            </p>
            <i class="fas fa-ellipsis-v"></i>
          </form>
            <div id="DataTables_Table_3_wrapper" class="dataTables_wrapper no-footer mobile-hide">
            <div class="dataTables_scroll">
              <div class="dataTables_scrollHead">
                <div class="dataTables_scrollHeadInner">
                  <table class="table-a  mobile-hide clickable dataTable no-footer" role="grid">
                    <thead>
                        <tr role="row">
                            <th>Property</th>
                            <th>Units</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                      </tr>
                    </thead>
                  </table>
                </div>
              </div>
            <div class="dataTables_scrollBody">
            <div class="table-wrapper"  tabindex="-1">
              <table class=" mobile-hide dataTable no-footer tree-table table-a footer-nav" id="DataTables_Table_3 leases-table" role="grid" aria-describedby="DataTables_Table_3_info">
                <thead>
                </thead>
					      <tbody>
                            <?php
if (isset($leases)) {
    foreach ($leases as $lease) {
        echo '<tr data-tt-id="' . $lease->type . "-" . $lease->id . '" data-id="' . $lease->id . '" data-type="lease">
                                                    <td>' . $lease->property . '</td>
                                                    <td>' . $lease->unit . '</td>
                                                    <td style="color:#0fa531;">' . $lease->start . '</td>
                                                    <td style="color:#0fa531;">' . $lease->end . '</td>
                                                </tr>';
        echo $lease->tree;
    }
}

?>

          </tbody>
					</table>
          </div>
        </div>
      </div>
    </div>
        </div>
          <ul class="list-bottom">
            <li><a href="#properties" type="property"><i class="icon-users"></i> <span>Users</span></a></li>
            <li><a href="#units" type="unit"><i class="icon-city"></i> <span>City</span></a></li>
            <li><a href="#tenants" type="tenant"><i class="icon-door"></i> <span>Door</span></a></li>
            <li><a  href="#leases" type="lease"><i class="icon-door"></i> <span>Door</span></a></li>
          </ul>

        <!--/aside-->