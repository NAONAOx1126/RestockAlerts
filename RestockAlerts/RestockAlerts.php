<?php
/*
 * Restock Alerts Plugin
 * Copyright (C) 2012 NetLife Inc. All Rights Reserved.
 * http://www.netlife-web.com/
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * 再入荷通知メール送信プラグインのメインクラス.
 *
 * @package RestockAlerts
 * @author Naohisa Minagawa
 * @version 1.0
 */
class RestockAlerts extends SC_Plugin_Base {
	const TBL_ALERT = "plg_restockalerts_restock_alerts";
	const COL_PRODUCT_ID = "plg_restockalerts_product_id";
	const COL_PRODUCT_CLASS_ID = "plg_restockalerts_product_class_id";
	const COL_EMAIL = "plg_restockalerts_email";
	const COL_CREATE_TIME = "plg_restockalerts_create_time";
	

    /**
     * コンストラクタ
     */
    public function __construct(array $arrSelfInfo) {
        parent::__construct($arrSelfInfo);
    }
    
    /**
     * インストール
     * installはプラグインのインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin plugin_infoを元にDBに登録されたプラグイン情報(dtb_plugin)
     * @return void
     */
    function install($arrPlugin) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        
        // テーブルの作成
        $sql = "CREATE TABLE IF NOT EXISTS `".RestockAlerts::TBL_ALERT."`(`".RestockAlerts::COL_PRODUCT_ID."` INT NOT NULL, ";
        $sql .= "`".RestockAlerts::COL_PRODUCT_CLASS_ID."` INT NOT NULL , `".RestockAlerts::COL_EMAIL."` VARCHAR( 200 ) NOT NULL, ";
        $sql .= "`".RestockAlerts::COL_CREATE_TIME."` DATETIME NOT NULL, ";
        $sql .= "PRIMARY KEY (`".RestockAlerts::COL_PRODUCT_ID."`, `".RestockAlerts::COL_PRODUCT_CLASS_ID."`, `".RestockAlerts::COL_EMAIL."`))";
        $sql .= " ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_unicode_ci";
        $objQuery->query($sql);
        
        // プラグインのテンプレートのディレクトリを生成
        $template_dir = PLUGIN_UPLOAD_REALDIR . 'RestockAlerts/templates/';
        
        // テンプレートの登録状況を確認する。
        $templates = $objQuery->select("id", "mtb_mail_tpl_path", "name = ?", array($template_dir."mail_templates/restockalerts_alert_mail.tpl"));
        if(count($templates) == 0){
	        // IDのMAXを取得
	        $templateMax = $objQuery->select("MAX(id) AS id, MAX(rank) AS rank", "mtb_mail_template");
	        if(count($templateMax) > 0){
		        $id = $templateMax[0]["id"] + 1;
		        $rank = $templateMax[0]["rank"] + 1;
	        }else{
	        	$id = "1";
	        	$rank = "0";
	        }
	        
	        // メールマスタにデータを登録
	        $sql = "INSERT INTO mtb_mail_template(id, name, rank) VALUES ('".$id."', '再入荷通知メール', '".$rank."')";
	        $objQuery->query($sql);
	        $sql = "INSERT INTO mtb_mail_tpl_path(id, name, rank) VALUES ('".$id."', '".$template_dir."mail_templates/restockalerts_alert_mail.tpl', '".$rank."')";
	        $objQuery->query($sql);
	        $sqlval = array();
	        $sqlval["template_id"] = $id;
	        $sqlval["subject"] = "再入荷のお知らせ";
	        $sqlval["header"] = "以下の商品の入荷をお知らせ致します。";
	        $sqlval["footer"] = "ご来店をお待ちしております。";
	        $sqlval["creator_id"] = "0";
	        $sqlval["del_flg"] = "0";
	        $objQuery->insert("dtb_mailtemplate", $sqlval);
	        
	        $masterData = new SC_DB_MasterData_Ex();
	        $masterData->clearCache("mtb_mail_template");
	        $masterData->clearCache("mtb_mail_tpl_path");
        }
        
        RestockAlerts::updateContents($arrPlugin);
    }
    
    function updateContents($arrPlugin){
        // ロゴ画像のコピー
        if(copy(PLUGIN_UPLOAD_REALDIR . "RestockAlerts/logo.png", PLUGIN_HTML_REALDIR . "RestockAlerts/logo.png") === false);
        
        // JS用のディレクトリの作成
        mkdir(PLUGIN_HTML_REALDIR . "RestockAlerts/js");
        
        // JSディレクトリのコピー
        SC_Utils_Ex::sfCopyDir(PLUGIN_UPLOAD_REALDIR . "RestockAlerts/js/", PLUGIN_HTML_REALDIR . "RestockAlerts/js/");
    }
    
    /**
     * アンインストール
     * uninstallはアンインストール時に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     * 
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function uninstall($arrPlugin) {
        $objQuery = SC_Query_Ex::getSingletonInstance();
        $objQuery->query("DROP TABLE IF EXISTS `".RestockAlerts::TBL_ALERT."`");

        // プラグインのテンプレートのディレクトリを生成
        $template_dir = PLUGIN_UPLOAD_REALDIR . 'RestockAlerts/templates/';
        
        // 再入荷通知のテンプレートを取得
        $templates = $objQuery->select("id", "mtb_mail_tpl_path", "name = ?", array($template_dir."mail_templates/restockalerts_alert_mail.tpl"));
        
        // メールマスタからデータを削除
        if(count($templates) > 0){
	        $sql = "DELETE FROM mtb_mail_template WHERE id = '".$templates[0]["id"]."'";
	        $objQuery->query($sql);
	        $sql = "DELETE FROM mtb_mail_tpl_path WHERE id = '".$templates[0]["id"]."'";
	        $objQuery->query($sql);
	        $sql = "DELETE FROM dtb_mailtemplate WHERE template_id = '".$templates[0]["id"]."'";
	        $objQuery->query($sql);
	        $masterData = new SC_DB_MasterData_Ex();
	        $masterData->clearCache("mtb_mail_template");
	        $masterData->clearCache("mtb_mail_tpl_path");
        }
    }
    
    /**
     * 稼働
     * enableはプラグインを有効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function enable($arrPlugin) {
        // nop
    }

    /**
     * 停止
     * disableはプラグインを無効にした際に実行されます.
     * 引数にはdtb_pluginのプラグイン情報が渡されます.
     *
     * @param array $arrPlugin プラグイン情報の連想配列(dtb_plugin)
     * @return void
     */
    function disable($arrPlugin) {
        // nop
    }
    
    function preregisterEmail($objPage) {
    	// 再入荷ボタンが押されて遷移した場合、モードをrestockに差し替え
    	if(isset($_POST["restock"])){
    		$_POST["mode"] = "restock";
    	}
    }

    function registerEmail($objPage) {
    	if($_POST["mode"] == "restock"){
    		if(!empty($_POST["email"])){
		        $objQuery = SC_Query_Ex::getSingletonInstance();
		        $restocks = $objQuery->select("*", RestockAlerts::TBL_ALERT, "`".RestockAlerts::COL_PRODUCT_ID."` = ? AND `".RestockAlerts::COL_PRODUCT_CLASS_ID."` = ? AND `".RestockAlerts::COL_EMAIL."` = ?", array($_POST["product_id"], $_POST["product_class_id"], $_POST["email"]));
		        if(count($restocks) == 0){
		        	$sqlval = array();
		        	$sqlval[RestockAlerts::COL_PRODUCT_ID] = $_POST["product_id"];
		        	$sqlval[RestockAlerts::COL_PRODUCT_CLASS_ID] = $_POST["product_class_id"];
		        	$sqlval[RestockAlerts::COL_EMAIL] = $_POST["email"];
		        	$sqlval[RestockAlerts::COL_CREATE_TIME] = date("Y-m-d H:i:s");
		        	$objQuery->insert(RestockAlerts::TBL_ALERT, $sqlval);
		        }
    			$objErr = new SC_CheckError_Ex();
    			$objErr->arrErr["restock_alerts"] = "入荷通知の登録を受け付けました。";
    			$objPage->arrErr = $objErr->arrErr;
    		}else{
    			$objErr = new SC_CheckError_Ex();
    			$objErr->arrErr["restock_alerts"] = "通知先のメールアドレスを入力して下さい。";
    			$objPage->arrErr = $objErr->arrErr;
    		}
    	}
    }

    function preregisterListEmail($objPage) {
    	// 再入荷ボタンが押されて遷移した場合、モードをrestockに差し替え
    	if(isset($_GET["restock"])){
    		$_GET["mode"] = "restock";
    		// 一覧のカート遷移を回避するため、$_REQUESTの値を変更
    		unset($_REQUEST["product_id"]);
    	}
    }

    function registerListEmail($objPage) {
    	if($_GET["mode"] == "restock"){
    		if(!empty($_GET["email"])){
		        $objQuery = SC_Query_Ex::getSingletonInstance();
		        $restocks = $objQuery->select("*", RestockAlerts::TBL_ALERT, "`".RestockAlerts::COL_PRODUCT_ID."` = ? AND `".RestockAlerts::COL_PRODUCT_CLASS_ID."` = ? AND `".RestockAlerts::COL_EMAIL."` = ?", array($_GET["product_id"], $_GET["product_class_id"], $_GET["email"]));
		        if(count($restocks) == 0){
		        	$sqlval = array();
		        	$sqlval[RestockAlerts::COL_PRODUCT_ID] = $_GET["product_id"];
		        	$sqlval[RestockAlerts::COL_PRODUCT_CLASS_ID] = $_GET["product_class_id"];
		        	$sqlval[RestockAlerts::COL_EMAIL] = $_GET["email"];
		        	$sqlval[RestockAlerts::COL_CREATE_TIME] = date("Y-m-d H:i:s");
		        	$objQuery->insert(RestockAlerts::TBL_ALERT, $sqlval);
		        }
    			$objErr = new SC_CheckError_Ex();
    			$objErr->arrErr["restock_alerts"] = "入荷通知の登録を受け付けました。";
				$js_fnOnLoad .= $objPage->lfSetSelectedData($objPage->arrProducts, $objPage->arrForm, $objErr->arrErr, $_GET["product_id"]);
    		}else{
    			$objErr = new SC_CheckError_Ex();
    			$objErr->arrErr["restock_alerts"] = "通知先のメールアドレスを入力して下さい。";
				$js_fnOnLoad .= $objPage->lfSetSelectedData($objPage->arrProducts, $objPage->arrForm, $objErr->arrErr, $_GET["product_id"]);
		        $objPage->tpl_javascript   .= 'function fnOnLoad(){' . $js_fnOnLoad . '}';
		        $objPage->tpl_onload       .= 'fnOnLoad(); ';
    		}
    	}
    }
    
    function alertEmail($objPage) {
		$objQuery = SC_Query_Ex::getSingletonInstance();
		$cols = RestockAlerts::TBL_ALERT.".*, dtb_products.name AS product_name, classcategory1.name AS classcategory1_name, classcategory2.name AS classcategory2_name";
		$from = RestockAlerts::TBL_ALERT.", dtb_products_class, dtb_products, dtb_classcategory AS classcategory1, dtb_classcategory AS classcategory2";
		$where = "`".RestockAlerts::TBL_ALERT."`.`".RestockAlerts::COL_PRODUCT_ID."` = dtb_products_class.product_id";
		$where .= " AND `".RestockAlerts::TBL_ALERT."`.`".RestockAlerts::COL_PRODUCT_CLASS_ID."` = dtb_products_class.product_class_id";
		$where .= " AND dtb_products_class.product_id = dtb_products.product_id";
		$where .= " AND dtb_products_class.classcategory_id1 = classcategory1.classcategory_id";
		$where .= " AND dtb_products_class.classcategory_id2 = classcategory2.classcategory_id";
		$where .= " AND (dtb_products_class.stock > 0 OR dtb_products_class.stock_unlimited > 0)";
        $restocks = $objQuery->select($cols, $from, $where);
        $alerts = array();
        foreach($restocks as $restock){
        	if(!is_array($alerts[$restock[RestockAlerts::COL_EMAIL]])){
        		$alerts[$restock[RestockAlerts::COL_EMAIL]] = array();
        	}
        	$alerts[$restock[RestockAlerts::COL_EMAIL]][] = $restock;
        }
        foreach($alerts as $email => $alert){
	        // プラグインのテンプレートのディレクトリを生成
	        $template_dir = PLUGIN_UPLOAD_REALDIR . 'RestockAlerts/templates/';
	        
	        // 再入荷通知のテンプレートを取得
	        $templates = $objQuery->select("id", "mtb_mail_tpl_path", "name = ?", array($template_dir."mail_templates/restockalerts_alert_mail.tpl"));
	        
	        // メールを送信
	        $objHelperMail = new SC_Helper_Mail_Ex();
	        $objPage->restocks = $alert;
        	$objHelperMail->sfSendTemplateMail($email, "", $templates[0]["id"], &$objPage);
        	
        	// メール送信をした登録を削除
        	$table = RestockAlerts::TBL_ALERT;
        	$where = RestockAlerts::COL_PRODUCT_ID." = ?";
			$where .= " AND ".RestockAlerts::COL_PRODUCT_CLASS_ID." = ?";
			$where .= " AND ".RestockAlerts::COL_EMAIL." = ?";
        	foreach($alert as $restock){
				$arrVal = array();
				$arrVal[] = $restock[RestockAlerts::COL_PRODUCT_ID];
				$arrVal[] = $restock[RestockAlerts::COL_PRODUCT_CLASS_ID];
				$arrVal[] = $restock[RestockAlerts::COL_EMAIL];
	        	$objQuery->delete($table, $where, $arrVal);
        	}
        }
    }

    /**
     * プレフィルタコールバック関数
     *
     * @param string &$source テンプレートのHTMLソース
     * @param LC_Page_Ex $objPage ページオブジェクト
     * @param string $filename テンプレートのファイル名
     * @return void
     */
    function prefilterTransform(&$source, LC_Page_Ex $objPage, $filename) {
        $objTransform = new SC_Helper_Transform($source);
        $debug = "";
        switch($objPage->arrPageLayout['device_type_id']){
            case DEVICE_TYPE_MOBILE:
		        $template_dir = PLUGIN_UPLOAD_REALDIR . 'RestockAlerts/mobile/';
            	break;
            case DEVICE_TYPE_SMARTPHONE:
		        $template_dir = PLUGIN_UPLOAD_REALDIR . 'RestockAlerts/sphone/';
                // 商品詳細画面
                if (strpos($filename, 'products/detail.tpl') !== false) {
                    $objTransform->select('div.cartin_btn', NULL, false)->insertBefore(file_get_contents($template_dir . 'restockalerts_javascript.tpl'));
                    $objTransform->select('div.cartin_btn div.attention', NULL, false)->replaceElement(file_get_contents($template_dir . 'restockalerts_products_detail_register.tpl'));
                }
            	break;
            case DEVICE_TYPE_PC:
		        $template_dir = PLUGIN_UPLOAD_REALDIR . 'RestockAlerts/templates/';
                // 商品一覧画面
                if (strpos($filename, 'products/list.tpl') !== false) {
                    $objTransform->select('div.cart_area', NULL, false)->insertBefore(file_get_contents($template_dir . 'restockalerts_javascript.tpl'));
                    $objTransform->select('div.cart_area div.attention', NULL, false)->replaceElement(file_get_contents($template_dir . 'restockalerts_products_list_register.tpl'));
                }
                // 商品詳細画面
                if (strpos($filename, 'products/detail.tpl') !== false) {
                    $objTransform->select('div.cart_area', NULL, false)->insertBefore(file_get_contents($template_dir . 'restockalerts_javascript.tpl'));
                    $objTransform->select('div.cart_area div.attention', NULL, false)->replaceElement(file_get_contents($template_dir . 'restockalerts_products_detail_register.tpl'));
                }
                break;
        }
        $source = $debug.$objTransform->getHTML();
    }
}
?>
