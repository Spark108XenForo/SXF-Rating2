<?php

namespace SXFRating2\Rating;

abstract class AbstractRating
{
	/**
	 * @var App
	 */
	protected $app;
	
	protected $errors;
	
	abstract public function renderList();
	
	abstract public function renderUserPlace(\XF\Entity\User $user);
	
	public function __construct(\XF\App $app)
	{
		$this->app = $app;
		
		$this->errors = [];
	}
	
	public function getErrors()
	{
		return $this->errors;
	}
	
	public function getLimit()
	{
		$limit = $this->app->request->filter('limit', 'int');
		
		if (!$limit)
		{
			$limit = \XF::options()->sxfrRatingItemLimit;
		}
		
		return $limit;
	}
	
	public function getIsUse()
	{
		return true;
	}
	
	public function hasAddOn($addOnId)
	{
		$addOn = $this->em()->find('XF:AddOn', $addOnId);

		if (!$addOn)
		{
			return false;
		}
		
		$addOn = new \XF\AddOn\AddOn($addOn);
		
		if (!$addOn->isActive())
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * @return App
	 */
	public function app()
	{
		return $this->app;
	}

	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	public function db()
	{
		return $this->app->db();
	}

	/**
	 * @return \XF\Mvc\Entity\Manager
	 */
	public function em()
	{
		return $this->app->em();
	}

	/**
	 * @param string $repository
	 *
	 * @return \XF\Mvc\Entity\Repository
	 */
	public function repository($repository)
	{
		return $this->app->repository($repository);
	}

	/**
	 * @param $finder
	 *
	 * @return \XF\Mvc\Entity\Finder
	 */
	public function finder($finder)
	{
		return $this->app->finder($finder);
	}

	/**
	 * @param string $finder
	 * @param array $where
	 * @param string|array $with
	 *
	 * @return null|\XF\Mvc\Entity\Entity
	 */
	public function findOne($finder, array $where, $with = null)
	{
		return $this->app->em()->findOne($finder, $where, $with);
	}

	/**
	 * @param string $class
	 *
	 * @return \XF\Service\AbstractService
	 */
	public function service($class)
	{
		return call_user_func_array([$this->app, 'service'], func_get_args());
	}
}