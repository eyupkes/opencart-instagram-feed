<?php

class ControllerExtensionModuleInstagramFeed extends Controller
{
    const MEDIA_LIST_URL = 'https://graph.instagram.com/me/media?';
    const MEDIA_INFO_URL = 'https://graph.instagram.com/{media_id}?';

    protected $access_token;
    protected $limit;

    public function index()
    {
        $this->access_token = $this->config->get('module_instagram_feed_api_key');
        $this->limit = $this->config->get('module_instagram_feed_limit');

        $this->load->language('extension/module/instagram_feed');

        // cache

        $data['images'] = $this->getImages();

        return $this->load->view('extension/module/instagram_feed', $data);
    }

    /**
     * @return array
     */
    protected function getImages(): array
    {
        $images = [];

        $media_list = $this->getMediaList();

        foreach ($media_list as $media) {
            $images[] = $this->getMediaInfo($media);
        }

        return $images;
    }

    /**
     * @return array
     */
    protected function getMediaList(): array
    {
        $media_list = [];

        try {
            $media_list = $this->client(self::MEDIA_LIST_URL);
        } catch (Exception $e) {

        }

        return array_slice($media_list['data'], 0, $this->limit);
    }

    /**
     * @param array $media
     * @return array
     */
    protected function getMediaInfo(array $media): array
    {
        $media_info = [];

        try {
            $media_info_url = str_replace('{media_id}', $media['id'], self::MEDIA_INFO_URL);
            $media_info = $this->client($media_info_url, ['caption', 'media_url', 'permalink', 'thumbnail_url', 'timestamp']);
        } catch (Exception $e) {

        }

        return $media_info;
    }

    /**
     * @param string $url
     * @param array $fields
     * @return array
     * @throws Exception
     */
    protected function client(string $url, array $fields = null): array
    {
        $query_params = http_build_query([
            'fields' => $fields ? implode(',', $fields) : $fields,
            'access_token' => $this->access_token,
        ]);

        $handler = curl_init($url . $query_params);
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
