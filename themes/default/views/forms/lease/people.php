<div id="wrapTableDivStyle">
<table class="tableGridTable" id="peoplesstable">
    <thead>
        <tr role="row">
            <th width="7%" class="text-center"></th>
            <th width="7%" class="text-center">Amount</th>
            <th width="7%" class="text-center">Name</th>
            <th width="7%" class="text-center">Security Deposit</th>
            <th width="7%" class="text-center">Last Months Rent</th>
            <th width="7%" class="text-center">Unit</th>
            <th width="7%" class="text-center">Start Date</th>
            <th width="7%" class="text-center">End Date</th>
            <th width="7%" class="text-center">Memo</th>
            <th width="7%" class="text-center">
                <a href="#" class="add"><i class="icon-plus-circle" aria-hidden="true"></i>
                    <span>Add</span></a>
            </th>
        </tr>
    </thead>
    <tbody>
    <?php if (!$lease->ttls) {?>
            <tr class="editing" id ="addtenantInstant">
                    <td></td>
                                    
                    <td>

                        
                    </td>
                    <td>              
                        <span class="select">
                        <label for="profile_id" class="hidden">Label</label>
                        <select stype="profile2" class="fastEditableSelect es-setup" key="tenant.name" modal="tenant" id="profile_id" name="temp[profile_id]">
                        <!-- < ?php
                            foreach ($profiles as $profile) {
                                echo '<option value="' . $profile->id . '">' . $profile->first_name . " " . $profile->last_name . '</option>';
                            } ?> -->
                        </select>
                        </span>
                    </td>
                    <td><a type ='button' class="btn" id="addtenanttolease1">Add Tenant To Lease</a></td>
                    <td></td>
                    <td></td>
                    <td></td>

            </tr>
    <?php }?>
    </tbody>
</table>
</div>

<script type="text/javascript">
    var template = function (id, data = {}) {
        var newRow = '<tr index="' + id + '" ' + (data.id ? 'rid="' + data.id + '"' : '') + ' rtype="tenanttolease" edit-mode="modal">' +
            '<td class="text-center"><i class="icon-user" aria-hidden="true"></i></td>' +
            '<td class="text-center">$' + number_format(data.amount) + '</td>' +
            '<td class="text-center">' + data.name + '</td>' +
            '<td class="text-center">$' + number_format(data.deposit) + '</td>' +
            '<td class="text-center">$' + number_format(data.last_month) + '</td>' +
            '<td class="text-center">' + data.unit + '</td>' +
            '<td class="text-center">' + (data.move_in == null ? "" : data.move_in) + '</td>' +
            '<td class="text-center">' + (data.move_out == null ? "" : data.move_out) + '</td>' +
            '<td class="text-center">' +  (data.memo == null ? "" : data.memo) + '</td>' +
            '<td class="text-center"><a href="#" class="delete"><i class="icon-x"></i></a></td>' +
            '</tr>';
        return newRow;
    };
    var parseData = function (json) {
        var data = {id: json.id, amount: json.amount, name: json.profile_id_text, deposit: json.deposit, last_month: json.last_month, unit: json.unit_id_text, move_in: json.move_in, move_out: json.move_out, memo: json.memo};
        return data;
    };

    var newFunction = function(that, grid){
        if($(that).closest('.modal').find("#unit_id").closest('.select').find('input[type=hidden]').val() > 0){
            JS.openDraggableModal('tenanttolease', 'add', null, $(that), {
                property: $(that).closest('.modal').find("#property_id").closest('.select').find('input[type=hidden]').val(),
                unit: $(that).closest('.modal').find("#unit_id").closest('.select').find('input[type=hidden]').val(),
                lease: $(that).closest('.modal').find("#lease_id").val(),
                tableGrid: true }, [{
                event: 'postsubmit',
                function: function (e, data) {
                    grid.addRowModal(data);
                }
            }]);
        }else{
            JS.showAlert('danger','No unit selected');
        }
    };

    var table = $('.modal').last().find('#peoplesstable').tableGrid({
        template: template,
        mode: 'modal',
        name: 'tenanttoleases',
        parseData: parseData,
        newFunction: newFunction,
        data: <?php echo $lease && $lease->ttls ? json_encode($lease->ttls) : 0 ?>
    });

</script>