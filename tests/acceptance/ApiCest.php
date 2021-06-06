<?php /** @noinspection PhpUnused */

namespace acceptance;

use AcceptanceTester;
use SoapClient;
use SoapFault;

class ApiCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
        $I->sendGET('sample-api');
        $I->seeResponseCodeIs(200);
    }

	/**
	 * Acceptance test runs soap call thru a web server.
     * Please start web server on port 8080, e.g. `php tests/app/yii serve`
	 *
	 * @throws SoapFault
	 */
	function mirrorWsTest(AcceptanceTester $I) {
		$param = 13;
		$wsdl = 'http://localhost:8080/sample-api';
		$method = 'mirror';

		$client = new SoapClient($wsdl, [
			'cache_wsdl'=>WSDL_CACHE_NONE,
			'cache_wsdl_ttl'=>0,
//            'trace' => true,
		]);

		$soapResult = $client->__soapCall($method, ['parameters'=>$param], ['exceptions' => 1]);
        codecept_debug('Result='.print_r($soapResult, true));

		$I->assertFalse(is_soap_fault($soapResult));
        $expected = 13;
		$I->assertEquals($expected, $soapResult);

//		$response = $client->__getLastResponse();   // turn on trace first
//        codecept_debug('Response='.print_r($response, true));

	}

    /**
     * Acceptance test runs soap call thru a web server.
     * Please start web server on port 8080, e.g. `php tests/app/yii serve`
     *
     * @throws SoapFault
     */
    function geStdClassWsTest(AcceptanceTester $I) {
        $wsdl = 'http://localhost:8080/sample-api';
        $method = 'getStdClass';
        $arrayValue = ['alma', 'banán', 'citrom'];

        $client = new SoapClient($wsdl, [
            'cache_wsdl'=>WSDL_CACHE_NONE,
            'cache_wsdl_ttl'=>0,
            'trace' => true,
        ]);

        $soapResult = $client->__soapCall($method, ['parameters'=>$arrayValue], ['exceptions' => 0]);
        $request = $client->__getLastRequest();   // turn on trace first
        codecept_debug('Request='.print_r($request, true));

        $I->assertFalse(is_soap_fault($soapResult));
		$response = $client->__getLastResponse();   // turn on trace first
        codecept_debug('Response='.print_r($response, true));

        codecept_debug('Result='.print_r($soapResult, true));

        $expected = new \stdClass();
        $expected->arr = $arrayValue;
        $I->assertEquals($expected, $soapResult);

        $expectedRequest = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope 
        xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
        xmlns:ns1="urn:uhi67/services/tests/app/controllers/SampleApiControllerwsdl" 
        xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
        xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" 
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
        SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
>
    <SOAP-ENV:Body>
        <ns1:getStdClass>
            <a SOAP-ENC:arrayType="xsd:string[3]" xsi:type="SOAP-ENC:Array">
                <item xsi:type="xsd:string">alma</item>
                <item xsi:type="xsd:string">banán</item>
                <item xsi:type="xsd:string">citrom</item>
            </a>
        </ns1:getStdClass>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EOT;

        $expectedResponse = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope 
        xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" 
        xmlns:ns1="urn:uhi67/services/tests/app/controllers/SampleApiControllerwsdl" 
        xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" 
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
        SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
>
    <SOAP-ENV:Body>
        <ns1:getStdClassResponse>
            <return xsi:type="SOAP-ENC:Struct">
                <arr SOAP-ENC:arrayType="xsd:string[3]" xsi:type="SOAP-ENC:Array">
                    <item xsi:type="xsd:string">alma</item>
                    <item xsi:type="xsd:string">banán</item>
                    <item xsi:type="xsd:string">citrom</item>
                </arr>
            </return>
        </ns1:getStdClassResponse>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
EOT;

    }
}
