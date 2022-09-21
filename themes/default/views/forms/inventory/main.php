
<div class="modal fade inventory-modal" id="inventoryModal" tabindex="-1" role="dialog" doc-type="items" main-id=<?= isset($inventory) && isset($inventory->id) ? $inventory->id : '-1' ?> type="inventory" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
                  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="inventory">
                            
                  <header class="modal-h">
					<h2 class="text-uppercase">Inventory-Charge</h2>
					<nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>		
					<nav>
						<ul>
                            <li><a href="#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
                            <li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>				
                            <li><?= isset($inventory) ? '<a href="inventory/deleteInventory/'.$inventory->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
							<li class="get_send_email_form"><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
							<li><a href="./"><i class="icon-brain"></i> <span>Brain</span></a></li>
							<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
							<li class="getDocuments"><a href="#" class="uploadDocument"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
							<li><a class="print" href="./"><i class="icon-print"></i> <span>Print</span></a></li>
						</ul>
					</nav>
				</header>

			<section class="plain">
					<div class="box">
						<div class="triple c">
							<p>
								<label for="item_name">Item Name</label>
								<?php if (isset($inventory) && isset($inventory->id)) echo '<input type="hidden" name="inventory[id]" value="' . $inventory->id . '"/>'; ?>
								<input class="form-control" type="text" value="<?= isset($inventory) && isset($inventory->item_name) ? $inventory->item_name : '' ?>" name="inventory[item_name]" id="name">
							</p>
							<p>
								<label for="type">Item Type</label>								
                                    <!-- <input class="form-control" type="text" value="< ?= isset($inventory) && isset($inventory->type) ? $inventory->type : '' ?>" name="inventory[type]" id="type">                            -->
									<select id="type"  name="inventory[type]"
									onchange="getSubItems($(this).val(), $(this).closest('#inventoryModal').find('#parent_item'));">
									<option value="0"></option>
										<?php foreach ($item_types as $k => $itype){
											echo '<option value="' . $k . '" ' . (isset($inventory) && $inventory->type == $k ? 'selected' : '') . '>' . $itype . '</option>';
										}?></select>
							</p>
							<p>
								<label for="parent_id">Subitem of:</label>
									<select id="parent_item"  name="inventory[parent_id]">
									<option value="0"></option>
										<?php foreach ($parents as $parent){
											echo '<option value="' . $parent->id . '" ' . (isset($inventory) && $inventory->parent_id == $parent->id ? 'selected' : '') . '>' . $parent->item_name . '</option>';
										}?></select>
							</p>
						</div>
					</div>
					<div class="triple d m10">
						<div>
							<h3 class="text-center size-g overlay-a text-uppercase m13">Sales information</h3>
							<ul class="list-input">
								<li>
									<label for="acct_income">Income Account</label>
									<select class=" editable-select quick-add set-up"  name="inventory[acct_income]" id="acct_income">
                                            <option value="-1" selected ></option>
									<?php
										echo '<option class="nested0" value="0"></option>'; 
										if (isset($subaccounts))
											foreach ($subaccounts as $saccount) {
												echo '<option data-id="'.$saccount->id.'" data-parent-id="'.$saccount->parent_id.'" class="nested'.$saccount->step.'"value="' . $saccount->id . '"  ' . (isset($inventory) && $inventory->acct_income == $saccount->id ? 'selected' : '') . '>' . $saccount->name . '</option>';
										} ?>
									</select>
								</li>
								<li>
									<label for="sales_description">Desc. for sales:</label>
									<input class="form-control" type="text" value="<?= isset($inventory) && isset($inventory->sales_description) ? $inventory->sales_description : '' ?>" name="inventory[sales_description]" id="description">
								</li>
								<li>
									<label for="sale_price">Sale Price: <span class="prefix">$</span></label>
									<input class="form-control" type="text" value="<?= isset($inventory) && isset($inventory->sale_price) ? $inventory->sale_price : '' ?>" name="inventory[sale_price]" id="sale_price">
								</li>
								<li>
									<label for="tax_code">Tax Code</label>
                                    <input class="form-control" type="text" value="<?= isset($inventory) && isset($inventory->tax_code) ? $inventory->tax_code : '' ?>" name="inventory[tax_code]" id="tax_code">
								</li>
							</ul>
						</div>
						<div>
							<h3 class="text-center size-g overlay-a text-uppercase m13">Purchase information</h3>
							<ul class="list-input">
								<li>
									<label for="part_num">Manufacturer part #</label>
									<input class="form-control" type="text" value="<?= isset($inventory) && isset($inventory->part_num) ? $inventory->part_num : '' ?>" name="inventory[part_num]" id="part_num">
								</li>
								<li>
									<label for="purchase_desc">Desc on Purchases:</label>
									<input class="form-control" type="text" value="<?= isset($inventory) && isset($inventory->purchase_desc) ? $inventory->purchase_desc : '' ?>" name="inventory[purchase_desc]" id="purchase_desc">
								</li>
								<li>
									<label for="purchase_price">Cost: <span class="prefix">$</span></label>
									<input class="form-control" type="text" value="<?= isset($inventory) && isset($inventory->purchase_price) ? $inventory->purchase_price : '' ?>" name="inventory[purchase_price]" id="purchase_price">
								</li>
								<li>
									<label for="acct_asset">Asset Account:</label>
									<select class=" editable-select quick-add set-up"  name="inventory[acct_asset]" id="acct_asset">
                                            <option value="-1" selected ></option>
									<?php
										echo '<option class="nested0" value="0"></option>'; 
										if (isset($subaccounts))
											foreach ($subaccounts as $saccount) {
												echo '<option data-id="'.$saccount->id.'" data-parent-id="'.$saccount->parent_id.'" class="nested'.$saccount->step.'"value="' . $saccount->id . '"  ' . (isset($inventory) && $inventory->acct_asset == $saccount->id ? 'selected' : '') . '>' . $saccount->name . '</option>';
										} ?>
									</select>
									</li>
							</ul>
						</div>
						
					</div>
					<p class="w910 m35">
						<label for="memo">Memo:</label>
						<input class="form-control" type="text" value="<?= isset($inventory) && isset($inventory->memo) ? $inventory->memo : '' ?>" name="inventory[memo]" id="memo">
					</p>
					<div style="margin-top:80px;" class="submit">
																			
							<p class="input-file">
								<label style="width:140px;" for="p-image"><input class = "upload" type="file"  id="p-image" targetimg="#inventory-image" name="image"> <span class="img"><img id="inventory-image" src="<?= isset($inventory) && $inventory->img_url != '' ? base_url() . "uploads/images/" . $inventory->img_url : 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_162b2febc6d%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_162b2febc6d%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.5%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' ?>" alt="Placeholder" width="200" height="200"></span> <span>Attach Photo</span></label>
							</p>
								<ul class="check-a a">
										<!-- <li><label for="active" class=" checkbox < ?= isset($inventory) && ($inventory->active == 1) ? 'active' : '' ?>"><input type="checkbox"  id="active" name="active"  class="hidden" aria-hidden="true"><div class="input"></div> Active?</label></li> -->
								<li style="float:left"><label for="active" class="checkbox <?= isset($inventory) && ($inventory->active == 1) ? 'active' : '' ?>"><input type="hidden" name="inventory[active]" value="0" /><input type="checkbox" value="1" <?= isset($inventory) && ($inventory->active == 1) ? 'checked' : '' ?> id="active"  name="inventory[active]"  class="hidden" aria-hidden="true"><div class="input" ></div> Active?</label></li>
								</ul>
						</div>
				</section>


         
     
        <footer>
          <ul class="list-btn">
            <li><button type="submit" after="mnew">Save &amp; New</button></li>
            <li><button type="submit" after="mclose">Save &amp; Close</button></li>
            <li><button type="submit" after="duplicate">Duplicate</button></li>
            <li><button type="button">Cancel</button></li>
            
          </ul>
          <ul>
            <li>Last Modified 12:22:31 pm 1/10/2018</li>
            <li>Last Modified by User</li>
          </ul>
        </footer>
      </form>
                                      

                              
                    </div>
		</div>

</div>
</div>
<script>
	console.log("here");
	var parents = <?php echo $jParents; ?>;
	//show subitem of only for the same type
	function getSubItems(value, td){
		var itemParent = td.val();
			$(td).empty();
			var subItemDropdown = "";
			subItemDropdown +="<option value='0'>None</option>";
			if(parents){
				for (var j = 0; j < parents.length; j++) {
				if(parents[j].type == value){
					subItemDropdown += `<option value='` + parents[j].id + `'`;
					if(parents[j].id == itemParent){ subItemDropdown += `selected`;}
					subItemDropdown += `>` + parents[j].item_name + `</option>`;
				} 	
			}
			}
			subItemDropdown += "</select>";
			$(td).append(subItemDropdown);
		console.log(itemParent);
	}
</script>







                <!--<section class="a" style="padding-right:50px;">
					<div class="double d m20">
						<div style="margin-left: 30px;">
							<p>
								<label for="property_id">Property</label>
                                <span class="select">
								<select id="property_id" name="transactions[h][property_id]">
								 <option value="-1" selected >Select Property</option>
								< ?php
                            foreach ($properties as $property) {
                                // echo '<option value="-1" selected >' . "Select Apples" . '</option>';
                                echo '<option value="' . $property->id . '" ' . (isset($headerTransaction) && $headerTransaction->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
                            } ?>
                                
                                </select>
                                </span>
							</p>
							<p>  
                                <label for="profile_id">Payee</label>
                                <span class="select">
								<select id="profile_id" name="transactions[h][profile_id]">
								<option value="-1" selected >Select Vendor</option>
                                < ?php foreach($names as $name): 
									echo '<option value="' . $name->id . '" ' . (isset($headerTransaction) && $headerTransaction->profile_id == $name->id ? 'selected' : '') . '>' . $name->vendor . '</option>';
								  endforeach; ?>
                                </select>
								</span>
							</p>
                            <p> 123 Main St.</p>
                            <p>Brooklyn< NY 11211</p>
							
							
							
						</div>
						<div style="margin-left: 15px;">
							<p>
								<label for="transaction_ref">Reference</label>
								<input type="text" value="< ?= isset($header) && isset($header->transaction_ref) ? $header->transaction_ref : '' ?>" id="transaction_ref" name="header[transaction_ref]" placeholder="Enter Ref#">
							</p>
							<p>
								<label for="transaction_date">Date</label>
								<input value="< ?= isset($header) && isset($header->transaction_date) ? $header->transaction_date : '' ?>" id="billDate" name="header[transaction_date]" value="<?php //echo date("Y-m-d"); ?>">
							</p>
							<p>
								<label for="credit">Amount <span class="prefix">$</span></label>
								<input type="number" value="< ?= isset($headerTransaction) && isset($headerTransaction->credit) ? $headerTransaction->credit : '' ?>" id="credit" name="transactions[h][credit]" placeholder="Enter Amount">
							</p>
							<p>
								<label for="date">Class</label>
                                <span class="select">
                                    <select id="bank" name="bank">
                                    <option value="-1" selected >Sales</option>
                                    <option value="-1" selected >Buying</option>
                                    <option value="-1" selected >Other</option>
                                    </select>
                                </span>
                            </p>
						</div>
					</div>	
					<p>
						<label for="memo">Memo:</label>
						<input type="memo" value="< ?= isset($header) && isset($header->memo) ? $header->memo : '' ?>" id="memo" name="header[memo]" placeholder="Enter Memo">
					</p>
                 
				</section>
-->
