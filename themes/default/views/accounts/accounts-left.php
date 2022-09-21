          <form action="./" method="post" class="form-search double">
            <ul class="list-square">
            <li><a id="addBillButton" href="#addBillButton"><i class="icon-checklist"></i> <span>Checklist</span></a></li>
              <li><a id="addCreditCard" href="#"><i class="icon-excel"></i> <span>Excel</span></a></li>
              <li class="a"><a id="addAccountButton" href="#addAccount"><i class="icon-plus"></i> <span>Add</span></a></li>
            </ul>
            <p>
              <label for="fsa">Search</label>
              <input type="text" id="psearch" name="fsa" >
              <button  type="submit">Submit</button>
              <a href="#"><i id="printIdB" class="icon-microphone"></i> <span>Record</span></a>
            </p>
              <a href="#" class="activefilter"><i class="fas fa-ellipsis-v"></i></a>
          </form>

            <div id = "accountList" class="tree-table no-footer clickable" role="grid">




					</div>
          <script>
  $("#printIdB").click( function(){printPart();})
  
</script>


















