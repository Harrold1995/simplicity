<div class="modal flexmodal fade property-modal" id="propertyModal" tabindex="-1" role="dialog" main-id=<?= isset($property) && isset($property->id) ? $property->id : '-1' ?> type="property" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data'>
                <div class="modal-h">
                    <h5 class="modal-title" id="exampleModalLongTitle"><?php echo $title; ?></h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row mt-3">
                            <div class="col-12 col-lg-6">
                                <div class="form-group row">
                                    <label for="name" class="col-auto">Short Name</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= isset($property) && isset($property->name) ? $property->name : '' ?>" class="form-control" name="property[name]" id="name" placeholder="Name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address" class="col-auto">Address</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= isset($property) && isset($property->address) ? $property->address : '' ?>" class="form-control col-xs-10" name="property[address]" id="address" placeholder="Address">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-6 row">
                                        <label for="city" class="col-auto">City</label>
                                        <div class="col field-input">
                                            <input type="text" value="<?= isset($property) && isset($property->city) ? $property->city : '' ?>" class="form-control" name="property[city]" id="city" placeholder="City">
                                        </div>
                                    </div>
                                    <div class="col row">
                                        <label for="state" class="col-auto">State</label>
                                        <div class="col field-select">
                                            <select class="form-control" id="state" name="property[state]">
                                                <option value="1">NY</option>
                                                <option value="2">NJ</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col row">
                                        <label for="reference" class="col-auto">Zip</label>
                                        <div class="col field-input">
                                            <input type="text" value="<?= isset($property) && isset($property->zip) ? $property->zip : '' ?>" class="form-control" name="property[zip]" id="zip" placeholder="Zip">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col row">
                                        <label for="borough" class="col-auto">Borough</label>
                                        <div class="col field-select">
                                            <select class="form-control" id="borough" name="property[borough]">
                                                <option value="1">1</option>
                                                <option value="2">2</option>
                                                <option value="3">3</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col row">
                                        <label for="block" class="col-auto">Block</label>
                                        <div class="col field-input">
                                            <input type="text" value="<?= isset($property) && isset($property->block) ? $property->block : '' ?>" class="form-control" name="property[block]" id="block" placeholder="Block">
                                        </div>
                                    </div>
                                    <div class="col row">
                                        <label for="lot" class="col-auto">Lot</label>
                                        <div class="col field-input">
                                            <input type="text" value="<?= isset($property) && isset($property->lot) ? $property->lot : '' ?>" class="form-control" name="property[lot]" id="lot" placeholder="Lot">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-9 col-lg-4">
                                <div class="form-group row">
                                    <label for="date" class="col-auto">Date</label>
                                    <div class="col field-input">
                                        <input class="form-control" value="<?= isset($property) && isset($property->date) ? $property->date : '2018-04-30' ?>" type="date" name="property[date]" id="date">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="sq_ft" class="col-auto">Total Sq Footage</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= isset($property) && isset($property->sq_ft) ? $property->sq_ft : '' ?>" class="form-control" id="sq_ft" name="property[sq_ft]" placeholder="Sq. ft">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="status" class="col-auto">Status</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= isset($property) && isset($property->status) ? $property->status : '' ?>" class="form-control" name="property[status]" id="status" placeholder="Status">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="default_bank" class="col-auto">Default Bank</label>
                                    <div class="col field-select">
                                        <select class="editable-select form-control" data-live-search="true" id="default_bank" name="property[default_bank]">
                                            <option value="1">Chase</option>
                                            <option value="2">Capital One</option>
                                            <option value="3">Bank of America</option>
                                            <option value="4">City</option>
                                            <option value="5">Barclays</option>
                                            <option value="6">Discover</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-3 col-lg-2 mb-3 lg-mb-0">
                                <div class="p-4">
                                    <img class="rounded w-100" id="property-image" src="<?= isset($property) && $property->image != '' ? base_url() . "uploads/images/" . $property->image : 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22200%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20200%20200%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_162b2febc6d%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A10pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_162b2febc6d%22%3E%3Crect%20width%3D%22200%22%20height%3D%22200%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2274.4296875%22%20y%3D%22104.5%22%3E200x200%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' ?>" data-holder-rendered="true">
                                </div>
                                <label class="btn btn-primary btn-block mt-3" onclick="$(this).next().trigger('click')">
                                    Attach Photo
                                </label>
                                <input type="file" name="image" class="upload d-none form-control" id="p-image" targetimg="#property-image">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-10">
                                <div class="form-group row">
                                    <label for="objective" class="col-auto">Objective</label>
                                    <div class="col field-input">
                                        <input type="text" value="<?= isset($property) && isset($property->objective) ? $property->objective : '' ?>" class="form-control" name="property[objective]" id="objective" placeholder="Objective">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="memo" class="col-auto">Memo</label>
                                    <div class="col field-input">
                                        <textarea onkeyup="JS.textAreaAdjust(this)" class="form-control" id="memo" name="property[memo]" rows="1"><?= isset($property) && isset($property->memo) ? $property->memo : '' ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-2 row">
                                <div class="col-auto col-lg-12 custom-control custom-checkbox form-group">
                                    <input type='hidden' value='0' name='property[stabilized]'>
                                    <input type="checkbox" <?= isset($property) && ($property->stabilized == 1) ? 'checked' : '' ?> value="1" class="custom-control-input" id="stabilized" name="property[stabilized]">
                                    <label class="custom-control-label checkbox-right" for="stabilized">Rent Stabilized</label>
                                </div>
                                <div class="col-auto col-lg-12 custom-control custom-checkbox form-group">
                                    <input type='hidden' value='0' name='property[active]'>
                                    <input type="checkbox" <?= isset($property) && ($property->active == 0) ? '' : 'checked' ?> value="1" class="custom-control-input" id="active" name="property[active]">
                                    <label class="custom-control-label checkbox-right <?= isset($property) && ($property->active == 0) ? '' : 'active' ?>" for="active">Active</label>
                                </div>

                            </div>
                        </div>
                        <ul class="nav nav-pills nav-fill" id="property-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="units" data-toggle="pill" href="#units-tab" role="tab" aria-controls="units-tab" aria-selected="true">Units</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="owners" data-toggle="pill" href="#owners-tab" role="tab" aria-controls="owners-tab" aria-selected="false">Owners</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="taxes" data-toggle="pill" href="#taxes-tab" role="tab" aria-controls="taxes-tab" aria-selected="false">Taxes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="utilitiesb" data-toggle="pill" href="#utilities-tab" role="tab">Utilities</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="insurance" data-toggle="pill" href="#insurance-tab" role="tab">Insurance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="cases" data-toggle="pill" href="#cases-tab" role="tab">Cases</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="settings" data-toggle="pill" href="#settings-tab" role="tab">Settings</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="notes" data-toggle="pill" href="#notes-tab" role="tab">Notes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="documents" data-toggle="pill" href="#documents-tab" role="tab">Documents</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="units-tab" role="tabpanel" aria-labelledby="units"  >
                                <?php require_once VIEWPATH . 'forms/property/units.php'; ?>
                            </div>

                            <div class="tab-pane fade" id="owners-tab" role="tabpanel" aria-labelledby="owners">
                                <?php require_once VIEWPATH . 'forms/property/owners.php'; ?>
                            </div>

                            <div class="tab-pane fade" id="taxes-tab" role="tabpanel" aria-labelledby="taxes" data-id = '10'>
                                <?php require_once VIEWPATH . 'forms/property/taxes.php'; ?>
                            </div>

                            <div class="tab-pane fade" id="utilities-tab" role="tabpanel" aria-labelledby="utilities" data-id = '9'>
                                <?php require_once VIEWPATH . 'forms/property/utilities.php'; ?>
                            </div>

                            <div class="tab-pane fade" id="insurance-tab" role="tabpanel" aria-labelledby="insurance" data-id = '8'>
                                <?php require_once VIEWPATH . 'forms/property/insurance.php'; ?>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <?= isset($property) ? '<a href="properties/deleteProperty/'.$property->id.'" class="deleteButton mr-auto"><i class="fas fa-trash-alt"></i> Delete item</a>' : '' ?>
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

