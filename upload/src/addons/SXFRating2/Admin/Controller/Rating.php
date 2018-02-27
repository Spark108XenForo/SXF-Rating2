<?php

namespace SXFRating2\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;
use XF\Admin\Controller\AbstractController;
use SXFRating2\Entity\Rating as EntityRating;

class Rating extends AbstractController
{
	public function actionIndex()
	{
		$ratings = \XF::finder('SXFRating2:Rating')->order('title')->fetch();
		
		$viewParams = [
			'ratings' => $ratings
		];
		
		return $this->view('SXFRating2:Rating\Listing', 'sxfr_rating_list', $viewParams);
	}

	protected function ratingEdit(EntityRating $rating)
	{
		$viewParams = [
			'rating' => $rating
		];
		
		return $this->view('SXFRating2:Rating\Edit', 'sxfr_rating_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$rating = $this->assertRatingExists($params->rating_id);
		
		return $this->ratingEdit($rating);
	}

	public function actionAdd()
	{
		$rating = $this->em()->create('SXFRating2:Rating');
		
		return $this->ratingEdit($rating);
	}

	protected function ratingSaveProcess(EntityRating $rating)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'rating_id' => 'str',
			'title' => 'str',
			'icon' => 'str',
			'callback' => 'str',
			'active' => 'bool'
		]);

		$form->basicEntitySave($rating, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->rating_id)
		{
			$rating = $this->assertRatingExists($params->rating_id);
		}
		else
		{
			$rating = $this->em()->create('SXFRating2:Rating');
		}

		$this->ratingSaveProcess($rating)->run();
		
		if ($this->request->exists('exit'))
		{
			$redirect = $this->buildLink('sxfr-ratings') . $this->buildLinkHash($rating->rating_id);
		}
		else
		{
			$redirect = $this->buildLink('sxfr-ratings/edit', $rating);
		}

		return $this->redirect($redirect);
	}

	public function actionDelete(ParameterBag $params)
	{
		$rating = $this->assertRatingExists($params->rating_id);
		
		if (!$rating->preDelete())
		{
			return $this->error($rating->getErrors());
		}

		if ($this->isPost())
		{
			$rating->delete();

			return $this->redirect($this->buildLink('sxfr-ratings'));
		}
		else
		{
			$viewParams = [
				'rating' => $rating
			];
			
			return $this->view('SXFRating2:Rating\Delete', 'sxfr_rating_delete', $viewParams);
		}
	}
	
	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		
		return $plugin->actionToggle('SXFRating2:Rating', 'active');
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