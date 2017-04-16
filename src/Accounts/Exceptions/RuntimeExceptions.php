<?php

namespace Accounts\Exceptions\Runtime;

class RuntimeException extends \RuntimeException {}

    class EmailIsInUseException extends RuntimeException {}

    class UserNotFoundException extends RuntimeException {}

    class EmailSendingFailedException extends RuntimeException {}