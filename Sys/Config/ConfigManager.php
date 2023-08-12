<?php

namespace Sys\Config;

use Sys\Database\DatabaseInterface;
use Sys\File\FileManager;
use Sys\Log\Logger;
use Sys\Request\RequestManager;
use Sys\SystemException;

class ConfigManager {

    /**
     * @var array
     */
    private $vars;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * Load configuration files
     * @throws SystemException
     */
	function __construct( Logger $log, DatabaseInterface $db, FileManager $files, RequestManager $request ) {
        $configPath = $files->rootDir( 'conf', 'conf.global.php' );
        $cachePath = $files->rootDir( 'conf', 'cache.global.php' );
        $log->add("Config");

        $this->vars = $this->loadVars( $configPath, $request->assetURL() );
        $this->cache = new Cache( $this->vars['auth_group'], $cachePath, $db );

        $this->setVarsFromSettingsCache();

        $this->vars['asset_url'] = $request->assetURL();
        $this->vars['base_url'] = $request->baseURL();
        $this->vars['page_url'] = $request->pageURL();
        $this->vars['root_url'] = $request->rootURL();
        $this->vars['base_page_addr'] = $request->basePageAddress();
	}

    private function setVarsFromSettingsCache() {
        if( !empty( $this->cache['settings'] ) AND is_array( $this->cache['settings'] )) {
            foreach( $this->cache['settings'] as $k => $v ) {
                $this->vars[ $k ] = $v;
            }
        }
    }

    /**
     * @param string $filename
     * @return array
     * @throws SystemException
     */
    private function loadVars( $filename, $assetURL ) {
        if( file_exists( $filename )) {
            $VARS = [
                'asset_url' => $assetURL
            ];

            include $filename;

            if( !empty( $VARS )) {
                return $VARS;
            } else {
                throw new SystemException("Конфигурационный файл пуст");
            }
        } else {
            throw new SystemException("Конфигурационный файл не найден");
        }
    }

    /**
     * Возвращает указанную переменную окружения из массива $_SERVER или если
     * в нем отсутствует, то с помощю функции getenv()
     *
     * @param	string	$key Имя переменной
     * @return	string Значение переменной ( FALSE если не найдена )
     * @since	2.2
     */
    public function getEnv( $key ) {
        $r = array();

        if( is_array( $_SERVER ) and count( $_SERVER ) and isset( $_SERVER[$key] )) {
            $r = $_SERVER[$key];
        } else {
            $r = getenv( $key );
        }

        return $r;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getVar( $key ) {
        return $this->vars[ $key ];
    }

    /**
     * @return array
     */
    public function getVars() {
        return $this->vars;
    }

    /**
     * @return Cache
     */
    public function getCache() {
        return $this->cache;
    }

    /**
     * @param int $groupId
     * @return array
     */
    public function getGroupCache( $groupId ) {
        return $this->cache['group_cache'][$groupId];
    }

    public function updateSettingsCache() {
        $this->cache->cacheSettings();
        $this->cache->save();

        $this->setVarsFromSettingsCache();
    }
}