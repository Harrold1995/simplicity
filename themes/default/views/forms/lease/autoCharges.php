<table class="table tableGridTable" id="auto_chargetable">
    <thead>
        <tr role="row">
            <th>Name</th>
            <th>Auto Charge Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Next Trans Date</th>
            <th>Amount</th>
            <th>Item</th>
            <th>Frequency</th>
            <th>Auto</th>
            <th style="width:2%;"></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script type="text/javascript">
    var template = function (id, data = {}) {
        if(_.isEmpty(data)) data = {pname: '', profile_id:0, name: '', start_date: '', end_date: '', next_trans_date: '', amount: 0, item_type_id: 0, frequency:1, auto:1};
        var newRow = '<tr index="' + id + '" ' + (data.id ? 'rid="' + data.id + '"' : '') + ' edit-mode="inputrow">' +
            '<td class="text-center">' + data.pname + '<select name="autoCharges[' + id + '][profile_id]" tsource="tenants" default="' + data.profile_id + '"/></td>' +
            '<td class="text-center">' + data.name + '<input name="autoCharges[' + id + '][name]" type="hidden" value="' + data.name + '"/></td>' +
            '<td class="text-center">' + data.start_date + '<input class ="leaveEmpty" data-validation = "required" data-title ="Start Date" name="autoCharges[' + id + '][start_date]" data-toggle="datepickerh" type="hidden" value="' + data.start_date + '"/></td>' +
            '<td class="text-center">' + data.end_date + '<input class ="leaveEmpty" name="autoCharges[' + id + '][end_date]" data-toggle="datepickerh" type="hidden" value="' + data.end_date + '"/></td>' +
            '<td class="text-center">' + data.next_trans_date + '<input class ="leaveEmpty" name="autoCharges[' + id + '][next_trans_date]" data-toggle="datepickerh" type="hidden" value="' + data.next_trans_date + '"/></td>' +
            '<td class="text-center">' + data.amount + '<input name="autoCharges[' + id + '][amount]" data-title ="Amount" data-validation = "nonzero" type="hidden" value="' + data.amount + '"/></td>' +
            '<td class="text-center">' + data.iname + '<select name="autoCharges[' + id + '][item_type_id]" data-title ="Item" data-validation = "required" tsource="items" default="' + data.item_type_id + '"/></td>' +
            '<td class="text-center">' + data.fname + '<select name="autoCharges[' + id + '][frequency]" data-validation = "required" data-title ="Frequency" tsource="frequencies" default="' + data.frequency + '"/></td>' +
            '<td class="text-center"><input type="checkbox" id="auto" name="autoCharges[' + id + '][auto]" ' + (data.auto == '1' ? 'checked' : '') + '/></td>' +
            '<td class="text-center"><a href="#" class="delete"><i class="icon-x"></i></a></td>' +
            '</tr>';
        return newRow;
    };

    var tsources = {
        items: <?php echo json_encode($items); ?>,
        tenants: <?php echo json_encode($tenants_on_lease); ?>,
        frequencies: <?php echo json_encode($frequencies); ?>
    };

    var parseData = function (json) {
        var data = {pname: json.profile_id_text, profile_id: json.profile_id, name: json.name, start_date: json.start_date, end_date: json.end_date, next_trans_date: json.next_trans_date, amount: json.amount, iname: json.item_type_id_text, item_type_id: json.item_type_id, fname: json.frequency_text, frequency: json.frequency, auto: json.auto};
        return data;
    };

    var table = $('.modal').last().find('#auto_chargetable').tableGrid({
        template: template,
        mode: 'inputrow',
        name: 'autoCharges',
        parseData: parseData,
        tsources: tsources,
        data: <?php echo $auto_charges && count($auto_charges) ? json_encode($auto_charges) : 0 ?>
    });
    console.log( <?php echo json_encode($auto_charges) ?>);

</script>
      