<?php
use Lucinda\STDOUT\Controller;

class UsersController extends Controller
{
    public function run(): void
    {
        $this->response->view()["test"] = "me";
    }
}