var prev_sort, next_sort, now_sort;

$(function () {
  /* 페이징 */
  $("#page_scale").on("change", function () {
    $("form[name='search_form1']").submit();
  });

  /* 목록 검색 */
  $(document).on("click", "input#search_word_btn", function () {

    $("input[name='search_date']").val('y');
    /*console.log( $("input[name='search_word']").val().length + ' / ' +  $("input[name='start_date']").val().length + ' / ' + $("input[name='finish_date']").val().length );*/

    if ($("input[name='search_word']").val().length == 0 && $("input[name='start_date']").val().length == 0 && $("input[name='finish_date']").val().length == 0) {
      modal({
        type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
        text: "검색어를 입력하세요.",
        callback: function (result) {
          $("input[name='search_word']").focus();
        }
      });
      return false;
    }
  });

  $("input[name='search_word']").on("keypress", function (key) {
    if (key.keyCode == 13) {
      $("input#search_word_btn").click();
      return false;
    }

  });

  /* 모듈 라벨 처리 */
  if ($("#search_word").length > 0) {
    if ($("#search_word").val().length > 0) {
      $("#search_word").siblings("label[for=search_word]").hide();
    }
  }

  $("#search_word").focus(function () {
    $(this).siblings("label[for=search_word]").hide();
  }).focusout(function () {
    if ($(this).val().length === 0) {
      $(this).siblings("label[for=search_word]").show();
    }
  });


  /* 공유하기 */
  /* a 태그에 data-type, data-url 이 있어야만 정상 작동. */
  $(document).on("click", "a.share_btn, #view_shear_btn > a.share_btn", function () {
    var type = $(this).data('type');
    var snsUrl = window.location.origin + $(this).data('url');
    var pageTitle = $(this).data('title');
    var goUrl = '';

    switch (type) {
      /*case "kakao" :
        sendLink(snsUrl);
        break;*/
      case "facebook" :
        goUrl = 'https://www.facebook.com/sharer.php?u=' + escape(snsUrl); /*+'&amp;t='+pageTitle; */
        window.open(goUrl);
        break;
      case "twitter" :
        /*	goUrl = 'http://twitter.com/home?status='+pageTitle+'%20'+escape(snsUrl);*/
        goUrl = 'https://twitter.com/intent/tweet?text=' + pageTitle + '&url=' + escape(snsUrl);
        window.open(goUrl);
        break;
      case "kakaostory" :
        /*goUrl = 'https://story.kakao.com/share?url='+escape(snsUrl)+'&amp;t='+pageTitle;
        window.open(goUrl); */
        sendStory(snsUrl, pageTitle);
        break;
      case "band" :
        goUrl = 'http://www.band.us/plugin/share?body=' + pageTitle + '%0A' + escape(snsUrl);
        window.open(goUrl);
        break;
      default :
        break;

    }

  });


  /*공유하기*/
  $(document).on("click", ".module_list_box .board_list_box .share_wrap .layor_btn_share", function () {
    $(this).siblings(".layor_share_box").stop().fadeToggle();
  });

  $(document).on("click", ".module_list_box .board_list_box .share_wrap .board_btn_share", function () {
    $(this).siblings(".board_share_box").stop().fadeToggle();
  });

  /*인기글*/
  var hot_list = $(".module_list_box .hot_board ul");
  var hot_list_count = hot_list.children().length;

  /*if(hot_list_count > 3){
    var hot_board = hot_list.bxSlider({
      minSlides: 2,
      maxSlides: 2,
      moveSlides: 1,
      slideWidth: 110,
      pager: false,
      controls: false,
      slideMargin: 5,
    });
  }else{
    $(".module_list_box .hot_board").addClass("no_slide");
  }*/
  if (hot_list_count <= 3) {
    $(".module_list_box .hot_board").addClass("no_slide");
  }

  $(".module_list_box .board_search_box .top_box .left_box a.btn_detail").on("click", function () {
    if ($(this).children("span.icon").hasClass("open") == true) {
      $(this).children("span.icon").removeClass("open").addClass("close");
      $(".module_list_box .board_search_box .bottom_box").stop().slideDown();
    } else {
      $(this).children("span.icon").removeClass("close").addClass("open");
      $(".module_list_box .board_search_box .bottom_box").stop().slideUp();
    }
  });

  /*날짜입력*/
  var now = new Date();
  var year = now.getFullYear();
  var mon = (now.getMonth() + 1) > 9 ? '' + (now.getMonth() + 1) : '0' + (now.getMonth() + 1);
  var day = now.getDate() > 9 ? '' + now.getDate() : '0' + now.getDate();
  var today = year + mon + day;
  //var idx = $(".view_layor").siblings("a.basic_cont").data("idx");
  var idx = $(".view_layor").parents("item").find("a.basic_cont").data("idx");

  $(".data_hidden").each(function () {
    $(this).val(today.replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3'));
  });
  $(".datetime").each(function () {
    $(this).val(today);
  });
  $('.datetime').datetimepicker({
    timepicker: false,
    format: 'Ymd',
    useCurrent: false,
    keepInvalid: true,
    lang: 'ko',
  }).on("change", function () {
    $(this).siblings(".data_hidden").val($(this).val().replace(/(\d{4})(\d{2})(\d{2})/, '$1-$2-$3'));
  });


  /* 상세보기 */
  $(document).on("click", ".module_list_box .board_list .item .thumb_box > a, .module_list_box .board_list .item > a, .module_list_box .board_list .item .cont_box > a:not(.link_viewbox) .module_list_box .board_list .item .thumb_box > a, .hot_board ul li.item > a, .module_list_box .board_list_box .board_list .item > a, .module_list_box .board_list_box .board_list .item .bottom_btn > a", function () {
    console.log(selfUrl + "self");

    if ($(this).hasClass('target')) {
      locaction.reload();
    }

    if ($(this).hasClass('list_lock')) {
      alert("비공개글입니다.");
      return false;
    }

    var view_base_layor = get_view_base_layor($(this).attr("title"), $(this).data('link'), $(this).data("idx"));
    //console.log(view_base_layor+"view_base_layor");

    if ($(this).hasClass('_viewClick')) {
      $(this).parents("li.item").append(view_base_layor);
    } else {
      $(this).parents(".item").append(view_base_layor);
    }

    loding($(this).siblings('div.view_layor'), "start", "fixed");

    $(document).find(".layor_header_box").after("<div class=\"view_cont_wrap\"><div class=\"cont_prev_box cont_scroll_box\"></div><div class=\"cont_active_box cont_scroll_box\"></div><div class=\"cont_next_box cont_scroll_box\"></div></div>");
    $(".view_layor .cont_scroll_box").width($(window).width());
    $(".view_layor .view_cont_wrap").width($(window).width() * 3).css("transform", "translateX(-" + $(window).width() + "px)");

    if ($(this).hasClass("notmine")) {
      alert("비공개글입니다.");
      return false;
    }

    var active_idx = $(this).data("idx");

    if ($(this).hasClass('_viewClick')) {
      var prev_idx = $(this).parents("li").prev("li").find("a").data("idx");
      var next_idx = $(this).parents("li").next("li").find("a").data("idx");
    } else {
      var prev_idx = $(this).parents(".item").prev(".item").find("a").data("idx");
      var next_idx = $(this).parents(".item").next(".item").find("a").data("idx");

      if (next_idx == undefined) {
        $("a#btn_more_news").click();
        setTimeout(function () {
          next_idx = $(this).parents(".item").next(".item").find("a").data("idx");
        }, 600);
      }
    }
    /*230720 권재영 페이지 로딩이 되는동안 기본적인 이벤트를 중지시킨다.*/
    /*터치차단*/
    $(".view_cont_wrap").css("pointer-events", "none");
    /*스와이프차단*/
    $(".view_cont_wrap").css("overscroll-behavior-x", "none");
    _action_slide_item($(".cont_prev_box"), prev_idx);
    _action_slide_item($(".cont_active_box"), active_idx, true);
    _action_slide_item($(".cont_next_box"), next_idx);
    setTimeout(function () {
      /*230720 권재영 페이지 로드 후 다시 이벤트 허용*/
      /*터치가능*/
      $(".view_cont_wrap").css("pointer-events", "auto");
      /*스와이프가능*/
      $(".view_cont_wrap").css("overscroll-behavior-x", "auto");
    }, 500);
    var lodingChk = setInterval(function () {
      if ($(document).find(".cont_active_box").html() != '') {
        setTimeout(function () {
          loding($("div.view_layor"), "end", "fixed");
          clearInterval(lodingChk);
        }, 600);
      }
    }, 600);

    /*
    $(".cont_prev_box").html(view_cont);
    $(".cont_active_box").html(view_cont);
    $(".cont_next_box").html(view_cont);*/

    $(document).find(".view_layor").show().stop().animate({"left": "0"}, 450, function () {
      $(".view_layor").css({"left": "auto", "right": "0"});
    });
    $("body").css("overflow", "hidden");

    function view_prev() {
      /*이전*/
      loding($('div.view_layor'), "start", "fixed");
      if ($(".view_layor .view_cont_wrap").find(".prev_box").html() == '' || $(".cont_active_box .module_view_box .board_view_pager .prev_box").hasClass("empty")) {
        modal({
          type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
          text: "처음페이지입니다",
          callback: function (result) {
            setTimeout(function () {
              loding($("div.view_layor"), "end", "fixed");
            }, 600);
          }
        });
        return false;

      }


      $(".view_layor .view_cont_wrap").addClass("on_transition").css({
        /*"transition":"ease-in-out 0.3s",*/
        "transform": "translateX(-" + $(window).width() * 2 + "px)"
      });

      setTimeout(function () {
        _action_view_insert("left", $(".view_layor .view_cont_wrap"));
      }, 300);


    }

    function view_next() {
      /*다음*/
      loding($('div.view_layor'), "start", "fixed");
      if ($(".view_layor .view_cont_wrap").find(".cont_prev_box").html() == '' || $(".cont_active_box .module_view_box .board_view_pager .prev_box").hasClass("empty")) {
        modal({
          type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
          text: "처음페이지입니다",
          callback: function (result) {
            setTimeout(function () {
              loding($("div.view_layor"), "end", "fixed");
            }, 600);
          }
        });
        return false;

      }

      $(".view_layor .view_cont_wrap").addClass("on_transition").css({
        /*"transition":"ease-in-out 0.3s",*/
        "transform": "translateX(0)"
      });


      setTimeout(function () {
        _action_view_insert("right", $(".view_layor .view_cont_wrap"));
      }, 300);

    }

    var vieSwipe = $(".view_layor .view_cont_wrap").swipe({
      /*터치스와이프*/
      swipe: function (event, direction, distance, duration, fingerCount, fingerData) {
        /*console.log(direction);
        console.log(distance);*/
        /*loding( $('div.view_layor') ,"start","fixed");	*/

        if (direction == "left") {
          view_next();
        } else if (direction == "right") {
          view_prev();
        }
      },
      /*이전글,다음글 클릭*/
      click: function (event, target) {
        if ($(target).parents("a").hasClass("exception_swipe")) {
          if ($(target).parents("a").hasClass("prev_box")) {
            view_prev();
          } else if ($(target).parents("a").hasClass("next_box")) {
            view_next();
          }
        }
      },
      threshold: 15,
      allowPageScroll: "vertical",
      excludedElements: "label, button, input, select, textarea, a:not(.exception_swipe), .noSwipe",
      triggerOnTouchEnd: false,
    });

  });

  /* 보기 창 닫기 */
  $(document).on("click", ".view_layor .layor_header_box .layor_close, a.layor_close", function () {
    $(this).parents(".view_layor").stop().animate({"right": "100%"}, 450, function () {
      $(document).find(".view_layor").hide();
      $("body").removeAttr("style");
      $(".view_layor").remove();
    });
  });


  /* 답변 수정 버튼 추가 */
  $(document).on("click", "a.rebtn_modify", function () {


    var idx = $(this).data("idx");
    var ori_cont = $(this).parents(".reply_wrap").find(".reply_cont").html().replaceAll(/\<br\>/gi, '');

    //console.log(ori_cont);

    $(this).parents().siblings(".reply_cont").hide();
    $(this).parents(".reply_wrap").append('<div class="edit_text_wrap"><textarea id="reply_input" name="reply_input">' + ori_cont + '</textarea><input type="hidden" name="idx" value="' + idx + '" /><input type="hidden" name="title" value="글에 대한 답변" /><div class="modal_btnbox"><ul><li class="btn_add"><a href="#none" class="reply_modify mobile" id="add_reply">확인</a></li><li class="btn_cancel"><a href="#none" class="modal_close modify_cancel modify_cancel">취소</a></li></ul></div></div>');
    $(this).parents(".reply_btn").hide();
  });

  /* 답변 입력 레이어 확인 버튼 */
  $(document).on("click", ".board_btn_box ul li.board_btn_reply a ", function () {
    var pidx = $(this).data("pidx");

    $.get(selfUrl + '?mode=view&return=json&idx=' + pidx, function (data) {
      setTimeout(function () {
        $("input[name=title]").val('[' + data.title + ']글에 대한 답변');
      }, 300);
    }, 'json');


    if ($(this).hasClass("active") == false) {
      $(this).addClass("active").parents(".board_btn_box").after('<div class="reply_layor"><form><strong>답변입력</strong><label for="reply_input" class="text_hidden">답변내용을 입력해주세요</label><textarea id="reply_input" name="reply_input"></textarea><input type="hidden" name="pidx" value="' + pidx + '" /><input type="hidden" name="title" value="글에 대한 답변" /><div class="modal_btnbox"><ul><li class="btn_add"><a href="#none" id="add_reply">확인</a></li><li class="btn_cancel"><a href="#none" class="modal_close">취소</a></li></ul></div></form></div>');
      $(".reply_layor").stop().slideDown();

    } else {

      $(".reply_layor").stop().slideUp(function () {
        $(this).remove();
        $(".board_btn_box ul li.board_btn_reply a").removeClass("active");
      });

    }
  });


  /* 답변 입력 레이어 취소 버튼 */
  $(document).on("click", " .modal_btnbox ul li.btn_cancel a", function () {

    if ($(this).hasClass("modify_cancel")) {
      $(this).parents(".edit_text_wrap").siblings(".reply_cont").show();
      $(this).parents(".edit_text_wrap").siblings(".reply_btn").show();
      $(this).parents(".edit_text_wrap").remove();
    } else {
      $(this).parents(".reply_layor").stop().slideUp(function () {
        $(this).remove();
        $(".board_btn_box ul li.board_btn_reply a").removeClass("active");
      });
    }
  });


  /* 답변입력 */
  $(document).on("click", "a#add_reply", function () {
    if ($(".reply_layor").find("textarea[name=reply_input]").val() == '') {
      modal({
        type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
        text: "답변을 입력하세요.",
        callback: function (result) {
          $(".reply_layor").find("textarea[name=reply_input]").focus();
        }
      });

      return false;
    }


    var formData = new FormData();
    var message = '';
    var type = '';
    if ($(this).hasClass("reply_modify")) {
      formData.append("idx", $("input[name=idx]").val());
      formData.append("mode", "reply_change");
      formData.append("contents", $("textarea#reply_input").val());
      formData.append("return", "json");
      /*console.log('ddd');*/
      message = "답변이 수정되었습니다.";
      type = 'modify';

    } else {
      formData.append("pidx", $("input[name=pidx]").val());
      formData.append("mode", "reply_save");

      formData.append("title", $("input[name=title]").val());
      formData.append("contents", $("textarea#reply_input").val());
      formData.append("return", "json");
      message = "답변이 등록되었습니다.";
      type = 'save';
    }

    $.ajax({
      url: selfUrl //request 보낼 서버의 경로
      , type: 'POST' // 메소드(get, post, put 등)
      , data: formData //보낼 데이터
      , processData: false
      , contentType: false
      , success: function (data) {
        /*		console.log( data );*/
        if (data == "ok") {
          modal({
            type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
            text: message,
            callback: function (result) {
              if (type == 'modify') {
                $(document).find(".view_layor .layor_header_box > a").click();
                $(document).find(".view_layor").siblings("a.basic_cont").click();
              } else {
                window.location.reload(true);
              }


            }
          });
          return false;
        }

      }
      , error: function (data) {
        /*		console.log(data);*/
      }
      , complete: function (response) {
        /*console.log( response );*/
      }
    });

  });

  /* 답변입력 */


  function get_view_base_layor(mname, viewUrl, idx) {
    var return_view = '<div class="view_layor" id="layor_view_wrap">';
    return_view += '<div class="layor_header_box">';
    return_view += '<a href="#none" class="layor_close"><span class="icon">레이어 닫기</span></a>';
    return_view += '<h3>' + mname + '</h3>';
    return_view += '<div class="share_wrap">';
    return_view += '<a href="#none" class="layor_btn_share share_btnn"><span class="icon">공유하기</span></a>';
    return_view += '<div class="layor_share_box">';
    return_view += '<ul class="share_btn" id="view_shear_btn">';
    //return_view +=										'<!-- li class="kakao share_btn"><a href="#none" data-type="kakao" data-title="'+mname+'" data-url="'+viewUrl+'" class="share_btn"><span class="icon share_btn">카카오톡</span></a></li -->';
    return_view += '<li class="facebook share_btn"><a href="#none" data-type="facebook" data-title="' + mname + '" data-url="' + viewUrl + '" class="share_btn"><span class="icon share_btn">페이스북</span></a></li>';
    return_view += '<li class="twitter share_btn"><a href="#none" data-type="twitter" data-title="' + mname + '"  data-url="' + viewUrl + '"  class="share_btn"><span class="icon  share_btn">엑스</span></a></li>';
    //return_view +=										'<li class="kakaostory share_btn"><a href="#none" data-type="kakaostory" data-title="'+mname+'"  data-url="'+viewUrl+'" class="share_btn"><span class="icon  share_btn">카카오스토리</span></a></li>';
    return_view += '<li class="band share_btn"><a href="#none" data-type="band" data-title="' + mname + '" data-url="' + viewUrl + '" class="share_btn"><span class="icon  share_btn">네이버밴드</span></a></li>';
    return_view += '</ul>';
    return_view += '</div>';
    return_view += '</div>';
    return_view += '</div>';
    return_view += '</div>';

    return return_view;

  }

  /* 하단 참고.
  $(".cont_prev_box").html(view_cont);
  $(".cont_active_box").html(view_cont);
  $(".cont_next_box").html(view_cont);
  */
  function _action_view_insert(type, obj) {
    /*	console.log(obj);*/
    /*230720 권재영 페이지 로딩이 되는동안 기본적인 이벤트를 중지시킨다.*/
    /*터치차단*/
    $(".view_cont_wrap").css("pointer-events", "none");
    /*스와이프차단*/
    $(".view_cont_wrap").css("overscroll-behavior-x", "none");
    switch (type) {
      case  "left":
        var tIdx = obj.find(".cont_next_box").data("idx");
        //console.log( tIdx );
        var goNextIdx = $(".board_list .item a[data-idx='" + tIdx + "']").parents(".item").next(".item").find("a").data("idx");
        //console.log( goNextIdx );

        if (goNextIdx == '' || goNextIdx == undefined) {
          //console.log( 'click' );
          $("a#btn_more_news").click();
          setTimeout(function () {
            goNextIdx = $(".board_list .item a[data-idx='" + tIdx + "']").parents(".item").next(".item").find("a").data("idx");
            //console.log( goNextIdx );

            obj.removeClass("on_transition");
            obj.children("div:first-child").remove();

            obj.find(".cont_active_box").addClass("cont_prev_box").removeClass("cont_active_box");
            obj.find(".cont_next_box").addClass("cont_active_box").removeClass("cont_next_box");
            obj.find(".cont_active_box").after('<div class="cont_scroll_box cont_next_box"></div>');
            obj.find(".cont_next_box").css({"width": $(window).width() + "px"});
            _action_slide_item(obj.find(".cont_next_box"), goNextIdx);

          }, 1200);
        } else {
          obj.removeClass("on_transition");
          obj.children("div:first-child").remove();

          obj.find(".cont_active_box").addClass("cont_prev_box").removeClass("cont_active_box");
          obj.find(".cont_next_box").addClass("cont_active_box").removeClass("cont_next_box");
          obj.find(".cont_active_box").after('<div class="cont_scroll_box cont_next_box"></div>');
          obj.find(".cont_next_box").css({"width": $(window).width() + "px"});
          _action_slide_item(obj.find(".cont_next_box"), goNextIdx);

        }


        var lodingChk = setInterval(function () {
          if ($(document).find(".cont_next_box").html() !== '' || goNextIdx == '' || goNextIdx == undefined) {
            setTimeout(function () {
              loding($("div.view_layor"), "end", "fixed");
              clearInterval(lodingChk);
            }, 600);
          }
        }, 600);


        obj.css({
          "transform": "translateX(-" + $(window).width() + "px)"
        });

        $(".module_view_box").animate({scrollTop: 0}, 300);

        break;
      case "right":

        var tIdx = obj.find(".cont_prev_box").data("idx");
        var goPrevIdx = $(".board_list .item a[data-idx='" + tIdx + "']").parents(".item").prev(".item").find("a").data("idx");


        obj.removeClass("on_transition");
        obj.children("div:last-child").remove();

        obj.find(".cont_active_box").addClass("cont_next_box").removeClass("cont_active_box");
        obj.find(".cont_prev_box").addClass("cont_active_box").removeClass("cont_prev_box");
        obj.find(".cont_active_box").before('<div class="cont_scroll_box cont_prev_box"></div>');
        obj.find(".cont_prev_box").css({"width": $(window).width() + "px"});
        _action_slide_item(obj.find(".cont_prev_box"), goPrevIdx);

        var lodingChk = setInterval(function () {
          if ($(document).find(".cont_prev_box").html() !== '' || goPrevIdx == '' || goPrevIdx == undefined) {
            setTimeout(function () {
              loding($("div.view_layor"), "end", "fixed");
              clearInterval(lodingChk);
            }, 600);
          }
        }, 600);

        obj.css({
          "transform": "translateX(-" + $(window).width() + "px)"
        });

        $(".module_view_box").animate({scrollTop: 0}, 300);

        break;
    }
    setTimeout(function () {
      /*230720 권재영 페이지 로드 후 다시 이벤트 허용*/
      /*터치가능*/
      $(".view_cont_wrap").css("pointer-events", "auto");
      /*스와이프가능*/
      $(".view_cont_wrap").css("overscroll-behavior-x", "auto");
    }, 500);


  }

  function _action_slide_item(thisObj, thisIdx, main = "false") {
    var first_url = selfUrl + "?mode=view&idx=" + thisIdx;
    thisObj.load(first_url + " div#module_content > .module_view_box", function (response, status, xhr) {
      if (status == "success") {
        /* 목록을 닫기 버튼으로 변경 */
        $("a#v_btn_list").attr("data-href", $("a#v_btn_list").attr("href"));
        $("a#v_btn_list").attr("href", "#none").addClass("layor_close");
        /* 다음/이전 글보기 슬라이딩을 위해서 액션 없애기 */
        $("a.next_box").attr("data-href", $("a#v_btn_list").attr("href"));
        $("a.next_box").attr("href", "#none");
        $("a.prev_box").attr("data-href", $("a#v_btn_list").attr("href"));
        $("a.prev_box").attr("href", "#none");
        setTimeout(function () {
        }, 300);

        /*동영상게시판 fixed 스크립트 / .load()함수는 스크립트 삭제되므로 레이어뷰 로딩끝나면 스크립트 추가 ##20200622김용선*/
        if ($(".module_list_box").hasClass("video_list_box")) {
          var ha = ($('.cont_active_box #video_view').offset().top + $('.cont_active_box #video_view').height());

          $(".cont_active_box .module_view_box").on("scroll", function () {
            if ($(".cont_active_box .module_view_box").scrollTop() > 300) {
              $(".cont_active_box #video_view").addClass('fixed_player');
              $('.cont_active_box .video_view, .cont_active_box .module_view_box').addClass('fixed_player');
            } else if ($(".cont_active_box .module_view_box").scrollTop() < 300) {
              $(".cont_active_box #video_view").removeClass('fixed_player');
              $('.cont_active_box .video_view, .cont_active_box .module_view_box').removeClass('fixed_player');
            } else {
              $(".cont_active_box #video_view").removeClass('fixed_player');
              $('.cont_active_box .video_view, .cont_active_box .module_view_box').removeClass('fixed_player');
            }
            ;
          });

          function playConfirm() {
            if (confirm("3G/LTE 등으로 재생 시 데이터사용료가 발생할 수 있습니다. 재생하시겠습니까?")) {
              $('#video_source').attr('src', $('#video_source').attr('data-ajax'));
              $('#video_view').get(0).play();
            } else {
              $('#video_view').get(0).pause();
            }
          }
        }

        /* 댓글 스크립트 못가져옮 */
        if ($(".module_list_box").hasClass("use_comment")) {
          $("#comment_input").focus(function () {
            $(this).siblings("label[for=" + $(this).attr("id") + "]").hide();
          }).focusout(function () {
            //console.log($(this).val());
            if ($(this).val() == '') {
              $(this).siblings("label[for=" + $(this).attr("id") + "]").show();
            } else {
              $(this).siblings("label[for=" + $(this).attr("id") + "]").hide();
            }
          });

          $('.icon_cm_modify').click(function () {
            var $this = $(this).parent().parent().children('.usr_comm');
            if ($this.attr('mode') == 'edit') return false;
            var $html = $this.html().replace(/<br>/g, "");
            var $idx = $(this).attr('id').replace('comment_', '');
            $this.html('<textarea class="w90" cols="15" rows="5">' + $html + '</textarea>');

            $this.attr('mode', 'edit');
            $this.find('textarea').focus();

            $this.find('textarea').blur(function () {
              $(this).parent().attr('mode', '');
              param = {contents: $(this).val(), idx: $idx};
              $.post("ybscript.io/module/comment/modify_comment", param).done(function (data) {
                //alert(data);
              });
              $(this).parent().html($(this).val().replace(/\n/g, '<br>'));

            });

          });

          /* 댓글 등록 */
          $('#comment_submit').click(function () {
            if ($("textarea#comment_input").val() == "") {
              modal({
                type: "alert", /* option : alert/confirm/prompt/success/warning/info/error/primary */
                text: "댓글을 입력해주세요.",
                callback: function (result) {
                  $('textarea#comment_input').focus();
                }
              });
              return false;
            } else {
              $("form#comment_form").submit();
            }
          });

          /* 댓글(실명인증) 등록 */
          $('#comment_submit_chk').click(function () {
            modal({
              type: "confirm", /* option : alert/confirm/prompt/success/warning/info/error/primary */
              text: "로그인 후 댓글등록이 가능합니다. 로그인 페이지로 이동하시겠습니까?",
              callback: function (result) {
                location.href = "<?php echo $_SYSTEM['rep_login']; ?>";
              }
            });
            return false;
          });

          $(".icon_cm_delete").click(function () {
            var result = confirm("삭제하시겠습니까?");
            if (result) location.replace(this.link_url);
            else return false;
          });

          function login_page_go() {
            if (confirm("로그인 후 댓글등록이 가능합니다. 로그인 페이지로 이동하시겠습니까?")) {
              location.href = "<?php echo $_SYSTEM['rep_login']; ?>";
            }
          }
        }


      } else {
        return false;
      }
    });
    thisObj.attr("data-idx", thisIdx);

  }

  /*
  로딩.
  type => start, end
  option => fixed, part
  */
  function loding(obj, type, option) {

    var html = '<div class="loding loding_' + option + '"><div class="loding_inner"><div class="spin_box"><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div><div><div></div></div></div><span class="text">로딩중입니다</span></div></div>';
    if (type == 'start') {
      obj.append(html);
    } else {
      /*console.log(obj);*/
      obj.find(".loding").remove();
    }

  }

});
