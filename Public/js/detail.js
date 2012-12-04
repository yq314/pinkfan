$(function(){
    $('a.like-img').hover(function(){
        $(this).next('.like-count').text('+1');
    }, function(){
        var $like_count = $(this).next('.like-count');
        $like_count.text($like_count.attr('data'));
    }).click(function(){
        var $this = $(this);
        var $like_count = $this.next('.like-count');
        var count = parseInt($like_count.attr('data'));
        pfUtil.like($(this).attr('data'), function(){
            $like_count.attr('data', count+1).text(count+1);
            $this.replaceWith('<span class="like-img fn-left">喜欢</span>');
            $('.like-ok').fadeIn();
            setTimeout(function(){
                $('.like-ok').fadeOut();
            }, 2000);
        });
        return false;
    });
});