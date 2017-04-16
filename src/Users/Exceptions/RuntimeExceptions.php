<?php

namespace Users\Exceptions\Runtime;

class RuntimeException extends \RuntimeException {}

    class EmailAlreadyExistsException extends RuntimeException {}

    class RoleAlreadyExistsException extends RuntimeException {}

    class RoleMissingException extends RuntimeException {}

    class WrongUserRoleException extends RuntimeException {}

    class ResourceNotFoundException extends RuntimeException {}