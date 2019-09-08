class CommentModel
{
    getParentComments(list_comment_url, onSuccess)
    {
        $.ajax({
            type: 'get',
            url: list_comment_url,
            success: function (comments) {
                console.log(comments);
                onSuccess(comments);
            },
            error: (xhr, textStatus) => {
                console.log(textStatus);
                console.log(xhr);
            }
        });

    }

    getChildComments(child_comments_url, parentId, onSuccess)
    {
        parentId = parseInt(parentId);

        if(parentId < 1 )
        {
            console.log('parent_id == '+parentId);
            return [];
        }

        $.ajax({
            type: 'post',
            url: child_comments_url,
            data: { "parent_id" : parentId },
            success: function (comments) {
                console.log(comments);
                onSuccess(comments);
            },
            error: (xhr, textStatus) => {
                console.log(textStatus);
                console.log(xhr);
            }
        });
    }

    getMoreChildComments(more_child_comments_url, parentId, lastChildCommentId, onSuccess)
    {
        parentId = parseInt(parentId);
        lastChildCommentId = parseInt(lastChildCommentId);

        if(parentId < 1 || lastChildCommentId < 1 )
        {
            console.log('parent_id == '+parentId+' | last child comment id == ' + lastChildCommentId);
            return [];
        }

        $.ajax({
            type: 'post',
            url: more_child_comments_url,
            data: { "parent_id" : parentId, "last_child_id" : lastChildCommentId},
            success: function (comments) {
                console.log(comments);
                onSuccess(comments);
            },
            error: (xhr, textStatus) => {
                console.log(textStatus);
                console.log(xhr);
            }
        });

    }

    postComment(post_comment_url, content, parentId = 0, replyTo = '', firstChild = false, onSuccess)
    {
        $.ajax({
            type: 'post',
            url: post_comment_url,
            data: { "parent_id" : parentId, 'content' : content, "replyTo" : replyTo, 'firstChild' : firstChild},
            success: function (comment) {
                console.log(comment);
                onSuccess(comment);
            },
            error: (xhr, textStatus) => {
                console.log(textStatus);
                console.log(xhr);
            }
        });

        return { id: 25, name: 'Shputin', content: content, hasChild: false, parent_id: parentId, replyTo: replyTo};
    }
}