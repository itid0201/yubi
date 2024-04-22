$(function(){
	/******* 검색 접근성 작업 *******/
	
	/*$("#search_word").val('');*/
	$(document).find(".band.share_btn").focusout(function(){
		$(".module_list_box .board_list_box .board_thumb .item .board_btn_share").siblings(".board_share_box").stop().fadeOut(function(){$(this).siblings(".board_btn_share").removeClass("on");});
	});
	
	/*230829 권재영 접근성 작업을 위해 막음*/
	/* setTimeout(function(){  sch_left(); sch_right(); },1000);
		
	function sch_left(){
		var $schObj_left = $(document).find("form#board_sch1 .left_box .select_box .selectric");		
		$schObj_left.find("span.selectric-label").html($schObj_left.find("span").text()+'<span class="hid">('+$schObj_left.find("span").text()+'선택됨)</span>');		
		$schObj_left.find("button").attr("title","목록 개수 설정 버튼");		
	}
	
	function sch_right(){
		var $schObj_right = $(document).find("form#board_sch1 .right_box .select_box .selectric");
		$schObj_right.find("span.selectric-label").html($schObj_right.find("span").text()+'<span class="hid">('+$schObj_right.find("span").text()+'선택됨)</span>');						
		$schObj_right.find("button").attr("title","검색 옵션 선택 버튼");
	}
	
	$(document).on("change","select#search_type",function(){
		sch_right();		
	});
	*/
	/******************************/

	
	/* 공유하기 */
	/* a 태그에 data-type, data-url 이 있어야만 정상 작동. */	
	$(document).on("click", "a.share_btn" , function(){
		var type = $(this).data('type');
		var snsUrl = window.location.origin+escape($(this).data('url'));
		var pageTiele = $(this).data('title');
		var gpurl = '';

		switch(type){
			case "facebook" :
				goUrl = 'https://www.facebook.com/sharer.php?u='+snsUrl+'&amp;t='+pageTiele;
				window.open(goUrl); 
				break;
			case "twitter" :
				/*goUrl = 'http://twitter.com/home?status='+pageTiele+'%20'+snsUrl;*/
				goUrl = 'https://twitter.com/intent/tweet?text='+pageTiele+'&url='+snsUrl;
				window.open(goUrl); 
				break;
			case "kakaostory" :
				goUrl = 'https://story.kakao.com/share?url='+snsUrl+'&amp;t='+pageTiele;
				window.open(goUrl); 
				break;
			case "band" :
				goUrl = 'http://www.band.us/plugin/share?body='+pageTiele+'%0A'+snsUrl;
				window.open(goUrl); 
				break;
			default :
				break;

		}

	});

	/* 목록 검색 */
	$(document).on("click","input#search_word_btn",function(){
		$("input[name='search_date']").val('y');
		if( $("input[name='search_word']").val().length == 0  && $("input[name='start_date']").val().length == 0 && $("input[name='finish_date']").val().length == 0 ){
			modal({
				type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: "검색어를 입력하세요.",
				callback: function(result) {	
					$("input[name='search_word']").focus();
				}
			});					
			return false;
		}
		var gourl = $("form[name='search_form1']").attr("action").replace("#search_word_btn","")+'#search_word_btn';
		$("form[name='search_form1']").attr("action",gourl );
	});	

	$("input[name='search_word']").on("keypress",function(key){
		if (key.keyCode == 13) {
			$("input#search_word_btn").click();
			return false;
		}

	});	

	/* 모듈 라벨 처리 */
	if( $("#search_word").val().length > 0 ){
		$("#search_word").siblings("label[for=search_word]").hide();
	}

	$("#search_word").focus(function(){
		$(this).siblings("label[for=search_word]").hide();
	}).focusout(function(){
		if( $(this).val().length === 0 ){
			$(this).siblings("label[for=search_word]").show();
		}
	});	

	/*인기글*/
	var hot_list = $(".module_list_box .hot_board ul");
	var hot_list_count = hot_list.children().length;
	hot_list.removeClass("standby");

	if(hot_list_count >= 3){
		var hot_board = hot_list.bxSlider({
			minSlides: 3,
			maxSlides: 3,
			moveSlides: 1,
			slideWidth: 295,
			pager: false,
			controls: false,
			slideMargin: 15,
		});
		$(".module_list_box .hot_board .control_box a").on("click", function(){
			if($(this).hasClass("prev") == true){
				hot_board.goToPrevSlide();
			}else if($(this).hasClass("next") == true){
				hot_board.goToNextSlide();
			}
		});
	}else{
		$(".module_list_box .hot_board").addClass("no_slide");
	}

	/*페이저*/
	$(".board_pager_box .pagemove_box input").on('keydown focusout', function(e){
		var value = $(this).val();
		$(this).after('<div id="virtual_dom">' + value + '</div>'); 
		var inputWidth =  $(document).find('#virtual_dom').width(); // 글자 하나의 대략적인 크기 
		if(inputWidth > 23){
			$(this).width(inputWidth+10);
		}else{
			$(this).width(26);
		}
		$('#virtual_dom').remove();
	});

	/* 숫자만 입력 */
	$("#pagemove_input").keyup(function (event) {
		var regexp = /[^0-9]/gi;
		var obj = $(this);
		var go_page = obj.val();
		if (regexp.test(go_page)) {
			modal({
				type: 'alert',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: '숫자만 입력하여 주세요.',
				callback: function(result) {						
						obj.val(go_page.replace(regexp, '')).focus();							
				}
			});                
		}

	});
	/* 페이지 바로가기 */
	$("a.pagemove_btn").on("click",function(){
		var obj = $("#pagemove_input");
		var go_page = obj.val();
		var total_page = $("input[name='total_page']").val();
		var navi_parameter = $("input[name='navi_parameter']").val();
		
		
		if( Number(go_page) > Number(total_page) ){
			modal({
				type: 'alert',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: '전체 '+total_page+' 페이지 입니다.\n '+go_page+'로 이동이 불가능합니다. ',
				callback: function(result) {						
						obj.val(total_page).focus();							
				}
			});   
		}else{
			window.location.href = '?mode=list&page='+go_page+navi_parameter+'#board_list';
		}
		
	});
	
	/* 페이징 */
	$("#page_scale").on("change",function(){
		
		/*console.log( $("form[name='search_form1']").attr("action") );		*/
		var gourl = $("form[name='search_form1']").attr("action").replace("#page_scale","")+'#page_scale';
		$("form[name='search_form1']").attr("action",gourl );
		//return false;
		$("form[name='search_form1']").submit();
	});
	/*230726 권재영 들어있는 이유를 모르겠음.*/
	/*$("a.search_reset").on("click",function(){	
		
		$(this).attr("href","#none");
		$("#search_word").attr("value",'');
		$(document).find("form[name='search_form1']").each(function() {
			this.reset();
		});
		var gourl = $("form[name='search_form1']").attr("action").replace("#search_word","")+'#search_word';
		$("form[name='search_form1']").attr("action",gourl );
		$("form[name='search_form1']").submit();
	});*/
	

	$(".data_hidden").each(function(){
		$(this).val( $(this).val().replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3'));
	});
	$(".datetime").each(function(){		
		$(this).val( $(this).siblings(".data_hidden").val().replace(/-/gi,"") );
	});
	$('.datetime').datetimepicker({
		timepicker:false,
		format:'Ymd',
		useCurrent: false,
		keepInvalid: true,
		lang: 'ko',
	}).on("change", function(){
		$(this).siblings(".data_hidden").val($(this).val().replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3'));
	});

	/************************************************ vod ****************************************************/					
	/*공유하기*/
	$(".module_list_box .board_list_box .board_video .item .top_util .board_btn_share").on("click", function(e){
		if($(this).hasClass("on") == false){
			$(this).addClass("on").siblings(".board_share_box").stop().slideDown();
		}else{
			$(this).siblings(".board_share_box").stop().slideUp(function(){$(this).siblings(".board_btn_share").removeClass("on");});
		}
		return false;
	});

	/*박스 클릭이벤트(링크)*/
	$(".module_list_box .board_list_box .board_video .item .cont_box").on("click", function(event){
		if( $(event.target).hasClass("share_btn")  == false){
			var this_link = $(this).siblings(".thumb_box").find(".item_click").attr("href");
			$(location).attr("href",this_link);
		}
	});

	/*박스 호버*/
	$(".module_list_box .board_list_box .board_video .item").on("mouseenter", function(){
		$(this).addClass("hover");
	}).on("mouseleave", function(){
		$(this).removeClass("hover");
	});	

	/************************************************ photo ****************************************************/					
		/*공유하기*/
	$(".module_list_box .board_list_box .board_photo .item .top_util .board_btn_share").on("click", function(e){
		if($(this).hasClass("on") == false){
			$(this).addClass("on").siblings(".board_share_box").stop().slideDown();
		}else{
			$(this).siblings(".board_share_box").stop().slideUp(function(){$(this).siblings(".board_btn_share").removeClass("on");});
		}
		return false;			
	});

	/*박스 클릭이벤트(링크)*/
	$(".module_list_box .board_list_box .board_photo .item .cont_box").on("click", function(event){
		if($(event.target).hasClass("share_btn") == false){
			var this_link = $(this).siblings(".thumb_box").find(".item_click").attr("href");
			$(location).attr("href",this_link);
		}
	});

	/*박스 호버*/
	$(".module_list_box .board_list_box .board_photo .item").on("mouseenter", function(){
		$(this).addClass("hover");
	}).on("mouseleave", function(){
		$(this).removeClass("hover");
	});

	/************************************************ thumb ****************************************************/		
	/*공유하기*/
	$(".module_list_box .board_list_box .board_thumb .item .board_btn_share").on("click", function(e){
		if($(this).hasClass("on") == false){
			$(this).addClass("on").siblings(".board_share_box").stop().fadeIn();
		}else{
			$(this).siblings(".board_share_box").stop().fadeOut(function(){$(this).siblings(".board_btn_share").removeClass("on");});
		}
		return false;			
	});

	/*박스 클릭이벤트(링크)*/
	$(".module_list_box .board_list_box .board_thumb .item").on("click", function(event){
		if( $(event.target).hasClass("share_btn") == false ){
			var this_link = $(this).siblings(".thumb_box").find(".item_click").attr("href");
			$(location).attr("href",this_link);
		}
	});	

	/* 비공개글 보기 막음. */
	$("a.list_lock").on("click",function(){
		modal({
			type: 'alert',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
			text: ' 비공개글입니다. ',
			callback: function(result) {
			}
		});   
		return false;		
	});
	
	/*새글일때 클래스*/
	/*
	if($(".module_list_box .board_list_box .board_video .item h3, .module_list_box .board_list_box .board_photo .item h3").children().hasClass("icon_box")){
		$(this).addClass("has_new");
	}
	*/
	$(".module_list_box .board_list_box .board_video .item h3:has('.icon_box'), .module_list_box .board_list_box .board_photo .item h3:has('.icon_box')").addClass("has_new");

});
