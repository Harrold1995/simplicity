<table class="table tableGridTable" id="renewaltable">
    <thead>
        <tr role="row">
            <th class="text-center">Renewal Form</th>
            <th class="text-center">rent</th>
            <th class="text-center">SD</th>
            <th class="text-center">LMR</th>
            <th class="text-center">Start Date</th>
            <th class="text-center">End Date</th>
            <th class="text-center">Notes</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script type="text/javascript">
    var template = function (id, data = {}) {
        var newRow = '<tr index="' + id + '" ' + (data.id ? 'rid="' + data.id + '"' : '') + ' edit-mode="inputrow">' +
            '<td class="text-center">' + data.renewal_form + '<input name="renewal[' + id + '][renewal_form]" type="hidden" value="' + data.renewal_form + '"/></td>' +
            '<td class="text-center">' + data.rent + '<input name="renewal[' + id + '][rent]" type="hidden" value="' + data.rent + '"/></td>' +
            '<td class="text-center">' + data.sd + '<input name="renewal[' + id + '][sd]" type="hidden" value="' + data.sd + '"/></td>' +
            '<td class="text-center">' + data.lmr + '<input name="renewal[' + id + '][lmr]" type="hidden" value="' + data.lmr + '"/></td>' +
            '<td class="text-center">' + data.start_date + '<input name="renewal[' + id + '][start_date]" data-toggle="datepicker" type="hidden" value="' + data.start_date + '"/></td>' +
            '<td class="text-center">' + data.end_date + '<input name="renewal[' + id + '][end_date]" data-toggle="datepicker" type="hidden" value="' + data.end_date + '"/></td>' +
            '<td class="text-center">' + data.notes + '<input name="renewal[' + id + '][notes]" type="hidden" value="' + data.notes + '"/></td>' +
            '<td class="text-center"><a href="#" class="delete"><i class="icon-x"></i></a></td>' +
            '</tr>';
        return newRow;
    };

    var parseData = function (json) {
        var data = {renewal_form: json.renewal_form, rent: json.rent, sd: json.sd, lmr: json.lmr, start_date: json.start_date, end_date: json.end_date, notes: json.notes};
        return data;
    };

    var table = $('.modal').last().find('#renewaltable').tableGrid({
        template: template,
        mode: 'inputrow',
        name: 'renewal',
        parseData: parseData,
        data: <?php echo $renewal && count($renewal) ? json_encode($renewal) : 0 ?>
    });

</script>