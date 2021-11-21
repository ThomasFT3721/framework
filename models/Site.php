<?php

namespace Models\FrameworkOtherBase;

class Site implements \App\Models\Model
{
    const DATABASE = "framework_other_base";
    const TABLE = "site";
    const PRIMARY_KEYS = [self::SIT_ID];
    //PRIMARY_KEY
    const SIT_ID = "sit_id"; //int(11)
    const SIT_USE_ID = "sit_use_id"; //int(11)
    const SIT_PRO_ID = "sit_pro_id"; //int(11)
    const SIT_URL = "sit_url"; //int(11)

    private int $sit_id;
    private int $sit_use_id;
    private ?\Models\Framework\User $user;
    private int $sit_pro_id;
    private ?\Models\Framework\Project $project;
    private int $sit_url;

    private function __construct()
    {
        $this->user = null;
        $this->project = null;
    }

    public static function __create(array $params): self
    {
        $obj = new static();
        $obj->sit_id = $params[self::SIT_ID];
        $obj->sit_use_id = $params[self::SIT_USE_ID];
        $obj->sit_pro_id = $params[self::SIT_PRO_ID];
        $obj->sit_url = $params[self::SIT_URL];
        return $obj;
    }

    public function getSitId(): int
    {
        return $this->sit_id;
    }

    public function setSitId(int $sit_id): self
    {
        $this->sit_id = $sit_id;

        return $this;
    }

    public function getSitUseId(): int
    {
        return $this->sit_use_id;
    }

    public function getUser(): \Models\Framework\User
    {
        if ($this->user === null) {
            $this->user = \Models\Framework\User::findByIdOrFail($this->getSitUseId());
        }
        return $this->user;
    }

    public function setSitUseId(int $sit_use_id): self
    {
        $this->sit_use_id = $sit_use_id;

        return $this;
    }

    public function getSitProId(): int
    {
        return $this->sit_pro_id;
    }

    public function getProject(): \Models\Framework\Project
    {
        if ($this->project === null) {
            $this->project = \Models\Framework\Project::findByIdOrFail($this->getSitProId());
        }
        return $this->project;
    }

    public function setSitProId(int $sit_pro_id): self
    {
        $this->sit_pro_id = $sit_pro_id;

        return $this;
    }

    public function getSitUrl(): int
    {
        return $this->sit_url;
    }

    public function setSitUrl(int $sit_url): self
    {
        $this->sit_url = $sit_url;

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
