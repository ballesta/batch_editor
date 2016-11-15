<?php

    /**
     * Batch edit a PHP script or any text file
     *
     * Links: http://php.net/manual/fr/function.preg-match.php
     *
     * Class Batch_script_editor
     */
    class Batch_script_editor
    {
        var $file_to_edit;
        var $lines;
        var $current_pointer;
        // File already cleaned up of previous (existing) generated code)
        var $file_already_cleaned=[];
        var $generated_block_begin = '//(( Code generated begin';
        var $generated_block_end   = '//)) Code generated end';

        function __construct($laravel_project_path)
        {
            // To keep track of cleaned files (suppress existing generated block)
            $this->file_already_cleaned=[];
	        $this->laravel_project_path = $laravel_project_path;
        }

        function edit($file_path)
        {
	        $full_file_path = $this->laravel_project_path . '\\' . $file_path;
            // Remember file name
            $this->file_to_edit = $full_file_path;
            // Read file to edit in array
            $this->lines = file($full_file_path,
                                FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            // First time seen only
            $this->remove_generated_code_blocks();
        }

        /**
         * Remove generated code upon first time encountered.
         * Used before to regenerate from Code_generator.
         * Adds file to already_generated
         * File content already in core
         */
        function remove_generated_code_blocks()
        {
            // First time file encountered ?
            if (!isset($this->file_already_cleaned[$this->file_to_edit]))
            {
                //echo "<br>**** clean: $this->file_to_edit]<hr>";
                do {
                    // Yes: remove generated code
                    // Init search pointer to beginning of file
                    $block_deleted = FALSE;
                    $this->move_to_beginning();
                    $begin = $this->search($this->generated_block_begin);
                    if (!($begin === FALSE)) {
                        // Found beginning of generated code
                        $end = $this->search($this->generated_block_end);
                        if (!($end === FALSE)) {
                            // Found corresponding end of generated code
                            // Delete block including open dans close markers
                            $this->delete($begin - 1, $end + 1);
                            $block_deleted = TRUE;

                        } else {
                            echo "No end of generated code, line : $begin";
                            die();
                        }
                    }
                } while ($block_deleted);
                // Done only once (first time file encountered)
                // Will not be processed next time
                $this->file_already_cleaned[$this->file_to_edit] = TRUE;
            }
            //var_dump($this->file_already_cleaned);
            $this->move_to_beginning();
        }

        function move_to_beginning()
        {
            $this->current_pointer = 0;
        }

        function search($pattern)
        {
            // Loop from current line pointer to search given pattern
            //echo '<hr>';
            $max_pointer = count($this->lines);
            for ($i = $this->current_pointer; $i < $max_pointer; $i++) {
                // echo $i,' : ', htmlentities($this->lines[$i]),'<br>';
                $line = $this->lines[$i];
                if (strpos($line, $pattern) === FALSE) {
                    // Not found, continue
                } else {
                    // Found
                    $this->current_pointer = $i;

                    // Return line pointer
                    return $i;
                }
            }

            // Fails if not found pattern
            return FALSE;
        }

        function delete($from, $to)
        {
            // Delete lines inside $from ... $to
            $nbr_of_lines_to_remove = $to - $from - 1;
            array_splice($this->lines, $from + 1, $nbr_of_lines_to_remove);
        }

        function move_to_end()
        {
            $this->current_pointer = count($this->lines);
        }

        // Search that must find

        function find($pattern)
        {
            if (($p = $this->search($pattern)) == FALSE) {
                // Fails if not found pattern
                echo "File: $this->file_to_edit<br>";
                die("*** Not found: find($pattern) ***");
            } else {
                return $p;
            }
        }

        /**
         * Search and find a regular expression in current line
         *
         * @param $regexp_pattern
         *
         * @return mixed
         */
        function find_regex($regexp_pattern)
        {
            $subject = $this->lines[$this->current_pointer];;
            $result = preg_match($regexp_pattern, $subject, $matches);
            if ($result === 1) {
                // Found pattern
                return $matches;
            } else {
                echo $subject, '<br>';
                die("*** Not found regexp: find_regex($regexp_pattern) ***");
            }
        }

        function insert_before($text)
        {
            $text_identified = $this->identifies_generated_block($text);
            array_splice($this->lines, $this->current_pointer, 0, $text_identified);
            $this->current_pointer += count($text);
        }

        function identifies_generated_block($text)
        {
            // Check if blade (html) file
            if (strpos($this->file_to_edit, '.blade') === FALSE) {
                // Not found: php source code, use php comments
                $begin = '//' . $this->generated_block_begin;
                $end   = '//' . $this->generated_block_end;
            } else {
                // Found: Blade, comments must be XML otherwise they are displayed
                $begin = '<!--' . $this->generated_block_begin . '-->';
                $end   = '<!--' . $this->generated_block_end   . '-->';
            }
            return array_merge([$begin], $text, [$end]);
        }

        function insert($text)
        {
            $this->insert_after($text);
        }

        function insert_after($text)
        {
            $text_identified = $this->identifies_generated_block($text);
	        // Splice = 'épisure' in french
            array_splice($this->lines,              // Input and output apres épisure
	                     $this->current_pointer + 1,// Offset where to cut ans splice
	                     0,                         // Length suppressed part
                                                    // No suppression in our case
	                     $text_identified           // Text to add
                        );
            $this->current_pointer += count($text);
        }

        function replace($from, $to, $replacement_text)
        {
            // Delete lines inside $from ... $to
            // Then replace with new text
            $replacement_text_identified = $this->identifies_generated_block($replacement_text);
            //print_r($replacement_text_identified); die();
            $nbr_of_lines_to_remove = $to - $from - 1;
            $suppressed_lines = array_splice($this->lines, $from + 1, $nbr_of_lines_to_remove, //$replacement_text
                $replacement_text_identified);
            //print_r( $suppressed_lines); die();
        }

        // Replace regexp in current line
        public function replace_regexp($from_regexp_pattern, $to_string)
        {
            $line = $this->lines[$this->current_pointer];
            $matches = 0;
            $result = preg_replace( $from_regexp_pattern
	                              , $to_string
	                              , $line
	                              , 1
	                              , $matches);
            $this->lines[$this->current_pointer] = $result;
            if ($matches == 1) {
                // Found and replaced pattern
                return TRUE;
            } else {
                die("*** Not found regexp to replace: $from_regexp_pattern ***");
            }
        }

        function replace_words($dictionnaire, $texte)
        {
            foreach ($dictionnaire as $mot => $remplacer) {
                $texte = str_replace('(----' . $mot . '----)', $remplacer, $texte);
            }

            return "    //(( Generated by LaraSpeed\n" . $texte . "    //)) End ";
        }

        // Saves edited file
        function save()
        {
            // $l = 1;
            $level = 0;
            foreach ($this->lines as &$line) {
                if ((strpos($line, '}')) !== FALSE) {
                    $level--;
                }

                $line = $this->identation($level) . trim($line) . "\n";

                if (strpos($line, '{') !== FALSE) {
                    $level++;
                }
            }
            $this->backup_file();
            file_put_contents($this->file_to_edit, $this->lines);
            // Several changes possible after file is opened
	        //$this->lines = []; // No! do not do it!
        }

        // Copy file to backup

        function identation($level)
        {
            $identation = '';
            for ($i = 1; $i <= $level; $i++)
                $identation .= '    ';

            return $identation;
        }

        function backup_file()
        {
            $file_name_without_suffix = substr($this->file_to_edit, 0, -4);
            $suffix = substr($this->file_to_edit, -4);
            // Concatenate time in seconds to original file name
            $backup_file_name = $file_name_without_suffix . '_' . time() . $suffix;
            // ++++ No backup
            // copy($this->file_to_edit, $backup_file_name);
        }

        function display($title)
        {
            echo "<hr><h1>$title</h1>";
            echo "Curseur:$this->current_pointer<br>";
            echo "<pre>";
            foreach ($this->lines as $line) {
                echo htmlentities($line), '<br>';
            }
            echo "</pre>";
            echo "*** fin ***";
        }

    }
