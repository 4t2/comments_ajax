
window.onDomReady = initReady;

// Initialize event depending on browser
function initReady(fn)
{
	//W3C-compliant browser
	if (document.addEventListener)
	{
		document.addEventListener('DOMContentLoaded', fn, false);
	}
	else // IE
	{
		document.onreadystatechange = function() {readyState(fn)}
	}
}

// IE execute function
function readyState(func)
{
	// DOM is ready
	if (document.readyState == "interactive" || document.readyState == "complete")
	{
		func();
	}
}

window.onDomReady(function()
{
	var commentsPerPage = commentsConfig.perPage;

	var myRequest = new Request({
	    url: '/ajax.php',
	    method: 'get',
	    data: 'action=fmd&id='+commentsConfig.id+'&parent='+commentsConfig.parent,
	    onRequest: function()
	    {
	    	loadingSpinnerEl = new Element('p',
			{
				'id' : 'loading-spinner',
				'html' : '<i class="fa fa-refresh fa-spin fa-fw"></i>&#160;'+commentsConfig.textLoadingSpinner
			});

			$('comments').grab(loadingSpinnerEl, 'top');
	    	//console.log('onRequest');
	    },
	    onComplete: function()
	    {
	    	loadingSpinnerEl.dispose();
			//console.log('onComplete');
	    },
	    onSuccess: function(response)
	    {
	    	data = JSON.decode(response);
	    	comments = JSON.decode(data.content);

	    	console.log(comments);

	    	addComments(comments);
	    },
	    onFailure: function()
	    {
	        console.log('onFailure');
	    }
	});

	myRequest.send();


	function addComments(comments)
	{
		if (commentsPerPage == 0)
		{
			commentsPerPage = comments.length;
		}

		if (comments.length > commentsPerPage)
		{
			showMoreEl = new Element('div',
			{
				'id' : 'show-all-comment',
				'html' : '<i class="fa fa-plus-square-o fa-fw"></i>&#160;'+commentsConfig.textShowAllComments,
				'events' :
				{
					'click' : function()
					{
						for (i=commentsPerPage; i<comments.length; i++)
						{
							commentElement = getCommentBlock(comments[i]);

							$('show-all-comment').grab(commentElement, 'before');

							commentElement.highlight('#6a6');
						}

						$('show-all-comment').dispose();
					}
				}
			});
			
			$('comments').grab(showMoreEl, 'top');
		}

        for (i=Math.min(comments.length, commentsPerPage)-1; i>=0; i--)
		{
			commentElement = getCommentBlock(comments[i]);
			$('comments').grab(commentElement, 'top');
		}
	}

	function getCommentBlock(comment)
	{
		wrapperEl = new Element('div',
		{
			'id' : comment.id,
			'class' : 'comment_default '+comment.class
		});
		
		dateEl = new Element('p',
		{
			'class' : 'date'
		});
		dateEl.adopt(new Element('time',
		{
			'datetime' : comment.datetime,
			'html' : '<i class="fa fa-calendar fa-fw"></i>&#160;'+comment.date+'&#160;<i class="fa fa-clock-o fa-fw"></i>&#160;'+comment.time
		}));

		infoEl = new Element('p',
		{
			'class' : 'info',
			'html' : '<i class="fa fa-comment-o fa-fw"></i>&#160;'+comment.by+'&#160;'+comment.name
		});

		commentEl = new Element('div',
		{
			'class' : 'comment',
			'html' : comment.comment
		});

		wrapperEl.adopt(dateEl, infoEl, commentEl);

		if (comment.hasReply)
		{
			replyEl = new Element('div',
			{
				'class' : 'reply'
			});
			replyInfoEl = new Element('p',
			{
				'class' : 'info',
				'html' : '<i class="fa fa-comments-o fa-fw"></i>&#160;'+comment.rby+'&#160;'+comment.replyAutor
			});
			replyCommentEl = new Element('div',
			{
				'class' : 'comment',
				'html' : comment.replyComment
			});
			
			replyEl.adopt(replyInfoEl, replyCommentEl)
			
			wrapperEl.adopt(replyEl);
		}
		
		return wrapperEl;
	}

});