<?php if(isset($singeVendor) && isset($singeVendor->first_name) && isset($singeVendor->last_name)) $vendorName = $singeVendor->first_name .' ' . $singeVendor->last_name; $typeId = $singeVendor->id;?> <!--used for notes-->
<div class="cols-b">
        <div class="double b">
							<div class="module-info">
              <figure><img src="<?= isset($singeVendor) && $singeVendor->img_url != '' ? base_url() . "uploads/images/" . $singeVendor->img_url : base_url().'themes/default/assets/images/profile.png' ?>" alt="Vendor"  height="200px" width="200px" style="object-fit: cover; width:200px; height:150px !important; opacity: 0.5;"></figure>             
                                <h2 class="text-center"><?= isset($singeVendor) ? $singeVendor->first_name .' ' . $singeVendor->last_name : '';?>
                                    <br>
                                    <?= isset($singeVendor) ? $singeVendor->address_line_1 : '';?>
                                    <br>
                                    <?= isset($singeVendor) ? $singeVendor->unit : '';?>
                                </h2>
								<footer>
									<ul class="list-contact">
										<li><a class="email" href="mailto:someone@someplace.com"><?= isset($singeVendor) ?'  '. $singeVendor->email : '';?></a></li>
										<li><a href="tel:3477777454"><?= isset($singeVendor) ? preg_replace('/\d{3}/', '$0-', str_replace('.', null, trim($singeVendor->phone)), 2) : '';?><span class="scheme-a">  preffered</span></a></li>
									</ul>
									<p class="link-btn"><a href="./">Message</a></p>
								</footer>	
							</div>
							<ul class="list-info">
                                <li>
                                    <label for="property_filter">Property Filter:</label>
                                    <div class=" field-select">
                                        <select style="padding-right: 12px !important;" class="form-control editable-select inputStyle" id="property_filter" >
                                            <option value="0" selected>All Properties</option>
                                            <?php
                                            foreach ($properties as $property) {
                                                echo '<option value="' . $property->id . '" >'. $property->name . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <label for="onlyopen" class="custom-checkbox mt-2">
                                            <input type="checkbox" value="1" id="onlyopen" name="onlyopen" class="hidden" aria-hidden="true">
                                            <div class="input d-inline-block"></div>
                                            Open Only
                                        </label>
                                    </div>
                                </li>
							</ul>
						</div>
						<aside>
							<ul class="list-box">
								<li><a href="#payBill" id="payBillButton" data-vendor-id="<?= isset($singeVendor) ? $singeVendor->id : '';?>">Pay Bill</a></li>
								<li><a href="#check" onclick = "JS.openDraggableModal('4', 'add', null, null, {profile: <?= isset($singeVendor) ? $singeVendor->id : '';?>});">Check</a></li>
								<li><a href="#addBillButton" onclick="JS.openDraggableModal('2', 'add', null, null, {profile: <?= isset($singeVendor) ? $singeVendor->id : '';?>});">Bill</a></li>
							</ul>
						</aside>
					</div>

		<div class="tabFunction">
          <nav class="double center">
            <ul class="list-horizontal nav" id="property-tabs2" role="tablist">
              <li class = "tablinks" ><a href="#setup-tab" onclick="tabswitch(event, 'setup',$(this))">Setup</a></li>
              <li class="tablinks" id="defaultOpen"><a  href="#bills-tab" onclick="tabswitch(event, 'bills',$(this))">Bills</a></li>
              <li class="tablinks"><a  href="#transactions-tab" onclick="tabswitch(event, 'transactions',$(this))">Transactions</a></li>
              <li class = "tablinks" > <a id="notesTab" href="#transactions-tab" onclick="tabswitch(event, 'notes',$(this))">Notes</a></li>
              <li class = "tablinks"> <a  id="autoTab" href="#transactions-tab" onclick="tabswitch(event, 'autobills',$(this))">Auto Bills</a></li>

            </ul>
            <ul class="list-square">
                
              <li id="editColumns" class="cpopup-trigger" manual="yes"  data-target="#columnspopup"><i class="icon-grid"></i></li>
              <li><a href="#"><i class="icon-envelope-outline2"></i> Messages</a></li>
              <li><a href="#"><i class="icon-checklist"></i> <span>Checklist</span></a></li>
              <li><a href="#"><i id="exportocsv" class="icon-excel slickButton"></i> <span>Excel</span></a></li>
              <li id="printId"><!--a href="./" class="print"--><i class="icon-print"></i> <!--span>Print</span></a--></li>
              <li><a href="#" id="addNote"><i class="icon-checklist"></i> <span>Checklist</span></a></li>
            </ul>
          </nav>

          <!-- Tab content -->

<div id="transactions" class="tabcontent" style="display:none">
  <?php require_once VIEWPATH . 'vendors/vendors-transactions.php';?>
</div>

<div id="bills" class="active tabcontent defaultOpenTab">
  <?php require_once VIEWPATH . 'vendors/vendor-bills.php';?>
</div>

<div id="notes" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'vendors/vendors-notes.php';?>
</div>

<div id="autobills" class="tabcontent" style="display:none" data-id = '19'>
<?php require_once VIEWPATH . 'vendors/vendors-autobills.php';?>
</div>

</div>

<?php require_once VIEWPATH . 'forms/notes/note.php';?>




