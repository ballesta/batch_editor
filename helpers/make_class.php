<?php

    /**
     * Created by PhpStorm.
     * User: bernard
     * Date: 01/10/2016
     * Time: 16:48
     */
    class Classe
    {
        function __construct ($class_name, $parent, $properties)
        {

            $p = '';
            foreach ($properties as $property)
            {
                $p .= "    var \$$property;\n";
            }

            $param='';
            foreach ($properties as $property)
            {
                $param .= "\$$property";
                if ($property !== end($properties))
                    $param .= ", ";
            }

            $xfer='';
            foreach ($properties as $property)
            {
                $xfer .= "        \$this->$property = \$$property;\n";
            }

            $extends = '';
            if (!is_null($parent))
            {
                $extends = "extends $parent";
            }

            $c = "<?php\n"
                ."class $class_name $extends\n"
                . "{\n"
                .      $p
                .      "\n"
                . "    function __construct ($param)\n"
                . "    {\n"
                .          $xfer
                . "    }\n"
                . "}\n";

            file_put_contents($class_name . '.php',$c);
        }
    }

    // ++++ Ajouter type propriété, description
    new Classe('Message',null,
                             ['Type_emetteur'
                             ,'Type_message'
                             ,'Identifiant_unique'
                             ]);

    new Classe('Start','Message',
                            ['Version_soft'
                            ,'Version_hardware'
                            ,'Period'
                            ,'Battery'
                            ]);

    new Classe('EventShoot','Message',
                            ['ID'
                            ,'Speed'
                            ]);

    new Classe('EventPass','Message',
                            ['ID'
                            ,'Speed'
                            ]);

    new Classe('EventControl','Message',
                            ['ID'
                            ,'Speed'
                            ]);

    new Classe('Mesure','Message',
                            ['Dist'
                            ,'Average'
                            ,'Max'
                            ,'Step'
                            ,'Sprint'
                            ,'Mobility'
                            ,'Shoot'
                            ,'Pass'
                            ,'Control'
                            ]);
