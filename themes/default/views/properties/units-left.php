<!-- <form action="./" method="post" class="form-search double">
            <ul class="list-square">
              <li class="a"><a id="addUnitButton" href="#addUnit"><i class="icon-plus"></i> <span>Add</span></a></li>
            </ul>
            <p>
              <label for="fsa">Search</label>
              <input type="text" id="psearch" name="fsa" >
              <button type="submit">Submit</button>
              <a href="./"><i class="icon-microphone"></i> <span>Record</span></a>
            </p>
          </form>
            <div id="DataTables_Table_3_wrapper" class="dataTables_wrapper no-footer mobile-hide">
            <div class="dataTables_scroll">
              <div class="dataTables_scrollHead">
                <div class="dataTables_scrollHeadInner">
                  <table class="table-a  mobile-hide dataTable no-footer" role="grid">
                    <thead>
                        <tr role="row">
                            <th>Unit</th>
                            <th>Property</th>
                        </tr>
                        </thead>
                  </table>
                </div>
              </div>
            <div class="dataTables_scrollBody">
            <div class="table-wrapper"  tabindex="-1">
              <table class=" mobile-hide dataTable no-footer tree-table table-a" id="DataTables_Table_3 units-table" role="grid" aria-describedby="DataTables_Table_3_info">
                -->



<!--aside-->
          <form action="./" method="post" class="form-search double">
            <ul class="list-square">
              <li id = "exportocsvC"><i id = "exportocsvC"  class="icon-excel"></i> </li>             
              <li class="a"><a id="addUnitButton" href="#addUnit"><i class="icon-plus"></i> <span>Add</span></a></li>
            </ul>
            <p>
              <label for="fsa">Search</label>
              <input type="text" id="psearch" name="fsa" >
              <button type="submit">Submit</button>
              <a href="#"><i id = "printIdD" class="icon-microphone"></i> <span>Record</span></a>
            </p>
              <a href="#" class="leasesfilter"><i class="fas fa-ellipsis-v"></i></a>
          </form>
            <div id = "rightList" class="footer-nav tree-table clickable no-footer" role="grid">
            </div>

    <ul class="list-bottom">
            <li><a href="#properties" type="property"><i class="icon-city"></i> <span>Users</span></a></li>
            <li class="active"><a href="#units" type="unit"><i class=" icon-door"></i> <span>City</span></a></li>
            <li><a href="#tenants" type="tenant"><i class=" icon-users"></i> <span>Door</span></a></li>
            <li><a  href="#leases" type="lease"><i class=" icon-users"></i> <span>Door</span></a></li>
    </ul>

     <script>
         $("#printIdD").click(function(){printPart();}) 
         $("#exportocsvC").click(function(){exportTableToCSV('tran.csv');})
        </script>