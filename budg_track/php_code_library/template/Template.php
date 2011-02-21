<?php

class Template
{
    /* replaceValues
     *
     * For each key in replacementRules, find {key} in the passed in data and replace 
     * with the value at that location. 
     *
     */
    function replaceValues($data, $replacementRules)
    {
        if (!is_array($replacementRules))
        {
            trigger_error("Argument specified to Template::replaceValues was not an array.");
            print_r($replacementRules);
        }
        // Add {} to key values, note this function would be more efficient if
        // The user did this
        $keys = array_keys($replacementRules);
        foreach ($keys as &$key)
        {
            // trigger error on keys non-scalar? won't str_replace do that?
            $key = '{' . strtoupper($key) . '}';
        }
        
        $data = str_replace($keys, array_values($replacementRules), $data); 

        // Split $values into two arrays, one of keys and one of replacements
        return $data;
    }


    /* replaceWithinBlock
     *  
     *  Find the first occurence of a block of {BLOCK:} {:BLOCK} extract the templated text, replace
     *  the values within that block with the replacement rules then repeate the templated text
     */
    function replaceWithinBlock($data, $block, $replacementRules)
    {
        // Do this without regexps
        // This could be made more efficient if the user specified where to start searching
        $block_begin = '{' . $block . ':}';
        $begin_pos = strpos($data, $block_begin); 

        if ($begin_pos !== FALSE)
        {
            // starts searching after 
            $block_end = '{:' . $block . '}';
            $end_pos = strpos($data, $block_end, $begin_pos);
            if ($end_pos !== FALSE)
            {
                $length = $end_pos - $begin_pos + strlen($block) + 3;
                if ($length > 0)
                {
                    // Extract the block
                    $block_text = substr($data, $begin_pos, $length);

                    // Replace within the block according to the rules
                    // and remove the tags
                    $replacementRules[':' . $block] = '';
                    $replacementRules[$block . ':'] = '';
                    $block_text_repl = Template::replaceValues($block_text, $replacementRules);

                    // Append the block to the replaced values
                    $block_text_repl .= $block_text;

                    // Copy the new copy for the block back into the string
                    $data = str_replace($block_text, $block_text_repl, $data);
                }
            }
        }
        return $data;
    }

    /* unhideBlock
     * 
     * Templates can also be written in the form {KEY:}blablabla{:KEY}.  This
     * call will remove the delimiters, leaving only blablabla in its place.  If
     * you don't call this or replace the KEY key with something (which also
     * converts it to a standard {KEY} key), the call to finalize() will remove
     * the entire thing, including blablabla.
     * 
     * Like with the other functions, this accepts an array of blocks.  If you
     * only pass a single value, it'll be treated as an array of one.
     */
    function unhideBlock($data, $keys) 
    {
        if (!is_array($keys)) 
        {
            $keys = array($keys);
        }
        foreach ($keys as $key) 
        {
            $data = str_replace('{' . $key. ':}', "", str_replace('{:' . $key . '}', "", $data));
        }
        return $data;
    } //}}}

    
    /* finalize
     * In case not all placeholders in the template got parsed out and replaced
     * with some real data, you should call this method to remove them before
     * spitting the whole thing out to the browser.
     */
    function finalize($data) 
    {
        $data = preg_replace("/\{[A-Z_]+(\|[^}]+)?\}/", "", $data);
        return preg_replace("/\{([A-Z_]*):\}.*?\{:\\1}/s", "", $data);
    } //

}


?>
