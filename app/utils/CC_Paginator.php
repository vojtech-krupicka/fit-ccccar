<?php
/**
 * CC_Paginator.php
 *
 * Pomocna trida pro vypocty ohledne strankovani a renderovani strankovace.
 *
 * @copyright 2014
 * @version 1.0.0
 */


class CC_Paginator
{
    
    /** @var int */
    protected $base = 1;
    
    
    /** @var int */
    protected $itemsPerPage = 1;
    
    
    /** @var int */
    protected $page = 1;
    
    
    /** @var int|null */
    protected $totalCount = null;
    
    
    public function render()
    {
        global $CC_SYSTEM;
        
        $params = $CC_SYSTEM->request->params;
        $count = 3;
        $steps = array();
		if ($this->getPageCount() > 1) {
			$offset = $left = $right = floor($count / 2);
			if($this->getPage() <= $offset && $this->getPage() < $this->getFirstPage()-1 + $count) {
				$right = $count - $this->getPage();
			}
			if($this->getPage() >= $this->getLastPage()-$offset && $this->getPage() > $this->getLastPage()-1 - $count) {
				$left = $this->getPage() - ($this->getLastPage() - $count) - 1;
			}
			
            if($this->getPage() - $left > $this->getFirstPage()) {
                $steps[] = $this->getFirstPage();
            }
            
			$steps = array_merge($steps, range(max($this->getFirstPage(), $this->getPage() - $left), min($this->getLastPage(), $this->getPage() + $right)));
            
            if($this->getPage() + $right < $this->getLastPage()) {
                $steps[] = $this->getLastPage();
            }
		}
        
        
        echo "<div class=\"row\">\n";
        echo "    <div class=\"col-md-6 pull-right text-right\">\n";
        
        if ($this->getPageCount() > 1) {
            echo "    <ul class=\"pagination pagination-sm\">\n";

            if($this->isFirst()) {
                echo "            <li class=\"disabled\"><span>&laquo;</span></li>\n";
            } else {
                $params['page'] = $this->getPage() - 1;
                echo "            <li><a href=\"". CC_Utils::link('this', $params) ."\">&laquo;</a></li>\n";
            }
            
            foreach ($steps as $index => $step) {
                $params['page'] = $step;
                echo "            <li". ($step == $this->getPage() ? ' class="active"' : '') ."><a href=\"". CC_Utils::link('this', $params) ."\">".(int)$step."</a></li>\n";
                if (isset($steps[$index+1]) && $steps[$index+1] > $step + 1) {
                    echo "            <li class=\"disabled\"><span>...</span></li>\n";
                }
            }

            if ($this->isLast()) {
                echo "            <li class=\"disabled\"><span>&raquo;</span></li>\n";
            } else {
                $params['page'] = $this->getPage() + 1;
                echo "            <li><a href=\"". CC_Utils::link('this', $params) ."\">&raquo;</a></li>\n";
            }
            echo "    </ul>\n";
        }
        
        echo "        <div><small><em>Zobrazeno záznamů: ". $this->getLength() ." (z celkem: ". $this->getTotalCount() ." záznamů)</em></small></div>\n";
        echo "    </div>\n";
        echo "</div>\n";
        
        return;
    }
    
    
    /**
     * Nastavi cislo aktualni stranky.
     * 
     * @param int $page
     * @return \Paginator
     */
    public function setPage($page)
    {
        $this->page = (int)$page;
        return $this;
    }
    
    
    /**
     * Vraci cislo aktualni stranky.
     * 
     * @return int
     */
    public function getPage()
    {
        return $this->base + $this->getPageIndex();
    }
    
    
    /**
     * Vraci cislo prvni stranky.
     * 
     * @return int
     */
    public function getFirstPage()
    {
        return $this->base;
    }
    
    
    /**
     * Vraci cislo posledni stranky.
     * 
     * @return int|null
     */
    public function getLastPage()
    {
        return $this->totalCount === NULL ? NULL : $this->base + max(0, $this->getPageCount() - 1);
    }
    
    
    /**
     * Nastavi zaklad (cislo prvni stranky).
     * 
     * @param int $base
     * @return \Paginator
     */
    public function setBase($base)
    {
        $this->base = (int)$base;
        return $this;
    }
    
    
    /**
     * Vraci zaklad (cislo prvni stranky).
     * 
     * @return int
     */
    public function getBase()
    {
        return $this->base;
    }
    
    
    /**
     * Spocita index stranky od 0.
     * 
     * @return int
     */
    protected function getPageIndex()
    {
        $index = max(0, $this->page - $this->base);
        return $this->totalCount === NULL ? $index : min($index, max(0, $this->getPageCount() - 1));
    }
    
    
    /**
     * Vraci true, pokud je aktualni stranka prvni strankou.
     * 
     * @return boolean
     */
    public function isFirst()
    {
        return $this->getPageIndex() === 0;
    }
    
    
    /**
     * Vraci true, pokud je aktualni stranka strankou posledni.
     * 
     * @return boolean
     */
    public function isLast()
    {
        return $this->totalCount === NULL ? false : $this->getPageIndex() >= $this->getPageCount() - 1;
    }
    
    
    /**
     * Vraci celkovy pocet stranek.
     * 
     * @return int|null
     */
    public function getPageCount()
    {
        return $this->totalCount === NULL ? NULL : (int) ceil($this->totalCount / $this->itemsPerPage);
    }
    
    
    /**
     * Nastavi pocet polozek na stranku.
     * 
     * @param int $items
     * @return \Paginator
     */
    public function setItemsPerPage($items)
    {
        $this->itemsPerPage = max(1, (int)$items);
        return $this;
    }
    
    
    /**
     * Vraci pocet polozek na stranku.
     * 
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }
    
    
    /**
     * Nastavi celkovy pocet polozek.
     * 
     * @param int|null $total
     * @return \Paginator
     */
    public function setTotalCount($total)
    {
        $this->totalCount = ($total == null) ? null : max(0, (int)$total);
        return $this;
    }
    
    
    /**
     * Vraci celkovy pocet polozek.
     * 
     * @return int|null
     */
    public function getTotalCount()
    {
        return $this->totalCount;
    }
    
    
    /**
     * Vraci absolutni index prvni polozky na aktualni strance.
     * 
     * @return int
     */
    public function getOffset()
    {
        return $this->getPageIndex() * $this->itemsPerPage;
    }    
    
    
    /**
     * Vraci pocet aktualne zobrazenych polozek na strance.
     * 
     * @return int
     */
    public function getLength()
    {
        return $this->totalCount === null 
                ? $this->itemsPerPage 
                : min($this->itemsPerPage, $this->totalCount - ($this->getPageIndex() * $this->itemsPerPage));
    }
}

