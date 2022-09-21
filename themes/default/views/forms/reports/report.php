    <div class="modal flexmodal fade report-modal hide" id="propertyModal" tabindex="-1" role="dialog" main-id="<?= isset($id) ? $id : '-1' ?>" type="report" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document" style ="max-width: 1200px">
            <div id="root">
                <div class="modal-content text-primary popup-a form-entry shown" style="padding: 15px 35px 15px 35px; ">
                    <!--form action="< ?php echo $target; ?>" method="post"-->
                    <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="report">

                        <header class="modal-h ui-draggable-handle">
                            <h2 class="text-uppercase" id="title2"><span><?php echo $title; ?></span></h2>
                            <nav>
                                <ul>
                                    <li><span class="buttons" style=""><span class="min" style=";padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                                    <li><span class="buttons" style=""><span class="reportMax" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                                    <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                                </ul>
                            </nav>
                            <nav>
                                <ul class="">
                                    <!-- <li><a href=""><i class="icon-chevron-left"></i> <span>Previous</span></a></li> -->

                                    <li class="get_send_email_form"><a href="#" class="report-pdf"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
                                    <li><a href="#"><i  class="icon-excel"></i> <span>Excel</span></a></li>
                                    <li id = 'printIdRR'>
                                        <a class="print printModal" href="#">
                                            <i id = 'printIdRR' class="icon-print"></i> <span>Print</span>
                                        </a>
                                    </li>
                                    <!--li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li-->
                                    <!--li><a href="#" class="uploadDocument"><i class="icon-paperclip"></i> <span>Attach</span></a></li-->
                                    <!--li><a class="print printModal" href="#"><i class="icon-print"></i> <span>Print</span></a></li>
                                    <li><a class="refresh-modal" href="#"><i class="icon-zoom2"></i> <span>Refresh</span></a></li-->
                                    <li><a href="<?php echo base_url('reports/edit/'.$id);?>" target="_blank"><i class="icon-tools"></i> <span>customize</span></a></li>
                                    <li><a class="refresh-modal" href="#"><i class="fas fa-sync-alt"></i> <span>Refresh</span></a></li>
                                    <li class="close2"><a href="#" data-dismiss="modal" aria-label="close"><i class="icon-x-thin"></i> <span>Close</span></a></li>
                                </ul>
                            </nav>
                        </header>

                        <section class="report-filters" style="border:none; padding:10px 15px 10px 15px;">
                            <input type="hidden" id="record_types" value="<?php echo $mtype;?>">
                            <div class-"row">
                                <span>FILTERS</span>

                            <span class="float-right">
                                <?php if((int)$settings->cr->id > 0) {
                                    echo'<span style="margin-right:70px;">
                                            <span style="font-weight:600;">'.$settings->cr->name1.'</span>
                                            <label class="switch float-none" style="line-height:18px;" for="cr-trigger">
                                                <input type="checkbox" value="1" class="no-js cr-trigger" id="cr-trigger">
                                                <span class="slider round"></span>
                                                <span class="option-text">'.$settings->cr->name2.'</span>
                                            </label>
                                        </span>';
                                    echo'<select class="d-none w-auto cr-select"><option '.(!$settings->newtype || $settings->newtype == $settings->type ? 'selected' : '').' value="'.$settings->type.'">'.$settings->cr->name1.'</option>';
                                    echo'<option '.($settings->newtype == $settings->cr->id ? 'selected' : '').' value="'.$settings->cr->id.'">'.$settings->cr->name2.'</option>';
                                    echo'</select>';
                                } ?>

                                    <ul class="print-orientation mini mr-4">
                                        <li <?php echo $settings->printmode != '1' ? 'class="active"' : '';?>>
                                            <input type="radio" <?php echo $settings->printmode == '1' ? 'checked' : '';?> name="print-mode" id="portrait" value="0">
                                            <label for="portrait"><i class="fas fa-portrait"></i></label>
                                        </li>
                                        <li <?php echo $settings->printmode == '1' ? 'class="active"' : '';?>>
                                            <input type="radio" <?php echo $settings->printmode == '1' ? 'checked' : '';?> name="print-mode" id="landscape" value="1">
                                            <label for="landscape"><i class="fas fa-image"></i></label>
                                        </li>
                                    </ul>
                                <span style="margin-right:70px;">
                                    <label class="switch float-none" style="line-height:18px;" for="slick-truncate">
                                        <input type="checkbox" value="1" class="no-js" <?php echo $settings->truncate == '1' ? 'checked' : '';?> id="slick-truncate">
                                        <span class="slider round"></span>
                                        <span class="option-text">Truncated</span>
                                    </label>
                                </span>
                                <span style="margin-right:70px;">
                                    <label class="switch float-none" style="line-height:18px;" for="slick-expanded">
                                        <input type="checkbox" value="1" class="no-js" checked id="slick-expanded">
                                        <span class="slider round"></span>
                                        <span class="option-text">Expanded</span>
                                    </label>
                                </span>
                                <a href="#" class="edit-columns cpopup-trigger" data-target="#columnspopup">Edit Columns</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="apply-button">Apply filters</a> | <a href="#" class="hide-button">Hide</a></span>
                            </div>
                            <div class="row shown" id="reports-filters" style="width:100%;">
                            <?php foreach($fs as $fid=>$filter) {
                                $def = array_shift($defaults);
                                ?>
                                <div class="col-auto filter-wrapper" id="f<?php echo $fid?>">
                                    <p>
                                        <label for="name<?php echo $filter->column; ?>"><?php echo $columns[$filter->column]['name']." ".$signs[$filter->condition]; ?></label>
                                        <?php if($filter->condition == 3) {
                                            $defs = explode('|',$def);
                                            if($filter->dtype == 'date') echo '<input type="text" class="dinput" id="name1-' . $filter->column . '" name="name1" value="' . (($defs[0] ) ? $defs[0] : $filter->fields[0]->name1) . '"> <span>and</span> <input type="text" class="dinput" id="name1-' . $filter->column . '" name="name2" value="' . (($defs[1] != '') ? $defs[1] : $filter->fields[0]->name2) . '">';
                                            else echo '<input type="text" id="name1-' . $filter->column . '" name="name1" value="' . (($defs[0]) ? $defs[0] : $filter->fields[0]->name1) . '"> <span>and</span> <input type="text" id="name1-' . $filter->column . '" name="name2" value="' . (($defs[1] != '') ? $defs[1] : $filter->fields[0]->name2) . '">';

                                        }else
                                            echo '<span><input type="text" '.($filter->dtype == 'date' ? 'class="dinput"' : '').' '.($columns[$filter->column]['source'] ? 'source="'.$columns[$filter->column]['source'].'"' : '').' id="name'. $filter->column .'" name="name" value="'. (($def) ? $def :  $filter->fields[0]->value) .'"></span>';
                                        ?>
                                        <input type="hidden" class="filtersel" value="<?php echo $filter->column ?>">
                                        <input type="hidden" class="filtercond" value="<?php echo $filter->condition ?>">
                                    </p>
                                </div>
                            <?php } ?>

                                <?php $cid = 1;
                                        foreach($params as $pid => $param) {
                                            $def = array_shift($defaults); ?>

                                            <div class="col-auto parameters" id="p<?php echo $pid?>">
                                                <p>
                                                    <label for="p<?php echo $cid; ?>"><?php echo $param->name; ?></label>
                                                    <span>
                                                        <input source="<?php echo $param->source; ?>" type="text" <?php echo($param->type == 'date') ? 'class="dinput"' : ''; ?> id="p<?php echo $cid++; ?>" key="<?php echo $param->key; ?>" value="<?php echo $def ? $def : $param->value; ?>">
                                                    </span>
                                                </p>
                                            </div>
                                        <?php } ?>

                            </div>
                        </section>
                        <div id="report-header">
                            <h2 class="text-uppercase" style= "color: #878A89; text-align:center"><?php echo strtoupper($title); ?></h2>
                            <h4 style= "color: #878A89; text-align:center"><?php echo (int)$settings->cr->id > 0 ? $settings->cr->name1 : ''?></h4>
                            <div></div>
                        </div>

                        <input type="hidden" id="record_types" value="<?php echo $mtype; ?>"/>
                        <div id="reports-table" style ="">
                              <div class="lds-roller" style ="padding-top: 150px;  overflow:hidden;  padding-left: 500px; min-width:600px"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>

    var reports = new Reports('', '#reports-table', '', "#reports-filters",
            {
                base: '<?php echo base_url();?>',
                modal: true,
                id: <?php echo $id;?>,
                <?php if($id == '-1') echo 'settings: '.json_encode($settings).',' ?>
                type: <?php echo $settings->type ? $settings->type : $rtype;?>,
                settingsURL: 'reports/getSettings',
                ajaxTableURL: 'reports/getAjaxTable',
                ajaxTableHeaderURL: 'reports/getAjaxTableHeader'
            });
            //$("#printIdR").click(function(){printPart();}) 
            //$("#exportocsvR").click(function(){exportTableToCSV("fileName");})
</script>