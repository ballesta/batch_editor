<?php
/**
 * Created by PhpStorm.
 * User: bernard
 * Date: 28/09/2016
 * Time: 19:09
 */

include 'batch_editor.php';

$base = 'H:\wamp-3-32\www\ms_football_salles\2-site\resources\views\reseauxsalles\index.blade.php';
$e = new Batch_script_editor($base);
$e->search('@foreach ($tableGrid as $field)');
$e->search('@endforeach');
$e->search('<td>');
$e->insert_after(
    ["{!!   Navigation::link('Complexes',",
                            "'Complexes sportifs (établissements) du réseau',",
                            "'complexesportif',",
                            "'club_id',",
                            '$row->club_id',
                            ")",
     "!!}"]);

$e->display("navigation");

$e->save();
