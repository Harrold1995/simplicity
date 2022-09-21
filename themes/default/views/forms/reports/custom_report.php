<div class="modal fade report-modal hide" id="propertyModal" tabindex="-1" role="dialog" main-id=<?= isset($property) && isset($property->id) ? $property->id : '-1' ?> type="property" aria-hidden="true" style="left: -325px;">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document" style ="max-width: 1200px">
            <div id="root">
                <div class="modal-content text-primary popup-a form-entry shown" style="padding: 35px; ">
                    <!--form action="< ?php echo $target; ?>" method="post"-->
                    <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="property">

                        <header class="modal-h ui-draggable-handle">
                            <h2 class="text-uppercase" id="title2"><span><?php echo $title; ?></span></h2>
                            <nav>
                                <ul class="">
                                    <!-- <li><a href=""><i class="icon-chevron-left"></i> <span>Previous</span></a></li> -->

                                    <li class="get_send_email_form"><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
                                    <li><a href="#"><i  class="icon-excel"></i> <span>Excel</span></a></li>
                                    <li id = 'printIdR'><a class="print printModal" href="#"><i id = 'printIdR' class="icon-print"></i> <span>Print</span></a></li>
                                    <li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
                                    <li><a href="./"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
                                    <!--li><a class="print printModal" href="#"><i class="icon-print"></i> <span>Print</span></a></li>
                                    <li><a class="refresh-modal" href="#"><i class="icon-zoom2"></i> <span>Refresh</span></a></li-->
                                    <li><a href="http://[::1]/Custom_styles/simplicity/reports/edit/<?php echo $id; ?>" target="_blank"><i class="icon-tools"></i> <span>customize</span></a></li>
                                    <li><a class="refresh-modal" href="#"><i class="icon-zoom2"></i> <span>Refresh</span></a></li>
                                    <li class="close2"><a href="#" data-dismiss="modal" aria-label="close"><i class="icon-x-thin"></i> <span>Close</span></a></li>
                                </ul>
                            </nav>
                        </header>

                        <section class="report-filters" style="border:none; padding:15px;">
                            <input type="hidden" id="record_types" value="<?php echo $mtype;?>">
                            <div class-"row">
                                <span>FILTERS</span>
                            <span class="float-right"><a href="#" class="cust-apply-button">Apply filters</a> | <a href="#" class="hide-button">Hide</a></span>
                            </div>
                            <div class="row shown" id="reports-filters">
                                <div class="col-auto filter-wrapper">
                                    <p>
                                        <label for="name72">property equals</label>
                                        <input type="text" autocomplete="off" sel-value="1" class=" es-input" name="filters[1][val]">                                        
                                        <input type="hidden" class="filtersel" value="=" name="filters[1][cond]">
                                        <input type="hidden" class="filtercond" name="filters[1][col]"value="properties.name">
                                        <input type="hidden" class="filtercond" name="filters[1][ttype]"value="text">
                                </p>
                                </div>
                                <div class="col-auto filter-wrapper">
                                    <p>
                                        <label for="date">Date</label>
                                        <input type="text" class="dinput"  id="date" onchange ="$(this).closest('form').find('#date2').val($(this).val()) ;" name="filters[2][val]" value="02/10/2019" autocomplete="new-password">                                        <input type="hidden" class="filtersel" value="67">
                                        <input type="hidden" class="filtercond" name="filters[2][cond]" value="<">
                                        <input type="hidden" class="filtercond" name="filters[2][col]"value="start">
                                        <input type="hidden" class="filtercond" name="filters[2][ttype]"value="date">
                                        <input type="hidden" class="dinput"  id="date2" name="filters[3][val]" value="02/10/2019" autocomplete="new-password">                                        <input type="hidden" class="filtersel" value="67">
                                        <input type="hidden" class="filtercond" name="filters[3][cond]" value=">">
                                        <input type="hidden" class="filtercond" name="filters[3][col]"value="end">
                                        <input type="hidden" class="filtercond" name="filters[3][ttype]"value="date">
                                        
                                        

                                    </p>
                                </div>
                                                            
                            
                                
                            </div>

                        </section>
                        <div id="report-header">
                         <h2 class="text-uppercase" style= "color: #878A89; text-align:center"><?php echo $title; ?></h2>

                         <?php foreach($fs as $filter) {
                                $def = array_shift($defaults);
                                ?>

                                        <h4 style= "color: #878A89; text-align:center"><?php echo $columns[$filter->column]['name'].":"; ?>
                                        <?php if($filter->condition == 3) {
                                            $defs = explode('|',$def);
                                            if($filter->dtype == 'date') echo  (($defs[0] != '') ? $defs[0] : $filter->fields[0]->name1) . '-' . (($defs[1] != '') ? $defs[1] : $filter->fields[0]->name2) ."</h4>";
                                            else echo '' . (($defs[0] != '') ? $defs[0] : $filter->fields[0]->name1) . '-' . (($defs[1] != '') ? $defs[1] : $filter->fields[0]->name2) . '</h4>';

                                        }else{
                                            $defs = explode('|',$def);
                                            echo  (($def != '') ? $def :  $filter->fields[0]->value) . '</h4>';
                                        }


                                           

                                         ?>


                           <?php } ?>

                           <?php $cid = 1;
                                        foreach($cs as $custom) {  if($custom->user == 'false' || $custom->user == false)
                                            echo'';
                                            else {?>
                                        
                                            <h4 style= "color: #878A89; text-align:center"><?php echo $custom->label; ?>:&nbsp<?php echo $custom->value; ?></h4>

                                <?php }} ?>
                                            </div>  

                        <input type="hidden" id="record_types" value="<?php echo $mtype; ?>"/>
                        <div id="reports-table" style ="padding-right: 30px;    padding-left: 30px; min-width:1100px">
                              <div class="lds-roller" style ="padding-top: 150px;  overflow:hidden;  padding-left: 500px; min-width:600px"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                        </div>
                        <footer>
                            <ul class="row mt-2" style="width:100%">
                                 <li class="col">Generated on: <?php echo date("Y-m-d H:i:s"); ?></li>
+                                <li class="col"><?php echo $this->session->userdata('first_name').$this->session->userdata('last_name'); ?></li>
                                <li class="col">SmartCity Software</li>
                            </ul>
                        </footer>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>

        var reports = new Reports('', '#reports-table', '', "#reports-filters",
            {
                base: '<?php echo base_url();?>',
                modal: true,
                id: <?php echo $id;?>,
                <?php if($id == '-1') echo 'settings: '.json_encode($settings).',' ?>
                type: <?php echo $settings->type ? $settings->type : $rtype;?>,
                settingsURL: 'reports/getSettings',
                ajaxTableURL: 'reports/getAjaxTable',
                ajaxTableHeaderURL: 'reports/getAjaxTableHeader'
            });



            
            $('.cust-apply-button').click( 
                
                function getReportData(){
                var form = $(this).closest('form');
                console.log(form[0]);
            
            $.post({
                    url: JS.baseUrl+"reports/customGetData",
                    processData: false,
                    data: new FormData(form[0]),
                    dataType: 'json',
                    contentType: false,
                    success: function (data) {
                        alert(data);
                    },
             });
             }
             ); 
            

          
            //$("#printIdR").click(function(){printPart();}) 
            //$("#exportocsvR").click(function(){exportTableToCSV("fileName");})
</script>