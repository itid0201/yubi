      <table class="setup_table" cellspacing="0" cellpadding="0" border="0" summary="">
      	<tbody>
        	<tr>
            	<th scope="row"><label>이미지사이즈</label></th>
                <td>
                   가로<input type="text" name="banner_size_width" class="number_only" value="<?php echo(empty($module_config['banner_size_width']) ? 120 : $module_config['banner_size_width'])?>" size="4" />px
                    <strong>X</strong> 세로<input type="text" name="banner_size_height" class="number_only" value="<?php echo(empty($module_config['banner_size_height']) ? 80 : $module_config['banner_size_height'])?>" size="4" />px
      		    </td>
            </tr>    	
        	<tr>
            	<th scope="row"><label>보이기/감추기 기간설정</label></th>
                <td>
                    <select name="banner_allow_schedule">
                      <option value="true" <?php echo ($module_config['banner_allow_schedule']=='true'?'selected="selected"':'');?>>사용</option>
                      <option value="false" <?php echo ($module_config['banner_allow_schedule']!='true'?'selected="selected"':'');?>>사용안함</option>
                    </select>
      		    </td>
            </tr>   
        	<tr>
            	<th scope="row"><label>배너 스킨사용</label></th>
                <td>
                    <input type="hidden" name="use_banner" readonly="readonly" value="true" />
                    <span class="input_text">필수</span>
      		    </td>
            </tr>             	            
		</tbody>
    </table>    