<?php

namespace Users\Exceptions\Logic;

class LogicException extends \LogicException {}

    class DomainException extends LogicException {}

    class InvalidArgumentException extends LogicException {}