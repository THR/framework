<?php

class HtmlHelper
{
    public static function makeTable($array,$labels=array())
    {
        $i=0;
        echo '<table border="1" width="100%">';

        if(!empty($labels))
        {
            echo '<tr>';

                foreach($labels as $label):
                echo '<td><strong>';
                    echo $label;
                echo '</strong></td>';
                endforeach;
            echo '</tr>';
        }

        foreach($array as $row)
        {
            echo '<tr>';

            foreach($row as $col)
            {
                echo '<td>';

                echo $col;

                echo '</td>';
            }

            echo '</tr>';
        }
        echo '</table>';
    }
}