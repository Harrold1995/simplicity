
<div class="modal fade transactions-modal" id="transactionsModel" tabindex="-1" role="dialog" main-id=<?=isset($bills) && isset($bills->id) ? $bills->id : '-1'?> type="account" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
                  <form action="<?php echo $target; ?>" method="post" autocomplete="off"type="transactions">


                  <header class="modal-h">
					<h2 class="text-uppercase">Transactions</h2>
					<nav>
						<ul>
							<li><a href="./"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
							<li><a href="./"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
							<li><a href="./"><i class="icon-trash"></i> <span>Delete</span></a></li>
							<li><a href="#!" id="email"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
							<li><a href="./"><i class="icon-brain"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
							<li><a href="./"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>

				<div class="has-table-c">
				<table class="table-c mobile-hide dataTable no-footer treetable tree-table" style="display: table; width: 100%;" id="transactions-table">
					<thead class="dataTables_scrollHead" style="display: table; width: 100%; table-layout: fixed; overflow: hidden;">
						<tr>
							<th id="sort_by_date" width="10%" style="border: 1px solid black;">Sort by date</th>
							<th id="sort_by_account" width="10%" style="border: 1px solid black;">sort by account</th>
							<th id="sort_by_transaction" width="10%" style="border: 1px solid black;">sort by transaction</th>
							<th id="sort_by_property" width="10%" style="border: 1px solid black;">sort by property</th>
							<th id="sort_by_balance"  width="10%" class="text-center">balance</th>
							<!--<th id="sort_by_credit"  width="10%">credit</th>
							<th id="sort_by_description"  width="10%">description</th> -->
						</tr>
					</thead>
                    <tbody id="transactionstable" class="dataTables_scrollBody" style="height: calc(100vh - 180px);overflow: auto;" >
                    </tbody>
                    <style type="text/css" onload="addRowToTransactionsForm($(this).closest('#transactionsModel').find('#transactionstable') )"></style>
			<tfoot>
            </tfoot>
        <footer>
          <ul class="list-btn">
            <!-- <li><button type="submit" after="mnew">Save &amp; New</button></li>
            <li><button type="submit" after="mclose">Save &amp; Close</button></li>
            <li><button>Duplicate</button></li> -->
            <li><button id="cancelButton" type="button">Cancel</button></li>

          </ul>
          <!-- <ul>
            <li>Last Modified 12:22:31 pm 1/10/2018</li>
            <li>Last Modified by <a href="#!">User</a></li>
          </ul> -->
        </footer>
      </form>

                    </div>
		</div>

</div>
</div>


<script src="https://d3js.org/d3.v4.min.js"></script>
<script  type="text/javascript">
console.log('hello');
var transactions = <?php echo $jgetAllTransactions; ?>;
var transactionsBalance = <?php echo $jgetAccountsTotal; ?>;
var eventlistenerArray = [];
   function testToggle(spot, row){
       let aa = "#" + spot;
       let bb = "." + spot;
       eventlistenerArray.push(aa);//used to delete eventlisteners.
    $('body').on('click', aa, function () {
                $( bb ).toggle();
                console.log('got here');
                    if($(bb).css('display') == 'none' ){
                        $( bb ).nextUntil( "tr." + row ).css( "display", "none" );
                    }
                });
   }
  function removeAllListeners(){
    for (var i = 0; i < eventlistenerArray.length; i++) {
//    console.log(eventlistenerArray[i]);
   $('body').off( "click", eventlistenerArray[i] );}
  }
    $('body').on('click', '#cancelButton', function () {removeAllListeners();});
    $('body').on('click', '#sort_by_date', function () {removeAllListeners(); addRowToTransactionsForm($(this).closest('#transactionsModel').find('#transactionstable'),"transaction_date")});
    $('body').on('click', '#sort_by_account', function () {removeAllListeners(); addRowToTransactionsForm($(this).closest('#transactionsModel').find('#transactionstable'),"account_id")});
    $('body').on('click', '#sort_by_transaction', function () {removeAllListeners(); addRowToTransactionsForm($(this).closest('#transactionsModel').find('#transactionstable'),"tid")});
    $('body').on('click', '#sort_by_property', function () {removeAllListeners(); addRowToTransactionsForm($(this).closest('#transactionsModel').find('#transactionstable'),"property_id")});
    $('body').on('click', '#sort_by_balance', function () {removeAllListeners(); addRowToTransactionsForm($(this).closest('#transactionsModel').find('#transactionstable'),"balance")});
    //$('body').on('click', '#sort_by_credit', function () {addRowToTransactionsForm($(this).closest('#transactionsModel').find('#transactionstable'),"credit")});
    //$('body').on('click', '#sort_by_description', function () {addRowToTransactionsForm($(this).closest('#transactionsModel').find('#transactionstable'),"description")});

function addRowToTransactionsForm(body,sortBy) {
    $("#transactionstable").empty();
         var sort = sortBy ? sortBy : 'tid';
         if(sort == "balance"){
            var expensesByName =  sliceData(transactionsBalance, sort);
         }else{
            var expensesByName =  sliceData(transactions, sort);
         }
        

newRow ="";
for (var i = 0; i < expensesByName.length; i++) {
    var z = expensesByName[i].key;
    z = z.replace(/ /g,'');
    z = z.replace(/&/g,"");
    testToggle(z, "one" );
    newRow += '<tr class="d3_table_account_categories one" id="'+ z +'"><td>Name: ' + expensesByName[i].key + '</td></tr>';
        for (var r = 0; r < expensesByName[i].values.length; r++) {
            if (typeof expensesByName[i].values[r].values !== 'undefined' && typeof expensesByName[i].values[r].key !== 'undefined'){
            var zz = expensesByName[i].values[r].key;
            zz = zz.replace(/ /g,'');
            zz = zz.replace(/&/g,"");
            testToggle(z+ '-' +zz, "two" );
            
            newRow += '<tr tabindex="0" class="' + z + '  sub two" data-id="" style="display: none;" id="' + z + '-'+ zz +'"><td style="background-color: yellow;">' + expensesByName[i].values[r].key + '</td></tr>';
            
                for (var y = 0; y < expensesByName[i].values[r].values.length; y++) {
                    if (/*typeof expensesByName[i].values[r].values[y].values !== 'undefined' &&*/ typeof expensesByName[i].values[r].values[y].key !== 'undefined'){
                    var zzz = expensesByName[i].values[r].values[y].key;
                     zzz = zzz.replace(/ /g,'');
                     zzz = zzz.replace(/&/g,"");
                    testToggle(zz+ '-' +zzz, "three" );
                    newRow += '<tr class="'+ z + '-' + zz + ' sub leaf three" data-id="" style="display: none; margin-left:20px;" id="' + zz + '-'+ zzz +'"><td style="background-color: aqua;">' + zzz /*expensesByName[i].values[r].values[y].key*/ + '</td></tr>';
                        if (typeof expensesByName[i].values[r].values[y].values !== 'undefined' && typeof expensesByName[i].values[r].values[y].values[0].values !== 'undefined'){
                            for (var w = 0; w < expensesByName[i].values[r].values[y].values.length; w++) {
                        newRow += '<tr class="'+ zz + '-' + zzz + '" data-id="" style="display: none; margin-left:40px;">';

                            $.each(expensesByName[i].values[r].values[y].values[w].values[0], function(key, value) {
                             newRow += '<td style="background-color: chartreuse;">'+ key + '</br> ' + value +'</td>';
                });
                newRow += '</tr>';
                }
            }else if(sort == "balance"){
                    newRow += '<tr class="'+ zz + '-' + zzz + '" data-id="" style="display: none; margin-left:40px;">';
                    newRow += '<td style="background-color: chartreuse;">Balance: '+ expensesByName[i].values[r].values[y].value +'</td></tr>';
                }
        }
      }
     }

    }

}

	body.append(newRow);
}

function sliceData(data, sort){
    if(sort == "balance"){
    var expensesByName = d3.nest()
  .key(function(d) { return d.acname; })
  .key(function(d) { return d.atname; })
  .key(function(d) { return d.aname; })
  //.key(function(d) { return d.balance; })
  .rollup(function(v) { return d3.sum(v, function(d) { return d.balance; }); })
  .entries(data);
  }else{
    var expensesByName = d3.nest()
  .key(function(d) { return d.acname; })
  .key(function(d) { return d.atname; })
  .key(function(d) { return d.aname; })
  .key(function(d) { return d[sort]; })
    //.rollup(function(v) { return d3.sum(v, function(d) { return d.debit; }); })
  .entries(data);
  }
  console.log(expensesByName);
return expensesByName;
}
</script>

