<?php
if(isset($unit) && isset($unit->name))
    $unitName = $unit->name;
$typeId = $unit->id;
$filter = "unit_id";
$pheader = "<h3>Property: ".$property->name."</h3><h3>Unit: ".$unit->name."</h3>";
?> <!--used for notes-->
<div class="cols-b">
						<div class="double b">
							<div class="module-info">
								<figure><img src="<?= isset($leaseTenants[0]->img_url) && $leaseTenants[0]->img_url != '' ? base_url() . "uploads/images/" . $leaseTenants[0]->img_url : base_url().'themes/default/assets/images/property.png' ?>" alt="Placeholder" width="168" height="160"></figure>
                                <h5 class="text-center" id = "accountName">
                                    
                                    <?=isset($unit) ? $unit->name : '';?>
                                    <br>
                                    <?=isset($property) ? $property->address : '';?>
                                    <br>
                                    <?=isset($unit) ? $unit->floor : '';?>-floor
                                     
                                </h5>
                                <h5 class="text-center">
                                    Unit Type
                                    <br>
                                <span style="font-weight:bold"><?=isset($unitType) ? $unitType->name : '';?></span>
                                    <br>
                                    <span class="text-center">Square Feet</span>
                                    <br>
                                    <span style="font-weight:bold"><?=isset($unit) ? $unit->sq_ft : '';?></span>                                 
                                </h5>
                                <h5 class="text-center">
                                    <span style="font-size: 14px;">Current Lease Period</span>
                                    <br>
                                    <span style="font-size: 16px;text-align:center;font-weight:bold;">
                                          <?php if(isset($lease)){
                                            echo  date("m/d/Y", strtotime($lease[0]->start)) .'-'. date("m/d/Y", strtotime($lease[0]->end));}
                                            else{echo "No current lease";}
                                          ?>
                                  </span>
                                  <br>
                                  <span style="font-size: 14px;">Current Lease Amount</span>
                                    <br>
                                  <span style="font-size: 16px;text-align:center;color:#df571b;font-weight:bold;">
                                    <?php if(isset($lease)){
                                        echo '$' . number_format($lease[0]->amount, 2);}
                                        else{echo "No current lease amount";}
                                      ?>
                                </span>                            
                                </h5>
							</div>
							<ul class="list-info">
                                <p>
                                    <b>Security Deposit</b>
                                    <h3 style="color:#df571b; text-align:center;"><b><?= isset($lease) ? number_format($lease->deposit, 2) : "<br>";?></b></h3>
                                </p>
                                <p>
                                    <b>Last Month's Rent</b>
                                </p>
                                <h3 style="color:#df571b; text-align:center;"><b><?= isset($lease) ? number_format($lease->last_month, 2) : "<br>";?></b></h3>
							</ul>
						</div>
						<aside>
							<ul class="list-box">
								<li  onClick="JS.newLease(<?= isset($unit->id) ? $unit->id : '';?>)"><a href="#newLease">New Lease</a></li>
								<li><a href="#">Listing Info</a></li>
								<li><a href="#">Rental History</a></li>
							</ul>
						</aside>
					</div>

                <div class="tabFunction">
    <nav class="double center">
                <ul class="list-horizontal nav" id="property-tabs2" role="tablist">
                  <li class = "tablinks" ><a href="#profile-tab" onclick="tabswitch(event, 'documents',$(this))">Documents</a></li>
                  <li class="tablinks" id="defaultOpen"><a  href="#transactions-tab" onclick="tabswitch(event, 'transactions',$(this))">Transactions</a></li>
                  <li class = "tablinks" > <a id="utilitiesTab" href="#utilities-tab" onclick="tabswitch(event, 'utilities',$(this))">Utilities</a></li>
                  <li class = "tablinks" > <a id="notesTab" href="#transactions-tab" onclick="tabswitch(event, 'notes',$(this))">Notes</a></li>
                  <!-- li class = "tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'conversations',$(this))">Conversations</a></li -->
                  <li class="tablinks"><a  href="#transactions-tab" onclick="tabswitch(event, 'maintenance',$(this))">Maintenance</a></li>
                </ul>
            <ul class="list-square">
               
                <li id="editColumns" class="cpopup-trigger" data-target="#columnspopup"><i class="icon-grid"></i></li>

                <li id = "exportocsv"><i id = "exportocsv"  class="icon-excel"></i> </li>
              <li id="printId"><!--a href="./" class="print"--><i id="printId" class="icon-print"></i> <!--span>Print</span></a--></li>
              <li><a href="#" id="addNote"><i class="icon-checklist"></i> <span>Checklist</span></a></li>
            </ul>
      </nav>

          


            <!-- Tab content -->

<div id="transactions" class="active tabcontent defaultOpenTab">
<?php require_once VIEWPATH . 'properties/properties-transactions.php';?>
</div>

<div id="documents" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/properties-documents.php';?>
</div>

<div id="utilities" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/units-utilities.php';?>
</div>

<div id="notes" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/properties-notes.php';?>
</div>

<div id="conversations" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/properties-conversations.php';?>
</div>

<div id="maintenance" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/units-maintenance.php';?>
</div> 

</div>

<?php require_once VIEWPATH . 'forms/notes/note.php';?>

<script>
  $("#printId").click( function(){printPart();})
  $("#exportocsv").click(function(){exportTableToCSV('tran.csv');})

</script>
