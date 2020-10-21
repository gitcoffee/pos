<?php

namespace Webkul\Pos\Http\Controllers\Api;

/**
 * PosConfig controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PosConfigController extends Controller
{
    protected $config = [];

    public function getPosConfig() {
        if ( $this->posExtensionStatus() ) {
            $config_data = array(
                'top_heading' => core()->getConfigData('pos.configuration.general.heading_on_login'),
                'top_sub_heading' => core()->getConfigData('pos.configuration.general.sub_heading_on_login'),
                'footer_content' => core()->getConfigData('pos.configuration.general.footer_content'),
                'footer_note' => core()->getConfigData('pos.configuration.general.footer_note'),
                'footer_link_text' => core()->getConfigData('pos.configuration.general.footer_link_text'),
                'footer_link' => core()->getConfigData('pos.configuration.general.footer_link'),
            );

            $this->config = $config_data;
            
            return response()->json([
                'status' => true,
                'message' => '',
                'config' => $this->config
            ]);    
        } else {
            return redirect()->route(url());
        }
    }

    public function posExtensionStatus() {
        if ( core()->getConfigData('pos.configuration.general.status') ) {
            return true;
        } else {
            return false;
        }
    }

    public function getLeftNavMenus() {
        $nav_menus = array();
        foreach (config('menu.posuser') as $item) {
            $nav_menus[$item['key']] = $item;
            $nav_menus[$item['key']]['name'] = trans($item['name']);
        }
        return response()->json(['pos_menus' => $nav_menus]);
    }
}