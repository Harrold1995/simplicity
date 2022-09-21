<div class="mb-4 form-entry">
    <label>Display name:</label>
    <input type="text" id="name" value="<?php echo $settings->name;?>">
</div>
<div class="row mb-1">
    <div class="col">
                <span class="ml-2">
                <label class="switch float-none" style="line-height:18px;" for="expanded">
                    <input type="checkbox" value="1" class="no-js" id="expanded" <?php echo $settings->expanded ? 'checked' : '';?>>
                    <span class="slider round"></span>
                    <span class="option-text">Expanded</span>
                </label>
            </span>
    </div>
    <div class="col">
                <span style="margin-right:70px;">
                <label class="switch float-none" style="line-height:18px;" for="truncated">
                    <input type="checkbox" value="1" class="no-js" id="truncated" <?php echo $settings->truncated ? 'checked' : '';?>>
                    <span class="slider round"></span>
                    <span class="option-text">Truncated</span>
                </label>
            </span>
    </div>
</div>
<?php if($report->settings->cr->id) { ?>
    <hr class="m-1">
    <div>
                <span class="ml-2">
                    <span sclass="option-text" style="font-size: 1.3em;"><?php echo $report->settings->cr->name1;?></span>
                    <label class="switch float-none ml-1" style="line-height:18px;" for="cr">
                        <input type="checkbox" value="<?php echo $report->settings->cr->id; ?>" class="no-js" id="cr" <?php echo $settings->cr ? 'checked' : '';?>>
                        <span class="slider round"></span>
                        <span class="option-text"><?php echo $report->settings->cr->name2;?></span>
                    </label>
                </span>
    </div>
<?php } ?>
<br/>
<?php foreach($report->settings->ufilters as $f) {
    if($filters[$f->column]->ismapped == null) $filters[$f->column]->ismapped = 1;
    if($filters[$f->column]->isexact == null) $filters[$f->column]->isexact = 1;
    ?>
    <div class="bre-filter-wrapper" fid="<?php echo $i++?>">
        <div class="form-entry bre-fe">
            <label for="">Column:</label><label class="text-left"><?php echo $columns[$f->column]['name'] ?></label>
            <input type="hidden" id="column" value="<?php echo $f->column?>"/>
        </div>
        <div class="form-entry bre-fe">
            <select class="col pr-2" id="ismapped">
                    <option value="1" <?php if($filters[$f->column]->ismapped == 1) echo 'selected'?>>Mapped to</option>
                    <option value="0" <?php if($filters[$f->column]->ismapped == 0) echo 'selected'?>>Value</option>
            </select>
            <select class="col" id="mapped" <?php if($filters[$f->column]->ismapped == 0) echo ' style="display:none;"'; else echo' default="'.$filters[$f->column]->mapped.'"';?>>
            </select>
            <input class="col" type="text" id="value" <?php if($filters[$f->column]->ismapped == 1) echo 'style="display:none;"'?> value="<?php echo $filters[$f->column]->value?>">
        </div>
        <div class="form-entry bre-fe" <?php if($filters[$f->column]->ismapped == 0) echo'style="display:none;"';?>>
            <select class="col pr-2" id="isexact">
                <option value="1" <?php if($filters[$f->column]->isexact == 1) echo 'selected'?>>Exact</option>
                <option value="0" <?php if($filters[$f->column]->isexact == 0) echo 'selected'?>>Relative</option>
            </select>
            <select class="col" id="map_key">
                <option value="value" <?php if($filters[$f->column]->map_key == 'value') echo 'selected'?>>Value</option>
                <option value="name1" <?php if($filters[$f->column]->map_key == 'name1') echo 'selected'?>>Value1</option>
                <option value="name2" <?php if($filters[$f->column]->map_key == 'name2') echo 'selected'?>>Value2</option>
            </select>
        </div>
        <div class="form-entry bre-fe" <?php if($filters[$f->column]->isexact || $filters[$f->column]->ismapped == '0') echo'style="display:none;"';?>>
            <input class="col" type="text" id="map_shift_value" value="<?php echo $filters[$f->column]->map_shift_value;?>">
            <select class="col" id="map_shift_type">
                <option value="y" <?php if($filters[$f->column]->map_shift_type == 'y') echo 'selected'?>>Year</option>
                <option value="M" <?php if($filters[$f->column]->map_shift_type == 'M') echo 'selected'?>>Month</option>
                <option value="w" <?php if($filters[$f->column]->map_shift_type == 'w') echo 'selected'?>>Week</option>
                <option value="d" <?php if($filters[$f->column]->map_shift_type == 'd') echo 'selected'?>>Day</option>
            </select>
        </div>
    </div>
<?php }
$i = 0;
foreach($report->settings->params as $f) { ?>
    <div class="bre-filter-wrapper" pid="<?php echo $i++?>">
        <div class="form-entry bre-fe">
            <label for="">Column:</label><label class="text-left"><?php echo $f->name ?></label>
            <input type="hidden" id="key" value="<?php echo $f->key?>"/>
        </div>
        <div class="form-entry bre-fe">
            <select class="col pr-2" id="ismapped">
                <option value="1" <?php if($params[$f->key]->ismapped == 1) echo 'selected'?>>Mapped to</option>
                <option value="0" <?php if($params[$f->key]->ismapped == 0) echo 'selected'?>>Value</option>
            </select>
            <select class="col" id="mapped" <?php if($params[$f->key]->ismapped == 0) echo 'style="display:none;"'; else echo' default="'.$params[$f->key]->mapped.'"';?>>
            </select>
            <input class="col" type="text" id="value" <?php if($params[$f->key]->ismapped == 1) echo 'style="display:none;"'?>>
        </div>
        <div class="form-entry bre-fe" <?php if($params[$f->key]->mapped < 0) echo 'style="display:none;"'; ?>>
            <select class="col pr-2" id="isexact">
                <option value="1" <?php if(!$params[$f->key]->isexact == 1) echo 'selected'?>>Exact</option>
                <option value="0" <?php if($params[$f->key]->isexact == 0) echo 'selected'?>>Relative</option>
            </select>
            <select class="col" id="map_key">
                <option value="value" <?php if($params[$f->key]->map_key == 'value') echo 'selected'?>>Value</option>
                <option value="name1" <?php if($params[$f->key]->map_key == 'name1') echo 'selected'?>>Value1</option>
                <option value="name2" <?php if($params[$f->key]->map_key == 'name2') echo 'selected'?>>Value2</option>
            </select>
        </div>
        <div class="form-entry bre-fe" <?php if($params[$f->key]->ismapped == 0 || $params[$f->key]->isexact == 1) echo 'style="display:none;"'; ?>>
            <input class="col" type="text" id="map_shift_value" value="<?php echo $params[$f->key]->map_shift_value;?>">
            <select class="col" id="map_shift_type">
                <option value="y" <?php if($params[$f->key]->map_shift_type == 'y') echo 'selected'?>>Year</option>
                <option value="M" <?php if($params[$f->key]->map_shift_type == 'M') echo 'selected'?>>Month</option>
                <option value="w" <?php if($params[$f->key]->map_shift_type == 'w') echo 'selected'?>>Week</option>
                <option value="d" <?php if($params[$f->key]->map_shift_type == 'd') echo 'selected'?>>Day</option>
            </select>
        </div>
    </div>
<?php } ?>
