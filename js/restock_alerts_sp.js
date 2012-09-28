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
 * 規格の選択状態に応じて, フィールドを設定する.
 * product.jsの処理を上書きするために使用する。
 *
 * @package RestockAlerts
 * @author Naohisa Minagawa
 * @version 1.0
 */
function checkStock($form, product_id, classcat_id1, classcat_id2) {

    classcat_id2 = classcat_id2 ? classcat_id2 : '';

    var classcat2;

    // 商品一覧時
    if (typeof productsClassCategories != 'undefined') {
        classcat2 = productsClassCategories[product_id][classcat_id1]['#' + classcat_id2];
    }
    // 詳細表示時
    else {
        classcat2 = classCategories[classcat_id1]['#' + classcat_id2];
    }

    // 商品コード
    var $product_code_default = $form.find('[id^=product_code_default]');
    var $product_code_dynamic = $form.find('[id^=product_code_dynamic]');
    if (classcat2
        && typeof classcat2['product_code'] != 'undefined') {
        $product_code_default.hide();
        $product_code_dynamic.show();
        $product_code_dynamic.text(classcat2['product_code']);
    } else {
        $product_code_default.show();
        $product_code_dynamic.hide();
    }

    // 在庫(品切れ)
    var $cartbtn_default = $form.find('[id^=cartbtn_default]');
    var $quantity_default = $form.find('[class^=cartin_btn] dl');
    var $cartbtn_dynamic = $form.find('[id^=restock]');
    if (classcat2 && classcat2['stock_find'] === false) {
        $cartbtn_dynamic.show();
        $quantity_default.hide();
        $cartbtn_default.hide();
    } else {
        $cartbtn_dynamic.hide();
        $quantity_default.show();
        $cartbtn_default.show();
    }

    // 通常価格
    var $price01_default = $form.find('[id^=price01_default]');
    var $price01_dynamic = $form.find('[id^=price01_dynamic]');
    if (classcat2
        && typeof classcat2['price01'] != 'undefined'
        && String(classcat2['price01']).length >= 1) {

        $price01_dynamic.text(classcat2['price01']).show();
        $price01_default.hide();
    } else {
        $price01_dynamic.hide();
        $price01_default.show();
    }

    // 販売価格
    var $price02_default = $form.find('[id^=price02_default]');
    var $price02_dynamic = $form.find('[id^=price02_dynamic]');
    if (classcat2
        && typeof classcat2['price02'] != 'undefined'
        && String(classcat2['price02']).length >= 1) {

        $price02_dynamic.text(classcat2['price02']).show();
        $price02_default.hide();
    } else {
        $price02_dynamic.hide();
        $price02_default.show();
    }

    // ポイント
    var $point_default = $form.find('[id^=point_default]');
    var $point_dynamic = $form.find('[id^=point_dynamic]');
    if (classcat2
        && typeof classcat2['point'] != 'undefined'
        && String(classcat2['point']).length >= 1) {

        $point_dynamic.text(classcat2['point']).show();
        $point_default.hide();
    } else {
        $point_dynamic.hide();
        $point_default.show();
    }

    // 商品規格
    var $product_class_id_dynamic = $form.find('[id^=product_class_id]');
    if (classcat2
        && typeof classcat2['product_class_id'] != 'undefined'
        && String(classcat2['product_class_id']).length >= 1) {

        $product_class_id_dynamic.val(classcat2['product_class_id']);
    } else {
        $product_class_id_dynamic.val('');
    }
}
