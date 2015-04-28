<?php

class DeliveryProfileGenericHttp extends DeliveryProfileHttp {
	
	public function setPattern($v)
	{
		$this->putInCustomData("pattern", $v);
	}
	public function getPattern()
	{
		return $this->getFromCustomData("pattern");
	}
	
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset) 
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		return kDeliveryUtils::formatGenericUrl($url, $this->getPattern(), $this->params);
	}
	
	protected function doGetFileSyncUrl(FileSync $fileSync)
	{
		$url = parent::doGetFileSyncUrl($fileSync);
		$pattern = $this->getPattern();
		if(is_null($pattern))
			$pattern = '{url}';
		return kDeliveryUtils::formatGenericUrl($url, $pattern, $this->params);
	}

	/**
	 * returns whether the delivery profile supports the passed deliveryAttributes in this case seekFrom
	 * @param DeliveryProfileDynamicAttributes $deliveryAttributes
	 */
	public function supportsDeliveryDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes) {
		if (!parent::supportsDeliveryDynamicAttributes($deliveryAttributes))
			return false;
	
		if ($deliveryAttributes->getSeekFromTime() > 0)
			return false;
				
		return true;
	}

}

