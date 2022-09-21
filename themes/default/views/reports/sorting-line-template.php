<li class="report-gline" grouping-id="<?php echo $ind; ?>">Sort by
    <span class="select">
        <select class="sorting-select" name="sorting[]">
            <option selected disabled>Choose</option>
            <?php foreach ($columns as $column) { ?>
                <option value="<?php echo $column['id'] ?>" style=""><?php echo $column['name'] ?></option>
            <?php } ?>
        
        </select>
     </span>
    <div id="totals" style="display:none">
        <div>
            <span class="select">
        <select class="order-select" name="sorting[]">
            <option value="asc" selected >ASC</option>
            <option value="desc" >DESC</option>
        </select>
     </span>
        </div>
        <ul class="sort">
            <li><a href="#" class="arrow-up"><i class="icon-arrow-up"></i> <span>Sort up</span></a></li>
            <li><a href="#" class="arrow-down"><i class="icon-arrow-down"></i> <span>Sort down</span></a></li>
        </ul>
        <a href="#" class="close">Close</a>
    </div>
</li>