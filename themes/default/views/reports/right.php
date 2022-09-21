<div class="tab-content">
<div id="background-filters" class="tab-pane in active">
    <ul class="list-filters">
        <div id="filters-section">
            <?php $i = 0;
            foreach ($filters as $filter) { ?>
                <li class="section filter-wrapper">
    <span class="condition arrow-down">
        <select name="filtersel[]" class="filtersel" id="filtersel<?php echo $i; ?>" first = "1">
            <?php foreach ($columns as $column) { ?>
                <option <?php echo $filter->column == $column['id'] ? 'selected' : ''; ?> value="<?php echo $column['id'] ?>" style="<?php //echo $column['checked'] == 'true' ? "" : "display:none" ?>"><?php echo $column['name'] ?></option>
            <?php } ?>
            <?php foreach ($cf as $column) { ?>
                <option <?php echo $filter->column == $column->id ? 'selected' : ''; ?> value="<?php echo $column->id ?>" style="<?php //echo $column['checked'] == 'true' ? "" : "display:none" ?>"><?php echo $column->name ?></option>
            <?php } ?>
        </select>
    </span>
    <span class="condition arrow-down">
        <select class="filtercond" id="filtercond<?php echo $i; ?>">
            <option value="0" <?php echo $filter->condition == 0 ? 'selected' : ''; ?>>Equals</option>
            <option value="1" <?php echo $filter->condition == 1 ? 'selected' : ''; ?>>Not Equals</option>
            <option value="2" <?php echo $filter->condition == 2 ? 'selected' : ''; ?>>Like</option>
            <option value="3" <?php echo $filter->condition == 3 ? 'selected' : ''; ?>>Between</option>
            <option value="4" <?php echo $filter->condition == 4 ? 'selected' : ''; ?>>></option>
            <option value="5" <?php echo $filter->condition == 5 ? 'selected' : ''; ?>><</option>
            <option value="6" <?php echo $filter->condition == 6 ? 'selected' : ''; ?>>In</option>
        </select>
    </span>
    <?php $k = 0; foreach ($filter->fields as $field) { ?>
        <div class="field">
            <?php if($k > 0) echo'<a href="#" class="delete-or"><i class="fas fa-times-circle"></i></a><div class="or-block">or</div>'; ?>
            <span class="value">
                <input type="text" id="filtername<?php echo $i.$k; ?>" name="name" value="<?php echo $field->value ?>" placeholder="Please enter filter value">
            </span>

            <span class="date" style="display:none">
                <span>
                     <input type="text" id="lfd<?php echo $i.$k; ?>" name="name1" value="<?php echo $field->name1 ?>">
                </span>
                and
                <span>
                    <input type="text" id="lfe<?php echo $i.$k++; ?>" name="name2" value="<?php echo $field->name2 ?>">
                </span>
            </span>
        </div>
    <?php } ?>
            <a href="#" class="add-or">Add "OR"</a>&nbsp;&nbsp;&nbsp;
            <a href="#" class="delete-section">Delete</a>

                </li>

                <?php $i++;
            } ?>
        </div>

        <li class="submit">
            <button id="add-filter">Add filter</button>
        </li>
        <li class="submit">
            <button id="apply-filter">Apply filter</button>
        </li>
    </ul>
        </div>
<div  id = "user-filters" class="tab-pane">
    <ul class="list-filters">
    <div id="ufilters-section">
        <?php $i = 0;
        foreach ($ufilters as $filter) { ?>
            <li class="section filter-wrapper">
<span class="condition arrow-down">
    <select name="filtersel[]" class="filtersel" id="filtersel<?php echo $i; ?>" first = "1">
        <?php foreach ($columns as $column) { ?>
            <option <?php echo $filter->column == $column['id'] ? 'selected' : ''; ?> value="<?php echo $column['id'] ?>" style="<?php //echo $column['checked'] == 'true' ? "" : "display:none" ?>"><?php echo $column['name'] ?></option>
        <?php } ?>
        <?php foreach ($cf as $column) { ?>
            <option <?php echo $filter->column == $column->id ? 'selected' : ''; ?> value="<?php echo $column->id ?>" style="<?php //echo $column['checked'] == 'true' ? "" : "display:none" ?>"><?php echo $column->name ?></option>
        <?php } ?>
    </select>
</span>
                <span class="condition arrow-down">
    <select class="filtercond" id="filtercond<?php echo $i; ?>">
        <option value="0" <?php echo $filter->condition == 0 ? 'selected' : ''; ?>>Equals</option>
        <option value="1" <?php echo $filter->condition == 1 ? 'selected' : ''; ?>>Not Equals</option>
        <option value="2" <?php echo $filter->condition == 2 ? 'selected' : ''; ?>>Like</option>
        <option value="3" <?php echo $filter->condition == 3 ? 'selected' : ''; ?>>Between</option>
        <option value="4" <?php echo $filter->condition == 4 ? 'selected' : ''; ?>>></option>
        <option value="5" <?php echo $filter->condition == 5 ? 'selected' : ''; ?>><</option>
        <option value="6" <?php echo $filter->condition == 6 ? 'selected' : ''; ?>>In</option>
    </select>
</span>

<?php $k = 0; foreach ($filter->fields as $field) { ?>
                    <div class="field">
                        <?php if($k > 0) echo'<a href="#" class="delete-or"><i class="fas fa-times-circle"></i></a><div class="or-block">or</div>'; ?>
                        <span class="value">
                <input type="text" id="filtername<?php echo $i.$k; ?>" name="name" value="<?php echo $field->value ?>" placeholder="Please enter filter value">
            </span>

                        <span class="date" style="display:none">
                <span>
                     <input type="text" id="lfd<?php echo $i.$k; ?>" name="name1" value="<?php echo $field->name1 ?>">
                </span>
                and
                <span>
                    <input type="text" id="lfe<?php echo $i.$k++; ?>" name="name2" value="<?php echo $field->name2 ?>">
                </span>
            </span>
                    </div>
                <?php } ?>
                <a href="#" class="add-or">Add "OR"</a>&nbsp;&nbsp;&nbsp;
                <a href="#" class="delete-section">Delete</a>
            </li>

            <?php $i++;
        } ?>
    </div>

    <li class="submit">
        <button id="add-ufilter">Add filter</button>
    </li>
    <li class="submit">
        <button id="apply-filter">Apply filter</button>
    </li>
    </ul>
    </div>
    <div id="custom-parameters" class="tab-pane in">

        <div class="mb-3">
            <h3 class="text-center">Custom recordset:</h3>
            <ul id="recordsets" class="list-filters">
                <div id="recordsets-section">
                    <li>
                        <select name="cr[id]">
                            <option value="0">No recordset</option>
                            <?php
                            foreach($types as $recordset) {
                                echo'<option '.($recordset->id == $cr->id ? 'selected' : '').' value="'.$recordset->id.'">'.$recordset->name.'</option>';
                            } ?>
                        </select>
                        <label>Default name: </label><input type="text" name="cr[name1]" value="<?php echo isset($cr->id) ? $cr->name1 : ''?>" placeholder="Default recordset name">
                        <label>Custom name: </label><input type="text" name="cr[name2]" value="<?php echo isset($cr->id) ? $cr->name2 : ''?>" placeholder="Custom recordset name">

                    </li>
                </div>
            </ul>
        </div>
        <p>
            <label for="custom_types" class="hidden">Choose Custom Function</label>
            <select id="custom_types">

                <option value="0" selected>No Custom Function</option>
                <option value="1">Trial Balance</option>

            </select>
        </p>
        <div class="list-tree"></div>
        <h3 class="text-center">Parameters:</h3>
        <ul id="report-params" class="list-filters">
            <div id="params-section">
                <?php foreach ($params as $p) { ?>
                <li>
                    <label>Parameter name: </label><input type="text" name="param_name" value="<?php echo $p->name ?>" placeholder="Parameter name"/>
                    <label>Parameter key: </label><input type="text" name="param_key" value="<?php echo $p->key ?>" placeholder="Parameter key"/>
                    <p>Use it as {params.<span><?php echo $p->key ?></span>}</p>
                    <p>
                        <select name="param_type">
                            <option value="text" <?php echo $p->type == 'text' ? 'selected' : ''; ?>>Text</option>
                            <option value="num" <?php echo $p->type == 'num' ? 'selected' : ''; ?>>Number</option>
                            <option value="date" <?php echo $p->type == 'date' ? 'selected' : ''; ?>>Date</option>
                        </select>
                    </p>
                    <label>Parameter value: </label>
                    <div>
                        <input type="text" <?php echo $p->type == 'date' ? ' class="dfield" ' : ''; ?> value="<?php echo $p->value ?>" name="param_value" placeholder="Parameter value"/>
                    </div>
                    <label>Parameter source: </label><input type="text"  value="<?php echo $p->source ?>" name="param_source" placeholder="Parameter source"/>

                    <a href="#" class="delete-section">Delete</a>
                </li>
                <?php } ?>
            </div>
        <li class="submit">
            <button id="add-params">Add parameter</button>
        </li>
        <li class="submit">
            <button id="apply-params">Refresh</button>
        </li>
        </ul>
    </div>
</div>
