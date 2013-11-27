<?php

class CommentsAjax extends \Comments
{
	public function getComments(\stdClass $objConfig, $strSource, $intParent, $varNotifies)
	{
		global $objPage;

		$total = 0;
		$gtotal = 0;
		$arrComments = array();

		$objComments = \CommentsModel::findPublishedBySourceAndParent($strSource, $intParent, ($objConfig->order == 'descending'));

		// Parse the comments
		if ($objComments !== null && ($total = $objComments->count()) > 0)
		{
			$count = 0;

			if ($objConfig->template == '')
			{
				$objConfig->template = 'com_default';
			}

			$objPartial = new \FrontendTemplate($objConfig->template);

			while ($objComments->next())
			{
				$objPartial->setData($objComments->row());

				// Clean the RTE output
				if ($objPage->outputFormat == 'xhtml')
				{
					$objComments->comment = \String::toXhtml($objComments->comment);
				}
				else
				{
					$objComments->comment = \String::toHtml5($objComments->comment);
				}

				$objPartial->comment = trim(str_replace(array('{{', '}}'), array('&#123;&#123;', '&#125;&#125;'), $objComments->comment));

				$objPartial->datim = \Date::parse($objPage->datimFormat, $objComments->date);
				$objPartial->date = \Date::parse($objPage->dateFormat, $objComments->date);
				$objPartial->class = (($count < 1) ? ' first' : '') . (($count >= ($total - 1)) ? ' last' : '') . (($count % 2 == 0) ? ' even' : ' odd');
				$objPartial->by = $GLOBALS['TL_LANG']['MSC']['com_by'];
				$objPartial->id = 'c' . $objComments->id;
				$objPartial->timestamp = $objComments->date;
				$objPartial->datetime = date('Y-m-d\TH:i:sP', $objComments->date);
				$objPartial->addReply = false;

				// Reply
				if ($objComments->addReply && $objComments->reply != '')
				{
					if (($objAuthor = $objComments->getRelated('author')) !== null)
					{
						$objPartial->addReply = true;
						$objPartial->rby = $GLOBALS['TL_LANG']['MSC']['com_reply'];
						$objPartial->reply = $this->replaceInsertTags($objComments->reply);
						$objPartial->author = $objAuthor;

						// Clean the RTE output
						if ($objPage->outputFormat == 'xhtml')
						{
							$objPartial->reply = \String::toXhtml($objPartial->reply);
						}
						else
						{
							$objPartial->reply = \String::toHtml5($objPartial->reply);
						}
					}
				}

				$arrComments[] = $objPartial->parse();
				++$count;
			}
		}

		return $arrComments;
	}


	public function renderCommentForm(\FrontendTemplate $objTemplate, \stdClass $objConfig, $strSource, $intParent, $varNotifies)
	{
		$objTemplate->addComment = $GLOBALS['TL_LANG']['MSC']['addComment'];
		$objTemplate->name = $GLOBALS['TL_LANG']['MSC']['com_name'];
		$objTemplate->email = $GLOBALS['TL_LANG']['MSC']['com_email'];
		$objTemplate->website = $GLOBALS['TL_LANG']['MSC']['com_website'];
		$objTemplate->allowComments = true;

		parent::renderCommentForm($objTemplate, $objConfig, $strSource, $intParent, $varNotifies);
	}
}
