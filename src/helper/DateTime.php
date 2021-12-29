<?php

namespace Zaacom\helper;

class DateTime extends \DateTime
{
    private string $datetime;

    public function __construct(string $datetime = 'now')
    {
        parent::__construct($datetime);
        $this->datetime = $datetime;
    }

    public function formatMin(string $format = 'Y-m-d'): string
    {
        return parent::format($format);
    }

    public function formatMax(string $format = 'Y-m-d H:i:s'): string
    {
        return parent::format($format);
    }

    public function formatFrenchMin(string $format = 'd/m/Y'): string
    {
        return parent::format($format);
    }
	public function formatFrenchMax(string $format = 'H:i:s d/m/Y'): string
	{
		return parent::format($format);
	}

	public function isValidDateTime(): bool
	{
		return $this->datetime !== "0000-01-01 00:00:00";
	}

    public function __toString()
    {
        return $this->datetime;
    }
}