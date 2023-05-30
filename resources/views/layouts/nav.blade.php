<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
            <div class="sidebar-header">
                <div>
                    <img src="/assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
                </div>
                <div>
                    <h6 class="logo-text">EHS Tecnologías</h6>
                </div>
                <div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
                </div>
            </div>
            <!--navigation-->
            <ul class="metismenu" id="menu">
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-home-circle'></i>
                        </div>
                        <div class="menu-title">Escritorio</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('index') }}"><i class="bx bx-right-arrow-alt"></i>Default</a>
                        </li>
                        <li> <a href="{{ url('dashboard-alternate') }}"><i class="bx bx-right-arrow-alt"></i>Alternate</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class="bx bx-category"></i>
                        </div>
                        <div class="menu-title">Application</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('app-emailbox') }}"><i class="bx bx-right-arrow-alt"></i>Email</a>
                        </li>
                        <li> <a href="{{ url('app-chat-box') }}"><i class="bx bx-right-arrow-alt"></i>Chat Box</a>
                        </li>
                        <li> <a href="{{ url('app-file-manager') }}"><i class="bx bx-right-arrow-alt"></i>File Manager</a>
                        </li>
                        <li> <a href="{{ url('app-contact-list') }}"><i class="bx bx-right-arrow-alt"></i>Contatcs</a>
                        </li>
                        <li> <a href="{{ url('app-to-do') }}"><i class="bx bx-right-arrow-alt"></i>Todo List</a>
                        </li>
                        <li> <a href="{{ url('app-invoice') }}"><i class="bx bx-right-arrow-alt"></i>Invoice</a>
                        </li>
                        <li> <a href="{{ url('app-fullcalender') }}"><i class="bx bx-right-arrow-alt"></i>Calendar</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">Marketing</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Margenes</div>
                    </a>
                    <ul>
                        <li> 
                            <a href="{{ url('margenes/mayor') }}"><i class="bx bx-right-arrow-alt"></i>Mayores al 10%</a>
                        </li>
                        <li> 
                            <a href="{{ url('margenes/menor') }}"><i class="bx bx-right-arrow-alt"></i>Menores al 10%</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Ofertas</div>
                    </a>
                    <ul>
                        <li> 
                            <a href="{{ url('promociones/cartas') }}"><i class="bx bx-right-arrow-alt"></i>Promociones Vigentes</a>
                        </li>
                        <li> 
                            <a href="{{ url('promociones/flash') }}"><i class="bx bx-right-arrow-alt"></i>Promociones Flash</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">WordPress</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Productos</div>
                    </a>
                    <ul>
                        <li> 
                            <a href="{{ url('/wp_xalapa') }}"><i class="bx bx-right-arrow-alt"></i>Xalapa</a>
                        </li>
                        <li> 
                            <a href="{{ url('/wp_todos') }}"><i class="bx bx-right-arrow-alt"></i>Otros Almacenes</a>
                        </li>
                        <li> 
                            <a href="{{ url('/wp_individual') }}"><i class="bx bx-right-arrow-alt"></i>Producto Individual</a>
                        </li>
                    </ul>
                </li>
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                    </div>
                    <div class="menu-title">Promociones</div>
                </a>
                <ul>
                    <li> 
                        <a href="{{ url('/wp_xalapa') }}"><i class="bx bx-right-arrow-alt"></i>Faltantes</a>
                    </li>
                    <li> 
                        <a href="{{ url('/wp_todos') }}"><i class="bx bx-right-arrow-alt"></i>Vigentes</a>
                    </li>
                    <li> 
                        <a href="{{ url('/wp_individual') }}"><i class="bx bx-right-arrow-alt"></i>Vencidas</a>
                    </li>
                </ul>
            </li>
            <li>
                <a class="has-arrow" href="javascript:;">
                    <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                    </div>
                    <div class="menu-title">Actualizar</div>
                </a>
                <ul>
                    <li> 
                        <a href="{{ url('/wp_precios') }}"><i class="bx bx-right-arrow-alt"></i>Precios</a>
                    </li>
                    <li> 
                        <a href="{{ url('/wp_inventario') }}"><i class="bx bx-right-arrow-alt"></i>Existencias</a>
                    </li>
                </ul>
            </li>
                {{-- <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Productos Xalapa</div>
                    </a>
                    <ul>
                        <li> 
                            <a href="{{ url('margenes/mayor') }}"><i class="bx bx-right-arrow-alt"></i>Mayores al 10%</a>
                        </li>
                        <li> 
                            <a href="{{ url('margenes/menor') }}"><i class="bx bx-right-arrow-alt"></i>Menores al 10%</a>
                        </li>
                    </ul>
                </li> --}}
                {{-- <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Promociones Xalapa</div>
                    </a>
                    <ul>
                        <li> 
                            <a href="{{ url('/ofertas_xalapa_nuevas') }}"><i class="bx bx-right-arrow-alt"></i>Nuevas</a>
                        </li>
                        <li> 
                            <a href="{{ url('/ofertas_xalapa_vigentes') }}"><i class="bx bx-right-arrow-alt"></i>Vigentes</a>
                        </li>
                        <li> 
                            <a href="{{ url('/ofertas_xalapa_mes') }}"><i class="bx bx-right-arrow-alt"></i>Del Mes</a>
                        </li>
                        <li> 
                            <a href="{{ url('/ofertas_xalapa_vencidas') }}"><i class="bx bx-right-arrow-alt"></i>Vencidas</a>
                        </li>
                    </ul>
                </li> --}}
                <li class="menu-label">Márgenes</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i>
                        </div>
                        <div class="menu-title">Promociones</div>
                    </a>
                    <ul>
                    <li> <a href="{{ url('promociones/nuevas') }}"><i class="bx bx-right-arrow-alt"></i>Nuevas</a>
                        </li>
                        <li> <a href="{{ url('promociones/vigentes') }}"><i class="bx bx-right-arrow-alt"></i>Vigentes</a>
                        </li>
                        <li> <a href="{{ url('promociones/mes') }}"><i class="bx bx-right-arrow-alt"></i>Del Mes</a>
                        </li>
                        <li> <a href="{{ url('promociones') }}"><i class="bx bx-right-arrow-alt"></i>Todas</a>
                        </li>
                        <li> <a href="{{ url('promociones/vencidas') }}"><i class="bx bx-right-arrow-alt"></i>Vencidas</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Cálculos</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('margenes') }}"><i class="bx bx-right-arrow-alt"></i>Márgenes</a>
                        </li>
                        <li> <a href="{{ url('existencias') }}"><i class="bx bx-right-arrow-alt"></i>Existencias</a>
                        </li>
                        <li> <a href="{{ url('sitemap') }}"><i class="bx bx-right-arrow-alt"></i>Sitemap</a>
                        </li>
                        <li> <a href="{{ url('enlaces') }}"><i class="bx bx-right-arrow-alt"></i>Enlaces</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Palabras Clave</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('palabras-clave/nuevas') }}"><i class="bx bx-right-arrow-alt"></i>Nuevas</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i></div>
                        <div class="menu-title">Productos Nuevos</div>
                    </a>
                    <ul>
                        <li> 
                            <a href="{{ url('productos/imagen') }}"><i class="bx bx-right-arrow-alt"></i>Imagenes Nuevas</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">CT Online</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Top 100</div>
                    </a>
                    <ul>
                        <li> <a href="{{ route('top100') }}"><i class="bx bx-right-arrow-alt"></i>Consultar</a>
                        </li>
                        <li> <a href="{{ url('subir-tops') }}"><i class="bx bx-right-arrow-alt"></i>Subir Archivo</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Google My Business</div>
                    </a>
                    <ul>
                    <li> <a href="{{ url('productos/gmb') }}"><i class="bx bx-right-arrow-alt"></i>Consultar GMB</a>
                        </li>
                    <li> <a href="{{ url('productos/gmb-nuevos') }}"><i class="bx bx-right-arrow-alt"></i>Nuevos</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">Consultas</li>
                <li>
                    <li>
                        <a href="{{ url('precios') }}">
                            <div class="parent-icon"><i class='bx bx-cookie'></i>
                            </div>
                            <div class="menu-title">Cotizador de Precios</div>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('productos') }}">
                            <div class="parent-icon"><i class='bx bx-cookie'></i>
                            </div>
                            <div class="menu-title">Consulta de productos</div>
                        </a>
                        <a href="{{ url('analytics') }}">
                            <div class="parent-icon"><i class='bx bx-cookie'></i>
                            </div>
                            <div class="menu-title">Páginas más visitadas</div>
                        </a>
                    </li>
                </li>
                
                <li class="menu-label">UI Elements</li>
                <li>
                    <a href="{{ url('widgets') }}">
                        <div class="parent-icon"><i class='bx bx-cookie'></i>
                        </div>
                        <div class="menu-title">Widgets</div>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-cart'></i>
                        </div>
                        <div class="menu-title">eCommerce</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('ecommerce-products') }}"><i class="bx bx-right-arrow-alt"></i>Products</a>
                        </li>
                        <li> <a href="{{ url('ecommerce-products-details') }}"><i class="bx bx-right-arrow-alt"></i>Product Details</a>
                        </li>
                        <li> <a href="{{ url('ecommerce-add-new-products') }}"><i class="bx bx-right-arrow-alt"></i>Add New Products</a>
                        </li>
                        <li> <a href="{{ url('ecommerce-orders') }}"><i class="bx bx-right-arrow-alt"></i>Orders</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-bookmark-heart'></i>
                        </div>
                        <div class="menu-title">Components</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('component-alerts') }}"><i class="bx bx-right-arrow-alt"></i>Alerts</a>
                        </li>
                        <li> <a href="{{ url('component-accordions') }}"><i class="bx bx-right-arrow-alt"></i>Accordions</a>
                        </li>
                        <li> <a href="{{ url('component-badges') }}"><i class="bx bx-right-arrow-alt"></i>Badges</a>
                        </li>
                        <li> <a href="{{ url('component-buttons') }}"><i class="bx bx-right-arrow-alt"></i>Buttons</a>
                        </li>
                        <li> <a href="{{ url('component-cards') }}"><i class="bx bx-right-arrow-alt"></i>Cards</a>
                        </li>
                        <li> <a href="{{ url('component-carousels') }}"><i class="bx bx-right-arrow-alt"></i>Carousels</a>
                        </li>
                        <li> <a href="{{ url('component-list-groups') }}"><i class="bx bx-right-arrow-alt"></i>List Groups</a>
                        </li>
                        <li> <a href="{{ url('component-media-object') }}"><i class="bx bx-right-arrow-alt"></i>Media Objects</a>
                        </li>
                        <li> <a href="{{ url('component-modals') }}"><i class="bx bx-right-arrow-alt"></i>Modals</a>
                        </li>
                        <li> <a href="{{ url('component-navs-tabs') }}"><i class="bx bx-right-arrow-alt"></i>Navs & Tabs</a>
                        </li>
                        <li> <a href="{{ url('component-navbar') }}"><i class="bx bx-right-arrow-alt"></i>Navbar</a>
                        </li>
                        <li> <a href="{{ url('component-paginations') }}"><i class="bx bx-right-arrow-alt"></i>Pagination</a>
                        </li>
                        <li> <a href="{{ url('component-popovers-tooltips') }}"><i class="bx bx-right-arrow-alt"></i>Popovers & Tooltips</a>
                        </li>
                        <li> <a href="{{ url('component-progress-bars') }}"><i class="bx bx-right-arrow-alt"></i>Progress</a>
                        </li>
                        <li> <a href="{{ url('component-spinners') }}"><i class="bx bx-right-arrow-alt"></i>Spinners</a>
                        </li>
                        <li> <a href="{{ url('component-notifications') }}"><i class="bx bx-right-arrow-alt"></i>Notifications</a>
                        </li>
                        <li> <a href="{{ url('component-avtars-chips') }}"><i class="bx bx-right-arrow-alt"></i>Avatrs & Chips</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-repeat"></i>
                        </div>
                        <div class="menu-title">Content</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('content-grid-system') }}"><i class="bx bx-right-arrow-alt"></i>Grid System</a>
                        </li>
                        <li> <a href="{{ url('content-typography') }}"><i class="bx bx-right-arrow-alt"></i>Typography</a>
                        </li>
                        <li> <a href="{{ url('content-text-utilities') }}"><i class="bx bx-right-arrow-alt"></i>Text Utilities</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"> <i class="bx bx-donate-blood"></i>
                        </div>
                        <div class="menu-title">Icons</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('icons-line-icons') }}"><i class="bx bx-right-arrow-alt"></i>Line Icons</a>
                        </li>
                        <li> <a href="{{ url('icons-boxicons') }}"><i class="bx bx-right-arrow-alt"></i>Boxicons</a>
                        </li>
                        <li> <a href="{{ url('icons-feather-icons') }}"><i class="bx bx-right-arrow-alt"></i>Feather Icons</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">Forms & Tables</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class='bx bx-message-square-edit'></i>
                        </div>
                        <div class="menu-title">Forms</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('form-elements') }}"><i class="bx bx-right-arrow-alt"></i>Form Elements</a>
                        </li>
                        <li> <a href="{{ url('form-input-group') }}"><i class="bx bx-right-arrow-alt"></i>Input Groups</a>
                        </li>
                        <li> <a href="{{ url('form-layouts') }}"><i class="bx bx-right-arrow-alt"></i>Forms Layouts</a>
                        </li>
                        <li> <a href="{{ url('form-validations') }}"><i class="bx bx-right-arrow-alt"></i>Form Validation</a>
                        </li>
                        <li> <a href="{{ url('form-wizard') }}"><i class="bx bx-right-arrow-alt"></i>Form Wizard</a>
                        </li>
                        <li> <a href="{{ url('form-text-editor') }}"><i class="bx bx-right-arrow-alt"></i>Text Editor</a>
                        </li>
                        <li> <a href="{{ url('form-file-upload') }}"><i class="bx bx-right-arrow-alt"></i>File Upload</a>
                        </li>
                        <li> <a href="{{ url('form-date-time-pickes') }}"><i class="bx bx-right-arrow-alt"></i>Date Pickers</a>
                        </li>
                        <li> <a href="{{ url('form-select2') }}"><i class="bx bx-right-arrow-alt"></i>Select2</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-grid-alt"></i>
                        </div>
                        <div class="menu-title">Tables</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('table-basic-table') }}"><i class="bx bx-right-arrow-alt"></i>Basic Table</a>
                        </li>
                        <li> <a href="{{ url('table-datatable') }}"><i class="bx bx-right-arrow-alt"></i>Data Table</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">Pages</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-lock"></i>
                        </div>
                        <div class="menu-title">Authentication</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('authentication-signin') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>Sign In</a>
                        </li>
                        <li> <a href="{{ url('authentication-signup') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>Sign Up</a>
                        </li>
                        <li> <a href="{{ url('authentication-signin-with-header-footer') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>Sign In with Header & Footer</a>
                        </li>
                        <li> <a href="{{ url('authentication-signup-with-header-footer') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>Sign Up with Header & Footer</a>
                        </li>
                        <li> <a href="{{ url('authentication-forgot-password') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>Forgot Password</a>
                        </li>
                        <li> <a href="{{ url('authentication-reset-password') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>Reset Password</a>
                        </li>
                        <li> <a href="{{ url('authentication-lock-screen') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>Lock Screen</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ url('user-profile') }}">
                        <div class="parent-icon"><i class="bx bx-user-circle"></i>
                        </div>
                        <div class="menu-title">User Profile</div>
                    </a>
                </li>
                <li>
                    <a href="{{ url('timeline') }}">
                        <div class="parent-icon"> <i class="bx bx-video-recording"></i>
                        </div>
                        <div class="menu-title">Timeline</div>
                    </a>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-error"></i>
                        </div>
                        <div class="menu-title">Errors</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('errors-404-error') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>404 Error</a>
                        </li>
                        <li> <a href="{{ url('errors-500-error') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>500 Error</a>
                        </li>
                        <li> <a href="{{ url('errors-coming-soon') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>Coming Soon</a>
                        </li>
                        <li> <a href="{{ url('error-blank-page') }}" target="_blank"><i class="bx bx-right-arrow-alt"></i>Blank Page</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ url('faq') }}">
                        <div class="parent-icon"><i class="bx bx-help-circle"></i>
                        </div>
                        <div class="menu-title">FAQ</div>
                    </a>
                </li>
                <li>
                    <a href="{{ url('pricing-table') }}">
                        <div class="parent-icon"><i class="bx bx-diamond"></i>
                        </div>
                        <div class="menu-title">Pricing</div>
                    </a>
                </li>
                <li class="menu-label">Charts & Maps</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-line-chart"></i>
                        </div>
                        <div class="menu-title">Charts</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('charts-apex-chart') }}"><i class="bx bx-right-arrow-alt"></i>Apex</a>
                        </li>
                        <li> <a href="{{ url('charts-chartjs') }}"><i class="bx bx-right-arrow-alt"></i>Chartjs</a>
                        </li>
                        <li> <a href="{{ url('charts-highcharts') }}"><i class="bx bx-right-arrow-alt"></i>Highcharts</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-map-alt"></i>
                        </div>
                        <div class="menu-title">Maps</div>
                    </a>
                    <ul>
                        <li> <a href="{{ url('map-google-maps') }}"><i class="bx bx-right-arrow-alt"></i>Google Maps</a>
                        </li>
                        <li> <a href="{{ url('map-vector-maps') }}"><i class="bx bx-right-arrow-alt"></i>Vector Maps</a>
                        </li>
                    </ul>
                </li>
                <li class="menu-label">Others</li>
                <li>
                    <a class="has-arrow" href="javascript:;">
                        <div class="parent-icon"><i class="bx bx-menu"></i>
                        </div>
                        <div class="menu-title">Menu Levels</div>
                    </a>
                    <ul>
                        <li> <a class="has-arrow" href="javascript:;"><i class="bx bx-right-arrow-alt"></i>Level One</a>
                            <ul>
                                <li> <a class="has-arrow" href="javascript:;"><i class="bx bx-right-arrow-alt"></i>Level Two</a>
                                    <ul>
                                        <li> <a href="javascript:;"><i class="bx bx-right-arrow-alt"></i>Level Three</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="https://codervent.com/rocker/documentation/" target="_blank">
                        <div class="parent-icon"><i class="bx bx-folder"></i>
                        </div>
                        <div class="menu-title">Documentation</div>
                    </a>
                </li>
                <li>
                    <a href="https://themeforest.net/user/codervent" target="_blank">
                        <div class="parent-icon"><i class="bx bx-support"></i>
                        </div>
                        <div class="menu-title">Support</div>
                    </a>
                </li>
            </ul>
            <!--end navigation-->
        </div>
        <!--end sidebar wrapper -->