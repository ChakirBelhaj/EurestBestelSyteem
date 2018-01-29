<?php
    /* Home */
    $this->get('/', 'IndexController@index',                                                        ['login' => false, 'role' => 'student']);
    $this->post('/', 'IndexController@filter',                                                      ['login' => false, 'role' => 'student']);
    $this->get('/products/{product_id}', 'ProductController@viewProduct',                           ['login' => false, 'role' => 'student']);

    $this->get('/cart', 'CartController@index',                                                     ['login' => false, 'role' => 'student']);

    /* Login / Logout */
    $this->get('/login', 'LoginController@index',                                                   ['login' => false, 'role' => 'student']);
    $this->post('/login', 'LoginController@postLogin',                                              ['login' => false, 'role' => 'student']);
    $this->get('/logout', 'LoginController@logout',                                                 ['login' => false]);

    $this->match('GET|POST', '/image', 'ImageController@index',                                     ['login' => false]);

    /* Password reset */
    $this->get('/password', 'PasswordController@index',                                             ['login' => false, 'role' => 'student']);
    $this->post('/password', 'PasswordController@postRequest',                                      ['login' => false, 'role' => 'student']);
    $this->get('/password/reset/{email}/{reset_token}', 'PasswordController@reset',                 ['login' => false, 'role' => 'student']);
    $this->post('/password/reset/{email}/{reset_token}', 'PasswordController@postReset',            ['login' => false, 'role' => 'student']);

    $this->get('/changepassword', 'ChangePasswordController@showChangPasswordPage',                 ['login' => false, 'role' => 'student']);
    $this->post('/changepassword', 'ChangePasswordController@changePassword',                       ['login' => false, 'role' => 'student']);

    /* Admin panel */
    $this->get('/adminpanel','AdminController@showAdminPanel',                                      ['login' => true, 'role' => 'admin']);

    $this->get('/addemployee', 'AdminController@showAddEmployeePanel',                              ['login' => true, 'role' => 'admin']);
    $this->post('/addemployee', 'AdminController@postRegisterEmployee',                             ['login' => true, 'role' => 'admin']);

    $this->get('/addadmin', 'AdminController@showAddAdminPanel',                                    ['login' => true, 'role' => 'admin']);
    $this->post('/addadmin', 'AdminController@postRegisterAdmin',                                   ['login' => true, 'role' => 'admin']);



    /* Register */
    $this->post('/register', 'RegisterController@postRegister',                                     ['login' => false]);
    $this->get('/register', 'RegisterController@showRegisterForm',                                  ['login' => false]);

    /* Orders */
    $this->post('/order/add', 'OrderController@add',                                                ['login' => true, 'role' => 'student']);
    $this->post('/order/clear', 'OrderController@clear',                                            ['login' => true, 'role' => 'student']);
    $this->post('/order/remove', 'OrderController@remove',                                          ['login' => true, 'role' => 'student']);
    $this->post('/order/submit', 'OrderController@submit',                                          ['login' => true, 'role' => 'student']);
    $this->post('/order/change', 'OrderController@change',                                          ['login' => true, 'role' => 'student']);

    /* Account */
    $this->get('/account', 'AccountController@showProfile',                                         ['login' => true, 'role' => 'student']);
    $this->get('/account/change', 'AccountController@showEditProfile',                              ['login' => true, 'role' => 'student']);
    $this->post('/account/change', 'AccountController@postEditProfile',                             ['login' => true, 'role' => 'student']);
    $this->get('/account/password', 'AccountController@passwordChange',                             ['login' => true, 'role' => 'student']);
    $this->post('/account/password', 'AccountController@postPasswordChange',                        ['login' => true, 'role' => 'student']);
    $this->get('/account/orders', 'AccountOrdersController@index',                                  ['login' => true, 'role' => 'student']);
    $this->get('/account/orders/{order_id}', 'AccountOrdersController@order',                       ['login' => true, 'role' => 'student']);
    $this->get('/account/orders/{order_id}/invoice', 'AccountOrdersController@invoice',             ['login' => true, 'role' => 'student']);
    $this->post('/account/orders/{order_id}/action', 'AccountOrdersController@action',              ['login' => true, 'role' => 'student']);
    $this->get('/account/orders/{order_id}/action', 'AccountOrdersController@actionGet',            ['login' => true, 'role' => 'student']);
    $this->get('/account/preferences', 'AccountController@preferences',                             ['login' => true, 'role' => 'student']);
    $this->post('/account/preferences', 'AccountController@postPreferences',                        ['login' => true, 'role' => 'student']);

    /* Manage catalog */
    $this->get('/manage', 'ManageCatalogController@products',                                       ['login' => true, 'role' => 'employee']);
    $this->get('/manage/categories', 'ManageCatalogController@categories',                          ['login' => true, 'role' => 'employee']);
    $this->post('/manage/categories', 'ManageCatalogController@postCategories',                     ['login' => true, 'role' => 'employee']);
    $this->get('/manage/categories/new', 'ManageCatalogController@newCategory',                     ['login' => true, 'role' => 'employee']);
    $this->post('/manage/categories/new', 'ManageCatalogController@postNewCategory',                ['login' => true, 'role' => 'employee']);
    $this->get('/manage/categories/category/{category_id}', 'ManageCatalogController@category',     ['login' => true, 'role' => 'employee']);
    $this->post('/manage/categories/category/{category_id}', 'ManageCatalogController@postCategory',['login' => true, 'role' => 'employee']);

    $this->get('/manage/products', 'ManageCatalogController@products',                              ['login' => true, 'role' => 'employee']);
    $this->get('/manage/products/new', 'ManageCatalogController@newProduct',                        ['login' => true, 'role' => 'employee']);
    $this->post('/manage/products/new', 'ManageCatalogController@postNewProduct',                   ['login' => true, 'role' => 'employee']);
    $this->get('/manage/products/product/{product_id}', 'ManageCatalogController@product',          ['login' => true, 'role' => 'employee']);
    $this->post('/manage/products/product/{product_id}', 'ManageCatalogController@postProduct',     ['login' => true, 'role' => 'employee']);
    $this->post('/manage/products', 'ManageCatalogController@postProducts',                         ['login' => true, 'role' => 'employee']);

    /* Payment */
    $this->get('/payment/pay/{order_id}', 'PaymentController@pay',                                  ['role' => 'student']);
    $this->post('/payment/pay/{order_id}', 'PaymentController@postPay',                             ['role' => 'student']);
    $this->get('/payment/return/{order_id}', 'PaymentController@return',                            ['role' => 'student']);
    $this->post('/payment/process/{order_id}', 'PaymentController@process',                         ['login' => false]);

    /* LiveOrders */
    $this->get('/orders',                  'LiveOverviewController@show',                           ['login' => true, 'role' => 'employee']);
    
    /* API */
    $this->post('/api/orders/fetch',       'LiveOverviewController@getOrders',                      ['login' => true, 'role' => 'employee']);
    $this->post('/api/orders/update',      'LiveOverviewController@updateOrders',                   ['login' => true, 'role' => 'employee']);
    $this->post('/api/order/update',       'LiveOverviewController@updateOrder',                    ['login' => true, 'role' => 'employee']);

    /* Error pages */
    $this->match('GET|POST', '/403', 'ErrorController@forbidden',                                   ['login' => false]);
    $this->match('GET|POST', '/404', 'ErrorController@notFound',                                    ['login' => false]);
    $this->match('GET|POST', '/500', 'ErrorController@internalServerError',                         ['login' => false]);