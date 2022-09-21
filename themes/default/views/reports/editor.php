<main id="content" class="cols-c a">
				<header class="flex-wrapper">
                    <div class="flex-header">
                        <form action="./" method="post" class="form-search">
                            <p>
                                <label for="fsa">Search</label>
                                <input type="text" id="fsa" name="fsa" required>
                                <button type="submit">Submit</button>
                                <a href="./"><i class="icon-microphone"></i> <span>Record</span></a>
                            </p>
                        </form>
                        <h3 class="text-center">Available columns</h3>
                    </div>
					<div class="accordion-a flex-body" id="report-columns">
					</div>	

				</header>
				<article>
					<form action="./" method="post" class="form-double">
						<p>
							<label for="fda">Report title</label>
							<input type="text" id="rname" name="rname" value="<?php echo $report->name;?>">
						</p>

						<p class="pull-right mb-0">
                            <span style="margin-right:70px;">
                                <label class="switch" for="slick-truncate">
                                    <input type="checkbox" value="1" class="no-js" <?php echo $settings->truncate == '1' ? 'checked' : '';?> id="slick-truncate">
                                    <span class="slider round"></span>
                                    <span class="option-text">Truncate</span>
                                </label>
                            </span>
                            <span style="">
                                <ul class="print-orientation pl-0">
                                    <li <?php echo $settings->printmode != '1' ? 'class="active"' : '';?>>
                                        <input type="radio" name="print-mode" id="portrait" value="0">
                                        <label for="portrait"><i class="fas fa-portrait"></i></label>
                                    </li>
                                    <li <?php echo $settings->printmode == '1' ? 'class="active"' : '';?>>
                                        <input type="radio" name="print-mode" id="landscape" value="1">
                                        <label for="landscape"><i class="fas fa-image"></i></label>
                                    </li>
                                </ul>
                            </span>
                            <span style="margin-right:70px;">
                                <label class="switch" for="slick-expanded">
                                    <input type="checkbox" value="1" class="no-js" checked id="slick-expanded">
                                    <span class="slider round"></span>
                                    <span class="option-text">Expanded</span>
                                </label>
                            </span>
							<label for="record_types" class="hidden">Choose Record Set</label>
							<select id="record_types">

								<option selected disabled>Choose Record Set</option>

								<?php foreach($types as $type){
									echo '<option value="' . $type->id . '" type="' . $type->modal_type . '">' . $type->name . '</option>';
									}
								?>
							</select>
                            <span>
                            <a accesskey="1" href="#" data-mode='add' data-type='custom' url='reports/getRecordSetModal'><i class="icon-plus-circle" aria-hidden="true"></i> <span class="hidden">Add</span></a>
                            </span>
						</p>
					</form>	
					<!--<table class="table-c e noscript" id="reports-table" style="width:100%">
					
					</table>-->
					<div id="reports-table"></div>
                    <div id="reports-footer">
                        <h2 class="overlay-a semi size-h m8 text-center w415">Grouping and sorting</h2>
                        <div>
                            <form action="./" method="post" class="form-tree">
                            <div id="reports-bottom">
                                <?php require_once VIEWPATH . 'reports/bottom.php'; ?>
                            </div>

                                <ul class="list-inline">
                                    <li><a href="#" id="add-group">Add Group</a></li>
                                    <li><a href="#" id="add-sort">Add sort</a></li>
                                    <li><a href="#" id="saveButton">Save Report</a></li>
                                </ul>
                            </form>
                        </div>
                    </div>
				</article>
				<footer>
					<div class="tabs-a flex-wrapper" id="reports-filters">
						<ul class="nav nav-tabs flex-header">
							<li class="active"><a href="#background-filters" data-toggle="tab" class="active">Background Filters</a></li>
							<li><a href="#user-filters" data-toggle="tab">User Filters</a></li>
                            <li><a href="#custom-parameters" data-toggle="tab">Custom</a></li>
						</ul>
						<div class="flex-body">
							<?php echo $right;?>
						</div>
					</div>
				</footer>
<script>
    $(document).ready(function () {
        var reports = new Reports('#report-columns', '#reports-table', '#reports-bottom', "#reports-filters",
            {
				base: '<?php echo base_url();?>',
				leftColumnURL: 'reports/getLeftColumn',
				recordSelect: '#record_types',
                customSelect: '#custom_types',
				ajaxTableURL: 'reports/getAjaxTable',
                settingsURL: 'reports/getSettings',
                saveURL: 'reports/save/<?php echo $id;?>',
				ajaxTableHeaderURL: 'reports/getAjaxTableHeader',
				groupingLineTemplateURL:'reports/getGroupingLine',
                sortingLineTemplateURL:'reports/getSortingLine',
                bottomURL:'reports/getBottom',
                filterLineTemplateURL:'reports/getFiltersLine',
			});
			
			reports.load(<?php echo (isset($settings->type) ? $settings->type : '0').", ".$id;?>);
    });
</script>




