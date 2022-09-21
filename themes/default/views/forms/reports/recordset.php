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
                                    <input type="text" value="<?= isset($set) ? $set->name : '' ?>" class="form-control" name="r[name]" id="name" placeholder="Recordset Name">
                                </div>
                            </div>
                            <div class="form-group row col-12">
                                <label for="query" class="col-auto">Sql Query</label>
                                <div class="col field-input">
                                    <textarea class="form-control" name="r[table]" id="query" placeholder="Recordset Sql Query"><?= isset($set) ? $set->table : '' ?></textarea>
                                </div>
                                <a class="btn col-auto sqlParse">Parse</a>
                            </div>
                            <div class="form-group row col-12">
                                <label for="query" class="col-auto">Modal Type</label>
                                <div class="col field-input">
                                    <input type="text" value="<?= isset($set) ? $set->modal_type : '' ?>" class="form-control" name="r[modal_type]" id="query" placeholder="Recordset Modal Type">
                                </div>

                            </div>
                            <div class="form-group row col-12">
                                <table class='parsedRows'>
                                    <tr>
                                        <th>Table</th>
                                        <th>Column</th>
                                        <th>Name</th>
                                        <th>Group Key</th>
                                        <th>Type</th>
                                        <th>Source</th>
                                        <th>Visible</th>
                                    </tr>
                                    <?php
                                    foreach($columns as $i => $column) {
                                        echo'<tr cid="'.$column->id.'" ckey="'.$column->table_name.'.'.$column->column_name.'"><td><input type="hidden" name="field['.$i.'][id]" value="'.$column->id.'"><input readonly name="field['.$i.'][table_name]" value="'.$column->table_name.'"></td>'.
                                            '<td><input readonly name="field['.$i.'][column_name]" value="'.$column->column_name.'"></td>'.
                                            '<td><input name="field['.$i.'][name]" value="'.$column->name.'"></td>'.
                                            '<td><input name="field['.$i.'][key_column]" value="'.$column->key_column.'"></td>'.
                                            '<td><input name="field['.$i.'][type]" value="'.$column->type.'"></td>'.
                                            '<td><input name="field['.$i.'][source]" value="'.$column->source.'"></td>'.
                                            '<td><div class="custom-control custom-checkbox form-group mb-0">'.
                                            '<input type="hidden" value="0" name="field['.$i.'][active]">'.
                                            '<input type="checkbox" '.($column->active ? 'checked' : '').' value="1" class="custom-control-input" name="field['.$i.'][active]" id="active">'.
                                            '<label class="custom-control-label checkbox-left text-left" for="active"></label>'.
                                            '</div></td><tr>';
                                    }
                                    ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" refresh="true" class="btn btn-primary">
                        <?php echo !($set) ? 'Add Recordset' : 'Edit Recordset'; ?>
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
