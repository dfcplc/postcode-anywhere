<?php
namespace Dfcplc\PostcodeAnywhere\InternationalBankValidation;

class Validate
{

	//Credit: Thanks to Stuart Sillitoe (http://stu.so/me) for the original PHP that these samples are based on.

	private $Key; //The key to use to authenticate to the service.
	private $IBAN; //The international bank account number to validate.
	private $Data; //Holds the results of the query

	public function __construct($Key, $IBAN)
	{
		$this->Key = $Key;
		$this->IBAN = $IBAN;
	}

	public function MakeRequest()
	{
		$url = "http://services.postcodeanywhere.co.uk/InternationalBankValidation/Interactive/Validate/v1.00/xmla.ws?";
		$url .= "&Key=" . urlencode($this->Key);
		$url .= "&IBAN=" . urlencode($this->IBAN);

		//Make the request to Postcode Anywhere and parse the XML returned
		$file = simplexml_load_file($url);

		//Check for an error, if there is one then throw an exception
		if ($file->Columns->Column->attributes()->Name == "Error")
		{
			throw new \Exception("[ID] " . $file->Rows->Row->attributes()->Error . " [DESCRIPTION] " . $file->Rows->Row->attributes()->Description . " [CAUSE] " . $file->Rows->Row->attributes()->Cause . " [RESOLUTION] " . $file->Rows->Row->attributes()->Resolution);
		}

		//Copy the data
		if ( !empty($file->Rows) )
		{
			foreach ($file->Rows->Row as $item)
			{
				 $this->Data[] = array('IsCorrect'=>$item->attributes()->IsCorrect);
			}
		}
	}

	public function HasData()
	{
		if ( !empty($this->Data) )
		{
			return $this->Data;
		}
		return false;
	}
}
