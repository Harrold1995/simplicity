
<form action="./" method="post" class="form-search double">
            <ul class="list-square">
              <li class="a"><a id="addInventoryButton" href="#addInventory"><i class="icon-plus"></i> <span>Add</span></a></li>
            </ul>
            <p>
              <label for="fsa">Search</label>
              <input type="text" id="psearch" name="fsa" >
              <button type="submit">Submit</button>
              <a href="./"><i class="icon-microphone"></i> <span>Record</span></a>
            </p>
            <a href="#" class="activefilter"><i class="fas fa-ellipsis-v"></i></a>
          </form>
          <table class="tree-table clickable mobile-hide no-footer" id="DataTables_Table_3 inventory-table" role="grid" aria-describedby="DataTables_Table_3_info">
                    <thead>
                        <tr role="row">
                            <th>name</th>
                            <th>type</th>
                        </tr>
                    </thead>
                          <tbody>
                          <?php
                          if (isset($inventory))
                              foreach ($inventory as $item) {
                                  echo '<tr data-tt-id="'. $item->type .'-' . $item->id . '" data-id="' . $item->id . '" data-type="inventory">
                                              <td>' . $item->name . '</td>
                                              <td>' . $item->info . '</td> </tr>';
                                              //<td class="overlay-f">' . (isset($item->children) ? count($item->children) : '0') . '</td>
                                        // </tr>';
                                  echo $item->tree;
                              }
                          ?>
              </tbody>
                    </table>
