
<div id="wrapTableDivStyle">
<table class="tableGridTable" id="key_codestable">
      <thead >
        <tr>
          <th>Unit</th>
          <th>Area</th>
          <th>Code</th>
          <th>Active</th>
          <th></th>
        </tr>

      </thead>
      <tbody>

      </tbody>
    </table>
</div>
    <script>

var template = function (id, data = {}) {
        var newRow = '<tr index="' + id + '" ' + (data.id ? 'rid="' + data.id + '"' : '') + ' data-id="'+ data.id +'" data-type="key_codes" rtype="key_codes" edit-mode="inputrow">' +
            '<td class="text-center">' + (data.unit ? data.uname : "") + '<select name="key_codes[' + id + '][unit]" tsource="units" default="' + data.unit + '"/></td>' +
            '<td class="text-center">' + (data.area ? data.area : "") + '<input name="key_codes[' + id + '][area]" type="hidden"  value="' + data.area + '"/></td>' +
            '<td class="text-center">' + (data.key_code ? data.key_code : "") + '<input name="key_codes[' + id + '][key_code]" type="hidden"  value="' + data.key_code + '"/></td>' +
            '<td class="text-center"><input type="checkbox" id="active" name="key_codes[' + id + '][active]" ' + (data.active == '1' ? 'checked' : '') + '/></td>' +
            '<td class="text-center"><a href="#" class="delete"><i class="icon-x"></i></a></td>' +
            '</tr>';
        return newRow;
    };

    var parseData = function (json) {
        var data = {id: json.id, area: json.area, key_code: json.key_code, active: json.active};
        return data;
    };

    var table = $('.modal').last().find('#key_codestable').tableGrid({
        template: template,
        mode: 'inputrow',
        name: 'key_codes',
        parseData: parseData,
        tsources: tsources,
        //newFunction: newFunction,
        data: <?php echo $key_codes  && count($key_codes)? json_encode($key_codes) : 0 ?>
    });

</script>