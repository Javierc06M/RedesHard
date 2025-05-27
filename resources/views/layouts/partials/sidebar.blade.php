<body class="sa-body">
    <!-- Header Principal -->
    <header class="sa-header">
        <div class="sa-header__left">
            <button class="sa-toggle-btn" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="sa-brand">
                <i class="fas fa-chart-line sa-brand__icon"></i>
                <span class="sa-brand__text">REDES <span class="sa-brand__pro">HARD</span></span>
            </div>
        </div>
        
        <div class="sa-header__center">
            <div class="sa-search">
                <i class="fas fa-search sa-search__icon"></i>
                <input type="text" placeholder="Buscar transacciones, clientes..." class="sa-search__input">
                <button class="sa-search__btn">
                    <i class="fas fa-sliders-h"></i>
                </button>
            </div>
        </div>
        
        <div class="sa-header__right">
           {{--  <div class="sa-notifications">
                <button class="sa-notifications__btn" id="notificationsBtn">
                    <i class="fas fa-bell"></i>
                    <span class="sa-notifications__badge">5</span>
                </button>
            </div>
            
            <div class="sa-messages">
                <button class="sa-messages__btn">
                    <i class="fas fa-envelope"></i>
                    <span class="sa-messages__badge">3</span>
                </button>
            </div> --}}
            
           @php
                $admin = Auth::guard('admin')->user();
                $nombre = $admin->nombre;
                $apellido = explode(' ', $admin->apellido)[0] ?? '';
                $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
                $imagen = $admin->imagen ?? null;
            @endphp

            <div class="sa-profile" id="profileDropdown">
                <div class="sa-profile__info">
                    <span class="sa-profile__name">{{ $admin->nombre }}</span>
                    <span class="sa-profile__role">{{ $admin->username ?? 'Administrador' }}</span>
                </div>
                <div class="sa-profile__avatar">
                    @if ($imagen)
                        <img src="{{ asset('storage/' . $imagen) }}" alt="Profile">
                    @else
                        <div class="avatar-iniciales">{{ $iniciales }}</div>
                    @endif
                </div>
                <button class="sa-profile__arrow">
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="sa-profile__menu">
                    <ul>
                        <li>
                            <a href="{{ route('admin.profile') }}">
                                <svg class="sa-menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="m12 1 1.68 2.32L16 2.24l.96 2.09L19 3.24l.32 2.24L22 4.56l-.32 2.24L19 7.76l-.96 2.09L16 8.76l-1.68 2.32L12 10l-1.68 1.08L8 8.76l-.96 1.09L4 7.76l-.32 1.24L1 4.56l.32-2.24L4 3.24l.96-2.09L8 2.24l1.68-2.32L12 1z"></path>
                                </svg>
                                <span class="sa-menu-text">Perfil</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.email') }}">
                                <svg class="sa-menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <circle cx="12" cy="16" r="1"></circle>
                                    <path d="m7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <span class="sa-menu-text">Cambiar contraseña</span>
                            </a>
                        </li>
                        <li>
                            <form id="logoutForm" class="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit">
                                    <svg class="sa-menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16,17 21,12 16,7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    <span class="sa-menu-text">Cerrar sesión</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sa-sidebar" id="sidebar">
        <div class="sa-sidebar__content">
            <!-- Sección de Usuario -->
            <div class="sa-sidebar__user">
                <div class="sa-user-info">
                    <h3 class="sa-user-info__name">{{ $admin->nombre}}</h3>
                    <p class="sa-user-info__title">{{ $admin->username }}</p>
                </div>
            </div>

            <!-- Navegación Principal -->
            <nav class="sa-nav">
                <div class="sa-nav__section">
                    <h4 class="sa-nav__title">Principal</h4>
                    <ul class="sa-nav__list">
                        <li class="sa-nav__item sa-nav__item--active">
                            <a href="{{ route('ventas.index') }}" 
                            class="sa-nav__link {{ request()->routeIs('ventas.index') ? 'active' : '' }}" 
                            data-section="dashboard">
                                <i class="sa-nav__icon fas fa-tachometer-alt"></i>
                                <span class="sa-nav__text">Ventas</span>
                                <div class="sa-nav__indicator"></div>
                            </a>

                        </li>
                        {{-- <li class="sa-nav__item">
                            <a href="#" class="sa-nav__link" data-section="analytics">
                                <i class="sa-nav__icon fas fa-chart-bar"></i>
                                <span class="sa-nav__text">Analytics</span>
                            </a>
                        </li> --}}
                    </ul>
                </div>

                {{-- <div class="sa-nav__section">
                    <h4 class="sa-nav__title">Ventas</h4>
                    <ul class="sa-nav__list">
                        <li class="sa-nav__item">
                            <a href="#" class="sa-nav__link" data-section="transactions">
                                <i class="sa-nav__icon fas fa-shopping-cart"></i>
                                <span class="sa-nav__text">Transacciones</span>
                                <span class="sa-nav__badge">24</span>
                            </a>
                        </li>
                        <li class="sa-nav__item">
                            <a href="#" class="sa-nav__link" data-section="invoices">
                                <i class="sa-nav__icon fas fa-receipt"></i>
                                <span class="sa-nav__text">Facturas</span>
                            </a>
                        </li>
                        <li class="sa-nav__item">
                            <a href="#" class="sa-nav__link" data-section="discounts">
                                <i class="sa-nav__icon fas fa-percentage"></i>
                                <span class="sa-nav__text">Descuentos</span>
                            </a>
                        </li>
                    </ul>
                </div> --}}

                {{-- <div class="sa-nav__section">
                    <h4 class="sa-nav__title">Gestión</h4>
                    <ul class="sa-nav__list">
                        <li class="sa-nav__item">
                            <a href="#" class="sa-nav__link" data-section="clients">
                                <i class="sa-nav__icon fas fa-users"></i>
                                <span class="sa-nav__text">Clientes</span>
                            </a>
                        </li>
                        <li class="sa-nav__item">
                            <a href="#" class="sa-nav__link" data-section="products">
                                <i class="sa-nav__icon fas fa-box"></i>
                                <span class="sa-nav__text">Productos</span>
                            </a>
                        </li>
                        <li class="sa-nav__item">
                            <a href="#" class="sa-nav__link" data-section="inventory">
                                <i class="sa-nav__icon fas fa-warehouse"></i>
                                <span class="sa-nav__text">Inventario</span>
                            </a>
                        </li>
                    </ul>
                </div> --}}

                {{-- <div class="sa-nav__section">
                    <h4 class="sa-nav__title">Reportes</h4>
                    <ul class="sa-nav__list">
                        <li class="sa-nav__item">
                            <a href="#" class="sa-nav__link" data-section="sales-report">
                                <i class="sa-nav__icon fas fa-chart-pie"></i>
                                <span class="sa-nav__text">Ventas</span>
                            </a>
                        </li>
                        <li class="sa-nav__item">
                            <a href="#" class="sa-nav__link" data-section="reports">
                                <i class="sa-nav__icon fas fa-file-alt"></i>
                                <span class="sa-nav__text">Informes</span>
                            </a>
                        </li>
                    </ul>
                </div> --}}
            </nav>

            <!-- Sección Inferior -->
            <div class="sa-sidebar__footer">
                <div class="sa-stats">
                    <div class="sa-stats__item">
                        <span class="sa-stats__label">Ventas Totales</span>
                        <span class="sa-stats__value">S/ {{ number_format($totalActual, 2) }}</span>

                    </div>
                    {{-- <div class="sa-stats__item">
                        <span class="sa-stats__label">Meta Mensual</span>
                        <div class="sa-progress">
                            <div class="sa-progress__fill" style="width: 78%"></div>
                        </div>
                        <span class="sa-stats__percentage">78%</span>
                    </div> --}}
                </div>
                

                <form id="logoutForm" class="logout-form" action="{{ route('logout') }}" method="post">
                    @csrf
                    <button class="sa-logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <script>
        document.querySelectorAll('.logout-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // prevenir envío inmediato
            
            Swal.fire({
                title: '¿Estás seguro de cerrar sesión?',
                text: "¡Te extrañaremos! 😢",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cerrar sesión',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // si confirma, enviar el formulario
                    form.submit();
                }
            });
        });
    });
    </script>