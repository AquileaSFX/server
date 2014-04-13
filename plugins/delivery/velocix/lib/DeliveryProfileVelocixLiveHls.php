<?php
/**
 * @package plugins.velocix
 * @subpackage storage
 */
class DeliveryProfileVelocixLiveHls extends DeliveryProfileLiveAppleHttp
{
	//limit the number of urls we check as there could be a gazillion of them
	const MAX_SEGMENTS_TO_CHECK = 3;
	const MAX_FLAVORS_TO_CHECK = 3;
	
	// TODO Once we found someone who uses this tokenizer parameters, 
	// These should be externalized.
	public function setParamName($v)
	{
		$this->putInCustomData("paramName", $v);
	}
	
	public function getParamName()
	{
		return $this->getFromCustomData("paramName");
	}

	/**
	 * @return kUrlTokenizer
	 */
	public function getTokenizer()
	{
		// For configuration purposes.
		if(is_null($this->params->getEntryId()))
			return parent::getTokenizer();
		
		$liveEntry = entryPeer::retrieveByPK($this->params->getEntryId());
		//if stream name doesn't start with 'auth' than the url stream is not tokenized
		if ($liveEntry && substr($liveEntry->getStreamName(), 0, 4) == 'auth'){
			$token = parent::getTokenizer();
			$token->setStreamName($liveEntry->getStreamName());
			$token->setProtocol('hds');
			return $token;
		}
		
		return null;
	}
	
	public function isLive ($url)
	{
		$parts = parse_url($url);
		parse_str($parts['query'], $query);
		$token = $query[$this->getParamName()];
		$data = $this->urlExists($url, kConf::get("hls_live_stream_content_type"));
		if(!$data)
		{
			KalturaLog::Info("URL [$url] returned no valid data. Exiting.");
			return false;
		}
		KalturaLog::debug("url return data:[$data]");
		$explodedLine = explode("\n", $data);
		$flavorsChecked = 0;
		if (strpos($data,'#EXT-X-STREAM-INF') !== false)
		{
			//handle master manifest
			foreach ($explodedLine as $streamUrl)
			{
				$streamUrl = trim($streamUrl);
				if (!$streamUrl || $streamUrl[0]=='#')
				{
					continue;
				}
				if ($flavorsChecked == self::MAX_FLAVORS_TO_CHECK)
				{
					break;
				}
				$manifestUrl = $this->checkIfValidUrl($streamUrl, $url);
				$manifestUrl .= $token ? '?'.$this->getParamName()."=$token" : '' ;
				$data = $this->urlExists($manifestUrl, kConf::get("hls_live_stream_content_type"));
				if (!$data)
				{
					continue;
				}
				//handle flavor manifest
				if ($this->checkSegments($data, $token, $manifestUrl))
				{
					return true;
				}
				++$flavorsChecked;
			}
		}
		else if (strpos($data,'#EXTINF') !== false)
		{
			//handle flavor manifest
			return $this->checkSegments($data, $token, $url);
		}
		return false;
	}
	
	private function checkSegments($data, $token, $manifestUrl)
	{
		$segments = explode("\n", $data);
		$segmentsChecked = 0;
		foreach ($segments as $segment){
			if (!$segment || $segment[0]=='#')
			{
				continue;
			}
			if ($segmentsChecked == self::MAX_SEGMENTS_TO_CHECK)
			{
				break;
			}
			$segmentUrl = $this->checkIfValidUrl($segment, $manifestUrl);
			$segmentUrl .= $token ? '?'.$this->getParamName()."=$token" : '' ;
			if ($this->urlExists($segmentUrl, kConf::get("hls_live_stream_content_type"),'0-0'))
			{
				KalturaLog::info("is live:[$segmentUrl]");
				return true;
			}
			++$segmentsChecked;
		}
		return false;
	}
}
