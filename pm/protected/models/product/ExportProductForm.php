<?php
require_once 'protected/extensions/PHPExcel/CachedObjectStorageFactory.php';
require_once 'protected/extensions/PHPExcel/Settings.php';
class ExportProductForm extends CFormModel {
	
	public function export() {
		// Find all products
		$criteria = new CDbCriteria();
		$criteria->order = 'prod_sn';
		
		//$criteria->condition = "prod_sn = '3716'"; // testing
		
		$model = ProductMaster::model();
		$model->setDbCriteria($criteria);
		
		$products = $model->findAll();
		
		$this->generateExcel($products);
	}
	
	private function generateExcel($products) {
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '8MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
	
		$columnNames = array(
				'customer',
				'prod_sn',
				'status',
				'no_jp',
				'factory_no',
				'made',
				'model',
				'model_no',
				'year',
				'item_group',
				'material',
				'product_desc',
				'product_desc_ch',
				'product_desc_jp',
				'accessory_remark',
				'company_remark',
				'pcs',
				'colour',
				'colour_no',
				'supplier',
				'molding',
				'moq',
				'cost',
				'kaito',
				'other',
				'purchase_cost',
				'business_price',
				'auction_price',
				'kaito_price',
				'buy_date',
				'receive_date',
				'factory_date',
				'pack_remark',
				'order_date',
				'progress',
				'receive_model_date',
				'person_in_charge',
				'state',
				'ship_date',
				'market_research_price',
				'yahoo_produce',
				'produce_status',
				'is_monopoly'
		);
	
		$dateColumnNames = array(
				'buy_date',
				'receive_date',
				'factory_date',
				'order_date',
				'receive_model_date',
				'ship_date'
		);
	
		$roleMatrix = Yii::app()->user->getState('role_matrix');
		foreach ($columnNames as $idx=>$columnName) {
			if (!GlobalFunction::checkPrivilege($roleMatrix, 'product_master', $columnName)) {
				unset($columnNames[$idx]);
			}
		}
	
		// Output to excel
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
	
		// Set properties
		$objPHPExcel->getProperties()->setCreator("BM AUTO")
		->setLastModifiedBy("BM AUTO")
		->setTitle("Product");
	
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
	
		// Header
		$rowNo = 1;

		$i = 0;
		foreach ($columnNames as $columnName) {
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, Yii::t('product_message', $columnName));
				
			if (in_array($columnName, $dateColumnNames)) {
				$sheet->getStyleByColumnAndRow($i-1)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			}
		}
	
		foreach($products as $product) {
			$i = 0;
			$rowNo++;
			foreach ($columnNames as $columnName) {
				if ($columnName == 'status') {
					$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['status'] == 'A' ? 'OK' : '');
				} else if ($columnName == 'is_monopoly') {
					$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['is_monopoly'] == 0 ? 'No' : 'Yes');
				} else if (in_array($columnName, $dateColumnNames)) {
					$sheet->setCellValueByColumnAndRow($i++, $rowNo, ExportProductForm::strToExcelDate($product[$columnName]));
				} else {
					$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product[$columnName]);
				}
			}
		}
	
		header("Content-type:application/vnd.ms-excel;charset=utf8");
		header('Content-Disposition: attachment;filename="product.xls"');
		header('Cache-Control: max-age=0');
	
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	public function generateExcel_bak($products) {
		$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '8MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		
		// Output to excel
		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();
		
		// Set properties
		$objPHPExcel->getProperties()->setCreator("BM AUTO")
		->setLastModifiedBy("BM AUTO")
		->setTitle("Product");
		
		$sheet = $objPHPExcel->setActiveSheetIndex(0);
		
		// Header
		$i = 0;
		$sheet->setCellValueByColumnAndRow($i++, 1, '客戶')
		->setCellValueByColumnAndRow($i++, 1, '產品S/N')
		->setCellValueByColumnAndRow($i++, 1, 'STATUS')
		->setCellValueByColumnAndRow($i++, 1, '品番')
		->setCellValueByColumnAndRow($i++, 1, '工廠編號')
		->setCellValueByColumnAndRow($i++, 1, '車種')
		->setCellValueByColumnAndRow($i++, 1, '車型')
		->setCellValueByColumnAndRow($i++, 1, '型號')
		->setCellValueByColumnAndRow($i++, 1, '年份')
		->setCellValueByColumnAndRow($i++, 1, '商品類別')
		->setCellValueByColumnAndRow($i++, 1, '材質')
		->setCellValueByColumnAndRow($i++, 1, '商品名EN')
		->setCellValueByColumnAndRow($i++, 1, '商品名CH')
		->setCellValueByColumnAndRow($i++, 1, '商品名JP')
		->setCellValueByColumnAndRow($i++, 1, '配件備忘')
		->setCellValueByColumnAndRow($i++, 1, '公司內部備忘')
		->setCellValueByColumnAndRow($i++, 1, 'PCS')
		->setCellValueByColumnAndRow($i++, 1, '顏色')
		->setCellValueByColumnAndRow($i++, 1, '顏色編號')
		->setCellValueByColumnAndRow($i++, 1, '供應商')
		->setCellValueByColumnAndRow($i++, 1, '模具費')
		->setCellValueByColumnAndRow($i++, 1, '最低起訂量')
		->setCellValueByColumnAndRow($i++, 1, '供应商報價')
		->setCellValueByColumnAndRow($i++, 1, '海渡價')
		->setCellValueByColumnAndRow($i++, 1, '批发价')
		->setCellValueByColumnAndRow($i++, 1, '原件樣品採購價')
		->setCellValueByColumnAndRow($i++, 1, Yii::t('product_message', business_price))
		->setCellValueByColumnAndRow($i++, 1, Yii::t('product_message', auction_price))
		->setCellValueByColumnAndRow($i++, 1, Yii::t('product_message', kaito_price))
		->setCellValueByColumnAndRow($i++, 1, '訂原件時間')
		->setCellValueByColumnAndRow($i++, 1, '原件收到日期')
		->setCellValueByColumnAndRow($i++, 1, '原件到廠日期')
		->setCellValueByColumnAndRow($i++, 1, '包裝备注')
		->setCellValueByColumnAndRow($i++, 1, '下單日期')
		->setCellValueByColumnAndRow($i++, 1, '开发進度及情况')
		->setCellValueByColumnAndRow($i++, 1, '寄往對車日期')
		->setCellValueByColumnAndRow($i++, 1, '對車負責人')
		->setCellValueByColumnAndRow($i++, 1, '對車情況')
		->setCellValueByColumnAndRow($i++, 1, '出货日期')
		->setCellValueByColumnAndRow($i++, 1, '市场调查的价格')
		->setCellValueByColumnAndRow($i++, 1, 'YAHOO出品')
		->setCellValueByColumnAndRow($i++, 1, '生產狀態')
		->setCellValueByColumnAndRow($i++, 1, Yii::t('product_message', 'is_monopoly'))
		;
		
		$sheet->getStyle('AD')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
		$sheet->getStyle('AE')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
		$sheet->getStyle('AF')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
		$sheet->getStyle('AH')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
		$sheet->getStyle('AJ')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
		$sheet->getStyle('AM')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
		
		
		$rowNo = 1;
		foreach($products as $product) {
			$i = 0;
			$rowNo++;
				
			/* $sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['customer'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['prod_sn'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['status'] == 'A' ? 'OK' : '')
			->setCellValueByColumnAndRow($i++, $rowNo, $product['no_jp'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['factory_no'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['made'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['model'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['model_no'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['year'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['item_group'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['material'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['product_desc'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['product_desc_ch'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['product_desc_jp'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['accessory_remark'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['company_remark'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['pcs'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['colour'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['colour_no'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['supplier'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['molding'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['moq'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['cost'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['kaito'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['other'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['purchase_cost']); */
			
			// setCellValueByColumnAndRow(): support formula
			// setCellValueExplicitByColumnAndRow(): not support formula (for handling the field value with prefix "=")
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['customer']);
			$sheet ->setCellValueByColumnAndRow($i++, $rowNo, $product['prod_sn']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['status'] == 'A' ? 'OK' : '');
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['no_jp']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['factory_no']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['made']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['model']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['model_no']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['year']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['item_group']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['material']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['product_desc']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['product_desc_ch']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['product_desc_jp']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['accessory_remark']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['company_remark']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['pcs']);
			$sheet->setCellValueExplicitByColumnAndRow($i++, $rowNo, $product['colour']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['colour_no']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['supplier']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['molding']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['moq']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['cost']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['kaito']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['other']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['purchase_cost']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['business_price']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['auction_price']);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['kaito_price']);
				
			//$sheet->getStyleByColumnAndRow($i, $rowNo)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, ExportProductForm::strToExcelDate($product['buy_date']));
				
			//$sheet->getStyleByColumnAndRow($i, $rowNo)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, ExportProductForm::strToExcelDate($product['receive_date']));
				
			//$sheet->getStyleByColumnAndRow($i, $rowNo)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, ExportProductForm::strToExcelDate($product['factory_date']));
				
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['pack_remark']);
				
			//$sheet->getStyleByColumnAndRow($i, $rowNo)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, ExportProductForm::strToExcelDate($product['order_date']));
				
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['progress']);
				
			//$sheet->getStyleByColumnAndRow($i, $rowNo)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, ExportProductForm::strToExcelDate($product['receive_model_date']));
				
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['person_in_charge'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['state']);
				
			//$sheet->getStyleByColumnAndRow($i, $rowNo)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, ExportProductForm::strToExcelDate($product['ship_date']));
				
			$sheet->setCellValueByColumnAndRow($i++, $rowNo, $product['market_research_price'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['yahoo_produce'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['produce_status'])
			->setCellValueByColumnAndRow($i++, $rowNo, $product['is_monopoly'] == 0 ? 'No' : 'Yes');
		
			/* $sheet->getStyle('Y'.$rowNo)
			 ->getNumberFormat()
			->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->getStyle('Z'.$rowNo)
			->getNumberFormat()
			->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->getStyle('AC'.$rowNo)
			->getNumberFormat()
			->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->getStyle('AE'.$rowNo)
			->getNumberFormat()
			->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->getStyle('AG'.$rowNo)
			->getNumberFormat()
			->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
			$sheet->getStyle('AJ'.$rowNo)
			->getNumberFormat()
			->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2); */
		}
		
		header("Content-type:application/vnd.ms-excel;charset=euc");
		header('Content-Disposition: attachment;filename="product.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}
	
	private static function strToExcelDate($dateStr) {
		if ($dateStr != NULL && $dateStr != '') {
			//return PHPExcel_Shared_Date::stringToExcel($dateStr);
			$date = strtotime($dateStr);
			return date('d-m-Y', $date);
		}
		else {
			return NULL;
		}
	}
}