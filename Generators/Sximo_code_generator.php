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
	        $module->queryWhere = [];
	        $this->insert_breadcrum($modele, $module);

	        if ($module->nom == 'reseauxsalles')
	        {
		        $module->queryWhere[] = $this->filter_by_user_reseau($module);
	        }
	        elseif ($module->nom == 'complexesportif')
		        $module->queryWhere[] = $this->filter_by_user_complexe_sportif($module);
        }

	    function filter_by_user_reseau(Module $module)
	    {
		    // Generate function to filter on current id
		    $table = $module->table;

		    $f = <<<END
		\$role = \Session::get('gid');
		if (\$role == 1 ||  \$role == 2)
		{
		    // Administrator: no filters by reseau
		}
		elseif (\$role == 4  ||  \$role == 5)
		{
		    // Responsable reseau : limit to user network
		    \$club_id = \Session::get('user_club_id');
		    \$where[] =  "   $table.club_id = \$club_id ";
		}
END;
		    return $f;
	    }

	    function filter_by_user_complexe_sportif(Module $module)
	    {
		    // Generate function to filter on current id
		    $table = $module->table;

		    $f = <<<END
		    // Filter depending on role
	        \$role = \Session::get('gid');
	        if (   \$role == 1  
	            || \$role == 2
	            || \$role == 4	            
	            )
	        {
	            // Administrators, responsable reseau: no filters by reseau
	        }
	        elseif (\$role == 5)
	        {
		        // Responsable complexe sportif : complexe sportif
		        \$id = \Session::get('user_complexe_salle_id');
		        \$where[] =  " $table.complexe_salle_id = \$id ";
	        }
END;
		    return $f;
	    }

        function has_many_begin(Modele $modele, Module $module, Has_many $has_many)
        {
            echo '----has_many_begin ----<br> ',
                 '---- ---- ', $module->nom, ' hasmany ',
	             $has_many->module_detail->nom . '<br>';
            $this->insert_link_to_detail_grid($modele, $module, $has_many);
            $this->insert_remember_filter_key_into_controller($modele, $module, $has_many);
            $this->insert_init_parent_key_into_controller($modele, $module, $has_many);
        }

	    // Insert modules breadcrumb
	    function insert_breadcrum(Modele $modele, Module $module)
	    {
		    // Get file to update
		    $file_path = 'resources/views' . '/' . $module->nom . '/index.blade.php';
		    // Remove generated code previously generated code on first read
		    $this->editor->edit($file_path);

		    // Do the changes

		    // Find line containing pattern
		    $this->editor->find('<div class="sbox">');
		    // Insert after current line
		    $parents_modules_inverse = $module->breadcrumb_ascendants($module);
		    $parents_modules = array_reverse($parents_modules_inverse);
		    $nbr_breadcrumb = count($parents_modules);
		    $n=1;
		    if ($nbr_breadcrumb > 0) {
			    $b=[];
			    $b[] = '<table>';
			    $b[] = '<tr>';
			    foreach ($parents_modules as $m)
			    {
					// Entity type
				    $b[] = '<td>';
				    $b[] = '<small>' . $m->title . '</small><br>';
				    // Entity label
				    $id_key = $m->id_key . '_identifier';
				    $label = "{{ Session::get('$id_key', null) }}";
				    $b[] = '<strong>' . $label . '</strong>';
				    $b[] = '<td>';
				    // Breadcrumb separator if not last displayed
				    if ($n < $nbr_breadcrumb) {
					    $b[] = '<td>';
					    $b[] = '&nbsp;&nbsp;<i class="icon-arrow-right2"></i>&nbsp;&nbsp;';
					    $b[] = '<td>';
				    }
				    $n++;
			    }
			    $b[] = '</tr>';
			    $b[] = '</table>';
			    $this->editor->insert($b);
		    }
		    // Must save even if nothing changed
		    $this->editor->save();
	    }

        // Insert button in list to drill down to detailed list
        function insert_link_to_detail_grid(Modele $modele, Module $module, Has_many $has_many)
        {
            // Get file to update
            $file_path = 'resources/views' . '/' . $module->nom . '/index.blade.php';
            // Save path to wite generated file
            //$this->full_file_path = $full_file_path;
            $this->editor->edit($file_path);

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
                '\Navigation::link_to_detail(  '                    ,
                '$text        = ' . "'$module_title',"              ,
                '$help        = ' . "'$has_many->explications',"    ,
                '$url         = ' . "URL::to('$module_name'),"      ,
                '$parent_key  = ' . "'$module->id_key',"            ,
                '$parent_label= ' . "'$module->identifier',"        ,
	            '$parent_id   = ' . '$row->' . $module->id_key . ",",
	            '$parent_name = ' . '$row->' . $module->identifier  ,
                ')' ,
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
            $file_path  = 'app/Http/Controllers'
	                    . '/'
                        . ucfirst($detail_module_name) . 'Controller.php';
            $this->editor->edit($file_path);

            // Do the changes

	        // 1. Remove descendants when a top level changes
	        // Example: Remove all id from session when "Reseau de salles" changes
	        // By change we mean when the id is pushed in session

	        $descendants  = $module->breadcrumb_descendants($module);
	        $remove_ids = [];
	        foreach ($descendants as $d)
	        {
		        $remove_ids [] = '\Session::forget("' . $d->id_key . '");';
		        $remove_ids [] = '\Session::forget("' . $d->id_key . '_identifier' . '");';
	        }
	        $this->editor->find('function getIndex( Request $request )');
	        $this->editor->find('{');
	        $this->editor->insert($remove_ids);
	        $this->editor->save();

            // 2. Get Key and label from URL then add it to Session
            //        $club_id = $request->query("club_id");
            //        \Session::put("club_id", $club_id);
            $save_key =
            [
                '// Get parameter in URL to use it as filter',
	            '$id'. ' = ' . '$request->query("' . $module->id_key . '");',
	            '$identifier'. ' = ' . '$request->query("' . $module->identifier . '");',
                'if (!is_null($id))',
	            '{',
                '    \Session::put("' . $module->id_key .'", $id);',
	            '    \Session::put("' . $module->id_key . '_identifier", $identifier);',
	            '}',
	            '$id = \Session::get("' . $module->id_key . '", null);',
	            '$active_filter = \Session::get("'. $module->id_key . '_identifier");' ,
	            '// Check if parent already selected',
	            'if (is_null($id))',
	            '{',
		        '    return Redirect::to("' . $module->nom . '")',
			    '    ->with("messagetext",',
				'        "Vous devez d\'abord sélectionner votre <br> "',
				'        ."<i>' . $module->title .'</i> <br>"',
				'        ."avant de choisir <br>"',
				'        ."<i>'. $detail_module_name_title .'</i>")',
			    '    ->with("msgstatus","warning");',
	            '}'
            ];

	        $this->editor->move_to_beginning();
            $this->editor->find('function getIndex( Request $request )');
            $this->editor->find('{');
            $this->editor->insert($save_key);
            $this->editor->save();
        }

        function insert_init_parent_key_into_controller($modele, $module, $has_many)
        {
            // Get file to update
            $detail_module_name = $has_many->module_detail->nom;
            $file_path  = 'app/Http/Controllers'
	                    . '/'
	                    . ucfirst($detail_module_name)
	                    . 'Controller.php';
            // Save path to wite generated file
            //$this->full_file_path = $full_file_path;
            $this->editor->edit($file_path);

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
            $this->insert_parent_filter_in_module($module, $parent);
        }

        /**
         * Injection of SQL filter on parent id into module model.
         * Model is generated by Sximo but without filter.
         * Run after Sximo rebuild action in module.
         * Called for every module 'belongs to one' parent relation
         * Changes done at module level, not in parent
         *
         * Adds also the filter on Reseaux and Clubsportif
         * depending on the user role.
         *
         *
         * @param \Module $module Generate parent filter
         * @param \Module $parent Parent to get parent_id to filter on
         */
        function insert_parent_filter_in_module(Module $module, Module $parent)
        {
            // Replace SQL where clause in model
            $parent_id_key = $parent->id_key;
            $filter =
            [
            	'// Filter on parent ',
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
                '    $where = "   $table.$key IS NOT NULL ";',
                '}',
                'else',
                '{',
                '    // Filter by parent id',
                '    $where = "   $table.$parent_id_key = $id ";',
                '}'
            ];

	        $module->queryWhere[] = $this->array_to_string($filter);
	        //var_dump($module->queryWhere);
        }

        // Convert array of strings to e signle string
        function array_to_string($array)
        {
	        $s = '';
	        foreach ($array as $line)
	        {
		        $s .= $line . "\n";
	        }
	        return $s;
        }


        function belongs_to_end(Modele $modele, Module $module, Module $parent)
        {
            echo '----belongs_to_end ', $module->nom, '<br>';
        }

        function module_end(Modele $modele, Module $module)
        {
            echo '--Module end ', $module->nom, '<br>';
	        // Generate function queryWhere based on stored $module->queryWhere[]
	        $this->substitute_queryWhere_in_model($modele, $module);

        }

        function substitute_queryWhere_in_model(Modele $modele, Module $module)
        {
	        $file_path = 'app/Models' . '/' . ucfirst($module->nom) . '.php';
	        $this->editor->edit($file_path);

	        // Read model source code for injection of generated code
	        $begin = $this->editor->find('public static function queryWhere(  ){');
	        $end = $this->editor->find('}');
	        // Generate code to compose  WHERE clause
	        $gen_queryWhere =<<<END
	        \$key_word='WHERE ';
	        foreach (\$module->queryWhere as \$c)
	        {
		        \$queryWhere .= \$key_word . \$c ;
		        // Following conditions
		        \$key_word='AND ';
	        }
			return \$queryWhere;
END;
	        // Replace function body by generated code
	        //var_dump($gen_queryWhere);
	        $this->editor->replace($begin, $end, [$gen_queryWhere]);
	        $this->editor->save();
        }


	    function modele_end(Modele $modele)
        {
            echo 'Modèle end ', $modele->nom, '<br>';
        }
    }