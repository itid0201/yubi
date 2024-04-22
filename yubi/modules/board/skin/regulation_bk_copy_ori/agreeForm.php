<?php 
echo $data['debug']; // only test.. 
$agree_title =  $_SYSTEM['module_config']['agree_title'];
?>
<div class="agree_wrapper">
<h4 class="c0">개인정보 제공 동의서</h4>
<script type="text/javascript">
function chkAgree(frm){
if(frm.agree1[0].checked == false){alert('개인정보 수집 및 이용목적에 동의하세요'); frm.agree1[0].focus(); return false;}
if(frm.agree2[0].checked == false){alert('개인정보의 제3자 이용목적에 동의하세요'); frm.agree2[0].focus(); return false;}
}
</script>
<form name="frm" action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=agree" method="post" onsubmit="return chkAgree(this);">
    <h5>개인정보 수집 및 이용 동의 내용</h5>
    <div class="mat20 agreeform_box">
    <div class="c_box2">
    <div class="in_box">
    <p>나주시청은 원활한 민원처리를 위해 아래와 같은 개인정보를 수집하고 있습니다.</p>
	<p class="basic">- 목적 : 민원접수처리</p>
    <p>- 항목 : 이름, 주소, 연락처</p>
    <p>- 보유기간 : 게시글 삭제시까지</p>
    </div>
    </div>


<div class="mat20 align_center agree_ment_m"> 
	<span class="agree_ment">위와 같이 개인정보를 수집하는데 동의하십니까?</span>
	<div class="agree_check">
		<div class="item"><input type="radio" name="agree1" id="agree1_a" value="y"><label for="agree1_a">동의함</label></div>
		<div class="item"><input type="radio" name="agree1" id="agree1_b" value="n"><label for="agree1_b">동의하지 않음</label></div>
	</div>
</div>


<span class="line_dot"></span>

        <p class="basic mat30 align_center">※ 정보주체는 개인정보의 수집ㆍ이용에 대한 동의를 거부할 수 있으며, 동의 거부시 서비스를 이용할 수 없습니다.</p>
        <p class="align_center mat10"><?php echo date("Y") ?> 년 <?php echo date("m") ?> 월</p>
        <!--<p class="align_center mat10">강진군수 귀하.</p>-->
        <p class="basic agree_btn_box align_center"><input type="submit" value="개인정보 제공 동의서 확인 완료" class="sch_btn" /></p>
        
    </div>
</form>
</div>
