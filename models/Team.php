<?php

namespace Models\Framework;

class Team implements \App\Models\Model
{
    const DATABASE = "framework";
    const TABLE = "team";
    const PRIMARY_KEYS = [self::TEA_ID];
    //PRIMARY_KEY
    const TEA_ID = "tea_id"; //int(11)
    const TEA_NAME = "tea_name"; //text
    const TEA_COLOR = "tea_color"; //varchar(9)

    private int $tea_id;
    private ?array $projectList;
    private ?array $userList;
    private string $tea_name;
    private string $tea_color;

    private function __construct()
    {
        $this->projectList = null;
        $this->userList = null;
    }

    public static function __create(array $params): self
    {
        $obj = new static();
        $obj->tea_id = $params[self::TEA_ID];
        $obj->tea_name = $params[self::TEA_NAME];
        $obj->tea_color = $params[self::TEA_COLOR];
        return $obj;
    }

    public function getTeaId(): int
    {
        return $this->tea_id;
    }

    public function setTeaId(int $tea_id): self
    {
        $this->tea_id = $tea_id;

        return $this;
    }

    public function getProjectList(): ?array
    {
        if ($this->projectList === null) {
            $this->projectList = Project::all();
        }
        return $this->projectList;
    }

    public function getUserList(): ?array
    {
        if ($this->userList === null) {
            $this->userList = User::all();
        }
        return $this->userList;
    }

    public function getTeaName(): string
    {
        return $this->tea_name;
    }

    public function setTeaName(string $tea_name): self
    {
        $this->tea_name = $tea_name;

        return $this;
    }

    public function getTeaColor(): string
    {
        return $this->tea_color;
    }

    public function setTeaColor(string $tea_color): self
    {
        $this->tea_color = $tea_color;

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
