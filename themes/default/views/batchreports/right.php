<?php foreach($fields as $field) { ?>
    <li>
        <div class="field-wrapper">
                <label for="field<?php echo $field->id; ?>"><?php echo $field->name." ".$signs[$field->condition]; ?></label>
                <?php if($field->condition == 3) {
                    echo"<div class='row'>";
                    if($field->dtype == 'date') echo '<div class="col pr-0"><input type="text" class="dinput" fid="'.$field->id.'" id="name1-' . $field->id . '" name="name1" value=""></div> <div class="col-auto p-1">and</div> <div class="col pl-0"><input type="text" class="dinput" fid="'.$field->id.'"  id="name2-' . $field->id . '" name="name2" value=""></div>';
                    else echo '<input type="text" fid="'.$field->id.'" id="name1-' . $field->id . '" name="name1" value=""> <span>and</span> <input type="text" fid="'.$field->id.'" id="name1-' . $field->id . '" name="name2" value="">';
                    echo"</div>";
                }else
                    echo '<span><input type="text" '.($field->dtype == 'date' ? 'class="dinput"' : '').' '.($field->source ? 'source="'.$field->source.'"' : '').' fid="'.$field->id.'" id="name'. $field->id .'" name="name" value=""></span>';
                ?>
                <input type="hidden" class="fieldcond" value="<?php echo $field->condition ?>">
        </div>
    </li>
<?php } ?>