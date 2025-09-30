<!DOCTYPE html>

<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{$_title}</title>
    <link rel="shortcut icon" href="{$app_url}application/storage/icon/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" sizes="57x57" href="{$app_url}application/storage/icon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="{$app_url}application/storage/icon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="{$app_url}application/storage/icon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="{$app_url}application/storage/icon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="{$app_url}application/storage/icon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="{$app_url}application/storage/icon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="{$app_url}application/storage/icon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="{$app_url}application/storage/icon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="{$app_url}application/storage/icon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="{$app_url}application/storage/icon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{$app_url}application/storage/icon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="{$app_url}application/storage/icon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{$app_url}application/storage/icon/favicon-16x16.png">
    <link rel="manifest" href="{$app_url}application/storage/icon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{$app_url}application/storage/icon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <link href="{$_theme}/css/bootstrap.min.css" rel="stylesheet">
    <link href="{$_theme}/lib/fa/css/font-awesome.min.css" rel="stylesheet">
    <link href="{$_theme}/lib/icheck/skins/all.css" rel="stylesheet">
    <link href="{$app_url}ui/lib/css/animate.css" rel="stylesheet">
    <link href="{$app_url}ui/lib/toggle/bootstrap-toggle.min.css" rel="stylesheet">
    <link href="{$_theme}/css/style.css?ver=2.0.1" rel="stylesheet">
    <link href="{$_theme}/css/component.css?ver=2.0.1" rel="stylesheet">
    <link href="{$_theme}/css/custom.css" rel="stylesheet">


    <link href="{$app_url}ui/lib/icons/css/ibilling_icons.css" rel="stylesheet">


    <link href="{$_theme}/css/material.css" rel="stylesheet">

    <link href="{$_theme}/css/{$_c['nstyle']}.css" rel="stylesheet">

    {foreach $plugin_ui_header_client as $plugin_ui_header_add}
        {$plugin_ui_header_add}
    {/foreach}

    {if $_c['rtl'] eq '1'}
        <link href="{$_theme}/css/bootstrap-rtl.min.css" rel="stylesheet">
        <link href="{$_theme}/css/style-rtl.min.css" rel="stylesheet">
    {/if}

    {if isset($xheader)}
        {$xheader}
    {/if}

    {$_c['header_scripts']}
<script src="https://alabaya-v2.mbills.in/ui/theme/ibilling/js/jquery-1.10.2.js"></script>

<script src="https://alabaya-v2.mbills.in/ui/theme/ibilling/js/jquery-ui-1.10.4.min.js"></script>

</head>

<body class="fixed-nav {if $_c['mininav']}mini-navbar{/if}">
<section>
    <div id="wrapper">
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">

                    {$client_extra_nav[0]}
                    <li {if $_application_menu eq 'dashboard'}class="active"{/if}><a href="{$_url}client/dashboard/">
                            <i class="icon-th-large-outline"></i>
                            <span class="nav-label">{$_L['Dashboard']}</span></a></li>
                            
                    {if Contacts::details()->gid == 3}
                        {$client_extra_nav[1]}
                        <li {if $_application_menu eq 'employee_invoices'}class="active"{/if}><a href="{$_url}client/employee_invoices/"><i class="icon-credit-card-1"></i> <span class="nav-label">{$_L['Invoices']}</span></a></li>
                    {else}
                        {$client_extra_nav[1]}
                        <li {if $_application_menu eq 'invoices'}class="active"{/if}><a href="{$_url}client/invoices/"><i class="icon-credit-card-1"></i> <span class="nav-label">{$_L['Invoices']}</span></a></li>
                    {/if}
                    <!-- {$client_extra_nav[2]}
                    <li {if $_application_menu eq 'quotes'}class="active"{/if}><a href="{$_url}client/quotes/"><i class="icon-article"></i> <span class="nav-label">{$_L['Quotes']}</span></a></li>
                    -->
                    {$client_extra_nav[3]}
                    <li {if $_application_menu eq 'transactions'}class="active"{/if}><a href="{$_url}client/transactions/"><i class="icon-database"></i> <span class="nav-label">{$_L['Transactions']}</span></a></li>
                    {$client_extra_nav[4]}
                    <li {if $_application_menu eq 'profile'}class="active"{/if}><a href="{$_url}client/profile/"><i class="icon-user-1"></i> <span class="nav-label">{$_L['Profile']}</span></a></li>

                </ul>

            </div>
        </nav>
        <style>
            .navbar-user-profile {
                width: 100%;
            }
            .navbar-right-user-profile {
                width: 20%;
            }
        </style>
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-fixed-top white-bg" role="navigation" style="margin-bottom: 0">

                    <img class="logo" style="max-height: 40px; width: auto;" src="{$app_url}application/storage/system/logo.png" alt="Logo">

                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary btn-flat" href="#"><i class="fa fa-dedent"></i> </a>

                    </div>
                    <ul class="nav navbar-top-links navbar-right navbar-right-user-profile hidden-xs">

                        <li class="dropdown navbar-user navbar-user-profile">

                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">

                                {if $user['img'] eq 'gravatar'}
                                    <img src="http://www.gravatar.com/avatar/{($user['email'])|md5}?s=200" class="img-circle" alt="{$user['fullname']}">
                                {elseif $user['img'] eq ''}
                                    <img src="{$app_url}ui/lib/imgs/default-user-avatar.png" alt="">
                                {else}
                                    <img src="{$user['img']}" class="img-circle" alt="{$user['account']}">
                                {/if}

                                <span class="hidden-xs">
                                    {$_L['Welcome']} {$user['account']}<br>
                                    <b style="color:#888;">{$branch_name}</b>
                                </span>
                                <b class="caret"></b>
                            </a>
                            <ul class="dropdown-menu animated fadeIn">
                                <li class="arrow"></li>
                                <li><a href="{$_url}client/profile/">{$_L['Edit Profile']}</a></li>

                                <li class="divider"></li>
                                <li><a href="{$_url}client/logout/">{$_L['Logout']}</a></li>

                            </ul>
                        </li>

                    </ul>

                </nav>
            </div>

            <div class="row wrapper white-bg page-heading">
                <div class="col-lg-12">
                    <h2 style="color: #2F4050; font-size: 16px; font-weight: 400; margin-top: 18px">{$_st} </h2>

                </div>

            </div>

            <div class="wrapper wrapper-content animated fadeIn">
                {if isset($notify)}
                {$notify}
{/if}