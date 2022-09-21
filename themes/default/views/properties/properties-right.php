<?php if (isset($property) && isset($property->name)) $propertyName = $property->name;
$typeId = $property->id;
$filter = "property_id";
$pheader = "<h3>Property: ".$property->name."</h3>"; ?>
<div class="cols-b flex-header">
    <div class="quadruple a">
        <div class="module-info" property_id = "<?= isset($property) ? $property->id : '200'; ?>">
            <figure class="reportLinkMultcont" property_id = "<?= isset($property) ? $property->id : '200'; ?>">
                
                <div class = "figcontainer">
                <img  src="<?= isset($property) && $property->image != '' ? base_url() . "uploads/images/" . $property->image : 'themes/default/assets/images/property.png' ?>" alt="Placeholder" height="200px" width="200px" style="object-fit: cover; width:200px; height:150px !important; opacity: 0.5;">
                    <figcaption><span id="title2"><?= isset($property) ? $property->name : ''; ?></span>
                        <br>
                        <?= isset($property) ? $property->address : ''; ?>
                        <br>
                        <span><?= isset($property) ? $property->city : ''; ?>, <?= isset($property) ? $property->state : ''; ?> <?= isset($property) ? $property->zip : ''; ?></span>
                    </figcaption>
                    <figcaption class ="hover">
                        <?php
                           $month = date("m");
                           $year = date("Y");
                           $startDate;
                           $endDate;

                          if ($month >6) {
                              $startDate = '01/01/'.$year ;
                              $endDate = '06/30/'.$year ;
                            } else {
                                $startDate =  '07/01/'.($year-1);
                                $endDate =  '12/31/'.($year-1);
                            };
                         ?>
                        <Span  class = "form-entry"><label for="start_date">Start Date</label><input id ="start_date" type="text"  data-toggle="datepicker" value="<?= $startDate ?>" ></Span>
                        <Span class = "form-entry"><label for="end_date">end Date</label><input id ="end_date" type="text"  data-toggle="datepicker" value="<?= $endDate ?>"></Span>
                        <p class = "link-btn">
                            <a  class="reportLinkMult button" property_id = "<?= isset($property) ? $property->id : '200'; ?>"><?= isset($property) ? "Run Reports for ".$property->name : ''; ?></a>
                        </p>
                        
                        <br>
                        
                    </figcaption>
                </div>
                
            </figure>
            <ul class="list-details">
                <li class ="viewEntity clickable_stat" entity-id ="<?= isset($property->entity_id) ? $property->entity_id : ''?>"><span>Entity</span><?= isset($property) ? $property->entityName : ''; ?></li>
                <li><span>Default Bank:</span><?= isset($propertyBank) ? $propertyBank : ''; ?></li>
                <li class="overlay-c size-b">
                    <span>Balance A/O today</span><?= isset($bankBalance) ? '$' . number_format($bankBalance, 2) : '$0.00'; ?>
                </li>
            </ul>
        </div>
        <div class="module-info a">
            <ul class="list-details">
                <li class="overlay-c size-e">
                    <span>Current Rent Roll:</span><?= isset($getPropertyRentTotal) ? '$' . number_format($getPropertyRentTotal, 2) : '$0.00'; ?>
                </li>
            </ul>
            <ul class="list-counts">
                <li><span><?= isset($unitsCount) ? $unitsCount : ''; ?></span> Units</li>
                <li><span><?= isset($vacancyCount) ? $vacancyCount : '0'; ?></span> Vacancies</li>
                <li>
                    <span><?= isset($getFutureVacancyCount) ? $getFutureVacancyCount : '0'; ?></span> Leases expiring in the next 2 months
                </li>
            </ul>
        </div>
        <div class="module-info a">
            <ul class="list-details">
                <li><span>Property Manager:</span><?= isset($property) ? $property->manager : ''; ?></li>
                <li class="size-f"><span>Open Cases:</span> 7</li>
            </ul>
        </div>
        <div class="module-info a">
            <ul class="list-details">
                <li class="reportLink overlay-c size-e clickable_stat" data-id="33" title="" defaults="<?= isset($property) ? $property->id : '200'; ?>" class="">
                    <span>All In:</span> <?= isset($allIn) ? '$' . number_format($allIn, 2) : '$0.00'; ?>
                    <span>Investors In: <?= isset($investorIn) ? '$' . number_format($investorIn, 2) : '$0.00'; ?></span> 
                    <span>LTL : <?= isset($MortgagesTot) ? '$' . number_format($MortgagesTot, 2) : '$0.00'; ?></span>
                    </li>

                <li class="overlay-c size-e"><?= isset($ytdProfit) ? '$' . number_format($ytdProfit, 2) : '$0.00'; ?>
                    <span>YTD Profit</span></li>
            </ul>
        </div>
    </div>
    <aside>
        <ul class="list-box">
            <li>
                <a href="#" class="reportLink" data-id="7" title="" defaults="<?= isset($property) ? $property->id : '200'; ?>$$<?php echo date("m/d/Y") ?>">Rent Roll</a>
            </li>
            <li>
                <a href="#" class="reportLink" data-id="5" title="" defaults="<?= isset($property) ? $property->id : '200'; ?>$$<?php echo date('01/01/Y')?>|<?php echo date("m/d/Y") ?>">Profit &amp; Loss</a>
            </li>
            <li>
                <a href="#" class="reportLink" data-id="57" title=" " defaults="<?= isset($property) ? $property->id : '200'; ?>$$<?php echo date("Y-m-d") ?>">Balance Sheet</a>
            </li>
        </ul>
    </aside>
</div>

<div class="tabFunction flex-body flex-wrapper">
    <nav class="double center flex-header">
        <ul class="list-horizontal nav" id="property-tabs2" role="tablist">
            <li class="tablinks"><a href="#profile-tab" onclick="tabswitch(event, 'documents',$(this))">Documents</a>
            </li>
            <li class="tablinks" id="defaultOpen">
                <a href="#transactions-tab" onclick="tabswitch(event, 'transactions',$(this))">Transactions</a></li>
            <li class="tablinks">
                <a id="notesTab" href="#transactions-tab" onclick="tabswitch(event, 'notes',$(this))">Notes</a></li>
            <!--li class="tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'conversations',$(this))">Conversations</a></li>
            <li class="tablinks"> <a  href="#transactions-tab" onclick="tabswitch(event, 'statements',$(this))">statements</a></li-->
            <li class="tablinks"><a  href="#maintenance-tab" onclick="tabswitch(event, 'maintenance',$(this))">Maintenance</a></li>
        </ul>
        <ul class="list-square">
            
            <li id="editColumns" class="cpopup-trigger" data-target="#columnspopup"><i class="icon-grid"></i></li>
            <li id="exportopdf"><i class="icon-pdf"></i></li>
            <li id="exportocsv"><i id="exportocsv" class="icon-excel"></i></li>
            <li id="printId"><!--a href="./" class="print"--><i id="printId" class="icon-print"></i> <!--span>Print</span></a-->
            </li>
            <li><a href="#" id="addNote"><i class="icon-checklist"></i> <span>Checklist</span></a></li>

        </ul>
    </nav>

    <!-- Tab content -->

    <div id="transactions" class="active tabcontent defaultOpenTab">
        <?php require_once VIEWPATH . 'properties/properties-transactions.php'; ?>
    </div>

    <div id="documents" class="tabcontent" style="display:none">
        <?php require_once VIEWPATH . 'properties/properties-documents.php'; ?>
    </div>

    <div id="notes" class="tabcontent" style="display:none">
        <?php require_once VIEWPATH . 'properties/properties-notes.php'; ?>
    </div>

    <div id="statements" class="tabcontent" style="display:none">
        <?php require_once VIEWPATH . 'properties/properties-statements.php'; ?>
    </div>

    <div id="conversations" class="tabcontent" style="display:none">
        <?php require_once VIEWPATH . 'properties/properties-conversations.php'; ?>
    </div>

    <div id="maintenance" class="tabcontent" style="display:none">
        <?php require_once VIEWPATH . 'properties/properties-maintenance.php'; ?>
    </div>

</div>

<!-- for notes -->
<?php require_once VIEWPATH . 'forms/notes/note.php'; ?>

<script>
    $("#printId").click(function () {
        printPart();
    })
    $("#exportocsv").click(function () {
        exportTableToCSV('tran.csv');
    })

    body = $('.module-info');
  JS.datePickerInit(body);

</script>


