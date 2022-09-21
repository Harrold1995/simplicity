<?php if(isset($getSingleAccount) && isset($getSingleAccount->name)) $accountName = $getSingleAccount->name;?> <!--used for notes-->
<!-- This is the main pagefor accounts right simular to forms/property/main.php-->

          <div class="cols-b">
            <div class="module-info">
            <?php 

              $image = '';
              if(isset($specialAccount->finins->logo)){
                $image = 'data:image/jpeg;base64,'.$specialAccount->finins->logo;
              } elseif(isset($getSingleAccount) && isset($getSingleAccount->image)){
                $image = base_url().'uploads/images/' . $getSingleAccount->image .'.png';
              } else {
                $image = base_url().'uploads/images/accountImage.png';
              }

              ?>


              <figure style="width:168px;height:160px;" class="reportLinkMultcont" property_id = "<?= isset($property) ? $property->id : '200'; ?>">
                
                <div class = "figcontainer">
                <img src="<?php echo $image; ?>" alt="Placeholder" style="width:158px; height:158px; padding:10px">
                    <figcaption><span id="title2"><?= isset($property) ? $property->name : ''; ?></span>
                    </figcaption>
                    <figcaption class ="hover">

                        <p class = "link-btn">
                          <br><br>
                          <?php if(isset($specialAccount->custom)){ ?>
                            <input type="text" id ='ignorebeforedate'>
                            <a id='ignoreBefore' data-id='<?php echo $specialAccount->finins->account_id ?>'>Ignore transactions Prior to Connecting your bank</a>
                        
                          <?php } else if(!$recDisplay !== "other"){?>

                              <a  class="button" id="chooseBankButton1"  data-account = "<?= $getSingleAccount->id ?>"  data-bank = "<?= isset($specialAccount->id) ? $specialAccount->id :0 ?>">Connect to Bank</a>
                            <?php } ?>
                            
                        </p>
                        
                        <br>
                        
                    </figcaption>
                </div>
                
            </figure>
              
              <h2> <span id = "title2"><?= isset($getSingleAccount) && isset($getSingleAccount->name) ? $getSingleAccount->name : '' ?></span>
                <br> Account # <span><?= isset($getSingleAccount) && isset($getSingleAccount->accno) ? $getSingleAccount->accno : '' ?> </span>
                <br> <?= isset($getSingleAccount) && isset($getSingleAccount->type) ? $getSingleAccount->type : '' ?>
                <span > <input  id="accountNum" type="hidden" value="<?php echo $getSingleAccount->id ?>"></span>
              </h2>
              
              <?php if($recDisplay == "start"){?>
                <footer>
                  <p>Last rec date <span class="overlay-d"><?= isset($reconciliation) && isset($reconciliation->statement_end_date) ? $reconciliation->statement_end_date : "" ?></span> <span class="em"><?= isset($reconciliation) && isset($reconciliation->statement_end_date) ?  round((time() - strtotime($reconciliation->statement_end_date)) /(60 * 60 * 24))." Days ago" : "no recs" ?></span></p>

                  <?php if(isset($specialAccount->finins)){
                    echo '<p class="link-btn"><a href="#!" data-rec-type="'.$getSingleAccount->account_types_id.'" id="reconciliationAutoButton">Start New Rec</a></p>';
                  } else {
                    echo '<p class="link-btn"><a href="#!" data-rec-type="'.$getSingleAccount->account_types_id.'" id="reconciliationButton">Start New Rec</a></p>';
                  } ?>

                  
                </footer>
              <?php }?>

              
              <?php if($recDisplay == "continue"){?>
                <footer class="grow">
								<div class="triple">
									<p>Statement Balance<span class="overlay-d">$<?= isset($reconciliation) && isset($reconciliation->statement_bal) ? number_format($reconciliation->statement_bal,2) : "<br>" ?></span> <span class="em"><?= isset($reconciliation) && isset($reconciliation->statement_end_date) ? $reconciliation->statement_end_date : "<br>" ?></span></p>
									<p>Cleared Balance<span class="overlay-c">$<?= isset($cleared) && isset($cleared[0]->amount) ?  number_format( $reconciliation->beginning_bal + $cleared[0]->amount,2) : "<br>" ?></span> <span class="em"><?= isset($count) && isset($count[0]->count) ? $count[0]->count : "<br>" ?></span></p>
									<p>Difference<span class="overlay-d">$<?= isset($reconciliation) ? number_format($reconciliation->statement_bal - ($reconciliation->beginning_bal + $cleared[0]->amount),2) : "<br>" ?></span></p>
								</div>
								<p class="link-btn"><a href="#!" data-rec-type="<?php echo $getSingleAccount->account_types_id; ?>" id="reconciliationButton">Continue Rec</a></p>
							</footer>	
              <?php }?>
              <?php if($recDisplay == "other"){?>
                <canvas id="bar-chart" width="200" height="150"></canvas>
                <label for="property_filter">Property Filter:
                                    <div class=" field-select">
                                        <select style="padding-right: 12px !important;" class="form-control editable-select inputStyle" id="property_filter" >
                                            <option value="0" selected>All Properties</option>
                                            <?php
                                            foreach ($properties as $property) {
                                                echo '<option value="' . $property->id . '" >'. $property->name . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div> </label>
              <?php }?>
            </div>
            <aside>
              <ul class="list-box">
              <?php if($getSingleAccount->type == "Bank"){ ?>
                    <li><a href="#printCheck" id="printBlankCheck"  data-account-id=<?= isset($getSingleAccount) ? $getSingleAccount->id : '';?>>Blank Check</a></li>
              <?php }elseif ($getSingleAccount->type == "Credit Card"){ ?>
                <li><a href="#ccbutton" id="ccButton" data-account-id="<?= isset($getSingleAccount) ? $getSingleAccount->id : '';?>">Card Transaction</a></li>
             <?php } else{ ?>
                <li><a href="#journalEntry" id="checkButton" >Journal Entry</a></li>
             <?php } ?>              
                <li><a href="#check" id="checkButton" data-account-id="<?= isset($getSingleAccount) ? $getSingleAccount->id : '';?>">Check</a></li>
                <li><a href="#deposit" class="depositButton">Deposit</a></li>
              </ul>
            </aside>
          </div>

		<div class="tabFunction">
          <nav class="double center">
            <ul class="list-horizontal nav" id="property-tabs2" role="tablist" style="margin: 0 auto">
              <li class = "tablinks" ><a href="#setup-tab" onclick="tabswitch(event, 'setup',$(this))">Setup</a></li>
              <li class="tablinks" id="defaultOpen"><a  href="#transactions-tab" onclick="tabswitch(event, 'transactions',$(this))">Transactions</a></li>
              <li class = "tablinks" > <a id="notesTab" href="#transactions-tab" onclick="tabswitch(event, 'notes',$(this))">Notes</a></li>
              <li class = "tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'statements',$(this))">Statements</a></li>
              <?php if(isset($specialAccount->custom)){ ?>
                <li class = "tablinks"> <a  href="#plaid-tab" onclick="tabswitch(event, 'plaid',$(this))">bank transactions</a></li>
              <?php } ?>

            </ul>
           
            <ul class="list-square">
                
                <li id="editColumns" class="cpopup-trigger" data-target="#columnspopup"><i class="icon-grid"></i></li>
              
              
              <li id = "exportocsv"><i id = "exportocsv" class="icon-excel"></i></li>
              <li id="printId"><!--a href="./" class="print"--><i id="printId" class="icon-print"></i> <!--span>Print</span></a--></li>
              <li><a href="#" id="addNote"><i class="icon-checklist"></i> <span>Checklist</span></a></li>
            </ul>
          </nav>

          <!-- Tab content -->
<div id="setup" class="tabcontent" style="display:none">

    <?php  require_once VIEWPATH . 'accounts/accounts-setup2.php';?>
</div>

<div id="transactions" class="active tabcontent defaultOpenTab">
  <?php require_once VIEWPATH . 'accounts/accounts-transactions.php';?>
</div>

<div id="notes" class="tabcontent" style="display:none">
  <?php require_once VIEWPATH . 'accounts/accounts-notes.php';?>
</div>

<div id="statements" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'accounts/accounts-statements.php';?>
</div>

  <?php if(isset($specialAccount->custom)){ ?>
      <div id="plaid" class="tabcontent" style="display:none">
          <?php require_once VIEWPATH . 'accounts/accounts-plaid.php';?>
      </div>
  <?php } ?>
</div>

<!-- for notes -->
<?php require_once VIEWPATH . 'forms/notes/note.php';?>





<script>
    $("#printId").click( function(){printPart();})
    $("#exportocsv").click(function(){exportTableToCSV("fileName");})
 
</script>

<script>
  $('#ignorebeforedate').datepicker();
    $('.editable-select').editableSelect();
      var transactionsGraph = <?php echo $transactionsGraph ? json_encode($transactionsGraph) : '0'?>;
      console.log(transactionsGraph);
      // var transactionsByMonth1 = [2478,5267,734,784,433];
      // var months1 = ["January", "February", "March", "April", "May"];
      // var transactionsByMonth = [2478,5267,734,784,433];
      // var months = ["January", "February", "March", "April", "May"];
      if(transactionsGraph != 0){
            new Chart(document.getElementById("bar-chart"), {
            type: 'bar',
            data: {
              labels: [transactionsGraph[0].month, transactionsGraph[1].month, transactionsGraph[2].month, transactionsGraph[3].month, transactionsGraph[4].month, transactionsGraph[5].month],
              datasets: [
                {
                  label: "Transactions total",
                  backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3e95cd"],
                  data: [transactionsGraph[0].balance, transactionsGraph[1].balance, transactionsGraph[2].balance, transactionsGraph[3].balance, transactionsGraph[4].balance, transactionsGraph[5].balance]
                }
              ]
            },
            options: {
              responsive: false,
              legend: { display: false },
              title: {
                display: true,
                text: 'Transactions total per month.'
              }
            }
        });
      }




</script>
