<?php
/*
Plugin Name:  Repsite
Description:  Replicated website using Firestorm
Version:      1.0.0
Author:       Eric Larsen
*/
require_once (plugin_dir_path(__FILE__) . "widget.php");

if (!class_exists('Repsite_Aregolife'))
{
	class Repsite_Aregolife

	{
		static $instance = false;
		public function __construct()
		{
			if (is_admin())
			{
				add_action('wp_ajax_repsite_header', array(
					$this,
					'repsite_handler'
				));
				add_action('wp_ajax_nopriv_repsite_header', array(
					$this,
					'repsite_handler'
				));
			}
			else
			{
				// front end
				add_action('wp_enqueue_scripts', array(
					$this,
					'front_scripts'
				) , 10);
				add_filter('option_siteurl', array(
					$this,
					'replace_siteurl'
				));
				add_filter('option_home', array(
					$this,
					'replace_siteurl'
				));
			}
			add_action('widgets_init', array(
				$this,
				'register_widget'
			));
		}
        
		public static function init()
		{
			if (!self::$instance) self::$instance = new self;
			return self::$instance;
		}
        
		public function replace_siteurl($val)
		{ //make all generated links keep distname as the subdomain
			return '//' . $_SERVER['HTTP_HOST'];
		}
        
		public function register_widget()
		{
			register_widget('Repsite_Widget');
		}
        
		public function front_scripts()
		{
			wp_enqueue_script('repsite_aregolife_script', plugins_url('public/js/repsite-aregolife.js', __FILE__) , array(
				'jquery'
			) , '1', false);
			$title_nonce = wp_create_nonce('repsite_nonce');
			wp_localize_script('repsite_aregolife_script', 'repsite_ajax', array(
				'ajax_url' => admin_url('admin-ajax.php') ,
				'nonce' => $title_nonce
			));
			wp_enqueue_style('repsite_aregolife_style', plugins_url('public/css/repsite-aregolife.css', __FILE__) , array() , '1', 'all');
		}
        
		public function repsite_handler()
		{
			$username = $_POST['username'];
			$url = "https://aregolife.com/"; //TODO: Create setting
			$data = [];
			if ($username)
			{
				$data["username"] = $username;
				$response = wp_remote_get($url . $username, array(
					'timeout' => 30
				)); //Get distributor information from FireStorm

				$body = wp_remote_retrieve_body($response);

                //parse HTML
				libxml_use_internal_errors(true);
				$dom = new DOMDocument;
				$dom->loadHTML($body);
                
				$repInfo = $dom->getElementById("ctl00_ReplicatedWebsiteInfoDiv");
				if ($repInfo)
				{
					$name = $dom->getElementById("ReplicatedFullDealerName")->textContent;
					$data["name"] = $name;
					$img = $repInfo->getElementsByTagName('img') [0]->getAttribute('src');
					$data["img"] = str_replace("..", $url . 'membertoolsdotnet', $img);
					$distid = substr($img, strpos($img, "DealerID=") + 9);
					$data["distid"] = $distid;
					$email = $dom->getElementById("ReplicatedEmail");
					if ($email)
					{
						$email = $email->getElementsByTagName('a') [0]->textContent;
						$data["email"] = $email;
					}
				}
				else
				{ //no info
					$data["error"] = true;
				}
			}
			else
			{ // no username
				$data["error"] = true;
			}
			wp_send_json(json_encode($data));
		}
	}
	$Repsite_Aregolife = Repsite_Aregolife::init();
}
