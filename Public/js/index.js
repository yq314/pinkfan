$(function(){
    $('.goods').live('mouseover', function(){
        $(this).find('a.like-img').show();
    }).live('mouseout', function(){
        $(this).find('a.like-img').hide();
    });

    $('a.like-img').live('click', function(){
        var $this = $(this);
        pfUtil.like($this.attr('data'), function(){
            var $likeok = $this.next('.like-ok');
            $this.parent().next().next().find('a.like-num').addClass('like-num-on').trigger('mouseover').click();
            $this.remove();
            $likeok.fadeIn();
            setTimeout(function(){
                $likeok.fadeOut();
            }, 2000);
        });
        return false;
    });

    $('a.like-num').live('click', function(){
        var $this = $(this);
        var $like_count = $(this).find('.like-count');
        var count = parseInt($like_count.attr('data')) + 1;
        $like_count.text(count);
        $this.next('i').animate({'font-size':'18px','opacity':'0'}, {complete: function(){
            $(this).hide();
            $this.replaceWith('<span class="fn-left like-num like-num-on">喜欢：<em>' + count + '</em></span>');
        }, duration:'slow'});
        return false;
    }).live('mouseover', function(){
        $(this).next('i').show();
    }).live('mouseout', function(){
        var $i = $(this).next('i');
        var $like_count = $(this).find('.like-count');
        if(!$i.is(':animated')){
            var count = $like_count.attr('data');
            $like_count.text(count);
            $i.hide();
        }
    });

    var $page = $('#page-nav');
    $page.pagination(goodsPage.total, {
        num_edge_entries: 2,
        num_display_entries: 4,
        items_per_page: 80,
        current_page:(parseInt(goodsPage.curPage)-1),
        isJump:true,
        callback: pageselectCallback
    });

    function pageselectCallback(page_index, jq){
		var pageA = $page.find("a");
		var prevA = $page.find("a.prev");
		var nextA = $page.find("a.next");
		pageA.each(function(){
			$(this).attr("href", goodsPage.query + "&p=" + $(this).text());
		});
		if(prevA[0]){
			prevA.attr("href", goodsPage.query + "&p=" + (parseInt(goodsPage.curPage) - 1));
		}
		if(nextA[0]){
			nextA.attr("href", goodsPage.query + "&p=" + (parseInt(goodsPage.curPage) + 1));
		}
		pageA.live("click",function(){
			window.location.href = $(this).attr("href");
		});
		return false;
	}

    $('#water-fall').masonry({
        itemSelector : '.goods',
        columnWidth : 230,
        gutterWidth : 10
    });

    function loadData(){
        goodsPage.loading = true;
        $.getJSON('http://pinkfan.sinaapp.com/index/getData/' + goodsPage.query + '&callback=?', {
            cur_page : goodsPage.curPage,
            sub_page : goodsPage.subPage
        }, function(data){
            if(data == ''){
                var str = (goodsPage.subPage == 1) ? '咦...糟糕！<br />这里连朵浮云都木有' : '唔...坏蛋！<br />已经被你拉到底了啦';
                $('#goods-loading').html(str).show();
                if(goodsPage.subPage != 1){
                    setTimeout(function(){
                        $('#goods-loading').hide();
                    }, 1500);
                }
                return true;
            }
            goodsPage.subPage += 1;
            var $newElem = $(data).css({opacity:0});
            $('#goods-loading').hide();
            $('#water-fall').append($newElem).masonry('appended', $newElem);
            $newElem.animate({opacity:1}, 1500);
            goodsPage.loading = false;

//            //读取pinglun.la数据
//            var tmp_cc_arr = [];
//            $newElem.find('.pll_comment_count_tag').each(function(){
//                tmp_cc_arr.push($(this).attr('href'));
//            });
//            var dict_ = {
//                "page_urls": tmp_cc_arr.join("-.-"),
//                "sha1": '72cbdb350c66f6a14084dfb5b7c04cda4cd25966'
//            };
//
//            var pll_ = document.createElement('script');
//            pll_.type = 'text/javascript';
//            pll_.charset = 'utf-8';
//            pll_.src = 'http://pinglun.la/manage2/get_comment_count/?' + pfUtil.serialize(dict_); (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(pll_);
        });
    }

//    loadData();

    $(window).scroll(function(){
        if(!goodsPage.loading && goodsPage.subPage < 6 && $(this).scrollTop() >= $(document).height() - $(this).height() - $('#footer').height()){
            $('#goods-loading').css({marginTop:'-50px'}).show();
            loadData();
        }
        if(goodsPage.subPage == 6){
            $('#page-box').show();
        }
    });
});