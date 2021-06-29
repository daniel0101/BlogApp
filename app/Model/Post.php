<?php

class Post extends AppModel{
    public $validate = array(
        'title' => array(
            'rule' => 'notBlank'
        ),
        'body' => array(
            'rule' => 'notBlank'
        )
    );

    public $actsAs = array('Acl' => array('type' => 'controlled'));

    public $belongsTo = ['User'];

    public function getPosts(){
        $params = [
            'conditions' => [
                'user_id' => AuthComponent::user('id')
            ],
            'recursive' => 1
        ];

        return $this->find('all',$params);
    }

    public function parentNode() {
        return null;
    }
}