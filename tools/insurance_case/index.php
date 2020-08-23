<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Формы");
?>

<?php
$APPLICATION->IncludeComponent(
    "intelgroup:vue.onlineforms",
    "",
    [],
    false
);
?>

    <div id="app" v-bind:title="blockTitle + ' дополнительный заголовок'">
        {{ message }}
        <h1 v-if="status == 1">Заголовок равно 1</h1>
        <h1 v-else-if="status == 2">Заголовок равно 2</h1>
        <h1 v-else>Заголовок равно {{ status }}</h1>

        <div v-bind:class="{ active: isActive }" v-on:click="functionName()">
            Блок с классами 1
        </div>

        <div v-bind:class="[ isActive == 3 ? 'active' : 'disabled', 'default']" @click="functionName2">
            Блок с классами 2
        </div>
    </div>

    <script>
        var app = new Vue({
            el: '#app',
            data: {
                message: 'Первое приложение на Vue',
                blockTitle: 'Динмический заголовок для блока',
                status: 1,
                isActive: 4
            },
            methods: {
                functionName() {
                    alert('Function Name 1');
                },
                functionName2: function () {
                    alert('Function Name 2');
                }
            }
        })
    </script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>