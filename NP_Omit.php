<?php
// 日本語文字化け防止
class NP_Omit extends NucleusPlugin { 
	function getName()             { return 'omit'; } 
	function getAuthor()           { return 'yama.kyms'; } 
	function getURL()              { return 'http://kyms.ne.jp/'; } 
	function getVersion()          { return '0.24'; } 
	function getMinNucleusVersion(){ return 324; }
	function getDescription()      { return 'Omit'; }
	function supportsFeature($w)   { return ($w == 'SqlTablePrefix') ? 1 : 0; }
	
	function doTemplateVar(&$item, $param1 = 'body', $param2 = 200, $param3 = "char")
	{
		$this->defineMultilanguage();
		switch($param1)
		{
			case 'body':
				$source = strip_tags(&$item->body);
				$str = $this->_omit($source, $param2, $param3);
				break;
			case 'more':
				$source = strip_tags(&$item->more);
				$str = $this->_omit($source, $param2, $param3);
				break;
			case 'smartbody':
				if (!$item->more)
				{
					$source = strip_tags(&$item->body);
					$str = $this->_omit($source, $param2, $param3);
				}
				else
				{
					$str = strip_tags(&$item->more);
				}
				break;
			default:
				$source = strip_tags(&$item->body);
				$str = $this->_omit($source, $param2, $param3);
		}
		echo $str;
	}
	
	function _omit($source, $param2, $param3)
	{
		$delim = ($param3 == "char") ? _NP_OMIT_DELIM : $param3;
		$arr = explode($delim, $source);
		$hitpos = mb_strpos($source, $delim);
		if ($param2 == "all")
		{
			$result = $source;
		}
		elseif (empty($hitpos))
		{
			$arr2 = preg_split('/\s/', $source);
			$len = 0;
			foreach($arr2 as $value)
			{
				if ( intval($param2) <= intval($len) + mb_strlen($value, _CHARSET) ) break;
				$result .= $value . ' ';
				$len = mb_strlen($result, _CHARSET);
			}
		}
		elseif ((mb_strlen($arr[0], _CHARSET) +1) > intval($param2))
		{
			$result = mb_substr($source, 0, $param2, _CHARSET);
		}
		else
		{
			$result = "";
			$len = 0;
			foreach($arr as $value)
			{
				if ( intval($param2) <= intval($len) + mb_strlen($value, _CHARSET) ) break;
				$result .= $value . $delim;
				$len = mb_strlen($result, _CHARSET);
			}
		}
	$result = str_replace(array("\r\n","\r","\n","\s","&nbsp;"), '', $result);
	$pattern = '(' . _NP_OMIT_DELIM .')' . '+';
	$result = mb_ereg_replace($pattern, _NP_OMIT_DELIM, $result);
	$result = trim($result);
	return $result;
	}
	
	function defineMultilanguage()
	{
		$multilang = array('_NP_OMIT_DELIM'    => array('.', '。'),);
		switch (preg_replace('@\\|/@', '', getLanguageName()))
		{
			case 'japanese-euc':
				foreach ($multilang as $key => $value) define($key, mb_convert_encoding($value[1], 'EUC-JP', 'UTF-8'));
				break;
			case 'japanese-utf8':
				foreach ($multilang as $key => $value) define($key, $value[1]);
				break;
			default:
				foreach ($multilang as $key => $value) define($key, $value[0]);
		}
	}
}
// 日本語文字化け防止
?>