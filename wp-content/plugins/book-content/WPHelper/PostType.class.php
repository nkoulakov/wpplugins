<?php

abstract class PostType {

    protected $messages;
    protected $action = false;
    private $slug;

    public function __construct() {

        add_action('init', array($this, 'register'), 10);

        add_action('init', array($this, 'routeActions'), 20);

        add_action('save_post', array($this, 'doSavePost'), 20, 2);

        add_action('admin_enqueue_scripts', array($this, 'doEnqueueStyles'));

        add_action('admin_enqueue_scripts', array($this, 'doEnqueueScripts'));
    }
    
    public function doEnqueueStyles($hook) {

        global $post;

        if ((0 == strcmp('post-new.php', $hook) || 0 == strcmp('post.php', $hook)) && $post->post_type == $this->postType()) {
            $this->enqueueStyles();
        }
    }

    public function doEnqueueScripts($hook) {

        global $post;

        if ((0 == strcmp('post-new.php', $hook) || 0 == strcmp('post.php', $hook)) && $post->post_type == $this->postType()) {
            $this->enqueueScripts();
        }
    }

    public function enqueueStyles() {
        
    }

    public function enqueueScripts() {
        
    }

    public function doSavePost($post_id, $post) {

        if ($this->postType() !== $post->post_type)
            return;

        $this->savePost($post_id, $post);
    }

    public function savePost($post_id, $post) {
        
    }

    abstract static public function postType();

    abstract static public function args();

    public function register() {

        register_post_type($this->postType(), $this->args());

        $post_type = get_post_type_object($this->postType());

        $this->slug = $post_type->rewrite['slug'];
    }

    public function getSlug() {
        return $this->slug;
    }

    public function routeActions() {

        // Make sure the page that handles the action is the page that sent it
        if (!isset($_REQUEST['post_type']))
            return false;

        if ($this->postType() !== $_REQUEST['post_type'])
            return false;

        if (!isset($_REQUEST['threevl_wphelper_post_type_' . $this->getSlug() . '_action']))
            return false;

        $this->setAction($_REQUEST['threevl_wphelper_post_type_' . $this->getSlug() . '_action']);

        $nonce = $_REQUEST['_threevl_wphelper_post_type_' . $this->getSlug() . '_wpnonce'];

        if (!wp_verify_nonce($nonce, $this->action))
            wp_die('Invalid Nonce');

        $result = $this->doAction($this->action);

        if ($this->isAjax() && method_exists($this, 'ajaxResponse')) {

            $result = $this->ajaxResponse($this->action);

            $result['threevl_wphelper_post_type_' . $this->getSlug() . '_action'] = $this->getAction();

            die(json_encode($result));
        }
    }

    public function setAction($action) {
        $this->action = $action;
    }

    public function getAction() {
        return $this->action;
    }

    public function doAction($action) {
        
    }

    public function addMessage(Message $message) {
        $this->messages[] = $message;
    }

    public function renderMessages($echo = false) {

        $output = '';

        if (is_array($this->messages)) {
            foreach ($this->messages as $message) {
                $output .= $message->render();
            }
        }

        if (!$echo)
            return $output;

        echo $output;
    }

    public function hasError() {

        if (is_array($this->messages)) {
            foreach ($this->messages as $message) {
                if (Message::T_ERROR == $message->getType()) {
                    return true;
                }
            }
        }

        return false;
    }

    private function isAjax() {

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == "XMLHttpRequest") {

            return true;
        }

        return false;
    }

}

?>