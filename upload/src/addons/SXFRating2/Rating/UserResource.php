<?php

namespace SXFRating2\Rating;

class UserResource extends AbstractRating
{
	public function renderList()
	{
		$limit = $this->getLimit();
		
		$users = $this->userFinder()->limit($limit)->fetch()->toArray();
		$users = array_values($users);
		
		return $users;
	}
	
	public function renderUserPlace(\XF\Entity\User $user)
	{
		$users = $this->userFinder()->fetch()->toArray();
		$users = array_values($users);
		
		foreach ($users as $key => $value)
		{
			if ($value->user_id == $user->user_id)
			{
				return $key + 1;
			}
		}
		
		return false;
	}
	
	public function getIsUse()
	{
		if (!$this->hasAddOn('XFRM'))
		{
			return false;
		}
		
		return true;
	}
	
	protected function userFinder()
	{
		return $this->finder('XF:User')->order('xfrm_resource_count', 'DESC');
	}
}