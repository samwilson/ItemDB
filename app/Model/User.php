<?php namespace Ormic\Model;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class User extends Base implements AuthenticatableContract {

    use Authenticatable;

    protected $rules = array(
        'username' => 'unique:users'
    );

    public function onCreated($user)
    {
        if (User::count() == 1 && !$user->isAdmin())
        {
            $adminRole = Role::firstOrCreate(array('name' => 'Administrator'));
            $user->roles()->attach($adminRole->id);
        }
    }

    public function roles()
    {
        return $this->belongsToMany('Ormic\Model\Role');
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = $value;
        if (empty($this->attributes['name']))
        {
            $this->attributes['name'] = $value;
        }
    }

    /**
     * Whether this User has a Role with the given name.
     * @param string $roleName
     */
    public function hasRole($roleName)
    {
        return $this->roles()->where('name', '=', $roleName)->count() == 1;
    }

    public function isAdmin()
    {
        return $this->roles()->where('id', '=', Role::ADMIN_ID)->count() > 0;
    }

}
