<?php

namespace Zaacom\routing;

class AllowedPermission
{
	public string $role;
	/**
	 * @var \Zaacom\attributes\enum\AllowPermissionEnum[]
	 */
	public array $permissions;
	
	public function __construct(string $role, array $permissions) {
		$this->role = $role;
		$this->permissions = $permissions;
	}

	/**
	 * @return string
	 */
	public function getRole(): string
	{
		return $this->role;
	}

	/**
	 * @return \Zaacom\attributes\enum\AllowPermissionEnum[]
	 */
	public function getPermissions(): array
	{
		return $this->permissions;
	}
}
