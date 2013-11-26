<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

class ModuleCommentsAjax extends \ModuleComments
{

	public function generate()
	{
		if (TL_MODE == 'FE')
		{
			$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/comments_ajax/assets/scripts/comments.js';
		}

		return parent::generate();
	}

	public function generateAjax()
	{
		$arrComments = array();
		$strSource = 'tl_page';
		$intParent = (int) \Input::get('parent');

		$objPage = \PageModel::findWithDetails($intParent);

		$objComments = \CommentsModel::findPublishedBySourceAndParent($strSource, $intParent, ($this->com_order == 'descending'));

		// Parse the comments
		if ($objComments !== null && ($total = $objComments->count()) > 0)
		{
			$count = 0;

			$objPartial = new \stdClass();

			while ($objComments->next())
			{
				// Clean the RTE output
				if ($objPage->outputFormat == 'xhtml')
				{
					$objPartial->comment = \String::toXhtml($objComments->comment);
				}
				else
				{
					$objPartial->comment = \String::toHtml5($objComments->comment);
				}

				$objPartial->name = $objComments->name;

				$objPartial->datim = \Date::parse($objPage->datimFormat, $objComments->date);
				$objPartial->time = \Date::parse($objPage->timeFormat, $objComments->timestamp);
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

				$arrComments[] = clone $objPartial;
				++$count;
			}
		}

		return json_encode($arrComments);
	}
	
}
