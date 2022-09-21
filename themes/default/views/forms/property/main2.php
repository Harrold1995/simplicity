<?php if(isset($property) && isset($property->name)) $propertyName = $property->name;?> <!--used for notes-->
<div class="modal flexmodal1 flexmodal  fade property-modal hide <?php echo $edit ?>" doc-type="properties" id="propertyModal" tabindex="-1" role="dialog" main-id=<?= isset($property) && isset($property->id) ? $property->id : '-1' ?> type="property" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="property">
                  
				  	<header ></header>
                       <header class="modal-h ui-draggable-handle">
                            <h2 class="text-uppercase"><span><?php echo $title; ?></span></h2>
                        
                            <!--ul class="list-btn ">
                                <li><a href="./">Purchse Closing Statement</a></li>
                                <li><a href="./">Sale Closing Statement</a></li>
                            </ul-->
							<nav>
								<ul>
									<li><span class="buttons" style=""><span class="min" style=";padding: 8px 20px;cursor: pointer;">_</span></span> </li>
									<li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
									<li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
								</ul>
							</nav>
                            <nav>
                                <ul class="">
								<!-- <li><a href=""><i class="icon-chevron-left"></i> <span>Previous</span></a></li> -->
								<li><a href="#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
								<!-- <li><a href="./"><i class="icon-chevron-right"></i> <span>Next</span></a></li> -->
								<li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
                                <li><?= isset($property) ? '<a href="properties/deleteProperty/'.$property->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
                                <li class="get_send_email_form"><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>

                                <li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
                                <li class="getDocuments"><a href="#"  class="uploadDocument"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
                                <li><a class="print printModal" href="#"><i class="icon-print"></i> <span>Print</span></a></li>
                                </ul>
                            </nav>
							</header>

				<section class="b">
					<div class="double e m20">
						<div>
							<p>
								<label for="name">Property Name</label>
								<input type="text" id="name" name="property[name]" value="<?= isset($property) && isset($property->name) ? $property->name : '' ?>">
							</p>
							<p>
								<label for="short_name">Short name</label>
								<input type="text" id="short_name" name="property[short_name]" value="<?= isset($property) && isset($property->short_name) ? $property->short_name : '' ?>">
							</p>
							<p>
								<label for="address">Address</label>
								<input type="text" id="address" name="property[address]" value="<?= isset($property) && isset($property->address) ? $property->address : '' ?>">
							</p>
							<div class="triple a">
								<p>
									<label for="city">City</label>
									<input type="text" id="city" name="property[city]" value="<?= isset($property) && isset($property->city) ? $property->city : '' ?>">
								</p>
								<p class="select">
									<label for="state">State</label>
									
									<!-- < ?php if(isset($property) && isset($property->state)){
										echo '<input type="text" id="state" name="property[state]" value="'. $property->state .'">';}
									else{?> -->
											<select id="state"  name="property[state]" class="editable-select">
												<?php echo StateDropdown( isset($property) && isset($property->state) ? $property->state : null , 'name'); ?>
											</select>
										<!-- < ?php }?>									 -->
									
								</p>
								<p>
									<label for="zip">Zip</label>
									<input type="text"  name="property[zip]" id="zip" value="<?= isset($property) && isset($property->zip) ? $property->zip : '' ?>">
								</p>
							</div>
							<div class="triple b">
								<p>
									<label for="borough">Borough</label>
									<input type="text"  id="borough" name="property[borough]" value="<?= isset($property) && isset($property->borough) ? $property->borough : '' ?>">
								</p>
								<p>
									<label for="block">Block</label>
									<input type="text" id="block" name="property[block]" value="<?= isset($property) && isset($property->block) ? $property->block : '' ?>">
								</p>
								<p>
									<label for="lot">Lot</label>
									<input type="text" name="property[lot]" id="lot" value="<?= isset($property) && isset($property->lot) ? $property->lot : '' ?>">
								</p>
							</div>
						</div>
						<div>
							<p>
								<label for="sq_ft">Total Sq Footage</label>
								<input type="text" id="sq_ft" name="property[sq_ft]" value="<?= isset($property) && isset($property->sq_ft) ? $property->sq_ft : '' ?>"  class="date">
							</p>
							<!-- <p>
								<label for="ffla">Total Sq Footage</label>
								<input type="text" id="ffla" name="ffla" value="25,000">
							</p> -->

							<p>
								<label for="manager">Manager</label>
								<span class="select">
								<select class="editable-select" id="manager" name="property[manager]">
								<option value="-1" selected ></option>

								  <?php
									echo '<option value="0"></option>'; 
									if (isset($allManagers))
										foreach ($allManagers as $managers) {
											echo '<option value="' . $managers->id . '" ' . (isset($manager) && $manager == $managers->id ? 'selected' : '') . '>' . $managers->name . '</option>';
									} ?>
								</select>
								</span>
							</p>

							<p>
								<label for="status">Status</label>
								<span class="select">
								<!-- <input type="text" name="property[status]" id="status" value="< ?= isset($property) && isset($property->status) ? $property->status : '' ?>"> -->
								<select class="form-control editable-select quick-add" id="status" name="property[status]" type="setting" key="property_status">
								<?php
								foreach ($property_status as $k => $stype) {
									echo '<option value="' . $k . '" ' . (isset($property) && $property->status == $k ? 'selected' : '') . '>' . $stype . '</option>';
								} ?>
								</select>
								</span>
							</p>
							<p>
								<label for="default_bank">Default Bank</label>
								<!-- <input type="text" id="default_bank" name="property[default_bank]" value="< ?= isset($property) && isset($property->default_bank) ? $property->default_bank : '' ?>"> -->
								<span class="select">
								<select class="editable-select" id="default_bank" name="property[default_bank]">
								<option value="-1" selected ></option>

								  <?php
									echo '<option value="0"></option>'; 
									if (isset($bankAccounts))
										foreach ($bankAccounts as $bank) {
											echo '<option value="' . $bank->id . '" ' . (isset($property) && $property->default_bank == $bank->id ? 'selected' : '') . '>' . $bank->name . '</option>';
									} ?>
								</select>
								</span>
							</p>
							<p>
								<label for="entity_id">Entities</label>
								<span class="select">
								<select class="editable-select quick-add set-up" id="entity_id" name="property[entity_id]" key = "entities.name" type = "table">
								<option value="-1"></option>
								<?php
									echo '<option value="0"></option>'; 
									if (isset($entities))
										foreach ($entities as $entity) {
											echo '<option value="' . $entity->id . '" ' . (isset($property) && $property->entity_id == $entity->id ? 'selected' : '') . '>' . $entity->name . '</option>';
									} ?>
								</select>
								</span>
							</p>
							<p>
							   <?php if(isset($property) && isset($property->entity_id)) {?>
							     <span class="viewEntity" entity-id ="<?= isset($property->entity_id) ? $property->entity_id : ''?>" style="cursor: pointer;font-size: 14px; box-shadow: 0 3px 6px rgba(0,0,0,.16); padding:8px;">View entity</span>
							   <?php } ?>
								
							</p>
						</div>
					</div>	
					<p>
						<label for="objective">Objective</label>
						<input type="text" name="property[objective]" id="objective"  value="<?= isset($property) && isset($property->objective) ? $property->objective : '' ?>">
					</p>
					<p>
						<label for="memo">Memo</label>
						<input type="text" id="memo" name="property[memo]" value="<?= isset($property) && isset($property->memo) ? $property->memo : '' ?>">
					</p>
					<div class="submit ">
						<p class="input-file">
							<label for="p-image"><input class = "upload" type="file"  id="p-image" targetimg="#property-image" name="image"> <span class="img"><img id="property-image" src="<?= isset($property) && $property->image != '' ? base_url() . "uploads/images/" . $property->image : 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_162b2febc6d%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_162b2febc6d%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.5%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' ?>" alt="Placeholder" width="200" height="200"></span> <span>Attach Photo</span></label>
						</p>
						<ul class="check-a a">
						<li><label for="stabilized" class="checkbox <?= isset($property) && ($property->stabilized == 1) ? 'active' : '' ?> "><input type="hidden" name="property[stabilized]" value="0" /><input type="checkbox" value="1" <?= isset($property) && ($property->stabilized == 1) ? 'checked' : '' ?> id="stabilized" name="property[stabilized]" class="hidden" aria-hidden="true"><div class="input" id ="rent_stabilized_button"></div>Rent Stabilized</label></li>
						<li><label for="active" class="checkbox <?= isset($property) && ($property->active == 1) ? 'active' : '' ?> <?php if($target == "properties/addProperty")echo 'active'; ?>"><input type="hidden" name="property[active]" value="0" /><input type="checkbox" value="1" <?= isset($property) && ($property->active == 1) ? 'checked' : '' ?> <?php if($target == "properties/addProperty")echo 'checked'; ?> id="active" name="property[active]" class="hidden" aria-hidden="true"><div class="input"></div>Active?</label></li>
						
						</ul>
					</div>


				</section>
		<div class="tabFunction">
				<nav class="double center ">
            <ul class="list-horizontal nav" id="property-tabs3" role="tablist" style="margin: 0 auto">
              <li class = "tablinks active" id="defaultOpen"><a href="#units-tab" onclick="tabswitch(event, 'units',$(this))">Units</a></li>
              <li class="tablinks"><a  href="#owners-tab" onclick="tabswitch(event, 'owners',$(this))">Owners</a></li>
              <li class = "tablinks" > <a href="#taxes-tab" onclick="tabswitch(event, 'taxes',$(this))">Taxes</a></li>
              <li class = "tablinks"> <a  href="#utilities-tab" onclick="tabswitch(event, 'utilities',$(this))">Utilities</a></li>
              <li class="tablinks"><a  href="#insurance-tab" onclick="tabswitch(event, 'insurance',$(this))">Insurance</a></li>
			  <li class="tablinks"><a  href="#management-tab" onclick="tabswitch(event, 'management',$(this))">Management</a></li>
              <li class = "tablinks" > <a href="#settings-tab" onclick="tabswitch(event, 'settings',$(this))">Settings</a></li>
							<li class = "tablinks" > <a href="#key_codes-tab" onclick="tabswitch(event, 'key_codes',$(this))">Key codes</a></li>
              <!--li class = "tablinks"> <a href="#notes-tab" onclick="tabswitch(event, 'formNotes',$(this))">Notes</a></li>
              <li class="tablinks"><a  href="#documents-tab" onclick="tabswitch(event, 'documents',$(this))">Documents</a></li-->
			  <li style="<?= isset($property) && ($property->stabilized == 1) ? '' : 'display:none' ?>" class = "tablinks" id="rent_stabilized_tab"> <a  href="#stabilized-tab" onclick="tabswitch(event, 'stabilized',$(this))">Rent Stabilized</a></li>
            </ul>
          </nav>



            <!-- Tab content -->

<div id="units" class="active tabcontent defaultOpenTab" data-id = '3'>
<?php require_once VIEWPATH . 'forms/property/units2.php';?>
</div>

<div id="owners" class="tabcontent" style="display:none" data-id = '11'>
<?php require_once VIEWPATH . 'forms/property/owners.php';?>
</div>

<div id="taxes" class="tabcontent" style="display:none" data-id = '10'>
<?php require_once VIEWPATH . 'forms/property/taxes.php';?>
</div> 

<div id="utilities" class="tabcontent" style="display:none" data-id = '9'>
<?php require_once VIEWPATH . 'forms/property/utilities.php';?>
</div>

<div id="insurance" class="tabcontent" style="display:none" data-id = '8'>
<?php require_once VIEWPATH . 'forms/property/insurance.php';?>
</div>

<div id="settings" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'forms/property/settings.php';?>
</div>

<div id="management" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'forms/property/management2.php';?>
</div>

<div id="formNotes" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'forms/property/notes.php';?>
</div>

<div id="documents" class="tabcontent" style="display:none">
<?php require_once VIEWPATH . 'forms/property/documents.php';?>
</div>
<div id="key_codes" class="tabcontent" style="display:none" data-id = '20'>
<?php require_once VIEWPATH . 'forms/property/key_codes.php';?>
</div>
</div>
				<footer>
					<ul class="list-btn ">
						<li><button type="submit" after="mnew">Save &amp; New</button></li>
						<li><button type="submit"  after="mclose">Save &amp; Close</button></li>
						 <!--< ?php if(strpos($target, 'add')){ ?>-->
							<li><button type="submit" after="duplicate">Duplicate</button></li>
						 <!--< ?php } ?>-->
						<li><button type="button">Cancel</button></li>
					</ul>
					<!--ul>
						<li>Last Modified 12:22:31 pm 1/10/2018</li>
						<li>Last Modified by <a href="./">User</a></li>
					</ul-->
				</footer>
            </form>
        </div>
	</div>
    </div>
</div>

<?php

/**
 * States Dropdown 
 *
 * @uses check_select
 * @param string $post, the one to make "selected"
 * @param string $type, by default it shows abbreviations. 'abbrev', 'name' or 'mixed'
 * @return string
 */
function StateDropdown($post=null, $type='abbrev') {
	$states = array(
		array('AK', 'Alaska'),
		array('AL', 'Alabama'),
		array('AR', 'Arkansas'),
		array('AZ', 'Arizona'),
		array('CA', 'California'),
		array('CO', 'Colorado'),
		array('CT', 'Connecticut'),
		array('DC', 'District of Columbia'),
		array('DE', 'Delaware'),
		array('FL', 'Florida'),
		array('GA', 'Georgia'),
		array('HI', 'Hawaii'),
		array('IA', 'Iowa'),
		array('ID', 'Idaho'),
		array('IL', 'Illinois'),
		array('IN', 'Indiana'),
		array('KS', 'Kansas'),
		array('KY', 'Kentucky'),
		array('LA', 'Louisiana'),
		array('MA', 'Massachusetts'),
		array('MD', 'Maryland'),
		array('ME', 'Maine'),
		array('MI', 'Michigan'),
		array('MN', 'Minnesota'),
		array('MO', 'Missouri'),
		array('MS', 'Mississippi'),
		array('MT', 'Montana'),
		array('NC', 'North Carolina'),
		array('ND', 'North Dakota'),
		array('NE', 'Nebraska'),
		array('NH', 'New Hampshire'),
		array('NJ', 'New Jersey'),
		array('NM', 'New Mexico'),
		array('NV', 'Nevada'),
		array('NY', 'New York'),
		array('OH', 'Ohio'),
		array('OK', 'Oklahoma'),
		array('OR', 'Oregon'),
		array('PA', 'Pennsylvania'),
		array('PR', 'Puerto Rico'),
		array('RI', 'Rhode Island'),
		array('SC', 'South Carolina'),
		array('SD', 'South Dakota'),
		array('TN', 'Tennessee'),
		array('TX', 'Texas'),
		array('UT', 'Utah'),
		array('VA', 'Virginia'),
		array('VT', 'Vermont'),
		array('WA', 'Washington'),
		array('WI', 'Wisconsin'),
		array('WV', 'West Virginia'),
		array('WY', 'Wyoming')
	);
	
	$options = '<option value=""></option>';
	
	foreach ($states as $state) {
		if ($type == 'abbrev') {
    	$options .= '<option value="'.$state[0].'" '. check_select($post, $state[0], false) .' >'.$state[0].'</option>'."\n";
    } elseif($type == 'name') {
    	$options .= '<option value="'.$state[1].'" '. check_select($post, $state[1], false) .' >'.$state[1].'</option>'."\n";
    } elseif($type == 'mixed') {
    	$options .= '<option value="'.$state[0].'" '. check_select($post, $state[0], false) .' >'.$state[1].'</option>'."\n";
    }
	}
		
	echo $options;
}

/**
 * Check Select Element 
 *
 * @param string $i, POST value
 * @param string $m, input element's value
 * @param string $e, return=false, echo=true 
 * @return string 
 */
function check_select($i,$m,$e=true) {
	if ($i != null) { 
		if ( $i == $m ) { 
			$var = ' selected="selected" '; 
		} else {
			$var = '';
		}
	} else {
		$var = '';	
	}
	if(!$e) {
		return $var;
	} else {
		echo $var;
	}
}
?>