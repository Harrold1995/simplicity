<?php if(isset($getSingleAccount) && isset($getSingleAccount->name)) $accountName = $getSingleAccount->name;?> <!--used for notes-->
<!-- This is the main pagefor accounts right simular to forms/property/main.php-->

          <div class="cols-b">
            <div class="module-info">
              <figure style="width:168px;height:160px;"><img  src="<?=base_url()?>uploads/images/accountImage.png" alt="Placeholder"  width="168" height="160"></figure>
              <h2 ><span id = "title2"><?= isset($getSingleAccount) && isset($getSingleAccount->name) ? $getSingleAccount->name : '' ?></span>
                <br> Account #<?= isset($getSingleAccount) && isset($getSingleAccount->accno) ? $getSingleAccount->accno : '' ?>
                <br> <?= isset($getSingleAccount) && isset($getSingleAccount->type) ? $getSingleAccount->type : '' ?>
                <span > <input  id="accountNum" type="hidden" value="<?php echo $getSingleAccount->id ?>"></span>
              </h2>
              <footer class="grow">
                        <ul class="list-legend strong">
                            <li>Advertising by Property</li>
                            <li>View by month</li>
                        </ul>
                        <figure class="image-a"><img src="http://placehold.it/517x149/ffffff" alt="Placeholder" width="517" height="149"></figure>
                </footer>	
            </div>
            <aside>
              <ul class="list-box">
                <li><a href="#" id="journalEntryButton">Journal Entry</a></li>
                <li><a href="#" id="checkButton">Check</a></li>
                <li><a href="#" id="depositButton">Deposit</a></li>
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

            </ul>
            <ul class="list-square">
              <li><a href="./"><i class="icon-envelope-outline2"></i> Messages</a></li>
              <li><a href="./"><i class="icon-checklist"></i> <span>Checklist</span></a></li>
              <li id = "exportocsv"><i id = "exportocsv"  class="icon-excel"></i> </li>
              <li id="printId"><!--a href="./" class="print"--><i id="printId" class="icon-print"></i> <!--span>Print</span></a--></li>
              <li><a href="#" id="addNote"><i class="icon-checklist"></i> <span>Checklist</span></a></li>
            </ul>
          </nav>

          <!-- Tab content -->
<div id="setup" class="tabcontent" style="display:none">

    <?php require_once VIEWPATH . 'accounts/accounts-setup.php';?>
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

</div>

<!-- for notes -->
<?php require_once VIEWPATH . 'forms/notes/note.php';?>
 
<script>
  
 
  $("#printId").click( function(){printPart();})
  $("#exportocsv").click(function(){exportTableToCSV("fileName");})
  
</script>
