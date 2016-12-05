<?php

namespace blitzik\Latte\Filters;

use Nette\SmartObject;

final class MonthByNumberFilter
{
    use SmartObject;


    public function __invoke($monthNumber)
    {
        switch ($monthNumber) {
            case 1: return 'Leden'; break;
            case 2: return 'Únor'; break;
            case 3: return 'Březen'; break;
            case 4: return 'Duben'; break;
            case 5: return 'Květen'; break;
            case 6: return 'Červen'; break;
            case 7: return 'Červenec'; break;
            case 8: return 'Srpen'; break;
            case 9: return 'Září'; break;
            case 10: return 'Říjen'; break;
            case 11: return 'Listopad'; break;
            case 12: return 'Prosinec'; break;
            default:
                throw new \InvalidArgumentException('Wrong input');
        }
    }
}