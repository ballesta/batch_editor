<?php
//include 'Reformat.php';

// Batch edit a PHP script or any text file
class Batch_script_editor
{
    var $file_to_edit;
    var $lines;
    var $current_pointer;

	function __construct($file_to_edit )
	{
        // Remember file name
        $this->file_to_edit = $file_to_edit;
		// Read file to edit in array
        $this->lines = file($file_to_edit ,
                            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		// Init search pointer to beginning of file
        $this->current_pointer = 0;
	}

    function move_to_begining()
    {
        $this->current_pointer = 0;
    }

    function move_to_end()
    {
        $this->current_pointer = count($this->lines);
    }

    var $block_begin = '';
    var $block_end = '';

    function remove_generated_code_blocks()
    {

    }

	function search($pattern)
	{
		// Loop from current line pointer to search given pattern
        //echo '<hr>';
        $max_pointer = count($this->lines);
        for($i = $this->current_pointer ; $i < $max_pointer ; $i++)
        {
            //echo $i,' : ', htmlentities($this->lines[$i]),'<br>';
            $line = $this->lines[$i];
            if (($p = strpos($line, $pattern)) !== FALSE)
            {
                $this->current_pointer = $i;
                // Return line pointer
                return $i;
            }
        }
        // Fails if not found pattern
        return false;
	}

	// Search that must find
    function find($pattern)
    {
        if (($p = $this->search($pattern)) == FALSE)
        {
            // Fails if not found pattern
            die("*** Not found: $pattern ***");
        }
        else
        {
            return $p;
        }
    }

    function insert_before($text)
    {
        array_splice ($this->lines,
                      $this->current_pointer,
                      0,
                      $text);
        $this->current_pointer += count($text);
    }
	function insert_after($text)
	{
        array_splice ($this->lines,
            $this->current_pointer+1,
            0,
            $text);
        $this->current_pointer += count($text);
	}

	function replace($from, $to, $replacement_text)
	{
		// Delete lines inside $from ... $to
		// Then replace with new text
        $nbr_of_lines_to_remove = $to-$from -1;
        //echo "--------->nbr_of_lines_to_remove:$nbr_of_lines_to_remove<br>";
        //echo "--------->replacement_text:$replacement_text[0]<br>";

        array_splice ($this->lines,
                             $from+1,
                             $nbr_of_lines_to_remove,
                             $replacement_text);
	}

    function  replace_words($dictionnaire, $texte)
    {
        foreach ($dictionnaire as $mot => $remplacer)
        {
            $texte = str_replace('(----'. $mot . '----)',
                $remplacer,
                $texte);
        }
        return    "    //(( Generated by LaraSpeed\n"
        .      $texte
        . "    //)) End ";
    }

    // Saves edited file
    function save()
    {
        $l=1;
        $level = 0;
        foreach($this->lines as &$line)
        {
            if ((strpos($line, '}')) !== FALSE)
            {
                $level--;
            }

            $line = $this->identation($level) . trim($line) . "\n";

            if (strpos($line, '{') !== FALSE)
            {
                $level++;
            }
        }
        $this->backup_file();
        file_put_contents($this->file_to_edit, $this->lines);
    }

    // Copy file to backup
    function backup_file()
    {
        $file_name_without_suffix = substr($this->file_to_edit, 0, -4);
        $suffix = substr($this->file_to_edit, -4);
        // Concatenate time in seconds to original file name
        $backup_file_name = $file_name_without_suffix . '_' . time() . $suffix;
        copy($this->file_to_edit, $backup_file_name);
    }

    function identation($level)
    {
        $identation = '';
        for ($i=1; $i <= $level; $i++)
            $identation .= '    ';
        return $identation;
    }

    function display($title)
    {
        echo "<hr><h1>$title</h1>";
        echo "Curseur:$this->current_pointer<br>";
        echo "<pre>";
        foreach($this->lines as $line)
        {
            echo htmlentities($line),'<br>';
        }
        echo "</pre>";
        echo "*** fin ***";
    }
}
