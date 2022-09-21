<?php if(isset($singleInventory) && isset($singleInventory->item_name)) $inventoryName = $singleInventory->item_name; $typeId = $singleInventory->id;?> <!--used for notes-->
<!-- This is the main pagefor accounts right simular to forms/property/main.php-->

          <div class="cols-b">
            <div class="module-info">
              <figure><img src="<?= isset($singleInventory) && $singleInventory->img_url != '' ? base_url() . "uploads/images/" . $singleInventory->img_url : '' ?>" alt="Placeholder"  height="200px" width="200px" style="object-fit: cover; width:200px; height:150px !important; opacity: 0.5;"></figure>
              <h2><?= isset($singleInventory) && isset($singleInventory->item_name) ? $singleInventory->item_name : '' ?><br> Item #<?= isset($singleInventory) && isset($singleInventory->id) ? $singleInventory->id : '' ?><br>
              <?= isset($singleInventory) && isset($singleInventory->type) ? $singleInventory->type : '' ?></h2>
              <footer>
                <p style="text-align:center"> <span class="overlay-d">6</span> <span class="em">pieces in stock</span></p>
                <p class="link-btn"><a href="./">Place an order</a></p>
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
            <ul class="list-horizontal nav" id="property-tabs2" role="tablist">
              <li class = "tablinks" ><a href="#setup-tab" onclick="tabswitch(event, 'setup',$(this))">Setup</a></li>
              <li class="tablinks" id="defaultOpen"><a  href="#transactions-tab" onclick="tabswitch(event, 'transactions',$(this))">Transactions</a></li>
              <li class = "tablinks" > <a id="notesTab" href="#transactions-tab" onclick="tabswitch(event, 'notes',$(this))">Notes</a></li>
              <li class = "tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'cases',$(this))">Cases</a></li>

            </ul>
            <ul class="list-square">
                
                <li id="editColumns" class="cpopup-trigger" data-target="#columnspopup"><i class="icon-grid"></i></li>
              <li><a href="./"><i class="icon-envelope-outline2"></i> Messages</a></li>
              <li><a href="./"><i class="icon-checklist"></i> <span>Checklist</span></a></li>
              <li><a href="./"><i class="icon-excel"></i> <span>Excel</span></a></li>
              <li id="printId"><!--a href="./" class="print"--><i class="icon-print"></i> <!--span>Print</span></a--></li>
              <li><a href="#" id="addNote"><i class="icon-checklist"></i> <span>Checklist</span></a></li>
            </ul>
          </nav>

          <!-- Tab content -->
<div id="setup" class="tabcontent" style="display:none">
    <?php require_once VIEWPATH . 'inventory/inventory-setup.php';?>
</div>

<div id="transactions" class="active tabcontent defaultOpenTab">
  <?php require_once VIEWPATH . 'inventory/inventory-transactions.php';?>
</div>

<div id="notes" class="tabcontent" style="display:none">
  <?php require_once VIEWPATH . 'inventory/inventory-notes.php';?>
</div>

<div id="cases" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'inventory/inventory-cases.php';?>
</div>

</div>

<!-- for notes -->
<?php require_once VIEWPATH . 'forms/notes/note.php';?>


