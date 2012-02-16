<?php defined('C5_EXECUTE') or die('Access Denied.');

Loader::model('attribute/types/default/controller');

class LertecoTextAttributeTypeController extends DefaultAttributeTypeController  {
	const TYPE_REGEXP = -1;
	const TYPE_FREE = 0;
	
	const TYPE_EMAIL = 1;
	const TYPE_URL = 2;
	const TYPE_CC = 3;


	public $typeOptions = array(self::TYPE_FREE => 'No type validation',
								self::TYPE_EMAIL => 'Email Address',
								self::TYPE_URL => 'Web Address',
								self::TYPE_REGEXP => 'Use Regular Expression'
								);

	protected $searchIndexFieldDefinition = 'X NULL';



	// *********** type setup (type form)
	public function type_form() {
		$this->set('textConfig', $this->getConfig());
		$this->set('typeOptions', $this->typeOptions);
	}
	
	public function saveKey($data) {
		$db = Loader::db();

		$whitelist = array('valType', 'valRegExp', 'valReq', 'formatType');
		// sets checkbox defaults correctly
		$dbupdate = array('valReq' => 0, 'formatType' => 0);

		foreach ($whitelist as $colname) {
			if (isset ($data[$colname])) {
				$dbupdate[$colname] = $data[$colname];
			}
		}
		$dbupdate['akID'] = $this->getAttributeKey()->getAttributeKeyID();

		$db->Replace('atLertecoText', $dbupdate, 'akID', true);
	}

	// *********** attribute key editing (form)
	public function form() {
		$hh = Loader::helper('html'); /* @var $hh HtmlHelper */
		
		$textConfig = $this->getConfig();
		$value = $this->getValue(true);
		$mustValidate = false;

		if ($textConfig['valType'] != self::TYPE_FREE || $textConfig['valReq']) {
			//we have to do some sort of validation... load all the stuff
			$this->addHeaderItem($hh->javascript('jquery.validate.min.js', 'lerteco_text_at'));
			$this->addHeaderItem($hh->javascript('jquery.validate.config.js', 'lerteco_text_at'));
			$mustValidate = true;
		}

		$this->set('textConfig', $textConfig);
		$this->set('fieldName', $this->field('value'));
		$this->set('value', $value);
		$this->set('mustVal', $mustValidate);
/*
		if (is_object($this->attributeValue)) {
			$value = Loader::helper('text')->entities($this->getAttributeValue()->getValue());
		}
		print Loader::helper('form')->text($this->field('value'), $value);
 * 
 */
	}

	// *********** Display
	public function  getValue($supressFormat = false) {
		$textConfig = $this->getConfig();
		$value = parent::getValue();

		if (! $supressFormat && $textConfig['formatType']) {
			return $this->format($value, $textConfig['valType']);
		} else {
			return $value;
		}
	}

	//should do a better job of sanitizing.
	public function  getDisplaySanitizedValue() {
		return $this->getValue();
	}

	private function format($val, $formatType) {
		switch ($formatType) {
			case self::TYPE_EMAIL:
				return "<a href=\"mailto://$val\">$val</a>";

				break;
			case self::TYPE_URL:
				return "<a href=\"$val\">" . preg_replace('!https?://!', '', $val) . "</a>";

				break;
			default:
				return $val;

				break;
		}
	}

	private function getConfig() {
		$db = Loader::db();
		if ($ak = $this->getAttributeKey()) {
			return $db->GetRow('SELECT * FROM atLertecoText WHERE akID = ?', $ak->getAttributeKeyID());
		}
	}

}

/*
class LertecoTextAttributeTypeController extends AttributeTypeController  {

	protected $searchIndexFieldDefinition = 'X NULL';
	
	public function getValue() {
		$db = Loader::db();
		$value = $db->GetOne("select value from atMultipleFiles where avID = ?", array($this->getAttributeValueID()));
		return $value;	 
	}
	
	
	public function getDisplayValue() {
		return $this->getValue();
	}


	public function searchForm($list) {
		$db = Loader::db();
		$list->filterByAttribute($this->attributeKey->getAttributeKeyHandle(), '%' . $this->request('value') . '%', 'like');
		return $list;
	}	
	
	public function search() { 
		$f = Loader::helper('form');
		print $f->text($this->field('value'), $value);
	}	 
	
	public function form(){   
		$al = Loader::helper('concrete/asset_library');    
	}	

	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue( $fIDs=array() ) {
		$db = Loader::db();
		if(!is_array($fIDs)) $fIDs=array();
		$cleanFIDs=array();
		foreach($fIDs as $fID) $cleanFIDs[]=intval($fID);
		$cleanFIDs = array_unique($cleanFIDs);
		$db->Replace('atMultipleFiles', array('avID' => $this->getAttributeValueID(), 'value' => join(',',$cleanFIDs)), 'avID', true);
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$arr = $this->attributeKey->getAttributeValueIDList();
		foreach($arr as $id) {
			$db->Execute('delete from atMultipleFiles where avID = ?', array($id));
		}
	}
	
	public function saveForm($data) { 
		$db = Loader::db();
		$this->saveValue($data['fID']);
	}
	
	public function deleteValue() {
		$db = Loader::db();
		$db->Execute('delete from atMultipleFiles where avID = ?', array($this->getAttributeValueID()));
	}
	
	
	static public function getFiles($valueStr=''){  
		$files=array();
		foreach(explode(',',$valueStr) as $fID){
			if(!intval($fID)) continue;
			$file = File::getByID(intval($fID));
			if(!is_object($file) || !$file->getFileID()) continue;   
			$files[]=$file; 
		}  	
		return $files; 
	}
	
}
*/

/*
class MultipleFilesAttributeTypeValue extends Object { 

	public static function getByID($avID) {
		$db = Loader::db();
		$value = $db->GetRow("select * from atMultipleFiles where avID = ?", array($avID));
		$mfatv = new MultipleFilesAttributeTypeValue();
		$mfatv->setPropertiesFromArray($value);
		if ($value['avID']) {
			return $mfatv;
		}
	} 
	
	public function __construct() {
		
	}	 
	
	public function getFiles(){  
		$files=array();
		foreach(explode(',',$this->value) as $fID){
			if(!intval($fID)) continue;
			$file = File::getByID(intval($fID));
			if(!is_object($file) || !$file->getFileID()) continue;   
			$files[]=$file; 
		}  	
		return $files; 
	}
	
	public function __toString() {
		$fileNames=array();
		foreach($this->getFiles() as $f){
			$fv = $f->getApprovedVersion();
			$fileNames[]=$fv->getTitle();
		} 
		return join(', ',$fileNames);	
	}
}
*/ 