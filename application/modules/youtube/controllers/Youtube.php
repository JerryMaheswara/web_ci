<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Youtube extends MY_Controller
{

    /*****************************************************************************/
    public function __construct()
    {
        parent::__construct();

        $this->data['treeview_menu']      = controller;
        $this->data['content_sub_header'] = ucfirst(controller);
        $this->data['content']            = 'Required ID.';

        $this->load->library('library_module');

        $this->data['aside_left']  = $this->parser->parse('dashboard/_aside_left.html', $this->data, true);
        $this->data['aside_right'] = $this->parser->parse('dashboard/_aside_right.html', $this->data, true);
        $this->data['header']      = $this->parser->parse('dashboard/_header.html', $this->data, true);
        $this->data['footer']      = $this->parser->parse('dashboard/_footer.html', $this->data, true);

    }

    /*****************************************************************************/
    public function index()
    {


// $a = 'No. 74909 1321 0000099 2017';

// $rest = substr($a, -12, 7); // returns "d"

// opn($rest);exit();


//         // $this->db->select('no_serti');
//         // $this->db->select('no_serti');
//         // $this->db->order_by('no_serti', 'desc');
//         // $last_kode = $this->db->get('tbl_mui', 1, 0)->result_array();
//         // $this->db->query("SELECT MAX(no_serti) as no_serti");

//         $this->db->select('MAX(no_serti) as no_serti');
//         $last_kode = $this->db->get('tbl_mui',1,0)->result_array();
//         if ($last_kode) { 
//             $kode = substr($last_kode[0]['no_serti'], -12, 7);
//             $deco = $kode + 1;
//             $kode_otomatis = '74909 1321 '. sprintf('%07d', $deco) . ' 2017';
//         }else{
//             $kode_otomatis = "00000000000000001";
//         }
//         echo $kode_otomatis;





        $this->data['q'] = $this->input->get('q') ?: '';
        // $this->data['youtube_container'] = $this->parser->parse('youtube_container.html', $this->data, true);
        $this->data['body'] = $this->parser->parse('youtube.html', $this->data, true);

        $this->parser->parse('dashboard/_index.html', $this->data, false);
    }
    /*****************************************************************************/
    public function youtube_ajax()
    {
        $this->data['q']             = $this->input->get('q') ?: '';
        $this->data['nextPageToken'] = '';
        $this->data['prevPageToken'] = '';
        $this->data['youtube_all']   = array();
        if ($this->input->get()) {

            $option['part']       = 'id,snippet';
            $option['maxResults'] = 12;
            $option['mine']       = 'true';
            // $option['videoEmbeddable'] = 'true';
            // $option['videoType']       = 'any';
            $option['key']       = 'AIzaSyCu3LZrJT7cgu9ZBUlX1xnsOH7hy2KYM04';
            $option['q']         = $this->input->get('q') ?: '';
            $option['pageToken'] = $this->input->get('pageToken') ?: '';

            $url  = "https://www.googleapis.com/youtube/v3/search?" . http_build_query($option, 'a', '&');
            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            // $curlheader[0] = "Authorization: Bearer " . $accessToken;
            // curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheader);

            $json_response = curl_exec($curl);
            curl_close($curl);
            $youtube_all                 = json_decode($json_response, true);
            $this->data['nextPageToken'] = isset($youtube_all['nextPageToken']) ? $youtube_all['nextPageToken'] : '';
            $this->data['prevPageToken'] = isset($youtube_all['prevPageToken']) ? $youtube_all['prevPageToken'] : '';
            foreach ($youtube_all['items'] as $key => $value) {
                if ($value['id']['kind'] == 'youtube#video') {

                    $youtube_all['items'][$key]['kind']         = str_replace('youtube#', '', $value['id']['kind']);
                    $youtube_all['items'][$key]['videoId']      = isset($value['id']['videoId']) ? $value['id']['videoId'] : '';
                    $youtube_all['items'][$key]['thumbnails']   = $value['snippet']['thumbnails']['medium']['url'];
                    $youtube_all['items'][$key]['title']        = $value['snippet']['title'];
                    $youtube_all['items'][$key]['description']  = $value['snippet']['description'];
                    $youtube_all['items'][$key]['publishedAt']  = $value['snippet']['publishedAt'];
                    $youtube_all['items'][$key]['channelTitle'] = $value['snippet']['channelTitle'];
                    $youtube_all['items'][$key]['channelId']    = $value['snippet']['channelId'];
                    $youtube_all['items'][$key]['duration']    = $this->video_detail($value['id']['videoId']);
                } else {
                    unset($youtube_all['items'][$key]);
                }

                unset($youtube_all['items'][$key]['id']);
                unset($youtube_all['items'][$key]['snippet']);
            }

            // opn($youtube_all);exit();
            $this->data['youtube_all'] = array($youtube_all);
            $res['nextPageToken']      = $this->data['nextPageToken'];
            $res['prevPageToken']      = $this->data['prevPageToken'];
            $res['youtube_data']       = $this->parser->parse('youtube_data.html', $this->data, true);
            $res['youtube_container']  = $this->parser->parse('youtube_container.html', $this->data, true);

            header('Content-Type: application/json');
            echo json_encode($res, JSON_PRETTY_PRINT);
        }
    }
    /*****************************************************************************/
    public function video_detail()
    {

        $option['part'] = 'id,contentDetails';
        $option['key']  = 'AIzaSyCu3LZrJT7cgu9ZBUlX1xnsOH7hy2KYM04';
        if ($this->input->is_ajax_request()) {
            $option['id']   = $this->input->get('id') ?: '';
        }

        $arg = func_get_args();
        if (isset($arg[0])) {
            $option['id'] = $arg[0];
        }

        $url  = "https://www.googleapis.com/youtube/v3/videos?" . http_build_query($option, 'a', '&');
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // $curlheader[0] = "Authorization: Bearer " . $accessToken;
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $curlheader);

        $json_response = curl_exec($curl);
        $youtube_all   = json_decode($json_response, true);
        $duration      = '';
        foreach ($youtube_all['items'] as $key => $value) {
            $duration = $youtube_all['items'][$key]['contentDetails']['duration'];
        }
        unset($youtube_all['items']);
        // $res['duration'] =
        $res['duration'] = $this->covtime($duration);
        // opn($duration);exit();
        // if ($this->input->is_ajax_request()) {
        //     header('Content-Type: application/json');
        //     echo json_encode($res, JSON_PRETTY_PRINT);
        // }else{
        // }
        return $this->covtime($duration);
    }
    /*****************************************************************************/
    public function covtime($youtube_time)
    {
        $start = new DateTime('@0'); // Unix epoch
        $start->add(new DateInterval($youtube_time));
        if (strlen($youtube_time) > 8) {
            return $start->format('g:i:s');
        } else {
            return $start->format('i:s');
        }
    }
    /*****************************************************************************/
}

/* End of file Youtube.php */
/* Location: ./application/controllers/Youtube.php */
