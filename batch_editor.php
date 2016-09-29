<?php
include 'Reformat.php';

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
	function search($pattern)
	{
		// Loop from current line pointer to search given pattern
        echo '<hr>';
        $max_pointer = count($this->lines);
        for($i = $this->current_pointer ; $i < $max_pointer ; $i++)
        {
            echo $i,' : ', htmlentities($this->lines[$i]),'<br>';
            $line = $this->lines[$i];
            if (($p = strpos($line, $pattern)) !== FALSE)
            {
                $this->current_pointer = $i;
                // Return line pointer
                return $i;
            }
        }
        // Fails if not found pattern
        die("Not found: $pattern");
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
        array_splice ($this->lines,
                             $from+1,
                             $nbr_of_lines_to_remove,
                             $replacement_text);
	}


    // Saves edited file
    function save()
    {
        foreach($this->lines as &$line)
        {
            $line .= "\n";
        }
        file_put_contents('code_genere.php', $this->lines);
        $r = new Reformat('code_genere.php');
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
