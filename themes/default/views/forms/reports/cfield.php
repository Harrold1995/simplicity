<div class="modal fade" data-type="setting" tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form action="<?php echo $target; ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><?php echo $title; ?></h5>

                    <button type="button" class="close close2" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="form-group row col-12">
                                <label for="name" class="col-auto">Name</label>
                                <div class="col field-input">
                                    <input type="text" value="<?= isset($set) ? $set->name : '' ?>" class="form-control" name="name" id="name" placeholder="Field Name">
                                </div>
                            </div>
                            <div class="form-group row col-12">
                                <label for="query" class="col-auto">Sql Query</label>
                                <div class="col field-input">
                                    <input type="text" value="<?= isset($set) ? $set->query : '' ?>" class="form-control" name="query" id="query" placeholder="Field Sql Query">
                                </div>
                            </div>
                            <div class="form-group row col-12">
                                <label for="types" class="col-auto">Field Type</label>
                                <div class="col field-input">
                                    <input type="text" value="<?= isset($set) ? $set->type : '' ?>" class="form-control" name="type" id="type" placeholder="text, num or date">
                                </div>
                            </div>
                            <div class="form-group row col-12">
                                <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                                    <input type="checkbox" value="1" <?= (isset($set) && $set->af == '1') ? 'checked' : '' ?> class="custom-control-input" name="af" id="af<?php echo $i ?>">
                                    <label class="custom-control-label checkbox-left text-left" for="af<?php echo $i ?>">Accumulative field</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <?php echo (isset($set)) ? 'Edit Field' : 'Add Field'; ?>
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
