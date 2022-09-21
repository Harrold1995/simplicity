<?php if(isset($tenant) && isset($tenant->name))
    $tenantName = $tenant->name;
$typeId = $tenant->id;
$filter = "profile_id";
$pheader = "<h3>Tenant: ".$tenant->name."</h3>";
?> <!--used for notes-->
<div class="cols-b">
						<div class="double b">
							<div class="module-info">
								<figure ><img src="<?= isset($tenant) && $tenant->img_url != '' ? base_url() . "uploads/images/" . $tenant->img_url : base_url().'themes/default/assets/images/profile.png' ?>" alt="Placeholder" width="168" height="160"></figure>
                                <h2 class="text-center" id = "accountName"><?= isset($tenant) ? $tenant->name : '';?>
                                    <br>
                                    <?php if (isset($tenant)) { echo $tenant->address_line_1;}  ?>
                                    <br>
                                    <?php if (isset($tenant)) { echo $tenant->unit;}  ?>
									<p class="link-btn" style="font-size: 12px;">
										<?php if ($tenant->invite_status == 0) { ?>
											<a class="inviteLink" href="<?php echo base_url() ?>/api/inviteuser/<?php echo isset($tenant) ? $tenant->id : '';?>">Invite to Portal</a>
										<?php } ?>
										<?php if ($tenant->invite_status == 1) { ?>
											Invite pending
                                            <a class="inviteLink" href="<?php echo base_url() ?>/api/inviteuser/<?php echo isset($tenant) ? $tenant->id : '';?>">Resend Invite</a>
										<?php } ?>
										<?php if ($tenant->invite_status == 2) { ?>
											<a class="deleteLink" href="<?php echo base_url() ?>/api/deleteuser/<?php echo isset($tenant) ? $tenant->id : '';?>">Remove from Portal</a>
										<?php } ?>
									</p>

								</h2>
								<footer>
									<ul class="list-contact">
										<li><a class="email" href="<?= isset($tenant) ?'  '.'mailto:'. $tenant->email : '';?>"><?= isset($tenant) ?'  '. $tenant->email : '';?></a></li>
                                        <!--li><a href="tel:3477777454">< ?= isset($tenant) ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($tenant->phone)), 2) : '';?><span class="scheme-a">preffered</span></a></li -->
                                        <li><a href="tel:3477777454"><?= isset($tenant) ? $tenant->phone : '';?></a></li>
									</ul>
									<p class="link-btn"><a href="./">Message</a></p>
								</footer>	
							</div>
							<ul class="list-info">
								<!-- <li>Account #125545</li>
								<li>Balance <span class="overlay-d">$1,250.00</span></li>
								<li class="em">20 days overdue</li> -->
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
                                    <button  onClick="JS.applyRefund(<?= isset($tenant->id) ? $tenant->id : '';?>
                                                                <?= isset($tenant->property_id) ?  ',' . $tenant->property_id : '';?>
                                                                <?= isset($tenant->unit_id) ?  ',' . $tenant->unit_id : '';?>
                                                                <?= isset($lid) ?  ',' . $lid : '';?>)" type="button" style="min-width: 125px">Refund/Apply</button>
                                <?php } ?>
                                </p>
							</ul>
						</div>
						<aside>
							<ul class="list-box">
                                <li onClick="JS.newCharge(<?= isset($tenant->id) ? $tenant->id : '';?>
                                                        <?= isset($lid) ?  ',' . $lid : '';?>)"><a href="#newCharge">Charge</a></li>
                                <li onClick="JS.receive_payment(<?= isset($tenant->id) ? $tenant->id : '';?>
                                                                             <?= isset($tenant->property_id) ?  ',' . $tenant->property_id : '';?>
                                                                             <?= isset($tenant->unit_id) ?  ',' . $tenant->unit_id : '';?>
                                                                             <?= isset($lid) ?  ',' . $lid : '';?>)"><a href="#payment">Payment</a></li>
                                <li id="tenantStatements" data-tenant-id="<?= isset($tenant->id) ? $tenant->id : '';?>" data-lease-id="<?= isset($lid) ? $lid : '';?>"><a href="!#">Statement</a></li>
							</ul>
						</aside>
					</div>

                    <div class="tabFunction">
    <nav class="double center">
                <ul class="list-horizontal nav" id="property-tabs2" role="tablist">
                  <li class = "tablinks" ><a href="#profile-tab" onclick="tabswitch(event, 'documents',$(this))">Documents</a></li>
                  <li class="tablinks" id="defaultOpen"><a  href="#transactions-tab" onclick="tabswitch(event, 'transactions',$(this))">Transactions</a></li>
                  <li class="tablinks" id="sdTab"><a  href="#sdTab" onclick="tabswitch(event, 'sd',$(this))">SD History</a></li>
                  <li class = "tablinks" > <a id="notesTab" href="#transactions-tab" onclick="tabswitch(event, 'notes',$(this))">Notes</a></li>
                  <!--li class = "tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'conversations',$(this))">Conversations</a></li-->
                  <li class="tablinks"><a  href="#transactions-tab" onclick="tabswitch(event, 'maintenance',$(this))">Maintenance</a></li>
                
                </ul>
                <ul class="list-square">
                    
                    <li id="editColumns" class="cpopup-trigger" manual="yes"  data-target="#columnspopup"><i class="icon-grid"></i></li>
                    <li id="exportopdf"><i class="icon-pdf"></i></li>
                    <li id = "exportocsv"><i id = "exportocsv"  class="icon-excel"></i> </li>
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

<div id="sd" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/tenant-sd.php';?>
</div>

<div id="documents" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/properties-documents.php';?>
</div>

<div id="statements" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'accounts/accounts-statements.php';?>
</div>

<div id="notes" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'properties/properties-notes.php';?>
</div>

<div id="maintenance" class="tabcontent" style="display:none">
	<?php require_once VIEWPATH . 'properties/tenants-maintenance.php';?>
</div>

</div>

<!-- for notes -->
<?php require_once VIEWPATH . 'forms/notes/note.php';?>

<script>
  $("#printId").click( function(){printPart();})
  $("#exportocsv").click(function(){exportTableToCSV('tran.csv');})
  $('.inviteLink').click( function(e){
  	e.preventDefault();
  	console.log(e);
  	$.post($(this).attr('href'), function(data){
		  JS.showAlert(data.status, data.message);
	  }, 'JSON');

  });
  function removeUser() {
	  $.post($('.deleteLink').attr('href'), function(data){
		  JS.showAlert(data.status, data.message);
	  }, 'JSON');
  }
  $('.deleteLink').click( function(e){
	  e.preventDefault();
	  JS.showAlert('warning', 'You want to remove this tenant from the portal?', null, removeUser);


  })
</script>
