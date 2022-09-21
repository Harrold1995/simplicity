<div class="row" >
    <?php if(isset($allFolder)) { ?>
    <div class = 'col-4 d-folder-wrapper folder-link'
        <?php echo ($allFolder->id) ? 'filter-id = "'. $allFolder->id.'" ' : ' '; echo 'values = "'. $values.'"'; ?> path = '<?php echo $path; ?>'>
        <div class="d-folder">
            <img src="https://cdn3.iconfinder.com/data/icons/alicons/32/multiple_files-512.png"/>
            <p><?php echo $allFolder->name; ?></p>
        </div>
    </div>
    <?php } ?>
    <?php foreach ($filters as $filter) { ?>
        <div class = 'col-4 d-folder-wrapper folder-link'  <?php echo ($generated || in_array($filter->id, $folders)) ? '' : 'style="display:none;" ';?>
            <?php echo ($filter->id!=0) ? 'filter-id = "'. $filter->id.'" ' : ' '; echo 'values = "'. $filter->values.'"'; ?> path = '<?php echo ($filter->path == null) ? $path : $filter->path; ?>'>
            <div class="d-folder">
                <img src="https://cdn2.iconfinder.com/data/icons/ourea-icons/256/Folder_-_Open_256x256-32.png"/>
                <p><?php echo $filter->name; ?></p>
            </div>
        </div>
    <?php } ?>
</div>
