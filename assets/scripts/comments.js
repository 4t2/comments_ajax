
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
	var myRequest = new Request({
	    url: '/ajax.php',
	    method: 'get',
	    data: 'action=fmd&id='+commentsConfig.id+'&parent='+commentsConfig.parent,
	    onRequest: function()
	    {
	    	loadingSpinnerEl = new Element('p',
			{
				'id' : 'loading-spinner',
				'html' : commentsConfig.textLoadingSpinner
			});

			$('comments').grab(loadingSpinnerEl, 'top');
	    },
	    onComplete: function()
	    {
	    	loadingSpinnerEl.dispose();
	    },
	    onSuccess: function(response)
	    {
	    	data = JSON.decode(response);
	    	comments = JSON.decode(data.content);

	    	addComments(comments);
	    },
	    onFailure: function()
	    {
	        loadingSpinnerEl.dispose();
	    }
	});

	myRequest.send();


	function addComments(comments)
	{
		if (commentsConfig.perPage == 0)
		{
			commentsConfig.perPage = comments.length;
		}

		if (comments.length > commentsConfig.perPage)
		{
			showMoreEl = new Element('div',
			{
				'id' : 'show-all-comment',
				'html' : commentsConfig.textShowAllComments,
				'events' :
				{
					'click' : function()
					{
						for (i=commentsConfig.perPage; i<comments.length; i++)
						{
							commentElement = Elements.from(comments[i]);
							commentElement.inject($('show-all-comment'), 'before');

							commentElement.highlight('#6a6');
						}

						$('show-all-comment').dispose();
					}
				}
			});
			
			$('comments').grab(showMoreEl, 'top');
		}

        for (i=Math.min(comments.length, commentsConfig.perPage)-1; i>=0; i--)
		{
			commentElement = Elements.from(comments[i]);
			commentElement.inject($('comments'), 'top');
		}
	}

});