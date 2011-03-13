<?php

require_once('WikiLoader.php');

class FragmentLoader {
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

	static private function collectCategories($entry)
	{
		$cats = array();
		if(isset($entry['categories']))
			foreach($entry['categories'] as $c)
				$cats[] = $c['title'];
		$cats = array_unique($cats);
		sort($cats);
		return $cats;
	}

	static private function processFrags($entries, $titleBlacklist=array())
	{
		$fragments = array();
		foreach($entries as $e) {
			$a = self::processString($e['revisions'][0]['*']);
			$a['wikiTitle'] = $e['title'];
			$a['categories'] = self::collectCategories($e);
			if(isset($a[1]) && $a[1] && !in_array($e['title'], $titleBlacklist))
				$fragments[] = $a;
		}
		return $fragments;
	}

	static public function getFragments()
	{
		$entries = WikiLoader::getEntriesWithPrefix('Fragment', true, true);
		$titleBlacklist = array('Fragment 99999 11-22');
		return self::processFrags($entries, $titleBlacklist);
	}

	static public function getFragmentsG2006()
	{
		$entries = WikiLoader::getEntriesWithPrefix('Guttenberg-2006/', true, true);
		$fragmentTitles = array();
		foreach($entries as $e) {
			if(preg_match('/^Guttenberg-2006\/(\d{3})$/', $e['title'], $match) && $match[1] >= 1) {
				$pagenum = (int) $match[1];
				$raw = $e['revisions'][0]['*'];
				$raw = preg_replace('/<!--.*?-->/s', '', $raw);
				preg_match_all('/\{\{:([- _A-Za-z0-9\/]*?)\}\}/', $raw, $match);
				$i = 0;
				while(isset($match[1][$i])) {
					$fragmentTitles[] = str_replace('_', ' ', $match[1][$i]);
					++$i;
				}
			}
		}

		$fragmentEntries = WikiLoader::getEntriesByTitles($fragmentTitles);
		return self::processFrags($fragmentEntries);
	}

	static public function getFragmentTypes()
	{
		$pageids = WikiLoader::getCategoryMembers('Kategorie:PlagiatsKategorien');
		$entries = WikiLoader::getEntries($pageids, true, true);

		$fragtypes = array();
		foreach($entries as $entry) {
			if (substr($entry['title'], 0, 10) == 'Kategorie:')
				$fragtypes[] = $entry['title'];
		}
		return $fragtypes;
	}
}
