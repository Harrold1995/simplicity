<div class="br-outer edit">
    <header>
        <h2>Batch Settings</h2>
        <div class="form-entry mb-4">
            <label for="name">Batch name:</label>
            <input name="name" id="batchname" value="<?php echo $batch->name;?>">
        </div>
        <h2>Batch Filters</h2>
        <div class="bre-fields">
        </div>
        <div class="text-center">
            <a href="#" class="bre-addfield"><i class="fas fa-plus-circle"></i></a>
        </div>
        <a href="#" class="br-propslide"><span><i class="fas fa-stream"></i></span></a>
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
                    <a href="#" id="savebatch">Save Batch</a>
                </div>
            </header>
        </div>
        <div class="br-container edit" id="br-main">
            <div class="reports-slick-top" id="batches-slick">
            </div>
            <div class="reports-slick-bottom" id="batches-slick">
            </div>
        </div>
    </div>
    <footer>
        <h2>Report Settings</h2>
        <div class="br-filters">
        </div>
        <a href="#" class="br-filterslide"><span><i class="fas fa-filter"></i></span></a>
    </footer>
</div>
<div class="bre-field-wrapper bre-template" fieldid="{index}">
    <div class="form-entry bre-fe">
        <label for="fname{index}">Name:</label>
        <input type="text" id="fname{index}">
    </div>
    <div class="form-entry bre-fe">
        <label for="ftype{index}">Type:</label>
        <select id="ftype{index}">
            <option value="text">Text</option>
            <option value="num">Num</option>
            <option value="date">Date</option>
        </select>
    </div>
    <div class="form-entry bre-fe">
        <label for="fcond{index}">Condition:</label>
        <select id="fcond{index}">
            <option value="0">Equals</option>
            <option value="1">Not Equals</option>
            <option value="2">Like</option>
            <option value="3">Between</option>
            <option value="4">&gt;</option>
            <option value="5">&lt;</option>
            <option value="6">In</option>
        </select>
    </div>
    <div class="form-entry bre-fe">
        <label for="fsource{index}">Source:</label>
        <input type="text" id="ftype{index}">
    </div>
    <a href="#" class="bre-delete"><i class="fas fa-times"></i></a>
</div>

    <script>
        $(document).ready(function () {
            var breports = new BatchReportsEditor('.br-outer', {
                    base: '<?php echo base_url();?>',
                    body: '.br-container',
                    header: '.br-header',
                    id: <?php echo $id ?>
                });
        });
    </script>