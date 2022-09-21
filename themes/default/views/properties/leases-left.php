
<!--aside-->
<form action="./" method="post" class="form-search double">
            <ul class="list-square">
              <li id = "exportocsvC"><i id = "exportocsvC"  class="icon-excel"></i> </li>
              <li class="a"><a id="addLeaseButton" href="#addLease"><i class="icon-plus"></i> <span>Add</span></a></li>
            </ul>
            <p>
              <label for="fsa">Search</label>
              <input type="text" id="psearch" name="fsa" >
              <button type="submit">Submit</button>
              <a href="#"><i id = "printIdF" class="icon-microphone"></i> <span>Record</span></a>
            </p>
            <a href="#" class="leasesfilter"><i class="fas fa-ellipsis-v"></i></a>
          </form>
          <div id = "rightList" class=" footer-nav mobile-hide tree-table clickable table-a" id="DataTables_Table_3 leases-table" role="grid" aria-describedby="DataTables_Table_3_info">

                    </div>


          <ul class="list-bottom">
            <li><a href="#properties" type="property"><i class="icon-city"></i> <span>Users</span></a></li>
            <li><a href="#units" type="unit"><i class="icon-door"></i> <span>City</span></a></li>
            <li><a href="#tenants" type="tenant"><i class="icon-users"></i> <span>Door</span></a></li>
            <li class="active"><a  href="#leases" type="lease"><i class="icon-users"></i> <span>Door</span></a></li>
          </ul>

        <!--/aside-->
        <script>
          
         $("#printIdF").click(function(){printPart();}) 
         $("#exportocsvC").click(function(){exportTableToCSV('tran.csv');})
       
        </script>