<?php

class ControllerExtensionModuleInstagramFeed extends Controller
{
    protected $base_url = 'https://instagram.com/me?';
    protected $api_key;
    protected $limit;

    public function index()
    {
        $this->api_key = $this->config->get('module_instagram_feed_api_key');
        $this->limit = $this->config->get('module_instagram_feed_limit');

        $this->load->language('extension/module/instagram_feed');

        // cache

        $data['images'] = [];

        return $this->load->view('extension/module/instagram_feed', $data);
    }

    /**
     * @param array $query
     * @return array
     * @throws Exception
     */
    protected function client(array $query): array
    {
        $query_params = http_build_query($query);

        $handler = curl_init($this->base_url . $query_params);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($handler);
        $response_info = curl_getinfo($handler);

        if (curl_errno($handler) !== 0) {
            throw new Exception('Something went wrong');
        }

        if ($response_info['http_code'] !== 200) {
            throw new Exception('Something went wrong');
        }

        curl_close($handler);

        return json_decode($result, true);
    }
}
