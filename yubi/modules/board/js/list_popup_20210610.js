$(function(){
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
		if(hot_list_count >= 3){
			var hot_board = hot_list.bxSlider({
				minSlides: 3,
				maxSlides: 3,
				moveSlides: 1,
				slideWidth: 290,
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
		$(document).on('keydown focusout', ".board_pager_box .pagemove_box input", function(e){
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
	  	$(document).on("keyup","#pagemove_input", function (event) {
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
	$(document).on("click","a.pagemove_btn",function(){
		var obj = $(document).find("#pagemove_input");		
		var go_page = obj.val();
		console.log( go_page );
		var total_page = $("input[name='total_page']").val();
		var navi_parameter = $("input[name='navi_parameter']").val();
		
		
		if( go_page > total_page ){
			modal({
				type: 'alert',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
				text: '전체 '+total_page+' 페이지 입니다.\n '+go_page+'로 이동이 불가능합니다. ',
				callback: function(result) {						
						obj.val(total_page).focus();							
				}
			});   
		}else{
			window.location.href = '?mode=list&page='+go_page+navi_parameter;
		}
		
	});
	
	/* 페이징 */
	$("#page_scale").on("change",function(){
		$("form[name='search_form1']").submit();
	});
	

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
	
	var obj_table = $('.change_list');
		/* 선택글 삭제 */
		$(document).on("click", ".check_delete", function(){
			
			console.log( 'test' );
			
			if(!isLogin) {
				modal({
						type: 'alert',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
						text: '로그인 후 이용 가능합니다.',
						callback: function(result) {
							self.location.href="/www/operation_guide/member_login?return_url="+selfUrl;
						}
					});
				return false;
			}		

			if( obj_table.find("input[type=checkbox]").is(":checked") != true){
				modal({
					type: 'alert',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
					text: '삭제할 글을 선택해주세요.',
					callback: function(result) {					
					}
				});
				return false;		

			}else{
					var check_idx = new Array();  
					$("input[name='check_idx[]']:checked").each(function(i){
						check_idx.push($(this).val());
					});
					console.log(check_idx);
					modal({
						type: 'confirm',  /* option : alert/confirm/prompt/success/warning/info/error/primary */
						text: '삭제하시겠습니까?',
						callback: function(result) {
							if(result){
								console.log(document.getElementById("form_del"));
								_admin_ajax("delete_all", check_idx);
							}				
						}
					});		

			}

		});
	
	
});


	/*********************************** function ********************************************/
	function _admin_ajax(type, idxs){
		/*var myForm = document.getElementById("form_del");*/
		var formData = new FormData();
		formData.append('mode', type);
		formData.append('return', 'json');
		formData.append("check_idx[]", idxs);
		
		console.log( selfUrl );
		
		$.ajax({
			url : selfUrl
			, type : "POST" /*request type(POST, GET)*/
			,contentType: false
			,processData: false
			, data : formData /*보내는 데이터 request*/			
			/*요청결과수신 reponse*/
			, beforeSend : /*요청보내기전선처리*/
				function (response) {
					/*alert("beforeSend");*/
				}
			, success : 
				function (json) { /*성공*/				
					console.log( json );
					
					if( json == "ok" ){
						$(".module_list_box .board_popup > div > .tab_cont_wrap .changebox > .tab_btn").click();
						
					}else{
						
					}
				}
			, error : /*에러*/
				function (response) {
					console.log( response );
				}
			, complete : /*완료후*/
				function(response, textStatus, XMLHttpRequest){
					/*console.log( response );*/
				}
		});		
		
		return false;
	}	
	
	
	/* 목록 가져오기 */	
	function get_list_data(type, $this, inputObj, wrapObj, img_path, imgSize, callback){			
		var page_scale = $("select#page_scale").val();
			console.log( type );
		$.ajax({
			url : selfUrl
			, type : "POST" /*request type(POST, GET)*/
			, dataType:"json" /*return type(xml, html, json, jsonp, script, text)*/
			, async : false /*동기화유무(true, false)*/
			, cache: false /*캐쉬사용여부(true, false)*/
			, data : {
				mode : "list"
				, sub_mode : type				
				, page_scale : page_scale
				, return : "json"
			} /*보내는 데이터 request*/
			
			/*요청결과수신 reponse*/
			, beforeSend : /*요청보내기전선처리*/
				function (response) {
					/*alert("beforeSend");*/
				}
			, success : 
				function (json) { /*성공*/				
					console.log( json );
					var html = '';
					var html_pager = '';
					print_list(type, json.list, img_path, imgSize,  function(get_html){
							if( get_html == '' ){
								get_html = '<div class="no_result"><span class="icon"></span>검색내역이 없습니다.</div>';
							}	
							
							inputObj.html('').show();
							console.log( json.navi_parameter );
							inputObj.append(get_html);				

							$(".board_pager_box").remove();
							html_pager = print_pager(json.total_count, json.page_scale, json.block_scale, json.page, json.json_navi_parameter);
							inputObj.after(html_pager);
							$(".board_search_box .left_box > span.total").html('총 '+json.total_count+'건');
							if( type == "sort" ){
								sortable();
								sorting();
							}
					});


				}
			, error : /*에러*/
				function (response) {
					console.log( response );
				}
			, complete : /*완료후*/
				function(response, textStatus, XMLHttpRequest){
					/*console.log( response );*/
				}
		});		
		callback(true);
		
	}
	
	/* 페이징 출력 */
	function print_pager($total, $list_scale, $page_scale, $page, $parameter){
		var html = '';
		var return_html = '';
		
		var $total_page = Math.ceil( Math.ceil($total) / Math.ceil($list_scale) );
		var $page_list = Math.ceil( Math.ceil($page) / Math.ceil($page_scale) ) - 1;		
		var navi_parameter = '&'+$parameter;

		  if ( $page > 1 ) {
			var $prev_page = $page - 1;
			var $prev = '<a class="prevpage" href="?page='+$prev_page+navi_parameter +'">이전페이지</a>';
			var $prev2 = '<a class="prev" title="이전페이지로 이동" href="?page='+$prev_page+navi_parameter +'"><span>이전페이지로 이동</span></a>';
		  } else {
			var $prev = '<em class="prevpage">이전페이지 없음</em>';
			var $prev2 = '';
		  }
		  var $page_end = ( $page_list + 1 ) * parseInt($page_scale);
		  if ( $page_end > $total_page )$page_end = $total_page;
		  html += '<ul>';
		  for ( var $setpage = $page_list * parseInt($page_scale) + 1; $setpage <= $page_end; $setpage++ ) {
			if ( $setpage == $page ){
				html += '<li><a class="active" >'+$setpage +'</a></li>';
			}else{
				html += '<li><a href="?page='+$setpage+navi_parameter +'" title="'+$setpage +' 페이지">'+$setpage +'</a></li>';
			} 
		  }
		  html += '</ul>';

		  if ( $page < $total_page ) {
			var $next_page = $page + 1;
			var $next = '<a class="nextpage" href="?page='+$next_page+navi_parameter +'">다음페이지</a>';
			var $next2 = '<a class="next" title="다음페이지로 이동" href="?page='+$next_page+navi_parameter +'"><span>다음페이지로 이동</span></a>';
		  } else {
			var $next = '<em class="nextpage">다음페이지 없음</em>';
			var $next2 = '';
		  }
		
		return_html  = '<div class="board_pager_box">';
		return_html += 		'<div class="pagenum_box">'+html+'</div>';
		if ( $total_page > 1 ) {	  	  
		return_html += 		'<div class="pagemove_box">';
		return_html += 			'<input type="hidden" name="total_page" value="'+$total_page +'" />';
		return_html += 			'<input type="hidden" name="navi_parameter" value="'+navi_parameter+'" />';
		return_html += 			'<span class="total">전체 <span class="bold">'+$total_page +'</span>페이지 중</span>';
		return_html += 			'<label for="pagemove_input">이동할 페이지 입력</label>';
		return_html += 			'<input type="text" id="pagemove_input" />';
		return_html += 			'<a href="#none" class="pagemove_btn"><span class="text_hidden">입력한</span>페이지로 이동</a>';
		return_html += 		'</div>';
		}		
		return_html += 	'</div>';

		return return_html;
		
	}
	
	/* 목록 출력 */
	function print_list(t, data, img_path, size, callback ){		
		var html ='';		
		console.log( t );
		if( data != undefined ){
			if ( data.length > 0 ){
					$.each(data, function(e,i){						
						if( t == "sort" ){
							html += '<div class="item">';
							html += 	'<div class="cont_box">';
							html += 		'<div class="left_box">';
							html += 			'<div class="check_box">';
							html += 				'<input type="checkbox" name="check_idx[]" id="popup_check'+e+'" class="box_hidden" value="'+data[e].idx+'" />';
							html += 				'<label for="popup_check'+e+'">팝업 선택</label>';
							html += 				'</div>';
							//html += 			'<div class="thumb_box"><img src="'+img_path+'83x60/'+data[e].photo_name+'" alt="'+data[e].title+'" /></div>';
							html += 			'<div class="thumb_box"><img src="'+img_path+'83x1/'+data[e].photo_name+'" alt="'+data[e].title+'" /></div>';
							html += 		'</div>';
							html += 		'<div class="right_box">';
							html += 			'<div class="cont">';
							html += 				'<h3>'+data[e].title+' </h3>';
							html += 				'<span class="date">'+data[e].top_start+' ~ '+data[e].top_end+'</span>';
							html += 			'</div>';
							html += 		'</div>';
							html += 	'</div>';
							html += 	'<div class="move_icon"><span class="icon"></span>이동</div>';
							html += '</div>';
						}else{
							html += '<div class="item">';
							html += 	'<a href="?mode=view&idx='+data[e].idx+'" title="'+data[e].title+' 에 대한 글내용 보기.">';
							html += 		'<div class="hover_box"><div class="lt"></div><div class="lr"></div><div class="lb"></div><div class="ll"></div></div>';
							html += 		'<div class="thumb_box"><img src="'+img_path+size+'/'+data[e].photo_name+'" alt="'+data[e].title+'" /></div>';
							html += 		'<div class="cont_box">';
							html += 			'<span class="date">'+data[e].top_start+' ~ '+data[e].top_end+'</span>';	
							html += 			'<h3>'+data[e].title+'</h3>';
							html += 		'</div>';
							html += 	'</a>';
							html += '</div>';							
						}						

					});

				}else{
					html = '';
				}
		}
		if( t == "sort" ){		
			html = '<div class="dag_box">'+html+'</div>';
			html += '<div class="board_manager_btn">';							
			/*html += 	'<input type="hidden" name="mode" value="delete_all" />';*/
			/*html += 	'<a href="#none" class="all_check"><span class="icon"></span>전체선택</a>' ;*/
			html += 	'<a href="#none" class="check_delete"><span class="icon"></span>선택삭제</a>' ;
			html += '</div>';
			
			html = '<form method="post" name="form_del" id="form_del" action="">'+html +'</form>';
		}
		
		callback(html);
		/*return html;			*/
	}

	function sortable(){
		/*드래그박스*/
		$(document).find(".module_list_box .board_popup .change_list .dag_box").sortable({
			cursor: "move",
			axis: "y",
			start: function(event, ui){
				$(".module_list_box .board_popup .change_list .item").css("transition","none");
				console.log('start');
			},
			stop: function(event, ui){
				$(".module_list_box .board_popup .change_list .item").css("transition","ease-in-out 0.3s");
				console.log('stop');
				sorting();
			},
		});

		$(".write_contbox .module_w").disableSelection();
	}

	function sorting(){
		var formData = new FormData();
		var idxs = new Array();  
		
		$("input[name='check_idx[]']").each(function(i){
			idxs.push($(this).val());
		});
		
		
		formData.append('mode', 'ajax_sorting');
		formData.append('return', 'json');
		formData.append("idxs", idxs);
		
		/*console.log( idxs );*/
		
		
		$.ajax({
			url : selfUrl
			, type : "POST" /*request type(POST, GET)*/
			,contentType: false
			,processData: false
			, data : formData /*보내는 데이터 request*/			
			/*요청결과수신 reponse*/
			, beforeSend : /*요청보내기전선처리*/
				function (response) {
					/*alert("beforeSend");*/
				}
			, success : 
				function (json) { /*성공*/				
					/*console.log( json );	*/				
					
				}
			, error : /*에러*/
				function (response) {
					console.log( response );
				}
			, complete : /*완료후*/
				function(response, textStatus, XMLHttpRequest){
					/*console.log( response );*/
				}
		});		
		
		return false;
	}

