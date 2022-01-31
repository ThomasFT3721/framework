<?php

namespace Zaacom\helper;


/**
 * @author Thomas FONTAINE--TUFFERY
 */
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

	public function isBefore(string|DateTime $datetime): bool
	{
		if (is_string($datetime)) {
			$datetime = new self($datetime);
		}
		return $this->getTimestamp() < $datetime->getTimestamp();
	}

	public function isAfter(string|DateTime $datetime): bool
	{
		if (is_string($datetime)) {
			$datetime = new self($datetime);
		}
		return $this->getTimestamp() > $datetime->getTimestamp();
	}

	public function equal(string|DateTime $datetime): bool
	{
		if (is_string($datetime)) {
			$datetime = new self($datetime);
		}
		return $this->getTimestamp() == $datetime->getTimestamp();
	}

	public function __toString()
	{
		return $this->formatMax();
	}
}
