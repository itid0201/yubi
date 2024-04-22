  	<table class="setup_table" cellspacing="0" cellpadding="0" border="0" summary="">
      	<tbody>
		<tr>
			<th scope="row">사진 파일 업로드<input type="hidden" name="use_gallery_img" readonly value="true" /></th>
			<td>필수</td>
		</tr>         
          <tr><th scope="row">첨부파일 숨기기</th><td>
          <label>
				<select name="hidden_attach">
				  <option value="true" <?php echo ($module_config['hidden_attach']=='true'?'selected="selected"':'');?>>사용</option>
				  <option value="false" <?php echo ($module_config['hidden_attach']!='true'?'selected="selected"':'');?>>사용안함</option>
				</select>
            </label></td></tr>
<!--			<tr>
				<th scope="row">메인 이미지 사이즈</th>
				<td>
					<p>
						<label for="main_width_size">사이즈</label>
						<input type="text" name="main_width_size" class="number_only" value="<?php echo empty($module_config['main_width_size'])?324:$module_config['main_width_size'];?>" size="4" />px X 
						<input type="text" name="main_height_size" class="number_only" value="<?php echo empty($module_config['main_height_size'])?198:$module_config['main_height_size'];?>" size="4" />px						
					</p>					
				</td>
			</tr>-->
			<!--<tr>
				<th scope="row">목록 넓이 설정( 갤러리에서만 사용 )</th>
				<td>
					<p>
						<label for="">사진1개일때:</label>
						<input type="text" name="gallary_width_size" class="number_only" value="<?php echo empty($module_config['gallary_width_size'][0])?418:$module_config['gallary_width_size'][0];?>" size="4" />px X 
						<input type="text" name="gallary_height_size" class="number_only" value="<?php echo empty($module_config['gallary_height_size'][0])?184:$module_config['gallary_height_size'][0];?>" size="4" />px						
					</p>
					<p>
						<label for="">사진2개일때:</label>
						<input type="text" name="gallary_width_size" class="number_only" value="<?php echo empty($module_config['gallary_width_size'][1])?418:$module_config['gallary_width_size'][1];?>" size="4" />px X 
						<input type="text" name="gallary_height_size" class="number_only" value="<?php echo empty($module_config['gallary_height_size'][1])?184:$module_config['gallary_height_size'][1];?>" size="4" />px
					</p>
					<p>
						<label for="">사진3개일때:</label>
						<input type="text" name="gallary_width_size" class="number_only" value="<?php echo empty($module_config['gallary_width_size'][2])?418:$module_config['gallary_width_size'][2];?>" size="4" />px X 
						<input type="text" name="gallary_height_size" class="number_only" value="<?php echo empty($module_config['gallary_height_size'][2])?184:$module_config['gallary_height_size'][2];?>" size="4" />px
					</p>
					<p>
						<label for="">사진4개일때:</label>
						<input type="text" name="gallary_width_size" class="number_only" value="<?php echo empty($module_config['gallary_width_size'][3])?418:$module_config['gallary_width_size'][3];?>" size="4" />px X 
						<input type="text" name="gallary_height_size" class="number_only" value="<?php echo empty($module_config['gallary_height_size'][3])?184:$module_config['gallary_height_size'][3];?>" size="4" />px
					</p>
					<p>
						<label for="">사진5개일때:</label>
						<input type="text" name="gallary_width_size" class="number_only" value="<?php echo empty($module_config['gallary_width_size'][4])?418:$module_config['gallary_width_size'][4];?>" size="4" />px X 
						<input type="text" name="gallary_height_size" class="number_only" value="<?php echo empty($module_config['gallary_height_size'][4])?184:$module_config['gallary_height_size'][4];?>" size="4" />px
					</p>
					<p>
						<label for="">사진6개이상:</label>
						<input type="text" name="gallary_width_size" class="number_only" value="<?php echo empty($module_config['gallary_width_size'][5])?418:$module_config['gallary_width_size'][5];?>" size="4" />px X 
						<input type="text" name="gallary_height_size" class="number_only" value="<?php echo empty($module_config['gallary_height_size'][5])?184:$module_config['gallary_height_size'][5];?>" size="4" />px
					</p>
				
				</td>        	
			</tr>    -->
			<!--<tr><th scope="row">목록 넓이 설정( 모자이크형 갤러리에서만 사용)</th><td><input type="text" name="gallary_width_size" class="number_only" value="<?php echo empty($module_config['gallary_width_size'])?910:$module_config['gallary_width_size'];?>" size="4" />px</td>-->
        </tbody>
      </table>    