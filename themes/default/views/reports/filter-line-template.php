<li class="section filter-wrapper">
    <span class="condition arrow-down">
        <select name="filtersel[]" class="filtersel" id="filtersel<?php echo $ind; ?>">
            <?php foreach ($columns as $column) { ?>
                <option value="<?php echo $column['id'] ?>" style="<?php //echo $column['checked'] == 'true' ? "" : "display:none" ?>"><?php echo $column['name'] ?></option>
            <?php } ?>
        </select>
    </span>
    <span class="condition arrow-down">
        <select class="filtercond" id="filtercond<?php echo $ind; ?>">
            <option value="0" selected>Equals</option>
            <option value="1">Not Equals</option>
            <option value="2">Like</option>
            <option value="3">Between</option>
            <option value="4">></option>
            <option value="5"><</option>
            <option value="6">In</option>
        </select>
    </span>
    <div class="field">
        <span class="value">
            <input type="text" id="filtername<?php echo $ind; ?>" name="name" placeholder="Please enter filter value">
        </span>

        <span class="date" style="display:none">
            <span>
                 <input type="text" id="lfd<?php echo $ind; ?>" name="name1" value="01/01/2019">
            </span>
            and
            <span>
                <input type="text" id="lfe<?php echo $ind; ?>" name="name2" value="01/01/2019">
            </span>
        </span>
    </div>
    <a href="#" class="add-or">Add "OR"</a>&nbsp;&nbsp;&nbsp;
    <a href="#" class="delete-section">Delete</a>
</li>