<?php if(isset($property) && isset($property->name)) $propertyName = $property->name;?> <!--used for notes-->
<div class="modal flexmodal1 fade company-settings-modal hide" id="propertyModal" tabindex="-1" role="dialog" main-id=<?= isset($property) && isset($property->id) ? $property->id : '-1' ?> type="companySettings" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <form action="<?php echo $target; ?>" method="post" enctype='multipart/form-data' type="companySettings">
                  
				  	<header ></header>
                       <header class="modal-h ui-draggable-handle">
                            <h2 class="text-uppercase"><span><?php echo $title; ?></span></h2>
                        
                            <!--ul class="list-btn ">
                                <li><a href="./">Purchse Closing Statement</a></li>
                                <li><a href="./">Sale Closing Statement</a></li>
                            </ul-->
							<nav>
								<ul>
									<li><span class="buttons" style=""><span class="min" style=";padding: 8px 20px;cursor: pointer;">_</span></span> </li>
									<li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
									<li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
								</ul>
							</nav>
							</header>

				<section class="b">
					<div class="double e m20">
						<div>
							<?php
							foreach ($companySettings as $k => $value) {
								echo '<p>
									<label for="' . $k . '">' . $k . '</label>
									<input type="text" id="' . $k . '" name="property[' . $k . ']" value="'. (isset($value)  ? $value : '').'">
								</p>';
								} ?>

							
						</div>
						<div>
														
						</div>
					</div>	
					
				</section>

				<footer>
					<ul class="list-btn ">
						<li><button type="submit" after="mnew">Save &amp; New</button></li>
						<li><button type="submit"  after="mclose">Save &amp; Close</button></li>
						 <!--< ?php if(strpos($target, 'add')){ ?>-->
							<li><button type="submit" after="duplicate">Duplicate</button></li>
						 <!--< ?php } ?>-->
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