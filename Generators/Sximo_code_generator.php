<?php

    /**
     * Created by PhpStorm.
     * User: bernard
     * Date: 29/09/2016
     * Time: 16:28
     */

    // Base class of all code generators
    include '../../Generators/Code_generator.php';

    class Sixmo_code_generator extends Code_generator
    {

        // Begin, end function called for each entity of model.

        function modele_begin(Modele $modele)
        {
            echo 'Modèle begin ', $modele->nom, '<br>';
        }

        function module_begin(Modele $modele, Module $module)
        {
            echo '--Module begin ', $module->nom, '<br>';

        }

        function has_many_begin(Modele $modele, Module $module, Has_many $has_many)
        {
            echo '----has_many_begin ---- ',
                 $module->nom, ' hasmany ', $has_many->module_detail->nom . '<br>';
            $this->insert_link_to_detail_grid($modele, $module, $has_many);
            $this->insert_remember_filter_key_into_controller($modele, $module, $has_many);
            $this->insert_init_parent_key_into_controller($modele, $module, $has_many);
        }

        // Insert button in list to drill down to detailed list
        function insert_link_to_detail_grid(Modele $modele, Module $module, Has_many $has_many)
        {
            // Get file to update
            $file_path = 'resources\\views' . '\\' . $module->nom . '\\index.blade.php';
            $full_file_path = $this->laravel_project_path . '\\' . $file_path;
            // Save path to wite generated file
            //$this->full_file_path = $full_file_path;
            $this->editor->edit($full_file_path);

            // Do the changes

            // 1. Enlarge action column to cope with added buttons
            //    ------------------------------------------------
            // Find line containing pattern
	        $this->editor->find("{{ Lang::get('core.btn_action') }}");
			// Replace in current line
            $this->editor->replace_regexp('#<th width=.+><span>(.+)</span></th>#'
	                                     ,'<th width="40%"><span>${1}</span></th>');

            // 2. Insert link to detail view of current line
            //    ------------------------------------------
            $module_name = $has_many->module_detail->nom;
            $module_title = $has_many->module_detail->title;
            // Prepare button link to details
            $link_to_detail =
            ['{!!',
                '\Navigation::link_to_detail(  '                   ,
                '$text        = ' . "'$module_title',"               ,
                '$help        = ' . "'$has_many->explications',"     ,
                '$url         = ' . "URL::to('$module_name'),"       ,
                '$parent_key  = ' . "'$module->id_key',"             ,

                '$parent_label= ' . "'$module->identifier',"         ,

                '$parent_id = ' . '$row->' . $module->id_key .")"  ,
             '!!}'
            ];

            $this->editor->find('@foreach ($tableGrid as $field)');
            $this->editor->find('@endforeach');
            $this->editor->find('<td>');
            $this->editor->insert($link_to_detail);
            $this->editor->save();
        }

        function insert_remember_filter_key_into_controller
        (
        	Modele      $modele,
	        Module      $module,
	        Has_many    $has_many
        )
        {
        	// Module modified is the detail module of the 'has many' relation.
	        // Not the module itself.
            // Get file to update
            $detail_module_name = $has_many->module_detail->nom;
	        $detail_module_name_title = $has_many->module_detail->title;
            $file_path  = 'app\\Http\\Controllers'
	                    . '\\' . $detail_module_name . 'Controller.php';
            $full_file_path = $this->laravel_project_path . '\\' . $file_path;
            // Save path to wite generated file
            //$this->full_file_path = $full_file_path;
            $this->editor->edit($full_file_path);

            // Do the changes

            // 1. Get Key from URL then add it to Session
            //        $club_id = $request->query("club_id");
            //        \Session::put("club_id", $club_id);
            $save_key =
            [
                '// Get parameter in URL to use it as filter',
                '$id'. ' = ' . '$request->query("' . $module->id_key . '");',
                'if (!is_null($id))',
                //'    \Session::put("' . $module->id_key . '_active_filter' . "',"
                //                  . $module->id_key ."');",
                '    \Session::put("' . $module->id_key .'", $id);',
	            '$id = \Session::get("' . $module->id_key . '", null);',
	            '// Check if parent already selected',
	            'if (is_null($id))',
	            '{',
		        'return Redirect::to("' . $module->nom . '")',
			    '    ->with("messagetext",',
				'        "Vous devez d\'abord sélectionner votre <br> "',
				'        ."<i>' . $module->title .'</i> <br>"',
				'        ."avant de choisir <br>"',
				'        ."<i>'. $detail_module_name_title .'</i>")',
			    '    ->with("msgstatus","warning");',
	            '}'
            ];

            $this->editor->find('function getIndex( Request $request )');
            $this->editor->find('{');
            $this->editor->insert($save_key);
            $this->editor->save();
        }

        function insert_init_parent_key_into_controller($modele, $module, $has_many)
        {
            // Get file to update
            $detail_module_name = $has_many->module_detail->nom;
            $file_path  = 'app\\Http\\Controllers'
	                    . '\\'
	                    . $detail_module_name
	                    . 'Controller.php';
            $full_file_path = $this->laravel_project_path . '\\' . $file_path;
            // Save path to wite generated file
            //$this->full_file_path = $full_file_path;
            $this->editor->edit($full_file_path);

            // Do the changes

            // teste :// Add club_id
            // teste :
            // teste :$club_id = \Session::get('club_id', null);
            // teste :$columns['club_id'] = $club_id;
            // teste :$this->data['row'] = $columns;

            $init_key =
                [
                    '$columns = $this->data[\'row\'];',
                    '$id' . ' = \Session::get(\'' . $module->id_key . '\', null);',
                    '$columns[\''. $module->id_key . '\']' . ' = '  . '$id;' ,
                    '$this->data[\'row\'] = $columns;'
                ];
            $this->editor->find('function getUpdate(Request $request, $id = null)');
            $this->editor->find('$this->data[\'row\'] = $this->model->getColumnTable');
            $this->editor->insert($init_key);
            $this->editor->save();


        }



        function has_many_end(Modele $modele, Module $module, Has_many $has_many)
        {
            echo '----has_many_end ', $module->nom, '<br>';
        }

        function belongs_to_begin(Modele $modele, Module $module, Module $parent)
        {
            echo '----belongs_to_begin ', $module->nom, ' belongs to ', $parent->nom, '<br>';
            $this->insert_parent_filter_in_controller($module, $parent);
        }

        /**
         * Injection of SQL filter on parent id into module controller.
         * Module controler is generated by Sximo but without filter.
         * Run after Sximo rebuild action in module.
         * Called for every module 'belongs to one' parent relation
         * Changes done in module, not in parent
         *
         * @param \Module $module Generate parent filter
         * @param \Module $parent Parent to get parent_id to filter on
         */
        function insert_parent_filter_in_controller(Module $module, Module $parent)
        {
            // Replace SQL where clause in controller
            $parent_id_key = $parent->id_key;
            $filter =
            [
                '$parent_id_key = ' . "'$parent_id_key';",
                '// Table',
                '$table = with(new static)->table;',
                '// clef primaire de la table',
                '$key = with(new static)->primaryKey;',
                '// Id du parent passée en paramètre?',
                '$id = \Session::get($parent_id_key, null);',
                'if (is_null($id))',
                '{',
                '    // No id,leave existing filter',
                '    $where = "  WHERE $table.$key IS NOT NULL ";',
                '}',
                'else',
                '{',
                '    // Filter by parent id',
                '    $where = "  WHERE $table.$parent_id_key = $id ";',
                '}',
                'return $where; '
            ];

            $file_path = 'app\\Models' . '\\' . $module->nom . '.php';
            $full_file_path = $this->laravel_project_path . '\\' . $file_path;
            $this->full_file_path = $full_file_path;
            $this->editor->edit($full_file_path);

            // Read controller source code for injection of generated code
            $begin = $this->editor->find('public static function queryWhere(  ){');
            $end = $this->editor->find('}');
            echo "$full_file_path<br>";
            echo "$begin $end<hr>";
            // Replace function body by generated code
            $this->editor->replace($begin, $end, $filter);
            $this->editor->save();
        }

        function belongs_to_end(Modele $modele, Module $module, Module $parent)
        {
            echo '----belongs_to_end ', $module->nom, '<br>';
        }

        function module_end(Modele $modele, Module $module)
        {
            echo '--Module end ', $module->nom, '<br>';
        }

        function modele_end(Modele $modele)
        {
            echo 'Modèle end ', $modele->nom, '<br>';
        }
    }