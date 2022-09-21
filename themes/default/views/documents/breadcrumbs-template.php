<ul>
    <?php
        $i=0;
        foreach ($crumbs as $crumb) { ?>
            <li>
                <a class="folder-link" path="<?php echo $crumb['path']; ?>" values="<?php echo $crumb['values']; ?>"><?php echo $crumb['name']; ?></a>
            </li>
            <?php //if(++$i < count($crumbs)) echo'<li>></li>'; ?>
    <?php } ?>
</ul>
