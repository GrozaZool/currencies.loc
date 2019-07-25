<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

use linslin\yii2\curl;


class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
		$curl = new curl\Curl();

		$exmo = json_decode($curl->get('https://api.exmo.com/v1/ticker/'));
		$bittrex = json_decode($curl->get('https://api.bittrex.com/api/v1.1/public/getmarketsummaries'));
		$poloniex = json_decode($curl->get('https://poloniex.com/public?command=returnTicker'));

		if ($curl->errorCode === null) {
			/**/
		}

		$data = array(
			
			"BTC" => array(
				"to"		=>	"USD",
				"rialto"	=>	array(
					"high" => $this->find_value( array(
						"exmo"		=>	$exmo->BTC_USD->high,
						"poloniex"	=>	$poloniex->USDT_BTC->high24hr,
						"bittrex"	=>	$this->bittrex($bittrex->result , "USD-BTC")->High,
					), "max"),
					"low" => $this->find_value( array(
						"exmo"		=>	$exmo->BTC_USD->low,
						"poloniex"	=>	$poloniex->USDT_BTC->low24hr,
						"bittrex"	=>	$this->bittrex($bittrex->result , "USD-BTC")->Low,
					), "min"),
				),
			),
			
			"LTC" => array(
				"to"		=>	"USD",
				"rialto"	=>	array(
					"high" => $this->find_value( array(
						"exmo"		=>	$exmo->LTC_USD->high,
						"poloniex"	=>	$poloniex->USDT_LTC->high24hr,
						"bittrex"	=>	$this->bittrex($bittrex->result , "USD-LTC")->High,
					), "max"),
					"low" => $this->find_value( array(
						"exmo"		=>	$exmo->LTC_USD->low,
						"poloniex"	=>	$poloniex->USDT_LTC->low24hr,
						"bittrex"	=>	$this->bittrex($bittrex->result , "USD-LTC")->Low,
					), "min"),
				),
			),
			
			"ETC" => array(
				"to"		=>	"USD",
				"rialto"	=>	array(
					"high" => $this->find_value( array(
						"exmo"		=>	$exmo->ETC_USD->high,
						"poloniex"	=>	$poloniex->USDT_ETC->high24hr,
						"bittrex"	=>	$this->bittrex($bittrex->result , "USD-ETC")->High,
					), "max"),
					"low" => $this->find_value( array(
						"exmo"		=>	$exmo->ETC_USD->low,
						"poloniex"	=>	$poloniex->USDT_ETC->low24hr,
						"bittrex"	=>	$this->bittrex($bittrex->result , "USD-ETC")->Low,
					), "min"),
				),
			),
			"ETH" => array(
				"to"		=>	"USD",
				"rialto"	=>	array(
					"high" => $this->find_value( array(
						"exmo"		=>	$exmo->ETH_USD->high,
						"poloniex"	=>	$poloniex->USDT_ETH->high24hr,
						"bittrex"	=>	$this->bittrex($bittrex->result , "USD-ETH")->High,
					), "max"),
					"low" => $this->find_value( array(
						"exmo"		=>	$exmo->ETH_USD->low,
						"poloniex"	=>	$poloniex->USDT_ETH->low24hr,
						"bittrex"	=>	$this->bittrex($bittrex->result , "USD-ETH")->Low,
					), "min"),
				),
			),
			"ZEC" => array(
				"to"		=>	"USD",
				"rialto"	=>	array(
					"high" => $this->find_value( array(
						"exmo"		=>	$exmo->ZEC_USD->high,
						"poloniex"	=>	$poloniex->USDT_ZEC->high24hr,
						"bittrex"	=>	$this->bittrex($bittrex->result , "USD-ZEC")->High,
					), "max"),
					"low" => $this->find_value( array(
						"exmo"		=>	$exmo->ZEC_USD->low,
						"poloniex"	=>	$poloniex->USDT_ZEC->low24hr,
						"bittrex"	=>	$this->bittrex($bittrex->result , "USD-ZEC")->Low,
					), "min"),
				),
			),

		);

		return $this->render('index', [
            // 'model' => $model,
            'data' => $data,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
	
	public function find_value($array, $mode){
		if (!$array){
			return array("-","");
		}

		if ($mode == "min"){
			$k = min($array);
		}
		elseif ($mode == "max"){
			$k = max($array);
		}
		if ($k){
			$ar = array_keys($array, $k);
		
			if ( count($ar) > 1 ){
				$return[] = implode(",", $ar);
			}else{
				$return[] = $ar[0];
			}

			$return[] = $k;
			return $return;
		}
	}

	public function bittrex($array, $curr){
		$item = null;
		foreach($array as $arr) {
			if ($curr == $arr->MarketName) {
				$item = $arr;
				return $item;
				// break;
			}
		}
	}
}
