<?php
class CommentsController extends AppController {
    public $uses = array('Comment');
    public $components = array('api');
}