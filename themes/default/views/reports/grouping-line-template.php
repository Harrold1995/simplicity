<li class="report-gline" grouping-id="<?php echo $ind; ?>">Group by
    <span class="select">
        <select class="grouping-select" name="grouping[]">
            <option selected disabled>Choose</option>
            <?php foreach ($columns as $column) { ?>
                <option value="<?php echo $column['id'] ?>" style="<?php echo $column['checked'] != 'gg' ? "" : "display:none" ?>"><?php echo $column['name'] ?></option>
            <?php } ?>
        
        </select>
     </span>
    <div id="totals" style="display:none">
        <div class="datef-wrapper" style="display:none">
                <span class="select">
                    <select class="datef">
                        <option value="0" selected>-</option>
                        <option value="1">M</option>
                        <option value="2">Y</option>
                    </select>
                </span>
        </div>
        <div class="hftotals">
            <div>
                <button type="button" class="d-inline-block ml-1 cpopup-trigger" data-target="#headerpopup<?= $ind ?>">
                    Totals
                </button>
                <div class="cpopup" id="headerpopup<?php echo $ind; ?>">
                    <table>
                        <tr><td>F</td><td>H</td><td>Column</td>

                    <?php
                    foreach ($columns as $column) {
                        if($column['type'] != 'num') continue;
                        echo '<tr>';
                        echo '<td><div class="custom-control custom-checkbox form-group mb-0">
                                    <input type="checkbox" value="' . $column['id'] . '" class="custom-control-input" name="footer[]" id="footer' . $ind . '_'.$column['id'].'">
                                    <label class="custom-control-label checkbox-left text-left" for="footer' . $ind . '_'.$column['id'].'"></label>
                                </div></td>';
                        echo '<td><div class="custom-control custom-checkbox form-group mb-0">
                                    <input type="checkbox" value="' . $column['id'] . '" class="custom-control-input" name="header[]" id="header' . $ind . '_'.$column['id'].'">
                                    <label class="custom-control-label checkbox-left text-left" for="header' . $ind . '_'.$column['id'].'"></label>
                                </div></td>';
                        echo '<td><div class="custom-control custom-checkbox form-group mb-0">
                                    <input type="checkbox" value="' . $column['id'] . '" class="custom-control-input" name="hide[]" id="hide' . $ind . '_'.$column['id'].'">
                                    <label class="custom-control-label checkbox-left text-left" for="hide' . $ind . '_'.$column['id'].'"></label>
                                </div></td>';
                        echo '<td><div class="form-group mb-0">
                                    <label class="text-left pl-0">' . $column['name'] . '</label>
                                </div></td>';
                        echo '</tr>';
                    } ?>
                    </table>

                    <div class="row justify-content-center mb-0 mt-1">
                        <a href="#">Done</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="hgtotals" style="display:none">
            <div class="mr-4 ml-4">
                        <span class="select">
                            <select class="hg-select">
                                <option selected disabled>Choose total</option>
                                <?php foreach ($columns as $column) {
                                    if ($column['type'] != "num") continue; ?>
                                    <option value="<?php echo $column['id'] ?>" style="<?php echo $column['checked'] == 'true' ? "" : "display:none" ?>"><?php echo $column['name'] ?></option>
                                <?php } ?>

                            </select>
                        </span>
            </div>
            <div class="mr-4">
                <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                    <input type="checkbox" value="1" class="custom-control-input" name="ht" id="ht<?php echo $ind ?>">
                    <label class="custom-control-label checkbox-left text-left" for="ht<?php echo $ind ?>">H Total</label>
                </div>
            </div>
            <div class="mr-4">
                <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                    <input type="checkbox" value="1"  class="custom-control-input" name="vt" id="vt<?php echo $ind ?>">
                    <label class="custom-control-label checkbox-left text-left" for="vt<?php echo $ind ?>">V Total</label>
                </div>
            </div>
            <div class="mr-4">
                <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                    <input type="checkbox" value="1"  class="custom-control-input" name="he" id="he<?php echo $ind ?>">
                    <label class="custom-control-label checkbox-left text-left" for="he<?php echo $ind ?>">Hide Empty</label>
                </div>
            </div>
        </div>
        <div class="mr-4">
            <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                <input type="checkbox" value="1" class="custom-control-input" name="hg" id="hg<?php echo $ind ?>">
                <label class="custom-control-label checkbox-left text-left" for="hg<?php echo $ind ?>">HG</label>
            </div>
        </div>
        <div class="mr-2">
            <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                <input type="checkbox" value="1" class="custom-control-input" name="exp" id="exp<?php echo $ind ?>">
                <label class="custom-control-label checkbox-left text-left" for="exp<?php echo $ind ?>">Expand</label>
            </div>
        </div>
        <div class="mr-2">
            <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                <input type="checkbox" value="1" class="custom-control-input" name="nexp" id="nexp<?php echo $ind ?>">
                <label class="custom-control-label checkbox-left text-left" for="nexp<?php echo $ind ?>">N Expand</label>
            </div>
        </div>
        <div>
            <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                <input type="checkbox" value="1" class="custom-control-input" name="col" id="col<?php echo $ind ?>">
                <label class="custom-control-label checkbox-left text-left" for="col<?php echo $ind ?>">Collapse</label>
            </div>
        </div>
        <ul class="sort">
            <li><a href="#" class="arrow-up"><i class="icon-arrow-up"></i> <span>Sort up</span></a></li>
            <li><a href="#" class="arrow-down"><i class="icon-arrow-down"></i> <span>Sort down</span></a></li>
        </ul>
        <a href="#" class="close">Close</a>
    </div>
</li>