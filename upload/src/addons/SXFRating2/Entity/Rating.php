<?php

namespace SXFRating2\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class Rating extends Entity
{
	public function hasFaIcon()
	{
		if ($this->icon && strpos($this->icon, 'fa-') === 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	protected function verifyCallback(&$class)
	{
		$class = trim($class);
		$class = trim($class, '\\');

		if (!\XF::$autoLoader->findFile($class))
		{
			$this->error(\XF::phrase('invalid_class_x', ['class' => $class]));
			return false;
		}

		return true;
	}
	
	public function getHandler()
	{
		$class = $this->callback;
		
		if (!\XF::$autoLoader->findFile($class))
		{
			return null;
		}
		
		$class = \XF::extendClass($class);
		
		$class = new $class($this->app());
		
		if (!($class instanceof \SXFRating2\Rating\AbstractRating))
		{
			return null;
		}
		
		return $class;
	}
	
	public function getList()
	{
		$handler = $this->handler;
		
		if ($handler)
		{
			return $handler->renderList();
		}
		
		return [];
	}
	
	public function getIsUse()
	{
		if ($this->isUpdate())
		{
			$handler = $this->handler;
			
			if (!$handler)
			{
				return false;
			}
			
			return $handler->getIsUse();
		}
		
		return true;
	}
	
	public function getVisitorPlace()
	{
		$handler = $this->handler;
		$visitor = \XF::visitor();
		
		if (!$visitor->user_id)
		{
			return false;
		}
		
		if (!$handler)
		{
			return false;
		}
		
		return $handler->renderUserPlace($visitor);
	}
	
	public function get($key)
	{
		if ($this->isUpdate() && $key === 'active')
		{
			if (!$this->getIsUse())
			{
				return false;
			}
		}
		
		return parent::get($key);
	}
	
	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_sxfr_rating';
		$structure->shortName = 'SXFRating2:Rating';
		$structure->primaryKey = 'rating_id';
		$structure->columns = [
			'rating_id' => ['type' => self::STR, 'nullable' => true],
			'title' => ['type' => self::STR, 'maxLength' => 50],
			'icon' => ['type' => self::STR, 'maxLength' => 100],
			'callback' => ['type' => self::STR, 'maxLength' => 50],
			'active' => ['type' => self::BOOL, 'default' => true]
		];
		
		$structure->getters = [
			'list' => true,
			'handler' => true,
			'isUse' => true,
			'visitorPlace' => true
		];

		return $structure;
	}
}