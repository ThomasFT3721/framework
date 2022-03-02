<?php

namespace Zaacom\attributes\enum;

enum AllowPermissionEnum: string
{
	case READ = "READ";
	case CREATE = "CREATE";
	case EDIT = "EDIT";
	case DELETE = "DELETE";
	case ALL = "ALL";
	case NONE = "NONE";


}
