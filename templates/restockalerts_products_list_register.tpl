<!--{*
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
 *}-->

<!--{*
 * PC商品一覧画面用の差し替えテンプレート
 *
 * @package RestockAlerts
 * @author Naohisa Minagawa
 * @version 1.0
 *}-->
<!--{if $tpl_classcat_find1[$id] || !$tpl_stock_find[$id]}-->
<div class="cartin clearfix" id="restock_dynamic_<!--{$id}-->">
	<input type="hidden" id="restock_hidden_<!--{$id}-->" name="restock_temp" value="登録" />
	<input type="text" name="email" size="40" style="<!--{$arrErr.restock_alerts|sfGetErrorColor}-->" />
	<input type="submit" name="restock" value="登録" onclick="$('#restock_hidden_<!--{$id}-->').attr('name', 'restock'); fnInCart(this.form); return false;" />
    <!--{if $arrErr.restock_alerts != ""}-->
    <br /><span class="attention"><!--{$arrErr.restock_alerts}--></span>
    <!--{/if}-->
</div>
<!--{/if}-->
