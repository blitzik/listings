<?php

namespace Listings\Exceptions\Logic;

class LogicException extends \LogicException {}

    class InvalidArgumentException extends LogicException {}

    class InvalidStateException extends LogicException {}

    class NoListingSetException extends LogicException  {}