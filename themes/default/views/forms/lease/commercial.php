<section style="background-color: #efebeb; color: black; border: none; box-shadow: none;">
            <div class="double e m20">
                <div>
				    <p>
                      <span>
                         <label for="commercial_policy_num">Commercial Policy Num</label>
                         <input type="text" value="<?= isset($lease) && isset($lease->commercial_policy_num) ? $lease->commercial_policy_num : '' ?>" name="lease[commercial_policy_num]" id="commercial_policy_num">
                       </span>
					</p>
                </div>
            </div>
</section>
      