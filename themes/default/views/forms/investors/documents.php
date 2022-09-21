<ul id="notesprint" class="list-notes" >
            <?php foreach($documents as $document) { ?>
            <li>
                <header class="mt-1">
                    <h3>Document</h3>
                    <ul>
                        <li>Mary Jane </li>
                        <li>1/1/2018</li>
                    </ul>
                </header>
                <p><a href="<?php echo $document->href; ?>" target="_blanc"><?php echo $document->name; ?></a></p>
                <ul class="list-square">
                    <li><a href="./"><i class="icon-envelope-outline2"></i> <span>Share</span></a></li>
                    <li><a href="./" class="print"><i class="icon-print"></i> <span>Print</span></a></li>
                </ul>
            </li>
            <?php } ?>
</ul>




