class CommentController
{
    constructor(commentModel, commentView, config)
    {
        this.commentModel = commentModel;
        this.commentView = commentView;
        this.config = config;
    }

    list_all()
    {
        this.commentModel.getParentComments(
            this.config.list_comment_url,
            (comments) => {
                this.commentView.showParentComments(
                    comments,
                    this.config.mainDivId,
                    this.config.renderConfig.showAnswerCommentsButtonTitle,
                    this.config.renderConfig.replyButtonValue,
                    this.config.renderConfig.sendButtonValue,
                    this.config.renderConfig.replyFormPlaceholder,
                    this.config.granted,
                    this.config.renderConfig.registerLinkTitle,
                    this.config.renderConfig.loginLinkTitle,
                    this.config.renderConfig.toCommentText,
                    this.config.register_url,
                    this.config.login_url,
                );

                $('a.show_answers').on('click', (event) => { this.onShowAnswersButtonClick(event); });

                if(this.config.granted === 'yes')
                {
                    $('a.reply_link').on('click', (event) => {  this.onReplyButtonClick(event); });
                    $('.send_button').on('click', (event) => { this.onSendButtonClick(event); })
                }

            });

    }

    onReplyButtonClick(event)
    {
        const form_wrapper = this.commentView.showHideAddCommentForm(event);

        if(form_wrapper !== null)
        {
            form_wrapper.find('.send_button').on('click', (event) => { this.onSendButtonClick(event); })
        }
    }

    onShowAnswersButtonClick(event)
    {
        event.preventDefault();

        const answer_link = $(event.target).parent();

        const comment_div = $(event.target).parent().parent().parent().parent().parent().parent();

        const child_comments_div = comment_div.find('.child_comments');

        if(!child_comments_div.html())
        {
            //get id from coment div
            const id = comment_div.attr('data-id');
            this.commentModel.getChildComments(
                this.config.child_comments_url,
                id,
                (comments_array) => {
                    this.commentView.showChildComment(
                        child_comments_div,
                        answer_link,
                        comments_array,
                        this.config.renderConfig.numberOfShownChildComments,
                        this.config.renderConfig.showMoreCommentsButtonTitle,
                        (event) => { this.onShowMoreButtonClick(event); },
                        this.config.renderConfig.showAnswerCommentsButtonTitle,
                        this.config.renderConfig.replyButtonValue,
                        this.config.renderConfig.hideAnswerCommentsButtonTitle,
                        this.config.granted
                    );

                    if(this.config.granted === 'yes')
                        child_comments_div.find('a.reply_link').on('click', (event) => { this.onReplyButtonClick(event); });

                }
            );
        }
        else
        {
            this.commentView.showHideChildCommentDiv(
                child_comments_div,
                answer_link,
                this.config.renderConfig.hideAnswerCommentsButtonTitle,
                this.config.renderConfig.showAnswerCommentsButtonTitle
            );
        }
    }

    onShowMoreButtonClick(event)
    {
        event.preventDefault();
        event.stopPropagation();

        const show_more_link = $(event.target).parent();

        const child_comments_div = show_more_link.parent().parent();

        const comment_div = child_comments_div.parent().parent().parent();

        //how many comments was shown
        //const numberOfPrevousComments = child_comments_div.children().length;

        //get array of comments
        const id = comment_div.attr('data-id');

        const lastChildCommentId = child_comments_div.children().last().prev().attr('data-id');


        //getMoreChildComments(more_child_comments_url, parentId, lastChildCommentId, onSuccess)
        this.commentModel.getMoreChildComments(
            this.config.more_child_comments_url,
            id,
            lastChildCommentId,
            (comments_array) => {
                this.commentView.showMoreChildComment(
                    child_comments_div,
                    show_more_link,
                    comments_array,
                    this.config.renderConfig.numberOfShownChildComments,
                    this.config.renderConfig.showAnswerCommentsButtonTitle,
                    this.config.renderConfig.replyButtonValue,
                    this.config.granted
                );

                if(this.config.granted === 'yes')
                    child_comments_div.find('a.reply_link').on('click', (event) => { this.onReplyButtonClick(event); });
            }
        );
    }

    onSendButtonClick(event)
    {
        event.preventDefault();
        event.stopPropagation();

        let content = '';
        let firstComment = false;
        let parent_id = 0;
        let replyTo = '';

        //is it child comment or not
        const comment_form = $(event.target).parent().parent();

        const main_comment = comment_form.parent().parent().parent().parent();

        content = comment_form.find('textarea').val();

        if(content === '')
            return;


        if(main_comment.attr('id') === 'comments')
        {
            //is it a parent comment

        }
        else
        {
            //if it's a child comment
            let comment_wrapper = comment_form.parent().parent().parent().parent().parent().parent().parent();

            //do we in child comments div
            let child_comment_div = comment_wrapper.parent();

            if(child_comment_div.attr('class') === 'row child_comments' )
            {
                //we in child_comment div

                //get replyTo
                replyTo = comment_wrapper.find('#name').html();
                //get parent_id
                parent_id = comment_wrapper.attr('data-parentId');
                let real_parent_id = comment_wrapper.parent().parent().parent().parent().attr('data-id');

                //console.log(real_parent_id + ' == ' + parent_id);
                if(parent_id !== real_parent_id)
                {
                    console.log('parent_id == '+parent_id + ' | real_parent_id == ' + real_parent_id);
                    parent_id = real_parent_id;
                }
            }
            else
            {
                //do parent comment has a child
                firstComment = (comment_wrapper.find('.child_comments').length > 0) ? false : true;
                parent_id = comment_wrapper.attr('data-id');

            }
        }

        this.commentModel.postComment(
            this.config.post_comment_url,
            content,
            parent_id,
            replyTo,
            firstComment,
            (comment) => {

                //console.log(comment);

                //show post comment
                const comment_node = this.commentView.showPostComment(
                    comment,
                    comment_form,
                    this.config.renderConfig.showAnswerCommentsButtonTitle,
                    this.config.renderConfig.replyButtonValue,
                    this.config.renderConfig.hideAnswerCommentsButtonTitle,
                    this.config.renderConfig.showMoreCommentsButtonTitle
                );

                if(comment_node instanceof Array)
                {
                    if(comment_node[1].prop("tagName") === 'A')
                    {
                        comment_node[0].find('.reply_link').on('click', (event) => { this.onReplyButtonClick(event); });
                        //show answers button
                        comment_node[1].on('click', (event) => { this.onShowAnswersButtonClick(event); });
                    }else
                    {
                        comment_node[0].find('.reply_link').on('click', (event) => { this.onReplyButtonClick(event); });
                        //show more answers button
                        comment_node[1].find('.show_more_answers').on('click', (event) => { this.onShowMoreButtonClick(event); });
                    }
                }
                else
                {
                    comment_node.find('.reply_link').on('click', (event) => { this.onReplyButtonClick(event); });
                }

            }
        );


    }
}