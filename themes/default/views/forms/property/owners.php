
<div id="wrapTableDivStyle">
<table class="tableGridTable" id="ownerstable">
      <thead >
        <tr>
          <th></th>
          <th>Percentage</th>
          <th>Name</th>
          <th>Address</th>
          <th>Email</th>
          <th>Phone</th>
          <th></th>
        </tr>

      </thead>
      <tbody>
  <tr class="editing">
              <td><i class="icon-user"></i><input type="hidden" name="temp[ind]" value="<?php echo count($owners);?>"></td>
                              
              <td>

                <span class="w60 has-input">
                  <label for="percentage" class="hidden">Label</label>
                  <input type="text" id="percentage" name="temp[percentage]" placeholder="% ">
                </span>
              </td>
              <td>              
                <span class="select">
                  <label for="profile_id" class="hidden">Label</label>
                   <select stype="profile" class="fastEditableSelect" key="profiles.first_name" modal="tenant" id="profile_id" name="temp[profile_id]">
                   <!-- < ?php
                    foreach ($profiles as $profile) {
                        echo '<option value="' . $profile->id . '">' . $profile->first_name . " " . $profile->last_name . '</option>';
                    } ?> -->
                  </select>
                </span>
              </td>
              <td><button class="btn " id="addOwner" source="tableapi/getProfileRow/owners">Add New Name</button></td>
              <td></td>
              <td></td>
              <td></td>

            </tr>


        <?php
          if (isset($owners))
              foreach ($owners as $owner) {
                echo '<tr role="row" id="' . $owner->owner_id . '" class="editOwner">
                      <td><i class="icon-user"></i>           
                      <input id="owner_id" name="owners[' . $owner->owner_id . '][id]" type="hidden" value="' . $owner->owner_id . '"/>
                      <input name="owners[' . $owner->owner_id . '][property_id]" type="hidden" value="' . $property->id . '"/>
                      </td>
                      <td class="dt-percentage"><input id="percentage" name="owners[' . $owner->owner_id . '][percentage]" type="hidden" value="' . $owner->percentage . '"/>' . $owner->percentage . '</td>
                      <td style="overflow: visible"><input id="profile_id" name="owners[' . $owner->owner_id . '][profile_id]" type="hidden" value="' . $owner->profile_id . '"/>' . $owner->first_name . ' ' . $owner->last_name . '</td>
                      <td class="ownerAddress">' . ucwords(rtrim($owner->address_line_1) . ', ' . (!empty($owner->address_line_2) ? rtrim($owner->address_line_2) . ', '  : '') . $owner->city) . ', ' . strtoupper($owner->state) . ' ' . $owner->area_code .'</td>
                      <td class="dt-email">' . $owner->email .' </td>
                      <td class="dt-phone">' . $owner->phone . '</td>
                      <td><a href="#" class="delete2"><i class="icon-x"></i></td>           
                    </tr>';
              }
        ?>

      </tbody>
    </table>
</div>