<div class="modal fade report-modal hide" id="propertyModal" tabindex="-1" role="dialog" main-id=<?= isset($property) && isset($property->id) ? $property->id : '-1' ?> type="property" aria-hidden="true" style="left: -325px;">
        <div class="modal-dialog modal-dialog-centered modal-lg " role="document">
            <div id="root">
                <div class="modal-content text-primary popup-a form-entry shown" style="width:140%; padding: 35px;">
                    <!--form action="< ?php echo $target; ?>" method="post"-->
                    <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="property">

                        <header class="modal-h ui-draggable-handle">
                            <h2 class="text-uppercase" id="title2"><span><?php echo $title; ?></span></h2>
                            <nav>
                                <ul class="">
                                    <!-- <li><a href=""><i class="icon-chevron-left"></i> <span>Previous</span></a></li> -->
                                    <li><a href="#" class="switchModal" dir="prev"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
                                    <!-- <li><a href="./"><i class="icon-chevron-right"></i> <span>Next</span></a></li> -->
                                    <li><a href="#" class="switchModal" dir="next"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
                                    <li><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
                                    <li><a id = "exportocsvR" href="#"><i id = "exportocsvR" class="icon-excel"></i> <span>Excel</span></a></li>
                                    <!--<li id = 'printIdR'><a class="print printModal" href="#"><i id = 'printIdR' class="icon-print"></i> <span>Print</span></a></li>-->
                                    <li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
                                    <li><a href="./"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
                                    <li><a class="print printModal" href="#"><i class="icon-print"></i> <span>Print</span></a></li>
                                    <li><a href="#" data-dismiss="modal" aria-label="close"><i class="icon-x-thin"></i> <span>Close</span></a></li>
                                </ul>
                            </nav>
                        </header>

                        <table class="table-bordered permissions" id="permissions">

                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center">Admin (Administrator) Group Permissions</th>
                                </tr>
                                <tr>
                                    <th class="text-center">General Access</th>
                                    <th colspan="5">
                                    <?php foreach ($general as $key => $value) { 
                                        if ($key == 'id' || $key == 'group_id') continue;
                                        $title = explode('_', $key);
                                    ?>
                                    <span class="custom-control custom-checkbox form-group p-standard general">
                                        <input type="hidden" name="<?=$key?>" value="0"/>
                                        <input type="checkbox" name="<?=$key?>" value="1" class="custom-control-input" id="<?=$key?>" <?=($value== 1) ? 'checked' : ''; ?>>
                                        <label class="custom-control-label" for="<?=$key?>"><?=ucfirst($title[0])?></label>
                                    </span>
                                    <?php } ?>
                                    </th>
                                </tr>

                                <tr >
                                    <th rowspan="2" class="text-center">Module Name</th>
                                    <th colspan="5" class="text-center">Permissions</th>
                                </tr>
                                
                                <tr>
                                    <th class="text-center">View</th>
                                    <th class="text-center">Add</th>
                                    <th class="text-center">Edit</th>
                                    <th class="text-center">Delete</th>
                                    <th class="text-center">Miscellaneous</th>
                                </tr>
                                </thead>
                                
                                <tbody>

                                <?php $isdelete=5;foreach ($permissions as $key => $value) { 
                                        if ($key == 'id' || $key == 'group_id') continue;                                        
                                        $title = explode('_', $key);
                                        if($header != $title[0]) {
                                            if($isdelete < 5) {echo  "<td colspan='".(5-$isdelete)."'>";$isdelete = 5;}
                                            if(isset($header)) echo '<tbody>';
                                            echo '<tr><th colspan="6" class="text-center">' . ucfirst($title[0]) . '</th></tr>';
                                        }
                                        $header = $title[0];
                                ?>  
                                
                                <?php if($module != $title[1]) {
                                    if($isdelete < 5) echo  "<td colspan='".(5-$isdelete)."'>";
                                    echo '<tr><td>' . ucfirst($title[1]) . '</td>';
                                    $i = 0;
                                    $isdelete = 0;
                                }
                                    $module = $title[1];
                                ?>  
                                <?php    
                                    if($title[2] == 'view' || $title[2] == 'add'|| $title[2] == 'edit' || $title[2] == 'delete') {
                                    echo '<td class="text-center">
                                            <div class="custom-control custom-checkbox form-group p-standard">
                                                <input type="hidden" name="'.$key.'" value="0"/>
                                                <input type="checkbox" value="1" class="custom-control-input" name="'.$key.'" id="'.$key.'" ' . ($value== 1 ? 'checked' : '') .' >
                                                <label class="custom-control-label p-standard-label" for="'.$key.'"></label>
                                            </div>
                                        </td>';
                                        $isdelete++;
                                } else {
                                    echo ($i == 0) ? "<td colspan='".(5-$isdelete)."'>" : "";
                                    $isdelete = 5;
                                    echo '<span class="custom-control custom-checkbox form-group p-standard">
                                            <input type="hidden" name="'.$key.'" value="0"/>
                                            <input type="checkbox" value="1" class="custom-control-input" name="'.$key.'" id="'.$key.'" ' . ($value== 1 ? 'checked' : '') .' >
                                            <label class="custom-control-label" for="'.$key.'">' . ucfirst($title[2]) . '</label>
                                        </span>';
                                    $i = 1;
                                }   
                                        
                                ?>                                                                           

                                <?php } ?>


                                </tbody>
                            </table>

                        	<footer class="mt-5 mb-5">
                                <ul class="list-btn ">
                                    <li><button type="submit" after="mclose">Save</button></li>
                                    <li><button type="button">Cancel</button></li>
                                </ul>
                                <!--ul>
                                    <li>Last Modified 12:22:31 pm 1/10/2018</li>
                                    <li>Last Modified by <a href="./">User</a></li>
                                </ul-->
                            </footer>
    
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#permissions span.custom-control.general input').trigger('change');
    </script>