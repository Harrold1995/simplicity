<!doctype html>
<?php $class = trim($this->router->fetch_class()); ?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $title; ?></title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/bootstrap.css">
    <link rel="stylesheet" media="screen" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.min.css">

    <!-- Main Styles -->

    <?= ($class != 'documents') ? '<link rel="stylesheet" media="screen" href="' . base_url() . 'themes/default/assets/css/module.css?v='.SCRIPT_VERSION.'">' : '<link rel="stylesheet" media="screen" href="' . base_url() . 'themes/default/assets/css/module.fixed.css?v='.SCRIPT_VERSION.'">' ?>
    <?= ($class == 'formbuilder') ? '<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <link href="' . base_url() . 'themes/default/assets/feditor/style.css?v='.SCRIPT_VERSION.'" rel="stylesheet">
    <link href="' . base_url() . 'themes/default/assets/feditor/static/css/style.css?v='.SCRIPT_VERSION.'" rel="stylesheet">' : ''
    ?>
    <!-- Tables & Selects -->
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/jquery-editable-select.css?v=<?php echo SCRIPT_VERSION;?>">
    <link rel="stylesheet" href="<?php echo base_url(); ?>themes/default/assets/css/jquery.treetable.css?v=<?php echo SCRIPT_VERSION;?>">
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/datatables.css?v=<?php echo SCRIPT_VERSION;?>">
    <link rel="stylesheet" media="screen" href="https://cdn.datatables.net/colreorder/1.5.1/css/colReorder.dataTables.min.css?v=<?php echo SCRIPT_VERSION;?>">
    <!-- Miscellaneous -->
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/jquery.calculadora.css">
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/jquery-ui.css">
    <link rel="stylesheet" media="screen" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <link rel="stylesheet" media="print" href="<?php echo base_url(); ?>themes/default/assets/styles/print.css?v=<?php echo SCRIPT_VERSION;?>">
    <link rel="icon" type="image/x-icon" href="<?php echo base_url(); ?>themes/default/assets/favicon.png">
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/datepicker.css?v=<?php echo SCRIPT_VERSION;?>">
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/batchreports.css?v=<?php echo SCRIPT_VERSION;?>">
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/selectize.css?v=<?php echo SCRIPT_VERSION;?>">
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/tooltipster/tooltipster.bundle.min.css">
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/tooltipster/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-shadow.min.css">
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/tooltipster/plugins/tooltipster/follower/tooltipster-follower.min.css">
    <!--<link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/tooltipster.min.css">-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>themes/default/assets/js/plugins/slick/slick.grid.css?v=<?php echo SCRIPT_VERSION;?>" type="text/css"/>
    <!-- Class Specific -->
    <?= ($class == 'formbuilder') ? '<link rel="stylesheet" media="screen" href="' . base_url() . 'themes/default/assets/formeo/style.css?v='.SCRIPT_VERSION.'">' : '' ?>
    <?= ($class == 'documents') ? '<link rel="stylesheet" media="screen" href="' . base_url() . 'themes/default/assets/css/documents.css?v='.SCRIPT_VERSION.'">' : '' ?>
    <!--<link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/reports.css">-->
    <!-- Search Styles -->
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/search.css?v=<?php echo SCRIPT_VERSION;?>">

    <!-- Custom Styles -->
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/custom.css?v=<?php echo SCRIPT_VERSION;?>">
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/feedback.css?v=<?php echo SCRIPT_VERSION;?>">
	<link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>themes/default/assets/css/maintenance.css?v=<?php echo SCRIPT_VERSION;?>">

    <style type="text/css">.tableWrapper table.dataTable td {
            vertical-align: middle
        }

        .tableWrapper table.dataTable.cell-border tbody tr:first-child th, .tableWrapper table.dataTable th {
            border-top: 1px solid #ddd;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd
        }

        .tableWrapper table.dataTable th:first-child {
            border-left: 1px solid #ddd
        }

        .tableWrapper table.dataTable.no-footer {
            border-bottom: 1px solid #ddd
        }

        .tableWrapper table.dataTable tr.rowHover:hover {
            cursor: pointer;
            cursor: hand;
            background: #e9f5dc
        }

        .tableWrapper table.dataTable td img {
            transition-duration: .5s
        }

        .tableWrapper table.dataTable td img.rotate-down {
            -ms-transform: rotate(90deg);
            -webkit-transform: rotate(90deg);
            transform: rotate(90deg)
        }

        .tableWrapper table.dataTable td img.rotate-up {
            -ms-transform: rotate(0deg);
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg)
        }</style>

    <!-- Main Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <script src="<?php echo base_url(); ?>themes/default/assets/js/bootstrap.js"></script>

    <script>
			window['_fs_debug'] = false;
			window['_fs_host'] = 'fullstory.com';
			window['_fs_script'] = 'edge.fullstory.com/s/fs.js';
			window['_fs_org'] = 'QA46Y';
			window['_fs_namespace'] = 'FS';
			(function(m,n,e,t,l,o,g,y){
				if (e in m) {if(m.console && m.console.log) { m.console.log('FullStory namespace conflict. Please set window["_fs_namespace"].');} return;}
				g=m[e]=function(a,b,s){g.q?g.q.push([a,b,s]):g._api(a,b,s);};g.q=[];
				o=n.createElement(t);o.async=1;o.crossOrigin='anonymous';o.src='https://'+_fs_script;
				y=n.getElementsByTagName(t)[0];y.parentNode.insertBefore(o,y);
				g.identify=function(i,v,s){g(l,{uid:i},s);if(v)g(l,v,s)};g.setUserVars=function(v,s){g(l,v,s)};g.event=function(i,v,s){g('event',{n:i,p:v},s)};
				g.shutdown=function(){g("rec",!1)};g.restart=function(){g("rec",!0)};
				g.log = function(a,b) { g("log", [a,b]) };
				g.consent=function(a){g("consent",!arguments.length||a)};
				g.identifyAccount=function(i,v){o='account';v=v||{};v.acctId=i;g(o,v)};
				g.clearUserCookie=function(){};
			})(window,document,window['_fs_namespace'],'script','user');
			</script>

</head>
<body>
<div style="font-family:micr37; height: 0px; overflow:hidden;  opacity: 0;">1</div>
<div id="root" class="no-print <?php if($class == 'dashboard') {echo 'dashboard-root';} ?>">
    <header id="top">
        <p id="logo"><a href="./" accesskey="h">Smart City</a></p>
        <nav id="skip">
            <ul>
                <li><a href="#nav" accesskey="n">Skip to navigation (n)</a></li>
                <li><a href="#aside" accesskey="a">Skip to sidebar (a)</a></li>
                <li><a href="#content" accesskey="c">Skip to content (c)</a></li>
            </ul>
        </nav>
        <nav id="nav">
            <h2><?php echo $h2; ?></h2>
            <ul>
                <li  id="headerSearchButton">
                    <a accesskey="1">
                        <i class="icon-zoom"></i>
                        <span class="hidden">Search</span>
                    </a>
                </li>

                <li class="timer" data-tooltip-content="#timer"><a accesskey="1">
                        <i class="icon-time" aria-hidden="true"></i> <span class="hidden">Add</span></a>
                    <em>(1)</em></li>
                <li class="tooltip-menu" data-tooltip-content="#report_list"><a accesskey="1">
                        <i class="icon-documents"></i> <span class="hidden">Add</span></a>
                    <em>(1)</em></li>
                <li class="tooltip-menu" data-tooltip-content="#adding_new"><a accesskey="1">
                        <i class="icon-plus-circle"></i> <span class="hidden">Add</span></a>
                    <em>(1)</em></li>
                <!--li><a accesskey="2" href="./"><i class="icon-notification"></i> <span>6</span>
                        <span class="hidden">Notifications</span></a> <em>(2)</em></li>
                <li>
                    <a accesskey="3" href="< ?php echo base_url(); ?>properties/sendEmail"><i class="icon-envelope-outline"></i>
                        <span>9</span>
                        <span class="hidden">Messages</span></a> <em>(3)</em></li-->
                <li>
                    <a accesskey="4" href="./" class="tooltip-menu-logout" data-tooltip-content="#logout"><img alt="Placeholder" width="46" height="42" src=
                        <?php if ($this->session->userdata('image') != null) {
                            echo '"' . base_url() . "uploads/images/" . $this->session->userdata('image') . '"';
                        } else {
                            echo '"' . base_url() . "themes/default/assets/images/profile.png" . '"';
                        } ?>
                        > hi <?php echo $this->session->userdata('first_name'); ?></a>
                    <em>(4)</em></li>


            </ul>
            <div class="globalSearchWrapper" id="headerSearchWrapper">
                <form class="globalSearchForm" id="headerSearchForm" autocomplete="off">
                    <input class="globalSearchInput" id="headerSearchInput" data-type = "all">
                </form>
                <div class="closeGlobalSearchBtn" id="closeHeaderSearchBtn">
                    <i class="icon-x2"></i>
                </div>
            </div>
        </nav>
        <nav id="openModals"  style="display: none; border: 1px solid grey; height: 31px;"><ul></ul></nav>
    </header>
    <nav id="aside">
        <ul>
            <li><a class="leftSideBarLink" type="property" href="<?php echo base_url('properties'); ?>"><i class="icon-home"></i> <span>properties</span></a></li>
            <li><a class="leftSideBarLink" accesskey="6" type="account" href="<?php echo base_url('accounts'); ?>"><i class="icon-coins"></i>
                    <span>Accounts</span></a>
                <em>(6)</em></li>
           
            <li><a class="leftSideBarLink" accesskey="8" type="vendors" href="<?php echo base_url('vendors'); ?>"><i class="icon-user"></i>
                    <span>Vendors</span></a> <em>(8)</em></li>
            <li><a class="leftSideBarLink" accesskey="8" type="maintenance" href="<?php echo base_url('maintenance'); ?>"><i class="icon-tools"></i>
            <span>Maintenance</span></a> <em>(8)</em></li>

            <?php   if ($this->permissions->checkPermissions('timesheet_general', false, false)) echo
                ' <li><a class="leftSideBarLink" accesskey="9" type="timesheet" href="'.base_url('timesheet').'"><i class="icon-time"></i>
            <span>Employees</span></a> <em>(9)</em></li>' ; ?>

            <li><a accesskey="5" href="<?php echo base_url(); ?>documents/0"><i class="icon-bank"></i>
                    <span>documents</span></a>
                <em>(5)</em></li>
                <li><a class="reportsList" accesskey="8" type="reportsList" href="#"><i class="icon-user"></i> <span>reports list</span></a>
                <em>(8)</em></li>
                <li><a class="leftSideBarLink" accesskey="7" type="inventory" href="<?php echo base_url('inventory'); ?>"><i class="icon-money"></i> <span>items</span></a>
                <em>(7)</em></li>
            <li><a class="" type="users" href="<?php echo base_url('users'); ?>"><i class="icon-tools"></i> <span>Settings</span></a></li>

        </ul>
    </nav>
    <?php require_once VIEWPATH . 'forms/error.php'; ?>
    <?php require_once VIEWPATH . 'forms/warning.php'; ?>
    <div class="myAlert-top newAlert alert-success">
        <a href="#" class="close alert-close" data-dismiss="alert" aria-label="close">&times;</a>
        <div class="alert-icon">
            <div class="check_mark">
                <div class="sa-icon sa-success animate">
                    <span class="sa-line sa-tip animateSuccessTip"></span>
                    <span class="sa-line sa-long animateSuccessLong"></span>
                    <div class="sa-placeholder"></div>
                    <div class="sa-fix"></div>
                </div>
            </div>

        </div>
        <div>
            <span class="alert-title">Success!!</span>
            <span class="alert-body"></span>
        </div>

    </div>
    <div class="myAlert-top newAlert alert-danger">
        <a href="#" class="close alert-close" data-dismiss="alert" aria-label="close">&times;</a>

        <div class="alert-icon">
            <xml version="1.0">
                <svg height="32px" version="1.1" viewBox="0 0 32 32" width="32px" xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <title/>
                    <defs/>
                    <g fill="none" fill-rule="evenodd" id="Icons new Arranged Names Color" stroke="none" stroke-width="1">
                        <g fill="#FF0000" id="101 Warning">
                            <path d="M14.4242327,6.14839275 C15.2942987,4.74072976 16.707028,4.74408442 17.5750205,6.14839275 L28.3601099,23.59738 C29.5216388,25.4765951 28.6755462,27 26.4714068,27 L5.5278464,27 C3.32321557,27 2.47386317,25.4826642 3.63914331,23.59738 Z M16,20 C16.5522847,20 17,19.5469637 17,19.0029699 L17,12.9970301 C17,12.4463856 16.5561352,12 16,12 C15.4477153,12 15,12.4530363 15,12.9970301 L15,19.0029699 C15,19.5536144 15.4438648,20 16,20 Z M16,24 C16.5522848,24 17,23.5522848 17,23 C17,22.4477152 16.5522848,22 16,22 C15.4477152,22 15,22.4477152 15,23 C15,23.5522848 15.4477152,24 16,24 Z M16,24" id="Triangle 29"/>
                        </g>
                    </g>
                </svg>
        </div>
        <div>
            <span class="alert-title">Error!</span>
            <span class="alert-body"></span>
        </div>

    </div>

    <div class="myAlert-top newAlert alert-warning">
        <a href="#" class="close alert-close" data-dismiss="alert" aria-label="close">&times;</a>

        <div class="alert-icon">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFAAAABQCAYAAACOEfKtAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAP4SURBVHhe7ZnJaxRREMbnkoxLFBe8akQJKiaaDa+CKy5EvYiggiiKwbglHqIEAi7gQfCiEEFQMIcEERUREU0y8X/wEHPyIoIKgiuEbusbamTSXTO9zOuZnp764AdDd733qqpfMt+8TqlUKpVKpVKpVCqVSqVSJUJWJr3dytTfI4bs8fRWvqzyI2ui/oqdSdv5UCP7+baqmKx3dRto9824G5iewT0OUxUS7bQxZ/Ny4B6HqSTZk/X7pMbNgmI4XJUv62UqTTvsg9i0PBCDWB6myoka0yc1TAKxPEwFWZMNy6yJ9HepWRKIxRgerqIdNSQ1qhgYw8NrW9ZkXYtkW7zI2hoay9PUrmgnFbQtXmAsT1Ob8mVbvKhVW+PXtniBOWrS1lDhvm2LF5iLp60NBbUtXtScraEdE9i2eIE5efpkK6xt8aJmbA3tlKK25ePoIvvI5k0in54sFMfkwNy8TDLlx7Z8frpAbB748rxBHDOLpNoav7blKzVJah748Wq+OCYfrJFIW0OF+bItP1/PE5sHftM9aYwTrMXLJkNBbMvfN3PF5gEpXiJxtoZ2RCDbIjXv2JZOMbYQWJOXr26FsS0ntnW6GnhqR4cYW4jE2BraCYFPW07t7HA18OzeNjG2GFib06hOhT1tOd/V5mrgha5WMdaTarU1pZy29B3Y6Grg5YMtYqwXyKEqbQ0lHvq0Bc1yNnDgULMY6wfkwmlVh0o9bRk83Oxq4NWj68VYP1SdraEnXtJpC5rlbODN4+vEWL8gJ04v3jJx2nLr5FpXA3FNivVL1dgaetKhXxLluH16jauBd3uaxNggIDdOM54Ka1ucoFnOBg6dK72BWeJqa0qxLU7u9652NfDBpVVibFCQYyxtDSVm7CURmuVs4HD/SjE2DMiV046HSrUtTkYGGl0NxDUpNgyxszX0RI2+JHo8uMLVwGfXlouxYUHOnH5lZcK2VILY2Bprov6tlGA1gNy5jMrIlG2pKJWyNSZtiwT9o8++oQP4LMWYADVUxNbACkgJmWDq0RK7d3/r/y8QfMY1KdYEqIXLKo9M25Z88ObtzJ72Wd/AANf8vpULStltDSyAlIgJ3j9c6mpeDtyTxpgANXF50Spq2zI9vFhsHsA9aYwJymZrorYtM2NzxBNpXMO9/FjTRG5rymVbvr1osO/0NNnduzrs7t3t2c+4JsUaJypbE7VtiQuoMRJbQ9u7V1owiVCtF7lsc6InMy0tlkToC2WKyzYnmvSPtFgSIV/4i8s2J9rWGWmxJEJ/beNctjlZmbq2qH59xInsrxKqlcs2K3s83UgL3KA/5xFabDRJoCbiOmrkclUqlUqlUqlUKpVKpVKpTCuV+gdLacDGL442NgAAAABJRU5ErkJggg==">
        </div>
        <div>
            <span class="alert-title">Warning!</span>
            <span class="alert-body"></span>
        </div>
        <footer>
            <ul class="list-btn" id="button-container" style="font-size: .4em;   display: flex; justify-content: center;">
               <li><button  type = "button" id="warningSubmit" >ok</button></li>
               <li><button  type = "button" id="warningCancel" >cancel</button></li>
            </ul>
        </footer>
    </div>
    <div id="minMaxCloseBox" style="display: none">
        <div class="handle-container"><span id="minMaxCloseHandle">open windows</span></div>

        <ul></ul>
    </div>
    <div id="rightPopup" class="h100" style="display: none">
    <span class = 'closeButton' id="rightPopupCloseBtn"><a  aria-label="close" style="float:right"><i class="icon-x-thin"></i></a></span>
    
    <div id="rightPopupWrapper"></div>

    </div>
    <form id="attach_document_form" style="visibility: hidden; width: 1px; height: 1px">
        <input type="file" id="attach_document" name="attach_document" style="visibility: hidden; width: 1px; height: 1px" multiple/>
    </form>
    <style>
        .tooltip_templates {
            display: none;
        }

        .list-container li a {
            line-height: 26px;

            font-family: Helvetica, Arial, sans-serif;

            font-size: 13px;
            color: #372f2b;
        }

        .column ul li {
            padding-left: 12px;
            width: 250px;
            -webkit-transform: perspective(1px) translateZ(0);
            transform: perspective(1px) translateZ(0);
        }

        .column ul li:before {
            content: "";
            position: absolute;
            z-index: -1;
            left: 0;
            right: 100%;
            bottom: 0;
            background: #f37ce4;
            height: 2px;
            -webkit-transition-property: right;
            transition-property: right;
            -webkit-transition-duration: 0.6s;
            transition-duration: 0.6s;
            -webkit-transition-timing-function: ease-out;
            transition-timing-function: ease-out;
        }

        .column ul li:hover:before {
            right: 0;
        }

        .list-container ul {
            list-style: none;
        }

        .column ul li a:hover {
            text-decoration: none;
            color: #f37ce4;
        }

        .column ul li:hover a {
            color: #f37ce4;
            font-weight: 700px;
        }

        .column ul li:hover {

            padding-left: 12px;
        }

        .list-container li:hover a {

        }

        .list-container h3 {
            margin: 20px 0 10px 0;
            line-height: 18px;

            font-size: 12px;
            color: #888888;

        }

        .tooltip_templates {
            padding-top: 25px;
        }

        #timer {
            width: 200px;
            height: 200px;
            padding: 10px;
            padding-top: 5px;
            /*background: url(themes/default/assets/images/animated clock.gif) center center no-repeat #fff;*/
            /* display: none;*/
        }

        #startEnd {
            text-align: center;
            height: 28px;
            width: 130px;
            background-color: #00be00;
            color: #fff;
            padding: 7px;
            border-radius: 15px;
            cursor: pointer;
            position: absolute;
            top: 80px;
            right: 50px;
            font-size: 15px;
        }

        #timer #timern {
            text-align: center;
            font-size: 7.5mm;
            margin-top: 4px;
        }

        #projectName {
            text-align: center;
        }

        #timer #selectproject {
            /* width: 110px;*/
            height: 15px;
        }

    </style>

        <div id="search-container-wrapper">
            <a id="advancedSearch"  style="float:left">Advanced Search</a>
            <a id="instantSearchCloseBtn" aria-label="close" style="float:right"><i class="icon-x-thin"></i></a>
        <div id="search-container-header">

            <ul style="float:left" class  = 'list-horizontal nav'>
                <li class = "active"><a class = "searchTabs" data-type ='all' >All</a></li>
                <li><a class = "searchTabs" data-type ='vendors'>vendors</a></li>
                <li><a class = "searchTabs" data-type ='tenants'>tenants</a></li>
                <li><a class = "searchTabs" data-type ='properties' >properties</a></li>
                <li><a class = "searchTabs" data-type ='owners' >owners</a></li>
                <li><a class = "searchTabs" data-type ='accounts'>accounts</a></li>
            </ul>
        </div>
        


        <div id="search-container">
        </div>

        </div>

    <div class="tooltip_templates">

        <div id="timer">
            <div class='timerHeader'>
                <input type="hidden" value="Start" id="timerStatus">
                <input type="hidden" id="startTime">
                <input type="hidden" id="timerProfile" value="<?php echo $this->session->userdata('profileId'); ?>">
                <img src="<?php echo base_url(); ?>themes/default/assets/images/animated clock.gif" alt="">
                <div id="selectproject">
                    <select id="selected">
                        <option id="1">Work</option>
                        <option id="2">Lunch</option>
                        <option id="3">Project 3</option>
                    </select>
                </div>
                <div id='timerInfo'>
                    <div id='projectName'></div>
                    <div id='timern'></div>
                </div>
            </div>

            <div id="startEnd">
                <span id="startEndButton"></span>
            </div>
        </div>

        <div id="logout" style="">
            <span>

                    <span>
                        <a href="<?php echo base_url(); ?>/auth/logout" class="list-btn "><span>logout</span></a>
                    </span>

        </div>

        <div id="report_list" style="display:block !important;width: calc(100vw - 35px); ">

            <div style="background-color: black">
                <br> <br>
                <h2 style="text-align: center; color:white;">reports</h2>
                <br>
            </div>
            <!-- overflow scroll is used when you click on the left side link for reports -->
            <div style="display:flex; flex-direction: row; flex-wrap: wrap; overflow: scroll;">

                <div class="list-container row pl-3 pr-3" style="width:100%">
                    <?php
                    foreach($reports as $cat => $group) { ?>
                        
                        <div class="reports-row">
                        <h3><?php echo $cat=='null' ? 'Unsorted' : $cat;?></h3>
                            <?php foreach($group as $report) { ?>
                                <div class="reports-col">
                                    <a href="#" class="reportLink" data-id="<?php echo $report->id;?>" tittle="<?php echo $report->name;?>"><?php echo $report->name;?></a>&nbsp;
                                    <a href="#" class="duplicateReport" title="Duplicate Report" data-id="<?php echo $report->id;?>"><i class="far fa-clone"></i></a>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <div class="reports-row mt-3">
                        <div class="reports-col">
                            <a href="<?php echo base_url('reports/edit');?>" style="color:#1077ff;">Add New Report</a>
                        </div>
                        <div class="reports-col">
                           <a href="<?php echo base_url('batchreports');?>"  target="_blank" style="color:#1077ff;">Report Batches</a>
                        </div>
                    </div>

                    </ul>
                </div>

            </div>

        </div>
        

        <div id="adding_new" style="width: calc(100vw - 35px); ">

            <div style="background-color: #fb5fe7">
                <br> <br>
                <h2 style="text-align: center; color:white;">ADD NEW</h2>
                <br>
            </div>

            <div style="display:flex; flex-direction: column; flex-flow: row wrap; ">

                <div class="list-container ">

                    <ul class="column">
                        <li>
                            <ul>
                                <h3>Leasing:</h3>
                                <li id="addPropertyButton"><a href="#addProperty">New Property</a></li>
                                <li id="addUnitButton"><a href="#addUnit">New Unit</a></li>
                                <li id="addTenantButton"><a href="#addTenant">New Tenant</a></li>
                                <li id="addLeaseButton"><a href="#addLease">New Lease</a></li>
                                <li id="inviteTenantsButton"><a href="#inviteTenantsButton">Invite Tenants To Portal</a></li>

                                

                            </ul>
                        </li>
                        <li>
                            <ul>
                                <h3>Accounting:</h3>
                                
                                <li id="addAccountButton"><a href="#addAccount">account</a></li>
                                <li id="journalEntryButton"><a href="#journalEntry"> Journal entry</a></li>
                                <!-- <li id="invoiceButton"><a href="#invoice"> Invoice</a></li> -->
                            </ul>
                        </li>

                    </ul>

                </div>

                <div class="list-container ">

                    <ul class="column">
                        <li>
                            <ul>
                                <h3>People:</h3>
                                   <li id="addTenantButton"><a href="#addTenant">New Tenant</a></li>
                                   <li id="addInvestorButton"><a href="#addInvestor">New Investor</a></li>
                                   <li id="addVendorButton"><a href="#addVendor">New Vendor</a></li>
                                   <li id="addEmployeeButton"><a href="#addEmployee">New Employee</a></li>
                            </ul>
                        </li>
                        <li>
                            <ul>
                                <h3>Banking:</h3>
                                

                                <li id="depositButton"><a href="#deposit"> Deposit</a></li>
                                <li id="checkButton"><a href="#check"> Check</a></li>                               
                                <li id="bankTransButton"><a href="#bankTransButton"> Bank Trans</a></li>                               
                                <li id="bank_transferButton"><a href="#bank_transferButton"> Bank transfer</a></li>
                                <li id="addCreditCard"><a href="#creditCard"> Credit Card Grid</a></li>
                                <li id="creditCardButton"><a href="#singlecreditCard"> Credit Card</a></li>
                                

                            </ul>
                        </li>
                </div>

                <div class="list-container ">
                    <ul class="column">
                        <li>
                            <ul>
                                <h3>Payables:</h3>
                                <li id="addBillButton"><a href="#addBill">Bill</a></li>                               
                                <li id="payBillsButton"><a href="#payBillsButton"> Pay Bill</a></li>
                                <li id="memorizedTransactionsButton">
                                    <a href="#memorizedTransactions"> Memorized Transactions</a>
                                </li>
                                <li id="checkToPrint"><a href="#checkToPrint"> check to print</a></li>
                            </ul>
                        </li>
                        <li>
                            <ul>
                                <h3>Recievables:</h3>
                                <li id="newCharge"><a href="#newCharge">Add A Charge</a></li>
                                <li id="newInvoice"><a href="#newInvoice">Add An Invoice</a></li>
                                <li id="receive_paymentButton"><a href="#receive_payment">Receive Payment</a></li>
                                <li id="transactionsImport"><a href="#transactionsImport">transaction Import</a></li>
                                <li id="getIn_courtButton"><a href="#inCourt">In Court</a></li>
                                <!--li id="encrypt"><a href="#"> encrypt</a></li-->                               
                                <li id="massEmailButton"><a href="#email">Email Active Tenants</a></li>                                
                                <li id="transfer_balButton"><a href="#transfer_balButton"> Balance transfer</a></li>
                                <li id="emailInvoice"><a href="#emailInvoice"> Choose Invoice</a></li>
                            </ul>
                        </li>

                    </ul>

                </div>

                <div class="list-container ">

                    <ul class="column">
                        <li>
                            <ul class="list-top">
                                <h3>Properties:</h3>
                                
                                <li id="utilitiesGrid"><a href="#utilitiesGrid"> Utilities Grid</a></li>
                                <li id="addEntitiesButton"><a href="#addEntities">Entities</a></li>
                                <li id="getEntitiesButton"><a href="#getEntities"> All Entities</a></li>                              
                                <li id="managementButton"><a href="#managementButton">Management</a></li>
                                <li id="propertyTaxesButton"><a href="#propertyTaxes">Property Taxes</a></li>
                            </ul>
                        </li>
                        <li>
                            <ul>
                                <h3>Investors:</h3>
                                <li id="addInvestorButton"><a href="#addInvestor">Add Investor</a></li>
                                <li id="getInvestorsButton"><a href="#getInvestor"> Investors</a></li>
                                <li id="capitalButton"><a href="#capitalButton">Capital</a></li>
                                <li id="disburseButton"><a href="#disburseButton">Disburse</a></li>
                            </ul>
                        </li>
                    </ul>

                </div>

            </div>

        </div>

    </div>
<?php if ($class != 'formbuilder')
    if ($class == 'documents')
        echo '<main id="content" class="cols-c">';
    elseif ($class == 'reports' || $class == 'batchreports' || $class == 'dashboard')
        echo '';
	elseif ($class == 'maintenance')
		echo '<main id="content" class="cols-a cols-maintenance">';
    else
        echo '<main id="content" class="cols-a">';
