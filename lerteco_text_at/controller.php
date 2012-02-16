<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Package that installs the advanced text attribute
 */

class LertecoTextAtPackage extends Package {

	protected $pkgHandle = 'lerteco_text_at';
	protected $appVersionRequired = '5.5.1';
	protected $pkgVersion = '1.0.0';
	
	public function getPackageDescription() { 
		return t('An advanced text attribute.');
	}
	
	public function getPackageName() {
		return t('Lerteco Advanced Text Attribute');
	}
	
	public function upgrade(){
		parent::upgrade();

		$this->configure();
	}
	
	public function install() {
		parent::install();

		$this->configure();
	}
	
	public function configure() {  
		$pkg = Package::getByHandle('lerteco_text_at');
		
		Loader::model('collection_types');
		Loader::model('collection_attributes');
		$db = Loader::db(); 
		  
		// install attribute type
		$at = AttributeType::getByHandle('lerteco_text');
		if(! is_object($at) || ! intval($at->getAttributeTypeID()) ) {
			$at = AttributeType::add('lerteco_text', t('Advanced Text (Lerteco)'), $pkg);
		} 


		$colAttrCat = AttributeKeyCategory::getByHandle('collection');
		$attrKeyTypeExists = $db->getOne('SELECT count(*) FROM AttributeTypeCategories WHERE atID=? AND akCategoryID=?',
											array($at->getAttributeTypeID(), $colAttrCat->getAttributeKeyCategoryID())
										);
		if (! $attrKeyTypeExists) {
			$colAttrCat->associateAttributeKeyType($at);
		}

		$userAttrCat = AttributeKeyCategory::getByHandle('user');
		$attrKeyTypeExists = $db->getOne('SELECT count(*) FROM AttributeTypeCategories WHERE atID=? AND akCategoryID=?',
											array($at->getAttributeTypeID(), $userAttrCat->getAttributeKeyCategoryID())
										);
		if (! $attrKeyTypeExists) {
			$userAttrCat->associateAttributeKeyType($at);
		}
	}
}

?>