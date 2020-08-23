<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main;
use Bitrix\Main\Request;
use Bitrix\Main\Context;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Localization\Loc as Loc;
use Bitrix\Main\SystemException;
use Sorevnovaniya\Tools;

class StandardComponent extends CBitrixComponent
{
    /**
     * кешируемые ключи arResult
     * @var []
     */
    protected $cacheKeys = [];
    /**
     * вохвращаемые значения
     * @var mixed
     */
    protected $returned;
    /**
     * кеш
     * @var mixed
     */
    protected $cache;

    /**
     * подключает языковые файлы
     */
    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * подготавливает входные параметры
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $result = [
            'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600,
            'CACHE_TYPE' => $params['CACHE_TYPE'] == 'N' ? $params['CACHE_TYPE'] : 'Y',
            'SHOW_TOP_MENU' => $params['SHOW_TOP_MENU'],
        ];
        return $result;
    }

    /**
     * выполняет логику работы компонента
     */
    public function executeComponent()
    {
        global $APPLICATION;
        try {
            $this->executeProlog();

            if (!$this->readDataFromCache()) {
                $this->startCache();
                $this->executeMain();
                $this->endCache();
            }
            $this->includeComponentTemplate();
            $this->executeEpilog();

            return $this->returned;
        } catch (SystemException $e) {
            ShowError($e->getMessage());
            $this->abortCache();
        }
    }

    /**
     * выполяет действия перед кешированием
     */
    protected function executeProlog()
    {
    }

    /**
     * определяет читать данные из кеша или нет
     * @return bool
     */
    protected function readDataFromCache()
    {

        if ($this->arParams['CACHE_TYPE'] == 'N') {
            return false;
        }

        $this->cache = cache::createInstance();
        $this->setCacheKeys();

        if ($this->cache->initCache($this->arParams['CACHE_TIME'], $this->cacheKeys)) {
            $this->arResult = $this->cache->getVars();

            return $this->arResult;
        } else {
            return false;
        }
    }

    /**
     * Устанавливаем ключи для кеша
     */
    protected function setCacheKeys()
    {

        $this->cacheKeys = 'standardKeys';
    }

    /**
     * кеширует ключи массива arResult
     */
    protected function startCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N') {
            return false;
        }

        $this->cache->startDataCache();

        if (is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0) {
            $this->SetResultCacheKeys($this->cacheKeys);
        }
    }

    /**
     * Основная логика компонента
     */
    protected function executeMain()
    {
        /*Логика компонента*/
    }

    /**
     * завершает кеширование
     * @return bool
     */
    protected function endCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N') {
            return false;
        }

        $this->cache->endDataCache($this->arResult);
    }

    /**
     * выполняет действия после выполения компонента
     */
    protected function executeEpilog()
    {
    }

    /**
     * прерывает кеширование
     */
    protected function abortCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N') {
            return false;
        }

        $this->cache->abortDataCache();
    }
}
