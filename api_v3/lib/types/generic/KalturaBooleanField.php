<?php
/**
 * A boolean representation to return evaluated dynamic value
 * 
 * @package api
 * @subpackage objects
 * @abstract
 */
abstract class KalturaBooleanField extends KalturaBooleanValue
{
	/* (non-PHPdoc)
	 * @see KalturaIntegerValue::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!is_null($this->value) && !($this->value instanceof KalturaNullField))
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NOT_UPDATABLE, $this->getFormattedPropertyNameWithClassName('value'));

		return parent::toObject($dbObject, $skip);
	}
}