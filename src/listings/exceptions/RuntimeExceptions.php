<?php

namespace Listings\Exceptions\Runtime;

class RuntimeException extends \RuntimeException {}

    class WrongMonthNumberException extends RuntimeException {}

    class WrongDayNumberException extends RuntimeException {}

    class WorkedHoursRangeException extends RuntimeException {}

    class LunchHoursRangeException extends RuntimeException {}

    class NegativeResultOfTimeCalcException extends RuntimeException {}

    class NegativeWorkedTimeException extends RuntimeException {}

    class ListingNotFoundException extends RuntimeException {}

    class EmployerNotFoundException extends RuntimeException {}