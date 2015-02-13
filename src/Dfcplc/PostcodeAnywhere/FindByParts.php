<?php
namespace Dfcplc\PostcodeAnywhere;

class FindByParts
{
	//Credit: Thanks to Stuart Sillitoe (http://stu.so/me) for the original PHP that these samples are based on.

	private $Key; //The key to use to authenticate to the service.
	private $Organisation; //The name of the organisation to find.
	private $Building; //The name of the building to find.
	private $Street; //The name of the street to find.
	private $Locality; //The name of the locality.
	private $Postcode; //The postcode to search with find.
	private $UserName; //The username associated with the Royal Mail license (not required for click licenses).
	private $Data; //Holds the results of the query

	function PostcodeAnywhere_Interactive_FindByParts_v1_00($Key, $Organisation, $Building, $Street, $Locality, $Postcode, $UserName)
	{
		$this->Key = $Key;
		$this->Organisation = $Organisation;
		$this->Building = $Building;
		$this->Street = $Street;
		$this->Locality = $Locality;
		$this->Postcode = $Postcode;
		$this->UserName = $UserName;
	}

	function MakeRequest()
	{
		$url = "http://services.postcodeanywhere.co.uk/PostcodeAnywhere/Interactive/FindByParts/v1.00/xmla.ws?";
		$url .= "&Key=" . urlencode($this->Key);
		$url .= "&Organisation=" . urlencode($this->Organisation);
		$url .= "&Building=" . urlencode($this->Building);
		$url .= "&Street=" . urlencode($this->Street);
		$url .= "&Locality=" . urlencode($this->Locality);
		$url .= "&Postcode=" . urlencode($this->Postcode);
		$url .= "&UserName=" . urlencode($this->UserName);

		//Make the request to Postcode Anywhere and parse the XML returned
		$file = simplexml_load_file($url);

		//Check for an error, if there is one then throw an exception
		if ($file->Columns->Column->attributes()->Name == "Error")
		{
			throw new Exception("[ID] " . $file->Rows->Row->attributes()->Error . " [DESCRIPTION] " . $file->Rows->Row->attributes()->Description . " [CAUSE] " . $file->Rows->Row->attributes()->Cause . " [RESOLUTION] " . $file->Rows->Row->attributes()->Resolution);
		}

		//Copy the data
		if ( !empty($file->Rows) )
		{
			foreach ($file->Rows->Row as $item)
			{
				$this->Data[] = array('Id'=>$item->attributes()->Id,'StreetAddress'=>$item->attributes()->StreetAddress,'Place'=>$item->attributes()->Place);
			}
		}
	}

	function HasData()
	{
		if ( !empty($this->Data) )
		{
			return $this->Data;
		}
		return false;
	}

}
