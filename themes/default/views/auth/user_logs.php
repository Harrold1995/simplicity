<div class="modal fade " tabindex="-1" role="dialog" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <!--<div id="infoMessage"><?php echo $message; ?></div>-->
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $title; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row mt-3">
                            <div class="col">
                              <?php foreach ($logs as $log) { ?>
                                <div style="width:100%;position:relative;display:flex;flex-direction:row;padding: 10px 0;border-bottom:1px solid rgba(0,0,0,0.05)" >
                                    <div style="width:150px;padding-top:4px;opacity:0.5">
                                        <?php
                                            echo $log->time;
                                        ?>
                                    </div>
                                    <div style="width:100%;font-size: 14px;">
                                        <?php
                                            echo $log->get_data_text();
                                        ?>
                                    </div>
                                </div>
                              <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>
