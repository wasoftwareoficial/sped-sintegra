<?php

namespace NFePHP\Sintegra;

use NFePHP\Sintegra\Common\BlockInterface;

abstract class Sintegra
{
    protected $possibles = [];

    public function __construct()
    {
        //todo
    }

    /**
     * Add
     * @param BlockInterface $block
     */
    public function add(BlockInterface $block = null)
    {
        if (empty($block)) {
            return;
        }
        $name = strtolower((new \ReflectionClass($block))->getShortName());
        if (key_exists($name, $this->possibles)) {
            $this->{$name} = $block->get();
        }
    }

    /**
     * Create a SINTEGRA string
     */
    public function get()
    {
        $sintegra = '';
        $keys = array_keys($this->possibles);
        foreach ($keys as $key) {
            if (isset($this->$key)) {
                $sintegra .= $this->$key;
            }
        }
        $sintegra .= $this->totalize($sintegra);
        return $sintegra;
    }

    /**
     * Totals blocks contents
     * @param string $sintegra
     * @return string
     */
    protected function totalize($sintegra)
    {
        $tot = '';
        $keys = [];
        $aefd = explode("\n", $sintegra);
        foreach ($aefd as $element) {
            $param = explode("|", $element);
            if (!empty($param[1])) {
                $key = $param[1];
                if (!empty($keys[$key])) {
                    $keys[$key] += 1;
                } else {
                    $keys[$key] = 1;
                }
            }
        }
        //Inicializa o bloco 9
        $tot .= "|9001|0|\n";
        $n = 0;
        foreach ($keys as $key => $value) {
            if (!empty($key)) {
                $tot .= "|9900|$key|$value|\n";
                $n++;
            }
        }
        $n++;
        $tot .= "|9900|9001|1|\n";
        $tot .= "|9900|9900|". ($n+3)."|\n";
        $tot .= "|9900|9990|1|\n";
        $tot .= "|9900|9999|1|\n";
        $tot .= "|9990|". ($n+6) ."|\n";
        $efd .= $tot;
        $n = count(explode("\n", $efd));
        $tot .= "|9999|$n|\n";
        return $tot;
    }
}