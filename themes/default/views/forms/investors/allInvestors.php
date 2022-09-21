
<div class="modal fade investor-modal" id="investorModal" tabindex="-1" role="dialog" main-id=<?= isset($investor) && isset($investor->id) ? $investor->id : '-1' ?> type="investor" ref-id="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
            <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px; width: 450px; top: 8%; height: 86%;">
				  <!--form action="< ?php echo $target; ?>" method="post"-->
				  <!-- <form action="< ?php echo $target; ?>" method="post" enctype='multipart/form-data' type="investor"> -->
                  
				  	<header class="modal-h">
                      <!-- </header> -->
				<!-- <header style="z-index: 17;"> -->
					<h2><?php echo $title; ?></h2>
					<nav>
						<ul>
							<li><span class="buttons" style=""><span class="min" style=";padding: 8px 20px;cursor: pointer;">_</span></span> </li>
							<li><span class="buttons" style=""><span class="max" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">[ ]</span></span></li>
							<li><span class="buttons" style=""><span class="close2" style="border-left: 1px solid #ccc;padding: 8px 20px;cursor: pointer;">X</span></span> </li>
						</ul>
					</nav>
                </header>
                <div>
                    <form action="./" method="post" class="form-search double">
                        <ul class="list-square">
                        <li id = "exportocsvC"><i id = "exportocsvC"  class="icon-excel"></i> </li>             
                        <li class="a"><a id="addUnitButton" href="#addUnit"><i class="icon-plus"></i> <span>Add</span></a></li>
                        </ul>
                        <p>
                        <label for="fsa">Search</label>
                        <input type="text" id="psearch" name="fsa" >
                        <button type="submit">Submit</button>
                        <a href="#"><i id = "printIdD" class="icon-microphone"></i> <span>Record</span></a>
                        </p>
                    </form>
                </div>
				<section style="95%">
                    <table id="investors-table" class="clickable2" style="overflow: scroll;">
                        <tr>
                            <th>Name</th>
                            <th>City</th>
                            <th>Memo</th>
                        </tr>
                        <?php
                        if (isset($investors))
                            // foreach ($investors as $investor) {
                            //     echo '<tr data-id="' . $investor->id . '" data-type="investor">
                            //             <td>' . $investor->name . '</td>
                            //             <td>' . $investor->city . '</td>
                            //             <td>' . $investor->memo . '</td>
                            //         </tr>';

                                    for ($x = 0; $x <= 55; $x++) {
                                        echo '<tr data-id="' . $investors[0]->id . '" data-type="investor">
                                                <td>' . $investors[0]->name . '</td>
                                                <td>' . $investors[0]->city . '</td>
                                                <td>' . $investors[0]->memo . '</td>
                                                </tr>';
                            }
                        ?>
                    </table>
				</section>
				<!-- <footer class="last-child">
					<ul class="list-btn">
						<li><button type="submit" after="mnew">Save &amp; New</button></li>
						<li><button type="submit"  after="mclose">Save &amp; Close</button></li>
						<li><button type="submit" after="duplicate">Duplicate</button></li>
						<li><button type="button">Cancel</button></li>
					</ul>
					<ul>
						<li>Last Modified 12:22:31 pm 1/10/2018</li>
						<li>Last Modified by User</li>
					</ul>
                </footer> -->
                <!-- </form> -->
        </div>
	</div>
    </div>
</div>
