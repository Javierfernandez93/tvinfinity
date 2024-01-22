<?php
/**
*
*/

namespace JFStudio;

class ML
{
	private $values = [];
	private $vocabulary = [];

	public function hasSamples(array $samples) {
		return array_sum($samples) ? true : false;
	}
	public function getSamples(string $phrase) : array {
		$words = explode(" ", $phrase);

		foreach ($words as $token) {
			$key = $this->searchToken($token);

			if($key !== null)
			{
				$values[$key] = $this->getValues()[$key];
			}
		}


		for($i = 0; $i <= sizeof($this->getValues()); $i++)
		{
			$result[$i] = $values[$i] ? $values[$i] : 0;
		}

		return $result;
	}

	public function getTargetsByBD(array $array = null) 
	{
		return array_column($array, "words");
	}
	public function getSamplesByBD(array $array = null) 
	{
		return array_column($array, "tag");
	}
	public function searchToken(string $token = null) 
	{
		$key = false;

		foreach ($this->getVocabulary() as $key => $word) {
			if(strtolower($word) === strtolower($token))
			{
				$_key = $key;
			}
		}

		return $_key;
	}
	public function getVocabulary() : array {
		return $this->vocabulary;
	}
	public function setVocabulary(array $vocabulary) : void {
		$this->vocabulary = $vocabulary;
	}
	public function getValues() : array {
		return $this->values;
	}
	public function setValues(array $values) : void
	{
		$this->values = $values;
	}
	public function convertValues(array $samples) : array
	{
		foreach ($samples as $p) {
		    foreach ($p as $key => $value) 
		    {
		        if(empty($r[$key]) == true)
		        {
		            $r[$key] = $value;
		        }  else{
		            $r[$key] += $value;
		        }
		    }
		}

		return $r;
	}
}