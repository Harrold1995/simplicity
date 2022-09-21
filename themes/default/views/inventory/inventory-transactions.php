<div id="DataTables_Table_7_wrapper" class="table-wrapper table-b-wrapper rightslick-table">

</div>
<script>
    var ItemId = <?php echo $typeId ?>;
    var filter ="t.item_id";
    var rightSlick = new SlickRight('.rightslick-table', {key: filter, dataUrl: "transactions1/getTransactionsDTData2/" + ItemId + "/" + filter});
</script>