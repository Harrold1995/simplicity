
<div class="modal fade entities-modal" id="entitiesModal" tabindex="-1" role="dialog" doc-type="entities" main-id=<?= isset($entities) && isset($entities->id) ? $entities->id : '-1' ?> type="entities" ref-id="" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
    <div id="root" class="no-print">
      <div class="modal-content  text-primary  popup-a form-entry shown" style="padding: 25px;max-height:500px;">
         <form action="<?php echo $target; ?>" method="post"type="entities">
            <header class="modal-h">
              <h2 class="text-uppercase">Entities</h2>
                <nav>
                  <ul>
                      <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                      <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                      <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                  </ul>
              </nav>
              <nav>
                <ul>
                  <li><a href="#" onclick="JS.openDraggableModal('entities', 'edit', <?= $entities->id -1?>);"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
                    <li><a href="#" onclick="JS.openDraggableModal('entities', 'edit', <?= $entities->id +1?>);"><i class="icon-chevron-right"></i> <span>Next</span></a></li>        
                  <li><?= isset($entities) ? '<a href="entitiess/deleteentities/'.$entities->id.'" class="deleteButton mr-auto"><i class="icon-trash"></i><span>Delete</span></a>' : '' ?></li>
                  <li class="get_send_email_form"><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
                  <li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
                  <li class="getDocuments"><a href="#" class="uploadDocument"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
                  <li><a href="#" id="printId"><i class="icon-print"></i> <span>Print</span></a></li>
                </ul>
              </nav>
            </header>
            <section class="plain modal-body" style="border-style: none; box-shadow:none;">
                
                        <div>
                          <h2 class="header-a text-center">General</h2>
                          <ul id="setupprint" class="list-input m30 plain">

                            <li><label for="name">Entities Name:</label> <input type="text" value="<?= isset($entities) && isset($entities->name) ? $entities->name : '' ?>" name="entities[name]" id="name" class="inputStyle" placeholder="Enter Name">
                            </li>

                            <li>
                              <label for="address">Address:</label> 
                              <input value="<?= isset($entities) && isset($entities->address) ? $entities->address : '' ?>"  id="address" name="entities[address]" class="inputStyle">
                            </li>

                            <li>
                              <label for="address">City:</label> 
                              <input value="<?= isset($entities) && isset($entities->city) ? $entities->city : '' ?>"  id="address" name="entities[city]" class="inputStyle">
                            </li>
                            <li>
                            <label for="address">State:</label> 
                              <input value="<?= isset($entities) && isset($entities->state) ? $entities->state : '' ?>"  id="address" name="entities[state]" class="inputStyle">
                            </li>
                            <li>
                            <label for="address">Zip:</label> 
                              <input value="<?= isset($entities) && isset($entities->zip) ? $entities->zip : '' ?>"  id="address" name="entities[zip]" class="inputStyle">
                            </li>

                            <li><label for="tax_id">Tax Id:</label> <input type="number" value="<?= isset($entities) && isset($entities->tax_id) ? $entities->tax_id : '' ?>" name="entities[tax_id]" id="tax_id" class="inputStyle" placeholder="">
                            </li>

                           <li>
                              <label for="closing_date">Closing Date:</label> 
                              <input data-toggle="datepicker" value="<?= isset($entities) && isset($entities->closing_date) ? $entities->closing_date : '' ?>"  id="closing_date" name="entities[closing_date]" class="inputStyle leaveEmpty">
                            </li>
                    
                            <!-- <li>
                              <span class="label">Active:</span>
                              <label for="active" class="checkbox < ?= isset($entities) && ($entities->active == 1) ? 'active' : '' ?>< ?php if($target == "entitiess/addentities")echo 'active'; ?>">
                                <input type="hidden" name="entities[active]" value="0" />
                                <input type="checkbox"  value="1" < ?= isset($entities) && ($entities->active == 1) ? 'checked' : '' ?>< ?php if($target == "entitiess/addentities")echo 'checked'; ?> id="active" name="entities[active]"  class="hidden" aria-hidden="true">
                                 
                              </label>
                            </li> -->                              
                           </ul>
                           
                        </div>

              <p class="m35">
                <label for="description">Description:</label>
                <input  data="text" value="<?= isset($entities) && isset($entities->description) ? $entities->description : '' ?>" name="entities[description]" id="description" placeholder="Enter Description">
              </p>           
            </section>
                    
                  
                
            <footer>
              <ul class="list-btn">
                <li><button type="submit" after="mnew">Save &amp; New</button></li>
                <li><button type="submit" after="mclose">Save &amp; Close</button></li>
                <li><button type="submit" after="duplicate">Duplicate</button></li>
                <li><button type="button">Cancel</button></li>
                
              </ul>
              <ul>
                <li>Last Modified 12:22:31 pm 1/10/2018</li>
                <li>Last Modified by User</li>
              </ul>
            </footer>

          </form>

      </div>
    </div>
  </div>
</div>
