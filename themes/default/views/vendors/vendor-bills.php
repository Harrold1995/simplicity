<div id="DataTables_Table_7_wrapper" class="table-wrapper table-b-wrapper rightslick-table  rightslick-table2">

</div>
<script>
    var ItemId = <?php echo $typeId ?>;
    var filter ="t.profile_id";
    var rightSlick = new SlickRight('.rightslick-table2', {key: filter + "bills", openonly: true, dataUrl: "transactions1/getTransactionsDTData2/" + ItemId + "/" + filter + "/bills"});
</script>