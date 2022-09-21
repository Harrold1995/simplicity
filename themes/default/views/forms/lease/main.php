<?php if(isset($lease) && isset($lease->start)) $leaseStart = $lease->start;?> <!--used for notes-->
<div class="modal flexmodal fade property-modal <?php echo $edit ?>" id="propertyModal" doc-type="leases" tabindex="-1" role="dialog" main-id=<?= isset($lease) && isset($lease->id) ? $lease->id : '-1' ?> type="lease" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="lease">
                  
				  	
                       <header class="modal-h ui-draggable-handle">
                            <h2 class="text-uppercase"><?php echo $title; ?></h2>

							<nav>
								<ul>
									<li><span class="buttons" style=""><span class="min" style=";padding: 8px 20px;cursor: pointer;">_</span></span> </li>
									<li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
									<li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
								</ul>
							</nav>
                            <nav>
                                <ul>
								<!-- <li><a href=""><i class="icon-chevron-left"></i> <span>Previous</span></a></li> -->
								<li><a href="#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
								<!-- <li><a href="./"><i class="icon-chevron-right"></i> <span>Next</span></a></li> -->
								<li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
                                <li><?= isset($lease) ? '<a href="leases/deleteLease/'.$lease->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
                                <li class="get_send_email_form"><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>

                                <li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
                                <li class="getDocuments"><a href="#" class="uploadDocument"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
                                <!-- <li><a class="print" href="./"><i class="icon-print"></i> <span>Print</span></a></li> -->
                                <?php if (isset($lease) && isset($lease->id))
                                echo '<li><a href="leases/printPdf/' . $lease->id . '" target="_blank"><i class="icon-print" aria-hidden="true"></i></a></li>';
                                ?>
                                </ul>
                            </nav>
                    </header>
					
                    <section class="b" style="z-index: 38;">
					<input type="hidden" id="lease_id" name="lease_id" value="<?= isset($lease) && isset($lease->id) ? $lease->id : 0 ?>">
					<div class="double f m20">
						<div>
							<p>
								<label for="property_id">Property</label>
								<span class="select">
                                <select onchange="JS.loadList('api/getUnitsList',$(this).closest('.select').find('input[type=hidden]').val(), '#unit_id', $(this).closest('.modal'))" class="form-control editable-select" id="property_id" name="property">
                                        <?php
                                        echo '<option value="0"></option>';
                                        foreach ($properties as $property) {
                                            echo '<option value="' . $property->id . '" ' . (isset($lease) && $lease->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
                                        } ?>
                                        </select>
                                </span>
							</p>
							<p>
								<label for="unit_id">Unit</label>
								<span class="select">
                                <select class="form-control editable-select" id="unit_id" name="lease[unit_id]" <?php echo $lease->ttls ? ' disabled' : '' ?>>
                                            <option value="0">None</option>
                                            <?php
											// if(isset($lease) && $lease->ttls == $lease->ttls ? ' disabled' : '')
                                            foreach ($units as $unit) {
                                                echo '<option value="' . $unit->id . '" ' . (isset($lease) && $lease->unit_id == $unit->id ? 'selected' : '') . '>' . $unit->name . '</option>';
                                            } ?>
                                        </select>
									</span>
								<!-- <input type="text" id="ffu" name="ffu" value="1-B"> -->
							</p>
							<p>
								<label for="late_charge">Late Charge<!-- Setup --><a href="#" class="lc-edit"><i class="fas fa-pen-square"></i></a></label>
                                <select id="late_charge" name="lease[late_charge_setup_id]" class="form-control editable-select set-up" key="latecharge.name" modal="custom|leases/getLateChargeModal" value = "<?=isset($lease) ? $lease->late_charge_setup_id: ''; ?>">
                                                <option value="0">None</option>
                                                <?php
                                                foreach ($lcsetups as $setup) {
                                                    echo '<option value="' . $setup->id . '" ' . (isset($lease) && $lease->late_charge_setup_id == $setup->id ? 'selected' : '') . '>' . $setup->name . '</option>';
                                                } ?>
                                            </select>
                                    </select>


							</p>
							<p>
								<label for="broker">Broker</label>

                                <input type="text" value="<?= isset($lease) && isset($lease->broker) ? $lease->broker : '' ?>" name="lease[broker]" id="broker"></span>

							</p>
							<p>
								<label for="lease_template">Lease Template</label>
								<span class="select">
                                <select class="editable-select" id="unit_id" name="lease[lease_template]">
										<?php
										foreach ($lease_templates as $lease_template) {
											echo '<option value="' . $lease_template->id . '" ' . (isset($lease) && $lease->lease_template == $lease_template->id ? 'selected' : '') . '>' . $lease_template->name . '</option>';
										} ?>
									</select>
								</span>
                            </p>
						</div>
						<div>
							<p>
								<label for="start">Start Date</label>
								    <input  data-toggle="datepicker" value="<?= isset($lease) && isset($lease->start) ? $lease->start :  date('Y-m-d') ?>" name="lease[start]" id="start" class = "leaveEmpty">
							</p>
							<p>
								<label for="end">End Date</label>
								<?php $newEnd = date('Y-m-d', strtotime('+1 year - 1 day'));?>
								    <input   data-toggle="datepicker" value="<?= isset($lease) && isset($lease->end) ? $lease->end : '' ?>" name="lease[end]" id="end" class = "leaveEmpty">
							</p>
							<p>
								<label for="amount">Rent Amount: <span class="prefix">$</span></label>
                                    <input type="text" value="<?= isset($lease) && isset($lease->amount) ? number_format($lease->amount, 2) : '' ?>" class="form-control decimal" name="lease[amount]" id="amount">
							</p>
							<p>
								<label for="deposit">Security Deposit <span class="prefix">$</span></label>
								<input type="text" value="<?= isset($lease) && isset($lease->deposit) ? number_format($lease->deposit, 2) : '' ?>" class="form-control decimal" name="lease[deposit]" id="deposit">
							</p>
							<p>
								<label for="last_month">Last month's rent <span class="prefix">$</span></label>
								    <input type="text" value="<?= isset($lease) && isset($lease->last_month) ? number_format($lease->last_month, 2) : '' ?>" class="form-control decimal" name="lease[last_month]" id="last_month">
							</p>
						</div>
					</div>
					<div class="triple" style="margin-right: 2%;">
						<p>
							<label for="move_in">Move in Date:</label>
							 <input  data-toggle="datepicker" value="<?= isset($lease) && isset($lease->move_in) ? $lease->move_in :  date('Y-m-d') ?>" name="lease[move_in]" id="move_in">
						</p>
						<p>
							<label for="move_out">Move Out Date</label>
							<!-- < ?php $newMoveout = isset($lease) ? '' : date('Y-m-d', strtotime('+1 year - 1 day'));?> -->
							<?php $emptyDateCheck =  isset($lease) && $lease->move_out != "" ? '' : "leaveEmpty" ?>
							 <input data-toggle="datepicker" value="<?= isset($lease) && isset($lease->move_out) ? $lease->move_out :  '' ?>" name="lease[move_out]" id="move_out" class="<?= $emptyDateCheck ?>">
						</p>
						<p>
							<label for="holdover">Holdover Rent Amount: <span class="prefix">$</span></label>
							    <input type="text" value="<?= isset($lease) && isset($lease->holdover) ? number_format($lease->holdover, 2) : '' ?>" class="form-control" name="lease[holdover]" id="holdover">
						</p>
					</div>
					<p>
						<label for="memo">Memo:</label>
						    <textarea onkeyup="JS.textAreaAdjust(this)" class="form-control" id="memo" name="lease[memo]" rows="1"><?= isset($lease) && isset($lease->memo) ? $lease->memo : '' ?></textarea>
					</p>
					<div class="submit">
					<?php if(isset($lease) && ($lease->original != "")){ ?>
						<div style="border: 1px solid black;">
							<a href="<?php echo base_url() . 'uploads/documents/'. $lease->original  ?>" target="_blank"><?=isset($lease->original) ? $lease->original : '';?>
							</a>
							<p class="input-file">
								<label for="p-image"><input type="file" name="original"  id="p-image" targetimg="#original-lease"> <span>Edit</span></label>
							</p>
						</div>
					<?php }else{ ?>
						<p class="input-file">
                        <!-- <p class='file-name'>< ?= isset($lease) && $lease->original != '' ? '<a href="' . base_url() . 'uploads/documents/' . $lease->original . '" target="_blank">' . $lease->original . '</a>' : '' ?>
							<p></p> -->
                            <label for="p-image"><input type="file" name="original"  id="p-image" targetimg="#original-lease"> <span>Attach Original</span></label>
						</p>
					<?php }?>

						<ul class="check-a a">
							<li><label for="pets" class="checkbox <?= isset($lease) && ($lease->pets == 1) ? 'active' : '' ?>"><input type="hidden" name="lease[pets]" value="0" /><input type="checkbox" value="1" <?= isset($lease) && ($lease->pets == 1) ? 'checked' : '' ?> id="pets" name="lease[pets]" class="hidden" aria-hidden="true"><div class="input"></div> Pets?</label></li>
							<li><label for="restrict_payments" class="checkbox <?= isset($lease) && ($lease->restrict_payments == 1) ? 'active' : '' ?>"><input type="hidden" name="lease[restrict_payments]" value="0" /><input type="checkbox"  value="1" <?= isset($lease) && ($lease->restrict_payments == 1) ? 'checked' : '' ?>  id="restrict_payments" name="lease[restrict_payments]" class="hidden" aria-hidden="true"><div class="input"></div> Restict Payments?</label></li>
							<li><label for="active" class="checkbox  <?= isset($lease) && ($lease->active == '0') ? '' : 'active' ?>"><input type="hidden" name="lease[active]" value="0" /><input type="checkbox"  value="1" <?= isset($lease)  && ($lease->active == '0') ? '' : 'checked' ?> id="active" name="lease[active]"  class="hidden" aria-hidden="true"><div class="input"></div>Active?</label></li>
							<li><label for="stabilized" class="checkbox <?= isset($lease) && ($lease->stabilized == 1) ? 'active' : '' ?>"><input type="hidden" name="lease[stabilized]" value="0" /><input type="checkbox" value="1" <?= isset($lease) && ($lease->stabilized == 1) ? 'checked' : '' ?> id="stabilized" name="lease[stabilized]"  class="hidden" aria-hidden="true"><div class="input" id ="rent_stabilized_button"></div> Rent Stablized?</label></li>
							<li><label for="section_8" class="checkbox <?= isset($lease) && ($lease->section_8 == 1) ? 'active' : '' ?>"><input type="hidden" name="lease[section_8]" value="0" /><input type="checkbox" value="1" <?= isset($lease) && ($lease->section_8 == 1) ? 'checked' : '' ?> id="section_8" name="lease[section_8]"  class="hidden" aria-hidden="true"><div class="input" id="section_8_button"></div> Section 8</label></li>
							<li><label for="in_court" class="checkbox <?= isset($lease) && ($lease->in_court == 1) ? 'active' : '' ?>"><input type="hidden" name="lease[in_court]" value="0" /><input type="checkbox" value="1" <?= isset($lease) && ($lease->in_court == 1) ? 'checked' : '' ?> id="in_court" name="lease[in_court]"  class="hidden" aria-hidden="true"><div class="input" id="in_court_button"></div> In Court</label></li>
							<li><label for="bill_collectively" class="checkbox <?= isset($lease) && ($lease->bill_collectively == 0) ? '' : 'active' ?>"><input type="hidden" name="lease[bill_collectively]" value="0" /><input type="checkbox" value="1" <?= isset($lease) && ($lease->bill_collectively == 0) ? '' : 'checked' ?> id="bill_collectively" name="lease[bill_collectively]"  class="hidden" aria-hidden="true"><div class="input"></div>Bill collectively</label></li>
							<li><label for="commercial" class="checkbox <?= isset($lease) && ($lease->commercial == 0) ? '' : 'active' ?>"><input type="hidden" name="lease[commercial]" value="0" /><input type="checkbox" value="1" <?= isset($lease) && ($lease->commercial == 0) ? '' : 'checked' ?> id="commercial" name="lease[commercial]"  class="hidden" aria-hidden="true"><div class="input" id="commercial_button"></div>commercial</label></li>
						</ul>
					</div>
				</section>

        <div class="tabFunction">
			<nav class="double center">
				<ul class="list-horizontal nav" id="property-tabs3" role="tablist">
				<li class = "tablinks active" id="defaultOpen"><a href="#people-tab" onclick="tabswitch(event, 'people',$(this))">People</a></li>
				<!--<li class = "tablinks" > <a href="#taxes-tab" onclick="tabswitch(event, 'utilities',$(this))">Utilities</a></li>-->
				<li class = "tablinks" > <a href="#settings-tab" onclick="tabswitch(event, 'renewal',$(this))">Renewal</a></li>
				<li class = "tablinks"> <a  href="#notes-tab" onclick="tabswitch(event, 'autoCharges',$(this))">Auto Charges</a></li>
				<li class = "tablinks"> <a  href="#maintenance-tab" onclick="tabswitch(event, 'maintenance',$(this))">Maintenance</a></li>
				<li style="<?= isset($lease) && ($lease->stabilized == 1) ? '' : 'display:none' ?>" class = "tablinks" id="rent_stabilized_tab"> <a  href="#stabilized-tab" onclick="tabswitch(event, 'stabilized',$(this))">Rent Stabilized</a></li>
				<li style="<?= isset($lease) && ($lease->commercial == 1) ? '' : 'display:none' ?>" class = "tablinks" id="commercial_tab"> <a  href="#commercial-tab" onclick="tabswitch(event, 'commercial',$(this))">Commercial</a></li>
				<li style="<?= isset($lease) && ($lease->section_8 == 1) ? '' : 'display:none' ?>" class = "tablinks" id="section_8_tab"> <a  href="#notes-tab" onclick="tabswitch(event, 'sect_8',$(this))">Section 8</a></li>
				<li style="<?= isset($lease) && ($lease->in_court == 1) ? '' : 'display:none' ?>" class = "tablinks" id="in_court_tab"> <a  href="#in_court-tab" onclick="tabswitch(event, 'in_court',$(this))">In Court</a></li>
				</ul>
			</nav>



            <!-- Tab content -->

            <div id="people" class="active tabcontent defaultOpenTab" data-id = '13'>
            <?php require_once VIEWPATH . 'forms/lease/people.php';?>
            </div>

           <div id="sect_8" class="tabcontent" style="display:none">
            <?php require_once VIEWPATH . 'forms/lease/sect_8.php';?>
            </div>

            <div id="utilities" class="tabcontent" style="display:none" data-id = '9'>
            <?php require_once VIEWPATH . 'forms/lease/utilities.php';?>
            </div>

			<div id="stabilized" class="tabcontent" style="display:none">
            <?php require_once VIEWPATH . 'forms/lease/rent_stabilized.php';?>
            </div>

			<div id="in_court" class="tabcontent" style="display:none">
            <?php require_once VIEWPATH . 'forms/lease/in_court.php';?>
            </div>

            <div id="renewal" class="tabcontent" style="display:none">
            <?php require_once VIEWPATH . 'forms/lease/renewal.php';?>
			</div>

			<div id="autoCharges" class="tabcontent" style="display:none"  data-id = '19'>
            <?php require_once VIEWPATH . 'forms/lease/autoCharges.php';?>
            </div>

			<div id="commercial" class="tabcontent" style="display:none"  data-id = '20'>
            <?php require_once VIEWPATH . 'forms/lease/commercial.php';?>
            </div>

			<div id="maintenance" class="tabcontent" style="display:none"  >
            <?php require_once VIEWPATH . 'forms/lease/maintenance.php';?>
            </div>
     </div>
<!-- <style type="text/css" onload="getDate($(this).closest('.modal'))"></style> -->
                <footer>
					<ul class="list-btn">
						<li><button type="submit" after="mnew">Save &amp; New</button></li>
						<li><button type="submit"  after="mclose">Save &amp; Close</button></li>
						<li><button type="submit" after="duplicate">Duplicate</button></li>
						<li><button type="button">Cancel</button></li>
					</ul>
					<ul>
						<li>Last Modified 12:22:31 pm 1/10/2018</li>
						<li>Last Modified by User</li>
					</ul>
				</footer>				
				<style type="text/css" onload="triggerBillCollectively($(this).closest('.modal'))"></style>
            </form>
        </div>
	</div>
    </div>
</div>

<script>
		function triggerBillCollectively(modal){
			$(modal).one('click', '#section_8_button', function () {
					$(this).closest('ul').find( "#bill_collectively" ).trigger('click');
				});
		}
</script>
