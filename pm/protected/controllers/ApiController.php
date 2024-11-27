<?php
Yii::import('application.models.cash.*');

class ApiController extends CController {
	
	public function init() {
		parent::init();
		
		Yii::app()->attachEventHandler('onError', array($this,'handleError'));
		Yii::app()->attachEventHandler('onException', array($this,'handleError'));
	}
	
	public function handleError(CEvent $event) {
		if ($event instanceof CExceptionEvent) {
			$this->sendResponse($event->exception->statusCode, $event->exception->getMessage());
	    }
	
	    $event->handled = TRUE;
	}
	
	protected function sendResponse($status = 200, $body = '', $contentType = 'application/json') {
		// Set the status
		$statusHeader = 'HTTP/1.1 ' . $status;
		header($statusHeader);
		// Set the content type
		header('Content-type: ' . $contentType);
	
		echo CJSON::encode($body);
		Yii::app()->end();
	}
	
	public function actionValidateLogin() {
		$post = file_get_contents("php://input");
		$data = CJSON::decode($post, true);
		
		$model = new LoginForm;
		
		$model->username = $data['id'];
		$model->password = $data['password'];
		
		if ($model->validate() && $model->login()) {
			$is_valid = true;
		} else {
			$is_valid = false;
		}
		
		echo CJSON::encode(array('is_valid'=>$is_valid));
	}
	
	public function actionCreateCash() {
		//$post = file_get_contents("php://input");
		//$data = CJSON::decode($post, true);
		
		$model = new CashMaintForm();
		//$model->setAttributes($dadta, false);
		$model->setAttributes($_POST, false);
		
		$model->image_file = $_FILES["image_file"];
		
		if ($model->create($imageFile)) {
			echo CJSON::encode(array('is_success'=>true));
		} else {
			echo CJSON::encode(array('is_success'=>false, 'error_messages'=>$model->error_messages));
		}
	}
	
	public function actionUpdateCash() {
		//$post = file_get_contents("php://input");
		//$data = CJSON::decode($post, true);
		
		$model = new CashMaintForm();
		//$model->setAttributes($dadta, false);
		$model->setAttributes($_POST, false);
		
		$model->image_file = $_FILES["image_file"];
		
		if ($model->update()) {
			echo CJSON::encode(array('is_success'=>true));
		} else {
			echo CJSON::encode(array('is_success'=>false, 'error_messages'=>$model->error_messages));
		}
	}
	
	public function actionGetCashes() {
		
		$model = new CashSearchForm();
		$model->setAttributes($_GET);
		
		if (!$model->validate()) {
			throw new CHttpException(400, 'Missing parameter');
		}
		
		$searchResult = $model->search();
		echo CJSON::encode($searchResult);
	}
	
	public function actionProduct() {
		$no_jp = $_GET['no_jp'];
		
		$criteria = new CDbCriteria();
		$criteria->compare('no_jp', $no_jp);
		
		$data = ProductMaster::model()->find($criteria);
		
		header('Access-Control-Allow-Origin: *');
		echo CJSON::encode($data == null ? '' : $data);
	}
}