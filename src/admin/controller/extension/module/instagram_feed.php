<?php

class ControllerExtensionModuleInstagramFeed extends Controller {
    protected $error = array();

    public function index() {
        $this->load->language('extension/module/instagram_feed');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('module_instagram_feed', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['api_key'])) {
            $data['error_api_key'] = $this->error['api_key'];
        } else {
            $data['error_api_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/instagram_feed', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/module/instagram_feed', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if (isset($this->request->post['module_instagram_feed_api_key'])) {
            $data['module_instagram_feed_api_key'] = $this->request->post['module_instagram_feed_api_key'];
        } else {
            $data['module_instagram_feed_api_key'] = $this->config->get('module_instagram_feed_api_key');
        }

        if (isset($this->request->post['module_instagram_feed_limit'])) {
            $data['module_instagram_feed_limit'] = $this->request->post['module_instagram_feed_limit'];
        } else {
            $data['module_instagram_feed_limit'] = $this->config->get('module_instagram_feed_limit');
        }

        if (isset($this->request->post['module_instagram_feed_status'])) {
            $data['module_instagram_feed_status'] = $this->request->post['module_instagram_feed_status'];
        } else {
            $data['module_instagram_feed_status'] = $this->config->get('module_instagram_feed_status');
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/instagram_feed', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/instagram_feed')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['module_instagram_feed_api_key'])) {
            $this->error['api_key'] = $this->language->get('error_api_key');
        }

        return !$this->error;
    }
}
