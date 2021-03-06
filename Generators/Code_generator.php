<?php
    /**
     * Code generators base class
     *
     * Created by PhpStorm.
     * User: bernard
     * Date: 29/09/2016
     * Time: 16:08
     */

    include '../../editor/batch_editor.php';

    abstract class Code_generator
    {
        // Location of de target Laravel project
        var $laravel_project_path;
        // File path being generated
        var $file_path;
        // Content (source code) being generated
        var $content;
        // Path of file being generated
        var $full_file_path;
        // Trace files generated to remove existing generated code
        var $file_already_generated= [];

        function __construct($laravel_project_path)
        {
            // Remember project
            $this->laravel_project_path = $laravel_project_path;
            // Editor session for whole code generation session
            $this->editor = new Batch_script_editor($laravel_project_path);
        }

        // Add generated source code to a file
        function inject_source_code_begin($file_path_in_project)
        {
            $this->file_path = $this->laravel_project_path
                             . '/'
                             . $file_path_in_project;
            $this->content = file_get_contents($this->file_path);
        }

	    function inject_source_code_end()
	    {
		    file_get_contents($this->file_path, $this->content);
	    }


	    function get_name_from_id($id_or_name)
	    {
		    return $id_or_name;
	    }

        abstract function modele_begin(Modele $modele);

        abstract function modele_end(Modele $modele);

        abstract function module_begin(Modele $modele, Module $module);

        abstract function module_end(Modele $modele, Module $module);

        abstract function has_many_begin(Modele $modele, Module $module, Has_many $Has_many);

        abstract function has_many_end(Modele $modele, Module $module, Has_many $Has_many);

        abstract function belongs_to_begin(Modele $modele, Module $module, Module $parent);

        abstract function belongs_to_end(Modele $modele, Module $module, Module $parent);
    }