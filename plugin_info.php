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
 * 再入荷通知メール送信プラグインの情報クラス.
 *
 * @package RestockAlerts
 * @author Naohisa Minagawa
 * @version 1.0
 */
class plugin_info{
    /** プラグインコード(必須)：プラグインを識別する為キーで、他のプラグインと重複しない一意な値である必要がありま. */
    static $PLUGIN_CODE       = "RestockAlerts";
    /** プラグイン名(必須)：EC-CUBE上で表示されるプラグイン名. */
    static $PLUGIN_NAME       = "再入荷通知メール送信";
    /** クラス名(必須)：プラグインのクラス（拡張子は含まない） */
    static $CLASS_NAME        = "RestockAlerts";
    /** プラグインバージョン(必須)：プラグインのバージョン. */
    static $PLUGIN_VERSION    = "1.1";
    /** 対応バージョン(必須)：対応するEC-CUBEバージョン. */
    static $COMPLIANT_VERSION = "2.12.2";
    /** 作者(必須)：プラグイン作者. */
    static $AUTHOR            = "Naohisa Minagawa";
    /** 説明(必須)：プラグインの説明. */
    static $DESCRIPTION       = "在庫切れの際にメールアドレスを登録できるようにし、該当商品が購入可能な状態になった際にメールで通知を行う機能を追加します。";
    /** プラグインURL：プラグイン毎に設定出来るURL（説明ページなど） */
    static $PLUGIN_SITE_URL   = "http://www.netlife-web.com/";
    /** フックポイント */
	static $HOOK_POINTS	= array(
		array("prefilterTransform", "prefilterTransform"), 
		array("LC_Page_Products_List_action_before", "preregisterListEmail"), 
		array("LC_Page_Products_Detail_action_before", "preregisterEmail"), 
		array("LC_Page_Products_List_action_after", "registerListEmail"), 
		array("LC_Page_Products_Detail_action_after", "registerEmail"), 
		array("LC_Page_Admin_Products_ProductClass_action_after", 'alertEmail'), 
		array("LC_Page_Admin_Products_Product_action_after", 'alertEmail'), 
		array("LC_Page_Admin_Products_UploadCSV_action_after", 'alertEmail')
	);
	/** ライセンス：プラグインに適用するライセンスを指定 */
	static $LICENSE	= "Apache License 2.0";
}
?>