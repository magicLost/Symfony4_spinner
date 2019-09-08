class RenderComment
{
    static createCommentHtml(comment, showAnswerCommentsButtonTitle, replyButtonValue, renderReplyButton = true)
    {
        let replyTo_end = (comment.replyTo) ? '<i class="fa fa-share  pl-2 pr-2"></i>' + comment.replyTo : '';
        const date = new Date(comment.created);
        //const date_end = date.getDay()+'-'+date.getMonth()+'-'+date.getFullYear();

        const reply_button_html = `
                    <div class="row comment_footer text-center pl-2" >
                          <a class="text-info reply_link" href=""><strong>${replyButtonValue}</strong></a>
                          <span class="separator pl-2">·</span>
                    </div>
                    
                    <div class="row reply_form" ></div>
        `;

        let comment_html_start = `
          <div class="comment-wrapper col-lg-12 mb-2 bg-dark pb-2 rounded border-bottom border-muted" data-id=${comment.id} data-parentId=${comment.parentId}>

            <div class="row">
            
                <div class="col-md-1 comment_img pt-2">
                
                    <i class="fa fa-user fa-4x profile-picture" ></i>
                    
                </div>
                
                <div class="col-md-11 pt-2">
                
                    <div class="row comment_header" >
                    
                        <div class="col-md-3 text-left"><strong><span id="name">${comment.name}</span><span class="text-info" id="replyTo">${replyTo_end}</strong></span></div>
                        <time class="col-md-9 text-right">${date.toLocaleString()}</time>

                    </div>
                    <div class="row comment_body p-2" >

                         <p>
                            ${comment.content}
                         </p>

                    </div>
                    
                    

        `;

        comment_html_start += (renderReplyButton) ? reply_button_html : '';

        if(comment.hasChild)
        {
            comment_html_start += RenderComment.getShowAnswerButtonHtml(showAnswerCommentsButtonTitle, '270') + '<div class="row child_comments"></div>';

            /*comment_html_start += `
            
                <div class="row show_child_comments">
                        <div class="col-md-12 pl-4 pt-2 ">
                            <a class="text-warning show_answers" href=""><strong>${showAnswerCommentsButtonTitle}</strong>&nbsp;&nbsp;<i class="fa fa-chevron-left fa-rotate-270"></i></a>
                        </div>
                </div>
                     
                
                <div class="row child_comments"></div>
            
            `;*/
        }else if(comment.parentId === null){

            comment_html_start += ' <div class="row child_comments"></div>';

        }else
        {
            //comment_html_start += '<hr />';
        }

        let comment_html_end = `
                
                </div>
            </div>
        </div>
        `;

        return comment_html_start + comment_html_end;
    }

    static getShowAnswerButtonHtml(buttonValue, rotate)
    {

        return `
            
                <div class="row show_child_comments">
                        <div class="col-md-12 pl-4 pt-2 ">
                            <a class="text-warning show_answers" href=""><strong>${buttonValue}</strong>&nbsp;&nbsp;<i class="fa fa-chevron-left fa-rotate-${rotate}"></i></a>
                        </div>
                </div>
            
            `;
    }


    static createShowMoreCommentsButton(showMoreCommentsButtonTitle)
    {
        return `
                <div class="col-md-12 text-center">
                    <a class="text-warning show_more_answers" href=""><strong>${showMoreCommentsButtonTitle}</strong></a>
                </div>
        `;
    }

    static createAuthToCommentMessage(registerLinkTitle, loginLinkTitle, toCommentText, register_url, login_url)
    {
        return `
          <div class="row">
          <div class="col-md-12 text-center"><p><a href="${register_url}">${registerLinkTitle}</a>, <a href="${login_url}">${loginLinkTitle}</a> ${toCommentText}</p></div>
</div>
         <br />
        `;
    }

    static createAddCommentFormHtml(sendButtonValue, replyFormPlaceholder)
    {
        return `
          <div class="form-wrapper col-lg-12 mb-1 pb-1 bg-dark rounded">

            <div class="row">
            
                <div class="col-md-1 comment_img">
                
                    <i class="fa fa-user fa-4x profile-picture" ></i>
                    
                </div>
                
                <div class="col-md-11 pt-3">
                
                    <div class="row form_body" >
                        <textarea class="col-md-12" name="text" id="comment_text" cols="1" rows="2" placeholder="${replyFormPlaceholder}"></textarea>
                        <div class="col-md-12 text-right pt-2"><button class="btn btn-info btn-sm w-25 send_button">${sendButtonValue}</button></div>

                    </div>
                
                </div>
            </div>

        </div>
         <br />
        `;
    }

    static arrayOfCommentsToHtml(comments, showAnswerCommentsButtonTitle, replyButtonValue, renderReplyButton = true)
    {
        let commment_html = '';

        for(let i in comments)
        {
            commment_html +=  RenderComment.createCommentHtml(comments[i], showAnswerCommentsButtonTitle, replyButtonValue, renderReplyButton);
        }

        return commment_html;
    }

    static arrayOfChildCommentsToHtml(comments, numberOfShownChildComments, showAnswerCommentsButtonTitle, replyButtonValue, renderReplyButton = true)
    {
        let commment_html = '';
        let count = 0;

        for(let i in comments)
        {
            count++;
            if(count > numberOfShownChildComments)
                continue;
            commment_html +=  RenderComment.createCommentHtml(comments[i], showAnswerCommentsButtonTitle, replyButtonValue, renderReplyButton);
        }

        return commment_html;
    }
}