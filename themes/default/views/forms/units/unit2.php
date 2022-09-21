
<div class="modal flexmodal fade unit-modal" id="unitModal" tabindex="-1" doc-type="units" role="dialog" main-id=<?= isset($unit) && isset($unit->id) ? $unit->id : '-1' ?> type="unit" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px;">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="unit">
                  
				  	
				<header class="modal-h">
					<h2><?php echo $title; ?></h2>
					<nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
					<nav>
						<ul>
							<li><?= isset($unit) ? '<a href="units/deleteUnit/'.$unit->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li><a href="./"><i class="icon-envelope-outline" aria-hidden="true"></i> <span>Envelope</span></a></li>
							<li class="getDocuments"><a href="#" class="uploadDocument"><i class="icon-paperclip" aria-hidden="true"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print" aria-hidden="true"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>
				<section class="c" style="padding-left: 25px;">
					<div class="d-flex">
						<div class="flex-fill" style="width: 25%; padding-right: 25px;">
							<p>
								<label for="name">Unit</label>
                                <input type="text" value="<?= isset($unit) && isset($unit->name) ? $unit->name : '' ?>" class="form-control" name="<?php echo str_replace("field","name", $fieldFormat);?>" id="name">
                                <?php if (isset($unit) && isset($unit->id)) echo '<input type="hidden" name="'.str_replace("field","id", $fieldFormat).'" value="' . $unit->id . '"/>'; ?>
                            </p>
							<p class="select">
								<label for="property_id">Property</label>
                                <select onchange="JS.loadList('api/getUnitsList',$('[name=\'property_id\']:last').val(), '#parent_id',  $(this).closest('.modal'), <?php echo $unit->id ?>)" class="editable-select quick-add set-up" id="property_id" name="<?php echo str_replace("field","property_id", $fieldFormat);?>" modal="property" type="table" key="properties.name">
                                    <?php
                                    echo '<option value="-1" '.(isset($unit) && $unit->property_id == -1 ? 'selected' : '' ).'>'.$property_name.'</option>';
                                    foreach ($properties as $property) {
                                        echo '<option value="' . $property->id . '" ' . (isset($unit) && $unit->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
                                    } ?>
                                 </select>
							</p>
							<p class="select">
								<label for="unit_type_id">Type</label>
                                <select class="editable-select quick-add" id="unit_type_id" name="<?php echo str_replace("field","unit_type_id", $fieldFormat);?>" type="setting" key="unit_types">
                                    <?php
                                    foreach ($unit_types as $k => $utype) {
                                        echo '<option value="' . $k . '" ' . (isset($unit) && $unit->unit_type_id == $k ? 'selected' : '') . '>' . $utype . '</option>';
                                    } ?>
                                </select>
							</p>
							<p class="select">
								<label for="status">Status</label>
                                <select class="editable-select quick-add" id="status" name="<?php echo str_replace("field","status", $fieldFormat);?>" type="setting" key="unit_status">
                                    <?php
                                    foreach ($unit_status as $k => $ustatus) {
                                        echo '<option value="' . $k . '" ' . (isset($unit) && $unit->status == $k ? 'selected' : '') . '>' . $ustatus . '</option>';
                                    } ?>
                                </select>
							</p>
						</div>
						<div class="flex-fill" style="width: 25%; padding-right: 25px;">
							<p class="select">
								<label for="parent_id" style="width: 263px;">Subunit Of</label>
                                <select class="editable-select" id="parent_id" name="<?php echo str_replace("field","parent_id", $fieldFormat);?>">
                                    <?php
                                    echo '<option class="nested0" value="0" ' . (isset($unit) && $unit->parent_id == 0 ? 'selected' : '') . '></option>';
                                    if (isset($subunits))
                                        foreach ($subunits as $sunit) {
                                            if ($unit->id == $sunit->id || $unit->name == $sunit->name) continue;
                                            echo '<option data-id="'.$sunit->id.'" data-parent-id="'.$sunit->parent_id.'" class="nested'.$sunit->step.'"value="' . $sunit->id . '" ' . (isset($unit) && $unit->parent_id == $sunit->id ? 'selected' : '') . '>' . $sunit->name . '</option>';
                                        } ?>
                                </select>
							</p>
							
								<p>
									<label for="sq_ft"  style="width: 137px;">SQ FT</label>
									<input type="text" value="<?= isset($unit) && isset($unit->sq_ft) ? $unit->sq_ft : '' ?>" id="sq_ft" name="<?php echo str_replace("field","sq_ft", $fieldFormat);?>" placeholder="Sq. ft">
								</p>
                            
                            <!-- <div  style="width: 64%;"> -->
								<p class="select">
                                    <label for="late_charge" style="width: 263px;">Late Charge Setup</label>
                                    <select id="late_charge" class="editable-select" name="<?php echo str_replace("field","late_charge", $fieldFormat);?>">
                                        <?php
                                            foreach ($lateCharges as $lateCharge) {
                                                echo '<option value="' . $lateCharge->id .'"' . (isset($unit) && $unit->late_charge == $lateCharge->id ? 'selected' : '') .'>' . $lateCharge->name .'</option>';
                                            } ?>
                                    </select>
									
								</p>
							</div>
							<div class="flex-fill" style="width: 25%;">
								<p>
									<label for="broker">Broker</label>
									<input type="text" value="<?= isset($unit) && isset($unit->broker) ? $unit->broker : '' ?>"  name="<?php echo str_replace("field","broker", $fieldFormat);?>" id="broker">
								</p>
							
							
								<p>
									<label for="market_rent">Market rent</label>
									<input type="text" value="<?= isset($unit) && isset($unit->market_rent) ? $unit->market_rent : '' ?>" name="<?php echo str_replace("field","market_rent", $fieldFormat);?>" id="market_rent">
								</p>
                                <p>
                                    <label for="floor">Floor</label>
                                    <input type="text" value="<?= isset($unit) && isset($unit->floor) ? $unit->floor : '' ?>" name="<?php echo str_replace("field","floor", $fieldFormat);?>" id="floor">
							    </p>
							</div>
							
					</div>	
					<ul class="check-a a">
						<li><label for="active" class="checkbox <?= isset($unit) && isset($unit->active) && ($unit->active == 0) ? '' : 'active' ?>"><input type="hidden" name="<?php echo str_replace("field","active", $fieldFormat);?>" value="0" /><input type="checkbox" value="1" <?= isset($unit) && isset($unit->active) && ($unit->active == 0) ? '' : 'checked' ?> id="active"  name="<?php echo str_replace("field","active", $fieldFormat);?>"  class="hidden" aria-hidden="true"><div class="input"></div>Active?</label></li>
						</ul>
					<p>
						<label for="memo">Memo:</label>
						<input type="text" value="<?= isset($unit) && isset($unit->memo) ? $unit->memo : '' ?>" name="<?php echo str_replace("field","memo", $fieldFormat);?>" id="memo">
					</p>
					
				</section>
				<div class="tabFunction">
			<nav class="double center">
				<ul class="list-horizontal nav" id="property-tabs3" role="tablist">
				<!--li class = "tablinks " > <a href="#taxes-tab" onclick="tabswitch(event, 'utilities',$(this))">Utilities</a></li -->
				<li class = "tablinks" > <a href="#settings-tab" onclick="tabswitch(event, 'listing',$(this))">Listing info</a></li>
				<li class = "tablinksactive"> <a  href="#maintenance-tab" onclick="tabswitch(event, 'maintenance',$(this))">Maintenance</a></li>
			</nav>



            <!-- Tab content -->


             <!-- removed for now because it is causing an error when editiong unit on property-->
            <!-- <div id="utilities" class="active tabcontent defaultOpenTab"  data-id = '9'>
            < ?php require_once VIEWPATH . 'forms/units/utilities.php';?>
            </div> -->

			<div id="listing" class="tabcontent" style="display:none" >
            <?php require_once VIEWPATH . 'forms/units/listing.php';?>
            </div>

			<div id="maintenance" class="tabcontent" style="display:none">
            <?php require_once VIEWPATH . 'forms/units/maintenance.php';?>
            </div>
     </div>



				<footer class="last-child">
					<ul class="list-btn">
						<li><button type="submit" after="mclose">Save &amp; Close</button></li>
						<li><button type="button">Cancel</button></li>
					</ul>
                </footer>
                </form>
        </div>
	</div>
    </div>
</div>