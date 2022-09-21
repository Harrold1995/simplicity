
                <table class="table-c b">
					<thead>
						<tr>
                            <th></th>
							<th>Unit</th>
							<th>Floor</th>
							<th>Type</th>
							<th class="text-center">SQ&nbsp;FT</th>
							<th class="text-center">Description</th>
							<th class="text-center">Market Rent</th>
							<th class="text-center">Status</th>
                          <th class="text-center link-icon"><a href="#"><i class="icon-plus-circle addUnitButton table-button"></i> <span>Add</span></a></th>
							<!--th class="text-center link-icon"><a href="#"><i class="icon-plus-circle "></i> <span>Add</span></a></th-->
						</tr>
					</thead>
					<tbody>
                    <?php  if (isset($units)){
                            foreach ($units as $unit) {?>
                        
                                    <tr>
                                    <td><i class="icon-door"></i></td>
                                        <td><?=isset($unit->name) ? $unit->name : '';?></td>
                                        <td><?=isset($unit->floor) ? $unit->floor : '';?></td>
                                        <td>3&nbsp;Bed</td>
                                        <td><?=isset($unit->sq_ft) ? $unit->sq_ft : '';?></td>
                                        <td><?=isset($unit->market_rent) ? $unit->market_rent : '';?></td>
                                        <td><?=isset($unit->memo) ? $unit->memo : '';?></td>
                                        <td><?=isset($unit->status) ? $unit->status : '';?></td>
                                        <td class="text-center link-icon"><a href="./"><i class="icon-x"></i> <span>Remove</span></a></td>
                                    </tr>       
                            <?php }} ?>
					</tbody>
				</table>
                <footer>
					<ul class="list-btn">
                    <?=isset($property) ? '<a href="properties/deleteProperty/'.$property->id.'" class="deleteButton mr-auto"><i class="fas fa-trash-alt"></i> Delete item</a>' : '' ?>
						<li><button type="submit">Save &amp; New</button></li>
						<li><a href="./">Duplicate</a></li>
						<li><button type="reset">Cancel</button></li>
						<li><a href="./">Save &amp; Close</a></li>
					</ul>
					<ul>
						<li>Last Modified 12:22:31 pm 1/10/2018</li>
						<li>Last Modified by <a href="./">User</a></li>
					</ul>
				</footer>
            <!--/form>
        </div>
    </div>
</div-->