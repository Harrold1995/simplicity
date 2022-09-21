<div id="DataTables_Table_7_wrapper" class="table-wrapper table-b-wrapper rightslick-table rightslick-table1">

</div>

</article>

<script>
    var ItemId = <?php echo $typeId ?>;
    var filter =  <?php echo "'$filter'" ?> ;    
    var lid = <?= (isset($lid) and $lid !== "") ? $lid : 0;?>;
    var filters=[];
    if ( filter == 'profile_id' && lid != 0 && lid != null){
        filters[0] = {'ItemId': lid, 'filter': 'lease_id'};
       filters[1] = {'ItemId': ItemId, 'filter': filter};
    }



    //$.post("transactions1/getTransactionsDTData2/" + ItemId + "/" + filter + "/" + (filter == 'profile_id' || filter == 'lease_id' ? 'tenant' : ''), {filters}, function (result) {
        //var rightSlick = new SlickRight('.rightslick-table1', result.data, result.columns, {pheader: "<?php echo $pheader;?>", key: filter});
    //}, "JSON");

    
    var rightSlick = new SlickRight($('.rightslick-table1').first(), {key: filter, data: filters, pheader: "<?php echo $pheader;?>", dataUrl: "transactions1/getTransactionsDTData2/" + ItemId + "/" + filter + "/" + (filter == 'profile_id' || filter == 'lease_id' ? 'tenant' : '')});
</script>