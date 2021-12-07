<?php

namespace Models\Framework;

class ProjectUser implements \App\Models\Model
{
    const DATABASE = "framework";
    const TABLE = "project_user";
    const PRIMARY_KEYS = [self::PRO_ID,self::USE_ID];
    //PRIMARY_KEY
    const PRO_ID = "pro_id"; //int(11)
    //PRIMARY_KEY
    const USE_ID = "use_id"; //int(11)

    private int $pro_id;
    private ?Project $project;
    private int $use_id;
    private ?User $user;

    private function __construct()
    {
        $this->project = null;
        $this->user = null;
    }

    public static function __create(array $params): self
    {
        $obj = new static();
        $obj->pro_id = $params[self::PRO_ID];
        $obj->use_id = $params[self::USE_ID];
        return $obj;
    }

    public function getProId(): int
    {
        return $this->pro_id;
    }

    public function getProject(): Project
    {
        if ($this->project === null) {
            $this->project = Project::findByIdOrFail($this->getProId());
        }
        return $this->project;
    }

    public function setProId(int $pro_id): self
    {
        $this->pro_id = $pro_id;

        return $this;
    }

    public function getUseId(): int
    {
        return $this->use_id;
    }

    public function getUser(): User
    {
        if ($this->user === null) {
            $this->user = User::findByIdOrFail($this->getUseId());
        }
        return $this->user;
    }

    public function setUseId(int $use_id): self
    {
        $this->use_id = $use_id;

        return $this;
    }


    // Interface functions

    public static function findById(mixed ...$ids): self|false
    {
        if (count(self::PRIMARY_KEYS) != count($ids)) {
            throw new \InvalidArgumentException("Too few arguments to function " . __CLASS__ . "::" . __FUNCTION__ . "(), " . count($ids) . " passed and exactly " . count(self::PRIMARY_KEYS) . " expected");
        }
        $where = [];
        foreach (self::PRIMARY_KEYS as $key => $value) {
            $where[] = [$value, $ids[$key]];
        }
        return \App\Models\QuerySelect::create(self::DATABASE)->from(self::TABLE)->where($where)->get(__CLASS__);
    }

    public static function findByIdOrFail(mixed ...$ids): self
    {
        $obj = self::findById(...$ids);
        if ($obj === false) {
            throw new \Exception('A faire l\'erreur');
        }
        return $obj;
    }

    public static function all(): array
    {
        return \App\Models\QuerySelect::create(self::DATABASE)->from(self::TABLE)->getAll(__CLASS__);
    }

    public static function each(callable $callable): array
    {
        return \App\Models\QuerySelect::create(self::DATABASE)->from(self::TABLE)->each($callable, __CLASS__);
    }

    public static function where(mixed ...$parameters): \App\Models\QuerySelect
    {
        return \App\Models\QuerySelect::create(self::DATABASE)->from(self::TABLE)->setClass(__CLASS__)->where(...$parameters);
    }

    public static function orWhere(mixed ...$parameters): \App\Models\QuerySelect
    {
        return \App\Models\QuerySelect::create(self::DATABASE)->from(self::TABLE)->setClass(__CLASS__)->orWhere(...$parameters);
    }
}
