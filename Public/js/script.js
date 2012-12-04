/**
 * escrolltotop jquery回到顶部插件，平滑返回顶部、
 *
 * 参数设置
 *   startline : 出现返回顶部按钮离顶部的距离
 *   scrollto : 滚动到距离顶部的距离，或者某个id元素的位置
 *   scrollduration : 平滑滚动时间
 *   fadeduration : 淡入淡出时间 eg:[ 500, 100 ] [0]淡入、[1]淡出
 *   controlHTML : html代码
 *   className ：样式名称
 *   titleName : 回到顶部的title属性
 *   offsetx : 回到顶部 right 偏移位置
 *   offsety : 回到顶部 bottom 偏移位置
 *   anchorkeyword : 猫点链接
 * eg:
 *   $.scrolltotop({
 *   	scrollduration: 1000
 *   });
 */
(function($){
	$.scrolltotop = function(options){
		options = jQuery.extend({
			startline : 100,				//出现返回顶部按钮离顶部的距离
			scrollto : 0,					//滚动到距离顶部的距离，或者某个id元素的位置
			scrollduration : 500,			//平滑滚动时间
			fadeduration : [ 500, 100 ],	//淡入淡出时间 ，[0]淡入、[1]淡出
			controlHTML : '<a href="javascript:;"><b>回到顶部↑</b></a>',		//html代码
			className: '',					//样式名称
			titleName: '回到顶部',				//回到顶部的title属性
			offsetx : 25,					//回到顶部 right 偏移位置
			offsety : 85,					//回到顶部 bottom 偏移位置
			anchorkeyword : '#top' 		//猫点链接
		}, options);

		var state = {
			isvisible : false,
			shouldvisible : false
		};

		var current = this;

		var $body,$control,$cssfixedsupport;

		var init = function(){
			var iebrws = document.all;
			$cssfixedsupport = !iebrws || iebrws
					&& document.compatMode == "CSS1Compat"
					&& window.XMLHttpRequest
			$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
			$control = $('<div class="'+options.className+'" id="topcontrol">' + options.controlHTML + '</div>').css({
				position : $cssfixedsupport ? 'fixed': 'absolute',
				bottom : options.offsety,
				right : options.offsetx,
				opacity : 0,
				cursor : 'pointer'
			}).attr({
				title : options.titleName
			}).click(function() {
				scrollup();
				return false;
			}).appendTo('body');
			if (document.all && !window.XMLHttpRequest && $control.text() != ''){
				$control.css({
					width : $control.width()
				});
			}
			togglecontrol();
			$('a[href="' + options.anchorkeyword + '"]').click(function() {
				scrollup();
				return false;
			});
			$(window).bind('scroll resize', function(e) {
				togglecontrol();
			})

			return current;
		};

		var scrollup = function() {
			if (!$cssfixedsupport){
				$control.css( {
					opacity : 0
				});
			}
			var dest = isNaN(options.scrollto) ? parseInt(options.scrollto): options.scrollto;
			if(typeof dest == "string"){
				dest = jQuery('#' + dest).length >= 1 ? jQuery('#' + dest).offset().top : 0;
			}
			$body.animate( {
				scrollTop : dest
			}, options.scrollduration);
		};

		var keepfixed = function() {
			var $window = jQuery(window);
			var controlx = $window.scrollLeft() + $window.width()
					- $control.width() - options.offsetx;
			var controly = $window.scrollTop() + $window.height()
					- $control.height() - options.offsety;
			$control.css( {
				left : controlx + 'px',
				top : controly + 'px'
			});
		};

		var togglecontrol = function() {
			var scrolltop = jQuery(window).scrollTop();
			if (!$cssfixedsupport){
				keepfixed()
			}
			state.shouldvisible = (scrolltop >= options.startline) ? true : false;
			if (state.shouldvisible && !state.isvisible) {
				$control.stop().animate( {
					opacity : 1
				}, options.fadeduration[0]);
				state.isvisible = true;
			} else if (state.shouldvisible == false && state.isvisible) {
				$control.stop().animate( {
					opacity : 0
				}, options.fadeduration[1]);
				state.isvisible = false;
			}
		};

		return init();
	};
})(jQuery);

/**
 * emptyValue 默认关键字效果
 */
(function($){
	$.fn.emptyValue = function(arg){
		this.each(function(){
			var input = $(this);
			var options = arg;
			if(typeof options == "string"){
				options = {empty: options}
			}
			options = jQuery.extend({
				empty: input.attr("placeholder")||"",
				className: "gray"
			}, options);
			return input.focus(function(){
				$(this).removeClass(options.className);
				if($(this).val() == options.empty){
					$(this).val("");
				}
			}).blur(function(){
				if($(this).val()==""){
					$(this).val(options.empty);
				}
				$(this).addClass(options.className);
			}).blur();
		});
	};
})(jQuery);

$(function(){
    $.scrolltotop({className:'totop'});
    $('#q').emptyValue();
    $('#search_btn').click(function(){
        $('#search_form').submit();
        return false;
    });
    $('#search_form').submit(function(){
        var $q = $('#q');
        if($q.val() == $q.attr('placeholder') || $.trim($q.val()) == ''){
            alert('你不说我怎么知道你要找什么呢？');
            $q.focus();
            return false;
        }
    });

    $(window).scroll(function(){
        if($(window).scrollTop() > $('#m-header').height()){
            $('#m-nav').addClass('nav-fixed');
        }else{
            $('#m-nav').removeClass('nav-fixed');
        }
    });
});

var pfUtil = {
    like : function(id, callback){
        $.getJSON('http://pinkfan.sinaapp.com/like/add?callback=?', {
            'id' : id
        }, function(data){
            if(data.status){
                callback();
            }else{
                alert(data.info);
            }
            return false;
        });
    },

    serialize : function(a){
        var b = [];
        for (var p in a) {
            b.push(p + '=' + encodeURIComponent(a[p]))
        }
        return b.join('&')
    }
}

function pll_init_comment_count_links(a) {
    $(".pll_comment_count_tag").each(function(){
        var $this = $(this);
        var href = $this.attr('href');
        $this.attr('href', href + '#pinglunla_here');
        $this.find('em').html(a[href]);
        $this.removeClass('pll_comment_count_tag');
    });
}