<?php
// ----------------------------------------------------------------------------
// Zoom Search Engine 5.1 (29/02/2008)
// PHP search front-end
// A fast custom website search engine using pre-indexed data files.
// Copyright (C) Wrensoft 2000 - 2008
//
// This script is designed for PHP 4.0+ only.
//
// email: zoom@wrensoft.com
// www: http://www.wrensoft.com
// ----------------------------------------------------------------------------

if(strcmp('4.0.0', phpversion()) > 0)
    die("This version of the script requires PHP 4.0.0 or higher.<br />");

$SETTINGSFILE = dirname(__FILE__)."/settings.php";
$WORDMAPFILE = dirname(__FILE__)."/zoom_wordmap.zdat";
$DICTIONARYFILE = dirname(__FILE__)."/zoom_dictionary.zdat";
$PAGEDATAFILE = dirname(__FILE__)."/zoom_pagedata.zdat";
$SPELLINGFILE = dirname(__FILE__)."/zoom_spelling.zdat";
$CATSFILE = dirname(__FILE__)."/zoom_cats.zdat";
$PAGETEXTFILE = dirname(__FILE__)."/zoom_pagetext.zdat";
$PAGEINFOFILE = dirname(__FILE__)."/zoom_pageinfo.zdat";
$RECOMMENDEDFILE = dirname(__FILE__)."/zoom_recommended.zdat";

// Check for dependent files
if (!file_exists($SETTINGSFILE) || !file_exists($WORDMAPFILE) || !file_exists($DICTIONARYFILE))
{
    print("<b>Zoom files missing error:</b> Zoom is missing one or more of the required index data files.<br />Please make sure the generated index files are uploaded to the same path as this search script.<br />");
    return;
}

require($SETTINGSFILE);

if ($Spelling == 1 && !file_exists($SPELLINGFILE))
    print("<b>Zoom files missing error:</b> Zoom is missing the 'zoom_spelling.zdat' file required for the Spelling Suggestion feature which has been enabled.<br />");

// ----------------------------------------------------------------------------
// Settings
// ----------------------------------------------------------------------------

// The options available in the dropdown menu for number of results
// per page
$PerPageOptions = array(10, 20, 50, 100);

/*
// For foreign language support, setlocale may be required on the server for
// wildcards and highlighting to work. Uncomment the following lines and specify
// the appropriate locale information
//if (setlocale(LC_ALL, "ru_RU.cp1251") == false) // for russian
//  print("Failed to change locale setting or locale setting invalid");
*/

// Index format information
$PAGEDATA_URL = 0;
$PAGEDATA_TITLE = 1;
$PAGEDATA_DESC = 2;
$PAGEDATA_IMG = 3;

$PAGEINFO_OFFSET = 0;
$PAGEINFO_DATETIME = 1;
$PAGEINFO_FILESIZE = 2;
$PAGEINFO_CAT = 4;
$MaxPageDataLineLen = 5178;

// ----------------------------------------------------------------------------
// Parameter initialisation
// ----------------------------------------------------------------------------

// Send HTTP header to define meta charset
if (isset($Charset) && $NoCharset == 0)
    header("Content-Type: text/html; charset=" . $Charset);

// For versions of PHP before 4.1.0
// we will emulate the superglobals by creating references
// NOTE: references created are NOT superglobals
if (!isset($_SERVER) && isset($HTTP_SERVER_VARS))
    $_SERVER = &$HTTP_SERVER_VARS;
if (!isset($_GET) && isset($HTTP_GET_VARS))
    $_GET = &$HTTP_GET_VARS;
if (!isset($_POST) && isset($HTTP_POST_VARS))
    $_POST = &$HTTP_POST_VARS;

// fix get/post variables if magic quotes are enabled
if (get_magic_quotes_gpc() == 1)
{
    if (isset($_GET))
        while (list($key, $value) = each($_GET))
        {
            if (!is_array($value))
                $_GET["$key"] = stripslashes($value);
        }
    if (isset($_POST))
        while (list($key, $value) = each($_POST))
            $_POST["$key"] = stripslashes($value);
}

// check magic_quotes for runtime stuff (reading from files, etc)
if (get_magic_quotes_runtime() == 1)
    set_magic_quotes_runtime(0);

// we use the method=GET and 'query' parameter now (for sub-result pages etc)
$IsZoomQuery = 0;
if (isset($_GET['zoom_query'])) 
{
    $query = $_GET['zoom_query'];
    $IsZoomQuery = 1;
}
else
    $query = "";

// number of results per page, defaults to 10 if not specified
if (isset($_GET['zoom_per_page']))
{
    $per_page = intval($_GET['zoom_per_page']);
    if ($per_page < 1)
    	$per_page = 1;
}
else
    $per_page = 10;

// current result page number, defaults to the first page if not specified
$NewSearch = 0;
if (isset($_GET['zoom_page']))
{
    $page = intval($_GET['zoom_page']);
    if ($page < 1)
        $page = 1;
}
else
{
    $page = 1;
    $NewSearch = 1;
}

// AND operator.
// 1 if we are searching for ALL terms
// 0 if we are searching for ANY terms (default)
if (isset($_GET['zoom_and']))
    $and = intval($_GET['zoom_and']);
elseif (isset($DefaultToAnd) && $DefaultToAnd == 1)
    $and = 1;
else
    $and = 0;

// for category support
if ($UseCats == 1)
{
	if (isset($_GET['zoom_cat']))
	{
	    if (is_array($_GET['zoom_cat']))
	        $cat = $_GET['zoom_cat'];
	    else
	        $cat = array($_GET['zoom_cat']);	        	        	    		        	 
	    $cat = array_filter($cat, "is_numeric");
	}
	else
	    $cat = array(-1);  // default to search all categories
	
	$num_zoom_cats = count($cat);
	if ($num_zoom_cats == 0)
		$cat = array(-1);  // default to search all categories
}

// for sorting options
// zero is default (relevance)
// 1 is sort by date (if Date/Time is available)
if (isset($_GET['zoom_sort']))
    $sort = intval($_GET['zoom_sort']);
else
    $sort = 0;

if (isset($LinkBackURL) == false || strlen($LinkBackURL) < 1)
    $SelfURL = $_SERVER['PHP_SELF'];
else
    $SelfURL = $LinkBackURL;

// init. link target string
$target = "";
if ($UseLinkTarget == 1 && isset($LinkTarget))
    $target = " target=\"" . $LinkTarget . "\" ";

$UseMBFunctions = 0;
if ($UseUTF8 == 1)
{
    if (function_exists('mb_strtolower'))
        $UseMBFunctions = 1;
}

// ----------------------------------------------------------------------------
// Functions
// ----------------------------------------------------------------------------

function PrintEndOfTemplate($template)
{
    global $ZoomInfo;
    global $STR_POWEREDBY;
    global $template_line;

    //Let others know about Zoom.
    if ($ZoomInfo == 1)
        print("<center><p><small>" . $STR_POWEREDBY . " <a href=\"http://www.wrensoft.com/zoom/\" target=\"_blank\"><b>Zoom Search Engine</b></a></small></p></center>");

    //Print out the end of the template
    while ($template_line < count($template)) {
        print($template[$template_line]);
        $template_line++;
    }
}

function PrintHighlightDescription($line)
{
    global $Highlighting;
    global $HighlightColor;
    global $RegExpSearchWords;
    global $numwords;
    global $SearchAsSubstring;

    if ($Highlighting == 0)
    {
        print($line);
        return;
    }

    $res = $line;

    for ($i = 0; $i < $numwords; $i++)
    {
        if (strlen($RegExpSearchWords[$i]) < 1)
            continue;

        // replace with marker text, assumes [;:] and [:;] is not the search text...
        if ($SearchAsSubstring == 1)
            $res = preg_replace("/(" .$RegExpSearchWords[$i] . ")/i", "[;:]$1[:;]", $res);
        else
            $res = preg_replace("/(\W|\A|\b)(" .$RegExpSearchWords[$i] . ")(\W|\Z|\b)/i", "$1[;:]$2[:;]$3", $res);
    }
    // replace the marker text with the html text
    // this is to avoid finding previous <span>'ed text.
    $res = str_replace("[;:]", "<span class=\"highlight\">", $res);
    $res = str_replace("[:;]", "</span>", $res);
    print $res;
}

function PrintNumResults($num)
{
    global $STR_NO_RESULTS, $STR_RESULT, $STR_RESULTS;
    if ($num == 0)
        return $STR_NO_RESULTS;
    else if ($num == 1)
        return $num . " " . $STR_RESULT;
    else
        return $num . " " . $STR_RESULTS;
}

function AddParamToURL($url, $paramStr)
{
    // add GET parameters to URL depending on
    // whether there are any existing parameters
    if (strpos($url, "?") !== false)
        return $url . "&amp;" . $paramStr;
    else
        return $url . "?" . $paramStr;
}

// ----------------------------------------------------------------------------
// Compares the two values, used for sorting output results
// Results that match all search terms are put first, highest score
// ----------------------------------------------------------------------------
function SortCompare ($a, $b)
{
    if ($a[2] < $b[2])
        return 1;
    else
    if ($a[2] > $b[2])
        return -1;
    else
    {
        if ($a[1] < $b[1])
            return 1;
        else
        if ($a[1] > $b[1])
            return -1;
        else
            return 0;
    }
}

function SortByDate ($a, $b)
{
    global $datetime;
    if ($datetime[$a[0]] < $datetime[$b[0]])
        return 1;
    else
    if ($datetime[$a[0]] > $datetime[$b[0]])
        return -1;
    else
    {
        // if equal dates/time, return based on sw matched and score
        return SortCompare($a, $b);
    }
}

function sw_compare ($a, $b)
{
    if ($a[0] == '-')
        return 1;

    if ($b[0] == '-')
        return -1;

    return 0;
}


// ----------------------------------------------------------------------------
// Translates a typical shell wildcard pattern ("zoo*" => "zoom" etc.)
// to a regular expression pattern. Supports only '*' and '?' characters.
// ----------------------------------------------------------------------------
function pattern2regexp($pattern)
{
    $i = 0;
    $len = strlen($pattern);

    if (strpos($pattern, "$") !== false)
        str_replace($pattern, "$", "\$");
    if (strpos($pattern, "#") !== false)
        str_replace($pattern, "#", "\#");

    $res = "";

    while ($i < $len) {
        $c = $pattern[$i];
        if ($c == '*')
            $res = $res . "[\d\S]*";
        else
        if ($c == '?')
            $res = $res . ".";
        else
        if ($c == '.')
            $res = $res . "\.";
        else
            $res = $res . preg_quote($c, '/');
        $i++;
    }
    return $res;
}

function wordcasecmp($word1, $word2)
{
    global $UseUTF8;
    global $UseMBFunctions;

    if ($UseUTF8 == 1 && $UseMBFunctions == 1)
    {
        // string length compare for speed reasons, only use mb_strtolower when absolutely necessary
        // assumes that the lowercase variant of multibyte characters are same length as their uppercase variant
        if (strlen($word1) == strlen($word2))
        {
            if (preg_match('/^[\x80-\xff]/', $word1) || preg_match('/^[\x80-\xff]/', $word2))
                return strcmp(mb_strtolower($word1, "UTF-8"), mb_strtolower($word2, "UTF-8"));
            else
                return strcasecmp($word1, $word2);
        }
        else
            return 1;
    }
    else
        return strcasecmp($word1, $word2);
}

function GetDictID($word)
{
    global $dict;
    global $dict_count;
    for ($i = 0; $i < $dict_count; $i++) {
        if (wordcasecmp($dict[$i][0], $word) == 0)
            return $i;
    }
    return -1;  // not found
}

function GetNextDictWord($fp_pagetext)
{
    global $DictIDLen;
    $dict_id = 0;
    $bytes_buffer = fread($fp_pagetext, $DictIDLen);   
    for ($i = 0; $i < $DictIDLen; $i++)
    {
        $dict_id = $dict_id | ord($bytes_buffer[$i])<<(8*$i);
    } 
    return $dict_id;
}

function SkipSearchWord($sw)
{
    global $SearchWords;
    global $SkippedWords;
    global $SkippedOutputStr;
    if ($SearchWords[$sw] != "")
    {
        if ($SkippedWords > 0)
            $SkippedOutputStr .= ", ";
        $SkippedOutputStr .= "\"<b>" . $SearchWords[$sw] . "</b>\"";
        $SearchWords[$sw] = "";
    }
    $SkippedWords++;
}

function GetSPCode($word)
{
    $Vowels = "AEIOU";
    $FrontV = "EIY";
    $VarSound = "CSPTG";
    $Dbl = ".";

    $metalen = 4;

    $tmpword = strtoupper($word);

    $wordlen = strlen($tmpword);
    if ($wordlen < 1)
        return "";

    // if ae, gn, kn, pn, wr then drop the first letter
    $strPtr = substr($tmpword, 0, 2);
    if ($strPtr == "AE" || $strPtr == "GN" || $strPtr == "KN" || $strPtr == "PN" || $strPtr == "WR")
        $tmpword = substr($tmpword, 1);

    // change x to s
    if ($tmpword{0} == "X")
        $tmpword = "S" . substr($tmpword, 1);

    // get rid of the 'h' in "wh"
    if (substr($tmpword, 0, 2) == "WH")
        $tmpword = "W" . substr($tmpword, 2);

    // update the word length
    $wordlen = strlen($tmpword);
    $lastChar = $wordlen-1;

    // remove an 's' from the end of the string
    if ($tmpword{$lastChar} == "S")
    {
        $tmpword = substr($tmpword, 0, $wordlen-1);
        $wordlen = strlen($tmpword);
        $lastChar = $wordlen-1;
    }

    $metaph = "";
    $Continue = false;

    for ($i = 0; strlen($metaph) < $metalen && $i < $wordlen; $i++)
    {
        $char = $tmpword{$i};
        $vowelBefore = false;
        $continue = false;
        if ($i > 0)
        {
            $prevChar = $tmpword{$i-1};
            if (strpos($Vowels, $prevChar) !== FALSE)
                $vowelBefore = true;
        }
        else
        {
            $prevChar = " ";
            if (strpos($Vowels, $char) !== FALSE)
            {
                $metaph  .= $tmpword{0};
                continue;
            }
        }

        $vowelAfter = false;
        $frontvAfter = false;
        $nextChar = " ";
        if ($i < $lastChar)
        {
            $nextChar = $tmpword{$i+1};
            if (strpos($Vowels, $nextChar) !== FALSE)
                $vowelAfter = true;
            if (strpos($FrontV, $nextChar) !== FALSE)
                $frontvAfter = true;
        }

        // skip double letters except ones in list
        if ($char == $nextChar && $nextChar != $Dbl)
            continue;

        $nextChar2 = " ";
        if ($i < ($lastChar-1))
            $nextChar2 = $tmpword{$i+2};

        $nextChar3 = " ";
        if ($i < ($lastChar-2))
            $nextChar3 = $tmpword{$i+3};

        switch ($char)
        {
        case "B":
            $silent = false;
            if ($i == $lastChar && $prevChar == "M")
                $silent = true;
            if ($silent == false)
                $metaph .= $char;
            break;
        case "C":
            if (!($i > 1 && $prevChar == "S" && $frontvAfter))
            {
                if ($i > 0 && $nextChar == "I" && $nextChar2 == "A")
                    $metaph .= "X";
                elseif ($frontvAfter)
                    $metaph .= "S";
                elseif ($i > 1 && $prevChar == "S" && $nextChar == "H")
                    $metaph .= "K";
                elseif ($nextChar == "H")
                {
                    if ($i == 0 && strpos($Vowels, $nextChar2) === FALSE)
                        $metaph .= "K";
                    else
                        $metaph .= "X";
                }
                elseif ($prevChar == "C")
                    $metaph .= "C";
                else
                    $metaph .= "K";
            }
            break;
        case "D":
            if ($nextChar == "G" && strpos($FrontV, $nextChar2) !== FALSE)
                $metaph .= "J";
            else
                $metaph .= "T";
            break;
        case "G":
            $silent = false;
            if ( ($i < ($lastChar-1) && $nextChar == "H") &&
                 (strpos($Vowels, $nextChar2) == FALSE))
                 $silent = true;

            if ( ($i == ($lastChar-3)) && $nextChar == "N" && $nextChar == "E" && $nextChar == "D")
                $silent = true;
            elseif ( ($i == ($lastChar-1)) && $nextChar == "N")
                $silent = true;

            if ($prevChar == "D" && $frontvAfter)
                $silent = true;

            if ($prevChar == "G")
                $hard = true;
            else
                $hard = false;

            if (!$silent)
            {
                if ($frontvAfter && (!$hard))
                    $metaph .= "J";
                else
                    $metaph .= "K";
            }
            break;
        case "H":
            $silent = false;
            if (strpos($VarSound, $prevChar) !== FALSE)
                $silent = true;
            if ($vowelBefore && !$vowelAfter)
                $silent = true;
            if (!$silent)
                $metaph .= $char;
            break;
        case "F":
        case "J":
        case "L":
        case "M":
        case "N":
        case "R":
            $metaph .= $char;
            break;
        case "K":
            if ($prevChar != "C")
                $metaph .= $char;
            break;
        case "P":
            if ($nextChar == "H")
                $metaph .= "F";
            else
                $metaph .= "P";
            break;
        case "Q":
            $metaph .= "K";
            break;
        case "S":
            if ($i > 1 && $nextChar == "I" && ($nextChar2 == "O" || $nextChar2 == "A"))
                $metaph .= "X";
            elseif ($nextChar == "H")
                $metaph .= "X";
            else
                $metaph .= "S";
            break;
        case "T":
            if ($i > 1 && $nextChar == "I" && ($nextChar2 == "O" || $nextChar2 == "A"))
                $metaph .= "X";
            elseif ($nextChar == "H")
            {
                if ($i > 0 || (strpos($Vowels, $nextChar2) !== FALSE))
                    $metaph .= "0";
                else
                    $metaph .= "T";
            }
            elseif (!($i < ($lastChar-2) && $nextChar == "C" && $nextChar2 == "H"))
                $metaph .= "T";
            break;
        case "V":
            $metaph .= "F";
            break;
        case "W":
        case "Y":
            if ($i < $lastChar && $vowelAfter)
                $metaph .= $char;
            break;
        case "X":
            $metaph .= "KS";
            break;
        case "Z":
            $metaph .= "S";
            break;
        }
    }
    if (strlen($metaph) == 0)
        return "";
    return $metaph;
}

function GetPageData($index)
{
    global $fp_pagedata, $pgdataoffset, $MaxPageDataLineLen;
    fseek($fp_pagedata, intval($pgdataoffset[$index]));
    $pgdata = fgets($fp_pagedata, $MaxPageDataLineLen);
    return explode("|", $pgdata);
}

function QueryEntities($query)
{
    $query = str_replace("&", "&#38;", $query);
    $query = str_replace("<", "&#60;", $query);
    $query = str_replace(">", "&#62;", $query);
    return $query;
}

function uniord($u) 
{
    $k = mb_convert_encoding($u, 'UCS-2LE', 'UTF-8');
    $k1 = ord(substr($k, 0, 1));
    $k2 = ord(substr($k, 1, 1));
    return $k2 * 256 + $k1;
} 

function FixQueryForAsianWords($query)
{
	// check if the multibyte functions we need to use are available
	if (!function_exists('mb_convert_encoding') || 
		!function_exists('mb_strlen') ||
		!function_exists('mb_substr'))
		return $query;
	
	$currCharType = 0;
	$lastCharType = 0;	// 0 is normal, 1 is hiragana, 2 is katakana, 3 is "han"
	
	// check for hiragan/katakana splitting required
	$newquery = "";
	$query_len = mb_strlen($query, "UTF-8");
	for ($i = 0; $i < $query_len; $i++)
	{		
		$ch = mb_substr($query, $i, 1, "UTF-8");
		$chVal = uniord($ch);
		
        if ($chVal >= 12352 && $chVal <= 12447)
            $currCharType = 1;
        else if ($chVal >= 12448 && $chVal <= 12543)
            $currCharType = 2;
        else if ($chVal >= 13312 && $chVal <= 44031)
            $currCharType = 3;
        else
            $currCharType = 0;
                        
        if ($lastCharType != $currCharType && $ch != " ")
            $newquery .= " ";            
        $lastCharType = $currCharType;                
        $newquery .= $ch;
    }
    return $newquery;
}

// ----------------------------------------------------------------------------
// Starts here
// ----------------------------------------------------------------------------

if ($Timing == 1 || $Logging == 1) {
    $mtime = explode(" ", microtime());
    $starttime = doubleval($mtime[1]) + doubleval($mtime[0]);
}

//Open and print start of result page template
$TemplateFilename = dirname(__FILE__) . "/" . $TemplateFilename;
$template = file ($TemplateFilename);
$numtlines = count ($template); //Number of lines in the template
$template_line = 0;
while ($template_line < $numtlines)
{
    if (!stristr($template[$template_line], "<!--ZOOMSEARCH-->")) {
        print($template[$template_line]);
        $template_line++;
    }
    else {
        break;
    }
}
$template_line++;

if ($UseCats)
{
    if (file_exists($CATSFILE))
    {
        $catnames = file($CATSFILE);
        $NumCats = count($catnames);
    }
    else
    {
        print("Zoom config error: Missing file(s) zoom_cats.zdat required for category enabled search mode");
        return;
    }
}
print("<!--Zoom Search Engine ".$Version."-->\n");

// Replace the key text <!--ZOOMSEARCH--> with the following
if ($FormFormat > 0)
{
    // Insert the form
    print("<form method=\"get\" action=\"".$SelfURL."\" class=\"zoom_searchform\">\n");
    print($STR_FORM_SEARCHFOR . " <input type=\"text\" name=\"zoom_query\" size=\"20\" value=\"".htmlspecialchars($query)."\" class=\"zoom_searchbox\" />\n");
    print("<input type=\"submit\" value=\"" . $STR_FORM_SUBMIT_BUTTON . "\" class=\"zoom_button\" />\n");
    if ($FormFormat == 2) {
        print("<span class=\"zoom_results_per_page\">" . $STR_FORM_RESULTS_PER_PAGE . "\n");
        print("<select name=\"zoom_per_page\">\n");
        reset($PerPageOptions);
        foreach ($PerPageOptions as $ppo) {
            print("<option");
            if ($ppo == $per_page)
                print(" selected=\"selected\"");
            print(">". $ppo ."</option>\n");
        }
        print("</select><br /><br /></span>\n");
        if ($UseCats)
        {
            print("<span class=\"zoom_categories\">\n");
            print($STR_FORM_CATEGORY . " ");
            if ($SearchMultiCats)
            {
                print("<ul>\n");
                print("<li><input type=\"checkbox\" name=\"zoom_cat[]\" value=\"-1\"");
                if ($cat[0] == -1)
                    print(" checked=\"checked\"");
                print(">$STR_FORM_CATEGORY_ALL</input></li>\n");
                for ($i = 0; $i < $NumCats; $i++)
                {
                    print("<li><input type=\"checkbox\" name=\"zoom_cat[]\" value=\"$i\"");
                    if ($cat[0] != -1)
                    {
                        for ($catit = 0; $catit < $num_zoom_cats; $catit++)
                        {
                            if ($i == $cat[$catit])
                            {
                                print(" checked=\"checked\"");
                                break;
                            }
                        }
                    }
                    print(">$catnames[$i]</input></li>\n");
                }
                print("</ul><br /><br />\n");
            }
            else
            {
                print("<select name=\"zoom_cat[]\">");
                // 'all cats option
                print("<option value=\"-1\">" . $STR_FORM_CATEGORY_ALL . "</option>");
                for($i = 0; $i < $NumCats; $i++) {
                    print("<option value=\"". $i . "\"");
                    if ($i == $cat[0])
                        print(" selected=\"selected\"");
                    print(">". $catnames[$i] . "</option>");
                }
                print("</select>&nbsp;&nbsp;\n");
            }
            print("</span>\n");
        }
        print("<span class=\"zoom_match\">" . $STR_FORM_MATCH . " \n");
        if ($and == 0) {
            print("<input type=\"radio\" name=\"zoom_and\" value=\"0\" checked=\"checked\" />" . $STR_FORM_ANY_SEARCH_WORDS . "\n");
            print("<input type=\"radio\" name=\"zoom_and\" value=\"1\" />" . $STR_FORM_ALL_SEARCH_WORDS . "\n");
        } else {
            print("<input type=\"radio\" name=\"zoom_and\" value=\"0\" />" . $STR_FORM_ANY_SEARCH_WORDS . "\n");
            print("<input type=\"radio\" name=\"zoom_and\" value=\"1\" checked=\"checked\" />" . $STR_FORM_ALL_SEARCH_WORDS . "\n");
        }
        print("<input type=\"hidden\" name=\"zoom_sort\" value=\"" . $sort . "\" />\n");
        print("<br /><br /></span>\n");
    }
    else
    {
        print("<input type=\"hidden\" name=\"zoom_per_page\" value=\"" . $per_page . "\" />\n");
        print("<input type=\"hidden\" name=\"zoom_and\" value=\"" . $and . "\" />\n");
        print("<input type=\"hidden\" name=\"zoom_sort\" value=\"" . $sort . "\" />\n");
    }
    print("</form>\n");
}

// Give up early if no search words provided
if (empty($query))
{
    // only display 'no query' line if no form is shown
    if ($IsZoomQuery == 1)
        print($STR_NO_QUERY . "<br />");

    PrintEndOfTemplate($template);
    return;
}

// Load index data files (*.zdat) ---------------------------------------------


// Open pagetext file
if ($DisplayContext == 1 || $AllowExactPhrase == 1)
{
    $fp_pagetext = fopen($PAGETEXTFILE, "rb");
    $teststr = fgets($fp_pagetext, 8);
    if ($teststr[0] == "T" && $teststr[2] == "h" && $teststr[4] == "i" && $teststr[6] == "s")
    {
        print("<b>Zoom config error:</b> The zoom_pagetext.zdat file is not properly created for the search settings specified.<br />Please check that you have re-indexed your site with the search settings selected in the configuration window.<br />");
        fclose($fp_pagetext);
        return;
    }
}

// Open recommended link file?
if ($Recommended == 1)
{
    $fp_rec = fopen($RECOMMENDEDFILE, "rt");
    $i = 0;
    while (!feof($fp_rec))
    {
        $recline = fgets($fp_rec, $MaxKeyWordLineLen);
        if (strlen($recline) > 0)
        {
            $sep = strrpos($recline, " ");
            if ($sep !== false)
            {
                $rec[$i][0] = substr($recline, 0, $sep);
                $rec[$i][1] = substr($recline, $sep);
                $i++;
            }
        }
    }
    fclose($fp_rec);
    $rec_count = $i;
}

//Open pageinfo file
$fp_pageinfo = fopen($PAGEINFOFILE, "rb");
$i = 0;
while (!feof($fp_pageinfo) && $i < $NumPages)
{
    $pageinfoline = fgets($fp_pageinfo, $MaxKeyWordLineLen);
    if (strlen($pageinfoline) > 2)  // skip deleted lines
    {
        $pageinforow = explode("|", $pageinfoline);
        $pgdataoffset[$i] = $pageinforow[$PAGEINFO_OFFSET];
        if ($UseDateTime == 1 || $DisplayDate == 1)
            $datetime[$i] = $pageinforow[$PAGEINFO_DATETIME];
        if ($DisplayFilesize == 1)
        {
            $filesize[$i] = $pageinforow[$PAGEINFO_FILESIZE] / 1024;
            if ($filesize[$i] < 1)
                $filesize[$i] = 1;
        }
        if ($UseCats == 1)
            $catpages[$i] = $pageinforow[$PAGEINFO_CAT];
    }
    $i++;
}
if ($Recommended == 1)
    $i += $rec_count;   // take into account the recommended links before verifying
if ($i < $NumPages)
{
    print("<b>Zoom config error</b>: The zoom_pageinfo.zdat file is invalid or not up-to-date. Please make sure you have uploaded all files from the same indexing session.<br />");
    $UseDateTime = 0;
    $UseZoomImage = 0;
    $DisplayFilesize = 0;
}
fclose($fp_pageinfo);

// Open pagedata file
$fp_pagedata = fopen($PAGEDATAFILE, "rb");

// Open wordmap file
$fp_wordmap = fopen($WORDMAPFILE, "rb");

// Open dictionary file
$fp_dict = fopen($DICTIONARYFILE, "rt");
$i = 0;
while (!feof($fp_dict))
{
    $dictline = fgets($fp_dict, $MaxKeyWordLineLen);
    if (strlen($dictline) > 0)
    {
        $dict[$i] = explode(" ", $dictline, 2);
        $i++;
    }
}
fclose($fp_dict);
$dict_count = $i;


// Prepare query for search ---------------------------------------------------

if ($MapAccents == 1) {
    $query = str_replace($AccentChars, $NormalChars, $query);
}

// Special query processing required when SearchAsSubstring is enabled
if ($SearchAsSubstring == 1 && $UseUTF8 == 1)
	$query = FixQueryForAsianWords($query);


// prepare search query, strip quotes, trim whitespace
if ($AllowExactPhrase == 0)
{
    $query = str_replace("\"", " ", $query);
}
if (strspn(".", $WordJoinChars) == 0)
    $query = str_replace(".", " ", $query);

if (strspn("-", $WordJoinChars) == 0)
    $query = preg_replace("/(\S)-/", "$1 ", $query);

if (strspn("#", $WordJoinChars) == 0)
	$query = preg_replace("/#(\S)/", " $1", $query);    

if (strspn("+", $WordJoinChars) == 0)
{
	$query = preg_replace("/[\+]+([^\+\s])/", " $1", $query);
	$query = preg_replace("/([^\+\s])\+\s/", "$1 ", $query);
	$query = preg_replace("/\s\+\s/", " ", $query);
}

if (strspn("_", $WordJoinChars) == 0)
    $query = str_replace("_", " ", $query);

if (strspn("'", $WordJoinChars) == 0)
    $query = str_replace("'", " ", $query);

if (strspn("$", $WordJoinChars) == 0)
    $query = str_replace("$", " ", $query);

if (strspn(",", $WordJoinChars) == 0)
    $query = str_replace(",", " ", $query);

if (strspn(":", $WordJoinChars) == 0)
    $query = str_replace(":", " ", $query);

if (strspn("&", $WordJoinChars) == 0)
    $query = str_replace("&", " ", $query);

if (strspn("/", $WordJoinChars) == 0)
    $query = str_replace("/", " ", $query);

if (strspn("\\", $WordJoinChars) == 0)
    $query = str_replace("\\", " ", $query);

// strip consecutive spaces, parenthesis, etc.
// also strip any of the wordjoinchars if followed immediately by a space
$query = preg_replace("/[\s\(\)\^\[\]\|\{\}\%]+|[\-._',:&\/\\\](\s|$)/", " ", $query);
$query = trim($query);

$queryForHTML = htmlspecialchars($query);
if ($ToLowerSearchWords == 1)
{
    if ($UseUTF8 == 1 && $UseMBFunctions == 1)
        $queryForSearch = mb_strtolower($query, "UTF-8");
    else
        $queryForSearch = strtolower($query);
}
else
	$queryForSearch = $query;

//Split search phrase into words
preg_match_all("/\"(.*?)\"|\-\"(.*?)\"|[^\\s\"]+/", $queryForSearch, $SearchWords);
$SearchWords = preg_replace("/\"[\s]+|[\s]+\"|\"/", "", $SearchWords[0]);

//Sort search words if there are negative signs
if (strpos($queryForSearch, "-") !== false)
    usort($SearchWords, "sw_compare");

$query_zoom_cats = "";

//Print heading
print("<div class=\"searchheading\">" . $STR_RESULTS_FOR . " " . $queryForHTML);
if ($UseCats)
{
    if ($cat[0] == -1)
    {
        print(" " . $STR_RESULTS_IN_ALL_CATEGORIES);
        $query_zoom_cats = "&amp;zoom_cat%5B%5D=-1";
    }
    else
    {
        print(" " . $STR_RESULTS_IN_CATEGORY . " ");
        for ($catit = 0; $catit < $num_zoom_cats; $catit++)
        {        	
            if ($catit > 0)
                print(", ");
            print("\"". rtrim($catnames[$cat[$catit]]) . "\"");
            $query_zoom_cats .= "&amp;zoom_cat%5B%5D=".$cat[$catit];
        }
    }
}
print "<br /><br /></div>\n";

print "<div class=\"results\">\n";

// Begin main search loop -----------------------------------------------------

$numwords = count ($SearchWords);
//$pagesCount = count($urls);
$pagesCount = $NumPages;
$outputline = 0;


// Initialise $res_table to be a 2D array of count($pages) long, filled with zeros.
//$res_table = array_fill(0, $pagesCount, array_fill(0, 6, 0));
$res_table = array();
for ($i = 0; $i < $pagesCount; $i++)
{
    $res_table[$i] = array();
    $res_table[$i][0] = 0;  // score
    $res_table[$i][1] = 0;  // num of sw matched
    $res_table[$i][2] = 0;  // pagetext ptr #1
    $res_table[$i][3] = 0;  // pagetext ptr #2
    $res_table[$i][4] = 0;  // pagetext ptr #3
    $res_table[$i][5] = 0;  // 'and' user search terms matched
}

$exclude_count = 0;

// check if word is in skipword file
$SkippedWords = 0;
$context_maxgoback = 1;
$SkippedExactPhrase = 0;

$maxscore = 0;

//$sw_results = array_fill(0, $numwords, 0);

// Prepopulate some data for each searchword
$sw_results = array();
for ($sw = 0; $sw < $numwords; $sw++)
{
    $sw_results[$sw] = 0;
    $UseWildCards[$sw] = 0;

    if (strpos($SearchWords[$sw], "*") !== false || strpos($SearchWords[$sw], "?") !== false)
    {
        $RegExpSearchWords[$sw] = pattern2regexp($SearchWords[$sw]);
        $UseWildCards[$sw] = 1;
    }

    if ($Highlighting == 1 && $UseWildCards[$sw] == 0)
    {
        $RegExpSearchWords[$sw] = $SearchWords[$sw];
        if (strpos($RegExpSearchWords[$sw], "\\") !== false)
            $RegExpSearchWords[$sw] = str_replace("\\", "\\\\", $RegExpSearchWords[$sw]);
        if (strpos($RegExpSearchWords[$sw], "/") !== false)
            $RegExpSearchWords[$sw] = str_replace("/", "\/", $RegExpSearchWords[$sw]);
        if (strpos($RegExpSearchWords[$sw], "+") !== false)
            $RegExpSearchWords[$sw] = str_replace("+", "\+", $RegExpSearchWords[$sw]);
    }
}

for ($sw = 0; $sw < $numwords; $sw++)
{
    if ($SearchWords[$sw] == "")
        continue;

    // check min length
    if (strlen($SearchWords[$sw]) < $MinWordLen)
    {
        SkipSearchWord($sw);
        continue;
    }

    $ExactPhrase = 0;
    $ExcludeTerm = 0;

    // Check exclusion searches
    if ($SearchWords[$sw][0] == "-")
    {
        $SearchWords[$sw] = substr($SearchWords[$sw], 1);
        $ExcludeTerm = 1;
        $exclude_count++;
    }

    if ($AllowExactPhrase == 1 && strpos($SearchWords[$sw], " ") !== false)
    {
        // Initialise exact phrase matching for this search term
        $ExactPhrase = 1;
        $phrase_terms = split(" ", $SearchWords[$sw]);
        //$phrase_terms = preg_split("/\W+/", $SearchWords[$sw], -1, 0 /*PREG_SPLIT_DELIM_CAPTURE*/);
        $num_phrase_terms = count($phrase_terms);
        if ($num_phrase_terms > $context_maxgoback)
            $context_maxgoback = $num_phrase_terms;

        $phrase_terms_data = array();
        $tmpid = 0;
        $WordNotFound = 0;
        for ($j = 0; $j < $num_phrase_terms; $j++)
        {
            $tmpid = GetDictID($phrase_terms[$j]);
            if ($tmpid == -1)   // word is not in dictionary
            {
                $WordNotFound = 1;
                break;
            }

            $wordmap_row = $dict[$tmpid][1];
            if ($wordmap_row != -1)
            {
                fseek($fp_wordmap, $wordmap_row);
                $countbytes = fread($fp_wordmap, 2);
                $phrase_data_count[$j] = ord($countbytes[0]) | ord($countbytes[1])<<8;
                for ($xbi = 0; $xbi < $phrase_data_count[$j]; $xbi++) {
                    $xbindata = fread($fp_wordmap, 8);
                    if (strlen($xbindata) == 0)
                        print "error in wordmap file: expected data not found";
                    $phrase_terms_data[$j][$xbi] = unpack("vscore/vpagenum/Vptr", $xbindata);
                }
            }
            else
            {
                $phrase_data_count[$j] = 0;
                $phrase_terms_data[$j] = 0;
            }
        }
        if ($WordNotFound == 1)
            continue;
    }
    else if ($UseWildCards[$sw])
    {
        $pattern = "/";

        // match entire word
        if ($SearchAsSubstring == 0)
            $pattern = $pattern . "\A";

        $pattern = $pattern . $RegExpSearchWords[$sw];

        if ($SearchAsSubstring == 0)
            $pattern = $pattern . "\Z";

        if ($ToLowerSearchWords != 0)
            $pattern = $pattern . "/i";
        else
            $pattern = $pattern . "/";
    }

    for ($i = 0; $i < $dict_count; $i++)
    {
        $dictline = $dict[$i];
        $word = $dict[$i][0];

        // if we're not using wildcards, direct match
        if ($ExactPhrase == 1)
        {
            // todo: move to next phrase term if first phrase term is skipped?
            // compare first term in exact phrase
            $result = wordcasecmp($phrase_terms[0], $word);
        }
        else if ($UseWildCards[$sw] == 0)
        {
            if ($SearchAsSubstring == 0)
                $result = wordcasecmp($SearchWords[$sw], $word);
            else
            {
                if (stristr($word, $SearchWords[$sw]) == FALSE)
                    $result = 1;    // not matched
                else
                    $result = 0;    // matched
            }
        }
        else
        {
            // if we have wildcards...
            $result = !(preg_match($pattern, $word));
        }
        // result = 0 if matched, result != 0 if not matched.

        // word found but indicated to be not indexed or skipped
        if ($result == 0 && $dictline[1] == -1)
        {
            if ($UseWildCards[$sw] == 0 && $SearchAsSubstring == 0)
            {
                if ($ExactPhrase == 1)
                    $SkippedExactPhrase = 1;

                SkipSearchWord($sw);
                break;
            }
            else
                continue;
        }

        if ($result == 0)
        {
            // keyword found in the dictionary

            if ($ExactPhrase == 1)
            {
                // we'll use the wordmap data for the first term that we have worked out earlier
                $data = $phrase_terms_data[0];
                $data_count = $phrase_data_count[0];
                $ContextSeeks = 0;
            }
            else
            {
                // seek to position in wordmap file
                fseek($fp_wordmap, $dictline[1]);
                //print "seeking in wordmap: " . $dictline[1] . "<br />";

                // first 2 bytes is data count
                $countbytes = fread($fp_wordmap, 2);
                $data_count = ord($countbytes[0]) | ord($countbytes[1])<<8;
                //print "data count: " . $data_count . "<br />";

                for ($bi = 0; $bi < $data_count; $bi++)
                {
                    $bindata = fread($fp_wordmap, 8);
                    if (strlen($bindata) == 0)
                        print "Error in wordmap file: expected data not found";
                    $data[$bi] = unpack("vscore/vpagenum/Vptr", $bindata);
                }
            }
            $sw_results[$sw] += $data_count;

            // Go through wordmap for each page this word appears on
            for ($j = 0; $j < $data_count; $j++)
            {
                $score = $data[$j]["score"];
                $txtptr = $data[$j]["ptr"];

                if ($score == 0)
                    continue;

                if ($ExactPhrase == 1)
                {
                    $maxptr = $data[$j]["ptr"];
                    $maxptr_term = 0;
                    $GotoNextPage = 0;

                    // Check if all of the other words in the phrase appears on this page.
                    for ($xi = 1; $xi < $num_phrase_terms; $xi++)
                    {
                        // see if this word appears at all on this page, if not, we stop scanning page.
                        // do not check for skipped words (data count value of zero)
                        if ($phrase_data_count[$xi] != 0)
                        {
                            // check wordmap for this search phrase to see if it appears on the current page.
                            for ($xbi = 0; $xbi < $phrase_data_count[$xi]; $xbi++) {
                                if ($phrase_terms_data[$xi][$xbi]["pagenum"] == $data[$j]["pagenum"])
                                {
                                    // intersection, this term appears on both pages, goto next term
                                    // remember biggest pointer.
                                    if ($phrase_terms_data[$xi][$xbi]["ptr"] > $maxptr)
                                    {
                                        $maxptr = $phrase_terms_data[$xi][$xbi]["ptr"];
                                        $maxptr_term = $xi;
                                    }
                                    $score += $phrase_terms_data[$xi][$xbi]["score"];
                                    break;
                                }
                            }
                            if ($xbi == $phrase_data_count[$xi]) // if not found
                            {
                                $GotoNextPage = 1;
                                break;  // goto next page
                            }
                        }
                    }   // end phrase term for loop
                    if ($GotoNextPage == 1)
                        continue;

                    // Check how many context seeks we have made.
                    $ContextSeeks++;
                    if ($ContextSeeks > $MaxContextSeeks)
                    {
                        print "<small>" . $STR_PHRASE_CONTAINS_COMMON_WORDS . " <b>\"" . $SearchWords[$sw] . "\"</b></small><br /><br />";
                        break;
                    }

                    // ok, so this page contains all of the words in the phrase
                    $FoundPhrase = 0;
                    $FoundFirstWord = 0;

                    // we goto the first occurance of the first word in pagetext
                    $pos = $maxptr - (($maxptr_term+3) * $DictIDLen);    // assume 3 possible punctuations.
                    // do not seek further back than the occurance of the first word (avoid wrong page)
                    if ($pos < $data[$j]["ptr"])
                        $pos = $data[$j]["ptr"];

                    fseek($fp_pagetext, $pos);

                    // now we look for the phrase within the context of this page
                    do
                    {
                        for ($xi = 0; $xi < $num_phrase_terms; $xi++)
                        {
                            // do...while loop to ignore punctuation marks in context phrase
                            do
                            {                                
                                // Inlined (and unlooped) the following function for speed reasons
                                //$xword_id = GetNextDictWord($fp_pagetext);
                                $bytes_buffer = fread($fp_pagetext, $DictIDLen);
                                $xword_id = ord($bytes_buffer[0]);
                                $xword_id = $xword_id | ord($bytes_buffer[1]) << 8;
                                if ($DictIDLen == 3)
                                	$xword_id = $xword_id | ord($bytes_buffer[2]) << (8*2);
                                $pos += $DictIDLen;
                                // check if we are at the end of page (wordid = 0) or invalid $xword_id
                                if ($xword_id == 0 || $xword_id >= $dict_count)
                                    break;
                            } while ($xword_id <= $DictReservedLimit && !feof($fp_pagetext));

                            if ($xword_id == 0 || $xword_id >= $dict_count)
                                break;
                                
                            $xdictword = $dict[$xword_id][0];
                            if ($MapAccents == 1)
                            	$xdictword = str_replace($AccentChars, $NormalChars, $xdictword);

                            // inlined if condition to avoid function call overhead with wordcasecmp()
                            if ($UseMBFunctions == 1)
                                $cmpRet = wordcasecmp($xdictword, $phrase_terms[$xi]);
                            else
                                $cmpRet = strcasecmp($xdictword, $phrase_terms[$xi]);

                            // if the words are NOT the same, we break out
                            if ($cmpRet != 0)
                            {
                                // also check against first word
                                if ($xi != 0 && wordcasecmp($xdictword, $phrase_terms[0]) == 0)
                                    $xi = 0;    // matched first word
                                else
                                    break;
                            }

                            // remember how many times we find the first word on this page
                            if ($xi == 0)
                            {
                                $FoundFirstWord++;
                                // remember the position of the 'start' of this phrase
                                $txtptr = $pos - $DictIDLen;
                            }
                        }
                        if ($xi == $num_phrase_terms)
                        {
                            // exact phrase found!
                            $FoundPhrase = 1;
                        }
                    } while ($xword_id != 0 && $FoundPhrase == 0 &&
                            $FoundFirstWord <= $data[$j]["score"]);

                    if ($FoundPhrase != 1)
                        continue;   // goto next page.
                }

                //Check if page is already in output list
                $pageexists = 0;
                $ipage = $data[$j]["pagenum"];

                if ($ExcludeTerm == 1)
                {
                    // we clear out the score entry so that it'll be excluded in the filtering stage
                    $res_table[$ipage][0] = 0;
                }
                elseif ($res_table[$ipage][0] == 0)
                {
                    // not in list, count this page as a unique match
                    $res_table[$ipage][0] += $score;
                    $res_table[$ipage][2] = $txtptr;
                }
                else
                {
                    // already in list
                    if ($res_table[$ipage][0] > 10000)
                    {
                        // take it easy if its too big (to prevent huge scores)
                        $res_table[$ipage][0] += 1;
                    }
                    else
                    {
                        $res_table[$ipage][0] += $score;    //Add in score
                        $res_table[$ipage][0] *= 2;         //Double Score as we have two words matching
                    }

                    // store the next two searchword matches
                    if ($res_table[$ipage][1] > 0 && $res_table[$ipage][1] < $MaxContextKeywords)
                    {
                        if ($res_table[$ipage][3] == 0)
                            $res_table[$ipage][3] = $txtptr;
                        elseif ($res_table[$ipage][4] == 0)
                            $res_table[$ipage][4] = $txtptr;
                    }
                }
                $res_table[$ipage][1] += 1;

                if ($res_table[$ipage][0] > $maxscore)
                    $maxscore = $res_table[$ipage][0];

                // store the 'and' user search terms matched' value
                if ($res_table[$ipage][5] == $sw || $res_table[$ipage][5] == $sw-$SkippedWords-$exclude_count)
                    $res_table[$ipage][5] += 1;
            }

            if ($UseWildCards[$sw] == 0 && $SearchAsSubstring == 0)
                break;  //This search word was found, so skip to next
        }
    }
}
//Close the files
fclose($fp_wordmap);

if ($SkippedWords > 0)
{
    print "<div class=\"summary\">" . $STR_SKIPPED_FOLLOWING_WORDS . " " . $SkippedOutputStr . "<br />\n";
    if ($SkippedExactPhrase == 1)
        print $STR_SKIPPED_PHRASE . ".<br />\n";
    print "<br /></div>\n";
}

//Count number of output lines that match ALL search terms
$oline = 0;
$fullmatches = 0;
$matches = 0;

// Second pass, results filtering.
$full_numwords = $numwords - $SkippedWords - $exclude_count;
for ($i = 0; $i < $pagesCount; $i++)
{
    $IsFiltered = false;
    if ($res_table[$i][0] > 0)
    {
        if ($UseCats && $cat[0] != -1)
        {
            // Using cats and not doing an "all cats" search
            if ($SearchMultiCats)
            {
                for ($cati = 0; $cati < $num_zoom_cats; $cati++)
                {
                    if ($catpages[$i][$cat[$cati]] == "1")
                        break;
                }
                if ($cati == $num_zoom_cats)
                    $IsFiltered = true;
            }
            else
            {
                if ($catpages[$i][$cat[0]] == "0")
                    $IsFiltered = true;
            }
        }
        if ($IsFiltered == false)
        {
            //if ($res_table[$i][1] >= $full_numwords)
            if ($res_table[$i][5] >= $full_numwords)
                $fullmatches++;
            else
            {
                // if AND search, only copy AND results
                if ($and == 1)
                    $IsFiltered = true;
            }
        }
        if ($IsFiltered == false)
        {
            // copy if not filtered out
            $output[$oline][0] = $i;                    // page index
            $output[$oline][1] = $res_table[$i][0];     // score
            $output[$oline][2] = $res_table[$i][1];     // num of sw matched
            $output[$oline][3] = $res_table[$i][2];     // pagetext ptr #1
            $output[$oline][4] = $res_table[$i][3];     // pagetext ptr #2
            $output[$oline][5] = $res_table[$i][4];     // pagetext ptr #3
            $oline++;
        }
    }
}
$matches = $oline;

//Sort results in order of score, use the "SortCompare" function
if ($matches > 1)
{
    if ($sort == 1 && $UseDateTime == 1 && isset($datetime))
    {
        usort($output, "SortByDate");
    }
    else
    {
        // Default sort by relevance
        usort($output, "SortCompare");
    }
}

// queryForURL is the query prepared to be passed in a URL.
$queryForURL = urlencode($query);

//Display search result information
print("<div class=\"summary\">\n");
if ($matches == 0)
    print $STR_SUMMARY_NO_RESULTS_FOUND;
elseif ($numwords > 1 && $and == 0)
{
    //OR
    $SomeTermMatches = $matches - $fullmatches;
    print PrintNumResults($fullmatches) . " " . $STR_SUMMARY_FOUND_CONTAINING_ALL_TERMS . " ";
    if ($SomeTermMatches > 0)
        print PrintNumResults($SomeTermMatches) . " " . $STR_SUMMARY_FOUND_CONTAINING_SOME_TERMS;
}
elseif ($numwords > 1 && $and == 1) //AND
    print PrintNumResults($fullmatches) . " " . $STR_SUMMARY_FOUND_CONTAINING_ALL_TERMS;
else
    print PrintNumResults($matches) . " " . $STR_SUMMARY_FOUND;

print "<br />\n</div>\n";

if ($matches < 3)
{
    if ($and == 1 && $numwords > 1)
        print "<div class=\"suggestion\"><br />" . $STR_POSSIBLY_GET_MORE_RESULTS . " <a href=\"".$SelfURL."?zoom_query=".$queryForURL."&amp;zoom_page=".$page."&amp;zoom_per_page=".$per_page.$query_zoom_cats."&amp;zoom_and=0&amp;zoom_sort=".$sort."\">". $STR_ANY_OF_TERMS . "</a>.</div>";
    else if ($UseCats && $cat[0] != -1)
        print "<div class=\"suggestion\"><br />" . $STR_POSSIBLY_GET_MORE_RESULTS . " <a href=\"".$SelfURL."?zoom_query=".$queryForURL."&amp;zoom_page=".$page."&amp;zoom_per_page=".$per_page."&amp;zoom_cat=-1&amp;zoom_and=".$and."&amp;zoom_sort=".$sort."\">" . $STR_ALL_CATS . "</a>.</div>";
}


if ($Spelling == 1)
{
    // load in spellings file
    $fp_spell = fopen($SPELLINGFILE, "rt");
    $i = 0;
    while (!feof($fp_spell))
    {
        $spline = fgets($fp_spell, $MaxKeyWordLineLen);
        if (strlen($spline) > 0)
        {
            $spell[$i] = explode(" ", $spline, 4);
            $i++;
        }
    }
    fclose($fp_spell);
    $spell_count = $i;

    $SuggestStr = "";
    $SuggestionFound = 0;
    $SuggestionCount = 0;

    $word = "";
    $word2 = "";
    $word3 = "";

    for ($sw = 0; $sw < $numwords; $sw++)
    {
        if ($sw_results[$sw] >= $SpellingWhenLessThan)
        {
            // this word has enough results
            if ($sw > 0)
                $SuggestStr = $SuggestStr . " ";
            $SuggestStr = $SuggestStr . $SearchWords[$sw];
        }
        else
        {
            // this word returned less results than threshold, and requires spelling suggestions
            $sw_spcode = GetSPCode($SearchWords[$sw]);
            //$sw_spcode = metaphone($SearchWords[$sw],4);

            if (strlen($sw_spcode) > 0)
            {
                $SuggestionFound = 0;
                for ($i = 0; $i < $spell_count && $SuggestionFound == 0; $i++)
                {
                    $spcode = $spell[$i][0];

                    if ($spcode == $sw_spcode)
                    {
                        $j = 0;
                        while ($SuggestionFound == 0 && $j < 3 && isset($spell[$i][1+$j]))
                        {
                            $dictid = intval($spell[$i][1+$j]);
                            $word = $dict[$dictid][0];

                            if (wordcasecmp($word, $SearchWords[$sw]) == 0)
                            {
                                // Check that it is not a skipped word or the same word
                                $SuggestionFound = 0;
                            }
                            else
                            {
                                $SuggestionFound = 1;
                                $SuggestionCount++;
                                if ($numwords == 1) // if single word search
                                {
                                    if ($j < 1 && isset($spell[$i][1+$j+1]))
                                    {
                                        $dictid = intval($spell[$i][1+$j+1]);
                                        $word2 = $dict[$dictid][0];
                                        if (wordcasecmp($word2, $SearchWords[$sw]) == 0)
                                            $word2 = "";
                                    }
                                    if ($j < 2 && isset($spell[$i][1+$j+2]))
                                    {
                                        $dictid = intval($spell[$i][1+$j+2]);
                                        $word3 = $dict[$dictid][0];
                                        if (wordcasecmp($word3, $SearchWords[$sw]) == 0)
                                            $word3 = "";
                                    }
                                }
                            }
                            $j++;
                        }
                    }
                    elseif (strcmp($spcode, $sw_spcode) > 0)
                    {
                        break;
                    }
                }

                if ($SuggestionFound == 1)
                {
                    if ($sw > 0)
                        $SuggestStr = $SuggestStr . " ";
                    $SuggestStr = $SuggestStr . $word;  // add string AFTER so we can preserve order of words
                }
            }
        }
    }
    if ($SuggestionCount > 0)
    {
        print "<div class=\"suggestion\"><br />" . $STR_DIDYOUMEAN . " <a href=\"".$SelfURL."?zoom_query=".urlencode($SuggestStr)."&amp;zoom_per_page=".$per_page.$query_zoom_cats."&amp;zoom_and=0&amp;zoom_sort=".$sort."\">". $SuggestStr . "</a>";
        if (strlen($word2) > 0)
            print " $STR_OR <a href=\"".$SelfURL."?zoom_query=".urlencode($word2)."&amp;zoom_per_page=".$per_page.$query_zoom_cats."&amp;zoom_and=".$and."&amp;zoom_sort=".$sort."\">". $word2 . "</a>";
        if (strlen($word3) > 0)
            print " $STR_OR <a href=\"".$SelfURL."?zoom_query=".urlencode($word3)."&amp;zoom_per_page=".$per_page.$query_zoom_cats."&amp;zoom_and=".$and."&amp;zoom_sort=".$sort."\">". $word3 . "</a>";
        print "?</div>";
    }
}

// Number of pages of results
$num_pages = ceil($matches / $per_page);
if ($num_pages > 1)
    print "<div class=\"result_pagescount\"><br />" . $num_pages . " " . $STR_PAGES_OF_RESULTS . "</div>\n";

// Show recommended links if any
$num_recs_found = 0;
if ($Recommended == 1)
{
    for ($rl = 0; $rl < $rec_count && $num_recs_found < $RecommendedMax; $rl++)
    {
        $rec_word = $rec[$rl][0];
        $rec_idx = intval($rec[$rl][1]);
        for ($sw = 0; $sw <= $numwords; $sw++)
        {
            // if finished with last search word, check the full search query
            $result = 1;
            if ($sw == $numwords)
                $result = wordcasecmp($queryForSearch, $rec_word);
            else if (strlen($SearchWords[$sw]) > 0)
            {            	
                if ($UseWildCards[$sw] == 1)
                {
                    $pattern = "/";

                    // match entire word
                    if ($SearchAsSubstring == 0)
                        $pattern = $pattern . "\A";

                    $pattern = $pattern . $RegExpSearchWords[$sw];

                    if ($SearchAsSubstring == 0)
                        $pattern = $pattern . "\Z";

                    if ($ToLowerSearchWords != 0)
                        $pattern = $pattern . "/i";
                    else
                        $pattern = $pattern . "/";

                    $result = !(preg_match($pattern, $rec_word));
                }
                else if ($SearchAsSubstring == 0)
                {
                    $result = wordcasecmp($SearchWords[$sw], $rec_word);
                }
                else
                {
                    if (stristr($rec_word, $SearchWords[$sw]) == FALSE)
                        $result = 1;    // not matched
                    else
                        $result = 0;    // matched
                }
            }

            if ($result == 0)
            {
                if ($num_recs_found == 0)
                {
                    print("<div class=\"recommended\">\n");
                    print("<div class=\"recommended_heading\">$STR_RECOMMENDED</div>\n");
                }
                $pgdata = GetPageData($rec_idx);
                $url = $pgdata[$PAGEDATA_URL];
                $title = $pgdata[$PAGEDATA_TITLE];
                $description = $pgdata[$PAGEDATA_DESC];

                $urlLink = $url;
                //$urlLink = rtrim($urls[$rec_idx]);

                if ($GotoHighlight == 1)
                {
                    if ($SearchAsSubstring == 1)
                        $urlLink = AddParamToURL($urlLink, "zoom_highlightsub=".$queryForURL);
                    else
                        $urlLink = AddParamToURL($urlLink, "zoom_highlight=".$queryForURL);
                }
                if ($PdfHighlight == 1)
                {
                    if (stristr($urlLink, ".pdf") != FALSE)
                        $urlLink = $urlLink."#search=&quot;".str_replace("\"", "", $query)."&quot;";
                }
                print("<div class=\"recommend_block\">\n");
                print("<div class=\"recommend_title\">");
                print("<a href=\"".$urlLink."\"" . $target . ">");
                if (strlen($title) > 1)
                    PrintHighlightDescription($title);
                else
                    PrintHighlightDescription($pgdata[$PAGEDATA_URL]);
                print("</a></div>\n");
                print("<div class=\"recommend_description\">");
                PrintHighlightDescription($description);
                print("</div>\n");
                print("<div class=\"recommend_infoline\">$url</div>\n");
                print("</div>");
                $num_recs_found++;
                break;
            }
        }
    }
    if ($num_recs_found > 0)
        printf("</div>");
}

// Show sorting options
if ($matches > 1)
{
    if ($UseDateTime == 1)
    {
        print("<div class=\"sorting\">");
        if ($sort == 1)
            print("<a href=\"".$SelfURL."?zoom_query=".$queryForURL."&amp;zoom_page=".$page."&amp;zoom_per_page=".$per_page.$query_zoom_cats."&amp;zoom_and=".$and."&amp;zoom_sort=0\">". $STR_SORTBY_RELEVANCE . "</a> / <b>". $STR_SORTEDBY_DATE . "</b>");
        else
            print("<b>". $STR_SORTEDBY_RELEVANCE . "</b> / <a href=\"".$SelfURL."?zoom_query=".$queryForURL."&amp;zoom_page=".$page."&amp;zoom_per_page=".$per_page.$query_zoom_cats."&amp;zoom_and=".$and."&amp;zoom_sort=1\">". $STR_SORTBY_DATE . "</a>");
        print("</div>");
    }
}

// Determine current line of result from the $output array
if ($page == 1) {
    $arrayline = 0;
} else {
    $arrayline = (($page - 1) * $per_page);
}

// The last result to show on this page
$result_limit = $arrayline + $per_page;

// Display the results
while ($arrayline < $matches && $arrayline < $result_limit)
{
    $ipage = $output[$arrayline][0];
    $score = $output[$arrayline][1];

    $pgdata = GetPageData($ipage);
    $url = $pgdata[$PAGEDATA_URL];
    $title = $pgdata[$PAGEDATA_TITLE];
    $description = $pgdata[$PAGEDATA_DESC];

    $urlLink = $url;

    //$urlLink = rtrim($urls[$ipage]);
    if ($GotoHighlight == 1)
    {
        if ($SearchAsSubstring == 1)
            $urlLink = AddParamToURL($urlLink, "zoom_highlightsub=".$queryForURL);
        else
            $urlLink = AddParamToURL($urlLink, "zoom_highlight=".$queryForURL);
    }
    if ($PdfHighlight == 1)
    {
        if (stristr($urlLink, ".pdf") != FALSE)        	
            $urlLink = $urlLink."#search=&quot;".str_replace("\"", "", $query)."&quot;";
    }

    if ($arrayline % 2 == 0)
        print("<div class=\"result_block\">");
    else
        print("<div class=\"result_altblock\">");

    if ($UseZoomImage)
    {
        if (isset($pgdata[$PAGEDATA_IMG]))
            $image = $pgdata[$PAGEDATA_IMG];
        else
            $image = "";
        if (strlen($image) > 0)
        {
            print("<div class=\"result_image\">");
            print("<a href=\"".$urlLink."\"" . $target . "><img src=\"$image\" alt=\"\" class=\"result_image\"></a>");
            print("</div>");
        }
    }

    print "<div class=\"result_title\">";
    if ($DisplayNumber == 1)
        print "<b>".($arrayline+1).".</b>&nbsp;";

    if ($DisplayTitle == 1)
    {
        print "<a href=\"".$urlLink."\"" . $target . ">";
        PrintHighlightDescription(rtrim($title));
        print "</a>";
    }
    else
        print "<a href=\"".$urlLink."\"" . $target . ">".rtrim($url)."</a>";

    if ($UseCats)
    {
        print " <span class=\"category\">";
        for ($catit = 0; $catit < $NumCats; $catit++)
        {
            if ($catpages[$ipage][$catit] == "1")
                print(" [".trim($catnames[$catit])."]");
        }
        print "</span>";
    }
    print "</div>\n";

    if ($DisplayMetaDesc == 1)
    {
        // Print meta description
        if (strlen($description) > 2) {
            print("<div class=\"description\">");
            PrintHighlightDescription(rtrim($description));
            print "</div>\n";
        }
    }

    if ($DisplayContext == 1)
    {
        // Extract contextual page content
        $context_keywords = $output[$arrayline][2]; // # of terms matched
        if ($context_keywords > $MaxContextKeywords)
            $context_keywords = $MaxContextKeywords;

        $context_word_count = ceil($ContextSize / $context_keywords);

        $goback = floor($context_word_count / 2);
        $gobackbytes = $goback * $DictIDLen;

        $last_startpos = 0;
        $last_endpos = 0;

        $FoundContext = 0;

        print "<div class=\"context\">\n";
        for ($j = 0; $j < $context_keywords && ($j == 0 || !feof($fp_pagetext)); $j++)
        {
            $origpos = $output[$arrayline][3 + $j];
            $startpos = $origpos;

            if ($gobackbytes < $startpos)
            {
                $startpos = $startpos - $gobackbytes;
                $noGoBack = false;
            }
            else
                $noGoBack = true;

            // Check that this will not overlap with previous extract
            if ($startpos > $last_startpos && $startpos < $last_endpos)
                $startpos = $last_endpos;   // we will just continue last extract if so.

            // find the pagetext pointed to
            fseek($fp_pagetext, $startpos);

            // remember the last start position
            $last_startpos = $startpos;

            $word_id = GetNextDictWord($fp_pagetext);
            $prev_word_id = 0;

            $context_str = "";

            $noSpaceForNextChar = false;

            for ($i = 0; $i < $context_word_count && !feof($fp_pagetext); $i++)
            {
                if ($noSpaceForNextChar == false)
                {
                    // No space for reserved words (punctuation, etc)
                    if ($word_id > $DictReservedNoSpaces)
                    {
                        if ($prev_word_id <= $DictReservedPrefixes || $prev_word_id > $DictReservedNoSpaces)
                            $context_str .= " ";
                    }
                    elseif  ($word_id > $DictReservedSuffixes && $word_id <= $DictReservedPrefixes)
                    {
                        // This is a Prefix character
                        $context_str .= " ";
                        $noSpaceForNextChar = true;
                    }
                    elseif ($word_id > $DictReservedPrefixes)   // this is a nospace character
                        $noSpaceForNextChar = true;
                }
                else
                    $noSpaceForNextChar = false;

                if ($word_id == 0 || $word_id == 1 || $word_id >= $dict_count)    // check if end of page or section
                {
                    // if end of page occurs AFTER word pointer (ie: reached next page)
                    if ($noGoBack || ftell($fp_pagetext) > $origpos)
                        break;          // then we stop.
                    else                // if end of page occurs BEFORE word pointer (ie: reached previous page)
                    {
                        $context_str = "";// then we clear the existing context buffer we've created.
                        $i = 0;
                    }
                }
                else
                {                    
                	if ($word_id <= $DictReservedLimit)
                    	$context_str .= $dict[$word_id][0];
                    else
                    	$context_str .= htmlspecialchars($dict[$word_id][0]);                    
                }

                $prev_word_id = $word_id;
                $word_id = GetNextDictWord($fp_pagetext);
            }

            // remember the last end position
            $last_endpos = ftell($fp_pagetext);

            if (strcmp(trim($context_str), trim($title)) == 0)
            {
                $context_str = ""; // clear the string if its identical to the title
            }

            if ($context_str != "")
            {
                print " <b>...</b> ";
                $FoundContext = 1;
                //$context_str = htmlspecialchars($context_str);
                PrintHighlightDescription($context_str);
            }
        }
        if ($FoundContext == 1)
            print " <b>...</b>";
        print "</div>\n";
    }

    $info_str = "";

    if ($DisplayTerms == 1)
    {
        $info_str .= $STR_RESULT_TERMS_MATCHED . " ". $output[$arrayline][2];
    }

    if ($DisplayScore == 1)
    {
        if (strlen($info_str) > 0)
            $info_str .= "&nbsp; - &nbsp;";
        $info_str .= $STR_RESULT_SCORE . " " . $score;
    }

    if ($DisplayDate == 1 && $datetime[$ipage] > 0)
    {
        if (strlen($info_str) > 0)
            $info_str .= "&nbsp; - &nbsp;";
        $info_str .= date("j M Y", $datetime[$ipage]);
    }

    if ($DisplayFilesize == 1)
    {
        if (strlen($info_str) > 0)
            $info_str .= "&nbsp; - &nbsp;";
        $info_str .= number_format($filesize[$ipage]) . "k";
    }

    if ($DisplayURL == 1)
    {
        if (strlen($info_str) > 0)
            $info_str .= "&nbsp; - &nbsp;";
        $info_str .= $STR_RESULT_URL . " ".rtrim($url);
    }

    print "<div class=\"infoline\">";
    print $info_str;
    print "</div>\n";

    print("</div>");

    $arrayline++;
}

if ($DisplayContext == 1 || $AllowExactPhrase == 1)
    fclose($fp_pagetext);

fclose($fp_pagedata);

// Show links to other result pages
if ($num_pages > 1)
{
    // 10 results to the left of the current page
    $start_range = $page - 10;
    if ($start_range < 1)
        $start_range = 1;

    // 10 to the right
    $end_range = $page + 10;
    if ($end_range > $num_pages)
        $end_range = $num_pages;

    print "<div class=\"result_pages\">\n" . $STR_RESULT_PAGES . " ";
    if ($page > 1)
        print "<a href=\"".$SelfURL."?zoom_query=".$queryForURL."&amp;zoom_page=".($page-1)."&amp;zoom_per_page=".$per_page.$query_zoom_cats."&amp;zoom_and=".$and."&amp;zoom_sort=".$sort."\">&lt;&lt; " . $STR_RESULT_PAGES_PREVIOUS . "</a> ";
    for ($i = $start_range; $i <= $end_range; $i++)
    {
        if ($i == $page)
            print $page." ";
        else
            print "<a href=\"".$SelfURL."?zoom_query=".$queryForURL."&amp;zoom_page=".($i)."&amp;zoom_per_page=".$per_page.$query_zoom_cats."&amp;zoom_and=".$and."&amp;zoom_sort=".$sort."\">".$i."</a> ";
    }
    if ($page != $num_pages)
        print "<a href=\"".$SelfURL."?zoom_query=".$queryForURL."&amp;zoom_page=".($page+1)."&amp;zoom_per_page=".$per_page.$query_zoom_cats."&amp;zoom_and=".$and."&amp;zoom_sort=".$sort."\">" . $STR_RESULT_PAGES_NEXT . " &gt;&gt;</a> ";
    print "</div>";
}
print "</div>"; // end of results style tag

if ($Timing == 1 || $Logging == 1)
{
    $mtime = explode(" ", microtime());
    $endtime   = doubleval($mtime[1]) + doubleval($mtime[0]);
    $difference = abs($starttime - $endtime);
    $timetaken = number_format($difference, 3, '.', '');
    if ($Timing == 1)
        print "<div class=\"searchtime\"><br /><br />" . $STR_SEARCH_TOOK . " " . $timetaken . " " . $STR_SECONDS . "</div>\n";
}

//Log the search words, if required
if ($Logging == 1)
{
    $LogQuery = str_replace("\"", "\"\"", $query);
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
    	$ip_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	$ip_array = explode(",", $ip_addr);
    	if (count($ip_array) > 0)
    		$ip_addr = trim($ip_array[0]);	// get first IP if there are multiple addresses
    }
    else
    	$ip_addr = $_SERVER['REMOTE_ADDR'];
    $LogString = Date("Y-m-d, H:i:s") . ", " . $ip_addr . ", \"" .$LogQuery  . "\", Matches = " . $matches;
    if ($and == 1)
        $LogString = $LogString . ", AND";
    else
        $LogString = $LogString . ", OR";

    if ($NewSearch == 1)
        $page = 0;

    $LogString = $LogString . ", PerPage = " . $per_page . ", PageNum = " . $page;

    if ($UseCats == 0)
        $LogString = $LogString . ", No cats";
    else
    {
        if ($cat[0] == -1)
            $LogString = $LogString . ", \"Cat = All\"";
        else
        {
            $LogString = $LogString . ", \"Cat = ";
            for ($cati = 0; $cati < $num_zoom_cats; $cati++)
            {
                if ($cati > 0)
                    $LogString = $LogString . ", ";
                $logCatStr = trim($catnames[$cat[$cati]]);
                $logCatStr = str_replace("\"", "\"\"", $logCatStr);
                $LogString = $LogString . $logCatStr;
            }
            $LogString = $LogString . "\"";
        }
    }
    $LogString = $LogString . ", Time = " . $timetaken;

    $LogString = $LogString . ", Rec = " . $num_recs_found;

    // end of entry
    $LogString = $LogString . "\r\n";

    $fp = fopen ($LogFileName, "ab");
    if ($fp != false)
    {
        fputs ($fp, $LogString);
        fclose ($fp);
    }
    else
    {
        print "Unable to write to log file (" . $LogFileName . "). Check that you have specified the correct log filename in your Indexer settings and that you have the required file permissions set.<br />";
    }
}

//Print out the end of the template
PrintEndOfTemplate($template);

?>
