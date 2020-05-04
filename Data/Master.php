<?php
session_start();
$localpath = getenv("SCRIPT_NAME");
$absolutepath = getenv("SCRIPT_FILENAME");
$_SERVER["DOCUMENT_ROOT"] = substr($absolutepath, 0, strpos($absolutepath, $localpath));
// include_once("$root/server/assets.php");
class Data {
	public function __construct() {}
	function DBConnection() {
		$srvrNm = "localhost";
		$uNm = "truuser";
		$pw = "hwj!#jaah";
		$db = "trudesigns";
		return mysqli_connect($srvrNm,$uNm,$pw,$db);
	}

	# Table Properties
	public function CountResult($res) {
		return mysqli_num_rows($res);
	}
	public function NoResult($res) {
		if (mysqli_num_rows($res) == 0) {
			return true;
		}
	}
	public function HasResult($res) {
		if (mysqli_num_rows($res) > 0) {
			return true;
		}
	}
	public function FieldName($res,$numFields) {
		return mysql_field_name($res,$numFields);
	}
	public function TableExists($tblNm) {
		if (mysqli_num_rows(mysqli_query($this->DBConnection(),"SHOW TABLES LIKE '".$tblNm."'"))) {
			return true;
		}
	}

	public function Insert($tbl,$fields,$vals) {
		mysqli_query($this->DBConnection(),"INSERT INTO $tbl($fields) VALUES($vals)");
	}

	# Select
	public function SelectTablePart($tblPart,$tblNm) {
		return mysqli_query($this->DBConnection(),"SELECT $tblPart FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$tblNm'");
	}
	public function SelectAll($tblNm) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm");
	}
	public function SelectAllWithLimit($tblNm,$lim) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm LIMIT $lim");
	}
	public function SelectOrderBy($tblNm,$field) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm ORDER BY $field");
	}
	public function SelectAllOrderByWithLimit($tblNm,$fieldNm,$lim) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm ORDER BY $fieldNm LIMIT $lim");
	}
	public function SelectAllOrderByDescendingWithLimit($tblNm,$fieldNm,$lim) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm ORDER BY $fieldNm DESC LIMIT $lim");
	}
	public function SelectByValue($tblNm,$fieldNm,$val) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm WHERE $fieldNm = '$val'");
	}
	public function SelectByValueOrderBy($tblNm,$fieldNm,$val,$orderField) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm WHERE $fieldNm = '$val' ORDER BY $orderField");
	}
	public function SelectByValueOrderByDescending($tblNm,$fieldNm,$val,$orderField) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm WHERE $fieldNm = '$val' ORDER BY $orderField DESC");
	}
	public function SelectWithLimit($tblNm,$fieldNm,$val,$lim) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm WHERE $fieldNm = '$val' LIMIT $lim");
	}
	public function SelectOrderByDescending($tblNm,$field) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm ORDER BY $field DESC");
	}
	public function ResultValue($res) {
		return mysqli_fetch_assoc($res);
	}
	public function SelectByMatch($tblNm,$fieldNm,$keyword) {
		return mysqli_query($this->DBConnection(),"SELECT * FROM $tblNm WHERE $fieldNm LIKE '%$keyword%'");
	}

	public function UpdateField($tblNm,$updateField,$updateVal,$fieldNm,$val) {
		return mysqli_query($this->DBConnection(),"UPDATE $tblNm SET $updateField = '$updateVal' WHERE $fieldNm = '$val'");
	}
	public function DeleteByID($tblNm,$idFieldNm,$id) {
		return mysqli_query($this->DBConnection(),"DELETE FROM $tblNm WHERE $idFieldNm = '$id'");
	}
	function NotEmpty($val) {
		if (strlen($val) > 1) {
			return true;
		}
	}
	public function URLData($cllr) {
		return $_GET[$cllr];
	}
	public function CurrentURLData() {
		return $_SERVER["QUERY_STRING"];
	}
	public function ConcatQuery($vals,$statement,$whereField,$concatVal) {
		$totalVals = count($vals);
		$query = $statement . " ";
		for ($q = 0; $q < $totalVals; $q++) {
			$concat = " $concatVal ";
			if ($q == ($totalVals - 1)) {
				$concat = "";
			}
			$val = $vals[$q];
			$query .= "$whereField = '$val' $concat";
		}
		return $query;
	}
}
$data = new Data;

# Strings
class Strings {
	function Exists($str, $chars) {
		return strpos($str, $chars) !== false;
	}
	public function NotEmpty($str) {
		return strlen($str) > 0 || $str != "";
	}
	function ToID($str) {
		return strtolower(str_replace(" ","_",str_replace(" /","",$str)));
	}
	function GetCharacters($dir, $str, $numChars) {
		$output = "";
		switch($dir) {
			case "left":
				$strlen = strlen($str);
				$cutoff = $strlen - $numChars;
				$output = substr($str,0,-$cutoff);
				break;
			case "right":
				$output = substr($str,0,-$numChars);
		}
		return $output;
	}
}
$STRINGS = new Strings;

# Server
class Server {

	# Errors
	public $_errMsg = "";
	public function SetErrorMessage($errMsg) {
		$this->_errMsg = $errMsg;
	}
	public function ErrorMessage() {
		return $this->_errMsg;
	}

	# Sub Domains
	public $subDoms = array(
		"/_pricing",
		"/_portfolio"
	);

	function ClearQueryString() {
		$_SERVER["QUERY_STRING"] = null;
	}

	# Sessions
	public function GetSession($sess) {
		if (isset($sess)) {
			return $sess;
		}
	}
	public function SessionExists($sess) {
		// $exists = false;
		// if (isset($_SESSION[$sess])) {
		// 	$exists = true;
		// }
		// return $exists;
		return isset($_SESSION[$sess]);
	}
	public function ClearSessions($sessions) {
		$arrSessions = explode(",",$sessions);
		for ($s = 0; $s < count($arrSessions); $s++) {
			if (isset($_SESSION[$arrSessions[$s]])) {
				unset($_SESSION[$arrSessions[$s]]);
			}
		}
	}

	public function CreateMedia($tblNm,$tblFieldImg,$tblFieldID,$mediaDir,$pstdImg) {
		$STRINGS = new Strings;
		$SP = new StoredProcedures;
		$arrTblFieldImgs = explode(",",$tblFieldImg);
		$arrPstdImgs = explode(",",$pstdImg);
		$arrFileNames = array();
		$arrTargetFiles = array();
		$err = "";

		# Update Media
		$SP->StoredProcedure("select_dreams_latest_dream_id");
		$targID = SelectDreamsLatestDreamID($tblFieldID);
		$mediaDir = $mediaDir . $targID . "/";
		for ($f = 0; $f < count($arrPstdImgs); $f++) {
			$fileName = basename($_FILES[$arrPstdImgs[$f]]["name"]);
			$arrFileNames[$f] .= $fileName;
			$arrTargetFiles[$f] .= $mediaDir . $arrTblFieldImgs[$f] . "/" . $arrFileNames[$f];
			if ($STRINGS->NotEmpty($fileName)) {
				mysqli_query($this->DBConnection(),"UPDATE $tblNm SET $arrTblFieldImgs[$f] = '$fileName' WHERE $tblFieldID = '$targID'");
			}
		}

		# File Okay
		for ($ff = 0; $ff < count($arrTblFieldImgs); $ff++) {
			if ($STRINGS->NotEmpty($arrFileNames[$ff])) {

				# Move to Directory
				if (!move_uploaded_file($_FILES[$arrPstdImgs[$ff]]["tmp_name"],$arrTargetFiles[$ff])) {
					$err .= "<p class='alert'>There was an error uploading your file.</p>";
				}

			}
		}

	}
	public function CheckMediaValid($using,$pstdImg) {
		$STRINGS = new Strings;
		$SERVER = new Server;
		$arrPstdImgs = explode(",",$pstdImg);
		$valid = false;
		for ($f = 0; $f < count($arrPstdImgs); $f++) {
			$fileName = basename($_FILES[$arrPstdImgs[$f]]["name"]);
			$fileType = pathinfo($fileName,PATHINFO_EXTENSION);

			# Limit File Type
			if ($fileType != "jpg" && $fileType != "png") {
				$valid = false;
				$SERVER->SetErrorMessage("Only JPG and PNG files are allowed.");
			} else {
				$valid = true;
			}

			# Check File Selected
			if ($STRINGS->NotEmpty($fileName)) {
				$valid = true;
			} else {
				$valid = false;
				$SERVER->SetErrorMessage("No file selected.");
			}

		}
		if (!$using) {
			$valid = true;
		}
		return $valid;
	}
	public function CurrentPage() {
		$curPg = basename($_SERVER["PHP_SELF"]);
		if ($curPg == "dreams.php") {
			$curPg = "projects.php";
		}
		return $curPg;
	}
}
$server = new Server;

# Stored Procedures
class StoredProcedures {
	private $_sp = "";
	public function __construct($sp = null) {
		$this->_sp = $sp;
	}
	public function GetStoredProcedure() {
		return $this->_sp;
	}
	public function StoredProcedure($spFile) {
		include_once($this->RootPath() . $spFile . ".php");
	}
	public function RootPath() {
		return str_replace("/_portfolio","",str_replace("/_pricing","",realpath($_SERVER["DOCUMENT_ROOT"]))) . "/server/stored_procedures/";
	}
	public function Path() {
		return "/server/stored_procedures/";
	}
	public function Request($query) {
		return $this->Path() . $this->_sp . ".php?" . $query;
	}
	public function HandleWriteParameters($params,$num) {
		$arrParams = array();
		$encrypt = false;
		for ($p = 0; $p < count($params); $p++) {
			$param = $params[$p];
			if ($param == "password") {
				$encrypt = true;
			} else {
				$encrypt = false;
			}
			if (!$encrypt) {
				$arrParams[$p] = nl2br(stripslashes($_POST[$param]));
			} else {
				$arrParams[$p] = sha1($_POST[$param]);
			}
		}
		return $arrParams[$num];
	}
	public function HandleWriteParametersAjax($params,$num) {
		$arrParams = array();
		$encrypt = false;
		for ($p = 0; $p < count($params); $p++) {
			$param = $params[$p];
			if ($param == "password") {
				$encrypt = true;
			} else {
				$encrypt = false;
			}
			if (!$encrypt) {
				$arrParams[$p] = nl2br(stripslashes($_GET[$param]));
			} else {
				$arrParams[$p] = sha1($_GET[$param]);
			}
		}
		return $arrParams[$num];
	}
	public function HandleReadParameters($params,$num) {
		$arrParam = array();
		for ($p = 0; $p < count($params); $p++) {
			$arrParam[$p] = $params[$p];
		}
		return $arrParam[$num];
	}
}
$sp = new StoredProcedures;

# Forms
class Forms {
	private $_errs = 0;
	private $_submitBtnName = "";

	# Constructors
	public function __construct($submitBtnName) {
		$this->_submitBtnName = $submitBtnName;
	}

	# Retain Entered Text for When Submission is Made
	public function RetainValues($pstd) {
		echo isset($_POST[$pstd]) ? $_POST[$pstd] : "";
	}

	function RetainFieldValues($cllr,$pstd,$origVal) {
		if ($cllr == "false") {
			$fieldVal = $pstd;
		} else {
			$fieldVal = strip_tags($origVal);
		}
		return $fieldVal;
	}
	public function Validate($field) {
		$errMsg = "";
		switch($field) {
			case "Email":
				$STRINGS = new Strings;
				if (!$STRINGS->Exists($_POST["email"],"@") && !$STRINGS->Exists($_POST["email"],".")) {
					$errMsg = $this->ErrorMessage("Invalid email address");
				}
				break;
		}
		if (array_key_exists($this->_submitBtnName,$_POST)) {
			return $errMsg;
		}
	}
	private function ErrorMessage($errMsg) {
		$this->_errs += 1;
		return "<p class='form_alert'>$errMsg</p>";
	}
	public function Valid() {
		return $this->_errs == 0;
	}
	public function SubmitButtonName() {
		return $this->_submitBtnName;
	}
	public function CheckboxList($fieldName) {
		$list = "<ul>";
		if (array_key_exists($this->_submitBtnName,$_POST)) {
			foreach($_POST[$fieldName] as $selItem) {
				$list .= "<li>$selItem</li>";
			}
		}
		$list .= "</ul>";
		return $list;
	}
	public function FormatParagraphs($txt) {
		return nl2br(stripslashes($txt));
	}
}

# Mail
class Mail {
	private $_from = "";
	private $_to = "";
	private $_subject = "";
	private $_msg = "";

	# Constructors
	public function __construct($from,$to,$subject,$msg) {
		$this->_from = $from;
		$this->_to = $to;
		$this->_subject = $subject;
		$this->_msg = $msg;
	}

	private function Generate($email = null) {
		$from = $this->_from;
		$headers = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8\r\n";
		if ($email == null) {
			$headers .= "From: $from";
		} else {
			$headers .= "From: <$email>";
		}
		return mail($this->_to,$this->_subject,$this->_msg,$headers);
	}
	public function Send($FORMS,$email = null) {
		if (array_key_exists($FORMS->SubmitButtonName(),$_POST)) {
			if ($FORMS->Valid()) {
				$this->Generate($email);
				echo "<p><b>Thank you " . $_POST["name"] . " for your submission!</b></p>";
			} else {
				echo "<p class='form_alert'>There was an issue submitting the form. Please try again.</p>";
			}
		}
		// else {
		// 	echo $FORMS->SubmitButtonName();
		// }
	}
}

# Users
class Users {
	public function Login() {
		$SP = new StoredProcedures;
		$SERVER = new Server;
		$success = false;
		if (!$SERVER->SessionExists("username")) {
			$SP->StoredProcedure("select_users_username_password");

			# Check Logged In
			if (SelectUsersUsernamePassword("Count") == 1)  {
				$success = true;
				$_SESSION["u_id"] = SelectUsersUsernamePassword("u_id");
				$_SESSION["username"] = SelectUsersUsernamePassword("username");
			} else {
				$success = false;
			}

		}
		return $success;
	}
	public function LoggedIn() {
		$SERVER = new Server;
		if ($SERVER->SessionExists("username")) {
			return true;
		}
	}
	public function InvalidLogin() {
		$DATA = new Data;
		$invalid = false;
		if ($DATA->URLData("login") == "n") {
			$invalid = true;
		}
		return $invalid;
	}
	public function User($prop) {
		$USERS = new Users;
		$SP = new StoredProcedures;
		if ($USERS->LoggedIn()) {
			$SP->StoredProcedure("select_users_current_id");
			return SelectUsersCurrentID($prop);
		}
	}
	public function Logout() {
		$server = new Server;
		if ($server->SessionExists("username")) {
			unset($_SESSION["u_id"]);
			unset($_SESSION["username"]);
		}
	}

	# Deny Access if Not Logged In
	public function DenyAccessToGuests() {
		$users = new Users;
		if (!$users->LoggedIn()) {
			header("Location: /_cms/index.php");
		}
	}

}
$users = new Users;

# Navigating
class Navigating {
	public function LinkActiveState($mobile,$linkNm,$curPg) {
		$activeState = "inactive";
		if ($linkNm == $curPg) {
			if (!$mobile) {
				$activeState = "active";
			} else {
				$activeState = "m_active";
			}
		}
		return $activeState;
	}
}
$NAVIGATING = new Navigating;

# JSON
class JSON {
	public function CreateWithRoots($sel,$rootLbl) {
		$DATA = new Data;
		$res = array();
		while ($r = $DATA->ResultValue($sel)) {
			$res[] = $r;
		}
		return "{\"$rootLbl\":" . json_encode($res) . "}";
	}
	public function CreateWithUniqueRoots($arrJSONS,$arrRootLbls) {
		$json = "{";
		$totalJSONS = count($arrJSONS);
		for ($r = 0; $r < $totalJSONS; $r++) {
			$delim = ",";
			if ($r == $totalJSONS - 1) {
				$delim = "";
			}
			$json .= "\"" . $arrRootLbls[$r] . "\":" . $arrJSONS[$r] . $delim;
		}
		$json .= "}";
		return $json;
	}
	public function Append($arrJSONS,$arrRootLbls) {
		$json = "";
		$totalJSONS = count($arrJSONS);
		for ($r = 0; $r < $totalJSONS; $r++) {
			$delim = ",";
			if ($r == $totalJSONS - 1) {
				$delim = "";
			}
			$json .= "\"" . $arrRootLbls[$r] . "\":" . $arrJSONS[$r] . $delim;
		}
		return $json;
	}
	public function SingleItem($json,$end = false) {
		$delim = "";
		if (!$end) {
			$delim = ",";
		}
		return str_replace("}","",str_replace("{","",json_encode($json))) . $delim;
	}
	public function Build($json) {
		return "{" . $json . "}";
	}
	public function Read($json, $arr = true) {
		$output = "";
		if (!$arr) {
			$output = json_decode($json);
		} else {
			$output = json_decode($json, $arr);
		}
		return $output;
	}
	public function Create($res) {
		return json_encode($res);
	}
}

?>