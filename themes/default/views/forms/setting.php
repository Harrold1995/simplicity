<div class="modal fade" data-type="setting" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form action="<?php echo $target; ?>" method="post" type="settings">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><?php echo $title; ?></h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="form-group row col">
                                <label for="name" class="col-auto">Setting</label>
                                <div class="col field-input">
                                    <input type="text" value="<?= isset($setting_details) ? $setting_details->name : '' ?>" class="form-control" name="details[name]" id="name" placeholder="Setting">
                                </div>
                            </div>
                            <div class="form-group row col">
                                <label for="name" class="col-auto">Key</label>
                                <div class="col field-input">
                                    <input type="text" value="<?= isset($setting_details) ? $setting_details->key : '' ?>" class="form-control" name="details[key]" id="key" placeholder="Key">
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="description" class="col-auto">Database Reference</label>
                            <div class="col field-input">
                                <input type="text" value="<?= isset($setting_details) ? $setting_details->reference : '' ?>" class="form-control" name="details[reference]" id="reference" placeholder="Reference">
                            </div>
                        </div>
                        <div class="box-list thead-light">
                            <div class="row setting-fields">
                                <div class="form-group col-1">
                                    Fields:
                                </div>
                                <div class="form-group col-11 row">
                                    <?php $find = 0;
                                    if(isset($setting) && is_array(array_values($setting)[0])) {
                                        foreach (array_values($setting)[0] as $id => $value) { ?>
                                            <div class="field-input col" column="<?= $find ?>">
                                                <input type="text" value="<?= $id ?>" class="form-control" name="fields[<?= $find ?>][name]">
                                                <a href="#" class="delete-field" column="<?= $find ?>"><i class="fas fa-times-circle"></i></a>
                                            </div>
                                            <?php $find++;
                                        }
                                    } else { ?>
                                        <div class="field-input col" column="<?= $find ?>">
                                            <input type="text" value="value" class="form-control" name="fields[<?= $find ?>][name]">
                                            <a href="#" class="delete-field" column="<?= $find ?>"><i class="fas fa-times-circle"></i></a>
                                        </div>
                                    <?php } ?>

                                    <a href="#" class="add-option"><i class="fas fa-plus-circle"></i></a>
                                </div>
                            </div>
                            <?php $ind = 0;
                            if (isset($setting))
                            foreach ($setting as $id => $value) { ?>
                                <div class="row">
                                        <div class="form-group col-1">
                                            <div class="field-input">
                                                <input type="text" value="<?= $id ?>" class="form-control setting-id" name="values[<?=$ind?>][id]" readonly>
                                            </div>
                                        </div>
                                        <div class="form-group col-11 row" row-id="<?=$ind?>">
                                            <?php $find=0;
                                            if(is_array($value)) {
                                                foreach ($value as $fid => $fvalue) { ?>
                                                    <div class="field-input col" column="<?= $find ?>">
                                                        <input type="text" value="<?= $fvalue ?>" class="form-control" name="values[<?= $ind ?>][<?= $find ?>][value]">
                                                    </div>
                                                    <?php $find++;
                                                }
                                            }else{ ?>
                                                    <div class="field-input col" column="0">
                                                        <input type = "text" value = "<?= $value ?>" class="form-control" name = "values[<?=$ind?>][0][value]" >
                                                    </div >
                                            <?php } ?>
                                            <a href="#" class="delete-option" onclick="$(this).parent().parent().remove();"><i class="fas fa-times-circle"></i></a>
                                        </div>
                                    </div>
                            <?php $ind++;
                            } ?>
                        </div>
                        <div class="row justify-content-center">
                            <a class="addLateChargeRule" href="#" onclick="JS.appendHtml('htmlapi/getSettingRow',$(this).parent().prev(), {id:($('.setting-id:last').val()), columns: $.map( $('.setting-fields .row').children(), function(o){return $(o).attr('column');}).join(',')})"><i class="fas fa-plus-square"></i></a>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" refresh="true" class="btn btn-primary">
                        Save
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
