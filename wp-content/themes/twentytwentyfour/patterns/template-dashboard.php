<?php
/**
 * Title: Dashboard template
 * Slug: twentytwentyfour/template-dashboard
 * Template Types: dashboard
 * Viewport width: full
 * Inserter: no
 */
?>


<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group">

    <div class="header-fixed sidebar-fixed sidebar-dark header-light right-sidebar-toggoler-out" id="body">

        <div class="wrapper">

            <aside class="left-sidebar bg-sidebar">
                <div id="sidebar" class="sidebar sidebar-with-footer">
                    <div class="app-brand">
                        <a href="javascript:void(0)" onclick="opentab(event, 'tab_1')" title="Dashboard">
                            
                            <span class="brand-name text-truncate">Home Dashboard</span>
                        </a>
                      </div>
                    <div class="" data-simplebar="" style="height: 100%;">
                        
                        <ul class="nav sidebar-inner" id="sidebar-menu">
                            <li class="has-sub active expand">
                                <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse"
                                    data-target="#dashboard" aria-expanded="false" aria-controls="dashboard">
                                    <i class="mdi mdi-widgets"></i>
                                    <span class="nav-text">TA2TA Services</span> <b class="caret"></b>
                                </a>

                                <ul class="collapse show" id="dashboard" data-parent="#sidebar-menu">
                                    <div class="sub-menu">
                                        <li class="">
                                            <a class="sidenav-item-link active" href="javascript:void(0)" onclick="opentab(event, 'tab_2')">
                                                <span class="nav-text">Request Roundtable</span>
                                            </a>
                                        </li>
                                        <li class="">
                                            <a class="sidenav-item-link" href="javascript:void(0)" onclick="opentab(event, 'tab_3')">
                                                <span class="nav-text">Request Virtual</span>
                                            </a>
                                        </li>
                                        <li class="">
                                            <a class="sidenav-item-link" href="/contact">
                                                <span class="nav-text">Contact us for other Training or Technical Assistance</span>
                                            </a>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                            <li class="has-sub ">
                                <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse"
                                    data-target="#app" aria-expanded="false" aria-controls="app">
                                    <i class="mdi mdi-view-dashboard-outline"></i>
                                    <span class="nav-text">Manage Organization</span> <b class="caret"></b>
                                </a>

                                <ul class="collapse " id="app" data-parent="#sidebar-menu">
                                    <div class="sub-menu">
                                        <li class="">
                                            <a class="sidenav-item-link" href="javascript:void(0)" onclick="opentab(event, 'tab_4')">
                                                <span class="nav-text">Organization Profile</span>
                                            </a>
                                        </li>

                                        <li class="">
                                            <a class="sidenav-item-link" href="javascript:void(0)" onclick="opentab(event, 'tab_5')">
                                                <span class="nav-text">Calendar Events</span>
                                            </a>
                                        </li>

                                        <li class="">
                                            <a class="sidenav-item-link" href="javascript:void(0)" onclick="opentab(event, 'tab_6')">
                                                <span class="nav-text">Directory</span>
                                            </a>
                                        </li>

                                        <li class="">
                                            <a class="sidenav-item-link" href="javascript:void(0)" onclick="opentab(event, 'tab_7')">
                                                <span class="nav-text">Library Resources</span>
                                            </a>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                            <li class="has-sub ">
                                <a class="sidenav-item-link" href="javascript:void(0)" data-toggle="collapse"
                                    data-target="#components" aria-expanded="false" aria-controls="components">
                                    <i class="mdi mdi-diamond-stone"></i>
                                    <span class="nav-text">News and Updates</span> <b class="caret"></b>
                                </a>

                                <ul class="collapse " id="components" data-parent="#sidebar-menu">
                                    <div class="sub-menu">
                                        <li class="">
                                            <a class="sidenav-item-link" href="javascript:void(0)" onclick="opentab(event, 'tab_8')">
                                                <span class="nav-text">Newsletters</span>
                                            </a>
                                        </li>
                                        <li class="">
                                            <a class="sidenav-item-link" href="#">
                                                <span class="nav-text">TA Provider Webinars</span>
                                            </a>
                                        </li>
                                        <li class="">
                                            <a class="sidenav-item-link" href="javascript:void(0)" onclick="opentab(event, 'tab_9')">
                                                <span class="nav-text">TA Provider Toolkit</span>
                                            </a>
                                        </li>
                                        <li class="">
                                            <a class="sidenav-item-link" href="/orientations-page">
                                                <span class="nav-text">TA Provider Orientations</span>
                                            </a>
                                        </li>
                                        <li class="">
                                            <a class="sidenav-item-link" href="javascript:void(0)" onclick="opentab(event, 'tab_10')">
                                                <span class="nav-text">Reporting Statistics</span>
                                            </a>
                                        </li>
                                    </div>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>
            <div class="page-wrapper">

                <header class="main-header " id="header">
                    <nav class="navbar navbar-static-top navbar-expand-lg">
                        
                        <button id="sidebar-toggler" class="sidebar-toggle">
                            <span class="sr-only">Toggle navigation</span>
                        </button>
                        
                        <div class="search-form d-none d-lg-inline-block">
                            <div class="input-group">
                                <!-- wp:post-title {"textAlign":"center","level":4,"style":{"spacing":{"margin":{"top":"var:preset|spacing|0","right":"var:preset|spacing|30","bottom":"var:preset|spacing|0","left":"var:preset|spacing|30"}},"typography":{"fontStyle":"normal","fontWeight":"700"}},"textColor":"contrast","UAGResponsiveConditions":true} /-->
                            </div>
                        </div>

                        <div class="navbar-right ">
                            <ul class="nav navbar-nav">
                                <li class="right-sidebar-in right-sidebar-2-menu">
                                    <i class="mdi mdi-settings mdi-spin"></i>
                                </li>
                                
                                <li class="dropdown user-menu">
                                    <button href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                                        <!-- wp:pattern {"slug":"twentytwentythree/user-avatar"} /-->
                                        <!-- wp:pattern {"slug":"twentytwentythree/current_user"} /-->
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        
                                        <li class="dropdown-header">
                                           <!-- wp:pattern {"slug":"twentytwentythree/user-avatar"} /-->
                                            <div class="d-inline-block">
                                                <!-- wp:pattern {"slug":"twentytwentythree/current_user"} /-->
                                                <!-- wp:pattern {"slug":"twentytwentythree/user_email"} /-->
                                            </div>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)" onclick="opentab(event, 'tab_0')">
                                                <i class="mdi mdi-account"></i> My Profile
                                            </a>
                                        </li>
                                        <li class="dropdown-footer">
                                            <a href="/wp-login.php?action=logout"> <i class="mdi mdi-logout"></i> Log Out </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </header>

                <div class="content-wrapper">
                    <div class="content">
                        <div id="tab_0" class="row o-tab" style="display: none">
                            <div class="col-12">
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h2>My Profile</h2>
                                    </div>
                                    <div class="card-body">
                                        <!-- wp:shortcode -->
                                        [gravityform id="11" title="false" ajax="true"]
                                        <!-- /wp:shortcode -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tab_1" class="row o-tab">
                            <div class="card card-default">
                                <div class="card-header">
                                    <h2>What would you like to do ?</h2>
                                </div>
                                <div class="card-body">
                                    <div class="dash-buttons">

                                        <a class="btn" href="javascript:void(0)" onclick="opentab(event, 'tab_2')">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M12 5.5A3.5 3.5 0 0 1 15.5 9a3.5 3.5 0 0 1-3.5 3.5A3.5 3.5 0 0 1 8.5 9A3.5 3.5 0 0 1 12 5.5M5 8c.56 0 1.08.15 1.53.42c-.15 1.43.27 2.85 1.13 3.96C7.16 13.34 6.16 14 5 14a3 3 0 0 1-3-3a3 3 0 0 1 3-3m14 0a3 3 0 0 1 3 3a3 3 0 0 1-3 3c-1.16 0-2.16-.66-2.66-1.62a5.536 5.536 0 0 0 1.13-3.96c.45-.27.97-.42 1.53-.42M5.5 18.25c0-2.07 2.91-3.75 6.5-3.75s6.5 1.68 6.5 3.75V20h-13zM0 20v-1.5c0-1.39 1.89-2.56 4.45-2.9c-.59.68-.95 1.62-.95 2.65V20zm24 0h-3.5v-1.75c0-1.03-.36-1.97-.95-2.65c2.56.34 4.45 1.51 4.45 2.9z"></path></svg>
                                            </span>
                                            <span class="text">Request Roundtable</span>
                                        </a>
                                        <a class="btn" href="javascript:void(0)" onclick="opentab(event, 'tab_3')">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M6 9a3 3 0 1 0 0-6a3 3 0 0 0 0 6Zm0-6V0m0 12V9M0 6h3m6 0h3M2 2l2 2m4 4l2 2m0-8L8 4M4 8l-2 2m16 2a3 3 0 1 0 0-6a3 3 0 0 0 0 6Zm0-6V3m0 12v-3m-6-3h3m6 0h3M14 5l2 2m4 4l2 2m0-8l-2 2m-4 4l-2 2m-5 8a3 3 0 1 0 0-6a3 3 0 0 0 0 6Zm0-6v-3m0 12v-3m-6-3h3m6 0h3M5 14l2 2m4 4l2 2m0-8l-2 2m-4 4l-2 2"/></svg>
                                            </span>
                                            <span class="text">Request Services</span>
                                        </a>
                                        <a class="btn" href="/contact">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="2048" height="2048" viewBox="0 0 2048 2048"><path fill="currentColor" d="M2048 384v768q-12-23-26-49t-31-51t-35-50t-36-42V640l-896 448l-896-448v896h960l64 128H0V384zm-143 128H143l881 441zm-97 1075q55 29 99 71t76 94t48 110t17 122v64h-128v-64q0-66-25-124t-69-101t-102-69t-124-26q-66 0-124 25t-102 69t-69 102t-25 124v64h-128v-64q0-63 16-121t48-110t76-94t100-72q-54-46-83-109t-29-134q0-66 25-124t68-101t102-69t125-26q66 0 124 25t101 69t69 102t26 124q0 70-29 133t-83 110m-400-243q0 40 15 75t41 61t61 41t75 15q40 0 75-15t61-41t41-61t15-75q0-40-15-75t-41-61t-61-41t-75-15q-40 0-75 15t-61 41t-41 61t-15 75"/></svg>
                                            </span>
                                            <span class="text">Contact TA2TA</span>
                                        </a>
                                        <a class="btn" href="javascript:void(0)" onclick="opentab(event, 'tab_4')">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M19 9a7 7 0 1 0-10.974 5.76L5 20l2.256.093L8.464 22l3.466-6.004c.024 0 .046.004.07.004s.046-.003.07-.004L15.536 22l1.232-1.866L19 20l-3.026-5.24A6.99 6.99 0 0 0 19 9M7 9a5 5 0 1 1 5 5a5 5 0 0 1-5-5"/><circle cx="12" cy="9" r="3" fill="currentColor"/></svg>
                                            </span>
                                            <span class="text">Organization</span>
                                        </a>
                                        <a class="btn" href="javascript:void(0)" onclick="opentab(event, 'tab_5')">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512"><path fill="currentColor" d="M472 96h-88V40h-32v56H160V40h-32v56H40a24.028 24.028 0 0 0-24 24v336a24.028 24.028 0 0 0 24 24h432a24.028 24.028 0 0 0 24-24V120a24.028 24.028 0 0 0-24-24m-8 352H48V128h80v40h32v-40h192v40h32v-40h80Z"/><path fill="currentColor" d="M112 224h32v32h-32zm88 0h32v32h-32zm80 0h32v32h-32zm88 0h32v32h-32zm-256 72h32v32h-32zm88 0h32v32h-32zm80 0h32v32h-32zm88 0h32v32h-32zm-256 72h32v32h-32zm88 0h32v32h-32zm80 0h32v32h-32zm88 0h32v32h-32z"/></svg>
                                            </span>
                                            <span class="text">Calendar Events</span>
                                        </a>
                                        <a class="btn" href="javascript:void(0)" onclick="opentab(event, 'tab_6')">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="2048" height="2048" viewBox="0 0 2048 2048"><path fill="currentColor" d="M1920 0v2048H256v-254H128v-128h128v-257H128v-128h128V769H128V641h128V385H128V257h128V0zm-128 128H384v1792h1408zm-128 384h-640V384h640zm0 256h-640V640h640zm-960 892q-39 0-73-14t-60-40t-40-60t-15-74q0-39 14-73t40-59t60-41t74-15q39 0 73 15t59 40t41 60t15 73q0 39-15 73t-40 60t-60 40t-73 15m0-256q-29 0-48 19t-20 49q0 29 19 48t49 20q29 0 48-19t20-49q0-29-19-48t-49-20m0-640q-39 0-73-14t-60-40t-40-60t-15-74q0-39 14-73t40-59t60-41t74-15q39 0 73 15t59 40t41 60t15 73q0 39-15 73t-40 60t-60 40t-73 15m0-256q-29 0-48 19t-20 49q0 29 19 48t49 20q29 0 48-19t20-49q0-29-19-48t-49-20m960 900h-640v-128h640zm0 256h-640v-128h640z"/></svg>
                                            </span>
                                            <span class="text">Resource Directory</span>
                                        </a>
                                        <a class="btn" href="javascript:void(0)" onclick="opentab(event, 'tab_7')">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512"><rect width="64" height="368" x="32" y="96" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" rx="16" ry="16"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M112 224h128M112 400h128"/><rect width="128" height="304" x="112" y="160" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" rx="16" ry="16"/><rect width="96" height="416" x="256" y="48" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" rx="16" ry="16"/><path fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" d="m422.46 96.11l-40.4 4.25c-11.12 1.17-19.18 11.57-17.93 23.1l34.92 321.59c1.26 11.53 11.37 20 22.49 18.84l40.4-4.25c11.12-1.17 19.18-11.57 17.93-23.1L445 115c-1.31-11.58-11.42-20.06-22.54-18.89Z"/></svg>
                                            </span>
                                            <span class="text">Library Resources</span>
                                        </a>
                                        <a class="btn" href="javascript:void(0)" onclick="opentab(event, 'tab_10')">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M22 21H2V3h2v16h2v-9h4v9h2V6h4v13h2v-5h4z"/></svg>
                                            </span>
                                            <span class="text">Reporting Statistics</span>
                                        </a>
                                        <a class="btn" href="javascript:void(0)" onclick="opentab(event, 'tab_0')">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path fill="currentColor" d="M12 4a5 5 0 1 1-5 5a5 5 0 0 1 5-5m0-2a7 7 0 1 0 7 7a7 7 0 0 0-7-7m10 28h-2v-5a5 5 0 0 0-5-5H9a5 5 0 0 0-5 5v5H2v-5a7 7 0 0 1 7-7h6a7 7 0 0 1 7 7zm0-26h10v2H22zm0 5h10v2H22zm0 5h7v2h-7z"/></svg>
                                            </span>
                                            <span class="text">Update your profile</span>
                                        </a>
                                        <a class="btn" href="javascript:void(0)" onclick="opentab(event, 'tab_8')">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><g fill="currentColor"><path d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.238.972h.738a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 1 1 0v9a1.5 1.5 0 0 1-1.5 1.5H1.497A1.497 1.497 0 0 1 0 13.5zM12 14c.37 0 .654-.211.853-.441c.092-.106.147-.279.147-.531V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5v11c0 .278.223.5.497.5z"></path><path d="M2 3h10v2H2zm0 3h4v3H2zm0 4h4v1H2zm0 2h4v1H2zm5-6h2v1H7zm3 0h2v1h-2zM7 8h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2z"></path></g></svg>
                                            </span>
                                            <span class="text">Newsletters!</span>
                                        </a>
                                        <a class="btn" href="https://rise.articulate.com/share/3pAkizAVuVskQ9BywhTg7N0K2KB-5l9D#/" target="_blank" >
                                            <span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M13.03 20H4a2 2 0 0 1-2-2V6c0-1.11.89-2 2-2h6l2 2h8a2 2 0 0 1 2 2v9.5l-1.04-1.06c.01-.14.04-.29.04-.44c0-2.76-2.24-5-5-5s-5 2.24-5 5c0 1.64.8 3.09 2.03 4m9.84 1.19l-4.11-4.11c.41-1.04.18-2.26-.68-3.11c-.9-.91-2.25-1.09-3.34-.59l1.94 1.94l-1.35 1.36l-1.99-1.95c-.54 1.09-.29 2.44.59 3.35a2.91 2.91 0 0 0 3.12.68l4.11 4.1c.18.19.45.19.63 0l1.04-1.03c.22-.18.22-.5.04-.64"></path></svg></span>
                                            <span class="text">TA Provider Toolkit</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div id="tab_2"class="row o-tab" style="display: none">
                            <div class="col-12">
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h2>Request Roundtable Services</h2>
                                    </div>
                                    <div class="card-body">
                                        <p class="dash-p">
                                            TA providers have the opportunity to convene roundtables to delve deeper into an issue and to identify emerging or complex
                                            issues that will advance and guide the fields of domestic violence, sexual assault, dating violence, and stalking.
                                            The TA2TA Resource Center may cover the cost of hosting virtual or in-person roundtables, in whole or in part, as determined by OVW.
                                        </p>
                                        <!-- wp:gravityforms/form {"formId":"18","title":false,"description":false,"ajax":true,"theme":"orbital","inputPrimaryColor":"#204ce5","labelFontSize":"16","descriptionFontSize":"12"} /-->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tab_3"class="row o-tab" style="display: none">
                            <div class="col-12">
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h2>Request Virtual Services</h2>
                                    </div>
                                    <div class="card-body">
                                        <p class="dash-p">
                                            Engage many participants at once by taking advantage of the webinar service available through the TA2TA Project. TA providers may host
                                            a webinar of up to 1,000 participants at no cost. Once your presentation and presenters have been determined, TA2TA will setup your
                                            webinar, assist you in hosting it, conduct trial runs for presenters, and secure closed captioning. At the conclusion of your webinar,
                                            you will receive a webinar report with participant numbers, a recording of your webinar, and the closed captioning transcript.
                                        </p>
                                        <!-- wp:shortcode -->
                                        [gravityform id="19" title="false" ajax="true"]
                                        <!-- /wp:shortcode -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tab_4" class="row o-tab" style="display: none">
                            <div class="col-12">
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h2>Update Organization Profile</h2>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-4">
                                            <!-- wp:shortcode -->
                                                [my_organization]
                                            <!-- /wp:shortcode -->
                                        </div>
                                        <!-- wp:gravityforms/form {"formId":"8","title":false,"description":false,"ajax":true,"inputPrimaryColor":"#204ce5"} /-->
                                    </div>
                                </div> 
                            </div>
                        </div>

                        <div id="tab_5" class="row o-tab" style="display: none">
                            <div class="col-12">
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h2>Calendar Events</h2>
                                    </div>
                                    <div class="card-body">
                                        <!-- wp:group {"layout":{"type":"constrained"},"UAGLoggedOut":true,"UAGDisplayConditions":"userstate"} -->
                                        <div class="wp-block-group">
                                            <!-- wp:columns {"verticalAlignment":"top","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|30","left":"var:preset|spacing|30"}}}} -->
                                            <div class="wp-block-columns are-vertically-aligned-top">
                                                <!-- wp:column {"verticalAlignment":"top","width":"33.33%","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"backgroundColor":"white"} -->
                                                <div class="wp-block-column is-vertically-aligned-top has-white-background-color has-background"
                                                    style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30);flex-basis:33.33%">
                                                    <!-- wp:pb/accordion-item {"initiallyOpen":true,"titleTag":"h4","uuid":193480,"className":"directory-container"} -->
                                                    <div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item no-js is-open directory-container"
                                                        data-initially-open="true" data-click-to-close="true" data-auto-close="true" data-scroll="false"
                                                        data-scroll-offset="0">
                                                        <h4 id="at-193480" class="c-accordion__title js-accordion-controller" role="button">List of events</h4>
                                                        <div id="ac-193480" class="c-accordion__content">
                                                            <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"0","bottom":"0","left":"0"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"left","orientation":"vertical"}} -->
                                                            <div class="wp-block-group"
                                                                style="padding-top:var(--wp--preset--spacing--40);padding-right:0;padding-bottom:0;padding-left:0">
                                                                <!-- wp:shortcode -->
                                                                    [event_list]
                                                                <!-- /wp:shortcode -->
                                                            </div>
                                                            <!-- /wp:group -->
                                                        </div>
                                                    </div>
                                                    <!-- /wp:pb/accordion-item -->
                                                </div>
                                                <!-- /wp:column -->
                                        
                                                <!-- wp:column {"verticalAlignment":"top","width":"66.66%"} -->
                                                <div class="wp-block-column is-vertically-aligned-top" style="flex-basis:66.66%">

                                                    <!-- wp:group {"style":{"border":{"width":"1px","radius":"5px"},"spacing":{"blockGap":"var:preset|spacing|30","padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}}},"backgroundColor":"white","layout":{"type":"constrained","contentSize":"1000px"},"UAGDay":[]} -->
                                                    <div class="wp-block-group has-white-background-color has-background"
                                                        style="border-width:1px;border-radius:5px;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
                                                        <!-- wp:shortcode -->
                                                        [event_title]
                                                        <!-- /wp:shortcode -->
                                                        <!-- wp:gravityforms/form {"formId":"15","title":false,"description":false,"ajax":true,"theme":"orbital","inputPrimaryColor":"#204ce5"} /-->
                                                    </div>
                                                    <!-- /wp:group -->
                                                </div>
                                                <!-- /wp:column -->
                                            </div>
                                            <!-- /wp:columns -->
                                        </div>
                                        <!-- /wp:group -->
                                    </div>
                                </div> 
                            </div>
                        </div>

                        <div id="tab_6" class="row o-tab" style="display: none">
                            <!-- wp:uagb/container {"block_id":"16c7da2e","backgroundType":"color","backgroundColor":"#f4fdff","topPaddingDesktop":11,"bottomPaddingDesktop":11,"leftPaddingDesktop":11,"rightPaddingDesktop":11,"topMarginDesktop":0,"bottomMarginDesktop":5,"leftMarginDesktop":10,"rightMarginDesktop":10,"marginLink":false,"variationSelected":true,"isBlockRootParent":true} -->
                            <div class="wp-block-uagb-container uagb-block-16c7da2e alignfull uagb-is-root-container">
                                <div class="uagb-container-inner-blocks-wrap">
                                    <!-- wp:template-part {"slug":"display-grant-project-form"} /-->
                                </div>
                            </div>
                            <!-- /wp:uagb/container -->
                        </div>
                        <div id="tab_7" class="row o-tab" style="display: none">
                            <div class="col-12">

                                <div class="card card-default">
                                    <div class="card-header">
                                        <h2>Library resources</h2>
                                    </div>
                                    <div class="card-body">

                                        <!-- wp:group {"layout":{"type":"constrained"}} -->
                                        <div class="wp-block-group">
                                            <!-- wp:paragraph -->
                                            <p>If you need to change the document file, we recommend setting the document to Archived in the Document Categories
                                                section and then using the form to add a new document.</p>
                                            <!-- /wp:paragraph -->
                                        
                                            <!-- wp:uagb/modal {"block_id":"588a9ca9","defaultTemplate":true,"icon":"plus","buttonText":"Add Document","buttonIcon":"plus","buttonIconPosition":"before","modalWidth":1000,"btnLinkColor":"#21251F","btnBgColor":"","imgTagHeight":350,"imgTagWidth":350,"showBtnIcon":true,"btnBorderTopWidth":0,"btnBorderLeftWidth":0,"btnBorderRightWidth":0,"btnBorderBottomWidth":0,"btnBorderTopLeftRadius":30,"btnBorderTopRightRadius":30,"btnBorderBottomLeftRadius":30,"btnBorderBottomRightRadius":30,"btnBorderStyle":"default"} -->
                                            <div class="wp-block-uagb-modal uagb-block-588a9ca9 uagb-modal-wrapper" data-escpress="disable"
                                                data-overlayclick="disable">
                                                <div class="uagb-spectra-button-wrapper wp-block-button">
                                                    <a
                                                        class="uagb-modal-button-link wp-block-button__link uagb-modal-trigger" href="#" onclick="return false;"
                                                        target="_self" rel="noopener noreferrer"><span class="uagb-modal-content-wrapper">
                                                            <svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                                <path
                                                                    d="M432 256c0 17.69-14.33 32.01-32 32.01H256v144c0 17.69-14.33 31.99-32 31.99s-32-14.3-32-31.99v-144H48c-17.67 0-32-14.32-32-32.01s14.33-31.99 32-31.99H192v-144c0-17.69 14.33-32.01 32-32.01s32 14.32 32 32.01v144h144C417.7 224 432 238.3 432 256z">
                                                                </path>
                                                            </svg>
                                                            <span class="uagb-inline-editing">Add Document</span>
                                                        </span>
                                                    </a>
                                                </div>
                                                <div class="uagb-effect-default uagb-modal-popup uagb-block-588a9ca9">
                                                    <div class="uagb-modal-popup-wrap">
                                                        <div class="uagb-modal-popup-content">
                                                            <!-- wp:uagb/info-box {"classMigrate":true,"headFontWeight":500,"subHeadSpace":30,"block_id":"a09c718a","showCtaIcon":false,"ctaType":"button","ctaText":"Call To Action","iconBottomMargin":15,"paddingBtnTop":14,"paddingBtnBottom":14,"paddingBtnLeft":28,"paddingBtnRight":28,"btnBorderTopWidth":1,"btnBorderLeftWidth":1,"btnBorderRightWidth":1,"btnBorderBottomWidth":1,"btnBorderTopLeftRadius":3,"btnBorderTopRightRadius":3,"btnBorderBottomLeftRadius":3,"btnBorderBottomRightRadius":3,"btnBorderStyle":"none","btnBorderColor":"#333"} -->
                                                            <div
                                                                class="wp-block-uagb-info-box uagb-block-a09c718a uagb-infobox__content-wrap  uagb-infobox-icon-above-title uagb-infobox-image-valign-top">
                                                                <div class="uagb-ifb-content">
                                                                    <div class="uagb-ifb-icon-wrap">
                                                                        <svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                                                            <path
                                                                                d="M0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256zM371.8 211.8C382.7 200.9 382.7 183.1 371.8 172.2C360.9 161.3 343.1 161.3 332.2 172.2L224 280.4L179.8 236.2C168.9 225.3 151.1 225.3 140.2 236.2C129.3 247.1 129.3 264.9 140.2 275.8L204.2 339.8C215.1 350.7 232.9 350.7 243.8 339.8L371.8 211.8z">
                                                                            </path>
                                                                        </svg>
                                                                    </div>
                                                                    <div class="uagb-ifb-title-wrap">
                                                                        <h3 class="uagb-ifb-title">Engage Your Visitors!</h3>
                                                                    </div>
                                                                    <p class="uagb-ifb-desc">Click here to change this text. Lorem ipsum dolor sit amet,
                                                                        consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis pulvinar
                                                                        dapibus.</p>
                                                                    <div class="uagb-ifb-button-wrapper wp-block-button">
                                                                        <a href="#"
                                                                            class="uagb-infobox-cta-link wp-block-button__link" target="_self"
                                                                            rel="noopener noreferrer" onclick="return false;" alt="">
                                                                            <span class="uagb-inline-editing">Call To Action</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- /wp:uagb/info-box -->
                                                        </div>
                                                        <div class="uagb-modal-popup-close">
                                                            <svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 320 512">
                                                                <path
                                                                    d="M310.6 361.4c12.5 12.5 12.5 32.75 0 45.25C304.4 412.9 296.2 416 288 416s-16.38-3.125-22.62-9.375L160 301.3L54.63 406.6C48.38 412.9 40.19 416 32 416S15.63 412.9 9.375 406.6c-12.5-12.5-12.5-32.75 0-45.25l105.4-105.4L9.375 150.6c-12.5-12.5-12.5-32.75 0-45.25s32.75-12.5 45.25 0L160 210.8l105.4-105.4c12.5-12.5 32.75-12.5 45.25 0s12.5 32.75 0 45.25l-105.4 105.4L310.6 361.4z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- /wp:uagb/modal -->
                                        </div>
                                        <!-- /wp:group -->
                                        
                                        <!-- wp:group {"layout":{"type":"constrained","contentSize":"1400px"},"UAGDay":[]} -->
                                        <div class="wp-block-group">
                                            <!-- wp:columns -->
                                            <div class="wp-block-columns">
                                                <!-- wp:column {"width":"33.33%","style":{"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"backgroundColor":"white"} -->
                                                <div class="wp-block-column has-white-background-color has-background"
                                                    style="padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30);flex-basis:33.33%">
                                                    <!-- wp:pb/accordion-item {"initiallyOpen":true,"titleTag":"h4","uuid":96050} -->
                                                    <div class="wp-block-pb-accordion-item c-accordion__item js-accordion-item no-js is-open"
                                                        data-initially-open="true" data-click-to-close="true" data-auto-close="true" data-scroll="false"
                                                        data-scroll-offset="0">
                                                        <h4 id="at-96050" class="c-accordion__title js-accordion-controller" role="button">List of Documents
                                                        </h4>
                                                        <div id="ac-96050" class="c-accordion__content"><!-- wp:group {"layout":{"type":"constrained"}} -->
                                                            <div class="wp-block-group">
                                                                <!-- wp:shortcode -->
                                                                    [document_list]
                                                                <!-- /wp:shortcode -->
                                                            </div>
                                                            <!-- /wp:group -->
                                                        </div>
                                                    </div>
                                                    <!-- /wp:pb/accordion-item -->
                                                </div>
                                                <!-- /wp:column -->
                                        
                                                <!-- wp:column {"width":"66.66%"} -->
                                                <div class="wp-block-column" style="flex-basis:66.66%">
                                                    <!-- wp:group {"style":{"border":{"width":"1px"},"spacing":{"padding":{"top":"var:preset|spacing|30","right":"var:preset|spacing|30","bottom":"var:preset|spacing|30","left":"var:preset|spacing|30"}}},"backgroundColor":"white","layout":{"type":"constrained"}} -->
                                                    <div class="wp-block-group has-white-background-color has-background"
                                                        style="border-width:1px;padding-top:var(--wp--preset--spacing--30);padding-right:var(--wp--preset--spacing--30);padding-bottom:var(--wp--preset--spacing--30);padding-left:var(--wp--preset--spacing--30)">
                                                        <!-- wp:shortcode -->
                                                        [document_title]
                                                        <!-- /wp:shortcode -->
                                        
                                                        <!-- wp:gravityforms/form {"formId":"16","title":false,"description":false,"inputPrimaryColor":"#204ce5"} /-->
                                                    </div>
                                                    <!-- /wp:group -->
                                                </div>
                                                <!-- /wp:column -->
                                            </div>
                                            <!-- /wp:columns -->
                                        </div>
                                        <!-- /wp:group -->

                                    </div>
                                </div>

                            </div>

                        </div>
                        <div id="tab_8" class="row o-tab" style="display: none">
                            <div class="col-12">
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h2>Newsletters</h2>
                                    </div>
                                    <div class="card-body">
                                        <!-- wp:post-content /-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tab_9" class="row o-tab" style="display: none">
                            <div class="col-12">
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h2>Reporting Statistics</h2>
                                    </div>
                                    <div class="card-body">
                                        REPORTING STATISTICS
                                        <a class="btn btn-block" href="https://rise.articulate.com/share/3pAkizAVuVskQ9BywhTg7N0K2KB-5l9D#/" target="_blank">Click to continue</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tab_10" class="row o-tab" style="display: none">
                            <div class="col-12">
                                <div class="card card-default">
                                    <div class="card-header">
                                        <h2>Button Redirect</h2>
                                    </div>
                                    <div class="card-body">
                                        <!-- wp:group {"layout":{"type":"constrained"}} -->
                                        <div class="wp-block-group">
                                            <div class="cover-redirect">
                                                <div class="org_overlay">
                                                    <div class="card">
                                                        <p class="dash-p">
                                                            Some message
                                                        </p>
                                                        <a class="btn btn-block" href="https://rise.articulate.com/share/3pAkizAVuVskQ9BywhTg7N0K2KB-5l9D#/" target="_blank">Click to continue</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /wp:group -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>
<!-- /wp:group -->


<script>
function opentab(evt, tabName) {

    var i, x, tablinks, current_tab;

    x = document.getElementsByClassName("o-tab");
    for (i = 0; i < x.length; i++) {
        x[i].style.display = "none";  
    }
    tablinks = document.getElementsByClassName("sidenav-item-link");
    for (i = 0; i < x.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";

    //Match the current tab with the form
    if(tabName == 'tab_2'){
        current_tab = 18;
    } else {
        current_tab = 0;
    }
}

// function grab_selected_event_id(selected_id){
//     //alert(document.URL + 'edit-event/?post_id=' + selected_id);
//     xhttp = new XMLHttpRequest();
//     //xhttp.open("GET", document.URL+ 'edit-event/?post_id=' + selected_id, true);
//     xhttp.open("GET",  'https://webpros.ta2ta.org/wp-json/tribe/events/v1/events/' + selected_id, false);
//     xhttp.onreadystatechange = function() {
//         if (this.readyState == 4) {
//             console.log(this.response);
//             var ev = JSON.parse(this.response);
//             document.getElementById('selected_event_to_edit').innerHTML = ev.title;
//         }
//     };
//     xhttp.send();
//     //xhttp.close();
//     //document.getElementById('selected_event_to_edit').innerHTML = event_name;
// }
// </script>
