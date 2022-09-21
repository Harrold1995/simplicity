<div class="br-outer">
    <header>
        <h2>Properties</h2>
        <ul id="br-properties" class="check-a b small-label">
        <?php foreach($properties as $property) { ?>
            <li>
                <label for="p<?php echo $property->id?>" class="custom-checkbox">
                    <input type="checkbox" id="p<?php echo $property->id?>" value="<?php echo $property->id?>" class="commonCheckbox">
                    <div class="input"></div>
                    <?php echo $property->name?>
                </label>
            </li>
        <?php } ?>
        </ul>
        <a href="#" class="br-propslide"><span><i class="fas fa-home"></i></span></a>
    </header>
    <div class="br-body">
        <div class="br-header">
            <header class="d-flex justify-content-between">
                <div class="form-search double d-flex">
                    <ul class="list-square">
                        <li class="a mr-2 mb-0" style="display:none;"><a href="#" id="addFavoritesButton"><i class="icon-plus"></i> <span>Add</span></a></li>
                    </ul>
                    <p>
                        <label for="fsa">Search</label>
                        <input type="text" id="psearch" name="fsa" class="wide" required>
                        <button type="submit">Submit</button>
                        <a href="./"><i class="icon-microphone"></i> <span>Record</span></a>
                    </p>
                </div>
                <div class="runbatchouter mr-2">
                    <a href="#" id="runbatch" style="display:none;">Run Batch</a>
                </div>
                <div class="runbatchouter mr-2">
                    <a href="#" id="addnew" >Create New Batch</a>
                </div>
                <div class="runbatchouter mr-2">
                    <a href="#" id="editbatch" style="display:none;">Edit Batch Settings</a>
                </div>
            </header>
        </div>
        <div class="br-container" id="br-main">
            <div id="batches-slick">
            </div>
        </div>
    </div>
    <footer>
        <h2>Filters</h2>
        <ul class="br-filters">
        </ul>
        <a href="#" class="br-filterslide"><span><i class="fas fa-filter"></i></span></a>
    </footer>
</div>
    <script>
        $(document).ready(function () {
            var breports = new BatchReports('.br-outer', {
                    base: '<?php echo base_url();?>',
                    body: '.br-container',
                    header: '.br-header'
                });
        });
    </script>