<?php
/************/
/************/
/************/
/************/
 
function is_base64_encoded($data)
{
    if(base64_encode(base64_decode($data, true)) === $data)
    {
        // echo 'Not a Base64-encoded string';
        return true;
    }else{
        return false;
    }
};
/************/
function print_r_reverse($in) {
    $lines = explode("\n", trim($in));
    if (trim($lines[0]) != 'Array') {
        // bottomed out to something that isn't an array
        return $in;
    } else {
        // this is an array, lets parse it
        if (preg_match("/(\s{5,})\(/", $lines[1], $match)) {
            // this is a tested array/recursive call to this function
            // take a set of spaces off the beginning
            $spaces = $match[1];
            $spaces_length = strlen($spaces);
            $lines_total = count($lines);
            for ($i = 0; $i < $lines_total; $i++) {
                if (substr($lines[$i], 0, $spaces_length) == $spaces) {
                    $lines[$i] = substr($lines[$i], $spaces_length);
                }
            }
        }
        array_shift($lines); // Array
        array_shift($lines); // (
        array_pop($lines); // )
        $in = implode("\n", $lines);
        // make sure we only match stuff with 4 preceding spaces (stuff for this array and not a nested one)
        preg_match_all("/^\s{4}\[(.+?)\] \=\> /m", $in, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $pos = array();
        $previous_key = '';
        $in_length = strlen($in);
        // store the following in $pos:
        // array with key = key of the parsed array's item
        // value = array(start position in $in, $end position in $in)
        foreach ($matches as $match) {
            $key = $match[1][0];
            $start = $match[0][1] + strlen($match[0][0]);
            $pos[$key] = array($start, $in_length);
            if ($previous_key != '') $pos[$previous_key][1] = $match[0][1] - 1;
            $previous_key = $key;
        }
        $ret = array();
        foreach ($pos as $key => $where) {
            // recursively see if the parsed out value is an array too
            $ret[$key] = print_r_reverse(substr($in, $where[0], $where[1] - $where[0]));
        }
        return $ret;
    }
} 
/************/
/************/
/************/

function XML2JSON($xml)
{
    function normalizeSimpleXML($obj, &$result)
    {
        $data = $obj;
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $res = null;
                normalizeSimpleXML($value, $res);
                if (($key == '@attributes') && ($key)) {
                    $result = $res;
                } else {
                    $result[$key] = $res;
                }
            }
        } else {
            $result = $data;
        }
    }
    normalizeSimpleXML(simplexml_load_string($xml), $result);
    return json_encode($result);
}
/************/
/************/
/************/
function html_format($html, $indentWith = '    ', $tagsWithoutIndentation = 'html,link,img,meta')
{
    // remove all line feeds and replace tabs with spaces
    $html     = str_replace(["\n", "\r", "\t"], ['', '', ' '], $html);
    $elements = preg_split('/(<.+>)/U', $html, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $dom      = parseDom($elements);

    $indent = 0;
    $output = array();
    foreach ($dom as $index => $element) {
        if ($element['opening']) {
            $output[] = "\n" . str_repeat($indentWith, $indent) . trim($element['content']);

            // make sure that only the elements who have not been blacklisted are being indented
            if (!in_array($element['type'], explode(',', $tagsWithoutIndentation))) {
                ++$indent;
            }
        } else if ($element['standalone']) {
            $output[] = "\n" . str_repeat($indentWith, $indent) . trim($element['content']);
        } else if ($element['closing']) {
            --$indent;
            $lf = "\n" . str_repeat($indentWith, abs($indent));
            if (isset($dom[$index - 1]) && $dom[$index - 1]['opening']) {
                $lf = '';
            }
            $output[] = $lf . trim($element['content']);
        } else if ($element['text']) {
            // $output[] = "\n".str_repeat($indentWith, $indent).trim($element['content']);
            $output[] = "\n" . str_repeat($indentWith, $indent) . preg_replace('/ [ \t]*/', ' ', $element['content']);
        } else if ($element['comment']) {
            $output[] = "\n" . str_repeat($indentWith, $indent) . trim($element['content']);
        }
    }

    return trim(implode('', $output));
}

/**
 * Parses an array of HTML tokens and adds basic information about about the type of
 * tag the token represents.
 *
 * @param Array $elements Array of HTML tokens (tags and text tokens).
 * @return Array HTML elements with extra information.
 */
function parseDom(array $elements)
{
    $dom = array();
    foreach ($elements as $element) {
        $isText       = false;
        $isComment    = false;
        $isClosing    = false;
        $isOpening    = false;
        $isStandalone = false;

        $currentElement = trim($element);

        // comment
        if (strpos($currentElement, '<!') === 0) {
            $isComment = true;
        }
        // closing tag
        else if (strpos($currentElement, '</') === 0) {
            $isClosing = true;
        }
        // stand-alone tag
        else if (preg_match('/\/>$/', $currentElement)) {
            $isStandalone = true;
        }
        // normal opening tag
        else if (strpos($currentElement, '<') === 0) {
            $isOpening = true;
        }
        // text
        else {
            $isText = true;
        }

        $dom[] = array(
            'text'       => $isText,
            'comment'    => $isComment,
            'closing'    => $isClosing,
            'opening'    => $isOpening,
            'standalone' => $isStandalone,
            'content'    => $element,
            'type'       => preg_replace('/^<\/?(\w+)[ >].*$/U', '$1', $element),
        );
    }
    return $dom;
}
/************/
/************/

function human_filesize($bytes, $decimals = 2)
{
    $factor = floor((strlen($bytes) - 1) / 3);
    if ($factor > 0) {
        $sz = 'KMGT';
    }

    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor - 1] . 'B';
}
/************/
/************/

function stc($str)
{
    $code = dechex(crc32($str));
    $code = substr($code, 0, 6);
    return '#' . $code;
}
/************/
/************/
function nl2p($string, $line_breaks = true, $xml = true)
{

    $string = str_replace(array('<p>', '</p>', '<br>', '<br />'), '', $string);

// It is conceivable that people might still want single line-breaks
    // without breaking into a new paragraph.
    if ($line_breaks == true) {
        return '<p>' . preg_replace(array("/([\n]{2,})/i", "/([^>])\n([^<])/i"), array("</p>\n<p>", '$1<br' . ($xml == true ? ' /' : '') . '>$2'), trim($string)) . '</p>';
    } else {
        return '<p>' . preg_replace(
            array("/([\n]{2,})/i", "/([\r\n]{3,})/i", "/([^>])\n([^<])/i"),
            array("</p>\n<p>", "</p>\n<p>", '$1<br' . ($xml == true ? ' /' : '') . '>$2'),

            trim($string)) . '</p>';
    }

}
/************/
function get_words($sentence, $count = 10, $suffix = '...')
{
    preg_match("/(?:\w+(?:\W+|$)){0,$count}/", $sentence, $matches);
    return $matches[0] . $suffix;
}
/************/
function time_to_int($time)
{
    // time format = H:i:s max (23:59:59)
    $durasi = strtotime($time) - strtotime('TODAY');
    return $durasi;
    // $durasi = gmdate('H:i:s', $durasi);
    // opn($durasi);exit();
}
/************/
/************/
function lq()
{
    $ci = &get_instance();
    return $ci->db->last_query();
}
/************/
function opn($array)
{
    echo '<pre style="border:2px solid #ccc; background-color:#eee;padding:4px;border-radius:0px;margin:62px 10 0 ">';
    echo '<div style="border:1px solid #ccc; padding:10px 10px;border-radius:0px">';
    // echo '<pre>';
    // var_dump($array);
    print_r($array);
    // echo '</pre> ';
    echo '</div>';
    echo '</pre>';
}

/************/
if (!function_exists('time_ago')) {
    function time_ago($ptime)
    {
        $etime = time() - $ptime;
        if ($etime < 1) {
            return '0 seconds';
        }
        $a = array(
            365 * 24 * 60 * 60 => 'year',
            30 * 24 * 60 * 60  => 'month',
            24 * 60 * 60       => 'day',
            60 * 60            => 'hour',
            60                 => 'minute',
            1                  => 'second');
        $a_plural = array(
            'year'   => 'years',
            'month'  => 'months',
            'day'    => 'days',
            'hour'   => 'hours',
            'minute' => 'minutes',
            'second' => 'seconds');

        foreach ($a as $secs => $str) {
            $d = $etime / $secs;
            if ($d >= 1) {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
            }
        }
    }
}

/**
---
 */

/**
 * Generate a URL friendly "slug" from a given string.
 *
 * @param  string  $title
 * @param  string  $separator
 * @return string
 */
function slug($title, $separator = '-')
{

    // $title = strtoentity($title);

    // Convert all dashes/underscores into separator
    $flip  = $separator == '-' ? '_' : '-';
    $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);

    // Remove all characters that are not the separator, letters, numbers, or whitespace.
    $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title));

    // Replace all separator characters and whitespace by a single separator
    $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);
    return trim($title, $separator);
}

/**
 * Generate a URL friendly "slug" from a given string.
 *
 * @param  string  $title
 * @param  string  $separator
 * @return string
 */
function slug_($title, $separator = '_')
{

    // $title = strtoentity($title);

    // Convert all dashes/underscores into separator
    $flip  = $separator == '-' ? '_' : '-';
    $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);

    // Remove all characters that are not the separator, letters, numbers, or whitespace.
    $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', mb_strtolower($title));

    // Replace all separator characters and whitespace by a single separator
    $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);
    return trim($title, $separator);
}

/**
---
 */
function strtoentity($input)
{
    $output = '';
    foreach (str_split($input) as $obj) {
        $output .= '&#' . ord($obj) . ';';
    }
    return $output;
}

/**
---
 */

function get_table($table, $prefix = null)
{
    $table    = (substr($table, 0, 3) == 'ae_') ? str_replace('ae_', '', $table) : $table;
    $ae_table = ('' != $table) ? (substr($table, 0, 3) == $prefix ? $table : $prefix . $table) : '';
    return $ae_table;
}

/**
---
 */
function base64topng($path, $name, $base64ofimage)
{
    list($type, $base64ofimage) = explode(';', $base64ofimage);
    list(, $base64ofimage)      = explode(',', $base64ofimage);

    $base64ofimage = base64_decode($base64ofimage);
    $name          = $name . '.png';

    file_put_contents($path . $name, $base64ofimage);

    return $name;
}

/**
---
 */
function get_image($url)
{
    $type    = pathinfo($url, PATHINFO_EXTENSION);
    $data    = file_get_contents($url);
    $dataUri = 'data:image/' . $type . ';base64,' . base64_encode($data);
    // $b64image = base64_encode(file_get_contents($image));
    return $dataUri;
}
