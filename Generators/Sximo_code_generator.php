<?php

/**
 * Created by PhpStorm.
 * User: bernard
 * Date: 29/09/2016
 * Time: 16:28
 */

include '../Generators/Code_generator.php';

class Sixmo_code_generator extends Code_generator
{
     function modele_begin(Modele $modele)
     {
         echo 'Modèle begin', $modele->nom, '<br>';
     }

     function modele_end(Modele $modele)
     {
         echo 'Modèle end', $modele->nom, '<br>';

     }

     function module_begin(Modele $modele, Module $module)
     {
         echo '--Module begin', $module->nom, '<br>';

     }

     function module_end(Modele $modele, Module $module)
     {
         echo '--Module end', $module->nom, '<br>';
     }

     function has_many_begin(Modele $modele, Module $module, Has_many $has_many)
     {
         echo '----has_many_begin', $module->nom, '<br>';
     }

     function has_many_end(Modele $modele, Module $module, Has_many $has_many)
     {
         echo '----has_many_end', $module->nom, '<br>';
     }
}