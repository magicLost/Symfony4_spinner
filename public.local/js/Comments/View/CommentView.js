class CommentView
{

    showParentComments(
        comments,
        mainDivId,
        showAnswerCommentsButtonTitle,
        replyButtonValue,
        sendButtonValue,
        replyFormPlaceholder,
        granted,
        registerLinkTitle,
        loginLinkTitle,
        toCommentText,
        register_url,
        login_url
    )
    {
        if(comments === undefined)
            console.log('Bad comments');

        if(mainDivId === undefined || mainDivId === '')
            console.log('Bad div id');


        let html = '';

        const mainDiv = $(mainDivId);
        let renderReplyButton = true;

        if(granted === 'yes')
        {

            html += RenderComment.createAddCommentFormHtml(sendButtonValue, replyFormPlaceholder);

        }else
        {
            html += RenderComment.createAuthToCommentMessage(registerLinkTitle, loginLinkTitle, toCommentText, register_url, login_url);
            renderReplyButton = false;

        }

        html += RenderComment.arrayOfCommentsToHtml(comments, showAnswerCommentsButtonTitle, replyButtonValue, renderReplyButton);

        mainDiv.append(html);

    }


    //on reply button click
    showHideAddCommentForm(event)
    {
        event.preventDefault();
        const reply_link = $(event.target);

        //console.log(reply_link.parent().parent().siblings('.reply_form'));

        const form_div = reply_link.parent().parent().siblings('.reply_form');

        //console.log(form_div.html());

        if(!form_div.html())
        {
            //console.log($('.form-wrapper'));
            const form_wrapper = $('.form-wrapper').eq(0).clone().appendTo(form_div);
            form_wrapper.find('textarea').val('');
            return form_wrapper;
        }
        else
        {
            form_div.html('');
            return null;
        }


    }


    //On show answer button click
    changeShowAnswersButtonHtml(value, rotate)
    {
        return `<strong>${value}</strong>&nbsp;&nbsp;<i class="fa fa-chevron-left fa-rotate-${rotate}"></i>`;
    }

    showHideChildCommentDiv(child_comments_div, answer_link, hideAnswerCommentsButtonTitle, showAnswerCommentsButtonTitle)
    {
        if(child_comments_div.css('display') !== 'none')
        {
            //console.log('two');
            answer_link.html(this.changeShowAnswersButtonHtml(showAnswerCommentsButtonTitle, 270));
            child_comments_div.css('display', 'none');
        }
        else
        {
            //console.log('three');
            answer_link.html(this.changeShowAnswersButtonHtml(hideAnswerCommentsButtonTitle, 90));
            child_comments_div.css('display', 'block');
        }
    }

    showChildComment(child_comments_div, answer_link, comments_array, numberOfShownChildComments, showMoreCommentsButtonTitle, onShowMoreButtonClick, showAnswerCommentsButtonTitle, replyButtonValue, hideAnswerCommentsButtonTitle, granted)
    {
        if(comments_array.length === 0)
        {
            console.log("No comments");
            return;
        }

        let html_child_comments = RenderComment.arrayOfChildCommentsToHtml(comments_array, numberOfShownChildComments, showAnswerCommentsButtonTitle, replyButtonValue, (granted === 'yes'));
        //add button show more comments

        child_comments_div.append(html_child_comments);

        if(comments_array.length > numberOfShownChildComments)
            this.addButtonShowMoreAnswers(child_comments_div, showMoreCommentsButtonTitle, onShowMoreButtonClick);

        //change button
        answer_link.html(this.changeShowAnswersButtonHtml(hideAnswerCommentsButtonTitle, 90));

    }

    showMoreChildComment(child_comments_div, show_more_link, comments_array, numberOfShownChildComments, showAnswerCommentsButtonTitle, replyButtonValue, granted)
    {
        let child_comments_html = RenderComment.arrayOfChildCommentsToHtml(comments_array, numberOfShownChildComments, showAnswerCommentsButtonTitle, replyButtonValue, (granted === 'yes'));

        show_more_link.parent().before(child_comments_html);

        if(comments_array.length <= numberOfShownChildComments)
            show_more_link.parent().remove();
    }

    addButtonShowMoreAnswers(child_comments_div, showMoreCommentsButtonTitle, onShowMoreButtonClick)
    {

        const show_more_button_html = RenderComment.createShowMoreCommentsButton(showMoreCommentsButtonTitle);

        child_comments_div.append(show_more_button_html);

        child_comments_div.find('.show_more_answers').on('click', onShowMoreButtonClick);

    }

    showPostComment(comment, comment_form, showAnswerCommentsButtonTitle, replyButtonValue, hideAnswerCommentsButtonTitle, showMoreCommentsButtonTitle)
    {
        const comment_html = RenderComment.createCommentHtml(comment, showAnswerCommentsButtonTitle, replyButtonValue);

        if(comment.parentId === null)
        {
            console.log('one');
            //const comment_node = $('#comments').children('br').after(comment_html);
            const br = $('#comments').children('br');
            comment_form.find('textarea').val('');
            return $(comment_html).insertAfter(br);
        }
        else
        {
            console.log('two');

            const comment_wrapper = $('.comment-wrapper[data-id="' + comment.parentId + '"]');
            const show_child_comments = comment_wrapper.find('.show_child_comments');

            //do we have show answers button
            if(show_child_comments[0] !== undefined)
            {
                console.log('three');
                const child_div = show_child_comments.parent().find('.child_comments');

                //is comment div visible
                if(child_div.children().length === 0)
                {
                    console.log('five');

                    //show child div
                    //change answer_button
                    show_child_comments.find('.show_answers').html(this.changeShowAnswersButtonHtml(hideAnswerCommentsButtonTitle, 90));

                    //show show more button
                    const showMoreButtonHtml = RenderComment.createShowMoreCommentsButton(showMoreCommentsButtonTitle);

                    //remove reply form
                    comment_form.parent().parent().parent().remove();

                    //add comment to child div
                    return [
                        $(comment_html).prependTo(child_div),
                        $(showMoreButtonHtml).appendTo(child_div)
                    ];
                }
                else
                {
                    console.log('six');

                    //remove reply form
                    comment_form.parent().parent().parent().remove();

                    //add comment to child div
                    return $(comment_html).prependTo(child_div);
                }
            }
            else
            {
                console.log('four');
                const child_div = comment_wrapper.find('.child_comments');

                comment_form.parent().parent().parent().remove();

                //add hide answers button
                const hideAnswerButtonHtml = RenderComment.getShowAnswerButtonHtml(hideAnswerCommentsButtonTitle, '90');
                $(child_div).before(hideAnswerButtonHtml);

                return [
                    $(comment_html).prependTo(child_div),
                    comment_wrapper.find('a.show_answers')
                ];
            }



        }
    }

}