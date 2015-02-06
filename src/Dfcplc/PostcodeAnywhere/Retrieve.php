<?php
namespace Dfcplc\PostcodeAnywhere;

class Retrieve
{
	//Credit: Thanks to Stuart Sillitoe (http://stu.so/me) for the original PHP that these samples are based on.

	private $Key; //The key to use to authenticate to the service.
	private $Id; //The Id from a Find method to retrieve the details for.
	private $Data; //Holds the results of the query

	public function __construct($Key, $Id)
	{
		$this->Key = $Key;
		$this->Id = $Id;
	}

	public function MakeRequest()
	{
		$url = "http://services.postcodeanywhere.co.uk/CapturePlus/Interactive/Retrieve/v2.10/xmla.ws?";
		$url .= "&Key=" . urlencode($this->Key);
		$url .= "&Id=" . urlencode($this->Id);

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
				$this->Data[] = array(
					'Id'=>$item->attributes()->Id,
					'DomesticId'=>$item->attributes()->DomesticId,
					'Language'=>$item->attributes()->Language,
					'LanguageAlternatives'=>$item->attributes()->LanguageAlternatives,
					'Department'=>$item->attributes()->Department,
					'Company'=>$item->attributes()->Company,
					'SubBuilding'=>$item->attributes()->SubBuilding,
					'BuildingNumber'=>$item->attributes()->BuildingNumber,
					'BuildingName'=>$item->attributes()->BuildingName,
					'SecondaryStreet'=>$item->attributes()->SecondaryStreet,
					'Street'=>$item->attributes()->Street,
					'Block'=>$item->attributes()->Block,
					'Neighbourhood'=>$item->attributes()->Neighbourhood,
					'District'=>$item->attributes()->District,
					'City'=>$item->attributes()->City,
					'Line1'=>$item->attributes()->Line1,
					'Line2'=>$item->attributes()->Line2,
					'Line3'=>$item->attributes()->Line3,
					'Line4'=>$item->attributes()->Line4,
					'Line5'=>$item->attributes()->Line5,
					'AdminAreaName'=>$item->attributes()->AdminAreaName,
					'AdminAreaCode'=>$item->attributes()->AdminAreaCode,
					'Province'=>$item->attributes()->Province,
					'ProvinceName'=>$item->attributes()->ProvinceName,
					'ProvinceCode'=>$item->attributes()->ProvinceCode,
					'PostalCode'=>$item->attributes()->PostalCode,
					'CountryName'=>$item->attributes()->CountryName,
					'CountryIso2'=>$item->attributes()->CountryIso2,
					'CountryIso3'=>$item->attributes()->CountryIso3,
					'CountryIsoNumber'=>$item->attributes()->CountryIsoNumber,
					'SortingNumber1'=>$item->attributes()->SortingNumber1,
					'SortingNumber2'=>$item->attributes()->SortingNumber2,
					'Barcode'=>$item->attributes()->Barcode,
					'POBoxNumber'=>$item->attributes()->POBoxNumber,
					'Label'=>$item->attributes()->Label,
					'Type'=>$item->attributes()->Type,
					'DataLevel'=>$item->attributes()->DataLevel
				);
			}
		}
	}

	public function HasData()
	{
		if (!empty($this->Data))
		{
			return $this->Data;
		}
		return false;
	}
}