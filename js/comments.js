function loadArticleComments(article_id) {
    if (article_id > 0) {

        ajaxQuery({'mode': 'get-article-comments', 'article': article_id}, function (data) {
            $('[data-comments-list]').html(data);
        });
    }
}

function getAddCommentForm(article_id, reply_id) {
    ajaxQuery({'mode': 'get-add-comment-form', 'article': article_id, 'reply': reply_id}, function (data) {
        $('[data-comment-form-wrapper]').html(data);
    });
}

function addCommentResponse(result, form) {
    form.fadeTo(200, 1);
    alert(result.message);

    if (result.status == 'ok') {
        loadArticleComments(result.article);
        $('[data-comment-form-wrapper]').html('');
    }
}


$(function() {
    loadArticleComments(parseInt($('[data-comments-list]').data('comments-list')));

    $('body').on('click', '[data-add-comment]', function (e) {
        e.preventDefault();
        getAddCommentForm($(this).data('add-comment'), $(this).data('reply-comment'));
    });
});