<?php

namespace SXFRating2\Pub\Controller;

use XF\Mvc\ParameterBag;
use XF\Pub\Controller\AbstractController;

class Rating extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		if ($params->rating_id)
		{
			return $this->rerouteController(__CLASS__, 'view', $params);
		}
		
		$visitor = \XF::visitor();
		
		if (!$visitor->canViewRating())
		{
			return $this->noPermission();
		}
		
		$page = $this->filterPage();
		$perPage = $this->options()->sxfrListPerPage;
		
		$find = $this->ratingFinder()->limitByPage($page, $perPage);
		
		$total = $find->total();
		
		$this->assertValidPage($page, $perPage, $total, 'ratings');
		
		$ratings = $find->fetch();
		
		$viewParams = [
			'ratings' => $ratings,

			'total' => $total,
			'page' => $page,
			'perPage' => $perPage
		];
		
		return $this->view('SXFRating2:Rating\Listing', 'sxfr_rating_list', $viewParams);
	}
	
	public function actionView(ParameterBag $params)
	{
		$rating = $this->assertRatingExists($params->rating_id);
		
		if (!$rating->isUse)
		{
			return $this->notFound();
		}
		
		$viewParams = [
			'rating' => $rating
		];
		
		return $this->view('SXFRating2:Rating\View', 'sxfr_rating_view', $viewParams);
	}
	
	protected function ratingFinder()
	{
		return \XF::finder('SXFRating2:Rating');
	}
	
	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \SXFRating2\Entity\Rating
	 */
	protected function assertRatingExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('SXFRating2:Rating', $id, $with, $phraseKey);
	}
}