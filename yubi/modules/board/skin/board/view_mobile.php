<?php echo $data['debug']; // only test.. ?>
<?php

//2012-10-10 황재복 : 휴대폰 번호 자동 차단 방지
if ($_SYSTEM['module_config']['use_phone_filter'] == 'false') {
    $data['title'] = preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/', '01\1－\2－\3', $data['title']);
    $data['contents'] = preg_replace('/01([016789])[-~.[:space:]]([0-9]{3,4})[-~.[:space:]]([0-9]{4})/', '01\1－\2－\3', $data['contents']);
}

############################# contetns #############################
if ($data['html_tag'] == "a") {
    $data['contents'] = unserialize(base64_decode($data['contents']));
    $contents = get_html_contents($data['contents'], $data['file_list'], NULL, $data);
} else {
    $temp[] = nl2br($data['contents']);
    //ob_clean();	print_r($temp);	exit;
    $contents = get_html_contents($temp, $data['file_list'], NULL, $data);
}
############################# contetns #############################


$print_modify = '';
if ($_SYSTEM['module_config']['board_id'] == 'www_notice' && $data['modify_date'] !== null) {
    $print_modify = '[수정] ';
}

## 2023.03.27 이사장과 소통에서 익명으로 작성 시 정보 비공개 처리하도록
if($_SYSTEM['module_config']['board_id'] == 'www_hope' && $data['anonymous'] == 'y' ) {
    $data['reg_name'] = '익명';
    $data['reg_id'] = 'anonymous';
    $data['reg_pin'] = 'anonymous';
}

?>
<div class="module_view_box">
    <div class="view_titlebox">
        <h3><?php echo $print_modify . $data['title']; ?></h3>
        <span><?php echo date('Y.m.d ', strtotime($data['reg_date'])); ?> / <?php echo($data['writer_display'] == 'department' ? $data['depart_name'] : $data['reg_name']); ?>
            <?php echo(($_SYSTEM['module_config']['board_id'] == 'youth_participate_budget_participation' || $_SYSTEM['module_config']['board_id'] == 'citizen_participate_budget_participation') ? ' / ' . $data['varchar_1'] : ''); ?>
		</span>
    </div>
    <input type="hidden" name="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="contbox">

        <?php
        ## -------------- 콘텐츠
        echo $contents;
        if ($_SYSTEM['module_config']['board_id'] != 'www_promote') {
            ## 이미지 출력 부분
            if (!empty($data['file_list'])) {
                foreach ($data['file_list'] as $file) {
                    if ($file['file_type'] == 'photo') {
                        $photo_file = $_SYSTEM['module_root'] . '/_data' . $_SYSTEM['module_config']['path'] . '/' . $_SYSTEM['module_config']['board_id'] . '/' . $file['re_name'];
                        $photo_size = getimagesize($photo_file);
                        if ($photo_size[0] <= $_SYSTEM['module_config']['contents_img_width_size']) $_SYSTEM['module_config']['contents_img_width_size'] = '';
                        echo '<div class="photo_view"><img src="./recity/ybmodule.file' . $_SYSTEM['module_config']['path'] . '/' . $_SYSTEM['module_config']['board_id'] . (!empty($_SYSTEM['module_config']['contents_img_width_size']) ? '/' . $_SYSTEM['module_config']['contents_img_width_size'] . 'x1' : '') . '/' . $file['re_name'] . '" alt="' . (!empty($file['title']) ? $file['title'] : $file['original_name']) . '" /></div>';
                    }
                }
            }
        }

        ## -------------- 답변글
        if (count($data['reply']) > 0) {
            echo print_reply($data['reply']);
        }


        ## --------------------- 첨부파일
        if (count($data['file_list']) > 0) {

            $group_file = (count($data['file_list']) > 0) ? '<a href="/ybscript.io/common/file_download?pkey=' . $_SESSION['private_key'] . '&file_type=all&idx=' . $data['idx'] . '&file=' . implode("|", $data['file_idxs']) . '" class="all_down">전체(Zip)다운로드</a>' : '<a href="#none" class="all_down none">전체(Zip)다운로드</a>';
            ?>
            <div class="file_viewbox">
                <div class="top_box">
                    <?php echo download_box_new("view", $data['idx'], $data['file_list']); ?>
                </div>
                <div class="bottom_box"><?php echo $group_file; ?></div>
            </div>
            <?php
        }
        ## ---------------------

        ## ---------------------공공누리 출력
        ## 테스트로  포항아이피에서  $data['kogl_type'] 값을 임시로 넣어둠.(1~5)
        //if( $_SERVER['REMOTE_ADDR'] == "49.254.140.140" ){ $data['kogl_type'] = "4"; }
        if ($data['use_open_type'] == 'true' && !empty($data['kogl_type'])) {
            echo '<div class="open_viewbox">';
            echo print_koglType_kogl($data);
            echo '</div>';
        }
        ## ---------------------
        ## --------------------- view_tag
        if ($data['use_view_tag'] == 'true') {
            echo print_view_tag();
        }
        ## ---------------------
        ?>
    </div>
    <?php

    ## 버튼 영역.
    $img_url = '/images/common/board/temp';
    $url = $_SERVER['PHP_SELF'];
    $user_info = array();
    $user_info['is_login'] = empty($_SYSTEM['myinfo']['is_login']) ? NULL : $_SYSTEM['myinfo']['is_login'];
    $user_info['user_pin'] = empty($_SYSTEM['myinfo']['my_pin']) ? NULL : $_SYSTEM['myinfo']['my_pin'];
    $arr_data['reg_pin'] = empty($data['reg_pin']) ? NULL : $data['reg_pin'];
    $arr_data['del'] = empty($data['del']) ? NULL : $data['del'];
    $arr_data['use_logoff_write'] = empty($data['use_logoff_write']) ? NULL : $data['use_logoff_write'];
    $arr_data['use_reply'] = empty($data['use_reply']) ? 'none' : $data['use_reply'];


    ## --------------------- 버튼 : start
    $print_button = print_button_new('view', $data['permission'], $data['parameter'], $url, $img_url, $user_info, $arr_data);
    if (!empty($print_button)) {
        echo '<!-- 버튼 START --><div class="board_btn_box align_right">' . $print_button . '</div><!-- 버튼 END -->';
    }
    ## ---------------------

    ## ---------------------  댓글
    if ($data['use_comment'] == 'true') include_once($data['module_root'] . '/_plugin/skin/comment.php');
    ## ---------------------


//    if($_SERVER['REMOTE_ADDR']=='49.254.140.140' && $_SESSION['user_id']=='yoonseo0084'){
//      print_r($data['nearby_articles']);
//      exit;
//    }

    ## --------------------- 이전글 / 다음글
    //print_r( $data['nearby_articles'] );
    $data['use_pager'] = 'true';
    if ($data['use_pager'] == 'true') {
        echo print_pager($data['nearby_articles'], $data['parameter'], $_SERVER['PHP_SELF']);
    }
    ## ---------------------

    ## --------------------- 인기 게시물
    if ($data['user_list_hot'] == 'true') {
        echo print_view_hot($data['hot_articles_data']);
    }
    ## ---------------------
    ?>
</div>	
