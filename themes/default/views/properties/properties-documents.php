
					 <div id="DataTables_Table_4_wrapper" class="dataTables_wrapper no-footer has-table-c mobile-hide b text-center">
                <div class="dataTables_scroll">
                  <div class="dataTables_scrollHead">
                    <div class="dataTables_scrollHeadInner">
                      <table class="table-c b text-center mobile-hide dataTable no-footer" role="grid">
                        <thead>
                          <tr role="row">
							<?php	if (isset($documentsInfo)){ ?>
								<th width="7%" class="text-center">Title</th>
							<?php	foreach ($documentsInfo as $k => $value) {?>
								<th width="7%" class="text-center"><?=isset($k) ? $k : '';?></th>
							<?php }} ?>
							<th width="7%" class="text-center"></th>
                          </tr>
                      </thead>
                    </table>
                  </div>
                </div>
              <div class="dataTables_scrollBody">
              <div class="table-wrapper"  tabindex="-1">
              <table class="table-b  text-center mobile-hide dataTable no-footer" id="DataTables_Table_4" role="grid" aria-describedby="DataTables_Table_4_info">
              <thead>
              </thead>
            
            <tbody>	
			<?php if (isset($documents)){ 
				foreach ($documents as $document) { ?>
				<tr>
					<td width='7%' class='text-center'>
						<a href="<?php echo base_url() . 'uploads/documents/'. $document->name  ?>" target="_blank"><?=isset($document->name) ? $document->name : '';?>
						</a>
					</td>
						<?php	if (isset($documentsInfo)){ ?>
							<?php foreach ($documentsInfo as $k => $value) {?>								
									<td width='7%' class='text-center'><?=isset($value) ? $value : '';?></td>								       
						<?php } } ?>
						<td width='7%' class='text-center'>
							<ul class="list-square">
								<li><a href="./"><i class="icon-envelope-outline2"></i> Messages</a></li>
								<li><a href="./" class="print"><i class="icon-print"></i> <span>Print</span></a></li>
							</ul>
						</td>
				</tr>			
			<?php } } ?>						
            </tbody>
				  </table>
        </div>
      </div>
    </div>
</div>

					
