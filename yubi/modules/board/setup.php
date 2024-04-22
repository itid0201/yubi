<link rel="stylesheet" type="text/css" href="<?php echo $_SERVER['SELF']; ?>css/setup.css" />

<div class="box_type">
  <ul class="tab_box2">
    <li class="on"><span><a href="#h3_1">모듈정보</a></span></li>
    <li><span><a href="#h3_2">기본설정</a></span></li>
    <li><span><a href="#h3_3">태그</a></span></li>
    <li><span><a href="#h3_4">글등록</a></span></li>
    <li><span><a href="#h3_5">답변글</a></span></li>
    <li><span><a href="#h3_6">글보기</a></span></li>
    <li><span><a href="#h3_7">글목록</a></span></li>
    <li><span><a href="#h3_8">추가기능</a></span></li>
  </ul>
  <form id="setting_form" method="post" action="">
    <input type="hidden" name="mode" value="setup_save" />
    <fieldset id="h3_1" class="webform on">
      <h3>모듈정보</h3>
      <table class="setup_table" cellspacing="0" cellpadding="0" border="0" summary="">
      	<tbody>
        	<tr><th scope="row">패키지명</th><td><input type="hidden" name="package_id" readonly value="<?php echo $module_config['package_id']; ?>" />
          <input type="hidden" name="package_name" readonly value="<?php echo $module_config['package_name']; ?>" />
          <span class="input_text"><?php echo $module_config['package_id']; ?>(<?php echo $module_config['package_name']; ?>)</span></td></tr>
          <tr><th scope="row">모듈위치</th><td><input type="hidden" name="module_path" readonly value="<?php echo $module_config['module_path']; ?>" />
          <span class="input_text"><?php echo $module_config['module_path']; ?></span></td></tr>
          <tr><th scope="row">모듈경로</th><td><input type="hidden" name="path" readonly value="<?php echo $module_config['path']; ?>" />
          <span class="input_text"><?php echo $module_config['path']; ?></span></td></tr>
          <tr><th scope="row">모듈이름</th><td><input type="hidden" name="board_name" readonly value="<?php echo $module_config['board_name']; ?>" />
          <span class="input_text"><?php echo $module_config['board_name']; ?></span></td></tr>
          <tr><th scope="row">모듈ID</th><td><input type="hidden" name="board_id" readonly value="<?php echo $module_config['board_id']; ?>" />
          <span class="input_text"><?php echo $module_config['board_id']; ?></span></td></tr>
          <tr><th scope="row">테이블명</th><td><input type="hidden" name="table_name" readonly value="<?php echo $module_config['table_name']; ?>" />
          <span class="input_text">
          <?php  echo $module_config['table_name']; ?>
          </span></td></tr>
        </tbody>
      </table>
    </fieldset>
    <fieldset id="h3_2" class="webform">
      <h3>기본설정</h3>
       <table class="setup_table" cellspacing="0" cellpadding="0" border="0" summary="">
      	<tbody>
          <tr>
			  <th scope="row">스킨</th><td>
			  <select name="skin_style" id="skin_style">
					  <option value="board" <?php echo ($module_config['skin_style']=='board'?'selected="selected"':'');?>>일반게시판</option>
					  <option value="notice" <?php echo ($module_config['skin_style']=='notice'?'selected="selected"':'');?>>공지게시판</option>
					  <option value="thumb" <?php echo ($module_config['skin_style']=='thumb'?'selected="selected"':'');?>>썸네일목록게시판(포토뉴스)</option>
            <option value="thumb_photo" <?php echo ($module_config['skin_style']=='thumb_photo'?'selected="selected"':'');?>>썸네일목록게시판(포토갤러리)</option>
            <option value="faq" <?php echo ($module_config['skin_style']=='faq'?'selected="selected"':'');?>>FAQ</option>
					  <!-- 이미지 관련 게시판 -->
					  <option value="gallery_photo" <?php echo ($module_config['skin_style']=='gallery_photo'?'selected="selected"':'');?>>포토갤러리(photo)</option>
            <option value="gallery_photo_foreign" <?php echo ($module_config['skin_style']=='gallery_photo_foreign'?'selected="selected"':'');?>>외국어 포토갤러리(photo-foreign)</option>
					  <option value="share_photo" <?php echo ($module_config['skin_style']=='share_photo'?'selected="selected"':'');?>>사진갤러리(슬라이더)</option>
					  <option value="viewer"  <?php echo ($module_config['skin_style']=='viewer'?'selected="selected"':'');?>>소식지(viewer)</option>
					  <option value="ebook"  <?php echo ($module_config['skin_style']=='ebook'?'selected="selected"':'');?>>소식지(ebook)</option>
					  <!-- 비디오관련 게시판 -->
					  <option value="stream_html5" <?php echo ($module_config['skin_style']=='stream_html5'?'selected="selected"':'');?>>스트리밍서비스(mp4변환)_html5</option>
				  	  <option value="stream_html5_foreign" <?php echo ($module_config['skin_style']=='stream_html5_foreign'?'selected="selected"':'');?>>외국어 스트리밍서비스(mp4변환)_html5(foreign)</option>
					  <option value="youtube" <?php echo ($module_config['skin_style']=='youtube'?'selected="selected"':'');?>>유투브게시판</option>
					  <!--배너/팝업 게시판(관리자 등록) -->
				      <!-- 배너를 팝업 게시판과 같이 쓰도록 한다. -->
					  <!--<option value="banner" <?php echo ($module_config['skin_style']=='banner'?'selected="selected"':'');?>>베너존</option>-->
					  <option value="popup"  <?php echo ($module_config['skin_style']=='popup'?'selected="selected"':'');?>>팝업존</option>
					  <option value="popup_color_multihost"  <?php echo ($module_config['skin_style']=='popup_color_multihost'?'selected="selected"':'');?>>상단팝업(배경색지정,멀티호스트)</option>
					  <!-- 본청관련 게시판 -->
					  <option value="minwon_form" <?php echo ($module_config['skin_style']=='minwon_form'?'selected="selected"':'');?>>민원서식편람</option>
					  <option value="approval_ex"  <?php echo ($module_config['skin_style']=='approval_ex'?'selected="selected"':'');?>>민원접수처리(결재-다중부서지정)</option>
					  <option value="regulation"  <?php echo ($module_config['skin_style']=='regulation'?'selected="selected"':'');?>>신고센터(결재)</option>
					  <option value="finance_form" <?php echo ($module_config['skin_style']=='finance_form'?'selected="selected"':'');?>>재정정보 예산편람</option>
					  <option value="finance_form_settlement" <?php echo ($module_config['skin_style']=='finance_form_settlement'?'selected="selected"':'');?>>재정정보 결산편람</option>

					  <!-- 외국어 게시판 -->
					  <option value="foreign_board_en"  <?php echo ($module_config['skin_style']=='foreign_board_en'?'selected="selected"':'');?>>다국어 일반게시판(영문)</option>
					  <option value="foreign_board_ch"  <?php echo ($module_config['skin_style']=='foreign_board_ch'?'selected="selected"':'');?>>다국어 일반게시판(중문)</option>
					  <option value="foreign_board_jp"  <?php echo ($module_config['skin_style']=='foreign_board_jp'?'selected="selected"':'');?>>다국어 일반게시판(일문)</option>
					  <option value="new_gallery_en"  <?php echo ($module_config['skin_style']=='new_gallery_en'?'selected="selected"':'');?>>다국어 갤러리(영문)</option>
					  <option value="new_gallery_ch"  <?php echo ($module_config['skin_style']=='new_gallery_ch'?'selected="selected"':'');?>>다국어 갤러리(중문)</option>
					  <option value="new_gallery_jp"  <?php echo ($module_config['skin_style']=='new_gallery_jp'?'selected="selected"':'');?>>다국어 갤러리(일문)</option>
					  <option value="stream_html5_en"  <?php echo ($module_config['skin_style']=='stream_html5_en'?'selected="selected"':'');?>>다국어 동영상_html5(영문)</option>
					  <option value="stream_html5_ch"  <?php echo ($module_config['skin_style']=='stream_html5_ch'?'selected="selected"':'');?>>다국어 동영상_html5(중문)</option>
					  <option value="stream_html5_jp"  <?php echo ($module_config['skin_style']=='stream_html5_jp'?'selected="selected"':'');?>>다국어 동영상_html5(일문)</option>
					  <option value="photonews_none_en"  <?php echo ($module_config['skin_style']=='photonews_none_en'?'selected="selected"':'');?>>포토뉴스(none)_en</option>
					  <option value="photonews_none_jp"  <?php echo ($module_config['skin_style']=='photonews_none_jp'?'selected="selected"':'');?>>포토뉴스(none)_jp</option>
					  <option value="photonews_none_ch"  <?php echo ($module_config['skin_style']=='photonews_none_ch'?'selected="selected"':'');?>>포토뉴스(none)_ch</option>
</select>
			  </td>
		  </tr>
		  <tr>
			<th scope="row">키워드 사용여부</th>
			<td>
			<label><input type="radio" name="use_keyword" id="use_keyword_y" style="border:0px;" value="true" <?php echo ($module_config['use_keyword']=='true'?'checked="checked"':'');?>/>사용함</label>
        	<label><input type="radio" name="use_keyword" id="use_keyword_n" style="border:0px;" value="false" <?php echo ($module_config['use_keyword']!='true'?'checked="checked"':'');?>/>사용안함</label>
			<br/><p>*일반사용자가 올리는 게시판에서는 사용안함으로 설정되어 있어야함. 공무원 등록 게시판에만 설정.</p>
			</td>
		  </tr>
		  <tr>
			<th scope="row">테마 사용여부</th>
			<td>
			<label><input type="radio" name="use_theme" id="use_theme_y" style="border:0px;" value="true" <?php echo ($module_config['use_theme']=='true'?'checked="checked"':'');?>/>사용함</label>
        	<label><input type="radio" name="use_theme" id="use_theme_n" style="border:0px;" value="false" <?php echo ($module_config['use_theme']!='true'?'checked="checked"':'');?>/>사용안함</label>
			<br/><p>*일반사용자가 올리는 게시판에서는 사용안함으로 설정되어 있어야함. 공무원 등록 게시판에만 설정.</p>
			</td>
		  </tr>
		  <tr>
			  <th scope="row">테마 등록관리<a href="#none" class="item_add">추가</a></th>
			  <?php
			  ##------------------  테마 기본값.
			  if( count($module_config['theme_list']) == 0 ){
				  $module_config['theme_list'] = array("관광지","마을경관","축제행사","문화체육","안전재난","정치외교","먹거리","시설/교통","산업경제","보건복지","교육과학","공모전","시정행정");
			  }

			  ?>
			  <td><select name="theme_list[]" id="theme_list" size="5" multiple="multiple">
					  <?php
					if(is_array($module_config['theme_list'])) {
						for($i=0;$i<count($module_config['theme_list']);$i++) {
							echo '<option value="'.$module_config['theme_list'][$i].'">'.$module_config['theme_list'][$i].'</option>';
						}
					}
					?>
					</select>
			  </td>
		  </tr>
          <tr>
          	<th scope="row">이전신고센터<br />(신고센터전용)</th>
            <td><input type="text" name="old_link" value="<?php echo $module_config['old_link']; ?>" /><br /></td>
          </tr>
          <tr><th scope="row">상단문구(글목록)</th><td><textarea name="list_msg" cols="60" rows="8"><?php echo stripslashes($module_config['list_msg']); ?></textarea></td></tr>
          <tr><th scope="row">상단문구(글목록-no css)</th><td><textarea name="list_msg_no_css" cols="60" rows="8"><?php echo stripslashes($module_config['list_msg_no_css']); ?></textarea></td></tr>
          <tr><th scope="row">상단문구(작성,수정,답변)</th><td><textarea name="write_msg" cols="60" rows="8"><?php echo stripslashes($module_config['write_msg']); ?></textarea></td></tr>
          <tr><th scope="row">내용(글 작성시 입력폼에 미리입력될 내용)</th><td><textarea name="default_contents" cols="60" rows="8"><?php echo stripslashes($module_config['default_contents']); ?></textarea></td></tr>
          <!--<tr><th scope="row">패키지명</th><td></td></tr>-->
        </tbody>
      </table>

      <p class="align_right"> <a href="#none" class="btn_big save_form"><span>저장하기</span></a> <a href="#none" class="btn_big return_list"><span>목록보기</span></a> </p>
    </fieldset>
    <fieldset id="h3_3" class="webform">
      <h3>태그설정</h3>
        <table class="setup_table" cellspacing="0" cellpadding="0" border="0" summary="">
      	<tbody>
          <tr><th scope="row">태그 필터링 사용여부</th><td><label>
          <input type="radio" name="use_tag" id="use_tag_1" style="border:0px;" value="true" <?php echo ($module_config['use_tag']=='true'?'checked="checked"':'');?> />
          사용</label>
        <label>
          <input type="radio" name="use_tag" id="use_tag_2" style="border:0px;" value="false" <?php echo ($module_config['use_tag']!='true'?'checked="checked"':'');?> />
          사용안함</label></td></tr>
           <?php
	if($module_config['use_tag']=='true') {
		$source_id = empty($module_config['source_id']) ? $module_config['board_id'] : $module_config['source_id'];
	?>
          <tr><th scope="row">source_id (tag와 연계)</th><td><input type="text" name="source_id" value="<?php echo $source_id; ?>" />
        <span class="vod_tip">tag가 사용되면 source_id는 필수로 들어가야 한다.
        source_id는 같은 모듈내의 board_id만 지정할 수 있다.
        만약 source_id가 빈값일 경우 자신의 board_id로 자동세팅 된다.</span></td></tr>
          <?php
		if($module_config['use_tag']=='true' && $source_id == $module_config['board_id']) {
			for($i=0;$i<count($module_config['tag_list_all']);$i++) {
				$tab_list_all .= '<option value="'.$module_config['tag_list_all'][$i].'">'.$module_config['tag_list_all'][$i].'</option>';
			}
		?>
          <tr><th scope="row">태그 설정관리<a href="#none" class="item_add">추가</a></th><td><select name="tag_list_all[]" id="tag_list_all" size="5"  multiple="multiple">
          <?php echo $tab_list_all; ?>
        </select></td></tr>
         <?php } ?>
          <tr><th scope="row">tag 선택(tag와 연계)</th><td><?php
		$source_config = $this->get_board_config($source_id);
		$module_config['tag_list_all'] = $source_config['tag_list_all'];
		if(is_array($module_config['tag_list_all'])) {
			foreach($module_config['tag_list_all'] as $value){
				echo '<label><input type="checkbox" name="tag_list[]" value="'.$value.'" '.(in_array($value, $module_config['tag_list']) ? 'checked="checked"' : '').' />'.$value.'</label>';
			}
		}
		?></td></tr>
         <?php
	}
	?>
        </tbody>
      </table>
      <p class="align_right"> <a href="#none" class="btn_big save_form"><span>저장하기</span></a> <a href="#none" class="btn_big return_list"><span>목록보기</span></a> </p>
    </fieldset>

    <fieldset id="h3_4" class="webform">
      <h3>글등록설정</h3>
        <table class="setup_table" cellspacing="0" cellpadding="0" border="0" summary="">
      	<tbody>
          <tr><th scope="row">카테고리 사용여부</th><td><label>
          <input type="radio" name="use_category_1" id="use_category_1_1" style="border:0px;" value="true" <?php echo ($module_config['use_category_1']=='true'?'checked="checked"':'');?>/>
          사용함</label>
        <label>
          <input type="radio" name="use_category_1" id="use_category_1_2" style="border:0px;" value="false" <?php echo ($module_config['use_category_1']!='true'?'checked="checked"':'');?>/>
          사용안함</label></td></tr>
			<?php $module_config['use_category_tab'] = empty($module_config['use_category_tab'])?'true':$module_config['use_category_tab'];?>
			<tr><th scope="row">카테고리 상단탭 사용여부</th><td><label>
          <input type="radio" name="use_category_tab" id="use_category_tab_y" style="border:0px;" value="true" <?php echo ($module_config['use_category_tab']=='true'?'checked="checked"':'');?>/>
          사용함</label>
        <label>
          <input type="radio" name="use_category_tab" id="use_category_tab_n" style="border:0px;" value="false" <?php echo ($module_config['use_category_tab']!='true'?'checked="checked"':'');?>/>
          사용안함</label></td></tr>

          <tr><th scope="row">카테고리 등록관리<a href="#none" class="item_add">추가</a></th><td><select name="category_1[]" id="category_1" size="5" multiple="multiple">
          <?php
        if(is_array($module_config['category_1'])) {
	        for($i=0;$i<count($module_config['category_1']);$i++) {
	        	echo '<option value="'.$module_config['category_1'][$i].'">'.$module_config['category_1'][$i].'</option>';
	        }
        }
        ?>
        </select></td></tr>
			<tr>
			  <th scope="row">대표이미지 설정</th>
			  <td>

				<input type="radio" name="main_image_use" id="main_image_use_y" style="border:0px;" value="true" <?php echo($module_config['main_image_use']=='true'?'checked="checked"':'')?>/>
				사용</label>
				<label>
					<input type="radio" name="main_image_use" id="main_image_use_n" style="border:0px;" value="false" <?php echo($module_config['main_image_use']!='true'?'checked="checked"':'')?>/>
          		사용안함</label>
				  <p class="wrap_div" style="clear:both">
					  <span class="sub_label" style="float: left; width:50%;padding-right:20px; ">보기페이지(view)에 이미지 출력 여부 : </span>
					  	<label>
					  	<input type="radio" name="main_image_view_use" id="main_image_view_use_y" style="border:0px;" value="true" <?php echo($module_config['main_image_view_use']=='true'?'checked="checked"':'')?>/>사용</label>
						<label>
						<input type="radio" name="main_image_view_use" id="main_image_view_use_n" style="border:0px;" value="false" <?php echo($module_config['main_image_view_use']!='true'?'checked="checked"':'')?>/>사용안함</label>
				  </p>

			  </td>
			</tr>
          <tr><th scope="row">개인정보제공동의 글쓰기</th><td><select name="use_privacy_write">
          <option value="true" <?php echo ($module_config['use_privacy_write']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_privacy_write']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">개인정보제공동의 문구</th><td><textarea name="privacy_msg" cols="60" rows="8"><?php echo stripslashes($module_config['privacy_msg']); ?></textarea></td></tr>
          <tr><th scope="row">손님(비회원) 글쓰기</th><td><select name="use_logoff_write">
          <option value="true" <?php echo ($module_config['use_logoff_write']=='true'?'selected="selected"':'');?>>허용</option>
          <option value="false" <?php echo ($module_config['use_logoff_write']!='true'?'selected="selected"':'');?>>허용안함</option>
        </select></td></tr>
          <tr><th scope="row">손님(비회원) 글쓰기시 주소,연락처 입력받기</th><td><select name="get_guest_info">
          <option value="true" <?php echo ($module_config['get_guest_info']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['get_guest_info']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">관리자 승인 후 노출 (비회원게시판에서 사용금지)</th><td><select  name="use_allow">
          <option value="true" <?php echo($module_config['use_allow']=='true'?'selected="selected"':'')?>>사용</option>
          <option value="false" <?php echo($module_config['use_allow']!='true'?'selected="selected"':'')?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">공개/비공개 기능</th><td><select  name="use_lock">
          <option value="true" <?php echo($module_config['use_lock']=='true'?'selected="selected"':'')?>>사용</option>
          <option value="false" <?php echo($module_config['use_lock']!='true'?'selected="selected"':'')?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">공개/비공개 기본값</th><td><select  name="lock_default">
          <option value="open" <?php echo($module_config['lock_default']=='open'?'selected="selected"':'')?>>공개</option>
          <option value="close" <?php echo($module_config['lock_default']!='open'?'selected="selected"':'')?>>비공개</option>
        </select></td></tr>
          <tr><th scope="row">첨부파일개수</th><td> <select name="file_upload_count">
          <option value="0" <?php echo($module_config['file_upload_count']=='0'?'selected="selected"':'')?>>없음</option>
          <?php
                    for($i=1; $i<=12; $i++){
                        if($i == $module_config['file_upload_count']) $selected = 'selected="selected"';
                        else $selected = '';
                        echo '<option value="'.$i.'" '.($i == $module_config['file_upload_count'] ? 'selected="selected"' : '').'>'.$i.'개</option>';
                    }
                    ?>
        </select></td></tr>
          <tr><th scope="row">전체 업로드 파일 크기</th><td><input type="text" name="max_size_upload" class="number_only" value="<?php echo(empty($module_config['max_size_upload']) ? 50 : $module_config['max_size_upload'])?>" size="4" />MB</td></tr>
          <tr><th scope="row">업로드제한 파일(확장자명)</th><td><?php
		// 모듈 생성시 아래 내용이 자동으로 생성되면 삭제 되어도 된다.
		if(empty($module_config['ext_deny_upload'])) $module_config['ext_deny_upload'] = 'php,phps,php3,html,htm,phtml,phtm,shtm,shtml,pl,cgi,asp,js,css,swf,exe,inc,ztx,dot';
		?>
        <textarea name="ext_deny_upload" cols="60" rows="3"><?php echo($module_config['ext_deny_upload']); ?></textarea></td></tr>
          <tr><th scope="row">업로드 파일 크기</th><td><input type="text" name="max_size_file" class="number_only" value="<?php echo(empty($module_config['max_size_file']) ? 10 : $module_config['max_size_file'])?>" size="4" />
        MB</td></tr>
          <tr><th scope="row">업로드허용 사진(확장자명)</th><td><?php
		// 모듈 생성시 아래 내용이 자동으로 생성되면 삭제 되어도 된다.
		if(empty($module_config['ext_photo_file'])) $module_config['ext_photo_file'] = 'jpg,gif,png,bmp';
		?>
        <textarea name="ext_photo_file" cols="60" rows="3"><?php echo($module_config['ext_photo_file']); ?></textarea></td></tr>
          <tr><th scope="row">업로드 사진 크기</th><td><input type="text" name="max_size_photo" class="number_only" value="<?php echo(empty($module_config['max_size_photo']) ? 2 : $module_config['max_size_photo'])?>" size="4" />
        MB</td></tr>
          <tr><th scope="row">업로드허용 동영상(확장자명)</th><td><?php
		// 모듈 생성시 아래 내용이 자동으로 생성되면 삭제 되어도 된다.
		if(empty($module_config['ext_movie_file'])) $module_config['ext_movie_file'] = 'wmv,avi,mpeg,mpg,mp4,mov,flv';
		?>
        <textarea name="ext_movie_file" cols="60" rows="3"><?php echo($module_config['ext_movie_file']); ?></textarea></td></tr>
          <tr><th scope="row">업로드 동영상 크기</th><td><input type="text" name="max_size_movie" class="number_only" value="<?php echo(empty($module_config['max_size_movie']) ? 50 : $module_config['max_size_movie'])?>" size="4" />
        MB</td></tr>
          <tr><th scope="row">웹편집기 사용</th><td><select name="use_editor">
          <option value="true" <?php echo ($module_config['use_editor']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_editor']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">지도 사용</th><td><select name="use_map">
          <option value="true" <?php echo ($module_config['use_map']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_map']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">등록시SMS알림</th><td><select name="use_write_sms">
          <option value="true" <?php echo ($module_config['use_write_sms']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_write_sms']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">SMS CALLBACK NUMBER</th><td><?php
		$module_config['write_sms_send_number']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1\2\3',$module_config['write_sms_send_number']);
		?>
        <input type="text" name="write_sms_send_number" value="<?php echo $module_config['write_sms_send_number']?>" style="width:130px" /></td></tr>
          <tr><th scope="row">SMS수신번호(※다중 입력시 ,로 구분)</th><td><?php
		$module_config['write_sms_recv_number']=preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/','01\1\2\3',$module_config['write_sms_recv_number']);
		?>
        <input type="text" name="write_sms_recv_number" value="<?php echo $module_config['write_sms_recv_number']?>" style="width:98%" /></td></tr>
          <tr><th scope="row">SMS알림문자</th><td><?php $module_config['write_sms_mesg'] = empty($module_config['write_sms_mesg']) ? '[사이트] '.$module_config['board_name'].' 게시판에 [등록자]님의 글이 등록되었습니다.' : $module_config['write_sms_mesg']?>
        <input type="text" name="write_sms_mesg" value="<?php echo $module_config['write_sms_mesg']?>" style="width:98%" />
		<span class="vod_tip">[등록자], [등록일시], [제목], [아이피], [메뉴], [사이트], [URL] 등으로 표기할 수 있음.</span></td></tr>
	  <tr><th scope="row">휴대전화번호 차단</th><td><select name="use_phone_filter">
          <option value="true" <?php echo ($module_config['use_phone_filter']!='false'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_phone_filter']=='false'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">필수입력필드 사용 여부 설정</th><td><select name="use_write_field">
          <option value="true" <?php echo ($module_config['use_write_field']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_write_field']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
        <?php
	  if($module_config['use_write_field'] == 'true') {
	  	include_once $module_config['module_path'].'/required_field.php';
	  ?>
          <tr><th scope="row">필수입력필드 설정</th><td><ul class="filed_list">
		<?php
			for($i=0;$i<count($required_field);$i++) {
				if($required_field[$i]['required'] == 1) {
					$checked = 'checked="checked"';
				} elseif(in_array($required_field[$i]['fields_name'], $module_config['required_value'])) {
					$checked = 'checked="checked"';
				} else {
					$checked = '';
				}

				echo '<li><input type="checkbox" name="required_value[]" id="required_value_'.$i.'" value="'.$required_field[$i]['fields_name'].'" '.$checked.' /><label for="required_value_'.$i.'">'.$required_field[$i]['label_name'].'('.$required_field[$i]['fields_name'].')</label></li>';
			}
		?>
		</ul></td></tr>
          <?php } ?>
	  	<tr>
			<th>
				파일 업로드 필수
			</th>
			<td><select name="use_file_check">
				  <option value="true" <?php echo ($module_config['use_file_check']=='true'?'selected="selected"':'');?>>사용</option>
				  <option value="false" <?php echo ($module_config['use_file_check']!='true'?'selected="selected"':'');?>>사용안함</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				사진 파일 업로드
			</th>
			<td><select name="use_gallery_img">
				  <option value="true" <?php echo ($module_config['use_gallery_img']=='true'?'selected="selected"':'');?>>사용</option>
				  <option value="false" <?php echo ($module_config['use_gallery_img']!='true'?'selected="selected"':'');?>>사용안함</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				첨부파일 숨기기
			</th>
			<td>
				<select name="hidden_attach">
				  <option value="true" <?php echo ($module_config['hidden_attach']=='true'?'selected="selected"':'');?>>사용</option>
				  <option value="false" <?php echo ($module_config['hidden_attach']!='true'?'selected="selected"':'');?>>사용안함</option>
				</select>
			</td>
		</tr>
        </tbody>
      </table>
      <p class="align_right"> <a href="#none" class="btn_big save_form"><span>저장하기</span></a> <a href="#none" class="btn_big return_list"><span>목록보기</span></a> </p>
    </fieldset>
    <fieldset id="h3_5" class="webform">
      <h3>답변글설정</h3>
        <table class="setup_table" cellspacing="0" cellpadding="0" border="0" summary="">
      	<tbody>
          <tr><th scope="row">답변글 사용여부</th><td><select name="use_reply">
          <option value="none" <?php echo ($module_config['use_reply']=='none'?'selected="selected"':'');?>>사용안함</option>
          <option value="admin" <?php echo ($module_config['use_reply']=='admin'?'selected="selected"':'');?>>관리자만허용</option>
          <option value="all" <?php echo ($module_config['use_reply']=='all'?'selected="selected"':'');?>>모두허용</option>
        </select></td></tr>
          <tr><th scope="row">답변글 등록시 SMS 알림요청</th><td><select name="use_reply_sms">
          <option value="none" <?php echo ($module_config['use_reply_sms']=='none'?'selected="selected"':'');?>>사용안함</option>
          <option value="admin" <?php echo ($module_config['use_reply_sms']=='admin'?'selected="selected"':'');?>>관리자만허용</option>
          <option value="all" <?php echo ($module_config['use_reply_sms']=='all'?'selected="selected"':'');?>>모두허용</option>
        </select></td></tr>
        </tbody>
      </table>
      <p class="align_right"> <a href="#none" class="btn_big save_form"><span>저장하기</span></a> <a href="#none" class="btn_big return_list"><span>목록보기</span></a> </p>
    </fieldset>
    <fieldset id="h3_6" class="webform">
      <h3>글보기설정</h3>
      <table class="setup_table" cellspacing="0" cellpadding="0" border="0" summary="">
      	<tbody>

		  <tr><th scope="row">보기페이지 파일 이미지 출력 </th><td><select name="use_view_photo_file">
          <option value="true" <?php echo ($module_config['use_view_photo_file']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_view_photo_file']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">보기페이지 하단에 목록출력</th><td><select name="list_at_view">
          <option value="true" <?php echo ($module_config['list_at_view']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['list_at_view']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">글읽은 횟수 차단시간(동일인 기준)</th><td><input type="text" name="visit_expire_term" class="number_only" value="<?php echo(empty($module_config['visit_expire_term']) ? 60 : $module_config['visit_expire_term'])?>" size="4" />
        분 동안 증가하지 않음</td></tr>
          <tr><th scope="row">댓글</th><td><select name="use_comment">
          <option value="true" <?php echo ($module_config['use_comment']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_comment']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">댓글 방식</th><td><select name="comment_type">
          <option value="normal" <?php echo ($module_config['comment_type']=='normal'?'selected="selected"':'');?>>일반형</option>
		  <option value="petition" <?php echo ($module_config['comment_type']=='petition'?'selected="selected"':'');?>>청원형</option>
          <option value="recommend" <?php echo ($module_config['comment_type']=='recommend'?'selected="selected"':'');?>>추천형</option>
          <option value="point" <?php echo ($module_config['comment_type']=='point'?'selected="selected"':'');?>>점수평가형</option>
        </select></td></tr>
          <tr><th scope="row">열람사유작성</th><td><select name="use_view_comment">
          <option value="true" <?php echo ($module_config['use_view_comment']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_view_comment']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
        <?php
		if(empty($module_config['contents_img_width_size'])) $module_config['contents_img_width_size']='980';
		?>
        <tr><th scope="row">본문내 이미지 가로크기</th><td><input type="text" name="contents_img_width_size" class="number_only" value="<?php echo($module_config['contents_img_width_size'])?>" size="4" />px</td></tr>
        </tbody>
      </table>
      <p class="align_right"> <a href="#none" class="btn_big save_form"><span>저장하기</span></a> <a href="#none" class="btn_big return_list"><span>목록보기</span></a> </p>
    </fieldset>
    <fieldset id="h3_7" class="webform">
      <h3>글목록설정</h3>
      <table class="setup_table" cellspacing="0" cellpadding="0" border="0" summary="">
      	<tbody>
		  <tr><th scope="row">목록 엑셀출력 여부(관리자만)</th>
			  <td><select name="use_list_excel">
			  <option value="true" <?php echo ($module_config['use_list_excel']=='true'?'selected="selected"':'');?>>사용</option>
			  <option value="false" <?php echo ($module_config['use_list_excel']!='true'?'selected="selected"':'');?>>사용안함</option>
			</select></td></tr>
      	  <tr><th scope="row">인기글 사용 여부(새로 추가됨)</th>
			  <td><select name="user_list_hot">
			  <option value="true" <?php echo ($module_config['user_list_hot']=='true'?'selected="selected"':'');?>>사용</option>
			  <option value="false" <?php echo ($module_config['user_list_hot']!='true'?'selected="selected"':'');?>>사용안함</option>
			</select></td></tr>
		 <tr><th scope="row">인기글 수(새로 추가됨)</th><td><input type="text" name="list_hot_count" class="number_only" value="<?php echo(empty($module_config['list_hot_count']) ? 5 : $module_config['list_hot_count'])?>" size="4" />
        개</td></tr>
		<tr>
			<th>목록에 첨부파일 사용</th>
			<td>
				<select name="use_list_attach">
				  <option value="true" <?php echo ($module_config['use_list_attach']=='true'?'selected="selected"':'');?>>사용</option>
				  <option value="false" <?php echo ($module_config['use_list_attach']!='true'?'selected="selected"':'');?>>사용안함</option>
				</select>
			</td>
		</tr>
			<tr><th scope="row">게시기간 사용여부</th><td><label>
          <input type="radio" name="use_period" id="use_period_1" style="border:0px;" value="true" <?php echo ($module_config['use_period']=='true'?'checked="checked"':'');?> />
          사용</label>
        <label>
          <input type="radio" name="use_period" id="use_period_2" style="border:0px;" value="false" <?php echo ($module_config['use_period']!='true'?'checked="checked"':'');?> />
          사용안함</label></td></tr>
      	  <tr><th scope="row">삭제된글 보기</th><td><select name="show_delete">
          <option value="true" <?php echo ($module_config['show_delete']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['show_delete']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">작성자가 쓴 글만 보기</th><td><select name="view_only_my_articles">
          <option value="true" <?php echo ($module_config['view_only_my_articles']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['view_only_my_articles']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">목록수</th><td><input type="text" name="page_scale" class="number_only" value="<?php echo(empty($module_config['page_scale']) ? 30 : $module_config['page_scale'])?>" size="4" />
        라인</td></tr>

		 <tr><th scope="row">목록수 검색 설정<a href="#none" class="item_add">추가</a></th><td>
          <select name="page_scale_search_list[]" id="page_scale_search_list" size="5" multiple="multiple">
          <?php
					// 모듈 생성시 아래 내용이 자동으로 생성되면 삭제 되어도 된다.
					if(empty($module_config['page_scale_search_list'])) $module_config['page_scale_search_list'] = array('5', '15', '30');

					  if(is_array($module_config['page_scale_search_list'])) {
						foreach($module_config['page_scale_search_list'] as $value) {
							//$temp = explode('|', $value);
							echo '<option value="'.$value.'">'.$value.'</option>';
						}
					  }
		?>
        </select>
		</td></tr>

          <tr><th scope="row">페이지블럭수</th><td><input type="text" name="block_scale" class="number_only" value="<?php echo(empty($module_config['block_scale']) ? 10 : $module_config['block_scale'])?>" size="4" />
        개</td></tr>
          <tr><th scope="row">제목 자르기(0은 사용안함)</th><td><input type="text" name="subject_cut" class="number_only" value="<?php echo($module_config['subject_cut'])?>" size="4" />
        자</td></tr>
          <tr><th scope="row">내용 자르기</th><td><input type="text" name="contents_cut" class="number_only" value="<?php echo($module_config['contents_cut'])?>" size="4" />
        자</td></tr>
          <tr><th scope="row">새글표시 설정기간</th><td><input type="text" name="new_article_term" class="number_only" value="<?php echo(empty($module_config['new_article_term'])? 3 : $module_config['new_article_term'])?>" size="4" />
        일 동안</td></tr>
          <tr><th scope="row">이름 감추기</th><td><select  name="use_hide_name_all">
          <option value="true" <?php echo($module_config['use_hide_name_all']=='true'?'selected="selected"':'')?>>사용</option>
          <option value="false" <?php echo($module_config['use_hide_name_all']!='true'?'selected="selected"':'')?>>사용안함</option>
        </select><span class="vod_tip">(홍길동 -> 홍OO) : 공개/비공개 모두 감춤</span></td></tr>
          <tr><th scope="row">비공개글 이름</th><td><select  name="use_hide_name">
          <option value="true" <?php echo($module_config['use_hide_name']=='true'?'selected="selected"':'')?>>사용</option>
          <option value="false" <?php echo($module_config['use_hide_name']!='true'?'selected="selected"':'')?>>사용안함</option>
        </select><span class="vod_tip">(홍길동 -> 홍OO)</span></td></tr>
          <tr><th scope="row">검색단어 등록 사용</th><td><select name="use_search_tag">
          <option value="true" <?php echo ($module_config['use_search_tag']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_search_tag']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">비공개글 제목</th><td><select  name="use_hide_title">
          <option value="true" <?php echo($module_config['use_hide_title']=='true'?'selected="selected"':'')?>>사용</option>
          <option value="false" <?php echo($module_config['use_hide_title']!='true'?'selected="selected"':'')?>>사용안함</option>
        </select><span class="vod_tip">(제목 -> 비공개글입니다)</span></td></tr>
          <tr><th scope="row">등록자 표시</th><td><select name="writer_display">
          <option value="person" <?php echo ((empty($module_config['writer_display']) || $module_config['writer_display']=='persion')?'selected="selected"':'');?>>등록자</option>
          <option value="department" <?php echo ($module_config['writer_display']=='department'?'selected="selected"':'');?>>등록부서</option>
          <option value="other_organ" <?php echo ($module_config['writer_display']=='other_organ'?'selected="selected"':'');?>>타기관</option>
          <option value="processing" <?php echo ($module_config['writer_display']=='processing'?'selected="selected"':'');?>>처리부서</option>
		  <option value="charge_organ" <?php echo ($module_config['writer_display']=='charge_organ'?'selected="selected"':'');?>>담당부서</option>
		  <option value="user_self" <?php echo ($module_config['writer_display']=='user_self'?'selected="selected"':'');?>>사용자지정</option>
        </select></td></tr>
        <?php
	  if($module_config['writer_display']=='user_self') { ?>
          <tr><th scope="row">등록자 표시 사용자 지정</th><td><input type="text" id="writer_display_user_self" name="writer_display_user_self" value="<?php echo ($module_config['writer_display_user_self']!=''?$module_config['writer_display_user_self']:'등록자'); ?>" size="10" /></td></tr>
          <?php } ?>
          <tr><th scope="row">별칭 사용</th><td><select name="use_nick_name">
          <option value="true" <?php echo ($module_config['use_nick_name']=='true'?'selected="selected"':'');?>>사용함</option>
          <option value="false" <?php echo ($module_config['use_nick_name']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">등록일 표시</th><td><select name="use_reg_date">
          <option value="true" <?php echo ($module_config['use_reg_date']!='false'?'selected="selected"':'');?>>표시함</option>
          <option value="false" <?php echo ($module_config['use_reg_date']=='false'?'selected="selected"':'');?>>표시안함</option>
        </select></td></tr>
          <tr><th scope="row">제목스타일 적용</th><td><label>
          <input type="radio" name="use_title_style" id="use_title_style_1" style="border:0px;" value="true" <?php echo($module_config['use_title_style']=='true'?'checked="checked"':'')?>/>
          사용</label>
        <label>
          <input type="radio" name="use_title_style" id="use_title_style_2" style="border:0px;" value="false" <?php echo($module_config['use_title_style']!='true'?'checked="checked"':'')?>/>
          사용안함</label></td></tr>
          <tr><th scope="row">제목스타일 등록관리<a href="#none" class="item_add">추가</a></th><td><select name="title_style[]" id="title_style" size="5" multiple="multiple">
          <?php
			if(is_array($module_config['title_style'])) {
				for($i=0;$i<count($module_config['title_style']);$i++) {
					echo '<option value="'.$module_config['title_style'][$i].'">'.$module_config['title_style'][$i].'</option>';
				}
			}
			?>
        </select></td></tr>
          <tr><th scope="row">검색필드 설정관리<a href="#none" class="item_add">추가</a></th><td>
          <select name="search_list[]" id="search_list" size="5" multiple="multiple">
          <?php
					// 모듈 생성시 아래 내용이 자동으로 생성되면 삭제 되어도 된다.
					if(empty($module_config['search_list'])) $module_config['search_list'] = array('title|제목', 'contents|내용', 'reg_name|등록자', 'reg_id|아이디');

					  if(is_array($module_config['search_list'])) {
						foreach($module_config['search_list'] as $value) {
							//$temp = explode('|', $value);
							echo '<option value="'.$value.'">'.$value.'</option>';
						}
					  }
		?>
        </select>
		</td></tr>
          <tr><th scope="row">처리상태사용</th><td><label>
          <input type="radio" name="use_process_1" id="use_process_1_1" style="border:0px;" value="true" <?php echo($module_config['use_process_1']=='true'?'checked="checked"':'')?>/>
          사용</label>
        <label>
          <input type="radio" name="use_process_1" id="use_process_1_2" style="border:0px;" value="false" <?php echo($module_config['use_process_1']!='true'?'checked="checked"':'')?>/>
          사용안함</label></td></tr>
          <tr><th scope="row">처리상태 관리<a href="#none" class="item_add">추가</a></th><td class="catein"><select name="process_1[]" id="process_1" size="5" multiple="multiple">
          <?php
			  if(is_array($module_config['process_1'])) {
				for($i=0;$i<count($module_config['process_1']);$i++) {
					echo '<option value="'.$module_config['process_1'][$i].'">'.$module_config['process_1'][$i].'</option>';
				}
			  }
			  ?>
        </select></td></tr>
          <tr><th scope="row">머릿글(top) 설정</th><td><select name="use_top">
          <option value="true" <?php echo ($module_config['use_top']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_top']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">TOP설정 제한 기능</th><td><select name="use_top_limit" id="use_top_limit">
          <option value="true" <?php echo ($module_config['use_top_limit']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_top_limit']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">TOP설정 제한 갯수</th><td><input type="text" id="use_top_limit_value" name="use_top_limit_value" value="<?php echo ($module_config['use_top_limit_value']!=''?$module_config['use_top_limit_value']:'0'); ?>" size="2" /> 개
		  <span class="vod_tip">※ 갯수 설정을 안했을 시 제한 없이 설정 가능. </span></td></tr>
          <tr><th scope="row">삭제사유 사용여부</th><td></td></tr>
          <tr><th scope="row">패키지명</th><td><select name="use_delete_reason" id="use_delete_reason">
          <option value="true" <?php echo ($module_config['use_delete_reason']=='true'?'selected="selected"':'');?>>사용</option>
          <option value="false" <?php echo ($module_config['use_delete_reason']!='true'?'selected="selected"':'');?>>사용안함</option>
        </select></td></tr>
          <tr><th scope="row">관리자 선택 삭제</th><td><label>
          <input type="radio" name="multi_delete" id="multi_delete_1" style="border:0px;" value="true" <?php echo($module_config['multi_delete']=='true'?'checked="checked"':'')?>/>
          사용</label>
        <label>
          <input type="radio" name="multi_delete" id="multi_delete_2" style="border:0px;" value="false" <?php echo($module_config['multi_delete']!='true'?'checked="checked"':'')?>/>
          사용안함</label></td></tr>
        </tbody>
      </table>
      <p class="align_right"> <a href="#none" class="btn_big save_form"><span>저장하기</span></a> <a href="#none" class="btn_big return_list"><span>목록보기</span></a> </p>
    </fieldset>
   <fieldset id="h3_8" class="webform">
      <h3>추가기능</h3>
      <table class="setup_table" cellspacing="0" cellpadding="0" border="0" summary="">
      	<tbody>
          <tr><th scope="row">게시물이동 설정 : 게시물 받기</th><td><label>
              <input type="radio" name="article_move_in" id="article_move_in_1" style="border:0px;" value="true" <?php echo($module_config['article_move_in']!='false'?'checked="checked"':'')?>/>허용
            </label>
            <label>
              <input type="radio" name="article_move_in" id="article_move_in_2" style="border:0px;" value="false" <?php echo($module_config['article_move_in']=='false'?'checked="checked"':'')?>/>허용안함
            </label></td></tr>
          <tr><th scope="row">게시물이동 설정 : 게시물 보내기</th><td><label>
              <input type="radio" name="article_move_out" id="article_move_out_1" style="border:0px;" value="true" <?php echo($module_config['article_move_out']!='false'?'checked="checked"':'')?>/>허용
            </label>
            <label>
              <input type="radio" name="article_move_out" id="article_move_out_2" style="border:0px;" value="false" <?php echo($module_config['article_move_out']=='false'?'checked="checked"':'')?>/>허용안함
            </label></td></tr>


             <tr><th scope="row">직원알리미</th><td><label>
              <input type="radio" name="staff_push" id="staff_push_1" style="border:0px;" value="true" <?php echo($module_config['staff_push']=='true'?'checked="checked"':'')?>/>사용
            </label>
            <label>
              <input type="radio" name="staff_push" id="staff_push_2" style="border:0px;" value="false" <?php echo($module_config['staff_push']!='true'?'checked="checked"':'')?>/>사용안함
            </label></td></tr>

        </tbody>
      </table>
    <?php
		## 스킨별로 적용되는 setting 정보를 가져온다.
		## 스킨이 바뀌면 ajax를 이용하여 해당 setting 내용을 여기에 적용시켜 준다.
		if(file_exists($module_config['module_path'].'skin/'.$module_config['skin_style'].'/setup.php')) {
			include_once $module_config['module_path'].'skin/'.$module_config['skin_style'].'/setup.php';
		}

		## 모듈카테고리별로 적용되는 setting 정보를 가져온다.
		if(file_exists($module_config['module_path'].'setup/'.$module_config['board_id'].'.php')) {
			include_once $module_config['module_path'].'setup/'.$module_config['board_id'].'.php';
		}

		## 공용 setting 파일 인크루드 (플러그인)
		## 디지토미 설정 등... 공통적으로 추가 하고자 하는 setting form 은 플러그인 폴더에 파일을 만들어 넣는다...
		$setting_plugin_path = $this->module_root.'/_plugin/setup';
		$d = dir($setting_plugin_path);
		while (false !== ($entry = $d->read())) {
			if($entry!='.' && $entry!='..' && preg_match('/.*?\.php$/',$entry)) include_once $setting_plugin_path.'/'.$entry;
		}
		$d->close();


	?>
      <p class="align_right"> <a href="#none" class="btn_big save_form"><span>저장하기</span></a> <a href="#none" class="btn_big return_list"><span>목록보기</span></a> </p>

    </fieldset>
  </form>
</div>

<script>
(function($) {
	$("a.return_list").attr("href","?mode=list");

	$.fn.CateControl = function (options) {

		this.parents('tr').find('td').append('<ul class="cate"></ul>');
		this.parents('tr').find('select').hide();
		this.parents('tr').find('select > option').each(function() {
			$._CateControl.append($(this));
		});

		this.parents('tr').find('ul').sortable({
		   stop: function(event, ui) {
			  var $selbox =  $(this).prev('select');

			  $(this).children('li').each(function(index, element) {
                	$("option:eq("+index+")",$selbox).html($(this).children('span').text());
				});

			}
		});
		this.click(function() {
			var $selbox =  $(this).parents('tr').find('select');

			$selbox.append('<option>새로운 항목</option>');
			$._CateControl.append($selbox.find('option:last-child'));
		});
	};

	$._CateControl =   {
		append : function(that) {
/*			console.log( that.html() );																	*/
/*			$li = $('<li><span>'+that.attr('text')+'</span><em><a href="#none" class="item_del">제거</a></em></li>');*/
			$li = $('<li><span>'+that.html()+'</span><em><a href="#none" class="item_del">제거</a></em></li>');
			that.parents('td').children('ul').append($li);

			$li.find('a.item_del').click(function() {
				$ul = $(this).parents('ul');
				if(confirm('정말로 삭제하시겠습니까?')) {
					var li_idx = $(this).parents('li').index();
					$(this).parents('li').remove();
					console.log(li_idx);
					$._CateControl.del($ul,li_idx);
				}
			});

			$li.children('span').click(function() {
				if($(this).find('input').length<1) {
					$._CateControl.checkout($(this).parents('ul'));
					$inp = $('<input type="text" value="'+$(this).html()+'" />');
					$inp.blur(function(){
						$ul = $(this).parents('ul');
/*						console.log($(this).val() );*/
						$(this).parent('span').html($(this).val());
						$._CateControl.save($ul);
					});
					$(this).html('').append($inp).find('input').focus();
				}
			});
		},
		save : function(that) {
			/*var $selbox =  that.parents('td').children('select');*/
			var $selbox =  that.parents('td').children('select');
			$selbox.attr('length',that.children('li').length);
			console.log( $selbox.length );
			that.children('li').each(function(index, element) {
                $("option:eq("+index+")",$selbox).html($(this).children('span').text());
			});
		},
		/* 서희진 추가 : 삭제시에 option 값 삭제*/
		del : function(that,idx) {
			var $selbox =  that.parents('td').children('select');
			$selbox.attr('length',that.children('li').length);

			$("option:eq("+idx+")",$selbox).remove();
			that.children('li').each(function(index, element) {
                $("option:eq("+index+")",$selbox).html($(this).children('span').text());
			});
		},
		checkout: function(that) {
			that.find('input').each(function(index, element) {
				$(this).blur();
			});

		}

	};

})(jQuery);
</script>
<script name="import_jquery.ready">

  	$('ul.tab_box2').find('a').click(function() {
			var $id =  $(this).attr('href');
			$('.webform:visible').hide();
			$($id).show();
			$(this).parent().parent().parent().find('.on').removeClass('on');
			$(this).parent().parent().addClass('on');
			return false;
    });
	$('.webform:first').show();
	$('.item_add').CateControl();

	$('.save_form').click(function(){

		$(this).parents('form').find('select > option').each(function() {
			if($(this).parent().attr('size')>0) {
				/*$(this).attr('selected','selected').attr('value',$(this).attr('text')); */
				$(this).attr('selected','selected').attr('value',$(this).html());
			}
		});
		var dataString = $(this).parents('form').serialize();
		$(this).parents('form').find('select > option').each(function() {if($(this).parent().attr('size')>0) { $(this).attr('selected','');} });

			$.ajax({
				type: "POST",
				url: window.location.pathname,
				data: dataString,
				success: function(data){

					if(data=='true') {
						alert('저장완료');
						location.reload();
					}
				}
		});

		return false;
	});
</script>