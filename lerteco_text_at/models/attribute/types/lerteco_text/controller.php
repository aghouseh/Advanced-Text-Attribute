<?php defined('C5_EXECUTE') or die('Access Denied.');

Loader::model('attribute/types/default/controller');

/**
 * Sets up the attribute type for advanced text
 */
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
	/**
	 * Run before the type form (ie, the piece of the page shown when setting up or configuring an instance of an attribute type)
	 */
	public function type_form() {
		$this->set('textConfig', $this->getConfig());
		$this->set('typeOptions', $this->typeOptions);
	}

	/**
	 * Run after type form is submitted
	 * @param array $data Relevant form elements passed by controller on submit
	 */
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
	/**
	 * Run before the form (ie, the editing of a specific attribute instance).
	 * Here we load the various config options that the form needs, plus any javascript
	 */
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
	}

	// *********** Display
	/**
	 * Returns the attribute value
	 * @param boolean $supressFormat When set to true, prevents the formatting of an attribute regardless of the formatType config setting
	 * @return string Could be in HTML if the user provided HTML or we're formatting the value
	 */
	public function  getValue($supressFormat = false) {
		$textConfig = $this->getConfig();
		$value = parent::getValue();

		if (! $supressFormat && $textConfig['formatType']) {
			return $this->format($value, $textConfig['valType']);
		} else {
			return $value;
		}
	}

	/**
	 * Sometimes called by, e.g., the value display on the user profile page.
	 * TODO: This should return a sanitized value, but that screws up the formatting HTML code. On the other hand, even if they request "sanitized", we should still provide formatted, just formatted /and/ sanitized
	 * @return string Result of getValue(). Could be HTML.
	 */
	public function  getDisplaySanitizedValue() {
		return $this->getValue();
	}

	/**
	 * Formats the attribute value in a defined way (url, email, etc)
	 * @param string $val Text to format
	 * @param int $formatType A constant of type SELF::TYPE_ referring to the type of formatting to do
	 * @return <type>
	 */
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

	/**
	 * Gets the attribute type configuration
	 * @return array Represents DB row of config
	 */
	private function getConfig() {
		$db = Loader::db();
		if ($ak = $this->getAttributeKey()) {
			return $db->GetRow('SELECT * FROM atLertecoText WHERE akID = ?', $ak->getAttributeKeyID());
		}
	}

}