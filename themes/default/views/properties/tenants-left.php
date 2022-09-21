                  <form action="./" method="post" class="form-search double">
            <ul class="list-square">
              <li id = "exportocsvC"><i id = "exportocsvC"  class="icon-excel"></i> </li>
              <li class="a"><a id="addTenantButton" href="#addTenant"><i class="icon-plus"></i> <span>Add</span></a></li>
            </ul>
            <p>
              <label for="fsa">Search</label>
              <input type="text" id="psearch" name="fsa" >
              <button type="submit">Submit</button>
              <a href="#"><i id = "printIdE" class="icon-microphone"></i> <span>Record</span></a>
            </p>
            <a href="#" class="leasesfilter"><i class="fas fa-ellipsis-v"></i></a>
          </form>
            <div id = "rightList" class="tree-table clickable no-footer footer-nav" role="grid">

					</div>
    <ul class="list-bottom">
            <li><a href="#properties" type="property"><i class="icon-city"></i> <span>Users</span></a></li>
            <li><a href="#units" type="unit"><i class="icon-door"></i> <span>City</span></a></li>
            <li class="active"><a href="#tenants" type="tenant"><i class="icon-users"></i> <span>Door</span></a></li>
            <li><a  href="#leases" type="lease"><i class="icon-users"></i> <span>Door</span></a></li>
    </ul>
    <script>
      $("#printIdE").click(function(){printPart();})
      $("#exportocsvC").click(function(){exportTableToCSV('tran.csv');})
    </script>
  