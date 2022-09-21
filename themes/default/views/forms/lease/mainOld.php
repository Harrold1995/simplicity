<div class="modal fade property-modal" id="propertyModal" tabindex="-1" role="dialog" type="property" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form action="<?php echo $target; ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><?php echo $title; ?></h5>
                    <nav class="ml-auto">
                        <ul>
                            <?php if (isset($lease) && isset($lease->id))
                                echo '<li><a href="leases/printPdf/' . $lease->id . '" target="_blank"><i class="fa fa-print" aria-hidden="true"></i></a></li>';
                            ?>
                        </ul>
                        <button type="button" class="close ml-auto" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </nav>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row mt-3">
                            <div class="col-12 col-lg-5">

                                <div class="form-group row">
                                    <label for="property" class="col-auto">Property</label>
                                    <div class="col field-select">
                                        <select onchange="JS.loadList('api/getUnitsList',this.options[this.selectedIndex].value, '#unit_id', $(this).closest('.modal-body'))" class="form-control" id="property_id">
                                        <?php
                                        echo '<option value="0" ' . (!isset($unit) ? 'selected' : '') . '>-Select Property-</option>';
                                        foreach ($properties as $property) {
                                            echo '<option value="' . $property->id . '" ' . (isset($lease) && $lease->property_id == $property->id ? 'selected' : '') . '>' . $property->name . '</option>';
                                        } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="unit_id" class="col-auto">Unit</label>
                                    <div class="col field-select">
                                        <select class="form-control" id="unit_id" name="lease[unit_id]">
                                            <option value="0">None</option>
                                            <?php
                                            foreach ($units as $unit) {
                                                echo '<option value="' . $unit->id . '" ' . (isset($lease) && $lease->unit_id == $unit->id ? 'selected' : '') . '>' . $unit->name . '</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="late_charge" class="col-auto">Late Charge Setup <a href="#" class="editButton" modal="custom|leases/getLateChargeModal" data-id="<?=isset($lease) ? $lease->late_charge_setup_id: ''; ?>"><i class="fas fa-pen-square"></i></a></label>
                                    <div class="col field-select">
                                        <select id="late_charge" name="lease[late_charge_setup_id]" class="form-control editable-select set-up" key="latecharge.name" modal="custom|leases/getLateChargeModal" value = "<?=isset($lease) ? $lease->late_charge_setup_id: ''; ?>">
                                            <option value="0">None</option>
                                            <?php
                                            foreach ($lcsetups as $setup) {
                                                echo '<option value="' . $setup->id . '" ' . (isset($lease) && $lease->late_charge_setup_id == $setup->id ? 'selected' : '') . '>' . $setup->name . '</option>';
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="broker" class="col-auto">Broker</label>
                                    <div class="col field-select">
                                        <select class="form-control" id="broker" name="lease[broker]">
                                            <option value="1">John Doe</option>
                                            <option value="2">Jane Doe</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="lease_template" class="col-auto">Lease Template</label>
                                    <div class="col field-select">
                                        <select class="form-control" id="lease_template" name="lease[lease_template]">
                                            <option value="1">Standard Resedential</option>
                                            <option value="2">Custom Resedential</option>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <div class="col-12 col-md-9 col-lg-5">
                                <div class="form-group row">
                                    <label for="start" class="col-auto">Start Date</label>
                                    <div class="col field-input">
                                        <input class="form-control" type="date" id="date" value="<?= isset($lease) && isset($lease->start) ? $lease->start : '2018-05-02' ?>" name="lease[start]" id="start">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="end" class="col-auto">End Date</label>
                                    <div class="col field-input">
                                        <input class="form-control" type="date" value="<?= isset($lease) && isset($lease->end) ? $lease->end : '2018-05-02' ?>" name="lease[end]" id="end">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="amount" class="col-auto">Rent Amount</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= isset($lease) && isset($lease->amount) ? $lease->amount : '' ?>" class="form-control decimal" name="lease[amount]" id="amount" placeholder="$1,000.00">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="deposit" class="col-auto">Security Deposit</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= isset($lease) && isset($lease->deposit) ? $lease->deposit : '' ?>" class="form-control decimal" name="lease[deposit]" id="deposit" placeholder="$1,000.00">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="last_month" class="col-auto">Last Month's Rent</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= isset($lease) && isset($lease->last_month) ? $lease->last_month : '' ?>" class="form-control decimal" name="lease[last_month]" id="last_month" placeholder="$1,000.00">
                                    </div>
                                </div>

                            </div>
                            <div class="col-12 col-md-3 col-lg-2 mb-3 lg-mb-0">

                                <div class="text-center">
                                    <p class='file-name'><?= isset($lease) && $lease->original != '' ? '<a href="' . base_url() . 'uploads/documents/' . $lease->original . '" target="_blank">' . $lease->original . '</a>' : '' ?>
                                    <p></p>
                                </div>

                                <label class="btn btn-primary btn-block mt-3" onclick="$(this).next().trigger('click')">
                                    Attach Original
                                </label>
                                <input type="file" name="original" class="upload d-none form-control" id="p-image" targetimg="#original-lease">

                                <div class="row mt-4 mr-3">
                                    <div class="col-auto col-lg-12 custom-control custom-checkbox form-group">
                                        <input type='hidden' value='0' name='lease[pets]'>
                                        <input type="checkbox" <?= isset($lease) && ($lease->pets == 1) ? 'checked' : '' ?> value="1" class="custom-control-input" id="pets" name="lease[pets]">
                                        <label class="custom-control-label checkbox-right" for="pets">Pets</label>
                                    </div>
                                    <div class="col-auto col-lg-12 custom-control custom-checkbox form-group">
                                        <input type='hidden' value='0' name='lease[restrict_payments]'>
                                        <input type="checkbox" <?= isset($lease) && ($lease->restrict_payments == 1) ? 'checked' : '' ?> value="1" class="custom-control-input" id="restrict_payments" name="lease[restrict_payments]">
                                        <label class="custom-control-label checkbox-right" for="restrict_payments">Restrict Payments</label>
                                    </div>
                                    <div class="col-auto col-lg-12 custom-control custom-checkbox form-group">
                                        <input type='hidden' value='0' name='lease[active]'>
                                        <input type="checkbox" <?= isset($lease) && ($lease->active == 1) ? 'checked' : '' ?> value="1" class="custom-control-input" id="active" name="lease[active]">
                                        <label class="custom-control-label checkbox-right" for="active">Active</label>
                                    </div>
                                    <div class="col-auto col-lg-12 custom-control custom-checkbox form-group">
                                        <input type='hidden' value='0' name='lease[stabilized]'>
                                        <input type="checkbox" <?= isset($lease) && ($lease->stabilized == 1) ? 'checked' : '' ?> value="1" class="custom-control-input" id="stabilized" name="lease[stabilized]">
                                        <label class="custom-control-label checkbox-right" for="stabilized">Rent Stabilized</label>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12 col-xl lg-mb-3 row">
                                <label for="move_in" class="col-auto">Move In Date</label>
                                <div class="col field-input">
                                    <input class="form-control" type="date" value="<?= isset($lease) && isset($lease->move_in) ? $lease->move_in : '2018-05-02' ?>" name="lease[move_in]" id="move_in">
                                </div>
                            </div>

                            <div class="col-12 col-xl lg-mb-3 row">
                                <label for="move_out" class="col-auto">Move Out Date</label>
                                <div class="col field-input">
                                    <input class="form-control" type="date" value="<?= isset($lease) && isset($lease->move_out) ? $lease->move_out : '2018-05-02' ?>" name="lease[move_out]" id="move_out">
                                </div>
                            </div>

                            <div class="col-12 col-xl lg-mb-3 row">
                                <label for="holdover" class="col-auto">Holdover Rent Amount</label>
                                <div class="col field-input">
                                    <input type="text" value="<?= isset($lease) && isset($lease->holdover) ? $lease->holdover : '' ?>" class="form-control" name="lease[holdover]" id="holdover" placeholder="$1,000.00">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="memo" class="col-auto">Memo</label>
                            <div class="col field-input">
                                <textarea onkeyup="JS.textAreaAdjust(this)" class="form-control" id="memo" name="lease[memo]" rows="1"><?= isset($lease) && isset($lease->memo) ? $lease->memo : '' ?></textarea>
                            </div>
                        </div>

                        <ul class="nav nav-pills nav-fill" id="lease-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="people" data-toggle="pill" href="#people-tab" role="tab" aria-controls="people-tab" aria-selected="true">People</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="owners-tab" data-toggle="pill" href="#owners-tab" role="tab">Notes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="taxes-tab" data-toggle="pill" href="#taxes-tab" role="tab">Utilities</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="utilities-tab" data-toggle="pill" href="#utilities-tab" role="tab">Documents</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="insurance-tab" data-toggle="pill" href="#insurance-tab" role="tab">Cases</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="cases-tab" data-toggle="pill" href="#cases-tab" role="tab">Autocharges</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="people-tab" role="tabpanel" aria-labelledby="people">
                                <?php require_once VIEWPATH . 'forms/lease/people.php'; ?>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" after="mnew" class="btn btn-primary ">
                        Save & New
                    </button>
                    <button type="submit" after="mclose" class="btn btn-primary ">
                        Save & Close
                    </button>
                    <button type="button" class="btn btn-primary ">
                        Duplicate
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
    $( "#date" ).datepicker();
    $( "#date" ).click(function() {
  alert( "Handler for .click() called." );
});
</script>