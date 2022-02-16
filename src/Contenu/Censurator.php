<?php

namespace App\Contenu;

class Censurator
{

    public function purify(String $string)
    {
        $motARemplacer = [" putin"," merde"," batard"," pute"," salo"," chier"," con",
            " fuck"," bit"," putain"," encul"," pd"," black"," hitler"];
        foreach ($motARemplacer as $mot) {
            $replacement = " ".str_repeat("*", mb_strlen($mot));
            $string = str_replace($mot,$replacement,$string);
        }
        return $string;
    }

}