<?php

/**
 * Classe de base des générateurs de code
 *
 * Created by PhpStorm.
 * User: bernard
 * Date: 29/09/2016
 * Time: 16:08
 */



abstract class Code_generator
{
    abstract function modele_begin(Modele $modele);
    abstract function modele_end  (Modele $modele);

    abstract function module_begin(Modele $modele, Module $module);
    abstract function module_end  (Modele $modele, Module $module);

    abstract function has_many_begin(Modele $modele, Module $module, Has_many $Has_many);
    abstract function has_many_end  (Modele $modele, Module $module, Has_many $Has_many);
}