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
                                    <input type="text" value="<?= isset($report) ? $report->name : '' ?>" class="form-control" name="name" id="name" placeholder="Report Name">
                                </div>
                            </div>
                            <div class="form-group row col-12">
                                <div class="custom-control custom-checkbox hg-checkbox form-group mb-0">
                                    <input type="checkbox" value="1"  class="custom-control-input" name="system" id="system">
                                    <label class="custom-control-label checkbox-left text-left" for="system">System Report</label>
                                </div>
                            </div>
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
