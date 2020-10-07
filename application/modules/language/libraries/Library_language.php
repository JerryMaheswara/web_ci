<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Library_language
{
    protected $ci;
    /*****************************************************************************/
    /*****************************************************************************/
    /*****************************************************************************/

    public function __construct()
    {
        $this->ci = &get_instance();

        $site_lang                   = $this->site_lang();
        $this->ci->data['site_lang'] = $site_lang;

        $current_language                   = $this->current_language();
        $this->ci->data['current_language'] = $current_language;

        /****************************************************** translate ----- begin ****/
        $param_site_term['table'] = 'site_term';
        $site_term                = $this->ci->model_generic->_get($param_site_term);
        foreach ($site_term as $key => $value) {
            $this->ci->data['m_' . $value->site_term_slug] = $value->site_term_name;
        }
        if (isset($this->ci->session->userdata['site_language_id'])) {
            // $this->ci->data['site_language_id'] = $this->ci->session->userdata['site_language_id'];
            $site_language_id         = $this->ci->session->userdata['site_language_id'];
            $param_translate['table'] = 'site_translate';
            $this->ci->db->where('site_translate.site_language_id', $site_language_id);
            $this->ci->db->join('site_term', 'site_term.site_term_id = site_translate.site_term_id', 'left');
            $define = $this->ci->model_generic->_get($param_translate);
            if (false == empty($define)) {
                foreach ($define as $key => $value) {
                    $this->ci->data['m_' . $value->site_term_slug] = $value->site_translate_translation;
                }
            }
        } else {
            // $this->ci->data['site_language_id'] = '';
            $param_site_term['table'] = 'site_term';
            $site_term                = $this->ci->model_generic->_get($param_site_term);
            foreach ($site_term as $key => $value) {
                $this->ci->data['m_' . $value->site_term_slug] = $value->site_term_name;
            }
        }
        /****************************************************** translate ----- end ****/

    }

    /*****************************************************************************/
    /*****************************************************************************/
    public function site_lang()
    {
        $param['table'] = 'site_language';
        $this->ci->db->join('language', 'language.language_id = site_language.language_id', 'left');
        $site_language = $this->ci->model_generic->_get($param);
        foreach ($site_language as $key => $value) {
            $value->site_lang_id = $value->site_language_id;
        }
        // $site_language = json_encode($site_language, JSON_PRETTY_PRINT);
        // header('Content-Type: application/json');
        // echo $site_language;
        // opn($site_language);exit();
        return $site_language;

    }
    /*****************************************************************************/
    /*****************************************************************************/

    public function current_language()
    {
        if (isset($_SESSION['site_language_id'])) {
            $site_language_id             = $this->ci->session->userdata['site_language_id'];
            $param_site_language['table'] = 'site_language';
            $this->ci->db->join('language', 'language.language_id = site_language.language_id', 'left');
            $this->ci->db->where('site_language_id', $site_language_id);
            $current_language = $this->ci->model_generic->_get($param_site_language);
            return $current_language;
        } else {
            $array['site_language_id']      = null;
            $array['language_culture_name'] = 'en';
            $array['country_iso_short']     = 'us';
            $array['language_id']           = '50';
            $current_language[]             = $array;
            // opn($current_language);exit();
            return $current_language;
        }
    }
    /*****************************************************************************/
    /*****************************************************************************/

}

/* End of file Library_language.php */
/* Location: ./application/modules/language/libraries/Library_language.php */
