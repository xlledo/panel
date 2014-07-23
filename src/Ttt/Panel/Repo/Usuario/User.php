<?php namespace Ttt\Panel\Repo\Usuario;

class User extends \Cartalyst\Sentry\Users\Eloquent\User{

	public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

	public function cleanGroups()
	{
		return $this->groups()->detach();
	}
}
