<?php declare(strict_types=1);

namespace HelpPC\NetteUtils\Utils;

class Paginator extends \Nette\Utils\Paginator
{

    /**
     * @return array<int, float|int>
     */
    public function getSteps(): array
    {
        if ($this->pageCount < 2) {
            $steps = [$this->getPage()];
        } else {
            $arr = range(max($this->getFirstPage(), $this->getPage() - 2), min($this->getLastPage(), $this->getPage() + 2));
            $count = 1;
            $perPage = (int) $this->getPageCount();
            $quotient = ($perPage - 1) / $count;
            for ($i = 0; $i <= $count; $i++) {
                $arr[] = round($quotient * $i) + $this->getFirstPage();
            }
            sort($arr);
            $steps = array_values(array_unique($arr));
        }

        return $steps;
    }

}
