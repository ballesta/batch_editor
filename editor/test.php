<?php
/**
 * Created by PhpStorm.
 * User: bernard
 * Date: 28/09/2016
 * Time: 19:09
 */

include 'batch_editor.php';

$e = new Batch_script_editor("test_input.php");
//$e->display("init");
//$b = $e->search('User: bernard');
//$e->display("User: bernard");
$f = $e->search('function ffff($aaaa,$bbbb)');
$begin = $e->search('{');
$end = $e->search('}');
echo "$begin .. $end <br> ";
$e->replace($begin,$end,[
    '    // Nouveau texte substitue aaaa',
    '    if($a == $b)',
    '    {',
    '        $xxxxx= 0;',
    '    }'
]);

$e->move_to_beginning();
$e->insert_before(["+++++++++++++++++"]) ;

$f = $e->search('function ffff($aaaa,$bbbb)');
$begin = $e->search('{');
$end = $e->search('}');
echo "$begin .. $end <br> ";
$e->replace($begin,$end,[
    '    $a[]=$b;',
    '    // Nouveau texte substitue bbbb',
    '    // Nouveau texte substitue cccc',
    '    // Nouveau texte substitue dddd',
    '    // Nouveau texte substitue eeee'
]);

//--
$f = $e->search('function gggg($aaaa,$bbbb)');
$begin = $e->search('{');
$end = $e->search('}');
echo "$begin .. $end <br> ";
$e->replace($begin,$end,[
    '    // Nouveau texte substitue xxxx',
    '    // Nouveau texte substitue xxxx',
    '    // Nouveau texte substitue xxxx',
    '    // Nouveau texte substitue xxxx',
    '    // Nouveau texte substitue xxxx'
]);
$e->move_to_end();
$e->insert_after(["-----------------"]);

$e->move_to_beginning();
$f = $e->search("<a>lien</a>");
$e->insert_before(["<a>lien avant</a>"]);
$e->insert_before(["<a>lien avant 2</a>"]);

$e->move_to_beginning();
$f = $e->search("<a>lien</a>");
$e->insert_after(["<a>lien apres</a>"]);
$e->insert_after(["<a>lien apres 2</a>"]);

$e->display("function");

$e->save();
