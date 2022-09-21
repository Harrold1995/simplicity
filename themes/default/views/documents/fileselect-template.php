
    <select class="form-control editable-select file-select" document-id="<?php echo $document_id;?>" index="<?php echo $index; ?>" type="<?php echo $type; ?>">
        <?php
        foreach ($data as $node) {
            echo '<option '.(($node->id == $preselected) ? 'selected' : '') . ' value="' . $node->id . '">' . $node->name . '</option>';
        } ?>
    </select>

