<?php

namespace Models\Framework;

class User implements \App\Models\Model
{
    const DATABASE = "framework";
    const TABLE = "user";
    const PRIMARY_KEYS = [self::USE_ID];
    //PRIMARY_KEY
    const USE_ID = "use_id"; //int(11)
    const SOC_ID = "soc_id"; //int(11)
    const USE_TEAM_ID = "use_team_id"; //int(11)
    const FIRSTNAME = "firstname"; //varchar(50)
    const LASTNAME = "lastname"; //varchar(50)
    const C_TINYINT = "c_tinyint"; //tinyint(1)
    const C_SMALLINT = "c_smallint"; //smallint(2)
    const C_MEDIUMINT = "c_mediumint"; //mediumint(3)
    const C_INT = "c_int"; //int(11)
    const C_BIGINT = "c_bigint"; //bigint(12)
    const C_DECIMAL = "c_decimal"; //decimal(12,0)
    const C_FLOAT = "c_float"; //float
    const C_DOUBLE = "c_double"; //double
    const C_REAL = "c_real"; //double
    const C_BIT = "c_bit"; //bit(1)
    const C_BOOLEAN = "c_boolean"; //tinyint(1)
    const C_ENUM = "c_enum"; //enum('a','b','c','d')
    const C_DATETIME = "c_datetime"; //datetime

    private int $use_id;
    private ?array $projectUserList;
    private int $soc_id;
    private ?Society $society;
    private int $use_team_id;
    private ?Team $team;
    private string $firstname;
    private string $lastname;
    private int $c_tinyint;
    private int $c_smallint;
    private int $c_mediumint;
    private int $c_int;
    private int $c_bigint;
    private float $c_decimal;
    private float $c_float;
    private float $c_double;
    private float $c_real;
    private int $c_bit;
    private int $c_boolean;
    private ?string $c_enum;
    private \App\Tools\DateTime $c_datetime;
    private ?array $siteList;

    private function __construct()
    {
        $this->projectUserList = null;
        $this->society = null;
        $this->team = null;
        $this->siteList = null;
    }

    public static function __create(array $params): self
    {
        $obj = new static();
        $obj->use_id = $params[self::USE_ID];
        $obj->soc_id = $params[self::SOC_ID];
        $obj->use_team_id = $params[self::USE_TEAM_ID];
        $obj->firstname = $params[self::FIRSTNAME];
        $obj->lastname = $params[self::LASTNAME];
        $obj->c_tinyint = $params[self::C_TINYINT];
        $obj->c_smallint = $params[self::C_SMALLINT];
        $obj->c_mediumint = $params[self::C_MEDIUMINT];
        $obj->c_int = $params[self::C_INT];
        $obj->c_bigint = $params[self::C_BIGINT];
        $obj->c_decimal = $params[self::C_DECIMAL];
        $obj->c_float = $params[self::C_FLOAT];
        $obj->c_double = $params[self::C_DOUBLE];
        $obj->c_real = $params[self::C_REAL];
        $obj->c_bit = $params[self::C_BIT];
        $obj->c_boolean = $params[self::C_BOOLEAN];
        $obj->c_enum = array_key_exists(self::C_ENUM, $params) ? $params[self::C_ENUM] : null;
        $obj->c_datetime = new \App\Tools\DateTime($params[self::C_DATETIME]);
        return $obj;
    }

    public function getUseId(): int
    {
        return $this->use_id;
    }

    public function setUseId(int $use_id): self
    {
        $this->use_id = $use_id;

        return $this;
    }

    public function getProjectUserList(): ?array
    {
        if ($this->projectUserList === null) {
            $this->projectUserList = ProjectUser::all();
        }
        return $this->projectUserList;
    }

    public function getSocId(): int
    {
        return $this->soc_id;
    }

    public function getSociety(): Society
    {
        if ($this->society === null) {
            $this->society = Society::findByIdOrFail($this->getSocId());
        }
        return $this->society;
    }

    public function setSocId(int $soc_id): self
    {
        $this->soc_id = $soc_id;

        return $this;
    }

    public function getUseTeamId(): int
    {
        return $this->use_team_id;
    }

    public function getTeam(): Team
    {
        if ($this->team === null) {
            $this->team = Team::findByIdOrFail($this->getUseTeamId());
        }
        return $this->team;
    }

    public function setUseTeamId(int $use_team_id): self
    {
        $this->use_team_id = $use_team_id;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCTinyint(): int
    {
        return $this->c_tinyint;
    }

    public function setCTinyint(int $c_tinyint): self
    {
        $this->c_tinyint = $c_tinyint;

        return $this;
    }

    public function getCSmallint(): int
    {
        return $this->c_smallint;
    }

    public function setCSmallint(int $c_smallint): self
    {
        $this->c_smallint = $c_smallint;

        return $this;
    }

    public function getCMediumint(): int
    {
        return $this->c_mediumint;
    }

    public function setCMediumint(int $c_mediumint): self
    {
        $this->c_mediumint = $c_mediumint;

        return $this;
    }

    public function getCInt(): int
    {
        return $this->c_int;
    }

    public function setCInt(int $c_int): self
    {
        $this->c_int = $c_int;

        return $this;
    }

    public function getCBigint(): int
    {
        return $this->c_bigint;
    }

    public function setCBigint(int $c_bigint): self
    {
        $this->c_bigint = $c_bigint;

        return $this;
    }

    public function getCDecimal(): float
    {
        return $this->c_decimal;
    }

    public function setCDecimal(float $c_decimal): self
    {
        $this->c_decimal = $c_decimal;

        return $this;
    }

    public function getCFloat(): float
    {
        return $this->c_float;
    }

    public function setCFloat(float $c_float): self
    {
        $this->c_float = $c_float;

        return $this;
    }

    public function getCDouble(): float
    {
        return $this->c_double;
    }

    public function setCDouble(float $c_double): self
    {
        $this->c_double = $c_double;

        return $this;
    }

    public function getCReal(): float
    {
        return $this->c_real;
    }

    public function setCReal(float $c_real): self
    {
        $this->c_real = $c_real;

        return $this;
    }

    public function getCBit(): int
    {
        return $this->c_bit;
    }

    public function setCBit(int $c_bit): self
    {
        $this->c_bit = $c_bit;

        return $this;
    }

    public function getCBoolean(): int
    {
        return $this->c_boolean;
    }

    public function setCBoolean(int $c_boolean): self
    {
        $this->c_boolean = $c_boolean;

        return $this;
    }

    public function getCEnum(): ?string
    {
        return $this->c_enum;
    }

    public function setCEnum(?string $c_enum): self
    {
        $this->c_enum = $c_enum;

        return $this;
    }

    public function getCDatetime(): \App\Tools\DateTime
    {
        return $this->c_datetime;
    }

    public function setCDatetime(\App\Tools\DateTime $c_datetime): self
    {
        $this->c_datetime = $c_datetime;

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
