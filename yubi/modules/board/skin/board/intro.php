<div class="board_wrapper">
	<div class="module_list_box">
		<?php 
		## 상단문구 출력
		if(!empty($data['list_msg'])) {
			if($data['device']=='mobile') echo '<p>'.stripslashes($data['list_msg']).'</p>';
			else echo '<div class="content_top_alert"><div class="alert_content">'.stripslashes($data['list_msg']).'</div></div>';
		}
		
		## 상단문구 출력(스타일 없음))
		if(!empty($data['list_msg_no_css'])) {
			echo '<div class="alert_content_none">'.stripslashes($data['list_msg_no_css']).'</div>';
		}
		?>
        
        <div class="btn_p align_right">
        <?php if($data['myinfo']['is_login'] == 'true') { ?>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=write" class="p2 mar5">글작성하기</a>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?mode=list" class="p3"><?php echo ($_SYSTEM['permission']['admin']==true?'작성글보기':'내가쓴글보기') ?></a>
        <?php } ?>

        </div>
	</div>
</div>
