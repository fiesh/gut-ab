<?php

class FragmentLoader {
	const API = 'http://de.guttenplag.wikia.com/api.php';
	const REDIRECT_PATTERN = '/^#(REDIRECT|WEITERLEITUNG)\s+/';

	static private function processString($s)
	{
		$needle = '';
		for($i = 1; $i < 12; $i++)
			$needle .= 'val_'.$i.'="([^"]*)"\s+';
		if (preg_match_all("/$needle/", $s, $a)) {
			for($i = 1; $i < 12; $i++) {
				$a[$i] = trim($a[$i][0]);
				if(strpos($a[$i], ',') !== false)
					$a[$i] = '"'.$a[$i].'"';
			}
			$a[0] = $s;
			return $a;
		} else {
			return false;
		}
	}

	static private function getPrefixList($prefix)
	{
		return unserialize(file_get_contents(self::API.'?action=query&prop=revisions&&format=php&generator=allpages&gaplimit=500&gapprefix='.urlencode($prefix)));
	}

	static private function getEntries($pageids)
	{
		return unserialize(file_get_contents(self::API.'?action=query&prop=revisions&rvprop=content&format=php&pageids='.urlencode(implode('|', $pageids))));
	}

	static private function getEntriesWithPrefix($prefix,
			$ignoreRedirects = true, $sortByTitle = true)
	{
		$polls = self::getPrefixList($prefix);
	
		$i = 0;
		$pageids = array();
		$entries = array();
		foreach($polls['query']['pages'] as $page) {
			$pageids[] = $page['pageid'];
			if(++$i === 49) {
				$i = 0;
				$entryPolls = self::getEntries($pageids);
				$entries = array_merge($entries, $entryPolls['query']['pages']);
				$pageids = array();
			}
		}
		$entryPolls = self::getEntries($pageids);
		if(isset($entryPolls['query']['pages']))
			$entries = array_merge($entries, $entryPolls['query']['pages']);

		if($ignoreRedirects) {
			$temp = array(); // will contain all non-redirects
			foreach($entries as $e) {
				if(!preg_match(self::REDIRECT_PATTERN, $e['revisions'][0]['*']))
					$temp[] = $e;
			}
			$entries = $temp;
		}

		if($sortByTitle) {
			$temp = array(); // will contain all wiki titles
			foreach($entries as $e)
				$temp[] = $e['title'];
			array_multisort($temp, $entries); // sort by wiki title
		}

		return $entries;
	}

	static private function getFragmentsWithPrefix($prefix)
	{
		$fragments = array();
		foreach(self::getEntriesWithPrefix($prefix) as $e) {
			$a = self::processString($e['revisions'][0]['*']);
			$a['wikiTitle'] = $e['title'];
			if(isset($a[1]) && $a[1])
				$fragments[] = $a;
		}
		return $fragments;
	}

	static public function getFragments()
	{
		$fragments = array();
		for($i = 0; $i < 5; $i++)
			$fragments = array_merge($fragments, self::getFragmentsWithPrefix("Fragment $i"));

		return $fragments;
	}
}
