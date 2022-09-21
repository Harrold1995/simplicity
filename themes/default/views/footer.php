<?php $class = trim($this->router->fetch_class());?>
<?php if ($class != 'reports' && $class != 'batchreports') echo'</main>';?>
</div>




<script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/scripts.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<!--<script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/custom.js"></script>-->
<script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/mobile.js?v=<?php echo SCRIPT_VERSION;?>"></script>


<!-- Libraries -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://d3js.org/d3.v4.min.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.11/lodash.min.js"></script>
<!--<script src="https://raw.githubusercontent.com/vkiryukhin/vkthread/master/vkthread/vkthread.js"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>


<!-- Tables & Selects -->
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/jquery-editable-select.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/fast-editable-select.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/jquery.tablesorter.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/jquery.treetable.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/jquery.dataTables.min.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/dataTables.colReorder.min.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/jquery.calculadora.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/table.filter.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/colResizable-1.6.min.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/nested.tables.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/slick.right.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/slick.maintenance.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/selectize.min.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<!-- Miscellaneous -->
<script src="https://cdn.plaid.com/link/v2/stable/link-initialize.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/custom.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script>JS.baseUrl = '<?php echo base_url(); ?>';</script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/formatter.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/validation.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/formGrid.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/tableGrid.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/formGridSlick.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/slickModules.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<!--<script src="<?php echo base_url(); ?>themes/default/assets/js/formGrid1.js"></script>-->
<script src="<?php echo base_url(); ?>themes/default/assets/js/formTabs.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/bootbox.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/currency.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/datepicker.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/printChecks.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/formsJs.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="https://momentjs.com/downloads/moment.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/tooltipster/tooltipster.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/tooltipster/plugins/follower/tooltipster-follower.js"></script>


<!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script> -->

<!-- Class Specific -->
<script src="<?php echo base_url(); ?>themes/default/assets/js/bill.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/journalEntry.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<!--<script src="<?php //echo base_url(); ?>themes/default/assets/js/reconciliation.js"></script>-->
<script src="<?php echo base_url(); ?>themes/default/assets/js/reconciliation2.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/receivedPayments.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/payBill.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/editBill.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/deposit.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/applyRefund.js?v=<?php echo SCRIPT_VERSION;?>"></script>


<?=($class == 'documents') ? '<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script>' : ''?>
<?=($class == 'formbuilder') ? '<script src="'.base_url().'themes/default/assets/formeo/formeo.min.js"></script>' : ''?>
<?=($class == 'documents') ? '<script src="'.base_url().'themes/default/assets/js/plugins/documents.js?v='.SCRIPT_VERSION.'"></script>' : ''?>
<script src="<?php echo base_url(); ?>themes/default/assets/js/operative.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/reports.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/checkboxBlock.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/batchReports.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/batchReportsEditor.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/customReports.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/jquery.event.drag-2.3.0.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/slick/slick.core.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/slick/slick.autotooltips.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/slick/slick.formatters.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/slick/slick.editors.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/slick/slick.grid.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/slick/slick.groupitemmetadataprovider.js"></script>
<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/slick/slick.dataview.js"></script>
<!-- Search -->
<script src="<?php echo base_url(); ?>themes/default/assets/js/search.js?v=<?php echo SCRIPT_VERSION;?>"></script>

<!-- Custom Scripts -->


<script defer src="<?php  echo base_url(); ?>themes/default/assets/javascript/custom2.js?v=<?php echo SCRIPT_VERSION;?>"></script>
<script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/html2canvas.min.js"></script>
<script defer src="<?php echo base_url(); ?>themes/default/assets/javascript/feedback.min.js"></script>
<!--<script defer src="<?php echo base_url(); ?>themes/default/assets/js/modal-scripts.js"></script>-->

    <!--div for custom Menus -->
    <div id = "menucontainer" style ="display:none;"></div>

    <!--div to dump print data -->
    <div id = "Checkarea">

    </div>
    <div id = "page" style = "display:none;">
            <div id="print_header">                
                    <h1 id = "pageHeader"></h1> <span id="print_header_date"></span>                    
                    <h3 id = "accName"></h3>
            </div>	

            <div id="print_information">

            </div>
        </div>
    <!-- end div to dump print data -->
    <div style="
    text-align: center;
    width: 100%" class ="no-print">© <?php echo date("Y"); ?> Copyright Simpli-city Systems.</div>
</body>
</html>
<script src="https://cdn.cardknox.com/ifields/2.5.1905.0801/ifields.min.js"></script> 
<script>
  sessionStorage.setItem('company_name', '<?php echo $this->session->userdata('company_name'); ?>');
  sessionStorage.setItem('company_phone', '<?php echo $this->session->userdata('company_phone'); ?>');
  sessionStorage.setItem('company_email', '<?php echo $this->session->userdata('company_email'); ?>');
  sessionStorage.setItem('company_logo', '<?php echo $this->session->userdata('company_logo'); ?>');
  sessionStorage.setItem('user_id', '<?php echo $this->session->userdata('user_id'); ?>');
  sessionStorage.setItem('colors', '<?php echo $this->session->userdata('colors'); ?>');
  colors = sessionStorage.getItem('colors').split(';');
  console.log(colors);


  const rootEl = document.querySelector(':root');
  root.style.setProperty('--pink1', colors[0]);
  root.style.setProperty('--pink2', colors[1]);
  root.style.setProperty('--pink3', colors[2]);
  root.style.setProperty('--pink4', colors[3]);
  root.style.setProperty('--pink5', colors[4]);
  root.style.setProperty('--pink6', colors[5]);
</script>
