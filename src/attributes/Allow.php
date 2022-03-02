<?php

namespace Zaacom\attributes;

use Zaacom\attributes\enum\AllowPermissionEnum;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Allow
{
	public function __construct(
		private array|string              $roles,
		private array|AllowPermissionEnum $permissions
	)
	{
		if (is_string($roles)) {
			$this->roles = [$roles];
		}
		if (!is_array($permissions)) {
			$this->permission = [$permissions];
		}
	}

	/**
	 * @return string[]
	 */
	public function getRoles(): array
	{
		return $this->roles;
	}

	/**
	 * @return AllowPermissionEnum[]
	 */
	public function getPermissions(): array
	{
		return $this->permission;
	}


}
