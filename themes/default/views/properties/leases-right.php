<?php if(isset($lease) && isset($lease->start))
    $leaseStart = $lease->start;
    $typeId = $lease->id;
    $filter = "lease_id";
$pheader = "<h3>Property: ".$property->name."</h3><h3>Unit: ".$unit->name."</h3><h3>Lease: ".$lease->start." - ".$lease->end."</h3>";
?> <!--used for notes-->
<div class="cols-b">
						<div class="double b">
							<div class="module-info">
                            <footer>
									<ul class="list-contact" >
                                    <?php $emptyDateCheck =  isset($lease) && $lease->move_out == "" ? 'leaveEmpty' : "" ?>
										<li style="padding:0px">Lease Dates<li>
										<li style="padding:0px">Lease Start: <input id="lstart" name="lease_start" class = "datepickerright leaveEmpty" data-toggle="datepicker" type="text" value ="<?= isset($lease) ? $lease->start : '';?>"> <li>
                                        <li style="padding:0px">Lease End: <input id="lend"  name="lease_end" class = "datepickerright leaveEmpty" data-toggle="datepicker" type="text" value ="<?= isset($lease) ? $lease->end : '';?>"><li>
									</ul>
                  <!--p class="link-btn"><a href="./">Renew</a></p>
                  <p class="link-btn"><a href="./">Move Out</a></p-->
								</footer>
                                <h2 class="text-center" id = "accountName">
                                    
                                    <?= isset($lease) ? $lease->address : '';?>
                                    <br>
                                    <?= isset($lease) ? $lease->unit : '';?>
                                    <br>
                                     
                                    <?php //if (isset($leaseTenants)){
                                      if($leaseTenants[0]->tenant){echo $leaseTenants[0]->tenant; }
                                      if($leaseTenants[1]->tenant){echo ", " . $leaseTenants[1]->tenant; }
                                      if($leaseTenants[2]->tenant){echo ",<br> " . $leaseTenants[2]->tenant; }
                                      ?>
                                        <!-- /foreach ($leaseTenants as $tenant) {
                                        if(isset($tenant->tenant)){ echo $tenant->tenant . ", ";}
                                        for($i = 0; $i < 3; $i++){echo $leaseTenants[$i]->tenant . ", ";}
                                        }
                                        }?> -->
                                        
                                        <?php if(count($leaseTenants) > 3){ echo " & " .(count($leaseTenants) - 3) . " more";} ?>
                                </h2>
								<footer>
									<ul class="list-contact" >
                                    <?php $emptyDateCheckIn =  isset($lease) && $lease->move_out != "" ? '' : "leaveEmpty";
                                          $emptyDateCheckOut =  isset($lease) && $lease->move_out == "" ? 'leaveEmpty' : ""  
                                    ?>
										<li style="padding:0px">Status: <?= isset($current) ? "Current" : 'Past';?><li>
										<li style="padding:0px">Move in: <input id="moveIn" name="move_in" class = "datepickerright <?php echo $emptyDateCheckIn ?>" data-toggle="datepicker" type="text" value ="<?= isset($lease) ? $lease->move_in : '';?>"> <li>
                                        <li style="padding:0px">Move out: <input id="moveout" class="datepickerright <?php echo $emptyDateCheckOut ?>" name="move_out" class = "datepickerright" data-toggle="datepicker" type="text" value ="<?= isset($lease) ? $lease->move_out : '';?>"><li>
									</ul>
                  <!--p class="link-btn"><a href="./">Renew</a></p>
                  <p class="link-btn"><a href="./">Move Out</a></p-->
								</footer>	
							</div>
							<ul class="list-info">
                                <p>
                                    <b>Security Deposit</b>
                                    <h3 style="color:<?= isset($sdBalance) && $sdBalance > 0 ? "#00be00" : "#df571b";?>; text-align:center;"><b>$<?= isset($sdBalance) ? number_format($sdBalance, 2) : "0.00";?></b></h3>
                                </p>
                                <p>
                                    <b>Last Month's Rent</b>
                                </p>
                                <h3 style="color:<?= isset($lmrBalance) && $lmrBalance > 0 ? "#00be00" : "#df571b";?>; text-align:center;"><b>$<?= isset($lmrBalance) ? number_format($lmrBalance, 2) : "0.00";?></b></h3>
                                <p>
                                <?php if($checkApplyRefund){?>
                                    <button  onClick="JS.applyRefund(<?= isset($leaseTenants[0]->id) ? $leaseTenants[0]->id : 'null';?>
                                                                <?= isset($lease->property_id) ?  ',' . $lease->property_id : '';?>
                                                                <?= isset($lease->unit_id) ?  ',' . $lease->unit_id : '';?>
                                                                <?= isset($lease->id) ?  ',' . $lease->id : '';?>)"type="button" style="min-width: 125px">Refund/Apply</button>
                                <?php } ?>
                                </p>
							</ul>
						</div>
						<aside>
							<ul class="list-box">
                                <li onClick="JS.newCharge(0,
                                                          <?= isset($lease->id) ? $lease->id : '';?>)"><a href="#newCharge">Charge</a></li>
                                <li  onClick="JS.receive_payment(<?= isset($leaseTenants[0]->id) ? $leaseTenants[0]->id : 'null';?>
                                                          <?= isset($lease->property_id) ?  ',' . $lease->property_id : '';?>
                                                          <?= isset($lease->unit_id) ?  ',' . $lease->unit_id : '';?>
                                                          <?= isset($lease->id) ?  ',' . $lease->id : '';?>)"><a href="#payment">Payment</a></li>
                                <li id="leaseStatements" data-lease-id="<?= isset($lease->id) ? $lease->id : '';?>"><a href="!#">Statement</a></li>
							</ul>
						</aside>
					</div>

                    <div class="tabFunction">
    <nav class="double center">
                <ul class="list-horizontal nav" id="property-tabs2" role="tablist">
                  <li class = "tablinks" ><a href="#profile-tab" onclick="tabswitch(event, 'documents',$(this))">Documents</a></li>
                  <li class="tablinks" id="defaultOpen"><a  href="#transactions-tab" onclick="tabswitch(event, 'transactions',$(this))">Transactions</a></li>
                  <li class = "tablinks" > <a id="notesTab" href="#transactions-tab" onclick="tabswitch(event, 'notes',$(this))">Notes</a></li>
                  <li class = "tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'sd',$(this))">SD History</a></li>
                  <!--li class="tablinks"><a  href="#transactions-tab" onclick="tabswitch(event, 'transactions',$(this))">Maintenance</a></li>
                  <li class = "tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'Legal',$(this))">Legal</a></li>
                  <li class = "tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'Autocharge',$(this))">Autocharge</a></li-->
                </ul>
                <ul class="list-square">
                    
                    <li id="editColumns" manual="yes" class="cpopup-trigger" data-target="#columnspopup"><i class="icon-grid"></i></li>

                    <li id = "exportocsv"><i id = "exportocsv"  class="icon-excel"></i> </li>
                <li onClick="JS.invoice(<?= isset($lease->id) ? $lease->id : 'null';?>)"><i class="icon-checklist"></i> <span></span></li>
                  <li id="printId"><!--a href="./" class="print"--><i id="printId" class="icon-print"></i> <!--span>Print</span></a--></li>
                  <li><a href="#" id="addNote"><i class="icon-checklist"></i> <span>Checklist</span></a></li>
              </ul>
          </nav>
          


            <!-- Tab content -->
<div id="setup" class="tabcontent" style="display:none">

<?php if ($getCreditCard) {require_once VIEWPATH . 'accounts/accounts-setup2.php';} else {require_once VIEWPATH . 'accounts/accounts-setup.php';}?>
</div>

<div id="transactions" class="active tabcontent defaultOpenTab">
<?php require_once VIEWPATH . 'properties/properties-transactions.php';?>
</div>

<div id="documents" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/properties-documents.php';?>
</div>

<div id="sd" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/tenant-sd.php';?>
</div>

<div id="notes" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/properties-notes.php';?>
</div>

</div>

<!-- for notes -->
<?php require_once VIEWPATH . 'forms/notes/note.php';?>


<?php require_once VIEWPATH . 'forms/notes/note.php';?>

<script>
  $("#printId").click( function(){printPart();})
  $("#exportocsv").click(function(){exportTableToCSV('tran.csv');})

  body = $('.module-info').first();
  JS.datePickerInit(body);

  $(".module-info ").find('input').change(function() {
  move_in =  $(".module-info ").find('input:hidden[name=move_in]').val();
  move_out =  $(".module-info ").find('input:hidden[name=move_out]').val();
  startDate =  $(".module-info ").find('input:hidden[name=lease_start]').val();
  endDate =  $(".module-info ").find('input:hidden[name=lease_end]').val();
  lid =  <?php echo $lease->id;?>;

   $.post({
                    url: 'leases/leaseDates',
                    data: 'lid='+lid+'&start='+startDate+'&end='+endDate+'&in='+move_in+'&out='+move_out,
                    success: function (data) {
                    console.log(data);
                    JS.showAlert("success", "Lease dates are updated!");
                },
                error: function (data) {
                    console.log('error ajax');
                    JS.showAlert("warning", "Error!");
                }
                });
  
});

</script>