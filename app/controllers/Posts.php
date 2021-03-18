<?php


class Posts extends Controller
{

    public function __construct()
    {
        if(!isLoggedIn()){
            redirect('users/login');
        }
        $this->postModel = $this->loadModel('Post');
        $this->userModel = $this->loadModel('User');
    }

    public function index(){
        $posts = $this->postModel->getPosts();
        $data = [
            'posts' => $posts
        ];
        $this->loadView('posts/index', $data);
    }

    public function add(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'user_id' => $_SESSION['user_id'],
                'title_err' => '',
                'body_err' => ''
            ];

            if(empty($data['title'])){
                $data['title_err'] = 'Please enter title';
            }

            if(empty($data['body'])){
                $data['body_err'] = 'Please enter body text';
            }

            if(empty($data['title_err']) && empty($data['body_err'])){
                if($this->postModel->addPost($data)){
                    doFlash('post_message', 'Post Added');
                    redirect('posts');
                }else{
                    die('Something went wrong');
                }
            }else{
                $this->loadView('posts/add', $data);
            }
        }else{
            $data = [
                'title' => '',
                'body' => ''
            ];
            $this->loadView('posts/add', $data);
        }

    }

    public function edit($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'id' => $id,
                'title_err' => '',
                'body_err' => ''
            ];

            if(empty($data['title'])){
                $data['title_err'] = 'Please enter title';
            }

            if(empty($data['body'])){
                $data['body_err'] = 'Please enter body text';
            }

            if(empty($data['title_err']) && empty($data['body_err'])){
                if($this->postModel->updatePost($data)){
                    doFlash('post_message', 'Post Updated');
                    redirect('posts');
                }else{
                    die('Something went wrong');
                }
            }else{
                $this->loadView('posts/edit', $data);
            }
        }else{
            $post = $this->postModel->getPostById($id);
            if($post['user_id'] != $_SESSION['user_id']){
                redirect('posts');
            }
            $data = [
                'id' => $id,
                'title' => $post['title'],
                'body' => $post['body']
            ];
            $this->loadView('posts/edit', $data);
        }

    }

    public function show($id){
        $post = $this->postModel->getPostById($id);
        $user = $this->userModel->findUserById($post['user_id']);
        $data = [
            'post' => $post,
            'user' => $user
        ];
        $this->loadView('posts/show', $data);
    }

    public function delete($id){

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $post = $this->postModel->getPostById($id);
            if($post['user_id'] != $_SESSION['user_id']){
                redirect('posts');
            }
            if($this->postModel->deletePost($id)){
                doFlash('post_message', 'Post removed');
                redirect('posts');
            }else{
                die('Something went wrong');
            }
        }else{
            redirect('posts');
        }

    }
}