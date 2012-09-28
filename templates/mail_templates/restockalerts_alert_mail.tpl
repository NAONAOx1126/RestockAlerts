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
 * 通知メールテンプレートのコアテンプレート
 *
 * @package RestockAlerts
 * @author Naohisa Minagawa
 * @version 1.0
 *}-->
<!--{$tpl_header}-->

<!--{foreach from=$restocks item="restock"}-->
<!--{$restock.product_name}--><!--{if $restock.classcategory1_name != ""}-->（<!--{$restock.classcategory1_name}--><!--{if $restock.classcategory2_name != ""}-->／<!--{$restock.classcategory2_name}--><!--{/if}-->）<!--{/if}-->

<!--{/foreach}-->

<!--{$tpl_footer}-->
