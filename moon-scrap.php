<?php

/**
 * Welcome to moon-scrap-monwoo (Licence Apache-2.0).
 *
 * @wordpress-plugin
 * Plugin Name:       MoonScrap Monwoo
 * Plugin URI:        https://moonkiosk.monwoo.com/en/missions/moon-scrap_en
 * Description:       <strong>MoonScrap</strong> is a scrapper build for web scraping. Plugin done by Miguel Monwoo (service@monwoo.com)
 * Version:           0.0.1-alpha
 * Author:            Miguel Monwoo
 * Author URI:        https://miguel.monwoo.com
 * License:           Apache-2.0
 * License URI:       https://miguel.monwoo.com/c-g-u
 * Text Domain:       moon-scrap
 * Domain Path:       /languages
 * Requires at least: 5.9.2
 * Requires PHP:      7.4
 * 
 * 🌖🌖 Copyright Monwoo 2022 🌖🌖, build by Miguel Monwoo,
 * service@monwoo.com
 * 
 * This WordPress plugin will :
 * - synchronise web navigation contents with a WordPress plugin 
 * and various sub-services (Chrome extension).
 *
 * {@link https://miguel.monwoo.com Miguel Monwoo R&D}
 * 
 * Distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND
 * 
 * You must retain, in the Source form of any Derivative Works
 * that You distribute, all copyright, patent, trademark, and
 * attribution notices from the Source form of the Work.
 * 
 * If You institute patent litigation against any entity
 * alleging that the Work constitutes direct patent infringement,
 * then any patent licenses granted to You under this License for that Work 
 * shall terminate as of the date such litigation is filed.
 * 
 * 
 * @since 0.0.1
 * @package moonScrap
 * @author service@monwoo.com
 *
 */

// 🌖🌖 Common stuff and configs : 🌖🌖
namespace {
    // If this file is called directly, abort.
    if ( ! defined( 'WPINC' ) ) {
        die;
    }

    $MoonKiosk_SHOULD_DEBUG = false;
    if (defined('MoonKiosk_SHOULD_DEBUG')) {
        $MoonKiosk_SHOULD_DEBUG = constant('MoonKiosk_SHOULD_DEBUG');
    }
    // $MoonKiosk_SHOULD_DEBUG = [true, true, false];
    // $MoonKiosk_SHOULD_DEBUG = [true, true, true];
    // $MoonKiosk_SHOULD_DEBUG = true;
}

namespace MoonScrap\Monwoo {
    use Exception;
    use WA\Config\App as WaConfigApp;

    // 🌖🌖 Ensure wa-config-monwoo plugin dependency order : 🌖🌖
    if (!class_exists(WaConfigApp::class)) {
        $pluginFolder = realpath(dirname(__DIR__));
        $myPlugin = str_replace("$pluginFolder/", "", realpath(__FILE__));

        delete_option( 'wa_config_master_load_plugin_path_opt' );

        var_dump($myPlugin);
        // Move self load order at end
        $wasAtEnd = false;
        if ( strlen($myPlugin)
        && $plugins = get_option( 'active_plugins' ) ) {
            if ( false !== ($idx = array_search( $myPlugin, $plugins ) )) {
                $wasAtEnd = $idx === (count($plugins) - 1);
                array_splice( $plugins, $idx, 1 );
                // array_unshift( $plugins, $myPlugin );
                $plugins[] = $myPlugin;
                update_option( 'active_plugins', $plugins );
            }
        }
        if ($wasAtEnd) {
            throw new Exception("Missing wa-config-monwoo plugin dependency");
        } else {
            var_dump($plugins);
            echo "\n Plugin order ajusted due to Missing class 'WA\Config\App',
            please reload this page if not automatically done";
            $redirectUrl = admin_url( 'plugins.php' );
            header( "Location: $redirectUrl", true, 302 );
            exit;
        }
    }

    // 🌖🌖 moon-scrap-lite App entry point : 🌖🌖
    $current_Version = "0.0.1-alpha";
    if (class_exists(App::class)) { // another class load
        $existing_Version = App::PLUGIN_VERSION;
        $app = App::instanceByRelativePath($pluginSrcPath, -1);
        $logMsg = "$pluginSrcPath : Will not load WA\\Config\\ since
        already loaded somewhere else at version $existing_Version
        for requested version $current_Version";
        $waConfigTextDomain = /*📜*/ 'wa-config'/*📜*/;
        if ($current_Version !== App::PLUGIN_VERSION) {
            App::addCompatibilityReport(
                __("Avertissement", $waConfigTextDomain),
                "$pluginSrcPath : $current_Version. " . __(
                    "Version pre-chargé MoonScrap\\Monwoo\\ non exacte : ",
                    $waConfigTextDomain
                ) . " $existing_Version.",
            );
        }
    } else { // first load
        /**
         * This class is the main moon-scrap App instance class
         * 
         * @since 0.0.1
         * @author service@monwoo.com
         */
        class App extends WaConfigApp
        {
            const PLUGIN_VERSION = "0.0.1-alpha";
    
            /**
             * App constructor.
             *
             * @param string $siteBaseHref This web site base URL
             * @param string $pluginFile The file name of the loaded plugin
             * @param string $iPrefix The instance prefix to use for iId generations
             * @param bool|array<int, bool> $shouldDebug True if should debug 
             * or Array of 3 boolean for each debug verbosity level
             * @return void
             */
            public function __construct(
                string $siteBaseHref,
                string $pluginFile,
                string $iPrefix,
                $shouldDebug
            ) {
                WaConfigApp::__construct(
                    $siteBaseHref, $pluginFile, $iPrefix, $shouldDebug
                );
            }
        }
    }
}

// 🌖🌖 Launch moon-scrap-lite plugin : 🌖🌖
namespace {
    use MoonScrap\Monwoo\App;

    $moonScrap_plugin = new App(
        site_url(),
        __FILE__,
        'moon-scrap',
        $MoonKiosk_SHOULD_DEBUG,
    );

    $moonScrap_plugin->bootstrap();
}
