
  <div id="wrapTableDivStyle">  
    <table class="tableGridTable" id="unitsstable">
    <thead>
        <tr role="row">
        <th width="7%" class="text-center"></th>
            <th width="7%" class="text-center">Unit2</th>
            <th width="7%" class="text-center">Floor</th>
            <th width="7%" class="text-center">Type</th>
            <th width="7%" class="text-center">SQ FT</th>
            <th width="7%" class="text-center">Description</th>
            <th width="7%" class="text-center">Market Rent</th>
            <th width="7%" class="text-center">Status</th>
            <th width="7%" class="text-center link-icon"><a href="#" class="add"><i class="icon-plus-circle  table-button"></i> <span>Add</span></a></th>
        </tr>
        </thead>

            <tbody style ="overflow:auto">
            </tbody>
    </table>
</div>

<script>

var propId = <?= isset($property) && isset($property->id) ? $property->id : '-1' ?>

var template = function (id, data = {}) {
        var newRow = '<tr index="' + id + '" ' + (data.id ? 'rid="' + data.id + '"' : '') + ' data-id="'+ data.id +'" data-type="unit" rtype="unit" edit-mode="modal">' +
            '<td class="text-center"><i class="icon-door" aria-hidden="true"></i></td>' +
            '<td class="text-center">' + (data.name ? data.name : "")+ '</td>' +
            '<td class="text-center">' + (data.floor ?  data.floor : "") + '</td>' +
            '<td class="text-center">' + (data.unit_type_name ? data.unit_type_name : "") + '</td>' +
            '<td class="text-center">' + (data.sq_ft ? data.sq_ft : "") + '</td>' +
            '<td class="text-center">' + (data.memo ? data.memo : "") + '</td>' +
            '<td class="text-center">' + (data.market_rent ? data.market_rent : "") + '</td>' +
            '<td class="text-center">' + (data.status ? data.status : "") + '</td>' +
            '<td class="text-center"><a href="#" class="delete"><i class="icon-x"></i></a></td>' +
            '</tr>';
        return newRow;
    };

    var parseData = function (json) {
        console.log(json);
        var data = {id: json.id, name: json.name, floor: json.floor, unit_type_name: json.unit_type_name, sq_ft: json.sq_ft, memo: json.memo, market_rent: json.market_rent, move_out: json.move_out, status: json.status};
        return data;
    };
/*     var newFunction = function(that, grid){
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
        data: < ?php echo $lease && $lease->ttls ? json_encode($lease->ttls) : 0 ?>
    }); */
    var newFunction = function(that, grid){
        console.log('newfunction');
            JS.openDraggableModal('unit', 'add', null, $(that).id, {
                property_name: $(that).closest('.modal').find('input[name="property[name]"]').val(),
                property_id: propId,
                tableGrid: true }, [{
                event: 'postsubmit',
                function: function (e, data) {
                    console.log('unit submitted')
                    console.log('data-returned');
                    grid.addRowModal(data);
                }
            }]);
            console.log('newfunction2');
    };

    var table = $('.modal').last().find('#unitsstable').tableGrid({
        template: template,
        mode: 'modal',
        name: 'unit',
        parseData: parseData,
        newFunction: newFunction,
        data: <?php echo $property && $property->units ? json_encode($property->units) : 0 ?>
    });

</script>
<!-- < ?php
    if (isset($property) && $property->units != null)
        foreach ($property->units as $unit) {
            echo "<tr data-id='" . $unit->id . "' id='" . $unit->id . "' data-type='unit' data-mode='edit' data-parent='true' role='row'>
                                            <td width='7%' class='text-center'><i class='icon-door'></i></td>
                                            <td width='7%' class='text-center'>" . $unit->name . "</td>
                                            <td width='7%' class='text-center'>" . $unit->floor . "</td>
                                            <td width='7%' class='text-center' value='" . $unit->unit_type_id . "'>" . $unit->unit_type_name . "</td>
                                            <td width='7%' class='text-center'>" . $unit->sq_ft . "</td>
                                            <td width='7%' class='text-center'>" . $unit->memo . "</td>
                                            <td width='7%' class='text-center'>" . $unit->market_rent . "</td>
                                            <td width='7%' class='text-center'>" . $unit->status . '</td>
                                            <td width="7%" class="text-center link-icon"><a href="units/deleteUnit/1"'.$unit->id.'" class="delete-row mr-auto"><i class="icon-x"></i></a></td>
                                        </tr>';			                                            
        }
    ?> -->