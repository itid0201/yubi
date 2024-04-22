<?php
// 아래부터 관리자일경우 ------------------------------------------------------------------------------- // if(나는==관리자) {
//$_GET['admin_save'] = 'true';
//$_GET['admin_save'] = 'true';
		## 관리자 설정 적용일 경우 넘어온 설정값을 update한다.
		## 이부분을 jquery ajax로 변경한다.
		/*
		if($this->permission['admin'] === true && $_GET['admin_save'] == 'true') {
			if($this->admin_save($idx, $_GET) !== true) call::xml_error('154', '관리자 설정을 저장하는중에 오류가 발생하였습니다.', $this->referer);
		}
	
	class.php
	## 사용상 주의 : admin_save는 권한등의 validation을 체크하지 않기 때문에 call하는 부분에서 validation 체크를 다 해야한다.
	public function admin_save($idx, $post=array()) {
		## ********************************************************************************************************************
		## 관리자기능 : 승인/비승인 설정, 처리상태 설정, top 설정, 관리자 코멘트등록, 게시물 이동, 삭제글 복구설정 추가해야 한다.
		## 관리자기능 interface가 어떻게 구현이 되는지 확인. 창을 따로가면 module member function 으로 따로가져가면되고 
		## 그렇지 않다면 3.0에서 처럼 submode를 정의해서 가야한다.
		## ** 주의 : 공개/비공개 설정은 관리자 뿐만이니라 글쓴이도 설정을 할 수있어야 하기 때문에 수정페이지로 뺀다.
		## ** 주의 : 아직 테스트 하지 않았다 테스트 해보아야 한다.
		## ********************************************************************************************************************
		*/

$view_html = ob_get_contents();
ob_clean();
ob_start();
?>
<div class="modify_setting_tab">
    <ul class="tab_menu">
        <li class="selected"><a href="#none" title="m1">글내용보기</a></li>
        <li><a href="#none" title="m2">등록자정보</a></li>
        <li><a href="#none" title="m3">게시물설정</a></li>
        <li><a href="#none" title="m4">로그정보</a></li>
        <li><a href="#none" title="m6">열람기록</a></li>
        <?php if($data['article_move_out'] != 'false') {?>
        <li><a href="#none" title="m5">이동하기</a></li>
        <?php }?>
    </ul>
</div>
<div id="tab_content_m1" class="tab_content selected"><?php echo $view_html; ?></div>
<div id="tab_content_m2" class="tab_content">
	<div class="modify_tablebox">
	<?php 
	$path_writer_info_file = $data['module_root'].'/_plugin/skin/admin_writer_info.php';
	if(file_exists($path_writer_info_file)) include_once $path_writer_info_file;
	?>
    </div>
</div>
<div id="tab_content_m3" class="tab_content">
	<div class="modify_tablebox">
    <?php
    $path_save_article_file = $data['module_path'].'/admin_save_article.php';
    if(file_exists($path_save_article_file)) include_once $path_save_article_file;
    else echo '설정파일이 없습니다.';
    ?>
    </div>    
</div>
<div id="tab_content_m4" class="tab_content">
	<div class="modify_tablebox">
<?php 
	$path_log_info_file = $data['module_root'].'/_plugin/skin/admin_log_info.php';
	if(file_exists($path_writer_info_file)) include_once $path_log_info_file;
?>
    </div>
</div>
<div id="tab_content_m6" class="tab_content">
	<div class="modify_tablebox">
<?php 
	$path_view_list_file = $data['module_root'].'/_plugin/skin/admin_view_list.php';
	if(file_exists($path_view_list_file)) include_once $path_view_list_file;
?>
    </div>
</div>
<?php if($data['article_move_out'] != 'false') {?>
<div id="tab_content_m5" class="tab_content">
	<div class="modify_tablebox">
      <?php 
      $path_move_article_file = $data['module_root'].'/_plugin/skin/admin_move_article.php';
      if(file_exists($path_move_article_file)) include_once $path_move_article_file;
      ?>
    </div>      
</div>
<?php }?>
<script type="text/javascript">
//<![CDATA[
$(function() { 
	$('.tab_menu > li > a').click(function() {
		$('.tab_menu').find('li[class="selected"]').removeClass('selected');
		$('#content').find('.tab_content').removeClass('selected').hide();
		var target = $(this).attr('title');
		$(this).parent('li').addClass('selected');
        $('#tab_content_'+target).show().addClass('selected');
		
	});

});
//]]>

</script>

<?php
 // 관리자일경우 끝 -----------------------------------------------------------------------------------------------------------------// } 
?>