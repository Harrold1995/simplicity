<div class="modal fade email-modal" id="emailModal" tabindex="-1" role="dialog" main-id='-1' type="email" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md " role="document" style="width: 500px;">
		  <div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; height:600px;">
            <form style= "top:40px; position: initial; bottom:0; right:0; box-shadow: initial; width: 460px;"
                    id="emailForm" action="<?php echo $target; ?>" method="post" class="form-fixed chargeForm" type="email"
                    formType=""><!-- formType used for js reload-->
       
                  <header class="modal-h">
					<h2 class="text-uppercase" style ="margin:0px; "><?php echo $title; ?></h2>
                    <nav>
                        <ul>
                            <li><span class="buttons" style=""><span class="min" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">_</span></span> </li>
                            <li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
                            <li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
                        </ul>
                    </nav>
		
				</header>

       

              <div class ="has-table-c">
				<table>
					<thead class="dataTables_scrollHead">
						<tr>
						</tr>
					</thead>
					<tbody>
                    <?php $userId = $this->ion_auth->get_user_id(); ?>
                                     <br>
                                     <h5>select recipients by</h5>
                                     <p>
                                         <ul class = 'toggleInputsul'>
                                           <li class = 'toggleInputs chosen' data-toggle = "profile"> <i class="icon-user" style = 'font-size: 45px'></i><br> Tenant</li>
                                           <li class = 'toggleInputs' data-toggle = "property" ><i class="icon-city" style = 'font-size: 45px'></i><br>Property</li>
                                         </ul>

                                       
</p>
                                    <p>Tenants
                                       <input class = 'condinput' id="profile" name="tenants" value="<?= isset($profiles) ? $profiles : '' ?>"/>
                                    </p>
                                    <p style ='display:none'>Properties
                                       <input class = 'condinput'  id="property" name="properties" value="<?= isset($properties) ? $properties : '' ?>"/>
                                    </p>

                                    <p>Template
                                        <span id="leasesSpan">
                                        <label for="lease_id"></label>
                                            <span class="select">
                                            <select class="form-control editable-select" id="template_id" name="template_id">
                                            <option value="plain">Plain text</option>
                                            <option value="basic">Basic HTML</option>
                                                </select>
                                            </span>
                                        </span><span id="formNames"></span>
                                    </p>
                                    
                                    <!--p>Date<span  id="dateSpan">
                                        <label for="charge_date"></label>
                                        <input data-toggle="datepicker"  id="charge_date" name="header[transaction_date]" value="<?php if($header->date) echo $header->date;?>">
                                                </span>
                                    </p-->
                                    <p>Subject
                                        <label for="subject"></label>
                                        <input type="text" id="amount" name="subject">
                                    </p>
                                    <p>body
                                        <label for="message"></label>
                                        <textarea id="message" name="message"></textarea>
                                    </p>
                                    <p><button type="submit" after="mclose" id="send">Send</button></p>
                                    <input type="hidden" name="recipients" id="recipients" value='profile'>

                    </tbody>
						  
					<tfoot>
						<tr>
						</tr>
					</tfoot>
					
				</table>
			</div>

        <footer>
        <?= $header ? 
        "<div>Last Modified $header->modified<div>
         <div>Last Modified by <a href='#!'>$header->user</a><div>" : '';
        ?>
         
        </footer>
                            </form>   
                    </div>
		</div>

</div>
</div>

<script>

             $('.condinput').each(function(){
                 that = this;
                 $(that).selectize({
                    persist: false,
                    maxItems: null,
                    valueField: 'id',
                    labelField: 'name',
                    searchField: 'name',
                    options: JS.sdata[that.id]
               })
            }); 
            
            $('.toggleInputs').click(function(e){
                  that = this;
                  modal = $(that).closest('.modal');
                  modal.find('.toggleInputs').each(function(){
                      if(this == that){
                          $(this).addClass('chosen');
                      } else {
                          $(this).removeClass('chosen');
                      }
                  });
                  modal.find('.condinput.selectized').each(function(){
                      if(this.id == $(that).attr('data-toggle') ){
                          $(this).closest('p').show();
                          $('#recipients').val(this.id);
                      } else {
                         $(this).closest('p').hide();
                      }
                  });
                  modal.find('#recipients').val($(that).attr('data-toggle'));
            });
</script>