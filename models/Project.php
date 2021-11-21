<?php

namespace Models\Framework;

class Project implements \App\Models\Model
{
    const DATABASE = "framework";
    const TABLE = "project";
    const PRIMARY_KEYS = [self::PRO_ID];
    //PRIMARY_KEY
    const PRO_ID = "pro_id"; //int(11)
    const PRO_TEA_ID = "pro_tea_id"; //int(11) | Un petit commentaire
    const PRO_NAME = "pro_name"; //varchar(100)

    private int $pro_id;
    private ?array $projectUserList;
    private ?int $pro_tea_id;
    private ?Team $team;
    private string $pro_name;
    private ?array $siteList;

    private function __construct()
    {
        $this->projectUserList = null;
        $this->team = null;
        $this->siteList = null;
    }

    public static function __create(array $params): self
    {
        $obj = new static();
        $obj->pro_id = $params[self::PRO_ID];
        $obj->pro_tea_id = array_key_exists(self::PRO_TEA_ID, $params) ? $params[self::PRO_TEA_ID] : null;
        $obj->pro_name = $params[self::PRO_NAME];
        return $obj;
    }

    public function getProId(): int
    {
        return $this->pro_id;
    }

    public function setProId(int $pro_id): self
    {
        $this->pro_id = $pro_id;

        return $this;
    }

    public function getProjectUserList(): ?array
    {
        if ($this->projectUserList === null) {
            $this->projectUserList = ProjectUser::all();
        }
        return $this->projectUserList;
    }

    public function getProTeaId(): ?int
    {
        return $this->pro_tea_id;
    }

    public function getTeam(): ?Team
    {
        if ($this->team === null && $this->getProTeaId() !== null) {
            $this->team = Team::findByIdOrFail($this->getProTeaId());
        }
        return $this->team;
    }

    public function setProTeaId(?int $pro_tea_id): self
    {
        $this->pro_tea_id = $pro_tea_id;

        return $this;
    }

    public function getProName(): string
    {
        return $this->pro_name;
    }

    public function setProName(string $pro_name): self
    {
        $this->pro_name = $pro_name;

        return $this;
    }

    public function getSiteList(): ?array
    {
        if ($this->siteList === null) {
            $this->siteList = \Models\FrameworkOtherBase\Site::all();
        }
        return $this->siteList;
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
