<?php 
echo $data['debug']; // only test.. 
$agree_title =  $_SYSTEM['module_config']['agree_title'];
?>

<h3><?php echo $agree_title; ?></h3>
<script type="text/javascript">
function chkAgree(frm){
if(frm.agree1[0].checked == false){alert('개인정보 수집 및 이용목적에 동의하세요'); frm.agree1[0].focus(); return false;}
}
</script>
<form name="frm" action="<?php echo $_SERVER['PHP_SELF']; ?>?mode=agree" method="post" onsubmit="return chkAgree(this);">
        <h4>개인정보의 수집 및 이용에 대한 안내</h4>
        <div class="agreeform_box">
        <p class="basic">「군수에게 바란다」는 관계법령 등에서 정하는 소관 업무의 수행을 위하여 다음과 같이 개인정보를 수집 및 이용합니다.</p>
        <div class="c_box2">
        <div class="in_box">
        <ul class="basic">
        	<li>수집된 개인정보는 정해진 목적 이외의 용도로는 이용되지 않습니다.</li>
            <li>관계법령 : 민원사무 처리에 관한 법률 및 동법 시행령, 전자정부법 및 동법 시행령</li>
            <li>가. 민원, 제안, 질의, 신고, 제안 등 모든 시민의견 접수·처리·사후관리 서비스 신청에 포함된 개인정보는 소관 업무 수행을 위해 행정·공공기관에서 이용합니다.</li>
            <li>나. 타 행정·공공기관 시스템 이용 민원의 전자적 처리를 위해 내부적으로 타 시스템 연계 및 이용 시 개인정보를 이용합니다.</li>
        </ul>
        <ul class="basic mat20">
        <li>개인정보 수집범위
        	<ul>
            <li>필수항목 : 이름, 연락처</li>
            <li>선택항목 : 주소</li>
            </ul>
        </li>
        <li>개인정보의 이용기간 및 보유기간 : 귀하께서 제공하신 개인정보는 게시물이 삭제될 때 파기합니다. 다만, 다른 법령에 따라 보존하여야 하는 경우에는 그러하지 않을 수 있습니다. (민원, 제안, 공익신고 등 : 10년)</li>
        </ul>
        
        </div>
        </div>
        
        <p class="basic align_center"><span>개인정보 수집 및 이용에 동의하십니까?</span>
                    <input type="radio" name="agree1" id="agree1_a" value="y" /><label for="agree1_a">동의함</label>
                    <input type="radio" name="agree1" id="agree1_b" value="n" /><label for="agree1_b">동의안함</label></p>

<span class="line_dot"></span>
        <p class="basic mat50">※ 귀하께서는 개인정보의 수집 및 이용, 제3자에게로의 제공에 동의하지 않을 권리가 있고, 동의거부에 따른 불이익은 없으나, 본 홈페이지의 민원 신청 서비스를 제공받을 수 없습니다. 본인은 상기 내용에 대한 충분한 이해를 하였으며, 귀하의 개인정보 수집이용에 동의합니다.</p>
        <p class="align_center mat20"><?php echo date("Y") ?> 년 <?php echo date("m") ?> 월</p>
        <p class="basic agree_btn_box align_center"><input type="submit" value="개인정보 제공 동의서 확인 완료" class="sch_btn" /></p>
    </div>
</form>

