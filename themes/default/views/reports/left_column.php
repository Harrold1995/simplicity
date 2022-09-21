
<?php foreach ($columns as $column) { ?>

    <h4 class="toggle"><?php echo ucfirst($column['table']); ?></h4>
    <div>

    <ul class="checklist-a">
        <?php foreach ($column['data'] as $data) { ?>
            <li><label for="cl<?php echo $i; ?>"><input type="checkbox" id="cl<?php echo $i++; ?>" value="<?php echo $data->id; ?>" dtype="<?php echo $data->type; ?>" <?php echo (in_array($data->id, $visible) || $id == 0) ? 'checked' : ''; ?>><?php echo $data->name; ?></label>
            <?php /*if($data->type == 'date') {?>
                <div class="list-filters">
                <span class="select arrow-down">
                    <select class="datef">
                        <option value="0" <?php echo $filter->datef == 0 ? 'selected' : ''; ?>>-</option>
                        <option value="1" <?php echo $filter->datef == 1 ? 'selected' : ''; ?>>M</option>
                        <option value="2" <?php echo $filter->datef == 2 ? 'selected' : ''; ?>>Y</option>
                    </select>
                </span>
                </div>
            <?php } */?></li>
        <?php } ?>
        </ul>
    </div>

<?php } ?>
<h4 class="toggle">Custom Fields</h4>
<div>

    <ul class="checklist-a" id="cfields">
        <?php foreach ($cf as $data) { ?>
            <li><label for="cl<?php echo $data->id; ?>"><input type="checkbox" id="cl<?php echo $data->id; ?>" value="<?php echo $data->id; ?>" dtype="<?php echo $data->type; ?>" query="<?php echo $data->query; ?>" af="<?php echo $data->af; ?>" <?php echo (in_array($data->id, $visible) || $id == 0) ? 'checked' : ''; ?>><span><?php echo $data->name; ?></span></label>
                <a href="#" class="edit-field"><i class="fas fa-edit"></i></a>
                <a href="#" class="delete-field"><i class="fas fa-times-circle"></i></a></li>
        <?php } ?>
    </ul>
    <div style="text-align:center;font-size:14px;">
        <a accesskey="1" href="#" id="addFieldButton"><i class="icon-plus-circle" aria-hidden="true"></i> <span class="hidden">Add</span></a>
    </div>
</div>


