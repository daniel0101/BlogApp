<?php

App::uses('AppController', 'Controller');

class PostsController extends AppController {
    public $helpers = array('Html', 'Form');

    public function beforeFilter(){
        parent::beforeFilter();

        $this->Auth->allow('any');
    }

    /**
     * view all blog posts
     * 
     * @param null
     * @return 
     */
    public function index() {
        $posts = $this->Post->getPosts();
        $this->set('posts', $posts);
    }

    /**
     * view contents of a single blog post
     * 
     * @param null
     * @return
     */
    public function view($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid post'));
        }

        $this->Post->recursive = 1;
        $post = $this->Post->findById($id);
        if (!$post) {
            throw new NotFoundException(__('Invalid post'));
        }
        $this->set('post', $post);
    }

    /**
     * Add new blog post by authenticated user
     * 
     * @param null
     * @return
     */
    public function add() {
        if ($this->request->is('post')) {
            $this->Post->create();
            $this->request->data['Post']['user_id'] = $this->Auth->user('id');
            if ($this->Post->save($this->request->data)) {
                $this->Flash->success(__('Your post has been saved.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(__('Unable to add your post.'));
        }
    }

    /**
     * Edit existing blog post
     * 
     * @param null
     * @return
     */
    public function edit($id = null) {
        if (!$id) {
            throw new NotFoundException(__('Invalid post'));
        }
    
        $post = $this->Post->findById($id);
        if (!$post) {
            throw new NotFoundException(__('Invalid post'));
        }
    
        if ($this->request->is(array('post', 'put'))) {
            $this->Post->id = $id;
            if ($this->Post->save($this->request->data)) {
                $this->Flash->success(__('Your post has been updated.'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(__('Unable to update your post.'));
        }
    
        if (!$this->request->data) {
            $this->request->data = $post;
        }
    }

    /**
     * Delete existing blog post
     * 
     * @param null
     * @return
     */
    public function delete($id) {
        if ($this->request->is('get')) {
            throw new MethodNotAllowedException();
        }
    
        if ($this->Post->delete($id)) {
            $this->Flash->success(
                __('The post with id: %s has been deleted.', h($id))
            );
        } else {
            $this->Flash->error(
                __('The post with id: %s could not be deleted.', h($id))
            );
        }
    
        return $this->redirect(array('action' => 'index'));
    }

    /**
     * checks if post is owned by a particular user
     * 
     * @param array $post
     * @param array $user
     * @return boolean
     */
    public function isOwnedBy($post, $user) {
        return $this->field('id', array('id' => $post, 'user_id' => $user)) !== false;
    }

    /**
     * Checks if user is authorized to perform action
     * 
     * @param array $user
     * @return boolean
     */
    public function isAuthorized($user) {
        // All registered users can add posts
        if ($this->action === 'add' && $this->Auth->user('role') === 'author') {
            return true;
        }
    
        // The owner of a post can edit and delete it
        if (in_array($this->action, array('edit', 'delete'))) {
            $postId = (int) $this->request->params['pass'][0];
            if ($this->Post->isOwnedBy($postId, $user['id'])) {
                return true;
            }
        }
    
        return parent::isAuthorized($user);
    }


    public function any_action(){
        $aro = $this->Acl->Aro;
    
        // Here's all of our group info in an array we can iterate through
        $groups = array(
            0 => array(
                'alias' => 'admin'
            ),
            1 => array(
                'alias' => 'authors'
            ),
            2 => array(
                'alias' => 'readers'
            )
        );
    
        // Iterate and create ARO groups
        foreach ($groups as $data) {
            // Remember to call create() when saving in loops...
            $aro->create();
    
            // Save data
            $aro->save($data);
        }
    
        // Other action logic goes here...
    }
    
}
