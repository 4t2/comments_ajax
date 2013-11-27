<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

class ModuleCommentsAjax extends \ModuleComments
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_comments_ajax';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD']['comments'][0]) . ' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	public function generateAjax()
	{
		global $objPage;

		$intParent = (int) \Input::get('parent');
		$objPage = \PageModel::findWithDetails($intParent);

		// Get the page layout
		$objLayout = $this->getPageLayout($objPage);

		// Set the layout template and template group
		$objPage->template = $objLayout->template ?: 'fe_page';
		$objPage->templateGroup = $objLayout->getRelated('pid')->templates;

		$strSource = 'tl_page';

		$this->import('CommentsAjax', 'Comments');
		$objConfig = new \stdClass();

		$objConfig->perPage = $this->perPage;
		$objConfig->order = $this->com_order;
		$objConfig->template = $this->com_template;
		$objConfig->requireLogin = $this->com_requireLogin;
		$objConfig->disableCaptcha = $this->com_disableCaptcha;
		$objConfig->bbcode = $this->com_bbcode;
		$objConfig->moderate = $this->com_moderate;

		$arrComments = $this->Comments->getComments($objConfig, $strSource, $intParent, $GLOBALS['TL_ADMIN_EMAIL']);

		return json_encode($arrComments);
	}


	protected function compile()
	{
		global $objPage;

		$this->import('CommentsAjax', 'Comments');
		$objConfig = new \stdClass();

		$objConfig->perPage = $this->perPage;
		$objConfig->order = $this->com_order;
		$objConfig->template = $this->com_template;
		$objConfig->requireLogin = $this->com_requireLogin;
		$objConfig->disableCaptcha = $this->com_disableCaptcha;
		$objConfig->bbcode = $this->com_bbcode;
		$objConfig->moderate = $this->com_moderate;

		$this->Comments->renderCommentForm($this->Template, $objConfig, 'tl_page', $objPage->id, $GLOBALS['TL_ADMIN_EMAIL']);

		$valCommentsCount = \CommentsModel::countPublishedBySourceAndParent('tl_page', $objPage->id);

		$this->Template->commentsCount = $valCommentsCount;

		if ($valCommentsCount > 0)
		{
			$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/comments_ajax/assets/scripts/comments.js';
		}

	}


	/**
	 * needed for the Ajax call
	 */
	protected function getPageLayout($objPage)
	{
		$blnMobile = ($objPage->mobileLayout && \Environment::get('agent')->mobile);

		// Override the autodetected value
		if (\Input::cookie('TL_VIEW') == 'mobile' && $objPage->mobileLayout)
		{
			$blnMobile = true;
		}
		elseif (\Input::cookie('TL_VIEW') == 'desktop')
		{
			$blnMobile = false;
		}

		$intId = $blnMobile ? $objPage->mobileLayout : $objPage->layout;
		$objLayout = \LayoutModel::findByPk($intId);

		// Die if there is no layout
		if ($objLayout === null)
		{
			header('HTTP/1.1 501 Not Implemented');
			$this->log('Could not find layout ID "' . $intId . '"', 'PageRegular getPageLayout()', TL_ERROR);
			die('No layout specified');
		}

		return $objLayout;
	}
}
