<?php

namespace Models\Framework;

class Society implements \App\Models\Model
{
    const DATABASE = "framework";
    const TABLE = "society";
    const PRIMARY_KEYS = [self::SOC_ID];
    //PRIMARY_KEY
    const SOC_ID = "soc_id"; //int(11)
    const SOC_NAME = "soc_name"; //varchar(100)

    private int $soc_id;
    private ?array $userList;
    private string $soc_name;

    private function __construct()
    {
        $this->userList = null;
    }

    public static function __create(array $params): self
    {
        $obj = new static();
        $obj->soc_id = $params[self::SOC_ID];
        $obj->soc_name = $params[self::SOC_NAME];
        return $obj;
    }

    public function getSocId(): int
    {
        return $this->soc_id;
    }

    public function setSocId(int $soc_id): self
    {
        $this->soc_id = $soc_id;

        return $this;
    }

    public function getUserList(): ?array
    {
        if ($this->userList === null) {
            $this->userList = User::all();
        }
        return $this->userList;
    }

    public function getSocName(): string
    {
        return $this->soc_name;
    }

    public function setSocName(string $soc_name): self
    {
        $this->soc_name = $soc_name;

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
