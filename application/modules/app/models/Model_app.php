<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_app extends CI_Model
{
    /*****************************************************************************/

    public function _get_app($limit = null, $offset = null)
    {
        $param_app['table'] = 'app';
        $app                = $this->model_generic->_get($param_app, $limit, $offset);
        // foreach ($app as $key => $value) {
        //     $value->app_tanggal = date('d M Y', strtotime($value->app_tanggal));
        // }
        return $app;
    }

    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/

    public function _subscribe_status()
    {
        $arg = func_get_args();
        if (isset($arg[0]) && isset($arg[1])) {
            $param_subscribe['organisation_id'] = $arg[0];
            $param_subscribe['app_id']          = $arg[1];
            $param_subscribe['table']           = 'subscribe';
            $this->db->where('organisation_id', $param_subscribe['organisation_id']);
            $this->db->where('app_id', $param_subscribe['app_id']);
            $subscribe = $this->model_generic->_get($param_subscribe);
            if ($subscribe) {
                foreach ($subscribe as $value) {
                    switch ($value->subscribe_status) {
                        case 1:
                            $value->subscribe_status_name  = 'subscribed';
                            $value->subscribe_status_icon  = 'check-square-o';
                            $value->subscribe_status_color = 'success';
                            $value->subscribe_status_check = 'hidden destroy';
                            break;
                        case 2:
                            $value->subscribe_status_name  = 'unsubscribed';
                            $value->subscribe_status_icon  = 'square-o';
                            $value->subscribe_status_color = 'danger';
                            $value->subscribe_status_check = '';
                            break;
                        case 3:
                            $value->subscribe_status_name  = 'pending';
                            $value->subscribe_status_icon  = 'check-square-o';
                            $value->subscribe_status_color = 'warning';
                            $value->subscribe_status_check = '';
                            break;
                        case 4:
                            $value->subscribe_status_name  = 'cleaned';
                            $value->subscribe_status_icon  = 'check-square-o';
                            $value->subscribe_status_color = 'primary';
                            $value->subscribe_status_check = '';
                            break;
                    }
                }
                // opn($subscribe);exit();
                return $subscribe;
            }
            $default['subscribe_status_name']  = 'unsubscribed';
            $default['subscribe_status_icon']  = 'square-o';
            $default['subscribe_status_color'] = 'danger';
            $default['subscribe_status_check'] = '';
            return array($default);
        }
    }
    /*****************************************************************************/
    # subscribe status:
    # 1 = subscribed
    # 2 = unsubscribed
    # 3 = pending
    # 4 = cleaned
    /*****************************************************************************/
    # A Double US = AWS = Amazone Web Service
    /*****************************************************************************/
}

/* End of file Model_app.php */
/* Location: ./application/modules/app/model/Model_app.php */
