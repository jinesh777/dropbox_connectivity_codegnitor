<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class dropboxs extends CI_Controller {
		private $header = array();
   		private $user = array();
		public function __construct() {
		parent::__construct();
		$this -> load -> library('session');
		$this -> load -> library('common');
		$this -> load -> helper('url');
	}
	// in constant defing dropbox key & secret 
	public function request_dropbox_user()
	{
		$params['key'] =DROPBOXKEY;
		$params['secret'] =DROPBOXTOKEN;
		$this->load->library('dropbox', $params);
		$data = $this->dropbox->get_request_token(base_url()."dropboxs/access_dropbox_user");
		$this->session->set_userdata('token_secret', $data['token_secret']);
		redirect($data['redirect']);
	}
	//This method should not be called directly, it will be called after 
    //the user approves your application and dropbox redirects to it
	public function access_dropbox_user()
	{	
		$params['key'] =DROPBOXKEY;
		$params['secret'] =DROPBOXTOKEN;
		$this->load->library('dropbox', $params);		
		$oauth = $this->dropbox->get_access_token($this->session->userdata('token_secret'));
		$this->session->set_userdata('oauth_token', $oauth['oauth_token']);
		$this->session->set_userdata('oauth_token_secret', $oauth['oauth_token_secret']);
        redirect('dropboxs/access_dropbox_userInfo');
	}
	//Once your application is approved you can proceed to load the library
    //with the access token data stored in the session. If you see your account
    //information printed out then you have successfully authenticated with
    //dropbox and can use the library to interact with your account.
	public function access_dropbox_userInfo()
	{
		$params['key'] =DROPBOXKEY;
		$params['secret'] =DROPBOXTOKEN;
		$params['access'] = array('oauth_token'=>urlencode($this->session->userdata('oauth_token')),
								  'oauth_token_secret'=>urlencode($this->session->userdata('oauth_token_secret')));	
		$this->load->library('dropbox', $params);
      	$metaData = $this->dropbox->metaData('');
		$contents=$metaData->contents;
		$files="";
		$folder="";
		foreach ($contents as  $value) {
			$file_parts = pathinfo($value->path);
			$cool_extensions = array('jpg','png','pdf','jpeg','bmp');
			if (isset($file_parts['extension']) && in_array($file_parts['extension'], $cool_extensions)){
				$display_file=$this->dropbox->media($value->path);
				$files[]=$display_file->url;
			}else{
				$folder[]=$value->path;
			}	
			# code...
		}
		print_r($folder);
		print_r($files);
		
			
	}
}
