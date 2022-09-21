<div  class="table-wrapper table-b-wrapper rightslick-table rightslick-table2">
</div>
</article>
<script>

var ItemId = <?php echo $typeId ?>;
var filter =  <?php echo "'$filter'" ?> ;
var filters=[];
if ( filter == 'profile_id' && lid != 0 && lid != null){
       filters[0] = {'ItemId': lid, 'filter': 'lease_id'};
       filters[1] = {'ItemId': ItemId, 'filter': filter};
    }

//$.post("transactions1/getTransactionsDTData2/" + ItemId + "/" + filter + "/sd" , {filters}, function (result) {
    //var rightSlick = new SlickRight('.rightslick-table2', result.data, result.columns, {pheader: "<?php echo $pheader;?>", key: filter + "sd"});
//}, "JSON");

    var rightSlick = new SlickRight('.rightslick-table2', {pheader: "<?php echo $pheader;?>", data:filters, key: filter + "sd", dataUrl: "transactions1/getTransactionsDTData2/" + ItemId + "/" + filter + "/sd"});
</script>