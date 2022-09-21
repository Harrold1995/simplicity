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

                        <div class="container">
                            <div class="row">
                                <div class="col-3" id="permission-properties">
                                    <div>
                                    <ul class="list-group">
                                        <li class="list-group-item active">Properties</li>
                                        <li class="list-group-item" >
                                            <div class="custom-control custom-checkbox form-group p-standard">
                                                <input type="checkbox" value="1" class="custom-control-input" <?php echo in_array(0, $ps) ? 'checked' : '';?> name="property[0]" id="all-properties">
                                                <label class="custom-control-label p-standard-label" for="all-properties"><em>*** All Properties</em></label>
                                            </div>
                                        </li>
                                        <?php
                                        foreach($properties as $property) {
                                            echo
                                            '<li class="list-group-item">
                                            <div class="custom-control custom-checkbox form-group p-standard">
                                                <input type="checkbox" value="1" class="custom-control-input properties" '.(in_array($property->id, $ps) ? 'checked' : '').' '.(in_array(0, $ps) ? 'disabled' : '').' name="property['.$property->id.']" id="property_'.$property->id.'">
                                                <label class="custom-control-label p-standard-label" for="property_'.$property->id.'">'. $property->name .'</label>
                                            </div>
                                        </li>';
                                        }
                                        ?>
                                        
                                    </ul>
                                    </div>
                                </div>
                            
                                <div class="col-9">
                                    <table class="table-bordered permissions" id="permissions">

                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="text-center">Name</th>
                                            <th colspan="5" class="text-center">Permissions</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">Enabled</th>
                                            <th class="text-center">View</th>
                                            <th class="text-center">Add</th>
                                            <th class="text-center">Edit</th>
                                            <th class="text-center">Delete</th>
                                        </tr>
                                    </thead>
                                        <tbody>
                                        <?php
                                            $options = ['enabled', 'view', 'add', 'edit', 'delete']; 
                                            foreach($allpermissions as $p) {
                                                echo'<tr class="parent">
                                                <td>'.$p['name'].'</td>';
                                                foreach($options as $o) {
                                                    echo'<td class="text-center">';
                                                    if(in_array($o, $p['options'])) 
                                                        echo'<div class="custom-control custom-checkbox form-group p-standard">
                                                                <input type="checkbox" value="1" class="custom-control-input" '.(in_array($p['key'].'_'.$o, $permissions) ? 'checked disabled' : (in_array($p['key'].'_'.$o, $upermissions) ? 'checked' : '').' name="'.$p['key'].'_'.$o.'"').'  id="name">
                                                                <label class="custom-control-label p-standard-label" for="name"></label>
                                                            </div>';
                                                    echo'</td>';
                                                }                                        
                                                echo'</tr>';
                                                foreach($p['data'] as $d) {
                                                    echo'<tr class="child" style="display:none;">
                                                    <td>'.$d['name'].'</td>';
                                                    foreach($options as $o) {
                                                        echo'<td class="text-center">';
                                                        if(in_array($o, $d['options'])) 
                                                            echo'<div class="custom-control custom-checkbox form-group p-standard">
                                                                    <input type="checkbox" value="1" class="custom-control-input" '.(in_array($d['key'].'_'.$o, $permissions) ? 'checked disabled' : (in_array($d['key'].'_'.$o, $upermissions) ? 'checked' : '').' name="'.$d['key'].'_'.$o.'"').' id="name">
                                                                    <label class="custom-control-label p-standard-label" for="name"></label>
                                                                </div>';
                                                        echo'</td>';
                                                    }                                        
                                                    echo'</tr>';
                                                } 
                                            }
                                        ?>
                                        
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        	<footer class="mt-5">
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
        $(document).ready(function(){
            $('.parent td').click(function(e){
                if(!$(this).is('td:first-of-type')) return true;
                $(this).parent().nextUntil('.parent').toggle();                
            });

            $('#all-properties').on('change', function() {
                if ($(this).is(':checked')) $('.properties').prop('checked', false).prop('disabled', true);
                else $('.properties').prop('disabled', false);
            })
        });

    </script>