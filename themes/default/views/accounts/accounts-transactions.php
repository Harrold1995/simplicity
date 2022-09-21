<div id="DataTables_Table_7_wrapper" class="table-wrapper table-b-wrapper rightslick-table accounts-trans">

</div>
<script>
    var ItemId = <?php echo $getSingleAccount->id ?>;
    var filter = "t.account_id";
    var rightSlick = new SlickRight('.accounts-trans', {key: filter, dataUrl: "transactions1/getTransactionsDTData2/" + ItemId + "/" + filter});

</script>