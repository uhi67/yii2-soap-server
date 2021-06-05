<?php /** @noinspection PhpUnused */

/**
 * @link https://github.com/uhi67/yii2-services
 * @license https://github.com/uhi67/yii2-services/blob/master/LICENSE.md
 */

namespace uhi67\services\tests\app\controllers;

use stdClass;
use uhi67\services\WebServiceAction;
//use uhi67\services\WsdlGenerator;
use yii\web\Controller;

/**
 * Class SampleApiController
 *
 * Endpoint: http://localhost:8080/sample-api?ws=1
 * WSDL: http://localhost:8080/sample-api
 * Target namespace: urn:uhi67/services/tests/app/controllers/SampleApiControllerwsdl
 */
class SampleApiController extends Controller
{
    public $enableCsrfValidation = false;

    public function actions()
    {
        /** @noinspection PhpUndefinedNamespaceInspection */
        /** @noinspection PhpUndefinedClassInspection */
        return [
            'index' => [
                'class' => WebServiceAction::class,
	            'serviceOptions' => [
	                'actor' => 'urn:uhi67/services/tests/app/controllers/SampleApiControllerwsdl',
//		            'generatorConfig' =>[
//		            	'class' => WsdlGenerator::class,
//					    'operationBodyStyle' => [
//						    'use' => WsdlGenerator::USE_LITERAL,
//						    'encodingStyle' => 'http://schemas.xmlsoap.org/soap/encoding/',
//					    ],
//		            ]
	            ],
                'classMap' => [
                    'SoapModel' => uhi67\services\tests\app\models\SoapModel::class,
                ],
            ],
        ];
    }

    /**
     * Write the description of the operation into the phpdoc comment block of the controller method.
     *
     * @param string $a
     * @return string
     * @soap
     */
    public function soapTest($a)
    {
        return get_class($a);
    }

	/**
     * Returns the value of the string argument.
     *
	 * @param string $aaa
	 * @return string
	 * @soap
	 */
	public function mirror($aaa)
	{
		//throw new \Exception('mirror='.print_r($param,true));
		return $aaa;
	}

    /**
     * @param array $params -- associative array
     * @return stdClass -- input array in object form
     * @soap
     */
    public function getObject($params)
    {
        $object = new stdClass();
        $object_result = new stdClass();
        foreach($params as $name=>$value) $object_result->$name = $value;
        $result_name = __FUNCTION__.'Result';
        $object->$result_name = $object_result;
        return $object;
    }

    /**
     * @param int $a
     * @param bool $b
     * @param string $c
     * @return \uhi67\services\tests\app\controllers\MyObject -- input values in object form
     * @soap
     * @noinspection PhpUnnecessaryFullyQualifiedNameInspection  -- dont use aliases in SOAP phpdoc blocks!
     */
    public function getObject2($a, $b, $c)
    {
        $object = new MyObject();
        $object->a = (int)$a;
        $object->b = (bool)$b;
        $object->c = $c;
        return $object;
    }

    public function actionHello()
    {
		return 'Hello World';
	}
}

/**
 * Class MyObject
 * @soap
 */
class MyObject {
    /**
     * An integer argument. Write structure element description into the comment block of the variable.
     *
     * @var int $a default 1
     * @soap
     */
    public $a=1;
    /**
     * A boolean argument
     *
     * @var bool $b
     * @soap
     */
    public $b;
    /**
     * A string argument
     *
     * @var string $c
     * @example A sample example
     * @soap
     */
    public $c;
}
