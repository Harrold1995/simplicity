<ul class="list-tree">

    <li class="report-gmain checklist-a" style="margin-left: 0px;">
        Top Level
        <div id="main" style="display: none">
            <div class="hftotals">
                <div class="ml-4 mr-4">
                    <div class="custom-control custom-checkbox form-group mb-0">
                        <input type="checkbox" <?php echo $top->gtotal ? 'checked' : '' ?> value="1" class="custom-control-input" name="gtotal" id="gtotal">
                        <label class="custom-control-label checkbox-left text-left" for="gtotal">Grand Total</label>
                    </div>
                </div>
                <div class="ml-4 mr-4">
                    <input type="text" id="gtotal_custom" value="<?php echo $top->gt_custom;?>">
                </div>
            </div>
        </div>
    </li>

    <?php $i = 0;
    foreach ($grouping as $group) {
        if ($group->type == 'grouping') { ?>
            <li class="report-gline checklist-a" grouping-id="<?php echo $i; ?>">Group by
                <span class="select">
        <select class="grouping-select" name="grouping[]">
            <option disabled>Choose</option>
            <?php foreach ($columns as $column) { ?>
                <option value="<?php echo $column['id'] ?>" style="<?php echo $column['checked'] != 'gg' ? "" : "display:none" ?>" <?php echo ($group->column == $column['id']) ? 'selected' : ''; ?>><?php echo $column['name'] ?></option>
            <?php } ?>

        </select>
     </span>
                <div id="totals" style="display:none">

                <div class="datef-wrapper" style="<?php echo ($group->dtype != 'date') ? 'display:none' : ''?>">
                <span class="select">
                    <select class="datef">
                        <option value="0" <?php echo $group->datef == 0 ? 'selected' : ''; ?>>-</option>
                        <option value="1" <?php echo $group->datef == 1 ? 'selected' : ''; ?>>M</option>
                        <option value="2" <?php echo $group->datef == 2 ? 'selected' : ''; ?>>Y</option>
                    </select>
                </span>
                </div>
                <div class="hftotals" <?php echo ($group->hg == '1') ? 'style="display:none"' : '';_?>>
                    <div>
                        <button type="button" class="d-inline-block ml-1 cpopup-trigger" data-target="#totalspopup<?= $i ?>">
                            Totals
                        </button>
                        <div class="cpopup" id="totalspopup<?php echo $i; ?>">
                            <table>
                                <tr><td>F</td><td>H</td><td>Hide 0</td><td>Column</td>
                            <?php
                            foreach ($columns as $column) {
                                if ($column['type'] != "num") continue;
                                echo'<tr>';
                                //echo'<label for="header' . $i . '_' . $column['id'] . '"><input type="checkbox" id="header' . $i . '_' . $column['id'] . '" ' . (in_array($column['id'], $group->header) ? 'checked' : '') . ' value="' . $column['id'] . '" name="header[]">' . $column['name'] . '</label>';
                                echo '<td><div class="custom-control custom-checkbox form-group mb-0">
                                                <input type="checkbox" ' . (in_array($column['id'], $group->footer) ? 'checked' : '') . ' value="' . $column['id'] . '" class="custom-control-input" name="footer[]" id="footer' . $i . '_' . $column['id'] . '">
                                                <label class="custom-control-label checkbox-left text-left" for="footer' . $i . '_' . $column['id'] . '"></label>
                                            </div></td>';
                                echo '<td><div class="custom-control custom-checkbox form-group mb-0">
                                                <input type="checkbox" ' . (in_array($column['id'], $group->header) ? 'checked' : '') . ' value="' . $column['id'] . '" class="custom-control-input" name="header[]" id="header' . $i . '_' . $column['id'] . '">
                                                <label class="custom-control-label checkbox-left text-left" for="header' . $i . '_' . $column['id'] . '"> </label>
                                            </div></td>';
                                echo '<td><div class="custom-control custom-checkbox form-group mb-0">
                                                <input type="checkbox" ' . (in_array($column['id'], $group->hide0) ? 'checked' : '') . ' value="' . $column['id'] . '" class="custom-control-input" name="hide[]" id="hide' . $i . '_' . $column['id'] . '">
                                                <label class="custom-control-label checkbox-left text-left" for="hide' . $i . '_' . $column['id'] . '"> </label>
                                            </div></td>';
                                echo '<td><div class="form-group mb-0">
                                                <label class="text-left pl-0" >' . $column['name'] . '</label>
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
                <div class="hgtotals" <?php echo ($group->hg != '1') ? 'style="display:none"' : '';_?>>
                    <div class="mr-4 ml-4">
                        <span class="select">
                            <select class="hg-select">
                                <option disabled>Choose total</option>
                                <?php foreach ($columns as $column) {
                                    if ($column['type'] != "num") continue; ?>
                                    <option value="<?php echo $column['id'] ?>" style="<?php echo $column['checked'] == 'true' ? "" : "display:none" ?>" <?php echo ($group->htotal == $column['id']) ? 'selected' : ''; ?>><?php echo $column['name'] ?></option>
                                <?php } ?>

                            </select>
                        </span>
                    </div>
                    <div class="mr-4">
                        <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                            <input type="checkbox" value="1" <?php echo ($group->ht == '1') ? 'checked' : '';_?> class="custom-control-input" name="ht" id="ht<?php echo $i ?>">
                            <label class="custom-control-label checkbox-left text-left" for="ht<?php echo $i ?>">H Total</label>
                        </div>
                    </div>
                    <div class="mr-4">
                        <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                            <input type="checkbox" value="1" <?php echo ($group->vt == '1') ? 'checked' : '';_?> class="custom-control-input" name="vt" id="vt<?php echo $i ?>">
                            <label class="custom-control-label checkbox-left text-left" for="vt<?php echo $i ?>">V Total</label>
                        </div>
                    </div>
                    <div class="mr-4">
                        <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                            <input type="checkbox" value="1" <?php echo ($group->he == '1') ? 'checked' : '';_?> class="custom-control-input" name="he" id="he<?php echo $i ?>">
                            <label class="custom-control-label checkbox-left text-left" for="he<?php echo $i ?>">Hide Empty</label>
                        </div>
                    </div>
                </div>
                    <div class="mr-4">
                        <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                            <input type="checkbox" value="1" <?php echo ($group->hg == '1') ? 'checked' : '';_?> class="custom-control-input" name="hg" id="hg<?php echo $i ?>">
                            <label class="custom-control-label checkbox-left text-left" for="hg<?php echo $i ?>">HG</label>
                        </div>
                    </div>
                    <div class="mr-2">
                        <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                            <input type="checkbox" value="1" <?php echo ($group->exp == '1') ? 'checked' : '';_?> class="custom-control-input" name="exp" id="exp<?php echo $i ?>">
                            <label class="custom-control-label checkbox-left text-left" for="exp<?php echo $i ?>">Expand</label>
                        </div>
                    </div>
                    <div class="mr-2">
                        <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                            <input type="checkbox" value="1" <?php echo ($group->nexp == '1') ? 'checked' : '';_?> class="custom-control-input" name="nexp" id="nexp<?php echo $i ?>">
                            <label class="custom-control-label checkbox-left text-left" for="nexp<?php echo $i ?>">N Expand</label>
                        </div>
                    </div>
                    <div>
                        <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                            <input type="checkbox" value="1" <?php echo ($group->col == '1') ? 'checked' : '';_?> class="custom-control-input" name="col" id="col<?php echo $i ?>">
                            <label class="custom-control-label checkbox-left text-left" for="col<?php echo $i ?>">Collapse</label>
                        </div>
                    </div>
                    <ul class="sort">
                        <li><a href="#" class="arrow-up"><i class="icon-arrow-up"></i> <span>Sort up</span></a></li>
                        <li><a href="#" class="arrow-down"><i class="icon-arrow-down"></i> <span>Sort down</span></a></li>
                    </ul>
                    <a href="#" class="close">Close</a>
                </div>
            </li>
            <?php
        } else { ?>
            <li class="report-gline" sorting-id="<?php echo $i; ?>">Sort by
                <span class="select">
        <select class="sorting-select" name="sorting[]">
            <option selected disabled>Choose</option>
            <?php foreach ($columns as $column) { ?>
                <option value="<?php echo $column['id'] ?>" <?php echo ($group->column == $column['id']) ? 'selected' : ''; ?>><?php echo $column['name'] ?></option>
            <?php } ?>

        </select>
     </span>
                <div id="totals" style="display:none">
                    <div>
            <span class="select">
        <select class="order-select" name="sorting[]">
            <option value="asc" <?php echo $group->order == 'asc' ? 'selected' : '';?>>ASC</option>
            <option value="desc" <?php echo $group->order == 'desc' ? 'selected' : '';?>>DESC</option>
        </select>
     </span>
                    </div>
                    <ul class="sort">
                        <li><a href="#" class="arrow-up"><i class="icon-arrow-up"></i> <span>Sort up</span></a></li>
                        <li><a href="#" class="arrow-down"><i class="icon-arrow-down"></i> <span>Sort down</span></a>
                        </li>
                    </ul>
                    <a href="#" class="close">Close</a>
                </div>
            </li>
        <?php }
        $i++;
    } ?>
</ul>
