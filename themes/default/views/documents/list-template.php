<ul>
    <?php
        $i=0;
        foreach ($list as $node) { ?>
        <li>
            <label class="switch" for="filteroption-<?php echo $i; ?>">
                <input type="checkbox" value="<?php echo $node->id; ?>" <?php echo in_array($node->id, $filters) ? "checked" : ""; ?> name="filters[]" class="document-filter-option" mode="type" filter="<?php echo $node->name?>" id="filteroption-<?php echo $i++; ?>">
                <span class="slider round"></span>
                <span class="option-text">
                    <?php echo $node->name; ?>
                </span>
            </label>

        </li>
    <?php } ?>
</ul>
