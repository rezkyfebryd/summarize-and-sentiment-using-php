<?php
if(!isset($basepath))
  exit("No direct script access allowed!");

require_once("core/conn.php");
$curr = $login->cek_login();
$login->login_redir();


$menu = isset($data['menu']) ? intval($data['menu']) : 0;
$submenu = isset($data['submenu']) ? intval($data['submenu']) : 0;

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?=isset($data['title']) ? $data['title']." - " : ""; ?>itSummarize</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="assets/bootstrap.min.css">
<link rel="stylesheet" href="assets/propeller.min.css">
<link rel="stylesheet" href="assets/alertify.min.css">
<link rel="stylesheet" href="assets/jquery.mCustomScrollbar.css">
<link rel="stylesheet" href="assets/font-awesome.min.css">
<link rel="stylesheet" href="assets/animate.css">
<link rel="stylesheet/less" href="style.less">
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<script src="assets/jquery-1.12.3.min.js"></script> 
</head>
<Body>


<!-- Nav menu -->
<nav class="navbar bg-primary pmd-navbar navbar-fixed-top pmd-z-depth" style="position:fixed; background-color:#66CDAA;">
  <div class="container-fluid"> 

      <div class="dropdown pmd-dropdown pmd-user-info pull-right">
        <a href="logout.php" class="btn-user dropdown-toggle media">
          <div class="media-left logout-button" data-toggle="tooltip" data-placement="bottom" title="Logout">
            <span class="fa fa-sign-out fa-fw fa-lg"></span>
            <span class="text">Logout</span>
          </div>
        </a>
      </div>

      <!-- Sidebar Toggle Button-->
      <!-- Brand and toggle get grouped for better mobile display -->
      <div class="navbar-header">
          <a href="javascript:void(0);" class="btn btn-sm pmd-btn-fab pmd-btn-flat pmd-ripple-effect btn-primary pull-left margin-r8 pmd-sidebar-toggle"><i class="fa fa-lg fa-bars"></i></a>
          <span class="navbar-brand">itSummarize</span> 
      </div>
      
  </div>
</nav>
    
<!-- Sidebar -->
<section id="pmd-main">
    <!-- Left sidebar -->
    <aside class="pmd-sidebar pmd-sidebar-left-fixed pmd-sidebar-left pmd-z-depth sidebar-hide-custom" role="navigation">
        <ul class="main-menu">
        <li>
        		<a href="javascript:;" class="has-child <?=is_same(2, $menu, "active")?>" style="background-color:#66CDAA;"><span class="fa fa-fw fa-bar-chart"></span><label>Summarization</label></a>
        		<ul class="submenu">
        			<li><a href="training.php" class=" <?=is_same(21, $submenu, "active")?>">Training</a></li>
        			<li><a href="summary.php" <?=is_same(22, $submenu, "active")?>>Testing</a></li>
        		</ul>
        	</li>
          <li>
        		<a href="javascript:;" class="has-child <?=is_same(2, $menu, "active")?>" style="background-color:#66CDAA;"><span class="fa fa-fw fa-bar-chart"></span><label>Sentiment Analysis</label></a>
        		<ul class="submenu">
        <li><a href="training1.php" class=" <?=is_same(6, $menu, "active")?>"></span><label>Training</label></a></li>
        <li><a href="sentiment.php" class=" <?=is_same(6, $menu, "active")?>"></span><label>Testing</label></a></li>
        </ul>
        	</li>
          <?php
          
          ?>
        </ul>
    </aside>





    <!-- Content -->
    <div class="pmd-content" id="content">