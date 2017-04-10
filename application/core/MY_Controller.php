<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    
    const AUTH_TYPE_SIMPLE = 1; //简单登录
    
    /**
     * 布局变量，默认default
     */
    var $layout = 'default';

    /**
     * 全局数据数组
     */
    var $data = array();

    /**
     * 视图变量
     */
    var $view = '';
    
    /**
     * 页面标题
     */
    var $title = '';

    function __construct() {
        parent::__construct();
    }

    /**
     * 登录校验
     */
    private function authCheck() {
        $authType = config_item('authType') ? config_item('authType') : self::AUTH_TYPE_SIMPLE;
        switch ($authType) {
            case self::AUTH_TYPE_SIMPLE:
                $this->simpleAuth();
                break;
        }
    }
    
    /**
     * 渲染页面
     */
    protected function render($view = '', $data = null) {
        $this->view = $view;
        $params = empty($data) ? $this->data : $data;
        if ($this->isAjax()) {
            $this->showJson($params);
        } else {
            $this->load->view($this->view, $params);
        }
    }

    /**
     * 全局数组赋值,最终会输出到模版里面
     * @param $key
     * @param $value
     */
    protected function assign($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * 是否为ajax请求
     */
    protected function isAjax() {
        return $this->input->is_ajax_request();
    }

    /**
     * json输出
     * @param $data
     */
    protected function showJson($data, $cors_enabled=false) {
        //允许跨域设置
        if ($cors_enabled) {
            $this->setCORS();
        }
        $charset = "utf-8";
        if ($this->input->get("charset") == "gbk") {
            $charset = "GBK";
        }
        $this->output->set_header("Content-Type:application/json; charset={$charset}");
        $callback = $this->input->get("callback");
        $jsonp = $this->input->get("jsonp");
        //字符转换
        if ($charset == "GBK") {
            $content = gbkJsonEncode($data);
        } else {
            $content = json_encode($data);
        }
        if ($callback && $this->checkFucName($callback)) {
            $this->output->append_output($callback . "(" . $content . ")");
        } elseif ($jsonp && $this->checkFucName($jsonp)) {
            $this->output->append_output("var {$jsonp}={$content}");
        } else {
            $this->output->append_output($content);
        }
    }

    /**
     * 允许跨域设置
     */
    protected function setCORS() {
        //允许跨域的域名白名单
        $allow_domains = config_item('allow_domains');
        $http_origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : NULL;
        if (in_array($http_origin, $allow_domains)) {
            header("Access-Control-Allow-Origin: $http_origin");
            header("Access-Control-Allow-Credentials: true");
            header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
            header("Access-Control-Allow-Headers: Content-Type");
        }
    }
    /**
     * 检查jsonp方法名是否合法，只允许字母、数字、点、下划线
     * @param $name
     * @return bool
     */
    protected function checkFucName($name) {
        if (preg_match('/^[a-zA-Z_\.0-9]*$/', $name)) {
            return $name;
        } else {
            return false;
        }
    }
    

    /**
     * 判断请求是POST
     * @return bool
     */
    protected function isPost() {
        return $this->input->post() !== false;
    }

    /**
     * 获取请求参数
     * @param unknown_type $key
     * @param unknown_type $default
     * @return unknown
     */
    protected function getPost($key = '', $default = '') {
        $tmp = $this->input->get_post($key);
        return is_null($tmp) ? $default : $tmp;
    }

    /**
     * 输出成功JSON格式
     * @param unknown_type $msg
     * @param unknown_type $data
     */
    protected function showSuccess($msg = '', $data = null) {
        $data = array('code' => 0, 'message' => $msg, 'data' => $data);
        $this->showJson($data);
    }

    /**
     * 输出异常JSON格式
     * @param unknown_type $msg
     * @param unknown_type $extra
     * @param unknown_type $code
     */
    protected function showError($msg = '', $data = null, $code = -1) {
        $data = array('code' => $code, 'message' => $msg, 'data' => $data);
        $this->showJson($data);
    }

    /**
     * 获取登录用户名
     */
    protected function getLoginName() {
        return isset($_SESSION[SESSION_KEY_LOGIN_NAME]) ? $_SESSION[SESSION_KEY_LOGIN_NAME] : '';
    }


    /**
     * 简单登录校验
     */
    protected function simpleAuth() {
        $loginName = $this->getLoginName();
        if ($loginName) {
            return TRUE;
        }
        // 获得控制器类名
        $ctrl = $this->router->fetch_class();
        // 如果登录失败，调转到登录页面
        if ($ctrl != 'user') {
            redirect('/user');
        }
    }
}
