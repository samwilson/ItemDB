<?php namespace Ormic\Model;

use Illuminate\Support\Facades\DB;

abstract class Base extends \Illuminate\Database\Eloquent\Model {

    /** @var boolean */
    public $timestamps = false;
    protected $hasOne = array();
    protected $hasMany = array();

    /** @var array|Column */
    protected $columns;

    /** @var User */
    protected $user;

    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        if (method_exists($this, 'onCreated'))
        {
            self::created(array($this, 'onCreated'));
        }
    }

    public function getHasOne()
    {
        return $this->hasOne;
    }

    public function getHasMany()
    {
        return $this->hasMany;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the columns of this Model.
     * @return array|Column
     * @throws \Exception
     */
    public function getColumns()
    {
        if ($this->columns)
        {
            return $this->columns;
        }

        switch (DB::connection()->getConfig('driver'))
        {
            case 'sqlite':
                $query = "pragma table_info(" . $this->getTable() . ")";
                $column_name = 'name';
                $reverse = false;
                break;

            case 'pgsql':
                $query = "SELECT column_name FROM information_schema.columns WHERE table_name = '" . $this->getTable() . "'";
                $column_name = 'column_name';
                $reverse = true;
                break;

            case 'mysql':
                $query = 'SHOW FULL COLUMNS FROM ' . $this->getTable();
                $column_name = 'Field';
                $reverse = false;
                break;

            case 'sqlsrv':
                $parts = explode('.', $this->getTable());
                $num = (count($parts) - 1);
                $table = $parts[$num];
                $query = "SELECT column_name FROM " . DB::connection()->getConfig('database') . ".INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'" . $table . "'";
                $column_name = 'column_name';
                $reverse = false;
                break;

            default:
                $error = 'Database driver not supported: ' . DB::connection()->getConfig('driver');
                throw new \Exception($error);
                break;
        }
        $this->columns = array();
        foreach (DB::select($query) as $columnInfo)
        {
            $column = new Column($this, $columnInfo->$column_name, $columnInfo);
            $this->columns[$column->getName()] = $column;
        }
        return $this->columns;
    }

    public function getAttributeTitle($attribute)
    {
        $value = $this->$attribute;
        $relation = $this->getRelation($attribute);
        if ($relation && $this->$relation)
        {
            $title = $this->$relation->getTitle();
        } else
        {
            $title = $value;
        }
        return $title;
    }

    /**
     * Get the related model, given an attribute name.
     * @param string $attr Can have trailing '_id'.
     */
    public function getBelongsTo($attr)
    {
        if (substr($attr, -3) == '_id')
        {
            $relationName = substr($attr, 0, -3);
            return $this->$relationName;
        }
        return false;
    }

    /**
     * Get the relation name for a given attribute.
     *
     * If an attribute name ends in '_id' then it is a foreign key, and has a
     * corresponding relation named the same but without the suffix.
     *
     * @param string $attr
     * @return string|false The relation name, or false if the attribute is not a relation.
     */
    public function getRelation($attr)
    {
        if (substr($attr, -3) == '_id')
        {
            return substr($attr, 0, -3);
        }
        return false;
    }

    public function getTitle()
    {
        if (isset($this->title))
        {
            return $this->title;
        }
        return $this->id;
    }

    public function getUrl($action = '')
    {
        $url = snake_case(str_plural(class_basename($this)), '-');
        $url .= '/' . $this->id;
        $url .= ($action) ? "/$action" : "";
        return url($url);
    }

    /**
     * Shortcut to get the ID of a given model, based on its title.
     * @param string $title
     * @return integer
     */
    public static function getId($title)
    {
        return self::firstOrCreate(array('title' => $title))->id;
    }

    /**
     * Set a belongs-to relation, creating the foreign entity if it doesn't already exist.
     * @param string $rel
     * @param string $title
     */
    public function setBelongsTo($rel, $title)
    {
        $exists = $this->$rel->first();
        if (!$exists->id)
        {
            
        }
    }

}
